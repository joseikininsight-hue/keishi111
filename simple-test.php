<?php
/**
 * Simple Test - 最小限のテスト
 */
require_once __DIR__ . '/wp-load.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simple Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { border-left: 4px solid #4caf50; }
        .error { border-left: 4px solid #f44336; }
        .warning { border-left: 4px solid #ff9800; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f0f0f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Simple Diagnostic Test</h1>
    
    <div class="box <?php echo function_exists('gi_enqueue_single_grant_assets') ? 'success' : 'error'; ?>">
        <h2>1. 関数の存在確認</h2>
        <p><strong>gi_enqueue_single_grant_assets:</strong> 
        <?php echo function_exists('gi_enqueue_single_grant_assets') ? '✅ EXISTS' : '❌ NOT FOUND'; ?></p>
        
        <?php if (!function_exists('gi_enqueue_single_grant_assets')): ?>
        <p style="color: #f44336; font-weight: bold;">
            ⚠️ 関数が見つかりません！functions.phpが正しく読み込まれていないか、定義前にエラーが発生しています。
        </p>
        <?php endif; ?>
    </div>
    
    <div class="box <?php echo file_exists(get_template_directory() . '/assets/css/single-grant.css') ? 'success' : 'error'; ?>">
        <h2>2. CSSファイルの確認</h2>
        <?php
        $css_path = get_template_directory() . '/assets/css/single-grant.css';
        $css_url = get_template_directory_uri() . '/assets/css/single-grant.css';
        ?>
        <p><strong>Path:</strong> <code><?php echo $css_path; ?></code></p>
        <p><strong>Exists:</strong> <?php echo file_exists($css_path) ? '✅ YES' : '❌ NO'; ?></p>
        <?php if (file_exists($css_path)): ?>
        <p><strong>Size:</strong> <?php echo round(filesize($css_path) / 1024, 2); ?> KB</p>
        <p><strong>URL:</strong> <a href="<?php echo $css_url; ?>" target="_blank"><?php echo $css_url; ?></a></p>
        <p><strong>First line:</strong> <code><?php echo htmlspecialchars(substr(file_get_contents($css_path), 0, 100)); ?>...</code></p>
        <?php endif; ?>
    </div>
    
    <div class="box <?php echo file_exists(get_template_directory() . '/assets/js/single-grant.js') ? 'success' : 'error'; ?>">
        <h2>3. JavaScriptファイルの確認</h2>
        <?php
        $js_path = get_template_directory() . '/assets/js/single-grant.js';
        $js_url = get_template_directory_uri() . '/assets/js/single-grant.js';
        ?>
        <p><strong>Path:</strong> <code><?php echo $js_path; ?></code></p>
        <p><strong>Exists:</strong> <?php echo file_exists($js_path) ? '✅ YES' : '❌ NO'; ?></p>
        <?php if (file_exists($js_path)): ?>
        <p><strong>Size:</strong> <?php echo round(filesize($js_path) / 1024, 2); ?> KB</p>
        <p><strong>URL:</strong> <a href="<?php echo $js_url; ?>" target="_blank"><?php echo $js_url; ?></a></p>
        <p><strong>First line:</strong> <code><?php echo htmlspecialchars(substr(file_get_contents($js_path), 0, 100)); ?>...</code></p>
        <?php endif; ?>
    </div>
    
    <div class="box">
        <h2>4. テーマ情報</h2>
        <p><strong>Theme Name:</strong> <?php echo wp_get_theme()->get('Name'); ?></p>
        <p><strong>Theme Directory:</strong> <code><?php echo get_template_directory(); ?></code></p>
        <p><strong>Theme URL:</strong> <code><?php echo get_template_directory_uri(); ?></code></p>
        <p><strong>Theme Version:</strong> <?php echo defined('GI_THEME_VERSION') ? GI_THEME_VERSION : 'NOT DEFINED'; ?></p>
    </div>
    
    <div class="box">
        <h2>5. 補助金ページのテスト</h2>
        <?php
        $grant_query = new WP_Query(array(
            'post_type' => 'grant',
            'posts_per_page' => 1,
            'post_status' => 'publish',
        ));
        
        if ($grant_query->have_posts()):
            $grant_query->the_post();
            $grant_url = get_permalink();
        ?>
        <p><strong>Sample Grant:</strong> <a href="<?php echo $grant_url; ?>" target="_blank"><?php the_title(); ?></a></p>
        <p><strong>Post ID:</strong> <?php the_ID(); ?></p>
        
        <h3>6. フックのテスト</h3>
        <?php
        // フックをシミュレート
        global $wp_filter;
        
        if (isset($wp_filter['wp_enqueue_scripts'])):
            echo '<p><strong>wp_enqueue_scripts hook:</strong> ✅ Registered</p>';
            
            $found = false;
            foreach ($wp_filter['wp_enqueue_scripts']->callbacks as $priority => $callbacks):
                foreach ($callbacks as $callback):
                    if (is_string($callback['function']) && $callback['function'] === 'gi_enqueue_single_grant_assets'):
                        $found = true;
                        echo '<p><strong>gi_enqueue_single_grant_assets:</strong> ✅ Found at priority ' . $priority . '</p>';
                    endif;
                endforeach;
            endforeach;
            
            if (!$found):
                echo '<p style="color: #f44336;"><strong>gi_enqueue_single_grant_assets:</strong> ❌ Not found in hook</p>';
            endif;
        else:
            echo '<p style="color: #f44336;"><strong>wp_enqueue_scripts hook:</strong> ❌ Not registered</p>';
        endif;
        
        wp_reset_postdata();
        else:
            echo '<p style="color: #ff9800;">No published grant posts found.</p>';
        endif;
        ?>
    </div>
    
    <div class="box warning">
        <h2>7. 次のステップ</h2>
        <ol>
            <li>上記の項目をすべて確認</li>
            <li>❌ エラーがある項目を特定</li>
            <li>補助金ページを開いてF12キーを押す</li>
            <li>Networkタブで<code>single-grant.css</code>と<code>single-grant.js</code>を検索</li>
            <li>ファイルが読み込まれているか確認（Status 200）</li>
        </ol>
        
        <h3>問題の切り分け:</h3>
        <ul>
            <li>✅ すべて正常 → ブラウザキャッシュの問題</li>
            <li>❌ 関数が見つからない → functions.phpにエラーがある</li>
            <li>❌ ファイルが存在しない → デプロイ失敗</li>
            <li>✅ ファイルは存在するが読み込まれない → フック登録の問題</li>
        </ul>
    </div>
    
    <div class="box">
        <h2>8. 直接URLテスト</h2>
        <p>以下のURLにブラウザで直接アクセスして、ファイルが表示されるか確認:</p>
        <ul>
            <li><a href="<?php echo get_template_directory_uri(); ?>/assets/css/single-grant.css" target="_blank">CSS File</a></li>
            <li><a href="<?php echo get_template_directory_uri(); ?>/assets/js/single-grant.js" target="_blank">JS File</a></li>
        </ul>
        <p>404エラーが出る場合 → ファイルがサーバーに存在しない<br>
        ファイルが表示される場合 → エンキュー処理に問題</p>
    </div>
</body>
</html>
