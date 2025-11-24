<?php
/**
 * Grant Single Page - Perfect Edition v24.2
 * 補助金詳細ページ - 完全版（モバイルナビ修正）
 * 
 * v24.2 変更点:
 * - 下部固定ナビゲーション廃止
 * - 右下フローティングAIボタン採用
 * - 共通バナーとの干渉解消
 * - 目次は通常のサイドバーに統合
 * 
 * @package Grant_Insight_Perfect
 * @version 24.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!have_posts()) {
    wp_redirect(home_url('/404'), 302);
    exit;
}

get_header();
the_post();

// ========================================
// データ取得・初期化
// ========================================

$post_id = get_the_ID();
$canonical_url = get_permalink($post_id);
$seo_title = get_the_title();
$current_year = date('Y');

// ACFデータ取得（全項目）
$grant_data = array(
    'organization' => function_exists('get_field') ? get_field('organization', $post_id) : '',
    'max_amount' => function_exists('get_field') ? get_field('max_amount', $post_id) : '',
    'max_amount_numeric' => function_exists('get_field') ? intval(get_field('max_amount_numeric', $post_id)) : 0,
    'subsidy_rate' => function_exists('get_field') ? get_field('subsidy_rate', $post_id) : '',
    'subsidy_rate_detailed' => function_exists('get_field') ? get_field('subsidy_rate_detailed', $post_id) : '',
    'deadline' => function_exists('get_field') ? get_field('deadline', $post_id) : '',
    'deadline_date' => function_exists('get_field') ? get_field('deadline_date', $post_id) : '',
    'application_period' => function_exists('get_field') ? get_field('application_period', $post_id) : '',
    'grant_target' => function_exists('get_field') ? get_field('grant_target', $post_id) : '',
    'contact_info' => function_exists('get_field') ? get_field('contact_info', $post_id) : '',
    'official_url' => function_exists('get_field') ? get_field('official_url', $post_id) : '',
    'application_status' => function_exists('get_field') ? get_field('application_status', $post_id) : 'open',
    'application_method' => function_exists('get_field') ? get_field('application_method', $post_id) : '',
    'required_documents' => function_exists('get_field') ? get_field('required_documents', $post_id) : '',
    'required_documents_detailed' => function_exists('get_field') ? get_field('required_documents_detailed', $post_id) : '',
    'eligible_expenses' => function_exists('get_field') ? get_field('eligible_expenses', $post_id) : '',
    'eligible_expenses_detailed' => function_exists('get_field') ? get_field('eligible_expenses_detailed', $post_id) : '',
    'adoption_rate' => function_exists('get_field') ? floatval(get_field('adoption_rate', $post_id)) : 0,
    'grant_difficulty' => function_exists('get_field') ? get_field('grant_difficulty', $post_id) : 'normal',
    'difficulty_level' => function_exists('get_field') ? get_field('difficulty_level', $post_id) : '',
    'is_featured' => function_exists('get_field') ? get_field('is_featured', $post_id) : false,
    'views_count' => function_exists('get_field') ? intval(get_field('views_count', $post_id)) : 0,
    'ai_summary' => function_exists('get_field') ? get_field('ai_summary', $post_id) : '',
    'area_notes' => function_exists('get_field') ? get_field('area_notes', $post_id) : '',
);

// タクソノミー取得
$taxonomies = array(
    'categories' => wp_get_post_terms($post_id, 'grant_category'),
    'prefectures' => wp_get_post_terms($post_id, 'grant_prefecture'),
    'municipalities' => wp_get_post_terms($post_id, 'grant_municipality'),
    'tags' => wp_get_post_tags($post_id),
);

foreach ($taxonomies as $key => $terms) {
    if (is_wp_error($terms) || empty($terms)) {
        $taxonomies[$key] = array();
    }
}

// 都道府県・市町村の表示ロジック
function gi_format_prefectures($prefectures) {
    if (empty($prefectures)) return '';
    
    $is_nationwide = false;
    foreach ($prefectures as $pref) {
        if (in_array($pref->slug, array('zenkoku', 'nationwide'))) {
            $is_nationwide = true;
            break;
        }
    }
    
    if ($is_nationwide || count($prefectures) >= 47) {
        return '全国';
    }
    
    if (count($prefectures) >= 5) {
        $display_prefs = array_slice($prefectures, 0, 4);
        $names = array_map(function($pref) { return $pref->name; }, $display_prefs);
        return implode('、', $names) . '...';
    }
    
    $names = array_map(function($pref) { return $pref->name; }, $prefectures);
    return implode('、', $names);
}

function gi_format_municipalities($municipalities, $prefectures) {
    if (empty($municipalities)) return '';
    
    if (!empty($prefectures)) {
        foreach ($prefectures as $pref) {
            if (in_array($pref->slug, array('zenkoku', 'nationwide'))) {
                return '';
            }
        }
        if (count($prefectures) >= 47) {
            return '';
        }
    }
    
    foreach ($municipalities as $muni) {
        if (stripos($muni->name, '全域') !== false || stripos($muni->slug, 'zeniki') !== false) {
            if (!empty($prefectures)) {
                return $prefectures[0]->name . '全域';
            }
        }
    }
    
    if (count($municipalities) >= 5) {
        $display_munis = array_slice($municipalities, 0, 4);
        $names = array_map(function($muni) { return $muni->name; }, $display_munis);
        return implode('、', $names) . '...';
    }
    
    $names = array_map(function($muni) { return $muni->name; }, $municipalities);
    return implode('、', $names);
}

$prefecture_display = gi_format_prefectures($taxonomies['prefectures']);
$municipality_display = gi_format_municipalities($taxonomies['municipalities'], $taxonomies['prefectures']);

// 金額フォーマット
$formatted_amount = '';
$max_amount_yen = intval($grant_data['max_amount_numeric']);
if ($max_amount_yen > 0) {
    if ($max_amount_yen >= 100000000) {
        $formatted_amount = number_format($max_amount_yen / 100000000, 1) . '億円';
    } elseif ($max_amount_yen >= 10000) {
        $formatted_amount = number_format($max_amount_yen / 10000) . '万円';
    } else {
        $formatted_amount = number_format($max_amount_yen) . '円';
    }
} elseif (!empty($grant_data['max_amount'])) {
    $formatted_amount = $grant_data['max_amount'];
}

// 締切日計算
$deadline_info = '';
$deadline_class = '';
$days_remaining = 0;
if (!empty($grant_data['deadline_date'])) {
    $deadline_timestamp = strtotime($grant_data['deadline_date']);
    if ($deadline_timestamp && $deadline_timestamp > 0) {
        $deadline_info = date('Y年n月j日', $deadline_timestamp);
        $current_time = current_time('timestamp');
        $days_remaining = ceil(($deadline_timestamp - $current_time) / 86400);
        
        if ($days_remaining <= 0) {
            $deadline_class = 'closed';
            $deadline_info .= ' (終了)';
        } elseif ($days_remaining <= 7) {
            $deadline_class = 'urgent';
            $deadline_info .= ' (残' . $days_remaining . '日)';
        } elseif ($days_remaining <= 30) {
            $deadline_class = 'warning';
        }
    }
} elseif (!empty($grant_data['deadline'])) {
    $deadline_info = $grant_data['deadline'];
}

// 難易度設定
$difficulty_configs = array(
    'easy' => array('label' => '易', 'dots' => 1, 'description' => '初心者向け'),
    'normal' => array('label' => '中', 'dots' => 2, 'description' => '一般的'),
    'hard' => array('label' => '難', 'dots' => 3, 'description' => '専門的'),
);
$difficulty = !empty($grant_data['grant_difficulty']) ? $grant_data['grant_difficulty'] : 'normal';
$difficulty_data = isset($difficulty_configs[$difficulty]) ? $difficulty_configs[$difficulty] : $difficulty_configs['normal'];

// ステータス
$status_configs = array(
    'open' => array('label' => '募集中', 'class' => 'open'),
    'closed' => array('label' => '終了', 'class' => 'closed'),
    'upcoming' => array('label' => '募集予定', 'class' => 'upcoming'),
);
$application_status = !empty($grant_data['application_status']) ? $grant_data['application_status'] : 'open';
$status_data = isset($status_configs[$application_status]) ? $status_configs[$application_status] : $status_configs['open'];

// 閲覧数更新
$current_views = intval($grant_data['views_count']);
$new_views = $current_views + 1;
if (function_exists('update_post_meta')) {
    update_post_meta($post_id, 'views_count', $new_views);
    $grant_data['views_count'] = $new_views;
}

// OGP画像取得
$og_image = '';
if (has_post_thumbnail($post_id)) {
    $og_image = get_the_post_thumbnail_url($post_id, 'large');
} else {
    $og_image = get_site_icon_url(512);
    if (empty($og_image)) {
        $og_image = get_template_directory_uri() . '/assets/images/default-og-grant.jpg';
        if (!file_exists(get_template_directory() . '/assets/images/default-og-grant.jpg')) {
            $og_image = 'https://via.placeholder.com/1200x630.png?text=' . urlencode($seo_title);
        }
    }
}

// メタディスクリプション生成
$meta_description = '';
if (!empty($grant_data['ai_summary'])) {
    $raw_text = wp_strip_all_tags($grant_data['ai_summary']);
    $meta_description = mb_substr($raw_text, 0, 160, 'UTF-8');
    if (mb_strlen($raw_text, 'UTF-8') > 160) {
        $meta_description .= '...';
    }
} elseif (has_excerpt()) {
    $raw_text = wp_strip_all_tags(get_the_excerpt());
    $meta_description = mb_substr($raw_text, 0, 160, 'UTF-8');
    if (mb_strlen($raw_text, 'UTF-8') > 160) {
        $meta_description .= '...';
    }
} else {
    $raw_text = wp_strip_all_tags(get_the_content());
    $meta_description = mb_substr($raw_text, 0, 160, 'UTF-8');
    if (mb_strlen($raw_text, 'UTF-8') > 160) {
        $meta_description .= '...';
    }
}

// 読了時間計算
$content = get_the_content();
$word_count = mb_strlen(strip_tags($content), 'UTF-8');
$reading_time = max(1, ceil($word_count / 400));

// キーワード生成
$seo_keywords = array();
$seo_keywords[] = $seo_title;
$seo_keywords[] = '補助金';
$seo_keywords[] = '助成金';
$seo_keywords[] = $current_year . '年度';
if (!empty($grant_data['organization'])) {
    $seo_keywords[] = $grant_data['organization'];
}
foreach ($taxonomies['categories'] as $cat) {
    $seo_keywords[] = $cat->name;
}
foreach ($taxonomies['prefectures'] as $pref) {
    $seo_keywords[] = $pref->name;
}
$seo_keywords = array_unique($seo_keywords);

// robots meta
$robots_content = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
if ($application_status === 'closed') {
    $robots_content = 'index, follow, max-snippet:160, max-image-preview:standard';
}

// レコメンドシステム v2.0（終了案件を除外）
function gi_get_scored_related_grants($post_id, $taxonomies, $grant_data, $limit = 12, $exclude_closed = true) {
    $candidate_args = array(
        'post_type' => 'grant',
        'posts_per_page' => 100,
        'post__not_in' => array($post_id),
        'post_status' => 'publish',
    );
    
    if ($exclude_closed) {
        $candidate_args['meta_query'] = array(
            'relation' => 'OR',
            array(
                'key' => 'application_status',
                'value' => 'open',
                'compare' => '='
            ),
            array(
                'key' => 'application_status',
                'compare' => 'NOT EXISTS'
            )
        );
    }
    
    $candidates = new WP_Query($candidate_args);
    $scored_grants = array();
    
    $current_pref_ids = !empty($taxonomies['prefectures']) ? wp_list_pluck($taxonomies['prefectures'], 'term_id') : array();
    $current_muni_ids = !empty($taxonomies['municipalities']) ? wp_list_pluck($taxonomies['municipalities'], 'term_id') : array();
    $current_cat_ids = !empty($taxonomies['categories']) ? wp_list_pluck($taxonomies['categories'], 'term_id') : array();
    $current_tag_ids = !empty($taxonomies['tags']) ? wp_list_pluck($taxonomies['tags'], 'term_id') : array();
    
    $current_is_nationwide = false;
    if (!empty($taxonomies['prefectures'])) {
        foreach ($taxonomies['prefectures'] as $pref) {
            if (in_array($pref->slug, array('zenkoku', 'nationwide')) || count($taxonomies['prefectures']) >= 47) {
                $current_is_nationwide = true;
                break;
            }
        }
    }
    
    if ($candidates->have_posts()) {
        while ($candidates->have_posts()) {
            $candidates->the_post();
            $candidate_id = get_the_ID();
            $score = 0;
            $match_details = array();
            
            $candidate_prefs = wp_get_post_terms($candidate_id, 'grant_prefecture', array('fields' => 'ids'));
            $candidate_munis = wp_get_post_terms($candidate_id, 'grant_municipality', array('fields' => 'ids'));
            $candidate_prefs_slugs = wp_get_post_terms($candidate_id, 'grant_prefecture', array('fields' => 'slugs'));
            
            $candidate_is_nationwide = (
                in_array('zenkoku', $candidate_prefs_slugs) || 
                in_array('nationwide', $candidate_prefs_slugs) || 
                count($candidate_prefs) >= 47
            );
            
            if (!empty($current_muni_ids) && !empty($candidate_munis)) {
                $muni_intersect = array_intersect($candidate_munis, $current_muni_ids);
                if (count($muni_intersect) > 0) {
                    $score += count($muni_intersect) * 200;
                    $match_details[] = '同じ市町村';
                }
            }
            
            if (!empty($current_pref_ids) && !empty($candidate_prefs)) {
                $pref_intersect = array_intersect($candidate_prefs, $current_pref_ids);
                if (count($pref_intersect) > 0) {
                    $score += count($pref_intersect) * 100;
                    $match_details[] = '同じ都道府県';
                }
            }
            
            if (!$current_is_nationwide && $candidate_is_nationwide) {
                $score += 50;
                $match_details[] = '全国対応';
            } elseif ($current_is_nationwide && !$candidate_is_nationwide) {
                $score += 20;
                $match_details[] = '地域限定';
            } elseif ($current_is_nationwide && $candidate_is_nationwide) {
                $score += 30;
                $match_details[] = '全国対応';
            }
            
            if (!empty($current_cat_ids)) {
                $candidate_cats = wp_get_post_terms($candidate_id, 'grant_category', array('fields' => 'ids'));
                $cat_intersect = array_intersect($candidate_cats, $current_cat_ids);
                if (count($cat_intersect) > 0) {
                    $score += count($cat_intersect) * 80;
                    $match_details[] = '同じカテゴリ';
                }
            }
            
            if (!empty($current_tag_ids)) {
                $candidate_tags = wp_get_post_tags($candidate_id, array('fields' => 'ids'));
                $tag_intersect = array_intersect($candidate_tags, $current_tag_ids);
                if (count($tag_intersect) > 0) {
                    $score += count($tag_intersect) * 40;
                    $match_details[] = '同じタグ';
                }
            }
            
            $candidate_status = function_exists('get_field') ? get_field('application_status', $candidate_id) : 'open';
            if ($candidate_status === 'open') {
                $candidate_deadline = function_exists('get_field') ? get_field('deadline_date', $candidate_id) : '';
                if (!empty($candidate_deadline)) {
                    $deadline_timestamp = strtotime($candidate_deadline);
                    if ($deadline_timestamp && $deadline_timestamp > current_time('timestamp')) {
                        $score += 30;
                        $match_details[] = '募集中';
                    }
                }
            }
            
            $candidate_amount = function_exists('get_field') ? intval(get_field('max_amount_numeric', $candidate_id)) : 0;
            $current_amount = $grant_data['max_amount_numeric'];
            if ($candidate_amount > 0 && $current_amount > 0) {
                $amount_diff_ratio = abs($candidate_amount - $current_amount) / max($candidate_amount, $current_amount);
                if ($amount_diff_ratio < 0.3) {
                    $score += 10;
                    $match_details[] = '似た金額';
                }
            }
            
            $candidate_views = function_exists('get_field') ? intval(get_field('views_count', $candidate_id)) : 0;
            if ($candidate_views > 100) {
                $score += 5;
            }
            
            if ($score >= 20) {
                $scored_grants[] = array(
                    'id' => $candidate_id,
                    'score' => $score,
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'match_details' => !empty($match_details) ? implode(', ', $match_details) : '関連',
                );
            }
        }
        wp_reset_postdata();
    }
    
    usort($scored_grants, function($a, $b) {
        if ($a['score'] === $b['score']) {
            return $b['id'] - $a['id'];
        }
        return $b['score'] - $a['score'];
    });
    
    return array_slice($scored_grants, 0, $limit);
}

$scored_related_grants = gi_get_scored_related_grants($post_id, $taxonomies, $grant_data, 12, true);

// 関連コラム取得
$related_columns_query = new WP_Query(array(
    'post_type' => 'column',
    'posts_per_page' => 6,
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => 'related_grants',
            'value' => '"' . $post_id . '"',
            'compare' => 'LIKE'
        )
    ),
    'orderby' => 'date',
    'order' => 'DESC'
));

// 公開日・更新日
$published_date = get_the_date('c');
$modified_date = get_the_modified_date('c');
?>

<!-- 構造化データ（JSON-LD） -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "MonetaryGrant",
            "@id": "<?php echo esc_js(get_permalink() . '#grant'); ?>",
            "name": <?php echo json_encode($seo_title); ?>,
            "description": <?php echo json_encode($meta_description); ?>,
            "url": "<?php echo esc_js(get_permalink()); ?>",
            "image": "<?php echo esc_js($og_image); ?>",
            "funder": {
                "@type": "Organization",
                "name": <?php echo json_encode($grant_data['organization'] ?: get_bloginfo('name')); ?>,
                "url": "<?php echo esc_js($grant_data['official_url'] ?: home_url('/')); ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo esc_js(get_site_icon_url(512)); ?>"
                }
            },
            <?php if ($max_amount_yen > 0): ?>
            "amount": {
                "@type": "MonetaryAmount",
                "currency": "JPY",
                "value": "<?php echo $max_amount_yen; ?>"
            },
            <?php endif; ?>
            <?php if (!empty($grant_data['deadline_date'])): ?>
            "applicationDeadline": "<?php echo esc_js($grant_data['deadline_date']); ?>",
            <?php endif; ?>
            "datePublished": "<?php echo $published_date; ?>",
            "dateModified": "<?php echo $modified_date; ?>",
            "inLanguage": "ja-JP"
        },
        {
            "@type": "Article",
            "@id": "<?php echo esc_js(get_permalink() . '#article'); ?>",
            "headline": <?php echo json_encode($seo_title); ?>,
            "description": <?php echo json_encode($meta_description); ?>,
            "image": "<?php echo esc_js($og_image); ?>",
            "datePublished": "<?php echo $published_date; ?>",
            "dateModified": "<?php echo $modified_date; ?>",
            "author": {
                "@type": "Organization",
                "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
                "url": "<?php echo esc_js(home_url()); ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo esc_js(get_site_icon_url(512)); ?>"
                }
            },
            "publisher": {
                "@type": "Organization",
                "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
                "url": "<?php echo esc_js(home_url()); ?>",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?php echo esc_js(get_site_icon_url(512)); ?>"
                }
            },
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "<?php echo esc_js(get_permalink()); ?>"
            }
        },
        {
            "@type": "BreadcrumbList",
            "@id": "<?php echo esc_js(get_permalink() . '#breadcrumb'); ?>",
            "itemListElement": [
                {
                    "@type": "ListItem",
                    "position": 1,
                    "name": "ホーム",
                    "item": "<?php echo esc_js(home_url('/')); ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "補助金一覧",
                    "item": "<?php echo esc_js(home_url('/grants/')); ?>"
                }
                <?php if (!empty($taxonomies['categories'])): ?>
                ,{
                    "@type": "ListItem",
                    "position": 3,
                    "name": "<?php echo esc_js($taxonomies['categories'][0]->name); ?>",
                    "item": "<?php echo esc_js(get_term_link($taxonomies['categories'][0])); ?>"
                },
                {
                    "@type": "ListItem",
                    "position": 4,
                    "name": <?php echo json_encode($seo_title); ?>,
                    "item": "<?php echo esc_js(get_permalink()); ?>"
                }
                <?php else: ?>
                ,{
                    "@type": "ListItem",
                    "position": 3,
                    "name": <?php echo json_encode($seo_title); ?>,
                    "item": "<?php echo esc_js(get_permalink()); ?>"
                }
                <?php endif; ?>
            ]
        }
    ]
}
</script>

<!-- スタイル定義 -->
<style>
/* ===============================================
   PERFECT DESIGN SYSTEM v24.2 - MOBILE NAV FIXED
   =============================================== */

html, body {
    overflow-x: hidden;
    width: 100%;
    max-width: 100vw;
    position: relative;
}

input[type="text"], 
input[type="search"], 
input[type="email"], 
input[type="tel"], 
textarea, 
select {
    font-size: 16px !important;
    -webkit-text-size-adjust: 100%;
    text-size-adjust: 100%;
}

:root {
    --gus-white: #ffffff;
    --gus-black: #111111;
    --gus-yellow: #FFD700;
    --gus-yellow-dark: #FFA500;
    --gus-gray-50: #f9f9f9;
    --gus-gray-100: #f5f5f5;
    --gus-gray-200: #eeeeee;
    --gus-gray-300: #e5e5e5;
    --gus-gray-400: #bdbdbd;
    --gus-gray-500: #9e9e9e;
    --gus-gray-600: #757575;
    --gus-gray-700: #616161;
    --gus-gray-800: #424242;
    --gus-gray-900: #212121;
    --gus-text-xs: 12px;
    --gus-text-sm: 13px;
    --gus-text-base: 14px;
    --gus-text-md: 14px;
    --gus-text-lg: 15px;
    --gus-text-xl: 16px;
    --gus-text-2xl: 20px;
    --gus-space-xs: 4px;
    --gus-space-sm: 8px;
    --gus-space-md: 12px;
    --gus-space-lg: 16px;
    --gus-space-xl: 24px;
    --gus-space-2xl: 32px;
    --gus-radius: 8px;
    --gus-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    --gus-shadow-lg: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gus-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --gus-sidebar-width: 360px;
    --gus-content-max-width: 1400px;
    --gus-font-ja: 'Noto Sans JP', sans-serif;
    --gus-font-en: 'Inter', sans-serif;
}

.gus-single {
    max-width: var(--gus-content-max-width);
    margin: 0 auto;
    padding: var(--gus-space-xl) var(--gus-space-lg);
    background: var(--gus-white);
    font-family: var(--gus-font-ja);
    font-size: var(--gus-text-base);
    color: var(--gus-gray-800);
    line-height: 1.7;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    user-select: text;
    overflow-x: hidden;
    width: 100%;
    box-sizing: border-box;
}

.gus-layout {
    display: grid;
    grid-template-columns: 1fr var(--gus-sidebar-width);
    gap: var(--gus-space-2xl);
    align-items: start;
}

.gus-main {
    min-width: 0;
    overflow-x: hidden;
    width: 100%;
    box-sizing: border-box;
}

.gus-sidebar {
    position: sticky;
    top: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    align-self: flex-start;
}

.gus-sidebar-card {
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: 16px;
    transition: all var(--gus-transition);
}

.gus-sidebar-card:hover {
    border-color: var(--gus-black);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.gus-sidebar-title {
    font-size: 11px;
    font-weight: 800;
    color: var(--gus-black);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ===============================================
   PC AIチャット
   =============================================== */
.gus-pc-ai-permanent {
    background: var(--gus-white);
    border: 2px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    display: flex;
    flex-direction: column;
    min-height: 400px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.gus-pc-ai-permanent:hover {
    border-color: var(--gus-black);
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
}

.gus-pc-ai-permanent-header {
    padding: 16px;
    background: linear-gradient(135deg, var(--gus-black) 0%, #1a1a1a 100%);
    color: var(--gus-white);
    border-bottom: 2px solid var(--gus-yellow);
    flex-shrink: 0;
}

.gus-pc-ai-permanent-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12.8px;
    font-weight: 800;
    margin: 0 0 6px 0;
    letter-spacing: -0.3px;
}

.gus-pc-ai-permanent-subtitle {
    font-size: 9.4px;
    opacity: 0.85;
    font-weight: 500;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.gus-pc-ai-permanent-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: var(--gus-gray-50);
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.gus-pc-ai-permanent-messages::-webkit-scrollbar {
    width: 6px;
}

.gus-pc-ai-permanent-messages::-webkit-scrollbar-track {
    background: transparent;
}

.gus-pc-ai-permanent-messages::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
}

.gus-pc-ai-permanent-input-container {
    padding: 14px;
    background: var(--gus-white);
    border-top: 2px solid var(--gus-gray-300);
    flex-shrink: 0;
}

.gus-pc-ai-permanent-input-wrapper {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.gus-pc-ai-permanent-input {
    flex: 1;
    padding: 12px 14px;
    border: 2px solid var(--gus-gray-300);
    border-radius: 4px;
    font-size: 16px;
    font-family: inherit;
    min-height: 44px;
    max-height: 80px;
    resize: none;
    transition: all var(--gus-transition);
    background: var(--gus-gray-100);
    color: #1A1A1A;
}

.gus-pc-ai-permanent-input:focus {
    outline: none;
    border-color: var(--gus-yellow);
    background: var(--gus-white);
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.2);
}

.gus-pc-ai-permanent-send {
    width: 44px;
    height: 44px;
    background: var(--gus-yellow);
    color: var(--gus-black);
    border: 2px solid var(--gus-yellow);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--gus-transition);
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
}

.gus-pc-ai-permanent-send:hover:not(:disabled) {
    background: var(--gus-black);
    color: var(--gus-yellow);
    border-color: var(--gus-black);
    transform: scale(1.05);
}

.gus-pc-ai-permanent-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.gus-pc-ai-permanent-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.gus-pc-ai-permanent-suggestion {
    padding: 7px 12px;
    background: var(--gus-gray-100);
    color: #1A1A1A;
    border: 1px solid var(--gus-gray-300);
    border-radius: 4px;
    font-size: 9.4px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--gus-transition);
    white-space: nowrap;
}

.gus-pc-ai-permanent-suggestion:hover {
    background: var(--gus-yellow);
    color: var(--gus-black);
    border-color: var(--gus-yellow);
    transform: translateY(-1px);
}

/* AIメッセージ */
.gus-ai-message {
    display: flex;
    gap: 10px;
    max-width: 90%;
    animation: messageSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.gus-ai-message--assistant {
    align-self: flex-start;
}

.gus-ai-message--user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.gus-ai-message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 2px solid;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.gus-ai-message--assistant .gus-ai-message-avatar {
    background: linear-gradient(135deg, var(--gus-black) 0%, #333333 100%);
    color: var(--gus-white);
    border-color: var(--gus-black);
}

.gus-ai-message--user .gus-ai-message-avatar {
    background: linear-gradient(135deg, var(--gus-white) 0%, var(--gus-gray-100) 100%);
    color: var(--gus-black);
    border-color: var(--gus-gray-300);
}

.gus-ai-message-content {
    background: var(--gus-gray-100);
    padding: 11px 14px;
    border-radius: 4px;
    border: 1px solid var(--gus-gray-300);
    font-size: 10.2px;
    line-height: 1.6;
    color: #1A1A1A;
    word-wrap: break-word;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}

.gus-ai-message--user .gus-ai-message-content {
    background: var(--gus-black);
    color: var(--gus-white);
    border-color: var(--gus-black);
}

/* タイピングインジケーター */
.gus-ai-typing {
    display: flex;
    gap: 10px;
    max-width: 90%;
    align-self: flex-start;
}

.gus-ai-typing-dots {
    background: var(--gus-gray-100);
    padding: 11px 14px;
    border-radius: 4px;
    border: 1px solid var(--gus-gray-300);
    display: flex;
    gap: 4px;
    align-items: center;
}

.gus-ai-typing-dot {
    width: 6px;
    height: 6px;
    background: #666666;
    border-radius: 4px;
    animation: typing 1.4s infinite;
}

.gus-ai-typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.gus-ai-typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 80%, 100% {
        transform: scale(0.7);
        opacity: 0.4;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

/* ===============================================
   おすすめ補助金（横スクロール式）
   =============================================== */
.gus-related-section {
    margin: var(--gus-space-2xl) 0;
    padding: var(--gus-space-xl) 0;
    background: linear-gradient(180deg, #FFFBF0 0%, var(--gus-white) 100%);
    border-top: 3px solid var(--gus-yellow);
    border-bottom: 1px solid var(--gus-gray-300);
}

.gus-related-header {
    display: flex;
    align-items: center;
    gap: var(--gus-space-md);
    margin-bottom: var(--gus-space-lg);
    padding-bottom: var(--gus-space-md);
    border-bottom: 2px solid var(--gus-gray-300);
}

.gus-related-icon {
    width: 40px;
    height: 40px;
    background: var(--gus-black);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.gus-related-title {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--gus-black);
    margin: 0;
    letter-spacing: -0.02em;
}

.gus-related-subtitle {
    font-size: 0.875rem;
    color: #666666;
    margin: 8px 0 0 0;
    font-weight: 500;
}

/* 横スクロールコンテナ */
.gus-carousel-container {
    position: relative;
    overflow: hidden;
    padding: 0 40px;
}

.gus-carousel-track {
    display: flex;
    gap: 16px;
    overflow-x: auto;
    scroll-behavior: smooth;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding: 4px 0;
}

.gus-carousel-track::-webkit-scrollbar {
    display: none;
}

.gus-carousel-card {
    flex: 0 0 320px;
    scroll-snap-align: start;
    background: var(--gus-white);
    border: 2px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: block;
}

.gus-carousel-card:hover {
    border-color: var(--gus-yellow);
    box-shadow: 0 8px 24px rgba(255, 215, 0, 0.3);
    transform: translateY(-4px);
}

/* ナビゲーションボタン */
.gus-carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    background: var(--gus-black);
    color: var(--gus-white);
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.gus-carousel-nav:hover {
    background: var(--gus-yellow);
    color: var(--gus-black);
    transform: translateY(-50%) scale(1.1);
}

.gus-carousel-nav-prev {
    left: 0;
}

.gus-carousel-nav-next {
    right: 0;
}

.gus-carousel-nav:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

/* スクロールヒント */
.gus-scroll-hint {
    text-align: center;
    padding: 12px;
    background: linear-gradient(180deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
    border: 1px dashed var(--gus-yellow);
    border-radius: 4px;
    margin-top: 12px;
    font-size: 11px;
    color: #B8860B;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* ===============================================
   テーブルデザイン（PC版）
   =============================================== */
.gus-table-wrapper {
    width: 100%;
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    position: relative;
    margin-bottom: 20px;
    border-radius: var(--gus-radius);
    overflow: hidden;
}

.gus-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    overflow: hidden;
    table-layout: auto;
    min-width: 600px;
    box-sizing: border-box;
}

.gus-table th,
.gus-table td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid var(--gus-gray-200);
    font-size: 11px;
    line-height: 1.8;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
    white-space: normal;
    vertical-align: top;
    box-sizing: border-box;
}

.gus-table th {
    background: linear-gradient(135deg, var(--gus-gray-50) 0%, var(--gus-gray-100) 100%);
    font-weight: 800;
    color: var(--gus-black);
    width: 140px;
    min-width: 140px;
    max-width: 140px;
    white-space: normal;
    border-right: 1px solid var(--gus-gray-300);
}

.gus-table td {
    font-weight: 500;
    color: var(--gus-gray-800);
    width: auto;
}

.gus-table tr:last-child th,
.gus-table tr:last-child td {
    border-bottom: none;
}

.gus-table tr:hover {
    background: var(--gus-gray-50);
}

/* タイトル行 */
.gus-table-title-row {
    background: linear-gradient(135deg, var(--gus-black) 0%, #1a1a1a 100%);
}

.gus-table-title-row:hover {
    background: linear-gradient(135deg, var(--gus-black) 0%, #1a1a1a 100%) !important;
}

.gus-table-title-cell {
    padding: 20px 24px !important;
    text-align: left !important;
    border-right: none !important;
    border-bottom: 2px solid var(--gus-yellow) !important;
    background: transparent !important;
}

.gus-table-title-cell h1 {
    margin: 0;
    padding: 0;
    font-size: 18px;
    font-weight: 800;
    line-height: 1.5;
    color: var(--gus-white);
    text-align: left;
    letter-spacing: 0.01em;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
    max-width: 100%;
    white-space: normal;
}

/* タグ行 */
.gus-table-tags-row {
    background: var(--gus-gray-50);
}

.gus-table-tags-cell {
    padding: 16px 18px !important;
}

.gus-table-tags-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.gus-table-tags-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.gus-table-tags-label {
    font-size: 13px;
    color: #666;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.gus-table-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.gus-table-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    background: var(--gus-white);
    color: #666;
    border: 1px solid var(--gus-gray-300);
    border-radius: 4px;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 600;
}

.gus-table-tag:hover {
    background: var(--gus-yellow);
    color: var(--gus-black);
    border-color: var(--gus-yellow);
    transform: translateY(-1px);
}

/* ===============================================
   モバイルテーブル代替（カード表示・完全版）
   =============================================== */
.gus-mobile-table-card-container {
    display: none;
    gap: var(--gus-space-md);
    flex-direction: column;
}

.gus-mobile-table-card {
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: var(--gus-space-lg);
}

.gus-mobile-table-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed var(--gus-gray-200);
}

.gus-mobile-table-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.gus-mobile-table-label {
    font-size: var(--gus-text-sm);
    font-weight: 700;
    color: var(--gus-gray-700);
    flex-basis: 35%;
    flex-shrink: 0;
}

.gus-mobile-table-value {
    font-size: var(--gus-text-sm);
    font-weight: 500;
    color: var(--gus-black);
    flex-basis: 65%;
    text-align: right;
}

.gus-mobile-table-value strong {
    font-weight: 700;
}

/* ヘッダー */
.gus-header {
    margin-bottom: var(--gus-space-xl);
}

.gus-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--gus-space-md);
    flex-wrap: wrap;
    gap: var(--gus-space-sm);
}

.gus-status-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--gus-space-xs);
    padding: 6px 12px;
    border-radius: 4px;
    font-size: var(--gus-text-sm);
    font-weight: 700;
    text-transform: uppercase;
    min-height: 32px;
}

.gus-status-badge.open {
    background: var(--gus-black);
    color: var(--gus-white);
}

.gus-status-badge.urgent {
    background: #DC2626;
    color: var(--gus-white);
}

.gus-status-badge.closed {
    background: var(--gus-gray-500);
    color: var(--gus-white);
}

.gus-featured-badge {
    background: var(--gus-yellow);
    color: var(--gus-black);
    padding: 6px 12px;
    font-size: var(--gus-text-sm);
    font-weight: 700;
    text-transform: uppercase;
    min-height: 32px;
    display: inline-flex;
    align-items: center;
    border-radius: 4px;
}

.gus-reading-time {
    display: inline-flex;
    align-items: center;
    gap: var(--gus-space-xs);
    color: var(--gus-gray-600);
    font-size: var(--gus-text-sm);
    margin-bottom: var(--gus-space-md);
}

/* セクション */
.gus-section {
    background: var(--gus-gray-50);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: var(--gus-space-xl);
    margin-bottom: var(--gus-space-lg);
    border-left: 3px solid var(--gus-black);
}

.gus-section-header {
    display: flex;
    align-items: center;
    gap: var(--gus-space-sm);
    margin-bottom: var(--gus-space-lg);
    padding-bottom: var(--gus-space-md);
    border-bottom: 1px solid var(--gus-gray-300);
}

.gus-section-icon {
    width: 20px;
    height: 20px;
    opacity: 0.7;
    flex-shrink: 0;
}

.gus-section-title {
    font-size: var(--gus-text-xl);
    font-weight: 700;
    color: var(--gus-black);
    margin: 0;
}

.gus-section-content {
    font-size: var(--gus-text-base);
    color: var(--gus-gray-700);
    line-height: 1.7;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* 難易度 */
.gus-difficulty {
    display: flex;
    align-items: center;
    gap: var(--gus-space-sm);
}

.gus-difficulty-dots {
    display: flex;
    gap: 4px;
}

.gus-difficulty-dot {
    width: 6px;
    height: 6px;
    border-radius: 4px;
    background: var(--gus-gray-300);
}

.gus-difficulty-dot.filled {
    background: var(--gus-black);
}

/* 申請フロー */
.gus-application-flow {
    display: flex;
    flex-direction: column;
    gap: var(--gus-space-md);
}

.gus-flow-step {
    display: flex;
    align-items: center;
    gap: var(--gus-space-lg);
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: var(--gus-space-lg);
    transition: all 0.2s ease;
}

.gus-flow-step:hover {
    border-color: var(--gus-yellow);
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.2);
}

.gus-flow-number {
    width: 48px;
    height: 48px;
    background: var(--gus-black);
    color: var(--gus-white);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--gus-text-xl);
    font-weight: 800;
    flex-shrink: 0;
}

.gus-flow-content h3 {
    font-size: var(--gus-text-lg);
    font-weight: 700;
    margin-bottom: var(--gus-space-xs);
}

.gus-flow-content p {
    font-size: var(--gus-text-sm);
    color: var(--gus-gray-600);
    margin: 0;
}

.gus-flow-arrow {
    text-align: center;
    font-size: var(--gus-text-2xl);
    color: var(--gus-gray-500);
}

/* FAQ */
.gus-faq {
    display: flex;
    flex-direction: column;
    gap: var(--gus-space-md);
}

.gus-faq-item {
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    padding: var(--gus-space-lg);
    transition: all 0.2s ease;
}

.gus-faq-item:hover {
    border-color: var(--gus-yellow);
}

.gus-faq-question {
    font-size: var(--gus-text-md);
    font-weight: 700;
    color: var(--gus-black);
    cursor: pointer;
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
}

.gus-faq-question::after {
    content: '+';
    font-size: var(--gus-text-2xl);
    font-weight: 700;
    transition: transform var(--gus-transition);
    flex-shrink: 0;
    margin-left: var(--gus-space-md);
}

.gus-faq-item[open] .gus-faq-question::after {
    transform: rotate(45deg);
}

.gus-faq-answer {
    margin-top: var(--gus-space-md);
    padding-top: var(--gus-space-md);
    border-top: 1px solid var(--gus-gray-200);
    color: var(--gus-gray-700);
    line-height: 1.7;
}

/* ボタン */
.gus-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: var(--gus-radius);
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
    width: 100%;
    min-height: 44px;
    text-align: center;
    letter-spacing: 0.3px;
}

.gus-btn-primary {
    background: var(--gus-yellow);
    color: var(--gus-black);
    border: 2px solid var(--gus-yellow);
}

.gus-btn-primary:hover {
    background: var(--gus-black);
    color: var(--gus-yellow);
    border-color: var(--gus-black);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.gus-btn-secondary {
    background: var(--gus-white);
    color: var(--gus-black);
    border: 1px solid var(--gus-gray-300);
}

.gus-btn-secondary:hover {
    border-color: var(--gus-black);
    background: var(--gus-gray-50);
    transform: translateY(-1px);
}

.gus-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* 目次 */
.gus-toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gus-toc-item {
    margin-bottom: 4px;
}

.gus-toc-link {
    display: block;
    padding: 8px 10px;
    color: #666666;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    border-left: 2px solid transparent;
    border-radius: 4px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.gus-toc-link:hover,
.gus-toc-link.active {
    color: var(--gus-black);
    background: var(--gus-gray-100);
    border-left-color: var(--gus-yellow);
    padding-left: 14px;
    font-weight: 600;
}

/* 関連コラム（サイドバー版） */
.gus-related-columns-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.gus-related-column-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px;
    background: #F9FAFB;
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    gap: 12px;
}

.gus-related-column-item:hover {
    background: var(--gus-white);
    border-color: var(--gus-yellow);
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.2);
    transform: translateX(2px);
}

.gus-related-column-content {
    flex: 1;
    min-width: 0;
}

.gus-related-column-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--gus-black);
    margin-bottom: 6px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.gus-related-column-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: #6B7280;
}

.gus-related-column-arrow {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9CA3AF;
    transition: all 0.2s ease;
}

.gus-related-column-item:hover .gus-related-column-arrow {
    color: var(--gus-yellow);
    transform: translateX(2px);
}

.gus-view-all-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 12px;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 600;
    color: var(--gus-black);
    background: #F3F4F6;
    border-radius: var(--gus-radius);
    text-decoration: none;
    transition: all 0.2s ease;
}

.gus-view-all-link:hover {
    background: var(--gus-yellow);
    transform: translateX(2px);
}

/* 関連コラム記事セクション */
.gus-related-columns-section {
    margin-top: var(--gus-space-2xl);
    margin-bottom: var(--gus-space-2xl);
    padding: var(--gus-space-2xl) 0;
    background: linear-gradient(180deg, var(--gus-gray-50) 0%, var(--gus-white) 100%);
    border-top: 3px solid var(--gus-gray-300);
}

.gus-related-section-header {
    display: flex;
    align-items: flex-start;
    gap: var(--gus-space-md);
    margin-bottom: var(--gus-space-xl);
}

.gus-related-section-icon {
    width: 40px;
    height: 40px;
    background: var(--gus-black);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.gus-related-section-title {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--gus-black);
    margin: 0 0 8px 0;
    letter-spacing: -0.02em;
}

.gus-related-columns-intro p {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
    color: #666666;
}

.gus-related-columns-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: var(--gus-space-xl);
}

.gus-related-column-card {
    display: block;
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    height: 100%;
}

.gus-related-column-card:hover {
    border-color: var(--gus-yellow);
    box-shadow: 0 8px 24px rgba(255, 215, 0, 0.2);
    transform: translateY(-4px);
}

.gus-related-column-thumbnail {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    overflow: hidden;
    background: linear-gradient(135deg, #F5F5F5 0%, var(--gus-gray-300) 100%);
}

.gus-related-column-thumbnail img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.gus-related-column-card:hover .gus-related-column-thumbnail img {
    transform: scale(1.08);
}

.gus-related-column-card-content {
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.gus-related-column-card-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.gus-related-column-category,
.gus-related-column-read-time {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #999999;
    font-weight: 600;
}

.gus-related-column-card-title {
    margin: 0;
    font-size: 16px;
    line-height: 1.5;
    font-weight: 700;
    color: #1a1a1a;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 48px;
}

.gus-related-column-card:hover .gus-related-column-card-title {
    color: var(--gus-black);
}

.gus-related-column-card-excerpt {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
    color: #666666;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.gus-related-columns-footer {
    margin-top: var(--gus-space-2xl);
    text-align: center;
    padding-top: var(--gus-space-xl);
    border-top: 2px solid var(--gus-gray-300);
}

/* ソーシャルシェア */
.gus-social-share {
    margin-top: var(--gus-space-2xl);
    padding: var(--gus-space-xl);
    background: var(--gus-gray-50);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
}

.gus-social-share h3 {
    font-size: var(--gus-text-lg);
    font-weight: 700;
    margin-bottom: var(--gus-space-md);
}

.gus-social-buttons {
    display: flex;
    gap: var(--gus-space-sm);
    flex-wrap: wrap;
}

.gus-social-buttons .gus-btn {
    width: auto;
    flex: 1;
    min-width: 120px;
}

/* ===============================================
   共通CTAバナー（統一版）
   =============================================== */
.gus-unified-cta-section {
    background: linear-gradient(135deg, var(--gus-black) 0%, #2d2d2d 100%);
    color: #ffffff !important;
    padding: 48px 0;
    position: relative;
    overflow: hidden;
    margin: var(--gus-space-2xl) 0;
}

.gus-unified-cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gus-yellow) 0%, var(--gus-yellow-dark) 100%);
}

.gus-unified-cta-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--gus-space-xl);
}

.gus-unified-cta-content {
    text-align: center;
}

.gus-unified-cta-title {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: var(--gus-space-md);
    color: var(--gus-white) !important;
}

.gus-unified-cta-description {
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: var(--gus-space-xl);
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    color: var(--gus-white) !important;
}

.gus-unified-cta-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--gus-space-lg);
    max-width: 900px;
    margin: 0 auto;
}

.gus-unified-cta-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--gus-space-md);
    padding: 20px 28px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: var(--gus-radius);
    transition: all 0.3s ease;
    min-height: 80px;
}

.gus-unified-cta-btn-primary {
    background: var(--gus-yellow);
    color: var(--gus-black) !important;
    border: 2px solid var(--gus-yellow);
}

.gus-unified-cta-btn-primary:hover {
    background: var(--gus-black);
    color: var(--gus-yellow) !important;
    border-color: var(--gus-yellow);
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(255, 215, 0, 0.3);
}

.gus-unified-cta-btn-secondary {
    background: #ffffff;
    color: var(--gus-black) !important;
    border: 2px solid var(--gus-gray-300);
}

.gus-unified-cta-btn-secondary:hover {
    background: var(--gus-yellow);
    color: var(--gus-black) !important;
    border-color: var(--gus-yellow);
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(255, 215, 0, 0.3);
}

/* パンくずナビ */
.gus-breadcrumb {
    margin-top: var(--gus-space-2xl);
    padding: var(--gus-space-lg);
    background: var(--gus-white);
    border: 1px solid var(--gus-gray-300);
    border-radius: var(--gus-radius);
}

.gus-breadcrumb ol {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: var(--gus-space-sm);
    font-size: var(--gus-text-sm);
}

.gus-breadcrumb li {
    display: flex;
    align-items: center;
}

.gus-breadcrumb a {
    color: var(--gus-gray-700);
    text-decoration: none;
    transition: color 0.2s ease;
}

.gus-breadcrumb a:hover {
    color: var(--gus-black);
    text-decoration: underline;
}

/* 検索ページスタイルのカテゴリー・地域セクション */
.gus-search-style-section {
    margin-top: 32px;
}

.gus-search-section-box {
    background: var(--gus-white);
    border: 1px solid var(--gus-black);
    border-radius: var(--gus-radius);
    padding: 20px;
    margin-bottom: 16px;
}

.gus-search-box-title {
    font-size: 13.6px;
    font-weight: 700;
    color: var(--gus-black);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--gus-gray-300);
}

.gus-search-box-title i {
    font-size: 13.6px;
    color: #666666;
}

.gus-search-links {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.gus-search-link {
    display: inline-block;
    padding: 6px 12px;
    font-size: 11px;
    font-weight: 600;
    color: var(--gus-black);
    background: var(--gus-white);
    border: 1px solid var(--gus-black);
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.gus-search-link:hover {
    background: var(--gus-yellow);
    transform: translateY(-1px);
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* ===============================================
   モバイルフローティングAIボタン（v24.2）
   =============================================== */
.gus-mobile-ai-floating-btn {
    display: none;
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--gus-yellow) 0%, var(--gus-yellow-dark) 100%);
    color: var(--gus-black);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    z-index: 999;
    box-shadow: 0 8px 24px rgba(255, 215, 0, 0.5);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    align-items: center;
    justify-content: center;
}

.gus-mobile-ai-floating-btn:hover {
    background: linear-gradient(135deg, var(--gus-black) 0%, #1a1a1a 100%);
    color: var(--gus-yellow);
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
}

.gus-mobile-ai-floating-btn:active {
    transform: scale(0.95);
}

/* ===============================================
   モバイルパネル（v24.2）
   =============================================== */
.gus-mobile-panel-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 998;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.gus-mobile-panel-overlay.active {
    opacity: 1;
    visibility: visible;
}

.gus-mobile-panel {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--gus-white);
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    padding: 0;
    max-height: 85vh;
    overflow: hidden;
    z-index: 1000;
    transform: translateY(100%);
    visibility: hidden;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
}

.gus-mobile-panel.active {
    transform: translateY(0);
    visibility: visible;
}

/* パネルヘッダー（黒・ゴールド統一） */
.gus-mobile-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 20px 16px 20px;
    background: linear-gradient(135deg, var(--gus-black) 0%, #1a1a1a 100%);
    border-bottom: 3px solid var(--gus-yellow);
    flex-shrink: 0;
    position: relative;
}

.gus-mobile-panel-header::before {
    content: '';
    position: absolute;
    top: 8px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 4px;
    background: rgba(255, 215, 0, 0.3);
    border-radius: 2px;
}

.gus-mobile-panel-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--gus-white);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.02em;
}

.gus-mobile-panel-title svg {
    width: 24px;
    height: 24px;
    stroke: var(--gus-yellow);
}

.gus-mobile-panel-close {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--gus-white);
    cursor: pointer;
    padding: 8px;
    line-height: 1;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 20px;
    transition: all 0.2s ease;
}

.gus-mobile-panel-close:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

/* AIパネル専用スタイル */
.gus-ai-panel {
    display: flex;
    flex-direction: column;
    height: 100%;
    max-height: calc(85vh - 80px);
    overflow: hidden;
}

/* メッセージエリア */
.gus-ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: linear-gradient(180deg, #FAFAFA 0%, var(--gus-white) 100%);
    min-height: 300px;
    max-height: calc(85vh - 280px);
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.gus-ai-chat-messages::-webkit-scrollbar {
    width: 4px;
}

.gus-ai-chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.gus-ai-chat-messages::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 2px;
}

/* メッセージバブル（モバイル専用） */
#mobileAIPanel .gus-ai-message-avatar {
    width: 36px;
    height: 36px;
    border-radius: 8px;
}

#mobileAIPanel .gus-ai-message--assistant .gus-ai-message-avatar {
    background: linear-gradient(135deg, var(--gus-black) 0%, #333333 100%);
    color: var(--gus-white);
}

#mobileAIPanel .gus-ai-message--user .gus-ai-message-avatar {
    background: linear-gradient(135deg, var(--gus-yellow) 0%, var(--gus-yellow-dark) 100%);
    color: var(--gus-black);
}

#mobileAIPanel .gus-ai-message-content {
    background: var(--gus-white);
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid var(--gus-gray-300);
    font-size: 15px;
    line-height: 1.6;
    color: var(--gus-black);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

#mobileAIPanel .gus-ai-message--user .gus-ai-message-content {
    background: var(--gus-black);
    color: var(--gus-white);
    border-color: var(--gus-black);
}

/* タイピングインジケーター（モバイル専用） */
#mobileAIPanel .gus-ai-typing-dots {
    background: var(--gus-white);
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid var(--gus-gray-300);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

#mobileAIPanel .gus-ai-typing-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

/* 入力エリア（モバイル専用） */
.gus-ai-input-container {
    padding: 16px;
    background: var(--gus-white);
    border-top: 2px solid var(--gus-gray-300);
    flex-shrink: 0;
}

.gus-ai-input-wrapper {
    display: flex;
    gap: 12px;
    align-items: flex-end;
    margin-bottom: 12px;
}

.gus-ai-input {
    flex: 1;
    padding: 14px 16px;
    border: 2px solid var(--gus-gray-300);
    border-radius: 12px;
    font-size: 16px !important;
    font-family: inherit;
    min-height: 52px;
    max-height: 120px;
    resize: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background: var(--gus-gray-50);
    color: var(--gus-black);
    line-height: 1.5;
}

.gus-ai-input:focus {
    outline: none;
    border-color: var(--gus-yellow);
    background: var(--gus-white);
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.2);
}

.gus-ai-input::placeholder {
    color: var(--gus-gray-500);
    font-size: 15px;
}

.gus-ai-send {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, var(--gus-yellow) 0%, var(--gus-yellow-dark) 100%);
    color: var(--gus-black);
    border: none;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
}

.gus-ai-send:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--gus-black) 0%, #1a1a1a 100%);
    color: var(--gus-yellow);
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.gus-ai-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    box-shadow: none;
}

/* サジェスチョンチップ（モバイル専用） */
.gus-ai-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.gus-ai-suggestion {
    padding: 10px 16px;
    background: var(--gus-white);
    color: var(--gus-black);
    border: 2px solid var(--gus-gray-300);
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
    min-height: 40px;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.gus-ai-suggestion:hover {
    background: var(--gus-yellow);
    color: var(--gus-black);
    border-color: var(--gus-yellow);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
}

.gus-ai-suggestion:active {
    transform: translateY(0);
}

/* Affiliate Ad Space */
.gus-sidebar-ad-space {
    background: var(--gus-gray-50) !important;
    border: 2px dashed var(--gus-gray-300) !important;
    border-radius: var(--gus-radius) !important;
    padding: 16px !important;
    min-height: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.gus-sidebar-ad-space:empty {
    display: none;
}

/* アクセシビリティ */
.gus-btn:focus-visible,
.gus-toc-link:focus-visible,
.gus-mobile-ai-floating-btn:focus-visible,
.gus-ai-send:focus-visible,
.gus-ai-suggestion:focus-visible,
.gus-mobile-panel-close:focus-visible {
    outline: 3px solid var(--gus-yellow);
    outline-offset: 2px;
}

/* ダークモード対応（オプション） */
@media (prefers-color-scheme: dark) {
    .gus-mobile-panel {
        background: #1a1a1a;
    }
    
    .gus-ai-chat-messages {
        background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 100%);
    }
    
    .gus-ai-message-content {
        background: #2a2a2a;
        border-color: #3a3a3a;
        color: var(--gus-white);
    }
    
    .gus-ai-input {
        background: #2a2a2a;
        border-color: #3a3a3a;
        color: var(--gus-white);
    }
    
    .gus-ai-suggestion {
        background: #2a2a2a;
        border-color: #3a3a3a;
        color: var(--gus-white);
    }
}

/* ===============================================
   レスポンシブ
   =============================================== */
@media (max-width: 1024px) {
    .gus-layout {
        display: flex;
        flex-direction: column;
    }
    
    .gus-sidebar {
        position: static;
        width: 100%;
        max-width: 100%;
        margin-top: var(--gus-space-2xl);
    }
    
    .gus-main {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }
    
    .gus-pc-ai-permanent {
        display: none !important;
    }
    
    .gus-related-columns-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .gus-carousel-card {
        flex: 0 0 280px;
    }
}

@media (max-width: 768px) {
    .gus-single {
        padding: var(--gus-space-lg) var(--gus-space-md);
        padding-bottom: 100px;
    }
    
    .gus-table-wrapper {
        display: none !important; 
    }
    
    .gus-mobile-table-card-container {
        display: flex !important; 
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .gus-mobile-ai-floating-btn {
        display: flex !important;
    }
    
    .gus-related-columns-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .gus-carousel-container {
        padding: 0 20px;
    }
    
    .gus-carousel-card {
        flex: 0 0 260px;
    }
    
    .gus-carousel-nav {
        width: 28px;
        height: 28px;
    }
    
    .gus-search-section-box {
        padding: 16px;
        margin-bottom: 12px;
    }
    
    .gus-search-box-title {
        font-size: 11.9px;
        margin-bottom: 12px;
        padding-bottom: 10px;
    }
    
    .gus-search-links {
        gap: 6px;
    }
    
    .gus-search-link {
        padding: 5px 10px;
        font-size: 10.2px;
    }
    
    .gus-unified-cta-buttons {
        grid-template-columns: 1fr;
        gap: var(--gus-space-md);
    }
}

@media (max-width: 480px) {
    .gus-mobile-table-card {
        padding: var(--gus-space-md);
    }
    
    .gus-carousel-card {
        flex: 0 0 240px;
    }
    
    .gus-mobile-ai-floating-btn {
        width: 56px;
        height: 56px;
        bottom: 20px;
        right: 20px;
    }
}

/* プリント */
@media print {
    .gus-sidebar,
    .gus-actions,
    .gus-social-share,
    .gus-mobile-ai-floating-btn,
    .gus-pc-ai-permanent,
    .gus-carousel-nav {
        display: none !important;
    }
    
    .gus-layout {
        grid-template-columns: 1fr;
    }
    
    .gus-mobile-table-card-container {
        display: none !important;
    }
    
    .gus-table-wrapper {
        display: block !important;
    }
}
</style>

<main class="gus-single" itemscope itemtype="https://schema.org/Article">
    <div class="gus-layout">
        <!-- メインコンテンツ -->
        <article class="gus-main">
            <!-- ヘッダー -->
            <header class="gus-header">
                <div class="gus-header-top">
                    <div class="gus-status-badge <?php echo $status_data['class']; ?> <?php echo $deadline_class; ?>">
                        <?php echo $status_data['label']; ?>
                        <?php if ($days_remaining > 0 && $days_remaining <= 30): ?>
                            · <?php echo $days_remaining; ?>日
                        <?php endif; ?>
                    </div>
                    <?php if ($grant_data['is_featured']): ?>
                        <div class="gus-featured-badge">
                            注目
                        </div>
                    <?php endif; ?>
                </div>
                <div class="gus-reading-time">
                    <span>■</span>
                    <span>読了時間: 約<?php echo $reading_time; ?>分</span>
                    <span>·</span>
                    <time datetime="<?php echo $published_date; ?>" itemprop="datePublished">
                        更新: <?php echo get_the_modified_date('Y年n月j日'); ?>
                    </time>
                </div>
            </header>

            <!-- AI要約 -->
            <?php if ($grant_data['ai_summary']): ?>
            <section id="ai-summary" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <h2 class="gus-section-title">AI要約</h2>
                </header>
                <div class="gus-section-content">
                    <p><?php echo esc_html($grant_data['ai_summary']); ?></p>
                </div>
            </section>
            <?php endif; ?>

            <!-- おすすめ補助金（横スクロール式・上部配置） -->
            <?php if (!empty($scored_related_grants)): ?>
            <section id="related" class="gus-related-section">
                <header class="gus-related-header">
                    <div class="gus-related-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <h2 class="gus-related-title">
                            あなたにおすすめの補助金
                            <span style="display: inline-block; margin-left: 12px; padding: 4px 10px; background: var(--gus-yellow); color: var(--gus-black); font-size: 12px; font-weight: 800; border-radius: 4px;">
                                AI選定
                            </span>
                        </h2>
                        <p class="gus-related-subtitle">
                            同じ市町村・都道府県・カテゴリの補助金を優先表示（<?php echo count($scored_related_grants); ?>件のマッチ）
                        </p>
                    </div>
                </header>
                
                <div class="gus-scroll-hint">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                    左右にスワイプして<?php echo min(count($scored_related_grants), 12); ?>件のおすすめ補助金を見る
                </div>
                
                <div class="gus-carousel-container">
                    <button class="gus-carousel-nav gus-carousel-nav-prev" id="carouselPrev" type="button" aria-label="前へ">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>
                    
                    <div class="gus-carousel-track" id="carouselTrack">
                        <?php
                        $display_count = 0;
                        foreach ($scored_related_grants as $related_grant):
                            if ($display_count >= 12) break;
                            $display_count++;
                            $related_id = $related_grant['id'];
                            $post = get_post($related_id);
                            if ($post) {
                                setup_postdata($post);
                                ?>
                                <div class="gus-carousel-card">
                                    <?php get_template_part('template-parts/grant-card-unified'); ?>
                                </div>
                                <?php
                            }
                        endforeach;
                        wp_reset_postdata();
                        ?>
                    </div>
                    
                    <button class="gus-carousel-nav gus-carousel-nav-next" id="carouselNext" type="button" aria-label="次へ">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
                
                <?php if (count($scored_related_grants) > 12): ?>
                <div style="margin-top: 24px; text-align: center;">
                    <a href="<?php echo home_url('/grants/'); ?>" class="gus-btn gus-btn-secondary" style="display: inline-flex; width: auto; min-width: 240px;">
                        さらに補助金を探す (残り<?php echo count($scored_related_grants) - 12; ?>件)
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <!-- 補助金詳細表（PC版テーブル + モバイル版カード完全版） -->
            <section id="grant-details" class="gus-section">
                <div class="gus-section-content">
                    
                    <!-- PC版テーブル（768px以上で表示） -->
                    <div class="gus-table-wrapper">
                        <table class="gus-table">
                            <thead>
                                <tr class="gus-table-title-row">
                                    <th colspan="2" class="gus-table-title-cell">
                                        <h1 itemprop="headline"><?php the_title(); ?></h1>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($formatted_amount): ?>
                                <tr>
                                    <th>補助金額<div style="font-size: 10px; font-weight: 500; color: #aaa;">(最大)</div></th>
                                    <td><strong style="font-size: var(--gus-text-xl); color: #DC2626;"><?php echo esc_html($formatted_amount); ?></strong></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if ($grant_data['subsidy_rate']): ?>
                                <tr>
                                    <th>補助率</th>
                                    <td><?php echo esc_html($grant_data['subsidy_rate']); ?>
                                        <?php if ($grant_data['subsidy_rate_detailed']): ?>
                                            <div style="font-size: var(--gus-text-xs); color: var(--gus-gray-600); margin-top: 4px;"><?php echo wp_kses_post($grant_data['subsidy_rate_detailed']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if ($deadline_info): ?>
                                <tr>
                                    <th>申請締切</th>
                                    <td>
                                        <strong style="<?php echo $deadline_class === 'urgent' ? 'color: #DC2626;' : ($deadline_class === 'warning' ? 'color: #F59E0B;' : ''); ?>">
                                            <?php echo esc_html($deadline_info); ?>
                                        </strong>
                                        <?php if ($grant_data['application_period']): ?>
                                            <div style="font-size: var(--gus-text-sm); color: var(--gus-gray-600); margin-top: 4px;">期間: <?php echo esc_html($grant_data['application_period']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                
                                <tr>
                                    <th>難易度 / 採択率</th>
                                    <td>
                                        <div class="gus-difficulty" style="margin-bottom: 4px;">
                                            <strong><?php echo $difficulty_data['label']; ?></strong>
                                            <div class="gus-difficulty-dots">
                                                <?php for ($i = 1; $i <= 3; $i++): ?>
                                                    <div class="gus-difficulty-dot <?php echo $i <= $difficulty_data['dots'] ? 'filled' : ''; ?>"></div>
                                                <?php endfor; ?>
                                            </div>
                                            <span style="font-size: var(--gus-text-sm); color: var(--gus-gray-600);">(<?php echo $difficulty_data['description']; ?>)</span>
                                        </div>
                                        <?php if ($grant_data['adoption_rate'] > 0): ?>
                                            <div style="font-size: var(--gus-text-sm); color: var(--gus-gray-700);">採択実績: <strong><?php echo number_format($grant_data['adoption_rate'], 1); ?>%</strong></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                
                                <?php if ($grant_data['organization']): ?>
                                <tr>
                                    <th>主催機関</th>
                                    <td><?php echo esc_html($grant_data['organization']); ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if ($grant_data['grant_target']): ?>
                                <tr>
                                    <th>対象者・対象事業</th>
                                    <td><?php echo wp_kses_post($grant_data['grant_target']); ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php $expenses_content = !empty($grant_data['eligible_expenses_detailed']) ? $grant_data['eligible_expenses_detailed'] : $grant_data['eligible_expenses']; ?>
                                <?php if ($expenses_content): ?>
                                <tr>
                                    <th>対象経費</th>
                                    <td><?php echo wp_kses_post($expenses_content); ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php $documents_content = !empty($grant_data['required_documents_detailed']) ? $grant_data['required_documents_detailed'] : $grant_data['required_documents']; ?>
                                <?php if ($documents_content): ?>
                                <tr>
                                    <th>必要書類</th>
                                    <td><?php echo wp_kses_post($documents_content); ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if ($prefecture_display || $municipality_display): ?>
                                <tr>
                                    <th>対象地域</th>
                                    <td>
                                        <?php echo esc_html($prefecture_display); ?>
                                        <?php if ($municipality_display): ?>
                                            <br><span style="font-size: var(--gus-text-sm); color: var(--gus-gray-600);">(市町村: <?php echo esc_html($municipality_display); ?>)</span>
                                        <?php endif; ?>
                                        <?php if ($grant_data['area_notes']): ?>
                                            <div style="font-size: var(--gus-text-xs); color: #B8860B; margin-top: 4px;"><?php echo esc_html($grant_data['area_notes']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if ($grant_data['application_method']): ?>
                                <tr>
                                    <th>申請方法</th>
                                    <td><?php echo nl2br(esc_html($grant_data['application_method'])); ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <tr>
                                    <th>更新状況</th>
                                    <td>
                                        最終更新: <?php echo get_the_modified_date('Y年n月j日'); ?> / 閲覧数: <?php echo number_format($grant_data['views_count']); ?> 回
                                    </td>
                                </tr>
                            </tbody>
                            
                            <!-- タグ行 -->
                            <?php if ($taxonomies['categories'] || $taxonomies['tags']): ?>
                            <tfoot>
                                <tr class="gus-table-tags-row">
                                    <td colspan="2" class="gus-table-tags-cell">
                                        <div class="gus-table-tags-container">
                                            <?php if ($taxonomies['categories']): ?>
                                            <div class="gus-table-tags-section">
                                                <div class="gus-table-tags-label">カテゴリー</div>
                                                <div class="gus-table-tags">
                                                    <?php foreach ($taxonomies['categories'] as $cat): ?>
                                                        <a href="<?php echo get_term_link($cat); ?>" class="gus-table-tag">
                                                            <?php echo esc_html($cat->name); ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($taxonomies['tags']): ?>
                                            <div class="gus-table-tags-section">
                                                <div class="gus-table-tags-label">タグ</div>
                                                <div class="gus-table-tags">
                                                    <?php foreach ($taxonomies['tags'] as $tag): ?>
                                                        <a href="<?php echo get_term_link($tag); ?>" class="gus-table-tag">
                                                            #<?php echo esc_html($tag->name); ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                    
                    <!-- モバイル版カード（768px以下で表示・ACF全項目対応） -->
                    <div class="gus-mobile-table-card-container">
                        <!-- 基本情報カード -->
                        <div class="gus-mobile-table-card">
                            <h2 style="font-size: 18px; font-weight: 800; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--gus-yellow);">
                                基本情報
                            </h2>
                            
                            <?php if ($formatted_amount): ?>
                            <div class="gus-mobile-table-row">
                                <div class="gus-mobile-table-label">補助金額（最大）</div>
                                <div class="gus-mobile-table-value" style="font-size: 16px; font-weight: 700; color: #DC2626;">
                                    <?php echo esc_html($formatted_amount); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($grant_data['organization']): ?>
                            <div class="gus-mobile-table-row">
                                <div class="gus-mobile-table-label">主催機関</div>
                                <div class="gus-mobile-table-value"><?php echo esc_html($grant_data['organization']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($deadline_info): ?>
                            <div class="gus-mobile-table-row">
                                <div class="gus-mobile-table-label">申請締切</div>
                                <div class="gus-mobile-table-value" style="<?php echo $deadline_class === 'urgent' ? 'color: #DC2626;' : ($deadline_class === 'warning' ? 'color: #F59E0B;' : ''); ?>">
                                    <strong><?php echo esc_html($deadline_info); ?></strong>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($grant_data['application_period']): ?>
                            <div class="gus-mobile-table-row">
                                <div class="gus-mobile-table-label">申請期間</div>
                                <div class="gus-mobile-table-value"><?php echo esc_html($grant_data['application_period']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($grant_data['subsidy_rate']): ?>
                            <div class="gus-mobile-table-row">
                                <div class="gus-mobile-table-label">補助率</div>
                                <div class="gus-mobile-table-value"><?php echo esc_html($grant_data['subsidy_rate']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="gus-mobile-table-row">
                                <div class="gus-mobile-table-label">難易度 / 採択率</div>
                                <div class="gus-mobile-table-value">
                                    <?php echo $difficulty_data['label']; ?> (<?php echo $difficulty_data['description']; ?>)
                                    <?php if ($grant_data['adoption_rate'] > 0): ?>
                                        <br>採択実績: <strong><?php echo number_format($grant_data['adoption_rate'], 1); ?>%</strong>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($prefecture_display): ?>
                            <div class="gus-mobile-table-row">
                                <div class="gus-mobile-table-label">対象地域</div>
                                <div class="gus-mobile-table-value"><?php echo esc_html($prefecture_display); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($municipality_display): ?>
                            <div class="gus-mobile-table-row">
                                <div class="gus-mobile-table-label">対象市町村</div>
                                <div class="gus-mobile-table-value"><?php echo esc_html($municipality_display); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($grant_data['area_notes']): ?>
                            <div class="gus-mobile-table-row" style="padding-bottom: 0;">
                                <div class="gus-mobile-table-label" style="font-weight: 500;">地域備考</div>
                                <div class="gus-mobile-table-value" style="color: #B8860B; text-align: left; padding-left: 10px;"><?php echo esc_html($grant_data['area_notes']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- 対象情報カード -->
                        <?php if ($grant_data['grant_target'] || $expenses_content || $documents_content): ?>
                        <div class="gus-mobile-table-card">
                            <h3 style="font-size: 16px; font-weight: 800; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--gus-black);">
                                対象情報
                            </h3>
                            <?php if ($grant_data['grant_target']): ?>
                            <div class="gus-mobile-table-row" style="flex-direction: column; align-items: flex-start;">
                                <div class="gus-mobile-table-label" style="margin-bottom: 4px; color: var(--gus-black);">対象者・対象事業</div>
                                <div class="gus-mobile-table-value" style="text-align: left; width: 100%;"><?php echo wp_kses_post($grant_data['grant_target']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($expenses_content): ?>
                            <div class="gus-mobile-table-row" style="flex-direction: column; align-items: flex-start;">
                                <div class="gus-mobile-table-label" style="margin-bottom: 4px; color: var(--gus-black);">対象経費</div>
                                <div class="gus-mobile-table-value" style="text-align: left; width: 100%;"><?php echo wp_kses_post($expenses_content); ?></div>
                            </div>
                            <?php endif; ?>

                            <?php if ($documents_content): ?>
                            <div class="gus-mobile-table-row" style="flex-direction: column; align-items: flex-start; border-bottom: none;">
                                <div class="gus-mobile-table-label" style="margin-bottom: 4px; color: var(--gus-black);">必要書類</div>
                                <div class="gus-mobile-table-value" style="text-align: left; width: 100%;"><?php echo wp_kses_post($documents_content); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>              

 <!-- 申請方法カード -->
                        <?php if ($grant_data['application_method']): ?>
                        <div class="gus-mobile-table-card">
                            <h3 style="font-size: 16px; font-weight: 800; margin-top: 0; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--gus-black);">
                                申請方法
                            </h3>
                            <div class="gus-mobile-table-row" style="flex-direction: column; align-items: flex-start; border-bottom: none;">
                                <div class="gus-mobile-table-value" style="text-align: left; width: 100%; font-weight: 500;"><?php echo nl2br(esc_html($grant_data['application_method'])); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- 関連キーワードカード -->
                        <?php if ($taxonomies['categories'] || $taxonomies['tags']): ?>
                        <div class="gus-mobile-table-card">
                            <h3 class="gus-sidebar-title" style="margin-top: 0;">関連キーワード</h3>
                            <div class="gus-table-tags-container" style="flex-direction: row; flex-wrap: wrap; gap: 8px;">
                                <?php foreach ($taxonomies['categories'] as $cat): ?>
                                    <a href="<?php echo get_term_link($cat); ?>" class="gus-table-tag" style="font-size: 10px; padding: 6px 10px; border-radius: 4px;">
                                        <?php echo esc_html($cat->name); ?>
                                    </a>
                                <?php endforeach; ?>
                                <?php foreach ($taxonomies['tags'] as $tag): ?>
                                    <a href="<?php echo get_term_link($tag); ?>" class="gus-table-tag" style="font-size: 10px; padding: 6px 10px; border-radius: 4px;">
                                        #<?php echo esc_html($tag->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- 詳細情報 -->
            <section id="details" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <h2 class="gus-section-title">この補助金の詳細情報</h2>
                </header>
                <div class="gus-section-content">
                    <?php
                    $full_content = get_the_content();
                    $full_content = apply_filters('the_content', $full_content);
                    echo $full_content;
                    ?>
                </div>
            </section>

            <!-- 申請の流れ -->
            <section id="application-flow" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <h2 class="gus-section-title">申請の流れ</h2>
                </header>
                <div class="gus-section-content">
                    <div class="gus-application-flow">
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">1</div>
                            <div class="gus-flow-content">
                                <h3>必要書類の準備</h3>
                                <p>事業計画書、見積書などを用意します。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">2</div>
                            <div class="gus-flow-content">
                                <h3>申請書類の提出</h3>
                                <p>オンラインまたは郵送で提出します。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">3</div>
                            <div class="gus-flow-content">
                                <h3>審査</h3>
                                <p>通常1〜2ヶ月程度かかります。</p>
                            </div>
                        </div>
                        <div class="gus-flow-arrow">↓</div>
                        <div class="gus-flow-step">
                            <div class="gus-flow-number">4</div>
                            <div class="gus-flow-content">
                                <h3>採択・交付決定</h3>
                                <p>結果通知と交付手続きを行います。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- よくある質問 -->
            <section id="faq" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <h2 class="gus-section-title">よくある質問</h2>
                </header>
                <div class="gus-section-content">
                    <div class="gus-faq">
                        <?php if ($grant_data['grant_target']): ?>
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">この補助金の対象者は誰ですか？</summary>
                            <div class="gus-faq-answer">
                                <?php echo wp_kses_post($grant_data['grant_target']); ?>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <?php $documents_display = !empty($grant_data['required_documents_detailed']) ? $grant_data['required_documents_detailed'] : $grant_data['required_documents']; ?>
                        <?php if ($documents_display): ?>
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">申請に必要な書類は何ですか？</summary>
                            <div class="gus-faq-answer">
                                <?php echo wp_kses_post($documents_display); ?>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <?php $expenses_display = !empty($grant_data['eligible_expenses_detailed']) ? $grant_data['eligible_expenses_detailed'] : $grant_data['eligible_expenses']; ?>
                        <?php if ($expenses_display): ?>
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">どのような経費が対象になりますか？</summary>
                            <div class="gus-faq-answer">
                                <?php echo wp_kses_post($expenses_display); ?>
                            </div>
                        </details>
                        <?php endif; ?>
                        
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">申請から採択までどのくらいかかりますか？</summary>
                            <div class="gus-faq-answer">
                                通常、申請から採択決定まで1〜2ヶ月程度かかります。ただし、補助金の種類や申請時期によって異なる場合がありますので、詳しくは担当窓口にお問い合わせください。
                            </div>
                        </details>
                        
                        <details class="gus-faq-item">
                            <summary class="gus-faq-question">不採択になった場合、再申請は可能ですか？</summary>
                            <div class="gus-faq-answer">
                                多くの場合、次回の募集期間で再申請が可能です。不採択の理由を確認し、改善した上で再度申請することをお勧めします。
                            </div>
                        </details>
                    </div>
                </div>
            </section>

            <!-- 関連コラム記事 -->
            <?php if ($related_columns_query && $related_columns_query->have_posts()): ?>
            <section id="related-columns" class="gus-related-columns-section">
                <header class="gus-related-section-header">
                    <div class="gus-related-section-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <h2 class="gus-related-section-title">詳しい記事</h2>
                        <div class="gus-related-columns-intro">
                            <p>この補助金について、さらに詳しく解説している記事はこちらです。</p>
                        </div>
                    </div>
                </header>
                
                <div class="gus-related-columns-grid">
                    <?php
                    $column_display_count = 0;
                    while ($related_columns_query->have_posts() && $column_display_count < 6):
                        $related_columns_query->the_post();
                        $column_display_count++;
                        $column_id = get_the_ID();
                        $read_time = function_exists('get_field') ? get_field('estimated_read_time', $column_id) : '';
                        $column_categories = get_the_terms($column_id, 'column_category');
                        $thumbnail_url = get_the_post_thumbnail_url($column_id, 'medium');
                        $excerpt = get_the_excerpt();
                    ?>
                    <a href="<?php the_permalink(); ?>" class="gus-related-column-card">
                        <?php if ($thumbnail_url): ?>
                        <div class="gus-related-column-thumbnail">
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                        </div>
                        <?php endif; ?>
                        
                        <div class="gus-related-column-card-content">
                            <div class="gus-related-column-card-meta">
                                <?php if ($column_categories && !is_wp_error($column_categories)): ?>
                                <span class="gus-related-column-category">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    <?php echo esc_html($column_categories[0]->name); ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($read_time): ?>
                                <span class="gus-related-column-read-time">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <?php echo esc_html($read_time); ?>分
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="gus-related-column-card-title">
                                <?php the_title(); ?>
                            </h3>
                            
                            <?php if ($excerpt): ?>
                            <p class="gus-related-column-card-excerpt">
                                <?php echo wp_trim_words($excerpt, 15, '...'); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
                
                <div class="gus-related-columns-footer">
                    <a href="<?php echo home_url('/columns/'); ?>" class="gus-btn gus-btn-primary" style="display: inline-flex; width: auto; min-width: 200px;">
                        すべてのコラムを見る
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </div>
            </section>
            <?php wp_reset_postdata(); endif; ?>

            <!-- お問い合わせ -->
            <?php if ($grant_data['contact_info']): ?>
            <section id="contact" class="gus-section">
                <header class="gus-section-header">
                    <svg class="gus-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <h2 class="gus-section-title">お問い合わせ</h2>
                </header>
                <div class="gus-section-content">
                    <?php echo nl2br(esc_html($grant_data['contact_info'])); ?>
                </div>
            </section>
            <?php endif; ?>

            <!-- ソーシャルシェア -->
            <div class="gus-social-share">
                <h3>この補助金情報をシェア</h3>
                <div class="gus-social-buttons">
                    <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" class="gus-btn gus-btn-secondary" target="_blank" rel="noopener noreferrer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                        </svg>
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" class="gus-btn gus-btn-secondary" target="_blank" rel="noopener noreferrer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                        </svg>
                        Facebook
                    </a>
                    <a href="https://social-plugins.line.me/lineit/share?url=<?php echo urlencode(get_permalink()); ?>" class="gus-btn gus-btn-secondary" target="_blank" rel="noopener noreferrer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                        </svg>
                        LINE
                    </a>
                    <button class="gus-btn gus-btn-secondary" onclick="navigator.clipboard.writeText('<?php echo esc_js(get_permalink()); ?>'); alert('URLをコピーしました');">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                        </svg>
                        URLコピー
                    </button>
                </div>
            </div>

            <!-- 共通CTAバナー（統一版） -->
            <section class="gus-unified-cta-section">
                <div class="gus-unified-cta-container">
                    <div class="gus-unified-cta-content">
                        <h2 class="gus-unified-cta-title">
                            他にも、あなたに合う補助金があるかもしれません
                        </h2>
                        <p class="gus-unified-cta-description">
                            助成金インサイトで最新の補助金情報を検索。<br>
                            あなたのビジネスに最適な支援制度を見つけましょう。
                        </p>
                        <div class="gus-unified-cta-buttons">
                            <a href="<?php echo home_url('/subsidy-diagnosis/'); ?>" class="gus-unified-cta-btn gus-unified-cta-btn-primary">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 11l3 3L22 4"/>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                                <span>
                                    <strong>AIで診断する</strong>
                                    <small style="display: block; font-size: 0.75rem; font-weight: 400; opacity: 0.9;">あなたに最適な補助金を提案</small>
                                </span>
                            </a>
                            <a href="<?php echo home_url('/grants/'); ?>" class="gus-unified-cta-btn gus-unified-cta-btn-secondary">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                                <span>
                                    <strong>一覧から探す</strong>
                                    <small style="display: block; font-size: 0.75rem; font-weight: 400; opacity: 0.9;">全ての補助金をチェック</small>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- カテゴリー・地域リンク -->
            <section class="gus-section gus-search-style-section">
                <h2 class="gus-section-title" style="font-size: var(--gus-text-lg); font-weight: 700; margin-bottom: 8px;">
                    この補助金のカテゴリー・地域
                </h2>
                <p style="font-size: 14px; color: #6B7280; margin-bottom: 24px;">
                    関連する補助金を素早く探せます
                </p>
                
                <!-- カテゴリー -->
                <?php if (!empty($taxonomies['categories'])): ?>
                <div class="gus-search-section-box">
                    <h3 class="gus-search-box-title">
                        <i class="fas fa-folder-open" aria-hidden="true" style="margin-right: 8px;"></i>
                        カテゴリ
                    </h3>
                    <div class="gus-search-links">
                        <?php foreach ($taxonomies['categories'] as $cat): ?>
                            <a href="<?php echo get_term_link($cat); ?>" class="gus-search-link" title="<?php echo esc_attr($cat->name); ?>の補助金を探す">
                                <?php echo esc_html($cat->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 都道府県 -->
                <?php if (!empty($taxonomies['prefectures'])): ?>
                <div class="gus-search-section-box">
                    <h3 class="gus-search-box-title">
                        <i class="fas fa-map-marker-alt" aria-hidden="true" style="margin-right: 8px;"></i>
                        都道府県
                    </h3>
                    <div class="gus-search-links">
                        <?php foreach ($taxonomies['prefectures'] as $pref): ?>
                            <a href="<?php echo get_term_link($pref); ?>" class="gus-search-link" title="<?php echo esc_attr($pref->name); ?>の補助金を探す">
                                <?php echo esc_html($pref->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- 市町村 -->
                <?php if (!empty($taxonomies['municipalities'])): ?>
                <div class="gus-search-section-box">
                    <h3 class="gus-search-box-title">
                        <i class="fas fa-building" aria-hidden="true" style="margin-right: 8px;"></i>
                        市町村
                    </h3>
                    <div class="gus-search-links">
                        <?php foreach ($taxonomies['municipalities'] as $muni): ?>
                            <a href="<?php echo get_term_link($muni); ?>" class="gus-search-link" title="<?php echo esc_attr($muni->name); ?>の補助金を探す">
                                <?php echo esc_html($muni->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </section>

            <!-- パンくずナビゲーション -->
            <nav class="gus-breadcrumb" aria-label="パンくずナビゲーション" itemscope itemtype="https://schema.org/BreadcrumbList">
                <ol>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo home_url('/'); ?>" itemprop="item">
                            <span itemprop="name">ホーム</span>
                        </a>
                        <meta itemprop="position" content="1">
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo home_url('/grants/'); ?>" itemprop="item">
                            <span itemprop="name">補助金一覧</span>
                        </a>
                        <meta itemprop="position" content="2">
                        <?php if (!empty($taxonomies['categories'])): ?>
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?php echo get_term_link($taxonomies['categories'][0]); ?>" itemprop="item">
                            <span itemprop="name"><?php echo esc_html($taxonomies['categories'][0]->name); ?></span>
                        </a>
                        <meta itemprop="position" content="3">
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" style="color: var(--gus-gray-900); font-weight: 600;" aria-current="page">
                        <span itemprop="name"><?php echo esc_html(wp_trim_words($seo_title, 8, '...')); ?></span>
                        <meta itemprop="position" content="4">
                        <meta itemprop="item" content="<?php echo esc_url(get_permalink()); ?>">
                    </li>
                        <?php else: ?>
                        <span aria-hidden="true">›</span>
                    </li>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" style="color: var(--gus-gray-900); font-weight: 600;" aria-current="page">
                        <span itemprop="name"><?php echo esc_html(wp_trim_words($seo_title, 8, '...')); ?></span>
                        <meta itemprop="position" content="3">
                        <meta itemprop="item" content="<?php echo esc_url(get_permalink()); ?>">
                    </li>
                        <?php endif; ?>
                </ol>
            </nav>
        </article>

        <!-- 右サイドバー -->
        <aside class="gus-sidebar" role="complementary" aria-label="サイドバー">
            <!-- 1. 広告スペース（最上部） -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="gus-sidebar-card gus-sidebar-ad-space">
                <?php
                $grant_category_ids = array();
                if (!empty($taxonomies['categories'])) {
                    foreach ($taxonomies['categories'] as $cat) {
                        $grant_category_ids[] = 'grant_category_' . $cat->term_id;
                    }
                }
                ji_display_ad('single_grant_sidebar_top', array(
                    'page_type' => 'single-grant',
                    'category_ids' => $grant_category_ids
                ));
                ?>
            </div>
            <?php endif; ?>

            <!-- 2. AIアシスタント（柔軟高さ維持） -->
            <div class="gus-pc-ai-permanent">
                <div class="gus-pc-ai-permanent-header">
                    <div class="gus-pc-ai-permanent-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <span>AIアシスタント</span>
                    </div>
                    <div class="gus-pc-ai-permanent-subtitle"><?php echo esc_html(wp_trim_words(get_the_title(), 10, '...')); ?></div>
                </div>
                <div class="gus-pc-ai-permanent-messages" id="pcPermanentMessages">
                    <div class="gus-ai-message gus-ai-message--assistant">
                        <div class="gus-ai-message-avatar">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20M2 12h20"/>
                            </svg>
                        </div>
                        <div class="gus-ai-message-content">
                            こんにちは！この助成金について何でもお聞きください。<br>
                            申請条件、必要書類、申請方法、対象経費など、詳しくお答えします。
                        </div>
                    </div>
                </div>
                <div class="gus-pc-ai-permanent-input-container">
                    <div class="gus-pc-ai-permanent-input-wrapper">
                        <textarea class="gus-pc-ai-permanent-input" id="pcPermanentInput" placeholder="例：申請条件は何ですか？" rows="2"></textarea>
                        <button class="gus-pc-ai-permanent-send" id="pcPermanentSend" type="button" aria-label="送信">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </button>
                    </div>
                    <div class="gus-pc-ai-permanent-suggestions">
                        <button class="gus-pc-ai-permanent-suggestion" type="button" data-question="申請条件を詳しく教えてください">
                            申請条件は？
                        </button>
                        <button class="gus-pc-ai-permanent-suggestion" type="button" data-question="必要な書類を教えてください">
                            必要書類は？
                        </button>
                        <button class="gus-pc-ai-permanent-suggestion" type="button" data-question="どんな費用が対象になりますか？">
                            対象経費は？
                        </button>
                        <button class="gus-pc-ai-permanent-suggestion" type="button" data-question="申請方法を教えてください">
                            申請方法は？
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3. 補助金検索 -->
            <?php get_template_part('template-parts/sidebar/search-widget'); ?>

            <!-- 4. アクション -->
            <div class="gus-sidebar-card">
                <h2 class="gus-sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    アクション
                </h2>
                <div class="gus-actions">
                    <?php if ($grant_data['official_url']): ?>
                    <a href="<?php echo esc_url($grant_data['official_url']); ?>" class="gus-btn gus-btn-primary" target="_blank" rel="noopener noreferrer">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        公式サイト
                    </a>
                    <?php endif; ?>
                    <button class="gus-btn gus-btn-secondary" onclick="window.print()" aria-label="印刷">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"/>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                            <rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        印刷
                    </button>
                </div>
            </div>

            <!-- 5. 広告スペース（中部） -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="gus-sidebar-card gus-sidebar-ad-space">
                <?php
                ji_display_ad('single_grant_sidebar_middle', array(
                    'page_type' => 'single-grant',
                    'category_ids' => $grant_category_ids
                ));
                ?>
            </div>
            <?php endif; ?>

            <!-- 6. 目次 -->
            <nav class="gus-sidebar-card" aria-label="目次">
                <h2 class="gus-sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                    目次
                </h2>
                <ul class="gus-toc-list">
                    <?php if ($grant_data['ai_summary']): ?>
                    <li class="gus-toc-item">
                        <a href="#ai-summary" class="gus-toc-link">AI要約</a>
                    </li>
                    <?php endif; ?>
                    <?php if (!empty($scored_related_grants)): ?>
                    <li class="gus-toc-item">
                        <a href="#related" class="gus-toc-link">おすすめ補助金</a>
                    </li>
                    <?php endif; ?>
                    <li class="gus-toc-item">
                        <a href="#grant-details" class="gus-toc-link">補助金詳細</a>
                    </li>
                    <li class="gus-toc-item">
                        <a href="#details" class="gus-toc-link">詳細情報</a>
                    </li>
                    <li class="gus-toc-item">
                        <a href="#application-flow" class="gus-toc-link">申請の流れ</a>
                    </li>
                    <li class="gus-toc-item">
                        <a href="#faq" class="gus-toc-link">よくある質問</a>
                    </li>
                    <?php if ($related_columns_query && $related_columns_query->have_posts()): ?>
                    <li class="gus-toc-item">
                        <a href="#related-columns" class="gus-toc-link">詳しい記事</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($grant_data['contact_info']): ?>
                    <li class="gus-toc-item">
                        <a href="#contact" class="gus-toc-link">お問い合わせ</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- 7. 関連コラム -->
            <?php if ($related_columns_query && $related_columns_query->have_posts()): ?>
            <div class="gus-sidebar-card">
                <h2 class="gus-sidebar-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    詳しい記事
                </h2>
                <div class="gus-related-columns-list">
                    <?php
                    $related_columns_query->rewind_posts();
                    $sidebar_column_count = 0;
                    while ($related_columns_query->have_posts() && $sidebar_column_count < 3):
                        $related_columns_query->the_post();
                        $sidebar_column_count++;
                        $column_id = get_the_ID();
                        $read_time = function_exists('get_field') ? get_field('estimated_read_time', $column_id) : '';
                        $column_categories = get_the_terms($column_id, 'column_category');
                    ?>
                    <a href="<?php the_permalink(); ?>" class="gus-related-column-item">
                        <div class="gus-related-column-content">
                            <div class="gus-related-column-title">
                                <?php echo wp_trim_words(get_the_title(), 12, '...'); ?>
                            </div>
                            <div class="gus-related-column-meta">
                                <?php if ($column_categories && !is_wp_error($column_categories)): ?>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    <?php echo esc_html($column_categories[0]->name); ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($read_time): ?>
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <?php echo esc_html($read_time); ?>分
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="gus-related-column-arrow">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
                <a href="<?php echo home_url('/columns/'); ?>" class="gus-view-all-link">
                    すべてのコラムを見る
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            </div>
            <?php wp_reset_postdata(); endif; ?>

            <!-- 8. アフィリエイト広告（下部） -->
            <?php if (function_exists('ji_display_ad')): ?>
            <div class="gus-sidebar-card gus-sidebar-ad-space">
                <?php
                ji_display_ad('single_grant_sidebar_bottom', array(
                    'page_type' => 'single-grant',
                    'category_ids' => $grant_category_ids
                ));
                ?>
            </div>
            <?php endif; ?>
        </aside>
    </div>
</main>

<!-- モバイルフローティングAIボタン -->
<button class="gus-mobile-ai-floating-btn" id="mobileAIFloatingBtn" type="button" aria-label="AIに質問">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
</button>

<!-- モバイルパネル用オーバーレイ -->
<div class="gus-mobile-panel-overlay" id="mobilePanelOverlay"></div>

<!-- AIチャットパネル -->
<div class="gus-mobile-panel" id="mobileAIPanel">
    <div class="gus-mobile-panel-header">
        <h2 class="gus-mobile-panel-title">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            AIに質問
        </h2>
        <button class="gus-mobile-panel-close" id="closeAIPanel" type="button" aria-label="閉じる">
            ✕
        </button>
    </div>
    <div class="gus-ai-panel">
        <div class="gus-ai-chat-messages" id="mobileAiMessages">
            <div class="gus-ai-message gus-ai-message--assistant">
                <div class="gus-ai-message-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 2v20M2 12h20"/>
                    </svg>
                </div>
                <div class="gus-ai-message-content">
                    こんにちは！この補助金について何でもお聞きください。申請条件、必要書類、対象経費など、詳しくお答えします。
                </div>
            </div>
        </div>
        <div class="gus-ai-input-container">
            <div class="gus-ai-input-wrapper">
                <textarea 
                    class="gus-ai-input" 
                    id="mobileAiInput" 
                    placeholder="例：申請条件は何ですか？" 
                    rows="1"
                    aria-label="質問を入力"></textarea>
                <button class="gus-ai-send" id="mobileAiSend" type="button" aria-label="送信">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                </button>
            </div>
            <div class="gus-ai-suggestions">
                <button class="gus-ai-suggestion" type="button" data-question="申請条件を詳しく教えてください">
                    申請条件は？
                </button>
                <button class="gus-ai-suggestion" type="button" data-question="必要な書類を教えてください">
                    必要書類は？
                </button>
                <button class="gus-ai-suggestion" type="button" data-question="どんな費用が対象になりますか？">
                    対象経費は？
                </button>
                <button class="gus-ai-suggestion" type="button" data-question="申請方法を教えてください">
                    申請方法は？
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // ===============================================
    // カルーセル機能
    // ===============================================
    const carouselTrack = document.getElementById('carouselTrack');
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    
    if (carouselTrack && prevBtn && nextBtn) {
        const scrollAmount = 336;
        
        function updateButtons() {
            const scrollLeft = carouselTrack.scrollLeft;
            const maxScroll = carouselTrack.scrollWidth - carouselTrack.clientWidth;
            
            prevBtn.disabled = scrollLeft <= 0;
            nextBtn.disabled = scrollLeft >= maxScroll - 10;
        }
        
        prevBtn.addEventListener('click', function() {
            carouselTrack.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });
        
        nextBtn.addEventListener('click', function() {
            carouselTrack.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        
        carouselTrack.addEventListener('scroll', updateButtons);
        window.addEventListener('resize', updateButtons);
        updateButtons();
    }
    
    // ===============================================
    // PC AI Chat
    // ===============================================
    const pcPermanentInput = document.getElementById('pcPermanentInput');
    const pcPermanentSend = document.getElementById('pcPermanentSend');
    const pcPermanentMessages = document.getElementById('pcPermanentMessages');
    const pcPermanentSuggestions = document.querySelectorAll('.gus-pc-ai-permanent-suggestion');
    
    if (pcPermanentSuggestions) {
        pcPermanentSuggestions.forEach(function(chip) {
            chip.addEventListener('click', function() {
                const question = this.getAttribute('data-question');
                if (pcPermanentInput) {
                    pcPermanentInput.value = question;
                    pcPermanentInput.focus();
                    if (pcPermanentSend) {
                        pcPermanentSend.click();
                    }
                }
            });
        });
    }
    
    async function sendPcPermanentQuestion() {
        const question = pcPermanentInput.value.trim();
        if (!question) return;
        
        addPcPermanentMessage(question, 'user');
        pcPermanentInput.value = '';
        pcPermanentInput.style.height = 'auto';
        pcPermanentSend.disabled = true;
        
        const typingId = showPcPermanentTyping();
        
        try {
            const formData = new FormData();
            formData.append('action', 'handle_grant_ai_question');
            formData.append('nonce', '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>');
            formData.append('post_id', '<?php echo $post_id; ?>');
            formData.append('question', question);
            
            const response = await fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            removePcPermanentTyping(typingId);
            
            if (data.success && data.data && data.data.answer) {
                addPcPermanentMessage(data.data.answer, 'assistant');
            } else {
                addPcPermanentMessage('申し訳ございません。回答の生成に失敗しました。', 'assistant');
            }
        } catch (error) {
            console.error('PC AI質問エラー:', error);
            removePcPermanentTyping(typingId);
            addPcPermanentMessage('通信エラーが発生しました。もう一度お試しください。', 'assistant');
        } finally {
            pcPermanentSend.disabled = false;
        }
    }
    
    function addPcPermanentMessage(content, type) {
        if (!pcPermanentMessages) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'gus-ai-message gus-ai-message--' + type;
        
        const avatar = document.createElement('div');
        avatar.className = 'gus-ai-message-avatar';
        avatar.innerHTML = type === 'assistant' 
            ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>'
            : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'gus-ai-message-content';
        contentDiv.innerHTML = content.replace(/\n/g, '<br>');
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(contentDiv);
        pcPermanentMessages.appendChild(messageDiv);
        pcPermanentMessages.scrollTop = pcPermanentMessages.scrollHeight;
    }
    
    function showPcPermanentTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'gus-ai-typing';
        typingDiv.id = 'pcPermanentTyping';
        
        const avatar = document.createElement('div');
        avatar.className = 'gus-ai-message-avatar';
        avatar.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg>';
        
        const dotsDiv = document.createElement('div');
        dotsDiv.className = 'gus-ai-typing-dots';
        dotsDiv.innerHTML = '<div class="gus-ai-typing-dot"></div><div class="gus-ai-typing-dot"></div><div class="gus-ai-typing-dot"></div>';
        
        typingDiv.appendChild(avatar);
        typingDiv.appendChild(dotsDiv);
        pcPermanentMessages.appendChild(typingDiv);
        pcPermanentMessages.scrollTop = pcPermanentMessages.scrollHeight;
        
        return 'pcPermanentTyping';
    }
    
    function removePcPermanentTyping(id) {
        const typing = document.getElementById(id);
        if (typing) typing.remove();
    }
    
    if (pcPermanentSend && pcPermanentInput) {
        pcPermanentSend.addEventListener('click', sendPcPermanentQuestion);
        pcPermanentInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendPcPermanentQuestion();
            }
        });
        pcPermanentInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 80) + 'px';
        });
    }
    
    // ===============================================
    // Mobile AI Floating Button & Panel
    // ===============================================
    const mobileAIFloatingBtn = document.getElementById('mobileAIFloatingBtn');
    const mobilePanelOverlay = document.getElementById('mobilePanelOverlay');
    const mobileAIPanel = document.getElementById('mobileAIPanel');
    const closeAIPanel = document.getElementById('closeAIPanel');
    const mobileAiInput = document.getElementById('mobileAiInput');
    const mobileAiSend = document.getElementById('mobileAiSend');
    const mobileAiMessages = document.getElementById('mobileAiMessages');
    const mobileAiSuggestions = document.querySelectorAll('#mobileAIPanel .gus-ai-suggestion');
    
    function openPanel(panel) {
        if (mobilePanelOverlay && panel) {
            mobilePanelOverlay.classList.add('active');
            panel.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closePanel() {
        if (mobilePanelOverlay && mobileAIPanel) {
            mobilePanelOverlay.classList.remove('active');
            mobileAIPanel.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    if (mobileAIFloatingBtn) mobileAIFloatingBtn.addEventListener('click', function() { openPanel(mobileAIPanel); });
    if (closeAIPanel) closeAIPanel.addEventListener('click', closePanel);
    if (mobilePanelOverlay) mobilePanelOverlay.addEventListener('click', closePanel);
    
    mobileAiSuggestions.forEach(function(chip) {
        chip.addEventListener('click', function() {
            const question = this.getAttribute('data-question');
            if (mobileAiInput) {
                mobileAiInput.value = question;
                mobileAiInput.focus();
                if (mobileAiSend) {
                    mobileAiSend.click();
                }
            }
        });
    });
    
    async function sendMobileAiQuestion() {
        const question = mobileAiInput.value.trim();
        if (!question) return;
        
        addMobileMessage(question, 'user');
        mobileAiInput.value = '';
        mobileAiInput.style.height = 'auto';
        mobileAiSend.disabled = true;
        
        const typingId = showMobileTyping();
        
        try {
            const formData = new FormData();
            formData.append('action', 'handle_grant_ai_question');
            formData.append('nonce', '<?php echo wp_create_nonce("gi_ajax_nonce"); ?>');
            formData.append('post_id', '<?php echo $post_id; ?>');
            formData.append('question', question);
            
            const response = await fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            removeMobileTyping(typingId);
            
            if (data.success && data.data && data.data.answer) {
                addMobileMessage(data.data.answer, 'assistant');
            } else {
                addMobileMessage('申し訳ございません。回答の生成に失敗しました。', 'assistant');
            }
        } catch (error) {
            console.error('モバイルAI質問エラー:', error);
            removeMobileTyping(typingId);
            addMobileMessage('通信エラーが発生しました。もう一度お試しください。', 'assistant');
        } finally {
            mobileAiSend.disabled = false;
        }
    }
    
    function addMobileMessage(content, type) {
        if (!mobileAiMessages) return;
        
        const messageDiv = document.createElement('div');
        messageDiv.className = 'gus-ai-message gus-ai-message--' + type;
        
        const avatar = document.createElement('div');
        avatar.className = 'gus-ai-message-avatar';
        avatar.innerHTML = type === 'assistant'
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20"/></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'gus-ai-message-content';
        contentDiv.innerHTML = content.replace(/\n/g, '<br>');
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(contentDiv);
        mobileAiMessages.appendChild(messageDiv);
        mobileAiMessages.scrollTop = mobileAiMessages.scrollHeight;
    }
    
    function showMobileTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'gus-ai-typing';
        typingDiv.id = 'mobileTyping';
        
        const avatar = document.createElement('div');
        avatar.className = 'gus-ai-message-avatar';
        avatar.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20"/></svg>';
        
        const dotsDiv = document.createElement('div');
        dotsDiv.className = 'gus-ai-typing-dots';
        dotsDiv.innerHTML = '<div class="gus-ai-typing-dot"></div><div class="gus-ai-typing-dot"></div><div class="gus-ai-typing-dot"></div>';
        
        typingDiv.appendChild(avatar);
        typingDiv.appendChild(dotsDiv);
        mobileAiMessages.appendChild(typingDiv);
        mobileAiMessages.scrollTop = mobileAiMessages.scrollHeight;
        
        return 'mobileTyping';
    }
    
    function removeMobileTyping(id) {
        const typing = document.getElementById(id);
        if (typing) typing.remove();
    }
    
    if (mobileAiSend && mobileAiInput) {
        mobileAiSend.addEventListener('click', sendMobileAiQuestion);
        mobileAiInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMobileAiQuestion();
            }
        });
        mobileAiInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
    
    // ===============================================
    // TOC Smooth Scroll
    // ===============================================
    const tocLinks = document.querySelectorAll('.gus-toc-link');
    tocLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                tocLinks.forEach(function(l) { l.classList.remove('active'); });
                this.classList.add('active');
            }
        });
    });
    
    // ===============================================
    // FAQ Accordion
    // ===============================================
    const faqItems = document.querySelectorAll('.gus-faq-item');
    faqItems.forEach(function(item) {
        const summary = item.querySelector('.gus-faq-question');
        if (summary) {
            item.addEventListener('toggle', function() {
                const isOpen = item.open;
                summary.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }
    });
    
    // ===============================================
    // Intersection Observer
    // ===============================================
    const sections = document.querySelectorAll('.gus-section[id]');
    const observerOptions = {
        root: null,
        rootMargin: '-20% 0px -70% 0px',
        threshold: 0
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                tocLinks.forEach(function(link) {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + id) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, observerOptions);
    
    sections.forEach(function(section) {
        observer.observe(section);
    });
    
    console.log('✅ Grant Single Page v24.2 Initialized');
});
</script>

<?php 
get_footer(); 
?>