<?php
/**
 * Diagnostic Check Script
 * このファイルをWordPressのルートディレクトリにアップロードして、
 * ブラウザから直接アクセスしてください（例: https://yoursite.com/diagnostic-check.php）
 */

// WordPressを読み込む
require_once __DIR__ . '/wp-load.php';

// HTMLヘッダー
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grant Insight - Diagnostic Check</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        .check-item { padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #ddd; }
        .success { background: #d4edda; border-left-color: #28a745; }
        .error { background: #f8d7da; border-left-color: #dc3545; }
        .warning { background: #fff3cd; border-left-color: #ffc107; }
        .info { background: #d1ecf1; border-left-color: #17a2b8; }
        code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: Monaco, Consolas, monospace; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .status { font-weight: bold; }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Grant Insight - Diagnostic Check</h1>
        <p>実行日時: <?php echo date('Y-m-d H:i:s'); ?></p>

        <h2>📁 1. テーマファイルの存在確認</h2>
        <?php
        $theme_dir = get_template_directory();
        $files_to_check = array(
            'single-grant.php' => '補助金詳細ページテンプレート',
            'functions.php' => 'テーマ関数ファイル',
            'assets/css/single-grant.css' => '補助金詳細CSS',
            'assets/js/single-grant.js' => '補助金詳細JavaScript',
            'inc/grant-data-helper.php' => 'データヘルパークラス',
            'template-parts/single/header.php' => 'ヘッダーテンプレート',
            'template-parts/single/ai-summary.php' => 'AI要約テンプレート',
            'template-parts/single/detail-info.php' => '詳細情報テンプレート',
            'template-parts/single/related-columns.php' => '関連コラムテンプレート',
            'template-parts/single/ai-chatbot.php' => 'AIチャットボットテンプレート',
        );

        foreach ($files_to_check as $file => $description) {
            $full_path = $theme_dir . '/' . $file;
            $exists = file_exists($full_path);
            $class = $exists ? 'success' : 'error';
            $status = $exists ? 'OK' : 'NOT FOUND';
            $status_class = $exists ? 'status-ok' : 'status-error';
            
            echo "<div class='check-item $class'>";
            echo "<strong>$description</strong><br>";
            echo "<code>$file</code><br>";
            echo "<span class='status $status_class'>$status</span>";
            if ($exists) {
                $size = filesize($full_path);
                $size_kb = round($size / 1024, 2);
                echo " - サイズ: {$size_kb} KB";
                echo " - 更新日時: " . date('Y-m-d H:i:s', filemtime($full_path));
            }
            echo "</div>";
        }
        ?>

        <h2>🔧 2. 関数の存在確認</h2>
        <?php
        $functions_to_check = array(
            'gi_enqueue_single_grant_assets' => 'アセットエンキュー関数',
            'gi_get_scored_related_grants' => '関連補助金取得関数',
        );

        foreach ($functions_to_check as $function => $description) {
            $exists = function_exists($function);
            $class = $exists ? 'success' : 'error';
            $status = $exists ? 'OK' : 'NOT FOUND';
            $status_class = $exists ? 'status-ok' : 'status-error';
            
            echo "<div class='check-item $class'>";
            echo "<strong>$description</strong><br>";
            echo "<code>$function()</code><br>";
            echo "<span class='status $status_class'>$status</span>";
            echo "</div>";
        }
        ?>

        <h2>📦 3. クラスの存在確認</h2>
        <?php
        $classes_to_check = array(
            'GI_Grant_Data_Helper' => 'データヘルパークラス',
            'GrantCardRenderer' => 'カードレンダリングクラス',
        );

        foreach ($classes_to_check as $class_name => $description) {
            $exists = class_exists($class_name);
            $class = $exists ? 'success' : 'error';
            $status = $exists ? 'OK' : 'NOT FOUND';
            $status_class = $exists ? 'status-ok' : 'status-error';
            
            echo "<div class='check-item $class'>";
            echo "<strong>$description</strong><br>";
            echo "<code>$class_name</code><br>";
            echo "<span class='status $status_class'>$status</span>";
            if ($exists) {
                $methods = get_class_methods($class_name);
                echo "<br>メソッド数: " . count($methods);
            }
            echo "</div>";
        }
        ?>

        <h2>🎨 4. アセット読み込み確認（補助金ページで確認）</h2>
        <?php
        // 最初の補助金投稿を取得
        $grant_query = new WP_Query(array(
            'post_type' => 'grant',
            'posts_per_page' => 1,
            'post_status' => 'publish',
        ));

        if ($grant_query->have_posts()) {
            $grant_query->the_post();
            $grant_id = get_the_ID();
            $grant_url = get_permalink($grant_id);
            echo "<div class='check-item info'>";
            echo "<strong>サンプル補助金ページ</strong><br>";
            echo "<a href='$grant_url' target='_blank'>$grant_url</a><br>";
            echo "このページを開いて、開発者ツール(F12)のNetworkタブで以下を確認してください：<br>";
            echo "<ul>";
            echo "<li><code>single-grant.css</code> が読み込まれているか</li>";
            echo "<li><code>single-grant.js</code> が読み込まれているか</li>";
            echo "<li><code>giSingleGrantSettings</code> 変数が定義されているか（Consoleタブ）</li>";
            echo "</ul>";
            echo "</div>";
            wp_reset_postdata();
        } else {
            echo "<div class='check-item warning'>";
            echo "<strong>警告</strong><br>";
            echo "公開済みの補助金投稿が見つかりません";
            echo "</div>";
        }
        ?>

        <h2>ℹ️ 5. テーマ情報</h2>
        <?php
        $theme = wp_get_theme();
        echo "<div class='check-item info'>";
        echo "<strong>テーマ名:</strong> " . $theme->get('Name') . "<br>";
        echo "<strong>バージョン:</strong> " . $theme->get('Version') . "<br>";
        echo "<strong>テーマディレクトリ:</strong> <code>$theme_dir</code><br>";
        echo "<strong>テーマURL:</strong> <code>" . get_template_directory_uri() . "</code><br>";
        echo "<strong>WordPress バージョン:</strong> " . get_bloginfo('version') . "<br>";
        echo "<strong>PHP バージョン:</strong> " . phpversion() . "<br>";
        echo "</div>";
        ?>

        <h2>⚙️ 6. 定数確認</h2>
        <?php
        $constants = array(
            'ABSPATH' => 'WordPressルートパス',
            'GI_THEME_VERSION' => 'テーマバージョン定数',
            'GI_THEME_PREFIX' => 'テーマプレフィックス',
            'WP_DEBUG' => 'デバッグモード',
        );

        foreach ($constants as $const => $description) {
            $exists = defined($const);
            $class = $exists ? 'success' : 'warning';
            $status = $exists ? 'OK' : 'NOT DEFINED';
            $status_class = $exists ? 'status-ok' : 'status-warning';
            
            echo "<div class='check-item $class'>";
            echo "<strong>$description</strong><br>";
            echo "<code>$const</code><br>";
            echo "<span class='status $status_class'>$status</span>";
            if ($exists) {
                $value = constant($const);
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                echo " - 値: <code>$value</code>";
            }
            echo "</div>";
        }
        ?>

        <h2>🔌 7. フック確認</h2>
        <?php
        global $wp_filter;
        
        echo "<div class='check-item info'>";
        echo "<strong>wp_enqueue_scripts にフックされている gi_enqueue_single_grant_assets</strong><br>";
        if (isset($wp_filter['wp_enqueue_scripts'])) {
            $found = false;
            foreach ($wp_filter['wp_enqueue_scripts']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback) {
                    if (is_array($callback['function'])) {
                        continue;
                    }
                    if ($callback['function'] === 'gi_enqueue_single_grant_assets') {
                        $found = true;
                        echo "<span class='status-ok'>✓ 検出されました</span> - 優先度: $priority";
                        break 2;
                    }
                }
            }
            if (!$found) {
                echo "<span class='status-error'>✗ 検出されませんでした</span>";
            }
        } else {
            echo "<span class='status-error'>✗ wp_enqueue_scripts フックが見つかりません</span>";
        }
        echo "</div>";
        ?>

        <h2>📋 8. エラーログ（最新10件）</h2>
        <?php
        $error_log = ini_get('error_log');
        if ($error_log && file_exists($error_log)) {
            echo "<div class='check-item info'>";
            echo "<strong>エラーログファイル:</strong> <code>$error_log</code><br>";
            $lines = file($error_log);
            $recent_errors = array_slice($lines, -10);
            if (!empty($recent_errors)) {
                echo "<pre>" . htmlspecialchars(implode('', $recent_errors)) . "</pre>";
            } else {
                echo "エラーログは空です";
            }
            echo "</div>";
        } else {
            echo "<div class='check-item warning'>";
            echo "エラーログファイルが見つかりません";
            echo "</div>";
        }
        ?>

        <h2>✅ 診断完了</h2>
        <div class='check-item info'>
            <p>上記の結果を確認して、以下をチェックしてください：</p>
            <ol>
                <li>すべてのファイルが「OK」になっているか</li>
                <li>すべての関数とクラスが「OK」になっているか</li>
                <li>サンプル補助金ページでCSSとJSが読み込まれているか</li>
                <li>エラーログに重大なエラーが記録されていないか</li>
            </ol>
            <p><strong>問題が見つかった場合：</strong></p>
            <ul>
                <li>WordPressの管理画面で「外観 > テーマ」を確認</li>
                <li>FTPでファイルが正しくアップロードされているか確認</li>
                <li>キャッシュプラグインを使用している場合はキャッシュをクリア</li>
                <li>ブラウザのキャッシュもクリア（Ctrl+Shift+R / Cmd+Shift+R）</li>
            </ul>
        </div>
    </div>
</body>
</html>
