<?php
/**
 * 補助金・助成金情報サイト - お問い合わせページ（表示テンプレート部分）
 * Grant & Subsidy Information Site - Contact Page (Display Template Part)
 * @package Grant_Insight_Contact
 * @version 5.0-template-only
 * 
 * 注意: このファイルは表示のみを担当します。POST処理は page-contact.php で行われます。
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// このファイルは page-contact.php から include されるため、get_header() は不要

// 構造化データ
$contact_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => 'お問い合わせ - 補助金インサイト',
    'description' => '補助金インサイトへのお問い合わせフォーム。サービスに関するご質問やご相談を承ります。',
    'url' => 'https://joseikin-insight.com/contact/'
);
?>

<!-- SEO メタタグ -->
<title>お問い合わせ | 補助金インサイト - AI活用型補助金検索ポータル</title>
<meta name="description" content="補助金インサイトへのお問い合わせフォーム。サービスに関するご質問、補助金検索のご相談、申請サポートに関するお問い合わせを承ります。">
<meta name="keywords" content="お問い合わせ,問い合わせ,相談,サポート,補助金インサイト,連絡先">
<link rel="canonical" href="https://joseikin-insight.com/contact/">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="お問い合わせ | 補助金インサイト">
<meta property="og:description" content="補助金インサイトへのお問い合わせフォーム。サービスに関するご質問やご相談を承ります。">
<meta property="og:url" content="https://joseikin-insight.com/contact/">

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($contact_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<article class="contact-page" itemscope itemtype="https://schema.org/ContactPage">
    
    <!-- ページヘッダー -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title" itemprop="headline">お問い合わせ</h1>
            <p class="page-subtitle">まずはお気軽にご相談ください</p>
            <p class="page-description">
                補助金・助成金に関するご質問やサイトの使い方など、どのようなことでもお気軽にお問い合わせください。専門スタッフが丁寧にご対応いたします。
            </p>
        </div>
    </header>
    
    <!-- メインコンテンツ -->
    <div class="page-content">
        <div class="container">
            
            <?php if ($form_success): ?>
            <!-- 送信成功メッセージ -->
            <section class="content-section success-message-section">
                <div class="success-message-box">
                    <div class="success-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <h2 class="success-title">お問い合わせを受け付けました</h2>
                    <p class="success-text">
                        お問い合わせいただき、誠にありがとうございます。<br>
                        ご入力いただいたメールアドレス宛に、受付完了メールを送信いたしました。<br>
                        内容を確認の上、2営業日以内に担当者よりご連絡させていただきます。
                    </p>
                    <div class="success-actions">
                        <a href="<?php echo home_url('/'); ?>" class="btn-primary">トップページへ戻る</a>
                        <a href="<?php echo home_url('/contact/'); ?>" class="btn-secondary">新しいお問い合わせ</a>
                    </div>
                </div>
            </section>
            <?php else: ?>
            
            <?php if (!empty($form_errors)): ?>
            <!-- エラーメッセージ -->
            <section class="content-section error-message-section">
                <div class="error-message-box">
                    <div class="error-header">
                        <div class="error-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <h3 class="error-title">入力内容にエラーがあります</h3>
                    </div>
                    <ul class="error-list">
                        <?php foreach ($form_errors as $error): ?>
                        <li><?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- よくあるご質問 -->
            <section class="content-section faq-section">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </span>
                    よくあるご質問
                </h2>
                <p class="section-intro">お問い合わせの前に、よくあるご質問をご確認ください。</p>
                
                <div class="faq-grid">
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <h3 class="faq-question">Q. 掲載されている補助金情報は最新ですか？</h3>
                        <p class="faq-answer">A. 当サイトでは、各省庁・自治体の公式情報を毎日収集し、週2回の専門スタッフによる確認を行っています。ただし、制度内容は予告なく変更される場合がありますので、申請前には必ず公式情報をご確認ください。</p>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <h3 class="faq-question">Q. 補助金の採択を保証してもらえますか？</h3>
                        <p class="faq-answer">A. 補助金・助成金の採択は、各制度の管轄機関が審査・決定するため、当サイトでは採択を保証することはできません。申請サポートサービスでは、書類作成のアドバイスや相談対応を行っています。</p>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <h3 class="faq-question">Q. サイトの利用に料金はかかりますか？</h3>
                        <p class="faq-answer">A. 補助金・助成金の検索機能は完全無料でご利用いただけます。申請サポートサービスなど一部の有料サービスについては、事前に料金を明示し、ご同意いただいた上で提供いたします。</p>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <h3 class="faq-question">Q. 個人情報はどのように管理されていますか？</h3>
                        <p class="faq-answer">A. お預かりした個人情報は、個人情報保護法に基づき適切に管理しています。SSL暗号化通信の採用、定期的なセキュリティ監査など、万全の体制で保護しています。詳しくは<a href="https://joseikin-insight.com/privacy/" class="text-link">プライバシーポリシー</a>をご覧ください。</p>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <h3 class="faq-question">Q. 申請代行サービスはありますか？</h3>
                        <p class="faq-answer">A. 申請書類作成のサポートや相談対応は行っておりますが、申請の代行業務は行っておりません。申請手続きはお客様ご自身で行っていただく必要があります。</p>
                    </div>
                </div>
            </section>
            
            <!-- お問い合わせフォーム -->
            <section class="content-section form-section">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </span>
                    お問い合わせフォーム
                </h2>
                
                <div class="form-container">
                    <form class="contact-form" id="contactForm" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('contact_form_submit', 'contact_form_nonce'); ?>
                        <input type="hidden" name="action" value="contact_form">
                        
                        <!-- お問い合わせ種別 -->
                        <div class="form-group">
                            <label for="inquiry-type" class="form-label required">
                                お問い合わせ種別
                            </label>
                            <select id="inquiry-type" name="inquiry_type" class="form-control" required>
                                <option value="">選択してください</option>
                                <option value="usage" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'usage') ? 'selected' : ''; ?>>サイトの使い方について</option>
                                <option value="grant-info" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'grant-info') ? 'selected' : ''; ?>>補助金・助成金の制度について</option>
                                <option value="update" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'update') ? 'selected' : ''; ?>>掲載情報の修正・更新</option>
                                <option value="media" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'media') ? 'selected' : ''; ?>>媒体掲載・取材依頼</option>
                                <option value="technical" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'technical') ? 'selected' : ''; ?>>技術的な問題・不具合</option>
                                <option value="other" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'other') ? 'selected' : ''; ?>>その他</option>
                            </select>
                        </div>
                        
                        <!-- お名前 -->
                        <div class="form-group">
                            <label for="name" class="form-label required">
                                お名前
                            </label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="山田 太郎" value="<?php echo isset($_POST['name']) ? esc_attr($_POST['name']) : ''; ?>" required>
                        </div>
                        
                        <!-- メールアドレス -->
                        <div class="form-group">
                            <label for="email" class="form-label required">
                                メールアドレス
                            </label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="example@example.com" value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" required>
                            <p class="form-help">回答のご連絡に使用いたします</p>
                        </div>
                        
                        <!-- 電話番号 -->
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                電話番号
                                <span class="optional-label">（任意）</span>
                            </label>
                            <input type="tel" id="phone" name="phone" class="form-control" placeholder="03-1234-5678" value="<?php echo isset($_POST['phone']) ? esc_attr($_POST['phone']) : ''; ?>">
                            <p class="form-help">緊急時のご連絡用</p>
                        </div>
                        
                        <!-- 会社名・団体名 -->
                        <div class="form-group">
                            <label for="company" class="form-label">
                                会社名・団体名
                                <span class="optional-label">（任意）</span>
                            </label>
                            <input type="text" id="company" name="company" class="form-control" placeholder="株式会社〇〇" value="<?php echo isset($_POST['company']) ? esc_attr($_POST['company']) : ''; ?>">
                        </div>
                        
                        <div class="form-row">
                            <!-- 業種 -->
                            <div class="form-group form-col">
                                <label for="industry" class="form-label">
                                    業種
                                    <span class="optional-label">（任意）</span>
                                </label>
                                <select id="industry" name="industry" class="form-control">
                                    <option value="">選択してください</option>
                                    <option value="manufacturing" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'manufacturing') ? 'selected' : ''; ?>>製造業</option>
                                    <option value="retail" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'retail') ? 'selected' : ''; ?>>小売業</option>
                                    <option value="service" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'service') ? 'selected' : ''; ?>>サービス業</option>
                                    <option value="it" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'it') ? 'selected' : ''; ?>>IT・通信業</option>
                                    <option value="construction" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'construction') ? 'selected' : ''; ?>>建設業</option>
                                    <option value="transport" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'transport') ? 'selected' : ''; ?>>運輸業</option>
                                    <option value="healthcare" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'healthcare') ? 'selected' : ''; ?>>医療・福祉</option>
                                    <option value="education" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'education') ? 'selected' : ''; ?>>教育・学習支援</option>
                                    <option value="agriculture" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'agriculture') ? 'selected' : ''; ?>>農林水産業</option>
                                    <option value="other" <?php echo (isset($_POST['industry']) && $_POST['industry'] === 'other') ? 'selected' : ''; ?>>その他</option>
                                </select>
                            </div>
                            
                            <!-- 従業員数 -->
                            <div class="form-group form-col">
                                <label for="employees" class="form-label">
                                    従業員数
                                    <span class="optional-label">（任意）</span>
                                </label>
                                <select id="employees" name="employees" class="form-control">
                                    <option value="">選択してください</option>
                                    <option value="1" <?php echo (isset($_POST['employees']) && $_POST['employees'] === '1') ? 'selected' : ''; ?>>1人（個人事業主）</option>
                                    <option value="2-5" <?php echo (isset($_POST['employees']) && $_POST['employees'] === '2-5') ? 'selected' : ''; ?>>2-5人</option>
                                    <option value="6-20" <?php echo (isset($_POST['employees']) && $_POST['employees'] === '6-20') ? 'selected' : ''; ?>>6-20人</option>
                                    <option value="21-50" <?php echo (isset($_POST['employees']) && $_POST['employees'] === '21-50') ? 'selected' : ''; ?>>21-50人</option>
                                    <option value="51-100" <?php echo (isset($_POST['employees']) && $_POST['employees'] === '51-100') ? 'selected' : ''; ?>>51-100人</option>
                                    <option value="101-300" <?php echo (isset($_POST['employees']) && $_POST['employees'] === '101-300') ? 'selected' : ''; ?>>101-300人</option>
                                    <option value="301+" <?php echo (isset($_POST['employees']) && $_POST['employees'] === '301+') ? 'selected' : ''; ?>>301人以上</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- 件名 -->
                        <div class="form-group">
                            <label for="subject" class="form-label required">
                                件名
                            </label>
                            <input type="text" id="subject" name="subject" class="form-control" placeholder="お問い合わせ件名" value="<?php echo isset($_POST['subject']) ? esc_attr($_POST['subject']) : ''; ?>" required>
                        </div>
                        
                        <!-- お問い合わせ内容 -->
                        <div class="form-group">
                            <label for="message" class="form-label required">
                                お問い合わせ内容
                            </label>
                            <textarea id="message" name="message" class="form-control" rows="8" placeholder="具体的にご記入ください（500文字以内）" maxlength="500" required><?php echo isset($_POST['message']) ? esc_textarea($_POST['message']) : ''; ?></textarea>
                            <p class="form-help">残り<span id="char-count">500</span>文字</p>
                        </div>
                        
                        <!-- ご希望の連絡方法 -->
                        <div class="form-group">
                            <label class="form-label">ご希望の連絡方法</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="contact_method" value="email" <?php echo (!isset($_POST['contact_method']) || $_POST['contact_method'] === 'email') ? 'checked' : ''; ?>>
                                    <span class="radio-text">メール</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="contact_method" value="phone" <?php echo (isset($_POST['contact_method']) && $_POST['contact_method'] === 'phone') ? 'checked' : ''; ?>>
                                    <span class="radio-text">電話</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="contact_method" value="either" <?php echo (isset($_POST['contact_method']) && $_POST['contact_method'] === 'either') ? 'checked' : ''; ?>>
                                    <span class="radio-text">どちらでも可</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- ご希望の連絡時間帯 -->
                        <div class="form-group">
                            <label class="form-label">ご希望の連絡時間帯</label>
                            <div class="checkbox-group-inline">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="contact_time[]" value="morning" <?php echo (isset($_POST['contact_time']) && in_array('morning', $_POST['contact_time'])) ? 'checked' : ''; ?>>
                                    <span class="checkbox-text">9:00-12:00</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="contact_time[]" value="afternoon" <?php echo (isset($_POST['contact_time']) && in_array('afternoon', $_POST['contact_time'])) ? 'checked' : ''; ?>>
                                    <span class="checkbox-text">13:00-17:00</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="contact_time[]" value="evening" <?php echo (isset($_POST['contact_time']) && in_array('evening', $_POST['contact_time'])) ? 'checked' : ''; ?>>
                                    <span class="checkbox-text">17:00-19:00</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="contact_time[]" value="anytime" <?php echo (!isset($_POST['contact_time']) || in_array('anytime', $_POST['contact_time'])) ? 'checked' : ''; ?>>
                                    <span class="checkbox-text">時間指定なし</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- 個人情報の取り扱い -->
                        <div class="privacy-notice">
                            <h3 class="privacy-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                個人情報の取り扱いについて
                            </h3>
                            <p>お問い合わせでお預かりした個人情報は、以下の目的でのみ使用いたします：</p>
                            <ul class="privacy-list">
                                <li>お問い合わせへの回答・対応</li>
                                <li>サービス改善のための統計分析</li>
                                <li>重要なお知らせの配信（ご希望の場合のみ）</li>
                            </ul>
                            <p>詳細は<a href="https://joseikin-insight.com/privacy/" class="text-link">プライバシーポリシー</a>をご確認ください。</p>
                        </div>
                        
                        <!-- プライバシーポリシー同意 -->
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label-large">
                                <input type="checkbox" id="privacy-agree" name="privacy_agree" required>
                                <span class="checkbox-text">
                                    個人情報の取り扱いに同意する
                                </span>
                            </label>
                        </div>
                        
                        <!-- 送信ボタン -->
                        <div class="form-actions">
                            <button type="submit" class="btn-submit">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                <span class="btn-text">送信する</span>
                            </button>
                        </div>
                        
                        <!-- 注意事項 -->
                        <div class="form-note">
                            <p class="note-title"><strong>送信前にご確認ください</strong></p>
                            <ul>
                                <li>入力内容に誤りがないかご確認ください</li>
                                <li>メールアドレスが正しくないと返信できません</li>
                                <li>通常2営業日以内にご返信いたします</li>
                            </ul>
                        </div>
                    </form>
                </div>
            </section>
            
            <?php endif; ?>
            
            <!-- その他のお問い合わせ方法 -->
            <section class="content-section contact-methods-section">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </span>
                    その他のお問い合わせ方法
                </h2>
                
                <div class="contact-methods-grid">
                    <div class="contact-method-card">
                        <div class="method-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <h3 class="method-title">お電話でのお問い合わせ</h3>
                        <p class="method-detail">TEL: 準備中</p>
                        <p class="method-time">受付時間: 平日 9:00-18:00（土日祝日除く）</p>
                    </div>
                    
                    <div class="contact-method-card">
                        <div class="method-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <h3 class="method-title">メールでのお問い合わせ</h3>
                        <p class="method-detail">Email: info@joseikin-insight.com</p>
                        <p class="method-time">回答まで2-3営業日いただく場合があります</p>
                    </div>
                    
                    <div class="contact-method-card">
                        <div class="method-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <h3 class="method-title">郵送でのお問い合わせ</h3>
                        <p class="method-detail">〒136-0073<br>東京都江東区北砂3-23-8　401</p>
                        <p class="method-time">補助金インサイト運営事務局 宛</p>
                    </div>
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
    --color-white: #ffffff;
    --color-black: #000000;
    --color-yellow: #ffeb3b;
    --color-yellow-dark: #ffc107;
    --color-yellow-light: #fff9c4;
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
    --color-success: #4caf50;
    --color-success-light: #c8e6c9;
    --color-error: #f44336;
    --color-error-light: #ffcdd2;
    --text-primary: var(--color-gray-900);
    --text-secondary: var(--color-gray-600);
    --bg-primary: var(--color-white);
    --bg-secondary: var(--color-gray-50);
    --bg-tertiary: var(--color-gray-100);
    --border-light: var(--color-gray-200);
    --border-medium: var(--color-gray-300);
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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
    --radius-2xl: 1rem;
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
    --transition-fast: 0.15s ease;
    --transition-base: 0.2s ease;
    --transition-slow: 0.3s ease;
}

* {
    box-sizing: border-box;
}

.contact-page {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans JP', sans-serif;
    color: var(--text-primary);
    background: var(--color-white);
    line-height: var(--line-height-normal);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
}

/* ========================================
   ページヘッダー
======================================== */
.page-header {
    background: var(--color-white);
    padding: var(--spacing-4xl) 0 var(--spacing-3xl);
    text-align: center;
    border-bottom: 1px solid var(--border-light);
}

.page-title {
    font-size: var(--font-size-4xl);
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-md);
    letter-spacing: -0.02em;
}

.page-subtitle {
    font-size: var(--font-size-xl);
    color: var(--text-secondary);
    margin: 0 0 var(--spacing-lg);
    font-weight: var(--font-weight-medium);
}

.page-description {
    font-size: var(--font-size-base);
    color: var(--text-secondary);
    max-width: 700px;
    margin: 0 auto;
    line-height: var(--line-height-relaxed);
}

.page-content {
    padding: var(--spacing-4xl) 0;
    background: var(--color-white);
}

.content-section {
    margin-bottom: var(--spacing-4xl);
}

.section-title {
    font-size: var(--font-size-2xl);
    font-weight: var(--font-weight-bold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-xl);
    padding-bottom: var(--spacing-md);
    border-bottom: 3px solid var(--color-primary);
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.title-icon {
    display: inline-flex;
    width: 32px;
    height: 32px;
    flex-shrink: 0;
}

.section-intro {
    color: var(--text-secondary);
    margin-bottom: var(--spacing-xl);
    font-size: var(--font-size-base);
}

/* ========================================
   成功メッセージ
======================================== */
.success-message-box {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid var(--color-success-light);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-4xl) var(--spacing-3xl);
    text-align: center;
    box-shadow: var(--shadow-lg);
    animation: slideDown 0.5s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.success-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: var(--color-white);
    border: 3px solid var(--color-success);
    border-radius: 50%;
    margin-bottom: var(--spacing-lg);
    color: var(--color-success);
    animation: scaleIn 0.5s ease 0.2s both;
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.5);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.success-title {
    font-size: var(--font-size-2xl);
    font-weight: var(--font-weight-bold);
    color: #166534;
    margin: 0 0 var(--spacing-md);
}

.success-text {
    font-size: var(--font-size-base);
    color: #15803d;
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--spacing-xl);
}

.success-actions {
    display: flex;
    gap: var(--spacing-md);
    justify-content: center;
    flex-wrap: wrap;
}

.btn-primary,
.btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-md) var(--spacing-2xl);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-semibold);
    border-radius: 50px;
    text-decoration: none;
    transition: all var(--transition-base);
    box-shadow: var(--shadow-sm);
}

.btn-primary {
    background: var(--color-primary);
    color: var(--color-black);
    border: 2px solid var(--color-primary);
}

.btn-primary:hover {
    background: var(--color-yellow-dark);
    border-color: var(--color-yellow-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--color-white);
    color: var(--text-primary);
    border: 2px solid var(--border-medium);
}

.btn-secondary:hover {
    background: var(--bg-secondary);
    border-color: var(--border-medium);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ========================================
   エラーメッセージ
======================================== */
.error-message-box {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 2px solid var(--color-error-light);
    border-left: 4px solid var(--color-error);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-xl);
    animation: slideDown 0.5s ease;
}

.error-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.error-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    color: var(--color-error);
    flex-shrink: 0;
}

.error-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: #991b1b;
    margin: 0;
}

.error-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.error-list li {
    position: relative;
    padding-left: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    color: #991b1b;
    font-size: var(--font-size-sm);
}

.error-list li::before {
    content: '•';
    position: absolute;
    left: var(--spacing-sm);
    color: var(--color-error);
    font-weight: bold;
    font-size: var(--font-size-lg);
}

/* ========================================
   FAQ
======================================== */
.faq-grid {
    display: grid;
    gap: var(--spacing-lg);
}

.faq-item {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    transition: all var(--transition-base);
    position: relative;
    overflow: hidden;
}

.faq-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--color-primary);
    transform: scaleY(0);
    transition: transform var(--transition-base);
}

.faq-item:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.faq-item:hover::before {
    transform: scaleY(1);
}

.faq-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: var(--color-yellow-light);
    border-radius: 50%;
    margin-bottom: var(--spacing-sm);
    color: var(--color-yellow-dark);
}

.faq-question {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-sm);
}

.faq-answer {
    font-size: var(--font-size-base);
    color: var(--text-secondary);
    line-height: var(--line-height-relaxed);
    margin: 0;
}

.text-link {
    color: var(--text-primary);
    text-decoration: underline;
    font-weight: var(--font-weight-medium);
    transition: color var(--transition-fast);
}

.text-link:hover {
    color: var(--color-yellow-dark);
}

/* ========================================
   フォーム
======================================== */
.form-container {
    background: var(--color-white);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-3xl);
    box-shadow: var(--shadow-sm);
}

.contact-form {
    max-width: 800px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: var(--spacing-xl);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-lg);
}

.form-col {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
}

.form-label.required::after {
    content: '必須';
    display: inline-block;
    margin-left: var(--spacing-sm);
    padding: 2px 8px;
    background: var(--color-gray-900);
    color: var(--color-white);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-bold);
    border-radius: var(--radius-sm);
}

.optional-label {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-normal);
    color: var(--text-secondary);
}

.form-control {
    width: 100%;
    padding: var(--spacing-md);
    font-size: var(--font-size-base);
    color: var(--text-primary);
    background: var(--color-white);
    border: 2px solid var(--border-medium);
    border-radius: var(--radius-lg);
    transition: all var(--transition-base);
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.1);
}

.form-control:hover {
    border-color: var(--color-gray-400);
}

textarea.form-control {
    resize: vertical;
    min-height: 150px;
    font-family: inherit;
}

.form-help {
    margin-top: var(--spacing-sm);
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

#char-count {
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
}

/* ラジオボタン・チェックボックス */
.radio-group,
.checkbox-group-inline {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-lg);
}

.radio-label,
.checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    cursor: pointer;
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-md);
    transition: background-color var(--transition-fast);
}

.radio-label:hover,
.checkbox-label:hover {
    background: var(--bg-secondary);
}

.radio-label input[type="radio"],
.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--color-primary);
}

.radio-text,
.checkbox-text {
    font-size: var(--font-size-base);
    color: var(--text-primary);
    user-select: none;
}

.checkbox-label-large {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    cursor: pointer;
    padding: var(--spacing-md);
    background: var(--bg-secondary);
    border: 2px solid var(--border-light);
    border-radius: var(--radius-lg);
    transition: all var(--transition-base);
}

.checkbox-label-large:hover {
    border-color: var(--color-primary);
    background: var(--color-yellow-light);
}

.checkbox-label-large input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: var(--color-primary);
}

/* プライバシー通知 */
.privacy-notice {
    background: var(--bg-tertiary);
    border-left: 4px solid var(--color-primary);
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-xl);
}

.privacy-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-sm);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.privacy-notice p {
    color: var(--text-secondary);
    line-height: var(--line-height-normal);
    margin: var(--spacing-sm) 0;
    font-size: var(--font-size-sm);
}

.privacy-list {
    list-style: none;
    padding: 0;
    margin: var(--spacing-sm) 0;
}

.privacy-list li {
    position: relative;
    padding-left: var(--spacing-xl);
    margin-bottom: var(--spacing-sm);
    color: var(--text-secondary);
    font-size: var(--font-size-sm);
}

.privacy-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 6px;
    height: 6px;
    background: var(--color-primary);
    border-radius: 50%;
}

/* 送信ボタン */
.form-actions {
    text-align: center;
    margin-top: var(--spacing-2xl);
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    padding: var(--spacing-lg) var(--spacing-4xl);
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-bold);
    color: var(--color-black);
    background: var(--color-primary);
    border: 3px solid var(--color-primary);
    border-radius: 50px;
    cursor: pointer;
    transition: all var(--transition-slow);
    box-shadow: var(--shadow-md);
}

.btn-submit:hover:not(:disabled) {
    background: transparent;
    color: var(--color-primary);
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(255, 213, 0, 0.3);
}

.btn-submit:active:not(:disabled) {
    transform: translateY(-1px);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* 注意事項 */
.form-note {
    margin-top: var(--spacing-xl);
    padding: var(--spacing-lg);
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    font-size: var(--font-size-sm);
}

.note-title {
    margin: 0 0 var(--spacing-sm);
    color: var(--text-primary);
}

.form-note ul {
    list-style: none;
    padding: 0;
    margin: var(--spacing-sm) 0 0;
}

.form-note li {
    position: relative;
    padding-left: var(--spacing-lg);
    margin-bottom: var(--spacing-xs);
    color: var(--text-secondary);
}

.form-note li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6em;
    width: 4px;
    height: 4px;
    background: var(--text-secondary);
    border-radius: 50%;
}

/* ========================================
   連絡方法カード
======================================== */
.contact-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}

.contact-method-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
    padding: var(--spacing-xl);
    text-align: center;
    transition: all var(--transition-base);
}

.contact-method-card:hover {
    border-color: var(--color-primary);
    box-shadow: var(--shadow-md);
    transform: translateY(-4px);
}

.method-icon {
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
    transition: all var(--transition-base);
}

.contact-method-card:hover .method-icon {
    background: var(--color-yellow-light);
    border-color: var(--color-primary);
    color: var(--color-yellow-dark);
}

.method-title {
    font-size: var(--font-size-lg);
    font-weight: var(--font-weight-semibold);
    color: var(--text-primary);
    margin: 0 0 var(--spacing-sm);
}

.method-detail {
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-medium);
    color: var(--text-primary);
    margin: var(--spacing-sm) 0;
}

.method-time {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin: 0;
}

/* ========================================
   関連リンク
======================================== */
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
    border-radius: var(--radius-xl);
    text-decoration: none;
    transition: all var(--transition-base);
}

.related-link-card:hover {
    background: var(--bg-tertiary);
    border-color: var(--color-primary);
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
    transition: all var(--transition-base);
}

.related-link-card:hover .link-card-icon {
    background: var(--color-yellow-light);
    border-color: var(--color-primary);
    color: var(--color-yellow-dark);
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

/* ========================================
   レスポンシブ
======================================== */
@media (max-width: 768px) {
    .page-header {
        padding: var(--spacing-3xl) 0 var(--spacing-2xl);
    }
    
    .page-title {
        font-size: var(--font-size-3xl);
    }
    
    .page-subtitle {
        font-size: var(--font-size-lg);
    }
    
    .form-container {
        padding: var(--spacing-xl);
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .radio-group,
    .checkbox-group-inline {
        flex-direction: column;
        gap: var(--spacing-md);
    }
    
    .btn-submit {
        width: 100%;
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
    
    .form-container {
        padding: var(--spacing-lg);
    }
    
    .success-message-box {
        padding: var(--spacing-2xl) var(--spacing-lg);
    }
    
    .success-icon {
        width: 64px;
        height: 64px;
    }
}

/* ========================================
   アクセシビリティ
======================================== */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* フォーカス表示 */
.form-control:focus,
.btn-submit:focus,
.radio-label input:focus,
.checkbox-label input:focus {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 文字数カウント
    const messageField = document.getElementById('message');
    const charCount = document.getElementById('char-count');
    
    if (messageField && charCount) {
        // 初期表示時の文字数カウント
        const initialLength = messageField.value.length;
        charCount.textContent = 500 - initialLength;
        
        messageField.addEventListener('input', function() {
            const remaining = 500 - this.value.length;
            charCount.textContent = remaining;
            
            if (remaining < 50) {
                charCount.style.color = 'var(--color-error)';
                charCount.style.fontWeight = 'var(--font-weight-bold)';
            } else if (remaining < 100) {
                charCount.style.color = 'var(--color-yellow-dark)';
                charCount.style.fontWeight = 'var(--font-weight-semibold)';
            } else {
                charCount.style.color = '';
                charCount.style.fontWeight = '';
            }
        });
    }
    
    // フォーム送信時の処理
    const form = document.getElementById('contactForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('.btn-submit');
            const buttonText = submitButton.querySelector('.btn-text');
            const originalText = buttonText.textContent;
            
            buttonText.textContent = '送信中...';
            submitButton.disabled = true;
            
            // 実際の送信はPHPで処理されるため、ここでは見た目の変更のみ
        });
    }
    
    // スムーススクロール
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // エラーメッセージがある場合、フォームまでスクロール
    const errorBox = document.querySelector('.error-message-box');
    if (errorBox) {
        setTimeout(() => {
            errorBox.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }, 100);
    }
});
</script>

<?php get_footer(); ?>