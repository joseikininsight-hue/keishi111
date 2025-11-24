<?php
/**
 * Template Part: Grant Card - Text Only Compact Style
 * 補助金カード（テキストのみコンパクト版）
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Grant_System
 * @version 2.1.0 - Text Only Compact Style
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// 現在の投稿情報を取得
$post_id = get_the_ID();
$deadline = get_post_meta($post_id, 'deadline_date', true);
$amount = get_post_meta($post_id, 'grant_amount_max', true);
$categories = get_the_terms($post_id, 'grant_category');
$prefecture = get_the_terms($post_id, 'grant_prefecture');
$is_featured = get_post_meta($post_id, 'is_featured', true);
$view_count = get_post_meta($post_id, 'view_count', true);

// 締切日までの日数計算
$days_left = null;
$is_urgent = false;
if ($deadline) {
    $deadline_date = new DateTime($deadline);
    $now = new DateTime();
    $diff = $now->diff($deadline_date);
    if ($diff->invert == 0) {
        $days_left = $diff->days;
        $is_urgent = ($days_left <= 14);
    }
}

// 新着判定（7日以内）
$is_new = (strtotime(get_the_date('Y-m-d')) > strtotime('-7 days'));

// 抜粋
$excerpt = get_the_excerpt();
if (empty($excerpt)) {
    $excerpt = wp_trim_words(strip_tags(get_the_content()), 30, '...');
}
?>

<article class="grant-card-text-compact" itemscope itemtype="https://schema.org/GovernmentService">
    <a href="<?php the_permalink(); ?>" class="grant-card-link" itemprop="url">
        
        <!-- ヘッダー部分 -->
        <div class="grant-card-header">
            
            <!-- バッジエリア -->
            <div class="grant-badges">
                <?php if ($is_featured) : ?>
                <span class="badge badge-featured">注目</span>
                <?php endif; ?>
                
                <?php if ($is_urgent && $days_left !== null) : ?>
                <span class="badge badge-urgent">あと<?php echo $days_left; ?>日</span>
                <?php endif; ?>
                
                <?php if ($is_new) : ?>
                <span class="badge badge-new">NEW</span>
                <?php endif; ?>
            </div>
            
            <!-- 投稿日 -->
            <time class="grant-date" datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished">
                <?php echo get_the_date('Y/m/d'); ?>
            </time>
        </div>
        
        <!-- カテゴリ・都道府県 -->
        <div class="grant-meta-tags">
            <?php if ($categories && !is_wp_error($categories)) : ?>
                <span class="meta-tag meta-category" itemprop="serviceType">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                    <?php echo esc_html($categories[0]->name); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($prefecture && !is_wp_error($prefecture)) : ?>
                <span class="meta-tag meta-location" itemprop="areaServed">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <?php echo esc_html($prefecture[0]->name); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- タイトル -->
        <h3 class="grant-title" itemprop="name">
            <?php the_title(); ?>
        </h3>
        
        <!-- 抜粋 -->
        <p class="grant-excerpt" itemprop="description">
            <?php echo esc_html($excerpt); ?>
        </p>
        
        <!-- フッターメタ情報 -->
        <div class="grant-footer-meta">
            <?php if ($amount) : ?>
            <span class="meta-item meta-amount">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                最大<?php echo number_format($amount); ?>万円
            </span>
            <?php endif; ?>
            
            <?php if ($deadline) : ?>
            <span class="meta-item meta-deadline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                締切: <?php echo date('Y/m/d', strtotime($deadline)); ?>
            </span>
            <?php endif; ?>
            
            <?php if ($view_count && $view_count > 0) : ?>
            <span class="meta-item meta-views">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <?php echo number_format($view_count); ?>
            </span>
            <?php endif; ?>
        </div>
        
    </a>
</article>

<style>
/* ========================================
   Text Only Compact Grant Card
   テキストのみコンパクト補助金カード
   ======================================== */

.grant-card-text-compact {
    background: #ffffff;
    border-bottom: 1px solid #e5e5e5;
    transition: background-color 0.2s ease;
}

.grant-card-text-compact:hover {
    background-color: #f8f8f8;
}

.grant-card-text-compact:last-child {
    border-bottom: none;
}

.grant-card-link {
    display: block;
    padding: 20px 24px;
    text-decoration: none;
    color: inherit;
}

/* ヘッダー部分 */
.grant-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 12px;
}

/* バッジエリア */
.grant-badges {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.grant-card-text-compact .badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #ffffff;
    text-transform: uppercase;
    line-height: 1;
}

.grant-card-text-compact .badge-featured {
    background: #ff6600;
}

.grant-card-text-compact .badge-urgent {
    background: #ff0033;
    animation: pulse-badge 2s infinite;
}

@keyframes pulse-badge {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.grant-card-text-compact .badge-new {
    background: #00cc00;
}

/* 投稿日 */
.grant-date {
    font-size: 13px;
    color: #999999;
    font-weight: 600;
    white-space: nowrap;
}

/* カテゴリ・都道府県 */
.grant-meta-tags {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.grant-card-text-compact .meta-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
}

.grant-card-text-compact .meta-category {
    color: #000000;
    background: #ffeb3b;
    padding: 4px 10px;
    border-radius: 4px;
}

.grant-card-text-compact .meta-location {
    color: #666666;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 4px;
}

/* タイトル */
.grant-title {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.5;
    color: #000000;
    margin: 0 0 10px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}

.grant-card-text-compact:hover .grant-title {
    color: #0066cc;
}

/* 抜粋 */
.grant-excerpt {
    font-size: 14px;
    line-height: 1.6;
    color: #666666;
    margin: 0 0 12px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* フッターメタ情報 */
.grant-footer-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.grant-card-text-compact .meta-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
}

.grant-card-text-compact .meta-amount {
    color: #0078ff;
}

.grant-card-text-compact .meta-deadline {
    color: #ff6600;
}

.grant-card-text-compact .meta-views {
    color: #9c27b0;
}

/* レスポンシブ */
@media (max-width: 640px) {
    .grant-card-link {
        padding: 16px 20px;
    }
    
    .grant-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .grant-title {
        font-size: 15px;
    }
    
    .grant-excerpt {
        font-size: 13px;
    }
    
    .grant-footer-meta {
        gap: 12px;
        font-size: 12px;
    }
    
    .grant-card-text-compact .meta-item {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .grant-card-link {
        padding: 14px 16px;
    }
    
    .grant-title {
        font-size: 14px;
    }
    
    .grant-excerpt {
        font-size: 12px;
        -webkit-line-clamp: 3;
    }
    
    .grant-footer-meta {
        gap: 10px;
    }
}

/* プリント対応 */
@media print {
    .grant-card-text-compact {
        page-break-inside: avoid;
        border-bottom: 1px solid #cccccc;
    }
    
    .grant-badges {
        display: none;
    }
    
    .grant-card-text-compact:hover {
        background-color: transparent;
    }
}

/* アクセシビリティ */
.grant-card-link:focus-visible {
    outline: 3px solid #ffeb3b;
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    .grant-card-text-compact,
    .grant-title,
    .badge-urgent {
        transition: none !important;
        animation: none !important;
    }
}


</style>
