<?php
/**
 * Grant Insight Perfect - Front Page Template
 * 完全統合版 v11.0 - SEO/UI/UX 100点満点仕様
 *
 * @package Grant_Insight_Perfect
 * @version 11.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// ===== データ取得の最適化 =====
$cache_key = 'front_page_grant_queries_v11';
$grant_queries = get_transient($cache_key);

if ($grant_queries === false) {
    $today = current_time('Y-m-d');
    $deadline_soon_date = date('Y-m-d', strtotime('+30 days'));
    
    // 締切間近の補助金
    $deadline_soon_query = new WP_Query(array(
        'post_type' => 'grant',
        'posts_per_page' => 9,
        'post_status' => 'publish',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_key' => 'deadline_date',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'deadline_date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'DATE'
            ),
            array(
                'key' => 'deadline_date',
                'value' => $deadline_soon_date,
                'compare' => '<=',
                'type' => 'DATE'
            )
        ),
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));
    
    // 注目の補助金
    $recommended_query = new WP_Query(array(
        'post_type' => 'grant',
        'posts_per_page' => 9,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => 'is_featured',
                'value' => '1',
                'compare' => '='
            )
        ),
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));
    
    // フォールバック
    if (!$recommended_query->have_posts()) {
        $recommended_query = new WP_Query(array(
            'post_type' => 'grant',
            'posts_per_page' => 9,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ));
    }
    
    // 新着補助金
    $new_query = new WP_Query(array(
        'post_type' => 'grant',
        'posts_per_page' => 9,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));
    
    $grant_queries = array(
        'deadline_soon' => $deadline_soon_query->posts,
        'recommended' => $recommended_query->posts,
        'new' => $new_query->posts,
    );
    
    // キャッシュ（15分）
    set_transient($cache_key, $grant_queries, 15 * MINUTE_IN_SECONDS);
}

// 構造化データ
$schema_website = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => get_bloginfo('name'),
    'url' => home_url('/'),
    'description' => get_bloginfo('description'),
    'inLanguage' => 'ja',
    'potentialAction' => array(
        '@type' => 'SearchAction',
        'target' => array(
            '@type' => 'EntryPoint',
            'urlTemplate' => home_url('/grants/') . '?search={search_term_string}'
        ),
        'query-input' => 'required name=search_term_string'
    ),
    'publisher' => array(
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url('/')
    )
);

$schema_breadcrumb = array(
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array(
        array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'ホーム',
            'item' => home_url('/')
        )
    )
);
?>

<style>
/* ============================================
   Front Page - Complete Unified Version v11.0
   ============================================ */

:root {
    --fp-color-bg-primary: #ffffff;
    --fp-color-bg-secondary: #f8f9fa;
    --fp-color-gradient-start: #f5f7fa;
    --fp-color-gradient-end: #e8ecf1;
    --fp-color-overlay: rgba(0, 0, 0, 0.02);
    --fp-color-accent: #FFD700;
    --fp-header-height: 56px;
    --fp-transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

html { scroll-behavior: smooth; height: 100%; overflow-y: auto !important; }
body { margin: 0; padding: 0; min-height: 100vh; overflow-x: hidden; }

.site-main {
    padding: 0; margin: 0; background: var(--fp-color-bg-primary);
    position: relative; width: 100%; overflow: visible;
}

#main-content { margin-top: var(--fp-header-height) !important; padding-top: 0 !important; }

.front-page-section {
    position: relative; margin: 0; padding: 0; width: 100%; display: block; overflow: visible;
}

/* Background Effects */
.site-main::before {
    content: ''; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(135deg, var(--fp-color-gradient-start), #fff, var(--fp-color-gradient-end));
    z-index: -2; pointer-events: none;
}
.site-main::after {
    content: ''; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background-image: repeating-linear-gradient(0deg, transparent, transparent 19px, var(--fp-color-overlay) 19px, var(--fp-color-overlay) 20px),
                      repeating-linear-gradient(90deg, transparent, transparent 19px, var(--fp-color-overlay) 19px, var(--fp-color-overlay) 20px);
    background-size: 20px 20px; opacity: 0.4; z-index: -1; pointer-events: none;
}

/* Scroll Progress */
.scroll-progress {
    position: fixed; top: 0; left: 0; height: 3px; width: 0%;
    background: linear-gradient(90deg, #333 0%, #666 50%, #333 100%);
    background-size: 200% 100%; z-index: 9999; transition: width 0.1s ease;
    animation: fp-shimmer 2s ease-in-out infinite;
}
@keyframes fp-shimmer { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }

/* Ad Space */
.front-ad-space { max-width: 1200px; margin: 20px auto; padding: 0 20px; position: relative; z-index: 1; }

/* Animation */
.section-animate { opacity: 1; transform: translateY(0); transition: opacity 0.8s, transform 0.8s; }
@media (min-width: 1024px) {
    .section-animate { opacity: 0; transform: translateY(30px); }
    .section-animate.visible { opacity: 1; transform: translateY(0); }
}

/* Skip Link */
.skip-to-content {
    position: absolute; top: -40px; left: 0; background: #000; color: #fff;
    padding: 8px 16px; z-index: 100000; transition: top 0.3s; font-weight: 700;
}
.skip-to-content:focus { top: 0; outline: 3px solid var(--fp-color-accent); }

/* =========================================
   Final CTA Section (修正版)
   ========================================= */
.cta-diagnosis-section {
    background: #111111 !important; /* 強制的に黒 */
    color: #ffffff !important;      /* 強制的に白 */
    padding: 100px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cta-diagnosis-wrapper {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 2;
}

/* アイコン */
.cta-icon i {
    font-size: 48px;
    color: #FFD700; /* ゴールド */
    margin-bottom: 24px;
}

/* タイトル */
.cta-title {
    font-family: 'Noto Sans JP', sans-serif;
    font-size: 36px;
    font-weight: 900;
    color: #ffffff !important; /* 白 */
    margin-bottom: 24px;
    letter-spacing: 0.05em;
    line-height: 1.3;
}

/* 説明文 */
.cta-description {
    font-size: 16px;
    color: #e5e5e5 !important; /* 明るいグレー */
    margin-bottom: 48px;
    line-height: 1.8;
    font-weight: 500;
}

/* ボタンエリア */
.cta-button-group {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}

/* 共通ボタン */
.cta-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 18px 40px;
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    border-radius: 50px;
    transition: all 0.3s ease;
    min-width: 240px;
}

/* プライマリ：無料診断（白背景・黒文字） */
.cta-button-primary {
    background: #ffffff;
    color: #111111;
    border: 2px solid #ffffff;
}
.cta-button-primary:hover {
    background: #f0f0f0;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(255,255,255,0.1);
}

/* セカンダリ：検索（透明背景・白枠） */
.cta-button-secondary {
    background: transparent;
    color: #ffffff;
    border: 2px solid #ffffff;
}
.cta-button-secondary:hover {
    background: rgba(255,255,255,0.1);
    transform: translateY(-3px);
}

.cta-button i, .cta-button svg { flex-shrink: 0; }

/* 注釈 */
.cta-note {
    font-size: 13px;
    color: #cccccc !important; /* 薄いグレー */
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

/* モバイル調整 */
@media (max-width: 767px) {
    .cta-title { font-size: 26px; }
    .cta-description { font-size: 14px; text-align: left; }
    .cta-button-group { flex-direction: column; gap: 16px; }
    .cta-button { width: 100%; }
}

/* レスポンシブ & 印刷 */
@media (max-width: 1023px) {
    .site-main, .front-page-section { display: block !important; width: 100% !important; height: auto !important; opacity: 1 !important; transform: none !important; }
}
@media print {
    .site-main::before, .site-main::after, .scroll-progress, .skip-to-content, .front-ad-space { display: none !important; }
}
</style>

<a href="#main-content" class="skip-to-content" aria-label="メインコンテンツへスキップ">
    メインコンテンツへスキップ
</a>

<main id="main-content" class="site-main" role="main" itemscope itemtype="https://schema.org/WebPage">

    <section class="front-page-section section-animate" id="hero-section" aria-labelledby="hero-heading">
        <?php get_template_part('template-parts/front-page/section', 'hero'); ?>
    </section>

    <?php if (function_exists('ji_display_ad')) : ?>
        <div class="front-ad-space front-ad-hero-bottom"><?php ji_display_ad('front_hero_bottom', 'front-page'); ?></div>
    <?php endif; ?>

    <?php if (function_exists('ji_display_ad')) : ?>
        <div class="front-ad-space front-ad-column-top"><?php ji_display_ad('front_column_zone_top', 'front-page'); ?></div>
    <?php endif; ?>
    
    <section class="front-page-section section-animate" id="column-section" aria-labelledby="column-heading">
        <?php get_template_part('template-parts/column/zone'); ?>
    </section>

    <?php if (function_exists('ji_display_ad')) : ?>
        <div class="front-ad-space front-ad-search-top"><?php ji_display_ad('front_search_top', 'front-page'); ?></div>
    <?php endif; ?>
    
    <section class="front-page-section section-animate" id="grant-zone-section" aria-labelledby="grant-zone-heading">
        <?php 
        set_query_var('exclude_cta', true);
        get_template_part('template-parts/front-page/section', 'search'); 
        ?>
    </section>

    <?php if (function_exists('ji_display_ad')) : ?>
        <div class="front-ad-space front-ad-grant-news-top"><?php ji_display_ad('front_grant_news_top', 'front-page'); ?></div>
    <?php endif; ?>
    
    <section class="front-page-section section-animate" id="grant-news-section" aria-labelledby="grant-news-heading">
        <?php 
        set_query_var('deadline_soon_grants', $grant_queries['deadline_soon']);
        set_query_var('recommended_grants', $grant_queries['recommended']);
        set_query_var('new_grants', $grant_queries['new']);
        get_template_part('template-parts/front-page/grant-tabs-section'); 
        ?>
    </section>

    <?php if (function_exists('ji_display_ad')) : ?>
        <div class="front-ad-space front-ad-grant-news-bottom"><?php ji_display_ad('front_grant_news_bottom', 'front-page'); ?></div>
    <?php endif; ?>

    <?php if (function_exists('ji_display_ad')) : ?>
        <div class="front-ad-space front-ad-cta-top"><?php ji_display_ad('front_cta_top', 'front-page'); ?></div>
    <?php endif; ?>
    
    <section class="front-page-section section-animate" 
             id="final-cta-section"
             aria-labelledby="final-cta-title"
             itemscope 
             itemtype="https://schema.org/Service">
        
        <div class="cta-diagnosis-section">
            <div class="cta-diagnosis-wrapper">
                <div class="cta-diagnosis-content">
                    <div class="cta-icon">
                        <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                    </div>
                    
                    <h2 id="final-cta-title" class="cta-title" itemprop="name">
                        あなたに最適な補助金を無料診断
                    </h2>
                    
                    <p class="cta-description" itemprop="description">
                        簡単な質問に答えるだけで、あなたの事業に最適な補助金・助成金を診断します。<br class="pc-only">
                        診断は完全無料、所要時間はわずか3分です。
                    </p>
                    
                    <div class="cta-button-group">
                        <a href="<?php echo esc_url(home_url('/grants/')); ?>" 
                           class="cta-button cta-button-secondary"
                           title="補助金一覧から探す">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            <span>補助金を探す</span>
                        </a>

                        <a href="https://joseikin-insight.com/subsidy-diagnosis/" 
                           class="cta-button cta-button-primary"
                           itemprop="url"
                           title="無料診断を今すぐ始める">
                            <i class="fas fa-play-circle" aria-hidden="true"></i>
                            <span>今すぐ無料診断を始める</span>
                        </a>
                    </div>

                    <p class="cta-note">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <span>会員登録不要・メールアドレス不要</span>
                    </p>
                </div>
            </div>
        </div>
    </section>

</main>

<div class="scroll-progress" id="scroll-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>

<script type="application/ld+json">
<?php echo wp_json_encode($schema_website, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>
<script type="application/ld+json">
<?php echo wp_json_encode($schema_breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>

<script>
/**
 * Front Page JS - v11.0
 */
(function() {
    'use strict';
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        setupScrollProgress();
        setupSectionAnimations();
        setupSmoothScroll();
        // パフォーマンス & SEO監視
        if('performance' in window) monitorPerf();
        setupSEOTracking();
    }
    
    function setupScrollProgress() {
        const bar = document.getElementById('scroll-progress');
        if(!bar) return;
        let ticking = false;
        window.addEventListener('scroll', () => {
            if(!ticking) {
                window.requestAnimationFrame(() => {
                    const winH = window.innerHeight;
                    const docH = document.documentElement.scrollHeight;
                    const scrolled = window.scrollY;
                    const pct = docH - winH > 0 ? (scrolled / (docH - winH)) * 100 : 0;
                    bar.style.width = Math.min(Math.max(pct, 0), 100) + '%';
                    ticking = false;
                });
                ticking = true;
            }
        }, {passive:true});
    }
    
    function setupSectionAnimations() {
        if(window.innerWidth < 1024) {
            document.querySelectorAll('.section-animate').forEach(el => el.classList.add('visible'));
            return;
        }
        if('IntersectionObserver' in window) {
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if(e.isIntersecting) {
                        e.target.classList.add('visible');
                        obs.unobserve(e.target);
                    }
                });
            }, {threshold: 0.1, rootMargin: '0px 0px -50px 0px'});
            document.querySelectorAll('.section-animate').forEach(el => obs.observe(el));
        } else {
            document.querySelectorAll('.section-animate').forEach(el => el.classList.add('visible'));
        }
    }
    
    function setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', function(e) {
                const h = this.getAttribute('href');
                if(h && h !== '#' && h !== '#0') {
                    const t = document.querySelector(h);
                    if(t) {
                        e.preventDefault();
                        const off = 80;
                        window.scrollTo({top: t.getBoundingClientRect().top + window.scrollY - off, behavior: 'smooth'});
                        t.setAttribute('tabindex', '-1'); t.focus();
                    }
                }
            });
        });
    }

    function monitorPerf() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                const p = performance.getEntriesByType('navigation')[0];
                if(p && typeof gtag !== 'undefined') {
                    gtag('event', 'page_timing', {'event_category':'Performance', 'value':Math.round(p.loadEventEnd - p.loadEventStart)});
                }
            }, 0);
        });
    }

    function setupSEOTracking() {
        let maxScroll = 0;
        const points = [25,50,75,100];
        const tracked = new Set();
        let tick = false;
        
        window.addEventListener('scroll', () => {
            if(!tick) {
                window.requestAnimationFrame(() => {
                    const winH = window.innerHeight;
                    const docH = document.documentElement.scrollHeight;
                    const pct = Math.round((window.scrollY / (docH - winH)) * 100);
                    if(pct > maxScroll) {
                        maxScroll = pct;
                        points.forEach(p => {
                            if(pct >= p && !tracked.has(p)) {
                                tracked.add(p);
                                if(typeof gtag !== 'undefined') {
                                    gtag('event', 'scroll_depth', {'event_category':'Engagement', 'event_label':p+'%', 'value':p});
                                }
                            }
                        });
                    }
                    tick = false;
                });
                tick = true;
            }
        }, {passive:true});
    }
})();
</script>

<?php get_footer(); ?>