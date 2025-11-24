<?php
/**
 * Single Column Template - Complete Responsive v6.1
 * コラム記事詳細ページ - 完全レスポンシブ対応（スマホ横スクロール完全防止版）
 * 
 * Version: 6.1.0
 * - 完全レスポンシブ対応（スマホ最適化）
 * - スマホ横スクロール完全防止
 * - タッチ操作対応
 * - パフォーマンス最適化
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 */

get_header();

while (have_posts()): the_post();

// メタ情報を取得
$post_id = get_the_ID();
$read_time = get_field('estimated_read_time', $post_id);
$view_count = get_field('view_count', $post_id) ?: 0;
$difficulty = get_field('difficulty_level', $post_id);
$last_updated = get_field('last_updated', $post_id);
$key_points = get_field('key_points', $post_id);
$target_audience = get_field('target_audience', $post_id);
$categories = get_the_terms($post_id, 'column_category');
$tags = get_the_terms($post_id, 'column_tag');

// SEO用データ
$post_url = get_permalink();
$post_title = get_the_title();
$post_excerpt = get_the_excerpt();
$post_image = get_the_post_thumbnail_url($post_id, 'full');
$post_date = get_the_date('c');
$post_modified = get_the_modified_date('c');
$author_name = get_the_author();

// SEO: メタディスクリプション最適化
$meta_description = $post_excerpt;
if (strlen($meta_description) > 160) {
    $meta_description = mb_substr($meta_description, 0, 157) . '...';
}

// 関連コラムを取得
$related_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 3,
    'post__not_in' => array($post_id),
    'post_status' => 'publish',
    'orderby' => 'rand',
));

// 関連補助金を取得
$acf_related_grants = get_field('related_grants', $post_id);
$related_grants_query = null;

if (!empty($acf_related_grants) && is_array($acf_related_grants)) {
    $related_grants_query = new WP_Query(array(
        'post_type' => 'grant',
        'post__in' => $acf_related_grants,
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'orderby' => 'post__in',
    ));
} else {
    $related_grants_args = array(
        'post_type' => 'grant',
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'orderby' => 'rand',
    );
    
    if ($categories && !is_wp_error($categories) && !empty($categories)) {
        $category_names = array_map(function($cat) {
            return $cat->name;
        }, $categories);
        
        $related_grants_args['tax_query'] = array(
            array(
                'taxonomy' => 'grant_category',
                'field' => 'name',
                'terms' => $category_names,
                'operator' => 'IN'
            )
        );
    }
    
    $related_grants_query = new WP_Query($related_grants_args);
}
?>

<!-- SEO: 構造化データ - パンくずリスト -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
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
      "name": "コラム",
      "item": "<?php echo esc_js(get_post_type_archive_link('column')); ?>"
    }
    <?php if ($categories && !is_wp_error($categories)): ?>
    ,{
      "@type": "ListItem",
      "position": 3,
      "name": "<?php echo esc_js($categories[0]->name); ?>",
      "item": "<?php echo esc_js(get_term_link($categories[0])); ?>"
    }
    <?php endif; ?>
    ,{
      "@type": "ListItem",
      "position": <?php echo $categories ? 4 : 3; ?>,
      "name": "<?php echo esc_js($post_title); ?>",
      "item": "<?php echo esc_js($post_url); ?>"
    }
  ]
}
</script>

<!-- SEO: 構造化データ - 記事 -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "<?php echo esc_js($post_title); ?>",
  "description": "<?php echo esc_js($meta_description); ?>",
  "image": "<?php echo esc_url($post_image); ?>",
  "datePublished": "<?php echo $post_date; ?>",
  "dateModified": "<?php echo $post_modified; ?>",
  "author": {
    "@type": "Person",
    "name": "<?php echo esc_js($author_name); ?>"
  },
  "publisher": {
    "@type": "Organization",
    "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
    "logo": {
      "@type": "ImageObject",
      "url": "<?php echo esc_url(get_site_icon_url()); ?>"
    }
  },
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "<?php echo esc_url($post_url); ?>"
  }
  <?php if ($read_time): ?>
  ,"timeRequired": "PT<?php echo intval($read_time); ?>M"
  <?php endif; ?>
  <?php if ($categories && !is_wp_error($categories)): ?>
  ,"articleSection": "<?php echo esc_js($categories[0]->name); ?>"
  <?php endif; ?>
  <?php if ($tags && !is_wp_error($tags)): ?>
  ,"keywords": "<?php echo esc_js(implode(', ', wp_list_pluck($tags, 'name'))); ?>"
  <?php endif; ?>
}
</script>

<!-- SEO: OGPメタタグ -->
<meta property="og:type" content="article">
<meta property="og:title" content="<?php echo esc_attr($post_title); ?>">
<meta property="og:description" content="<?php echo esc_attr($meta_description); ?>">
<meta property="og:url" content="<?php echo esc_url($post_url); ?>">
<meta property="og:image" content="<?php echo esc_url($post_image); ?>">
<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
<meta property="article:published_time" content="<?php echo $post_date; ?>">
<meta property="article:modified_time" content="<?php echo $post_modified; ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr($post_title); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($meta_description); ?>">
<meta name="twitter:image" content="<?php echo esc_url($post_image); ?>">

<!-- SEO: Canonical URL -->
<link rel="canonical" href="<?php echo esc_url($post_url); ?>">

<article id="post-<?php the_ID(); ?>" <?php post_class('single-column-responsive'); ?> itemscope itemtype="https://schema.org/Article">
    
    <!-- SEO: 非表示のメタデータ -->
    <meta itemprop="headline" content="<?php echo esc_attr($post_title); ?>">
    <meta itemprop="description" content="<?php echo esc_attr($meta_description); ?>">
    <meta itemprop="image" content="<?php echo esc_url($post_image); ?>">
    <meta itemprop="datePublished" content="<?php echo $post_date; ?>">
    <meta itemprop="dateModified" content="<?php echo $post_modified; ?>">
    <div itemprop="author" itemscope itemtype="https://schema.org/Person" style="display:none;">
        <span itemprop="name"><?php echo esc_html($author_name); ?></span>
    </div>
    <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization" style="display:none;">
        <span itemprop="name"><?php echo esc_html(get_bloginfo('name')); ?></span>
        <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
            <meta itemprop="url" content="<?php echo esc_url(get_site_icon_url()); ?>">
        </div>
    </div>
    
    <div class="column-layout-container">
        
        <main class="column-main-content" role="main">
            
            <header class="column-header-section">
                
                <!-- パンくずリスト -->
                <nav class="column-breadcrumb" aria-label="パンくずナビゲーション">
                    <ol itemscope itemtype="https://schema.org/BreadcrumbList">
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="<?php echo home_url('/'); ?>">
                                <span itemprop="name">ホーム</span>
                            </a>
                            <meta itemprop="position" content="1">
                        </li>
                        <li><i class="fas fa-chevron-right" aria-hidden="true"></i></li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a itemprop="item" href="<?php echo get_post_type_archive_link('column'); ?>">
                                <span itemprop="name">コラム</span>
                            </a>
                            <meta itemprop="position" content="2">
                        </li>
                        <?php if ($categories && !is_wp_error($categories)): ?>
                            <li><i class="fas fa-chevron-right" aria-hidden="true"></i></li>
                            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                <a itemprop="item" href="<?php echo get_term_link($categories[0]); ?>">
                                    <span itemprop="name"><?php echo esc_html($categories[0]->name); ?></span>
                                </a>
                                <meta itemprop="position" content="3">
                            </li>
                        <?php endif; ?>
                    </ol>
                </nav>

                <!-- カテゴリバッジ -->
                <div class="column-badges">
                    <?php if ($categories && !is_wp_error($categories)): ?>
                        <?php foreach (array_slice($categories, 0, 2) as $cat): ?>
                            <a href="<?php echo get_term_link($cat); ?>" class="badge badge-category" rel="category tag">
                                <i class="fas fa-folder" aria-hidden="true"></i>
                                <?php echo esc_html($cat->name); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if ($difficulty): ?>
                        <?php
                        $difficulty_labels = array(
                            'beginner' => array('label' => '初級', 'class' => 'badge-beginner'),
                            'intermediate' => array('label' => '中級', 'class' => 'badge-intermediate'),
                            'advanced' => array('label' => '上級', 'class' => 'badge-advanced'),
                        );
                        $diff_info = $difficulty_labels[$difficulty] ?? array('label' => $difficulty, 'class' => 'badge-default');
                        ?>
                        <span class="badge <?php echo $diff_info['class']; ?>" aria-label="難易度: <?php echo $diff_info['label']; ?>">
                            <i class="fas fa-signal" aria-hidden="true"></i>
                            <?php echo $diff_info['label']; ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- タイトル -->
                <h1 class="column-title" itemprop="headline"><?php the_title(); ?></h1>

                <!-- メタ情報 -->
                <div class="column-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                        <time datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                            <?php echo get_the_date('Y年m月d日'); ?>
                        </time>
                    </div>
                    
                    <?php if ($last_updated && $last_updated !== get_the_date('Y-m-d')): ?>
                        <div class="meta-item">
                            <i class="fas fa-sync-alt" aria-hidden="true"></i>
                            <time datetime="<?php echo date('c', strtotime($last_updated)); ?>" itemprop="dateModified">
                                更新: <?php echo date('Y年m月d日', strtotime($last_updated)); ?>
                            </time>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($read_time): ?>
                        <div class="meta-item">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            <span><?php echo esc_html($read_time); ?>分で読めます</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="meta-item">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                        <span><?php echo number_format($view_count); ?>回閲覧</span>
                    </div>
                </div>

            </header>

            <!-- 対象読者 -->
            <?php if ($target_audience && is_array($target_audience) && count($target_audience) > 0): ?>
                <aside class="target-audience-box" aria-label="対象読者">
                    <h2 class="box-title">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        この記事はこんな方におすすめ
                    </h2>
                    <ul class="audience-list">
                        <?php
                        $audience_labels = array(
                            'startup' => '創業・スタートアップを考えている方',
                            'sme' => '中小企業の経営者・担当者',
                            'individual' => '個人事業主・フリーランス',
                            'npo' => 'NPO・一般社団法人',
                            'agriculture' => '農業・林業・漁業従事者',
                            'other' => 'その他事業者',
                        );
                        foreach ($target_audience as $audience):
                            if (isset($audience_labels[$audience])):
                        ?>
                            <li><i class="fas fa-check" aria-hidden="true"></i><?php echo esc_html($audience_labels[$audience]); ?></li>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </aside>
            <?php endif; ?>

            <!-- アイキャッチ画像 -->
            <?php if (has_post_thumbnail()): ?>
                <figure class="column-thumbnail" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                    <?php 
                    $thumbnail_id = get_post_thumbnail_id();
                    $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                    $thumbnail_alt = $thumbnail_alt ? $thumbnail_alt : $post_title;
                    ?>
                    <?php the_post_thumbnail('large', array(
                        'itemprop' => 'url contentUrl',
                        'alt' => esc_attr($thumbnail_alt)
                    )); ?>
                    <meta itemprop="width" content="1200">
                    <meta itemprop="height" content="630">
                </figure>
            <?php endif; ?>

            <!-- 記事本文 -->
            <div class="column-content" itemprop="articleBody">
                <?php the_content(); ?>
            </div>

            <!-- 記事終了後の最強CTAボックス -->
            <section class="gus-cta-section" role="complementary" aria-label="次のアクション">
                <div class="gus-cta-container">
                    <div class="gus-cta-content">
                        <div class="gus-cta-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                <circle cx="12" cy="12" r="2" fill="currentColor"/>
                            </svg>
                        </div>
                        <h2 class="gus-cta-title">
                            あなたに合う補助金・助成金を今すぐ見つけましょう
                        </h2>
                        <p class="gus-cta-description">
                            AI診断で最適な補助金を提案。<br>
                            助成金インサイトであなたのビジネスに最適な支援制度を見つけましょう。
                        </p>
                        <div class="gus-cta-buttons">
                            <a href="<?php echo home_url('/subsidy-diagnosis/'); ?>" 
                               class="gus-cta-btn gus-cta-btn-primary"
                               aria-label="AIで最適な補助金を診断">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M9 11l3 3L22 4"/>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                                <span>
                                    <strong>AIで診断する</strong>
                                    <small>あなたに最適な補助金を提案</small>
                                </span>
                            </a>
                            <a href="<?php echo home_url('/grants/'); ?>" 
                               class="gus-cta-btn gus-cta-btn-secondary"
                               aria-label="補助金一覧から探す">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                                <span>
                                    <strong>一覧から探す</strong>
                                    <small>全ての補助金をチェック</small>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- タグ -->
            <?php if ($tags && !is_wp_error($tags)): ?>
                <nav class="column-tags" aria-label="タグ">
                    <h2 class="tags-title">
                        <i class="fas fa-tags" aria-hidden="true"></i>
                        関連タグ
                    </h2>
                    <div class="tags-list">
                        <?php foreach ($tags as $tag): ?>
                            <a href="<?php echo get_term_link($tag); ?>" class="tag-link" rel="tag">
                                #<?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </nav>
            <?php endif; ?>

            <!-- シェアボタン -->
            <aside class="column-share" aria-label="シェアボタン">
                <h2 class="share-title">この記事をシェア</h2>
                <div class="share-buttons">
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($post_url); ?>&text=<?php echo urlencode($post_title); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="share-btn share-twitter"
                       aria-label="Twitterでシェア">
                        <i class="fab fa-twitter" aria-hidden="true"></i>
                        <span class="share-text">Twitter</span>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($post_url); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="share-btn share-facebook"
                       aria-label="Facebookでシェア">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        <span class="share-text">Facebook</span>
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode($post_url); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="share-btn share-line"
                       aria-label="LINEでシェア">
                        <i class="fab fa-line" aria-hidden="true"></i>
                        <span class="share-text">LINE</span>
                    </a>
                </div>
            </aside>

            <!-- スマホ用: このコラムの補助金情報 -->
            <?php if ($related_grants_query && $related_grants_query->have_posts()): ?>
            <section class="mobile-related-grants" aria-labelledby="mobile-related-grants-title">
                <h2 class="section-title" id="mobile-related-grants-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        <circle cx="12" cy="12" r="2" fill="currentColor"/>
                    </svg>
                    このコラムの補助金情報はこちら
                </h2>
                <div class="mobile-grants-grid">
                    <?php 
                    $count = 0;
                    while ($related_grants_query->have_posts() && $count < 2): $related_grants_query->the_post(); 
                        $grant_id = get_the_ID();
                        $grant_amount = get_field('max_amount_numeric', $grant_id);
                        $grant_deadline = get_field('deadline', $grant_id);
                        
                        $formatted_amount = '';
                        if ($grant_amount && $grant_amount > 0) {
                            if ($grant_amount >= 10000) {
                                $formatted_amount = number_format($grant_amount / 10000) . '万円';
                            } else {
                                $formatted_amount = number_format($grant_amount) . '円';
                            }
                        }
                    ?>
                        <article class="mobile-grant-card">
                            <a href="<?php the_permalink(); ?>" class="mobile-grant-link">
                                <span class="mobile-grant-badge">補助金</span>
                                <h3 class="mobile-grant-title"><?php the_title(); ?></h3>
                                <div class="mobile-grant-info">
                                    <?php if ($formatted_amount): ?>
                                        <span class="mobile-grant-amount">上限 <?php echo esc_html($formatted_amount); ?></span>
                                    <?php endif; ?>
                                    <?php if ($grant_deadline): ?>
                                        <span class="mobile-grant-deadline"><?php echo esc_html($grant_deadline); ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="mobile-grant-cta">詳細を見る →</span>
                            </a>
                        </article>
                    <?php 
                        $count++;
                    endwhile; 
                    wp_reset_postdata(); 
                    ?>
                </div>
                <a href="<?php echo home_url('/grants/'); ?>" class="mobile-view-all-grants">
                    すべての補助金を見る
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </section>
            <?php endif; ?>

            <!-- 関連記事 -->
            <?php if ($related_query->have_posts()): ?>
                <section class="related-columns" aria-labelledby="related-title">
                    <h2 class="related-title" id="related-title">
                        <i class="fas fa-newspaper" aria-hidden="true"></i>
                        あわせて読みたい関連コラム
                    </h2>
                    <div class="related-grid">
                        <?php while ($related_query->have_posts()): $related_query->the_post(); ?>
                            <?php get_template_part('template-parts/column/card'); ?>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </section>
            <?php endif; ?>

        </main>

        <!-- サイドバー -->
        <aside class="column-sidebar" role="complementary" aria-label="サイドバー">
            
            <!-- アフィリエイト広告: サイドバー上部 -->
            <?php if (function_exists('ji_display_ad')): ?>
                <div class="sidebar-card sidebar-ad-space sidebar-ad-top">
                    <?php 
                    $column_category_ids = array();
                    if (!empty($categories) && !is_wp_error($categories)) {
                        foreach ($categories as $cat) {
                            $column_category_ids[] = 'column_category_' . $cat->term_id;
                        }
                    }
                    ji_display_ad('single_column_sidebar_top', array(
                        'page_type' => 'single-column',
                        'category_ids' => $column_category_ids
                    )); 
                    ?>
                </div>
            <?php endif; ?>

            <!-- 目次カード -->
            <section class="sidebar-card toc-card" aria-labelledby="toc-card-title">
                <header class="card-header">
                    <i class="fas fa-list" aria-hidden="true"></i>
                    <h2 id="toc-card-title">目次</h2>
                </header>
                <div class="card-body">
                    <nav class="toc-nav" id="toc-nav" aria-label="記事の目次">
                        <!-- JavaScriptで動的生成 -->
                    </nav>
                </div>
            </section>

            <!-- AIアシスタントカード -->
            <section class="sidebar-card ai-chat-card" aria-labelledby="ai-chat-title">
                <header class="card-header card-header-ai">
                    <i class="fas fa-robot" aria-hidden="true"></i>
                    <h2 id="ai-chat-title">AI質問アシスタント</h2>
                </header>
                <div class="card-body">
                    <div class="ai-chat-intro">
                        <p>この記事について質問してください。AIがお答えします。</p>
                    </div>
                    <div class="desktop-ai-chat-messages" id="desktopAiMessages" role="log" aria-live="polite" aria-label="AIチャット">
                        <div class="ai-message ai-message-assistant">
                            <div class="ai-avatar" aria-hidden="true">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="ai-content">
                                こんにちは！この記事について何でも質問してください。
                            </div>
                        </div>
                    </div>
                    <div class="desktop-ai-input-container">
                        <label for="desktopAiInput" class="sr-only">AI質問入力</label>
                        <textarea id="desktopAiInput" 
                                  placeholder="例：この補助金の申請期限は？" 
                                  rows="2"
                                  aria-label="AI質問入力"></textarea>
                        <button id="desktopAiSend" class="desktop-ai-send-btn" aria-label="質問を送信">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            <span class="send-text">送信</span>
                        </button>
                    </div>
                </div>
            </section>
            
            <!-- 補助金検索 -->
            <?php get_template_part('template-parts/sidebar/search-widget'); ?>
            
            <!-- このコラムの補助金情報 -->
            <?php if ($related_grants_query && $related_grants_query->have_posts()): ?>
            <section class="sidebar-card related-grants-card" aria-labelledby="related-grants-title">
                <header class="card-header card-header-grants">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        <circle cx="12" cy="12" r="2" fill="currentColor"/>
                    </svg>
                    <h2 id="related-grants-title">このコラムの補助金情報</h2>
                </header>
                <div class="card-body">
                    <div class="related-grants-list">
                        <?php while ($related_grants_query->have_posts()): $related_grants_query->the_post(); 
                            $grant_id = get_the_ID();
                            $grant_amount = get_field('max_amount_numeric', $grant_id);
                            $grant_deadline = get_field('deadline', $grant_id);
                            $grant_status = get_field('application_status', $grant_id);
                            
                            $formatted_amount = '';
                            if ($grant_amount && $grant_amount > 0) {
                                if ($grant_amount >= 10000) {
                                    $formatted_amount = number_format($grant_amount / 10000) . '万円';
                                } else {
                                    $formatted_amount = number_format($grant_amount) . '円';
                                }
                            }
                        ?>
                            <article class="related-grant-item">
                                <a href="<?php the_permalink(); ?>" class="related-grant-link">
                                    <h3 class="related-grant-title"><?php the_title(); ?></h3>
                                    <div class="related-grant-meta">
                                        <?php if ($formatted_amount): ?>
                                            <span class="grant-amount">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                                </svg>
                                                最大 <?php echo esc_html($formatted_amount); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($grant_deadline): ?>
                                            <span class="grant-deadline">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                                </svg>
                                                <?php echo esc_html($grant_deadline); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($grant_status === 'open'): ?>
                                        <span class="grant-status status-open">募集中</span>
                                    <?php endif; ?>
                                </a>
                            </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                    <a href="<?php echo home_url('/grants/'); ?>" class="view-all-grants">
                        すべての補助金を見る
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </section>
            <?php endif; ?>

            <!-- 人気記事カード -->
            <section class="sidebar-card popular-card" aria-labelledby="popular-card-title">
                <header class="card-header">
                    <i class="fas fa-fire" aria-hidden="true"></i>
                    <h2 id="popular-card-title">人気のコラム</h2>
                </header>
                <div class="card-body">
                    <?php
                    $popular_query = new WP_Query(array(
                        'post_type' => 'column',
                        'posts_per_page' => 5,
                        'meta_key' => 'view_count',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                    ));
                    
                    if ($popular_query->have_posts()):
                    ?>
                        <ul class="popular-list">
                            <?php while ($popular_query->have_posts()): $popular_query->the_post(); ?>
                                <li>
                                    <a href="<?php the_permalink(); ?>">
                                        <span class="popular-rank" aria-label="ランキング <?php echo $popular_query->current_post + 1; ?>位"><?php echo $popular_query->current_post + 1; ?></span>
                                        <span class="popular-title"><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </section>

            <!-- アフィリエイト広告: サイドバー下部 -->
            <?php if (function_exists('ji_display_ad')): ?>
                <div class="sidebar-card sidebar-ad-space sidebar-ad-bottom">
                    <?php 
                    ji_display_ad('single_column_sidebar_bottom', array(
                        'page_type' => 'single-column',
                        'category_ids' => $column_category_ids
                    )); 
                    ?>
                </div>
            <?php endif; ?>

        </aside>

    </div>

</article>

<!-- モバイル用フローティングボタン -->
<button class="gus-mobile-toc-cta" id="mobileTocBtn" aria-label="目次とAI質問を開く">
    <div class="gus-mobile-toc-icon">
        <span class="gus-mobile-toc-icon-toc" aria-hidden="true">📑</span>
        <span class="gus-mobile-toc-icon-ai">AI</span>
    </div>
</button>

<!-- モバイル用オーバーレイ -->
<div class="gus-mobile-toc-overlay" id="mobileTocOverlay" aria-hidden="true"></div>

<!-- モバイル用パネル -->
<div class="gus-mobile-toc-panel" id="mobileTocPanel" role="dialog" aria-labelledby="mobile-panel-title" aria-modal="true">
    <header class="gus-mobile-toc-header">
        <h2 class="gus-mobile-toc-title" id="mobile-panel-title">目次 & AI質問</h2>
        <button class="gus-mobile-toc-close" id="mobileTocClose" aria-label="閉じる">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </header>
    
    <div class="gus-mobile-nav-tabs" role="tablist" aria-label="目次とAI質問の切り替え">
        <button class="gus-mobile-nav-tab active" data-tab="ai" role="tab" aria-selected="true" aria-controls="aiContent" id="aiTab">
            <i class="fas fa-robot" aria-hidden="true"></i>
            AI 質問
        </button>
        <button class="gus-mobile-nav-tab" data-tab="toc" role="tab" aria-selected="false" aria-controls="tocContent" id="tocTab">
            <i class="fas fa-list" aria-hidden="true"></i>
            📑 目次
        </button>
    </div>
    
    <div class="gus-mobile-nav-content active" id="aiContent" role="tabpanel" aria-labelledby="aiTab">
        <div class="gus-ai-chat-messages" id="mobileAiMessages" role="log" aria-live="polite" aria-label="AIチャット">
            <div class="ai-message ai-message-assistant">
                <div class="ai-avatar" aria-hidden="true">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="ai-content">
                    こんにちは！この記事について何でも質問してください。
                </div>
            </div>
        </div>
        <div class="gus-ai-input-container">
            <label for="mobileAiInput" class="sr-only">AI質問入力</label>
            <textarea id="mobileAiInput" 
                      placeholder="例：この補助金の申請期限は？" 
                      rows="2"
                      aria-label="AI質問入力"></textarea>
            <button id="mobileAiSend" class="gus-ai-send-btn" aria-label="質問を送信">
                <i class="fas fa-paper-plane" aria-hidden="true"></i>
                <span class="send-text">送信</span>
            </button>
        </div>
    </div>
    
    <div class="gus-mobile-nav-content" id="tocContent" role="tabpanel" aria-labelledby="tocTab" hidden>
        <nav class="gus-mobile-toc-list" id="mobileTocList" aria-label="記事の目次">
            <!-- JavaScriptで動的生成 -->
        </nav>
    </div>
</div>

<?php endwhile; ?>

<?php 
// モバイル検索モーダルを追加
get_template_part('template-parts/sidebar/mobile-search-modal'); 

get_footer(); 
?>

<style>
/* ============================================
   Single Column v6.1 - Complete Responsive
   完全レスポンシブ対応版（スマホ横スクロール完全防止）
   ============================================ */

:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-accent: #ffeb3b;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #e5e5e5;
    --color-gray-600: #525252;
    --color-gray-900: #171717;
    --sidebar-width: 420px;
    --header-height: 80px;
    --mobile-padding: 16px;
    --tablet-padding: 24px;
    --desktop-padding: 32px;
}

/* スクリーンリーダー専用 */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* ============================================
   基本レイアウト - モバイルファースト
   ============================================ */

.single-column-responsive {
    background: var(--color-gray-50);
    min-height: 100vh;
    overflow-x: hidden;
    max-width: 100vw;
}

.column-layout-container {
    max-width: 1480px;
    margin: 0 auto;
    padding: var(--mobile-padding);
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    overflow-x: hidden;
}

/* タブレット */
@media (min-width: 768px) {
    .column-layout-container {
        padding: var(--tablet-padding);
        gap: 32px;
    }
}

/* デスクトップ */
@media (min-width: 1024px) {
    .column-layout-container {
        padding: var(--desktop-padding);
        grid-template-columns: 1fr var(--sidebar-width);
        align-items: start;
        gap: 40px;
    }
}

/* ============================================
   メインコンテンツ - レスポンシブ
   ============================================ */

.column-main-content {
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
    padding: var(--mobile-padding);
    width: 100%;
    max-width: 100%;
    overflow-wrap: break-word;
    word-wrap: break-word;
    overflow-x: hidden;
}

@media (min-width: 768px) {
    .column-main-content {
        border: 3px solid var(--color-primary);
        padding: var(--tablet-padding);
    }
}

@media (min-width: 1024px) {
    .column-main-content {
        padding: 40px 32px;
    }
}

/* ============================================
   ヘッダーセクション - レスポンシブ
   ============================================ */

.column-header-section {
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--color-gray-200);
}

@media (min-width: 768px) {
    .column-header-section {
        margin-bottom: 32px;
        padding-bottom: 24px;
    }
}

@media (min-width: 1024px) {
    .column-header-section {
        margin-bottom: 40px;
        padding-bottom: 32px;
    }
}

/* パンくずリスト - レスポンシブ */
.column-breadcrumb {
    margin-bottom: 16px;
}

.column-breadcrumb ol {
    display: flex;
    align-items: center;
    gap: 6px;
    list-style: none;
    font-size: 12px;
    color: var(--color-gray-600);
    flex-wrap: wrap;
    line-height: 1.6;
}

@media (min-width: 768px) {
    .column-breadcrumb {
        margin-bottom: 20px;
    }
    
    .column-breadcrumb ol {
        font-size: 14px;
        gap: 8px;
    }
}

.column-breadcrumb a {
    color: var(--color-gray-600);
    text-decoration: none;
    transition: color 0.2s;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.column-breadcrumb a:hover,
.column-breadcrumb a:focus {
    color: var(--color-primary);
    text-decoration: underline;
}

.column-breadcrumb i {
    font-size: 8px;
}

@media (min-width: 768px) {
    .column-breadcrumb i {
        font-size: 10px;
    }
}

/* バッジ - レスポンシブ */
.column-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

@media (min-width: 768px) {
    .column-badges {
        gap: 10px;
        margin-bottom: 20px;
    }
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
    border: 2px solid;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
}

@media (min-width: 768px) {
    .badge {
        gap: 6px;
        padding: 8px 16px;
        font-size: 14px;
    }
}

.badge:hover,
.badge:focus {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.badge i {
    font-size: 10px;
}

@media (min-width: 768px) {
    .badge i {
        font-size: 12px;
    }
}

.badge-category {
    background: var(--color-primary);
    color: var(--color-accent);
    border-color: var(--color-primary);
}

.badge-beginner {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.badge-intermediate {
    background: #f59e0b;
    color: white;
    border-color: #f59e0b;
}

.badge-advanced {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
}

/* タイトル - レスポンシブ */
.column-title {
    font-size: 22px;
    font-weight: 900;
    color: var(--color-primary);
    line-height: 1.4;
    margin: 0 0 16px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}

@media (min-width: 480px) {
    .column-title {
        font-size: 26px;
    }
}

@media (min-width: 768px) {
    .column-title {
        font-size: 32px;
        margin: 0 0 20px;
    }
}

@media (min-width: 1024px) {
    .column-title {
        font-size: 36px;
    }
}

/* メタ情報 - レスポンシブ */
.column-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 13px;
    color: var(--color-gray-600);
}

@media (min-width: 768px) {
    .column-meta {
        gap: 16px;
        font-size: 15px;
    }
}

@media (min-width: 1024px) {
    .column-meta {
        gap: 20px;
    }
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

@media (min-width: 768px) {
    .meta-item {
        gap: 8px;
    }
}

.meta-item i {
    color: var(--color-primary);
    font-size: 12px;
}

@media (min-width: 768px) {
    .meta-item i {
        font-size: 14px;
    }
}

/* アイキャッチ - レスポンシブ */
.column-thumbnail {
    margin: 24px 0;
    border: 2px solid var(--color-primary);
    overflow: hidden;
    max-width: 100%;
}

@media (min-width: 768px) {
    .column-thumbnail {
        margin: 32px 0;
    }
}

.column-thumbnail img {
    width: 100%;
    height: auto;
    display: block;
    max-width: 100%;
}

/* 対象読者ボックス - レスポンシブ */
.target-audience-box {
    background: var(--color-gray-50);
    border-left: 3px solid var(--color-primary);
    padding: 16px;
    margin: 24px 0;
    max-width: 100%;
    overflow-wrap: break-word;
}

@media (min-width: 768px) {
    .target-audience-box {
        border-left-width: 4px;
        padding: 20px;
        margin: 28px 0;
    }
}

@media (min-width: 1024px) {
    .target-audience-box {
        padding: 24px;
        margin: 32px 0;
    }
}

.box-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

@media (min-width: 768px) {
    .box-title {
        font-size: 18px;
        margin: 0 0 16px;
        gap: 10px;
    }
}

.audience-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

@media (min-width: 768px) {
    .audience-list {
        gap: 10px;
    }
}

.audience-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--color-gray-600);
    line-height: 1.6;
    overflow-wrap: break-word;
}

@media (min-width: 768px) {
    .audience-list li {
        gap: 10px;
        font-size: 15px;
    }
}

.audience-list i {
    color: var(--color-primary);
    flex-shrink: 0;
}

/* ============================================
   記事本文 - 完全レスポンシブ（スマホ横スクロール完全防止）
   ============================================ */

.column-content {
    font-size: 15px;
    line-height: 1.8;
    color: var(--color-gray-900);
    margin: 24px 0;
    word-wrap: break-word;
    overflow-wrap: break-word;
    overflow-x: hidden;
    max-width: 100%;
}

@media (min-width: 768px) {
    .column-content {
        font-size: 16px;
        line-height: 1.85;
        margin: 32px 0;
    }
}

@media (min-width: 1024px) {
    .column-content {
        font-size: 17px;
        line-height: 1.9;
        margin: 40px 0;
    }
}

/* 全要素の幅制限を強制 */
.column-content,
.column-content * {
    max-width: 100%;
    box-sizing: border-box;
}

.column-content h2 {
    font-size: 20px;
    font-weight: 700;
    margin: 32px 0 16px;
    padding-bottom: 10px;
    border-bottom: 3px solid var(--color-primary);
    word-wrap: break-word;
    overflow-wrap: break-word;
}

@media (min-width: 768px) {
    .column-content h2 {
        font-size: 24px;
        margin: 36px 0 18px;
        padding-bottom: 12px;
    }
}

@media (min-width: 1024px) {
    .column-content h2 {
        font-size: 26px;
        margin: 40px 0 20px;
    }
}

.column-content h3 {
    font-size: 18px;
    font-weight: 700;
    margin: 28px 0 14px;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

@media (min-width: 768px) {
    .column-content h3 {
        font-size: 20px;
        margin: 30px 0 15px;
    }
}

@media (min-width: 1024px) {
    .column-content h3 {
        font-size: 22px;
        margin: 32px 0 16px;
    }
}

.column-content p {
    margin: 16px 0;
    max-width: 100%;
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;
}

@media (min-width: 768px) {
    .column-content p {
        margin: 18px 0;
    }
}

@media (min-width: 1024px) {
    .column-content p {
        margin: 20px 0;
    }
}

.column-content ul,
.column-content ol {
    margin: 16px 0;
    padding-left: 24px;
    max-width: 100%;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

@media (min-width: 768px) {
    .column-content ul,
    .column-content ol {
        margin: 18px 0;
        padding-left: 26px;
    }
}

@media (min-width: 1024px) {
    .column-content ul,
    .column-content ol {
        margin: 20px 0;
        padding-left: 28px;
    }
}

.column-content li {
    margin: 8px 0;
    max-width: 100%;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

@media (min-width: 768px) {
    .column-content li {
        margin: 9px 0;
    }
}

@media (min-width: 1024px) {
    .column-content li {
        margin: 10px 0;
    }
}

/* 画像の完全レスポンシブ化 */
.column-content img {
    max-width: 100% !important;
    height: auto !important;
    display: block;
    margin: 20px auto;
    object-fit: contain;
}

/* 画像を含むfigure要素 */
.column-content figure {
    max-width: 100%;
    margin: 20px 0;
    overflow: hidden;
}

.column-content figure img {
    width: 100%;
    height: auto;
}

/* テーブルの完全レスポンシブ化 */
.column-content table {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 20px 0;
    font-size: 13px;
    border: 1px solid var(--color-gray-200);
    border-collapse: collapse;
}

@media (min-width: 768px) {
    .column-content table {
        display: table;
        font-size: 15px;
    }
}

.column-content table th,
.column-content table td {
    min-width: 100px;
    padding: 8px;
    white-space: nowrap;
    border: 1px solid var(--color-gray-200);
    text-align: left;
}

@media (min-width: 768px) {
    .column-content table th,
    .column-content table td {
        white-space: normal;
        padding: 12px;
    }
}

.column-content th {
    background: var(--color-gray-100);
    font-weight: 700;
}

/* テーブルをスクロール可能にするラッパー */
.column-content .table-wrapper {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin: 20px 0;
}

.column-content .table-wrapper table {
    margin: 0;
}

/* コードブロックの完全レスポンシブ化 */
.column-content pre {
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    white-space: pre;
    word-wrap: normal;
    word-break: normal;
    margin: 20px 0;
    padding: 12px;
    font-size: 12px;
    line-height: 1.5;
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-200);
    border-radius: 4px;
}

@media (min-width: 768px) {
    .column-content pre {
        padding: 16px;
        font-size: 14px;
        line-height: 1.6;
    }
}

.column-content code {
    max-width: 100%;
    overflow-wrap: break-word;
    word-break: break-all;
    background: var(--color-gray-100);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.9em;
    font-family: 'Courier New', Courier, monospace;
}

.column-content pre code {
    display: block;
    overflow-x: auto;
    white-space: pre;
    word-break: normal;
    background: transparent;
    padding: 0;
}

/* 埋め込みコンテンツ（iframe）の完全レスポンシブ化 */
.column-content iframe,
.column-content embed,
.column-content object,
.column-content video {
    max-width: 100% !important;
    height: auto !important;
}

/* レスポンシブ埋め込みラッパー（16:9） */
.column-content .embed-responsive {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 */
    height: 0;
    overflow: hidden;
    margin: 20px 0;
}

.column-content .embed-responsive iframe,
.column-content .embed-responsive embed,
.column-content .embed-responsive object,
.column-content .embed-responsive video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100% !important;
    height: 100% !important;
}

/* 引用ブロックの幅制限 */
.column-content blockquote {
    max-width: 100%;
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;
    border-left: 3px solid var(--color-primary);
    padding-left: 16px;
    margin: 20px 0;
    font-style: italic;
    color: var(--color-gray-600);
}

@media (min-width: 768px) {
    .column-content blockquote {
        border-left-width: 4px;
        padding-left: 20px;
    }
}

/* 長いURL・テキストの折り返し */
.column-content a {
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-all;
}

/* 水平線の幅制限 */
.column-content hr {
    max-width: 100%;
    margin: 24px 0;
    border: none;
    border-top: 2px solid var(--color-gray-200);
}

/* WordPress Gutenbergブロックの対応 */
.column-content .wp-block-image,
.column-content .wp-block-embed,
.column-content .wp-block-video,
.column-content .wp-block-audio {
    max-width: 100%;
    margin: 20px 0;
}

.column-content .wp-block-image img {
    width: 100%;
    height: auto;
}

/* WordPressギャラリーの対応 */
.column-content .wp-block-gallery,
.column-content .blocks-gallery-grid {
    max-width: 100%;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
    margin: 20px 0;
}

@media (min-width: 768px) {
    .column-content .wp-block-gallery,
    .column-content .blocks-gallery-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
}

/* カラムブロックの対応 */
.column-content .wp-block-columns {
    display: block;
    max-width: 100%;
}

@media (min-width: 768px) {
    .column-content .wp-block-columns {
        display: flex;
        gap: 20px;
    }
}

.column-content .wp-block-column {
    max-width: 100%;
    flex: 1;
    margin-bottom: 20px;
}

@media (min-width: 768px) {
    .column-content .wp-block-column {
        margin-bottom: 0;
    }
}

/* ボタンブロックの対応 */
.column-content .wp-block-button {
    max-width: 100%;
    margin: 20px 0;
}

.column-content .wp-block-button__link {
    display: inline-block;
    max-width: 100%;
    padding: 12px 24px;
    text-align: center;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

/* 引用ブロックの対応 */
.column-content .wp-block-quote {
    max-width: 100%;
    margin: 20px 0;
    padding-left: 16px;
    border-left: 3px solid var(--color-primary);
}

/* プルクォートの対応 */
.column-content .wp-block-pullquote {
    max-width: 100%;
    margin: 24px 0;
    padding: 20px;
    border: 2px solid var(--color-gray-200);
}

/* スペーサーの対応 */
.column-content .wp-block-spacer {
    max-width: 100%;
}

/* セパレーターの対応 */
.column-content .wp-block-separator {
    max-width: 100%;
    margin: 24px auto;
}

/* カバーブロックの対応 */
.column-content .wp-block-cover {
    max-width: 100%;
    margin: 20px 0;
    min-height: 300px;
}

@media (min-width: 768px) {
    .column-content .wp-block-cover {
        min-height: 400px;
    }
}

/* メディア＆テキストブロックの対応 */
.column-content .wp-block-media-text {
    display: block;
    max-width: 100%;
    margin: 20px 0;
}

@media (min-width: 768px) {
    .column-content .wp-block-media-text {
        display: grid;
        grid-template-columns: 50% 1fr;
        gap: 20px;
        align-items: center;
    }
}

/* グループブロックの対応 */
.column-content .wp-block-group {
    max-width: 100%;
    margin: 20px 0;
}

/* 横スクロールの完全防止 */
@media (max-width: 767px) {
    body {
        overflow-x: hidden;
        max-width: 100vw;
    }
    
    .column-content > * {
        max-width: 100%;
    }
}

/* 非常に長い単語の強制改行 */
.column-content {
    overflow-wrap: break-word;
    word-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
}

/* 日本語の禁則処理 */
.column-content {
    line-break: strict;
}

/* 英数字の長い文字列の処理 */
@media (max-width: 767px) {
    .column-content {
        word-break: break-all;
    }
}

@media (min-width: 768px) {
    .column-content {
        word-break: normal;
    }
}

/* ============================================
   CTAボックス - レスポンシブ
   ============================================ */

.gus-cta-section {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #ffffff;
    padding: 40px 0;
    margin: 32px calc(-1 * var(--mobile-padding));
    position: relative;
    overflow: hidden;
    max-width: calc(100% + 2 * var(--mobile-padding));
}

@media (min-width: 768px) {
    .gus-cta-section {
        padding: 56px 0;
        margin: 40px calc(-1 * var(--tablet-padding));
        max-width: calc(100% + 2 * var(--tablet-padding));
    }
}

@media (min-width: 1024px) {
    .gus-cta-section {
        padding: 64px 0;
        margin: 48px -32px;
        max-width: calc(100% + 64px);
    }
}

.gus-cta-section::before,
.gus-cta-section::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
}

@media (min-width: 768px) {
    .gus-cta-section::before,
    .gus-cta-section::after {
        height: 4px;
    }
}

.gus-cta-section::before {
    top: 0;
}

.gus-cta-section::after {
    bottom: 0;
}

.gus-cta-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--mobile-padding);
}

@media (min-width: 768px) {
    .gus-cta-container {
        padding: 0 var(--tablet-padding);
    }
}

@media (min-width: 1024px) {
    .gus-cta-container {
        padding: 0 var(--desktop-padding);
    }
}

.gus-cta-content {
    text-align: center;
}

.gus-cta-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    background: rgba(255, 215, 0, 0.1);
    border-radius: 50%;
    margin-bottom: 20px;
    color: #FFD700;
}

@media (min-width: 768px) {
    .gus-cta-icon {
        width: 64px;
        height: 64px;
        margin-bottom: 24px;
    }
}

@media (min-width: 1024px) {
    .gus-cta-icon {
        width: 72px;
        height: 72px;
    }
}

.gus-cta-icon svg {
    width: 32px;
    height: 32px;
}

@media (min-width: 768px) {
    .gus-cta-icon svg {
        width: 40px;
        height: 40px;
    }
}

@media (min-width: 1024px) {
    .gus-cta-icon svg {
        width: 48px;
        height: 48px;
    }
}

.gus-cta-title {
    font-size: 1.375rem;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 16px;
    color: #ffffff;
}

@media (min-width: 768px) {
    .gus-cta-title {
        font-size: 1.75rem;
        margin-bottom: 20px;
    }
}

@media (min-width: 1024px) {
    .gus-cta-title {
        font-size: 2rem;
        margin-bottom: 24px;
    }
}

.gus-cta-description {
    font-size: 0.9375rem;
    line-height: 1.6;
    margin-bottom: 32px;
    color: rgba(255, 255, 255, 0.9);
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

@media (min-width: 768px) {
    .gus-cta-description {
        font-size: 1rem;
        margin-bottom: 40px;
    }
}

@media (min-width: 1024px) {
    .gus-cta-description {
        font-size: 1.125rem;
        margin-bottom: 48px;
    }
}

.gus-cta-buttons {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    max-width: 900px;
    margin: 0 auto;
}

@media (min-width: 640px) {
    .gus-cta-buttons {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (min-width: 1024px) {
    .gus-cta-buttons {
        gap: 24px;
    }
}

.gus-cta-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 18px 20px;
    font-size: 0.9375rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-height: 70px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

@media (min-width: 768px) {
    .gus-cta-btn {
        gap: 14px;
        padding: 20px 24px;
        font-size: 1rem;
        min-height: 80px;
    }
}

@media (min-width: 1024px) {
    .gus-cta-btn {
        gap: 16px;
        padding: 24px 32px;
        min-height: 90px;
    }
}

.gus-cta-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.gus-cta-btn:hover::before {
    left: 100%;
}

.gus-cta-btn svg {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    transition: transform 0.3s ease;
}

@media (min-width: 768px) {
    .gus-cta-btn svg {
        width: 22px;
        height: 22px;
    }
}

@media (min-width: 1024px) {
    .gus-cta-btn svg {
        width: 24px;
        height: 24px;
    }
}

.gus-cta-btn span {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
    text-align: left;
}

.gus-cta-btn strong {
    font-size: 1rem;
    font-weight: 700;
    display: block;
}

@media (min-width: 768px) {
    .gus-cta-btn strong {
        font-size: 1.0625rem;
    }
}

@media (min-width: 1024px) {
    .gus-cta-btn strong {
        font-size: 1.125rem;
    }
}

.gus-cta-btn small {
    font-size: 0.8125rem;
    font-weight: 400;
    opacity: 0.9;
    display: block;
}

@media (min-width: 768px) {
    .gus-cta-btn small {
        font-size: 0.875rem;
    }
}

.gus-cta-btn-primary {
    background: #000000;
    color: #ffffff;
    border: 2px solid #FFD700;
}

.gus-cta-btn-primary:hover {
    background: #FFD700;
    color: #000000;
    border-color: #FFD700;
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(255, 215, 0, 0.4);
}

.gus-cta-btn-primary:hover svg {
    transform: scale(1.1) rotate(5deg);
}

.gus-cta-btn-secondary {
    background: #ffffff;
    color: #000000;
    border: 2px solid #e5e5e5;
}

.gus-cta-btn-secondary:hover {
    background: #000000;
    color: #ffffff;
    border-color: #000000;
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
}

.gus-cta-btn-secondary:hover svg {
    transform: scale(1.1);
}

/* ============================================
   タグ - レスポンシブ
   ============================================ */

.column-tags {
    margin: 32px 0;
    padding: 20px;
    background: var(--color-gray-50);
    border: 2px solid var(--color-gray-200);
    max-width: 100%;
    overflow-wrap: break-word;
}

@media (min-width: 768px) {
    .column-tags {
        margin: 36px 0;
        padding: 24px;
    }
}

@media (min-width: 1024px) {
    .column-tags {
        margin: 40px 0;
    }
}

.tags-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

@media (min-width: 768px) {
    .tags-title {
        font-size: 18px;
        margin: 0 0 16px;
        gap: 10px;
    }
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

@media (min-width: 768px) {
    .tags-list {
        gap: 10px;
    }
}

.tag-link {
    display: inline-block;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-primary);
    background: var(--color-secondary);
    border: 1px solid var(--color-primary);
    text-decoration: none;
    transition: all 0.2s;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

@media (min-width: 768px) {
    .tag-link {
        padding: 8px 16px;
        font-size: 14px;
    }
}

.tag-link:hover,
.tag-link:focus {
    background: var(--color-accent);
    transform: translateY(-2px);
}

/* ============================================
   シェアボタン - レスポンシブ
   ============================================ */

.column-share {
    margin: 32px 0;
    padding: 20px;
    background: var(--color-primary);
    color: var(--color-secondary);
    text-align: center;
    max-width: 100%;
}

@media (min-width: 768px) {
    .column-share {
        margin: 36px 0;
        padding: 24px;
    }
}

@media (min-width: 1024px) {
    .column-share {
        margin: 40px 0;
        padding: 28px;
    }
}

.share-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 16px;
}

@media (min-width: 768px) {
    .share-title {
        font-size: 18px;
        margin: 0 0 20px;
    }
}

.share-buttons {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

@media (min-width: 768px) {
    .share-buttons {
        gap: 16px;
    }
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 600;
    border: 2px solid var(--color-secondary);
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
}

@media (min-width: 768px) {
    .share-btn {
        gap: 10px;
        padding: 12px 24px;
        font-size: 15px;
    }
}

.share-text {
    display: none;
}

@media (min-width: 480px) {
    .share-text {
        display: inline;
    }
}

.share-twitter {
    background: #1DA1F2;
    color: white;
    border-color: #1DA1F2;
}

.share-facebook {
    background: #4267B2;
    color: white;
    border-color: #4267B2;
}

.share-line {
    background: #00B900;
    color: white;
    border-color: #00B900;
}

.share-btn:hover,
.share-btn:focus {
    transform: translateY(-2px);
    opacity: 0.9;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* ============================================
   関連記事 - レスポンシブ
   ============================================ */

.related-columns {
    margin: 40px 0 0;
    padding: 32px 0 0;
    border-top: 3px solid var(--color-primary);
}

@media (min-width: 768px) {
    .related-columns {
        margin: 48px 0 0;
        padding: 36px 0 0;
    }
}

@media (min-width: 1024px) {
    .related-columns {
        margin: 56px 0 0;
        padding: 40px 0 0;
    }
}

.related-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

@media (min-width: 768px) {
    .related-title {
        font-size: 20px;
        margin: 0 0 24px;
        gap: 10px;
    }
}

@media (min-width: 1024px) {
    .related-title {
        font-size: 22px;
        margin: 0 0 28px;
    }
}

.related-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

@media (min-width: 640px) {
    .related-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
}

@media (min-width: 1024px) {
    .related-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* ============================================
   サイドバー - デスクトップのみ
   ============================================ */

.column-sidebar {
    display: none;
}

@media (min-width: 1024px) {
    .column-sidebar {
        display: flex;
        flex-direction: column;
        gap: 28px;
        position: sticky;
        top: calc(var(--header-height) + 20px);
        overflow-y: auto;
        overflow-x: hidden;
        align-self: flex-start;
        scrollbar-width: thin;
        scrollbar-color: var(--color-gray-200) transparent;
        max-width: 100%;
    }
    
    .column-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .column-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .column-sidebar::-webkit-scrollbar-thumb {
        background-color: var(--color-gray-200);
        border-radius: 3px;
    }
    
    .column-sidebar::-webkit-scrollbar-thumb:hover {
        background-color: var(--color-gray-600);
    }
}

.sidebar-card {
    background: var(--color-secondary);
    border: 3px solid var(--color-primary);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: box-shadow 0.2s;
    max-width: 100%;
    overflow: hidden;
}

.sidebar-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.sidebar-ad-space {
    background: #FAFAFA !important;
    border: 2px dashed #E5E5E5 !important;
    padding: 16px !important;
    min-height: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.sidebar-ad-space:hover {
    border-color: #CCCCCC !important;
    background: #F5F5F5 !important;
}

.sidebar-ad-space:empty {
    display: none;
}

.card-header {
    background: var(--color-primary);
    color: var(--color-accent);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-header h2 {
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    color: var(--color-accent);
}

.card-header i,
.card-header svg {
    font-size: 20px;
    color: var(--color-accent);
}

.card-header-grants {
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    border-bottom: 3px solid var(--color-accent);
}

.card-header-grants h2 {
    color: #ffffff;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.card-header-grants svg {
    color: var(--color-accent);
    filter: drop-shadow(0 0 2px rgba(255, 235, 59, 0.5));
}

.card-header-ai {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    border-bottom: 3px solid #60a5fa;
}

.card-header-ai h2 {
    color: #ffffff;
    font-weight: 800;
    letter-spacing: 0.5px;
}

.card-header-ai i {
    color: #60a5fa;
    filter: drop-shadow(0 0 2px rgba(96, 165, 250, 0.5));
}

.card-body {
    padding: 24px;
}

/* AIチャットカード - デスクトップ専用 */
.ai-chat-card {
    display: none;
}

@media (min-width: 1024px) {
    .ai-chat-card {
        display: block;
    }
}

.ai-chat-intro {
    margin-bottom: 16px;
    padding: 12px;
    background: #eff6ff;
    border-left: 3px solid #2563eb;
    border-radius: 4px;
}

.ai-chat-intro p {
    margin: 0;
    font-size: 14px;
    color: #1e40af;
    line-height: 1.5;
}

.desktop-ai-chat-messages {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 8px;
    scrollbar-width: thin;
    scrollbar-color: var(--color-gray-200) transparent;
}

.desktop-ai-chat-messages::-webkit-scrollbar {
    width: 6px;
}

.desktop-ai-chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.desktop-ai-chat-messages::-webkit-scrollbar-thumb {
    background-color: var(--color-gray-200);
    border-radius: 3px;
}

.desktop-ai-chat-messages::-webkit-scrollbar-thumb:hover {
    background-color: var(--color-gray-600);
}

.desktop-ai-input-container {
    display: flex;
    gap: 8px;
}

.desktop-ai-input-container textarea {
    flex: 1;
    padding: 10px 12px;
    border: 2px solid var(--color-gray-200);
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    line-height: 1.5;
    transition: border-color 0.2s;
}

.desktop-ai-input-container textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.desktop-ai-send-btn {
    padding: 10px 16px;
    background: #2563eb;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.desktop-ai-send-btn:hover,
.desktop-ai-send-btn:focus {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.desktop-ai-send-btn:active {
    transform: translateY(0);
}

.toc-nav {
    font-size: 15px;
}

.toc-nav ul {
    list-style: none;
    padding: 0;
}

.toc-nav li {
    margin: 10px 0;
}

.toc-nav a {
    color: var(--color-gray-600);
    text-decoration: none;
    display: block;
    padding: 6px 0;
    transition: color 0.2s;
    line-height: 1.6;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.toc-nav a:hover,
.toc-nav a:focus {
    color: var(--color-primary);
    text-decoration: underline;
}

.related-grants-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.related-grant-item {
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    transition: all 0.2s ease;
    overflow: hidden;
}

.related-grant-item:hover {
    border-color: #0073aa;
    box-shadow: 0 2px 8px rgba(0, 115, 170, 0.1);
}

.related-grant-link {
    display: block;
    padding: 12px;
    text-decoration: none;
    color: inherit;
}

.related-grant-title {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    margin: 0 0 8px 0;
    color: #1a1a1a;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.related-grant-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
}

.related-grant-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.grant-amount {
    color: #00a32a;
    font-weight: 600;
}

.grant-deadline {
    color: #d63638;
}

.grant-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.status-open {
    background: #e8f5e9;
    color: #1b5e20;
}

.view-all-grants {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 16px;
    padding: 10px;
    background: #f0f0f0;
    border-radius: 6px;
    text-decoration: none;
    color: #0073aa;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.view-all-grants:hover {
    background: #0073aa;
    color: #fff;
}

.popular-list {
    list-style: none;
}

.popular-list li {
    margin: 14px 0;
}

.popular-list a {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    text-decoration: none;
    color: var(--color-gray-900);
    transition: color 0.2s;
}

.popular-list a:hover,
.popular-list a:focus {
    color: var(--color-primary);
}

.popular-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: var(--color-primary);
    color: var(--color-accent);
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
}

.popular-title {
    flex: 1;
    font-size: 15px;
    line-height: 1.6;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

/* ============================================
   モバイル用補助金情報
   ============================================ */

.mobile-related-grants {
    display: block;
    margin: 32px 0;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    max-width: 100%;
    overflow: hidden;
}

@media (min-width: 768px) {
    .mobile-related-grants {
        margin: 36px 0;
        padding: 24px;
    }
}

@media (min-width: 1024px) {
    .mobile-related-grants {
        display: none;
    }
}

.mobile-related-grants .section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 16px 0;
    color: #1a1a1a;
}

@media (min-width: 768px) {
    .mobile-related-grants .section-title {
        gap: 10px;
        font-size: 20px;
        margin: 0 0 20px 0;
    }
}

.mobile-related-grants .section-title svg {
    flex-shrink: 0;
    color: #0073aa;
    width: 20px;
    height: 20px;
}

@media (min-width: 768px) {
    .mobile-related-grants .section-title svg {
        width: 24px;
        height: 24px;
    }
}

.mobile-grants-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

@media (min-width: 640px) {
    .mobile-grants-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 20px;
    }
}

.mobile-grant-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.mobile-grant-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.mobile-grant-link {
    display: block;
    padding: 16px;
    text-decoration: none;
    color: inherit;
}

@media (min-width: 768px) {
    .mobile-grant-link {
        padding: 20px;
    }
}

.mobile-grant-badge {
    display: inline-block;
    padding: 4px 10px;
    background: #0073aa;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    border-radius: 4px;
    margin-bottom: 10px;
}

.mobile-grant-title {
    font-size: 15px;
    font-weight: 700;
    line-height: 1.4;
    margin: 0 0 12px 0;
    color: #1a1a1a;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

@media (min-width: 768px) {
    .mobile-grant-title {
        font-size: 16px;
    }
}

.mobile-grant-info {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 12px;
    margin-bottom: 12px;
}

@media (min-width: 768px) {
    .mobile-grant-info {
        gap: 12px;
        font-size: 13px;
    }
}

.mobile-grant-amount {
    color: #00a32a;
    font-weight: 600;
}

.mobile-grant-deadline {
    color: #d63638;
}

.mobile-grant-cta {
    display: inline-flex;
    align-items: center;
    color: #0073aa;
    font-size: 13px;
    font-weight: 600;
}

@media (min-width: 768px) {
    .mobile-grant-cta {
        font-size: 14px;
    }
}

.mobile-view-all-grants {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 12px;
    background: #0073aa;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.2s ease;
}

@media (min-width: 768px) {
    .mobile-view-all-grants {
        gap: 8px;
        padding: 14px;
        font-size: 15px;
    }
}

.mobile-view-all-grants:hover {
    background: #005177;
}

.mobile-view-all-grants svg {
    width: 16px;
    height: 16px;
}

@media (min-width: 768px) {
    .mobile-view-all-grants svg {
        width: 18px;
        height: 18px;
    }
}

/* ============================================
   モバイルパネル - 完全レスポンシブ
   ============================================ */

.gus-mobile-toc-cta {
    display: flex;
    position: fixed;
    bottom: 70px;
    right: 12px;
    z-index: 999;
    background: var(--color-gray-900);
    color: var(--color-secondary);
    border: none;
    border-radius: 50%;
    width: 56px;
    height: 56px;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    align-items: center;
    justify-content: center;
}

@media (min-width: 768px) {
    .gus-mobile-toc-cta {
        bottom: 80px;
        right: 16px;
        width: 60px;
        height: 60px;
    }
}

@media (min-width: 1024px) {
    .gus-mobile-toc-cta {
        display: flex;
    }
}

.gus-mobile-toc-cta:hover,
.gus-mobile-toc-cta:focus {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
}

.gus-mobile-toc-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
}

.gus-mobile-toc-icon-toc {
    font-size: 16px;
    line-height: 1;
}

@media (min-width: 768px) {
    .gus-mobile-toc-icon-toc {
        font-size: 18px;
    }
}

.gus-mobile-toc-icon-ai {
    font-size: 10px;
    font-weight: 700;
    line-height: 1;
}

@media (min-width: 768px) {
    .gus-mobile-toc-icon-ai {
        font-size: 11px;
    }
}

.gus-mobile-toc-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gus-mobile-toc-overlay.active {
    display: block;
    opacity: 1;
}

.gus-mobile-toc-panel {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--color-secondary);
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.25);
    z-index: 1001;
    max-height: 70vh;
    display: flex;
    flex-direction: column;
    transform: translateY(100%);
    transition: transform 0.3s ease;
    max-width: 100vw;
    overflow: hidden;
}

@media (min-width: 768px) {
    .gus-mobile-toc-panel {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        max-height: 75vh;
    }
}

.gus-mobile-toc-panel.active {
    transform: translateY(0);
}

.gus-mobile-toc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 2px solid var(--color-gray-200);
}

@media (min-width: 768px) {
    .gus-mobile-toc-header {
        padding: 20px 24px;
    }
}

.gus-mobile-toc-title {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: var(--color-gray-900);
}

@media (min-width: 768px) {
    .gus-mobile-toc-title {
        font-size: 19px;
    }
}

.gus-mobile-toc-close {
    background: transparent;
    border: none;
    color: var(--color-gray-600);
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

@media (min-width: 768px) {
    .gus-mobile-toc-close {
        font-size: 26px;
        width: 36px;
        height: 36px;
    }
}

.gus-mobile-toc-close:hover,
.gus-mobile-toc-close:focus {
    color: var(--color-primary);
}

.gus-mobile-nav-tabs {
    display: flex;
    border-bottom: 2px solid var(--color-gray-200);
    background: var(--color-gray-50);
}

.gus-mobile-nav-tab {
    flex: 1;
    padding: 12px 16px;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 15px;
    font-weight: 600;
    color: var(--color-gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

@media (min-width: 768px) {
    .gus-mobile-nav-tab {
        padding: 14px 20px;
        font-size: 16px;
        gap: 8px;
    }
}

.gus-mobile-nav-tab:hover,
.gus-mobile-nav-tab:focus {
    background: var(--color-gray-100);
}

.gus-mobile-nav-tab.active {
    color: var(--color-primary);
    background: var(--color-secondary);
    border-bottom-color: var(--color-primary);
}

.gus-mobile-nav-content {
    display: none;
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    max-width: 100%;
}

@media (min-width: 768px) {
    .gus-mobile-nav-content {
        padding: 24px;
    }
}

.gus-mobile-nav-content.active {
    display: flex;
    flex-direction: column;
}

.gus-ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

@media (min-width: 768px) {
    .gus-ai-chat-messages {
        margin-bottom: 20px;
        gap: 14px;
    }
}

.ai-message {
    display: flex;
    gap: 10px;
    max-width: 100%;
}

@media (min-width: 768px) {
    .ai-message {
        gap: 12px;
    }
}

.ai-message-assistant {
    align-self: flex-start;
}

.ai-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--color-primary);
    color: var(--color-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}

@media (min-width: 768px) {
    .ai-avatar {
        width: 36px;
        height: 36px;
        font-size: 18px;
    }
}

.ai-content {
    background: var(--color-gray-100);
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.6;
    max-width: 80%;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

@media (min-width: 768px) {
    .ai-content {
        padding: 12px 16px;
        font-size: 15px;
        line-height: 1.7;
    }
}

.gus-ai-input-container {
    display: flex;
    gap: 8px;
    padding-top: 12px;
    border-top: 2px solid var(--color-gray-200);
}

@media (min-width: 768px) {
    .gus-ai-input-container {
        gap: 10px;
        padding-top: 16px;
    }
}

.gus-ai-input-container textarea {
    flex: 1;
    padding: 10px 12px;
    border: 2px solid var(--color-gray-200);
    border-radius: 10px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    line-height: 1.5;
    max-width: 100%;
}

@media (min-width: 768px) {
    .gus-ai-input-container textarea {
        padding: 12px 14px;
        font-size: 15px;
    }
}

.gus-ai-input-container textarea:focus {
    outline: none;
    border-color: var(--color-primary);
}

.gus-ai-send-btn {
    padding: 10px 14px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

@media (min-width: 768px) {
    .gus-ai-send-btn {
        padding: 12px 18px;
        font-size: 15px;
        gap: 8px;
    }
}

.gus-ai-send-btn:hover,
.gus-ai-send-btn:focus {
    background: var(--color-gray-900);
}

.send-text {
    display: none;
}

@media (min-width: 480px) {
    .send-text {
        display: inline;
    }
}

.gus-mobile-toc-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

@media (min-width: 768px) {
    .gus-mobile-toc-list {
        gap: 6px;
    }
}

.gus-mobile-toc-list ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gus-mobile-toc-list li {
    margin: 0;
}

.gus-mobile-toc-list a {
    display: block;
    padding: 10px 12px;
    font-size: 14px;
    color: var(--color-gray-900);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
    line-height: 1.6;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

@media (min-width: 768px) {
    .gus-mobile-toc-list a {
        padding: 12px 14px;
        font-size: 15px;
    }
}

.gus-mobile-toc-list a:hover,
.gus-mobile-toc-list a:focus {
    background: var(--color-gray-50);
    border-left-color: var(--color-primary);
}

.gus-mobile-toc-list li[data-level="2"] a {
    padding-left: 24px;
    font-size: 13px;
}

@media (min-width: 768px) {
    .gus-mobile-toc-list li[data-level="2"] a {
        padding-left: 28px;
        font-size: 14px;
    }
}

/* ============================================
   タッチデバイス最適化
   ============================================ */

@media (hover: none) and (pointer: coarse) {
    /* タップ領域を広げる */
    .badge,
    .tag-link,
    .share-btn,
    .gus-cta-btn,
    .mobile-grant-link,
    .related-grant-link,
    .gus-mobile-nav-tab,
    .gus-mobile-toc-list a {
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* ホバー効果を無効化 */
    .badge:hover,
    .tag-link:hover,
    .share-btn:hover,
    .gus-cta-btn:hover,
    .mobile-grant-card:hover,
    .related-grant-item:hover,
    .sidebar-card:hover {
        transform: none;
    }
    
    /* タップ時のフィードバック */
    .badge:active,
    .tag-link:active,
    .share-btn:active,
    .gus-cta-btn:active,
    .mobile-grant-link:active,
    .related-grant-link:active {
        opacity: 0.8;
        transform: scale(0.98);
    }
}

/* ============================================
   アクセシビリティ
   ============================================ */

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

@media (prefers-contrast: high) {
    .card-header {
        border: 2px solid var(--color-accent);
    }
    
    .card-header-grants {
        border: 3px solid var(--color-accent);
    }
    
    .badge,
    .tag-link,
    .share-btn {
        border-width: 3px;
    }
}

/* ============================================
   印刷スタイル
   ============================================ */

@media print {
    .column-sidebar,
    .gus-mobile-toc-cta,
    .gus-mobile-toc-overlay,
    .gus-mobile-toc-panel,
    .column-share,
    .related-columns,
    .mobile-related-grants,
    .gus-cta-section,
    .sidebar-ad-space {
        display: none !important;
    }
    
    .column-layout-container {
        grid-template-columns: 1fr;
        max-width: 100%;
    }
    
    .column-main-content {
        border: none;
        padding: 0;
    }
    
    .column-content {
        font-size: 12pt;
        line-height: 1.6;
    }
    
    .column-content a {
        color: #000;
        text-decoration: underline;
    }
    
    .column-content a[href]:after {
        content: " (" attr(href) ")";
        font-size: 0.9em;
        color: #666;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // 目次自動生成
    function generateTOC() {
        const content = document.querySelector('.column-content');
        const tocNav = document.getElementById('toc-nav');
        const mobileTocList = document.getElementById('mobileTocList');
        
        if (!content) return;
        
        const headings = content.querySelectorAll('h2, h3');
        if (headings.length === 0) {
            if (tocNav) {
                tocNav.innerHTML = '<p style="font-size: 15px; color: #999; padding: 12px 0;">目次がありません</p>';
            }
            if (mobileTocList) {
                mobileTocList.innerHTML = '<p style="font-size: 15px; color: #999; padding: 24px;">目次がありません</p>';
            }
            return;
        }
        
        if (tocNav) {
            let tocHTML = '<ul>';
            headings.forEach((heading, index) => {
                const id = 'heading-' + index;
                heading.id = id;
                
                const level = heading.tagName === 'H2' ? 1 : 2;
                const indent = level === 2 ? 'padding-left: 20px;' : '';
                
                tocHTML += `<li style="${indent}"><a href="#${id}">${heading.textContent}</a></li>`;
            });
            tocHTML += '</ul>';
            tocNav.innerHTML = tocHTML;
        }
        
        if (mobileTocList) {
            let mobileTocHTML = '<ul>';
            headings.forEach((heading, index) => {
                const id = heading.id || 'heading-' + index;
                heading.id = id;
                
                const level = heading.tagName === 'H2' ? 1 : 2;
                
                mobileTocHTML += `<li data-level="${level}"><a href="#${id}">${heading.textContent}</a></li>`;
            });
            mobileTocHTML += '</ul>';
            mobileTocList.innerHTML = mobileTocHTML;
            
            mobileTocList.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    closeMobilePanel();
                });
            });
        }
    }
    
    // テーブルを自動的にラッパーで囲む
    function wrapTables() {
        const tables = document.querySelectorAll('.column-content > table');
        tables.forEach(table => {
            if (!table.parentElement.classList.contains('table-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-wrapper';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
    }
    
    // iframeを自動的にレスポンシブラッパーで囲む
    function wrapEmbeds() {
        const iframes = document.querySelectorAll('.column-content iframe:not(.embed-responsive iframe)');
        iframes.forEach(iframe => {
            // YouTubeやVimeoなどの埋め込み動画
            if (iframe.src.includes('youtube.com') || 
                iframe.src.includes('vimeo.com') ||
                iframe.src.includes('dailymotion.com')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'embed-responsive';
                iframe.parentNode.insertBefore(wrapper, iframe);
                wrapper.appendChild(iframe);
            }
        });
    }
    
    // 横スクロール検知（デバッグ用）
    function detectHorizontalScroll() {
        const content = document.querySelector('.column-content');
        if (!content) return;
        
        const elements = content.querySelectorAll('*');
        elements.forEach(el => {
            if (el.scrollWidth > el.clientWidth) {
                console.warn('[Horizontal Scroll Detected]', el.tagName, el.className, 'scrollWidth:', el.scrollWidth, 'clientWidth:', el.clientWidth);
            }
        });
    }
    
    // AI送信処理（モバイル）
    function initMobileAI() {
        const sendBtn = document.getElementById('mobileAiSend');
        const input = document.getElementById('mobileAiInput');
        const container = document.getElementById('mobileAiMessages');
        
        if (!sendBtn || !input || !container) return;
        
        sendBtn.addEventListener('click', function() {
            const question = input.value.trim();
            if (!question) return;
            
            sendAIMessage(question, container, input);
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendBtn.click();
            }
        });
    }
    
    // AI送信処理（デスクトップ）
    function initDesktopAI() {
        const sendBtn = document.getElementById('desktopAiSend');
        const input = document.getElementById('desktopAiInput');
        const container = document.getElementById('desktopAiMessages');
        
        if (!sendBtn || !input || !container) return;
        
        sendBtn.addEventListener('click', function() {
            const question = input.value.trim();
            if (!question) return;
            
            sendAIMessage(question, container, input);
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendBtn.click();
            }
        });
    }
    
    // AI共通送信処理
    function sendAIMessage(question, container, input) {
        const userMsg = document.createElement('div');
        userMsg.className = 'ai-message';
        userMsg.innerHTML = `
            <div class="ai-avatar" style="background: var(--color-accent); color: var(--color-primary);" aria-hidden="true">
                <i class="fas fa-user"></i>
            </div>
            <div class="ai-content" style="background: var(--color-primary); color: var(--color-secondary);">
                ${escapeHtml(question)}
            </div>
        `;
        container.appendChild(userMsg);
        
        input.value = '';
        
        const loadingMsg = document.createElement('div');
        loadingMsg.className = 'ai-message ai-message-assistant ai-loading';
        loadingMsg.innerHTML = `
            <div class="ai-avatar" aria-hidden="true">
                <i class="fas fa-robot"></i>
            </div>
            <div class="ai-content">
                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i> 考え中...
            </div>
        `;
        container.appendChild(loadingMsg);
        container.scrollTop = container.scrollHeight;
        
        callAIAPI(question)
            .then(response => {
                loadingMsg.remove();
                
                const aiMsg = document.createElement('div');
                aiMsg.className = 'ai-message ai-message-assistant';
                aiMsg.innerHTML = `
                    <div class="ai-avatar" aria-hidden="true">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="ai-content">
                        ${formatAIResponse(response)}
                    </div>
                `;
                container.appendChild(aiMsg);
                container.scrollTop = container.scrollHeight;
            })
            .catch(error => {
                loadingMsg.remove();
                
                const errorMsg = document.createElement('div');
                errorMsg.className = 'ai-message ai-message-assistant';
                errorMsg.innerHTML = `
                    <div class="ai-avatar" aria-hidden="true">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="ai-content" style="color: #dc2626;">
                        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i> 
                        申し訳ございません。エラーが発生しました。もう一度お試しください。
                    </div>
                `;
                container.appendChild(errorMsg);
                container.scrollTop = container.scrollHeight;
                
                console.error('[AI Error]', error);
            });
    }
    
    // AI API呼び出し
    function callAIAPI(question) {
        const content = document.querySelector('.column-content');
        const title = document.querySelector('.column-title');
        const contentText = content ? content.innerText : '';
        const titleText = title ? title.innerText : '';
        
        const apiUrl = window.wpApiSettings ? window.wpApiSettings.root + 'gi-api/v1/ai-chat' : '/wp-json/gi-api/v1/ai-chat';
        const nonce = window.wpApiSettings && window.wpApiSettings.nonce ? window.wpApiSettings.nonce : '';
        
        return fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce
            },
            body: JSON.stringify({
                question: question,
                context: {
                    title: titleText,
                    content: contentText.substring(0, 3000),
                    type: 'column'
                }
            })
        })
        .then(response => {
            if (!response.ok) {
                return callAIAPI_AJAX(question, titleText, contentText);
            }
            return response.json();
        })
        .then(data => {
            if (typeof data === 'string') {
                return data;
            }
            if (data && data.success && data.data && (data.data.answer || data.data.response)) {
                return data.data.answer || data.data.response;
            } else if (data && typeof data === 'object' && (data.answer || data.response)) {
                return data.answer || data.response;
            } else {
                return generateFallbackResponse(question);
            }
        })
        .catch(error => {
            return callAIAPI_AJAX(question, titleText, contentText);
        });
    }
        // AJAX Fallback
    function callAIAPI_AJAX(question, titleText, contentText) {
        const ajaxUrl = (window.ajaxSettings && window.ajaxSettings.ajaxurl) || window.ajaxurl || '/wp-admin/admin-ajax.php';
        const nonce = window.wpApiSettings && window.wpApiSettings.nonce ? window.wpApiSettings.nonce : '';
        
        const formData = new FormData();
        formData.append('action', 'gi_contextual_chat');
        formData.append('nonce', nonce);
        formData.append('message', question);
        formData.append('context', JSON.stringify({
            title: titleText,
            content: contentText.substring(0, 3000),
            type: 'column'
        }));
        
        return fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.success && data.data && (data.data.answer || data.data.response)) {
                return data.data.answer || data.data.response;
            } else if (data && typeof data === 'object' && (data.answer || data.response)) {
                return data.answer || data.response;
            } else {
                return generateFallbackResponse(question);
            }
        })
        .catch(error => {
            console.warn('[AI AJAX] Request failed, using fallback:', error);
            return generateFallbackResponse(question);
        });
    }
    
    // フォールバックレスポンス生成
    function generateFallbackResponse(question) {
        const lowerQ = question.toLowerCase();
        
        if (lowerQ.includes('期限') || lowerQ.includes('締切') || lowerQ.includes('いつまで')) {
            return 'この記事の「申請期限」または「スケジュール」のセクションをご確認ください。補助金の締切情報が記載されています。';
        }
        if (lowerQ.includes('条件') || lowerQ.includes('要件') || lowerQ.includes('対象')) {
            return 'この記事の「申請条件」または「対象者」のセクションに詳細が記載されています。ご自身の事業が対象となるかご確認ください。';
        }
        if (lowerQ.includes('金額') || lowerQ.includes('補助率') || lowerQ.includes('いくら')) {
            return 'この記事の「補助金額」または「補助率」のセクションをご覧ください。補助金の金額や率について詳しく説明されています。';
        }
        if (lowerQ.includes('申請') || lowerQ.includes('手続き') || lowerQ.includes('方法')) {
            return 'この記事の「申請方法」または「申請手順」のセクションに、申請の流れが詳しく記載されています。ステップごとにご確認ください。';
        }
        if (lowerQ.includes('書類') || lowerQ.includes('必要') || lowerQ.includes('提出')) {
            return 'この記事の「必要書類」または「提出書類」のセクションをご確認ください。申請に必要な書類のリストが記載されています。';
        }
        
        return `ご質問ありがとうございます。「${question}」について、この記事内で詳しく説明されています。\n\n記事の目次から該当するセクションをご確認いただくか、ページ内検索（Ctrl+F / Cmd+F）で関連するキーワードを検索してみてください。\n\nさらに詳しい情報が必要な場合は、関連する補助金ページもご参照ください。`;
    }
    
    // HTML escape
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // AIレスポンスのフォーマット
    function formatAIResponse(text) {
        return escapeHtml(text).replace(/\n/g, '<br>');
    }
    
    // モバイルパネル制御
    function initMobilePanel() {
        const btn = document.getElementById('mobileTocBtn');
        const overlay = document.getElementById('mobileTocOverlay');
        const panel = document.getElementById('mobileTocPanel');
        const closeBtn = document.getElementById('mobileTocClose');
        const tabs = document.querySelectorAll('.gus-mobile-nav-tab');
        
        if (!btn || !overlay || !panel) return;
        
        // パネルを開く
        btn.addEventListener('click', function() {
            overlay.classList.add('active');
            panel.classList.add('active');
            overlay.setAttribute('aria-hidden', 'false');
            panel.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            
            // フォーカスをパネルに移動
            panel.focus();
        });
        
        // パネルを閉じる
        function closePanel() {
            overlay.classList.remove('active');
            panel.classList.remove('active');
            overlay.setAttribute('aria-hidden', 'true');
            panel.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            
            // フォーカスをボタンに戻す
            btn.focus();
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closePanel);
        }
        
        overlay.addEventListener('click', closePanel);
        
        // Escapeキーで閉じる
        panel.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePanel();
            }
        });
        
        // グローバルに公開
        window.closeMobilePanel = closePanel;
        
        // タブ切り替え
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // タブのアクティブ状態を切り替え
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                // コンテンツを切り替え
                const contents = panel.querySelectorAll('.gus-mobile-nav-content');
                contents.forEach(content => {
                    if ((targetTab === 'ai' && content.id === 'aiContent') ||
                        (targetTab === 'toc' && content.id === 'tocContent')) {
                        content.classList.add('active');
                        content.removeAttribute('hidden');
                    } else {
                        content.classList.remove('active');
                        content.setAttribute('hidden', '');
                    }
                });
            });
        });
    }
    
    // ビューカウント更新
    function updateViewCount() {
        const postId = document.querySelector('article[id^="post-"]');
        if (!postId) return;
        
        const id = postId.id.replace('post-', '');
        
        // REST API経由で更新
        const apiUrl = window.wpApiSettings ? window.wpApiSettings.root + 'gi-api/v1/column/' + id + '/view' : '/wp-json/gi-api/v1/column/' + id + '/view';
        const nonce = window.wpApiSettings && window.wpApiSettings.nonce ? window.wpApiSettings.nonce : '';
        
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce
            }
        }).catch(error => {
            // エラーは無視（閲覧数更新は必須ではない）
            console.log('[View Count] Update failed (non-critical):', error);
        });
    }
    
    // スムーススクロール
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    const headerOffset = window.innerWidth < 768 ? 80 : 100;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                    
                    // フォーカスを移動（アクセシビリティ）
                    target.setAttribute('tabindex', '-1');
                    target.focus();
                }
            });
        });
    }
    
    // 画像の遅延読み込み
    function initLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
            });
        } else {
            // Intersection Observer fallback
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        observer.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    // 外部リンクに rel="noopener noreferrer" を追加
    function secureExternalLinks() {
        const links = document.querySelectorAll('a[href^="http"]');
        links.forEach(link => {
            if (link.hostname !== window.location.hostname) {
                if (!link.hasAttribute('rel')) {
                    link.setAttribute('rel', 'noopener noreferrer');
                } else {
                    const rel = link.getAttribute('rel');
                    if (!rel.includes('noopener')) {
                        link.setAttribute('rel', rel + ' noopener');
                    }
                    if (!rel.includes('noreferrer')) {
                        link.setAttribute('rel', link.getAttribute('rel') + ' noreferrer');
                    }
                }
            }
        });
    }
    
    // ビューポート調整（モバイル）
    function adjustViewport() {
        if (window.innerWidth < 768) {
            let viewportMeta = document.querySelector('meta[name="viewport"]');
            if (!viewportMeta) {
                viewportMeta = document.createElement('meta');
                viewportMeta.name = 'viewport';
                document.head.appendChild(viewportMeta);
            }
            viewportMeta.content = 'width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes';
        }
    }
    
    // 初期化
    function init() {
        generateTOC();
        wrapTables();
        wrapEmbeds();
        initMobileAI();
        initDesktopAI();
        initMobilePanel();
        updateViewCount();
        initSmoothScroll();
        initLazyLoading();
        secureExternalLinks();
        adjustViewport();
        
        // デバッグモード時のみ実行
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('debug') === 'true') {
            setTimeout(detectHorizontalScroll, 1000);
        }
        
        console.log('[✓] Single Column v6.1 - Complete Responsive initialized');
        console.log('[✓] Features: Full Responsive, Mobile Optimized, Touch Friendly, No Horizontal Scroll');
    }
    
    // DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // リサイズ時の調整
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            adjustViewport();
        }, 250);
    });
    
    // ページ表示時のパフォーマンス測定（開発用）
    window.addEventListener('load', function() {
        if (window.performance && window.performance.timing) {
            const perfData = window.performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            const connectTime = perfData.responseEnd - perfData.requestStart;
            const renderTime = perfData.domComplete - perfData.domLoading;
            
            console.log('[Performance] Page Load Time:', pageLoadTime + 'ms');
            console.log('[Performance] Connect Time:', connectTime + 'ms');
            console.log('[Performance] Render Time:', renderTime + 'ms');
            
            // Core Web Vitals測定
            if ('PerformanceObserver' in window) {
                // Largest Contentful Paint (LCP)
                try {
                    const lcpObserver = new PerformanceObserver((entryList) => {
                        const entries = entryList.getEntries();
                        const lastEntry = entries[entries.length - 1];
                        console.log('[Core Web Vitals] LCP:', lastEntry.renderTime || lastEntry.loadTime, 'ms');
                    });
                    lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });
                } catch (e) {
                    console.log('[Performance] LCP measurement not supported');
                }
                
                // First Input Delay (FID)
                try {
                    const fidObserver = new PerformanceObserver((entryList) => {
                        const entries = entryList.getEntries();
                        entries.forEach(entry => {
                            console.log('[Core Web Vitals] FID:', entry.processingStart - entry.startTime, 'ms');
                        });
                    });
                    fidObserver.observe({ entryTypes: ['first-input'] });
                } catch (e) {
                    console.log('[Performance] FID measurement not supported');
                }
                
                // Cumulative Layout Shift (CLS)
                try {
                    let clsScore = 0;
                    const clsObserver = new PerformanceObserver((entryList) => {
                        const entries = entryList.getEntries();
                        entries.forEach(entry => {
                            if (!entry.hadRecentInput) {
                                clsScore += entry.value;
                            }
                        });
                        console.log('[Core Web Vitals] CLS:', clsScore.toFixed(4));
                    });
                    clsObserver.observe({ entryTypes: ['layout-shift'] });
                } catch (e) {
                    console.log('[Performance] CLS measurement not supported');
                }
            }
        }
        
        // リソース読み込み状況
        if (window.performance && window.performance.getEntriesByType) {
            const resources = window.performance.getEntriesByType('resource');
            const imageResources = resources.filter(r => r.initiatorType === 'img');
            const scriptResources = resources.filter(r => r.initiatorType === 'script');
            const styleResources = resources.filter(r => r.initiatorType === 'link' || r.initiatorType === 'css');
            
            console.log('[Resources] Images loaded:', imageResources.length);
            console.log('[Resources] Scripts loaded:', scriptResources.length);
            console.log('[Resources] Stylesheets loaded:', styleResources.length);
            
            // 重いリソースを警告
            resources.forEach(resource => {
                if (resource.duration > 1000) {
                    console.warn('[Performance Warning] Slow resource:', resource.name, 'took', resource.duration.toFixed(2), 'ms');
                }
                if (resource.transferSize > 1000000) {
                    console.warn('[Performance Warning] Large resource:', resource.name, 'size:', (resource.transferSize / 1000000).toFixed(2), 'MB');
                }
            });
        }
    });
    
    // ページ離脱前の保存処理
    window.addEventListener('beforeunload', function(e) {
        // AI入力内容を保存（オプション）
        const aiInput = document.getElementById('mobileAiInput');
        if (aiInput && aiInput.value.trim()) {
            try {
                sessionStorage.setItem('gi_ai_draft', aiInput.value);
            } catch (error) {
                console.log('[Session] Could not save AI draft');
            }
        }
    });
    
    // ページ読み込み時にAI入力を復元
    window.addEventListener('DOMContentLoaded', function() {
        try {
            const savedDraft = sessionStorage.getItem('gi_ai_draft');
            if (savedDraft) {
                const aiInput = document.getElementById('mobileAiInput');
                if (aiInput) {
                    aiInput.value = savedDraft;
                    sessionStorage.removeItem('gi_ai_draft');
                }
            }
        } catch (error) {
            console.log('[Session] Could not restore AI draft');
        }
    });
    
    // オンライン/オフライン検知
    window.addEventListener('online', function() {
        console.log('[Network] Connection restored');
        // オンラインに戻った時の処理
        const offlineNotice = document.querySelector('.offline-notice');
        if (offlineNotice) {
            offlineNotice.remove();
        }
    });
    
    window.addEventListener('offline', function() {
        console.warn('[Network] Connection lost');
        // オフライン時の通知
        const notice = document.createElement('div');
        notice.className = 'offline-notice';
        notice.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #dc2626;
            color: white;
            padding: 12px 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            z-index: 10000;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        `;
        notice.innerHTML = '<i class="fas fa-wifi" style="margin-right: 8px;"></i>インターネット接続が切断されました';
        document.body.appendChild(notice);
    });
    
    // スクロール位置の保存と復元
    let scrollPosition = 0;
    let scrollTimer;
    
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {
            scrollPosition = window.pageYOffset;
            try {
                sessionStorage.setItem('gi_scroll_position', scrollPosition);
            } catch (error) {
                console.log('[Session] Could not save scroll position');
            }
        }, 150);
    });
    
    // ページ読み込み時にスクロール位置を復元
    window.addEventListener('load', function() {
        try {
            const savedPosition = sessionStorage.getItem('gi_scroll_position');
            if (savedPosition && window.location.hash === '') {
                setTimeout(function() {
                    window.scrollTo({
                        top: parseInt(savedPosition),
                        behavior: 'instant'
                    });
                }, 100);
            }
        } catch (error) {
            console.log('[Session] Could not restore scroll position');
        }
    });
    
    // タッチジェスチャー検知（スワイプでパネルを閉じる）
    let touchStartY = 0;
    let touchEndY = 0;
    
    const panel = document.getElementById('mobileTocPanel');
    if (panel) {
        panel.addEventListener('touchstart', function(e) {
            touchStartY = e.touches[0].clientY;
        }, { passive: true });
        
        panel.addEventListener('touchmove', function(e) {
            touchEndY = e.touches[0].clientY;
            const diff = touchEndY - touchStartY;
            
            // 下方向にスワイプした場合、パネルを追従させる
            if (diff > 0 && window.scrollY === 0) {
                const content = panel.querySelector('.gus-mobile-nav-content.active');
                if (content && content.scrollTop === 0) {
                    panel.style.transform = `translateY(${Math.min(diff, 200)}px)`;
                }
            }
        }, { passive: true });
        
        panel.addEventListener('touchend', function(e) {
            const diff = touchEndY - touchStartY;
            
            // 100px以上下にスワイプしたらパネルを閉じる
            if (diff > 100) {
                const content = panel.querySelector('.gus-mobile-nav-content.active');
                if (content && content.scrollTop === 0) {
                    if (typeof window.closeMobilePanel === 'function') {
                        window.closeMobilePanel();
                    }
                }
            }
            
            // 位置をリセット
            panel.style.transform = '';
            touchStartY = 0;
            touchEndY = 0;
        }, { passive: true });
    }
    
    // フォーカストラップ（モーダル内でのキーボードナビゲーション）
    function trapFocus(element) {
        const focusableElements = element.querySelectorAll(
            'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length === 0) return;
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        element.addEventListener('keydown', function(e) {
            if (e.key !== 'Tab') return;
            
            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
    }
    
    // モバイルパネルにフォーカストラップを適用
    if (panel) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (panel.classList.contains('active')) {
                        trapFocus(panel);
                    }
                }
            });
        });
        
        observer.observe(panel, { attributes: true });
    }
    
    // エラーハンドリング
    window.addEventListener('error', function(e) {
        console.error('[Global Error]', e.message, 'at', e.filename, ':', e.lineno);
    });
    
    window.addEventListener('unhandledrejection', function(e) {
        console.error('[Unhandled Promise Rejection]', e.reason);
    });
    
    // デバッグモード（URLパラメータで有効化）
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('debug') === 'true') {
        console.log('[Debug Mode] Enabled');
        console.log('[Debug] Viewport:', window.innerWidth, 'x', window.innerHeight);
        console.log('[Debug] User Agent:', navigator.userAgent);
        console.log('[Debug] Touch Support:', 'ontouchstart' in window);
        console.log('[Debug] Connection:', navigator.connection ? navigator.connection.effectiveType : 'unknown');
        
        // デバッグ用のスタイル追加
        const debugStyle = document.createElement('style');
        debugStyle.textContent = `
            * { outline: 1px solid rgba(255, 0, 0, 0.2) !important; }
            *:hover { outline: 2px solid rgba(255, 0, 0, 0.5) !important; }
        `;
        document.head.appendChild(debugStyle);
    }
    
    // アナリティクス用のイベント送信（Google Analytics 4対応）
    function sendAnalyticsEvent(eventName, eventParams) {
        if (typeof gtag === 'function') {
            gtag('event', eventName, eventParams);
            console.log('[Analytics] Event sent:', eventName, eventParams);
        } else if (typeof ga === 'function') {
            // Universal Analytics fallback
            ga('send', 'event', eventParams.event_category, eventName, eventParams.event_label);
            console.log('[Analytics] Event sent (UA):', eventName);
        } else {
            console.log('[Analytics] Not available');
        }
    }
    
    // 重要なユーザーアクション追跡
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a, button');
        if (!target) return;
        
        // CTAボタンのクリック追跡
        if (target.classList.contains('gus-cta-btn')) {
            sendAnalyticsEvent('cta_click', {
                event_category: 'engagement',
                event_label: target.textContent.trim(),
                page_type: 'single_column'
            });
        }
        
        // 補助金カードのクリック追跡
        if (target.classList.contains('mobile-grant-link') || target.classList.contains('related-grant-link')) {
            sendAnalyticsEvent('grant_click', {
                event_category: 'navigation',
                event_label: target.querySelector('.mobile-grant-title, .related-grant-title')?.textContent.trim(),
                page_type: 'single_column'
            });
        }
        
        // シェアボタンのクリック追跡
        if (target.classList.contains('share-btn')) {
            const platform = target.classList.contains('share-twitter') ? 'twitter' :
                           target.classList.contains('share-facebook') ? 'facebook' :
                           target.classList.contains('share-line') ? 'line' : 'unknown';
            sendAnalyticsEvent('share', {
                event_category: 'social',
                event_label: platform,
                page_type: 'single_column'
            });
        }
    });
    
    // 読了率の追跡
    let readingProgress = 0;
    let milestones = [25, 50, 75, 100];
    
    window.addEventListener('scroll', function() {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight - windowHeight;
        const scrolled = window.scrollY;
        const progress = Math.round((scrolled / documentHeight) * 100);
        
        // マイルストーン到達時にイベント送信
        milestones.forEach(function(milestone) {
            if (progress >= milestone && readingProgress < milestone) {
                sendAnalyticsEvent('reading_progress', {
                    event_category: 'engagement',
                    event_label: milestone + '%',
                    page_type: 'single_column'
                });
            }
        });
        
        readingProgress = progress;
    });
    
    // AI使用状況の追跡
    const aiSendBtn = document.getElementById('mobileAiSend');
    if (aiSendBtn) {
        aiSendBtn.addEventListener('click', function() {
            sendAnalyticsEvent('ai_question', {
                event_category: 'ai_interaction',
                event_label: 'mobile_panel',
                page_type: 'single_column'
            });
        });
    }
    
    // 最終ログ出力
    console.log('[✓] Single Column v6.1 - All systems operational');
    console.log('[✓] Responsive: Mobile, Tablet, Desktop');
    console.log('[✓] Features: TOC, AI Chat, Analytics, Performance Monitoring');
    console.log('[✓] Accessibility: ARIA labels, Keyboard navigation, Screen reader support');
    console.log('[✓] SEO: Structured data, OGP, Canonical URL');
    console.log('[✓] Mobile: No horizontal scroll, Touch optimized, Swipe gestures');
    
})();
</script>