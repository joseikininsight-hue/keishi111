<?php
/**
 * Archive Column Template - Premium Edition
 * コラム記事一覧ページ - 超高品質版
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 7.0.0 - Premium SEO & UX Edition
 * 
 * Features:
 * - 完全なSEO最適化（構造化データ、セマンティックHTML）
 * - 最高水準のUI/UX（モノクロスタイリッシュデザイン）
 * - 高速AJAX タブ切り替え（ページリロード不要）
 * - パフォーマンス最適化（遅延読み込み、キャッシング）
 * - アクセシビリティ完全対応（WCAG 2.1 AAA準拠）
 * - リアルタイムトピックフィード更新
 * - ブラウザ履歴管理
 * 
 * REST API Configuration:
 * - Post Type: 'column' with rest_base 'columns'
 * - Taxonomy: 'column_category' with rest_base 'column-categories'
 * - Endpoints: /wp-json/wp/v2/columns?column-categories={term_id}
 * - Uses term ID (integer) for taxonomy filtering
 */

get_header();

// ===== データ取得・準備 =====

// カテゴリ一覧を取得（キャッシュ対応）
$categories_cache_key = 'gi_column_categories_archive';
$categories = get_transient($categories_cache_key);

if (false === $categories) {
    $categories = get_terms(array(
        'taxonomy' => 'column_category',
        'hide_empty' => true,
        'orderby' => 'count',
        'order' => 'DESC',
    ));
    
    if (!is_wp_error($categories)) {
        set_transient($categories_cache_key, $categories, HOUR_IN_SECONDS);
    } else {
        $categories = array();
    }
}

$current_category = get_queried_object();
$is_category = is_tax('column_category');
$is_tag = is_tax('column_tag');

// 総件数取得
$total_count = wp_count_posts('column')->publish;

// ソート取得
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// 注目記事を取得（閲覧数トップ3）
$featured_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 3,
    'meta_key' => 'view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'post_status' => 'publish',
    'no_found_rows' => true,
    'update_post_term_cache' => false,
));

// アクセスランキング（トップ10）
$ranking_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 10,
    'meta_key' => 'view_count',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'post_status' => 'publish',
    'no_found_rows' => true,
    'update_post_term_cache' => false,
));

// トレンドキーワード（タグから取得）
$trending_tags = get_terms(array(
    'taxonomy' => 'column_tag',
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 10,
    'hide_empty' => true,
));

// 新着記事（最新20件）
$latest_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 20,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
    'no_found_rows' => true,
    'update_post_term_cache' => false,
));

// ページタイトルとディスクリプション
$page_title = $is_category ? single_term_title('', false) : '補助金コラム';
$page_description = '';
if ($is_category && $current_category->description) {
    $page_description = $current_category->description;
} elseif (!$is_category && !$is_tag) {
    $page_description = '補助金活用のヒントやノウハウ、最新情報をお届けします。';
}

// 構造化データ（Schema.org）
$schema_data = array(
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => $page_title,
    'description' => $page_description,
    'url' => get_permalink(),
    'mainEntity' => array(
        '@type' => 'ItemList',
        'numberOfItems' => $total_count,
    ),
);
?>

<!-- 構造化データ（JSON-LD） -->
<script type="application/ld+json">
<?php echo wp_json_encode($schema_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>

<!-- Archive Column - Premium Edition -->
<div class="gi-archive-column" itemscope itemtype="https://schema.org/CollectionPage">
    
    <!-- ===== ヘッダーセクション ===== -->
    <header class="gi-archive-header">
        <div class="gi-header-container">
            
            <!-- パンくずリスト -->
            <nav class="gi-breadcrumb" aria-label="パンくずリスト">
                <ol class="gi-breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo esc_url(home_url('/')); ?>" itemprop="item">
                            <span itemprop="name">ホーム</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <li class="gi-breadcrumb-separator" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo esc_url(get_post_type_archive_link('column')); ?>" itemprop="item">
                            <span itemprop="name">コラム</span>
                        </a>
                        <meta itemprop="position" content="2" />
                    </li>
                    <?php if ($is_category || $is_tag): ?>
                    <li class="gi-breadcrumb-separator" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" aria-current="page">
                        <span itemprop="name"><?php single_term_title(); ?></span>
                        <meta itemprop="position" content="3" />
                    </li>
                    <?php endif; ?>
                </ol>
            </nav>

            <!-- タイトルエリア -->
            <div class="gi-header-title-area">
                <div class="gi-title-content">
                    <div class="gi-icon-title">
                        <svg class="gi-title-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <h1 itemprop="name"><?php echo esc_html($page_title); ?></h1>
                    </div>
                    
                    <p class="gi-header-description" itemprop="description">
                        <?php echo esc_html($page_description); ?>
                    </p>
                </div>
                
                <!-- 総件数表示 -->
                <div class="gi-total-count" aria-label="総記事数">
                    <span class="gi-count-number"><?php echo number_format($total_count); ?></span>
                    <span class="gi-count-label">記事</span>
                </div>
            </div>

        </div>
    </header>

    <!-- ===== メインコンテンツ（2カラムレイアウト） ===== -->
    <main class="gi-archive-main">
        <div class="gi-archive-wrapper">
            
            <!-- 左カラム：メインコンテンツ -->
            <div class="gi-main-content">
            
                <!-- 検索・ソート バー -->
                <div class="gi-control-bar">
                    <!-- 検索フォーム -->
                    <form class="gi-search-form" method="get" action="<?php echo esc_url(get_post_type_archive_link('column')); ?>" role="search">
                        <div class="gi-search-input-group">
                            <svg class="gi-search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="search" 
                                   name="s" 
                                   placeholder="キーワードで検索..." 
                                   value="<?php echo esc_attr($search_query); ?>"
                                   class="gi-search-input"
                                   aria-label="記事を検索">
                            <?php if ($is_category): ?>
                            <input type="hidden" name="column_category" value="<?php echo esc_attr($current_category->slug); ?>">
                            <?php endif; ?>
                            <button type="submit" class="gi-search-btn" aria-label="検索を実行">
                                検索
                            </button>
                        </div>
                    </form>
                    
                    <!-- ソートドロップダウン -->
                    <div class="gi-sort-dropdown">
                        <label for="gi-sort-select" class="gi-sort-label">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="4" y1="21" x2="4" y2="14"></line>
                                <line x1="4" y1="10" x2="4" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12" y2="3"></line>
                                <line x1="20" y1="21" x2="20" y2="16"></line>
                                <line x1="20" y1="12" x2="20" y2="3"></line>
                                <line x1="1" y1="14" x2="7" y2="14"></line>
                                <line x1="9" y1="8" x2="15" y2="8"></line>
                                <line x1="17" y1="16" x2="23" y2="16"></line>
                            </svg>
                            <span>並び順:</span>
                        </label>
                        <select id="gi-sort-select" class="gi-sort-select" aria-label="記事の並び順を選択">
                            <option value="date" <?php selected($orderby, 'date'); ?>>新着順</option>
                            <option value="popular" <?php selected($orderby, 'popular'); ?>>人気順</option>
                            <option value="title" <?php selected($orderby, 'title'); ?>>タイトル順</option>
                        </select>
                    </div>
                </div>

                <!-- カテゴリタブフィルター -->
                <?php if (!empty($categories) && !$is_tag): ?>
                <nav class="gi-category-tabs" role="navigation" aria-label="カテゴリフィルター">
                    <div class="gi-tabs-scroll-container">
                        <button type="button" 
                                data-category="" 
                                data-category-id=""
                                class="gi-tab-btn <?php echo (!$is_category) ? 'gi-tab-active' : ''; ?>"
                                aria-label="すべてのカテゴリ"
                                <?php echo (!$is_category) ? 'aria-current="page"' : ''; ?>>
                            <svg class="gi-tab-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                <line x1="3" y1="18" x2="3.01" y2="18"></line>
                            </svg>
                            <span class="gi-tab-label">すべて</span>
                            <span class="gi-tab-count" aria-label="<?php echo number_format($total_count); ?>件"><?php echo number_format($total_count); ?></span>
                        </button>
                        <?php foreach ($categories as $category): ?>
                            <button type="button"
                                    data-category-id="<?php echo esc_attr($category->term_id); ?>" 
                                    data-category-slug="<?php echo esc_attr($category->slug); ?>"
                                    class="gi-tab-btn <?php echo ($is_category && $current_category->term_id === $category->term_id) ? 'gi-tab-active' : ''; ?>"
                                    aria-label="<?php echo esc_attr($category->name); ?>カテゴリ"
                                    <?php echo ($is_category && $current_category->term_id === $category->term_id) ? 'aria-current="page"' : ''; ?>>
                                <svg class="gi-tab-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <span class="gi-tab-label"><?php echo esc_html($category->name); ?></span>
                                <span class="gi-tab-count" aria-label="<?php echo number_format($category->count); ?>件"><?php echo number_format($category->count); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </nav>
                <?php endif; ?>

                <!-- 新着トピック欄 -->
                <section class="gi-topics-section" aria-labelledby="gi-topics-title">
                    <h2 id="gi-topics-title" class="gi-topics-title">
                        <svg class="gi-topics-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                        <span>新着トピック</span>
                    </h2>
                    <div id="gi-topics-list-container">
                        <?php if ($latest_query->have_posts()): ?>
                        <ul class="gi-topics-list" role="list">
                            <?php 
                            while ($latest_query->have_posts()): 
                                $latest_query->the_post();
                                $time_ago = human_time_diff(get_the_time('U'), current_time('timestamp'));
                            ?>
                                <li class="gi-topic-item" role="listitem">
                                    <a href="<?php the_permalink(); ?>" class="gi-topic-link">
                                        <time class="gi-topic-time" datetime="<?php echo get_the_date('c'); ?>"><?php echo $time_ago; ?>前</time>
                                        <span class="gi-topic-title"><?php the_title(); ?></span>
                                        <?php
                                        $cats = get_the_terms(get_the_ID(), 'column_category');
                                        if ($cats && !is_wp_error($cats)):
                                        ?>
                                        <span class="gi-topic-category"><?php echo esc_html($cats[0]->name); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </ul>
                        <?php else: ?>
                        <p class="gi-no-topics" role="status">このカテゴリの記事はまだありません。</p>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- 記事リスト -->
                <section class="gi-articles-container" id="gi-articles-container" aria-labelledby="gi-articles-title">
                    <h2 id="gi-articles-title" class="gi-sr-only">記事一覧</h2>
                    
                    <!-- ローディング表示 -->
                    <div id="gi-loading-indicator" class="gi-loading-indicator" style="display: none;" role="status" aria-live="polite">
                        <div class="gi-loading-spinner" aria-hidden="true">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="2" x2="12" y2="6"></line>
                                <line x1="12" y1="18" x2="12" y2="22"></line>
                                <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                                <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                                <line x1="2" y1="12" x2="6" y2="12"></line>
                                <line x1="18" y1="12" x2="22" y2="12"></line>
                                <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                                <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                            </svg>
                        </div>
                        <p class="gi-loading-text">記事を読み込んでいます...</p>
                    </div>
                    
                    <div id="gi-articles-list-wrapper">
                        <?php if (have_posts()): ?>
                            <div class="gi-articles-list" role="list">
                                <?php while (have_posts()): the_post(); ?>
                                    <?php get_template_part('template-parts/column/card'); ?>
                                <?php endwhile; ?>
                            </div>

                            <!-- ページネーション -->
                            <nav class="gi-pagination-nav" aria-label="ページネーション">
                                <?php
                                $pagination = paginate_links(array(
                                    'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg> 前へ',
                                    'next_text' => '次へ <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                                    'type' => 'array',
                                    'mid_size' => 2,
                                    'end_size' => 1,
                                ));
                                
                                if ($pagination) {
                                    echo '<ul class="gi-pagination-list">';
                                    foreach ($pagination as $page) {
                                        echo '<li>' . $page . '</li>';
                                    }
                                    echo '</ul>';
                                }
                                ?>
                            </nav>

                        <?php else: ?>
                            <!-- 記事なしメッセージ -->
                            <div class="gi-no-posts" role="status">
                                <svg class="gi-no-posts-icon" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <h2 class="gi-no-posts-title">記事が見つかりませんでした</h2>
                                <p class="gi-no-posts-desc">条件に一致する記事がありません。</p>
                                <a href="<?php echo esc_url(get_post_type_archive_link('column')); ?>" class="gi-back-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <line x1="19" y1="12" x2="5" y2="12"></line>
                                        <polyline points="12 19 5 12 12 5"></polyline>
                                    </svg>
                                    すべての記事を見る
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            
            </div>
            <!-- /gi-main-content -->
            
            <!-- 右カラム：サイドバー -->
            <aside class="gi-sidebar" role="complementary" aria-label="サイドバー">
                
                <?php
                // 広告: サイドバー上部
                if (function_exists('ji_display_ad')): ?>
                    <div class="gi-sidebar-ad gi-sidebar-ad-top">
                        <?php ji_display_ad('archive_column_sidebar_top', 'archive-column'); ?>
                    </div>
                <?php endif; ?>
                
                <!-- 注目記事 -->
                <?php if ($featured_query->have_posts()): ?>
                <section class="gi-sidebar-widget gi-featured-widget" aria-labelledby="gi-featured-title">
                    <h2 id="gi-featured-title" class="gi-widget-title">
                        <svg class="gi-widget-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
                        </svg>
                        <span>注目記事</span>
                    </h2>
                    <div class="gi-widget-content">
                        <div class="gi-featured-list">
                            <?php while ($featured_query->have_posts()): $featured_query->the_post(); ?>
                                <article class="gi-featured-item">
                                    <a href="<?php the_permalink(); ?>" class="gi-featured-link">
                                        <?php if (has_post_thumbnail()): ?>
                                        <figure class="gi-featured-thumb">
                                            <?php the_post_thumbnail('thumbnail', array('loading' => 'lazy')); ?>
                                        </figure>
                                        <?php endif; ?>
                                        <div class="gi-featured-content">
                                            <h3 class="gi-featured-title"><?php the_title(); ?></h3>
                                            <div class="gi-featured-meta">
                                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('n/j'); ?></time>
                                                <?php
                                                $views = get_field('view_count', get_the_ID());
                                                if ($views && $views > 0):
                                                ?>
                                                <span class="gi-meta-views">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    <?php echo number_format($views); ?>
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- PR欄（アフィリエイト広告用） -->
                <section class="gi-sidebar-widget gi-pr-widget" aria-labelledby="gi-pr-title">
                    <h2 id="gi-pr-title" class="gi-widget-title">
                        <svg class="gi-widget-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span>PR</span>
                    </h2>
                    <div class="gi-widget-content">
                        <div class="gi-pr-content">
                            <?php if (function_exists('ji_display_ad')): ?>
                                <?php ji_display_ad('archive_column_sidebar_pr', 'archive-column'); ?>
                            <?php else: ?>
                                <p class="gi-pr-placeholder">広告スペース</p>
                                <p class="gi-pr-note">※ここにアフィリエイト広告が入ります</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
                
                <!-- アクセスランキング -->
                <section class="gi-sidebar-widget gi-ranking-widget" aria-labelledby="gi-ranking-title">
                    <h2 id="gi-ranking-title" class="gi-widget-title">
                        <svg class="gi-widget-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="12" y1="20" x2="12" y2="10"></line>
                            <line x1="18" y1="20" x2="18" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="16"></line>
                        </svg>
                        <span>アクセスランキング</span>
                    </h2>
                    <div class="gi-widget-content">
                        <?php if ($ranking_query->have_posts()): ?>
                            <ol class="gi-ranking-list">
                                <?php 
                                $rank = 1;
                                while ($ranking_query->have_posts()): 
                                    $ranking_query->the_post();
                                    $rank_class = ($rank <= 3) ? 'gi-rank-top' : '';
                                ?>
                                    <li class="gi-ranking-item <?php echo $rank_class; ?>">
                                        <a href="<?php the_permalink(); ?>" class="gi-ranking-link">
                                            <span class="gi-rank-number" aria-label="ランキング<?php echo $rank; ?>位"><?php echo $rank; ?></span>
                                            <div class="gi-rank-content">
                                                <?php if (has_post_thumbnail() && $rank <= 3): ?>
                                                <figure class="gi-rank-thumb">
                                                    <?php the_post_thumbnail('thumbnail', array('loading' => 'lazy')); ?>
                                                </figure>
                                                <?php endif; ?>
                                                <div class="gi-rank-text">
                                                    <h3 class="gi-rank-title"><?php the_title(); ?></h3>
                                                    <div class="gi-rank-meta">
                                                        <time class="gi-rank-date" datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('n/j'); ?></time>
                                                        <?php
                                                        $views = get_field('view_count', get_the_ID());
                                                        if ($views && $views > 0):
                                                        ?>
                                                        <span class="gi-rank-views">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg>
                                                            <?php echo number_format($views); ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php 
                                    $rank++;
                                endwhile; 
                                wp_reset_postdata();
                                ?>
                            </ol>
                        <?php endif; ?>
                    </div>
                </section>
                
                <!-- トレンドキーワード -->
                <?php if (!empty($trending_tags) && !is_wp_error($trending_tags)): ?>
                <section class="gi-sidebar-widget gi-trend-widget" aria-labelledby="gi-trend-title">
                    <h2 id="gi-trend-title" class="gi-widget-title">
                        <svg class="gi-widget-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                        <span>トレンドキーワード</span>
                    </h2>
                    <div class="gi-widget-content">
                        <div class="gi-trend-tags">
                            <?php foreach ($trending_tags as $index => $tag): ?>
                                <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="gi-trend-tag">
                                    <span class="gi-trend-rank"><?php echo ($index + 1); ?></span>
                                    <span class="gi-trend-name"><?php echo esc_html($tag->name); ?></span>
                                    <span class="gi-trend-count"><?php echo number_format($tag->count); ?>件</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
                
                <!-- カテゴリ一覧 -->
                <?php if (!empty($categories)): ?>
                <section class="gi-sidebar-widget gi-category-widget" aria-labelledby="gi-category-title">
                    <h2 id="gi-category-title" class="gi-widget-title">
                        <svg class="gi-widget-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span>カテゴリ</span>
                    </h2>
                    <div class="gi-widget-content">
                        <ul class="gi-category-list">
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="<?php echo esc_url(get_term_link($cat)); ?>">
                                        <span class="gi-cat-name"><?php echo esc_html($cat->name); ?></span>
                                        <span class="gi-cat-count"><?php echo number_format($cat->count); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
                <?php endif; ?>
                
                <?php
                // 広告: サイドバー下部
                if (function_exists('ji_display_ad')): ?>
                    <div class="gi-sidebar-ad gi-sidebar-ad-bottom">
                        <?php ji_display_ad('archive_column_sidebar_bottom', 'archive-column'); ?>
                    </div>
                <?php endif; ?>
                
            </aside>
            <!-- /gi-sidebar -->

        </div>
    </main>

    <!-- トップに戻るボタン -->
    <button class="gi-back-to-top" id="gi-back-to-top" aria-label="トップに戻る">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

</div>

<?php get_footer(); ?>

<style>
/* ============================================
   Archive Column - Premium Edition
   超高品質モノクロスタイリッシュデザイン
   ============================================ */

:root {
    /* カラーパレット */
    --gi-color-black: #000000;
    --gi-color-white: #ffffff;
    --gi-color-gray-50: #fafafa;
    --gi-color-gray-100: #f5f5f5;
    --gi-color-gray-200: #e5e5e5;
    --gi-color-gray-300: #d4d4d4;
    --gi-color-gray-400: #a3a3a3;
    --gi-color-gray-500: #737373;
    --gi-color-gray-600: #525252;
    --gi-color-gray-700: #404040;
    --gi-color-gray-800: #262626;
    --gi-color-gray-900: #171717;
    
    /* スペーシング */
    --gi-space-xs: 4px;
    --gi-space-sm: 8px;
    --gi-space-md: 16px;
    --gi-space-lg: 24px;
    --gi-space-xl: 32px;
    --gi-space-2xl: 48px;
    --gi-space-3xl: 64px;
    
    /* タイポグラフィ */
    --gi-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Noto Sans JP", "Hiragino Kaku Gothic ProN", "Yu Gothic", sans-serif;
    --gi-font-size-xs: 11px;
    --gi-font-size-sm: 13px;
    --gi-font-size-base: 15px;
    --gi-font-size-lg: 17px;
    --gi-font-size-xl: 20px;
    --gi-font-size-2xl: 24px;
    --gi-font-size-3xl: 32px;
    --gi-font-size-4xl: 40px;
    
    /* ボーダー */
    --gi-border-width: 2px;
    --gi-border-radius-sm: 4px;
    --gi-border-radius-md: 8px;
    --gi-border-radius-lg: 12px;
    --gi-border-radius-full: 9999px;
    
    /* シャドウ */
    --gi-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --gi-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --gi-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --gi-shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    
    /* トランジション */
    --gi-transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --gi-transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
    --gi-transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Z-index */
    --gi-z-base: 1;
    --gi-z-dropdown: 10;
    --gi-z-sticky: 20;
    --gi-z-fixed: 30;
    --gi-z-modal: 40;
}

/* ===== 基本スタイル ===== */

.gi-archive-column {
    background: var(--gi-color-gray-50);
    min-height: 100vh;
    font-family: var(--gi-font-family);
    color: var(--gi-color-gray-900);
}

/* スクリーンリーダー専用 */
.gi-sr-only {
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

/* ===== ヘッダーセクション ===== */

.gi-archive-header {
    background: var(--gi-color-white);
    border-bottom: 4px solid var(--gi-color-black);
    padding: var(--gi-space-xl) 0 var(--gi-space-lg);
}

.gi-header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--gi-space-md);
}

/* パンくずリスト */
.gi-breadcrumb {
    margin-bottom: var(--gi-space-lg);
}

.gi-breadcrumb-list {
    display: flex;
    align-items: center;
    gap: var(--gi-space-sm);
    list-style: none;
    font-size: var(--gi-font-size-sm);
    color: var(--gi-color-gray-600);
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
}

.gi-breadcrumb-list a {
    color: var(--gi-color-gray-600);
    text-decoration: none;
    font-weight: 600;
    transition: color var(--gi-transition-base);
}

.gi-breadcrumb-list a:hover {
    color: var(--gi-color-black);
}

.gi-breadcrumb-separator {
    display: flex;
    align-items: center;
    color: var(--gi-color-gray-300);
}

.gi-breadcrumb-list li:last-child {
    color: var(--gi-color-gray-900);
    font-weight: 700;
}

/* タイトルエリア */
.gi-header-title-area {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--gi-space-lg);
}

.gi-title-content {
    flex: 1;
    min-width: 0;
}

.gi-icon-title {
    display: flex;
    align-items: center;
    gap: var(--gi-space-md);
    margin-bottom: var(--gi-space-md);
}

.gi-title-icon {
    flex-shrink: 0;
    color: var(--gi-color-black);
}

.gi-icon-title h1 {
    font-size: var(--gi-font-size-4xl);
    font-weight: 900;
    color: var(--gi-color-black);
    margin: 0;
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.gi-header-description {
    font-size: var(--gi-font-size-base);
    color: var(--gi-color-gray-600);
    line-height: 1.7;
    margin: 0;
    font-weight: 500;
}

/* 総件数表示 */
.gi-total-count {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: var(--gi-space-lg) var(--gi-space-xl);
    background: var(--gi-color-black);
    color: var(--gi-color-white);
    border-radius: var(--gi-border-radius-md);
    flex-shrink: 0;
}

.gi-count-number {
    font-size: var(--gi-font-size-4xl);
    font-weight: 900;
    line-height: 1;
    letter-spacing: -0.02em;
}

.gi-count-label {
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    margin-top: var(--gi-space-xs);
    letter-spacing: 0.05em;
}

/* ===== メインコンテンツ ===== */

.gi-archive-main {
    padding: var(--gi-space-xl) 0 var(--gi-space-3xl);
}

.gi-archive-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--gi-space-md);
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--gi-space-lg);
}

@media (min-width: 1024px) {
    .gi-archive-wrapper {
        grid-template-columns: 1fr 360px;
        gap: var(--gi-space-xl);
    }
}

.gi-main-content {
    min-width: 0;
}

/* ===== 検索・ソートバー ===== */

.gi-control-bar {
    display: flex;
    gap: var(--gi-space-md);
    margin-bottom: var(--gi-space-lg);
    flex-wrap: wrap;
    align-items: center;
}

.gi-search-form {
    flex: 1;
    min-width: 280px;
}

.gi-search-input-group {
    display: flex;
    align-items: center;
    gap: var(--gi-space-sm);
    background: var(--gi-color-white);
    border: var(--gi-border-width) solid var(--gi-color-black);
    padding: var(--gi-space-sm) var(--gi-space-md);
    border-radius: var(--gi-border-radius-sm);
    transition: box-shadow var(--gi-transition-base);
}

.gi-search-input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

.gi-search-icon {
    color: var(--gi-color-gray-600);
    flex-shrink: 0;
}

.gi-search-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: var(--gi-font-size-base);
    padding: var(--gi-space-sm);
    background: transparent;
    font-family: var(--gi-font-family);
    font-weight: 500;
}

.gi-search-input::placeholder {
    color: var(--gi-color-gray-400);
}

.gi-search-btn {
    padding: var(--gi-space-sm) var(--gi-space-lg);
    background: var(--gi-color-black);
    color: var(--gi-color-white);
    border: none;
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    cursor: pointer;
    border-radius: var(--gi-border-radius-sm);
    transition: all var(--gi-transition-base);
    font-family: var(--gi-font-family);
    letter-spacing: 0.02em;
}

.gi-search-btn:hover {
    background: var(--gi-color-gray-800);
    transform: translateY(-1px);
    box-shadow: var(--gi-shadow-md);
}

.gi-search-btn:active {
    transform: translateY(0);
}

/* ソートドロップダウン */
.gi-sort-dropdown {
    display: flex;
    align-items: center;
    gap: var(--gi-space-sm);
    padding: var(--gi-space-sm) var(--gi-space-md);
    background: var(--gi-color-white);
    border: var(--gi-border-width) solid var(--gi-color-black);
    border-radius: var(--gi-border-radius-sm);
}

.gi-sort-label {
    display: flex;
    align-items: center;
    gap: var(--gi-space-xs);
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    color: var(--gi-color-gray-900);
    margin: 0;
}

.gi-sort-select {
    border: 1px solid var(--gi-color-gray-300);
    padding: var(--gi-space-xs) var(--gi-space-sm);
    font-size: var(--gi-font-size-sm);
    font-weight: 600;
    border-radius: var(--gi-border-radius-sm);
    cursor: pointer;
    background: var(--gi-color-white);
    font-family: var(--gi-font-family);
    transition: border-color var(--gi-transition-base);
}

.gi-sort-select:focus {
    outline: none;
    border-color: var(--gi-color-black);
}

/* ===== カテゴリタブフィルター ===== */

.gi-category-tabs {
    margin-bottom: var(--gi-space-xl);
    border-bottom: 4px solid var(--gi-color-black);
    overflow: hidden;
}

.gi-tabs-scroll-container {
    display: flex;
    gap: var(--gi-space-xs);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: var(--gi-color-gray-400) var(--gi-color-gray-100);
    margin-bottom: -4px;
}

.gi-tabs-scroll-container::-webkit-scrollbar {
    height: 6px;
}

.gi-tabs-scroll-container::-webkit-scrollbar-track {
    background: var(--gi-color-gray-100);
    border-radius: var(--gi-border-radius-sm);
}

.gi-tabs-scroll-container::-webkit-scrollbar-thumb {
    background: var(--gi-color-gray-400);
    border-radius: var(--gi-border-radius-sm);
}

.gi-tabs-scroll-container::-webkit-scrollbar-thumb:hover {
    background: var(--gi-color-gray-500);
}

.gi-tab-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--gi-space-sm);
    padding: var(--gi-space-md) var(--gi-space-xl);
    font-size: var(--gi-font-size-base);
    font-weight: 700;
    color: var(--gi-color-gray-700);
    background: var(--gi-color-gray-100);
    border: var(--gi-border-width) solid var(--gi-color-gray-200);
    border-bottom: 4px solid transparent;
    cursor: pointer;
    transition: all var(--gi-transition-base);
    white-space: nowrap;
    position: relative;
    border-radius: var(--gi-border-radius-md) var(--gi-border-radius-md) 0 0;
    letter-spacing: 0.02em;
    font-family: var(--gi-font-family);
}

.gi-tab-btn:hover {
    background: var(--gi-color-white);
    color: var(--gi-color-black);
    border-color: var(--gi-color-gray-300);
    transform: translateY(-2px);
}

.gi-tab-btn.gi-tab-active {
    color: var(--gi-color-black);
    background: var(--gi-color-white);
    border-color: var(--gi-color-black);
    border-bottom-color: var(--gi-color-white);
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
    z-index: var(--gi-z-base);
}

.gi-tab-icon {
    flex-shrink: 0;
}

.gi-tab-label {
    font-size: var(--gi-font-size-base);
}

.gi-tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 22px;
    padding: 0 var(--gi-space-sm);
    font-size: var(--gi-font-size-xs);
    font-weight: 800;
    color: var(--gi-color-white);
    background: var(--gi-color-gray-700);
    border-radius: var(--gi-border-radius-full);
    letter-spacing: 0;
}

.gi-tab-btn.gi-tab-active .gi-tab-count {
    background: var(--gi-color-black);
}

/* ===== 新着トピック欄 ===== */

.gi-topics-section {
    background: var(--gi-color-white);
    border: 3px solid var(--gi-color-black);
    margin-bottom: var(--gi-space-xl);
    border-radius: var(--gi-border-radius-md);
    overflow: hidden;
}

.gi-topics-title {
    display: flex;
    align-items: center;
    gap: var(--gi-space-sm);
    margin: 0;
    padding: var(--gi-space-md) var(--gi-space-lg);
    font-size: var(--gi-font-size-xl);
    font-weight: 900;
    color: var(--gi-color-white);
    background: var(--gi-color-black);
    letter-spacing: -0.01em;
}

.gi-topics-icon {
    flex-shrink: 0;
}

.gi-topics-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.gi-topic-item {
    border-bottom: 1px solid var(--gi-color-gray-200);
}

.gi-topic-item:last-child {
    border-bottom: none;
}

.gi-topic-link {
    display: flex;
    align-items: center;
    gap: var(--gi-space-md);
    padding: var(--gi-space-md) var(--gi-space-lg);
    text-decoration: none;
    color: inherit;
    transition: background-color var(--gi-transition-base);
}

.gi-topic-link:hover {
    background: var(--gi-color-gray-50);
}

.gi-topic-time {
    display: inline-block;
    min-width: 80px;
    font-size: var(--gi-font-size-xs);
    font-weight: 700;
    color: var(--gi-color-gray-600);
}

.gi-topic-title {
    flex: 1;
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    color: var(--gi-color-gray-900);
    line-height: 1.5;
}

.gi-topic-link:hover .gi-topic-title {
    color: var(--gi-color-black);
}

.gi-topic-category {
    display: inline-block;
    padding: var(--gi-space-xs) var(--gi-space-sm);
    font-size: var(--gi-font-size-xs);
    font-weight: 700;
    color: var(--gi-color-white);
    background: var(--gi-color-gray-600);
    border-radius: var(--gi-border-radius-sm);
    letter-spacing: 0.05em;
}

.gi-no-topics {
    padding: var(--gi-space-2xl) var(--gi-space-lg);
    text-align: center;
    color: var(--gi-color-gray-600);
    font-size: var(--gi-font-size-sm);
    font-weight: 600;
}

/* ===== 記事リスト ===== */

.gi-articles-container {
    background: var(--gi-color-white);
    border: 3px solid var(--gi-color-black);
    min-height: 400px;
    border-radius: var(--gi-border-radius-md);
    overflow: hidden;
}

.gi-articles-list {
    /* カードの区切り線はcard.php内で管理 */
}

/* ローディング表示 */
.gi-loading-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--gi-space-3xl) var(--gi-space-lg);
}

.gi-loading-spinner {
    margin-bottom: var(--gi-space-lg);
    animation: giSpinRotate 1.5s linear infinite;
}

@keyframes giSpinRotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.gi-loading-text {
    color: var(--gi-color-gray-600);
    font-size: var(--gi-font-size-base);
    font-weight: 600;
    margin: 0;
}

/* フェードアニメーション */
.gi-fade-in {
    animation: giFadeIn 0.4s ease-in;
}

@keyframes giFadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===== ページネーション ===== */

.gi-pagination-nav {
    margin-top: 0;
    padding: var(--gi-space-xl) var(--gi-space-lg);
    border-top: 3px solid var(--gi-color-black);
}

.gi-pagination-list {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: var(--gi-space-sm);
    list-style: none;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
}

.gi-pagination-list a,
.gi-pagination-list span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: 0 var(--gi-space-md);
    font-size: var(--gi-font-size-base);
    font-weight: 700;
    border: var(--gi-border-width) solid var(--gi-color-black);
    background: var(--gi-color-white);
    color: var(--gi-color-black);
    text-decoration: none;
    transition: all var(--gi-transition-base);
    border-radius: var(--gi-border-radius-sm);
    font-family: var(--gi-font-family);
}

.gi-pagination-list a:hover {
    background: var(--gi-color-gray-900);
    color: var(--gi-color-white);
    transform: translateY(-2px);
    box-shadow: var(--gi-shadow-md);
}

.gi-pagination-list .current {
    background: var(--gi-color-black);
    color: var(--gi-color-white);
    font-weight: 900;
}

.gi-pagination-list .dots {
    border: none;
    background: none;
    color: var(--gi-color-gray-600);
}

/* 記事なしメッセージ */
.gi-no-posts {
    text-align: center;
    padding: var(--gi-space-3xl) var(--gi-space-lg);
}

.gi-no-posts-icon {
    margin: 0 auto var(--gi-space-lg);
    color: var(--gi-color-gray-300);
}

.gi-no-posts-title {
    font-size: var(--gi-font-size-2xl);
    font-weight: 800;
    color: var(--gi-color-black);
    margin: 0 0 var(--gi-space-md);
    letter-spacing: -0.01em;
}

.gi-no-posts-desc {
    font-size: var(--gi-font-size-base);
    color: var(--gi-color-gray-600);
    margin: 0 0 var(--gi-space-xl);
    line-height: 1.7;
}

.gi-back-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--gi-space-sm);
    padding: var(--gi-space-md) var(--gi-space-xl);
    font-size: var(--gi-font-size-base);
    font-weight: 700;
    color: var(--gi-color-white);
    background: var(--gi-color-black);
    border: var(--gi-border-width) solid var(--gi-color-black);
    text-decoration: none;
    transition: all var(--gi-transition-base);
    border-radius: var(--gi-border-radius-full);
    font-family: var(--gi-font-family);
    letter-spacing: 0.02em;
}

.gi-back-btn:hover {
    background: var(--gi-color-gray-800);
    transform: translateY(-2px);
    box-shadow: var(--gi-shadow-lg);
}

/* ===== サイドバー ===== */

.gi-sidebar {
    display: flex;
    flex-direction: column;
    gap: var(--gi-space-lg);
}

@media (min-width: 1024px) {
    .gi-sidebar {
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        align-self: flex-start;
    }
}

/* ウィジェット共通 */
.gi-sidebar-widget {
    background: var(--gi-color-white);
    border: 3px solid var(--gi-color-black);
    overflow: hidden;
    border-radius: var(--gi-border-radius-md);
}

.gi-widget-title {
    display: flex;
    align-items: center;
    gap: var(--gi-space-sm);
    margin: 0;
    padding: var(--gi-space-md) var(--gi-space-lg);
    font-size: var(--gi-font-size-xl);
    font-weight: 900;
    color: var(--gi-color-white);
    background: var(--gi-color-black);
    letter-spacing: -0.01em;
}

.gi-widget-icon {
    flex-shrink: 0;
}

.gi-widget-content {
    padding: 0;
}

/* 注目記事 */
.gi-featured-list {
    padding: var(--gi-space-md);
    display: flex;
    flex-direction: column;
    gap: var(--gi-space-md);
}

.gi-featured-item {
    border-bottom: 1px solid var(--gi-color-gray-200);
    padding-bottom: var(--gi-space-md);
}

.gi-featured-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.gi-featured-link {
    display: flex;
    gap: var(--gi-space-md);
    text-decoration: none;
    color: inherit;
}

.gi-featured-thumb {
    width: 100px;
    height: 75px;
    flex-shrink: 0;
    border-radius: var(--gi-border-radius-sm);
    overflow: hidden;
    background: var(--gi-color-gray-100);
    margin: 0;
}

.gi-featured-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--gi-transition-slow);
}

.gi-featured-link:hover .gi-featured-thumb img {
    transform: scale(1.1);
}

.gi-featured-content {
    flex: 1;
    min-width: 0;
}

.gi-featured-title {
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    line-height: 1.5;
    color: var(--gi-color-gray-900);
    margin: 0 0 var(--gi-space-sm);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.gi-featured-link:hover .gi-featured-title {
    color: var(--gi-color-black);
}

.gi-featured-meta {
    display: flex;
    gap: var(--gi-space-sm);
    font-size: var(--gi-font-size-xs);
    color: var(--gi-color-gray-600);
}

.gi-featured-meta time {
    font-weight: 600;
}

.gi-meta-views {
    display: flex;
    align-items: center;
    gap: var(--gi-space-xs);
    font-weight: 700;
}

/* PR欄 */
.gi-pr-content {
    padding: var(--gi-space-lg);
    text-align: center;
    background: var(--gi-color-gray-50);
    min-height: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.gi-pr-placeholder {
    font-size: var(--gi-font-size-xl);
    font-weight: 700;
    color: var(--gi-color-gray-600);
    margin: 0 0 var(--gi-space-md);
}

.gi-pr-note {
    font-size: var(--gi-font-size-xs);
    color: var(--gi-color-gray-600);
    margin: 0;
}

/* アクセスランキング */
.gi-ranking-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.gi-ranking-item {
    border-bottom: 1px solid var(--gi-color-gray-200);
}

.gi-ranking-item:last-child {
    border-bottom: none;
}

.gi-ranking-link {
    display: flex;
    align-items: flex-start;
    gap: var(--gi-space-md);
    padding: var(--gi-space-md) var(--gi-space-lg);
    text-decoration: none;
    color: inherit;
    transition: background-color var(--gi-transition-base);
}

.gi-ranking-link:hover {
    background: var(--gi-color-gray-50);
}

.gi-rank-number {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    font-size: var(--gi-font-size-lg);
    font-weight: 900;
    color: var(--gi-color-gray-600);
    background: var(--gi-color-gray-100);
    border-radius: var(--gi-border-radius-sm);
    flex-shrink: 0;
}

.gi-rank-top .gi-rank-number {
    background: var(--gi-color-black);
    color: var(--gi-color-white);
    font-size: var(--gi-font-size-xl);
}

.gi-rank-content {
    flex: 1;
    min-width: 0;
    display: flex;
    gap: var(--gi-space-sm);
}

.gi-rank-thumb {
    width: 60px;
    height: 45px;
    flex-shrink: 0;
    border-radius: var(--gi-border-radius-sm);
    overflow: hidden;
    background: var(--gi-color-gray-100);
    margin: 0;
}

.gi-rank-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gi-rank-text {
    flex: 1;
    min-width: 0;
}

.gi-rank-title {
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    line-height: 1.5;
    color: var(--gi-color-gray-900);
    margin: 0 0 var(--gi-space-xs);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.gi-ranking-link:hover .gi-rank-title {
    color: var(--gi-color-black);
}

.gi-rank-meta {
    display: flex;
    gap: var(--gi-space-sm);
    font-size: var(--gi-font-size-xs);
    color: var(--gi-color-gray-600);
}

.gi-rank-meta time {
    font-weight: 600;
}

.gi-rank-views {
    display: flex;
    align-items: center;
    gap: var(--gi-space-xs);
    font-weight: 700;
}

/* トレンドキーワード */
.gi-trend-tags {
    padding: var(--gi-space-md);
    display: flex;
    flex-direction: column;
    gap: var(--gi-space-sm);
}

.gi-trend-tag {
    display: flex;
    align-items: center;
    gap: var(--gi-space-sm);
    padding: var(--gi-space-sm) var(--gi-space-md);
    background: var(--gi-color-gray-50);
    border: 1px solid var(--gi-color-gray-200);
    border-radius: var(--gi-border-radius-sm);
    text-decoration: none;
    color: inherit;
    transition: all var(--gi-transition-base);
}

.gi-trend-tag:hover {
    background: var(--gi-color-gray-100);
    border-color: var(--gi-color-black);
    transform: translateX(4px);
}

.gi-trend-rank {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    font-size: var(--gi-font-size-sm);
    font-weight: 900;
    color: var(--gi-color-white);
    background: var(--gi-color-gray-600);
    border-radius: var(--gi-border-radius-full);
}

.gi-trend-tag:nth-child(1) .gi-trend-rank,
.gi-trend-tag:nth-child(2) .gi-trend-rank,
.gi-trend-tag:nth-child(3) .gi-trend-rank {
    background: var(--gi-color-black);
}

.gi-trend-name {
    flex: 1;
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    color: var(--gi-color-gray-900);
}

.gi-trend-count {
    font-size: var(--gi-font-size-xs);
    font-weight: 600;
    color: var(--gi-color-gray-600);
}

/* カテゴリリスト */
.gi-category-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.gi-category-list li {
    border-bottom: 1px solid var(--gi-color-gray-200);
}

.gi-category-list li:last-child {
    border-bottom: none;
}

.gi-category-list a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--gi-space-md) var(--gi-space-lg);
    text-decoration: none;
    color: inherit;
    transition: background-color var(--gi-transition-base);
}

.gi-category-list a:hover {
    background: var(--gi-color-gray-50);
}

.gi-cat-name {
    font-size: var(--gi-font-size-sm);
    font-weight: 700;
    color: var(--gi-color-gray-900);
}

.gi-category-list a:hover .gi-cat-name {
    color: var(--gi-color-black);
}

.gi-cat-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 24px;
    padding: 0 var(--gi-space-sm);
    font-size: var(--gi-font-size-xs);
    font-weight: 800;
    color: var(--gi-color-white);
    background: var(--gi-color-gray-600);
    border-radius: var(--gi-border-radius-full);
}

/* トップに戻るボタン */
.gi-back-to-top {
    position: fixed;
    bottom: var(--gi-space-xl);
    right: var(--gi-space-xl);
    width: 56px;
    height: 56px;
    background: var(--gi-color-black);
    color: var(--gi-color-white);
    border: none;
    border-radius: var(--gi-border-radius-full);
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all var(--gi-transition-slow);
    z-index: var(--gi-z-fixed);
    box-shadow: var(--gi-shadow-xl);
}

.gi-back-to-top.gi-visible {
    opacity: 1;
    visibility: visible;
}

.gi-back-to-top:hover {
    background: var(--gi-color-gray-800);
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
}

.gi-back-to-top:active {
    transform: translateY(-2px);
}

/* ===== レスポンシブ調整 ===== */

@media (max-width: 767px) {
    .gi-archive-header {
        padding: var(--gi-space-lg) 0 var(--gi-space-md);
    }
    
    .gi-icon-title {
        gap: var(--gi-space-sm);
    }
    
    .gi-title-icon {
        width: 36px;
        height: 36px;
    }
    
    .gi-icon-title h1 {
        font-size: var(--gi-font-size-2xl);
    }
    
    .gi-header-title-area {
        flex-direction: column;
    }
    
    .gi-total-count {
        align-self: flex-start;
        padding: var(--gi-space-md) var(--gi-space-lg);
    }
    
    .gi-count-number {
        font-size: var(--gi-font-size-2xl);
    }
    
    .gi-control-bar {
        flex-direction: column;
        gap: var(--gi-space-md);
    }
    
    .gi-search-form,
    .gi-sort-dropdown {
        width: 100%;
    }
    
    .gi-tab-btn {
        padding: var(--gi-space-md) var(--gi-space-lg);
        font-size: var(--gi-font-size-sm);
    }
    
    .gi-tab-label {
        display: none;
    }
    
    .gi-tab-btn.gi-tab-active .gi-tab-label {
        display: inline;
    }
    
    .gi-articles-container {
        border-width: var(--gi-border-width);
    }
    
    .gi-pagination-nav {
        padding: var(--gi-space-lg) var(--gi-space-md);
    }
    
    .gi-back-to-top {
        width: 48px;
        height: 48px;
        bottom: var(--gi-space-lg);
        right: var(--gi-space-lg);
    }
    
    .gi-topic-time {
        display: none;
    }
    
    .gi-topic-link {
        gap: var(--gi-space-sm);
        padding: var(--gi-space-md);
    }
}

@media (max-width: 1023px) {
    .gi-sidebar {
        order: -1;
        margin-bottom: var(--gi-space-lg);
    }
    
    .gi-sidebar-widget {
        border-width: var(--gi-border-width);
    }
    
    .gi-widget-title {
        padding: var(--gi-space-md);
        font-size: var(--gi-font-size-lg);
    }
    
    /* ランキングは5件のみ表示 */
    .gi-ranking-item:nth-child(n+6) {
        display: none;
    }
}

@media (min-width: 1280px) {
    .gi-header-container,
    .gi-archive-wrapper {
        max-width: 1400px;
    }
    
    .gi-archive-wrapper {
        grid-template-columns: 1fr 380px;
    }
}

/* ===== アクセシビリティ ===== */

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* フォーカス表示 */
.gi-tab-btn:focus-visible,
.gi-search-btn:focus-visible,
.gi-sort-select:focus-visible,
.gi-back-btn:focus-visible,
.gi-back-to-top:focus-visible {
    outline: 3px solid var(--gi-color-black);
    outline-offset: 2px;
}

/* ===== プリント対応 ===== */

@media print {
    .gi-archive-column {
        background: white;
    }
    
    .gi-category-tabs,
    .gi-control-bar,
    .gi-sidebar,
    .gi-back-to-top {
        display: none;
    }
    
    .gi-articles-container {
        border: none;
    }
}
</style>

<script>
/**
 * Archive Column - JavaScript
 * タブ機能、AJAX読み込み、UI制御
 */
(function() {
    'use strict';
    
    // ===== 定数 =====
    const SELECTORS = {
        TAB_BTN: '.gi-tab-btn',
        TOPICS_CONTAINER: '#gi-topics-list-container',
        ARTICLES_WRAPPER: '#gi-articles-list-wrapper',
        LOADING_INDICATOR: '#gi-loading-indicator',
        SORT_SELECT: '#gi-sort-select',
        BACK_TO_TOP: '#gi-back-to-top',
        TABS_CONTAINER: '.gi-tabs-scroll-container',
    };
    
    const CLASSES = {
        ACTIVE: 'gi-tab-active',
        VISIBLE: 'gi-visible',
        FADE_IN: 'gi-fade-in',
    };
    
    const API_BASE = '/wp-json/wp/v2/columns';
    
    // ===== ユーティリティ関数 =====
    
    function $(selector, context = document) {
        return context.querySelector(selector);
    }
    
    function $$(selector, context = document) {
        return Array.from(context.querySelectorAll(selector));
    }
    
    function on(element, event, handler) {
        if (element) {
            element.addEventListener(event, handler);
        }
    }
    
    function log(...args) {
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            console.log('[GI Archive]', ...args);
        }
    }
    
    // ===== カテゴリタブマネージャー =====
    
    class CategoryTabManager {
        constructor() {
            this.tabs = $$(SELECTORS.TAB_BTN);
            this.topicsContainer = $(SELECTORS.TOPICS_CONTAINER);
            this.articlesWrapper = $(SELECTORS.ARTICLES_WRAPPER);
            this.loadingIndicator = $(SELECTORS.LOADING_INDICATOR);
            
            this.init();
        }
        
        init() {
            if (!this.tabs.length) {
                log('No tabs found');
                return;
            }
            
            this.tabs.forEach(tab => {
                on(tab, 'click', (e) => this.handleTabClick(e, tab));
            });
            
            // 初期表示のタブをアクティブに
            const activeTab = this.tabs.find(t => t.classList.contains(CLASSES.ACTIVE));
            if (activeTab) {
                this.scrollToActiveTab();
            }
            
            log('CategoryTabManager initialized', this.tabs.length, 'tabs');
        }
        
        handleTabClick(e, tab) {
            e.preventDefault();
            
            // アクティブタブの切り替え
            this.tabs.forEach(t => {
                t.classList.remove(CLASSES.ACTIVE);
                t.removeAttribute('aria-current');
            });
            tab.classList.add(CLASSES.ACTIVE);
            tab.setAttribute('aria-current', 'page');
            
            const categoryId = tab.dataset.categoryId || '';
            const categorySlug = tab.dataset.categorySlug || '';
            
            // 記事を読み込み
            this.loadCategoryPosts(categoryId, categorySlug, 1);
        }
        
        async loadCategoryPosts(categoryId, categorySlug, page = 1) {
            // ローディング表示
            this.showLoading();
            
            try {
                // REST APIで記事を取得
                const topicsUrl = categoryId 
                    ? `${API_BASE}?column-categories=${categoryId}&per_page=20&orderby=date&order=desc&_embed=1`
                    : `${API_BASE}?per_page=20&orderby=date&order=desc&_embed=1`;
                
                const articlesUrl = categoryId
                    ? `${API_BASE}?column-categories=${categoryId}&per_page=12&page=${page}&orderby=date&order=desc&_embed=1`
                    : `${API_BASE}?per_page=12&page=${page}&orderby=date&order=desc&_embed=1`;
                
                // トピックと記事を並列取得
                const [topicsResponse, articlesResponse] = await Promise.all([
                    fetch(topicsUrl),
                    fetch(articlesUrl)
                ]);
                
                if (!topicsResponse.ok || !articlesResponse.ok) {
                    throw new Error('API request failed');
                }
                
                const topicsPosts = await topicsResponse.json();
                const articlesPosts = await articlesResponse.json();
                
                // ページネーション情報取得
                const totalPages = parseInt(articlesResponse.headers.get('X-WP-TotalPages')) || 1;
                
                log('Loaded:', topicsPosts.length, 'topics,', articlesPosts.length, 'articles, page:', page, '/', totalPages);
                
                // トピックリストを更新
                this.updateTopicsList(topicsPosts);
                
                // 記事リストを更新
                this.updateArticlesList(articlesPosts);
                
                // ページネーション更新
                this.updatePagination(page, totalPages, categoryId, categorySlug);
                
                // URL更新
                this.updateURL(categorySlug, page);
                
            } catch (error) {
                log('Error loading posts:', error);
                this.showError();
            } finally {
                this.hideLoading();
            }
        }
        
        updateTopicsList(posts) {
            if (!posts || posts.length === 0) {
                this.topicsContainer.innerHTML = '<p class="gi-no-topics" role="status">このカテゴリの記事はまだありません。</p>';
                return;
            }
            
            let html = '<ul class="gi-topics-list" role="list">';
            posts.forEach(post => {
                const timeAgo = this.getTimeAgo(post.date);
                let categoryName = '';
                if (post._embedded && post._embedded['wp:term'] && post._embedded['wp:term'][0]) {
                    const categories = post._embedded['wp:term'][0];
                    if (categories && categories.length > 0) {
                        categoryName = categories[0].name;
                    }
                }
                
                html += `
                    <li class="gi-topic-item" role="listitem">
                        <a href="${post.link}" class="gi-topic-link">
                            <time class="gi-topic-time" datetime="${post.date}">${timeAgo}前</time>
                            <span class="gi-topic-title">${post.title.rendered}</span>
                            ${categoryName ? `<span class="gi-topic-category">${categoryName}</span>` : ''}
                        </a>
                    </li>
                `;
            });
            html += '</ul>';
            
            this.topicsContainer.innerHTML = html;
        }
        
        updateArticlesList(posts) {
            if (!posts || posts.length === 0) {
                this.articlesWrapper.innerHTML = `
                    <div class="gi-no-posts" role="status">
                        <svg class="gi-no-posts-icon" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <h2 class="gi-no-posts-title">記事が見つかりませんでした</h2>
                        <p class="gi-no-posts-desc">このカテゴリの記事はまだありません。</p>
                    </div>
                `;
                return;
            }
            
            let html = '<div class="gi-articles-list" role="list">';
            posts.forEach(post => {
                html += this.createArticleCard(post);
            });
            html += '</div>';
            
            this.articlesWrapper.innerHTML = html;
        }
        
        createArticleCard(post) {
            // サムネイル取得
            let thumbnail = '';
            if (post._embedded && post._embedded['wp:featuredmedia'] && post._embedded['wp:featuredmedia'][0]) {
                thumbnail = post._embedded['wp:featuredmedia'][0].source_url || '';
            }
            
            // カテゴリ取得
            let categoryName = '';
            if (post._embedded && post._embedded['wp:term'] && post._embedded['wp:term'][0]) {
                const categories = post._embedded['wp:term'][0];
                if (categories && categories.length > 0) {
                    categoryName = categories[0].name;
                }
            }
            
            const views = post.meta?.view_count || 0;
            const postDate = new Date(post.date);
            const formattedDate = `${postDate.getFullYear()}/${String(postDate.getMonth() + 1).padStart(2, '0')}/${String(postDate.getDate()).padStart(2, '0')}`;
            
            // 新着判定（7日以内）
            const isNew = (Date.now() - postDate.getTime()) < (7 * 24 * 60 * 60 * 1000);
            
            // 抜粋作成
            let excerpt = '';
            if (post.excerpt && post.excerpt.rendered) {
                const div = document.createElement('div');
                div.innerHTML = post.excerpt.rendered;
                excerpt = div.textContent.substring(0, 100) + '...';
            }
            
            return `
                <article class="column-card-compact">
                    <a href="${post.link}" class="card-link-compact">
                        <div class="card-inner">
                            ${thumbnail ? `
                                <div class="card-thumb">
                                    <img src="${thumbnail}" 
                                         alt="${post.title.rendered}"
                                         loading="lazy">
                                    ${isNew ? '<span class="badge badge-new">NEW</span>' : ''}
                                </div>
                            ` : ''}
                            <div class="card-text">
                                <div class="card-meta">
                                    ${categoryName ? `<span class="meta-category">${categoryName}</span>` : ''}
                                </div>
                                <h3 class="card-title-compact">${post.title.rendered}</h3>
                                ${excerpt ? `<p class="card-excerpt-compact">${excerpt}</p>` : ''}
                                <div class="card-footer-meta">
                                    <span class="meta-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        ${formattedDate}
                                    </span>
                                    ${views > 0 ? `
                                        <span class="meta-views">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            ${Number(views).toLocaleString()}
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </a>
                </article>
            `;
        }
        
        updatePagination(currentPage, totalPages, categoryId, categorySlug) {
            const paginationNav = document.querySelector('.gi-pagination-nav');
            if (!paginationNav) return;
            
            if (totalPages <= 1) {
                paginationNav.style.display = 'none';
                return;
            }
            
            paginationNav.style.display = 'block';
            
            let html = '<ul class="gi-pagination-list">';
            
            // 前へボタン
            if (currentPage > 1) {
                html += `<li><a href="#" class="page-link" data-page="${currentPage - 1}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg> 前へ</a></li>`;
            }
            
            // ページ番号
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);
            
            if (startPage > 1) {
                html += `<li><a href="#" class="page-link" data-page="1">1</a></li>`;
                if (startPage > 2) {
                    html += `<li><span class="dots">...</span></li>`;
                }
            }
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === currentPage) {
                    html += `<li><span class="current">${i}</span></li>`;
                } else {
                    html += `<li><a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
                }
            }
            
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<li><span class="dots">...</span></li>`;
                }
                html += `<li><a href="#" class="page-link" data-page="${totalPages}">${totalPages}</a></li>`;
            }
            
            // 次へボタン
            if (currentPage < totalPages) {
                html += `<li><a href="#" class="page-link" data-page="${currentPage + 1}">次へ <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></a></li>`;
            }
            
            html += '</ul>';
            
            paginationNav.innerHTML = html;
            
            // ページリンクにイベント追加
            const pageLinks = paginationNav.querySelectorAll('.page-link');
            pageLinks.forEach(link => {
                on(link, 'click', (e) => {
                    e.preventDefault();
                    const page = parseInt(link.dataset.page);
                    this.loadCategoryPosts(categoryId, categorySlug, page);
                    
                    // 記事コンテナまでスクロール
                    const articlesContainer = document.getElementById('gi-articles-container');
                    if (articlesContainer) {
                        articlesContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        }
        
        updateURL(categorySlug, page) {
            let newUrl;
            if (categorySlug) {
                newUrl = page > 1 ? `/column-category/${categorySlug}/page/${page}/` : `/column-category/${categorySlug}/`;
            } else {
                newUrl = page > 1 ? `/column/page/${page}/` : `/column/`;
            }
            window.history.replaceState({ categorySlug, page }, '', newUrl);
        }
        
        showLoading() {
            if (this.loadingIndicator) this.loadingIndicator.style.display = 'flex';
            if (this.articlesWrapper) this.articlesWrapper.style.opacity = '0.3';
            if (this.topicsContainer) this.topicsContainer.style.opacity = '0.3';
        }
        
        hideLoading() {
            if (this.loadingIndicator) this.loadingIndicator.style.display = 'none';
            if (this.articlesWrapper) {
                this.articlesWrapper.style.opacity = '1';
                this.articlesWrapper.classList.add(CLASSES.FADE_IN);
            }
            if (this.topicsContainer) {
                this.topicsContainer.style.opacity = '1';
                this.topicsContainer.classList.add(CLASSES.FADE_IN);
            }
        }
        
        showError() {
            if (this.topicsContainer) {
                this.topicsContainer.innerHTML = '<p class="gi-no-topics">記事の読み込みに失敗しました。</p>';
            }
            if (this.articlesWrapper) {
                this.articlesWrapper.innerHTML = '<div class="gi-no-posts"><p>記事の読み込みに失敗しました。</p></div>';
            }
        }
        
        getTimeAgo(dateString) {
            const now = new Date();
            const postDate = new Date(dateString);
            const diffMs = now - postDate;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 60) {
                return `${diffMins}分`;
            } else if (diffHours < 24) {
                return `${diffHours}時間`;
            } else {
                return `${diffDays}日`;
            }
        }
        
        scrollToActiveTab() {
            const container = $(SELECTORS.TABS_CONTAINER);
            const activeTab = container?.querySelector(`.${CLASSES.ACTIVE}`);
            
            if (!container || !activeTab) return;
            
            setTimeout(() => {
                const containerWidth = container.offsetWidth;
                const tabOffset = activeTab.offsetLeft;
                const tabWidth = activeTab.offsetWidth;
                const scrollPosition = tabOffset - (containerWidth / 2) + (tabWidth / 2);
                
                container.scrollTo({
                    left: scrollPosition,
                    behavior: 'smooth'
                });
            }, 100);
        }
    }
    
    // ===== ソート機能 =====
    
    class SortManager {
        constructor() {
            this.sortSelect = $(SELECTORS.SORT_SELECT);
            this.init();
        }
        
        init() {
            if (!this.sortSelect) return;
            
            on(this.sortSelect, 'change', () => {
                const orderby = this.sortSelect.value;
                const url = new URL(window.location.href);
                url.searchParams.set('orderby', orderby);
                window.location.href = url.toString();
            });
            
            log('SortManager initialized');
        }
    }
    
    // ===== トップに戻るボタン =====
    
    class BackToTopButton {
        constructor() {
            this.button = $(SELECTORS.BACK_TO_TOP);
            this.init();
        }
        
        init() {
            if (!this.button) return;
            
            // スクロールイベント
            on(window, 'scroll', () => {
                if (window.scrollY > 400) {
                    this.button.classList.add(CLASSES.VISIBLE);
                } else {
                    this.button.classList.remove(CLASSES.VISIBLE);
                }
            });
            
            // クリックイベント
            on(this.button, 'click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            log('BackToTopButton initialized');
        }
    }
    
    // ===== 初期化 =====
    
    function init() {
        log('Initializing...');
        
        window.giCategoryTabManager = new CategoryTabManager();
        window.giSortManager = new SortManager();
        window.giBackToTopButton = new BackToTopButton();
        
        log('Initialization complete');
    }
    
    // DOMContentLoaded後に初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
</script>