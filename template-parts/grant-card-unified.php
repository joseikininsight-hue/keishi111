<?php
/**
 * Grant Card List Portal - Yahoo! JAPAN Style v4.1 STABLE
 * template-parts/grant-card-list-portal.php
 * 
 * 表示崩れ完全対策版
 * - CLS（Cumulative Layout Shift）ゼロ
 * - 初期レンダリング最適化
 * - アニメーション遅延なし
 * 
 * @package Grant_Insight_Portal
 * @version 4.1.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

global $post;
$post_id = get_the_ID();
if (!$post_id) {
    return;
}

// 基本データ
$title = get_the_title($post_id);
$permalink = get_permalink($post_id);
$excerpt = get_the_excerpt($post_id);

if (empty($excerpt)) {
    $content = get_the_content($post_id);
    $excerpt = wp_trim_words(strip_tags($content), 30, '...');
}

// ACFフィールド（必要最小限）
$organization = get_field('organization', $post_id) ?: '';
$organization_type = get_field('organization_type', $post_id) ?: 'national';
$deadline_date = get_field('deadline_date', $post_id) ?: '';
$application_status = get_field('application_status', $post_id) ?: 'open';
$adoption_rate = floatval(get_field('adoption_rate', $post_id));
$subsidy_rate_detailed = get_field('subsidy_rate_detailed', $post_id) ?: '';
$is_featured = get_field('is_featured', $post_id) ?: false;
$ai_summary = get_field('ai_summary', $post_id) ?: get_post_meta($post_id, 'ai_summary', true);

// タクソノミー
$categories = get_the_terms($post_id, 'grant_category');
$prefectures = get_the_terms($post_id, 'grant_prefecture');

$main_category = '';
if ($categories && !is_wp_error($categories)) {
    $main_category = $categories[0]->name;
}

// 地域表示（シンプル化）
$region_display = '全国';
if ($prefectures && !is_wp_error($prefectures)) {
    $count = count($prefectures);
    if ($count < 20) {
        if ($count === 1) {
            $region_display = $prefectures[0]->name;
        } else {
            $region_display = $count . '都道府県';
        }
    }
}

// ステータス
$status_config = array(
    'open' => array('label' => '募集中', 'class' => 'status-open'),
    'upcoming' => array('label' => '近日', 'class' => 'status-upcoming'),
    'closed' => array('label' => '終了', 'class' => 'status-closed'),
);
$status_data = $status_config[$application_status] ?? $status_config['open'];

// 締切情報（明確化：締切日を常に表示）
$deadline_display = '';
$deadline_urgency = ''; // 緊急度表示（残り〇日）
$deadline_class = '';
$is_urgent = false;

if ($deadline_date) {
    $deadline_timestamp = strtotime($deadline_date);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
        
        // 実際の締切日を常に表示（Y/m/d形式）
        $deadline_display = date('Y/m/d', $deadline_timestamp) . '締切';
        
        if ($days_remaining <= 0) {
            $deadline_display = date('Y/m/d', $deadline_timestamp) . '締切済';
            $deadline_class = 'deadline-expired';
        } elseif ($days_remaining <= 3) {
            $deadline_urgency = 'あと' . $days_remaining . '日';
            $deadline_class = 'deadline-critical';
            $is_urgent = true;
        } elseif ($days_remaining <= 7) {
            $deadline_urgency = 'あと' . $days_remaining . '日';
            $deadline_class = 'deadline-warning';
            $is_urgent = true;
        } elseif ($days_remaining <= 14) {
            $deadline_urgency = 'あと' . $days_remaining . '日';
            $deadline_class = 'deadline-soon';
        } else {
            $deadline_class = 'deadline-normal';
        }
    }
}

// 補助率表示（シンプル化）
$subsidy_display = '';
if ($subsidy_rate_detailed) {
    if (strpos($subsidy_rate_detailed, '2/3') !== false) {
        $subsidy_display = '2/3補助';
    } elseif (strpos($subsidy_rate_detailed, '1/2') !== false) {
        $subsidy_display = '1/2補助';
    } elseif (strpos($subsidy_rate_detailed, '3/4') !== false) {
        $subsidy_display = '3/4補助';
    } elseif (strpos($subsidy_rate_detailed, '100') !== false || strpos($subsidy_rate_detailed, '全額') !== false) {
        $subsidy_display = '全額補助';
    }
}

// キャッチコピー（厳選）
$catch_tags = array();

if ($is_featured) {
    $catch_tags[] = array('text' => 'おすすめ', 'type' => 'featured');
}

if ($is_urgent) {
    $catch_tags[] = array('text' => '締切間近', 'type' => 'urgent');
}

if ($adoption_rate >= 70) {
    $catch_tags[] = array('text' => '高採択率', 'type' => 'success');
}

// 最大2つまで
$catch_tags = array_slice($catch_tags, 0, 2);
?>

<style>
/* ============================================
   🎯 Yahoo! JAPAN Perfect Card v4.1 STABLE
   表示崩れ完全対策版
============================================ */

.grant-card-perfect {
    /* Yahoo!カラー */
    --y-red: #FF0033;
    --y-black: #000000;
    --y-gray-dark: #333333;
    --y-gray: #666666;
    --y-gray-light: #999999;
    --y-bg: #FFFFFF;
    --y-border: #E5E5E5;
    --y-hover: #F8F8F8;
    --y-blue: #0078FF;
    --y-success: #00C851;
    --y-warning: #FF8800;
    
    color-scheme: light !important;
}

@media (prefers-color-scheme: dark) {
    .grant-card-perfect,
    .grant-card-perfect * {
        color-scheme: light !important;
    }
}

/* ===== CLS対策：固定サイズ指定（超コンパクト版） ===== */
.grant-card-perfect {
    background: var(--y-bg);
    border: 1px solid var(--y-border);
    border-radius: 0;
    transition: box-shadow 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
    cursor: pointer;
    display: block;
    position: relative;
    /* 最小高さをさらに縮小して超コンパクト化 */
    min-height: 120px;
    /* transformではなくbox-shadowとborder-colorのみ変更 */
    will-change: box-shadow, border-color;
    /* 横スクロール完全防止 */
    overflow-x: hidden;
    max-width: 100%;
    box-sizing: border-box;
}

.grant-card-perfect:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    border-color: var(--y-gray-light);
    background: var(--y-hover);
}

.card-link {
    text-decoration: none;
    color: inherit;
    display: block;
    padding: 10px 12px;
    /* パディングをさらに縮小 */
    overflow-x: hidden;
    max-width: 100%;
    box-sizing: border-box;
}

/* ===== ヘッダー：固定高さ ===== */
.card-header-perfect {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    /* 固定高さを縮小 */
    min-height: 20px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
    /* 固定高さ */
    height: 20px;
    display: inline-flex;
    align-items: center;
}

.status-open {
    background: #E8F5E9;
    color: #2E7D32;
}

.status-upcoming {
    background: #E3F2FD;
    color: #1565C0;
}

.status-closed {
    background: #F5F5F5;
    color: #757575;
}

.catch-tags {
    display: flex;
    gap: 4px;
}

.catch-tag {
    padding: 3px 8px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    line-height: 1;
    /* 固定高さ */
    height: 18px;
    display: inline-flex;
    align-items: center;
}

.catch-featured {
    background: linear-gradient(135deg, #FFD700, #FF8C00);
    color: #000;
}

.catch-urgent {
    background: var(--y-red);
    color: #FFF;
    /* アニメーションは0.2秒遅延させて初期レンダリングを安定化 */
    animation: pulse 1.5s ease infinite 0.2s;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.85; }
}

.catch-success {
    background: var(--y-success);
    color: #FFF;
}

.category-tag {
    font-size: 11px;
    color: var(--y-gray);
    font-weight: 600;
    line-height: 1;
}

/* ===== タイトル：固定行数（超コンパクト版） ===== */
.card-title-perfect {
    font-size: 15px;
    font-weight: 700;
    line-height: 1.35;
    color: var(--y-black);
    margin: 0 0 4px 0;
    /* 固定2行表示 */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    /* 固定高さ（15px * 1.35 * 2行 = 40.5px） */
    min-height: 41px;
    max-height: 41px;
    transition: color 0.15s ease;
}

.grant-card-perfect:hover .card-title-perfect {
    color: var(--y-blue);
}

/* ===== 要約：固定行数（超コンパクト版） ===== */
.card-summary-perfect {
    font-size: 11px;
    line-height: 1.5;
    color: var(--y-gray-dark);
    margin: 0 0 6px 0;
    /* 固定2行表示 */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    /* 固定高さ（11px * 1.5 * 2行 = 33px） */
    min-height: 33px;
    max-height: 33px;
}

/* ===== メタ情報：固定高さ（超コンパクト版） ===== */
.card-meta-perfect {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 6px;
    /* 固定最小高さを縮小 */
    min-height: 24px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: var(--y-gray-dark);
    padding: 4px 8px;
    background: var(--y-hover);
    border-radius: 3px;
    font-weight: 600;
    /* 固定高さを縮小 */
    height: 24px;
    line-height: 1;
}

.meta-icon {
    width: 13px;
    height: 13px;
    stroke: var(--y-gray);
    stroke-width: 2;
    flex-shrink: 0;
}

.meta-highlight {
    color: #0056CC; /* Darker blue for better contrast (changed from #0078FF) */
    font-weight: 700;
}

/* ===== フッター：固定高さ（超コンパクト版） ===== */
.card-footer-perfect {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 8px;
    border-top: 1px solid var(--y-border);
    /* 固定高さを縮小 */
    min-height: 40px;
}

.footer-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* 締切情報コンテナ */
.deadline-info {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.deadline-badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    border: 2px solid;
    /* 固定高さ */
    height: 32px;
    display: inline-flex;
    align-items: center;
    line-height: 1;
}

/* 緊急度バッジ（あと〇日） */
.deadline-urgency {
    font-size: 11px;
    font-weight: 800;
    padding: 5px 10px;
    height: 28px;
}

/* 締切日バッジ */
.deadline-date {
    font-size: 12px;
    font-weight: 600;
}

/* 緊急度の色（あと〇日） */
.deadline-urgency.deadline-critical {
    background: var(--y-red);
    color: #FFF;
    border-color: var(--y-red);
    /* アニメーションは0.3秒遅延 */
    animation: pulse 1.5s ease infinite 0.3s;
}

/* 締切日の色 */
.deadline-date.deadline-critical {
    background: #FFEBEE;
    color: var(--y-red);
    border-color: var(--y-red);
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-1px); }
    75% { transform: translateX(1px); }
}

.deadline-urgency.deadline-warning {
    background: var(--y-warning);
    color: #FFF;
    border-color: var(--y-warning);
}

.deadline-date.deadline-warning {
    background: #FFF3E0;
    color: var(--y-warning);
    border-color: var(--y-warning);
}

.deadline-urgency.deadline-soon {
    background: #E65100; /* Darker orange for better contrast */
    color: #FFF;
    border-color: #E65100;
}

.deadline-date.deadline-soon {
    background: #FFF8E1;
    color: #E65100; /* Darker orange for better contrast (changed from #F57F17) */
    border-color: #F57F17;
}

.deadline-date.deadline-normal {
    background: var(--y-hover);
    color: var(--y-gray-dark);
    border-color: var(--y-border);
}

.deadline-date.deadline-expired {
    background: #F5F5F5;
    color: #9E9E9E;
    border-color: #E0E0E0;
}

.adoption-badge {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    background: #C8E6C9; /* Darker background for better contrast */
    border-radius: 4px;
    font-size: 12px;
    font-weight: 700;
    color: #1B5E20; /* Darker green for better contrast (changed from #00C851) */
    /* 固定高さ */
    height: 32px;
    line-height: 1;
}

.adoption-icon {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    stroke-width: 2;
    flex-shrink: 0;
}

.footer-right {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    color: var(--y-gray);
    line-height: 1;
}

.org-name {
    font-weight: 600;
    color: var(--y-gray-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

.region-name {
    padding: 3px 8px;
    background: var(--y-hover);
    border-radius: 3px;
    font-weight: 600;
    color: var(--y-gray-dark);
    white-space: nowrap;
    /* 固定高さ */
    height: 22px;
    display: inline-flex;
    align-items: center;
    line-height: 1;
}

/* ===== レスポンシブ ===== */
@media (max-width: 768px) {
    .grant-card-perfect {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    .card-link {
        padding: 14px;
    }
    
    .card-title-perfect {
        font-size: 16px;
        /* 16px * 1.5 * 2 = 48px */
        min-height: 48px;
        max-height: 48px;
    }
    
    .card-summary-perfect {
        font-size: 12px;
        line-height: 1.7;
        /* 12px * 1.7 * 2 = 40.8px */
        min-height: 41px;
        max-height: 41px;
    }
    
    .card-footer-perfect {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        min-height: auto;
    }
    
    .footer-left {
        width: 100%;
        justify-content: space-between;
    }
    
    .footer-right {
        width: 100%;
    }
    
    .org-name {
        max-width: 100%;
    }
}

@media (max-width: 480px) {
    .grant-card-perfect {
        min-height: 120px;
        /* 横スクロール完全防止 */
        overflow-x: hidden !important;
        max-width: 100% !important;
    }
    
    .card-link {
        padding: 10px;
        overflow-x: hidden;
    }
    
    .card-title-perfect {
        font-size: 14px;
        line-height: 1.35;
        /* 14px * 1.35 * 2 = 37.8px */
        min-height: 38px;
        max-height: 38px;
    }
    
    .card-summary-perfect {
        font-size: 11px;
        line-height: 1.45;
        /* 11px * 1.45 * 2 = 31.9px */
        min-height: 32px;
        max-height: 32px;
    }
    
    .catch-tags {
        display: none;
    }
    
    .card-meta-perfect {
        gap: 6px;
        margin-bottom: 5px;
    }
    
    .meta-item {
        font-size: 10px;
        padding: 3px 6px;
        height: 22px;
    }
    
    .card-footer-perfect {
        padding-top: 6px;
        min-height: 36px;
    }
}

/* ===== パフォーマンス最適化 ===== */
.grant-card-perfect {
    /* GPU加速を有効化 */
    transform: translateZ(0);
    backface-visibility: hidden;
    /* will-changeは必要最小限に */
    will-change: box-shadow;
}

/* ホバー時もtransformを使わない */
.grant-card-perfect:hover {
    /* transformは使用しない */
}

.grant-card-perfect:active {
    /* クリック時もレイアウトシフトなし */
    opacity: 0.95;
}

/* アクセシビリティ */
.grant-card-perfect:focus-within {
    outline: 2px solid var(--y-blue);
    outline-offset: 2px;
}

/* アニメーション無効化設定 */
@media (prefers-reduced-motion: reduce) {
    .grant-card-perfect,
    .grant-card-perfect *,
    .catch-urgent,
    .deadline-critical {
        animation: none !important;
        transition: none !important;
    }
}

/* フォント読み込み最適化 */
.grant-card-perfect {
    font-display: swap;
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* 画像がある場合の対策（将来的に追加する場合） */
.card-image {
    aspect-ratio: 16 / 9;
    object-fit: cover;
}
</style>

<article class="grant-card-perfect" 
         data-post-id="<?php echo esc_attr($post_id); ?>"
         itemscope 
         itemtype="https://schema.org/GovernmentService">
    
    <a href="<?php echo esc_url($permalink); ?>" class="card-link" itemprop="url">
        
        <!-- ヘッダー：ステータス＋カテゴリ -->
        <div class="card-header-perfect">
            <div class="header-left">
                <span class="status-badge <?php echo esc_attr($status_data['class']); ?>">
                    <?php echo esc_html($status_data['label']); ?>
                </span>
                
                <?php if (!empty($catch_tags)): ?>
                <div class="catch-tags">
                    <?php foreach ($catch_tags as $tag): ?>
                        <span class="catch-tag catch-<?php echo esc_attr($tag['type']); ?>">
                            <?php echo esc_html($tag['text']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($main_category): ?>
                <span class="category-tag" itemprop="category"><?php echo esc_html($main_category); ?></span>
            <?php endif; ?>
        </div>
        
        <!-- タイトル -->
        <h3 class="card-title-perfect" itemprop="name">
            <?php echo esc_html($title); ?>
        </h3>
        
        <!-- 要約 -->
        <?php if ($ai_summary): ?>
        <p class="card-summary-perfect" itemprop="description">
            <?php echo esc_html($ai_summary); ?>
        </p>
        <?php elseif ($excerpt): ?>
        <p class="card-summary-perfect" itemprop="description">
            <?php echo esc_html($excerpt); ?>
        </p>
        <?php endif; ?>
        
        <!-- メタ情報 -->
        <?php if ($subsidy_display): ?>
        <div class="card-meta-perfect">
            <div class="meta-item">
                <svg class="meta-icon" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-linecap="round"/>
                </svg>
                <span class="meta-highlight"><?php echo esc_html($subsidy_display); ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- フッター -->
        <div class="card-footer-perfect">
            <div class="footer-left">
                
                <!-- 締切 -->
                <?php if ($deadline_display): ?>
                <div class="deadline-info">
                    <?php if ($deadline_urgency): ?>
                    <div class="deadline-badge deadline-urgency <?php echo esc_attr($deadline_class); ?>">
                        <?php echo esc_html($deadline_urgency); ?>
                    </div>
                    <?php endif; ?>
                    <div class="deadline-badge deadline-date <?php echo esc_attr($deadline_class); ?>" 
                         itemprop="validThrough" 
                         content="<?php echo esc_attr($deadline_date); ?>">
                        <?php echo esc_html($deadline_display); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 採択率 -->
                <?php if ($adoption_rate > 0): ?>
                <div class="adoption-badge">
                    <svg class="adoption-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    採択率<?php echo esc_html($adoption_rate); ?>%
                </div>
                <?php endif; ?>
                
            </div>
            
            <div class="footer-right">
                <?php if ($organization): ?>
                    <span class="org-name" itemprop="provider"><?php echo esc_html($organization); ?></span>
                    <span>・</span>
                <?php endif; ?>
                <span class="region-name" itemprop="areaServed"><?php echo esc_html($region_display); ?></span>
            </div>
        </div>
        
    </a>
    
</article>
