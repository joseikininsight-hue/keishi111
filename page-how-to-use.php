<?php
/**
 * Template Name: 使い方ガイド (How To Use Page)
 * 
 * SEO 100%対応の使い方専用固定ページ
 * カーセンサー型ポータルサイト設計
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0-seo-optimized
 */

get_header();

// 動画設定
$video_config = array(
    'video_url' => 'https://youtu.be/mh1MDXl1t50',
    'video_id' => 'mh1MDXl1t50',
    'video_title' => '補助金検索システムの使い方 | 3分で分かるAI補助金検索',
    'video_description' => 'AIを活用した補助金・助成金検索システムの使い方を3分で解説。キーワード検索からAI相談まで、初心者でも簡単に最適な補助金を見つけられます。',
    'thumbnail' => 'https://img.youtube.com/vi/mh1MDXl1t50/maxresdefault.jpg',
    'duration' => 'PT3M',
    'upload_date' => '2024-01-15'
);

// ステップガイド設定
$steps = array(
    array(
        'number' => '01',
        'icon' => 'fas fa-search',
        'title' => 'キーワード検索',
        'description' => '業種や目的を入力すると、AIが全国の補助金データベースから最適な制度を瞬時に検索します。',
        'keywords' => '補助金検索, AI検索, キーワード検索'
    ),
    array(
        'number' => '02',
        'icon' => 'fas fa-filter',
        'title' => 'カテゴリー絞り込み',
        'description' => 'IT導入、ものづくり、創業支援など、業種別・目的別のカテゴリーから効率的に絞り込めます。',
        'keywords' => 'カテゴリー検索, 業種別補助金, 絞り込み検索'
    ),
    array(
        'number' => '03',
        'icon' => 'fas fa-robot',
        'title' => 'AI相談で詳細確認',
        'description' => '各補助金の申請要件や必要書類をAIアシスタントに質問して、疑問を即座に解消できます。',
        'keywords' => 'AI相談, 補助金質問, 申請サポート'
    )
);

// 構造化データ（VideoObject）
$video_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'VideoObject',
    'name' => $video_config['video_title'],
    'description' => $video_config['video_description'],
    'thumbnailUrl' => $video_config['thumbnail'],
    'uploadDate' => $video_config['upload_date'],
    'duration' => $video_config['duration'],
    'contentUrl' => $video_config['video_url'],
    'embedUrl' => 'https://www.youtube.com/embed/' . $video_config['video_id'],
    'publisher' => array(
        '@type' => 'Organization',
        'name' => '補助金インサイト',
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => get_site_icon_url()
        )
    )
);

// HowTo構造化データ
$howto_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    'name' => '補助金・助成金の検索方法',
    'description' => 'AIを活用した補助金検索システムの使い方を3ステップで解説',
    'image' => $video_config['thumbnail'],
    'totalTime' => $video_config['duration'],
    'tool' => array(
        '@type' => 'HowToTool',
        'name' => '補助金インサイト AI検索システム'
    ),
    'step' => array()
);

foreach ($steps as $index => $step) {
    $howto_schema['step'][] = array(
        '@type' => 'HowToStep',
        'position' => $index + 1,
        'name' => $step['title'],
        'text' => $step['description'],
        'url' => home_url('/how-to-use/#step-' . ($index + 1))
    );
}

// FAQセクション
$faqs = array(
    array(
        'question' => '補助金検索は無料ですか？',
        'answer' => 'はい、完全無料でご利用いただけます。会員登録も不要で、すぐに検索を開始できます。'
    ),
    array(
        'question' => 'どのような補助金が検索できますか？',
        'answer' => 'IT導入補助金、ものづくり補助金、小規模事業者持続化補助金など、全国の主要な補助金・助成金を網羅しています。'
    ),
    array(
        'question' => 'AI相談はどのように使いますか？',
        'answer' => '各補助金の詳細ページにあるAIチャット機能から、申請要件や必要書類などを質問できます。'
    ),
    array(
        'question' => '地域別の補助金も検索できますか？',
        'answer' => 'はい、都道府県・市町村別に絞り込んで検索できます。地域特有の補助金も見つけやすくなっています。'
    )
);

// FAQ構造化データ
$faq_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array()
);

foreach ($faqs as $faq) {
    $faq_schema['mainEntity'][] = array(
        '@type' => 'Question',
        'name' => $faq['question'],
        'acceptedAnswer' => array(
            '@type' => 'Answer',
            'text' => $faq['answer']
        )
    );
}
?>

<!-- SEO メタタグ -->
<meta name="description" content="<?php echo esc_attr($video_config['video_description']); ?> | 完全無料で全国の補助金・助成金を検索できるAIシステムの使い方を詳しく解説します。">
<meta property="og:title" content="<?php echo esc_attr($video_config['video_title']); ?> | 補助金インサイト">
<meta property="og:description" content="<?php echo esc_attr($video_config['video_description']); ?>">
<meta property="og:image" content="<?php echo esc_url($video_config['thumbnail']); ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="<?php echo esc_url(get_permalink()); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr($video_config['video_title']); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($video_config['video_description']); ?>">
<meta name="twitter:image" content="<?php echo esc_url($video_config['thumbnail']); ?>">

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($video_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<script type="application/ld+json">
<?php echo wp_json_encode($howto_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<script type="application/ld+json">
<?php echo wp_json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<!-- Breadcrumb構造化データ -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
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
            "name": "使い方ガイド",
            "item": "<?php echo esc_url(get_permalink()); ?>"
        }
    ]
}
</script>

<!-- フォント・アイコン読み込み -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="how-to-page">
    
    <!-- パンくずリスト -->
    <nav class="breadcrumb-nav" aria-label="パンくずリスト">
        <div class="container">
            <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo esc_url(home_url('/')); ?>">
                        <span itemprop="name">ホーム</span>
                    </a>
                    <meta itemprop="position" content="1" />
                </li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="active">
                    <span itemprop="name">使い方ガイド</span>
                    <meta itemprop="position" content="2" />
                </li>
            </ol>
        </div>
    </nav>

    <!-- ヒーローセクション -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-badge">
                <div class="badge-dot"></div>
                <span>HOW TO USE</span>
            </div>
            
            <h1 class="hero-title">
                <span class="title-main">使い方ガイド</span>
                <span class="title-sub">3ステップで簡単検索</span>
            </h1>
            
            <p class="hero-description">
                <?php echo esc_html($video_config['video_description']); ?>
            </p>
        </div>
    </section>

    <!-- メインコンテンツ -->
    <section class="content-section">
        <div class="container">
            <div class="content-wrapper">
                
                <!-- 左側：動画カード -->
                <article class="video-card" itemscope itemtype="https://schema.org/VideoObject">
                    <div class="video-container lite-youtube" data-video-id="<?php echo esc_attr($video_config['video_id']); ?>" data-video-title="<?php echo esc_attr($video_config['video_title']); ?>">
                        <img src="https://i.ytimg.com/vi/<?php echo esc_attr($video_config['video_id']); ?>/hqdefault.jpg" 
                             alt="<?php echo esc_attr($video_config['video_title']); ?>" 
                             class="youtube-thumbnail"
                             loading="lazy"
                             width="480"
                             height="360">
                        
                        <button class="youtube-play-button" aria-label="動画を再生" type="button">
                            <svg width="68" height="48" viewBox="0 0 68 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M66.52 7.74c-.78-2.93-2.49-5.41-5.42-6.19C55.79.13 34 0 34 0S12.21.13 6.9 1.55c-2.93.78-4.63 3.26-5.42 6.19C.06 13.05 0 24 0 24s.06 10.95 1.48 16.26c.78 2.93 2.49 5.41 5.42 6.19C12.21 47.87 34 48 34 48s21.79-.13 27.1-1.55c2.93-.78 4.64-3.26 5.42-6.19C67.94 34.95 68 24 68 24s-.06-10.95-1.48-16.26z" fill="red"/>
                                <path d="M45 24L27 14v20" fill="white"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="video-info">
                        <div class="video-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="video-text">
                            <h2 itemprop="name"><?php echo esc_html($video_config['video_title']); ?></h2>
                            <p itemprop="description"><?php echo esc_html($video_config['video_description']); ?></p>
                        </div>
                    </div>
                    
                    <meta itemprop="thumbnailUrl" content="<?php echo esc_url($video_config['thumbnail']); ?>">
                    <meta itemprop="uploadDate" content="<?php echo esc_attr($video_config['upload_date']); ?>">
                    <meta itemprop="duration" content="<?php echo esc_attr($video_config['duration']); ?>">
                </article>

                <!-- 右側：ステップカード -->
                <div class="steps-wrapper">
                    <div class="steps-header">
                        <i class="fas fa-list-ol"></i>
                        <h2>3ステップで簡単検索</h2>
                    </div>
                    
                    <div class="steps-grid">
                        <?php foreach ($steps as $index => $step) : ?>
                        <article class="step-card" id="step-<?php echo $index + 1; ?>" itemprop="step" itemscope itemtype="https://schema.org/HowToStep">
                            <div class="step-number"><?php echo esc_html($step['number']); ?></div>
                            <div class="step-icon">
                                <i class="<?php echo esc_attr($step['icon']); ?>"></i>
                            </div>
                            <h3 class="step-title" itemprop="name"><?php echo esc_html($step['title']); ?></h3>
                            <p class="step-description" itemprop="text"><?php echo esc_html($step['description']); ?></p>
                            <meta itemprop="keywords" content="<?php echo esc_attr($step['keywords']); ?>">
                            <meta itemprop="position" content="<?php echo $index + 1; ?>">
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="cta-wrapper">
                        <a href="<?php echo esc_url(home_url('/grants/')); ?>" class="cta-button">
                            <span>今すぐ検索を始める</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQセクション -->
    <section class="faq-section" itemscope itemtype="https://schema.org/FAQPage">
        <div class="container">
            <div class="faq-header">
                <h2>よくある質問</h2>
                <p>補助金検索システムについて、よくお寄せいただく質問にお答えします</p>
            </div>
            
            <div class="faq-list">
                <?php foreach ($faqs as $index => $faq) : ?>
                <article class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                    <h3 class="faq-question" itemprop="name">
                        <i class="fas fa-question-circle"></i>
                        <?php echo esc_html($faq['question']); ?>
                    </h3>
                    <div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                        <p itemprop="text"><?php echo esc_html($faq['answer']); ?></p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTAセクション -->
    <section class="final-cta-section">
        <div class="container">
            <div class="final-cta-content">
                <h2>今すぐ補助金検索を始めましょう</h2>
                <p>AIが最適な補助金を見つけるお手伝いをします</p>
                <a href="<?php echo esc_url(home_url('/grants/')); ?>" class="final-cta-button">
                    <span>補助金を検索する</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

</main>

<style>
/* ============================================
   使い方ページ - 白黒スタイリッシュデザイン
   ============================================ */

/* ベース設定 */
.how-to-page {
    font-family: 'Inter', 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    color: #0a0a0a;
    background: #ffffff;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* パンくずリスト */
.breadcrumb-nav {
    padding: 100px 0 20px;
    background: #ffffff;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 10px;
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: 13px;
    color: #666666;
}

.breadcrumb li {
    display: flex;
    align-items: center;
}

.breadcrumb li:not(:last-child)::after {
    content: '›';
    margin-left: 10px;
    color: #cccccc;
}

.breadcrumb a {
    color: #666666;
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb a:hover {
    color: #000000;
}

.breadcrumb li.active span {
    color: #000000;
    font-weight: 600;
}

/* ヒーローセクション */
.hero-section {
    padding: 60px 0;
    background: #ffffff;
    text-align: center;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #000000;
    color: #ffffff;
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.badge-dot {
    width: 7px;
    height: 7px;
    background: #ffffff;
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.hero-title {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}

.title-main {
    font-size: 48px;
    font-weight: 900;
    color: #000000;
    line-height: 1.1;
    letter-spacing: -0.02em;
}

.title-sub {
    font-size: 20px;
    font-weight: 500;
    color: #666666;
    line-height: 1.4;
}

.hero-description {
    font-size: 16px;
    line-height: 1.7;
    color: #666666;
    max-width: 700px;
    margin: 0 auto;
}

/* コンテンツセクション */
.content-section {
    padding: 60px 0;
    background: #f8f8f8;
}

.content-wrapper {
    display: grid;
    grid-template-columns: 1fr;
    gap: 40px;
    align-items: start;
}

@media (min-width: 1024px) {
    .content-wrapper {
        grid-template-columns: 1fr 1fr;
    }
}

/* 動画カード */
.video-card {
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    position: sticky;
    top: 120px;
}

.video-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.14);
}

.video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #000000;
    cursor: pointer;
}

.youtube-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease;
}

.lite-youtube:hover .youtube-thumbnail {
    opacity: 0.85;
}

.youtube-play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    opacity: 0.9;
    transition: all 0.3s ease;
    z-index: 10;
}

.youtube-play-button:hover {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.1);
}

.video-info {
    padding: 20px;
    display: flex;
    gap: 16px;
    align-items: start;
    background: #fafafa;
}

.video-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    background: #000000;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 24px;
}

.video-text h2 {
    font-size: 18px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 8px 0;
    line-height: 1.3;
}

.video-text p {
    font-size: 14px;
    line-height: 1.6;
    color: #666666;
    margin: 0;
}

/* ステップラッパー */
.steps-wrapper {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.steps-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 24px;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 12px;
}

.steps-header i {
    font-size: 24px;
    color: #000000;
}

.steps-header h2 {
    font-size: 20px;
    font-weight: 700;
    color: #000000;
    margin: 0;
}

.steps-grid {
    display: grid;
    gap: 18px;
}

.step-card {
    position: relative;
    padding: 24px;
    background: #ffffff;
    border: 2px solid #000000;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.step-card:hover {
    transform: translateX(6px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.step-number {
    position: absolute;
    top: -12px;
    right: -12px;
    width: 40px;
    height: 40px;
    background: #000000;
    color: #ffffff;
    border: 2px solid #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 900;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.step-icon {
    width: 56px;
    height: 56px;
    background: #000000;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 28px;
    margin-bottom: 16px;
}

.step-title {
    font-size: 18px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 8px 0;
    line-height: 1.3;
}

.step-description {
    font-size: 14px;
    line-height: 1.7;
    color: #666666;
    margin: 0;
}

.cta-wrapper {
    margin-top: 10px;
}

.cta-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    padding: 18px 32px;
    background: #000000;
    color: #ffffff;
    border: 2px solid #000000;
    border-radius: 12px;
    font-size: 17px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
}

.cta-button:hover {
    background: #ffffff;
    color: #000000;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.16);
}

.cta-button i {
    transition: transform 0.3s ease;
}

.cta-button:hover i {
    transform: translateX(4px);
}

/* FAQセクション */
.faq-section {
    padding: 80px 0;
    background: #ffffff;
}

.faq-header {
    text-align: center;
    margin-bottom: 50px;
}

.faq-header h2 {
    font-size: 36px;
    font-weight: 900;
    color: #000000;
    margin: 0 0 12px 0;
}

.faq-header p {
    font-size: 16px;
    color: #666666;
    margin: 0;
}

.faq-list {
    display: grid;
    gap: 20px;
    max-width: 900px;
    margin: 0 auto;
}

.faq-item {
    padding: 24px;
    background: #f8f8f8;
    border: 2px solid #000000;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.faq-item:hover {
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.faq-question {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 18px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 12px 0;
}

.faq-question i {
    font-size: 20px;
}

.faq-answer p {
    font-size: 15px;
    line-height: 1.7;
    color: #666666;
    margin: 0;
}

/* 最終CTAセクション */
.final-cta-section {
    padding: 80px 0;
    background: #000000;
    text-align: center;
}

.final-cta-content h2 {
    font-size: 36px;
    font-weight: 900;
    color: #ffffff;
    margin: 0 0 12px 0;
}

.final-cta-content p {
    font-size: 16px;
    color: #cccccc;
    margin: 0 0 30px 0;
}

.final-cta-button {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 18px 36px;
    background: #ffffff;
    color: #000000;
    border: 2px solid #ffffff;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
}

.final-cta-button:hover {
    background: transparent;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 255, 255, 0.2);
}

.final-cta-button i {
    transition: transform 0.3s ease;
}

.final-cta-button:hover i {
    transform: translateX(6px);
}

/* レスポンシブ */
@media (max-width: 767px) {
    .breadcrumb-nav {
        padding: 80px 0 15px;
    }
    
    .hero-section {
        padding: 40px 0;
    }
    
    .title-main {
        font-size: 32px;
    }
    
    .title-sub {
        font-size: 16px;
    }
    
    .content-section {
        padding: 40px 0;
    }
    
    .content-wrapper {
        gap: 30px;
    }
    
    .video-card {
        position: static;
    }
    
    .faq-section {
        padding: 50px 0;
    }
    
    .faq-header h2 {
        font-size: 28px;
    }
    
    .final-cta-section {
        padding: 50px 0;
    }
    
    .final-cta-content h2 {
        font-size: 28px;
    }
}
</style>

<script>
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        initLiteYouTube();
    });
    
    function initLiteYouTube() {
        const liteYouTubes = document.querySelectorAll('.lite-youtube');
        
        liteYouTubes.forEach(element => {
            const videoId = element.dataset.videoId;
            const videoTitle = element.dataset.videoTitle;
            
            element.addEventListener('click', function() {
                const iframe = document.createElement('iframe');
                iframe.className = 'youtube-iframe';
                iframe.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;border:none;';
                iframe.src = `https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1&playsinline=1&autoplay=1`;
                iframe.title = videoTitle;
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                
                element.innerHTML = '';
                element.appendChild(iframe);
            }, { once: true });
        });
    }
})();
</script>

<?php
get_footer();
