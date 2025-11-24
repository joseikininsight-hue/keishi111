<?php
/**
 * Template Part: Global Sticky CTA - Mobile Compact Version
 * グローバルスティッキーCTA - モバイル最適化・検索重視版 v11.1
 * * @package Grant_Insight_Perfect
 * @version 11.1.0
 * * === 変更点 ===
 * - Mobile Height: 72px -> 58px (画面占有率を削減)
 * - Layout: 検索(60%) : 診断(40%) の比率は維持
 * - Style: ハイエンド・モノクローム
 */

if (!defined('ABSPATH')) exit;

// ページ設定で非表示にする場合の判定
if (get_query_var('hide_sticky_cta')) return;
?>

<div id="ui-sticky-cta" class="ui-sticky-cta" aria-hidden="false">
    <div class="ui-sticky-inner">
        
        <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" 
           class="ui-sticky-btn ui-btn-diagnosis"
           aria-label="無料で診断する">
            <div class="ui-btn-icon-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </div>
            <div class="ui-btn-text">
                <span class="en">DIAGNOSIS</span>
                <span class="ja">無料診断</span>
            </div>
        </a>

        <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
           class="ui-sticky-btn ui-btn-search"
           aria-label="補助金・助成金を探す">
            <div class="ui-btn-text">
                <span class="en">SEARCH GRANTS</span>
                <span class="ja">助成金を探す</span>
            </div>
            <div class="ui-btn-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            
            <div class="ui-btn-effect"></div>
        </a>

    </div>
</div>

<style>
/* ============================================
   Global Sticky CTA Styles (Mobile Compact)
   ============================================ */

:root {
    --cta-z-index: 9999;
    /* デフォルト（PC・タブレット）の高さ */
    --cta-height: 70px; 
    --cta-bg-light: #ffffff;
    --cta-bg-dark: #111111;
    --cta-text-main: #111111;
    --cta-text-inverse: #ffffff;
    --cta-border: #e5e5e5;
    --cta-accent: #FFD700;
    --cta-font-en: 'Inter', -apple-system, sans-serif;
    --cta-font-ja: 'Noto Sans JP', sans-serif;
    --cta-trans: cubic-bezier(0.2, 0.8, 0.2, 1);
}

/* スマホ用の変数を上書き */
@media (max-width: 767px) {
    :root {
        /* モバイル時の高さを縮小 (58px) */
        --cta-height: 58px;
    }
}

/* Wrapper */
.ui-sticky-cta {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: var(--cta-z-index);
    background: var(--cta-bg-light);
    border-top: 1px solid var(--cta-border);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
    padding-bottom: env(safe-area-inset-bottom);
    transform: translateY(0);
    transition: transform 0.4s var(--cta-trans);
    will-change: transform;
}

/* Hidden State (Scroll Down) */
.ui-sticky-cta.is-hidden {
    transform: translateY(100%);
}

/* Inner Layout */
.ui-sticky-inner {
    display: flex;
    height: var(--cta-height);
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

/* Buttons Common */
.ui-sticky-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border: none;
    cursor: pointer;
    padding: 0 12px;
    gap: 10px;
    position: relative;
    overflow: hidden;
    transition: background-color 0.2s;
}

/* Text Styling */
.ui-btn-text {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.1;
}

.ui-btn-text .en {
    font-family: var(--cta-font-en);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.05em;
    opacity: 0.8;
    text-transform: uppercase;
    margin-bottom: 1px;
}

.ui-btn-text .ja {
    font-family: var(--cta-font-ja);
    font-size: 14px;
    font-weight: 700;
}

/* --- Diagnosis Button (Secondary / White) --- */
.ui-btn-diagnosis {
    flex: 0 0 40%; /* Width 40% */
    background: var(--cta-bg-light);
    color: var(--cta-text-main);
    border-right: 1px solid var(--cta-border);
}

.ui-btn-diagnosis:active {
    background: #f5f5f5;
}

.ui-btn-diagnosis .ui-btn-text .ja {
    font-size: 12px; /* Compact */
}
.ui-btn-diagnosis .ui-btn-text .en {
    font-size: 9px;
}

/* --- Search Button (Primary / Black / Focus) --- */
.ui-btn-search {
    flex: 1; /* Takes remaining space (60%) */
    background: var(--cta-bg-dark);
    color: var(--cta-text-inverse);
}

.ui-btn-search:active {
    background: #000000;
    opacity: 0.9;
}

/* Highlight "SEARCH" text */
.ui-btn-search .ui-btn-text .en {
    color: var(--cta-accent);
    opacity: 1;
    font-size: 9px;
}

.ui-btn-search .ui-btn-text .ja {
    font-size: 15px; /* Larger for emphasis */
}

/* Icon Wrapper */
.ui-btn-icon-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Shine Animation (Only on Search) */
.ui-btn-effect {
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
    transform: skewX(-25deg);
    animation: cta-shine 6s infinite;
    pointer-events: none;
}

@keyframes cta-shine {
    0%, 80% { left: -100%; }
    100% { left: 200%; }
}

/* Responsive Adjustments */
@media (max-width: 767px) {
    /* さらにコンパクトに調整 */
    .ui-sticky-btn {
        padding: 0 8px;
        gap: 8px;
    }
    
    .ui-btn-text .en {
        font-size: 8px; /* 英語表記を極小に */
        letter-spacing: 0;
    }
    
    .ui-btn-diagnosis .ui-btn-text .ja {
        font-size: 11px;
    }
    
    .ui-btn-search .ui-btn-text .ja {
        font-size: 14px;
    }
    
    /* アイコンサイズ微調整 */
    .ui-btn-icon-wrap svg {
        width: 16px;
        height: 16px;
    }
}

/* Body padding handling via JS is preferred, but CSS fallback */
body {
    padding-bottom: calc(var(--cta-height) + env(safe-area-inset-bottom));
}
</style>

<script>
(function() {
    'use strict';

    const stickyBar = document.getElementById('ui-sticky-cta');
    if (!stickyBar) return;

    let lastScrollY = window.scrollY;
    let ticking = false;
    const threshold = 50; // スクロール感度

    const updateUI = () => {
        const currentScrollY = window.scrollY;
        const windowHeight = window.innerHeight;
        const docHeight = document.documentElement.scrollHeight;

        // 1. ページ最下部では常に表示
        if (windowHeight + currentScrollY >= docHeight - 50) {
            stickyBar.classList.remove('is-hidden');
            ticking = false;
            return;
        }

        // 2. スクロール方向制御
        if (currentScrollY > lastScrollY && currentScrollY > threshold) {
            // 下へスクロール: 隠す
            stickyBar.classList.add('is-hidden');
        } else {
            // 上へスクロール: 表示
            stickyBar.classList.remove('is-hidden');
        }

        lastScrollY = currentScrollY;
        ticking = false;
    };

    // Scroll Listener
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(updateUI);
            ticking = true;
        }
    }, { passive: true });

    // 初期表示演出
    setTimeout(() => {
        stickyBar.classList.remove('is-hidden');
    }, 800);

    // Google Analytics Event
    const btns = stickyBar.querySelectorAll('.ui-sticky-btn');
    btns.forEach(btn => {
        btn.addEventListener('click', function() {
            const label = this.querySelector('.en').innerText;
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'Sticky CTA',
                    'event_label': label
                });
            }
        });
    });

    console.log('✅ Compact Sticky CTA Initialized');
})();
</script>