<?php
/**
 * Template Name: Basics Page (補助金・助成金の基礎知識)
 * 
 * SEO特化の白黒ベース + イエローアクセントの基礎知識ページ
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

get_header();

// SEO用メタ情報
$page_title = '補助金・助成金の基礎知識｜初心者向け完全ガイド 2025年版 | ' . get_bloginfo('name');
$page_description = '補助金・助成金とは何か、制度の種類、申請から受給までの流れを初心者にもわかりやすく解説。メリット・デメリット、申請要件、成功のコツまで網羅した完全ガイド2025年版。';
$page_keywords = '補助金,助成金,基礎知識,初心者,申請方法,申請要件,メリット,デメリット,事業資金,資金調達';
$page_url = home_url('/basics/');

?>

<!-- SEO Meta Tags -->
<meta name="description" content="<?php echo esc_attr($page_description); ?>">
<meta name="keywords" content="<?php echo esc_attr($page_keywords); ?>">
<link rel="canonical" href="<?php echo esc_url($page_url); ?>">

<!-- Open Graph -->
<meta property="og:type" content="article">
<meta property="og:title" content="<?php echo esc_attr($page_title); ?>">
<meta property="og:description" content="<?php echo esc_attr($page_description); ?>">
<meta property="og:url" content="<?php echo esc_url($page_url); ?>">
<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($page_description); ?>">

<!-- 構造化データ（JSON-LD） -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "補助金・助成金の基礎知識｜初心者向け完全ガイド 2025年版",
  "description": "<?php echo esc_js($page_description); ?>",
  "url": "<?php echo esc_url($page_url); ?>",
  "datePublished": "2025-11-02",
  "dateModified": "2025-11-02",
  "inLanguage": "ja",
  "author": {
    "@type": "Organization",
    "name": "<?php echo esc_js(get_bloginfo('name')); ?>"
  },
  "publisher": {
    "@type": "Organization",
    "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
    "url": "<?php echo esc_url(home_url('/')); ?>"
  },
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
        "name": "補助金・助成金の基礎知識",
        "item": "<?php echo esc_url($page_url); ?>"
      }
    ]
  }
}
</script>

<style>
/* ========== Basics Page Styles ========== */

/* ベース設定 */
.basics-page {
    background: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, 'Noto Sans JP', sans-serif;
    line-height: 1.8;
    color: #1a1a1a;
}

/* ヒーローセクション */
.basics-hero {
    padding: 120px 20px 80px;
    text-align: center;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #ffffff;
}

.basics-hero-title {
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 900;
    margin-bottom: 24px;
    letter-spacing: -0.02em;
}

.basics-hero-lead {
    font-size: clamp(1.1rem, 2.5vw, 1.4rem);
    font-weight: 300;
    max-width: 800px;
    margin: 0 auto;
    opacity: 0.9;
    line-height: 1.7;
}

/* 目次 */
.contents-nav {
    max-width: 900px;
    margin: 0 auto 60px;
    padding: 40px;
    background: #f9f9f9;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
}

.contents-nav h2 {
    font-size: 1.5rem;
    font-weight: 800;
    margin-bottom: 24px;
    color: #1a1a1a;
}

.contents-nav ol {
    list-style-position: inside;
    padding: 0;
}

.contents-nav li {
    font-size: 1.05rem;
    line-height: 2;
    margin-bottom: 12px;
}

.contents-nav a {
    color: #1a1a1a;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.contents-nav a:hover {
    color: #FFD500;
}

/* メインコンテンツ */
.basics-content {
    max-width: 900px;
    margin: 0 auto;
    padding: 80px 20px;
}

.basics-content section {
    margin-bottom: 80px;
    scroll-margin-top: 100px;
}

.basics-content h2 {
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 32px;
    padding-bottom: 16px;
    border-bottom: 4px solid #FFD500;
    color: #1a1a1a;
}

.basics-content h3 {
    font-size: 1.7rem;
    font-weight: 700;
    margin: 40px 0 24px;
    color: #1a1a1a;
}

.basics-content h4 {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 32px 0 20px;
    color: #1a1a1a;
}

.basics-content p {
    font-size: 1.05rem;
    line-height: 1.9;
    margin-bottom: 24px;
    color: #333;
}

.basics-content ul,
.basics-content ol {
    margin: 24px 0;
    padding-left: 28px;
}

.basics-content li {
    font-size: 1.05rem;
    line-height: 1.9;
    margin-bottom: 12px;
    color: #333;
}

.basics-content li strong {
    color: #1a1a1a;
    font-weight: 700;
}

.basics-content a {
    color: #1a1a1a;
    text-decoration: underline;
    font-weight: 600;
}

.basics-content a:hover {
    color: #FFD500;
}

/* ハイライトボックス */
.highlight-box {
    background: #fffdf5;
    border: 3px solid #FFD500;
    border-radius: 12px;
    padding: 32px;
    margin: 32px 0;
}

.highlight-box h4 {
    font-size: 1.3rem;
    font-weight: 800;
    margin-top: 0;
    margin-bottom: 20px;
    color: #1a1a1a;
}

/* 比較テーブル */
.comparison-table {
    width: 100%;
    border-collapse: collapse;
    margin: 32px 0;
    font-size: 1rem;
}

.comparison-table thead {
    background: #1a1a1a;
    color: #ffffff;
}

.comparison-table th {
    padding: 16px;
    text-align: left;
    font-weight: 700;
}

.comparison-table td {
    padding: 16px;
    border: 1px solid #e0e0e0;
}

.comparison-table tbody tr:nth-child(even) {
    background: #f9f9f9;
}

/* 例示ボックス */
.example-box {
    background: #f9f9f9;
    border-left: 4px solid #1a1a1a;
    padding: 24px 28px;
    margin: 32px 0;
    border-radius: 0 8px 8px 0;
}

.example-box h4 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-top: 0;
    margin-bottom: 16px;
    color: #1a1a1a;
}

/* メリット・デメリットリスト */
.merit-list h4,
.demerit-list h4 {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 32px 0 20px;
    color: #1a1a1a;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* プロセスフロー */
.process-flow {
    margin: 40px 0;
}

.step {
    display: flex;
    gap: 24px;
    margin-bottom: 32px;
    padding: 28px;
    background: #f9f9f9;
    border-radius: 12px;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
}

.step:hover {
    border-color: #FFD500;
    box-shadow: 0 4px 20px rgba(255, 213, 0, 0.15);
}

.step-number {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    background: #FFD500;
    color: #1a1a1a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 900;
}

.step-content h3 {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0 0 16px;
    color: #1a1a1a;
}

.step-content ul {
    margin: 0;
    padding-left: 20px;
}

.duration {
    margin-top: 16px;
    font-weight: 600;
    color: #666;
}

/* サイズ基準テーブル */
.size-criteria table {
    width: 100%;
    border-collapse: collapse;
    margin: 24px 0;
}

.size-criteria th,
.size-criteria td {
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    text-align: left;
}

.size-criteria thead {
    background: #1a1a1a;
    color: #ffffff;
}

.size-criteria tbody tr:nth-child(even) {
    background: #f9f9f9;
}

/* ヒントセクション */
.tips-section h4,
.writing-tips h4,
.failure-patterns h4 {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 32px 0 20px;
    color: #1a1a1a;
}

/* 警告ボックス */
.warning-box {
    background: #fff5f5;
    border: 3px solid #ef4444;
    border-radius: 12px;
    padding: 32px;
    margin: 40px 0;
}

.warning-box h3 {
    font-size: 1.4rem;
    font-weight: 800;
    color: #dc2626;
    margin-top: 0;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.warning-box h3::before {
    content: '!';
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #ef4444;
    color: #ffffff;
    border-radius: 50%;
    font-size: 1.3rem;
    font-weight: 900;
}

/* 次のステップセクション */
.next-steps {
    background: #f9f9f9;
    border-radius: 20px;
    padding: 60px 40px;
    margin-top: 80px;
}

.next-steps h2 {
    font-size: 2rem;
    font-weight: 800;
    text-align: center;
    margin-bottom: 24px;
    color: #1a1a1a;
    border: none;
}

.next-steps > p {
    text-align: center;
    font-size: 1.1rem;
    margin-bottom: 40px;
    color: #333;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 60px;
}

.btn {
    display: inline-block;
    padding: 16px 40px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #FFD500;
    color: #1a1a1a;
}

.btn-primary:hover {
    background: #1a1a1a;
    color: #FFD500;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.btn-secondary {
    background: #ffffff;
    color: #1a1a1a;
    border: 2px solid #1a1a1a;
}

.btn-secondary:hover {
    background: #1a1a1a;
    color: #ffffff;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.btn-outline {
    background: transparent;
    color: #1a1a1a;
    border: 2px solid #1a1a1a;
}

.btn-outline:hover {
    background: #1a1a1a;
    color: #ffffff;
}

.support-info {
    text-align: center;
    padding-top: 40px;
    border-top: 2px solid #e0e0e0;
}

.support-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 16px;
    color: #1a1a1a;
}

.support-info p {
    margin-bottom: 24px;
}

/* トップへ戻るボタン */
.back-to-top {
    position: fixed;
    bottom: 40px;
    right: 40px;
    width: 56px;
    height: 56px;
    background: #FFD500;
    color: #1a1a1a;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
}

.back-to-top.visible {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    background: #1a1a1a;
    color: #FFD500;
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
}

/* レスポンシブ */
@media (max-width: 768px) {
    .basics-hero {
        padding: 80px 20px 60px;
    }

    .contents-nav {
        padding: 24px 20px;
    }

    .basics-content {
        padding: 60px 20px;
    }

    .basics-content h2 {
        font-size: 1.8rem;
    }

    .basics-content h3 {
        font-size: 1.4rem;
    }

    .basics-content h4 {
        font-size: 1.2rem;
    }

    .step {
        flex-direction: column;
        padding: 20px;
    }

    .comparison-table {
        font-size: 0.9rem;
    }

    .comparison-table th,
    .comparison-table td {
        padding: 12px 8px;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        text-align: center;
    }

    .back-to-top {
        bottom: 24px;
        right: 24px;
        width: 48px;
        height: 48px;
    }
}

/* 印刷スタイル */
@media print {
    .basics-hero,
    .next-steps,
    .back-to-top {
        display: none;
    }

    .basics-content {
        padding: 0;
    }
}
</style>

<div class="basics-page">
    <!-- ヒーローセクション -->
    <section class="basics-hero">
        <h1 class="basics-hero-title">補助金・助成金の基礎知識</h1>
        <p class="basics-hero-lead">
            補助金・助成金とは何か、どのような制度があるのか、申請から受給までの流れを初心者の方にもわかりやすく解説します。事業資金調達の選択肢として、ぜひご活用ください。
        </p>
    </section>

    <!-- メインコンテンツ -->
    <div class="basics-content">

        <!-- 目次 -->
        <nav class="contents-nav">
            <h2>目次</h2>
            <ol>
                <li><a href="#definition">補助金・助成金とは</a></li>
                <li><a href="#difference">補助金と助成金の違い</a></li>
                <li><a href="#types">主な制度の種類</a></li>
                <li><a href="#benefits">メリットとデメリット</a></li>
                <li><a href="#process">申請から受給までの流れ</a></li>
                <li><a href="#requirements">一般的な申請要件</a></li>
                <li><a href="#tips">申請成功のコツ</a></li>
                <li><a href="#attention">注意点とリスク</a></li>
            </ol>
        </nav>

        <section id="definition">
            <h2>補助金・助成金とは</h2>
            
            <h3>基本的な定義</h3>
            <p>補助金・助成金とは、国や地方自治体が特定の政策目的を達成するために、民間企業や個人事業主、団体等に対して交付する給付金です。融資とは異なり、<strong>原則として返済不要</strong>の資金支援制度です。</p>

            <div class="highlight-box">
                <h4>重要なポイント</h4>
                <ul>
                    <li><strong>返済不要</strong>：条件を満たせば返済義務はありません</li>
                    <li><strong>政策実現の手段</strong>：国や自治体の政策目標達成が目的</li>
                    <li><strong>公募制</strong>：多くは期間限定で公募され、審査により採択</li>
                    <li><strong>事後精算</strong>：多くの場合、事業完了後に実績に基づき交付</li>
                </ul>
            </div>

            <h3>制度の目的</h3>
            <p>補助金・助成金制度は、以下のような政策目的で設計されています：</p>
            <ul>
                <li><strong>経済活性化</strong>：中小企業の投資促進、新事業創出支援</li>
                <li><strong>雇用促進</strong>：失業者の就職支援、人材育成の推進</li>
                <li><strong>技術革新</strong>：研究開発の促進、イノベーション創出</li>
                <li><strong>地域振興</strong>：地方創生、地域産業の活性化</li>
                <li><strong>社会課題解決</strong>：環境対策、少子高齢化対応、DX推進</li>
            </ul>
        </section>

        <section id="difference">
            <h2>補助金と助成金の違い</h2>
            
            <p>「補助金」と「助成金」は似た制度ですが、運用面で重要な違いがあります。</p>

            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>項目</th>
                        <th>補助金</th>
                        <th>助成金</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>主な所管</td>
                        <td>経済産業省、自治体等</td>
                        <td>厚生労働省</td>
                    </tr>
                    <tr>
                        <td>審査</td>
                        <td>競争的審査あり</td>
                        <td>要件を満たせば原則支給</td>
                    </tr>
                    <tr>
                        <td>採択率</td>
                        <td>制度により異なる（10-50%程度）</td>
                        <td>要件充足で高確率</td>
                    </tr>
                    <tr>
                        <td>募集期間</td>
                        <td>期間限定（締切あり）</td>
                        <td>通年募集が多い</td>
                    </tr>
                    <tr>
                        <td>主な目的</td>
                        <td>事業成長、技術革新</td>
                        <td>雇用促進、労働環境改善</td>
                    </tr>
                </tbody>
            </table>

            <h3>具体例</h3>
            <div class="example-box">
                <h4>補助金の例</h4>
                <ul>
                    <li>ものづくり補助金（設備投資支援）</li>
                    <li>事業再構築補助金（事業転換支援）</li>
                    <li>IT導入補助金（IT投資支援）</li>
                    <li>小規模事業者持続化補助金（販路開拓支援）</li>
                </ul>

                <h4>助成金の例</h4>
                <ul>
                    <li>キャリアアップ助成金（非正規雇用者の処遇改善）</li>
                    <li>人材開発支援助成金（従業員訓練支援）</li>
                    <li>両立支援等助成金（育児・介護との両立支援）</li>
                    <li>トライアル雇用助成金（試行雇用支援）</li>
                </ul>
            </div>
        </section>

        <section id="types">
            <h2>主な制度の種類</h2>

            <h3>実施機関別分類</h3>
            
            <h4>国（各省庁）の制度</h4>
            <ul>
                <li><strong>経済産業省</strong>：中小企業支援、技術開発、エネルギー関連</li>
                <li><strong>厚生労働省</strong>：雇用関連、働き方改革、人材育成</li>
                <li><strong>国土交通省</strong>：運輸・物流、建設・住宅、観光関連</li>
                <li><strong>農林水産省</strong>：農業、林業、水産業の振興</li>
                <li><strong>総務省</strong>：ICT活用、地域振興、消防・防災</li>
            </ul>

            <h4>地方自治体の制度</h4>
            <ul>
                <li><strong>都道府県</strong>：広域的な産業振興、大規模プロジェクト支援</li>
                <li><strong>市区町村</strong>：地域密着型支援、住民生活向上</li>
            </ul>

            <h3>目的別分類</h3>

            <h4>事業成長支援</h4>
            <ul>
                <li>設備投資補助金：生産設備、IT機器等の導入支援</li>
                <li>販路開拓補助金：展示会出展、広告宣伝等の支援</li>
                <li>研究開発補助金：新技術・新製品の開発支援</li>
            </ul>

            <h4>働き方・雇用支援</h4>
            <ul>
                <li>雇用創出助成金：新規雇用の促進</li>
                <li>人材育成助成金：従業員のスキルアップ支援</li>
                <li>働き方改革助成金：労働環境の改善支援</li>
            </ul>

            <h4>社会課題対応</h4>
            <ul>
                <li>環境・省エネ補助金：脱炭素、省エネ設備導入</li>
                <li>DX推進補助金：デジタル化の促進</li>
                <li>事業承継補助金：後継者への事業引継ぎ支援</li>
            </ul>
        </section>

        <section id="benefits">
            <h2>メリットとデメリット</h2>

            <h3>メリット</h3>
            <div class="merit-list">
                <h4>資金面のメリット</h4>
                <ul>
                    <li><strong>返済不要</strong>：条件を満たせば返済義務なし</li>
                    <li><strong>まとまった金額</strong>：数十万円～数億円規模の支援</li>
                    <li><strong>自己負担軽減</strong>：投資リスクの軽減効果</li>
                </ul>

                <h4>事業面のメリット</h4>
                <ul>
                    <li><strong>投資促進効果</strong>：設備投資や新事業への後押し</li>
                    <li><strong>信用度向上</strong>：採択実績による対外的信用力向上</li>
                    <li><strong>ネットワーク拡大</strong>：支援機関や他の採択者との関係構築</li>
                </ul>

                <h4>経営面のメリット</h4>
                <ul>
                    <li><strong>計画策定効果</strong>：申請を通じた事業計画の精緻化</li>
                    <li><strong>PDCAサイクル</strong>：定期報告を通じた事業管理の向上</li>
                    <li><strong>専門家支援</strong>：申請支援を通じた外部専門家との連携</li>
                </ul>
            </div>

            <h3>デメリット・注意点</h3>
            <div class="demerit-list">
                <h4>手続き面の負担</h4>
                <ul>
                    <li><strong>申請準備負荷</strong>：詳細な計画書作成や書類準備</li>
                    <li><strong>審査期間</strong>：申請から採択まで数ヶ月を要する</li>
                    <li><strong>報告義務</strong>：事業期間中の定期的な進捗報告</li>
                </ul>

                <h4>資金面のリスク</h4>
                <ul>
                    <li><strong>事前負担</strong>：多くは精算払いのため初期費用が必要</li>
                    <li><strong>返還リスク</strong>：条件違反時の補助金返還義務</li>
                    <li><strong>課税対象</strong>：補助金は原則として課税所得</li>
                </ul>

                <h4>運用面の制約</h4>
                <ul>
                    <li><strong>用途制限</strong>：補助対象経費以外には使用不可</li>
                    <li><strong>事業期間</strong>：定められた期間内での事業完了義務</li>
                    <li><strong>変更制限</strong>：事業内容変更時の承認手続き</li>
                </ul>
            </div>
        </section>

        <section id="process">
            <h2>申請から受給までの流れ</h2>

            <div class="process-flow">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>情報収集・制度選択</h3>
                        <ul>
                            <li>事業目的に合致する制度の調査</li>
                            <li>申請要件・スケジュールの確認</li>
                            <li>補助率・上限額の確認</li>
                        </ul>
                        <p class="duration">期間：1-2週間</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>申請準備</h3>
                        <ul>
                            <li>事業計画書の作成</li>
                            <li>必要書類の収集・作成</li>
                            <li>見積書の取得</li>
                        </ul>
                        <p class="duration">期間：2-4週間</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>申請提出</h3>
                        <ul>
                            <li>電子申請システムでの提出（多くの制度）</li>
                            <li>郵送・持参での提出</li>
                            <li>提出期限の厳守</li>
                        </ul>
                        <p class="duration">期間：1日</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>審査・採択発表</h3>
                        <ul>
                            <li>書面審査（全案件）</li>
                            <li>面接審査（一部制度）</li>
                            <li>採択・不採択の通知</li>
                        </ul>
                        <p class="duration">期間：1-3ヶ月</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h3>交付申請・決定</h3>
                        <ul>
                            <li>詳細な事業計画の提出</li>
                            <li>交付決定通知の受領</li>
                            <li>補助事業の開始準備</li>
                        </ul>
                        <p class="duration">期間：2-4週間</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">6</div>
                    <div class="step-content">
                        <h3>事業実施</h3>
                        <ul>
                            <li>交付決定後の事業着手</li>
                            <li>計画に沿った事業の実施</li>
                            <li>適切な経費管理・証拠保全</li>
                        </ul>
                        <p class="duration">期間：6ヶ月-2年（制度により異なる）</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">7</div>
                    <div class="step-content">
                        <h3>実績報告・検査</h3>
                        <ul>
                            <li>事業完了後の実績報告書提出</li>
                            <li>領収書等の証拠書類提出</li>
                            <li>必要に応じて現地検査</li>
                        </ul>
                        <p class="duration">期間：1-2ヶ月</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">8</div>
                    <div class="step-content">
                        <h3>補助金支払い</h3>
                        <ul>
                            <li>補助金額の確定</li>
                            <li>指定口座への振込</li>
                            <li>事業完了・支払完了</li>
                        </ul>
                        <p class="duration">期間：2-4週間</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="requirements">
            <h2>一般的な申請要件</h2>

            <h3>事業者要件</h3>
            <h4>規模要件</h4>
            <ul>
                <li><strong>中小企業</strong>：業種別の資本金・従業員数基準</li>
                <li><strong>小規模事業者</strong>：より厳格な従業員数基準</li>
                <li><strong>個人事業主</strong>：開業届の提出等</li>
            </ul>

            <div class="size-criteria">
                <h4>中小企業の定義（主要業種）</h4>
                <table>
                    <thead>
                        <tr><th>業種</th><th>資本金</th><th>従業員数</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>製造業</td><td>3億円以下</td><td>300人以下</td></tr>
                        <tr><td>卸売業</td><td>1億円以下</td><td>100人以下</td></tr>
                        <tr><td>小売業</td><td>5千万円以下</td><td>50人以下</td></tr>
                        <tr><td>サービス業</td><td>5千万円以下</td><td>100人以下</td></tr>
                    </tbody>
                </table>
            </div>

            <h4>経営状況要件</h4>
            <ul>
                <li><strong>継続性</strong>：一定期間以上の事業継続実績</li>
                <li><strong>納税状況</strong>：税務申告・納税の適正履行</li>
                <li><strong>財務健全性</strong>：債務超過でないこと等</li>
            </ul>

            <h3>事業要件</h3>
            <h4>事業内容</h4>
            <ul>
                <li><strong>政策適合性</strong>：制度目的に合致した事業内容</li>
                <li><strong>革新性</strong>：従来にない新たな取組み</li>
                <li><strong>実現可能性</strong>：計画の妥当性・実行力</li>
            </ul>

            <h4>効果要件</h4>
            <ul>
                <li><strong>定量効果</strong>：売上増加、雇用創出等の数値目標</li>
                <li><strong>波及効果</strong>：地域経済や社会への貢献</li>
                <li><strong>継続性</strong>：事業の持続可能性</li>
            </ul>

            <h3>除外要件</h3>
            <p>以下に該当する場合は申請できません：</p>
            <ul>
                <li>暴力団関係者</li>
                <li>重大な法令違反の履歴</li>
                <li>税金の滞納</li>
                <li>同一事業での他制度利用</li>
                <li>公的資金の不正受給歴</li>
            </ul>
        </section>

        <section id="tips">
            <h2>申請成功のコツ</h2>

            <h3>事前準備のポイント</h3>
            <div class="tips-section">
                <h4>制度選択</h4>
                <ul>
                    <li><strong>目的の明確化</strong>：何のための資金調達かを明確にする</li>
                    <li><strong>複数制度の比較</strong>：複数の制度を比較検討する</li>
                    <li><strong>過去の採択事例研究</strong>：採択事例の内容を参考にする</li>
                </ul>

                <h4>計画策定</h4>
                <ul>
                    <li><strong>現状分析の徹底</strong>：自社の強み・弱み・市場環境の分析</li>
                    <li><strong>目標設定の具体化</strong>：達成可能で挑戦的な数値目標</li>
                    <li><strong>実施体制の確立</strong>：必要な人材・組織体制の整備</li>
                </ul>

                <h4>差別化戦略</h4>
                <ul>
                    <li><strong>独自性の訴求</strong>：他社にない自社ならではの特徴</li>
                    <li><strong>社会性の強調</strong>：地域や社会への貢献度</li>
                    <li><strong>実績・経験の活用</strong>：これまでの事業実績の効果的な提示</li>
                </ul>
            </div>

            <h3>申請書作成のコツ</h3>
            <div class="writing-tips">
                <h4>記載のポイント</h4>
                <ul>
                    <li><strong>結論ファースト</strong>：要点を冒頭で明確に示す</li>
                    <li><strong>根拠の明示</strong>：主張には必ず根拠を付ける</li>
                    <li><strong>数値の活用</strong>：可能な限り定量的に表現する</li>
                    <li><strong>図表の効果的利用</strong>：視覚的に分かりやすく表現</li>
                </ul>

                <h4>審査員目線</h4>
                <ul>
                    <li><strong>審査基準の理解</strong>：公表されている審査基準を熟読</li>
                    <li><strong>第三者視点</strong>：専門知識のない人でも理解できる表現</li>
                    <li><strong>リスクへの言及</strong>：想定されるリスクと対策の記載</li>
                </ul>
            </div>

            <h3>よくある失敗パターン</h3>
            <div class="failure-patterns">
                <h4>避けるべきミス</h4>
                <ul>
                    <li><strong>要件未確認</strong>：申請要件の見落としや誤解</li>
                    <li><strong>期限遅れ</strong>：提出期限に間に合わない</li>
                    <li><strong>書類不備</strong>：必要書類の不足や記載漏れ</li>
                    <li><strong>計画の非現実性</strong>：実現困難な過大な目標設定</li>
                    <li><strong>独自性不足</strong>：他社と差別化されていない内容</li>
                </ul>
            </div>
        </section>

        <section id="attention">
            <h2>注意点とリスク</h2>

            <h3>申請時の注意点</h3>
            <h4>書類作成時</h4>
            <ul>
                <li><strong>虚偽記載の禁止</strong>：事実と異なる記載は重大な違反</li>
                <li><strong>整合性の確保</strong>：申請書内での数値・内容の一貫性</li>
                <li><strong>期限の厳守</strong>：締切時刻（多くは17時）の確認</li>
            </ul>

            <h4>提出前チェック</h4>
            <ul>
                <li>必要書類の完備確認</li>
                <li>記載内容の最終確認</li>
                <li>電子申請システムの動作確認</li>
            </ul>

            <h3>採択後のリスク管理</h3>
            <h4>事業実施上のリスク</h4>
            <ul>
                <li><strong>計画変更リスク</strong>：市場環境変化による計画修正の必要性</li>
                <li><strong>コスト超過リスク</strong>：見積もり以上の費用発生</li>
                <li><strong>期間遅延リスク</strong>：予定どおり事業が進まない可能性</li>
            </ul>

            <h4>コンプライアンスリスク</h4>
            <ul>
                <li><strong>補助金返還</strong>：条件違反による全額または一部返還</li>
                <li><strong>加算金</strong>：悪質な場合の利息相当額の追加負担</li>
                <li><strong>信用失墜</strong>：不正受給による社会的信用の低下</li>
            </ul>

            <h3>税務上の注意点</h3>
            <h4>課税関係</h4>
            <ul>
                <li><strong>法人税・所得税</strong>：補助金は原則として課税所得</li>
                <li><strong>消費税</strong>：補助金に消費税は課されない</li>
                <li><strong>圧縮記帳</strong>：設備取得の場合の特例適用</li>
            </ul>

            <h4>会計処理</h4>
            <ul>
                <li><strong>収益計上時期</strong>：補助金確定時または入金時</li>
                <li><strong>仮受金処理</strong>：概算払いを受けた場合の処理</li>
                <li><strong>返還引当金</strong>：返還リスクがある場合の引当処理</li>
            </ul>

            <div class="warning-box">
                <h3>重要な注意事項</h3>
                <p>補助金・助成金の不正受給は刑事罰の対象となる場合があります。また、一度不正受給を行うと、将来にわたって他の制度への申請が制限される可能性があります。必ず制度のルールを遵守し、適正な申請・事業実施を行ってください。</p>
            </div>
        </section>

        <section class="next-steps">
            <h2>次のステップ</h2>
            <p>補助金・助成金の基礎知識を学んだら、実際に申請に向けて行動を開始しましょう。</p>
            
            <div class="action-buttons">
                <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="btn btn-primary">補助金を探す</a>
                <a href="<?php echo esc_url(home_url('/glossary/')); ?>" class="btn btn-secondary">用語集を見る</a>
                <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="btn btn-secondary">よくある質問</a>
            </div>

            <div class="support-info">
                <h3>申請サポートをご希望の方</h3>
                <p>補助金申請に不安がある方、より確実な採択を目指したい方は、専門家による申請サポートサービスもご利用いただけます。</p>
                <a href="<?php echo esc_url(home_url('/support/')); ?>" class="btn btn-outline">申請サポートについて</a>
            </div>
        </section>

    </div>
</div>

<!-- トップへ戻るボタン -->
<button id="back-to-top" class="back-to-top" aria-label="ページトップへ戻る">
    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // トップへ戻るボタン
    const backToTopBtn = document.getElementById('back-to-top');
    
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
    
    // スムーススクロール
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href !== '#' && href !== '#0') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const offset = 100;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    console.log('[OK] Basics Page initialized - 補助金の基礎知識ページ');
});
</script>

<?php get_footer(); ?>
