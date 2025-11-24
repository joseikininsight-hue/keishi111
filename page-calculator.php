<?php
/**
 * Template Name: 助成金計算ツール
 * Description: 助成金・補助金の金額シミュレーション計算ツール
 * 
 * @package Joseikin_Insight
 * @version 1.0.0
 */

get_header();
?>

<style>
    /* Calculator Page Specific Styles */
    .calculator-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 1rem;
        text-align: center;
    }
    
    .calculator-hero h1 {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    
    .calculator-hero p {
        font-size: 1.125rem;
        opacity: 0.95;
        max-width: 800px;
        margin: 0 auto 2rem;
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
    
    .calculator-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 1rem;
    }
    
    .calculator-intro {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .calculator-intro h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1e293b;
    }
    
    .calculator-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }
    
    @media (min-width: 768px) {
        .calculator-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 1024px) {
        .calculator-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    .calculator-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .calculator-card:hover {
        border-color: #667eea;
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
    }
    
    .calculator-card-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
    }
    
    .calculator-card h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1e293b;
    }
    
    .calculator-card p {
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }
    
    .calculator-card-features {
        list-style: none;
        padding: 0;
        margin-bottom: 1.5rem;
    }
    
    .calculator-card-features li {
        padding: 0.5rem 0;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .calculator-card-features li::before {
        content: "✓";
        color: #667eea;
        font-weight: 700;
    }
    
    .calculator-btn {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .calculator-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .info-section {
        background: #f8fafc;
        padding: 3rem 0;
        margin-top: 3rem;
    }
    
    .info-section-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .info-section h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: center;
        color: #1e293b;
    }
    
    .faq-list {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .faq-item {
        border-bottom: 1px solid #e2e8f0;
        padding: 1.5rem 0;
    }
    
    .faq-item:last-child {
        border-bottom: none;
    }
    
    .faq-question {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .faq-question::before {
        content: "Q";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: #667eea;
        color: white;
        border-radius: 50%;
        font-weight: 700;
        flex-shrink: 0;
    }
    
    .faq-answer {
        color: #475569;
        line-height: 1.7;
        padding-left: 2.5rem;
    }
    
    .benefits-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    @media (min-width: 768px) {
        .benefits-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .benefit-item {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        gap: 1rem;
    }
    
    .benefit-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    
    .benefit-content h3 {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1e293b;
    }
    
    .benefit-content p {
        color: #64748b;
        line-height: 1.6;
        font-size: 0.9375rem;
    }
</style>

<!-- Hero Section -->
<section class="calculator-hero">
    <div>
        <h1>助成金計算ツール</h1>
        <p>あなたの事業に最適な助成金・補助金の受給額をシミュレーション。簡単な入力で、受け取れる金額の目安がすぐにわかります。</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="breadcrumb-container">
        <a href="<?php echo esc_url(home_url('/')); ?>" style="color: #667eea; text-decoration: none;">ホーム</a>
        <span>/</span>
        <span>助成金計算ツール</span>
    </div>
</div>

<!-- Main Content -->
<div class="calculator-container">
    
    <!-- Introduction -->
    <div class="calculator-intro">
        <h2>助成金・補助金の受給額をシミュレーション</h2>
        <p>助成金インサイトの計算ツールは、各種助成金・補助金の受給可能額を簡単にシミュレーションできるツールです。事業計画や申請前の参考として、ぜひご活用ください。</p>
    </div>
    
    <!-- Calculator Cards -->
    <div class="calculator-grid">
        
        <!-- 雇用関係助成金 -->
        <div class="calculator-card">
            <div class="calculator-card-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>雇用関係助成金計算</h3>
            <p>キャリアアップ助成金、特定求職者雇用開発助成金など、雇用に関する助成金の受給額を計算します。</p>
            <ul class="calculator-card-features">
                <li>キャリアアップ助成金</li>
                <li>特定求職者雇用開発助成金</li>
                <li>トライアル雇用助成金</li>
                <li>両立支援等助成金</li>
            </ul>
            <a href="<?php echo esc_url(home_url('/calculator/employment/')); ?>" class="calculator-btn">計算する</a>
        </div>
        
        <!-- 設備投資補助金 -->
        <div class="calculator-card">
            <div class="calculator-card-icon">
                <i class="fas fa-industry"></i>
            </div>
            <h3>設備投資補助金計算</h3>
            <p>ものづくり補助金、IT導入補助金など、設備投資に関する補助金の受給額を計算します。</p>
            <ul class="calculator-card-features">
                <li>ものづくり補助金</li>
                <li>IT導入補助金</li>
                <li>小規模事業者持続化補助金</li>
                <li>事業再構築補助金</li>
            </ul>
            <a href="<?php echo esc_url(home_url('/calculator/equipment/')); ?>" class="calculator-btn">計算する</a>
        </div>
        
        <!-- 研究開発補助金 -->
        <div class="calculator-card">
            <div class="calculator-card-icon">
                <i class="fas fa-flask"></i>
            </div>
            <h3>研究開発補助金計算</h3>
            <p>研究開発型スタートアップ支援事業など、研究開発に関する補助金の受給額を計算します。</p>
            <ul class="calculator-card-features">
                <li>研究開発型スタートアップ支援</li>
                <li>戦略的基盤技術高度化支援事業</li>
                <li>省エネルギー投資促進支援事業</li>
                <li>新エネルギー等設備導入支援</li>
            </ul>
            <a href="<?php echo esc_url(home_url('/calculator/research/')); ?>" class="calculator-btn">計算する</a>
        </div>
        
        <!-- 創業・起業支援 -->
        <div class="calculator-card">
            <div class="calculator-card-icon">
                <i class="fas fa-rocket"></i>
            </div>
            <h3>創業・起業支援計算</h3>
            <p>創業補助金、スタートアップ支援など、創業・起業に関する助成金の受給額を計算します。</p>
            <ul class="calculator-card-features">
                <li>創業補助金</li>
                <li>地域創造的起業補助金</li>
                <li>新規開業資金</li>
                <li>女性・若者・シニア創業サポート</li>
            </ul>
            <a href="<?php echo esc_url(home_url('/calculator/startup/')); ?>" class="calculator-btn">計算する</a>
        </div>
        
        <!-- 人材育成・能力開発 -->
        <div class="calculator-card">
            <div class="calculator-card-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h3>人材育成・能力開発計算</h3>
            <p>人材開発支援助成金、教育訓練給付金など、人材育成に関する助成金の受給額を計算します。</p>
            <ul class="calculator-card-features">
                <li>人材開発支援助成金</li>
                <li>キャリア形成促進助成金</li>
                <li>専門実践教育訓練給付金</li>
                <li>職業能力開発推進者選任費</li>
            </ul>
            <a href="<?php echo esc_url(home_url('/calculator/training/')); ?>" class="calculator-btn">計算する</a>
        </div>
        
        <!-- 働き方改革支援 -->
        <div class="calculator-card">
            <div class="calculator-card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <h3>働き方改革支援計算</h3>
            <p>働き方改革推進支援助成金、時間外労働等改善助成金など、働き方改革に関する助成金を計算します。</p>
            <ul class="calculator-card-features">
                <li>働き方改革推進支援助成金</li>
                <li>時間外労働等改善助成金</li>
                <li>業務改善助成金</li>
                <li>テレワーク導入促進助成金</li>
            </ul>
            <a href="<?php echo esc_url(home_url('/calculator/workstyle/')); ?>" class="calculator-btn">計算する</a>
        </div>
        
    </div>
    
</div>

<!-- Benefits Section -->
<section class="info-section">
    <div class="info-section-content">
        <h2>計算ツールのメリット</h2>
        
        <div class="benefits-grid">
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-stopwatch"></i>
                </div>
                <div class="benefit-content">
                    <h3>スピーディーな計算</h3>
                    <p>必要な情報を入力するだけで、数秒で受給額の目安を算出。複雑な計算式も自動で処理します。</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="benefit-content">
                    <h3>正確な見積もり</h3>
                    <p>最新の助成金制度に基づいた計算式を使用。制度変更にも随時対応しています。</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="benefit-content">
                    <h3>いつでもどこでも</h3>
                    <p>スマートフォン、タブレット、PCから24時間いつでもアクセス可能。場所を選ばず利用できます。</p>
                </div>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="benefit-content">
                    <h3>完全無料</h3>
                    <p>すべての計算ツールを無料でご利用いただけます。会員登録も不要です。</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<div class="info-section">
    <div class="info-section-content">
        <h2>よくある質問</h2>
        
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-question">計算結果は正確ですか？</div>
                <div class="faq-answer">
                    当サイトの計算ツールは、各助成金・補助金の公式な計算方法に基づいて設計されています。ただし、実際の受給額は審査結果や申請時期、企業の状況により変動する可能性がありますので、あくまで目安としてご利用ください。
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">入力した情報は保存されますか？</div>
                <div class="faq-answer">
                    入力された情報は計算結果の表示のみに使用され、サーバーに保存されることはありません。個人情報の保護には万全を期しております。
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">計算結果をもとに申請できますか？</div>
                <div class="faq-answer">
                    計算結果は申請の参考資料としてご利用いただけますが、正式な申請には各助成金の申請要件を満たし、必要書類を揃える必要があります。詳しくは各助成金の詳細ページをご確認ください。
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">複数の助成金を組み合わせて受給できますか？</div>
                <div class="faq-answer">
                    助成金によっては併用が可能な場合もありますが、制限がある場合もあります。各助成金の詳細ページで併用可否をご確認いただくか、専門家にご相談されることをおすすめします。
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">計算ツールは無料で使えますか？</div>
                <div class="faq-answer">
                    はい、すべての計算ツールを完全無料でご利用いただけます。会員登録も不要で、何度でもお使いいただけます。
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<section class="calculator-hero" style="padding: 3rem 1rem;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h2 style="font-size: 2rem; margin-bottom: 1rem;">まずは診断してみませんか？</h2>
        <p style="margin-bottom: 2rem; opacity: 0.95;">簡単な質問に答えるだけで、あなたに最適な助成金を見つけることができます。</p>
        <a href="<?php echo esc_url(home_url('/subsidy-diagnosis/')); ?>" class="calculator-btn" style="font-size: 1.125rem; padding: 1rem 2rem;">
            <i class="fas fa-stethoscope" style="margin-right: 0.5rem;"></i>
            助成金診断を始める
        </a>
    </div>
</section>

<?php get_footer(); ?>
