<?php
/**
 * JOSEIKIN INSIGHT - Minna Style Footer
 * 助成金インサイト専用 完全黒背景フッター
 * 
 * @package Joseikin_Insight_Footer
 * @version 5.0.1
 */

// SNS URLヘルパー関数 - エラー修正版
if (!function_exists('gi_get_sns_urls')) {
    function gi_get_sns_urls() {
        return [
            'twitter' => get_option('gi_sns_twitter_url', 'https://twitter.com/joseikininsight'),
            'facebook' => get_option('gi_sns_facebook_url', 'https://facebook.com/joseikin.insight'),
            'linkedin' => get_option('gi_sns_linkedin_url', ''),
            'instagram' => get_option('gi_sns_instagram_url', 'https://instagram.com/joseikin_insight'),
            'youtube' => get_option('gi_sns_youtube_url', 'https://www.youtube.com/channel/UCbfjOrG3nSPI3GFzKnGcspQ'),
            'note' => get_option('gi_sns_note_url', 'https://note.com/joseikin_insight')
        ];
    }
}

// 統計情報取得関数 - エラー修正版
if (!function_exists('gi_get_cached_stats')) {
    function gi_get_cached_stats() {
        $stats = wp_cache_get('gi_stats', 'grant_insight');
        
        if (false === $stats) {
            $stats = [
                'total_grants' => wp_count_posts('grant')->publish ?? 0,
                'active_grants' => 0,
                'total_views' => 0
            ];
            
            // 募集中の助成金数を取得
            $active_query = new WP_Query([
                'post_type' => 'grant',
                'post_status' => 'publish',
                'meta_query' => [
                    [
                        'key' => 'grant_status',
                        'value' => 'active',
                        'compare' => '='
                    ]
                ],
                'posts_per_page' => -1,
                'fields' => 'ids'
            ]);
            $stats['active_grants'] = $active_query->found_posts;
            wp_reset_postdata();
            
            // 総閲覧数を取得（存在する場合）
            $total_views = get_option('gi_total_views', 0);
            if ($total_views > 0) {
                $stats['total_views'] = $total_views;
            }
            
            wp_cache_set('gi_stats', $stats, 'grant_insight', 3600);
        }
        
        return $stats;
    }
}
?>

    </main>

    <style>
        /* ===============================================
           JOSEIKIN INSIGHT - MINNA STYLE FOOTER
           完全黒背景フッター - 助成金サイト専用
           =============================================== */
        
        :root {
            /* Footer Colors */
            --footer-bg: #000000;
            --footer-text: #ffffff;
            --footer-text-dim: #999999;
            --footer-text-muted: #666666;
            --footer-border: rgba(255, 255, 255, 0.08);
            --footer-hover: rgba(255, 255, 255, 0.05);
            --footer-accent: #ffffff;
            
            /* Typography */
            --footer-font: 'Noto Sans JP', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Spacing */
            --footer-space-xs: 0.25rem;
            --footer-space-sm: 0.5rem;
            --footer-space-md: 1rem;
            --footer-space-lg: 1.5rem;
            --footer-space-xl: 2rem;
            --footer-space-2xl: 3rem;
            --footer-space-3xl: 4rem;
            --footer-space-4xl: 5rem;
            
            /* Transitions */
            --footer-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* ===============================================
           MAIN FOOTER
           =============================================== */
        .ji-site-footer {
            background: var(--footer-bg);
            color: var(--footer-text);
            font-family: var(--footer-font);
            position: relative;
            overflow: hidden;
        }
        
        .ji-footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--footer-space-xl);
        }
        
        @media (min-width: 768px) {
            .ji-footer-container {
                padding: 0 var(--footer-space-2xl);
            }
        }
        
        /* ===============================================
           FOOTER TOP SECTION
           =============================================== */
        .ji-footer-top {
            padding: var(--footer-space-4xl) 0 var(--footer-space-3xl);
            border-bottom: 1px solid var(--footer-border);
        }
        
        .ji-footer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: var(--footer-space-3xl);
        }
        
        @media (min-width: 768px) {
            .ji-footer-grid {
                grid-template-columns: 2fr 1fr 1fr 1fr;
                gap: var(--footer-space-2xl);
            }
        }
        
        /* Brand Section */
        .ji-footer-brand {
            max-width: 400px;
        }
        
        .ji-footer-logo-wrapper {
            display: flex;
            align-items: center;
            gap: var(--footer-space-md);
            margin-bottom: var(--footer-space-xl);
        }
        
        .ji-footer-logo-icon {
            width: 48px;
            height: 48px;
            background: var(--footer-text);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--footer-bg);
            letter-spacing: -0.02em;
            flex-shrink: 0;
        }
        
        .ji-footer-logo-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .ji-footer-logo-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--footer-text);
            letter-spacing: 0.01em;
            line-height: 1.2;
        }
        
        .ji-footer-logo-subtitle {
            font-size: 0.75rem;
            color: var(--footer-text-dim);
            font-weight: 500;
            letter-spacing: 0.02em;
        }
        
        .ji-footer-description {
            font-size: 0.9375rem;
            color: var(--footer-text-dim);
            line-height: 1.7;
            margin-bottom: var(--footer-space-xl);
        }
        
        /* Social Links */
        .ji-footer-social {
            display: flex;
            gap: var(--footer-space-md);
            flex-wrap: wrap;
        }
        
        .ji-footer-social-link {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--footer-text);
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            text-decoration: none;
            font-size: 1.125rem;
            transition: all var(--footer-transition);
        }
        
        .ji-footer-social-link:hover {
            background: var(--footer-text);
            color: var(--footer-bg);
            transform: translateY(-2px);
        }
        
        /* Footer Columns */
        .ji-footer-column {
            display: flex;
            flex-direction: column;
        }
        
        .ji-footer-column-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--footer-text);
            margin-bottom: var(--footer-space-lg);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        
        .ji-footer-nav {
            display: flex;
            flex-direction: column;
            gap: var(--footer-space-md);
        }
        
        .ji-footer-link {
            color: var(--footer-text-dim);
            text-decoration: none;
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all var(--footer-transition);
            display: flex;
            align-items: center;
            gap: var(--footer-space-sm);
            padding: var(--footer-space-xs) 0;
        }
        
        .ji-footer-link:hover {
            color: var(--footer-text);
            padding-left: var(--footer-space-sm);
        }
        
        .ji-footer-link i {
            font-size: 0.75rem;
            opacity: 0.7;
        }
        
        /* ===============================================
           STATS SECTION
           =============================================== */
        .ji-footer-stats {
            padding: var(--footer-space-3xl) 0;
            border-bottom: 1px solid var(--footer-border);
        }
        
        .ji-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--footer-space-xl);
        }
        
        @media (min-width: 768px) {
            .ji-stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        .ji-stat-item {
            text-align: center;
            padding: var(--footer-space-lg);
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            transition: all var(--footer-transition);
        }
        
        .ji-stat-item:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
        }
        
        .ji-stat-number {
            font-size: 2rem;
            font-weight: 900;
            color: var(--footer-text);
            display: block;
            margin-bottom: var(--footer-space-xs);
            letter-spacing: -0.02em;
            line-height: 1;
        }
        
        @media (min-width: 768px) {
            .ji-stat-number {
                font-size: 2.5rem;
            }
        }
        
        .ji-stat-label {
            font-size: 0.8125rem;
            color: var(--footer-text-dim);
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        
        .ji-stat-sublabel {
            font-size: 0.6875rem;
            color: #808080; /* Improved contrast (changed from #666666) */
            font-weight: 500;
            margin-top: var(--footer-space-xs);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        
        /* ===============================================
           NEWSLETTER SECTION
           =============================================== */
        .ji-footer-newsletter {
            padding: var(--footer-space-3xl) 0;
            border-bottom: 1px solid var(--footer-border);
        }
        
        .ji-newsletter-content {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        
        .ji-newsletter-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.08);
            color: var(--footer-text);
            padding: var(--footer-space-sm) var(--footer-space-lg);
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: var(--footer-space-lg);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        
        .ji-newsletter-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--footer-text);
            margin-bottom: var(--footer-space-md);
            letter-spacing: -0.02em;
            line-height: 1.3;
        }
        
        @media (min-width: 768px) {
            .ji-newsletter-title {
                font-size: 2.25rem;
            }
        }
        
        .ji-newsletter-description {
            font-size: 0.9375rem;
            color: var(--footer-text-dim);
            margin-bottom: var(--footer-space-xl);
            line-height: 1.7;
        }
        
        .ji-newsletter-form {
            display: flex;
            flex-direction: column;
            gap: var(--footer-space-md);
            max-width: 500px;
            margin: 0 auto;
        }
        
        @media (min-width: 640px) {
            .ji-newsletter-form {
                flex-direction: row;
            }
        }
        
        .ji-newsletter-input {
            flex: 1;
            padding: var(--footer-space-lg) var(--footer-space-xl);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: var(--footer-text);
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all var(--footer-transition);
            font-family: var(--footer-font);
        }
        
        .ji-newsletter-input::placeholder {
            color: var(--footer-text-muted);
        }
        
        .ji-newsletter-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .ji-newsletter-submit {
            padding: var(--footer-space-lg) var(--footer-space-2xl);
            background: var(--footer-text);
            color: var(--footer-bg);
            border: none;
            border-radius: 12px;
            font-size: 0.9375rem;
            font-weight: 700;
            cursor: pointer;
            transition: all var(--footer-transition);
            white-space: nowrap;
            font-family: var(--footer-font);
            letter-spacing: 0.01em;
        }
        
        .ji-newsletter-submit:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 255, 255, 0.2);
        }
        
        /* ===============================================
           FOOTER BOTTOM
           =============================================== */
        .ji-footer-bottom {
            padding: var(--footer-space-2xl) 0;
        }
        
        .ji-footer-bottom-content {
            display: flex;
            flex-direction: column;
            gap: var(--footer-space-lg);
            align-items: center;
            text-align: center;
        }
        
        @media (min-width: 768px) {
            .ji-footer-bottom-content {
                flex-direction: row;
                justify-content: space-between;
                text-align: left;
            }
        }
        
        .ji-footer-copyright {
            font-size: 0.875rem;
            color: #808080; /* Improved contrast (changed from #666666) */
            font-weight: 500;
        }
        
        .ji-footer-copyright strong {
            color: var(--footer-text-dim);
            font-weight: 700;
        }
        
        .ji-footer-legal-links {
            display: flex;
            gap: var(--footer-space-xl);
            flex-wrap: wrap;
            justify-content: center;
        }
        
        @media (min-width: 768px) {
            .ji-footer-legal-links {
                justify-content: flex-end;
            }
        }
        
        .ji-footer-legal-link {
            color: var(--footer-text-dim);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all var(--footer-transition);
        }
        
        .ji-footer-legal-link:hover {
            color: var(--footer-text);
        }
        
        /* ===============================================
           BACK TO TOP BUTTON
           =============================================== */
        .ji-back-to-top {
            position: fixed !important;
            bottom: 135px !important; /* 絞り込みボタン(65px) + ボタン高さ(50px) + 余白(20px) */
            left: 16px !important;
            right: auto !important;
            top: auto !important;
            width: 48px !important;
            height: 48px !important;
            background: var(--footer-text) !important;
            color: var(--footer-bg) !important;
            border: none !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.25rem !important;
            cursor: pointer !important;
            opacity: 0;
            visibility: hidden;
            transition: all var(--footer-transition) !important;
            z-index: 1001 !important; /* 絞り込みボタン(1000)より上 */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
            margin: 0 !important;
            padding: 0 !important;
            transform: none !important;
        }
        
        .ji-back-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .ji-back-to-top:hover {
            background: rgba(255, 255, 255, 0.9) !important;
            transform: translateY(-4px) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25) !important;
        }
        
        /* デスクトップでは右下に配置 */
        @media (min-width: 1024px) {
            .ji-back-to-top {
                bottom: 2rem !important;
                right: 2rem !important;
                left: auto !important;
                width: 56px !important;
                height: 56px !important;
            }
        }
        
        /* Hide other back-to-top buttons that might conflict */
        button:not(#ji-back-to-top)[class*="back-to-top"],
        button:not(#ji-back-to-top)[id*="back-to-top"],
        a:not(#ji-back-to-top)[class*="back-to-top"],
        a:not(#ji-back-to-top)[id*="back-to-top"],
        .back-to-top:not(#ji-back-to-top),
        #back-to-top:not(.ji-back-to-top),
        [class*="scroll-to-top"]:not(#ji-back-to-top),
        [class*="scrolltop"]:not(#ji-back-to-top),
        [id*="scroll-top"]:not(#ji-back-to-top) {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        /* Hide MonsterInsights branding - COMPREHENSIVE */
        /* Target all possible MonsterInsights elements */
        .monsterinsights-frontend-tracking,
        .monsterinsights-badge,
        .monsterinsights-banner,
        .monsterinsights-popup,
        .monsterinsights-widget,
        .monsterinsights-indicator,
        .monsterinsights-logo,
        .monsterinsights-branding,
        [class*="monsterinsights"],
        [id*="monsterinsights"],
        [class*="MonsterInsights"],
        [id*="MonsterInsights"],
        /* Target Monster Insights variations */
        [class*="monster-insights"],
        [id*="monster-insights"],
        [class*="Monster-Insights"],
        [id*="Monster-Insights"],
        /* Target common tracking/analytics badges */
        [class*="analytics-badge"],
        [class*="tracking-badge"],
        /* Target by image alt text pattern */
        img[alt*="MonsterInsights"],
        img[alt*="Monster Insights"],
        img[src*="monsterinsights"],
        img[src*="monster-insights"],
        /* Target anchor links */
        a[href*="monsterinsights"],
        a[href*="monster-insights"],
        /* Target divs with inline styles (common for injected content) */
        div[style*="monsterinsights"],
        /* Specific patterns seen in wild */
        .mi-badge,
        .mi-tracking,
        .mi-widget,
        #mi-badge,
        #mi-tracking {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            pointer-events: none !important;
        }
        
        /* ===============================================
           DECORATIVE ELEMENTS
           =============================================== */
        .ji-footer-decoration {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.1) 50%, 
                transparent 100%);
        }
        
        /* ===============================================
           ACCESSIBILITY
           =============================================== */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        .ji-footer-link:focus-visible,
        .ji-footer-social-link:focus-visible,
        .ji-newsletter-input:focus-visible,
        .ji-newsletter-submit:focus-visible,
        .ji-back-to-top:focus-visible {
            outline: 2px solid var(--footer-text);
            outline-offset: 2px;
        }
    </style>

    <!-- Ultra Stylish Minna Style Footer -->
    <footer class="ji-site-footer">
        <div class="ji-footer-decoration"></div>
        
        <div class="ji-footer-container">
            
            <!-- Footer Top Section -->
            <div class="ji-footer-top">
                <div class="ji-footer-grid">
                    
                    <!-- Brand Section -->
                    <div class="ji-footer-brand">
                        <div class="ji-footer-logo-wrapper">
                            <div class="ji-footer-logo-icon">JI</div>
                            <div class="ji-footer-logo-text">
                                <div class="ji-footer-logo-title">Joseikin Insight</div>
                                <div class="ji-footer-logo-subtitle">助成金インサイト</div>
                            </div>
                        </div>
                        
                        <p class="ji-footer-description">
                            日本全国の助成金・補助金情報を一元化し、あなたに最適な支援制度を見つけるお手伝いをします。最新情報を随時更新中。
                        </p>
                        
                        <!-- Social Links -->
                        <div class="ji-footer-social">
                            <?php
                            $sns_urls = gi_get_sns_urls();
                            $social_icons = [
                                'twitter' => 'fab fa-twitter',
                                'facebook' => 'fab fa-facebook-f',
                                'instagram' => 'fab fa-instagram',
                                'youtube' => 'fab fa-youtube',
                                'note' => 'fas fa-sticky-note',
                                'linkedin' => 'fab fa-linkedin-in'
                            ];
                            
                            foreach ($sns_urls as $platform => $url) {
                                if (!empty($url) && isset($social_icons[$platform])) {
                                    echo '<a href="' . esc_url($url) . '" class="ji-footer-social-link" aria-label="' . ucfirst($platform) . '" target="_blank" rel="noopener noreferrer">';
                                    echo '<i class="' . esc_attr($social_icons[$platform]) . '"></i>';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Services Column -->
                    <div class="ji-footer-column">
                        <h3 class="ji-footer-column-title">Services</h3>
                        <nav class="ji-footer-nav">
                            <a href="<?php echo esc_url(get_post_type_archive_link('grant')); ?>" class="ji-footer-link">
                                <i class="fas fa-search"></i>
                                <span>助成金を探す</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="ji-footer-link">
                                <i class="fas fa-stethoscope"></i>
                                <span>診断システム</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/grants/?status=active')); ?>" class="ji-footer-link">
                                <i class="fas fa-clock"></i>
                                <span>募集中の助成金</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/grants/?orderby=new')); ?>" class="ji-footer-link">
                                <i class="fas fa-star"></i>
                                <span>新着助成金</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/calculator/')); ?>" class="ji-footer-link">
                                <i class="fas fa-calculator"></i>
                                <span>助成金計算ツール</span>
                            </a>
                        </nav>
                    </div>

                    <!-- Guide Column -->
                    <div class="ji-footer-column">
                        <h3 class="ji-footer-column-title">Guide</h3>
                        <nav class="ji-footer-nav">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="ji-footer-link">
                                <i class="fas fa-home"></i>
                                <span>ホーム</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/how-to-use/')); ?>" class="ji-footer-link">
                                <i class="fas fa-book-open"></i>
                                <span>使い方ガイド</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="ji-footer-link">
                                <i class="fas fa-question-circle"></i>
                                <span>よくある質問</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/knowledge/')); ?>" class="ji-footer-link">
                                <i class="fas fa-graduation-cap"></i>
                                <span>助成金の基礎知識</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/glossary/')); ?>" class="ji-footer-link">
                                <i class="fas fa-book"></i>
                                <span>用語集</span>
                            </a>
                        </nav>
                    </div>

                    <!-- Company Column -->
                    <div class="ji-footer-column">
                        <h3 class="ji-footer-column-title">Company</h3>
                        <nav class="ji-footer-nav">
                            <a href="<?php echo esc_url(home_url('/about/')); ?>" class="ji-footer-link">
                                <i class="fas fa-info-circle"></i>
                                <span>運営者情報</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="ji-footer-link">
                                <i class="fas fa-envelope"></i>
                                <span>お問い合わせ</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/column/')); ?>" class="ji-footer-link">
                                <i class="fas fa-rss"></i>
                                <span>コラム・お知らせ</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/support/')); ?>" class="ji-footer-link">
                                <i class="fas fa-life-ring"></i>
                                <span>ヘルプセンター</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/sitemap/')); ?>" class="ji-footer-link">
                                <i class="fas fa-sitemap"></i>
                                <span>サイトマップ</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <?php
            $stats = gi_get_cached_stats();
            if ($stats && !empty($stats['total_grants'])):
            ?>
            <div class="ji-footer-stats">
                <div class="ji-stats-grid">
                    <div class="ji-stat-item">
                        <span class="ji-stat-number"><?php echo number_format($stats['total_grants']); ?></span>
                        <div class="ji-stat-label">掲載助成金数</div>
                        <div class="ji-stat-sublabel">Total Grants</div>
                    </div>
                    
                    <?php if (!empty($stats['active_grants'])): ?>
                    <div class="ji-stat-item">
                        <span class="ji-stat-number"><?php echo number_format($stats['active_grants']); ?></span>
                        <div class="ji-stat-label">募集中</div>
                        <div class="ji-stat-sublabel">Active Now</div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="ji-stat-item">
                        <span class="ji-stat-number">47</span>
                        <div class="ji-stat-label">都道府県対応</div>
                        <div class="ji-stat-sublabel">Prefectures</div>
                    </div>
                    
                    <?php if (!empty($stats['total_views'])): ?>
                    <div class="ji-stat-item">
                        <span class="ji-stat-number"><?php echo number_format($stats['total_views']); ?></span>
                        <div class="ji-stat-label">総閲覧数</div>
                        <div class="ji-stat-sublabel">Total Views</div>
                    </div>
                    <?php else: ?>
                    <div class="ji-stat-item">
                        <span class="ji-stat-number">24/7</span>
                        <div class="ji-stat-label">情報更新</div>
                        <div class="ji-stat-sublabel">Always Updated</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Newsletter Section -->
            <div class="ji-footer-newsletter">
                <div class="ji-newsletter-content">
                    <div class="ji-newsletter-badge">Newsletter</div>
                    <h3 class="ji-newsletter-title">最新情報をお届けします</h3>
                    <p class="ji-newsletter-description">
                        新着助成金情報やお得なキャンペーン情報をいち早くお届け。今すぐ登録して、あなたに最適な支援制度を見逃さないようにしましょう。
                    </p>
                    <form class="ji-newsletter-form" id="ji-newsletter-form">
                        <input 
                            type="email" 
                            class="ji-newsletter-input" 
                            placeholder="メールアドレスを入力"
                            required
                            aria-label="メールアドレス"
                        >
                        <button type="submit" class="ji-newsletter-submit">
                            登録する
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="ji-footer-bottom">
                <div class="ji-footer-bottom-content">
                    <div class="ji-footer-copyright">
                        &copy; <?php echo date('Y'); ?> <strong><?php bloginfo('name'); ?></strong>. All rights reserved.
                    </div>
                    
                    <nav class="ji-footer-legal-links">
                        <a href="<?php echo esc_url(home_url('/privacy/')); ?>" class="ji-footer-legal-link">プライバシーポリシー</a>
                        <a href="<?php echo esc_url(home_url('/terms/')); ?>" class="ji-footer-legal-link">利用規約</a>
                        <a href="<?php echo esc_url(home_url('/disclaimer/')); ?>" class="ji-footer-legal-link">免責事項</a>
                    </nav>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="ji-back-to-top" class="ji-back-to-top" aria-label="ページトップへ戻る">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
    /**
     * Joseikin Insight Footer JavaScript
     * Back to Top & Newsletter functionality
     */
    (function() {
        'use strict';
        
        // Back to Top Button
        const backToTopBtn = document.getElementById('ji-back-to-top');
        
        function updateBackToTop() {
            if (window.scrollY > 400) {
                backToTopBtn?.classList.add('show');
            } else {
                backToTopBtn?.classList.remove('show');
            }
        }
        
        // Initial check
        updateBackToTop();
        
        // Update on scroll (passive for better performance)
        window.addEventListener('scroll', updateBackToTop, { passive: true });
        
        // Smooth scroll to top on click
        backToTopBtn?.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Newsletter Form
        const newsletterForm = document.getElementById('ji-newsletter-form');
        
        newsletterForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const submitBtn = this.querySelector('button[type="submit"]');
            const email = emailInput?.value;
            
            if (!email) return;
            
            // Show loading state
            const originalText = submitBtn.textContent;
            submitBtn.textContent = '登録中...';
            submitBtn.disabled = true;
            
            // Simulate API call (replace with actual implementation)
            setTimeout(() => {
                // Success
                submitBtn.textContent = '登録完了！';
                submitBtn.style.background = '#10b981';
                emailInput.value = '';
                
                // Reset after 3 seconds
                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    submitBtn.style.background = '';
                }, 3000);
                
                console.log('Newsletter subscription:', email);
            }, 1000);
        });
        
        console.log('[✓] Joseikin Insight Footer initialized');
        
        // Remove MonsterInsights and conflicting elements dynamically
        function removeUnwantedElements() {
            // MonsterInsights patterns
            const monsterInsightsSelectors = [
                '[class*="monsterinsights"]',
                '[id*="monsterinsights"]',
                '[class*="MonsterInsights"]',
                '[id*="MonsterInsights"]',
                '[class*="monster-insights"]',
                '[id*="monster-insights"]',
                'img[alt*="MonsterInsights"]',
                'img[alt*="Monster Insights"]',
                'img[src*="monsterinsights"]',
                'a[href*="monsterinsights"]',
                '.mi-badge',
                '#mi-badge'
            ];
            
            // Conflicting back-to-top buttons
            const conflictingButtons = [
                'button:not(#ji-back-to-top)[class*="back-to-top"]',
                'button:not(#ji-back-to-top)[id*="back-to-top"]',
                'a:not(#ji-back-to-top)[class*="back-to-top"]',
                '.back-to-top:not(#ji-back-to-top)',
                '#back-to-top:not(.ji-back-to-top)',
                '[class*="scroll-to-top"]:not(#ji-back-to-top)',
                '[class*="scrolltop"]:not(#ji-back-to-top)'
            ];
            
            const allSelectors = [...monsterInsightsSelectors, ...conflictingButtons];
            
            allSelectors.forEach(selector => {
                try {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(el => {
                        if (el && el.id !== 'ji-back-to-top') {
                            el.remove();
                            console.log('[Removed unwanted element]:', selector);
                        }
                    });
                } catch (e) {
                    // Ignore selector errors
                }
            });
        }
        
        // Run immediately
        removeUnwantedElements();
        
        // Run after DOM is fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', removeUnwantedElements);
        }
        
        // Run after all resources are loaded (including plugins)
        window.addEventListener('load', () => {
            removeUnwantedElements();
            // Run again after a delay to catch late-loading elements
            setTimeout(removeUnwantedElements, 1000);
            setTimeout(removeUnwantedElements, 3000);
        });
        
        // Monitor for dynamically added elements using MutationObserver
        const observer = new MutationObserver((mutations) => {
            let shouldCheck = false;
            mutations.forEach(mutation => {
                if (mutation.addedNodes.length > 0) {
                    shouldCheck = true;
                }
            });
            if (shouldCheck) {
                removeUnwantedElements();
            }
        });
        
        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
    })();
    </script>

    <?php 
    // グローバルスティッキーCTAバナーを表示
    get_template_part('template-parts/global-sticky-cta'); 
    ?>

    <?php wp_footer(); ?>
</body>
</html>