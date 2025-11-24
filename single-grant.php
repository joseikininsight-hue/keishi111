<?php
/**
 * Grant Single Page - Perfect Edition v24.2
 * 補助金詳細ページ - 完全版（モバイルナビ修正）
 * 
 * v24.2 変更点:
 * - 下部固定ナビゲーション廃止
 * - 右下フローティングAIボタン採用
 * - 共通バナーとの干渉解消
 * - 目次は通常のサイドバーに統合
 * 
 * @package Grant_Insight_Perfect
 * @version 24.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!have_posts()) {
    wp_redirect(home_url('/404'), 302);
    exit;
}

get_header();
the_post();

// ========================================
// データ取得・初期化 (GI_Grant_Data_Helper 使用)
// ========================================

$post_id = get_the_ID();
$canonical_url = get_permalink($post_id);
$seo_title = get_the_title();
$current_year = date('Y');

// 統一データヘルパーで全データ取得
$grant_data = GI_Grant_Data_Helper::get_all_data($post_id);

// タクソノミーはget_all_dataに含まれているので取り出す
$taxonomies = array(
    'categories' => $grant_data['categories'],
    'prefectures' => $grant_data['prefectures'],
    'municipalities' => $grant_data['municipalities'],
    'tags' => $grant_data['tags'],
);

// 都道府県・市町村の表示（Helper使用）
$prefecture_display = GI_Grant_Data_Helper::format_prefectures($taxonomies['prefectures']);
$municipality_display = GI_Grant_Data_Helper::format_municipalities($taxonomies['municipalities'], $taxonomies['prefectures']);

// 金額フォーマット（GrantCardRenderer使用）
$formatted_amount = GrantCardRenderer::format_amount($grant_data['max_amount_numeric'], $grant_data['max_amount']);

// 締切日計算（Helper使用）
$deadline_data = GI_Grant_Data_Helper::get_deadline_info($grant_data['deadline_date'], $grant_data['deadline']);
$deadline_info = $deadline_data['text'];
$deadline_class = $deadline_data['class'];
$days_remaining = $deadline_data['days_remaining'];

// 難易度設定（Helper使用）
$difficulty_data = GI_Grant_Data_Helper::get_difficulty_info($grant_data['grant_difficulty']);

// ステータス（Helper使用）
$application_status = !empty($grant_data['application_status']) ? $grant_data['application_status'] : 'open';
$status_data = GI_Grant_Data_Helper::get_status_info($application_status);

// 閲覧数更新
$current_views = intval($grant_data['views_count']);
$new_views = $current_views + 1;
if (function_exists('update_post_meta')) {
    update_post_meta($post_id, 'views_count', $new_views);
    $grant_data['views_count'] = $new_views;
}

// OGP画像取得（Helper使用）
$og_image = $grant_data['og_image'];

// メタディスクリプション生成（Helper使用）
$meta_description = GI_Grant_Data_Helper::generate_meta_description($post_id);

// 読了時間計算（Helper使用）
$content = $grant_data['content'];
$reading_time = GI_Grant_Data_Helper::calculate_reading_time($content);

// キーワード生成（Helper使用）
$seo_keywords = GI_Grant_Data_Helper::generate_seo_keywords($post_id);

// robots meta
$robots_content = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
if ($application_status === 'closed') {
    $robots_content = 'index, follow, max-snippet:160, max-image-preview:standard';
}

// レコメンドシステム（終了案件を除外）
// 関数は inc/ai-functions.php に移動済み
$scored_related_grants = gi_get_scored_related_grants($post_id, $taxonomies, $grant_data, 12, true);

// 関連コラム取得
$related_columns_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 6,
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => 'related_grants',
            'value' => '"' . $post_id . '"',
            'compare' => 'LIKE'
        )
    ),
    'orderby' => 'date',
    'order' => 'DESC'
));

// 公開日・更新日
$published_date = get_the_date('c');
$modified_date = get_the_modified_date('c');
?>

<!-- 構造化データ（JSON-LD） -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "MonetaryGrant",
            "@id": "<?php echo esc_js(get_permalink() . '#grant'); ?>",
            "name": <?php echo json_encode($seo_title); ?>,
            "description": <?php echo json_encode($meta_description); ?>,
            "url": "<?php echo esc_js(get_permalink()); ?>",
            "image": "<?php echo esc_js($og_image); ?>",
            "funder": {
                "@type": "Organization",
                "name": <?php echo json_encode($grant_data['organization'] ?: get_bloginfo('name')); ?>,
                "url": "<?php echo esc_js($grant_data['official_url'] ?: home_url('/')); ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo esc_js(get_site_icon_url(512)); ?>"
                }
            },
            <?php if ($max_amount_yen > 0): ?>
            "amount": {
                "@type": "MonetaryAmount",
                "currency": "JPY",
                "value": "<?php echo $max_amount_yen; ?>"
            },
            <?php endif; ?>
            <?php if (!empty($grant_data['deadline_date'])): ?>
            "applicationDeadline": "<?php echo esc_js($grant_data['deadline_date']); ?>",
            <?php endif; ?>
            "datePublished": "<?php echo $published_date; ?>",
            "dateModified": "<?php echo $modified_date; ?>",
            "inLanguage": "ja-JP"
        },
        {
            "@type": "Article",
            "@id": "<?php echo esc_js(get_permalink() . '#article'); ?>",
            "headline": <?php echo json_encode($seo_title); ?>,
            "description": <?php echo json_encode($meta_description); ?>,
            "image": "<?php echo esc_js($og_image); ?>",
            "datePublished": "<?php echo $published_date; ?>",
            "dateModified": "<?php echo $modified_date; ?>",
            "author": {
                "@type": "Organization",
                "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
                "url": "<?php echo esc_js(home_url()); ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo esc_js(get_site_icon_url(512)); ?>"
                }
            },
            "publisher": {
                "@type": "Organization",
                "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
                "url": "<?php echo esc_js(home_url()); ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo esc_js(get_site_icon_url(512)); ?>"
                }
            },
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "<?php echo esc_js(get_permalink()); ?>"
            }
        },
        {
            "@type": "BreadcrumbList",
            "@id": "<?php echo esc_js(get_permalink() . '#breadcrumb'); ?>",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "ホーム",
                    "item": "<?php echo esc_js(home_url('/')); ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "補助金一覧",
                    "item": "<?php echo esc_js(home_url('/grants/')); ?>"
                }
                <?php if (!empty($taxonomies['categories'])): ?>
                ,{
                    "@type": "ListItem",
                    "position": 3,
                    "name": "<?php echo esc_js($taxonomies['categories'][0]->name); ?>",
                    "item": "<?php echo esc_js(get_term_link($taxonomies['categories'][0])); ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 4,
                    "name": <?php echo json_encode($seo_title); ?>,
                    "item": "<?php echo esc_js(get_permalink()); ?>"
                }
                <?php else: ?>
                ,{
                    "@type": "ListItem",
                    "position": 3,
                    "name": <?php echo json_encode($seo_title); ?>,
                    "item": "<?php echo esc_js(get_permalink()); ?>"
                }
                <?php endif; ?>
            ]
        }
    ]
}
</script>

<!-- スタイル定義: 外部CSSファイルに移動 (assets/css/single-grant.css) -->
<!-- Styles are now loaded via wp_enqueue_style() in functions.php -->

<main class="gus-single" itemscope itemtype="https://schema.org/Article">
    <div class="gus-layout">
        <!-- メインコンテンツ -->
        <article class="gus-main">
            <!-- ヘッダー -->
            <header class="gus-header">
                <div class="gus-header-top">
                    <div class="gus-status-badge <?php echo $status_data['class']; ?> <?php echo $deadline_class; ?>">
                        <?php echo $status_data['label']; ?>
                        <?php if ($days_remaining > 0 && $days_remaining <= 30): ?>
                            · <?php echo $days_remaining; ?>日
                        <?php endif; ?>
                    </div>
                    <?php if ($grant_data['is_featured']): ?>
                        <div class="gus-featured-badge">
                            注目
                        </div>
                    <?php endif; ?>
                </div>
                <div class="gus-reading-time">
                    <span>■</span>
                    <span>読了時間: 約<?php echo $reading_time; ?>分</span>
                    <span>·</span>
                    <time datetime="<?php echo $published_date; ?>" itemprop="datePublished">
                        更新: <?php echo get_the_modified_date('Y年n月j日'); ?>
                    </time>
                </div>
            </header>

            <!-- AI要約 -->
            <?php if ($grant_data['ai_summary']): ?>
            <section id="ai-summary" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <h2 class="gus-section-title">AI要約</h2>
                </header>
                <div class="gus-section-content">
                    <p><?php echo esc_html($grant_data['ai_summary']); ?></p>
                </div>
            </section>
            <?php endif; ?>

            <!-- おすすめ補助金（グリッド表示） -->
            <?php if (!empty($scored_related_grants)): ?>
            <section id="related" class="gus-related-section">
                <header class="gus-related-header">
                    <div class="gus-related-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <h2 class="gus-related-title">あなたにおすすめの補助金</h2>
                        <p class="gus-related-subtitle">
                            条件が近い補助金をAIがピックアップしました
                        </p>
                    </div>
                </header>
                
                <div class="gus-related-grid">
                    <?php
                    $display_count = 0;
                    foreach ($scored_related_grants as $related_grant):
                        if ($display_count >= 6) break; // 表示数は6件に制限
                        $display_count++;
                        $related_id = $related_grant['id'];
                        $post = get_post($related_id);
                        if ($post) {
                            setup_postdata($post);
                            get_template_part('template-parts/grant-card-unified');
                        }
                    endforeach;
                    wp_reset_postdata();
                    ?>
                </div>
                
                <div style="margin-top: 24px; text-align: center;">
                    <a href="<?php echo home_url('/grants/'); ?>" class="gus-btn gus-btn-secondary">
                        補助金一覧を見る
                    </a>
                </div>
            </section>
            
            <style>
            .gus-related-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr); /* PCは3列 */
                gap: 20px;
                padding: 0 20px;
            }
            @media (max-width: 768px) {
                .gus-related-grid {
                    grid-template-columns: repeat(2, 1fr); /* スマホは2列 */
                    gap: 10px;
                    padding: 0;
                }
            }
            @media (max-width: 480px) {
                .gus-related-grid {
                    grid-template-columns: 1fr; /* 小さいスマホは1列 */
                }
            }
            </style>
            <?php endif; ?>

            <!-- 補助金詳細表（PC/スマホ統一デザイン） -->
            <section id="grant-details" class="gus-section">
                <div class="gus-spec-table-wrapper">
                    <div class="gus-spec-header">
                        <span class="gus-spec-badge">制度概要</span>
                        <h1 class="gus-spec-title" itemprop="headline"><?php the_title(); ?></h1>
                    </div>

                    <dl class="gus-spec-list">
                        <div class="gus-spec-row">
                            <dt class="gus-spec-term">補助金額(最大)</dt>
                            <dd class="gus-spec-desc price"><?php echo esc_html($formatted_amount); ?></dd>
                        </div>
                        
                        <?php if ($grant_data['subsidy_rate']): ?>
                        <div class="gus-spec-row">
                            <dt class="gus-spec-term">補助率</dt>
                            <dd class="gus-spec-desc">
                                <?php echo esc_html($grant_data['subsidy_rate']); ?>
                                <?php if ($grant_data['subsidy_rate_detailed']): ?>
                                    <div class="gus-spec-sub"><?php echo wp_kses_post($grant_data['subsidy_rate_detailed']); ?></div>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <?php endif; ?>

                        <div class="gus-spec-row">
                            <dt class="gus-spec-term">申請締切</dt>
                            <dd class="gus-spec-desc">
                                <span class="<?php echo $deadline_class; ?>"><?php echo esc_html($deadline_info); ?></span>
                                <?php if ($grant_data['application_period']): ?>
                                    <div class="gus-spec-sub">期間: <?php echo esc_html($grant_data['application_period']); ?></div>
                                <?php endif; ?>
                            </dd>
                        </div>

                        <?php if ($grant_data['grant_target']): ?>
                        <div class="gus-spec-row">
                            <dt class="gus-spec-term">対象者</dt>
                            <dd class="gus-spec-desc"><?php echo wp_kses_post($grant_data['grant_target']); ?></dd>
                        </div>
                        <?php endif; ?>

                        <?php if ($expenses_content): ?>
                        <div class="gus-spec-row">
                            <dt class="gus-spec-term">対象経費</dt>
                            <dd class="gus-spec-desc"><?php echo wp_kses_post($expenses_content); ?></dd>
                        </div>
                        <?php endif; ?>

                        <div class="gus-spec-row">
                            <dt class="gus-spec-term">対象地域</dt>
                            <dd class="gus-spec-desc">
                                <?php echo esc_html($prefecture_display); ?>
                                <?php if ($grant_data['area_notes']): ?>
                                    <div class="gus-spec-note">※<?php echo esc_html($grant_data['area_notes']); ?></div>
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>
                </div>

                <style>
                .gus-spec-table-wrapper {
                    border: 1px solid #e5e5e5;
                    border-radius: 8px;
                    overflow: hidden;
                    margin-bottom: 30px;
                    background: #fff;
                }
                .gus-spec-header {
                    background: #333;
                    color: #fff;
                    padding: 15px 20px;
                    border-bottom: 3px solid var(--gus-yellow);
                }
                .gus-spec-badge {
                    display: inline-block;
                    background: var(--gus-yellow);
                    color: #000;
                    font-size: 11px;
                    font-weight: bold;
                    padding: 2px 8px;
                    border-radius: 2px;
                    margin-bottom: 5px;
                }
                .gus-spec-title {
                    margin: 0;
                    font-size: 16px;
                    font-weight: bold;
                    line-height: 1.4;
                    color: #fff;
                }
                .gus-spec-list {
                    margin: 0;
                    padding: 0;
                }
                .gus-spec-row {
                    display: grid;
                    grid-template-columns: 160px 1fr;
                    border-bottom: 1px solid #f0f0f0;
                }
                .gus-spec-row:last-child { border-bottom: none; }
                .gus-spec-term {
                    background: #f9f9f9;
                    padding: 15px;
                    font-weight: bold;
                    font-size: 14px;
                    color: #333;
                    display: flex;
                    align-items: center;
                }
                .gus-spec-desc {
                    margin: 0;
                    padding: 15px;
                    font-size: 14px;
                    line-height: 1.6;
                }
                .gus-spec-desc.price {
                    font-size: 18px;
                    font-weight: bold;
                    color: #DC2626;
                }
                .gus-spec-sub { font-size: 12px; color: #666; margin-top: 4px; }
                .gus-spec-note { font-size: 12px; color: #B8860B; margin-top: 4px; }
                .gus-spec-desc .urgent { color: #DC2626; font-weight: bold; }
                .gus-spec-desc .warning { color: #F59E0B; font-weight: bold; }

                @media (max-width: 600px) {
                    .gus-spec-row {
                        grid-template-columns: 110px 1fr; 
                    }
                    .gus-spec-term {
                        padding: 12px 10px;
                        font-size: 12px;
                        line-height: 1.3;
                    }
                    .gus-spec-desc {
                        padding: 12px 10px;
                    }
                }
                </style>
            </section>

            <!-- 詳細情報 -->
            <section id="details" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <h2 class="gus-section-title">この補助金の詳細情報</h2>
                </header>
                <div class="gus-section-content">
                    <?php
                    $full_content = get_the_content();
                    $full_content = apply_filters('the_content', $full_content);
                    echo $full_content;
                    ?>
                </div>
            </section>

            <!-- 申請の流れ -->
            <section id="application-flow" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <h2 class="gus-section-title">申請の流れ</h2>
                </header>
                <div class="gus-section-content">
                    <div class="gus-application-flow">
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">1</div>
                            <div class="gus-flow-content">
                                <h3>必要書類の準備</h3>
                                <p>事業計画書、見積書などを用意します。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">2</div>
                            <div class="gus-flow-content">
                                <h3>申請書類の提出</h3>
                                <p>オンラインまたは郵送で提出します。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">3</div>
                            <div class="gus-flow-content">
                                <h3>審査</h3>
                                <p>通常1〜2ヶ月程度かかります。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">4</div>
                            <div class="gus-flow-content">
                                <h3>採択・交付決定</h3>
                                <p>結果通知と交付手続きを行います。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- よくある質問 -->
            <section id="faq" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <h2 class="gus-section-title">よくある質問</h2>
                </header>
                <div class="gus-section-content">
                    <div class="gus-faq">
                        <?php if ($grant_data['grant_target']): ?>
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">この補助金の対象者は誰ですか？</summary>
                            <div class="gus-faq-answer">
                                <?php echo wp_kses_post($grant_data['grant_target']); ?>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <?php $documents_display = !empty($grant_data['required_documents_detailed']) ? $grant_data['required_documents_detailed'] : $grant_data['required_documents']; ?>
                        <?php if ($documents_display): ?>
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">申請に必要な書類は何ですか？</summary>
                            <div class="gus-faq-answer">
                                <?php echo wp_kses_post($documents_display); ?>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <?php $expenses_display = !empty($grant_data['eligible_expenses_detailed']) ? $grant_data['eligible_expenses_detailed'] : $grant_data['eligible_expenses']; ?>
                        <?php if ($expenses_display): ?>
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">どのような経費が対象になりますか？</summary>
                            <div class="gus-faq-answer">
                                <?php echo wp_kses_post($expenses_display); ?>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">申請から採択までどのくらいかかりますか？</summary>
                            <div class="gus-faq-answer">
                                通常、申請から採択決定まで1〜2ヶ月程度かかります。ただし、補助金の種類や申請時期によって異なる場合がありますので、詳しくは担当窓口にお問い合わせください。
                            </div>
                        </details>
                        
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">不採択になった場合、再申請は可能ですか？</summary>
                            <div class="gus-faq-answer">
                                多くの場合、次回の募集期間で再申請が可能です。不採択の理由を確認し、改善した上で再度申請することをお勧めします。
                            </div>
                        </details>
                    </div>
                </div>
            </section>

            <!-- 関連コラム記事 -->
            <?php if ($related_columns_query && $related_columns_query->have_posts()): ?>
            <section id="related-columns" class="gus-related-columns-section">
                <header class="gus-related-section-header">
                    <div class="gus-related-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <h2 class="gus-related-section-title">詳しい記事</h2>
                        <div class="gus-related-columns-intro">
                            <p>この補助金について、さらに詳しく解説している記事はこちらです。</p>
                        </div>
                    </div>
                </header>
                
                <div class="gus-related-columns-grid">
                    <?php
                    $column_display_count = 0;
                    while ($related_columns_query->have_posts() && $column_display_count < 6):
                        $related_columns_query->the_post();
                        $column_display_count++;
                        $column_id = get_the_ID();
                        $read_time = function_exists('get_field') ? get_field('estimated_read_time', $column_id) : '';
                        $column_categories = get_the_terms($column_id, 'column_category');
                        $thumbnail_url = get_the_post_thumbnail_url($column_id, 'medium');
                        $excerpt = get_the_excerpt();
                    ?>
                    <a href="<?php the_permalink(); ?>" class="gus-related-column-card">
                        <?php if ($thumbnail_url): ?>
                        <div class="gus-related-column-thumbnail">
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                        </div>
                        <?php endif; ?>
                        
                        <div class="gus-related-column-card-content">
                            <div class="gus-related-column-card-meta">
                                <?php if ($column_categories && !is_wp_error($column_categories)): ?>
                                <span class="gus-related-column-category">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    <?php echo esc_html($column_categories[0]->name); ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($read_time): ?>
                                <span class="gus-related-column-read-time">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <?php echo esc_html($read_time); ?>分
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="gus-related-column-card-title">
                                <?php the_title(); ?>
                            </h3>
                            
                            <?php if ($excerpt): ?>
                            <p class="gus-related-column-card-excerpt">
                                <?php echo wp_trim_words($excerpt, 15, '...'); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
                
                <div class="gus-related-columns-footer">
                    <a href="<?php echo home_url('/columns/'); ?>" class="gus-btn gus-btn-primary" style="display: inline-flex; width: auto; min-width: 200px;">
                        すべてのコラムを見る
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </div>
            </section>
            <?php wp_reset_postdata(); endif; ?>

            <!-- お問い合わせ -->
            <?php if ($grant_data['contact_info']): ?>
            <section id="contact" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <h2 class="gus-section-title">お問い合わせ</h2>
                </header>
                <div class="gus-section-content">
                    <?php echo nl2br(esc_html($grant_data['contact_info'])); ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- ソーシャルシェア -->
            <div class="gus-social-share">
                <h3>この補助金情報をシェア</h3>
                <div class="gus-social-buttons">
                    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" class="gus-btn gus-btn-secondary" target="_blank" rel="noopener noreferrer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                        </svg>
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" class="gus-btn gus-btn-secondary" target="_blank" rel="noopener noreferrer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                        </svg>
                        Facebook
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode(get_permalink()); ?>" class="gus-btn gus-btn-secondary" target="_blank" rel="noopener noreferrer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                        </svg>
                        LINE
                    </a>
                    <button class="gus-btn gus-btn-secondary" onclick="navigator.clipboard.writeText('<?php echo esc_js(get_permalink()); ?>'); alert('URLをコピーしました');">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                        URLコピー
                    </button>
                </div>
            </div>

            <!-- 共通CTAバナー（統一版） -->
            <section class="gus-unified-cta-section">
                <div class="gus-unified-cta-container">
                    <div class="gus-unified-cta-content">
                        <h2 class="gus-unified-cta-title">
                            他にも、あなたに合う補助金があるかもしれません
                        </h2>
                        <p class="gus-unified-cta-description">
                            助成金インサイトで最新の補助金情報を検索。<br>
                            あなたのビジネスに最適な支援制度を見つけましょう。
                        </p>
                        <div class="gus-unified-cta-buttons">
                            <a href="<?php echo home_url('/subsidy-diagnosis/'); ?>" class="gus-unified-cta-btn gus-unified-cta-btn-primary">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 11l3 3L22 4"/>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                                <span>
                                    <strong>AIで診断する</strong>
                                    <small style="display: block; font-size: 0.75rem; font-weight: 400; opacity: 0.9;">あなたに最適な補助金を提案</small>
                                </span>
                            </a>
                            <a href="<?php echo home_url('/grants/'); ?>" class="gus-unified-cta-btn gus-unified-cta-btn-secondary">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                                <span>
                                    <strong>一覧から探す</strong>
                                    <small style="display: block; font-size: 0.75rem; font-weight: 400; opacity: 0.9;">全ての補助金をチェック</small>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- カテゴリー・地域リンク -->
            <section class="gus-section gus-search-style-section">
                <h2 class="gus-section-title" style="font-size: var(--gus-text-lg); font-weight: 700; margin-bottom: 8px;">
                    この補助金のカテゴリー・地域
                </h2>
                <p style="font-size: 14px; color: #6B7280; margin-bottom: 24px;">
                    関連する補助金を素早く探せます
                </p>
                
                <!-- カテゴリー -->
                <?php if (!empty($taxonomies['categories'])): ?>
                <div class="gus-search-section-box">
                    <h3 class="gus-search-box-title">
                        <i class="fas fa-folder-open" aria-hidden="true" style="margin-right: 8px;"></i>
                        カテゴリ
                    </h3>
                    <div class="gus-search-links">
                        <?php foreach ($taxonomies['categories'] as $cat): ?>
                            <a href="<?php echo get_term_link($cat); ?>" class="gus-search-link" title="<?php echo esc_attr($cat->name); ?>の補助金を探す">
                                <?php echo esc_html($cat->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 都道府県 -->
                <?php if (!empty($taxonomies['prefectures'])): ?>
                <div class="gus-search-section-box">
                    <h3 class="gus-search-box-title">
                        <i class="fas fa-map-marker-alt" aria-hidden="true" style="margin-right: 8px;"></i>
                        都道府県
                    </h3>
                    <div class="gus-search-links">
                        <?php foreach ($taxonomies['prefectures'] as $pref): ?>
                            <a href="<?php echo get_term_link($pref); ?>" class="gus-search-link" title="<?php echo esc_attr($pref->name); ?>の補助金を探す">
                                <?php echo esc_html($pref->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 市町村 -->
                <?php if (!empty($taxonomies['municipalities'])): ?>
                <div class="gus-search-section-box">
                    <h3 class="gus-search-box-title">
                        <i class="fas fa-building" aria-hidden="true" style="margin-right: 8px;"></i>
                        市町村
                    </h3>
                    <div class="gus-search-links">
                        <?php foreach ($taxonomies['municipalities'] as $muni): ?>
                            <a href="<?php echo get_term_link($muni); ?>" class="gus-search-link" title="<?php echo esc_attr($muni->name); ?>の補助金を探す">
                                <?php echo esc_html($muni->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </section>

            <!-- パンくずナビゲーション -->
            <nav class="gus-breadcrumb" aria-label="パンくずナビゲーション" itemscope itemtype="https://schema.org/BreadcrumbList">
                <ol>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo home_url('/'); ?>" itemprop="item">
                            <span itemprop="name">ホーム</span>
                        </a>
                        <meta itemprop="position" content="1">
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo home_url('/grants/'); ?>" itemprop="item">
                            <span itemprop="name">補助金一覧</span>
                        </a>
                        <meta itemprop="position" content="2">
                        <?php if (!empty($taxonomies['categories'])): ?>
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo get_term_link($taxonomies['categories'][0]); ?>" itemprop="item">
                            <span itemprop="name"><?php echo esc_html($taxonomies['categories'][0]->name); ?></span>
                        </a>
                        <meta itemprop="position" content="3">
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" style="color: var(--gus-gray-900); font-weight: 600;" aria-current="page">
                        <span itemprop="name"><?php echo esc_html(wp_trim_words($seo_title, 8, '...')); ?></span>
                        <meta itemprop="position" content="4">
                        <meta itemprop="item" content="<?php echo esc_url(get_permalink()); ?>">
                    </li>
                        <?php else: ?>
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" style="color: var(--gus-gray-900); font-weight: 600;" aria-current="page">
                        <span itemprop="name"><?php echo esc_html(wp_trim_words($seo_title, 8, '...')); ?></span>
                        <meta itemprop="position" content="3">
                        <meta itemprop="item" content="<?php echo esc_url(get_permalink()); ?>">
                    </li>
                        <?php endif; ?>
                </ol>
            </nav>
        </article>

        <!-- 右サイドバー -->
        <aside class="gus-sidebar" role="complementary" aria-label="サイドバー">
            <!-- 1. 広告スペース（最上部） -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="gus-sidebar-card gus-sidebar-ad-space">
                <?php
                $grant_category_ids = array();
                if (!empty($taxonomies['categories'])) {
                    foreach ($taxonomies['categories'] as $cat) {
                        $grant_category_ids[] = 'grant_category_' . $cat->term_id;
                    }
                }
                ji_display_ad('single_grant_sidebar_top', array(
                    'page_type' => 'single-grant',
                    'category_ids' => $grant_category_ids
                ));
                ?>
            </div>
            <?php endif; ?>

            <!-- 2. AIアシスタント（柔軟高さ維持） -->
            <div class="gus-pc-ai-permanent">
                <div class="gus-pc-ai-permanent-header">
                    <div class="gus-pc-ai-permanent-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <span>AIアシスタント</span>
                    </div>
                    <div class="gus-pc-ai-permanent-subtitle"><?php echo esc_html(wp_trim_words(get_the_title(), 10, '...')); ?></div>
                </div>
                <div class="gus-pc-ai-permanent-messages" id="pcPermanentMessages">
                    <div class="gus-ai-message gus-ai-message--assistant">
                        <div class="gus-ai-message-avatar">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20M2 12h20"/>
                            </svg>
                        </div>
                        <div class="gus-ai-message-content">
                            こんにちは！この助成金について何でもお聞きください。<br>
                            申請条件、必要書類、申請方法、対象経費など、詳しくお答えします。
                        </div>
                    </div>
                </div>
                <div class="gus-pc-ai-permanent-input-container">
                    <div class="gus-pc-ai-permanent-input-wrapper">
                        <textarea class="gus-pc-ai-permanent-input" id="pcPermanentInput" placeholder="例：申請条件は何ですか？" rows="2"></textarea>
                        <button class="gus-pc-ai-permanent-send" id="pcPermanentSend" type="button" aria-label="送信">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </button>
                    </div>
                    <div class="gus-pc-ai-permanent-suggestions">
                        <button class="gus-pc-ai-permanent-suggestion" type="button" data-question="申請条件を詳しく教えてください">
                            申請条件は？
                        </button>
                        <button class="gus-pc-ai-permanent-suggestion" type="button" data-question="必要な書類を教えてください">
                            必要書類は？
                        </button>
                        <button class="gus-pc-ai-permanent-suggestion" type="button" data-question="どんな費用が対象になりますか？">
                            対象経費は？
                        </button>
                        <button class="gus-pc-ai-permanent-suggestion" type="button" data-question="申請方法を教えてください">
                            申請方法は？
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3. 補助金検索 -->
            <?php get_template_part('template-parts/sidebar/search-widget'); ?>

            <!-- 4. アクション -->
            <div class="gus-sidebar-card">
                <h2 class="gus-sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    アクション
                </h2>
                <div class="gus-actions">
                    <?php if ($grant_data['official_url']): ?>
                    <a href="<?php echo esc_url($grant_data['official_url']); ?>" class="gus-btn gus-btn-primary" target="_blank" rel="noopener noreferrer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        公式サイト
                    </a>
                    <?php endif; ?>
                    <button class="gus-btn gus-btn-secondary" onclick="window.print()" aria-label="印刷">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        印刷
                    </button>
                </div>
            </div>

            <!-- 5. 広告スペース（中部） -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="gus-sidebar-card gus-sidebar-ad-space">
                <?php
                ji_display_ad('single_grant_sidebar_middle', array(
                    'page_type' => 'single-grant',
                    'category_ids' => $grant_category_ids
                ));
                ?>
            </div>
            <?php endif; ?>

            <!-- 6. 目次 -->
            <nav class="gus-sidebar-card" aria-label="目次">
                <h2 class="gus-sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                    目次
                </h2>
                <ul class="gus-toc-list">
                    <?php if ($grant_data['ai_summary']): ?>
                    <li class="gus-toc-item">
                        <a href="#ai-summary" class="gus-toc-link">AI要約</a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($scored_related_grants)): ?>
                    <li class="gus-toc-item">
                        <a href="#related" class="gus-toc-link">おすすめ補助金</a>
                    </li>
                    <?php endif; ?>
                    <li class="gus-toc-item">
                        <a href="#grant-details" class="gus-toc-link">補助金詳細</a>
                    </li>
                    <li class="gus-toc-item">
                        <a href="#details" class="gus-toc-link">詳細情報</a>
                    </li>
                    <li class="gus-toc-item">
                        <a href="#application-flow" class="gus-toc-link">申請の流れ</a>
                    </li>
                    <li class="gus-toc-item">
                        <a href="#faq" class="gus-toc-link">よくある質問</a>
                    </li>
                    <?php if ($related_columns_query && $related_columns_query->have_posts()): ?>
                    <li class="gus-toc-item">
                        <a href="#related-columns" class="gus-toc-link">詳しい記事</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($grant_data['contact_info']): ?>
                    <li class="gus-toc-item">
                        <a href="#contact" class="gus-toc-link">お問い合わせ</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- 7. 関連コラム -->
            <?php if ($related_columns_query && $related_columns_query->have_posts()): ?>
            <div class="gus-sidebar-card">
                <h2 class="gus-sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    詳しい記事
                </h2>
                <div class="gus-related-columns-list">
                    <?php
                    $related_columns_query->rewind_posts();
                    $sidebar_column_count = 0;
                    while ($related_columns_query->have_posts() && $sidebar_column_count < 3):
                        $related_columns_query->the_post();
                        $sidebar_column_count++;
                        $column_id = get_the_ID();
                        $read_time = function_exists('get_field') ? get_field('estimated_read_time', $column_id) : '';
                        $column_categories = get_the_terms($column_id, 'column_category');
                    ?>
                    <a href="<?php the_permalink(); ?>" class="gus-related-column-item">
                        <div class="gus-related-column-content">
                            <div class="gus-related-column-title">
                                <?php echo wp_trim_words(get_the_title(), 12, '...'); ?>
                            </div>
                            <div class="gus-related-column-meta">
                                <?php if ($column_categories && !is_wp_error($column_categories)): ?>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    <?php echo esc_html($column_categories[0]->name); ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($read_time): ?>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <?php echo esc_html($read_time); ?>分
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="gus-related-column-arrow">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
                <a href="<?php echo home_url('/columns/'); ?>" class="gus-view-all-link">
                    すべてのコラムを見る
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            </div>
            <?php wp_reset_postdata(); endif; ?>

            <!-- 8. アフィリエイト広告（下部） -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="gus-sidebar-card gus-sidebar-ad-space">
                <?php
                ji_display_ad('single_grant_sidebar_bottom', array(
                    'page_type' => 'single-grant',
                    'category_ids' => $grant_category_ids
                ));
                ?>
            </div>
            <?php endif; ?>
        </aside>
    </div>
</main>

<!-- AIチャット（改善版） -->
<div id="ai-chat-wrapper">
    <button id="ai-launcher" aria-label="AIに質問">
        <div class="ai-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </div>
        <span class="ai-label">AI質問</span>
    </button>

    <div id="ai-window">
        <div class="ai-header">
            <div class="ai-header-title">
                <span class="ai-avatar-icon">🤖</span> AIアシスタント
            </div>
            <button id="ai-close">×</button>
        </div>
        <div class="ai-body" id="ai-messages">
            <div class="ai-msg bot">
                <div class="ai-bubble">
                    こんにちは!この補助金について何でも聞いてください。<br>
                    「対象経費は?」「申請期限は?」などお答えします。
                </div>
            </div>
        </div>
        <div class="ai-footer">
            <div class="ai-suggestions">
                <button onclick="sendAiQuery('申請条件は?')">申請条件</button>
                <button onclick="sendAiQuery('必要書類は?')">必要書類</button>
            </div>
            <div class="ai-input-area">
                <input type="text" id="ai-input" placeholder="質問を入力...">
                <button id="ai-send">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* AIチャット用CSS */
#ai-launcher {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 56px;
    height: 56px;
    background: #111;
    color: #FFD700;
    border-radius: 50%;
    border: 2px solid #FFD700;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s;
}
#ai-launcher:hover { transform: scale(1.05); }
.ai-label { font-size: 9px; font-weight: bold; margin-top: -2px; }

#ai-window {
    display: none;
    position: fixed;
    bottom: 160px;
    right: 20px;
    width: 340px;
    height: 450px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    z-index: 10000;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #eee;
}
@media (max-width: 480px) {
    #ai-window {
        width: 92%;
        right: 4%;
        bottom: 160px;
        height: 50vh;
    }
}

.ai-header {
    background: #111;
    color: #fff;
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
}
.ai-body {
    flex: 1;
    background: #f5f5f5;
    padding: 15px;
    overflow-y: auto;
}
.ai-msg { margin-bottom: 10px; display: flex; }
.ai-msg.bot { justify-content: flex-start; }
.ai-msg.user { justify-content: flex-end; }
.ai-bubble {
    padding: 10px 14px;
    border-radius: 12px;
    max-width: 85%;
    font-size: 14px;
    line-height: 1.5;
}
.ai-msg.bot .ai-bubble { background: #fff; border: 1px solid #ddd; border-top-left-radius: 2px; }
.ai-msg.user .ai-bubble { background: #FFD700; color: #000; border-top-right-radius: 2px; }

.ai-footer { padding: 10px; background: #fff; border-top: 1px solid #eee; }
.ai-suggestions { display: flex; gap: 5px; margin-bottom: 8px; overflow-x: auto; }
.ai-suggestions button {
    white-space: nowrap;
    background: #f0f0f0;
    border: none;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    cursor: pointer;
}
.ai-input-area { display: flex; gap: 8px; }
#ai-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 14px;
}
#ai-send {
    background: #111;
    color: #fff;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
#ai-close {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
}
</style>

<script>
document.getElementById('ai-launcher').addEventListener('click', function() {
    const win = document.getElementById('ai-window');
    win.style.display = (win.style.display === 'flex') ? 'none' : 'flex';
});
document.getElementById('ai-close').addEventListener('click', function() {
    document.getElementById('ai-window').style.display = 'none';
});

function sendAiQuery(text) {
    const msgs = document.getElementById('ai-messages');
    
    const userDiv = document.createElement('div');
    userDiv.className = 'ai-msg user';
    userDiv.innerHTML = `<div class="ai-bubble">${text}</div>`;
    msgs.appendChild(userDiv);
    msgs.scrollTop = msgs.scrollHeight;

    setTimeout(() => {
        const botDiv = document.createElement('div');
        botDiv.className = 'ai-msg bot';
        botDiv.innerHTML = `<div class="ai-bubble">「${text}」ですね。記事内を確認します...<br>(※実際の回答ロジックが入ります)</div>`;
        msgs.appendChild(botDiv);
        msgs.scrollTop = msgs.scrollHeight;
    }, 800);
}

document.getElementById('ai-send').addEventListener('click', function() {
    const input = document.getElementById('ai-input');
    if(input.value.trim()) {
        sendAiQuery(input.value);
        input.value = '';
    }
});

document.getElementById('ai-input').addEventListener('keypress', function(e) {
    if(e.key === 'Enter') {
        document.getElementById('ai-send').click();
    }
});
</script>
        </div>
        <div class="gus-ai-input-container">
            <div class="gus-ai-input-wrapper">
                <textarea 
                    class="gus-ai-input" 
                    id="mobileAiInput" 
                    placeholder="例：申請条件は何ですか？" 
                    rows="1"
                    aria-label="質問を入力"></textarea>
                <button class="gus-ai-send" id="mobileAiSend" type="button" aria-label="送信">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
            <div class="gus-ai-suggestions">
                <button class="gus-ai-suggestion" type="button" data-question="申請条件を詳しく教えてください">
                    申請条件は？
                </button>
                <button class="gus-ai-suggestion" type="button" data-question="必要な書類を教えてください">
                    必要書類は？
                </button>
                <button class="gus-ai-suggestion" type="button" data-question="どんな費用が対象になりますか？">
                    対象経費は？
                </button>
                <button class="gus-ai-suggestion" type="button" data-question="申請方法を教えてください">
                    申請方法は？
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript: 外部JSファイルに移動 (assets/js/single-grant.js) -->
<!-- Scripts are now loaded via wp_enqueue_script() in functions.php -->

<?php 
get_footer(); 
?>