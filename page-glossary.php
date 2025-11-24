<?php
/**
 * Template Name: Glossary Page (補助金・助成金用語集)
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// テンプレートファイルを直接読み込む
$template_file = get_template_directory() . '/pages/templates/page-glossary.php';

if (file_exists($template_file)) {
    include $template_file;
} else {
    // フォールバック: テンプレートが見つからない場合
    get_header();
    ?>
    <div style="padding: 100px 20px; text-align: center;">
        <h1>用語集ページ</h1>
        <p>テンプレートファイルが見つかりません。</p>
        <p>ファイルパス: <?php echo esc_html($template_file); ?></p>
    </div>
    <?php
    get_footer();
}
?>
