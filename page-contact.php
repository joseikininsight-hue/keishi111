<?php
/**
 * Template Name: Contact Page (お問い合わせ)
 * 
 * 補助金・助成金情報サイト - お問い合わせページ
 * Grant & Subsidy Information Site - Contact Page
 * 
 * @package Grant_Insight_Perfect
 * @version 5.0-fixed-form-processing
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

get_header();

// メール送信処理
$form_submitted = false;
$form_success = false;
$form_errors = array();

// POSTデータ処理は admin_post フックで処理されるため、ここでは処理しない
// GET パラメータのみ処理

// GET パラメータで成功メッセージを表示
if (isset($_GET['contact_sent']) && $_GET['contact_sent'] == '1') {
    $form_success = true;
}

// GET パラメータでエラーメッセージを表示
if (isset($_GET['contact_error']) && $_GET['contact_error'] == '1' && isset($_GET['error_msg'])) {
    $form_errors = explode('|', urldecode($_GET['error_msg']));
}

// pages/templates内の実際のテンプレートファイルを読み込み
$template_path = get_template_directory() . '/pages/templates/page-contact.php';

if (file_exists($template_path)) {
    include $template_path;
} else {
    // フォールバック処理は削除し、直接レンダリング
    ?>
    <div class="container">
        <h1>Contact Page</h1>
        <p>Contact page template not found. Please check pages/templates/page-contact.php</p>
    </div>
    <?php
    get_footer();
}
?>