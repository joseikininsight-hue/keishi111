<?php
/**
 * Municipality Archive Template for Grant - Yahoo! JAPAN Inspired SEO Perfect Edition
 * 市町村別助成金・補助金アーカイブページ - Yahoo!風デザイン・SEO完全最適化版
 * 
 * @package Grant_Insight_Perfect
 * @version 19.0.0 - Municipality Specialized with Yahoo! JAPAN Style
 * 
 * === Features ===
 * - Based on archive-grant.php structure
 * - Municipality-fixed filter
 * - Yahoo! JAPAN inspired design
 * - Sidebar layout (PC only) with rankings & topics
 * - Ad spaces reserved in sidebar
 * - Mobile: No sidebar, optimized single column
 * - SEO Perfect (Schema.org, OGP, Twitter Card)
 * - All archive functions preserved
 */

get_header();

// 現在の市町村情報を取得
$current_municipality = get_queried_object();
$municipality_name = $current_municipality->name;
$municipality_slug = $current_municipality->slug;
$municipality_description = $current_municipality->description;
$municipality_count = $current_municipality->count;
$municipality_id = $current_municipality->term_id;

// 市町村メタ情報取得
$municipality_meta = get_term_meta($municipality_id);

// 都道府県データ
$prefectures = gi_get_all_prefectures();
$parent_prefecture = null;
$related_municipalities = [];

// 現在の市町村の都道府県を特定
foreach ($prefectures as $pref) {
    if (isset($pref['municipalities']) && is_array($pref['municipalities'])) {
        foreach ($pref['municipalities'] as $municipality) {
            if ($municipality['slug'] === $municipality_slug) {
                $parent_prefecture = $pref;
                $related_municipalities = array_filter($pref['municipalities'], function($m) use ($municipality_slug) {
                    return $m['slug'] !== $municipality_slug;
                });
                break 2;
            }
        }
    }
}

// 地域グループ
$region_groups = [
    'hokkaido' => '北海道',
    'tohoku' => '東北',
    'kanto' => '関東',
    'chubu' => '中部',
    'kinki' => '近畿',
    'chugoku' => '中国',
    'shikoku' => '四国',
    'kyushu' => '九州・沖縄'
];

// SEO用データ
$current_year = date('Y');
$current_month = date('n');

// ページタイトル・説明文の生成
$page_title = $municipality_name . 'の助成金・補助金一覧【' . $current_year . '年度最新版】';
$page_description = $municipality_description ?: 
    $municipality_name . 'で利用できる助成金・補助金を' . number_format($municipality_count) . '件掲載。' . 
    ($parent_prefecture ? $parent_prefecture['name'] . 'の制度と組み合わせて活用することも可能です。' : '') .
    $current_year . '年度の最新募集情報を毎日更新中。地域密着型の支援制度から国の制度まで幅広く掲載。';

$canonical_url = get_term_link($current_municipality);

// 総件数
$total_grants = wp_count_posts('grant')->publish;
$total_grants_formatted = number_format($total_grants);

// サイドバー用：新着トピックス（市町村内）
$recent_grants = new WP_Query([
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'tax_query' => [
        [
            'taxonomy' => 'grant_municipality',
            'field' => 'term_id',
            'terms' => $municipality_id
        ]
    ]
]);

// パンくずリスト用データ
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')]
];

if ($parent_prefecture) {
    $breadcrumbs[] = ['name' => $parent_prefecture['name'], 'url' => get_term_link($parent_prefecture['slug'], 'grant_prefecture')];
}

$breadcrumbs[] = ['name' => $municipality_name, 'url' => ''];

// 構造化データ: CollectionPage
$schema_collection = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $page_title,
    'description' => $page_description,
    'url' => $canonical_url,
    'inLanguage' => 'ja-JP',
    'dateModified' => current_time('c'),
    'provider' => [
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url(),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => get_site_icon_url(512) ?: home_url('/wp-content/uploads/2025/10/1.png')
        ]
    ],
    'mainEntity' => [
        '@type' => 'ItemList',
        'name' => $page_title,
        'description' => $page_description,
        'numberOfItems' => $municipality_count,
        'itemListElement' => []
    ]
];

if ($parent_prefecture) {
    $schema_collection['spatialCoverage'] = [
        '@type' => 'City',
        'name' => $municipality_name,
        'containedInPlace' => [
            '@type' => 'AdministrativeArea',
            'name' => $parent_prefecture['name'],
            'addressCountry' => 'JP'
        ]
    ];
}

// 構造化データ: BreadcrumbList
$breadcrumb_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => []
];

foreach ($breadcrumbs as $index => $breadcrumb) {
    $breadcrumb_schema['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => $index + 1,
        'name' => $breadcrumb['name'],
        'item' => !empty($breadcrumb['url']) ? $breadcrumb['url'] : $canonical_url
    ];
}

// 構造化データ: GovernmentService
$government_service_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'GovernmentService',
    'name' => $municipality_name . 'の助成金・補助金サービス',
    'description' => $page_description,
    'serviceType' => '助成金・補助金情報提供サービス',
    'provider' => [
        '@type' => 'GovernmentOrganization',
        'name' => $municipality_name,
        'url' => $canonical_url
    ],
    'areaServed' => [
        '@type' => 'City',
        'name' => $municipality_name,
        'addressCountry' => 'JP'
    ],
    'availableChannel' => [
        '@type' => 'ServiceChannel',
        'serviceUrl' => $canonical_url,
        'serviceType' => 'オンライン情報提供'
    ]
];

if ($parent_prefecture) {
    $government_service_schema['areaServed']['containedInPlace'] = [
        '@type' => 'AdministrativeArea',
        'name' => $parent_prefecture['name'],
        'addressCountry' => 'JP'
    ];
}

// OGP画像
$og_image = get_site_icon_url(1200) ?: home_url('/wp-content/uploads/2025/10/1.png');

// キーワード生成
$keywords = ['助成金', '補助金', $municipality_name, '検索', '申請', '支援制度', $current_year . '年度'];
if ($parent_prefecture) {
    $keywords[] = $parent_prefecture['name'];
}
$keywords_string = implode(',', $keywords);
?>

<!-- 構造化データ: CollectionPage -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_collection, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: BreadcrumbList -->
<script type="application/ld+json">
<?php echo wp_json_encode($breadcrumb_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- 構造化データ: GovernmentService -->
<script type="application/ld+json">
<?php echo wp_json_encode($government_service_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<main class="grant-archive-yahoo-style grant-municipality-archive" 
      id="municipality-<?php echo esc_attr($municipality_slug); ?>" 
      role="main"
      itemscope 
      itemtype="https://schema.org/CollectionPage">

    <!-- パンくずリスト -->
    <nav class="breadcrumb-nav" 
         aria-label="パンくずリスト" 
         itemscope 
         itemtype="https://schema.org/BreadcrumbList">
        <div class="yahoo-container">
            <ol class="breadcrumb-list">
                <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                <li class="breadcrumb-item" 
                    itemprop="itemListElement" 
                    itemscope 
                    itemtype="https://schema.org/ListItem">
                    <?php if (!empty($breadcrumb['url'])): ?>
                        <a href="<?php echo esc_url($breadcrumb['url']); ?>" 
                           itemprop="item"
                           title="<?php echo esc_attr($breadcrumb['name']); ?>へ移動">
                            <span itemprop="name"><?php echo esc_html($breadcrumb['name']); ?></span>
                        </a>
                    <?php else: ?>
                        <span itemprop="name"><?php echo esc_html($breadcrumb['name']); ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?php echo $index + 1; ?>">
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>

    <!-- 市町村ヒーローセクション -->
    <header class="category-hero-section municipality-hero" 
            itemscope 
            itemtype="https://schema.org/WPHeader">
        <div class="yahoo-container">
            <div class="hero-content-wrapper">
                
                <!-- 市町村バッジ -->
                <div class="category-badge municipality-badge">
                    <svg class="badge-icon" 
                         width="20" 
                         height="20" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>市町村別助成金</span>
                </div>

                <!-- メインタイトル -->
                <h1 class="category-main-title" itemprop="headline">
                    <span class="category-name-highlight"><?php echo esc_html($municipality_name); ?></span>
                    <span class="title-text">の助成金・補助金</span>
                    <span class="year-badge"><?php echo $current_year; ?>年度版</span>
                </h1>

                <!-- 市町村説明文 -->
                <div class="category-lead-section" itemprop="description">
                    <?php if ($municipality_description): ?>
                    <div class="category-description-rich">
                        <?php echo wpautop(wp_kses_post($municipality_description)); ?>
                    </div>
                    <?php endif; ?>
                    <p class="category-lead-sub">
                        <?php echo esc_html($municipality_name); ?>で利用できる助成金・補助金を
                        <strong><?php echo number_format($municipality_count); ?>件</strong>掲載。
                        <?php if ($parent_prefecture): ?>
                        <?php echo esc_html($parent_prefecture['name']); ?>の制度と組み合わせて活用することも可能です。
                        <?php endif; ?>
                        <?php echo $current_year; ?>年度の最新募集情報を毎日更新しています。
                    </p>
                </div>

                <!-- メタ情報 -->
                <div class="category-meta-info" role="group" aria-label="市町村統計情報">
                    <div class="meta-item" itemscope itemtype="https://schema.org/QuantitativeValue">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <path d="M9 11H7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V11h-2v8H9v-8z"/>
                            <path d="M13 7h2l-5-5-5 5h2v4h6V7z"/>
                        </svg>
                        <strong itemprop="value"><?php echo number_format($municipality_count); ?></strong>
                        <span itemprop="unitText">件の助成金</span>
                    </div>
                    <?php if ($parent_prefecture): ?>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        都道府県: <a href="<?php echo esc_url(get_term_link($parent_prefecture['slug'], 'grant_prefecture')); ?>" 
                                    class="prefecture-link" 
                                    itemprop="containedInPlace"><?php echo esc_html($parent_prefecture['name']); ?></a>
                    </div>
                    <?php endif; ?>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <time datetime="<?php echo $current_year; ?>" itemprop="dateModified">
                            <?php echo $current_year; ?>年度最新情報
                        </time>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span>毎日更新中</span>
                    </div>
                </div>

                <!-- 特徴カード -->
                <div class="feature-cards-grid">
                    <article class="feature-card">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3>リアルタイム更新</h3>
                            <p><?php echo esc_html($municipality_name); ?>の最新募集情報・締切情報を毎日チェック。見逃しを防ぎます。</p>
                        </div>
                    </article>

                    <article class="feature-card">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3>地域密着型情報</h3>
                            <p><?php echo esc_html($municipality_name); ?>独自の助成金から国の制度まで網羅。地域の実情に合わせた支援情報を提供。</p>
                        </div>
                    </article>

                    <article class="feature-card">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3>詳細な申請ガイド</h3>
                            <p>申請方法から採択のコツまで、専門家監修の情報を提供。初めての方でも安心して申請できます。</p>
                        </div>
                    </article>
                </div>

                <!-- 関連地域（都道府県・近隣市町村） -->
                <?php if ($parent_prefecture || !empty($related_municipalities)): ?>
                <div class="related-areas-section">
                    <h2 class="related-areas-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        関連する地域
                    </h2>
                    <div class="related-areas-grid">
                        <?php if ($parent_prefecture): ?>
                        <a href="<?php echo esc_url(get_term_link($parent_prefecture['slug'], 'grant_prefecture')); ?>" 
                           class="related-area-card prefecture-card"
                           title="<?php echo esc_attr($parent_prefecture['name']); ?>の助成金を見る">
                            <div class="card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <span class="card-name"><?php echo esc_html($parent_prefecture['name']); ?></span>
                            <span class="card-label">都道府県全体</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($related_municipalities)): ?>
                        <?php $displayed = 0; foreach ($related_municipalities as $municipality): 
                            if ($displayed >= 5) break;
                            $displayed++;
                        ?>
                        <a href="<?php echo esc_url(get_term_link($municipality['slug'], 'grant_municipality')); ?>" 
                           class="related-area-card municipality-card"
                           title="<?php echo esc_attr($municipality['name']); ?>の助成金を見る">
                            <div class="card-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/>
                                </svg>
                            </div>
                            <span class="card-name"><?php echo esc_html($municipality['name']); ?></span>
                            <span class="card-label">近隣市町村</span>
                        </a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- 2カラムレイアウト -->
    <div class="yahoo-container yahoo-two-column-layout">
        
        <!-- メインコンテンツ -->
        <div class="yahoo-main-content">
            
            <!-- 検索バー -->
            <section class="yahoo-search-section">
                <div class="search-bar-wrapper">
                    <label for="keyword-search" class="visually-hidden">キーワード検索</label>
                    <div class="search-input-container">
                        <svg class="search-icon" 
                             width="20" 
                             height="20" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="search" 
                               id="keyword-search" 
                               class="search-input" 
                               placeholder="助成金名、実施機関、対象事業で検索..."
                               data-municipality="<?php echo esc_attr($municipality_slug); ?>"
                               aria-label="助成金を検索"
                               autocomplete="off">
                        <button class="search-clear-btn" 
                                id="search-clear-btn" 
                                style="display: none;" 
                                aria-label="検索をクリア"
                                type="button">×</button>
                        <button class="search-execute-btn" 
                                id="search-btn" 
                                aria-label="検索を実行"
                                type="button">検索</button>
                    </div>
                </div>
            </section>

            <!-- モバイル用フローティングフィルターボタン -->
            <button class="mobile-filter-toggle" 
                    id="mobile-filter-toggle"
                    aria-label="フィルターを開く"
                    aria-expanded="false"
                    type="button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                <span class="filter-count-badge" id="mobile-filter-count" style="display: none;">0</span>
            </button>

            <!-- フィルターパネル背景オーバーレイ -->
            <div class="filter-panel-overlay" id="filter-panel-overlay"></div>

            <!-- プルダウン式フィルターセクション（市町村固定） -->
            <section class="yahoo-filter-section" id="filter-panel" 
                     role="search" 
                     aria-label="助成金検索フィルター">
                
                <!-- フィルターヘッダー -->
                <div class="filter-header">
                    <h2 class="filter-title">
                        <svg class="title-icon" 
                             width="18" 
                             height="18" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        絞り込み
                    </h2>
                    <button class="mobile-filter-close" 
                            id="mobile-filter-close"
                            aria-label="フィルターを閉じる"
                            type="button">×</button>
                    <button class="filter-reset-all" 
                            id="reset-all-filters-btn" 
                            style="display: none;" 
                            aria-label="すべてのフィルターをリセット"
                            type="button">
                        <svg width="14" 
                             height="14" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <polyline points="1 4 1 10 7 10"/>
                            <polyline points="23 20 23 14 17 14"/>
                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                        </svg>
                        リセット
                    </button>
                </div>

                <!-- プルダウンフィルターグリッド（市町村選択を除外） -->
                <div class="yahoo-filters-grid">
                    
                    <!-- 助成金額 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="amount-label">助成金額</label>
                        <div class="custom-select" 
                             id="amount-select" 
                             role="combobox" 
                             aria-labelledby="amount-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">指定なし</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-option active" 
                                     data-value="" 
                                     role="option">指定なし</div>
                                <div class="select-option" 
                                     data-value="0-100" 
                                     role="option">〜100万円</div>
                                <div class="select-option" 
                                     data-value="100-500" 
                                     role="option">100万円〜500万円</div>
                                <div class="select-option" 
                                     data-value="500-1000" 
                                     role="option">500万円〜1000万円</div>
                                <div class="select-option" 
                                     data-value="1000-3000" 
                                     role="option">1000万円〜3000万円</div>
                                <div class="select-option" 
                                     data-value="3000+" 
                                     role="option">3000万円以上</div>
                            </div>
                        </div>
                    </div>

                    <!-- 募集状況 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="status-label">募集状況</label>
                        <div class="custom-select" 
                             id="status-select" 
                             role="combobox" 
                             aria-labelledby="status-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">すべて</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-option active" 
                                     data-value="" 
                                     role="option">すべて</div>
                                <div class="select-option" 
                                     data-value="active" 
                                     role="option">募集中</div>
                                <div class="select-option" 
                                     data-value="upcoming" 
                                     role="option">募集予定</div>
                                <div class="select-option" 
                                     data-value="closed" 
                                     role="option">募集終了</div>
                            </div>
                        </div>
                    </div>

                    <!-- 並び順 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="sort-label">並び順</label>
                        <div class="custom-select" 
                             id="sort-select" 
                             role="combobox" 
                             aria-labelledby="sort-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">新着順</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-option active" 
                                     data-value="date_desc" 
                                     role="option">新着順</div>
                                <div class="select-option" 
                                     data-value="amount_desc" 
                                     role="option">金額が高い順</div>
                                <div class="select-option" 
                                     data-value="deadline_asc" 
                                     role="option">締切が近い順</div>
                                <div class="select-option" 
                                     data-value="featured_first" 
                                     role="option">注目順</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 選択中のフィルター表示 -->
                <div class="active-filters-display" 
                     id="active-filters" 
                     style="display: none;">
                    <div class="active-filters-label">
                        <svg width="14" 
                             height="14" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                        </svg>
                        適用中:
                    </div>
                    <div class="active-filter-tags" id="active-filter-tags"></div>
                </div>
                
                <!-- モバイル用フィルター適用ボタン -->
                <div class="mobile-filter-apply-section" id="mobile-filter-apply-section">
                    <button class="mobile-apply-filters-btn" 
                            id="mobile-apply-filters-btn" 
                            type="button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        フィルターを適用
                    </button>
                </div>
            </section>

            <!-- 検索結果セクション -->
            <section class="yahoo-results-section">
                
                <!-- 結果ヘッダー -->
                <div class="results-header">
                    <div class="results-info">
                        <h2 class="results-title">検索結果</h2>
                        <div class="results-meta">
                            <span class="total-count">
                                <strong id="current-count">0</strong>件
                            </span>
                            <span class="showing-range">
                                （<span id="showing-from">1</span>〜<span id="showing-to">12</span>件を表示）
                            </span>
                        </div>
                    </div>

                    <div class="view-controls">
                        <button class="view-btn active" 
                                data-view="single" 
                                title="単体表示" 
                                type="button">
                            <svg width="18" 
                                 height="18" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20"/>
                            </svg>
                        </button>
                        <button class="view-btn" 
                                data-view="grid" 
                                title="カード表示" 
                                type="button">
                            <svg width="18" 
                                 height="18" 
                                 viewBox="0 0 24 24" 
                                 fill="currentColor" 
                                 aria-hidden="true">
                                <rect x="3" y="3" width="7" height="7"/>
                                <rect x="14" y="3" width="7" height="7"/>
                                <rect x="3" y="14" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ローディング -->
                <div class="loading-overlay" 
                     id="loading-overlay" 
                     style="display: none;">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="loading-text">検索中...</p>
                    </div>
                </div>

                <!-- 助成金表示エリア -->
                <div class="grants-container-yahoo" 
                     id="grants-container" 
                     data-view="single">
                    <?php
                    // 初期表示用WP_Query（市町村固定）
                    $initial_query = new WP_Query([
                        'post_type' => 'grant',
                        'posts_per_page' => 12,
                        'post_status' => 'publish',
                        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                        'tax_query' => [
                            [
                                'taxonomy' => 'grant_municipality',
                                'field' => 'term_id',
                                'terms' => $municipality_id
                            ]
                        ],
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ]);
                    
                    if ($initial_query->have_posts()) :
                        while ($initial_query->have_posts()) : 
                            $initial_query->the_post();
                            include(get_template_directory() . '/template-parts/grant-card-unified.php');
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<div class="no-results-message" style="text-align: center; padding: 60px 20px;">';
                        echo '<p style="font-size: 1.125rem; color: #666; margin-bottom: 20px;">該当する助成金が見つかりませんでした。</p>';
                        echo '<p style="color: #999;">検索条件を変更して再度お試しください。</p>';
                        echo '</div>';
                    endif;
                    ?>
                </div>

                <!-- 結果なし -->
                <div class="no-results" 
                     id="no-results" 
                     style="display: none;">
                    <svg class="no-results-icon" 
                         width="64" 
                         height="64" 
                         viewBox="0 0 24 24" 
                         fill="none" 
                         stroke="currentColor" 
                         stroke-width="2" 
                         aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <h3 class="no-results-title">該当する助成金が見つかりませんでした</h3>
                    <p class="no-results-message">
                        検索条件を変更して再度お試しください。
                    </p>
                </div>

                <!-- ページネーション -->
                <div class="pagination-wrapper" 
                     id="pagination-wrapper">
                    <?php
                    if (isset($initial_query) && $initial_query->max_num_pages > 1) {
                        $big = 999999999;
                        
                        // すべての現在のクエリパラメータを保持
                        $preserved_params = array();
                        foreach ($_GET as $key => $value) {
                            if (!empty($value) && $key !== 'paged') {
                                $preserved_params[$key] = sanitize_text_field($value);
                            }
                        }
                        
                        // ベースURLにクエリパラメータを追加
                        $base_url = add_query_arg($preserved_params, str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ));
                        
                        echo paginate_links( array(
                            'base' => $base_url,
                            'format' => '&paged=%#%',
                            'current' => max( 1, get_query_var('paged') ),
                            'total' => $initial_query->max_num_pages,
                            'type' => 'plain',
                            'prev_text' => '前へ',
                            'next_text' => '次へ',
                            'mid_size' => 2,
                            'end_size' => 1,
                            'add_args' => $preserved_params,
                        ) );
                    }
                    ?>
                </div>
            </section>
        </div>

        <!-- サイドバー（PC only） -->
        <aside class="yahoo-sidebar" role="complementary" aria-label="サイドバー">
            
            <!-- 広告枠1: サイドバー上部 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-top">
                <?php ji_display_ad('municipality_grant_sidebar_top', 'taxonomy-grant_municipality'); ?>
            </div>
            <?php endif; ?>

            <!-- 広告枠2: サイドバー中央 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-middle">
                <?php ji_display_ad('municipality_grant_sidebar_middle', 'taxonomy-grant_municipality'); ?>
            </div>
            <?php endif; ?>

            <!-- アクセスランキング -->
            <?php
            $ranking_periods = array(
                array('days' => 3, 'label' => '3日間', 'id' => 'ranking-3days'),
                array('days' => 7, 'label' => '週間', 'id' => 'ranking-7days'),
                array('days' => 0, 'label' => '総合', 'id' => 'ranking-all'),
            );
            
            $default_period = 3;
            $ranking_data = function_exists('ji_get_ranking') ? ji_get_ranking('grant', $default_period, 10) : array();
            ?>
            
            <section class="sidebar-widget sidebar-ranking">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                        <polyline points="17 6 23 6 23 12"/>
                    </svg>
                    アクセスランキング
                </h3>
                
                <div class="ranking-tabs">
                    <?php foreach ($ranking_periods as $index => $period): ?>
                        <button 
                            type="button" 
                            class="ranking-tab <?php echo $index === 0 ? 'active' : ''; ?>" 
                            data-period="<?php echo esc_attr($period['days']); ?>"
                            data-target="#<?php echo esc_attr($period['id']); ?>">
                            <?php echo esc_html($period['label']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="widget-content">
                    <?php foreach ($ranking_periods as $index => $period): ?>
                        <div 
                            id="<?php echo esc_attr($period['id']); ?>" 
                            class="ranking-content <?php echo $index === 0 ? 'active' : ''; ?>"
                            data-period="<?php echo esc_attr($period['days']); ?>">
                            
                            <?php if ($index === 0): ?>
                                <?php if (!empty($ranking_data)): ?>
                                    <ol class="ranking-list">
                                        <?php foreach ($ranking_data as $rank => $item): ?>
                                            <li class="ranking-item rank-<?php echo $rank + 1; ?>">
                                                <a href="<?php echo get_permalink($item->post_id); ?>" class="ranking-link">
                                                    <span class="ranking-number"><?php echo $rank + 1; ?></span>
                                                    <span class="ranking-title">
                                                        <?php echo esc_html(get_the_title($item->post_id)); ?>
                                                    </span>
                                                    <span class="ranking-views">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                        <?php echo number_format($item->total_views); ?>
                                                    </span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php else: ?>
                                    <div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 10px; opacity: 0.3; display: block;">
                                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                                            <polyline points="17 6 23 6 23 12"/>
                                        </svg>
                                        <p style="margin: 0; font-size: 14px; font-weight: 500;">まだデータがありません</p>
                                        <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.7;">ページが閲覧されるとランキングが表示されます</p>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="ranking-loading">読み込み中...</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 新着トピックス（市町村内） -->
            <section class="sidebar-widget sidebar-topics">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                        <line x1="6" y1="1" x2="6" y2="4"/>
                        <line x1="10" y1="1" x2="10" y2="4"/>
                        <line x1="14" y1="1" x2="14" y2="4"/>
                    </svg>
                    <?php echo esc_html($municipality_name); ?>の新着トピックス
                </h3>
                <div class="widget-content">
                    <?php if ($recent_grants->have_posts()) : ?>
                        <ul class="topics-list">
                            <?php while ($recent_grants->have_posts()) : $recent_grants->the_post(); ?>
                                <li class="topics-item">
                                    <a href="<?php the_permalink(); ?>" class="topics-link">
                                        <time class="topics-date" datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date('Y/m/d'); ?>
                                        </time>
                                        <span class="topics-title"><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-data">データがありません</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 広告枠3 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-bottom">
                <?php ji_display_ad('municipality_grant_sidebar_bottom', 'taxonomy-grant_municipality'); ?>
            </div>
            <?php endif; ?>

            <!-- 関連地域（サイドバー版） -->
            <?php if ($parent_prefecture || !empty($related_municipalities)): ?>
            <section class="sidebar-widget sidebar-related-areas">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    関連する地域
                </h3>
                <div class="widget-content">
                    <ul class="related-areas-list">
                        <?php if ($parent_prefecture): ?>
                        <li class="related-area-item prefecture-item">
                            <a href="<?php echo esc_url(get_term_link($parent_prefecture['slug'], 'grant_prefecture')); ?>" class="related-area-link">
                                <?php echo esc_html($parent_prefecture['name']); ?>
                                <span class="area-label">都道府県</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (!empty($related_municipalities)): ?>
                        <?php $displayed = 0; foreach ($related_municipalities as $municipality): 
                            if ($displayed >= 5) break;
                            $displayed++;
                        ?>
                        <li class="related-area-item">
                            <a href="<?php echo esc_url(get_term_link($municipality['slug'], 'grant_municipality')); ?>" class="related-area-link">
                                <?php echo esc_html($municipality['name']); ?>
                                <span class="area-label">近隣</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </section>
            <?php endif; ?>
        </aside>
    </div>

</main>

<!-- CSS（完全版・省略なし） -->
<style>
/* ===================================
   Yahoo! JAPAN Inspired Municipality Archive Design
   完全版CSS - 省略なし
   =================================== */

:root {
    --yahoo-primary: #000000;
    --yahoo-secondary: #ffffff;
    --yahoo-gray-50: #fafafa;
    --yahoo-gray-100: #f5f5f5;
    --yahoo-gray-200: #e5e5e5;
    --yahoo-gray-300: #d4d4d4;
    --yahoo-gray-400: #a3a3a3;
    --yahoo-gray-500: #737373;
    --yahoo-gray-600: #525252;
    --yahoo-gray-700: #404040;
    --yahoo-gray-800: #262626;
    --yahoo-gray-900: #171717;
    --yahoo-red: #ff0033;
    --yahoo-blue: #0078ff;
    --yahoo-green: #00cc00;
    --yahoo-yellow: #FFEB3B;
    --yahoo-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    --yahoo-radius: 4px;
    --yahoo-font: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif;
    --transition-fast: 0.15s ease;
    --transition-normal: 0.3s ease;
}

/* ===== Base ===== */
.grant-archive-yahoo-style {
    font-family: var(--yahoo-font);
    color: var(--yahoo-primary);
    background: var(--yahoo-gray-50);
    line-height: 1.6;
}

.yahoo-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
}

/* ===== Visually Hidden ===== */
.visually-hidden {
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

/* ===== Breadcrumb ===== */
.breadcrumb-nav {
    padding: 12px 0;
    background: var(--yahoo-secondary);
    border-bottom: 1px solid var(--yahoo-gray-200);
}

.breadcrumb-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 13px;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin-left: 6px;
    color: var(--yahoo-gray-400);
}

.breadcrumb-item a {
    color: var(--yahoo-gray-600);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.breadcrumb-item a:hover {
    color: var(--yahoo-primary);
    text-decoration: underline;
}

.breadcrumb-item span {
    color: var(--yahoo-primary);
    font-weight: 600;
}

/* ===== Municipality Hero Section ===== */
.category-hero-section.municipality-hero {
    padding: 40px 0;
    background: linear-gradient(135deg, var(--yahoo-gray-50) 0%, var(--yahoo-secondary) 100%);
    border-bottom: 2px solid var(--yahoo-primary);
    position: relative;
}

.category-hero-section.municipality-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at top right, rgba(0, 0, 0, 0.02) 0%, transparent 50%);
    pointer-events: none;
}

.hero-content-wrapper {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.category-badge.municipality-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: var(--yahoo-yellow);
    color: var(--yahoo-primary);
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 16px;
    border-radius: 20px;
}

.badge-icon {
    color: var(--yahoo-primary);
}

.category-main-title {
    font-size: 36px;
    font-weight: 800;
    color: var(--yahoo-primary);
    margin: 0 0 20px 0;
    line-height: 1.3;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
}

.category-name-highlight {
    background: linear-gradient(180deg, transparent 60%, rgba(255, 235, 59, 0.4) 60%);
    padding: 0 4px;
    display: inline-block;
}

.title-text {
    color: var(--yahoo-gray-700);
    font-size: 32px;
}

.year-badge {
    display: inline-block;
    padding: 4px 12px;
    background: var(--yahoo-primary);
    color: var(--yahoo-secondary);
    border-radius: 16px;
    font-size: 14px;
    font-weight: 700;
}

.category-lead-section {
    margin: 24px 0;
}

.category-description-rich {
    margin-bottom: 20px;
    line-height: 1.8;
    color: var(--yahoo-gray-700);
    font-size: 15px;
}

.category-description-rich p {
    margin: 0 0 16px 0;
}

.category-description-rich p:last-child {
    margin-bottom: 0;
}

.category-lead-sub {
    font-size: 15px;
    color: var(--yahoo-gray-600);
    margin: 0;
    line-height: 1.7;
}

.category-lead-sub strong {
    color: var(--yahoo-primary);
    font-weight: 700;
}

.category-meta-info {
    display: flex;
    align-items: center;
    gap: 24px;
    margin: 24px 0;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: var(--yahoo-gray-700);
}

.meta-icon {
    color: var(--yahoo-gray-500);
    flex-shrink: 0;
}

.meta-item strong {
    color: var(--yahoo-primary);
    font-weight: 700;
    font-size: 18px;
}

.prefecture-link {
    color: var(--yahoo-blue);
    text-decoration: none;
    font-weight: 600;
    transition: color var(--transition-fast);
}

.prefecture-link:hover {
    color: var(--yahoo-primary);
    text-decoration: underline;
}

.feature-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
    margin: 32px 0;
}

.feature-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 20px;
    background: var(--yahoo-secondary);
    border: 2px solid var(--yahoo-gray-200);
    border-radius: var(--yahoo-radius);
    transition: all var(--transition-normal);
}

.feature-card:hover {
    border-color: var(--yahoo-primary);
    transform: translateY(-2px);
    box-shadow: var(--yahoo-shadow);
}

.feature-card-icon {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    background: var(--yahoo-primary);
    border-radius: var(--yahoo-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--yahoo-secondary);
}

.feature-card-content {
    flex: 1;
}

.feature-card-content h3 {
    font-size: 15px;
    font-weight: 700;
    color: var(--yahoo-primary);
    margin: 0 0 8px 0;
}

.feature-card-content p {
    font-size: 13px;
    color: var(--yahoo-gray-600);
    margin: 0;
    line-height: 1.5;
}

/* ===== Related Areas Section ===== */
.related-areas-section {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px solid var(--yahoo-gray-200);
}

.related-areas-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: 700;
    color: var(--yahoo-primary);
    margin: 0 0 20px 0;
}

.related-areas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
}

.related-area-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px;
    background: var(--yahoo-secondary);
    border: 2px solid var(--yahoo-gray-200);
    border-radius: var(--yahoo-radius);
    text-decoration: none;
    transition: all var(--transition-fast);
    color: var(--yahoo-primary);
    text-align: center;
}

.related-area-card:hover {
    border-color: var(--yahoo-primary);
    transform: translateY(-2px);
    box-shadow: var(--yahoo-shadow);
}

.related-area-card .card-icon {
    width: 36px;
    height: 36px;
    background: var(--yahoo-gray-100);
    border-radius: var(--yahoo-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--yahoo-gray-600);
    margin-bottom: 8px;
}

.related-area-card.prefecture-card .card-icon {
    background: var(--yahoo-yellow);
    color: var(--yahoo-primary);
}

.related-area-card .card-name {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
}

.related-area-card .card-label {
    font-size: 11px;
    color: var(--yahoo-gray-500);
}

/* ===== Two Column Layout ===== */
.yahoo-two-column-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 24px;
    padding: 24px 16px;
    align-items: start;
}

.yahoo-main-content {
    min-width: 0;
}

.yahoo-sidebar {
    position: sticky;
    top: 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ===== Search Section ===== */
.yahoo-search-section {
    margin-bottom: 20px;
}

.search-bar-wrapper {
    background: var(--yahoo-secondary);
    padding: 16px;
    border: 1px solid var(--yahoo-gray-200);
    box-shadow: var(--yahoo-shadow);
    border-radius: var(--yahoo-radius);
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
    background: var(--yahoo-secondary);
    border: 2px solid var(--yahoo-gray-300);
    overflow: hidden;
    border-radius: var(--yahoo-radius);
}

.search-input-container:focus-within {
    border-color: var(--yahoo-primary);
}

.search-icon {
    position: absolute;
    left: 12px;
    color: var(--yahoo-gray-400);
    pointer-events: none;
}

.search-input {
    flex: 1;
    padding: 10px 12px 10px 40px;
    border: none;
    outline: none;
    font-size: 14px;
    background: transparent;
}

.search-clear-btn {
    background: none;
    border: none;
    color: var(--yahoo-gray-400);
    padding: 6px;
    cursor: pointer;
    font-size: 20px;
    line-height: 1;
    transition: color var(--transition-fast);
}

.search-clear-btn:hover {
    color: var(--yahoo-gray-700);
}

.search-execute-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    background: var(--yahoo-primary);
    border: none;
    color: var(--yahoo-secondary);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background var(--transition-fast);
}

.search-execute-btn:hover {
    background: var(--yahoo-gray-800);
}

/* ===== Mobile Filter Toggle ===== */
.mobile-filter-toggle {
    display: none;
    position: fixed;
    bottom: 65px; /* グローバルバナー(50px) + 余白(15px) */
    left: 16px;
    padding: 10px 16px;
    background: var(--yahoo-primary);
    color: var(--yahoo-secondary);
    border: none;
    border-radius: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    cursor: pointer;
    z-index: 1000; /* グローバルバナーより上 */
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
}

.filter-count-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    background: var(--yahoo-red);
    color: var(--yahoo-secondary);
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
}

.mobile-filter-close {
    display: none;
    background: none;
    border: none;
    font-size: 24px;
    line-height: 1;
    color: var(--yahoo-gray-600);
    cursor: pointer;
    padding: 6px;
    margin-left: auto;
}

.mobile-filter-apply-section {
    display: none;
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px;
    background: var(--yahoo-secondary);
    border-top: 2px solid var(--yahoo-gray-200);
    z-index: 20;
}

.mobile-apply-filters-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    background: var(--yahoo-primary);
    color: var(--yahoo-secondary);
    border: none;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.mobile-apply-filters-btn:active {
    background: var(--yahoo-gray-800);
}

.filter-panel-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 997;
    opacity: 0;
    transition: opacity var(--transition-normal);
}

.filter-panel-overlay.active {
    display: block;
    opacity: 1;
}

/* ===== Filter Section ===== */
.yahoo-filter-section {
    background: var(--yahoo-secondary);
    padding: 16px;
    border: 1px solid var(--yahoo-gray-200);
    box-shadow: var(--yahoo-shadow);
    margin-bottom: 20px;
    border-radius: var(--yahoo-radius);
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 700;
    color: var(--yahoo-primary);
    margin: 0;
}

.title-icon {
    color: var(--yahoo-gray-600);
}

.filter-reset-all {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: var(--yahoo-gray-100);
    border: 1px solid var(--yahoo-gray-300);
    color: var(--yahoo-gray-700);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border-radius: var(--yahoo-radius);
    transition: all var(--transition-fast);
}

.filter-reset-all:hover {
    background: var(--yahoo-gray-200);
    border-color: var(--yahoo-gray-400);
}

.yahoo-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}

.filter-dropdown-wrapper {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    font-weight: 600;
    color: var(--yahoo-gray-700);
}

/* ===== Custom Select ===== */
.custom-select {
    position: relative;
    width: 100%;
}

.select-trigger {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    background: var(--yahoo-secondary);
    border: 1px solid var(--yahoo-gray-300);
    color: var(--yahoo-gray-700);
    font-size: 13px;
    cursor: pointer;
    text-align: left;
    border-radius: var(--yahoo-radius);
    transition: all var(--transition-fast);
}

.select-trigger:hover {
    border-color: var(--yahoo-gray-400);
}

.custom-select.active .select-trigger {
    border-color: var(--yahoo-primary);
}

.select-value {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.select-arrow {
    flex-shrink: 0;
    color: var(--yahoo-gray-500);
    transition: transform var(--transition-fast);
}

.custom-select.active .select-arrow {
    transform: rotate(180deg);
}

.select-dropdown {
    position: absolute;
    top: calc(100% + 2px);
    left: 0;
    right: 0;
    background: var(--yahoo-secondary);
    border: 1px solid var(--yahoo-gray-300);
    box-shadow: var(--yahoo-shadow);
    max-height: 280px;
    overflow-y: auto;
    z-index: 100;
    border-radius: var(--yahoo-radius);
}

.select-option {
    padding: 8px 10px;
    cursor: pointer;
    font-size: 13px;
    color: var(--yahoo-gray-700);
    display: flex;
    align-items: center;
    gap: 6px;
    transition: background var(--transition-fast);
}

.select-option:hover {
    background: var(--yahoo-gray-100);
}

.select-option.active {
    background: var(--yahoo-gray-100);
    color: var(--yahoo-primary);
    font-weight: 600;
}

/* ===== Active Filters ===== */
.active-filters-display {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--yahoo-gray-50);
    border: 1px solid var(--yahoo-gray-200);
    flex-wrap: wrap;
    border-radius: var(--yahoo-radius);
}

.active-filters-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--yahoo-gray-700);
}

.active-filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    flex: 1;
}

.filter-tag {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: var(--yahoo-primary);
    color: var(--yahoo-secondary);
    font-size: 12px;
    font-weight: 500;
    border-radius: 12px;
}

.filter-tag-remove {
    background: none;
    border: none;
    color: var(--yahoo-secondary);
    cursor: pointer;
    padding: 0;
    width: 14px;
    height: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    line-height: 1;
    opacity: 0.8;
    transition: opacity var(--transition-fast);
}

.filter-tag-remove:hover {
    opacity: 1;
}

/* ===== Results Section ===== */
.yahoo-results-section {
    background: var(--yahoo-secondary);
    padding: 16px;
    border: 1px solid var(--yahoo-gray-200);
    box-shadow: var(--yahoo-shadow);
    border-radius: var(--yahoo-radius);
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--yahoo-gray-200);
}

.results-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--yahoo-primary);
    margin: 0 0 6px 0;
}

.results-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
    color: var(--yahoo-gray-600);
}

.total-count strong {
    font-size: 16px;
    color: var(--yahoo-primary);
}

.view-controls {
    display: flex;
    gap: 2px;
    background: var(--yahoo-gray-100);
    padding: 2px;
    border-radius: var(--yahoo-radius);
}

.view-btn {
    background: transparent;
    border: none;
    padding: 6px 8px;
    cursor: pointer;
    color: var(--yahoo-gray-600);
    transition: all var(--transition-fast);
    border-radius: var(--yahoo-radius);
}

.view-btn:hover {
    color: var(--yahoo-primary);
}

.view-btn.active {
    background: var(--yahoo-primary);
    color: var(--yahoo-secondary);
}

/* ===== Loading ===== */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-spinner {
    text-align: center;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid var(--yahoo-gray-200);
    border-top-color: var(--yahoo-primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 12px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    font-size: 13px;
    color: var(--yahoo-gray-600);
    margin: 0;
}

/* ===== Grants Container ===== */
.grants-container-yahoo {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
    min-height: 300px;
}

.grants-container-yahoo[data-view="grid"] {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

/* ===== No Results ===== */
.no-results {
    text-align: center;
    padding: 60px 20px;
    color: var(--yahoo-gray-600);
}

.no-results-icon {
    color: var(--yahoo-gray-300);
    margin-bottom: 16px;
}

.no-results-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--yahoo-primary);
    margin: 0 0 10px 0;
}

.no-results-message {
    font-size: 14px;
    margin: 0;
}

/* ===== Pagination ===== */
.pagination-wrapper {
    margin-top: 24px;
    display: flex;
    justify-content: center;
    padding: 16px 0;
    border-top: 1px solid var(--yahoo-gray-200);
}

.pagination-wrapper .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    margin: 0 2px;
    border: 1px solid var(--yahoo-gray-300);
    background: var(--yahoo-secondary);
    color: var(--yahoo-gray-700);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border-radius: var(--yahoo-radius);
    transition: all var(--transition-fast);
}

.pagination-wrapper .page-numbers:hover {
    border-color: var(--yahoo-primary);
    color: var(--yahoo-primary);
    background: var(--yahoo-gray-50);
}

.pagination-wrapper .page-numbers.current {
    background: var(--yahoo-primary);
    border-color: var(--yahoo-primary);
    color: var(--yahoo-secondary);
}

/* ===== Sidebar ===== */
.yahoo-sidebar {
    background: transparent;
}

.sidebar-widget {
    background: var(--yahoo-secondary);
    border: 1px solid var(--yahoo-gray-200);
    box-shadow: var(--yahoo-shadow);
    overflow: hidden;
    border-radius: var(--yahoo-radius);
}

.widget-title {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: var(--yahoo-gray-50);
    border-bottom: 2px solid var(--yahoo-primary);
    font-size: 14px;
    font-weight: 700;
    color: var(--yahoo-primary);
    margin: 0;
}

.widget-content {
    padding: 16px;
}

.no-data {
    text-align: center;
    padding: 20px;
    color: var(--yahoo-gray-500);
    font-size: 13px;
}

/* ===== Sidebar Ad Spaces ===== */
.sidebar-ad-space {
    background: var(--yahoo-gray-100);
    border: 1px solid var(--yahoo-gray-200);
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--yahoo-gray-400);
    font-size: 12px;
    border-radius: var(--yahoo-radius);
}

/* ===== Ranking ===== */
.sidebar-ranking {
    background: #FFFFFF;
    border: 1px solid #E4E4E4;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 20px;
}

.sidebar-ranking .widget-title {
    background: linear-gradient(135deg, #F8F9FA 0%, #FFFFFF 100%);
    padding: 12px 16px;
    border-bottom: 2px solid #FF0033;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #333;
}

.ranking-tabs {
    display: flex;
    border-bottom: 1px solid #E4E4E4;
    background: #FAFAFA;
}

.ranking-tab {
    flex: 1;
    padding: 10px 12px;
    border: none;
    background: transparent;
    color: #666;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.ranking-tab:hover {
    background: #F0F0F0;
    color: #333;
}

.ranking-tab.active {
    background: #FFFFFF;
    color: #00C851;
    font-weight: 700;
}

.ranking-tab.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: #FF0033;
}

.ranking-content {
    display: none;
    padding: 0;
}

.ranking-content.active {
    display: block;
}

.ranking-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.ranking-item {
    border-bottom: 1px solid #F0F0F0;
    transition: background 0.2s;
}

.ranking-item:last-child {
    border-bottom: none;
}

.ranking-item:hover {
    background: #F8F9FA;
}

.ranking-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    text-decoration: none;
    color: #333;
}

.ranking-number {
    flex-shrink: 0;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    border-radius: 3px;
    background: #F0F0F0;
    color: #666;
}

.rank-1 .ranking-number {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #fff;
    box-shadow: 0 2px 4px rgba(255, 165, 0, 0.3);
    font-size: 14px;
}

.rank-2 .ranking-number {
    background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
    color: #fff;
    box-shadow: 0 2px 4px rgba(192, 192, 192, 0.3);
}

.rank-3 .ranking-number {
    background: linear-gradient(135deg, #CD7F32, #A0522D);
    color: #fff;
    box-shadow: 0 2px 4px rgba(205, 127, 50, 0.3);
}

.ranking-title {
    flex: 1;
    font-size: 13px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ranking-link:hover .ranking-title {
    color: #00C851;
    text-decoration: underline;
}

.ranking-views {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #999;
}

.ranking-loading {
    padding: 40px 20px;
    text-align: center;
    color: #999;
    font-size: 13px;
}

.ranking-empty {
    text-align: center;
    padding: 30px 20px;
    color: #666;
}

/* ===== Topics ===== */
.topics-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.topics-item {
    border-bottom: 1px solid var(--yahoo-gray-200);
    padding-bottom: 12px;
}

.topics-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.topics-link {
    display: flex;
    flex-direction: column;
    gap: 4px;
    text-decoration: none;
    color: inherit;
}

.topics-link:hover .topics-title {
    color: var(--yahoo-blue);
    text-decoration: underline;
}

.topics-date {
    font-size: 11px;
    color: var(--yahoo-gray-500);
}

.topics-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--yahoo-primary);
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* ===== Sidebar Related Areas ===== */
.sidebar-related-areas .related-areas-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.related-area-item {
    border-bottom: 1px solid var(--yahoo-gray-200);
    padding-bottom: 8px;
}

.related-area-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.related-area-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    color: var(--yahoo-primary);
    font-size: 13px;
    font-weight: 600;
    transition: color var(--transition-fast);
}

.related-area-link:hover {
    color: var(--yahoo-blue);
    text-decoration: underline;
}

.related-area-link .area-label {
    font-size: 11px;
    color: var(--yahoo-gray-500);
    font-weight: 400;
}

.related-area-item.prefecture-item .related-area-link {
    color: var(--yahoo-blue);
}

/* ===== Responsive ===== */
@media (max-width: 1024px) {
    .yahoo-two-column-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .yahoo-sidebar {
        display: none;
    }
}

@media (max-width: 768px) {
    .mobile-filter-apply-section {
        display: block !important;
    }
    
    .mobile-filter-toggle {
        display: flex !important;
    }
    
    .mobile-filter-close {
        display: block !important;
    }
    
    .yahoo-filter-section {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--yahoo-secondary);
        z-index: 998;
        padding: 50px 16px 80px !important;
        overflow-y: auto !important;
        transform: translateX(100%);
        box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }
    
    .yahoo-filter-section.active {
        transform: translateX(0) !important;
    }
    
    .filter-header {
        position: sticky;
        top: 0;
        background: var(--yahoo-secondary);
        z-index: 10;
        padding: 12px 0 !important;
        margin-bottom: 16px !important;
        border-bottom: 1px solid var(--yahoo-gray-200);
    }
    
    .yahoo-container {
        padding: 0 12px;
    }
    
    .category-main-title {
        font-size: 28px;
    }
    
    .title-text {
        font-size: 24px;
    }
    
    .year-badge {
        font-size: 12px;
        padding: 3px 10px;
    }
    
    .category-meta-info {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }
    
    .feature-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .related-areas-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    }
    
    .yahoo-filters-grid {
        grid-template-columns: 1fr;
    }
    
    .grants-container-yahoo[data-view="grid"] {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .category-main-title {
        font-size: 24px;
    }
    
    .title-text {
        font-size: 20px;
    }
    
    .search-input-container {
        flex-direction: column;
    }
    
    .search-execute-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- JavaScript（完全版・省略なし） -->
<script>
(function() {
    'use strict';
    
    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    const NONCE = '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>';
    const MUNICIPALITY_SLUG = '<?php echo esc_js($municipality_slug); ?>';
    const MUNICIPALITY_ID = <?php echo $municipality_id; ?>;
    
    const state = {
        currentPage: 1,
        perPage: 12,
        view: 'single',
        filters: {
            search: '',
            municipality: MUNICIPALITY_SLUG,
            amount: '',
            status: '',
            sort: 'date_desc'
        },
        isLoading: false
    };
    
    const elements = {};
    
    function init() {
        console.log('🚀 Municipality Archive v19.0 Initialized:', MUNICIPALITY_SLUG);
        
        initializeElements();
        initializeFromUrlParams();
        setupCustomSelects();
        setupEventListeners();
        loadGrants();
    }
    
    function initializeElements() {
        elements.grantsContainer = document.getElementById('grants-container');
        elements.loadingOverlay = document.getElementById('loading-overlay');
        elements.noResults = document.getElementById('no-results');
        elements.resultsCount = document.getElementById('current-count');
        elements.showingFrom = document.getElementById('showing-from');
        elements.showingTo = document.getElementById('showing-to');
        elements.paginationWrapper = document.getElementById('pagination-wrapper');
        elements.activeFilters = document.getElementById('active-filters');
        elements.activeFilterTags = document.getElementById('active-filter-tags');
        
        elements.keywordSearch = document.getElementById('keyword-search');
        elements.searchBtn = document.getElementById('search-btn');
        elements.searchClearBtn = document.getElementById('search-clear-btn');
        
        elements.amountSelect = document.getElementById('amount-select');
        elements.statusSelect = document.getElementById('status-select');
        elements.sortSelect = document.getElementById('sort-select');
        
        elements.viewBtns = document.querySelectorAll('.view-btn');
        elements.resetAllFiltersBtn = document.getElementById('reset-all-filters-btn');
        
        elements.mobileFilterToggle = document.getElementById('mobile-filter-toggle');
        elements.mobileFilterClose = document.getElementById('mobile-filter-close');
        elements.filterPanel = document.getElementById('filter-panel');
        elements.mobileFilterCount = document.getElementById('mobile-filter-count');
        elements.filterPanelOverlay = document.getElementById('filter-panel-overlay');
    }
    
    function initializeFromUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        
        const searchParam = urlParams.get('search');
        if (searchParam) {
            state.filters.search = searchParam;
            if (elements.keywordSearch) {
                elements.keywordSearch.value = searchParam;
                elements.searchClearBtn.style.display = 'flex';
            }
        }
    }
    
    function setupCustomSelects() {
        setupSingleSelect(elements.amountSelect, (value) => {
            state.filters.amount = value;
            state.currentPage = 1;
            loadGrants();
        });
        
        setupSingleSelect(elements.statusSelect, (value) => {
            state.filters.status = value;
            state.currentPage = 1;
            loadGrants();
        });
        
        setupSingleSelect(elements.sortSelect, (value) => {
            state.filters.sort = value;
            state.currentPage = 1;
            loadGrants();
        });
    }
    
    function setupSingleSelect(selectElement, onChange) {
        if (!selectElement) return;
        
        const trigger = selectElement.querySelector('.select-trigger');
        const dropdown = selectElement.querySelector('.select-dropdown');
        const options = selectElement.querySelectorAll('.select-option');
        const valueSpan = selectElement.querySelector('.select-value');
        
        trigger.addEventListener('click', () => {
            const isActive = selectElement.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                selectElement.classList.add('active');
                selectElement.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
            }
        });
        
        options.forEach(option => {
            option.addEventListener('click', () => {
                const value = option.dataset.value;
                const text = option.textContent.trim();
                
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                });
                option.classList.add('active');
                option.setAttribute('aria-selected', 'true');
                
                valueSpan.textContent = text;
                
                selectElement.classList.remove('active');
                selectElement.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                // モバイルではフィルターパネルを閉じない（適用ボタンで一括適用）
                if (window.innerWidth > 768) {
                    onChange(value);
                } else {
                    // モバイルでは値だけ更新して、loadGrantsは呼ばない
                    const filterName = selectElement.id.replace('-select', '');
                    if (filterName === 'region') {
                        state.filters.region = value;
                        filterPrefecturesByRegion(value);
                    } else if (filterName === 'amount') {
                        state.filters.amount = value;
                    } else if (filterName === 'status') {
                        state.filters.status = value;
                    } else if (filterName === 'sort') {
                        state.filters.sort = value;
                    }
                }
            });
        });
    }
    
    function closeAllSelects() {
        document.querySelectorAll('.custom-select').forEach(select => {
            select.classList.remove('active');
            select.setAttribute('aria-expanded', 'false');
            const dropdown = select.querySelector('.select-dropdown');
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        });
    }
    
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-select')) {
            closeAllSelects();
        }
    });
    
    function setupEventListeners() {
        if (elements.keywordSearch) {
            elements.keywordSearch.addEventListener('input', debounce(handleSearchInput, 300));
            elements.keywordSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearch();
                }
            });
        }
        
        if (elements.searchBtn) {
            elements.searchBtn.addEventListener('click', handleSearch);
        }
        
        if (elements.searchClearBtn) {
            elements.searchClearBtn.addEventListener('click', clearSearch);
        }
        
        elements.viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                elements.viewBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                state.view = this.dataset.view;
                elements.grantsContainer.setAttribute('data-view', state.view);
            });
        });
        
        if (elements.resetAllFiltersBtn) {
            elements.resetAllFiltersBtn.addEventListener('click', resetAllFilters);
        }
        
        // モバイル用フィルター適用ボタン
        const mobileApplyFiltersBtn = document.getElementById('mobile-apply-filters-btn');
        if (mobileApplyFiltersBtn) {
            mobileApplyFiltersBtn.addEventListener('click', function() {
                state.currentPage = 1;
                loadGrants();
                closeMobileFilter();
            });
        }
        
        if (elements.mobileFilterToggle) {
            elements.mobileFilterToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (elements.filterPanel && elements.filterPanel.classList.contains('active')) {
                    closeMobileFilter();
                } else {
                    openMobileFilter();
                }
            }, false);
        }
        
        if (elements.mobileFilterClose) {
            elements.mobileFilterClose.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMobileFilter();
            }, false);
        }
        
        if (elements.filterPanelOverlay) {
            elements.filterPanelOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMobileFilter();
            }, false);
        }
        
        if (elements.filterPanel) {
            elements.filterPanel.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && window.innerWidth <= 768) {
                if (elements.filterPanel && elements.filterPanel.classList.contains('active')) {
                    closeMobileFilter();
                }
            }
        });
    }
    
    function openMobileFilter() {
        if (elements.filterPanel) {
            elements.filterPanel.classList.add('active');
            document.body.style.overflow = 'hidden';
            
            if (elements.filterPanelOverlay) {
                elements.filterPanelOverlay.classList.add('active');
            }
            if (elements.mobileFilterToggle) {
                elements.mobileFilterToggle.setAttribute('aria-expanded', 'true');
            }
        }
    }
    
    function closeMobileFilter() {
        if (elements.filterPanel) {
            elements.filterPanel.classList.remove('active');
            document.body.style.overflow = '';
            
            if (elements.filterPanelOverlay) {
                elements.filterPanelOverlay.classList.remove('active');
            }
            if (elements.mobileFilterToggle) {
                elements.mobileFilterToggle.setAttribute('aria-expanded', 'false');
            }
        }
    }
    
    function handleSearchInput() {
        const query = elements.keywordSearch.value.trim();
        if (query.length > 0) {
            elements.searchClearBtn.style.display = 'flex';
        } else {
            elements.searchClearBtn.style.display = 'none';
        }
    }
    
    function handleSearch() {
        const query = elements.keywordSearch.value.trim();
        state.filters.search = query;
        state.currentPage = 1;
        loadGrants();
    }
    
    function clearSearch() {
        elements.keywordSearch.value = '';
        state.filters.search = '';
        elements.searchClearBtn.style.display = 'none';
        state.currentPage = 1;
        loadGrants();
    }
    
    function resetAllFilters() {
        state.filters = {
            search: '',
            municipality: MUNICIPALITY_SLUG,
            amount: '',
            status: '',
            sort: 'date_desc'
        };
        state.currentPage = 1;
        
        elements.keywordSearch.value = '';
        elements.searchClearBtn.style.display = 'none';
        
        resetCustomSelect(elements.amountSelect, '指定なし');
        resetCustomSelect(elements.statusSelect, 'すべて');
        resetCustomSelect(elements.sortSelect, '新着順');
        
        loadGrants();
    }
    
    function resetCustomSelect(selectElement, defaultText) {
        if (!selectElement) return;
        
        const valueSpan = selectElement.querySelector('.select-value');
        const options = selectElement.querySelectorAll('.select-option');
        
        valueSpan.textContent = defaultText;
        options.forEach(opt => {
            opt.classList.remove('active');
            opt.setAttribute('aria-selected', 'false');
        });
        options[0].classList.add('active');
        options[0].setAttribute('aria-selected', 'true');
    }
    
    function loadGrants() {
        if (state.isLoading) return;
        
        state.isLoading = true;
        showLoading(true);
        
        const formData = new FormData();
        formData.append('action', 'gi_ajax_load_grants');
        formData.append('nonce', NONCE);
        formData.append('page', state.currentPage);
        formData.append('posts_per_page', state.perPage);
        formData.append('view', state.view);
        
        if (state.filters.search) {
            formData.append('search', state.filters.search);
        }
        
        formData.append('municipalities', JSON.stringify([MUNICIPALITY_SLUG]));
        
        if (state.filters.amount) {
            formData.append('amount', state.filters.amount);
        }
        
        if (state.filters.status) {
            formData.append('status', JSON.stringify([state.filters.status]));
        }
        
        formData.append('sort', state.filters.sort);
        
        fetch(AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayGrants(data.data.grants);
                updateStats(data.data.stats);
                updatePagination(data.data.pagination);
                updateActiveFiltersDisplay();
            } else {
                showError('データの読み込みに失敗しました。');
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            showError('通信エラーが発生しました。');
        })
        .finally(() => {
            state.isLoading = false;
            showLoading(false);
        });
    }
    
    function displayGrants(grants) {
        if (!elements.grantsContainer) return;
        
        if (!grants || grants.length === 0) {
            elements.grantsContainer.innerHTML = '';
            elements.grantsContainer.style.display = 'none';
            if (elements.noResults) {
                elements.noResults.style.display = 'block';
            }
            return;
        }
        
        elements.grantsContainer.style.display = state.view === 'single' ? 'flex' : 'grid';
        if (elements.noResults) {
            elements.noResults.style.display = 'none';
        }
        
        const fragment = document.createDocumentFragment();
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = grants.map(grant => grant.html).join('');
        
        while (tempDiv.firstChild) {
            fragment.appendChild(tempDiv.firstChild);
        }
        
        elements.grantsContainer.innerHTML = '';
        elements.grantsContainer.appendChild(fragment);
        
        if (window.innerWidth <= 768) {
            closeMobileFilter();
        }
    }
    
    function updateStats(stats) {
        if (elements.resultsCount) {
            elements.resultsCount.textContent = (stats.total_found || 0).toLocaleString();
        }
        if (elements.showingFrom) {
            elements.showingFrom.textContent = (stats.showing_from || 0).toLocaleString();
        }
        if (elements.showingTo) {
            elements.showingTo.textContent = (stats.showing_to || 0).toLocaleString();
        }
    }
    
    function updatePagination(pagination) {
        if (!elements.paginationWrapper) return;
        
        if (!pagination || pagination.total_pages <= 1) {
            elements.paginationWrapper.innerHTML = '';
            return;
        }
        
        const currentPage = pagination.current_page || 1;
        const totalPages = pagination.total_pages || 1;
        
        let html = '<div class="page-numbers">';
        
        // Previous button
        if (currentPage > 1) {
            html += `<a href="#" class="page-numbers prev" data-page="${currentPage - 1}">前へ</a>`;
        }
        
        // Page numbers logic
        const range = 2; // Show 2 pages on each side of current
        let startPage = Math.max(1, currentPage - range);
        let endPage = Math.min(totalPages, currentPage + range);
        
        // First page
        if (startPage > 1) {
            html += `<a href="#" class="page-numbers" data-page="1">1</a>`;
            if (startPage > 2) {
                html += '<span class="page-numbers dots">…</span>';
            }
        }
        
        // Page number buttons
        for (let i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += `<span class="page-numbers current">${i}</span>`;
            } else {
                html += `<a href="#" class="page-numbers" data-page="${i}">${i}</a>`;
            }
        }
        
        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += '<span class="page-numbers dots">…</span>';
            }
            html += `<a href="#" class="page-numbers" data-page="${totalPages}">${totalPages}</a>`;
        }
        
        // Next button
        if (currentPage < totalPages) {
            html += `<a href="#" class="page-numbers next" data-page="${currentPage + 1}">次へ</a>`;
        }
        
        html += '</div>';
        
        elements.paginationWrapper.innerHTML = html;
        
        // Add click handlers to pagination links
        elements.paginationWrapper.querySelectorAll('a.page-numbers').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page && page !== currentPage) {
                    state.currentPage = page;
                    loadGrants();
                    
                    // Scroll to top of results
                    if (elements.grantsContainer) {
                        elements.grantsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    }
    
    function updateActiveFiltersDisplay() {
        if (!elements.activeFilters || !elements.activeFilterTags) return;
        
        const tags = [];
        
        if (state.filters.search) {
            tags.push({
                type: 'search',
                label: `検索: "${state.filters.search}"`,
                value: state.filters.search
            });
        }
        
        if (state.filters.amount) {
            const labels = {
                '0-100': '〜100万円',
                '100-500': '100万円〜500万円',
                '500-1000': '500万円〜1000万円',
                '1000-3000': '1000万円〜3000万円',
                '3000+': '3000万円以上'
            };
            tags.push({
                type: 'amount',
                label: `金額: ${labels[state.filters.amount]}`,
                value: state.filters.amount
            });
        }
        
        if (state.filters.status) {
            const labels = {
                'active': '募集中',
                'upcoming': '募集予定',
                'closed': '募集終了'
            };
            tags.push({
                type: 'status',
                label: `状況: ${labels[state.filters.status]}`,
                value: state.filters.status
            });
        }
        
        if (tags.length === 0) {
            elements.activeFilters.style.display = 'none';
            elements.resetAllFiltersBtn.style.display = 'none';
            if (elements.mobileFilterCount) {
                elements.mobileFilterCount.style.display = 'none';
            }
            return;
        }
        
        elements.activeFilters.style.display = 'flex';
        elements.resetAllFiltersBtn.style.display = 'flex';
        
        if (elements.mobileFilterCount) {
            elements.mobileFilterCount.textContent = tags.length;
            elements.mobileFilterCount.style.display = 'flex';
        }
        
        elements.activeFilterTags.innerHTML = tags.map(tag => `
            <div class="filter-tag">
                <span>${escapeHtml(tag.label)}</span>
                <button class="filter-tag-remove" 
                        data-type="${tag.type}" 
                        data-value="${escapeHtml(tag.value)}"
                        type="button">×</button>
            </div>
        `).join('');
        
        elements.activeFilterTags.querySelectorAll('.filter-tag-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                removeFilter(this.dataset.type, this.dataset.value);
            });
        });
    }
    
    function removeFilter(type, value) {
        switch(type) {
            case 'search':
                clearSearch();
                break;
            case 'amount':
                state.filters.amount = '';
                resetCustomSelect(elements.amountSelect, '指定なし');
                break;
            case 'status':
                state.filters.status = '';
                resetCustomSelect(elements.statusSelect, 'すべて');
                break;
        }
        
        state.currentPage = 1;
        loadGrants();
    }
    
    function showLoading(show) {
        if (elements.loadingOverlay) {
            elements.loadingOverlay.style.display = show ? 'flex' : 'none';
        }
        if (elements.grantsContainer) {
            elements.grantsContainer.style.opacity = show ? '0.5' : '1';
        }
    }
    
    function showError(message) {
        console.error('Error:', message);
        alert(message);
    }
    
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    console.log('✅ Municipality Archive v19.0 - Fully Loaded');
    
})();

// アクセスランキング タブ切り替え（同じコード）
(function() {
    'use strict';
    
    const tabs = document.querySelectorAll('.ranking-tab');
    const contents = document.querySelectorAll('.ranking-content');
    
    if (tabs.length === 0) return;
    
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            const period = this.getAttribute('data-period');
            const targetId = this.getAttribute('data-target');
            
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            contents.forEach(c => c.classList.remove('active'));
            const targetContent = document.querySelector(targetId);
            
            if (targetContent) {
                targetContent.classList.add('active');
                
                const hasLoadingDiv = targetContent.querySelector('.ranking-loading');
                
                if (hasLoadingDiv) {
                    loadRankingData(period, targetContent);
                }
            }
        });
    });
    
    function loadRankingData(period, container) {
        container.innerHTML = '<div class="ranking-loading">読み込み中...</div>';
        
        const formData = new FormData();
        formData.append('action', 'get_ranking_data');
        formData.append('period', period);
        formData.append('post_type', 'grant');
        
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                container.innerHTML = data.data;
            } else {
                container.innerHTML = '<div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;"><p style="margin: 0; font-size: 14px;">データがありません</p></div>';
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            container.innerHTML = '<div class="ranking-error" style="text-align: center; padding: 30px 20px; color: #999;"><p style="margin: 0; font-size: 14px;">エラーが発生しました</p></div>';
        });
    }
    
})();
</script>

<?php get_footer(); ?>