<?php
/**
 * Template Part: Front Page Search Section - Personal Focus & UX Improved v14.0
 * フロントページ検索セクション - 個人向け強化・市町村UI改善版
 * * @package Grant_Insight_Perfect
 * @version 14.0.0
 */

if (!defined('ABSPATH')) exit;

// ==========================================================================
// 1. データ取得
// ==========================================================================

// カテゴリー
$all_categories = get_terms(['taxonomy' => 'grant_category', 'hide_empty' => false, 'orderby' => 'count', 'order' => 'DESC']);
$popular_categories = array_slice($all_categories, 0, 10);
$all_categories_limited = array_slice($all_categories, 0, 100);

// 都道府県
$prefectures = function_exists('gi_get_all_prefectures') ? gi_get_all_prefectures() : [];

// タグ
$popular_tags = get_terms(['taxonomy' => 'grant_tag', 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 10]);
$all_tags = get_terms(['taxonomy' => 'grant_tag', 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 100]);

// カテゴリグループ
$category_groups = [
    ['name' => 'TYPES', 'ja' => '補助金の種類', 'icon' => 'briefcase', 'categories' => array_slice($all_categories, 0, 8)],
    ['name' => 'FIELDS', 'ja' => '対象分野', 'icon' => 'industry', 'categories' => array_slice($all_categories, 8, 8)],
    ['name' => 'SUPPORTS', 'ja' => '支援内容', 'icon' => 'hands-helping', 'categories' => array_slice($all_categories, 16, 8)]
];

// 地域データ
$regions_data = [
    ['name' => 'HOKKAIDO / TOHOKU', 'ja' => '北海道・東北', 'icon' => 'map', 'prefectures' => ['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県']],
    ['name' => 'HOKURIKU / KOSHINETSU', 'ja' => '北陸・甲信越', 'icon' => 'mountain', 'prefectures' => ['新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県']],
    ['name' => 'KANTO', 'ja' => '関東', 'icon' => 'city', 'prefectures' => ['東京都', '埼玉県', '千葉県', '神奈川県', '茨城県', '栃木県', '群馬県']],
    ['name' => 'TOKAI', 'ja' => '東海', 'icon' => 'building', 'prefectures' => ['愛知県', '岐阜県', '三重県', '静岡県']],
    ['name' => 'KANSAI', 'ja' => '関西', 'icon' => 'landmark', 'prefectures' => ['大阪府', '兵庫県', '京都府', '滋賀県', '奈良県', '和歌山県']],
    ['name' => 'CHUGOKU', 'ja' => '中国', 'icon' => 'water', 'prefectures' => ['鳥取県', '島根県', '岡山県', '広島県', '山口県']],
    ['name' => 'SHIKOKU', 'ja' => '四国', 'icon' => 'tree', 'prefectures' => ['徳島県', '香川県', '愛媛県', '高知県']],
    ['name' => 'KYUSHU / OKINAWA', 'ja' => '九州・沖縄', 'icon' => 'sun', 'prefectures' => ['福岡県', '佐賀県', '熊本県', '大分県', '宮崎県', '鹿児島県', '長崎県', '沖縄県']]
];

// 統計
$total_grants = wp_count_posts('grant')->publish;

// 構造化データ
$schema_website = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'url' => home_url('/'),
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => home_url('/grants/?search={search_term_string}'),
        'query-input' => 'required name=search_term_string'
    ]
];
?>

<script type="application/ld+json">
<?php echo wp_json_encode($schema_website, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>

<main class="ui-search-wrapper">

    <section class="ui-stats-bar" aria-label="データベース統計">
        <div class="ui-inner ui-stats-flex">
            <div class="ui-stat-item">
                <span class="label">DATABASE:</span>
                <span class="value"><?php echo number_format($total_grants); ?></span>
                <span class="unit">件掲載</span>
            </div>
            <div class="ui-stat-item">
                <span class="label">UPDATE:</span>
                <time class="date" datetime="<?php echo date('Y-m-d'); ?>"><?php echo date('Y.m.d'); ?> 更新</time>
            </div>
        </div>
    </section>

    <section class="ui-section ui-bg-light" role="search" aria-labelledby="main-search-title">
        <div class="ui-inner">
            <header class="ui-header">
                <p class="ui-sub-title">SEARCH GRANTS</p>
                <h2 id="main-search-title" class="ui-main-title">補助金・助成金を検索</h2>
            </header>

            <form id="grant-search-form" action="<?php echo esc_url(home_url('/grants/')); ?>" method="get" class="ui-search-form">
                
                <div class="ui-form-row">
                    <label class="ui-label">受付状況</label>
                    <div class="ui-radio-group">
                        <label class="ui-radio-btn">
                            <input type="radio" name="status" value="open" checked>
                            <span class="ui-radio-text">募集中のみ</span>
                        </label>
                        <label class="ui-radio-btn">
                            <input type="radio" name="status" value="all">
                            <span class="ui-radio-text">すべて表示</span>
                        </label>
                    </div>
                </div>

                <div class="ui-form-grid">
                    <div class="ui-input-group">
                        <label for="category-select" class="ui-label">用途・カテゴリー</label>
                        <div class="ui-select-wrap">
                            <select id="category-select" name="category" class="ui-select">
                                <option value="">選択してください</option>
                                <?php foreach ($all_categories as $cat) : ?>
                                    <option value="<?php echo esc_attr($cat->slug); ?>">
                                        <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="ui-input-group">
                        <label for="prefecture-select" class="ui-label">都道府県</label>
                        <div class="ui-select-wrap">
                            <select id="prefecture-select" name="prefecture" class="ui-select">
                                <option value="">選択してください</option>
                                <?php foreach ($prefectures as $pref) : ?>
                                    <option value="<?php echo esc_attr($pref['slug']); ?>">
                                        <?php echo esc_html($pref['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="ui-input-group" id="municipality-group" style="display: none;">
                        <label for="municipality-select" class="ui-label">市町村</label>
                        <div class="ui-select-wrap">
                            <select id="municipality-select" name="municipality" class="ui-select">
                                <option value="">選択してください</option>
                            </select>
                            <div id="municipality-spinner" class="ui-spinner"></div>
                        </div>
                    </div>
                </div>

                <div class="ui-form-row">
                    <label for="keyword-input" class="ui-label">フリーワード</label>
                    <input type="search" id="keyword-input" name="search" class="ui-input" placeholder="例：IT導入、設備投資、創業支援..." autocomplete="off">
                </div>

                <div class="ui-actions">
                    <button type="button" id="reset-btn" class="ui-btn ui-btn-outline">
                        <i class="fas fa-undo"></i> 条件クリア
                    </button>
                    <button type="submit" id="search-btn" class="ui-btn ui-btn-solid">
                        <i class="fas fa-search"></i> この条件で検索
                    </button>
                </div>

                <div class="ui-sub-links">
                    <a href="<?php echo esc_url(home_url('/grants/')); ?>"><i class="fas fa-list"></i> 詳細検索</a>
                    <a href="<?php echo esc_url(home_url('/saved-searches/')); ?>"><i class="fas fa-bookmark"></i> 保存条件</a>
                    <a href="<?php echo esc_url(home_url('/history/')); ?>"><i class="fas fa-history"></i> 閲覧履歴</a>
                </div>
            </form>
        </div>
    </section>

    <section class="ui-section" aria-labelledby="target-title">
        <div class="ui-inner">
            <header class="ui-header">
                <p class="ui-sub-title">SEARCH BY TARGET</p>
                <h2 id="target-title" class="ui-main-title">対象者から探す</h2>
            </header>

            <div class="ui-target-grid">
                <a href="<?php echo esc_url(home_url('/grants/?grant_tag=個人向け')); ?>" class="ui-target-card featured">
                    <div class="ui-target-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="ui-target-content">
                        <h3>個人事業主・フリーランス</h3>
                        <p>個人でも使える補助金・持続化給付金など</p>
                    </div>
                    <div class="ui-target-arrow"><i class="fas fa-arrow-right"></i></div>
                </a>

                <a href="<?php echo esc_url(home_url('/grants/?grant_tag=中小企業')); ?>" class="ui-target-card">
                    <div class="ui-target-icon"><i class="fas fa-building"></i></div>
                    <div class="ui-target-content">
                        <h3>中小企業</h3>
                        <p>ものづくり・事業再構築・IT導入など</p>
                    </div>
                    <div class="ui-target-arrow"><i class="fas fa-arrow-right"></i></div>
                </a>

                <a href="<?php echo esc_url(home_url('/grants/?grant_tag=創業')); ?>" class="ui-target-card">
                    <div class="ui-target-icon"><i class="fas fa-rocket"></i></div>
                    <div class="ui-target-content">
                        <h3>創業・スタートアップ</h3>
                        <p>起業資金・創業融資・オフィス賃料など</p>
                    </div>
                    <div class="ui-target-arrow"><i class="fas fa-arrow-right"></i></div>
                </a>
            </div>
        </div>
    </section>

    <section class="ui-section ui-bg-light" aria-labelledby="purpose-title">
        <div class="ui-inner">
            <header class="ui-header">
                <p class="ui-sub-title">BROWSE BY PURPOSE</p>
                <h2 id="purpose-title" class="ui-main-title">用途・目的から探す</h2>
            </header>

            <div class="ui-grid-3">
                <?php foreach ($category_groups as $group) : ?>
                <article class="ui-card">
                    <div class="ui-card-header">
                        <i class="fas fa-<?php echo esc_attr($group['icon']); ?> icon"></i>
                        <div class="ui-card-titles">
                            <h3 class="title"><?php echo esc_html($group['ja']); ?></h3>
                            <span class="sub"><?php echo esc_html($group['name']); ?></span>
                        </div>
                    </div>
                    <div class="ui-link-list">
                        <?php foreach ($group['categories'] as $cat) : ?>
                        <a href="<?php echo get_term_link($cat); ?>" class="ui-list-item">
                            <span class="name"><?php echo esc_html($cat->name); ?></span>
                            <span class="count"><?php echo $cat->count; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="ui-section" aria-labelledby="cat-title">
        <div class="ui-inner">
            <header class="ui-header">
                <p class="ui-sub-title">POPULAR CATEGORIES</p>
                <h2 id="cat-title" class="ui-main-title">人気カテゴリから探す</h2>
            </header>

            <div class="ui-filter-box">
                <input type="text" id="cat-filter-input" class="ui-filter-input" placeholder="カテゴリをキーワードで絞り込む...">
            </div>

            <div class="ui-pill-grid" id="popular-categories-container">
                <?php foreach ($popular_categories as $cat) : ?>
                <a href="<?php echo get_term_link($cat); ?>" class="ui-pill ui-pill-cat" data-name="<?php echo esc_attr($cat->name); ?>">
                    <i class="fas fa-folder"></i>
                    <span class="text"><?php echo esc_html($cat->name); ?></span>
                    <span class="count"><?php echo $cat->count; ?></span>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($all_categories_limited) && count($all_categories_limited) > 10) : ?>
            <div class="ui-collapse-box">
                <button type="button" id="all-categories-toggle" class="ui-collapse-btn" aria-expanded="false">
                    <span>すべてのカテゴリを見る</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="all-categories-content" class="ui-collapse-content" hidden>
                    <div class="ui-pill-grid" id="all-categories-container">
                        <?php foreach ($all_categories_limited as $cat) : ?>
                        <a href="<?php echo get_term_link($cat); ?>" class="ui-pill ui-pill-cat" data-name="<?php echo esc_attr($cat->name); ?>">
                            <span class="text"><?php echo esc_html($cat->name); ?></span>
                            <span class="count"><?php echo $cat->count; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <p id="no-categories-msg" class="ui-no-data" style="display:none;">一致するカテゴリがありません</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="ui-section ui-bg-light" aria-labelledby="region-title">
        <div class="ui-inner">
            <header class="ui-header">
                <p class="ui-sub-title">BROWSE BY REGION</p>
                <h2 id="region-title" class="ui-main-title">都道府県から探す</h2>
            </header>

            <div class="ui-grid-4">
                <?php foreach ($regions_data as $region) : ?>
                <article class="ui-region-card">
                    <div class="ui-region-head">
                        <i class="fas fa-<?php echo esc_attr($region['icon']); ?>"></i>
                        <h3><?php echo esc_html($region['ja']); ?></h3>
                    </div>
                    <div class="ui-pref-links">
                        <?php foreach ($region['prefectures'] as $pref_name) : 
                            $pref_slug = '';
                            foreach($prefectures as $p) { if($p['name'] === $pref_name) { $pref_slug = $p['slug']; break; } }
                            if($pref_slug):
                        ?>
                        <a href="<?php echo get_term_link($pref_slug, 'grant_prefecture'); ?>" class="ui-pref-link">
                            <?php echo esc_html($pref_name); ?>
                        </a>
                        <?php endif; endforeach; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="ui-section" aria-labelledby="muni-title">
        <div class="ui-inner">
            <header class="ui-header">
                <p class="ui-sub-title">MUNICIPALITY SEARCH</p>
                <h2 id="muni-title" class="ui-main-title">市町村から探す</h2>
            </header>

            <div class="ui-muni-interface">
                <div class="ui-muni-control">
                    <label for="municipality-prefecture-filter" class="ui-label center">都道府県を選択してください</label>
                    <select id="municipality-prefecture-filter" class="ui-select-lg">
                        <option value="">都道府県を選択...</option>
                        <?php foreach ($prefectures as $pref) : ?>
                            <option value="<?php echo esc_attr($pref['slug']); ?>"><?php echo esc_html($pref['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="municipality-loading" class="ui-loading" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i> 読み込み中...
                </div>

                <div id="municipality-list" class="ui-muni-grid">
                    <div class="ui-muni-placeholder">
                        <div class="placeholder-icon"><i class="fas fa-map-marked-alt"></i></div>
                        <p class="placeholder-text">都道府県を選択すると、<br>ここに市町村一覧が表示されます</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($popular_tags)) : ?>
    <section class="ui-section ui-bg-light" aria-labelledby="tags-title">
        <div class="ui-inner">
            <header class="ui-header">
                <p class="ui-sub-title">POPULAR TAGS</p>
                <h2 id="tags-title" class="ui-main-title">人気キーワードから探す</h2>
            </header>

            <div class="ui-filter-box">
                <input type="text" id="tag-filter-input" class="ui-filter-input" placeholder="タグをキーワードで絞り込む...">
            </div>

            <div class="ui-pill-grid" id="popular-tags-container">
                <?php foreach ($popular_tags as $tag) : ?>
                <a href="<?php echo home_url('/grants/?grant_tag='.$tag->slug); ?>" class="ui-pill ui-pill-tag" data-name="<?php echo esc_attr($tag->name); ?>">
                    <i class="fas fa-hashtag"></i>
                    <span class="text"><?php echo esc_html($tag->name); ?></span>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($all_tags) && count($all_tags) > 10) : ?>
            <div class="ui-collapse-box">
                <button type="button" id="all-tags-toggle" class="ui-collapse-btn" aria-expanded="false">
                    <span>すべてのタグを見る</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="all-tags-content" class="ui-collapse-content" hidden>
                    <div class="ui-pill-grid" id="all-tags-container">
                        <?php foreach ($all_tags as $tag) : ?>
                        <a href="<?php echo home_url('/grants/?grant_tag='.$tag->slug); ?>" class="ui-pill ui-pill-tag" data-name="<?php echo esc_attr($tag->name); ?>">
                            <span class="text"><?php echo esc_html($tag->name); ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <p id="no-tags-msg" class="ui-no-data" style="display:none;">一致するタグがありません</p>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <div class="ui-trust-bar">
        <div class="ui-inner">
            <p class="ui-trust-text">
                <i class="fas fa-check-circle"></i>
                <span>当サイトの情報は各省庁・自治体の公表データに基づき、専門家監修のもと更新されています。</span>
            </p>
        </div>
    </div>

    <?php if (!get_query_var('exclude_cta')): ?>
    <section class="ui-cta-section" aria-label="無料診断">
        <div class="ui-inner">
            <div class="ui-cta-card">
                <div class="ui-cta-icon"><i class="fas fa-clipboard-check"></i></div>
                <h2 class="ui-cta-title">あなたに最適な補助金を無料診断</h2>
                <p class="ui-cta-desc">簡単な質問に答えるだけで、あなたの事業に最適な補助金・助成金を診断します。<br>所要時間はわずか3分です。</p>
                <div class="ui-cta-btns">
                    <a href="https://joseikin-insight.com/subsidy-diagnosis/" class="ui-btn ui-btn-solid-white">
                        <i class="fas fa-play-circle"></i> 今すぐ無料診断
                    </a>
                </div>
                <p class="ui-cta-note"><i class="fas fa-info-circle"></i> 会員登録・メールアドレス不要</p>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<style>
:root {
    --ui-black: #111111;
    --ui-gray-dark: #333333;
    --ui-gray-mid: #888888;
    --ui-gray-light: #e5e5e5;
    --ui-bg-light: #f9f9f9;
    --ui-white: #ffffff;
    --ui-accent: #FFD700;
    --ui-font-en: 'Inter', sans-serif;
    --ui-font-ja: 'Noto Sans JP', sans-serif;
    --ui-trans: 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    --ui-radius: 8px;
}

.ui-search-wrapper {
    font-family: var(--ui-font-ja);
    color: var(--ui-black);
    background: var(--ui-white);
    line-height: 1.6;
}

.ui-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.ui-section { padding: 80px 0; }
.ui-bg-light { background: var(--ui-bg-light); }

/* Headers */
.ui-header {
    text-align: center;
    margin-bottom: 56px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.ui-sub-title {
    font-family: var(--ui-font-en);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.15em;
    color: var(--ui-gray-mid);
    margin: 0 0 12px 0;
    text-transform: uppercase;
    display: block;
}
.ui-main-title {
    font-size: 32px;
    font-weight: 900;
    margin: 0;
    letter-spacing: 0.05em;
    line-height: 1.3;
}

/* Stats Bar */
.ui-stats-bar { background: var(--ui-black); color: var(--ui-white); padding: 12px 0; font-size: 13px; }
.ui-stats-flex { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; }
.ui-stat-item { display: flex; align-items: center; gap: 8px; }
.ui-stat-item .label { color: #aaa; font-family: var(--ui-font-en); font-weight: 700; }
.ui-stat-item .value { color: var(--ui-accent); font-weight: 700; font-size: 16px; font-family: var(--ui-font-en); }

/* Forms */
.ui-search-form { max-width: 900px; margin: 0 auto; }
.ui-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; margin-bottom: 24px; }
.ui-label { display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--ui-gray-dark); }
.ui-label.center { text-align: center; }

.ui-select, .ui-input, .ui-select-lg {
    width: 100%; height: 56px; padding: 0 16px; border: 1px solid var(--ui-gray-light);
    background: var(--ui-white); font-size: 15px; border-radius: var(--ui-radius);
    appearance: none; transition: var(--ui-trans); font-family: var(--ui-font-ja);
}
.ui-select:focus, .ui-input:focus, .ui-select-lg:focus { outline: none; border-color: var(--ui-black); box-shadow: 0 0 0 1px var(--ui-black); }
.ui-select-lg { height: 64px; font-size: 16px; font-weight: 700; }
.ui-select-wrap, .ui-input-wrap { position: relative; }
.ui-select-wrap::after { content: ''; position: absolute; right: 16px; top: 50%; border: 5px solid transparent; border-top-color: var(--ui-black); transform: translateY(-25%); pointer-events: none; }

.ui-radio-group { display: flex; gap: 10px; margin-bottom: 8px; }
.ui-radio-btn { flex: 1; position: relative; cursor: pointer; }
.ui-radio-btn input { position: absolute; opacity: 0; }
.ui-radio-text {
    display: flex; align-items: center; justify-content: center; height: 48px;
    border: 1px solid var(--ui-gray-light); background: var(--ui-white);
    font-weight: 700; font-size: 14px; border-radius: var(--ui-radius); transition: var(--ui-trans);
}
.ui-radio-btn input:checked + .ui-radio-text { background: var(--ui-black); color: var(--ui-white); border-color: var(--ui-black); }

.ui-actions { display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-top: 32px; }
.ui-btn {
    height: 56px; display: flex; align-items: center; justify-content: center; gap: 10px;
    font-weight: 700; font-size: 15px; cursor: pointer; border-radius: var(--ui-radius);
    transition: var(--ui-trans); border: none;
}
.ui-btn-outline { background: transparent; border: 1px solid var(--ui-black); color: var(--ui-black); }
.ui-btn-outline:hover { background: var(--ui-bg-light); }
.ui-btn-solid { background: var(--ui-black); color: var(--ui-white); }
.ui-btn-solid:hover { opacity: 0.85; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.ui-sub-links { margin-top: 24px; text-align: center; font-size: 13px; color: var(--ui-gray-mid); }
.ui-sub-links a { margin: 0 10px; display: inline-flex; align-items: center; gap: 6px; }
.ui-sub-links a:hover { color: var(--ui-black); text-decoration: underline; }

/* Target Audience Grid */
.ui-target-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
.ui-target-card {
    display: flex; align-items: center; padding: 24px; background: var(--ui-white);
    border: 1px solid var(--ui-gray-light); border-radius: var(--ui-radius);
    text-decoration: none; color: var(--ui-black); transition: var(--ui-trans);
    position: relative; overflow: hidden;
}
.ui-target-card:hover { border-color: var(--ui-black); transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
.ui-target-card.featured { border-left: 4px solid var(--ui-black); }
.ui-target-icon {
    width: 50px; height: 50px; background: var(--ui-bg-light); border-radius: 50%;
    display: flex; align-items: center; justify-content: center; font-size: 20px;
    margin-right: 16px; color: var(--ui-gray-dark); flex-shrink: 0;
}
.ui-target-content h3 { font-size: 18px; font-weight: 700; margin: 0 0 4px 0; line-height: 1.4; }
.ui-target-content p { font-size: 13px; color: var(--ui-gray-mid); margin: 0; font-weight: 500; }
.ui-target-arrow { margin-left: auto; color: var(--ui-gray-light); transition: var(--ui-trans); }
.ui-target-card:hover .ui-target-arrow { color: var(--ui-black); transform: translateX(4px); }
.ui-target-card:hover .ui-target-icon { background: var(--ui-black); color: var(--ui-white); }

/* Grids & Cards */
.ui-grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
.ui-grid-4 { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; }
.ui-card, .ui-region-card {
    background: var(--ui-white); border: 1px solid var(--ui-gray-light); padding: 24px;
    border-radius: var(--ui-radius); transition: var(--ui-trans);
}
.ui-card:hover, .ui-region-card:hover { border-color: var(--ui-black); transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
.ui-card-header { border-bottom: 2px solid var(--ui-black); padding-bottom: 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
.ui-card-header .icon { font-size: 20px; color: var(--ui-gray-dark); }
.ui-card-header h3 { margin: 0; font-size: 18px; font-weight: 700; line-height: 1.2; }
.ui-card-header .sub { font-family: var(--ui-font-en); font-size: 11px; color: var(--ui-gray-mid); font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
.ui-region-head { border-bottom: 1px solid var(--ui-gray-light); padding-bottom: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
.ui-region-head h3 { margin: 0; font-size: 16px; font-weight: 700; }
.ui-list-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--ui-bg-light); font-size: 14px; font-weight: 500; }
.ui-list-item:hover { padding-left: 8px; color: var(--ui-black); }
.ui-pref-links { display: flex; flex-wrap: wrap; gap: 8px; }
.ui-pref-link { font-size: 13px; padding: 6px 12px; background: var(--ui-bg-light); color: var(--ui-gray-dark); border-radius: 4px; }
.ui-pref-link:hover { background: var(--ui-black); color: var(--ui-white); }

/* Pills & Collapse */
.ui-filter-box { max-width: 500px; margin: 0 auto 40px; }
.ui-filter-input { width: 100%; height: 50px; padding: 0 24px; border: 1px solid var(--ui-gray-light); text-align: center; font-size: 14px; border-radius: 25px; }
.ui-pill-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
.ui-pill { display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; border: 1px solid var(--ui-gray-light); background: var(--ui-white); font-size: 13px; font-weight: 700; border-radius: 4px; transition: var(--ui-trans); }
.ui-pill:hover { border-color: var(--ui-black); background: var(--ui-black); color: var(--ui-white); }
.ui-collapse-box { text-align: center; margin-top: 40px; }
.ui-collapse-btn { background: none; border: none; border-bottom: 1px solid var(--ui-black); padding-bottom: 4px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
.ui-collapse-content { margin-top: 30px; animation: slideDown 0.3s ease-out; }
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

/* Municipality List UI */
.ui-muni-interface { max-width: 800px; margin: 0 auto; }
.ui-muni-control { margin-bottom: 40px; }
.ui-muni-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px; min-height: 200px; }
.ui-muni-link { display: block; padding: 12px; background: var(--ui-bg-light); text-align: center; font-size: 13px; border-radius: 4px; }
.ui-muni-link:hover { background: var(--ui-black); color: var(--ui-white); }

/* Improved Placeholder */
.ui-muni-placeholder { 
    grid-column: 1 / -1; 
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 40px; background: var(--ui-bg-light); border: 2px dashed var(--ui-gray-light); border-radius: 8px;
    color: var(--ui-gray-mid); text-align: center;
}
.placeholder-icon { font-size: 32px; margin-bottom: 16px; color: var(--ui-gray-light); }
.placeholder-text { font-size: 14px; font-weight: 700; line-height: 1.6; }

.ui-loading { text-align: center; padding: 40px; color: var(--ui-gray-mid); font-size: 14px; }
.ui-spinner { width: 24px; height: 24px; border: 3px solid #ccc; border-top-color: #000; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 10px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Trust Bar */
.ui-trust-bar { padding: 24px 0; background: var(--ui-bg-light); text-align: center; border-top: 1px solid var(--ui-gray-light); }
.ui-trust-text { font-size: 12px; color: var(--ui-gray-mid); display: flex; align-items: center; justify-content: center; gap: 6px; }

/* CTA */
.ui-cta-section { background: var(--ui-black); padding: 100px 0; color: var(--ui-white); }
.ui-cta-card { max-width: 700px; margin: 0 auto; text-align: center; padding: 40px; border: 1px solid rgba(255,255,255,0.2); }
.ui-cta-icon { font-size: 40px; color: var(--ui-accent); margin-bottom: 20px; }
.ui-cta-title { font-size: 32px; font-weight: 900; margin-bottom: 16px; }
.ui-cta-desc { color: #ccc; margin-bottom: 32px; font-size: 16px; }
.ui-btn-solid-white { background: var(--ui-white); color: var(--ui-black); padding: 16px 40px; border-radius: 50px; }
.ui-btn-solid-white:hover { transform: scale(1.05); box-shadow: 0 0 20px rgba(255,255,255,0.2); }
.ui-cta-note { margin-top: 16px; font-size: 13px; color: #888; }

/* Responsive */
@media (max-width: 768px) {
    .ui-section { padding: 60px 0; }
    .ui-main-title { font-size: 24px; }
    .ui-actions { grid-template-columns: 1fr; }
    .ui-form-grid { gap: 16px; }
    .ui-cta-card { padding: 20px; border: none; }
    .ui-target-card { padding: 20px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    const AJAX_URL = '<?php echo admin_url("admin-ajax.php"); ?>';
    const NONCE = '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>';

    // 1. 市町村連動 (Main Form)
    const mainPrefSelect = document.getElementById('prefecture-select');
    const mainMuniGroup = document.getElementById('municipality-group');
    const mainMuniSelect = document.getElementById('municipality-select');
    const mainMuniSpinner = document.getElementById('municipality-spinner');

    if(mainPrefSelect && mainMuniSelect) {
        mainPrefSelect.addEventListener('change', function() {
            const slug = this.value;
            mainMuniSelect.innerHTML = '<option value="">選択してください</option>';
            if(!slug) { mainMuniGroup.style.display = 'none'; return; }
            mainMuniGroup.style.display = 'block';
            mainMuniSelect.disabled = true;
            if(mainMuniSpinner) mainMuniSpinner.style.display = 'block';

            const formData = new FormData();
            formData.append('action', 'gi_get_municipalities_for_prefecture');
            formData.append('prefecture_slug', slug);
            formData.append('nonce', NONCE);

            fetch(AJAX_URL, { method: 'POST', body: formData }).then(res=>res.json()).then(data=>{
                mainMuniSelect.disabled = false;
                if(mainMuniSpinner) mainMuniSpinner.style.display = 'none';
                if(data.success) {
                    const items = data.data.municipalities || (data.data.data ? data.data.data.municipalities : []);
                    if(items.length > 0) {
                        mainMuniSelect.innerHTML = '<option value="">すべての市町村</option>' + items.map(m => `<option value="${m.slug}">${m.name}</option>`).join('');
                    } else mainMuniSelect.innerHTML = '<option value="">データなし</option>';
                } else mainMuniSelect.innerHTML = '<option value="">エラー</option>';
            }).catch(err=>{ mainMuniSelect.disabled=false; mainMuniSelect.innerHTML='<option value="">通信エラー</option>'; });
        });
    }

    // 2. 市町村一覧表示 (List Section)
    const listPrefFilter = document.getElementById('municipality-prefecture-filter');
    const listMuniContainer = document.getElementById('municipality-list');
    const listLoading = document.getElementById('municipality-loading');

    if(listPrefFilter && listMuniContainer) {
        listPrefFilter.addEventListener('change', function() {
            const slug = this.value;
            if(!slug) {
                listMuniContainer.innerHTML = `
                    <div class="ui-muni-placeholder">
                        <div class="placeholder-icon"><i class="fas fa-map-marked-alt"></i></div>
                        <p class="placeholder-text">都道府県を選択すると、<br>ここに市町村一覧が表示されます</p>
                    </div>`;
                return;
            }
            listMuniContainer.style.display = 'none';
            if(listLoading) listLoading.style.display = 'block';

            const formData = new FormData();
            formData.append('action', 'gi_get_municipalities_for_prefecture');
            formData.append('prefecture_slug', slug);
            formData.append('nonce', NONCE);

            fetch(AJAX_URL, { method: 'POST', body: formData }).then(res=>res.json()).then(data=>{
                if(listLoading) listLoading.style.display = 'none';
                listMuniContainer.style.display = 'grid';
                if(data.success) {
                    const items = data.data.municipalities || (data.data.data ? data.data.data.municipalities : []);
                    if(items.length > 0) {
                        listMuniContainer.innerHTML = items.map(m => `<a href="<?php echo home_url('/grant_municipality/'); ?>${m.slug}/" class="ui-muni-link">${m.name}</a>`).join('');
                    } else listMuniContainer.innerHTML = '<p class="ui-msg-box">データが見つかりません。</p>';
                } else listMuniContainer.innerHTML = '<p class="ui-msg-box">読み込みエラー。</p>';
            });
        });
    }

    // 3. フィルター (Category/Tags)
    function setupFilter(inputId, containerId, msgId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);
        const msg = document.getElementById(msgId);
        if(input && container) {
            input.addEventListener('input', function() {
                const val = this.value.toLowerCase();
                let count = 0;
                const items = container.querySelectorAll('.ui-pill');
                items.forEach(el => {
                    if(el.getAttribute('data-name').toLowerCase().includes(val)) { el.style.display = 'inline-flex'; count++; }
                    else el.style.display = 'none';
                });
                const hiddenContainer = document.querySelector(containerId.replace('popular', 'all'));
                if(hiddenContainer) {
                     hiddenContainer.querySelectorAll('.ui-pill').forEach(el => {
                        if(el.getAttribute('data-name').toLowerCase().includes(val)) { el.style.display = 'inline-flex'; count++; }
                        else el.style.display = 'none';
                     });
                }
                if(msg) msg.style.display = count === 0 ? 'block' : 'none';
            });
        }
    }
    setupFilter('cat-filter-input', 'popular-categories-container', 'no-categories-msg');
    setupFilter('tag-filter-input', 'popular-tags-container', 'no-tags-msg');

    // 4. 開閉トグル
    function setupToggle(btnId, contentId) {
        const btn = document.getElementById(btnId);
        const content = document.getElementById(contentId);
        if(btn && content) {
            btn.addEventListener('click', function() {
                const isHidden = content.hidden;
                content.hidden = !isHidden;
                this.setAttribute('aria-expanded', isHidden);
                const span = this.querySelector('span');
                const icon = this.querySelector('i');
                if(isHidden) { span.textContent='閉じる'; icon.className='fas fa-chevron-up'; }
                else { span.textContent='すべて見る'; icon.className='fas fa-chevron-down'; }
            });
        }
    }
    setupToggle('all-categories-toggle', 'all-categories-content');
    setupToggle('all-tags-toggle', 'all-tags-content');

    // 5. リセット
    const resetBtn = document.getElementById('reset-btn');
    const form = document.getElementById('grant-search-form');
    if(resetBtn && form) {
        resetBtn.addEventListener('click', function() {
            form.reset();
            if(mainMuniGroup) mainMuniGroup.style.display = 'none';
            if(mainMuniSelect) mainMuniSelect.innerHTML = '<option value="">選択してください</option>';
        });
    }

    console.log('✅ Search Section v14.0 Initialized');
});
</script>