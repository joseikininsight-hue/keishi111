<?php
/**
 * Template Part: Column Card - Yahoo! News Compact Style
 * コラム記事カード（Yahoo!ニュース風コンパクト）
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 2.0.0 - Yahoo! News Compact Style
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// 現在の投稿情報を取得
$post_id = get_the_ID();
$read_time = get_field('estimated_read_time', $post_id);
$view_count = get_field('view_count', $post_id);
if (empty($view_count)) {
    $view_count = get_post_meta($post_id, 'view_count', true);
}
$difficulty = get_field('difficulty_level', $post_id);
$categories = get_the_terms($post_id, 'column_category');
$is_new = (strtotime(get_the_date('Y-m-d')) > strtotime('-7 days'));

// サムネイル
$thumbnail_url = get_the_post_thumbnail_url($post_id, 'thumbnail');

// 抜粋
$excerpt = get_the_excerpt();
if (empty($excerpt)) {
    $excerpt = wp_trim_words(strip_tags(get_the_content()), 20, '...');
}
?>

<article class="column-card-compact">
    <a href="<?php the_permalink(); ?>" class="card-link-compact">
        <div class="card-inner">
            
            <!-- 左側：サムネイル -->
            <?php if ($thumbnail_url) : ?>
            <div class="card-thumb">
                <img src="<?php echo esc_url($thumbnail_url); ?>" 
                     alt="<?php the_title_attribute(); ?>"
                     loading="lazy">
                
                <!-- バッジ -->
                <?php if ($is_new) : ?>
                <span class="badge badge-new">NEW</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- 右側：テキスト -->
            <div class="card-text">
                
                <!-- カテゴリ・難易度 -->
                <div class="card-meta">
                    <?php if ($categories && !is_wp_error($categories)) : ?>
                        <?php $cat = $categories[0]; ?>
                        <span class="meta-category"><?php echo esc_html($cat->name); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($difficulty) : ?>
                        <?php
                        $difficulty_labels = array(
                            'beginner' => '初級',
                            'intermediate' => '中級',
                            'advanced' => '上級',
                        );
                        $label = isset($difficulty_labels[$difficulty]) ? $difficulty_labels[$difficulty] : $difficulty;
                        ?>
                        <span class="meta-difficulty"><?php echo esc_html($label); ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- タイトル -->
                <h3 class="card-title-compact">
                    <?php the_title(); ?>
                </h3>
                
                <!-- 抜粋 -->
                <p class="card-excerpt-compact">
                    <?php echo esc_html($excerpt); ?>
                </p>
                
                <!-- 下部メタ情報 -->
                <div class="card-footer-meta">
                    <span class="meta-date">
                        <i class="fas fa-calendar"></i>
                        <?php echo get_the_date('Y/m/d'); ?>
                    </span>
                    
                    <?php if ($read_time) : ?>
                    <span class="meta-time">
                        <i class="fas fa-clock"></i>
                        <?php echo esc_html($read_time); ?>分
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($view_count && $view_count > 0) : ?>
                    <span class="meta-views">
                        <i class="fas fa-eye"></i>
                        <?php echo number_format($view_count); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </a>
</article>

<style>
/* ========================================
   Yahoo! News Compact Style Column Card
   Yahoo!ニュース風コンパクトコラムカード
   ======================================== */

.column-card-compact {
    background: #ffffff;
    border-bottom: 1px solid #e5e5e5;
    transition: background-color 0.2s ease;
}

.column-card-compact:hover {
    background-color: #f8f8f8;
}

.card-link-compact {
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 16px;
}

.card-inner {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

/* 左側：サムネイル */
.card-thumb {
    position: relative;
    flex-shrink: 0;
    width: 120px;
    height: 80px;
    border-radius: 4px;
    overflow: hidden;
    background: #f5f5f5;
}

.card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.column-card-compact:hover .card-thumb img {
    transform: scale(1.1);
}

/* バッジ */
.card-thumb .badge {
    position: absolute;
    top: 4px;
    left: 4px;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 2px;
    line-height: 1;
}

.badge-new {
    background: #2196f3;
    color: #ffffff;
}

/* 右側：テキスト */
.card-text {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* メタ情報（上部） */
.card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 12px;
}

.meta-category {
    padding: 3px 8px;
    background: #000000;
    color: #ffeb3b;
    border-radius: 2px;
    font-weight: 600;
}

.meta-difficulty {
    padding: 3px 8px;
    background: #f5f5f5;
    color: #666666;
    border-radius: 2px;
    font-weight: 500;
}

/* タイトル */
.card-title-compact {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.4;
    color: #000000;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s ease;
}

.column-card-compact:hover .card-title-compact {
    color: #0066cc;
}

/* 抜粋 */
.card-excerpt-compact {
    font-size: 13px;
    line-height: 1.5;
    color: #666666;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 下部メタ情報 */
.card-footer-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: #999999;
}

.card-footer-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.card-footer-meta i {
    font-size: 11px;
}

.meta-date i {
    color: #2196f3;
}

.meta-time i {
    color: #ff9800;
}

.meta-views i {
    color: #9c27b0;
}

/* レスポンシブ */
@media (max-width: 640px) {
    .card-inner {
        gap: 12px;
    }
    
    .card-thumb {
        width: 100px;
        height: 67px;
    }
    
    .card-title-compact {
        font-size: 15px;
    }
    
    .card-excerpt-compact {
        font-size: 12px;
    }
    
    .card-footer-meta {
        font-size: 11px;
        gap: 8px;
    }
}
</style>
