<?php
/**
 * Template Name: サイトマップ
 * Description: 助成金インサイト全ページのサイトマップ
 * 
 * @package Joseikin_Insight
 * @version 1.0.0
 */

get_header();
?>

<style>
    /* Sitemap Page Specific Styles */
    .sitemap-hero {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        color: white;
        padding: 4rem 1rem;
        text-align: center;
    }
    
    .sitemap-hero h1 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    
    .sitemap-hero p {
        font-size: 1.125rem;
        opacity: 0.95;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.7;
    }
    
    .breadcrumb {
        background: #f7fafc;
        padding: 1rem 0;
    }
    
    .breadcrumb-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .sitemap-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 1rem;
    }
    
    .sitemap-intro {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 3rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        text-align: center;
    }
    
    .sitemap-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    @media (min-width: 768px) {
        .sitemap-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .sitemap-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .sitemap-section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #3b82f6;
    }
    
    .sitemap-section-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }
    
    .sitemap-section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    
    .sitemap-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sitemap-links li {
        margin-bottom: 0.75rem;
    }
    
    .sitemap-links a {
        color: #475569;
        text-decoration: none;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        border-radius: 6px;
    }
    
    .sitemap-links a:hover {
        color: #3b82f6;
        background: #eff6ff;
        padding-left: 1rem;
    }
    
    .sitemap-links a::before {
        content: "→";
        color: #3b82f6;
        font-weight: 700;
    }
    
    .sitemap-subsection {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .sitemap-subsection-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 1rem;
    }
    
    .stats-section {
        background: #f8fafc;
        padding: 2rem;
        border-radius: 12px;
        margin-top: 3rem;
        text-align: center;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        max-width: 600px;
        margin: 0 auto;
    }
    
    @media (min-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
            max-width: 100%;
        }
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 900;
        color: #3b82f6;
        display: block;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 600;
    }
</style>

<!-- Hero Section -->
<section class="sitemap-hero">
    <div>
        <h1>サイトマップ</h1>
        <p>助成金インサイトの全ページへのリンク一覧です。お探しの情報を素早く見つけることができます。</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="breadcrumb-container">
        <a href="<?php echo esc_url(home_url('/')); ?>" style="color: #3b82f6; text-decoration: none;">ホーム</a>
        <span>/</span>
        <span>サイトマップ</span>
    </div>
</div>

<!-- Main Content -->
<div class="sitemap-container">
    
    <!-- Introduction -->
    <div class="sitemap-intro">
        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem; color: #1e293b;">助成金インサイト サイトマップ</h2>
        <p style="color: #64748b; line-height: 1.7;">当サイトの全ページを分類別に掲載しています。目的のページをお探しの際にご活用ください。</p>
    </div>
    
    <!-- Sitemap Grid -->
    <div class="sitemap-grid">
        
        <!-- メインページ -->
        <div class="sitemap-section">
            <div class="sitemap-section-header">
                <div class="sitemap-section-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h2 class="sitemap-section-title">メインページ</h2>
            </div>
            <ul class="sitemap-links">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">トップページ</a></li>
                <li><a href="<?php echo esc_url(home_url('/about/')); ?>">運営者情報</a></li>
                <li><a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a></li>
                <li><a href="<?php echo esc_url(home_url('/sitemap/')); ?>">サイトマップ</a></li>
            </ul>
        </div>
        
        <!-- 助成金検索 -->
        <div class="sitemap-section">
            <div class="sitemap-section-header">
                <div class="sitemap-section-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h2 class="sitemap-section-title">助成金検索</h2>
            </div>
            <ul class="sitemap-links">
                <li><a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>">助成金一覧</a></li>
                <li><a href="<?php echo esc_url(home_url('/grants/?status=active')); ?>">募集中の助成金</a></li>
                <li><a href="<?php echo esc_url(home_url('/grants/?orderby=new')); ?>">新着助成金</a></li>
                <li><a href="<?php echo esc_url(home_url('/grants/?orderby=popular')); ?>">人気の助成金</a></li>
            </ul>
        </div>
        
        <!-- ツール・診断 -->
        <div class="sitemap-section">
            <div class="sitemap-section-header">
                <div class="sitemap-section-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h2 class="sitemap-section-title">ツール・診断</h2>
            </div>
            <ul class="sitemap-links">
                <li><a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>">助成金診断システム</a></li>
                <li><a href="<?php echo esc_url(home_url('/calculator/')); ?>">助成金計算ツール</a></li>
                <li><a href="<?php echo esc_url(home_url('/calculator/employment/')); ?>">雇用関係助成金計算</a></li>
                <li><a href="<?php echo esc_url(home_url('/calculator/equipment/')); ?>">設備投資補助金計算</a></li>
                <li><a href="<?php echo esc_url(home_url('/calculator/research/')); ?>">研究開発補助金計算</a></li>
                <li><a href="<?php echo esc_url(home_url('/calculator/startup/')); ?>">創業・起業支援計算</a></li>
            </ul>
        </div>
        
        <!-- ガイド・サポート -->
        <div class="sitemap-section">
            <div class="sitemap-section-header">
                <div class="sitemap-section-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h2 class="sitemap-section-title">ガイド・サポート</h2>
            </div>
            <ul class="sitemap-links">
                <li><a href="<?php echo esc_url(home_url('/how-to-use/')); ?>">使い方ガイド</a></li>
                <li><a href="<?php echo esc_url(home_url('/faq/')); ?>">よくある質問</a></li>
                <li><a href="<?php echo esc_url(home_url('/support/')); ?>">ヘルプセンター</a></li>
                <li><a href="<?php echo esc_url(home_url('/knowledge/')); ?>">助成金の基礎知識</a></li>
                <li><a href="<?php echo esc_url(home_url('/glossary/')); ?>">用語集</a></li>
            </ul>
        </div>
        
        <!-- コラム・情報 -->
        <div class="sitemap-section">
            <div class="sitemap-section-header">
                <div class="sitemap-section-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h2 class="sitemap-section-title">コラム・情報</h2>
            </div>
            <ul class="sitemap-links">
                <li><a href="<?php echo esc_url(home_url('/column/')); ?>">コラム一覧</a></li>
                <?php
                // 最新のコラム記事を5件表示
                $recent_columns = new WP_Query([
                    'post_type' => 'column',
                    'posts_per_page' => 5,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ]);
                
                if ($recent_columns->have_posts()) {
                    while ($recent_columns->have_posts()) {
                        $recent_columns->the_post();
                        echo '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
                    }
                    wp_reset_postdata();
                }
                ?>
            </ul>
        </div>
        
        <!-- カテゴリー別 -->
        <div class="sitemap-section">
            <div class="sitemap-section-header">
                <div class="sitemap-section-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h2 class="sitemap-section-title">カテゴリー別助成金</h2>
            </div>
            <ul class="sitemap-links">
                <?php
                $categories = get_terms([
                    'taxonomy' => 'grant_category',
                    'hide_empty' => true,
                    'number' => 10
                ]);
                
                if (!is_wp_error($categories) && !empty($categories)) {
                    foreach ($categories as $category) {
                        echo '<li><a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . ' (' . $category->count . '件)</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
        
        <!-- 地域別 -->
        <div class="sitemap-section">
            <div class="sitemap-section-header">
                <div class="sitemap-section-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h2 class="sitemap-section-title">都道府県別助成金</h2>
            </div>
            <ul class="sitemap-links">
                <?php
                $prefectures = get_terms([
                    'taxonomy' => 'grant_prefecture',
                    'hide_empty' => true,
                    'number' => 10
                ]);
                
                if (!is_wp_error($prefectures) && !empty($prefectures)) {
                    foreach ($prefectures as $prefecture) {
                        echo '<li><a href="' . esc_url(get_term_link($prefecture)) . '">' . esc_html($prefecture->name) . ' (' . $prefecture->count . '件)</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
        
        <!-- 法的情報 -->
        <div class="sitemap-section">
            <div class="sitemap-section-header">
                <div class="sitemap-section-icon">
                    <i class="fas fa-gavel"></i>
                </div>
                <h2 class="sitemap-section-title">法的情報</h2>
            </div>
            <ul class="sitemap-links">
                <li><a href="<?php echo esc_url(home_url('/privacy/')); ?>">プライバシーポリシー</a></li>
                <li><a href="<?php echo esc_url(home_url('/terms/')); ?>">利用規約</a></li>
                <li><a href="<?php echo esc_url(home_url('/disclaimer/')); ?>">免責事項</a></li>
            </ul>
        </div>
        
    </div>
    
    <!-- Stats Section -->
    <div class="stats-section">
        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem; color: #1e293b;">サイト統計情報</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number"><?php echo wp_count_posts('grant')->publish ?? 0; ?></span>
                <div class="stat-label">掲載助成金数</div>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?php echo wp_count_posts('column')->publish ?? 0; ?></span>
                <div class="stat-label">コラム記事数</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">47</span>
                <div class="stat-label">対応都道府県</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">24/7</span>
                <div class="stat-label">情報更新</div>
            </div>
        </div>
    </div>
    
</div>

<!-- CTA Section -->
<section class="sitemap-hero" style="padding: 3rem 1rem;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 2rem; margin-bottom: 1rem;">お探しの情報が見つかりませんか？</h2>
        <p style="margin-bottom: 2rem; opacity: 0.95;">お気軽にお問い合わせください。専門スタッフが丁寧にサポートいたします。</p>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="calculator-btn" style="display: inline-block; background: white; color: #3b82f6; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 1.125rem;">
            <i class="fas fa-envelope" style="margin-right: 0.5rem;"></i>
            お問い合わせ
        </a>
    </div>
</section>

<?php get_footer(); ?>
