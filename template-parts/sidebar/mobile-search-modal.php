<?php
/**
 * Mobile Search Modal
 * モバイル検索モーダル
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// カテゴリーを取得
$mobile_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => false,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 50
));

// 都道府県を取得
$mobile_prefectures = gi_get_all_prefectures();
?>

<!-- モバイル用検索オーバーレイ -->
<div class="gus-mobile-search-overlay" id="mobileSearchOverlay" aria-hidden="true"></div>

<!-- モバイル用検索モーダル -->
<div class="gus-mobile-search-modal" id="mobileSearchModal" role="dialog" aria-labelledby="mobile-search-title" aria-modal="true">
    <header class="gus-mobile-search-header">
        <h2 class="gus-mobile-search-title" id="mobile-search-title">
            <i class="fas fa-search" aria-hidden="true"></i>
            補助金を探す
        </h2>
        <button class="gus-mobile-search-close" id="mobileSearchClose" aria-label="閉じる">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </header>
    
    <div class="gus-mobile-search-content">
        <form class="mobile-search-form" 
              id="mobile-search-form" 
              action="<?php echo esc_url(home_url('/grants/')); ?>"
              method="get"
              role="search"
              aria-label="モバイル補助金検索フォーム">
            
            <!-- 用途（カテゴリ） -->
            <div class="mobile-form-group">
                <label class="mobile-form-label" for="mobile-category-select">
                    <i class="fas fa-briefcase" aria-hidden="true"></i>
                    <span>用途から探す</span>
                </label>
                <select id="mobile-category-select" 
                        name="category" 
                        class="mobile-form-select"
                        aria-label="補助金の用途を選択">
                    <option value="">カテゴリーを選択</option>
                    <?php if (!empty($mobile_categories) && !is_wp_error($mobile_categories)): ?>
                        <?php foreach ($mobile_categories as $cat): ?>
                            <option value="<?php echo esc_attr($cat->slug); ?>">
                                <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- 都道府県 -->
            <div class="mobile-form-group">
                <label class="mobile-form-label" for="mobile-prefecture-select">
                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                    <span>都道府県から探す</span>
                </label>
                <select id="mobile-prefecture-select" 
                        name="prefecture" 
                        class="mobile-form-select"
                        aria-label="都道府県を選択">
                    <option value="">都道府県を選択</option>
                    <?php if (!empty($mobile_prefectures)): ?>
                        <?php foreach ($mobile_prefectures as $pref): ?>
                            <option value="<?php echo esc_attr($pref['slug']); ?>">
                                <?php echo esc_html($pref['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- 市町村（都道府県選択後に動的読み込み） -->
            <div class="mobile-form-group" id="mobile-municipality-group" style="display: none;">
                <label class="mobile-form-label" for="mobile-municipality-select">
                    <i class="fas fa-building" aria-hidden="true"></i>
                    <span>市町村</span>
                </label>
                <select id="mobile-municipality-select" 
                        name="municipality" 
                        class="mobile-form-select"
                        aria-label="市町村を選択">
                    <option value="">市町村を選択</option>
                </select>
            </div>

            <!-- フリーワード検索 -->
            <div class="mobile-form-group">
                <label class="mobile-form-label" for="mobile-keyword-input">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <span>キーワード検索</span>
                </label>
                <input type="search" 
                       id="mobile-keyword-input" 
                       name="search"
                       class="mobile-form-input" 
                       placeholder="例：IT導入、設備投資、創業支援"
                       aria-label="フリーワード検索"
                       autocomplete="off">
            </div>

            <!-- ボタングループ -->
            <div class="mobile-button-group">
                <button type="button" 
                        class="mobile-btn mobile-btn-reset" 
                        id="mobile-reset-btn"
                        aria-label="検索条件をクリア">
                    <i class="fas fa-undo" aria-hidden="true"></i>
                    <span>クリア</span>
                </button>
                <button type="submit" 
                        class="mobile-btn mobile-btn-search" 
                        id="mobile-search-btn"
                        aria-label="補助金を検索">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <span>検索する</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* ============================================
   Mobile Search Modal Styles
   モバイル検索モーダル スタイル
   ============================================ */

/* オーバーレイ */
.gus-mobile-search-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 999;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gus-mobile-search-overlay.active {
    display: block;
    opacity: 1;
}

/* モーダル */
.gus-mobile-search-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #FFFFFF;
    z-index: 1000;
    transform: translateY(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    flex-direction: column;
}

.gus-mobile-search-modal.active {
    display: flex;
    transform: translateY(0);
}

/* ヘッダー */
.gus-mobile-search-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
    color: #FFFFFF;
    border-bottom: 3px solid #FFD700;
    flex-shrink: 0;
}

.gus-mobile-search-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    color: #FFFFFF;
}

.gus-mobile-search-title i {
    font-size: 20px;
    color: #FFD700;
}

.gus-mobile-search-close {
    width: 36px;
    height: 36px;
    background: transparent;
    border: 2px solid #FFFFFF;
    border-radius: 0;
    color: #FFFFFF;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.gus-mobile-search-close:active {
    background: #FFFFFF;
    color: #000000;
}

.gus-mobile-search-close i {
    font-size: 18px;
}

/* コンテンツ */
.gus-mobile-search-content {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 20px 16px;
}

/* フォーム */
.mobile-search-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.mobile-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.mobile-form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #000000;
}

.mobile-form-label i {
    font-size: 14px;
    color: #666666;
}

.mobile-form-select,
.mobile-form-input {
    width: 100%;
    padding: 14px 16px;
    font-size: 16px;
    font-weight: 500;
    color: #000000;
    background: #FFFFFF;
    border: 2px solid #E5E5E5;
    border-radius: 0;
    transition: all 0.2s ease;
}

.mobile-form-select:focus,
.mobile-form-input:focus {
    outline: none;
    border-color: #000000;
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3);
}

.mobile-form-input::placeholder {
    color: #999999;
    font-weight: 400;
}

/* ボタングループ */
.mobile-button-group {
    display: flex;
    gap: 12px;
    margin-top: 10px;
    padding-top: 20px;
    border-top: 2px solid #E5E5E5;
}

.mobile-btn {
    flex: 1;
    padding: 16px 20px;
    font-size: 16px;
    font-weight: 700;
    border: 2px solid #000000;
    border-radius: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.mobile-btn:active {
    transform: scale(0.98);
}

.mobile-btn i {
    font-size: 16px;
}

.mobile-btn-reset {
    background: #FFFFFF;
    color: #000000;
}

.mobile-btn-reset:active {
    background: #F5F5F5;
}

.mobile-btn-search {
    background: #000000;
    color: #FFD700;
}

.mobile-btn-search:active {
    background: #FFD700;
    color: #000000;
}
</style>

<script>
(function() {
    'use strict';
    
    // モバイル検索モーダルの初期化
    function initMobileSearchModal() {
        const searchModal = document.getElementById('mobileSearchModal');
        const searchOverlay = document.getElementById('mobileSearchOverlay');
        const searchClose = document.getElementById('mobileSearchClose');
        const prefectureSelect = document.getElementById('mobile-prefecture-select');
        const municipalityGroup = document.getElementById('mobile-municipality-group');
        const municipalitySelect = document.getElementById('mobile-municipality-select');
        const resetBtn = document.getElementById('mobile-reset-btn');
        const searchForm = document.getElementById('mobile-search-form');
        
        if (!searchModal || !searchOverlay || !searchClose) {
            return;
        }
        
        // モーダルを開く関数をグローバルに公開
        window.openMobileSearchModal = function() {
            searchModal.classList.add('active');
            searchOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        };
        
        // モーダルを閉じる
        function closeModal() {
            searchModal.classList.remove('active');
            searchOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // 閉じるボタンクリック
        if (searchClose) {
            searchClose.addEventListener('click', closeModal);
        }
        
        // オーバーレイクリック
        if (searchOverlay) {
            searchOverlay.addEventListener('click', closeModal);
        }
        
        // ESCキーで閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchModal.classList.contains('active')) {
                closeModal();
            }
        });
        
        // 都道府県選択時の処理
        if (prefectureSelect && municipalityGroup && municipalitySelect) {
            prefectureSelect.addEventListener('change', function() {
                const selectedPrefecture = this.value;
                
                if (selectedPrefecture) {
                    municipalityGroup.style.display = 'flex';
                    loadMunicipalitiesMobile(selectedPrefecture);
                } else {
                    municipalityGroup.style.display = 'none';
                    municipalitySelect.innerHTML = '<option value="">市町村を選択</option>';
                }
            });
        }
        
        // リセットボタンの処理
        if (resetBtn && searchForm) {
            resetBtn.addEventListener('click', function() {
                searchForm.reset();
                if (municipalityGroup) {
                    municipalityGroup.style.display = 'none';
                }
                if (municipalitySelect) {
                    municipalitySelect.innerHTML = '<option value="">市町村を選択</option>';
                }
            });
        }
    }
    
    // 市町村を読み込む（AJAX）
    function loadMunicipalitiesMobile(prefectureSlug) {
        const municipalitySelect = document.getElementById('mobile-municipality-select');
        
        if (!municipalitySelect) {
            return;
        }
        
        // ローディング表示
        municipalitySelect.innerHTML = '<option value="">読み込み中...</option>';
        municipalitySelect.disabled = true;
        
        // AJAX リクエスト
        fetch(ajaxurl || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_municipalities&prefecture=' + encodeURIComponent(prefectureSlug)
        })
        .then(response => response.json())
        .then(data => {
            municipalitySelect.disabled = false;
            
            if (data.success && data.data && data.data.length > 0) {
                let html = '<option value="">市町村を選択</option>';
                data.data.forEach(function(municipality) {
                    html += '<option value="' + municipality.slug + '">' + 
                            municipality.name + '</option>';
                });
                municipalitySelect.innerHTML = html;
            } else {
                municipalitySelect.innerHTML = '<option value="">市町村が見つかりません</option>';
            }
        })
        .catch(error => {
            console.error('Error loading municipalities:', error);
            municipalitySelect.disabled = false;
            municipalitySelect.innerHTML = '<option value="">読み込みエラー</option>';
        });
    }
    
    // DOMContentLoaded後に初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileSearchModal);
    } else {
        initMobileSearchModal();
    }
})();
</script>
