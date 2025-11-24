<?php
/**
 * Template Part: Ultimate UI Grant & Column Section
 * 補助金・コラム・ランキング統合セクション（フラット構造・モノクロームUI版）
 * * @package Grant_Insight_Perfect
 * @version 25.0.0 - Ultimate Monochrome
 * * Concept:
 * - Monochrome & Minimal: 白と黒、グレーのみで構成された洗練されたUI
 * - Flat Structure: ネストを排除し、縦スクロールで完結するモバイルファースト設計
 * - High Performance: 必要な情報への最短アクセス
 */

if (!defined('ABSPATH')) exit;

// ==========================================================================
// 1. データ取得 (Data Retrieval)
// ==========================================================================

// 日付比較用
$today = date('Y-m-d');

// 1-A. 補助金データ：注目 (Featured)
$grants_featured = new WP_Query([
    'post_type'      => 'grant',
    'posts_per_page' => 3, // 重要度が高いので3つに厳選
    'post_status'    => 'publish',
    'meta_key'       => 'is_featured',
    'meta_value'     => '1',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);

// 1-B. 補助金データ：締切間近 (Deadline Soon)
$grants_deadline = new WP_Query([
    'post_type'      => 'grant',
    'posts_per_page' => 5,
    'post_status'    => 'publish',
    'meta_key'       => 'deadline_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [
        [
            'key'     => 'deadline_date',
            'value'   => $today,
            'compare' => '>=',
            'type'    => 'DATE'
        ]
    ],
    'no_found_rows'  => true,
]);

// 1-C. 補助金データ：新着 (New)
$grants_new = new WP_Query([
    'post_type'      => 'grant',
    'posts_per_page' => 5,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);

// 2-A. コラムデータ：新着 (New Columns)
$columns_new = new WP_Query([
    'post_type'      => 'column',
    'posts_per_page' => 4,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);

// 2-B. コラムデータ：人気 (Popular Columns)
$columns_popular = new WP_Query([
    'post_type'      => 'column',
    'posts_per_page' => 5,
    'post_status'    => 'publish',
    'meta_key'       => 'view_count',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);

// 3. お知らせ (News)
$news_query = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => 5,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
]);

// 4. ランキングデータ (Ranking)
// ※ ji_get_ranking関数が存在しない場合のフォールバックを含める
$grant_ranking  = function_exists('ji_get_ranking') ? ji_get_ranking('grant', 7, 5) : [];
$column_ranking = function_exists('ji_get_ranking') ? ji_get_ranking('column', 7, 5) : [];

// 5. カウントデータ
$count_grants  = wp_count_posts('grant')->publish;
$count_columns = wp_count_posts('column')->publish;
$count_news    = wp_count_posts('post')->publish;

// ==========================================================================
// 2. 構造化データ (JSON-LD)
// ==========================================================================
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => '補助金・コラム総合情報',
    'description' => '最新の補助金情報と経営コラム、ランキング情報を集約しています。',
    'mainEntity' => [
        '@type' => 'ItemList',
        'itemListElement' => []
    ]
];
?>

<script type="application/ld+json">
<?php echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>

<section class="ui-section" id="main-info-hub" aria-label="総合情報セクション">
    <div class="ui-container">
        
        <header class="ui-header">
            <h2 class="ui-title">LATEST INTELLIGENCE</h2>
            <p class="ui-subtitle">補助金・コラム・最新情報ハブ</p>
        </header>

        <div class="ui-tabs-wrapper">
            <nav class="ui-tabs-nav" role="tablist" aria-label="情報カテゴリ">
                <button class="ui-tab-btn active" role="tab" id="tab-grants" aria-selected="true" aria-controls="panel-grants" data-tab="grants">
                    <span class="ui-tab-en">GRANTS</span>
                    <span class="ui-tab-ja">補助金</span>
                    <span class="ui-count"><?php echo number_format($count_grants); ?></span>
                </button>
                <button class="ui-tab-btn" role="tab" id="tab-columns" aria-selected="false" aria-controls="panel-columns" data-tab="columns">
                    <span class="ui-tab-en">COLUMNS</span>
                    <span class="ui-tab-ja">コラム</span>
                    <span class="ui-count"><?php echo number_format($count_columns); ?></span>
                </button>
                <button class="ui-tab-btn" role="tab" id="tab-news" aria-selected="false" aria-controls="panel-news" data-tab="news">
                    <span class="ui-tab-en">NEWS</span>
                    <span class="ui-tab-ja">お知らせ</span>
                    <span class="ui-count"><?php echo number_format($count_news); ?></span>
                </button>
            </nav>
        </div>

        <div class="ui-panels-container">

            <div class="ui-panel active" id="panel-grants" role="tabpanel" aria-labelledby="tab-grants">
                
                <?php if ($grants_featured->have_posts()) : ?>
                <section class="ui-sub-section">
                    <h3 class="ui-sub-title">
                        <span class="ui-icon-dot"></span>注目
                    </h3>
                    <div class="ui-grid-featured">
                        <?php while ($grants_featured->have_posts()) : $grants_featured->the_post(); 
                            $limit = get_post_meta(get_the_ID(), 'limit_amount', true);
                            $deadline = get_post_meta(get_the_ID(), 'deadline_date', true);
                        ?>
                        <article class="ui-card ui-card-lg">
                            <a href="<?php the_permalink(); ?>" class="ui-card-link">
                                <div class="ui-card-body">
                                    <div class="ui-tags">
                                        <span class="ui-tag ui-tag-black">注目</span>
                                        <?php if ($deadline && $deadline <= date('Y-m-d', strtotime('+2 weeks'))) : ?>
                                            <span class="ui-tag ui-tag-line">締切間近</span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="ui-card-title"><?php the_title(); ?></h4>
                                    <div class="ui-card-meta">
                                        <dl>
                                            <dt>上限額</dt>
                                            <dd><?php echo $limit ? esc_html($limit) : '-'; ?></dd>
                                        </dl>
                                        <dl>
                                            <dt>締切</dt>
                                            <dd><?php echo $deadline ? date('Y.m.d', strtotime($deadline)) : '随時'; ?></dd>
                                        </dl>
                                    </div>
                                </div>
                            </a>
                        </article>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </section>
                <?php endif; ?>

                <div class="ui-dual-columns">
                    <section class="ui-sub-section">
                        <h3 class="ui-sub-title"><span class="ui-icon-dot"></span>締切間近</h3>
                        <div class="ui-list-group">
                            <?php if ($grants_deadline->have_posts()) : while ($grants_deadline->have_posts()) : $grants_deadline->the_post(); 
                                $deadline = get_post_meta(get_the_ID(), 'deadline_date', true);
                                $days_left = (strtotime($deadline) - strtotime($today)) / (60 * 60 * 24);
                            ?>
                            <article class="ui-list-item">
                                <a href="<?php the_permalink(); ?>" class="ui-list-link">
                                    <div class="ui-list-content">
                                        <h4 class="ui-list-title"><?php the_title(); ?></h4>
                                        <time class="ui-list-date ui-text-alert">
                                            あと<?php echo max(0, ceil($days_left)); ?>日 (<?php echo date('m/d', strtotime($deadline)); ?>)
                                        </time>
                                    </div>
                                    <span class="ui-arrow">→</span>
                                </a>
                            </article>
                            <?php endwhile; else: ?>
                                <p class="ui-empty">現在、締切間近の案件はありません。</p>
                            <?php endif; wp_reset_postdata(); ?>
                        </div>
                        <div class="ui-more-container">
                            <a href="<?php echo home_url('/grants/?orderby=deadline'); ?>" class="ui-btn-more">View All Deadlines</a>
                        </div>
                    </section>

                    <section class="ui-sub-section">
                        <h3 class="ui-sub-title"><span class="ui-icon-dot"></span>新着</h3>
                        <div class="ui-list-group">
                            <?php if ($grants_new->have_posts()) : while ($grants_new->have_posts()) : $grants_new->the_post(); ?>
                            <article class="ui-list-item">
                                <a href="<?php the_permalink(); ?>" class="ui-list-link">
                                    <div class="ui-list-content">
                                        <h4 class="ui-list-title"><?php the_title(); ?></h4>
                                        <time class="ui-list-date"><?php echo get_the_date('Y.m.d'); ?></time>
                                    </div>
                                    <span class="ui-arrow">→</span>
                                </a>
                            </article>
                            <?php endwhile; endif; wp_reset_postdata(); ?>
                        </div>
                        <div class="ui-more-container">
                            <a href="<?php echo home_url('/grants/?orderby=date'); ?>" class="ui-btn-more">View All New Grants</a>
                        </div>
                    </section>
                </div>
            </div>

            <div class="ui-panel" id="panel-columns" role="tabpanel" aria-labelledby="tab-columns" hidden>
                
                <section class="ui-sub-section">
                    <h3 class="ui-sub-title"><span class="ui-icon-dot"></span>新着コラム</h3>
                    <div class="ui-grid-columns">
                        <?php if ($columns_new->have_posts()) : while ($columns_new->have_posts()) : $columns_new->the_post(); ?>
                        <article class="ui-card ui-card-md">
                            <a href="<?php the_permalink(); ?>" class="ui-card-link">
                                <div class="ui-card-img">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium', ['class' => 'ui-thumb-img']); ?>
                                    <?php else : ?>
                                        <div class="ui-no-img">No Image</div>
                                    <?php endif; ?>
                                </div>
                                <div class="ui-card-body">
                                    <time class="ui-card-date"><?php echo get_the_date('Y.m.d'); ?></time>
                                    <h4 class="ui-card-title"><?php the_title(); ?></h4>
                                </div>
                            </a>
                        </article>
                        <?php endwhile; endif; wp_reset_postdata(); ?>
                    </div>
                </section>

                <section class="ui-sub-section">
                    <h3 class="ui-sub-title"><span class="ui-icon-dot"></span>よく読まれている記事</h3>
                    <div class="ui-list-group">
                        <?php if ($columns_popular->have_posts()) : while ($columns_popular->have_posts()) : $columns_popular->the_post(); ?>
                        <article class="ui-list-item">
                            <a href="<?php the_permalink(); ?>" class="ui-list-link">
                                <div class="ui-list-content">
                                    <h4 class="ui-list-title"><?php the_title(); ?></h4>
                                    <div class="ui-meta-row">
                                        <time class="ui-list-date"><?php echo get_the_date('Y.m.d'); ?></time>
                                        <span class="ui-views">PV: <?php echo get_post_meta(get_the_ID(), 'view_count', true); ?></span>
                                    </div>
                                </div>
                                <span class="ui-arrow">→</span>
                            </a>
                        </article>
                        <?php endwhile; endif; wp_reset_postdata(); ?>
                    </div>
                    <div class="ui-more-container">
                        <a href="<?php echo home_url('/columns/'); ?>" class="ui-btn-more">View All Columns</a>
                    </div>
                </section>
            </div>

            <div class="ui-panel" id="panel-news" role="tabpanel" aria-labelledby="tab-news" hidden>
                <section class="ui-sub-section">
                    <div class="ui-list-group ui-list-simple">
                        <?php if ($news_query->have_posts()) : while ($news_query->have_posts()) : $news_query->the_post(); ?>
                        <article class="ui-list-item">
                            <a href="<?php the_permalink(); ?>" class="ui-list-link">
                                <div class="ui-list-content">
                                    <div class="ui-news-meta">
                                        <time class="ui-list-date"><?php echo get_the_date('Y.m.d'); ?></time>
                                        <?php 
                                            $cat = get_the_category();
                                            if ($cat) echo '<span class="ui-cat-label">' . esc_html($cat[0]->name) . '</span>';
                                        ?>
                                    </div>
                                    <h4 class="ui-list-title"><?php the_title(); ?></h4>
                                </div>
                            </a>
                        </article>
                        <?php endwhile; else: ?>
                            <p class="ui-empty">現在お知らせはありません。</p>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>
                    <div class="ui-more-container">
                        <a href="<?php echo home_url('/news/'); ?>" class="ui-btn-more">View All News</a>
                    </div>
                </section>
            </div>

        </div><aside class="ui-ranking-section" aria-label="ランキング">
            <div class="ui-ranking-grid">
                
                <div class="ui-ranking-col">
                    <header class="ui-ranking-header">
                        <h3 class="ui-ranking-title">GRANT RANKING</h3>
                        <p class="ui-ranking-sub">週間アクセスランキング（補助金）</p>
                    </header>
                    <?php if (!empty($grant_ranking)) : ?>
                    <ol class="ui-rank-list">
                        <?php foreach ($grant_ranking as $i => $item) : ?>
                        <li class="ui-rank-item">
                            <a href="<?php echo get_permalink($item->post_id); ?>" class="ui-rank-link">
                                <span class="ui-rank-num"><?php echo sprintf('%02d', $i + 1); ?></span>
                                <span class="ui-rank-text"><?php echo esc_html(get_the_title($item->post_id)); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <?php else: ?>
                        <p class="ui-empty-sm">集計中</p>
                    <?php endif; ?>
                </div>

                <div class="ui-ranking-col">
                    <header class="ui-ranking-header">
                        <h3 class="ui-ranking-title">COLUMN RANKING</h3>
                        <p class="ui-ranking-sub">週間アクセスランキング（コラム）</p>
                    </header>
                    <?php if (!empty($column_ranking)) : ?>
                    <ol class="ui-rank-list">
                        <?php foreach ($column_ranking as $i => $item) : ?>
                        <li class="ui-rank-item">
                            <a href="<?php echo get_permalink($item->post_id); ?>" class="ui-rank-link">
                                <span class="ui-rank-num"><?php echo sprintf('%02d', $i + 1); ?></span>
                                <span class="ui-rank-text"><?php echo esc_html(get_the_title($item->post_id)); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <?php else: ?>
                        <p class="ui-empty-sm">集計中</p>
                    <?php endif; ?>
                </div>

            </div>
        </aside>

    </div>
</section>

<style>
:root {
    /* Monochrome Palette */
    --ui-black: #111111;
    --ui-dark: #222222;
    --ui-gray: #666666;
    --ui-light: #e5e5e5;
    --ui-pale: #f9f9f9;
    --ui-white: #ffffff;
    
    /* Typography */
    --ui-font-main: 'Inter', 'Helvetica Neue', Arial, sans-serif;
    --ui-font-jp: 'Noto Sans JP', sans-serif;
    
    /* Spacing & sizing */
    --ui-space-container: 1200px;
    --ui-radius: 0px; /* Sharp edges for stylish look */
    --ui-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

/* Base Reset & Structure */
.ui-section {
    background: var(--ui-white);
    color: var(--ui-black);
    font-family: var(--ui-font-jp);
    padding: 100px 0;
    line-height: 1.6;
}

.ui-container {
    max-width: var(--ui-space-container);
    margin: 0 auto;
    padding: 0 24px;
}

a { text-decoration: none; color: inherit; }
ul, ol { list-style: none; padding: 0; margin: 0; }
h2, h3, h4, p { margin: 0; }

/* Header */
.ui-header {
    margin-bottom: 60px;
    text-align: center;
}

.ui-title {
    font-family: var(--ui-font-main);
    font-size: 14px;
    letter-spacing: 0.2em;
    font-weight: 700;
    margin-bottom: 16px;
}

.ui-subtitle {
    font-size: 32px;
    font-weight: 900;
    letter-spacing: -0.02em;
}

/* Tabs Navigation */
.ui-tabs-wrapper {
    border-bottom: 1px solid var(--ui-light);
    margin-bottom: 40px;
}

.ui-tabs-nav {
    display: flex;
    justify-content: center;
    gap: 0;
}

.ui-tab-btn {
    background: transparent;
    border: none;
    padding: 20px 40px;
    cursor: pointer;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: var(--ui-gray);
    transition: color 0.3s ease;
}

.ui-tab-btn:hover {
    color: var(--ui-black);
}

.ui-tab-btn.active {
    color: var(--ui-black);
}

.ui-tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background: var(--ui-black);
}

.ui-tab-en {
    font-family: var(--ui-font-main);
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.1em;
    margin-bottom: 4px;
}

.ui-tab-ja {
    font-size: 14px;
    font-weight: 700;
}

.ui-count {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 10px;
    color: var(--ui-gray);
    font-family: var(--ui-font-main);
}

/* Panels */
.ui-panel {
    display: none;
    animation: fadeIn 0.6s var(--ui-ease);
}

.ui-panel.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Sub Sections */
.ui-sub-section {
    margin-bottom: 60px;
}

.ui-sub-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ui-icon-dot {
    width: 6px;
    height: 6px;
    background: var(--ui-black);
    border-radius: 50%;
}

/* Layout Helpers */
.ui-dual-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.ui-grid-featured {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

.ui-grid-columns {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}

/* Cards (Universal) */
.ui-card {
    background: var(--ui-white);
    border: 1px solid var(--ui-light);
    transition: border-color 0.3s, transform 0.3s var(--ui-ease);
}

.ui-card:hover {
    border-color: var(--ui-black);
    transform: translateY(-4px);
}

.ui-card-link {
    display: block;
    height: 100%;
}

.ui-card-body {
    padding: 24px;
}

/* Featured Grants Card */
.ui-tags {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
}

.ui-tag {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 2px;
}

.ui-tag-black {
    background: var(--ui-black);
    color: var(--ui-white);
}

.ui-tag-line {
    border: 1px solid var(--ui-black);
    color: var(--ui-black);
}

.ui-card-title {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.5;
    margin-bottom: 16px;
    min-height: 3em; /* Align heights */
}

.ui-card-meta dl {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 8px;
    border-bottom: 1px dashed var(--ui-light);
    padding-bottom: 4px;
}

.ui-card-meta dt { color: var(--ui-gray); }
.ui-card-meta dd { font-weight: 700; font-family: var(--ui-font-main); }

/* Column Card */
.ui-card-img {
    aspect-ratio: 16/9;
    background: var(--ui-pale);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ui-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s var(--ui-ease);
}

.ui-card:hover .ui-thumb-img {
    transform: scale(1.05);
}

.ui-no-img {
    color: var(--ui-light);
    font-size: 12px;
    font-weight: 700;
}

.ui-card-date {
    font-size: 12px;
    color: var(--ui-gray);
    display: block;
    margin-bottom: 8px;
    font-family: var(--ui-font-main);
}

/* List Item Style */
.ui-list-group {
    border-top: 1px solid var(--ui-light);
}

.ui-list-item {
    border-bottom: 1px solid var(--ui-light);
    transition: background 0.2s;
}

.ui-list-item:hover {
    background: var(--ui-pale);
}

.ui-list-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 0;
    gap: 20px;
}

.ui-list-content {
    flex: 1;
}

.ui-list-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 6px;
}

.ui-list-date {
    font-size: 12px;
    color: var(--ui-gray);
    font-family: var(--ui-font-main);
}

.ui-text-alert {
    color: var(--ui-black);
    font-weight: 700;
}

.ui-cat-label {
    background: var(--ui-light);
    font-size: 10px;
    padding: 2px 6px;
    margin-left: 8px;
    font-weight: 700;
}

.ui-arrow {
    font-family: var(--ui-font-main);
    font-weight: 300;
    transform: translateX(0);
    transition: transform 0.3s;
}

.ui-list-link:hover .ui-arrow {
    transform: translateX(5px);
}

/* More Button */
.ui-more-container {
    margin-top: 24px;
    text-align: right;
}

.ui-btn-more {
    font-family: var(--ui-font-main);
    font-size: 13px;
    font-weight: 700;
    border-bottom: 1px solid var(--ui-black);
    padding-bottom: 2px;
    transition: opacity 0.3s;
}

.ui-btn-more:hover {
    opacity: 0.6;
}

/* Ranking Section */
.ui-ranking-section {
    margin-top: 80px;
    padding-top: 80px;
    border-top: 4px solid var(--ui-black);
}

.ui-ranking-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
}

.ui-ranking-header {
    margin-bottom: 30px;
}

.ui-ranking-title {
    font-family: var(--ui-font-main);
    font-size: 20px;
    font-weight: 900;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
}

.ui-ranking-sub {
    font-size: 12px;
    color: var(--ui-gray);
}

.ui-rank-item {
    border-bottom: 1px solid var(--ui-light);
}

.ui-rank-link {
    display: flex;
    align-items: center;
    padding: 16px 0;
    gap: 16px;
}

.ui-rank-num {
    font-family: var(--ui-font-main);
    font-size: 20px;
    font-weight: 900;
    color: var(--ui-light); /* Default inactive color */
    min-width: 30px;
    transition: color 0.3s;
}

.ui-rank-link:hover .ui-rank-num {
    color: var(--ui-black);
}

.ui-rank-item:nth-child(1) .ui-rank-num,
.ui-rank-item:nth-child(2) .ui-rank-num,
.ui-rank-item:nth-child(3) .ui-rank-num {
    color: var(--ui-black);
}

.ui-rank-text {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
    flex: 1;
}

/* ====================
   Mobile Responsive
   ==================== */
@media (max-width: 768px) {
    .ui-section { padding: 60px 0; }
    
    .ui-subtitle { font-size: 24px; }
    
    .ui-tabs-nav {
        justify-content: flex-start;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 0;
        border-bottom: none;
    }

    .ui-tabs-wrapper {
        border-bottom: 1px solid var(--ui-light);
        margin: 0 -24px 30px; /* Full width scroll */
        padding: 0 24px;
    }
    
    .ui-tab-btn {
        padding: 16px 20px;
        flex-shrink: 0;
    }

    /* Stack layout for mobile */
    .ui-dual-columns,
    .ui-ranking-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }

    .ui-grid-featured,
    .ui-grid-columns {
        /* Horizontal Scroll on Mobile */
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        gap: 16px;
        padding-bottom: 16px; /* Scrollbar space */
        margin-right: -24px; /* Bleed right */
    }
    
    .ui-grid-featured .ui-card,
    .ui-grid-columns .ui-card {
        min-width: 280px;
        scroll-snap-align: start;
    }
    
    .ui-thumb-img {
        height: 160px; /* Fixed height for consistency */
    }

    /* Adjust Ranking for Mobile */
    .ui-ranking-section {
        margin-top: 60px;
        padding-top: 40px;
        border-top: 1px solid var(--ui-black); /* Thinner on mobile */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const tabs = document.querySelectorAll('.ui-tab-btn');
    const panels = document.querySelectorAll('.ui-panel');

    // Tab Switching Logic
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.tab;

            // 1. Reset Tabs
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });

            // 2. Activate Clicked Tab
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');

            // 3. Reset Panels
            panels.forEach(p => {
                p.classList.remove('active');
                p.hidden = true;
            });

            // 4. Activate Target Panel
            const activePanel = document.getElementById(`panel-${target}`);
            if(activePanel) {
                activePanel.classList.add('active');
                activePanel.hidden = false;
            }
        });
    });

    // Intersection Observer for Lazy Loading / Animations
    // 要素が見えたらふわりと表示する演出（オプション）
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.ui-card, .ui-list-item').forEach(el => {
        // 初期スタイル設定（JSが有効な場合のみ）
        el.style.opacity = 0;
        el.style.transform = 'translateY(10px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});
</script>