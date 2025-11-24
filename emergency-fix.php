<?php
/**
 * Emergency CSS/JS Fix
 * このファイルをブラウザで直接開いて、CSSとJSを強制的にインライン出力
 * 
 * 使い方:
 * 1. このファイルをWordPressルートにアップロード
 * 2. ブラウザで https://yoursite.com/emergency-fix.php?post_id=XXX にアクセス
 *    (XXXは補助金のpost ID)
 * 3. 出力されたHTMLをコピー
 * 4. 問題が解決する場合、CSSとJSファイル自体は正しいことが確認できる
 */

require_once __DIR__ . '/wp-load.php';

// POSTIDを取得
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

if (!$post_id) {
    // POSTIDが指定されていない場合、最初の補助金を表示
    $grant_query = new WP_Query(array(
        'post_type' => 'grant',
        'posts_per_page' => 1,
        'post_status' => 'publish',
    ));
    
    if ($grant_query->have_posts()) {
        $grant_query->the_post();
        $post_id = get_the_ID();
        wp_reset_postdata();
    } else {
        die('No grant posts found. Please specify ?post_id=XXX');
    }
}

// 投稿を取得
$post = get_post($post_id);
if (!$post || $post->post_type !== 'grant') {
    die('Invalid grant post ID');
}

// テーマディレクトリ
$theme_dir = get_template_directory();
$css_file = $theme_dir . '/assets/css/single-grant.css';
$js_file = $theme_dir . '/assets/js/single-grant.js';

// ファイルの存在確認
$css_exists = file_exists($css_file);
$js_exists = file_exists($js_file);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Fix - <?php echo esc_html($post->post_title); ?></title>
    
    <?php if ($css_exists): ?>
    <!-- INLINE CSS (Emergency) -->
    <style>
    <?php echo file_get_contents($css_file); ?>
    </style>
    <?php else: ?>
    <p style="color: red; font-weight: bold;">ERROR: CSS file not found at <?php echo $css_file; ?></p>
    <?php endif; ?>
</head>
<body>
    <div style="position: fixed; top: 0; left: 0; right: 0; background: #ff0000; color: white; padding: 10px; text-align: center; z-index: 9999; font-weight: bold;">
        ⚠️ EMERGENCY MODE - CSS/JS INLINE LOADED
    </div>
    <div style="height: 40px;"></div>
    
    <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 20px; margin: 20px; border-radius: 8px;">
        <h2 style="margin: 0 0 10px 0;">🔧 診断情報</h2>
        <p><strong>Post ID:</strong> <?php echo $post_id; ?></p>
        <p><strong>Post Title:</strong> <?php echo esc_html($post->post_title); ?></p>
        <p><strong>Post Type:</strong> <?php echo $post->post_type; ?></p>
        <p><strong>Theme Directory:</strong> <?php echo $theme_dir; ?></p>
        <p><strong>CSS File:</strong> <?php echo $css_exists ? '✅ EXISTS' : '❌ NOT FOUND'; ?> (<?php echo $css_file; ?>)</p>
        <p><strong>JS File:</strong> <?php echo $js_exists ? '✅ EXISTS' : '❌ NOT FOUND'; ?> (<?php echo $js_file; ?>)</p>
        
        <?php if ($css_exists): ?>
        <p><strong>CSS File Size:</strong> <?php echo round(filesize($css_file) / 1024, 2); ?> KB</p>
        <?php endif; ?>
        
        <?php if ($js_exists): ?>
        <p><strong>JS File Size:</strong> <?php echo round(filesize($js_file) / 1024, 2); ?> KB</p>
        <?php endif; ?>
        
        <hr style="margin: 20px 0;">
        
        <h3>テスト結果の判定:</h3>
        <ul>
            <li>✅ デザインが正しく表示される → CSSファイル自体は正常、エンキュー処理に問題</li>
            <li>❌ デザインが表示されない → CSSファイルの内容に問題</li>
            <li>✅ JavaScriptが動作する → JSファイル自体は正常、エンキュー処理に問題</li>
            <li>❌ JavaScriptが動作しない → JSファイルの内容に問題</li>
        </ul>
        
        <p><strong>正常なページURL:</strong> <a href="<?php echo get_permalink($post_id); ?>" target="_blank"><?php echo get_permalink($post_id); ?></a></p>
    </div>
    
    <?php
    // 実際のsingle-grant.phpの内容を読み込んで実行
    global $post;
    $post = get_post($post_id);
    setup_postdata($post);
    
    // single-grant.phpの内容を取得（ヘッダー/フッター除く）
    $single_grant_file = $theme_dir . '/single-grant.php';
    if (file_exists($single_grant_file)) {
        // PHPファイルの内容を読み取り、get_header()とget_footer()を除外
        $content = file_get_contents($single_grant_file);
        
        // get_header()とget_footer()をコメントアウト
        $content = preg_replace('/get_header\(\);/', '// get_header();', $content);
        $content = preg_replace('/get_footer\(\);/', '// get_footer();', $content);
        
        // 最初のPHPタグを除去
        $content = preg_replace('/<\?php/', '', $content, 1);
        
        // 評価して実行
        ob_start();
        eval('?>' . $content);
        $output = ob_get_clean();
        
        echo $output;
    } else {
        echo '<p style="color: red; font-weight: bold;">ERROR: single-grant.php not found</p>';
    }
    
    wp_reset_postdata();
    ?>
    
    <?php if ($js_exists): ?>
    <!-- INLINE JS (Emergency) -->
    <script src="<?php echo includes_url('js/jquery/jquery.min.js'); ?>"></script>
    <script>
    var giSingleGrantSettings = {
        postId: <?php echo $post_id; ?>,
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('gi_single_grant_nonce'); ?>',
        restUrl: '<?php echo rest_url(); ?>',
        restNonce: '<?php echo wp_create_nonce('wp_rest'); ?>'
    };
    </script>
    <script>
    <?php echo file_get_contents($js_file); ?>
    </script>
    <?php else: ?>
    <script>
    console.error('JS file not found at <?php echo $js_file; ?>');
    </script>
    <?php endif; ?>
</body>
</html>
<?php
