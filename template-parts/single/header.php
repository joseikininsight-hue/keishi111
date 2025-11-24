<?php
/**
 * Template Part: Single Grant Header
 * 補助金詳細ページ - ヘッダー部分（閲覧数・お気に入り機能含む）
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// データを$argsから取得
$post_id = isset($args['post_id']) ? $args['post_id'] : get_the_ID();
$grant_data = isset($args['grant_data']) ? $args['grant_data'] : array();
$status_data = isset($args['status_data']) ? $args['status_data'] : array('label' => '募集中', 'class' => 'open');
$taxonomies = isset($args['taxonomies']) ? $args['taxonomies'] : array();
$formatted_amount = isset($args['formatted_amount']) ? $args['formatted_amount'] : '';
$deadline_info = isset($args['deadline_info']) ? $args['deadline_info'] : '';
$deadline_class = isset($args['deadline_class']) ? $args['deadline_class'] : '';

// お気に入り状態の取得
$user_favorites = function_exists('gi_get_user_favorites') ? gi_get_user_favorites() : array();
$is_favorite = in_array($post_id, $user_favorites);
?>

<!-- パンくずリスト -->
<div class="gus-breadcrumb">
    <a href="<?php echo home_url('/'); ?>">ホーム</a>
    <span class="separator">›</span>
    <a href="<?php echo get_post_type_archive_link('grant'); ?>">補助金一覧</a>
    <?php if (!empty($taxonomies['categories'])): ?>
        <span class="separator">›</span>
        <a href="<?php echo get_term_link($taxonomies['categories'][0]); ?>">
            <?php echo esc_html($taxonomies['categories'][0]->name); ?>
        </a>
    <?php endif; ?>
    <span class="separator">›</span>
    <span class="current"><?php echo esc_html(get_the_title()); ?></span>
</div>

<!-- ステータスバー（募集中/終了/募集予定） -->
<div class="gus-status-bar status-<?php echo esc_attr($status_data['class']); ?>">
    <div class="gus-status-badge">
        <?php echo esc_html($status_data['label']); ?>
    </div>
    
    <!-- 閲覧数・お気に入り・シェア -->
    <div class="gus-meta-actions">
        <!-- 閲覧数 -->
        <div class="gus-views-count" title="閲覧数">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <span><?php echo number_format($grant_data['views_count']); ?></span>
        </div>
        
        <!-- お気に入りボタン -->
        <button class="gus-favorite-btn <?php echo $is_favorite ? 'is-favorite' : ''; ?>" 
                data-post-id="<?php echo $post_id; ?>"
                title="<?php echo $is_favorite ? 'お気に入りから削除' : 'お気に入りに追加'; ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="<?php echo $is_favorite ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <span><?php echo $is_favorite ? 'お気に入り済み' : 'お気に入り'; ?></span>
        </button>
        
        <!-- シェアボタン -->
        <button class="gus-share-btn" title="シェア">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
            </svg>
            <span>シェア</span>
        </button>
    </div>
</div>

<!-- タイトル -->
<h1 class="gus-title">
    <?php the_title(); ?>
</h1>

<!-- メタ情報（更新日・読了時間） -->
<div class="gus-meta-info">
    <div class="gus-meta-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        <span>更新日: <?php echo get_the_modified_date('Y年n月j日'); ?></span>
    </div>
    
    <?php if (isset($args['reading_time']) && $args['reading_time'] > 0): ?>
    <div class="gus-meta-item">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
        </svg>
        <span>読了時間: 約<?php echo $args['reading_time']; ?>分</span>
    </div>
    <?php endif; ?>
</div>

<!-- ヒーローグリッド（重要情報） -->
<div class="gus-hero-grid">
    <div class="gus-hero-item gus-hero-amount">
        <div class="gus-hero-label">最大補助額</div>
        <div class="gus-hero-value"><?php echo esc_html($formatted_amount); ?></div>
    </div>
    
    <?php if (!empty($grant_data['subsidy_rate'])): ?>
    <div class="gus-hero-item">
        <div class="gus-hero-label">補助率</div>
        <div class="gus-hero-value"><?php echo esc_html($grant_data['subsidy_rate']); ?></div>
    </div>
    <?php endif; ?>
    
    <?php if ($deadline_info): ?>
    <div class="gus-hero-item gus-hero-deadline <?php echo $deadline_class; ?>">
        <div class="gus-hero-label">申請締切</div>
        <div class="gus-hero-value"><?php echo esc_html($deadline_info); ?></div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($grant_data['organization'])): ?>
    <div class="gus-hero-item">
        <div class="gus-hero-label">実施機関</div>
        <div class="gus-hero-value"><?php echo esc_html($grant_data['organization']); ?></div>
    </div>
    <?php endif; ?>
</div>

<!-- CTAボタン -->
<div class="gus-cta-buttons">
    <?php if (!empty($grant_data['official_url'])): ?>
    <a href="<?php echo esc_url($grant_data['official_url']); ?>" 
       class="gus-btn gus-btn-primary" 
       target="_blank" 
       rel="noopener">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
            <polyline points="15 3 21 3 21 9"></polyline>
            <line x1="10" y1="14" x2="21" y2="3"></line>
        </svg>
        公式サイトで詳細を見る
    </a>
    <?php endif; ?>
    
    <button class="gus-btn gus-btn-secondary gus-print-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        印刷する
    </button>
</div>
