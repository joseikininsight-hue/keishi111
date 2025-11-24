<?php
/**
 * Purpose Page Template for Grant - 用途別補助金一覧 v2.0
 * 
 * Matches design and filter functionality with taxonomy-grant_category.php
 * Uses actual category terms from database via purpose-to-category mapping
 * 
 * @package Grant_Insight_Perfect
 * @version 2.0.0
 */

get_header();

// Get purpose slug from URL
$purpose_slug = get_query_var('gi_purpose');

// Purpose categories data - v2.1: Updated to 8 main + 5 additional structure
// Matches the updated front-page section-search.php design
$purpose_categories = array(
    // ===== 8 Main Purposes =====
    array(
        'title' => '設備を導入したい',
        'icon' => 'fas fa-industry',
        'slug' => 'equipment',
        'url' => home_url('/purpose/equipment/'),
        'description' => '製造設備・機械・IT機器など、事業に必要な設備投資を支援する助成金',
        'keywords' => '設備投資, ものづくり補助金, IT導入補助金, 生産性向上, DX'
    ),
    array(
        'title' => '人材育成したい',
        'icon' => 'fas fa-user-graduate',
        'slug' => 'training',
        'url' => home_url('/purpose/training/'),
        'description' => '従業員の研修・資格取得・スキルアップなど、人材育成と雇用を支援',
        'keywords' => '人材育成, 人材確保, 雇用維持, 働き方改革, キャリアアップ'
    ),
    array(
        'title' => '営業強化したい',
        'icon' => 'fas fa-chart-line',
        'slug' => 'sales',
        'url' => home_url('/purpose/sales/'),
        'description' => '販路開拓・マーケティング・広告宣伝など、営業活動を支援する助成金',
        'keywords' => '販路拡大, 事業拡大, 新規事業, EC, 海外展開'
    ),
    array(
        'title' => '事業を始めたい',
        'icon' => 'fas fa-rocket',
        'slug' => 'startup',
        'url' => home_url('/purpose/startup/'),
        'description' => '創業・起業・新規事業の立ち上げを支援する助成金',
        'keywords' => '創業, スタートアップ, 起業, 新規事業, 第二創業'
    ),
    array(
        'title' => 'IT化を進めたい',
        'icon' => 'fas fa-laptop-code',
        'slug' => 'digital',
        'url' => home_url('/purpose/digital/'),
        'description' => 'デジタル化・DX推進・IT システム導入など、デジタル化を支援',
        'keywords' => 'DX, IT導入, デジタル化, 生産性向上, クラウド'
    ),
    array(
        'title' => '資金調達したい',
        'icon' => 'fas fa-yen-sign',
        'slug' => 'funding',
        'url' => home_url('/purpose/funding/'),
        'description' => '運転資金・設備資金など、事業資金の調達を支援する助成金',
        'keywords' => '資金調達, 運転資金, 設備投資, 事業拡大, 創業'
    ),
    array(
        'title' => '環境対策したい',
        'icon' => 'fas fa-leaf',
        'slug' => 'environment',
        'url' => home_url('/purpose/environment/'),
        'description' => '省エネ・脱炭素・環境配慮型事業など、環境対策を支援',
        'keywords' => '省エネ, 再エネ, 脱炭素, SDGs, 環境保護'
    ),
    array(
        'title' => '海外展開したい',
        'icon' => 'fas fa-globe-asia',
        'slug' => 'global',
        'url' => home_url('/purpose/global/'),
        'description' => '輸出・海外進出・インバウンド対応など、グローバル展開を支援',
        'keywords' => '海外展開, 輸出, インバウンド, 販路拡大, 国際交流'
    ),
    
    // ===== 5 Additional Purposes =====
    array(
        'title' => '事業を引き継ぎたい',
        'icon' => 'fas fa-handshake',
        'slug' => 'succession',
        'url' => home_url('/purpose/succession/'),
        'description' => '事業承継・M&A・後継者育成など、事業の引き継ぎを支援',
        'keywords' => '事業承継, M&A, 後継者育成, 経営改善, 事業再構築'
    ),
    array(
        'title' => '研究開発したい',
        'icon' => 'fas fa-flask',
        'slug' => 'rnd',
        'url' => home_url('/purpose/rnd/'),
        'description' => '新技術・新製品の研究開発やイノベーション創出を支援',
        'keywords' => '研究開発, R&D, イノベーション, AI, IoT, 先端技術'
    ),
    array(
        'title' => '住宅関連の支援',
        'icon' => 'fas fa-home',
        'slug' => 'housing',
        'url' => home_url('/purpose/housing/'),
        'description' => '住宅購入・リフォーム・省エネ改修など、住宅関連の支援',
        'keywords' => '住宅支援, リフォーム, 省エネ, 空き家対策, 子育て支援'
    ),
    array(
        'title' => '農林水産業を支援',
        'icon' => 'fas fa-tractor',
        'slug' => 'agriculture',
        'url' => home_url('/purpose/agriculture/'),
        'description' => '農業・林業・漁業の経営改善や6次産業化を支援',
        'keywords' => '農業, 林業, 漁業, 6次産業化, 設備投資, 販路拡大'
    ),
    array(
        'title' => '個人で使いたい',
        'icon' => 'fas fa-user',
        'slug' => 'individual',
        'url' => home_url('/purpose/individual/'),
        'description' => '個人事業主・フリーランス・資格取得など、個人向けの支援',
        'keywords' => '起業, 独立, フリーランス, 資格取得, テレワーク'
    ),
);

// Find the purpose data based on slug
$current_purpose = null;
foreach ($purpose_categories as $purpose) {
    if ($purpose['slug'] === $purpose_slug) {
        $current_purpose = $purpose;
        break;
    }
}

// 404 if purpose not found
if (!$current_purpose) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    include(locate_template('404.php'));
    exit;
}

// Extract purpose data
$purpose_name = $current_purpose['title'];
$purpose_description = $current_purpose['description'];
$purpose_keywords = $current_purpose['keywords'];
$canonical_url = $current_purpose['url'];

// Get actual category slugs from database for this purpose
$category_slugs = gi_get_category_slugs_for_purpose($purpose_slug);

// Debug output
error_log('[Page Purpose] Purpose slug: ' . $purpose_slug);
error_log('[Page Purpose] Category slugs count: ' . count($category_slugs));
if (empty($category_slugs)) {
    error_log('[Page Purpose] WARNING: No category slugs found for purpose: ' . $purpose_slug);
}

// Count grants matching these categories
$grants_count_args = array(
    'post_type' => 'grant',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'tax_query' => array(
        'relation' => 'OR'
    )
);

// Add each category to tax_query
foreach ($category_slugs as $cat_slug) {
    $grants_count_args['tax_query'][] = array(
        'taxonomy' => 'grant_category',
        'field' => 'slug',
        'terms' => $cat_slug
    );
}

$grants_count_query = new WP_Query($grants_count_args);
$category_count = $grants_count_query->found_posts;
wp_reset_postdata();

// For backward compatibility, set category variables
$category_name = $purpose_name;
$category_slug = $purpose_slug;
$category_description = $purpose_description;

// SEO用データ
$current_year = date('Y');
$current_month = date('n');
$season = ($current_month >= 3 && $current_month <= 5) ? '春' : 
          (($current_month >= 6 && $current_month <= 8) ? '夏' : 
          (($current_month >= 9 && $current_month <= 11) ? '秋' : '冬'));

$page_title = $purpose_name . ' | 助成金・補助金一覧【' . $current_year . '年度最新版】';
$page_description = $purpose_name . 'ための助成金・補助金を' . $category_count . '件掲載。' . $current_year . '年度の最新募集情報、申請要件、対象事業、助成金額、締切日を詳しく解説。都道府県・市町村別の検索も可能。';

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

// パンくずリスト用データ
$breadcrumbs = [
    ['name' => 'ホーム', 'url' => home_url()],
    ['name' => '助成金・補助金検索', 'url' => get_post_type_archive_link('grant')],
    ['name' => '用途で探す', 'url' => home_url('/#browse-section')],
    ['name' => $purpose_name, 'url' => '']
];
?>

<!-- SEO構造化データ -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "<?php echo esc_js($page_title); ?>",
    "description": "<?php echo esc_js($page_description); ?>",
    "url": "<?php echo esc_url($canonical_url); ?>",
    "provider": {
        "@type": "Organization",
        "name": "助成金インサイト",
        "url": "<?php echo esc_url(home_url()); ?>"
    },
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
            <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
            {
                "@type": "ListItem",
                "position": <?php echo $index + 1; ?>,
                "name": "<?php echo esc_js($breadcrumb['name']); ?>"
                <?php if (!empty($breadcrumb['url'])): ?>
                ,"item": "<?php echo esc_url($breadcrumb['url']); ?>"
                <?php endif; ?>
            }<?php echo $index < count($breadcrumbs) - 1 ? ',' : ''; ?>
            <?php endforeach; ?>
        ]
    }
}
</script>

<main class="grant-archive-optimized purpose-page" id="purpose-<?php echo esc_attr($purpose_slug); ?>" itemscope itemtype="https://schema.org/CollectionPage">

    <!-- パンくずリスト -->
    <nav class="breadcrumb-nav" aria-label="パンくずリスト" itemscope itemtype="https://schema.org/BreadcrumbList">
        <div class="container">
            <ol class="breadcrumb-list">
                <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <?php if (!empty($breadcrumb['url'])): ?>
                        <a href="<?php echo esc_url($breadcrumb['url']); ?>" itemprop="item">
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

    <!-- ヒーローセクション -->
    <section class="category-hero-section" itemscope itemtype="https://schema.org/WPHeader">
        <div class="container">
            <div class="hero-content-wrapper">
                <!-- 用途バッジ -->
                <div class="category-badge">
                    <i class="<?php echo esc_attr($current_purpose['icon']); ?>" aria-hidden="true"></i>
                    <span><?php echo esc_html($purpose_name); ?></span>
                </div>

                <!-- メインタイトル -->
                <h1 class="category-main-title" itemprop="headline">
                    <span class="category-name-highlight"><?php echo esc_html($purpose_name); ?></span>
                    <span class="title-text">ための助成金・補助金</span>
                    <span class="year-badge"><?php echo $current_year; ?>年度版</span>
                </h1>

                <!-- リード文 -->
                <div class="category-lead-section" itemprop="description">
                    <?php if ($category_description): ?>
                    <p class="category-lead-text">
                        <?php echo esc_html($category_description); ?>
                    </p>
                    <?php endif; ?>
                    <p class="category-lead-sub">
                        <?php echo $current_year; ?>年度の最新募集情報を<?php echo number_format($category_count); ?>件掲載中。
                        都道府県・市町村別の検索にも対応し、あなたの地域で利用できる助成金を簡単に見つけられます。
                    </p>
                </div>

                <!-- メタ情報 -->
                <div class="category-meta-info">
                    <div class="meta-item">
                        <svg class="meta-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M9 11H7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V11h-2v8H9v-8z"/>
                            <path d="M13 7h2l-5-5-5 5h2v4h6V7z"/>
                        </svg>
                        <strong><?php echo number_format($category_count); ?></strong>件の助成金
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <time datetime="<?php echo $current_year; ?>"><?php echo $current_year; ?>年度最新情報</time>
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        毎日更新中
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        市町村別対応
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
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="feature-card-content">
                            <h3>地域別検索対応</h3>
                            <p>都道府県・市町村で絞り込み。地域密着型の助成金も見つかります。</p>
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
            </div>
        </div>
    </section>

    <!-- プルダウン式フィルターセクション -->
    <section class="dropdown-filter-section" role="search" aria-label="助成金検索フィルター">
        <div class="container">
            
            <!-- フィルターヘッダー -->
            <div class="filter-header">
                <h2 class="filter-title">
                    <svg class="title-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    絞り込み検索
                </h2>
                <button class="filter-reset-all" id="reset-all-filters-btn" style="display: none;" aria-label="すべてのフィルターをリセット">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polyline points="1 4 1 10 7 10"/>
                        <polyline points="23 20 23 14 17 14"/>
                        <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                    </svg>
                    すべてリセット
                </button>
            </div>

            <!-- 検索バー -->
            <div class="search-bar-wrapper">
                <label for="keyword-search" class="visually-hidden">キーワード検索</label>
                <div class="search-input-container">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="search" 
                           id="keyword-search" 
                           class="search-input" 
                           placeholder="助成金名、実施機関、対象事業、キーワードで検索..."
                           data-category="<?php echo esc_attr($category_slug); ?>"
                           aria-label="助成金を検索">
                    <button class="search-clear-btn" id="search-clear-btn" style="display: none;" aria-label="検索をクリア">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                    <button class="search-execute-btn" id="search-btn" aria-label="検索を実行">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        検索
                    </button>
                </div>
            </div>

            <!-- プルダウンフィルターグリッド -->
            <div class="dropdown-filters-grid">
                
                <!-- 地域選択 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="region-label">
                        <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        地域
                    </label>
                    <div class="custom-select" id="region-select" role="combobox" aria-labelledby="region-label" aria-expanded="false">
                        <button class="select-trigger" type="button" aria-haspopup="listbox">
                            <span class="select-value">全国</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" role="listbox" style="display: none;">
                            <div class="select-option active" data-value="" role="option" aria-selected="true">全国</div>
                            <?php foreach ($region_groups as $region_slug => $region_name): ?>
                                <div class="select-option" data-value="<?php echo esc_attr($region_slug); ?>" role="option" aria-selected="false">
                                    <?php echo esc_html($region_name); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- 都道府県選択（複数選択対応） -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="prefecture-label">
                        <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                        </svg>
                        都道府県
                        <span class="multi-select-badge" id="prefecture-count-badge" style="display: none;" aria-live="polite">0</span>
                    </label>
                    <div class="custom-select multi-select" id="prefecture-select" role="combobox" aria-labelledby="prefecture-label" aria-expanded="false" aria-multiselectable="true">
                        <button class="select-trigger" type="button" aria-haspopup="listbox">
                            <span class="select-value">選択してください</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown multi-select-dropdown" role="listbox" style="display: none;">
                            <div class="select-search-wrapper">
                                <label for="prefecture-search" class="visually-hidden">都道府県を検索</label>
                                <input type="search" 
                                       class="select-search-input" 
                                       placeholder="都道府県を検索..."
                                       id="prefecture-search"
                                       aria-label="都道府県を検索">
                            </div>
                            <div class="select-options-wrapper" id="prefecture-options">
                                <div class="select-option all-option" data-value="" role="option">
                                    <input type="checkbox" id="pref-all" class="option-checkbox" aria-label="すべての都道府県">
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
                                               value="<?php echo esc_attr($pref['slug']); ?>"
                                               aria-label="<?php echo esc_attr($pref['name']); ?>">
                                        <label for="pref-<?php echo $index; ?>">
                                            <?php echo esc_html($pref['name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="select-actions">
                                <button class="select-action-btn clear-btn" id="clear-prefecture-btn" type="button">クリア</button>
                                <button class="select-action-btn apply-btn" id="apply-prefecture-btn" type="button">適用</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 市町村選択（都道府県選択後に表示） -->
                <div class="filter-dropdown-wrapper" id="municipality-wrapper" style="display: none;">
                    <label class="filter-label" id="municipality-label">
                        <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M20 9v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9"/>
                            <path d="M9 22V12h6v10M2 10.6L12 2l10 8.6"/>
                        </svg>
                        市町村
                        <span class="selected-prefecture-name" id="selected-prefecture-name"></span>
                    </label>
                    <div class="custom-select" id="municipality-select" role="combobox" aria-labelledby="municipality-label" aria-expanded="false">
                        <button class="select-trigger" type="button" aria-haspopup="listbox">
                            <span class="select-value">すべて</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" role="listbox" style="display: none;">
                            <div class="select-search-wrapper">
                                <label for="municipality-search" class="visually-hidden">市町村を検索</label>
                                <input type="search" 
                                       class="select-search-input" 
                                       placeholder="市町村を検索..."
                                       id="municipality-search"
                                       aria-label="市町村を検索">
                            </div>
                            <div class="select-options-wrapper" id="municipality-options">
                                <div class="select-option active" data-value="" role="option" aria-selected="true">すべて</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 助成金額 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="amount-label">
                        <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        助成金額
                    </label>
                    <div class="custom-select" id="amount-select" role="combobox" aria-labelledby="amount-label" aria-expanded="false">
                        <button class="select-trigger" type="button" aria-haspopup="listbox">
                            <span class="select-value">指定なし</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" role="listbox" style="display: none;">
                            <div class="select-option active" data-value="" role="option" aria-selected="true">指定なし</div>
                            <div class="select-option" data-value="0-100" role="option" aria-selected="false">〜100万円</div>
                            <div class="select-option" data-value="100-500" role="option" aria-selected="false">100万円〜500万円</div>
                            <div class="select-option" data-value="500-1000" role="option" aria-selected="false">500万円〜1000万円</div>
                            <div class="select-option" data-value="1000-3000" role="option" aria-selected="false">1000万円〜3000万円</div>
                            <div class="select-option" data-value="3000+" role="option" aria-selected="false">3000万円以上</div>
                        </div>
                    </div>
                </div>

                <!-- 募集状況 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="status-label">
                        <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        募集状況
                    </label>
                    <div class="custom-select" id="status-select" role="combobox" aria-labelledby="status-label" aria-expanded="false">
                        <button class="select-trigger" type="button" aria-haspopup="listbox">
                            <span class="select-value">すべて</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" role="listbox" style="display: none;">
                            <div class="select-option active" data-value="" role="option" aria-selected="true">すべて</div>
                            <div class="select-option" data-value="active" role="option" aria-selected="false">
                                <span class="status-dot status-active" aria-hidden="true"></span>
                                募集中
                            </div>
                            <div class="select-option" data-value="upcoming" role="option" aria-selected="false">
                                <span class="status-dot status-upcoming" aria-hidden="true"></span>
                                募集予定
                            </div>
                            <div class="select-option" data-value="closed" role="option" aria-selected="false">
                                <span class="status-dot status-closed" aria-hidden="true"></span>
                                募集終了
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 難易度 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="difficulty-label">
                        <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        申請難易度
                    </label>
                    <div class="custom-select" id="difficulty-select" role="combobox" aria-labelledby="difficulty-label" aria-expanded="false">
                        <button class="select-trigger" type="button" aria-haspopup="listbox">
                            <span class="select-value">指定なし</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" role="listbox" style="display: none;">
                            <div class="select-option active" data-value="" role="option" aria-selected="true">指定なし</div>
                            <div class="select-option" data-value="easy" role="option" aria-selected="false">易しい</div>
                            <div class="select-option" data-value="normal" role="option" aria-selected="false">普通</div>
                            <div class="select-option" data-value="hard" role="option" aria-selected="false">難しい</div>
                        </div>
                    </div>
                </div>

                <!-- 並び順 -->
                <div class="filter-dropdown-wrapper">
                    <label class="filter-label" id="sort-label">
                        <svg class="label-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="4" y1="6" x2="16" y2="6"/>
                            <line x1="4" y1="12" x2="14" y2="12"/>
                            <line x1="4" y1="18" x2="12" y2="18"/>
                        </svg>
                        並び順
                    </label>
                    <div class="custom-select" id="sort-select" role="combobox" aria-labelledby="sort-label" aria-expanded="false">
                        <button class="select-trigger" type="button" aria-haspopup="listbox">
                            <span class="select-value">新着順</span>
                            <svg class="select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </button>
                        <div class="select-dropdown" role="listbox" style="display: none;">
                            <div class="select-option active" data-value="date_desc" role="option" aria-selected="true">新着順</div>
                            <div class="select-option" data-value="amount_desc" role="option" aria-selected="false">金額が高い順</div>
                            <div class="select-option" data-value="deadline_asc" role="option" aria-selected="false">締切が近い順</div>
                            <div class="select-option" data-value="featured_first" role="option" aria-selected="false">注目順</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 選択中のフィルター表示 -->
            <div class="active-filters-display" id="active-filters" style="display: none;" role="status" aria-live="polite">
                <div class="active-filters-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    適用中のフィルター:
                </div>
                <div class="active-filter-tags" id="active-filter-tags"></div>
            </div>
        </div>
    </section>

    <!-- 検索結果セクション -->
    <section class="results-section-optimized">
        <div class="container">
            
            <!-- 結果ヘッダー（グリッド切替削除） -->
            <div class="results-header">
                <div class="results-info">
                    <h2 class="results-title">検索結果</h2>
                    <div class="results-meta" role="status" aria-live="polite">
                        <span class="total-count">
                            <strong id="current-count">0</strong>件
                        </span>
                        <span class="showing-range">
                            （<span id="showing-from">1</span>〜<span id="showing-to">12</span>件を表示）
                        </span>
                    </div>
                </div>
                <!-- グリッド切替ボタンを削除 -->
            </div>

            <!-- ローディング -->
            <div class="loading-overlay" id="loading-overlay" style="display: none;" role="status" aria-live="polite">
                <div class="loading-spinner">
                    <div class="spinner" aria-hidden="true"></div>
                    <p class="loading-text">検索中...</p>
                </div>
            </div>

            <!-- 助成金表示エリア（単体表示専用） -->
            <div class="grants-container-portal" id="grants-container">
                <!-- JavaScriptで動的に読み込まれます -->
            </div>

            <!-- 結果なし -->
            <div class="no-results" id="no-results" style="display: none;" role="status" aria-live="polite">
                <svg class="no-results-icon" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <h3 class="no-results-title">該当する助成金が見つかりませんでした</h3>
                <p class="no-results-message">
                    検索条件を変更して再度お試しください。
                </p>
            </div>

            <!-- ページネーション -->
            <div class="pagination-wrapper" id="pagination-wrapper" style="display: none;">
                <nav class="pagination" id="pagination" aria-label="検索結果のページネーション"></nav>
            </div>
        </div>
    </section>

</main>

<!-- 視覚的に隠すためのユーティリティクラス -->
<style>
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
</style>

<!-- CSS（グリッド切替削除・単体表示専用） -->
<style>
/* ===== CSS Variables ===== */
:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #e5e5e5;
    --color-gray-300: #d4d4d4;
    --color-gray-400: #a3a3a3;
    --color-gray-500: #737373;
    --color-gray-600: #525252;
    --color-gray-700: #404040;
    --color-gray-800: #262626;
    --color-gray-900: #171717;
    --color-success: #22c55e;
    --color-warning: #f59e0b;
    --color-error: #ef4444;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --transition-fast: 0.15s ease;
    --transition-normal: 0.3s ease;
    --border-radius: 8px;
}

/* ===== Base Styles ===== */
.grant-archive-optimized {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif;
    color: var(--color-primary);
    background: var(--color-secondary);
    line-height: 1.6;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ===== Breadcrumb ===== */
.breadcrumb-nav {
    padding: 16px 0;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.breadcrumb-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 14px;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin-left: 8px;
    color: var(--color-gray-400);
    font-size: 16px;
}

.breadcrumb-item a {
    color: var(--color-gray-600);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.breadcrumb-item a:hover {
    color: var(--color-primary);
    text-decoration: underline;
}

.breadcrumb-item span {
    color: var(--color-primary);
    font-weight: 600;
}

/* ===== Hero Section ===== */
.category-hero-section {
    padding: 60px 0;
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-secondary) 100%);
    border-bottom: 2px solid var(--color-gray-200);
}

.hero-content-wrapper {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 20px;
}

.badge-icon {
    color: var(--color-secondary);
}

.category-main-title {
    font-size: 48px;
    font-weight: 800;
    color: var(--color-primary);
    margin: 0 0 20px 0;
    line-height: 1.2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.category-name-highlight {
    background: linear-gradient(180deg, transparent 60%, rgba(255, 235, 59, 0.4) 60%);
    padding: 0 8px;
}

.title-text {
    color: var(--color-gray-700);
}

.year-badge {
    display: inline-block;
    padding: 6px 16px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 20px;
    font-size: 16px;
    font-weight: 700;
}

.category-lead-section {
    margin: 30px 0;
}

.category-lead-text {
    font-size: 18px;
    color: var(--color-gray-700);
    margin: 0 0 16px 0;
    line-height: 1.7;
}

.category-lead-sub {
    font-size: 16px;
    color: var(--color-gray-600);
    margin: 0;
    line-height: 1.8;
}

.category-meta-info {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    margin: 30px 0 40px 0;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    color: var(--color-gray-700);
}

.meta-icon {
    color: var(--color-gray-500);
}

.meta-item strong {
    color: var(--color-primary);
    font-weight: 700;
}

.feature-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 40px;
}

.feature-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 24px;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-200);
    border-radius: var(--border-radius);
    transition: all var(--transition-normal);
    text-align: left;
}

.feature-card:hover {
    border-color: var(--color-primary);
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.feature-card-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    background: var(--color-primary);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-secondary);
}

.feature-card-content {
    flex: 1;
}

.feature-card-content h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 8px 0;
}

.feature-card-content p {
    font-size: 14px;
    color: var(--color-gray-600);
    margin: 0;
    line-height: 1.5;
}

/* ===== Dropdown Filter Section ===== */
.dropdown-filter-section {
    padding: 50px 0;
    background: var(--color-secondary);
    border-bottom: 1px solid var(--color-gray-200);
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0;
}

.title-icon {
    color: var(--color-gray-600);
}

.filter-reset-all {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    color: var(--color-gray-700);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.filter-reset-all:hover {
    background: var(--color-gray-200);
    border-color: var(--color-gray-400);
}

/* ===== Search Bar ===== */
.search-bar-wrapper {
    margin-bottom: 30px;
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
    max-width: 100%;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: border-color var(--transition-fast);
}

.search-input-container:focus-within {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
}

.search-icon {
    position: absolute;
    left: 16px;
    color: var(--color-gray-400);
    pointer-events: none;
}

.search-input {
    flex: 1;
    padding: 14px 16px 14px 48px;
    border: none;
    outline: none;
    font-size: 15px;
    background: transparent;
}

.search-clear-btn {
    background: none;
    border: none;
    color: var(--color-gray-400);
    padding: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color var(--transition-fast);
}

.search-clear-btn:hover {
    color: var(--color-gray-700);
}

.search-execute-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 24px;
    background: var(--color-primary);
    border: none;
    color: var(--color-secondary);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background var(--transition-fast);
}

.search-execute-btn:hover {
    background: var(--color-gray-800);
}

/* ===== Dropdown Filters Grid ===== */
.dropdown-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.filter-dropdown-wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-gray-700);
}

.label-icon {
    color: var(--color-gray-500);
}

.multi-select-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
}

.selected-prefecture-name {
    font-size: 12px;
    color: var(--color-gray-500);
    font-weight: 400;
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
    padding: 12px 16px;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    color: var(--color-gray-700);
    font-size: 14px;
    cursor: pointer;
    transition: all var(--transition-fast);
    text-align: left;
}

.select-trigger:hover {
    border-color: var(--color-gray-400);
}

.custom-select.active .select-trigger {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
}

.select-value {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.select-arrow {
    flex-shrink: 0;
    color: var(--color-gray-500);
    transition: transform var(--transition-fast);
}

.custom-select.active .select-arrow {
    transform: rotate(180deg);
}

.select-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    max-height: 300px;
    overflow-y: auto;
    z-index: 100;
}

.select-option {
    padding: 12px 16px;
    cursor: pointer;
    transition: background var(--transition-fast);
    font-size: 14px;
    color: var(--color-gray-700);
    display: flex;
    align-items: center;
    gap: 8px;
}

.select-option:hover {
    background: var(--color-gray-100);
}

.select-option.active {
    background: var(--color-gray-100);
    color: var(--color-primary);
    font-weight: 600;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-dot.status-active {
    background: var(--color-success);
}

.status-dot.status-upcoming {
    background: var(--color-warning);
}

.status-dot.status-closed {
    background: var(--color-gray-400);
}

/* ===== Multi Select ===== */
.multi-select-dropdown {
    max-height: 400px;
}

.select-search-wrapper {
    padding: 12px;
    border-bottom: 1px solid var(--color-gray-200);
    position: sticky;
    top: 0;
    background: var(--color-secondary);
    z-index: 10;
}

.select-search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    font-size: 13px;
    outline: none;
}

.select-search-input:focus {
    border-color: var(--color-primary);
}

.select-options-wrapper {
    max-height: 250px;
    overflow-y: auto;
}

.select-option .option-checkbox {
    margin-right: 8px;
    cursor: pointer;
}

.select-option label {
    flex: 1;
    cursor: pointer;
}

.select-actions {
    display: flex;
    gap: 8px;
    padding: 12px;
    border-top: 1px solid var(--color-gray-200);
    position: sticky;
    bottom: 0;
    background: var(--color-secondary);
}

.select-action-btn {
    flex: 1;
    padding: 8px 16px;
    border: 1px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.select-action-btn.clear-btn {
    background: var(--color-secondary);
    color: var(--color-gray-700);
}

.select-action-btn.clear-btn:hover {
    background: var(--color-gray-100);
}

.select-action-btn.apply-btn {
    background: var(--color-primary);
    color: var(--color-secondary);
    border-color: var(--color-primary);
}

.select-action-btn.apply-btn:hover {
    background: var(--color-gray-800);
}

/* ===== Active Filters ===== */
.active-filters-display {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--border-radius);
    flex-wrap: wrap;
}

.active-filters-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-gray-700);
    white-space: nowrap;
}

.active-filter-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    flex: 1;
}

.filter-tag {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 16px;
    font-size: 13px;
    font-weight: 500;
}

.filter-tag-remove {
    background: none;
    border: none;
    color: var(--color-secondary);
    cursor: pointer;
    padding: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    line-height: 1;
    opacity: 0.8;
    transition: opacity var(--transition-fast);
}

.filter-tag-remove:hover {
    opacity: 1;
}

/* ===== Results Section ===== */
.results-section-optimized {
    padding: 60px 0;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 20px;
}

.results-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0 0 8px 0;
}

.results-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 14px;
    color: var(--color-gray-600);
}

.total-count strong {
    font-size: 20px;
    color: var(--color-primary);
}

/* グリッド切替ボタンのスタイルを削除 */

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
    width: 50px;
    height: 50px;
    border: 4px solid var(--color-gray-200);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 16px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    font-size: 14px;
    color: var(--color-gray-600);
    margin: 0;
}

/* ===== Grants Container (Portal Type Only) ===== */
.grants-container-portal {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 60px;
    min-height: 400px;
}

/* ===== No Results ===== */
.no-results {
    text-align: center;
    padding: 80px 20px;
    color: var(--color-gray-600);
}

.no-results-icon {
    color: var(--color-gray-300);
    margin-bottom: 20px;
}

.no-results-title {
    font-size: 24px;
    font-weight: 600;
    color: var(--color-primary);
    margin: 0 0 12px 0;
}

.no-results-message {
    font-size: 16px;
    margin: 0;
}

/* ===== Pagination ===== */
.pagination-wrapper {
    margin-top: 60px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: 0 12px;
    border: 2px solid var(--color-gray-300);
    border-radius: var(--border-radius);
    background: var(--color-secondary);
    color: var(--color-gray-700);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.pagination-btn:hover:not(:disabled) {
    border-color: var(--color-primary);
    color: var(--color-primary);
}

.pagination-btn.active {
    background: var(--color-primary);
    border-color: var(--color-primary);
    color: var(--color-secondary);
}

.pagination-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.pagination-ellipsis {
    padding: 0 8px;
    color: var(--color-gray-400);
}

/* ===== Responsive Design ===== */
@media (max-width: 768px) {
    .category-main-title {
        font-size: 32px;
    }

    .year-badge {
        font-size: 14px;
        padding: 5px 12px;
    }

    .category-lead-text {
        font-size: 16px;
    }

    .category-lead-sub {
        font-size: 15px;
    }

    .category-meta-info {
        flex-direction: column;
        gap: 16px;
    }

    .feature-cards-grid {
        grid-template-columns: 1fr;
    }

    .dropdown-filters-grid {
        grid-template-columns: 1fr;
    }

    .results-header {
        flex-direction: column;
        align-items: stretch;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 16px;
    }

    .category-main-title {
        font-size: 28px;
    }

    .search-input-container {
        flex-direction: column;
    }

    .search-execute-btn {
        width: 100%;
        justify-content: center;
    }

    .filter-header {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }
}
</style>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- JavaScript（グリッド切替削除・単体表示専用） -->
<script>
(function() {
    'use strict';
    
    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    const NONCE = '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>';
    const PURPOSE_SLUG = '<?php echo esc_js($purpose_slug); ?>';
    const CATEGORY_SLUGS = <?php echo json_encode($category_slugs); ?>;
    
    console.log('[Purpose Debug] PURPOSE_SLUG:', PURPOSE_SLUG);
    console.log('[Purpose Debug] CATEGORY_SLUGS:', CATEGORY_SLUGS);
    console.log('[Purpose Debug] CATEGORY_SLUGS type:', typeof CATEGORY_SLUGS);
    console.log('[Purpose Debug] CATEGORY_SLUGS is array:', Array.isArray(CATEGORY_SLUGS));
    console.log('[Purpose Debug] CATEGORY_SLUGS length:', CATEGORY_SLUGS ? CATEGORY_SLUGS.length : 0);
    
    if (!CATEGORY_SLUGS || !Array.isArray(CATEGORY_SLUGS) || CATEGORY_SLUGS.length === 0) {
        console.error('[Purpose Error] CATEGORY_SLUGS is empty or invalid!');
        console.error('[Purpose Error] This will cause AJAX requests to fail');
    }
    
    const state = {
        currentPage: 1,
        perPage: 12,
        filters: {
            search: '',
            category: CATEGORY_SLUGS,
            prefecture: [],
            municipality: '',
            region: '',
            amount: '',
            status: '',
            difficulty: '',
            sort: 'date_desc'
        },
        isLoading: false,
        tempPrefectures: [],
        currentMunicipalities: []
    };
    
    const elements = {
        grantsContainer: document.getElementById('grants-container'),
        loadingOverlay: document.getElementById('loading-overlay'),
        noResults: document.getElementById('no-results'),
        resultsCount: document.getElementById('current-count'),
        showingFrom: document.getElementById('showing-from'),
        showingTo: document.getElementById('showing-to'),
        pagination: document.getElementById('pagination'),
        paginationWrapper: document.getElementById('pagination-wrapper'),
        activeFilters: document.getElementById('active-filters'),
        activeFilterTags: document.getElementById('active-filter-tags'),
        
        keywordSearch: document.getElementById('keyword-search'),
        searchBtn: document.getElementById('search-btn'),
        searchClearBtn: document.getElementById('search-clear-btn'),
        
        regionSelect: document.getElementById('region-select'),
        
        prefectureSelect: document.getElementById('prefecture-select'),
        prefectureSearch: document.getElementById('prefecture-search'),
        prefectureOptions: document.getElementById('prefecture-options'),
        clearPrefectureBtn: document.getElementById('clear-prefecture-btn'),
        applyPrefectureBtn: document.getElementById('apply-prefecture-btn'),
        prefectureCountBadge: document.getElementById('prefecture-count-badge'),
        
        municipalitySelect: document.getElementById('municipality-select'),
        municipalityWrapper: document.getElementById('municipality-wrapper'),
        municipalitySearch: document.getElementById('municipality-search'),
        municipalityOptions: document.getElementById('municipality-options'),
        selectedPrefectureName: document.getElementById('selected-prefecture-name'),
        
        amountSelect: document.getElementById('amount-select'),
        statusSelect: document.getElementById('status-select'),
        difficultySelect: document.getElementById('difficulty-select'),
        sortSelect: document.getElementById('sort-select'),
        
        resetAllFiltersBtn: document.getElementById('reset-all-filters-btn')
    };
    
    function init() {
        console.log('🚀 Purpose page initialized (Portal Type Only):', PURPOSE_SLUG);
        console.log('📊 Configuration:', {
            ajaxUrl: AJAX_URL,
            noncePresent: !!NONCE,
            purposeSlug: PURPOSE_SLUG,
            categoryCount: CATEGORY_SLUGS.length,
            categories: CATEGORY_SLUGS
        });
        
        setupCustomSelects();
        setupEventListeners();
        updateActiveFiltersDisplay();
        loadGrants();
    }
    
    // ===== カスタムセレクトの初期化 =====
    function setupCustomSelects() {
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
        
        setupSingleSelect(elements.difficultySelect, (value) => {
            state.filters.difficulty = value;
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
                
                onChange(value);
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
            valueSpan.textContent = '選択してください';
            elements.prefectureCountBadge.style.display = 'none';
        } else {
            valueSpan.textContent = `${count}件選択中`;
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
    
    // ===== 市町村機能 =====
    function loadMunicipalities(prefectureSlug, prefectureName) {
        console.log('🏘️ Loading municipalities:', prefectureSlug, prefectureName);
        
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
            console.log('📦 Municipality API Response:', data);
            
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
            
            console.log('🎯 Municipalities count:', municipalities.length);
            
            if (municipalities.length > 0) {
                state.currentMunicipalities = municipalities;
                renderMunicipalityOptions(municipalities);
            } else {
                renderMunicipalityOptions([]);
            }
        })
        .catch(error => {
            console.error('🚨 Municipality fetch error:', error);
            renderMunicipalityOptions([]);
        });
    }
    
    function renderMunicipalityOptions(municipalities) {
        if (!elements.municipalityOptions) return;
        
        let html = '<div class="select-option active" data-value="" role="option" aria-selected="true">すべて</div>';
        
        municipalities.forEach(municipality => {
            html += `<div class="select-option" data-value="${municipality.slug}" role="option" aria-selected="false">${municipality.name}</div>`;
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
                state.currentPage = 1;
                
                console.log('✅ Municipality selected:', value || '(all)');
                
                loadGrants();
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
    
    // ===== イベントリスナー設定 =====
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
        
        if (elements.resetAllFiltersBtn) {
            elements.resetAllFiltersBtn.addEventListener('click', resetAllFilters);
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
        console.log('🔄 Resetting all filters');
        
        state.filters = {
            search: '',
            category: CATEGORY_SLUGS,
            prefecture: [],
            municipality: '',
            region: '',
            amount: '',
            status: '',
            difficulty: '',
            sort: 'date_desc'
        };
        state.tempPrefectures = [];
        state.currentPage = 1;
        
        elements.keywordSearch.value = '';
        elements.searchClearBtn.style.display = 'none';
        
        resetCustomSelect(elements.regionSelect, '全国');
        resetCustomSelect(elements.amountSelect, '指定なし');
        resetCustomSelect(elements.statusSelect, 'すべて');
        resetCustomSelect(elements.difficultySelect, '指定なし');
        resetCustomSelect(elements.sortSelect, '新着順');
        
        updatePrefectureDisplay();
        updatePrefectureCheckboxes();
        
        filterPrefecturesByRegion('');
        hideMunicipalityFilter();
        
        console.log('✅ All filters reset');
        
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
    
    // ===== データ読み込み =====
    function loadGrants() {
        if (state.isLoading) return;
        
        state.isLoading = true;
        showLoading(true);
        
        const formData = new FormData();
        formData.append('action', 'gi_ajax_load_grants');
        formData.append('nonce', NONCE);
        formData.append('page', state.currentPage);
        formData.append('posts_per_page', state.perPage);
        
        if (state.filters.search) {
            formData.append('search', state.filters.search);
        }
        
        formData.append('categories', JSON.stringify(CATEGORY_SLUGS));
        
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
        
        if (state.filters.difficulty) {
            formData.append('difficulty', JSON.stringify([state.filters.difficulty]));
        }
        
        formData.append('sort', state.filters.sort);
        
        console.log('📡 Loading grants:', state.filters);
        
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
            console.error('❌ Fetch Error:', error);
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
        
        elements.grantsContainer.style.display = 'flex';
        if (elements.noResults) {
            elements.noResults.style.display = 'none';
        }
        
        elements.grantsContainer.innerHTML = grants.map(grant => grant.html).join('');
        
        if (typeof setupAIButtonListeners === 'function') {
            setupAIButtonListeners();
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
        if (!elements.pagination || !elements.paginationWrapper) return;
        
        if (!pagination || pagination.total_pages <= 1) {
            elements.paginationWrapper.style.display = 'none';
            return;
        }
        
        elements.paginationWrapper.style.display = 'block';
        
        let html = '';
        const current = pagination.current_page;
        const total = pagination.total_pages;
        
        html += `<button class="pagination-btn" ${current === 1 ? 'disabled' : ''} data-page="${current - 1}">前へ</button>`;
        
        const maxPages = 7;
        let startPage = Math.max(1, current - Math.floor(maxPages / 2));
        let endPage = Math.min(total, startPage + maxPages - 1);
        
        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }
        
        if (startPage > 1) {
            html += `<button class="pagination-btn" data-page="1">1</button>`;
            if (startPage > 2) {
                html += `<span class="pagination-ellipsis">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            html += `<button class="pagination-btn ${i === current ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        
        if (endPage < total) {
            if (endPage < total - 1) {
                html += `<span class="pagination-ellipsis">...</span>`;
            }
            html += `<button class="pagination-btn" data-page="${total}">${total}</button>`;
        }
        
        html += `<button class="pagination-btn" ${current === total ? 'disabled' : ''} data-page="${current + 1}">次へ</button>`;
        
        elements.pagination.innerHTML = html;
        
        elements.pagination.querySelectorAll('.pagination-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.disabled) {
                    state.currentPage = parseInt(this.dataset.page);
                    loadGrants();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
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
        
        if (state.filters.difficulty) {
            const labels = {
                'easy': '易しい',
                'normal': '普通',
                'hard': '難しい'
            };
            tags.push({
                type: 'difficulty',
                label: `難易度: ${labels[state.filters.difficulty]}`,
                value: state.filters.difficulty
            });
        }
        
        if (tags.length === 0) {
            elements.activeFilters.style.display = 'none';
            elements.resetAllFiltersBtn.style.display = 'none';
            return;
        }
        
        elements.activeFilters.style.display = 'flex';
        elements.resetAllFiltersBtn.style.display = 'flex';
        
        elements.activeFilterTags.innerHTML = tags.map(tag => `
            <div class="filter-tag">
                <span>${escapeHtml(tag.label)}</span>
                <button class="filter-tag-remove" data-type="${tag.type}" data-value="${escapeHtml(tag.value)}">×</button>
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
            case 'difficulty':
                state.filters.difficulty = '';
                resetCustomSelect(elements.difficultySelect, '指定なし');
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
        return text.replace(/[&<>"']/g, m => map[m]);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    console.log('✅ Category archive loaded (Portal Type Only - No Grid Toggle)');
})();
</script>

<?php get_footer(); ?>