<?php
/**
 * Asset Debug Script
 * アセットファイルのパスとURLを診断
 */
require_once __DIR__ . '/wp-load.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== Asset Debug Information ===\n\n";

// テーマ情報
echo "Theme Directory: " . get_template_directory() . "\n";
echo "Theme Directory URI: " . get_template_directory_uri() . "\n";
echo "Theme Name: " . wp_get_theme()->get('Name') . "\n";
echo "Theme Version: " . (defined('GI_THEME_VERSION') ? GI_THEME_VERSION : 'NOT DEFINED') . "\n\n";

// CSSファイルパス
$css_path = get_template_directory() . '/assets/css/single-grant.css';
$css_url = get_template_directory_uri() . '/assets/css/single-grant.css';

echo "--- CSS File ---\n";
echo "Path: $css_path\n";
echo "Exists: " . (file_exists($css_path) ? 'YES' : 'NO') . "\n";
if (file_exists($css_path)) {
    echo "Size: " . round(filesize($css_path) / 1024, 2) . " KB\n";
    echo "Modified: " . date('Y-m-d H:i:s', filemtime($css_path)) . "\n";
    
    // 最初の100文字を読む
    $content = file_get_contents($css_path, false, null, 0, 100);
    echo "First 100 chars: " . substr($content, 0, 100) . "...\n";
}
echo "URL: $css_url\n";
echo "URL Version: $css_url?ver=" . (defined('GI_THEME_VERSION') ? GI_THEME_VERSION : '') . "\n\n";

// JSファイルパス
$js_path = get_template_directory() . '/assets/js/single-grant.js';
$js_url = get_template_directory_uri() . '/assets/js/single-grant.js';

echo "--- JS File ---\n";
echo "Path: $js_path\n";
echo "Exists: " . (file_exists($js_path) ? 'YES' : 'NO') . "\n";
if (file_exists($js_path)) {
    echo "Size: " . round(filesize($js_path) / 1024, 2) . " KB\n";
    echo "Modified: " . date('Y-m-d H:i:s', filemtime($js_path)) . "\n";
    
    // 最初の100文字を読む
    $content = file_get_contents($js_path, false, null, 0, 100);
    echo "First 100 chars: " . substr($content, 0, 100) . "...\n";
}
echo "URL: $js_url\n";
echo "URL Version: $js_url?ver=" . (defined('GI_THEME_VERSION') ? GI_THEME_VERSION : '') . "\n\n";

// 関数とクラスの確認
echo "--- Functions & Classes ---\n";
echo "gi_enqueue_single_grant_assets: " . (function_exists('gi_enqueue_single_grant_assets') ? 'EXISTS' : 'NOT FOUND') . "\n";
echo "GI_Grant_Data_Helper: " . (class_exists('GI_Grant_Data_Helper') ? 'EXISTS' : 'NOT FOUND') . "\n";
echo "GrantCardRenderer: " . (class_exists('GrantCardRenderer') ? 'EXISTS' : 'NOT FOUND') . "\n\n";

// アクションフック確認
echo "--- Action Hooks ---\n";
global $wp_filter;
if (isset($wp_filter['wp_enqueue_scripts'])) {
    echo "wp_enqueue_scripts hook registered: YES\n";
    foreach ($wp_filter['wp_enqueue_scripts']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            if (is_string($callback['function']) && $callback['function'] === 'gi_enqueue_single_grant_assets') {
                echo "gi_enqueue_single_grant_assets found at priority: $priority\n";
            }
        }
    }
} else {
    echo "wp_enqueue_scripts hook: NOT FOUND\n";
}
echo "\n";

// サンプル補助金ページ
$grant_query = new WP_Query(array(
    'post_type' => 'grant',
    'posts_per_page' => 1,
    'post_status' => 'publish',
));

if ($grant_query->have_posts()) {
    $grant_query->the_post();
    echo "--- Sample Grant Page ---\n";
    echo "Title: " . get_the_title() . "\n";
    echo "URL: " . get_permalink() . "\n";
    echo "ID: " . get_the_ID() . "\n";
    echo "is_singular('grant'): " . (is_singular('grant') ? 'YES' : 'NO') . "\n\n";
    wp_reset_postdata();
}

// HTMLテスト出力
echo "--- HTML Test Output ---\n";
echo "To test if CSS is loaded, visit a grant page and:\n";
echo "1. Open browser DevTools (F12)\n";
echo "2. Go to Network tab\n";
echo "3. Filter by 'single-grant'\n";
echo "4. Refresh page (Ctrl+R)\n";
echo "5. Check if single-grant.css and single-grant.js are loaded (Status 200)\n\n";

echo "Expected URLs:\n";
echo "CSS: $css_url?ver=" . (defined('GI_THEME_VERSION') ? GI_THEME_VERSION : '') . "\n";
echo "JS: $js_url?ver=" . (defined('GI_THEME_VERSION') ? GI_THEME_VERSION : '') . "\n\n";

// 直接URLにアクセスしてテスト
echo "--- Direct URL Test ---\n";
echo "Test these URLs directly in your browser:\n";
echo "CSS: $css_url\n";
echo "JS: $js_url\n\n";

echo "If you get a 404 error, the files are not accessible.\n";
echo "If you see the file content, the files are accessible but not being enqueued.\n\n";

// wp_enqueue_scripts を手動で実行してテスト
echo "--- Manual Enqueue Test ---\n";
if (function_exists('gi_enqueue_single_grant_assets')) {
    // 補助金ページをシミュレート
    global $post;
    if ($grant_query->have_posts()) {
        $grant_query->the_post();
        $post = get_post();
        
        // グローバル変数を設定
        $GLOBALS['post'] = $post;
        
        // クエリフラグを設定
        set_query_var('post_type', 'grant');
        
        echo "Simulating grant page context...\n";
        echo "Post ID: " . get_the_ID() . "\n";
        echo "Post Type: " . get_post_type() . "\n";
        
        // 関数を呼び出し
        ob_start();
        gi_enqueue_single_grant_assets();
        ob_end_clean();
        
        // エンキューされているか確認
        $css_enqueued = wp_style_is('gi-single-grant', 'enqueued') || wp_style_is('gi-single-grant', 'registered');
        $js_enqueued = wp_script_is('gi-single-grant', 'enqueued') || wp_script_is('gi-single-grant', 'registered');
        
        echo "CSS enqueued: " . ($css_enqueued ? 'YES' : 'NO') . "\n";
        echo "JS enqueued: " . ($js_enqueued ? 'YES' : 'NO') . "\n";
        
        // 登録されているスタイルとスクリプトを確認
        global $wp_styles, $wp_scripts;
        
        if (isset($wp_styles->registered['gi-single-grant'])) {
            $style = $wp_styles->registered['gi-single-grant'];
            echo "CSS registered:\n";
            echo "  Handle: " . $style->handle . "\n";
            echo "  Src: " . $style->src . "\n";
            echo "  Ver: " . $style->ver . "\n";
        }
        
        if (isset($wp_scripts->registered['gi-single-grant'])) {
            $script = $wp_scripts->registered['gi-single-grant'];
            echo "JS registered:\n";
            echo "  Handle: " . $script->handle . "\n";
            echo "  Src: " . $script->src . "\n";
            echo "  Ver: " . $script->ver . "\n";
        }
        
        wp_reset_postdata();
    }
} else {
    echo "gi_enqueue_single_grant_assets function NOT FOUND\n";
}

echo "\n=== End Debug ===\n";
