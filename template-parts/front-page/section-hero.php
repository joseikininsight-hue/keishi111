<?php
/**
 * Hero Section - Vertically Centered Ultimate Version
 * ヒーローセクション - 縦方向中央配置・SEO/UI/UX完全最適化版
 * * @package Grant_Insight_Perfect
 * @version 40.0-centered-ultimate
 */

if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// 安全な出力用関数
if (!function_exists('gih_safe_output')) {
    function gih_safe_output($text) {
        return esc_html($text);
    }
}

// オプション取得用関数
if (!function_exists('gih_get_option')) {
    function gih_get_option($key, $default = '') {
        $value = get_option('gih_' . $key, $default);
        return !empty($value) ? $value : $default;
    }
}

// 設定値
$hero_config = array(
    'main_title' => gih_get_option('hero_main_title', '補助金・助成金を'),
    'sub_title' => gih_get_option('hero_sub_title', 'AIが効率的に検索'),
    'description' => gih_get_option('hero_description', 'あなたのビジネスに最適な補助金・助成金情報を、最新AIテクノロジーで効率的に検索。専門家による申請サポートで成功まで導きます。'),
    'cta_primary_text' => gih_get_option('hero_cta_primary', '無料で助成金を探す'),
    'cta_primary_url' => esc_url(home_url('/grants/')),
    'cta_secondary_text' => gih_get_option('hero_cta_secondary', '無料診断はこちら'),
    'cta_secondary_url' => esc_url('https://joseikin-insight.com/subsidy-diagnosis/'),
    'hero_image' => esc_url('https://joseikin-insight.com/1-3/'), // 仮画像URL
    'site_name' => get_bloginfo('name'),
    'site_url' => home_url(),
    'site_description' => get_bloginfo('description')
);

// 構造化データ生成
$schema_web_app = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebApplication',
    'name' => '補助金インサイト - AI補助金検索システム',
    'applicationCategory' => 'BusinessApplication',
    'description' => '全国の補助金・助成金情報をAIが効率的に検索。業種別・地域別対応で最適な制度を発見できる無料プラットフォーム。',
    'url' => $hero_config['site_url'],
    'operatingSystem' => 'Web Browser',
    'offers' => array(
        '@type' => 'Offer',
        'price' => '0',
        'priceCurrency' => 'JPY',
        'availability' => 'https://schema.org/InStock'
    ),
    'provider' => array(
        '@type' => 'Organization',
        'name' => $hero_config['site_name'],
        'url' => $hero_config['site_url']
    )
);

$schema_organization = array(
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $hero_config['site_name'],
    'url' => $hero_config['site_url'],
    'description' => '補助金・助成金情報をAIで効率的に検索できるプラットフォーム。専門家による申請サポートを提供。',
    'contactPoint' => array(
        '@type' => 'ContactPoint',
        'contactType' => 'customer support',
        'availableLanguage' => array('ja', 'Japanese')
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
            'item' => $hero_config['site_url']
        )
    )
);

$schema_website = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $hero_config['site_name'],
    'url' => $hero_config['site_url'],
    'potentialAction' => array(
        '@type' => 'SearchAction',
        'target' => array(
            '@type' => 'EntryPoint',
            'urlTemplate' => $hero_config['site_url'] . '/grants/?search={search_term_string}'
        ),
        'query-input' => 'required name=search_term_string'
    )
);
?>

<meta name="description" content="補助金・助成金をAIが効率的に検索｜業種別・地域別対応、専門家による申請サポート完備。完全無料で今すぐ検索開始。">
<meta name="keywords" content="補助金,助成金,AI検索,事業支援,申請サポート,無料検索,ビジネス支援">
<meta name="author" content="<?php echo esc_attr($hero_config['site_name']); ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="<?php echo esc_url($hero_config['site_url']); ?>">

<meta property="og:type" content="website">
<meta property="og:title" content="補助金・助成金をAIが効率的に検索 | <?php echo esc_attr($hero_config['site_name']); ?>">
<meta property="og:description" content="あなたのビジネスに最適な補助金・助成金を発見。専門家による充実したサポートで成功まで導きます。完全無料。">
<meta property="og:url" content="<?php echo esc_url($hero_config['site_url']); ?>">
<meta property="og:image" content="<?php echo esc_url($hero_config['hero_image']); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="800">
<meta property="og:site_name" content="<?php echo esc_attr($hero_config['site_name']); ?>">
<meta property="og:locale" content="ja_JP">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="補助金・助成金をAIが効率的に検索">
<meta name="twitter:description" content="あなたのビジネスに最適な補助金・助成金を発見。専門家サポート完備。完全無料。">
<meta name="twitter:image" content="<?php echo esc_url($hero_config['hero_image']); ?>">

<script type="application/ld+json">
<?php echo wp_json_encode($schema_web_app, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>
<script type="application/ld+json">
<?php echo wp_json_encode($schema_organization, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>
<script type="application/ld+json">
<?php echo wp_json_encode($schema_breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>
<script type="application/ld+json">
<?php echo wp_json_encode($schema_website, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>

<main id="main-content" role="main" itemscope itemtype="https://schema.org/WebPage">
    
    <section class="gih-hero-section" 
             id="hero-section" 
             role="banner" 
             aria-labelledby="hero-main-heading">
        
        <div class="gih-container">
            
            <div class="gih-desktop-layout">
                <div class="gih-content-grid">
                    
                    <article class="gih-content-left">
                        
                        <h1 class="gih-title" id="hero-main-heading" itemprop="headline">
                            <span class="gih-title-main">
                                <?php echo gih_safe_output($hero_config['main_title']); ?>
                            </span>
                            <span class="gih-title-highlight">
                                <?php echo gih_safe_output($hero_config['sub_title']); ?>
                            </span>
                        </h1>
                        
                        <p class="gih-description" itemprop="description">
                            <?php echo gih_safe_output($hero_config['description']); ?>
                        </p>
                        
                        <ul class="gih-features" aria-label="主な特徴">
                            <li class="gih-feature-item">
                                <svg class="gih-feature-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                                </svg>
                                <span>全国の補助金・助成金を網羅</span>
                            </li>
                            <li class="gih-feature-item">
                                <svg class="gih-feature-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                                </svg>
                                <span>業種別・地域別の最適マッチング</span>
                            </li>
                            <li class="gih-feature-item">
                                <svg class="gih-feature-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                                </svg>
                                <span>専門家による申請サポート</span>
                            </li>
                        </ul>
                        
                        <div class="gih-cta-group">
                            <a href="<?php echo esc_url($hero_config['cta_primary_url']); ?>" 
                               class="gih-btn gih-btn-primary"
                               aria-label="無料で助成金を探す">
                                <svg class="gih-btn-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" fill="currentColor"/>
                                </svg>
                                <span><?php echo gih_safe_output($hero_config['cta_primary_text']); ?></span>
                            </a>
                            
                            <a href="<?php echo esc_url($hero_config['cta_secondary_url']); ?>" 
                               class="gih-btn gih-btn-secondary"
                               aria-label="無料診断を受ける">
                                <svg class="gih-btn-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" fill="currentColor"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" fill="currentColor"/>
                                </svg>
                                <span><?php echo gih_safe_output($hero_config['cta_secondary_text']); ?></span>
                            </a>
                        </div>
                    </article>
                    
                    <aside class="gih-content-right" aria-label="システムイメージ">
                        <figure class="gih-image-wrapper">
                            <img src="<?php echo esc_url($hero_config['hero_image']); ?>" 
                                 alt="補助金・助成金AI検索システムのインターフェース"
                                 class="gih-hero-image"
                                 width="1200"
                                 height="800"
                                 loading="eager"
                                 fetchpriority="high"
                                 decoding="async"
                                 itemprop="image">
                        </figure>
                    </aside>
                </div>
            </div>
            
            <div class="gih-mobile-layout">
                
                <h1 class="gih-mobile-title" itemprop="headline">
                    <span class="gih-mobile-title-main">
                        <?php echo gih_safe_output($hero_config['main_title']); ?>
                    </span>
                    <span class="gih-mobile-title-highlight">
                        <?php echo gih_safe_output($hero_config['sub_title']); ?>
                    </span>
                </h1>
                
                <p class="gih-mobile-description" itemprop="description">
                    最新AIテクノロジーがあなたのビジネスに最適な補助金・助成金を効率的に検索。専門家による充実したサポートで成功まで導きます。
                </p>
                
                <ul class="gih-mobile-features" aria-label="主な特徴">
                    <li class="gih-mobile-feature-item">
                        <svg class="gih-mobile-feature-icon" width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                        </svg>
                        <span>全国の補助金・助成金を網羅</span>
                    </li>
                    <li class="gih-mobile-feature-item">
                        <svg class="gih-mobile-feature-icon" width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                        </svg>
                        <span>業種・地域別マッチング</span>
                    </li>
                    <li class="gih-mobile-feature-item">
                        <svg class="gih-mobile-feature-icon" width="16" height="16" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
                        </svg>
                        <span>専門家サポート完備</span>
                    </li>
                </ul>
                
                <figure class="gih-mobile-image">
                    <img src="<?php echo esc_url($hero_config['hero_image']); ?>" 
                         alt="補助金・助成金AI検索システム"
                         width="800"
                         height="600"
                         loading="eager"
                         fetchpriority="high"
                         decoding="async"
                         itemprop="image">
                </figure>
                
                <div class="gih-mobile-cta-group">
                    <a href="<?php echo esc_url($hero_config['cta_primary_url']); ?>" 
                       class="gih-mobile-btn gih-mobile-btn-primary"
                       aria-label="無料で助成金を探す">
                        <svg class="gih-mobile-btn-icon" width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" fill="currentColor"/>
                        </svg>
                        <span><?php echo gih_safe_output($hero_config['cta_primary_text']); ?></span>
                    </a>
                    
                    <a href="<?php echo esc_url($hero_config['cta_secondary_url']); ?>" 
                       class="gih-mobile-btn gih-mobile-btn-secondary"
                       aria-label="無料診断を受ける">
                        <svg class="gih-mobile-btn-icon" width="18" height="18" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" fill="currentColor"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" fill="currentColor"/>
                        </svg>
                        <span><?php echo gih_safe_output($hero_config['cta_secondary_text']); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    
</main>

<style>
/* ============================================
   Hero Section - Vertically Centered Ultimate
   v40.0-centered-ultimate
   ============================================ */

:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-accent: #ffeb3b;
    --color-accent-hover: #ffc107;
    --color-text-primary: #000000;
    --color-text-secondary: #666666;
    --color-border: #e5e5e5;
    --font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    --transition: 0.3s ease;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 6px 16px rgba(0, 0, 0, 0.15);
}

/* Base Settings - Centered */
.gih-hero-section {
    position: relative;
    background: #ffffff;
    font-family: var(--font-family);
    overflow: hidden;
    min-height: calc(100vh - 56px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 0;
}

/* Grid Pattern Background */
.gih-hero-section::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: 
        linear-gradient(0deg, rgba(0,0,0,.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,.02) 1px, transparent 1px);
    background-size: 20px 20px;
    pointer-events: none;
    opacity: 0.5;
    z-index: 1;
}

.gih-container {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Desktop Layout */
.gih-desktop-layout {
    display: none;
}

@media (min-width: 1024px) {
    .gih-desktop-layout {
        display: block;
    }
}

.gih-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

/* Left Content */
.gih-content-left {
    display: flex;
    flex-direction: column;
    gap: 28px;
}

/* Title */
.gih-title {
    margin: 0;
    line-height: 1.2;
}

.gih-title-main {
    display: block;
    font-size: 28px;
    font-weight: 400;
    color: var(--color-text-secondary);
    margin-bottom: 8px;
}

.gih-title-highlight {
    display: block;
    font-size: 48px;
    font-weight: 900;
    color: var(--color-primary);
    position: relative;
    line-height: 1.1;
}

.gih-title-highlight::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 12px;
    background: var(--color-accent);
    z-index: -1;
}

/* Description */
.gih-description {
    font-size: 16px;
    line-height: 1.7;
    color: var(--color-text-secondary);
    font-weight: 400;
    margin: 0;
}

/* Features */
.gih-features {
    display: flex;
    flex-direction: column;
    gap: 12px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.gih-feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    color: var(--color-text-primary);
    font-weight: 500;
}

.gih-feature-icon {
    flex-shrink: 0;
    color: var(--color-accent);
    background: var(--color-primary);
    border-radius: 50%;
    padding: 3px;
}

/* CTA Buttons */
.gih-cta-group {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.gih-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 28px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: all var(--transition);
    box-shadow: var(--shadow-md);
    cursor: pointer;
}

.gih-btn-primary {
    background: var(--color-accent);
    color: var(--color-primary);
    border: 2px solid var(--color-accent);
}

.gih-btn-primary:hover {
    background: var(--color-accent-hover);
    border-color: var(--color-accent-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.gih-btn-secondary {
    background: var(--color-secondary);
    color: var(--color-primary);
    border: 2px solid var(--color-border);
}

.gih-btn-secondary:hover {
    background: #f5f7fa;
    border-color: var(--color-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.gih-btn:active {
    transform: translateY(0);
}

.gih-btn:focus {
    outline: 3px solid var(--color-accent);
    outline-offset: 2px;
}

.gih-btn-icon {
    flex-shrink: 0;
}

/* Right Image */
.gih-content-right {
    position: relative;
}

.gih-image-wrapper {
    position: relative;
    width: 100%;
    margin: 0;
}

.gih-hero-image {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
}

/* Mobile Layout */
.gih-mobile-layout {
    display: block;
    text-align: center;
}

@media (min-width: 1024px) {
    .gih-mobile-layout {
        display: none;
    }
}

/* Mobile Title */
.gih-mobile-title {
    margin: 0 0 20px 0;
    line-height: 1.2;
}

.gih-mobile-title-main {
    display: block;
    font-size: 24px;
    font-weight: 400;
    color: var(--color-text-secondary);
    margin-bottom: 8px;
}

.gih-mobile-title-highlight {
    display: block;
    font-size: 36px;
    font-weight: 900;
    color: var(--color-primary);
    position: relative;
    line-height: 1.1;
}

.gih-mobile-title-highlight::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 10px;
    background: var(--color-accent);
    z-index: -1;
}

/* Mobile Description */
.gih-mobile-description {
    font-size: 15px;
    line-height: 1.7;
    color: var(--color-text-secondary);
    margin: 0 0 24px 0;
}

/* Mobile Features */
.gih-mobile-features {
    display: flex;
    flex-direction: column;
    gap: 10px;
    list-style: none;
    margin: 0 0 24px 0;
    padding: 0;
    text-align: left;
}

.gih-mobile-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: var(--color-text-primary);
    font-weight: 500;
}

.gih-mobile-feature-icon {
    flex-shrink: 0;
    color: var(--color-accent);
    background: var(--color-primary);
    border-radius: 50%;
    padding: 2px;
}

/* Mobile Image */
.gih-mobile-image {
    width: 100%;
    margin: 24px 0;
}

.gih-mobile-image img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
}

/* Mobile CTA */
.gih-mobile-cta-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 24px;
}

.gih-mobile-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 16px 24px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: all var(--transition);
    box-shadow: var(--shadow-md);
}

.gih-mobile-btn-primary {
    background: var(--color-accent);
    color: var(--color-primary);
    border: 2px solid var(--color-accent);
}

.gih-mobile-btn-primary:active {
    transform: scale(0.98);
    background: var(--color-accent-hover);
}

.gih-mobile-btn-secondary {
    background: var(--color-secondary);
    color: var(--color-primary);
    border: 2px solid var(--color-border);
}

.gih-mobile-btn-secondary:active {
    transform: scale(0.98);
    background: #f5f7fa;
}

.gih-mobile-btn:focus {
    outline: 3px solid var(--color-accent);
    outline-offset: 2px;
}

.gih-mobile-btn-icon {
    flex-shrink: 0;
}

/* Tablet Adjustments */
@media (min-width: 768px) and (max-width: 1023px) {
    .gih-hero-section {
        min-height: calc(100vh - 56px);
        padding: 80px 0;
    }
    .gih-mobile-title-main { font-size: 28px; }
    .gih-mobile-title-highlight { font-size: 42px; }
    .gih-mobile-description { font-size: 16px; }
}

/* Small Mobile */
@media (max-width: 640px) {
    .gih-hero-section {
        min-height: auto;
        padding: 60px 0;
    }
    .gih-container { padding: 0 16px; }
    .gih-mobile-title-main { font-size: 22px; }
    .gih-mobile-title-highlight { font-size: 32px; }
    .gih-mobile-description { font-size: 14px; }
}

/* Large Desktop */
@media (min-width: 1400px) {
    .gih-content-grid { gap: 80px; }
    .gih-title-main { font-size: 32px; }
    .gih-title-highlight { font-size: 56px; }
    .gih-description { font-size: 18px; }
}

/* Short Viewport */
@media (max-height: 700px) and (min-width: 1024px) {
    .gih-hero-section {
        min-height: auto;
        padding: 80px 0;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    class GrantHeroSystem {
        constructor() {
            this.init();
        }
        
        init() {
            this.setupImageOptimization();
            this.setupAccessibility();
            this.setupCTATracking();
            console.log('[✓] Hero System v40.0 - Initialized');
        }
        
        setupImageOptimization() {
            const images = document.querySelectorAll('.gih-hero-image, .gih-mobile-image img');
            
            images.forEach(img => {
                if (img.complete) {
                    img.style.opacity = '1';
                } else {
                    img.addEventListener('load', () => {
                        img.style.opacity = '1';
                    }, { once: true });
                }
            });
        }
        
        setupAccessibility() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    document.body.classList.add('keyboard-nav');
                }
            });
            
            document.addEventListener('mousedown', () => {
                document.body.classList.remove('keyboard-nav');
            });
        }
        
        setupCTATracking() {
            const ctaButtons = document.querySelectorAll('.gih-btn, .gih-mobile-btn');
            
            ctaButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const buttonText = btn.querySelector('span')?.textContent || 'Unknown';
                    
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'cta_click', {
                            'event_category': 'engagement',
                            'event_label': buttonText
                        });
                    }
                });
            });
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.grantHeroSystem = new GrantHeroSystem();
        });
    } else {
        window.grantHeroSystem = new GrantHeroSystem();
    }
    
})();
</script>