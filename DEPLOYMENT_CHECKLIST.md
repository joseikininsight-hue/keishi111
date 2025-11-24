# 🚀 デプロイメントチェックリスト

## ✅ 修正完了事項

### 1. CSS/JSファイルの修正
- ✅ `assets/css/single-grant.css` を正しく抽出（45KB, 2,061行）
- ✅ `assets/js/single-grant.js` を正しく抽出（16KB, 378行）
- ✅ 以前のファイルは破損していた（最初の行が `}` だけ）
- ✅ 元の `single-grant.php`（コミット f3aab8e）から完全なコードを再抽出

### 2. 修正内容の詳細

#### CSS修正
```bash
# 元のファイル（破損）
行数: 3,108行
開始: } （エラー）
サイズ: 69KB（重複・不要なコード含む）

# 修正後のファイル
行数: 2,061行
開始: /* PERFECT DESIGN SYSTEM v24.2 - MOBILE NAV FIXED */
サイズ: 45KB（クリーンで完全なCSS）
```

#### JavaScript修正
```bash
# 元のファイル（破損）
行数: 427行
状態: 不完全

# 修正後のファイル
行数: 378行
状態: 完全なJavaScript（全機能含む）
```

## 🔧 本番環境でのデプロイ手順

### ステップ 1: ファイルの更新確認
GitHubから最新のコードをプル：
```bash
cd /path/to/your/wordpress/wp-content/themes/your-theme
git pull origin main
```

### ステップ 2: ファイルの存在確認
以下のファイルが存在し、正しいサイズであることを確認：
```bash
ls -lh assets/css/single-grant.css  # 45KB前後
ls -lh assets/js/single-grant.js     # 16KB前後
```

### ステップ 3: ファイルの内容確認
```bash
# CSSの先頭を確認（コメントから始まるはず）
head -5 assets/css/single-grant.css

# 出力例:
# /* ===============================================
#    PERFECT DESIGN SYSTEM v24.2 - MOBILE NAV FIXED
#    =============================================== */
#
# html, body {

# JavaScriptの先頭を確認
head -5 assets/js/single-grant.js

# 出力例:
# document.addEventListener('DOMContentLoaded', function() {
#     'use strict';
#     
#     // ===============================================
#     // カルーセル機能
```

### ステップ 4: キャッシュのクリア

#### WordPressキャッシュ
```
管理画面 > プラグイン > キャッシュプラグイン > キャッシュをクリア
```

使用している可能性があるプラグイン：
- WP Super Cache
- W3 Total Cache
- WP Rocket
- LiteSpeed Cache
- Autoptimize

#### サーバーキャッシュ
```bash
# OPcacheをクリア（PHPキャッシュ）
# 方法1: WordPress管理画面で任意のファイルを保存
# 方法2: サーバー再起動
```

#### CDNキャッシュ（使用している場合）
```
Cloudflare: キャッシュをパージ
他のCDN: 該当するコントロールパネルでパージ
```

#### ブラウザキャッシュ
```
Chrome/Edge: Ctrl+Shift+R (Windows) / Cmd+Shift+R (Mac)
Firefox: Ctrl+F5 (Windows) / Cmd+Shift+R (Mac)
Safari: Cmd+Option+R
```

### ステップ 5: 動作確認

#### 5.1 診断スクリプトの実行
```
https://あなたのサイト.com/diagnostic-check.php
```

以下を確認：
- ✅ すべてのファイルが「OK」
- ✅ すべてのクラスと関数が「OK」
- ✅ `gi_enqueue_single_grant_assets` がフックされている

#### 5.2 ブラウザ開発者ツールで確認
補助金詳細ページを開いて F12 キーを押す：

**Networkタブ**:
1. ページをリロード（Ctrl+R）
2. フィルターに「single-grant」と入力
3. 以下が表示されることを確認：
   ```
   single-grant.css  Status: 200  Size: 45KB
   single-grant.js   Status: 200  Size: 16KB
   ```

**Consoleタブ**:
1. エラーがないことを確認
2. 以下のコマンドを実行：
   ```javascript
   console.log(giSingleGrantSettings);
   ```
3. オブジェクトが表示されることを確認：
   ```javascript
   {
     postId: 123,
     ajaxUrl: "https://yoursite.com/wp-admin/admin-ajax.php",
     nonce: "...",
     restUrl: "https://yoursite.com/wp-json/",
     restNonce: "..."
   }
   ```

**Elementsタブ**:
1. `<head>` セクションを確認
2. 以下のリンクタグが存在することを確認：
   ```html
   <link rel='stylesheet' id='gi-single-grant-css' 
         href='.../assets/css/single-grant.css?ver=11.0.1' />
   ```
3. `</body>` の前を確認
4. 以下のスクリプトタグが存在することを確認：
   ```html
   <script id='gi-single-grant-js' 
           src='.../assets/js/single-grant.js?ver=11.0.1'></script>
   ```

#### 5.3 視覚的確認
補助金詳細ページで以下を確認：

✅ **デザインが適用されている**:
- ヘッダーセクションが正しくスタイリングされている
- カードレイアウトが適切に表示されている
- カラースキーム（黄色・黒・白）が適用されている
- レスポンシブデザインが機能している

✅ **JavaScript機能が動作している**:
- カルーセルの左右ボタンが機能する
- お気に入りボタンが動作する（クリックでハートが変化）
- 目次がクリック可能で、スムーススクロールする
- AIチャットボタンが表示される
- 共有ボタンが機能する

### ステップ 6: トラブルシューティング

#### 問題A: CSSが適用されない
**原因チェック**:
```bash
# ファイルが存在するか
ls -lh assets/css/single-grant.css

# ファイルの先頭が正しいか（}から始まらない）
head -1 assets/css/single-grant.css

# パーミッションが正しいか
chmod 644 assets/css/single-grant.css
```

**解決方法**:
1. ブラウザのNetworkタブで404エラーがないか確認
2. キャッシュを再度クリア
3. テーマディレクトリのパスが正しいか確認
4. `functions.php` の `gi_enqueue_single_grant_assets` 関数を確認

#### 問題B: JavaScriptが動作しない
**原因チェック**:
```bash
# ファイルが存在するか
ls -lh assets/js/single-grant.js

# ファイルの内容が正しいか
head -1 assets/js/single-grant.js
# 出力: document.addEventListener('DOMContentLoaded', function() {
```

**Console確認**:
```javascript
// jQueryが読み込まれているか
console.log(typeof jQuery);  // "function" が表示されるべき

// スクリプトが実行されているか
console.log('Grant Single Page v24.2 Initialized');
// メッセージが表示されない場合、スクリプトが実行されていない
```

#### 問題C: 404エラー（ファイルが見つからない）
**確認事項**:
1. テーマディレクトリが正しいか
   ```php
   // functions.phpで確認
   echo get_template_directory();
   echo get_template_directory_uri();
   ```

2. ファイルパスが正しいか
   ```
   正: /wp-content/themes/your-theme/assets/css/single-grant.css
   誤: /assets/css/single-grant.css
   ```

3. .htaccess の設定を確認
   ```apache
   # リライトルールでアセットファイルが除外されているか確認
   RewriteRule ^assets/ - [L]
   ```

#### 問題D: キャッシュが原因
**完全なキャッシュクリア**:
```bash
# 1. WordPressオブジェクトキャッシュ
wp cache flush

# 2. OPcache（PHP）
# wp-admin/options.phpで WP_DEBUG を一時的にtrueに設定

# 3. ブラウザ
# Ctrl+Shift+Delete でキャッシュを完全削除

# 4. CDN
# Cloudflareなどのダッシュボードで「すべてをパージ」

# 5. サーバー再起動（最終手段）
sudo systemctl restart apache2  # または nginx
```

### ステップ 7: 最終確認チェックリスト

- [ ] 診断スクリプトで全項目が「OK」
- [ ] CSS（45KB）が正しく読み込まれている
- [ ] JavaScript（16KB）が正しく読み込まれている
- [ ] デザインが完全に適用されている
- [ ] お気に入り機能が動作する
- [ ] カルーセルが動作する
- [ ] AIチャットボタンが表示される
- [ ] 共有ボタンが動作する
- [ ] レスポンシブデザインが機能する（スマホ・タブレット・PC）
- [ ] ブラウザコンソールにエラーがない
- [ ] ページ読み込み速度が正常

## 📞 サポート情報

### 問題が解決しない場合
1. `diagnostic-check.php` の結果をスクリーンショット
2. ブラウザ開発者ツールのNetworkタブをスクリーンショット
3. ブラウザ開発者ツールのConsoleタブをスクリーンショット
4. 補助金詳細ページのスクリーンショット

これらを提供してください。

### よくある質問

**Q: なぜ以前のCSSファイルは破損していたのか？**
A: リファクタリング時の抽出プロセスで、開始位置が1行ずれていました。`<style>` タグの次の行から抽出すべきところ、`}` の行から抽出してしまいました。

**Q: なぜファイルサイズが小さくなったのか？**
A: 元のファイル（69KB、3,108行）には重複コードや不要なコードが含まれていました。正しく抽出したファイル（45KB、2,061行）はクリーンで最適化されています。

**Q: キャッシュをクリアしてもデザインが適用されない場合は？**
A: 以下を順番に試してください：
1. ブラウザを完全に閉じて再起動
2. シークレットモード/プライベートブラウジングで確認
3. 別のブラウザで確認
4. スマートフォンから確認

**Q: 一部のページでは正常だが、他のページで問題がある場合は？**
A: 特定のキャッシュプラグインがページごとにキャッシュしている可能性があります。個別にキャッシュをクリアしてください。

## 🎉 デプロイ成功の確認

すべてのチェック項目が完了したら、以下のメッセージが表示されるはずです：

```
✅ Grant Insight Perfect v11.0.1
✅ CSS: PERFECT DESIGN SYSTEM v24.2 適用済み
✅ JavaScript: 全機能動作中
✅ AIアシスタント: 準備完了
```

おめでとうございます！🎊
