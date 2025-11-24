<?php
/**
 * 補助金・助成金情報サイト - 当サイトについてページ
 * Grant & Subsidy Information Site - About Us Page
 * @package Grant_Insight_About
 * @version 1.1-url-fixed
 * 
 * === 主要機能 ===
 * 1. サイト概要の説明
 * 2. 運営理念とサービス内容
 * 3. 信頼性と免責事項
 * 4. SEO最適化
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// ヘルパー関数
if (!function_exists('gip_safe_output')) {
    function gip_safe_output($text, $allow_html = false) {
        return $allow_html ? wp_kses_post($text) : esc_html($text);
    }
}

get_header(); // ヘッダーを読み込み


// 構造化データ
$about_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'AboutPage',
    'name' => '当サイトについて - 補助金インサイト',
    'description' => '補助金インサイトは、全国の補助金・助成金情報を効率的に検索できるAI活用型のポータルサイトです。',
    'url' => 'https://joseikin-insight.com/about/',
    'mainEntity' => array(
        '@type' => 'Organization',
        'name' => '補助金インサイト',
        'url' => 'https://joseikin-insight.com',
        'description' => '中小企業・個人事業主・スタートアップ企業向けの補助金・助成金情報検索サービス'
    )
);
?>

<!-- SEO メタタグ -->
<title>当サイトについて | 補助金インサイト - AI活用型補助金検索ポータル</title>
<meta name="description" content="補助金インサイトは、全国の補助金・助成金情報をAIで効率的に検索できるポータルサイト。中小企業・個人事業主向けに情報提供と申請サポートを行っています。">
<meta name="keywords" content="補助金インサイト,当サイトについて,運営情報,サービス内容,信頼性,AI検索">
<link rel="canonical" href="https://joseikin-insight.com/about/">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="当サイトについて | 補助金インサイト">
<meta property="og:description" content="補助金インサイトは、全国の補助金・助成金情報をAIで効率的に検索できるポータルサイト。">
<meta property="og:url" content="https://joseikin-insight.com/about/">

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($about_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<article class="about-page" itemscope itemtype="https://schema.org/AboutPage">
    
    <!-- ページヘッダー -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title" itemprop="headline">当サイトについて</h1>
            <p class="page-subtitle">補助金インサイトのサービス概要と運営情報</p>
        </div>
    </header>
    
    <!-- メインコンテンツ -->
    <div class="page-content">
        <div class="container">
            
            <!-- 補助金インサイトとは -->
            <section class="content-section" id="about-overview">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                    </span>
                    補助金インサイトとは
                </h2>
                <div class="section-content">
                    <p class="lead-text">
                        補助金インサイトは、全国の補助金・助成金情報を効率的に検索できるAI活用型のポータルサイトです。中小企業・個人事業主・スタートアップ企業の皆様が、ビジネスに適した支援制度を見つけ、申請手続きを円滑に進められるよう、情報提供とサポートサービスを行っています。
                    </p>
                </div>
            </section>
            
            <!-- 運営理念 -->
            <section class="content-section" id="philosophy">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </span>
                    運営理念
                </h2>
                <div class="section-content">
                    <p>
                        私たちは「情報格差の解消」を通じて、日本の中小企業の成長を支援することを使命としています。従来、補助金・助成金の情報は分散しており、適切な制度を見つけることが困難でした。AIテクノロジーを活用することで、この課題を解決し、より多くの事業者が支援制度を活用できる環境づくりに貢献します。
                    </p>
                </div>
            </section>
            
            <!-- サービス内容 -->
            <section class="content-section" id="services">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M9 3v18M3 9h18M3 15h18M15 3v18"/>
                        </svg>
                    </span>
                    サービス内容
                </h2>
                <div class="section-content">
                    
                    <!-- 補助金・助成金検索機能 -->
                    <div class="service-item">
                        <h3 class="service-title">
                            <span class="service-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="M21 21l-4.35-4.35"/>
                                </svg>
                            </span>
                            補助金・助成金検索機能
                        </h3>
                        <ul class="service-list">
                            <li>業種・地域・目的別の詳細検索</li>
                            <li>AIによる条件マッチング機能</li>
                            <li>リアルタイムでの最新情報更新</li>
                        </ul>
                    </div>
                    
                    <!-- 情報提供サービス -->
                    <div class="service-item">
                        <h3 class="service-title">
                            <span class="service-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                            </span>
                            情報提供サービス
                        </h3>
                        <ul class="service-list">
                            <li>国・都道府県・市区町村の制度情報</li>
                            <li>申請要件・期限・必要書類の整理</li>
                            <li>制度変更・新設情報の通知</li>
                        </ul>
                    </div>
                    
                    <!-- 申請サポートサービス -->
                    <div class="service-item">
                        <h3 class="service-title">
                            <span class="service-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                            申請サポートサービス
                        </h3>
                        <ul class="service-list">
                            <li>申請書類作成のガイダンス</li>
                            <li>専門家による相談対応</li>
                            <li>申請手続きのフォローアップ</li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <!-- 運営体制 -->
            <section class="content-section" id="operation">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </span>
                    運営体制
                </h2>
                <div class="section-content">
                    
                    <!-- 情報収集・管理体制 -->
                    <div class="subsection">
                        <h3 class="subsection-title">情報収集・管理体制</h3>
                        <ul class="info-list">
                            <li>各省庁・自治体の公式情報を定期的に収集</li>
                            <li>専門スタッフによる情報の精査・更新</li>
                            <li>複数のソースからの情報照合による正確性確保</li>
                        </ul>
                    </div>
                    
                    <!-- 専門チーム -->
                    <div class="subsection">
                        <h3 class="subsection-title">専門チーム</h3>
                        <ul class="team-list">
                            <li>補助金申請実務経験者</li>
                            <li>中小企業診断士</li>
                            <li>行政書士</li>
                            <li>ITシステム開発者</li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <!-- 情報の信頼性について -->
            <section class="content-section" id="reliability">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </span>
                    情報の信頼性について
                </h2>
                <div class="section-content">
                    
                    <!-- 情報源 -->
                    <div class="subsection">
                        <h3 class="subsection-title">情報源</h3>
                        <p>本サイトで提供する情報は、以下の公的機関の公式発表に基づいています：</p>
                        <ul class="source-list">
                            <li>各省庁（経済産業省、厚生労働省、国土交通省等）</li>
                            <li>都道府県・市区町村の公式ウェブサイト</li>
                            <li>独立行政法人・公的機関の発表資料</li>
                        </ul>
                    </div>
                    
                    <!-- 更新頻度 -->
                    <div class="subsection">
                        <h3 class="subsection-title">更新頻度</h3>
                        <ul class="update-list">
                            <li>毎日の自動データ収集</li>
                            <li>週2回の専門スタッフによる内容確認</li>
                            <li>重要な制度変更時の即座更新</li>
                        </ul>
                    </div>
                    
                    <!-- 免責事項 -->
                    <div class="subsection disclaimer-box">
                        <h3 class="subsection-title">免責事項</h3>
                        <ul class="disclaimer-list">
                            <li>最終的な申請要件・条件は各制度の公式情報をご確認ください</li>
                            <li>申請結果についての保証はいたしかねます</li>
                            <li>制度内容は予告なく変更される場合があります</li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <!-- 個人情報の取り扱い -->
            <section class="content-section" id="privacy">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    個人情報の取り扱い
                </h2>
                <div class="section-content">
                    <p>
                        当サイトでは、利用者の皆様に安心してサービスをご利用いただけるよう、個人情報保護法に基づき適切な管理を行っています。
                    </p>
                    <ul class="privacy-list">
                        <li>取得した個人情報は、サービス提供目的のみに使用</li>
                        <li>第三者への提供は、法令に基づく場合を除き行いません</li>
                        <li>SSL暗号化通信による情報保護</li>
                        <li>定期的なセキュリティ監査の実施</li>
                    </ul>
                    <p class="privacy-note">
                        詳細は<a href="https://joseikin-insight.com/privacy/" class="text-link">プライバシーポリシー</a>をご覧ください。
                    </p>
                </div>
            </section>
            
            <!-- 関連ページへのリンク -->
            <section class="content-section" id="related-links">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                    </span>
                    関連ページ
                </h2>
                <div class="related-links-grid">
                    <a href="https://joseikin-insight.com/contact/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">お問い合わせ</h3>
                            <p class="link-card-description">サービスに関するご質問はこちら</p>
                        </div>
                    </a>
                    
                    <a href="https://joseikin-insight.com/privacy/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">プライバシーポリシー</h3>
                            <p class="link-card-description">個人情報の取り扱いについて</p>
                        </div>
                    </a>
                    
                    <a href="https://joseikin-insight.com/terms/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">利用規約</h3>
                            <p class="link-card-description">サービス利用の規約について</p>
                        </div>
                    </a>
                </div>
            </section>
            
        </div>
    </div>
</article>

<style>
:root {
    /* カラーパレット */
    --color-white: #ffffff;
    --color-black: #000000;
    --color-yellow: #ffeb3b;
    --color-yellow-dark: #ffc107;
    
    /* グレースケール */
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #eeeeee;
    --color-gray-300: #e0e0e0;
    --color-gray-400: #bdbdbd;
    --color-gray-500: #9e9e9e;
    --color-gray-600: #757575;
    --color-gray-700: #616161;
    --color-gray-800: #424242;
    --color-gray-900: #212121;
    
    /* セマンティックカラー */
    --color-primary: var(--color-yellow);
    --text-primary: var(--color-gray-900);
    --text-secondary: var(--color-gray-600);
    --text-tertiary: var(--color-gray-500);
    
    /* 背景 */
    --bg-primary: var(--color-white);
    --bg-secondary: var(--color-gray-50);
    --bg-tertiary: var(--color-gray-100);
    
    /* ボーダー */
    --border-light: var(--color-gray-200);
    --border-medium: var(--color-gray-300);
    
    /* シャドウ */
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    
    /* スペーシング */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    --spacing-2xl: 2.5rem;
    --spacing-3xl: 3rem;
    --spacing-4xl: 4rem;
    
    /* ボーダーラディウス */
    --radius-sm: 0.25rem;
    --radius-md: 0.375rem;
    --radius-lg: 0.5rem;
    --radius-xl: 0.75rem;
    
    /* タイポグラフィ */
    --font-size-xs: 0.75rem;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.25rem;
    --font-size-2xl: 1.5rem;
    --font-size-3xl: 1.875rem;
    --font-size-4xl: 2.25rem;
    
    --font-weight-normal: 400;
    --font-weight-medium: 500;
    --font-weight-semibold: 600;
    --font-weight-bold: 700;
    
    --line-height-tight: 1.25;
    --line-height-normal: 1.5;
    --line-height-relaxed: 1.75;
}

/* ベーススタイル */
.about-page {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: var(--text-primary);
    background: var(--bg-primary);
}

/* コンテナ */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

/* ページヘッダー */
.page-header {
    background: var(--bg-secondary);
    padding: var(--spacing-4xl) 0 var(--spacing-3xl);
    text-align: center;
    border-bottom: 1px solid var(--border-light);
}

.page-title {
    font-size: var(--font-size-4xl);
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-md);
    line-height: var(--line-height-tight);
}

.page-subtitle {
    font-size: var(--font-size-lg);
    color: var(--text-secondary);
    margin: 0;
    line-height: var(--line-height-normal);
}

/* ページコンテンツ */
.page-content {
    padding: var(--spacing-4xl) 0;
}

/* コンテンツセクション */
.content-section {
    margin-bottom: var(--spacing-4xl);
}

.content-section:last-child {
    margin-bottom: 0;
}

/* セクションタイトル */
.section-title {
    font-size: var(--font-size-2xl);
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-xl);
    padding-bottom: var(--spacing-md);
    border-bottom: 2px solid var(--color-primary);
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    color: var(--text-primary);
}

/* セクションコンテンツ */
.section-content {
    line-height: var(--line-height-relaxed);
}

.lead-text {
    font-size: var(--font-size-lg);
    color: var(--text-primary);
    line-height: var(--line-height-relaxed);
    margin: 0;
}

.section-content p {
    font-size: var(--font-size-base);
    color: var(--text-secondary);
    line-height: var(--line-height-relaxed);
    margin: 0 0 var(--spacing-lg);
}

.section-content p:last-child {
    margin-bottom: 0;
}

/* サービスアイテム */
.service-item {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    margin-bottom: var(--spacing-xl);
}

.service-item:last-child {
    margin-bottom: 0;
}

.service-title {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-md);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.service-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    color: var(--text-primary);
}

.service-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.service-list li {
    position: relative;
    padding-left: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
}

.service-list li:last-child {
    margin-bottom: 0;
}

.service-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 6px;
    height: 6px;
    background: var(--color-primary);
    border-radius: 50%;
}

/* サブセクション */
.subsection {
    margin-bottom: var(--spacing-2xl);
}

.subsection:last-child {
    margin-bottom: 0;
}

.subsection-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-md);
}

/* 各種リスト */
.info-list,
.team-list,
.source-list,
.update-list,
.privacy-list {
    list-style: none;
    padding: 0;
    margin: var(--spacing-md) 0 0;
}

.info-list li,
.team-list li,
.source-list li,
.update-list li,
.privacy-list li {
    position: relative;
    padding-left: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
}

.info-list li:last-child,
.team-list li:last-child,
.source-list li:last-child,
.update-list li:last-child,
.privacy-list li:last-child {
    margin-bottom: 0;
}

.info-list li::before,
.team-list li::before,
.source-list li::before,
.update-list li::before,
.privacy-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 6px;
    height: 6px;
    background: var(--text-secondary);
    border-radius: 50%;
}

/* 免責事項ボックス */
.disclaimer-box {
    background: var(--bg-tertiary);
    border-left: 4px solid var(--color-gray-600);
    padding: var(--spacing-lg);
    border-radius: var(--radius-md);
}

.disclaimer-list {
    list-style: none;
    padding: 0;
    margin: var(--spacing-md) 0 0;
}

.disclaimer-list li {
    position: relative;
    padding-left: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--text-primary);
    line-height: var(--line-height-normal);
    font-weight: var(--font-weight-medium);
}

.disclaimer-list li:last-child {
    margin-bottom: 0;
}

.disclaimer-list li::before {
    content: '!';
    position: absolute;
    left: 0;
    top: 0;
    width: 20px;
    height: 20px;
    background: var(--color-gray-600);
    color: var(--color-white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-bold);
}

/* プライバシーノート */
.privacy-note {
    margin-top: var(--spacing-lg);
    padding: var(--spacing-md);
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
}

/* テキストリンク */
.text-link {
    color: var(--text-primary);
    text-decoration: underline;
    font-weight: var(--font-weight-medium);
    transition: color 0.2s ease;
}

.text-link:hover {
    color: var(--color-gray-700);
}

/* 関連リンクグリッド */
.related-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}

.related-link-card {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-md);
    padding: var(--spacing-lg);
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    text-decoration: none;
    transition: all 0.2s ease;
}

.related-link-card:hover {
    background: var(--bg-tertiary);
    border-color: var(--border-medium);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.link-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: var(--color-white);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    flex-shrink: 0;
}

.link-card-content {
    flex: 1;
}

.link-card-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-xs);
}

.link-card-description {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin: 0;
    line-height: var(--line-height-normal);
}

/* レスポンシブ調整 */
@media (max-width: 768px) {
    .page-header {
        padding: var(--spacing-3xl) 0 var(--spacing-2xl);
    }
    
    .page-title {
        font-size: var(--font-size-3xl);
    }
    
    .page-subtitle {
        font-size: var(--font-size-base);
    }
    
    .page-content {
        padding: var(--spacing-3xl) 0;
    }
    
    .content-section {
        margin-bottom: var(--spacing-3xl);
    }
    
    .section-title {
        font-size: var(--font-size-xl);
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-sm);
    }
    
    .service-item {
        padding: var(--spacing-lg);
    }
    
    .service-title {
        font-size: var(--font-size-lg);
    }
    
    .related-links-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0 var(--spacing-md);
    }
    
    .page-header {
        padding: var(--spacing-2xl) 0 var(--spacing-xl);
    }
    
    .page-title {
        font-size: var(--font-size-2xl);
    }
    
    .page-subtitle {
        font-size: var(--font-size-sm);
    }
    
    .page-content {
        padding: var(--spacing-2xl) 0;
    }
    
    .section-title {
        font-size: var(--font-size-lg);
    }
    
    .lead-text {
        font-size: var(--font-size-base);
    }
}

/* プリント対応 */
@media print {
    .about-page {
        background: white;
        color: black;
    }
    
    .page-header {
        background: white;
        border-bottom: 2px solid black;
    }
    
    .service-item {
        border: 1px solid black;
        page-break-inside: avoid;
    }
    
    .related-link-card {
        display: none;
    }
}
</style>

<?php get_footer(); ?>
