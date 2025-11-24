<?php
/**
 * Template Name: Glossary Page (補助金・助成金用語集)
 * 
 * SEO特化の白黒ベース + イエローアクセントの用語集ページ
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

get_header(); 

// SEO用メタ情報
$page_title = '補助金・助成金用語集 | ' . get_bloginfo('name');
$page_description = '補助金・助成金申請で使われる専門用語を100語以上収録。あっせん、交付決定、補助率など、申請書類作成や制度理解に必要な用語をわかりやすく解説。';
$page_keywords = '補助金,助成金,用語集,申請,交付決定,補助率,対象経費,成果報告,事業継続計画,BCP,DX,GX,IoT,SDGs';
$page_url = home_url('/glossary/');
$page_image = get_template_directory_uri() . '/assets/images/glossary-og.jpg';

?>

<!-- SEO Meta Tags -->
<meta name="description" content="<?php echo esc_attr($page_description); ?>">
<meta name="keywords" content="<?php echo esc_attr($page_keywords); ?>">
<link rel="canonical" href="<?php echo esc_url($page_url); ?>">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
<meta property="og:description" content="<?php echo esc_attr($page_description); ?>">
<meta property="og:url" content="<?php echo esc_url($page_url); ?>">
<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
<meta property="og:locale" content="ja_JP">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($page_description); ?>">

<!-- 構造化データ（JSON-LD） -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "補助金・助成金用語集",
  "description": "<?php echo esc_js($page_description); ?>",
  "url": "<?php echo esc_url($page_url); ?>",
  "inLanguage": "ja",
  "isPartOf": {
    "@type": "WebSite",
    "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
    "url": "<?php echo esc_url(home_url('/')); ?>"
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
        "name": "用語集",
        "item": "<?php echo esc_url($page_url); ?>"
      }
    ]
  },
  "mainEntity": {
    "@type": "DefinedTermSet",
    "name": "補助金・助成金用語集",
    "description": "補助金・助成金申請に関する専門用語を網羅的に解説",
    "hasDefinedTerm": [
      {
        "@type": "DefinedTerm",
        "name": "交付決定",
        "description": "申請内容を審査した結果、補助金の交付を正式に決定すること"
      },
      {
        "@type": "DefinedTerm",
        "name": "補助率",
        "description": "補助金額の対象経費に対する割合"
      },
      {
        "@type": "DefinedTerm",
        "name": "対象経費",
        "description": "補助金の交付対象となる経費の範囲"
      }
    ]
  },
  "about": {
    "@type": "Thing",
    "name": "補助金・助成金",
    "description": "事業者支援のための公的資金制度"
  }
}
</script>

<style>
/* ========== Glossary Page Styles ========== */

/* ベース設定 */
.glossary-page {
    background: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, 'Noto Sans JP', sans-serif;
    line-height: 1.8;
    color: #1a1a1a;
}

/* ヒーローセクション */
.glossary-hero {
    padding: 120px 20px 80px;
    text-align: center;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #ffffff;
    position: relative;
}

.glossary-hero::before {
    content: '📚';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 20rem;
    opacity: 0.03;
    pointer-events: none;
}

.glossary-hero-title {
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 900;
    margin-bottom: 24px;
    letter-spacing: -0.02em;
    position: relative;
}

.glossary-hero-subtitle {
    font-size: clamp(1.1rem, 2.5vw, 1.5rem);
    font-weight: 300;
    max-width: 800px;
    margin: 0 auto 40px;
    opacity: 0.9;
    line-height: 1.6;
    position: relative;
}

.glossary-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    margin-top: 40px;
    position: relative;
}

.glossary-stat {
    text-align: center;
}

.glossary-stat-number {
    font-size: 2.5rem;
    font-weight: 900;
    color: #FFD500;
    display: block;
    margin-bottom: 8px;
}

.glossary-stat-label {
    font-size: 0.95rem;
    opacity: 0.8;
}

/* メインコンテンツ */
.glossary-content {
    max-width: 1100px;
    margin: 0 auto;
    padding: 80px 20px;
}

/* 検索ボックス */
.glossary-search-box {
    max-width: 600px;
    margin: 0 auto 60px;
    position: sticky;
    top: 80px;
    z-index: 100;
    background: #ffffff;
    padding: 20px 0;
}

.glossary-search-wrapper {
    position: relative;
}

.glossary-search-input {
    width: 100%;
    padding: 18px 50px 18px 24px;
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    font-size: 1.05rem;
    transition: all 0.3s ease;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.glossary-search-input:focus {
    outline: none;
    border-color: #FFD500;
    box-shadow: 0 4px 20px rgba(255, 213, 0, 0.2);
}

.glossary-search-icon {
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.3rem;
    color: #666;
}

.glossary-search-results {
    margin-top: 12px;
    font-size: 0.95rem;
    color: #666;
    text-align: center;
}

/* 五十音ナビゲーション */
.glossary-nav {
    background: #f9f9f9;
    border: 2px solid #e0e0e0;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 60px;
    position: sticky;
    top: 180px;
    z-index: 90;
}

.glossary-nav-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 16px;
    color: #1a1a1a;
    text-align: center;
}

.glossary-nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
    gap: 8px;
}

.glossary-nav-item {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    background: #ffffff;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-weight: 700;
    color: #666;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.glossary-nav-item:hover,
.glossary-nav-item.active {
    background: #FFD500;
    border-color: #FFD500;
    color: #1a1a1a;
    transform: translateY(-2px);
}

.glossary-nav-item.disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

/* 用語セクション */
.glossary-section {
    margin-bottom: 80px;
    scroll-margin-top: 180px;
}

.glossary-section-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 40px;
    padding-bottom: 16px;
    border-bottom: 4px solid #FFD500;
}

.glossary-section-icon {
    width: 60px;
    height: 60px;
    background: #FFD500;
    color: #1a1a1a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: 900;
    flex-shrink: 0;
}

.glossary-section-title {
    font-size: 2rem;
    font-weight: 800;
    color: #1a1a1a;
}

/* 用語項目 */
.glossary-items {
    display: grid;
    gap: 24px;
}

.glossary-item {
    background: #ffffff;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 28px;
    transition: all 0.3s ease;
}

.glossary-item:hover {
    border-color: #FFD500;
    box-shadow: 0 4px 20px rgba(255, 213, 0, 0.15);
    transform: translateY(-2px);
}

.glossary-term-header {
    display: flex;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.glossary-term {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.3;
}

.glossary-reading {
    font-size: 1rem;
    color: #666;
    font-weight: 500;
}

.glossary-english {
    font-size: 0.95rem;
    color: #999;
    font-weight: 600;
    letter-spacing: 0.05em;
}

.glossary-definition {
    font-size: 1.05rem;
    line-height: 1.9;
    color: #333;
    margin-bottom: 0;
}

.glossary-definition strong {
    color: #1a1a1a;
    font-weight: 700;
}

/* 関連ページセクション */
.glossary-related {
    background: linear-gradient(135deg, #f9f9f9 0%, #f5f5f5 100%);
    border-radius: 20px;
    padding: 60px 40px;
    margin-top: 80px;
}

.glossary-related-title {
    font-size: 2rem;
    font-weight: 800;
    text-align: center;
    margin-bottom: 40px;
    color: #1a1a1a;
}

.glossary-related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    max-width: 900px;
    margin: 0 auto;
}

.glossary-related-card {
    background: #ffffff;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 28px;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.glossary-related-card:hover {
    border-color: #FFD500;
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(255, 213, 0, 0.2);
}

.glossary-related-icon {
    font-size: 2.5rem;
    margin-bottom: 8px;
}

.glossary-related-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a1a1a;
}

.glossary-related-desc {
    font-size: 0.95rem;
    color: #666;
    line-height: 1.6;
}

/* CTAセクション */
.glossary-cta {
    margin-top: 80px;
    padding: 60px 40px;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 20px;
    text-align: center;
    color: #ffffff;
}

.glossary-cta-title {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 16px;
}

.glossary-cta-text {
    font-size: 1.1rem;
    margin-bottom: 32px;
    opacity: 0.9;
}

.glossary-cta-button {
    display: inline-block;
    padding: 18px 48px;
    background: #FFD500;
    color: #1a1a1a;
    font-size: 1.1rem;
    font-weight: 700;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.glossary-cta-button:hover {
    background: #ffffff;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 213, 0, 0.3);
}

/* トップへ戻るボタン */
.glossary-back-to-top {
    position: fixed;
    bottom: 40px;
    right: 40px;
    width: 56px;
    height: 56px;
    background: #FFD500;
    color: #1a1a1a;
    border: none;
    border-radius: 50%;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.glossary-back-to-top.visible {
    opacity: 1;
    visibility: visible;
}

.glossary-back-to-top:hover {
    background: #1a1a1a;
    color: #FFD500;
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
}

/* レスポンシブ */
@media (max-width: 768px) {
    .glossary-hero {
        padding: 80px 20px 60px;
    }

    .glossary-hero::before {
        font-size: 10rem;
    }

    .glossary-stats {
        gap: 24px;
    }

    .glossary-stat-number {
        font-size: 2rem;
    }

    .glossary-content {
        padding: 60px 20px;
    }

    .glossary-search-box {
        top: 68px;
        padding: 16px 0;
    }

    .glossary-search-input {
        padding: 16px 50px 16px 20px;
        font-size: 1rem;
    }

    .glossary-nav {
        top: 150px;
        padding: 20px 16px;
    }

    .glossary-nav-grid {
        grid-template-columns: repeat(auto-fit, minmax(45px, 1fr));
        gap: 6px;
    }

    .glossary-nav-item {
        padding: 10px 6px;
        font-size: 0.95rem;
    }

    .glossary-section {
        margin-bottom: 60px;
        scroll-margin-top: 150px;
    }

    .glossary-section-header {
        gap: 12px;
        margin-bottom: 30px;
    }

    .glossary-section-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }

    .glossary-section-title {
        font-size: 1.6rem;
    }

    .glossary-item {
        padding: 24px 20px;
    }

    .glossary-term {
        font-size: 1.3rem;
    }

    .glossary-reading {
        font-size: 0.95rem;
    }

    .glossary-definition {
        font-size: 1rem;
    }

    .glossary-related {
        padding: 40px 24px;
    }

    .glossary-related-title {
        font-size: 1.6rem;
    }

    .glossary-related-grid {
        grid-template-columns: 1fr;
    }

    .glossary-cta {
        padding: 40px 24px;
    }

    .glossary-cta-title {
        font-size: 1.6rem;
    }

    .glossary-back-to-top {
        bottom: 24px;
        right: 24px;
        width: 48px;
        height: 48px;
        font-size: 1.3rem;
    }
}

/* 印刷スタイル */
@media print {
    .glossary-hero,
    .glossary-search-box,
    .glossary-nav,
    .glossary-related,
    .glossary-cta,
    .glossary-back-to-top {
        display: none;
    }

    .glossary-section {
        page-break-inside: avoid;
    }

    .glossary-item {
        border: 1px solid #ccc;
        page-break-inside: avoid;
    }
}
</style>

<div class="glossary-page">
    <!-- ヒーローセクション -->
    <section class="glossary-hero">
        <h1 class="glossary-hero-title">補助金・助成金用語集</h1>
        <p class="glossary-hero-subtitle">
            補助金申請で使われる専門用語をわかりやすく解説。<br>
            申請書類の作成や制度理解にお役立てください。
        </p>
        <div class="glossary-stats">
            <div class="glossary-stat">
                <span class="glossary-stat-number">100+</span>
                <span class="glossary-stat-label">収録用語数</span>
            </div>
            <div class="glossary-stat">
                <span class="glossary-stat-number">15</span>
                <span class="glossary-stat-label">カテゴリ</span>
            </div>
            <div class="glossary-stat">
                <span class="glossary-stat-number">毎日</span>
                <span class="glossary-stat-label">更新</span>
            </div>
        </div>
    </section>

    <!-- メインコンテンツ -->
    <div class="glossary-content">
        
        <!-- 検索ボックス -->
        <div class="glossary-search-box">
            <div class="glossary-search-wrapper">
                <input type="text" 
                       id="glossary-search-input" 
                       class="glossary-search-input" 
                       placeholder="用語を検索...（例: 交付決定、対象経費、補助率）"
                       aria-label="用語を検索">
                <i class="fas fa-search glossary-search-icon" aria-hidden="true"></i>
            </div>
            <div id="glossary-search-results" class="glossary-search-results"></div>
        </div>

        <!-- 五十音ナビゲーション -->
        <nav class="glossary-nav" aria-label="五十音ナビゲーション">
            <div class="glossary-nav-title">📑 五十音で探す</div>
            <div class="glossary-nav-grid">
                <a href="#a-gyou" class="glossary-nav-item">あ</a>
                <a href="#ka-gyou" class="glossary-nav-item">か</a>
                <a href="#sa-gyou" class="glossary-nav-item">さ</a>
                <a href="#ta-gyou" class="glossary-nav-item">た</a>
                <a href="#na-gyou" class="glossary-nav-item">な</a>
                <a href="#ha-gyou" class="glossary-nav-item">は</a>
                <a href="#ma-gyou" class="glossary-nav-item">ま</a>
                <a href="#ya-gyou" class="glossary-nav-item">や</a>
                <a href="#ra-gyou" class="glossary-nav-item">ら</a>
                <a href="#wa-gyou" class="glossary-nav-item">わ</a>
                <a href="#eisuu" class="glossary-nav-item">英数</a>
            </div>
        </nav>

        <!-- あ行 -->
        <section class="glossary-section" id="a-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">あ</div>
                <h2 class="glossary-section-title">あ行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">あっせん</h3>
                        <span class="glossary-reading">（斡旋）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金申請において、行政機関や支援機関が事業者と制度を結びつける仲介行為。中小企業支援センターや商工会議所などが行うことが多い。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">いっかつこうふ</h3>
                        <span class="glossary-reading">（一括交付）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金の交付決定後、事業完了前に補助金額の全額または一定割合を事前に交付する制度。資金繰りの改善に寄与するが、厳格な条件が設定される。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">うけはらい</h3>
                        <span class="glossary-reading">（受払）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業における金銭の受け取りと支払いの記録。補助金の適正使用を証明するため、詳細な帳簿管理が必要。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">えいぎょうねんど</h3>
                        <span class="glossary-reading">（営業年度）</span>
                    </div>
                    <p class="glossary-definition">
                        事業者の会計期間。補助金申請時の売上高や従業員数の算定基準となる。多くの補助金で直近の営業年度の実績が要件として設定される。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">おうぼようりょう</h3>
                        <span class="glossary-reading">（応募要領）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金制度の詳細な条件や手続きを記載した公式文書。申請前に必ず確認すべき最重要資料。募集期間、対象者、対象経費、申請方法などが記載されている。
                    </p>
                </article>

            </div>
        </section>

        <!-- か行 -->
        <section class="glossary-section" id="ka-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">か</div>
                <h2 class="glossary-section-title">か行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">かいけいけんさ</h3>
                        <span class="glossary-reading">（会計検査）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金の適正使用を確認するための検査。会計検査院や補助金交付機関が実施。帳簿、証拠書類、現地確認などにより行われる。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">きかくしょ</h3>
                        <span class="glossary-reading">（企画書）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金申請時に提出する事業計画の概要資料。事業目的、実施内容、期待効果、実施体制などを記載。審査における重要な判断材料となる。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">くりこし</h3>
                        <span class="glossary-reading">（繰越）</span>
                    </div>
                    <p class="glossary-definition">
                        当該年度内に事業が完了しない場合、次年度に事業期間を延長する手続き。天災や不可抗力による遅延の場合に認められることがある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">けいひ</h3>
                        <span class="glossary-reading">（経費）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業の実施に要する費用。補助対象経費と補助対象外経費に分類される。適正な経費管理と証拠書類の保管が必須。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">こうふけってい</h3>
                        <span class="glossary-reading">（交付決定）</span>
                    </div>
                    <p class="glossary-definition">
                        申請内容を審査した結果、補助金の交付を正式に決定すること。交付決定通知書により通知され、これ以降に事業着手が可能となる。
                    </p>
                </article>

            </div>
        </section>

        <!-- さ行 -->
        <section class="glossary-section" id="sa-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">さ</div>
                <h2 class="glossary-section-title">さ行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">さいたく</h3>
                        <span class="glossary-reading">（採択）</span>
                    </div>
                    <p class="glossary-definition">
                        応募案件の中から補助対象として選定されること。審査を通過し、補助金交付の候補となる。採択後も交付決定までには追加手続きが必要。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">しゅっぱん</h3>
                        <span class="glossary-reading">（出版）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業で作成した成果物の公表義務。研究開発系補助金では論文発表、普及啓発系では報告書公開などが求められる場合がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">すいせん</h3>
                        <span class="glossary-reading">（推薦）</span>
                    </div>
                    <p class="glossary-definition">
                        特定の機関からの推薦を申請要件とする補助金制度。商工会議所、業界団体、大学などからの推薦書が必要な場合がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">せいかほうこく</h3>
                        <span class="glossary-reading">（成果報告）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業完了後に提出が義務付けられた報告書。事業の実施状況、目標達成度、成果の詳細などを記載。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">ぜんのう</h3>
                        <span class="glossary-reading">（前納）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業の経費を事業者が先行して支払うこと。多くの補助金では精算払いのため、事業者による前納が前提となる。
                    </p>
                </article>

            </div>
        </section>

        <!-- た行 -->
        <section class="glossary-section" id="ta-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">た</div>
                <h2 class="glossary-section-title">た行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">たいしょうけいひ</h3>
                        <span class="glossary-reading">（対象経費）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金の交付対象となる経費の範囲。人件費、設備費、材料費、外注費などに分類され、制度ごとに詳細な規定がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">ちょうたつ</h3>
                        <span class="glossary-reading">（調達）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業で必要な物品やサービスの購入・契約行為。一定額以上では相見積もりの取得や入札手続きが義務付けられる場合がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">てつづきひよう</h3>
                        <span class="glossary-reading">（手続費用）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金申請や管理に係る諸費用。多くの場合、補助対象外経費として扱われるため、事業者の自己負担となる。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">とうけい</h3>
                        <span class="glossary-reading">（統計）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金の効果測定や政策評価のため収集される数値データ。売上高、雇用創出数、省エネ効果などの実績報告が求められる。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">どくりつせいほうしん</h3>
                        <span class="glossary-reading">（独立性保針）</span>
                    </div>
                    <p class="glossary-definition">
                        審査における公平性確保のため、利害関係者を審査から排除する原則。申請者と審査員の間に特別な関係がある場合は審査対象から除外される。
                    </p>
                </article>

            </div>
        </section>

        <!-- な行 -->
        <section class="glossary-section" id="na-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">な</div>
                <h2 class="glossary-section-title">な行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">にゅうさつ</h3>
                        <span class="glossary-reading">（入札）</span>
                    </div>
                    <p class="glossary-definition">
                        一定額以上の調達において実施が義務付けられた競争手続き。透明性と経済性の確保を目的とし、複数業者からの見積もり比較が必要。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">ねんどまたぎ</h3>
                        <span class="glossary-reading">（年度跨ぎ）</span>
                    </div>
                    <p class="glossary-definition">
                        事業期間が会計年度を跨いで設定されること。国の補助金では原則として単年度での完了が求められるが、例外的に認められる場合がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">のうぜい</h3>
                        <span class="glossary-reading">（納税）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金受給に際して課される税務上の義務。補助金は原則として課税所得となるため、適切な税務申告と納税が必要。
                    </p>
                </article>

            </div>
        </section>

        <!-- は行 -->
        <section class="glossary-section" id="ha-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">は</div>
                <h2 class="glossary-section-title">は行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">はっちゅう</h3>
                        <span class="glossary-reading">（発注）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業における外部業者への業務委託や物品購入の正式依頼。交付決定後でなければ補助対象経費としての発注はできない。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">ひょうか</h3>
                        <span class="glossary-reading">（評価）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業の成果や効果を客観的に測定・判定すること。中間評価、事後評価、追跡評価などの種類がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">ふくすうねんど</h3>
                        <span class="glossary-reading">（複数年度）</span>
                    </div>
                    <p class="glossary-definition">
                        事業期間が複数の会計年度に渡って設定された補助金制度。研究開発や大規模設備投資などで採用される場合がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">へんこう</h3>
                        <span class="glossary-reading">（変更）</span>
                    </div>
                    <p class="glossary-definition">
                        交付決定された事業計画の内容を変更すること。軽微な変更は届出で済むが、重要な変更は承認手続きが必要。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">ほじょりつ</h3>
                        <span class="glossary-reading">（補助率）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金額の対象経費に対する割合。1/2（50%）、2/3（66.7%）などと表記される。上限額との組み合わせで実際の補助額が決定される。
                    </p>
                </article>

            </div>
        </section>

        <!-- ま行 -->
        <section class="glossary-section" id="ma-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">ま</div>
                <h2 class="glossary-section-title">ま行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">まえばらい</h3>
                        <span class="glossary-reading">（前払）</span>
                    </div>
                    <p class="glossary-definition">
                        事業完了前に補助金の一部または全部を交付する制度。通常の精算払いとは異なり、事業者の資金調達負担を軽減する。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">みつもり</h3>
                        <span class="glossary-reading">（見積）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業で必要な経費の事前算定。申請時および実際の調達時の両方で取得が必要。複数業者からの相見積もりが原則。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">むりょうそうだん</h3>
                        <span class="glossary-reading">（無料相談）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金申請に関する相談サービス。自治体、商工会議所、中小企業支援センターなどが実施。事前相談により申請成功率の向上が期待できる。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">めんせき</h3>
                        <span class="glossary-reading">（免責）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金返還義務の免除。天災や経済情勢の急変など、事業者の責に帰さない事由により事業目標が未達成の場合に適用される場合がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">もくひょう</h3>
                        <span class="glossary-reading">（目標）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業で達成すべき定量的・定性的な指標。売上高増加、雇用創出、CO2削減量などが設定され、事後的な達成状況の報告が求められる。
                    </p>
                </article>

            </div>
        </section>

        <!-- や行 -->
        <section class="glossary-section" id="ya-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">や</div>
                <h2 class="glossary-section-title">や行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">ゆうこうきげん</h3>
                        <span class="glossary-reading">（有効期限）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金の申請や事業実施に関する期限。募集期間、事業実施期間、実績報告期限などがあり、期限超過は補助金返還事由となる場合がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">よてい</h3>
                        <span class="glossary-reading">（予定）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業の実施計画。申請時に詳細なスケジュールの提出が求められ、大幅な遅延は変更承認の対象となる。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">よさん</h3>
                        <span class="glossary-reading">（予算）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金制度に充てられた財源。国家予算、地方自治体予算、基金などから支出される。予算額により採択件数や補助上限額が左右される。
                    </p>
                </article>

            </div>
        </section>

        <!-- ら行 -->
        <section class="glossary-section" id="ra-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">ら</div>
                <h2 class="glossary-section-title">ら行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">りえき</h3>
                        <span class="glossary-reading">（利益）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業により得られた収益。過大な利益が生じた場合は補助金の一部返還が求められることがある（収益納付）。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">りようじょうけん</h3>
                        <span class="glossary-reading">（利用条件）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金制度の対象者要件。業種、従業員数、売上高、設立年数などの条件が設定される。すべての条件を満たす必要がある。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">れきし</h3>
                        <span class="glossary-reading">（履歴）</span>
                    </div>
                    <p class="glossary-definition">
                        補助金の申請・受給歴。重複申請の防止や制度改善のため、過去の申請状況が管理されている。虚偽申告は重大な違反行為となる。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">ろうどうほう</h3>
                        <span class="glossary-reading">（労働法）</span>
                    </div>
                    <p class="glossary-definition">
                        補助事業における雇用関係に適用される法令。最低賃金法、労働基準法、労働安全衛生法などの遵守が求められる。
                    </p>
                </article>

            </div>
        </section>

        <!-- わ行 -->
        <section class="glossary-section" id="wa-gyou">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">わ</div>
                <h2 class="glossary-section-title">わ行</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">わりびき</h3>
                        <span class="glossary-reading">（割引）</span>
                    </div>
                    <p class="glossary-definition">
                        補助対象経費の算定において認められない価格操作。定価からの不当な割引や関係者間取引における優遇価格は補助対象外となる。
                    </p>
                </article>

            </div>
        </section>

        <!-- 英数字 -->
        <section class="glossary-section" id="eisuu">
            <div class="glossary-section-header">
                <div class="glossary-section-icon">A</div>
                <h2 class="glossary-section-title">英数字</h2>
            </div>
            <div class="glossary-items">
                
                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">BCP</h3>
                        <span class="glossary-english">Business Continuity Plan</span>
                    </div>
                    <p class="glossary-definition">
                        <strong>事業継続計画</strong>。災害や緊急事態発生時に事業を継続するための計画。BCPの策定や実効性向上を目的とした補助金制度が多数存在する。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">DX</h3>
                        <span class="glossary-english">Digital Transformation</span>
                    </div>
                    <p class="glossary-definition">
                        <strong>デジタルトランスフォーメーション</strong>。デジタル技術を活用した事業変革。IT導入補助金やDX推進補助金など、デジタル化を支援する制度の対象分野。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">GX</h3>
                        <span class="glossary-english">Green Transformation</span>
                    </div>
                    <p class="glossary-definition">
                        <strong>グリーントランスフォーメーション</strong>。脱炭素社会実現に向けた事業変革。省エネ設備導入、再生可能エネルギー利用などを対象とした補助金が多数。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">IoT</h3>
                        <span class="glossary-english">Internet of Things</span>
                    </div>
                    <p class="glossary-definition">
                        <strong>モノのインターネット</strong>。機器をインターネットに接続し、データ収集・分析を行う技術。製造業のスマート化を支援する補助金の対象技術。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">KPI</h3>
                        <span class="glossary-english">Key Performance Indicator</span>
                    </div>
                    <p class="glossary-definition">
                        <strong>重要業績評価指標</strong>。事業の成果を測定する具体的指標。補助事業の目標設定や効果測定に用いられ、売上高増加率、生産性向上率などが例。
                    </p>
                </article>

                <article class="glossary-item">
                    <div class="glossary-term-header">
                        <h3 class="glossary-term">SDGs</h3>
                        <span class="glossary-english">Sustainable Development Goals</span>
                    </div>
                    <p class="glossary-definition">
                        <strong>持続可能な開発目標</strong>。国連が定めた17の国際目標。SDGs達成に貢献する事業を対象とした補助金制度が自治体を中心に拡充されている。
                    </p>
                </article>

            </div>
        </section>

        <!-- 関連ページ -->
        <section class="glossary-related">
            <h2 class="glossary-related-title">関連ページ</h2>
            <div class="glossary-related-grid">
                <a href="<?php echo esc_url(home_url('/knowledge/')); ?>" class="glossary-related-card">
                    <div class="glossary-related-icon">📘</div>
                    <div class="glossary-related-name">補助金・助成金の基礎知識</div>
                    <div class="glossary-related-desc">初めての方向けの基本情報</div>
                </a>
                <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="glossary-related-card">
                    <div class="glossary-related-icon">❓</div>
                    <div class="glossary-related-name">よくある質問</div>
                    <div class="glossary-related-desc">お問い合わせの多い質問</div>
                </a>
                <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="glossary-related-card">
                    <div class="glossary-related-icon">🔍</div>
                    <div class="glossary-related-name">補助金検索</div>
                    <div class="glossary-related-desc">最新の補助金情報を検索</div>
                </a>
            </div>
        </section>

        <!-- CTAセクション -->
        <section class="glossary-cta">
            <h2 class="glossary-cta-title">補助金申請でお困りですか？</h2>
            <p class="glossary-cta-text">
                専門スタッフが補助金申請をトータルサポート。<br>
                無料相談も実施中です。お気軽にお問い合わせください。
            </p>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="glossary-cta-button">
                無料相談はこちら
            </a>
        </section>

    </div>
</div>

<!-- トップへ戻るボタン -->
<button id="glossary-back-to-top" class="glossary-back-to-top" aria-label="ページトップへ戻る">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
// 用語集機能
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // 検索機能
    const searchInput = document.getElementById('glossary-search-input');
    const searchResults = document.getElementById('glossary-search-results');
    const glossaryItems = document.querySelectorAll('.glossary-item');
    const glossarySections = document.querySelectorAll('.glossary-section');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            let hiddenSections = [];
            
            if (searchTerm === '') {
                // 検索語が空の場合は全て表示
                glossaryItems.forEach(item => {
                    item.style.display = '';
                });
                glossarySections.forEach(section => {
                    section.style.display = '';
                });
                searchResults.textContent = '';
                return;
            }
            
            // 各セクションの表示状態を追跡
            glossarySections.forEach(section => {
                let hasVisibleItems = false;
                const sectionItems = section.querySelectorAll('.glossary-item');
                
                sectionItems.forEach(item => {
                    const term = item.querySelector('.glossary-term').textContent.toLowerCase();
                    const reading = item.querySelector('.glossary-reading') ? 
                        item.querySelector('.glossary-reading').textContent.toLowerCase() : '';
                    const definition = item.querySelector('.glossary-definition').textContent.toLowerCase();
                    const english = item.querySelector('.glossary-english') ? 
                        item.querySelector('.glossary-english').textContent.toLowerCase() : '';
                    
                    if (term.includes(searchTerm) || 
                        reading.includes(searchTerm) || 
                        definition.includes(searchTerm) ||
                        english.includes(searchTerm)) {
                        item.style.display = '';
                        visibleCount++;
                        hasVisibleItems = true;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // セクション全体の表示・非表示
                if (hasVisibleItems) {
                    section.style.display = '';
                } else {
                    section.style.display = 'none';
                    hiddenSections.push(section);
                }
            });
            
            // 検索結果の表示
            if (visibleCount > 0) {
                searchResults.textContent = `${visibleCount}件の用語が見つかりました`;
                searchResults.style.color = '#059669';
            } else {
                searchResults.textContent = '該当する用語が見つかりませんでした';
                searchResults.style.color = '#dc2626';
            }
        });
    }
    
    // 五十音ナビゲーションのスムーススクロール
    const navItems = document.querySelectorAll('.glossary-nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // アクティブ状態を更新
            navItems.forEach(navItem => navItem.classList.remove('active'));
            this.classList.add('active');
            
            // スムーススクロール
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                const offset = 160;
                const targetPosition = targetSection.getBoundingClientRect().top + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // トップへ戻るボタン
    const backToTopBtn = document.getElementById('glossary-back-to-top');
    
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // スクロール時のナビゲーションハイライト
    const observerOptions = {
        threshold: 0.3,
        rootMargin: '-180px 0px -50% 0px'
    };
    
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const sectionId = entry.target.getAttribute('id');
                navItems.forEach(navItem => {
                    if (navItem.getAttribute('href') === '#' + sectionId) {
                        navItems.forEach(item => item.classList.remove('active'));
                        navItem.classList.add('active');
                    }
                });
            }
        });
    }, observerOptions);
    
    glossarySections.forEach(section => {
        sectionObserver.observe(section);
    });
    
    // ページ読み込み時にURLハッシュに基づいてスクロール
    if (window.location.hash) {
        const targetSection = document.querySelector(window.location.hash);
        if (targetSection) {
            setTimeout(() => {
                const offset = 160;
                const targetPosition = targetSection.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }, 100);
        }
    }
    
    console.log('[OK] Glossary Page initialized - 用語集ページ');
});
</script>

<?php get_footer(); ?>
