<?php
/**
 * Sidebar Search Widget - Refined
 * サイドバー検索ウィジェット - UX/UI/デザイン統合版
 * * v1.0.0-refine 変更点:
 * - デザインシステム統合: 角丸(8px)と配色をトップページのデザインに統一。
 * - UX改善: フォームグループの境界とラベル表示を調整し、視認性を向上。
 * * @package Grant_Insight_Perfect
 * @version 1.0.0-refine
 */

if (!defined('ABSPATH')) {
    exit;
}

// カテゴリーを取得 (gi_get_all_prefectures() はグローバル関数として定義されている前提)
$all_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 50
));

// 都道府県を取得
// 注: 'gi_get_all_prefectures' が存在しない環境ではエラーになるため、適切な関数名を確認してください。
if (function_exists('gi_get_all_prefectures')) {
    $prefectures = gi_get_all_prefectures();
} else {
    $prefectures = [];
}

// デザイン変数をPHPで再定義 (単一ページテンプレートから継承される前提だが、安全のため再定義)
$gus_radius = '8px';
$gus_black = '#111111';
$gus_yellow = '#FFD700';
$gus_gray_300 = '#e5e5e5';
$gus_gray_100 = '#f5f5f5';
?>

<section class="gus-sidebar-card sidebar-search-card" aria-labelledby="sidebar-search-title">
    <header class="card-header card-header-search" style="
        background: linear-gradient(135deg, <?php echo esc_attr($gus_black); ?> 0%, #1a1a1a 100%);
        color: #ffffff;
        border-bottom: 2px solid <?php echo esc_attr($gus_yellow); ?>;
        padding: 16px;
    ">
        <i class="fas fa-search" aria-hidden="true" style="color: <?php echo esc_attr($gus_yellow); ?>;"></i>
        <h2 id="sidebar-search-title" style="color: #ffffff; font-weight: 800; font-size: 16px; margin: 0;">補助金を探す</h2>
    </header>
    <div class="card-body">
        <form class="sidebar-search-form" 
              id="sidebar-search-form" 
              action="<?php echo esc_url(home_url('/grants/')); ?>"
              method="get"
              role="search"
              aria-label="サイドバー補助金検索フォーム">
            
            <div class="sidebar-form-group">
                <label class="sidebar-form-label" for="sidebar-category-select" style="
                    background: <?php echo esc_attr($gus_gray_100); ?>;
                    color: <?php echo esc_attr($gus_black); ?>;
                    border-bottom: 1px solid <?php echo esc_attr($gus_gray_300); ?>;
                ">
                    <i class="fas fa-briefcase" aria-hidden="true" style="color: <?php echo esc_attr($gus_black); ?>;"></i>
                    <span>用途・目的</span>
                </label>
                <select id="sidebar-category-select" 
                        name="category" 
                        class="sidebar-form-select"
                        aria-label="補助金の用途を選択"
                        style="border-radius: 4px;">
                    <option value="">カテゴリーを選択</option>
                    <?php if (!empty($all_categories) && !is_wp_error($all_categories)): ?>
                        <?php foreach ($all_categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat->slug); ?>">
                                <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="sidebar-form-group">
                <label class="sidebar-form-label" for="sidebar-prefecture-select" style="
                    background: <?php echo esc_attr($gus_gray_100); ?>;
                    color: <?php echo esc_attr($gus_black); ?>;
                    border-bottom: 1px solid <?php echo esc_attr($gus_gray_300); ?>;
                ">
                    <i class="fas fa-map-marker-alt" aria-hidden="true" style="color: <?php echo esc_attr($gus_black); ?>;"></i>
                    <span>都道府県</span>
                </label>
                <select id="sidebar-prefecture-select" 
                        name="prefecture" 
                        class="sidebar-form-select"
                        aria-label="都道府県を選択"
                        style="border-radius: 4px;">
                    <option value="">都道府県を選択</option>
                    <?php if (!empty($prefectures)): ?>
                        <?php foreach ($prefectures as $pref): ?>
                            <option value="<?php echo esc_attr($pref['slug']); ?>">
                                <?php echo esc_html($pref['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="sidebar-form-group" style="border-bottom: none; padding-bottom: 0;">
                <label class="sidebar-form-label" for="sidebar-keyword-input" style="
                    background: <?php echo esc_attr($gus_gray_100); ?>;
                    color: <?php echo esc_attr($gus_black); ?>;
                    border-bottom: 1px solid <?php echo esc_attr($gus_gray_300); ?>;
                ">
                    <i class="fas fa-search" aria-hidden="true" style="color: <?php echo esc_attr($gus_black); ?>;"></i>
                    <span>キーワード</span>
                </label>
                <input type="search" 
                       id="sidebar-keyword-input" 
                       name="search"
                       class="sidebar-form-input" 
                       placeholder="例：IT導入、設備投資"
                       aria-label="フリーワード検索"
                       autocomplete="off"
                       style="border-radius: 4px;">
            </div>

            <div class="sidebar-button-group">
                <button type="button" 
                        class="sidebar-btn sidebar-btn-reset" 
                        id="sidebar-reset-btn"
                        aria-label="検索条件をクリア"
                        style="
                            border-radius: <?php echo esc_attr($gus_radius); ?>;
                            border: 1px solid <?php echo esc_attr($gus_black); ?>;
                            background: #FFFFFF;
                            color: <?php echo esc_attr($gus_black); ?>;
                        ">
                    <i class="fas fa-undo" aria-hidden="true"></i>
                    <span>クリア</span>
                </button>
                <button type="submit" 
                        class="sidebar-btn sidebar-btn-search" 
                        id="sidebar-search-btn"
                        aria-label="補助金を検索"
                        style="
                            border-radius: <?php echo esc_attr($gus_radius); ?>;
                            background: <?php echo esc_attr($gus_black); ?>;
                            color: <?php echo esc_attr($gus_yellow); ?>;
                            border: 2px solid <?php echo esc_attr($gus_black); ?>;
                        ">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <span>検索</span>
                </button>
            </div>
        </form>
    </div>
</section>

<style>
/* ============================================
    Sidebar Search Widget Styles - REFINED
    ============================================ */

.gus-sidebar-card {
    /* 継承: 全体の角丸と基本のボーダー */
    border-radius: <?php echo esc_attr($gus_radius); ?>;
    border: 1px solid <?php echo esc_attr($gus_gray_300); ?>;
}

.card-header-search {
    /* 継承: TOPページCTAに合わせたグラデーションと強い境界線 */
    background: linear-gradient(135deg, <?php echo esc_attr($gus_black); ?> 0%, #1a1a1a 100%);
    color: #ffffff;
    border-bottom: 2px solid <?php echo esc_attr($gus_yellow); ?>; /* 3px -> 2px */
    padding: 16px;
    border-radius: <?php echo esc_attr($gus_radius); ?> <?php echo esc_attr($gus_radius); ?> 0 0; /* 角丸を上部にのみ適用 */
}

.card-header-search h2 {
    color: #ffffff;
    font-weight: 800;
    font-size: 16px;
    margin: 0;
}

.sidebar-search-form {
    display: flex;
    flex-direction: column;
    gap: 0;
    padding: 0;
}

/* フォームグループ（各入力欄のコンテナ） */
.sidebar-form-group {
    display: flex;
    flex-direction: column;
    gap: 0;
    /* 内部の区切り線を調整 */
    border-bottom: 1px solid <?php echo esc_attr($gus_gray_300); ?>; /* 2pxから1pxに調整 */
    padding: 0; /* paddingをフォームラベルとインプットに移動 */
    background: #FFFFFF;
}

.sidebar-form-group:last-of-type {
    border-bottom: none;
}

/* フォームラベル（黒背景の項目名） */
.sidebar-form-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    padding: 12px 16px; /* 適切なパディング */
    margin: 0; /* グループに内包されるためマージンをリセット */
    
    /* 継承されたスタイルをオーバーライド */
    background: none !important;
    color: <?php echo esc_attr($gus_black); ?> !important;
    border-bottom: 1px solid <?php echo esc_attr($gus_gray_300); ?> !important;
}

.sidebar-form-label i {
    font-size: 12px;
    color: <?php echo esc_attr($gus_black); ?>; /* アイコンカラーを黒に統一 */
}

/* フォーム要素（Select/Input） */
.sidebar-form-select,
.sidebar-form-input {
    width: 100%;
    padding: 12px 16px;
    font-size: 15px;
    font-weight: 500;
    color: #000000;
    background: #FFFFFF;
    border: none; /* グループのボーダーを使用するため削除 */
    border-radius: 0; 
    min-height: 48px;
    box-shadow: none !important;
}

.sidebar-form-select:focus,
.sidebar-form-input:focus {
    /* フォーカス時に背景をわずかに変え、視認性を高める */
    background: <?php echo esc_attr($gus_gray_50); ?>;
    outline: 2px solid <?php echo esc_attr($gus_yellow); ?>;
    border-color: <?php echo esc_attr($gus_yellow); ?>;
    z-index: 10;
}

/* ボタングループ */
.sidebar-button-group {
    display: flex;
    gap: 8px;
    padding: 16px;
    background: <?php echo esc_attr($gus_gray_50); ?>; /* F5F5F5 -> var(--gus-gray-50) */
    border-top: 2px solid <?php echo esc_attr($gus_gray_300); ?>;
}

.sidebar-btn {
    flex: 1;
    padding: 11px 12px;
    font-size: 14px;
    font-weight: 700;
    border-radius: <?php echo esc_attr($gus_radius); ?>; /* 角丸適用 */
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.sidebar-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.sidebar-btn-reset {
    background: #FFFFFF;
    color: <?php echo esc_attr($gus_black); ?>;
    border: 1px solid <?php echo esc_attr($gus_black); ?>; /* アウトラインスタイル */
}

.sidebar-btn-reset:hover {
    background: <?php echo esc_attr($gus_gray_100); ?>;
}

.sidebar-btn-search {
    /* プライマリボタンのスタイルを適用 */
    background: <?php echo esc_attr($gus_black); ?>;
    color: <?php echo esc_attr($gus_yellow); ?>;
    border: 2px solid <?php echo esc_attr($gus_black); ?>;
}

.sidebar-btn-search:hover {
    background: <?php echo esc_attr($gus_yellow); ?>;
    color: <?php echo esc_attr($gus_black); ?>;
    border-color: <?php echo esc_attr($gus_yellow); ?>;
}

/* レスポンシブ対応 */
@media (max-width: 1023px) {
    .sidebar-search-card {
        /* サイドバー全体が非表示になるため、このまま */
        display: none;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    // サイドバー検索フォームの初期化
    function initSidebarSearchForm() {
        const form = document.getElementById('sidebar-search-form');
        const resetBtn = document.getElementById('sidebar-reset-btn');
        
        if (!form) {
            return;
        }
        
        // リセットボタンの処理
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                form.reset();
                // 検索結果ページでリセットした場合、URLクエリをクリアしてリダイレクト
                if (window.location.search) {
                    window.location.href = window.location.pathname;
                }
            });
        }
    }
    
    // DOMContentLoaded後に初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarSearchForm);
    } else {
        initSidebarSearchForm();
    }
})();
</script>