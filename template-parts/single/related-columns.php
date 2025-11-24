<?php
/**
 * Template Part: Related Columns
 * 関連コラム（解説記事）表示
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
$taxonomies = isset($args['taxonomies']) ? $args['taxonomies'] : array();

// 関連コラムを取得（inc/column-system.php の gi_get_columns_by_grant 使用）
$related_columns = array();
if (function_exists('gi_get_columns_by_grant')) {
    $related_columns = gi_get_columns_by_grant($post_id, 6);
}

// コラムがない場合は表示しない
if (empty($related_columns)) {
    return;
}
?>

<section class="gus-related-columns">
    <div class="gus-section-header">
        <h2 class="gus-section-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
            この補助金の関連解説記事
        </h2>
        <p class="gus-section-desc">補助金の活用方法や申請のコツをわかりやすく解説しています</p>
    </div>
    
    <div class="gus-columns-grid">
        <?php foreach ($related_columns as $column): ?>
        <article class="gus-column-card">
            <?php if (!empty($column['thumbnail'])): ?>
            <div class="gus-column-thumbnail">
                <a href="<?php echo esc_url($column['permalink']); ?>">
                    <img src="<?php echo esc_url($column['thumbnail']); ?>" 
                         alt="<?php echo esc_attr($column['title']); ?>" 
                         loading="lazy">
                </a>
            </div>
            <?php endif; ?>
            
            <div class="gus-column-content">
                <?php if (!empty($column['category'])): ?>
                <div class="gus-column-category">
                    <?php echo esc_html($column['category']); ?>
                </div>
                <?php endif; ?>
                
                <h3 class="gus-column-title">
                    <a href="<?php echo esc_url($column['permalink']); ?>">
                        <?php echo esc_html($column['title']); ?>
                    </a>
                </h3>
                
                <?php if (!empty($column['excerpt'])): ?>
                <p class="gus-column-excerpt">
                    <?php echo esc_html(wp_trim_words($column['excerpt'], 30, '...')); ?>
                </p>
                <?php endif; ?>
                
                <div class="gus-column-meta">
                    <?php if (!empty($column['date'])): ?>
                    <span class="gus-column-date">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <?php echo date('Y.m.d', strtotime($column['date'])); ?>
                    </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($column['reading_time'])): ?>
                    <span class="gus-column-reading-time">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <?php echo $column['reading_time']; ?>分
                    </span>
                    <?php endif; ?>
                </div>
                
                <a href="<?php echo esc_url($column['permalink']); ?>" class="gus-column-link">
                    記事を読む
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    
    <div class="gus-columns-footer">
        <a href="<?php echo get_post_type_archive_link('column'); ?>" class="gus-btn gus-btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
            すべての解説記事を見る
        </a>
    </div>
</section>
