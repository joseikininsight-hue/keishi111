<?php
/**
 * Template Name: Disclaimer Page (免責事項)
 * 
 * SEO特化の白黒ベース + イエローアクセントの免責事項ページ
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

get_header();

// SEO用メタ情報
$page_title = '免責事項 | ' . get_bloginfo('name');
$page_description = '補助金インサイトの免責事項。情報の正確性、利用上の注意事項、責任の限界、第三者サービスについて、技術的制約、法的事項について定めています。';
$page_keywords = '免責事項,補助金,助成金,利用規約,情報提供,責任範囲,注意事項';
$page_url = home_url('/disclaimer/');

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

<!-- Twitter Card -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo esc_attr($page_title); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($page_description); ?>">

<!-- 構造化データ（JSON-LD） -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "免責事項",
  "description": "<?php echo esc_js($page_description); ?>",
  "url": "<?php echo esc_url($page_url); ?>",
  "datePublished": "2025-11-01",
  "dateModified": "2025-11-01",
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
        "name": "免責事項",
        "item": "<?php echo esc_url($page_url); ?>"
      }
    ]
  }
}
</script>

<style>
/* ========== Disclaimer Page Styles ========== */

/* ベース設定 */
.disclaimer-page {
    background: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, 'Noto Sans JP', sans-serif;
    line-height: 1.8;
    color: #1a1a1a;
}

/* ヒーローセクション */
.disclaimer-hero {
    padding: 120px 20px 80px;
    text-align: center;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #ffffff;
    position: relative;
}

.disclaimer-hero::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255, 213, 0, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.disclaimer-hero-title {
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 900;
    margin-bottom: 24px;
    letter-spacing: -0.02em;
    position: relative;
}

.disclaimer-hero-subtitle {
    font-size: clamp(1rem, 2.5vw, 1.3rem);
    font-weight: 300;
    max-width: 700px;
    margin: 0 auto 32px;
    opacity: 0.9;
    line-height: 1.6;
    position: relative;
}

.last-updated {
    font-size: 1rem;
    color: #FFD500;
    font-weight: 600;
    position: relative;
}

/* メインコンテンツ */
.disclaimer-content {
    max-width: 900px;
    margin: 0 auto;
    padding: 80px 20px;
}

.disclaimer-content h2 {
    font-size: 2rem;
    font-weight: 800;
    margin: 60px 0 32px;
    padding-bottom: 16px;
    border-bottom: 4px solid #FFD500;
    color: #1a1a1a;
}

.disclaimer-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 40px 0 20px;
    color: #1a1a1a;
}

.disclaimer-content p {
    font-size: 1.05rem;
    line-height: 1.9;
    margin-bottom: 24px;
    color: #333;
}

.disclaimer-content ul {
    margin: 24px 0;
    padding-left: 28px;
}

.disclaimer-content li {
    font-size: 1.05rem;
    line-height: 1.9;
    margin-bottom: 16px;
    color: #333;
}

.disclaimer-content li strong {
    color: #1a1a1a;
    font-weight: 700;
}

.disclaimer-content a {
    color: #1a1a1a;
    text-decoration: underline;
    font-weight: 600;
    transition: color 0.3s ease;
}

.disclaimer-content a:hover {
    color: #FFD500;
}

/* 重要なお知らせボックス */
.important-notice {
    background: #fffdf5;
    border: 3px solid #FFD500;
    border-radius: 12px;
    padding: 32px;
    margin: 60px 0;
}

.important-notice h3 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1a1a1a;
    margin-top: 0;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.important-notice h3::before {
    content: '!';
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: #FFD500;
    color: #1a1a1a;
    border-radius: 50%;
    font-size: 1.3rem;
    font-weight: 900;
    flex-shrink: 0;
}

.important-notice p {
    font-size: 1.05rem;
    margin-bottom: 0;
}

/* ハイライトボックス */
.highlight-box {
    background: #f9f9f9;
    border-left: 4px solid #1a1a1a;
    padding: 24px 28px;
    margin: 32px 0;
    border-radius: 0 8px 8px 0;
}

.highlight-box h4 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-top: 0;
    margin-bottom: 16px;
    color: #1a1a1a;
}

/* CTAセクション */
.disclaimer-cta {
    margin-top: 80px;
    padding: 60px 40px;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 20px;
    text-align: center;
    color: #ffffff;
}

.disclaimer-cta-title {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 16px;
}

.disclaimer-cta-text {
    font-size: 1.1rem;
    margin-bottom: 32px;
    opacity: 0.9;
}

.disclaimer-cta-button {
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

.disclaimer-cta-button:hover {
    background: #ffffff;
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 213, 0, 0.3);
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
    font-size: 1.5rem;
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
    .disclaimer-hero {
        padding: 80px 20px 60px;
    }

    .disclaimer-content {
        padding: 60px 20px;
    }

    .disclaimer-content h2 {
        font-size: 1.6rem;
        margin: 40px 0 24px;
    }

    .disclaimer-content h3 {
        font-size: 1.3rem;
        margin: 32px 0 16px;
    }

    .disclaimer-content p,
    .disclaimer-content li {
        font-size: 1rem;
    }

    .important-notice {
        padding: 24px 20px;
        margin: 40px 0;
    }

    .disclaimer-cta {
        padding: 40px 24px;
    }

    .disclaimer-cta-title {
        font-size: 1.6rem;
    }

    .back-to-top {
        bottom: 24px;
        right: 24px;
        width: 48px;
        height: 48px;
        font-size: 1.3rem;
    }
}

/* 印刷スタイル */
@media print {
    .disclaimer-hero,
    .disclaimer-cta,
    .back-to-top {
        display: none;
    }

    .disclaimer-content {
        padding: 0;
    }
}
</style>

<div class="disclaimer-page">
    <!-- ヒーローセクション -->
    <section class="disclaimer-hero">
        <h1 class="disclaimer-hero-title">免責事項</h1>
        <p class="disclaimer-hero-subtitle">
            補助金インサイトをご利用いただく前に、必ずお読みください。
        </p>
        <p class="last-updated">最終更新日：2025年11月1日</p>
    </section>

    <!-- メインコンテンツ -->
    <div class="disclaimer-content">
        
        <h2>情報の正確性について</h2>
        
        <h3>情報収集と更新について</h3>
        <p>補助金インサイトは、各省庁・都道府県・市区町村が公式に発表する補助金・助成金情報を基に、データベースの構築・更新を行っております。情報の収集は以下の方法により実施しております：</p>
        <ul>
            <li>各行政機関の公式ウェブサイトからの情報取得</li>
            <li>公示・公告文書の定期的な確認</li>
            <li>制度所管部署からの直接情報提供</li>
            <li>専門スタッフによる情報の精査・検証</li>
        </ul>

        <h3>情報の制約</h3>
        <p>本サイトで提供する情報について、以下の制約があることをご理解ください：</p>
        <ul>
            <li><strong>完全性の制約</strong>：全ての補助金・助成金制度を網羅することを目指しておりますが、一部の制度が掲載されていない可能性があります</li>
            <li><strong>正確性の制約</strong>：情報は定期的に更新しておりますが、制度変更の反映に時間を要する場合があります</li>
            <li><strong>最新性の制約</strong>：制度内容は予告なく変更される場合があり、本サイトの情報と実際の制度内容に差異が生じる可能性があります</li>
        </ul>

        <h2>利用上の注意事項</h2>
        
        <h3>申請前の確認義務</h3>
        <p>補助金・助成金の申請を行う前には、以下の確認を必ず行ってください：</p>
        <ul>
            <li>制度所管機関の公式ウェブサイトでの最新情報確認</li>
            <li>募集要領・申請の手引きの詳細な読み込み</li>
            <li>申請条件・対象要件の詳細な確認</li>
            <li>提出書類・添付資料の内容確認</li>
            <li>申請期限・事業期間の正確な把握</li>
        </ul>

        <h3>申請結果について</h3>
        <p>本サイトの利用は、補助金・助成金の申請結果に影響を与えるものではありません：</p>
        <ul>
            <li>採択・不採択の結果については、制度所管機関の審査により決定されます</li>
            <li>本サイトの情報を参考に申請を行った場合でも、採択を保証するものではありません</li>
            <li>申請結果についての苦情・要望は、直接制度所管機関にお問い合わせください</li>
        </ul>

        <h2>責任の限界</h2>

        <h3>損害賠償の免責</h3>
        <p>本サイトの利用に関連して生じた以下の損害について、当社は一切の責任を負いません：</p>
        <ul>
            <li>情報の誤りや遅延により生じた機会損失</li>
            <li>申請の不採択により生じた損失</li>
            <li>制度変更により生じた準備費用の無駄</li>
            <li>本サイトの一時的な利用不能により生じた損失</li>
            <li>第三者による情報の不正利用により生じた損害</li>
        </ul>

        <h3>間接損害の免責</h3>
        <p>以下の間接的な損害についても、当社は責任を負いません：</p>
        <ul>
            <li>事業機会の逸失による損失</li>
            <li>第三者との契約不履行による損害</li>
            <li>信用失墜による損害</li>
            <li>精神的苦痛による慰謝料</li>
        </ul>

        <h2>第三者サービスについて</h2>

        <h3>外部リンク</h3>
        <p>本サイトには、制度所管機関や関連団体への外部リンクが含まれております：</p>
        <ul>
            <li>外部サイトの内容については、当該サイト運営者が責任を負います</li>
            <li>外部サイトの利用により生じた損害について、当社は責任を負いません</li>
            <li>外部サイトのプライバシーポリシーや利用規約をご確認の上、ご利用ください</li>
        </ul>

        <h3>専門家紹介サービス</h3>
        <p>本サイトが紹介する専門家・支援機関について：</p>
        <ul>
            <li>紹介された専門家との間で発生した問題については、当事者間で解決してください</li>
            <li>専門家の助言や作業結果について、当社は保証いたしません</li>
            <li>報酬や契約条件については、直接専門家との間で取り決めてください</li>
        </ul>

        <h2>技術的制約</h2>

        <h3>システム障害</h3>
        <p>以下の事由によりサービスが利用できない場合があります：</p>
        <ul>
            <li>サーバーメンテナンスや障害</li>
            <li>インターネット回線の不具合</li>
            <li>天災や停電等の不可抗力</li>
            <li>サイバー攻撃や不正アクセス</li>
        </ul>
        <p>これらの事由による利用不能について、当社は責任を負いません。</p>

        <h3>ブラウザ環境</h3>
        <p>本サイトは以下の環境での利用を推奨しております：</p>
        <ul>
            <li>Chrome 最新版</li>
            <li>Firefox 最新版</li>
            <li>Safari 最新版</li>
            <li>Microsoft Edge 最新版</li>
        </ul>
        <p>推奨環境以外での利用により生じた問題について、当社は責任を負いません。</p>

        <h2>法的事項</h2>

        <h3>準拠法</h3>
        <p>本免責事項の解釈・適用については、日本国法を準拠法とします。</p>

        <h3>管轄裁判所</h3>
        <p>本サイトの利用に関して紛争が生じた場合は、当社所在地を管轄する地方裁判所を第一審の専属的合意管轄裁判所とします。</p>

        <h2>免責事項の変更</h2>
        <p>本免責事項は、法令の変更や事業内容の変更に応じて、予告なく変更される場合があります。変更後の免責事項は、本ページへの掲載により効力を生じるものとします。</p>

        <h2>お問い合わせ</h2>
        <p>本免責事項に関するご質問やご不明な点がございましたら、<a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせページ</a>よりご連絡ください。</p>

        <div class="important-notice">
            <h3>重要なお知らせ</h3>
            <p>補助金・助成金の申請は、事業者の責任において行ってください。本サイトは情報提供を目的としており、申請の代行や成果の保証を行うものではありません。申請前には必ず制度所管機関の公式情報を確認し、不明な点は直接お問い合わせいただくことを強く推奨いたします。</p>
        </div>

        <!-- CTAセクション -->
        <section class="disclaimer-cta">
            <h2 class="disclaimer-cta-title">補助金申請サポート</h2>
            <p class="disclaimer-cta-text">
                補助金申請に不安がある方、専門家のサポートをご希望の方は<br>
                お気軽にお問い合わせください。
            </p>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="disclaimer-cta-button">
                お問い合わせはこちら
            </a>
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
    
    console.log('[OK] Disclaimer Page initialized - 免責事項ページ');
});
</script>

<?php get_footer(); ?>
