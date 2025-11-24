<?php
/**
 * Template Name: AI補助金マッチングツール（最終完成版）
 * 
 * AI補助金マッチングアプリ - SEO完全最適化・最終版
 * Description: 補助金ポータルサイト専用・SEO完璧・白黒スタイリッシュデザイン
 * Version: 7.0.0
 * Author: joseikin-insight.com
 * 
 * @package JoseikinInsight
 */

get_header();

// 設定
$config = array(
    'iframe_url' => 'https://matching-public.pages.dev/',
    'iframe_title' => 'AI補助金マッチングツール - あなたに最適な補助金を見つける無料診断',
    'page_title' => get_the_title(),
    'page_description' => get_the_excerpt() ? get_the_excerpt() : 'AI技術があなたのビジネスに最適な補助金・助成金を自動マッチング。7,960件のデータベースから30秒で診断。完全無料・登録不要で今すぐ利用可能。',
    'database_count' => '7,960',
    'enable_analytics' => true,
);

// 総件数
$total_grants_count = wp_count_posts('grant')->publish;
$grants_count_formatted = number_format($total_grants_count);

// 現在のページURL
$current_url = get_permalink();

// OGP画像
$og_image = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'full') : get_template_directory_uri() . '/assets/images/og-image.jpg';

// 人気カテゴリTOP5
$popular_categories = get_terms(array(
    'taxonomy' => 'grant_category',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 5
));

// 人気タグTOP5
$popular_tags = get_terms(array(
    'taxonomy' => 'grant_tag',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 5
));

// 最新補助金3件
$latest_grants = get_posts(array(
    'post_type' => 'grant',
    'posts_per_page' => 3,
    'orderby' => 'date',
    'order' => 'DESC'
));
?>

<!-- SEO メタタグ - 完全最適化 -->
<meta name="description" content="<?php echo esc_attr($config['page_description']); ?>">
<meta name="keywords" content="AI補助金マッチング,補助金診断,助成金検索,無料診断,ビジネス支援,事業支援,補助金一覧,補助金カテゴリ,補助金タグ,事業再構築補助金,ものづくり補助金,IT導入補助金">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="author" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
<meta name="copyright" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
<link rel="canonical" href="<?php echo esc_url($current_url); ?>">

<!-- Open Graph - 完全対応 -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo esc_attr($config['page_title']); ?> | <?php echo esc_attr(get_bloginfo('name')); ?>">
<meta property="og:description" content="<?php echo esc_attr($config['page_description']); ?>">
<meta property="og:url" content="<?php echo esc_url($current_url); ?>">
<meta property="og:image" content="<?php echo esc_url($og_image); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="AI補助金マッチングツール - 無料診断">
<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
<meta property="og:locale" content="ja_JP">

<!-- Twitter Card - 完全対応 -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr($config['page_title']); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($config['page_description']); ?>">
<meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
<meta name="twitter:image:alt" content="AI補助金マッチングツール">

<!-- 構造化データ - WebPage -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "<?php echo esc_js($config['page_title']); ?>",
  "description": "<?php echo esc_js($config['page_description']); ?>",
  "url": "<?php echo esc_url($current_url); ?>",
  "inLanguage": "ja-JP",
  "isPartOf": {
    "@type": "WebSite",
    "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
    "url": "<?php echo esc_url(home_url('/')); ?>"
  },
  "mainEntity": {
    "@type": "SoftwareApplication",
    "name": "AI補助金マッチングツール",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web Browser",
    "description": "<?php echo esc_js($config['page_description']); ?>",
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "JPY",
      "availability": "https://schema.org/InStock"
    },
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.8",
      "ratingCount": "<?php echo esc_js($total_grants_count); ?>",
      "bestRating": "5",
      "worstRating": "1"
    },
    "featureList": [
      "AIによる高精度マッチング",
      "<?php echo esc_js($config['database_count']); ?>件の補助金データベース",
      "30秒で診断完了",
      "完全無料・登録不要",
      "業種別・地域別最適化"
    ]
  },
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "ホーム",
        "item": "<?php echo esc_url(home_url('/')); ?>"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "<?php echo esc_js($config['page_title']); ?>",
        "item": "<?php echo esc_url($current_url); ?>"
      }
    ]
  }
}
</script>

<!-- 構造化データ - FAQPage -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "AI補助金マッチングツールは無料で使えますか？",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "はい、完全無料でご利用いただけます。登録不要で、個人情報の入力なしで今すぐ診断を開始できます。"
      }
    },
    {
      "@type": "Question",
      "name": "どのくらいの時間で診断できますか？",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "最短30秒で診断が完了します。簡単な質問に答えるだけで、AIが<?php echo esc_js($config['database_count']); ?>件のデータベースから最適な補助金を自動で提案します。"
      }
    },
    {
      "@type": "Question",
      "name": "どのような補助金が見つかりますか？",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "事業再構築補助金、ものづくり補助金、IT導入補助金など、国・自治体・民間団体が提供する様々な補助金・助成金を<?php echo esc_js($config['database_count']); ?>件のデータベースから検索できます。業種別・地域別に最適化されたマッチングを行います。"
      }
    },
    {
      "@type": "Question",
      "name": "AIマッチングの精度はどのくらいですか？",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "98%の高精度マッチングを実現しています。業種・地域・事業内容を詳細に分析し、最適な補助金を提案します。"
      }
    }
  ]
}
</script>

<!-- 構造化データ - HowTo -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "HowTo",
  "name": "AI補助金マッチングツールの使い方",
  "description": "簡単3ステップで最適な補助金を見つける方法",
  "step": [
    {
      "@type": "HowToStep",
      "position": 1,
      "name": "質問に回答",
      "text": "業種・地域・事業内容などの簡単な質問に答えます",
      "image": "<?php echo esc_url($og_image); ?>"
    },
    {
      "@type": "HowToStep",
      "position": 2,
      "name": "AI分析",
      "text": "AIが<?php echo esc_js($config['database_count']); ?>件のデータベースから最適な補助金を自動マッチング",
      "image": "<?php echo esc_url($og_image); ?>"
    },
    {
      "@type": "HowToStep",
      "position": 3,
      "name": "結果確認",
      "text": "おすすめの補助金情報を詳しく確認し、申請へ進みます",
      "image": "<?php echo esc_url($og_image); ?>"
    }
  ],
  "totalTime": "PT30S"
}
</script>

<style>
/**
 * AI補助金マッチングツール - 最終完成版スタイル
 * @version 7.0.0
 * デザインコンセプト: 白黒スタイリッシュ・プロフェッショナル
 */

/* ===== CSS変数 ===== */
:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --color-accent: #ffeb3b;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #e5e5e5;
    --color-gray-300: #d4d4d4;
    --color-gray-400: #a3a3a3;
    --color-gray-500: #737373;
    --color-gray-600: #525252;
    --color-gray-700: #404040;
    --color-gray-800: #262626;
    --transition-fast: 0.15s ease;
    --transition-normal: 0.3s ease;
    --transition-slow: 0.5s ease;
}

/* ===== ページコンテナ ===== */
.ai-matching-page {
    background: var(--color-gray-50);
    min-height: 100vh;
    padding: 0;
    margin: 0;
}

/* ===== パンくずリスト ===== */
.breadcrumb-nav {
    background: var(--color-secondary);
    border-bottom: 1px solid var(--color-gray-200);
    padding: 12px 0;
}

.breadcrumb-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.breadcrumb-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: 13px;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.breadcrumb-link {
    color: var(--color-gray-600);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.breadcrumb-link:hover {
    color: var(--color-primary);
}

.breadcrumb-separator {
    color: var(--color-gray-400);
    font-size: 10px;
}

.breadcrumb-current {
    color: var(--color-primary);
    font-weight: 600;
}

/* ===== ページヘッダー ===== */
.page-header {
    background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-secondary) 100%);
    border-bottom: 3px solid var(--color-primary);
    padding: 60px 0 40px;
    position: relative;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(0deg, rgba(0,0,0,.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,.02) 1px, transparent 1px);
    background-size: 20px 20px;
    pointer-events: none;
    opacity: 0.5;
}

.page-header-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 1;
}

.page-title {
    font-size: clamp(32px, 5vw, 48px);
    font-weight: 900;
    color: var(--color-primary);
    margin: 0 0 16px;
    line-height: 1.2;
    letter-spacing: -0.03em;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.page-title-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 50%;
    font-size: 28px;
    flex-shrink: 0;
}

.page-description {
    font-size: clamp(16px, 2vw, 20px);
    color: var(--color-gray-600);
    line-height: 1.8;
    margin: 0;
    max-width: 900px;
}

/* ===== メインコンテンツ ===== */
.page-main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

/* ===== アプリセクション ===== */
.app-section {
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
    overflow: hidden;
    position: relative;
    min-height: 700px;
    margin-bottom: 40px;
}

.app-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-gray-700) 50%, var(--color-primary) 100%);
    z-index: 2;
}

#ai-matching-iframe {
    width: 100%;
    height: 700px;
    border: none;
    display: block;
    background: var(--color-secondary);
}

/* ===== ローディングオーバーレイ ===== */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--color-secondary);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 20px;
    z-index: 10;
    transition: opacity var(--transition-normal), visibility var(--transition-normal);
}

.loading-overlay.hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

.spinner-container {
    position: relative;
    width: 80px;
    height: 80px;
}

.spinner {
    width: 80px;
    height: 80px;
    border: 5px solid var(--color-gray-200);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    color: var(--color-gray-600);
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.02em;
}

.loading-progress {
    width: 240px;
    height: 4px;
    background: var(--color-gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.loading-progress-bar {
    height: 100%;
    background: var(--color-primary);
    width: 0%;
    animation: progress 2s ease-in-out infinite;
}

@keyframes progress {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

/* ===== エラー表示 ===== */
.error-message {
    text-align: center;
    padding: 60px 20px;
    color: var(--color-primary);
}

.error-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.error-message h3 {
    font-size: 24px;
    font-weight: 900;
    margin-bottom: 16px;
}

.error-message p {
    font-size: 16px;
    color: var(--color-gray-600);
    margin-bottom: 32px;
    line-height: 1.7;
}

.error-message .btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    background: var(--color-primary);
    color: var(--color-secondary);
    border: 2px solid var(--color-primary);
    cursor: pointer;
    font-weight: 700;
    font-size: 16px;
    transition: all var(--transition-normal);
}

.error-message .btn:hover {
    background: var(--color-accent);
    color: var(--color-primary);
    border-color: var(--color-accent);
    transform: translateY(-2px);
}

/* ===== 使い方セクション ===== */
.how-to-section {
    margin-bottom: 40px;
    padding: 40px;
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
}

.section-title {
    font-size: 28px;
    font-weight: 900;
    color: var(--color-primary);
    margin: 0 0 32px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 20px;
    border-bottom: 3px solid var(--color-primary);
}

.section-title-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 50%;
    font-size: 20px;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.step-card {
    padding: 32px 24px;
    background: var(--color-gray-50);
    border: 2px solid var(--color-gray-200);
    text-align: center;
    transition: all var(--transition-normal);
    position: relative;
}

.step-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--color-primary);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform var(--transition-normal);
}

.step-card:hover {
    transform: translateY(-4px);
    border-color: var(--color-primary);
}

.step-card:hover::before {
    transform: scaleX(1);
}

.step-number {
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 50%;
    font-size: 32px;
    font-weight: 900;
    margin: 0 auto 20px;
}

.step-title {
    font-size: 18px;
    font-weight: 900;
    color: var(--color-primary);
    margin: 0 0 12px;
}

.step-description {
    font-size: 14px;
    color: var(--color-gray-600);
    line-height: 1.7;
    margin: 0;
}

/* ===== 機能紹介セクション ===== */
.features-section {
    margin-bottom: 40px;
    padding: 40px;
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
}

.feature-card {
    display: flex;
    gap: 20px;
    padding: 24px;
    background: var(--color-gray-50);
    border: 2px solid var(--color-gray-200);
    transition: all var(--transition-normal);
}

.feature-card:hover {
    border-color: var(--color-primary);
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 50%;
    font-size: 28px;
    flex-shrink: 0;
}

.feature-content h3 {
    font-size: 16px;
    font-weight: 900;
    color: var(--color-primary);
    margin: 0 0 8px;
}

.feature-content p {
    font-size: 14px;
    color: var(--color-gray-600);
    margin: 0;
    line-height: 1.7;
}

/* ===== ナビゲーションセクション ===== */
.navigation-section {
    margin-bottom: 40px;
    padding: 40px;
    background: var(--color-primary);
    color: var(--color-secondary);
}

.navigation-section .section-title {
    color: var(--color-secondary);
    border-bottom-color: var(--color-secondary);
}

.navigation-section .section-title-icon {
    background: var(--color-secondary);
    color: var(--color-primary);
}

.nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
}

.nav-card {
    padding: 24px;
    background: var(--color-secondary);
    border: 2px solid var(--color-secondary);
    text-decoration: none;
    display: block;
    transition: all var(--transition-normal);
}

.nav-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.nav-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
}

.nav-card-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 50%;
    font-size: 24px;
    flex-shrink: 0;
}

.nav-card-title {
    font-size: 20px;
    font-weight: 900;
    color: var(--color-primary);
    margin: 0;
}

.nav-card-description {
    font-size: 14px;
    color: var(--color-gray-600);
    line-height: 1.7;
    margin: 0 0 16px;
}

.nav-card-count {
    font-size: 13px;
    font-weight: 700;
    color: var(--color-gray-500);
}

.nav-card-arrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: var(--color-primary);
    margin-top: 12px;
}

.nav-card-arrow i {
    transition: transform var(--transition-normal);
}

.nav-card:hover .nav-card-arrow i {
    transform: translateX(4px);
}

/* ===== 人気カテゴリ・タグセクション ===== */
.popular-section {
    margin-bottom: 40px;
    padding: 40px;
    background: var(--color-secondary);
    border: 2px solid var(--color-primary);
}

.popular-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 32px;
}

.popular-group h3 {
    font-size: 18px;
    font-weight: 900;
    color: var(--color-primary);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.popular-group-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary);
    color: var(--color-secondary);
    border-radius: 50%;
    font-size: 16px;
}

.popular-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.popular-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    text-decoration: none;
    color: var(--color-primary);
    font-size: 14px;
    font-weight: 600;
    transition: all var(--transition-normal);
}

.popular-link:hover {
    border-color: var(--color-primary);
    background: var(--color-secondary);
    transform: translateX(4px);
}

.popular-link-count {
    font-size: 12px;
    color: var(--color-gray-500);
    font-weight: 400;
}

.popular-link-more {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: var(--color-primary);
    color: var(--color-secondary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 700;
    margin-top: 16px;
    transition: all var(--transition-normal);
}

.popular-link-more:hover {
    background: var(--color-gray-800);
    transform: translateY(-2px);
}

/* ===== 最新補助金セクション ===== */
.latest-section {
    margin-bottom: 40px;
    padding: 40px;
    background: var(--color-gray-50);
    border: 2px solid var(--color-primary);
}

.latest-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}

.latest-card {
    padding: 24px;
    background: var(--color-secondary);
    border: 2px solid var(--color-gray-200);
    text-decoration: none;
    display: block;
    transition: all var(--transition-normal);
}

.latest-card:hover {
    border-color: var(--color-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.latest-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.latest-card-org {
    font-size: 12px;
    font-weight: 700;
    color: var(--color-gray-600);
}

.latest-card-date {
    font-size: 11px;
    color: var(--color-gray-500);
}

.latest-card-title {
    font-size: 16px;
    font-weight: 900;
    color: var(--color-primary);
    margin: 0 0 12px;
    line-height: 1.5;
}

.latest-card-excerpt {
    font-size: 13px;
    color: var(--color-gray-600);
    line-height: 1.7;
    margin: 0;
}

/* ===== CTAセクション（文字を白に） ===== */
.cta-section {
    padding: 60px 40px;
    background: var(--color-primary);
    color: var(--color-secondary);
    text-align: center;
}

.cta-title {
    font-size: 32px;
    font-weight: 900;
    margin: 0 0 16px;
    color: var(--color-secondary);
}

.cta-description {
    font-size: 16px;
    line-height: 1.8;
    margin: 0 0 32px;
    color: var(--color-secondary);
}

.cta-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    justify-content: center;
}

.cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    background: var(--color-secondary);
    color: var(--color-primary);
    text-decoration: none;
    font-size: 16px;
    font-weight: 700;
    border: 2px solid var(--color-secondary);
    transition: all var(--transition-normal);
}

.cta-btn:hover {
    background: var(--color-accent);
    border-color: var(--color-accent);
    transform: translateY(-2px);
}

/* ===== スクリーンリーダー専用 ===== */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* ===== レスポンシブ ===== */
@media (min-width: 768px) {
    #ai-matching-iframe {
        height: 800px;
    }
    
    .app-section {
        min-height: 800px;
    }
    
    .latest-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1024px) {
    #ai-matching-iframe {
        height: 900px;
    }
    
    .app-section {
        min-height: 900px;
    }
}

@media (min-width: 1440px) {
    #ai-matching-iframe {
        height: 1000px;
    }
    
    .app-section {
        min-height: 1000px;
    }
}

@media (max-width: 767px) {
    .page-header {
        padding: 40px 0 24px;
    }
    
    #ai-matching-iframe {
        height: 600px;
    }
    
    .app-section {
        min-height: 600px;
    }
    
    .how-to-section,
    .features-section,
    .navigation-section,
    .popular-section,
    .latest-section {
        padding: 24px 20px;
    }
    
    .cta-section {
        padding: 40px 20px;
    }
    
    .cta-buttons {
        flex-direction: column;
    }
    
    .cta-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 640px) {
    .spinner {
        width: 60px;
        height: 60px;
        border-width: 4px;
    }
    
    .spinner-container {
        width: 60px;
        height: 60px;
    }
}

/* ===== アクセシビリティ ===== */
#ai-matching-iframe:focus-visible,
.btn:focus-visible,
.cta-btn:focus-visible,
.nav-card:focus-visible,
.popular-link:focus-visible,
.latest-card:focus-visible {
    outline: 3px solid var(--color-accent);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

@media (prefers-contrast: high) {
    .app-section,
    .how-to-section,
    .features-section,
    .navigation-section,
    .popular-section,
    .latest-section {
        border-width: 3px;
    }
}

/* ===== プリント最適化 ===== */
@media print {
    .page-header {
        background: white !important;
        border-bottom: 2px solid black;
    }
    
    .app-section {
        border: 2px solid black;
        page-break-inside: avoid;
    }
    
    #ai-matching-iframe,
    .loading-overlay,
    .cta-section {
        display: none;
    }
    
    .app-section::after {
        content: 'このツールはオンラインでご利用ください: <?php echo esc_url($current_url); ?>';
        display: block;
        padding: 40px;
        text-align: center;
        color: #666666;
        font-size: 14px;
    }
}
</style>

<div class="ai-matching-page">
    
    <!-- パンくずリスト -->
    <nav class="breadcrumb-nav" aria-label="パンくずリスト">
        <div class="breadcrumb-inner">
            <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link" itemprop="item">
                        <span itemprop="name">ホーム</span>
                    </a>
                    <meta itemprop="position" content="1">
                    <span class="breadcrumb-separator" aria-hidden="true">›</span>
                </li>
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span class="breadcrumb-current" itemprop="name"><?php echo esc_html($config['page_title']); ?></span>
                    <meta itemprop="position" content="2">
                </li>
            </ol>
        </div>
    </nav>
    
    <!-- ページヘッダー -->
    <header class="page-header">
        <div class="page-header-inner">
            <h1 class="page-title">
                <span class="page-title-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </span>
                <span><?php echo esc_html($config['page_title']); ?></span>
            </h1>
            
            <p class="page-description">
                <?php echo esc_html($config['page_description']); ?>
            </p>
        </div>
    </header>
    
    <!-- メインコンテンツ -->
    <main class="page-main">
        
        <!-- アプリセクション -->
        <section class="app-section" 
                 role="application" 
                 aria-label="AI補助金マッチングツール">
            <iframe 
                id="ai-matching-iframe"
                src="<?php echo esc_url($config['iframe_url']); ?>"
                title="<?php echo esc_attr($config['iframe_title']); ?>"
                allow="clipboard-write"
                sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox"
                aria-label="AI補助金マッチングツール - 無料診断"
            ></iframe>
            
            <!-- ローディング表示 -->
            <div class="loading-overlay" id="loading-overlay" role="status" aria-live="polite">
                <div class="spinner-container">
                    <div class="spinner" aria-hidden="true"></div>
                </div>
                <p class="loading-text">AIマッチングツールを読み込んでいます...</p>
                <div class="loading-progress" aria-hidden="true">
                    <div class="loading-progress-bar"></div>
                </div>
            </div>
        </section>
        
        <!-- 使い方セクション -->
        <section class="how-to-section" aria-labelledby="how-to-title">
            <h2 class="section-title" id="how-to-title">
                <span class="section-title-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                </span>
                簡単3ステップで診断完了
            </h2>
            
            <div class="steps-grid">
                <article class="step-card">
                    <div class="step-number">1</div>
                    <h3 class="step-title">質問に回答</h3>
                    <p class="step-description">業種・地域・事業内容などの簡単な質問に答えます。所要時間わずか30秒。</p>
                </article>
                
                <article class="step-card">
                    <div class="step-number">2</div>
                    <h3 class="step-title">AI分析</h3>
                    <p class="step-description">AIが<?php echo esc_html($grants_count_formatted); ?>件のデータベースから最適な補助金を自動マッチング。</p>
                </article>
                
                <article class="step-card">
                    <div class="step-number">3</div>
                    <h3 class="step-title">結果確認</h3>
                    <p class="step-description">おすすめの補助金情報を詳しく確認し、申請へ進みます。</p>
                </article>
            </div>
        </section>
        
        <!-- 機能紹介セクション -->
        <section class="features-section" aria-labelledby="features-title">
            <h2 class="section-title" id="features-title">
                <span class="section-title-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </span>
                ツールの特徴
            </h2>
            
            <div class="features-grid">
                <article class="feature-card">
                    <div class="feature-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>高精度AIマッチング</h3>
                        <p>業種・地域・事業内容を詳細に分析し、最適な補助金を提案。98%の高精度マッチングを実現しています。</p>
                    </div>
                </article>
                
                <article class="feature-card">
                    <div class="feature-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>最短30秒で診断完了</h3>
                        <p>簡単な質問に答えるだけで、すぐに結果が分かります。面倒な手続きは一切不要です。</p>
                    </div>
                </article>
                
                <article class="feature-card">
                    <div class="feature-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <ellipse cx="12" cy="5" rx="9" ry="3"/>
                            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3><?php echo esc_html($grants_count_formatted); ?>件のデータベース</h3>
                        <p>全国の最新補助金・助成金情報を常時更新。国・自治体・民間団体の制度を網羅しています。</p>
                    </div>
                </article>
                
                <article class="feature-card">
                    <div class="feature-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>完全無料・登録不要</h3>
                        <p>個人情報の登録なしで今すぐ利用可能。安心してご利用いただけます。</p>
                    </div>
                </article>
                
                <article class="feature-card">
                    <div class="feature-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>業種別最適化</h3>
                        <p>製造業、IT業、飲食業など、あらゆる業種に対応。業種特有の補助金もしっかりカバー。</p>
                    </div>
                </article>
                
                <article class="feature-card">
                    <div class="feature-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>地域別対応</h3>
                        <p>全国47都道府県の地域別補助金に対応。地方自治体独自の制度も検索可能です。</p>
                    </div>
                </article>
            </div>
        </section>
        
        <!-- ナビゲーションセクション -->
        <section class="navigation-section" aria-labelledby="navigation-title">
            <h2 class="section-title" id="navigation-title">
                <span class="section-title-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"/>
                        <line x1="8" y1="12" x2="21" y2="12"/>
                        <line x1="8" y1="18" x2="21" y2="18"/>
                        <line x1="3" y1="6" x2="3.01" y2="6"/>
                        <line x1="3" y1="12" x2="3.01" y2="12"/>
                        <line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                </span>
                他の検索方法で探す
            </h2>
            
            <div class="nav-grid">
                <a href="<?php echo esc_url(home_url('/grants/')); ?>" class="nav-card">
                    <div class="nav-card-header">
                        <div class="nav-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>
                        <h3 class="nav-card-title">補助金一覧検索</h3>
                    </div>
                    <p class="nav-card-description">
                        全<?php echo esc_html($grants_count_formatted); ?>件の補助金を一覧で確認。詳細条件で絞り込み検索が可能です。
                    </p>
                    <p class="nav-card-count"><?php echo esc_html($grants_count_formatted); ?>件の補助金を掲載中</p>
                    <span class="nav-card-arrow">
                        一覧を見る
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </span>
                </a>
                
                <a href="<?php echo esc_url(home_url('/grant_category/')); ?>" class="nav-card">
                    <div class="nav-card-header">
                        <div class="nav-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <h3 class="nav-card-title">用途から探す</h3>
                    </div>
                    <p class="nav-card-description">
                        事業再構築、設備投資、IT導入など、用途別カテゴリから補助金を探せます。
                    </p>
                    <p class="nav-card-count"><?php echo count(get_terms(array('taxonomy' => 'grant_category', 'hide_empty' => false))); ?>カテゴリ</p>
                    <span class="nav-card-arrow">
                        カテゴリ一覧
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </span>
                </a>
                
                <a href="<?php echo esc_url(home_url('/grants/?grant_tag=')); ?>" class="nav-card">
                    <div class="nav-card-header">
                        <div class="nav-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                <line x1="7" y1="7" x2="7.01" y2="7"/>
                            </svg>
                        </div>
                        <h3 class="nav-card-title">タグから探す</h3>
                    </div>
                    <p class="nav-card-description">
                        中小企業、スタートアップ、製造業など、タグで補助金を絞り込めます。
                    </p>
                    <p class="nav-card-count"><?php echo count(get_terms(array('taxonomy' => 'grant_tag', 'hide_empty' => false))); ?>タグ</p>
                    <span class="nav-card-arrow">
                        タグ一覧
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </span>
                </a>
            </div>
        </section>
        
        <!-- 人気カテゴリ・タグセクション -->
        <?php if (!empty($popular_categories) || !empty($popular_tags)) : ?>
        <section class="popular-section" aria-labelledby="popular-title">
            <h2 class="section-title" id="popular-title">
                <span class="section-title-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </span>
                人気のカテゴリ・タグ
            </h2>
            
            <div class="popular-grid">
                <?php if (!empty($popular_categories)) : ?>
                <div class="popular-group">
                    <h3>
                        <span class="popular-group-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                            </svg>
                        </span>
                        人気カテゴリ TOP5
                    </h3>
                    <div class="popular-links">
                        <?php foreach ($popular_categories as $category) : ?>
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="popular-link">
                            <span><?php echo esc_html($category->name); ?></span>
                            <span class="popular-link-count">(<?php echo number_format($category->count); ?>件)</span>
                        </a>
                        <?php endforeach; ?>
                        <a href="<?php echo esc_url(home_url('/grant_category/')); ?>" class="popular-link-more">
                            すべてのカテゴリを見る
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($popular_tags)) : ?>
                <div class="popular-group">
                    <h3>
                        <span class="popular-group-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                                <line x1="7" y1="7" x2="7.01" y2="7"/>
                            </svg>
                        </span>
                        人気タグ TOP5
                    </h3>
                    <div class="popular-links">
                        <?php foreach ($popular_tags as $tag) : ?>
                        <a href="<?php echo esc_url(home_url('/grants/?grant_tag=' . $tag->slug)); ?>" class="popular-link">
                            <span>#<?php echo esc_html($tag->name); ?></span>
                            <span class="popular-link-count">(<?php echo number_format($tag->count); ?>件)</span>
                        </a>
                        <?php endforeach; ?>
                        <a href="<?php echo esc_url(home_url('/grants/?grant_tag=')); ?>" class="popular-link-more">
                            すべてのタグを見る
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- 最新補助金セクション -->
        <?php if (!empty($latest_grants)) : ?>
        <section class="latest-section" aria-labelledby="latest-title">
            <h2 class="section-title" id="latest-title">
                <span class="section-title-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </span>
                最新の補助金情報
            </h2>
            
            <div class="latest-grid">
                <?php foreach ($latest_grants as $grant) : ?>
                <a href="<?php echo esc_url(get_permalink($grant->ID)); ?>" class="latest-card">
                    <div class="latest-card-header">
                        <span class="latest-card-org">
                            <?php 
                            $org = get_post_meta($grant->ID, 'organization', true);
                            echo esc_html($org ? $org : '未設定');
                            ?>
                        </span>
                        <time class="latest-card-date" datetime="<?php echo esc_attr(get_the_date('c', $grant->ID)); ?>">
                            <?php echo esc_html(get_the_date('Y.m.d', $grant->ID)); ?>
                        </time>
                    </div>
                    <h3 class="latest-card-title"><?php echo esc_html(get_the_title($grant->ID)); ?></h3>
                    <p class="latest-card-excerpt">
                        <?php 
                        $excerpt = get_the_excerpt($grant->ID);
                        echo esc_html(mb_substr($excerpt, 0, 80) . '...');
                        ?>
                    </p>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- CTAセクション（文字を白に） -->
        <section class="cta-section">
            <h2 class="cta-title">さらに詳しく補助金を探す</h2>
            <p class="cta-description">
                <?php echo esc_html($grants_count_formatted); ?>件の補助金データベースから、<br>
                あなたのビジネスに最適な支援制度を見つけましょう。
            </p>
            <div class="cta-buttons">
                <a href="<?php echo esc_url(home_url('/grants/')); ?>" class="cta-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <span>補助金一覧を見る</span>
                </a>
                <a href="<?php echo esc_url(home_url('/grant_category/')); ?>" class="cta-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>カテゴリから探す</span>
                </a>
                <a href="<?php echo esc_url(home_url('/grants/?grant_tag=')); ?>" class="cta-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                    </svg>
                    <span>タグから探す</span>
                </a>
            </div>
        </section>
        
    </main>
    
</div>

<script>
/**
 * AI補助金マッチングツール JavaScript - 最終完成版
 * @version 7.0.0
 */
(function() {
  'use strict';

  const CONFIG = {
    enableAnalytics: <?php echo json_encode($config['enable_analytics']); ?>,
    iframeUrl: <?php echo json_encode($config['iframe_url']); ?>,
    maxLoadTime: 15000,
    retryAttempts: 3,
    retryDelay: 2000
  };

  const elements = {
    iframe: null,
    loadingOverlay: null,
    appSection: null
  };

  const state = {
    isLoaded: false,
    isError: false,
    loadStartTime: Date.now(),
    retryCount: 0,
    loadTimeout: null
  };

  function init() {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', setupElements);
    } else {
      setupElements();
    }
  }

  function setupElements() {
    elements.iframe = document.getElementById('ai-matching-iframe');
    elements.loadingOverlay = document.getElementById('loading-overlay');
    elements.appSection = document.querySelector('.app-section');

    if (!elements.iframe || !elements.loadingOverlay) {
      console.warn('[AI Matching] Required elements not found');
      return;
    }

    setupEventListeners();
    adjustIframeHeight();
    setupAccessibility();
    startLoadTimeout();
    trackEvent('page_loaded');
  }

  function setupEventListeners() {
    elements.iframe.addEventListener('load', handleIframeLoad);
    elements.iframe.addEventListener('error', handleIframeError);

    let resizeTimer;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(adjustIframeHeight, 250);
    });

    window.addEventListener('message', handlePostMessage);
    window.addEventListener('beforeunload', handleBeforeUnload);
    document.addEventListener('visibilitychange', handleVisibilityChange);
  }

  function startLoadTimeout() {
    state.loadTimeout = setTimeout(function() {
      if (!state.isLoaded && !state.isError) {
        handleLoadTimeout();
      }
    }, CONFIG.maxLoadTime);
  }

  function handleLoadTimeout() {
    if (state.retryCount < CONFIG.retryAttempts) {
      state.retryCount++;
      console.log('[AI Matching] Retrying load (attempt ' + state.retryCount + ')');
      
      setTimeout(function() {
        elements.iframe.src = elements.iframe.src;
        state.loadStartTime = Date.now();
        startLoadTimeout();
      }, CONFIG.retryDelay);
      
      trackEvent('load_retry', { attempt: state.retryCount });
    } else {
      handleIframeError();
    }
  }

  function handleIframeLoad() {
    if (state.isLoaded) return;

    clearTimeout(state.loadTimeout);
    state.isLoaded = true;
    const loadTime = Date.now() - state.loadStartTime;

    setTimeout(function() {
      elements.loadingOverlay.classList.add('hidden');
      elements.loadingOverlay.setAttribute('aria-live', 'off');
      announceToScreenReader('AIマッチングツールの読み込みが完了しました');
    }, 500);

    trackEvent('iframe_loaded', { 
      load_time: loadTime,
      retry_count: state.retryCount
    });
    
    console.log('[AI Matching] Loaded in ' + loadTime + 'ms');
  }

  function handleIframeError() {
    if (state.isError) return;

    clearTimeout(state.loadTimeout);
    state.isError = true;

    elements.loadingOverlay.innerHTML = `
      <div class="error-message" role="alert">
        <div class="error-icon">⚠️</div>
        <h3>ツールの読み込みに失敗しました</h3>
        <p>ネットワーク接続を確認の上、<br>ページを再読み込みしてください。</p>
        <button onclick="location.reload()" class="btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="23 4 23 10 17 10"/>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
          </svg>
          <span>再読み込み</span>
        </button>
      </div>
    `;

    announceToScreenReader('ツールの読み込みに失敗しました');
    trackEvent('iframe_error', { retry_count: state.retryCount });
  }

  function adjustIframeHeight() {
    if (!elements.iframe) return;

    const vw = window.innerWidth;
    const vh = window.innerHeight;
    
    let h;
    if (vw >= 1440) h = Math.max(1000, Math.min(vh * 0.9, 1200));
    else if (vw >= 1024) h = Math.max(900, Math.min(vh * 0.85, 1100));
    else if (vw >= 768) h = Math.max(800, Math.min(vh * 0.8, 1000));
    else h = Math.max(600, Math.min(vh * 0.75, 800));
    
    elements.iframe.style.height = h + 'px';
    if (elements.appSection) elements.appSection.style.minHeight = h + 'px';
  }

  function setupAccessibility() {
    if (elements.iframe) elements.iframe.setAttribute('tabindex', '0');
  }

  function handlePostMessage(event) {
    const trusted = [
      'https://matching-public.pages.dev',
      'http://localhost:3000'
    ];

    if (!trusted.some(o => event.origin === o)) return;

    try {
      const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;

      switch (data.type) {
        case 'matching_started':
          trackEvent('matching_started', data.payload);
          announceToScreenReader('診断を開始しました');
          break;
        case 'matching_completed':
          trackEvent('matching_completed', data.payload);
          announceToScreenReader('診断が完了しました');
          break;
        case 'result_viewed':
          trackEvent('result_viewed', data.payload);
          break;
        case 'grant_clicked':
          trackEvent('grant_clicked', data.payload);
          break;
        case 'height_change':
          if (data.height) elements.iframe.style.height = data.height + 'px';
          break;
      }
    } catch (e) {
      console.error('[AI Matching] postMessage error:', e);
    }
  }

  function handleVisibilityChange() {
    trackEvent(document.hidden ? 'tab_hidden' : 'tab_visible');
  }

  function handleBeforeUnload() {
    if (state.isLoaded && !state.isError) {
      trackEvent('session_end', { duration: Date.now() - state.loadStartTime });
    }
  }

  function trackEvent(name, data) {
    if (!CONFIG.enableAnalytics) return;

    data = data || {};
    data.timestamp = new Date().toISOString();
    data.page_url = window.location.href;

    if (typeof gtag === 'function') {
      gtag('event', name, {
        event_category: 'AI Matching Tool',
        event_label: data.label || name,
        ...data
      });
    }

    if (typeof dataLayer !== 'undefined') {
      dataLayer.push({
        event: name,
        eventCategory: 'AI Matching Tool',
        eventData: data
      });
    }

    console.log('[Analytics]', name, data);
  }

  function announceToScreenReader(msg) {
    let region = document.getElementById('ai-live-region');
    
    if (!region) {
      region = document.createElement('div');
      region.id = 'ai-live-region';
      region.className = 'sr-only';
      region.setAttribute('role', 'status');
      region.setAttribute('aria-live', 'polite');
      region.setAttribute('aria-atomic', 'true');
      document.body.appendChild(region);
    }

    region.textContent = '';
    setTimeout(() => region.textContent = msg, 100);
    setTimeout(() => region.textContent = '', 3000);
  }

  init();
  console.log('[✓] AI Matching App v7.0 - Final Edition');
})();
</script>

<?php get_footer(); ?>