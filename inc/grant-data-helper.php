<?php
/**
 * Grant Data Helper - Data Retrieval Abstraction Layer
 * 補助金データ取得の統一インターフェース
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * GI_Grant_Data_Helper クラス
 * 補助金データの取得・整形を一元管理
 */
class GI_Grant_Data_Helper {
    
    /**
     * 補助金の全データを取得
     * 
     * @param int $post_id 投稿ID
     * @return array 整形されたデータ配列
     */
    public static function get_all_data($post_id) {
        if (!$post_id || !get_post($post_id)) {
            return array();
        }
        
        // 基本情報
        $data = array(
            'id' => $post_id,
            'title' => get_the_title($post_id),
            'content' => get_post_field('post_content', $post_id),
            'excerpt' => get_the_excerpt($post_id),
            'permalink' => get_permalink($post_id),
            'date' => get_the_date('Y-m-d', $post_id),
            'modified_date' => get_the_modified_date('Y-m-d', $post_id),
        );
        
        // ACFフィールド
        $acf_fields = array(
            'organization',
            'max_amount',
            'max_amount_numeric',
            'subsidy_rate',
            'subsidy_rate_detailed',
            'deadline',
            'deadline_date',
            'application_period',
            'grant_target',
            'contact_info',
            'official_url',
            'application_status',
            'application_method',
            'required_documents',
            'required_documents_detailed',
            'eligible_expenses',
            'eligible_expenses_detailed',
            'adoption_rate',
            'grant_difficulty',
            'difficulty_level',
            'is_featured',
            'views_count',
            'ai_summary',
            'area_notes',
        );
        
        foreach ($acf_fields as $field) {
            $value = function_exists('get_field') ? get_field($field, $post_id) : get_post_meta($post_id, $field, true);
            $data[$field] = $value !== false ? $value : '';
        }
        
        // 数値型フィールドの型変換
        $data['max_amount_numeric'] = intval($data['max_amount_numeric']);
        $data['adoption_rate'] = floatval($data['adoption_rate']);
        $data['views_count'] = intval($data['views_count']);
        
        // タクソノミー
        $data['categories'] = wp_get_post_terms($post_id, 'grant_category');
        $data['prefectures'] = wp_get_post_terms($post_id, 'grant_prefecture');
        $data['municipalities'] = wp_get_post_terms($post_id, 'grant_municipality');
        $data['tags'] = wp_get_post_tags($post_id);
        
        // WP_Errorの処理
        if (is_wp_error($data['categories'])) $data['categories'] = array();
        if (is_wp_error($data['prefectures'])) $data['prefectures'] = array();
        if (is_wp_error($data['municipalities'])) $data['municipalities'] = array();
        if (is_wp_error($data['tags'])) $data['tags'] = array();
        
        // アイキャッチ画像
        $data['thumbnail'] = get_the_post_thumbnail_url($post_id, 'large');
        $data['og_image'] = self::get_og_image($post_id);
        
        return $data;
    }
    
    /**
     * OG画像URLを取得
     * 
     * @param int $post_id 投稿ID
     * @return string OG画像URL
     */
    public static function get_og_image($post_id) {
        if (has_post_thumbnail($post_id)) {
            return get_the_post_thumbnail_url($post_id, 'large');
        }
        
        $site_icon = get_site_icon_url(512);
        if ($site_icon) {
            return $site_icon;
        }
        
        $default_image = get_template_directory_uri() . '/assets/images/default-og-grant.jpg';
        if (file_exists(get_template_directory() . '/assets/images/default-og-grant.jpg')) {
            return $default_image;
        }
        
        return 'https://via.placeholder.com/1200x630.png?text=' . urlencode(get_the_title($post_id));
    }
    
    /**
     * 都道府県の表示用フォーマット
     * 
     * @param array $prefectures 都道府県term配列
     * @return string フォーマット済み文字列
     */
    public static function format_prefectures($prefectures) {
        if (empty($prefectures)) {
            return '';
        }
        
        // 全国判定
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
        
        // 5件以上は省略
        if (count($prefectures) >= 5) {
            $display_prefs = array_slice($prefectures, 0, 4);
            $names = array_map(function($pref) { return $pref->name; }, $display_prefs);
            return implode('、', $names) . '...';
        }
        
        $names = array_map(function($pref) { return $pref->name; }, $prefectures);
        return implode('、', $names);
    }
    
    /**
     * 市町村の表示用フォーマット
     * 
     * @param array $municipalities 市町村term配列
     * @param array $prefectures 都道府県term配列
     * @return string フォーマット済み文字列
     */
    public static function format_municipalities($municipalities, $prefectures) {
        if (empty($municipalities)) {
            return '';
        }
        
        // 全国の場合は市町村表示なし
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
        
        // 全域判定
        foreach ($municipalities as $muni) {
            if (stripos($muni->name, '全域') !== false || stripos($muni->slug, 'zeniki') !== false) {
                if (!empty($prefectures)) {
                    return $prefectures[0]->name . '全域';
                }
            }
        }
        
        // 5件以上は省略
        if (count($municipalities) >= 5) {
            $display_munis = array_slice($municipalities, 0, 4);
            $names = array_map(function($muni) { return $muni->name; }, $display_munis);
            return implode('、', $names) . '...';
        }
        
        $names = array_map(function($muni) { return $muni->name; }, $municipalities);
        return implode('、', $names);
    }
    
    /**
     * 締切日情報を取得
     * 
     * @param string $deadline_date 締切日（日付形式）
     * @param string $deadline 締切テキスト
     * @return array 締切情報配列
     */
    public static function get_deadline_info($deadline_date, $deadline = '') {
        $info = array(
            'text' => '',
            'class' => '',
            'days_remaining' => 0,
            'is_urgent' => false,
            'is_closed' => false,
        );
        
        if (!empty($deadline_date)) {
            $deadline_timestamp = strtotime($deadline_date);
            if ($deadline_timestamp && $deadline_timestamp > 0) {
                $info['text'] = date('Y年n月j日', $deadline_timestamp);
                $current_time = current_time('timestamp');
                $info['days_remaining'] = ceil(($deadline_timestamp - $current_time) / 86400);
                
                if ($info['days_remaining'] <= 0) {
                    $info['class'] = 'closed';
                    $info['text'] .= ' (終了)';
                    $info['is_closed'] = true;
                } elseif ($info['days_remaining'] <= 7) {
                    $info['class'] = 'urgent';
                    $info['text'] .= ' (残' . $info['days_remaining'] . '日)';
                    $info['is_urgent'] = true;
                } elseif ($info['days_remaining'] <= 30) {
                    $info['class'] = 'warning';
                }
            }
        } elseif (!empty($deadline)) {
            $info['text'] = $deadline;
        }
        
        return $info;
    }
    
    /**
     * 難易度情報を取得
     * 
     * @param string $difficulty 難易度キー
     * @return array 難易度情報配列
     */
    public static function get_difficulty_info($difficulty) {
        $configs = array(
            'easy' => array('label' => '易', 'dots' => 1, 'description' => '初心者向け', 'color' => 'green'),
            'normal' => array('label' => '中', 'dots' => 2, 'description' => '一般的', 'color' => 'blue'),
            'hard' => array('label' => '難', 'dots' => 3, 'description' => '専門的', 'color' => 'orange'),
        );
        
        $difficulty = !empty($difficulty) ? $difficulty : 'normal';
        return isset($configs[$difficulty]) ? $configs[$difficulty] : $configs['normal'];
    }
    
    /**
     * ステータス情報を取得
     * 
     * @param string $status ステータスキー
     * @return array ステータス情報配列
     */
    public static function get_status_info($status) {
        $configs = array(
            'open' => array('label' => '募集中', 'class' => 'open', 'color' => 'green'),
            'closed' => array('label' => '終了', 'class' => 'closed', 'color' => 'gray'),
            'upcoming' => array('label' => '募集予定', 'class' => 'upcoming', 'color' => 'yellow'),
        );
        
        $status = !empty($status) ? $status : 'open';
        return isset($configs[$status]) ? $configs[$status] : $configs['open'];
    }
    
    /**
     * SEO用メタディスクリプション生成
     * 
     * @param int $post_id 投稿ID
     * @return string メタディスクリプション
     */
    public static function generate_meta_description($post_id) {
        $ai_summary = function_exists('get_field') ? get_field('ai_summary', $post_id) : '';
        
        if (!empty($ai_summary)) {
            $raw_text = wp_strip_all_tags($ai_summary);
            $description = mb_substr($raw_text, 0, 160, 'UTF-8');
            if (mb_strlen($raw_text, 'UTF-8') > 160) {
                $description .= '...';
            }
            return $description;
        }
        
        if (has_excerpt($post_id)) {
            $raw_text = wp_strip_all_tags(get_the_excerpt($post_id));
            $description = mb_substr($raw_text, 0, 160, 'UTF-8');
            if (mb_strlen($raw_text, 'UTF-8') > 160) {
                $description .= '...';
            }
            return $description;
        }
        
        $content = get_post_field('post_content', $post_id);
        $raw_text = wp_strip_all_tags($content);
        $description = mb_substr($raw_text, 0, 160, 'UTF-8');
        if (mb_strlen($raw_text, 'UTF-8') > 160) {
            $description .= '...';
        }
        
        return $description;
    }
    
    /**
     * 読了時間を計算
     * 
     * @param string $content 本文内容
     * @return int 読了時間（分）
     */
    public static function calculate_reading_time($content) {
        $word_count = mb_strlen(strip_tags($content), 'UTF-8');
        return max(1, ceil($word_count / 400));
    }
    
    /**
     * SEOキーワードを生成
     * 
     * @param int $post_id 投稿ID
     * @return array キーワード配列
     */
    public static function generate_seo_keywords($post_id) {
        $keywords = array();
        
        $keywords[] = get_the_title($post_id);
        $keywords[] = '補助金';
        $keywords[] = '助成金';
        $keywords[] = date('Y') . '年度';
        
        $organization = function_exists('get_field') ? get_field('organization', $post_id) : '';
        if (!empty($organization)) {
            $keywords[] = $organization;
        }
        
        $categories = wp_get_post_terms($post_id, 'grant_category');
        if (!is_wp_error($categories) && !empty($categories)) {
            foreach ($categories as $cat) {
                $keywords[] = $cat->name;
            }
        }
        
        $prefectures = wp_get_post_terms($post_id, 'grant_prefecture');
        if (!is_wp_error($prefectures) && !empty($prefectures)) {
            foreach ($prefectures as $pref) {
                $keywords[] = $pref->name;
            }
        }
        
        return array_unique($keywords);
    }
}
