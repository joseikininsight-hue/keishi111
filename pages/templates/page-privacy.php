<?php
/**
 * 補助金・助成金情報サイト - プライバシーポリシーページ（完全版）
 * Grant & Subsidy Information Site - Privacy Policy Page (Complete)
 * @package Grant_Insight_Privacy
 * @version 1.0-complete
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

get_header(); // ヘッダーを読み込み

// 構造化データ
$privacy_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'プライバシーポリシー - 補助金インサイト',
    'description' => '補助金インサイトのプライバシーポリシー。個人情報の取り扱い、収集する情報、利用目的などについて定めています。',
    'url' => 'https://joseikin-insight.com/privacy/',
    'datePublished' => '2025-10-09',
    'dateModified' => '2025-10-09'
);
?>

<!-- SEO メタタグ -->
<title>プライバシーポリシー | 補助金インサイト - AI活用型補助金検索ポータル</title>
<meta name="description" content="補助金インサイトのプライバシーポリシー。個人情報の取り扱い、収集する情報、利用目的、第三者提供、セキュリティ対策などについて定めています。">
<meta name="keywords" content="プライバシーポリシー,個人情報保護,情報セキュリティ,補助金インサイト,個人情報の取り扱い">
<link rel="canonical" href="https://joseikin-insight.com/privacy/">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="プライバシーポリシー | 補助金インサイト">
<meta property="og:description" content="補助金インサイトのプライバシーポリシー。個人情報の取り扱いについて定めています。">
<meta property="og:url" content="https://joseikin-insight.com/privacy/">

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($privacy_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<article class="privacy-page" itemscope itemtype="https://schema.org/WebPage">
    
    <!-- ページヘッダー -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title" itemprop="headline">プライバシーポリシー</h1>
            <div class="page-meta">
                <p class="meta-item">制定日：<time datetime="2025-10-09">2025年10月9日</time></p>
                <p class="meta-item">最終改定日：<time datetime="2025-10-09">2025年10月9日</time></p>
            </div>
        </div>
    </header>
    
    <!-- メインコンテンツ -->
    <div class="page-content">
        <div class="container">
            
            <!-- 前文 -->
            <section class="content-section preamble-section">
                <div class="preamble-content">
                    <p>
                        補助金インサイト（以下「当サイト」といいます）は、運営者である中澤圭志（以下「当社」といいます）が、ユーザーの個人情報の重要性を認識し、個人情報の保護に関する法律（以下「個人情報保護法」といいます）を遵守すると共に、以下のプライバシーポリシー（以下「本ポリシー」といいます）に従い、適切な取扱い及び保護に努めます。
                    </p>
                </div>
            </section>
            
            <!-- 目次 -->
            <section class="content-section toc-section">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6"/>
                            <line x1="8" y1="12" x2="21" y2="12"/>
                            <line x1="8" y1="18" x2="21" y2="18"/>
                            <line x1="3" y1="6" x2="3.01" y2="6"/>
                            <line x1="3" y1="12" x2="3.01" y2="12"/>
                            <line x1="3" y1="18" x2="3.01" y2="18"/>
                        </svg>
                    </span>
                    目次
                </h2>
                <nav class="toc-nav">
                    <ul class="toc-list">
                        <li><a href="#section-1" class="toc-link">第1条（個人情報）</a></li>
                        <li><a href="#section-2" class="toc-link">第2条（個人情報の収集方法）</a></li>
                        <li><a href="#section-3" class="toc-link">第3条（個人情報を収集・利用する目的）</a></li>
                        <li><a href="#section-4" class="toc-link">第4条（利用目的の変更）</a></li>
                        <li><a href="#section-5" class="toc-link">第5条（個人情報の第三者提供）</a></li>
                        <li><a href="#section-6" class="toc-link">第6条（個人情報の開示）</a></li>
                        <li><a href="#section-7" class="toc-link">第7条（個人情報の訂正及び削除）</a></li>
                        <li><a href="#section-8" class="toc-link">第8条（個人情報の利用停止等）</a></li>
                        <li><a href="#section-9" class="toc-link">第9条（Cookie（クッキー）等の使用）</a></li>
                        <li><a href="#section-10" class="toc-link">第10条（アクセス解析ツール）</a></li>
                        <li><a href="#section-11" class="toc-link">第11条（プライバシーポリシーの変更）</a></li>
                        <li><a href="#section-12" class="toc-link">第12条（お問い合わせ窓口）</a></li>
                    </ul>
                </nav>
            </section>
            
            <!-- 第1条 -->
            <section class="content-section policy-section" id="section-1">
                <h2 class="policy-title">第1条（個人情報）</h2>
                <div class="policy-content">
                    <p>
                        「個人情報」とは、個人情報保護法にいう「個人情報」を指すものとし、生存する個人に関する情報であって、当該情報に含まれる氏名、生年月日、住所、電話番号、連絡先その他の記述等により特定の個人を識別できる情報及び容貌、指紋、声紋にかかるデータ、及び健康保険証の保険者番号などの当該情報単体から特定の個人を識別できる情報（個人識別情報）を指します。
                    </p>
                </div>
            </section>
            
            <!-- 第2条 -->
            <section class="content-section policy-section" id="section-2">
                <h2 class="policy-title">第2条（個人情報の収集方法）</h2>
                <div class="policy-content">
                    <p>当サイトは、ユーザーが利用登録をする際に氏名、メールアドレス、会社名、電話番号などの個人情報をお尋ねすることがあります。また、ユーザーと提携先などとの間でなされたユーザーの個人情報を含む取引記録や決済に関する情報を、当サイトの提携先（情報提供元、広告主、広告配信先などを含みます。以下「提携先」といいます）などから収集することがあります。</p>
                    
                    <h3 class="subsection-title">収集する個人情報の種類</h3>
                    <ul class="info-list">
                        <li>氏名（法人の場合は担当者名）</li>
                        <li>メールアドレス</li>
                        <li>電話番号</li>
                        <li>会社名・団体名</li>
                        <li>住所</li>
                        <li>業種・従業員数等の属性情報</li>
                        <li>その他、サービス提供に必要な情報</li>
                    </ul>
                </div>
            </section>
            
            <!-- 第3条 -->
            <section class="content-section policy-section" id="section-3">
                <h2 class="policy-title">第3条（個人情報を収集・利用する目的）</h2>
                <div class="policy-content">
                    <p>当サイトが個人情報を収集・利用する目的は、以下のとおりです。</p>
                    
                    <ol class="purpose-list">
                        <li>当サイトのサービスの提供・運営のため</li>
                        <li>ユーザーからのお問い合わせに回答するため（本人確認を行うことを含む）</li>
                        <li>ユーザーが利用中のサービスの新機能、更新情報、キャンペーン等及び当サイトが提供する他のサービスの案内のメールを送付するため</li>
                        <li>メンテナンス、重要なお知らせなど必要に応じたご連絡のため</li>
                        <li>利用規約に違反したユーザーや、不正・不当な目的でサービスを利用しようとするユーザーの特定をし、ご利用をお断りするため</li>
                        <li>ユーザーにご自身の登録情報の閲覧や変更、削除、ご利用状況の閲覧を行っていただくため</li>
                        <li>サービスの改善・新サービスの開発のための分析を行うため</li>
                        <li>補助金・助成金情報のマッチング精度向上のため</li>
                        <li>上記の利用目的に付随する目的</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第4条 -->
            <section class="content-section policy-section" id="section-4">
                <h2 class="policy-title">第4条（利用目的の変更）</h2>
                <div class="policy-content">
                    <ol class="article-list">
                        <li>当サイトは、利用目的が変更前と関連性を有すると合理的に認められる場合に限り、個人情報の利用目的を変更するものとします。</li>
                        <li>利用目的の変更を行った場合には、変更後の目的について、当サイト所定の方法により、ユーザーに通知し、または本ウェブサイト上に公表するものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第5条 -->
            <section class="content-section policy-section highlight-section" id="section-5">
                <h2 class="policy-title">第5条（個人情報の第三者提供）</h2>
                <div class="policy-content">
                    <ol class="article-list">
                        <li>当サイトは、次に掲げる場合を除いて、あらかじめユーザーの同意を得ることなく、第三者に個人情報を提供することはありません。ただし、個人情報保護法その他の法令で認められる場合を除きます。
                            <ul class="sub-list">
                                <li>人の生命、身体または財産の保護のために必要がある場合であって、本人の同意を得ることが困難であるとき</li>
                                <li>公衆衛生の向上または児童の健全な育成の推進のために特に必要がある場合であって、本人の同意を得ることが困難であるとき</li>
                                <li>国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合であって、本人の同意を得ることにより当該事務の遂行に支障を及ぼすおそれがあるとき</li>
                                <li>予め次の事項を告知あるいは公表し、かつ当サイトが個人情報保護委員会に届出をしたとき
                                    <ul class="sub-sub-list">
                                        <li>利用目的に第三者への提供を含むこと</li>
                                        <li>第三者に提供されるデータの項目</li>
                                        <li>第三者への提供の手段または方法</li>
                                        <li>本人の求めに応じて個人情報の第三者への提供を停止すること</li>
                                        <li>本人の求めを受け付ける方法</li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>前項の定めにかかわらず、次に掲げる場合には、当該情報の提供先は第三者に該当しないものとします。
                            <ul class="sub-list">
                                <li>当サイトが利用目的の達成に必要な範囲内において個人情報の取扱いの全部または一部を委託する場合</li>
                                <li>合併その他の事由による事業の承継に伴って個人情報が提供される場合</li>
                                <li>個人情報を特定の者との間で共同して利用する場合であって、その旨並びに共同して利用される個人情報の項目、共同して利用する者の範囲、利用する者の利用目的および当該個人情報の管理について責任を有する者の氏名または名称について、あらかじめ本人に通知し、または本人が容易に知り得る状態に置いた場合</li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </section>
            
            <!-- 第6条 -->
            <section class="content-section policy-section" id="section-6">
                <h2 class="policy-title">第6条（個人情報の開示）</h2>
                <div class="policy-content">
                    <ol class="article-list">
                        <li>当サイトは、本人から個人情報の開示を求められたときは、本人に対し、遅滞なくこれを開示します。ただし、開示することにより次のいずれかに該当する場合は、その全部または一部を開示しないこともあり、開示しない決定をした場合には、その旨を遅滞なく通知します。
                            <ul class="sub-list">
                                <li>本人または第三者の生命、身体、財産その他の権利利益を害するおそれがある場合</li>
                                <li>当サイトの業務の適正な実施に著しい支障を及ぼすおそれがある場合</li>
                                <li>その他法令に違反することとなる場合</li>
                            </ul>
                        </li>
                        <li>前項の定めにかかわらず、履歴情報および特性情報などの個人情報以外の情報については、原則として開示いたしません。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第7条 -->
            <section class="content-section policy-section" id="section-7">
                <h2 class="policy-title">第7条（個人情報の訂正及び削除）</h2>
                <div class="policy-content">
                    <ol class="article-list">
                        <li>ユーザーは、当サイトの保有する自己の個人情報が誤った情報である場合には、当サイトが定める手続きにより、当サイトに対して個人情報の訂正、追加または削除（以下「訂正等」といいます）を請求することができます。</li>
                        <li>当サイトは、ユーザーから前項の請求を受けてその請求に応じる必要があると判断した場合には、遅滞なく、当該個人情報の訂正等を行うものとします。</li>
                        <li>当サイトは、前項の規定に基づき訂正等を行った場合、または訂正等を行わない旨の決定をしたときは遅滞なく、これをユーザーに通知します。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第8条 -->
            <section class="content-section policy-section" id="section-8">
                <h2 class="policy-title">第8条（個人情報の利用停止等）</h2>
                <div class="policy-content">
                    <ol class="article-list">
                        <li>当サイトは、本人から、個人情報が、利用目的の範囲を超えて取り扱われているという理由、または不正の手段により取得されたものであるという理由により、その利用の停止または消去（以下「利用停止等」といいます）を求められた場合には、遅滞なく必要な調査を行います。</li>
                        <li>前項の調査結果に基づき、その請求に応じる必要があると判断した場合には、遅滞なく、当該個人情報の利用停止等を行います。</li>
                        <li>当サイトは、前項の規定に基づき利用停止等を行った場合、または利用停止等を行わない旨の決定をしたときは、遅滞なく、これをユーザーに通知します。</li>
                        <li>前2項にかかわらず、利用停止等に多額の費用を有する場合その他利用停止等を行うことが困難な場合であって、ユーザーの権利利益を保護するために必要なこれに代わるべき措置をとれる場合は、この代替策を講じるものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第9条 -->
            <section class="content-section policy-section" id="section-9">
                <h2 class="policy-title">第9条（Cookie（クッキー）等の使用）</h2>
                <div class="policy-content">
                    <ol class="article-list">
                        <li>当サイトは、ユーザーによるサービスの利用状況を把握するため、Cookie（クッキー）を使用することがあります。Cookieとは、ウェブサーバーからユーザーのブラウザに送信され、ユーザーが使用しているコンピュータのハードディスクに保存される情報です。</li>
                        <li>Cookieには個人を特定する情報は含まれませんが、当サイトが保有する個人情報と関連付けられる場合があります。</li>
                        <li>ユーザーは、ブラウザの設定によりCookieの受け取りを拒否することができます。ただし、Cookieを拒否した場合、当サイトの一部のサービスが利用できなくなる場合があります。</li>
                    </ol>
                    
                    <h3 class="subsection-title">Cookieの利用目的</h3>
                    <ul class="info-list">
                        <li>ユーザーの利便性向上（ログイン状態の保持など）</li>
                        <li>サイトの利用状況の分析</li>
                        <li>サービスの改善・最適化</li>
                        <li>広告配信の最適化</li>
                    </ul>
                </div>
            </section>
            
            <!-- 第10条 -->
            <section class="content-section policy-section" id="section-10">
                <h2 class="policy-title">第10条（アクセス解析ツール）</h2>
                <div class="policy-content">
                    <p>当サイトでは、Googleによるアクセス解析ツール「Googleアナリティクス」を使用しています。このGoogleアナリティクスはデータの収集のためにCookieを使用しています。このデータは匿名で収集されており、個人を特定するものではありません。</p>
                    
                    <p>この機能はCookieを無効にすることで収集を拒否することが出来ますので、お使いのブラウザの設定をご確認ください。この規約に関しての詳細は<a href="https://marketingplatform.google.com/about/analytics/terms/jp/" target="_blank" rel="noopener noreferrer" class="text-link">Googleアナリティクスサービス利用規約</a>のページや<a href="https://policies.google.com/technologies/ads?hl=ja" target="_blank" rel="noopener noreferrer" class="text-link">Googleポリシーと規約</a>ページをご覧ください。</p>
                    
                    <h3 class="subsection-title">使用しているアクセス解析ツール</h3>
                    <ul class="info-list">
                        <li>Googleアナリティクス</li>
                        <li>その他、サービス改善のための解析ツール</li>
                    </ul>
                </div>
            </section>
            
            <!-- 第11条 -->
            <section class="content-section policy-section" id="section-11">
                <h2 class="policy-title">第11条（プライバシーポリシーの変更）</h2>
                <div class="policy-content">
                    <ol class="article-list">
                        <li>本ポリシーの内容は、法令その他本ポリシーに別段の定めのある事項を除いて、ユーザーに通知することなく、変更することができるものとします。</li>
                        <li>当サイトが別途定める場合を除いて、変更後のプライバシーポリシーは、本ウェブサイトに掲載したときから効力を生じるものとします。</li>
                        <li>本ポリシーの変更は、変更後のプライバシーポリシーが当サイトに掲載された時点で有効になるものとします。ユーザーは定期的に本ポリシーを確認し、最新の内容を把握するものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第12条 -->
            <section class="content-section policy-section" id="section-12">
                <h2 class="policy-title">第12条（お問い合わせ窓口）</h2>
                <div class="policy-content">
                    <p>本ポリシーに関するお問い合わせは、下記の窓口までお願いいたします。</p>
                    
                    <div class="contact-info-box">
                        <h3 class="contact-title">運営者情報</h3>
                        <dl class="contact-details">
                            <dt>サイト名</dt>
                            <dd>補助金インサイト</dd>
                            
                            <dt>運営者</dt>
                            <dd>中澤圭志</dd>
                            
                            <dt>所在地</dt>
                            <dd>〒136-0073<br>東京都江東区北砂3-23-8　401</dd>
                            
                            <dt>メールアドレス</dt>
                            <dd><a href="mailto:info@joseikin-insight.com" class="text-link">info@joseikin-insight.com</a></dd>
                            
                            <dt>お問い合わせフォーム</dt>
                            <dd><a href="https://joseikin-insight.com/contact/" class="text-link">https://joseikin-insight.com/contact/</a></dd>
                        </dl>
                        
                        <p class="contact-note">
                            ※お問い合わせへの回答には、2〜3営業日程度お時間をいただく場合がございます。<br>
                            ※土日祝日、年末年始は休業日とさせていただきます。
                        </p>
                    </div>
                </div>
            </section>
            
            <!-- 個人情報保護の取り組み -->
            <section class="content-section security-measures-section">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </span>
                    個人情報保護の取り組み
                </h2>
                
                <div class="security-content">
                    <p class="security-intro">
                        当サイトでは、お預かりした個人情報を適切に保護するため、以下のセキュリティ対策を実施しています。
                    </p>
                    
                    <div class="security-grid">
                        <div class="security-item">
                            <div class="security-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <h3 class="security-title">SSL暗号化通信</h3>
                            <p class="security-description">個人情報の送信時には、SSL（Secure Socket Layer）による暗号化通信を使用し、第三者による情報の盗聴、改ざん、なりすましを防止しています。</p>
                        </div>
                        
                        <div class="security-item">
                            <div class="security-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </div>
                            <h3 class="security-title">アクセス制限</h3>
                            <p class="security-description">個人情報へのアクセスは、業務上必要な従業員のみに限定し、適切なアクセス管理を実施しています。</p>
                        </div>
                        
                        <div class="security-item">
                            <div class="security-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <h3 class="security-title">定期的な監査</h3>
                            <p class="security-description">個人情報の取り扱い状況について、定期的に内部監査を実施し、適切な管理体制を維持しています。</p>
                        </div>
                        
                        <div class="security-item">
                            <div class="security-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <h3 class="security-title">従業員教育</h3>
                            <p class="security-description">全従業員に対して、個人情報保護に関する教育・研修を定期的に実施し、意識向上に努めています。</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 附則 -->
            <section class="content-section supplementary-section">
                <h2 class="policy-title">附　則</h2>
                <div class="policy-content">
                    <p>本ポリシーは、2025年10月9日から施行いたします。</p>
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
                    <a href="https://joseikin-insight.com/about/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4M12 8h.01"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">当サイトについて</h3>
                            <p class="link-card-description">サービス概要と運営情報</p>
                        </div>
                    </a>
                    
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
            
            <!-- ページトップへ戻るボタン -->
            <div class="page-top-btn-wrapper">
                <a href="#" class="page-top-btn" aria-label="ページトップへ戻る">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    <span>ページトップへ</span>
                </a>
            </div>
            
        </div>
    </div>
</article>

<style>
:root {
    --color-white: #ffffff;
    --color-black: #000000;
    --color-yellow: #ffeb3b;
    --color-yellow-dark: #ffc107;
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
    --color-primary: var(--color-yellow);
    --text-primary: var(--color-gray-900);
    --text-secondary: var(--color-gray-600);
    --bg-primary: var(--color-white);
    --bg-secondary: var(--color-gray-50);
    --bg-tertiary: var(--color-gray-100);
    --border-light: var(--color-gray-200);
    --border-medium: var(--color-gray-300);
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    --spacing-2xl: 2.5rem;
    --spacing-3xl: 3rem;
    --spacing-4xl: 4rem;
    --radius-sm: 0.25rem;
    --radius-md: 0.375rem;
    --radius-lg: 0.5rem;
    --radius-xl: 0.75rem;
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

.privacy-page {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: var(--text-primary);
    background: var(--bg-primary);
}

.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

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
    margin: 0 0 var(--spacing-lg);
}

.page-meta {
    display: flex;
    justify-content: center;
    gap: var(--spacing-xl);
    flex-wrap: wrap;
}

.meta-item {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin: 0;
}

.page-content {
    padding: var(--spacing-4xl) 0;
}

.content-section {
    margin-bottom: var(--spacing-4xl);
}

.preamble-content p {
    font-size: var(--font-size-base);
    line-height: var(--line-height-relaxed);
    color: var(--text-secondary);
}

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
    width: 32px;
    height: 32px;
}

/* 目次 */
.toc-nav {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: var(--spacing-sm);
}

.toc-link {
    display: block;
    padding: var(--spacing-sm) var(--spacing-md);
    color: var(--text-primary);
    text-decoration: none;
    border-radius: var(--radius-md);
    transition: all 0.2s ease;
    font-size: var(--font-size-sm);
}

.toc-link:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    transform: translateX(4px);
}

/* ポリシーセクション */
.policy-section {
    scroll-margin-top: 80px;
}

.policy-title {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-lg);
    padding: var(--spacing-md);
    background: var(--bg-secondary);
    border-left: 4px solid var(--color-primary);
    border-radius: var(--radius-md);
}

.policy-content {
    padding-left: var(--spacing-lg);
}

.policy-content p {
    color: var(--text-secondary);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--spacing-lg);
}

.article-list {
    list-style: none;
    counter-reset: article-counter;
    padding: 0;
    margin: 0;
}

.article-list > li {
    counter-increment: article-counter;
    position: relative;
    padding-left: var(--spacing-2xl);
    margin-bottom: var(--spacing-lg);
    line-height: var(--line-height-relaxed);
    color: var(--text-secondary);
}

.article-list > li::before {
    content: counter(article-counter) ".";
    position: absolute;
    left: 0;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

.sub-list {
    list-style: none;
    padding: 0;
    margin: var(--spacing-md) 0 0;
}

.sub-list li {
    position: relative;
    padding-left: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    line-height: var(--line-height-normal);
}

.sub-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 6px;
    height: 6px;
    background: var(--text-secondary);
    border-radius: 50%;
}

.sub-sub-list {
    list-style: none;
    padding: 0;
    margin: var(--spacing-sm) 0 0 var(--spacing-lg);
}

.sub-sub-list li {
    position: relative;
    padding-left: var(--spacing-lg);
    margin-bottom: var(--spacing-xs);
    font-size: var(--font-size-sm);
}

.sub-sub-list li::before {
    content: '・';
    position: absolute;
    left: 0;
    color: var(--text-secondary);
}

.subsection-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: var(--spacing-xl) 0 var(--spacing-md);
}

.info-list,
.purpose-list {
    list-style: none;
    padding: 0;
    margin: var(--spacing-md) 0;
}

.info-list li,
.purpose-list li {
    position: relative;
    padding-left: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
}

.info-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 6px;
    height: 6px;
    background: var(--color-primary);
    border-radius: 50%;
}

.purpose-list {
    counter-reset: purpose-counter;
}

.purpose-list li {
    counter-increment: purpose-counter;
}

.purpose-list li::before {
    content: counter(purpose-counter) ".";
    position: absolute;
    left: 0;
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    background: none;
    width: auto;
    height: auto;
    border-radius: 0;
}

/* ハイライトセクション */
.highlight-section {
    background: var(--bg-secondary);
    border: 2px solid var(--border-medium);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
}

/* 連絡先情報ボックス */
.contact-info-box {
    background: var(--bg-tertiary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    margin-top: var(--spacing-lg);
}

.contact-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-lg);
    padding-bottom: var(--spacing-sm);
    border-bottom: 2px solid var(--color-primary);
}

.contact-details {
    display: grid;
    gap: var(--spacing-md);
}

.contact-details dt {
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.contact-details dd {
    color: var(--text-secondary);
    margin: 0 0 var(--spacing-md);
    padding-left: var(--spacing-lg);
}

.contact-note {
    margin-top: var(--spacing-lg);
    padding: var(--spacing-md);
    background: var(--color-white);
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
}

.text-link {
    color: var(--text-primary);
    text-decoration: underline;
    font-weight: var(--font-weight-medium);
}

.text-link:hover {
    color: var(--color-gray-700);
}

/* セキュリティ対策 */
.security-intro {
    color: var(--text-secondary);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--spacing-xl);
}

.security-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}

.security-item {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    text-align: center;
}

.security-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    background: var(--color-white);
    border: 2px solid var(--border-light);
    border-radius: 50%;
    margin-bottom: var(--spacing-md);
    color: var(--text-primary);
}

.security-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-sm);
}

.security-description {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
    margin: 0;
}

/* 附則 */
.supplementary-section .policy-content p {
    color: var(--text-secondary);
    font-size: var(--font-size-base);
    line-height: var(--line-height-normal);
}

/* 関連リンク */
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
}

/* ページトップボタン */
.page-top-btn-wrapper {
    text-align: center;
    margin-top: var(--spacing-4xl);
}

.page-top-btn {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-md) var(--spacing-xl);
    background: var(--color-primary);
    color: var(--color-black);
    text-decoration: none;
    border-radius: var(--radius-xl);
    font-weight: var(--font-weight-semibold);
    transition: all 0.2s ease;
    box-shadow: var(--shadow-md);
}

.page-top-btn:hover {
    background: var(--color-yellow-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* レスポンシブ */
@media (max-width: 768px) {
    .page-header {
        padding: var(--spacing-3xl) 0 var(--spacing-2xl);
    }
    
    .page-title {
        font-size: var(--font-size-3xl);
    }
    
    .page-meta {
        flex-direction: column;
        gap: var(--spacing-sm);
    }
    
    .toc-list {
        grid-template-columns: 1fr;
    }
    
    .policy-content {
        padding-left: 0;
    }
    
    .security-grid {
        grid-template-columns: 1fr;
    }
    
    .related-links-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0 var(--spacing-md);
    }
    
    .page-title {
        font-size: var(--font-size-2xl);
    }
    
    .section-title {
        font-size: var(--font-size-xl);
        flex-direction: column;
        align-items: flex-start;
    }
    
    .policy-title {
        font-size: var(--font-size-lg);
    }
}

/* スムーススクロール */
html {
    scroll-behavior: smooth;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ページトップボタン
    const pageTopBtn = document.querySelector('.page-top-btn');
    if (pageTopBtn) {
        pageTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // 目次リンクのスムーススクロール
    const tocLinks = document.querySelectorAll('.toc-link');
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php get_footer(); // フッターを読み込み ?>