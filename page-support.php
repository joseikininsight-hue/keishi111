<?php
/**
 * Template Name: ヘルプセンター
 * Description: 助成金インサイトのヘルプ・サポートページ
 * 
 * @package Joseikin_Insight
 * @version 1.0.0
 */

get_header();
?>

<style>
    /* Support Page Specific Styles */
    .support-hero {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 4rem 1rem;
        text-align: center;
    }
    
    .support-hero h1 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    
    .support-hero p {
        font-size: 1.125rem;
        opacity: 0.95;
        max-width: 800px;
        margin: 0 auto 2rem;
        line-height: 1.7;
    }
    
    .search-box {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }
    
    .search-input {
        width: 100%;
        padding: 1rem 3.5rem 1rem 1.5rem;
        border-radius: 12px;
        border: none;
        font-size: 1rem;
        font-family: inherit;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
    
    .search-input:focus {
        outline: none;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    }
    
    .search-button {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .search-button:hover {
        transform: translateY(-50%) scale(1.05);
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
    
    .support-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 1rem;
    }
    
    .quick-links {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    @media (min-width: 640px) {
        .quick-links {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 1024px) {
        .quick-links {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    .quick-link-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
    }
    
    .quick-link-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);
    }
    
    .quick-link-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        margin: 0 auto 1rem;
    }
    
    .quick-link-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .quick-link-desc {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.6;
    }
    
    .faq-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 3rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .faq-section h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        color: #1e293b;
        text-align: center;
    }
    
    .faq-categories {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    @media (min-width: 768px) {
        .faq-categories {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .faq-category {
        background: #f8fafc;
        border-radius: 8px;
        padding: 1.5rem;
    }
    
    .faq-category-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .faq-category-title i {
        color: #10b981;
    }
    
    .faq-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .faq-list li {
        margin-bottom: 0.75rem;
    }
    
    .faq-list a {
        color: #475569;
        text-decoration: none;
        font-size: 0.9375rem;
        transition: all 0.2s ease;
        display: block;
        padding: 0.5rem;
        border-radius: 4px;
    }
    
    .faq-list a:hover {
        color: #10b981;
        background: white;
        padding-left: 1rem;
    }
    
    .contact-section {
        background: #f8fafc;
        border-radius: 12px;
        padding: 3rem 2rem;
        text-align: center;
    }
    
    .contact-section h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1e293b;
    }
    
    .contact-section p {
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .contact-methods {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        max-width: 800px;
        margin: 0 auto 2rem;
    }
    
    @media (min-width: 768px) {
        .contact-methods {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    .contact-method {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
    }
    
    .contact-method-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
    }
    
    .contact-method-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .contact-method-info {
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .contact-btn {
        display: inline-block;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.125rem;
        transition: all 0.3s ease;
    }
    
    .contact-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
        color: white;
    }
    
    .resources-section {
        margin-top: 3rem;
    }
    
    .resources-section h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        color: #1e293b;
        text-align: center;
    }
    
    .resources-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    @media (min-width: 768px) {
        .resources-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    .resource-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .resource-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    
    .resource-card h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .resource-card h3 i {
        color: #10b981;
    }
    
    .resource-card p {
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }
    
    .resource-link {
        color: #10b981;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .resource-link:hover {
        gap: 0.75rem;
    }
</style>

<!-- Hero Section -->
<section class="support-hero">
    <div>
        <h1>ヘルプセンター</h1>
        <p>助成金インサイトの使い方、よくある質問、サポート情報をご案内します。</p>
        
        <div class="search-box">
            <input type="search" class="search-input" placeholder="お困りのことを検索...">
            <button class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="breadcrumb-container">
        <a href="<?php echo esc_url(home_url('/')); ?>" style="color: #10b981; text-decoration: none;">ホーム</a>
        <span>/</span>
        <span>ヘルプセンター</span>
    </div>
</div>

<!-- Main Content -->
<div class="support-container">
    
    <!-- Quick Links -->
    <div class="quick-links">
        <a href="<?php echo esc_url(home_url('/how-to-use/')); ?>" class="quick-link-card">
            <div class="quick-link-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="quick-link-title">使い方ガイド</div>
            <div class="quick-link-desc">サイトの基本的な使い方を解説</div>
        </a>
        
        <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="quick-link-card">
            <div class="quick-link-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="quick-link-title">よくある質問</div>
            <div class="quick-link-desc">頻繁に寄せられる質問と回答</div>
        </a>
        
        <a href="<?php echo esc_url(home_url('/knowledge/')); ?>" class="quick-link-card">
            <div class="quick-link-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="quick-link-title">基礎知識</div>
            <div class="quick-link-desc">助成金の基本を学ぶ</div>
        </a>
        
        <a href="<?php echo esc_url(home_url('/glossary/')); ?>" class="quick-link-card">
            <div class="quick-link-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="quick-link-title">用語集</div>
            <div class="quick-link-desc">専門用語をわかりやすく解説</div>
        </a>
    </div>
    
    <!-- FAQ Categories -->
    <div class="faq-section">
        <h2>よくある質問カテゴリー</h2>
        
        <div class="faq-categories">
            <div class="faq-category">
                <h3 class="faq-category-title">
                    <i class="fas fa-search"></i>
                    助成金の探し方
                </h3>
                <ul class="faq-list">
                    <li><a href="<?php echo esc_url(home_url('/faq/#search-1')); ?>">条件を指定して助成金を探す方法は？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#search-2')); ?>">業種に合った助成金を見つけるには？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#search-3')); ?>">地域別の助成金を探すには？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#search-4')); ?>">募集中の助成金だけを表示するには？</a></li>
                </ul>
            </div>
            
            <div class="faq-category">
                <h3 class="faq-category-title">
                    <i class="fas fa-file-alt"></i>
                    申請について
                </h3>
                <ul class="faq-list">
                    <li><a href="<?php echo esc_url(home_url('/faq/#apply-1')); ?>">助成金の申請手順は？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#apply-2')); ?>">必要な書類は何ですか？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#apply-3')); ?>">申請期限はどこで確認できますか？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#apply-4')); ?>">複数の助成金に同時申請できますか？</a></li>
                </ul>
            </div>
            
            <div class="faq-category">
                <h3 class="faq-category-title">
                    <i class="fas fa-calculator"></i>
                    計算ツールについて
                </h3>
                <ul class="faq-list">
                    <li><a href="<?php echo esc_url(home_url('/faq/#calc-1')); ?>">計算結果は正確ですか？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#calc-2')); ?>">入力データは保存されますか？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#calc-3')); ?>">計算結果を印刷できますか？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#calc-4')); ?>">スマートフォンでも使えますか？</a></li>
                </ul>
            </div>
            
            <div class="faq-category">
                <h3 class="faq-category-title">
                    <i class="fas fa-user-circle"></i>
                    アカウント・会員
                </h3>
                <ul class="faq-list">
                    <li><a href="<?php echo esc_url(home_url('/faq/#account-1')); ?>">会員登録は必要ですか？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#account-2')); ?>">利用料金はかかりますか？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#account-3')); ?>">パスワードを忘れた場合は？</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/#account-4')); ?>">退会する方法は？</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Resources -->
    <div class="resources-section">
        <h2>お役立ちリソース</h2>
        
        <div class="resources-grid">
            <div class="resource-card">
                <h3>
                    <i class="fas fa-video"></i>
                    動画ガイド
                </h3>
                <p>助成金の探し方、申請手順を動画でわかりやすく解説しています。</p>
                <a href="<?php echo esc_url(home_url('/videos/')); ?>" class="resource-link">
                    動画を見る
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="resource-card">
                <h3>
                    <i class="fas fa-download"></i>
                    ダウンロード資料
                </h3>
                <p>助成金申請に役立つテンプレートやチェックリストをダウンロードできます。</p>
                <a href="<?php echo esc_url(home_url('/downloads/')); ?>" class="resource-link">
                    資料を見る
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="resource-card">
                <h3>
                    <i class="fas fa-newspaper"></i>
                    コラム・記事
                </h3>
                <p>助成金に関する最新情報やノウハウを定期的に配信しています。</p>
                <a href="<?php echo esc_url(home_url('/column/')); ?>" class="resource-link">
                    記事を読む
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
</div>

<!-- Contact Section -->
<div class="support-container">
    <div class="contact-section">
        <h2>お困りの際はお気軽にご連絡ください</h2>
        <p>上記で解決しない場合は、以下の方法でお問い合わせいただけます。専門スタッフが丁寧にサポートいたします。</p>
        
        <div class="contact-methods">
            <div class="contact-method">
                <div class="contact-method-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="contact-method-title">メール</div>
                <div class="contact-method-info">24時間受付<br>2営業日以内に回答</div>
            </div>
            
            <div class="contact-method">
                <div class="contact-method-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="contact-method-title">電話</div>
                <div class="contact-method-info">平日 10:00-18:00<br>専門スタッフが対応</div>
            </div>
            
            <div class="contact-method">
                <div class="contact-method-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="contact-method-title">チャット</div>
                <div class="contact-method-info">平日 10:00-18:00<br>リアルタイム対応</div>
            </div>
        </div>
        
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="contact-btn">
            <i class="fas fa-paper-plane" style="margin-right: 0.5rem;"></i>
            お問い合わせフォーム
        </a>
    </div>
</div>

<?php get_footer(); ?>
