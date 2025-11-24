<?php
/**
 * Template Name: Disclaimer Page (免責事項)
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// テンプレートファイルを直接読み込む
$template_file = get_template_directory() . '/pages/templates/page-disclaimer.php';

if (file_exists($template_file)) {
    include $template_file;
} else {
    // フォールバック
    get_header();
    ?>
    <div style="padding: 100px 20px; text-align: center;">
        <h1>免責事項ページ</h1>
        <p>テンプレートファイルが見つかりません。</p>
    </div>
    <?php
    get_footer();
}
?>
