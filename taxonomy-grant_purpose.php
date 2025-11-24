<?php
/**
 * Purpose Archive Template for Grant - Complete Edition v21.0
 * 用途別助成金・補助金アーカイブページ - 完全版
 * 
 * @package Grant_Insight_Perfect
 * @version 21.0.0 - Purpose Specialized Complete
 * 
 * === Features ===
 * - Purpose-fixed filter (purpose selector hidden)
 * - Yahoo! JAPAN inspired design
 * - Sidebar layout (PC only) with rankings & topics
 * - Ad spaces reserved in sidebar
 * - Mobile: No sidebar, optimized single column
 * - SEO Perfect (Schema.org, OGP, Twitter Card)
 * - All archive functions preserved
 * - URL parameter support for search/category/prefecture/municipality
 */

get_header();

// 現在の用途情報を取得
$current_purpose = get_queried_object();
$purpose_name = $current_purpose->name;
$purpose_slug = $current_purpose->slug;
$purpose_description = $current_purpose->description;
$purpose_count = $current_purpose->count;
$purpose_id = $current_purpose->term_id;

// 用途メタ情報取得（カスタムフィールドがあれば）
$purpose_meta = get_term_meta($purpose_id);
$purpose_icon = isset($purpose_meta['purpose_icon']) ? $purpose_meta['purpose_icon'][0] : '';
$purpose_color = isset($purpose_meta['purpose_color']) ? $purpose_meta['purpose_color'][0] : '#000000';

// 都道府県データ
$prefectures = gi_get_all_prefectures();

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

// カテゴリーデータ
$all_categories = get_terms([
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC'
]);

// SEO用データ
$current_year = date('Y');
$current_month = date('n');
$season = ($current_month >= 3 && $current_month <= 5) ? '春' : 
          (($current_month >= 6 && $current_month <= 8) ? '夏' : 
          (($current_month >= 9 && $current_month <= 11) ? '秋' : '冬'));

// ページタイトル・説明文の生成
$page_title = $purpose_name . 'の助成金・補助金一覧【' . $current_year . '年度最新版】';
$page_description = $purpose_description ?: 
    $purpose_name . 'を目的とした助成金・補助金を' . number_format($purpose_count) . '件掲載。' . 
    $current_year . '年度の最新募集情報、申請要件、対象事業、助成金額、締切日を詳しく解説。' . 
    '都道府県・市町村別の検索にも対応し、あなたの地域で利用できる助成金を簡単に見つけられます。';

$canonical_url = get_term_link($current_purpose);

// 総件数
$total_grants = wp_count_posts('grant')->publish;
$total_grants_formatted = number_format($total_grants);

// サイドバー用：新着トピックス（用途内）
$recent_grants = new WP_Query([
    'post_type' => 'grant',
    'posts_per_page' => 5,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'tax_query' => [
        [
            'taxonomy' => 'grant_purpose',
            'field' => 'term_id',
            'terms' => $purpose_id
        ]
    ]
]);

// 関連用途（同じ親用途の兄弟用途）
$related_purposes = [];
if ($current_purpose->parent > 0) {
    $related_purposes = get_terms([
        'taxonomy' => 'grant_purpose',
        'parent' => $current_purpose->parent,
        'exclude' => [$purpose_id],
        'hide_empty' => true,
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => 6
    ]);
} else {
    // 親用途の場合は子用途を取得
    $related_purposes = get_terms([
        'taxonomy' => 'grant_purpose',
        'parent' => $purpose_id,
        'hide_empty' => true,
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => 6
    ]);
}

// パンくずリスト用データ
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')]
];

// 親用途があればパンくずに追加
if ($current_purpose->parent > 0) {
    $parent_purpose = get_term($current_purpose->parent, 'grant_purpose');
    if ($parent_purpose && !is_wp_error($parent_purpose)) {
        $breadcrumbs[] = ['name' => $parent_purpose->name, 'url' => get_term_link($parent_purpose)];
    }
}

$breadcrumbs[] = ['name' => $purpose_name, 'url' => ''];

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
        'numberOfItems' => $purpose_count,
        'itemListElement' => []
    ]
];

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

// OGP画像
$og_image = get_site_icon_url(1200) ?: home_url('/wp-content/uploads/2025/10/1.png');

// キーワード生成
$keywords = ['助成金', '補助金', $purpose_name, '検索', '申請', '支援制度', $current_year . '年度'];
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

<main class="grant-archive-yahoo-style grant-purpose-archive" 
      id="purpose-<?php echo esc_attr($purpose_slug); ?>" 
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

    <!-- 用途ヒーローセクション -->
    <header class="purpose-hero-section" 
            itemscope 
            itemtype="https://schema.org/WPHeader">
        <div class="yahoo-container">
            <div class="hero-content-wrapper">
                
                <!-- 用途バッジ -->
                <div class="purpose-badge" 
                     role="status"
                     <?php if ($purpose_color): ?>
                     style="background: <?php echo esc_attr($purpose_color); ?>;"
                     <?php endif; ?>>
                    <?php if ($purpose_icon): ?>
                        <img src="<?php echo esc_url($purpose_icon); ?>" 
                             alt="<?php echo esc_attr($purpose_name); ?>アイコン" 
                             class="badge-icon-img"
                             width="20" 
                             height="20">
                    <?php else: ?>
                        <svg class="badge-icon" 
                             width="20" 
                             height="20" 
                             viewBox="0 0 24 24" 
                             fill="none" 
                             stroke="currentColor" 
                             stroke-width="2" 
                             aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    <?php endif; ?>
                    <span>用途別助成金</span>
                </div>

                <!-- メインタイトル -->
                <h1 class="purpose-main-title" itemprop="headline">
                    <span class="purpose-name-highlight"><?php echo esc_html($purpose_name); ?></span>
                    <span class="title-text">の助成金・補助金</span>
                    <span class="year-badge"><?php echo $current_year; ?>年度版</span>
                </h1>

                <!-- 用途説明文 -->
                <div class="purpose-lead-section" itemprop="description">
                    <?php if ($purpose_description): ?>
                    <div class="purpose-description-rich">
                        <?php echo wpautop(wp_kses_post($purpose_description)); ?>
                    </div>
                    <?php endif; ?>
                    <p class="purpose-lead-sub">
                        <?php echo esc_html($purpose_name); ?>を目的とした助成金・補助金を
                        <strong><?php echo number_format($purpose_count); ?>件</strong>掲載。
                        <?php echo $current_year; ?>年度の最新募集情報を毎日更新中。
                        都道府県・市町村別の検索にも対応し、あなたの地域で利用できる助成金を簡単に見つけられます。
                    </p>
                </div>

                <!-- メタ情報 -->
                <div class="purpose-meta-info" role="group" aria-label="用途統計情報">
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
                        <strong itemprop="value"><?php echo number_format($purpose_count); ?></strong>
                        <span itemprop="unitText">件の助成金</span>
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
                        <span>地域別対応</span>
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
                            <p>最新の募集情報・締切情報を毎日チェック。見逃しを防ぎます。</p>
                        </div>
                    </article>

                    <article class="feature-card">
                        <div class="feature-card-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3>用途特化型</h3>
                            <p><?php echo esc_html($purpose_name); ?>に最適な助成金を厳選してご紹介。</p>
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
                            <p>申請方法から採択のコツまで、専門家監修の情報を提供。</p>
                        </div>
                    </article>
                </div>

                <!-- 関連用途 -->
                <?php if (!empty($related_purposes) && !is_wp_error($related_purposes)): ?>
                <div class="related-purposes-section">
                    <h2 class="related-purposes-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                        <?php echo $current_purpose->parent > 0 ? '関連する用途' : 'サブ用途'; ?>
                    </h2>
                    <div class="related-purposes-grid">
                        <?php foreach ($related_purposes as $rel_purpose): ?>
                        <a href="<?php echo esc_url(get_term_link($rel_purpose)); ?>" 
                           class="related-purpose-card"
                           title="<?php echo esc_attr($rel_purpose->name); ?>の助成金を見る">
                            <span class="related-purpose-name"><?php echo esc_html($rel_purpose->name); ?></span>
                            <span class="related-purpose-count"><?php echo number_format($rel_purpose->count); ?>件</span>
                        </a>
                        <?php endforeach; ?>
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
                               data-purpose="<?php echo esc_attr($purpose_slug); ?>"
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

            <!-- プルダウン式フィルターセクション（用途選択は非表示） -->
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

                <!-- プルダウンフィルターグリッド（用途選択を除外） -->
                <div class="yahoo-filters-grid">
                    
                    <!-- カテゴリー選択 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="category-label">カテゴリー
                            <span class="multi-select-badge" 
                                  id="category-count-badge" 
                                  style="display: none;">0</span>
                        </label>
                        <div class="custom-select multi-select" 
                             id="category-select" 
                             role="combobox" 
                             aria-labelledby="category-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">選択</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown multi-select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-search-wrapper">
                                    <input type="search" 
                                           class="select-search-input" 
                                           placeholder="検索..."
                                           id="category-search"
                                           autocomplete="off">
                                </div>
                                <div class="select-options-wrapper" id="category-options">
                                    <div class="select-option all-option" 
                                         data-value="" 
                                         role="option">
                                        <input type="checkbox" 
                                               id="cat-all" 
                                               class="option-checkbox">
                                        <label for="cat-all">すべて</label>
                                    </div>
                                    <?php foreach ($all_categories as $index => $cat): ?>
                                        <div class="select-option" 
                                             data-value="<?php echo esc_attr($cat->slug); ?>"
                                             data-name="<?php echo esc_attr($cat->name); ?>"
                                             role="option">
                                            <input type="checkbox" 
                                                   id="cat-<?php echo $index; ?>" 
                                                   class="option-checkbox" 
                                                   value="<?php echo esc_attr($cat->slug); ?>">
                                            <label for="cat-<?php echo $index; ?>">
                                                <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="select-actions">
                                    <button class="select-action-btn clear-btn" 
                                            id="clear-category-btn" 
                                            type="button">クリア</button>
                                    <button class="select-action-btn apply-btn" 
                                            id="apply-category-btn" 
                                            type="button">適用</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 地域選択 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="region-label">地域</label>
                        <div class="custom-select" 
                             id="region-select" 
                             role="combobox" 
                             aria-labelledby="region-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">全国</span>
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
                                     role="option">全国</div>
                                <?php foreach ($region_groups as $region_slug => $region_name): ?>
                                    <div class="select-option" 
                                         data-value="<?php echo esc_attr($region_slug); ?>" 
                                         role="option">
                                        <?php echo esc_html($region_name); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 都道府県選択 -->
                    <div class="filter-dropdown-wrapper">
                        <label class="filter-label" id="prefecture-label">都道府県
                            <span class="multi-select-badge" 
                                  id="prefecture-count-badge" 
                                  style="display: none;">0</span>
                        </label>
                        <div class="custom-select multi-select" 
                             id="prefecture-select" 
                             role="combobox" 
                             aria-labelledby="prefecture-label" 
                             aria-expanded="false">
                            <button class="select-trigger" 
                                    type="button" 
                                    aria-haspopup="listbox">
                                <span class="select-value">選択</span>
                                <svg class="select-arrow" 
                                     width="14" 
                                     height="14" 
                                     viewBox="0 0 24 24" 
                                     fill="currentColor" 
                                     aria-hidden="true">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </button>
                            <div class="select-dropdown multi-select-dropdown" 
                                 role="listbox" 
                                 style="display: none;">
                                <div class="select-search-wrapper">
                                    <input type="search" 
                                           class="select-search-input" 
                                           placeholder="検索..."
                                           id="prefecture-search"
                                           autocomplete="off">
                                </div>
                                <div class="select-options-wrapper" id="prefecture-options">
                                    <div class="select-option all-option" 
                                         data-value="" 
                                         role="option">
                                        <input type="checkbox" 
                                               id="pref-all" 
                                               class="option-checkbox">
                                        <label for="pref-all">すべて</label>
                                    </div>
                                    <?php foreach ($prefectures as $index => $pref): ?>
                                        <div class="select-option" 
                                             data-value="<?php echo esc_attr($pref['slug']); ?>"
                                             data-region="<?php echo esc_attr($pref['region']); ?>"
                                             data-name="<?php echo esc_attr($pref['name']); ?>"
                                             role="option">
                                            <input type="checkbox" 
                                                   id="pref-<?php echo $index; ?>" 
                                                   class="option-checkbox" 
                                                   value="<?php echo esc_attr($pref['slug']); ?>">
                                            <label for="pref-<?php echo $index; ?>">
                                                <?php echo esc_html($pref['name']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="select-actions">
                                    <button class="select-action-btn clear-btn" 
                                            id="clear-prefecture-btn" 
                                            type="button">クリア</button>
                                    <button class="select-action-btn apply-btn" 
                                            id="apply-prefecture-btn" 
                                            type="button">適用</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 市町村選択 -->
                    <div class="filter-dropdown-wrapper" 
                         id="municipality-wrapper" 
                         style="display: none;">
                        <label class="filter-label" id="municipality-label">市町村
                            <span class="selected-prefecture-name" 
                                  id="selected-prefecture-name"></span>
                        </label>
                        <div class="custom-select" 
                             id="municipality-select" 
                             role="combobox" 
                             aria-labelledby="municipality-label" 
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
                                <div class="select-search-wrapper">
                                    <input type="search" 
                                           class="select-search-input" 
                                           placeholder="検索..."
                                           id="municipality-search"
                                           autocomplete="off">
                                </div>
                                <div class="select-options-wrapper" id="municipality-options">
                                    <div class="select-option active" 
                                         data-value="" 
                                         role="option">すべて</div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                    // 初期表示用WP_Query（用途固定）
                    $initial_query = new WP_Query([
                        'post_type' => 'grant',
                        'posts_per_page' => 12,
                        'post_status' => 'publish',
                        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
                        'tax_query' => [
                            [
                                'taxonomy' => 'grant_purpose',
                                'field' => 'term_id',
                                'terms' => $purpose_id
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
                <?php ji_display_ad('purpose_grant_sidebar_top', 'taxonomy-grant_purpose'); ?>
            </div>
            <?php endif; ?>

            <!-- 広告枠2: サイドバー中央 -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="sidebar-ad-space sidebar-ad-middle">
                <?php ji_display_ad('purpose_grant_sidebar_middle', 'taxonomy-grant_purpose'); ?>
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

            <!-- 新着トピックス（用途内） -->
            <section class="sidebar-widget sidebar-topics">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                        <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                        <line x1="6" y1="1" x2="6" y2="4"/>
                        <line x1="10" y1="1" x2="10" y2="4"/>
                        <line x1="14" y1="1" x2="14" y2="4"/>
                    </svg>
                    <?php echo esc_html($purpose_name); ?>の新着トピックス
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
                <?php ji_display_ad('purpose_grant_sidebar_bottom', 'taxonomy-grant_purpose'); ?>
            </div>
            <?php endif; ?>

            <!-- 関連用途（サイドバー版） -->
            <?php if (!empty($related_purposes) && !is_wp_error($related_purposes)): ?>
            <section class="sidebar-widget sidebar-related-purposes">
                <h3 class="widget-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <?php echo $current_purpose->parent > 0 ? '関連する用途' : 'サブ用途'; ?>
                </h3>
                <div class="widget-content">
                    <ul class="related-purposes-list">
                        <?php foreach (array_slice($related_purposes, 0, 5) as $rel_purpose): ?>
                        <li class="related-purpose-item">
                            <a href="<?php echo esc_url(get_term_link($rel_purpose)); ?>" class="related-purpose-link">
                                <?php echo esc_html($rel_purpose->name); ?>
                                <span class="related-purpose-count">(<?php echo number_format($rel_purpose->count); ?>)</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
            <?php endif; ?>
        </aside>
    </div>

    <!-- SEOコンテンツセクション -->
    <section class="seo-content-section">
        <div class="yahoo-container">
            <div class="seo-content-wrapper">
                <h2 class="seo-title"><?php echo esc_html($purpose_name); ?>の助成金について</h2>
                <div class="seo-text">
                    <p>
                        <?php echo esc_html($purpose_name); ?>を目的とした助成金・補助金は、
                        事業者の皆様が特定の目標を達成するための重要な資金調達手段です。
                        当サイトでは、<?php echo $current_year; ?>年度に募集される<?php echo esc_html($purpose_name); ?>関連の助成金情報を
                        <?php echo number_format($purpose_count); ?>件掲載しており、
                        国や自治体、民間団体が実施する様々な制度を幅広くカバーしています。
                    </p>
                    <p>
                        <?php echo esc_html($purpose_name); ?>の助成金は、用途に応じた専門的な支援を提供しており、
                        申請要件や対象事業も明確に定められています。
                        各助成金の詳細な申請方法や必要書類、採択のポイントについては、
                        各制度の詳細ページでご確認ください。
                    </p>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- CSS（完全版・省略なし） -->
<!-- カテゴリーアーカイブと共通のCSSを使用するため、
     用途アーカイブ特有のスタイルのみを追加 -->
<style>
/* ===== Purpose Specific Styles ===== */

.purpose-hero-section {
    padding: 40px 0;
    background: linear-gradient(135deg, #fff3e0 0%, #ffffff 100%);
    border-bottom: 2px solid var(--yahoo-gray-200);
    position: relative;
}

.purpose-hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at top right, rgba(255, 152, 0, 0.05) 0%, transparent 50%);
    pointer-events: none;
}

.purpose-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: #ff9800;
    color: var(--yahoo-secondary);
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 16px;
    border-radius: 20px;
}

.purpose-main-title {
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

.purpose-name-highlight {
    background: linear-gradient(180deg, transparent 60%, rgba(255, 152, 0, 0.3) 60%);
    padding: 0 4px;
    display: inline-block;
}

.purpose-lead-section {
    margin: 24px 0;
}

.purpose-description-rich {
    margin-bottom: 20px;
    line-height: 1.8;
    color: var(--yahoo-gray-700);
    font-size: 15px;
}

.purpose-description-rich p {
    margin: 0 0 16px 0;
}

.purpose-description-rich p:last-child {
    margin-bottom: 0;
}

.purpose-lead-sub {
    font-size: 15px;
    color: var(--yahoo-gray-600);
    margin: 0;
    line-height: 1.7;
}

.purpose-lead-sub strong {
    color: var(--yahoo-primary);
    font-weight: 700;
}

.purpose-meta-info {
    display: flex;
    align-items: center;
    gap: 24px;
    margin: 24px 0;
    flex-wrap: wrap;
}

/* ===== Related Purposes Section ===== */
.related-purposes-section {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px solid var(--yahoo-gray-200);
}

.related-purposes-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: 700;
    color: var(--yahoo-primary);
    margin: 0 0 20px 0;
}

.related-purposes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}

.related-purpose-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    background: var(--yahoo-secondary);
    border: 2px solid var(--yahoo-gray-200);
    border-radius: var(--yahoo-radius);
    text-decoration: none;
    transition: all var(--transition-fast);
    color: var(--yahoo-primary);
}

.related-purpose-card:hover {
    border-color: #ff9800;
    transform: translateY(-2px);
    box-shadow: var(--yahoo-shadow);
}

.related-purpose-name {
    font-size: 14px;
    font-weight: 600;
}

.related-purpose-count {
    font-size: 12px;
    color: var(--yahoo-gray-500);
}

/* ===== Sidebar Related Purposes ===== */
.sidebar-related-purposes .related-purposes-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.related-purpose-item {
    border-bottom: 1px solid var(--yahoo-gray-200);
    padding-bottom: 8px;
}

.related-purpose-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.related-purpose-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    color: var(--yahoo-primary);
    font-size: 13px;
    font-weight: 600;
    transition: color var(--transition-fast);
}

.related-purpose-link:hover {
    color: #ff9800;
    text-decoration: underline;
}

.sidebar-related-purposes .related-purpose-count {
    font-size: 11px;
    color: var(--yahoo-gray-500);
    font-weight: 400;
}

/* ===== SEO Content Section ===== */
.seo-content-section {
    padding: 60px 0;
    background: var(--yahoo-gray-50);
    border-top: 1px solid var(--yahoo-gray-200);
}

.seo-content-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

.seo-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--yahoo-primary);
    margin: 0 0 24px 0;
    text-align: center;
}

.seo-text {
    font-size: 16px;
    color: var(--yahoo-gray-700);
    line-height: 1.8;
}

.seo-text p {
    margin: 0 0 20px 0;
    text-align: left;
}

.seo-text p:last-child {
    margin-bottom: 0;
}

/* ===== Mobile Filter ===== */
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

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .mobile-filter-toggle {
        display: flex !important;
    }
    
    .mobile-filter-apply-section {
        display: block !important;
    }
    
    .purpose-main-title {
        font-size: 28px;
    }
    
    .title-text {
        font-size: 24px;
    }
    
    .year-badge {
        font-size: 12px;
        padding: 3px 10px;
    }
    
    .purpose-meta-info {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }
    
    .feature-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .related-purposes-grid {
        grid-template-columns: 1fr;
    }
    
    .seo-title {
        font-size: 24px;
    }
    
    .seo-text {
        font-size: 15px;
    }
}

@media (max-width: 480px) {
    .purpose-main-title {
        font-size: 24px;
    }
    
    .title-text {
        font-size: 20px;
    }
}
</style>

<!-- JavaScript（完全版・省略なし） -->
<script>
(function() {
    'use strict';
    
    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    const NONCE = '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>';
    const PURPOSE_SLUG = '<?php echo esc_js($purpose_slug); ?>';
    const PURPOSE_ID = <?php echo $purpose_id; ?>;
    
    const state = {
        currentPage: 1,
        perPage: 12,
        view: 'single',
        filters: {
            search: '',
            category: [],
            purpose: [PURPOSE_SLUG],
            prefecture: [],
            municipality: '',
            region: '',
            amount: '',
            status: '',
            sort: 'date_desc'
        },
        isLoading: false,
        tempCategories: [],
        tempPrefectures: [],
        currentMunicipalities: []
    };
    
    const elements = {};
    
    function init() {
        console.log('🚀 Purpose Archive v21.0 Initialized:', PURPOSE_SLUG);
        
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
        
        elements.categorySelect = document.getElementById('category-select');
        elements.categorySearch = document.getElementById('category-search');
        elements.categoryOptions = document.getElementById('category-options');
        elements.clearCategoryBtn = document.getElementById('clear-category-btn');
        elements.applyCategoryBtn = document.getElementById('apply-category-btn');
        elements.categoryCountBadge = document.getElementById('category-count-badge');
        
        elements.regionSelect = document.getElementById('region-select');
        
        elements.prefectureSelect = document.getElementById('prefecture-select');
        elements.prefectureSearch = document.getElementById('prefecture-search');
        elements.prefectureOptions = document.getElementById('prefecture-options');
        elements.clearPrefectureBtn = document.getElementById('clear-prefecture-btn');
        elements.applyPrefectureBtn = document.getElementById('apply-prefecture-btn');
        elements.prefectureCountBadge = document.getElementById('prefecture-count-badge');
        
        elements.municipalitySelect = document.getElementById('municipality-select');
        elements.municipalityWrapper = document.getElementById('municipality-wrapper');
        elements.municipalitySearch = document.getElementById('municipality-search');
        elements.municipalityOptions = document.getElementById('municipality-options');
        elements.selectedPrefectureName = document.getElementById('selected-prefecture-name');
        
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
            console.log('🔍 Search keyword from URL:', searchParam);
        }
        
        const categoryParam = urlParams.get('category');
        if (categoryParam) {
            state.filters.category = [categoryParam];
            console.log('📁 Category from URL:', categoryParam);
        }
        
        const prefectureParam = urlParams.get('prefecture');
        if (prefectureParam) {
            state.filters.prefecture = [prefectureParam];
            console.log('📍 Prefecture from URL:', prefectureParam);
        }
        
        const municipalityParam = urlParams.get('municipality');
        if (municipalityParam) {
            state.filters.municipality = municipalityParam;
            console.log('🏘️ Municipality from URL:', municipalityParam);
        }
    }
    
    function setupCustomSelects() {
        setupMultiSelectCategory();
        
        setupSingleSelect(elements.regionSelect, (value) => {
            state.filters.region = value;
            filterPrefecturesByRegion(value);
            state.currentPage = 1;
            loadGrants();
        });
        
        setupMultiSelectPrefecture();
        setupMunicipalitySelect();
        
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
    
    function setupMultiSelectCategory() {
        if (!elements.categorySelect) return;
        
        const trigger = elements.categorySelect.querySelector('.select-trigger');
        const dropdown = elements.categorySelect.querySelector('.select-dropdown');
        const valueSpan = elements.categorySelect.querySelector('.select-value');
        const checkboxes = elements.categoryOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('cat-all');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.categorySelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.categorySelect.classList.add('active');
                elements.categorySelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
                state.tempCategories = [...state.filters.category];
                updateCategoryCheckboxes();
            }
        });
        
        if (elements.categorySearch) {
            elements.categorySearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.categoryOptions.querySelectorAll('.select-option:not(.all-option)');
                
                options.forEach(option => {
                    const name = option.dataset.name.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
        
        if (allCheckbox) {
            allCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    state.tempCategories = [];
                    checkboxes.forEach(cb => {
                        if (cb !== allCheckbox) {
                            cb.checked = false;
                        }
                    });
                }
            });
        }
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    
                    if (e.target.checked) {
                        if (!state.tempCategories.includes(value)) {
                            state.tempCategories.push(value);
                        }
                        allCheckbox.checked = false;
                    } else {
                        const index = state.tempCategories.indexOf(value);
                        if (index > -1) {
                            state.tempCategories.splice(index, 1);
                        }
                        if (state.tempCategories.length === 0) {
                            allCheckbox.checked = true;
                        }
                    }
                });
            }
        });
        
        if (elements.clearCategoryBtn) {
            elements.clearCategoryBtn.addEventListener('click', () => {
                state.tempCategories = [];
                updateCategoryCheckboxes();
                allCheckbox.checked = true;
            });
        }
        
        if (elements.applyCategoryBtn) {
            elements.applyCategoryBtn.addEventListener('click', () => {
                state.filters.category = [...state.tempCategories];
                updateCategoryDisplay();
                elements.categorySelect.classList.remove('active');
                elements.categorySelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                state.currentPage = 1;
                loadGrants();
            });
        }
    }
    
    function updateCategoryCheckboxes() {
        const checkboxes = elements.categoryOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('cat-all');
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.checked = state.tempCategories.includes(checkbox.value);
            }
        });
        
        allCheckbox.checked = state.tempCategories.length === 0;
    }
    
    function updateCategoryDisplay() {
        const valueSpan = elements.categorySelect.querySelector('.select-value');
        const count = state.filters.category.length;
        
        if (count === 0) {
            valueSpan.textContent = '選択';
            elements.categoryCountBadge.style.display = 'none';
        } else {
            valueSpan.textContent = `${count}件選択`;
            elements.categoryCountBadge.textContent = count;
            elements.categoryCountBadge.style.display = 'inline-flex';
        }
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
    
    function setupMultiSelectPrefecture() {
        if (!elements.prefectureSelect) return;
        
        const trigger = elements.prefectureSelect.querySelector('.select-trigger');
        const dropdown = elements.prefectureSelect.querySelector('.select-dropdown');
        const valueSpan = elements.prefectureSelect.querySelector('.select-value');
        const checkboxes = elements.prefectureOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('pref-all');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.prefectureSelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.prefectureSelect.classList.add('active');
                elements.prefectureSelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
                state.tempPrefectures = [...state.filters.prefecture];
                updatePrefectureCheckboxes();
            }
        });
        
        if (elements.prefectureSearch) {
            elements.prefectureSearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.prefectureOptions.querySelectorAll('.select-option:not(.all-option)');
                
                options.forEach(option => {
                    const name = option.dataset.name.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
        
        if (allCheckbox) {
            allCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    state.tempPrefectures = [];
                    checkboxes.forEach(cb => {
                        if (cb !== allCheckbox) {
                            cb.checked = false;
                        }
                    });
                }
            });
        }
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.addEventListener('change', (e) => {
                    const value = e.target.value;
                    
                    if (e.target.checked) {
                        if (!state.tempPrefectures.includes(value)) {
                            state.tempPrefectures.push(value);
                        }
                        allCheckbox.checked = false;
                    } else {
                        const index = state.tempPrefectures.indexOf(value);
                        if (index > -1) {
                            state.tempPrefectures.splice(index, 1);
                        }
                        if (state.tempPrefectures.length === 0) {
                            allCheckbox.checked = true;
                        }
                    }
                });
            }
        });
        
        if (elements.clearPrefectureBtn) {
            elements.clearPrefectureBtn.addEventListener('click', () => {
                state.tempPrefectures = [];
                updatePrefectureCheckboxes();
                allCheckbox.checked = true;
            });
        }
        
        if (elements.applyPrefectureBtn) {
            elements.applyPrefectureBtn.addEventListener('click', () => {
                state.filters.prefecture = [...state.tempPrefectures];
                updatePrefectureDisplay();
                elements.prefectureSelect.classList.remove('active');
                elements.prefectureSelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                if (state.filters.prefecture.length === 1) {
                    const prefectureSlug = state.filters.prefecture[0];
                    const prefectureOption = document.querySelector(`.select-option[data-value="${prefectureSlug}"]`);
                    const prefectureName = prefectureOption ? prefectureOption.dataset.name : '';
                    loadMunicipalities(prefectureSlug, prefectureName);
                } else {
                    hideMunicipalityFilter();
                    state.filters.municipality = '';
                }
                
                state.currentPage = 1;
                loadGrants();
            });
        }
    }
    
    function setupMunicipalitySelect() {
        if (!elements.municipalitySelect) return;
        
        const trigger = elements.municipalitySelect.querySelector('.select-trigger');
        const dropdown = elements.municipalitySelect.querySelector('.select-dropdown');
        const valueSpan = elements.municipalitySelect.querySelector('.select-value');
        
        trigger.addEventListener('click', () => {
            const isActive = elements.municipalitySelect.classList.contains('active');
            closeAllSelects();
            if (!isActive) {
                elements.municipalitySelect.classList.add('active');
                elements.municipalitySelect.setAttribute('aria-expanded', 'true');
                dropdown.style.display = 'block';
            }
        });
        
        if (elements.municipalitySearch) {
            elements.municipalitySearch.addEventListener('input', (e) => {
                const query = e.target.value.toLowerCase();
                const options = elements.municipalityOptions.querySelectorAll('.select-option');
                
                options.forEach(option => {
                    const name = option.textContent.toLowerCase();
                    if (name.includes(query)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });
        }
    }
    
    function updatePrefectureCheckboxes() {
        const checkboxes = elements.prefectureOptions.querySelectorAll('.option-checkbox');
        const allCheckbox = document.getElementById('pref-all');
        
        checkboxes.forEach(checkbox => {
            if (checkbox !== allCheckbox) {
                checkbox.checked = state.tempPrefectures.includes(checkbox.value);
            }
        });
        
        allCheckbox.checked = state.tempPrefectures.length === 0;
    }
    
    function updatePrefectureDisplay() {
        const valueSpan = elements.prefectureSelect.querySelector('.select-value');
        const count = state.filters.prefecture.length;
        
        if (count === 0) {
            valueSpan.textContent = '選択';
            elements.prefectureCountBadge.style.display = 'none';
        } else {
            valueSpan.textContent = `${count}件選択`;
            elements.prefectureCountBadge.textContent = count;
            elements.prefectureCountBadge.style.display = 'inline-flex';
        }
    }
    
    function filterPrefecturesByRegion(region) {
        if (!elements.prefectureOptions) return;
        
        const options = elements.prefectureOptions.querySelectorAll('.select-option:not(.all-option)');
        
        options.forEach(option => {
            const optionRegion = option.dataset.region;
            if (!region || optionRegion === region) {
                option.style.display = 'flex';
            } else {
                option.style.display = 'none';
            }
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
    
    function loadMunicipalities(prefectureSlug, prefectureName) {
        if (!prefectureSlug) {
            hideMunicipalityFilter();
            return;
        }
        
        if (elements.municipalityWrapper) {
            elements.municipalityWrapper.style.display = 'block';
        }
        
        if (elements.selectedPrefectureName) {
            elements.selectedPrefectureName.textContent = `（${prefectureName}）`;
        }
        
        const formData = new FormData();
        formData.append('action', 'gi_get_municipalities_for_prefecture');
        formData.append('prefecture_slug', prefectureSlug);
        formData.append('nonce', NONCE);
        
        fetch(AJAX_URL, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let municipalities = [];
            
            if (data.success) {
                if (data.data && data.data.data && Array.isArray(data.data.data.municipalities)) {
                    municipalities = data.data.data.municipalities;
                } else if (data.data && Array.isArray(data.data.municipalities)) {
                    municipalities = data.data.municipalities;
                } else if (Array.isArray(data.municipalities)) {
                    municipalities = data.municipalities;
                } else if (Array.isArray(data.data)) {
                    municipalities = data.data;
                }
            }
            
            if (municipalities.length > 0) {
                state.currentMunicipalities = municipalities;
                renderMunicipalityOptions(municipalities);
            } else {
                renderMunicipalityOptions([]);
            }
        })
        .catch(error => {
            console.error('Municipality fetch error:', error);
            renderMunicipalityOptions([]);
        });
    }
    
    function renderMunicipalityOptions(municipalities) {
        if (!elements.municipalityOptions) return;
        
        let html = '<div class="select-option active" data-value="" role="option">すべて</div>';
        
        municipalities.forEach(municipality => {
            html += `<div class="select-option" data-value="${municipality.slug}" role="option">${municipality.name}</div>`;
        });
        
        elements.municipalityOptions.innerHTML = html;
        
        const options = elements.municipalityOptions.querySelectorAll('.select-option');
        const valueSpan = elements.municipalitySelect.querySelector('.select-value');
        const dropdown = elements.municipalitySelect.querySelector('.select-dropdown');
        
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
                
                elements.municipalitySelect.classList.remove('active');
                elements.municipalitySelect.setAttribute('aria-expanded', 'false');
                dropdown.style.display = 'none';
                
                state.filters.municipality = value;
                // モバイルではフィルターパネルを閉じない（適用ボタンで一括適用）
                if (window.innerWidth > 768) {
                    state.currentPage = 1;
                    loadGrants();
                }
            });
        });
    }
    
    function hideMunicipalityFilter() {
        if (elements.municipalityWrapper) {
            elements.municipalityWrapper.style.display = 'none';
        }
        
        state.filters.municipality = '';
        if (elements.municipalitySelect) {
            const valueSpan = elements.municipalitySelect.querySelector('.select-value');
            if (valueSpan) {
                valueSpan.textContent = 'すべて';
            }
        }
    }
    
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
            category: [],
            purpose: [PURPOSE_SLUG],
            prefecture: [],
            municipality: '',
            region: '',
            amount: '',
            status: '',
            sort: 'date_desc'
        };
        state.tempCategories = [];
        state.tempPrefectures = [];
        state.currentPage = 1;
        
        elements.keywordSearch.value = '';
        elements.searchClearBtn.style.display = 'none';
        
        updateCategoryDisplay();
        updateCategoryCheckboxes();
        
        resetCustomSelect(elements.regionSelect, '全国');
        
        updatePrefectureDisplay();
        updatePrefectureCheckboxes();
        
        resetCustomSelect(elements.amountSelect, '指定なし');
        resetCustomSelect(elements.statusSelect, 'すべて');
        resetCustomSelect(elements.sortSelect, '新着順');
        
        filterPrefecturesByRegion('');
        hideMunicipalityFilter();
        
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
        
        if (state.filters.category.length > 0) {
            formData.append('categories', JSON.stringify(state.filters.category));
        }
        
        formData.append('purposes', JSON.stringify([PURPOSE_SLUG]));
        
        if (state.filters.prefecture.length > 0) {
            formData.append('prefectures', JSON.stringify(state.filters.prefecture));
        }
        
        if (state.filters.municipality && state.filters.municipality !== '') {
            formData.append('municipalities', JSON.stringify([state.filters.municipality]));
        }
        
        if (state.filters.region) {
            formData.append('region', state.filters.region);
        }
        
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
        
        if (state.filters.category.length > 0) {
            state.filters.category.forEach(catSlug => {
                const option = document.querySelector(`.select-option[data-value="${catSlug}"]`);
                if (option) {
                    tags.push({
                        type: 'category',
                        label: option.dataset.name || option.textContent.trim(),
                        value: catSlug
                    });
                }
            });
        }
        
        if (state.filters.prefecture.length > 0) {
            state.filters.prefecture.forEach(prefSlug => {
                const option = document.querySelector(`.select-option[data-value="${prefSlug}"]`);
                if (option) {
                    tags.push({
                        type: 'prefecture',
                        label: option.dataset.name || option.textContent.trim(),
                        value: prefSlug
                    });
                }
            });
        }
        
        if (state.filters.municipality) {
            const municipalityOption = Array.from(elements.municipalityOptions.querySelectorAll('.select-option')).find(opt => opt.dataset.value === state.filters.municipality);
            if (municipalityOption) {
                tags.push({
                    type: 'municipality',
                    label: `市町村: ${municipalityOption.textContent.trim()}`,
                    value: state.filters.municipality
                });
            }
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
            case 'category':
                const catIndex = state.filters.category.indexOf(value);
                if (catIndex > -1) {
                    state.filters.category.splice(catIndex, 1);
                }
                state.tempCategories = [...state.filters.category];
                updateCategoryDisplay();
                updateCategoryCheckboxes();
                break;
            case 'prefecture':
                const prefIndex = state.filters.prefecture.indexOf(value);
                if (prefIndex > -1) {
                    state.filters.prefecture.splice(prefIndex, 1);
                }
                state.tempPrefectures = [...state.filters.prefecture];
                updatePrefectureDisplay();
                updatePrefectureCheckboxes();
                
                if (state.filters.prefecture.length !== 1) {
                    hideMunicipalityFilter();
                }
                break;
            case 'municipality':
                state.filters.municipality = '';
                const valueSpan = elements.municipalitySelect.querySelector('.select-value');
                if (valueSpan) {
                    valueSpan.textContent = 'すべて';
                }
                const options = elements.municipalityOptions.querySelectorAll('.select-option');
                options.forEach(opt => {
                    opt.classList.remove('active');
                    opt.setAttribute('aria-selected', 'false');
                });
                options[0].classList.add('active');
                options[0].setAttribute('aria-selected', 'true');
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
    
    console.log('✅ Purpose Archive v21.0 - Fully Loaded');
    
})();

// アクセスランキング タブ切り替え
(function() {
    'use strict';
    
    const tabs = document.querySelectorAll('.ranking-tab');
    const contents = document.querySelectorAll('.ranking-content');
    
    if (tabs.length === 0) return;
    
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            const period = this.getAttribute('data-period');
            const targetId = this.getAttribute('data-target');
            
            console.log('📊 Tab clicked - Period:', period, 'Target:', targetId);
            
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            contents.forEach(c => c.classList.remove('active'));
            const targetContent = document.querySelector(targetId);
            
            if (targetContent) {
                targetContent.classList.add('active');
                console.log('✅ Target content found:', targetId);
                
                const hasLoadingDiv = targetContent.querySelector('.ranking-loading');
                console.log('🔍 Has loading div:', hasLoadingDiv !== null);
                
                if (hasLoadingDiv) {
                    loadRankingData(period, targetContent);
                } else {
                    console.log('ℹ️ Data already loaded for this period');
                }
            } else {
                console.error('❌ Target content not found:', targetId);
            }
        });
    });
    
    function loadRankingData(period, container) {
        console.log('🔄 Loading ranking data for period:', period);
        
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
        .then(response => {
            console.log('📡 Response received:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Data parsed:', data);
            if (data.success && data.data) {
                container.innerHTML = data.data;
                console.log('✅ Ranking loaded successfully');
            } else {
                console.warn('⚠️ No data in response');
                container.innerHTML = '<div class="ranking-empty" style="text-align: center; padding: 30px 20px; color: #666;"><p style="margin: 0; font-size: 14px;">データがありません</p></div>';
            }
        })
        .catch(error => {
            console.error('❌ Fetch Error:', error);
            container.innerHTML = '<div class="ranking-error" style="text-align: center; padding: 30px 20px; color: #999;"><p style="margin: 0; font-size: 14px;">エラーが発生しました</p><p style="margin: 5px 0 0; font-size: 12px; opacity: 0.7;">しばらくしてから再度お試しください</p></div>';
        });
    }
    
    console.log('✅ Ranking tabs initialized');
})();

// デバッグ: 広告関数の存在確認
(function() {
    console.log('\n🔍 === Ad Function Debug Info (Purpose Archive) ===');
    console.log('📍 Page: taxonomy-grant_purpose.php');
    
    <?php
    echo "console.log('🔵 PHP Debug Info:');";
    echo "console.log('  - ji_display_ad exists: " . (function_exists('ji_display_ad') ? 'YES ✅' : 'NO ❌') . "');";
    echo "console.log('  - JI_Affiliate_Ad_Manager class exists: " . (class_exists('JI_Affiliate_Ad_Manager') ? 'YES ✅' : 'NO ❌') . "');";
    echo "console.log('  - Purpose: " . esc_js($purpose_name) . "');";
    echo "console.log('  - Purpose Slug: " . esc_js($purpose_slug) . "');";
    ?>
    
    const adSpaces = document.querySelectorAll('.sidebar-ad-space');
    console.log(`  - Total ad spaces found: ${adSpaces.length}`);
    
    adSpaces.forEach((space, index) => {
        const className = space.className;
        const hasContent = space.innerHTML.trim().length > 0;
        console.log(`  - Ad space #${index + 1}: ${className}`);
        console.log(`    Content: ${hasContent ? 'YES ✅' : 'EMPTY ❌'}`);
        if (hasContent) {
            console.log(`    HTML length: ${space.innerHTML.trim().length} chars`);
        }
    });
    
    console.log('\n💡 Purpose Archive Ad Spaces:');
    console.log('  1. purpose_grant_sidebar_top');
    console.log('  2. purpose_grant_sidebar_middle');
    console.log('  3. purpose_grant_sidebar_bottom');
    console.log('🔍 ================================\n');
})();
</script>

<?php get_footer(); ?>