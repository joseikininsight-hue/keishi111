<?php
/**
 * SEO Content Manager - PV数順記事管理システム
 * 
 * 機能:
 * - PV数順の記事一覧表示（Grant & Column）
 * - ワンクリック編集機能
 * - 修正チェック機能（修正済み/未修正フラグ）
 * - フィルタリング機能（修正状態、PV数範囲）
 * - 一括操作（複数記事を一括で修正済みにする）
 * - 統計情報表示
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 管理画面メニューに追加
 */
add_action('admin_menu', 'gi_seo_manager_menu', 25);
function gi_seo_manager_menu() {
    add_menu_page(
        'SEO記事管理',           // ページタイトル
        'SEO記事管理',           // メニュータイトル
        'edit_posts',            // 必要な権限
        'gi-seo-manager',        // メニュースラッグ
        'gi_seo_manager_page',   // コールバック関数
        'dashicons-chart-line',  // アイコン
        26                        // メニュー位置
    );
}

/**
 * SEO記事管理ページのメイン表示
 */
function gi_seo_manager_page() {
    // 権限チェック
    if (!current_user_can('edit_posts')) {
        wp_die(__('この機能を使用する権限がありません。'));
    }
    
    // 一括操作の処理
    if (isset($_POST['bulk_action']) && isset($_POST['post_ids']) && check_admin_referer('gi_seo_bulk_action')) {
        $action = sanitize_text_field($_POST['bulk_action']);
        $post_ids = array_map('intval', $_POST['post_ids']);
        
        if ($action === 'mark_revised') {
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, '_seo_content_revised', 'yes');
                update_post_meta($post_id, '_seo_revised_date', current_time('mysql'));
                update_post_meta($post_id, '_seo_revised_by', get_current_user_id());
            }
            echo '<div class="notice notice-success is-dismissible"><p>' . count($post_ids) . '件の記事を修正済みにしました。</p></div>';
        } elseif ($action === 'mark_unrevised') {
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, '_seo_content_revised', 'no');
                delete_post_meta($post_id, '_seo_revised_date');
                delete_post_meta($post_id, '_seo_revised_by');
            }
            echo '<div class="notice notice-success is-dismissible"><p>' . count($post_ids) . '件の記事を未修正にしました。</p></div>';
        }
    }
    
    // 単一記事の修正状態変更
    if (isset($_GET['toggle_revised']) && isset($_GET['post_id']) && check_admin_referer('gi_toggle_revised_' . $_GET['post_id'])) {
        $post_id = intval($_GET['post_id']);
        $current_status = get_post_meta($post_id, '_seo_content_revised', true);
        $new_status = ($current_status === 'yes') ? 'no' : 'yes';
        
        update_post_meta($post_id, '_seo_content_revised', $new_status);
        if ($new_status === 'yes') {
            update_post_meta($post_id, '_seo_revised_date', current_time('mysql'));
            update_post_meta($post_id, '_seo_revised_by', get_current_user_id());
        } else {
            delete_post_meta($post_id, '_seo_revised_date');
            delete_post_meta($post_id, '_seo_revised_by');
        }
        
        $message = ($new_status === 'yes') ? '修正済みにしました。' : '未修正にしました。';
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
    }
    
    // フィルター取得
    $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'all';
    $revised_status = isset($_GET['revised']) ? sanitize_text_field($_GET['revised']) : 'all';
    $recruitment_status = isset($_GET['recruitment']) ? sanitize_text_field($_GET['recruitment']) : 'active'; // デフォルト: 募集中のみ
    $pv_min = isset($_GET['pv_min']) ? intval($_GET['pv_min']) : 0;
    $pv_max = isset($_GET['pv_max']) ? intval($_GET['pv_max']) : 999999999;
    $sort_by = isset($_GET['sort_by']) ? sanitize_text_field($_GET['sort_by']) : 'pv'; // デフォルト: PV順
    $per_page = 50;
    $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
    
    // 記事データ取得
    $posts_data = gi_get_posts_with_pv($post_type, $revised_status, $recruitment_status, $pv_min, $pv_max, $sort_by, $per_page, $paged);
    $total_posts = $posts_data['total'];
    $posts = $posts_data['posts'];
    $stats = $posts_data['stats'];
    
    // ページ数計算
    $total_pages = ceil($total_posts / $per_page);
    
    ?>
    <div class="wrap gi-seo-manager">
        <h1 class="wp-heading-inline">
            <span class="dashicons dashicons-chart-line" style="font-size: 32px; width: 32px; height: 32px; margin-right: 8px;"></span>
            SEO記事管理 - PV数順一覧
        </h1>
        
        <p class="description" style="margin-top: 10px;">
            PV数の多い順に記事を表示し、SEO改善のための修正状況を管理します。<br>
            記事タイトルをクリックすると編集画面へ移動します。
        </p>
        
        <!-- 統計情報 -->
        <div class="gi-stats-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
            <div class="gi-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">総記事数</div>
                <div style="font-size: 32px; font-weight: 700;"><?php echo number_format($stats['total']); ?></div>
            </div>
            <div class="gi-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">修正済み</div>
                <div style="font-size: 32px; font-weight: 700;"><?php echo number_format($stats['revised']); ?></div>
                <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                    (<?php echo $stats['total'] > 0 ? number_format(($stats['revised'] / $stats['total']) * 100, 1) : 0; ?>%)
                </div>
            </div>
            <div class="gi-stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">未修正</div>
                <div style="font-size: 32px; font-weight: 700;"><?php echo number_format($stats['unrevised']); ?></div>
                <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                    (<?php echo $stats['total'] > 0 ? number_format(($stats['unrevised'] / $stats['total']) * 100, 1) : 0; ?>%)
                </div>
            </div>
            <div class="gi-stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">総PV数</div>
                <div style="font-size: 32px; font-weight: 700;"><?php echo number_format($stats['total_pv']); ?></div>
            </div>
            <?php if ($post_type !== 'column'): // 補助金の場合のみ表示 ?>
            <div class="gi-stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">募集中</div>
                <div style="font-size: 32px; font-weight: 700;"><?php echo number_format($stats['active']); ?></div>
            </div>
            <div class="gi-stat-card" style="background: linear-gradient(135deg, #a8a8a8 0%, #6b6b6b 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">募集終了</div>
                <div style="font-size: 32px; font-weight: 700;"><?php echo number_format($stats['expired']); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- フィルター -->
        <form method="get" action="" class="gi-filters" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 20px 0;">
            <input type="hidden" name="page" value="gi-seo-manager">
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                <div>
                    <label for="post_type" style="display: block; margin-bottom: 5px; font-weight: 600;">投稿タイプ</label>
                    <select name="post_type" id="post_type" style="width: 100%;">
                        <option value="all" <?php selected($post_type, 'all'); ?>>すべて</option>
                        <option value="grant" <?php selected($post_type, 'grant'); ?>>補助金</option>
                        <option value="column" <?php selected($post_type, 'column'); ?>>コラム</option>
                    </select>
                </div>
                
                <div>
                    <label for="revised" style="display: block; margin-bottom: 5px; font-weight: 600;">修正状態</label>
                    <select name="revised" id="revised" style="width: 100%;">
                        <option value="all" <?php selected($revised_status, 'all'); ?>>すべて</option>
                        <option value="yes" <?php selected($revised_status, 'yes'); ?>>修正済み</option>
                        <option value="no" <?php selected($revised_status, 'no'); ?>>未修正</option>
                    </select>
                </div>
                
                <div id="recruitment-filter" style="<?php echo ($post_type === 'column') ? 'display:none;' : ''; ?>">
                    <label for="recruitment" style="display: block; margin-bottom: 5px; font-weight: 600;">募集状態（補助金）</label>
                    <select name="recruitment" id="recruitment" style="width: 100%;">
                        <option value="all" <?php selected($recruitment_status, 'all'); ?>>すべて</option>
                        <option value="active" <?php selected($recruitment_status, 'active'); ?>>募集中のみ</option>
                        <option value="expired" <?php selected($recruitment_status, 'expired'); ?>>募集終了のみ</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort_by" style="display: block; margin-bottom: 5px; font-weight: 600;">並び順</label>
                    <select name="sort_by" id="sort_by" style="width: 100%;">
                        <option value="pv" <?php selected($sort_by, 'pv'); ?>>PV数順</option>
                        <option value="deadline" <?php selected($sort_by, 'deadline'); ?>>締切日順</option>
                        <option value="modified" <?php selected($sort_by, 'modified'); ?>>更新日順</option>
                    </select>
                </div>
                
                <div>
                    <label for="pv_min" style="display: block; margin-bottom: 5px; font-weight: 600;">PV数（最小）</label>
                    <input type="number" name="pv_min" id="pv_min" value="<?php echo esc_attr($pv_min); ?>" style="width: 100%;" min="0">
                </div>
                
                <div>
                    <label for="pv_max" style="display: block; margin-bottom: 5px; font-weight: 600;">PV数（最大）</label>
                    <input type="number" name="pv_max" id="pv_max" value="<?php echo esc_attr($pv_max > 999999999 ? '' : $pv_max); ?>" style="width: 100%;" min="0" placeholder="無制限">
                </div>
                
                <div>
                    <button type="submit" class="button button-primary" style="width: 100%; height: 36px;">
                        <span class="dashicons dashicons-filter" style="margin-top: 4px;"></span> フィルター適用
                    </button>
                </div>
                
                <div>
                    <a href="?page=gi-seo-manager" class="button" style="width: 100%; height: 36px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                        <span class="dashicons dashicons-image-rotate" style="margin-top: 4px;"></span> リセット
                    </a>
                </div>
            </div>
        </form>
        
        <!-- 記事一覧テーブル -->
        <form method="post" action="" id="posts-filter">
            <?php wp_nonce_field('gi_seo_bulk_action'); ?>
            
            <div class="tablenav top" style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
                <div class="alignleft actions bulkactions">
                    <select name="bulk_action" id="bulk-action-selector-top">
                        <option value="">一括操作を選択</option>
                        <option value="mark_revised">修正済みにする</option>
                        <option value="mark_unrevised">未修正にする</option>
                    </select>
                    <button type="submit" class="button action">適用</button>
                </div>
                
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo number_format($total_posts); ?>件</span>
                    <?php if ($total_pages > 1): ?>
                        <?php
                        $page_links = paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total' => $total_pages,
                            'current' => $paged,
                            'type' => 'plain'
                        ));
                        echo $page_links;
                        ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped table-view-list posts">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-cb check-column" style="width: 40px;">
                            <input type="checkbox" id="cb-select-all">
                        </th>
                        <th scope="col" style="width: 60px; text-align: center;">順位</th>
                        <th scope="col" style="width: 100px; text-align: right;">PV数</th>
                        <th scope="col">タイトル</th>
                        <th scope="col" style="width: 100px; text-align: center;">タイプ</th>
                        <th scope="col" style="width: 130px; text-align: center;">締切日</th>
                        <th scope="col" style="width: 120px; text-align: center;">修正状態</th>
                        <th scope="col" style="width: 120px; text-align: center;">最終更新</th>
                        <th scope="col" style="width: 100px; text-align: center;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px;">
                                <span class="dashicons dashicons-info" style="font-size: 48px; color: #ccc;"></span>
                                <p style="margin: 10px 0 0 0; color: #666;">該当する記事が見つかりませんでした。</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $rank = ($paged - 1) * $per_page + 1;
                        foreach ($posts as $post): 
                            $revised = get_post_meta($post->ID, '_seo_content_revised', true);
                            $revised_date = get_post_meta($post->ID, '_seo_revised_date', true);
                            $revised_by = get_post_meta($post->ID, '_seo_revised_by', true);
                            $pv_count = intval(get_post_meta($post->ID, 'views_count', true));
                            
                            $edit_url = get_edit_post_link($post->ID);
                            $toggle_url = wp_nonce_url(
                                add_query_arg(array(
                                    'toggle_revised' => 1,
                                    'post_id' => $post->ID
                                )),
                                'gi_toggle_revised_' . $post->ID
                            );
                            
                            $post_type_label = ($post->post_type === 'grant') ? '補助金' : 'コラム';
                            $post_type_color = ($post->post_type === 'grant') ? '#10b981' : '#3b82f6';
                        ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="post_ids[]" value="<?php echo esc_attr($post->ID); ?>">
                            </th>
                            <td style="text-align: center; font-weight: 700; font-size: 16px; color: #666;">
                                #<?php echo $rank; ?>
                            </td>
                            <td style="text-align: right; font-weight: 700; font-size: 16px; color: <?php echo $pv_count > 1000 ? '#ef4444' : ($pv_count > 500 ? '#f59e0b' : '#6b7280'); ?>;">
                                <?php echo number_format($pv_count); ?>
                            </td>
                            <td>
                                <strong>
                                    <a href="<?php echo esc_url($edit_url); ?>" style="text-decoration: none; color: #2563eb; font-size: 14px;">
                                        <?php echo esc_html($post->post_title); ?>
                                    </a>
                                </strong>
                                <div style="margin-top: 5px; font-size: 12px; color: #6b7280;">
                                    <a href="<?php echo get_permalink($post->ID); ?>" target="_blank" style="text-decoration: none; color: #6b7280;">
                                        <span class="dashicons dashicons-visibility" style="font-size: 14px; vertical-align: middle;"></span>
                                        表示
                                    </a>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span style="display: inline-block; padding: 4px 12px; background: <?php echo $post_type_color; ?>; color: white; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    <?php echo esc_html($post_type_label); ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <?php 
                                if ($post->post_type === 'grant' && function_exists('get_field')) {
                                    $deadline_date = get_field('deadline_date', $post->ID);
                                    if ($deadline_date) {
                                        $deadline_timestamp = strtotime($deadline_date);
                                        $current_time = current_time('timestamp');
                                        $days_remaining = ceil(($deadline_timestamp - $current_time) / (60 * 60 * 24));
                                        
                                        $deadline_display = date('Y/m/d', $deadline_timestamp);
                                        
                                        if ($days_remaining <= 0) {
                                            $badge_color = '#6b7280';
                                            $badge_text = '募集終了';
                                        } elseif ($days_remaining <= 7) {
                                            $badge_color = '#ef4444';
                                            $badge_text = 'あと' . $days_remaining . '日';
                                        } elseif ($days_remaining <= 30) {
                                            $badge_color = '#f59e0b';
                                            $badge_text = 'あと' . $days_remaining . '日';
                                        } else {
                                            $badge_color = '#10b981';
                                            $badge_text = 'あと' . $days_remaining . '日';
                                        }
                                        
                                        echo '<div style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 4px;">' . esc_html($deadline_display) . '</div>';
                                        echo '<span style="display: inline-block; padding: 3px 8px; background: ' . $badge_color . '; color: white; border-radius: 3px; font-size: 11px; font-weight: 600;">' . esc_html($badge_text) . '</span>';
                                    } else {
                                        echo '<span style="color: #9ca3af; font-size: 12px;">締切日未設定</span>';
                                    }
                                } else {
                                    echo '<span style="color: #9ca3af; font-size: 12px;">-</span>';
                                }
                                ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($revised === 'yes'): ?>
                                    <span style="display: inline-block; padding: 6px 12px; background: #10b981; color: white; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                        <span class="dashicons dashicons-yes-alt" style="font-size: 14px; vertical-align: middle; margin-top: -2px;"></span>
                                        修正済み
                                    </span>
                                    <?php if ($revised_date): ?>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                                            <?php echo date('Y/m/d H:i', strtotime($revised_date)); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 6px 12px; background: #ef4444; color: white; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                        <span class="dashicons dashicons-dismiss" style="font-size: 14px; vertical-align: middle; margin-top: -2px;"></span>
                                        未修正
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; font-size: 13px; color: #6b7280;">
                                <?php echo get_the_modified_date('Y/m/d', $post->ID); ?><br>
                                <span style="font-size: 11px;"><?php echo get_the_modified_time('H:i', $post->ID); ?></span>
                            </td>
                            <td style="text-align: center;">
                                <a href="<?php echo esc_url($edit_url); ?>" class="button button-small" style="margin-bottom: 5px; display: inline-block;">
                                    <span class="dashicons dashicons-edit" style="font-size: 14px; vertical-align: middle;"></span>
                                    編集
                                </a>
                                <br>
                                <a href="<?php echo esc_url($toggle_url); ?>" class="button button-small" style="display: inline-block;">
                                    <span class="dashicons dashicons-update" style="font-size: 14px; vertical-align: middle;"></span>
                                    切替
                                </a>
                            </td>
                        </tr>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="tablenav bottom" style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
                <div class="alignleft actions bulkactions">
                    <select name="bulk_action" id="bulk-action-selector-bottom">
                        <option value="">一括操作を選択</option>
                        <option value="mark_revised">修正済みにする</option>
                        <option value="mark_unrevised">未修正にする</option>
                    </select>
                    <button type="submit" class="button action">適用</button>
                </div>
                
                <div class="tablenav-pages">
                    <?php if ($total_pages > 1): ?>
                        <?php echo $page_links; ?>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
    
    <style>
    .gi-seo-manager .tablenav-pages .page-numbers {
        padding: 5px 10px;
        margin: 0 2px;
        background: white;
        border: 1px solid #ddd;
        text-decoration: none;
        color: #2271b1;
        border-radius: 4px;
    }
    .gi-seo-manager .tablenav-pages .page-numbers.current {
        background: #2271b1;
        color: white;
        border-color: #2271b1;
    }
    .gi-seo-manager .tablenav-pages .page-numbers:hover {
        background: #f0f0f1;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // 全選択チェックボックス
        $('#cb-select-all').on('change', function() {
            $('input[name="post_ids[]"]').prop('checked', $(this).prop('checked'));
        });
        
        // 投稿タイプ変更時の募集状態フィルター表示/非表示
        $('#post_type').on('change', function() {
            var postType = $(this).val();
            if (postType === 'column') {
                $('#recruitment-filter').hide();
            } else {
                $('#recruitment-filter').show();
            }
        });
        
        // 一括操作確認
        $('form#posts-filter').on('submit', function(e) {
            var action = $('select[name="bulk_action"]:visible').val();
            if (!action) {
                return true;
            }
            
            var checked = $('input[name="post_ids[]"]:checked').length;
            if (checked === 0) {
                alert('記事を選択してください。');
                e.preventDefault();
                return false;
            }
            
            var actionText = (action === 'mark_revised') ? '修正済み' : '未修正';
            if (!confirm(checked + '件の記事を' + actionText + 'にしますか？')) {
                e.preventDefault();
                return false;
            }
        });
    });
    </script>
    <?php
}

/**
 * PV数順に記事を取得（高速化版 - 直接SQLクエリ使用）
 */
function gi_get_posts_with_pv($post_type, $revised_status, $recruitment_status, $pv_min, $pv_max, $sort_by, $per_page, $paged) {
    global $wpdb;
    
    // ベースクエリ構築
    $post_types = ($post_type === 'all') ? array('grant', 'column') : array($post_type);
    $post_type_placeholders = implode(',', array_fill(0, count($post_types), '%s'));
    
    // WHERE句構築
    $where = array();
    $where[] = "p.post_status = 'publish'";
    $where[] = $wpdb->prepare("p.post_type IN ($post_type_placeholders)", ...$post_types);
    
    // PV数フィルター
    $where[] = $wpdb->prepare("CAST(pv.meta_value AS UNSIGNED) BETWEEN %d AND %d", $pv_min, $pv_max);
    
    // 修正状態フィルター（シンプル化）
    if ($revised_status === 'yes') {
        $where[] = "rev.meta_value = 'yes'";
    } elseif ($revised_status === 'no') {
        $where[] = "(rev.meta_value IS NULL OR rev.meta_value != 'yes')";
    }
    
    // 募集状態フィルター（補助金のみ）
    if ($post_type !== 'column' && $recruitment_status !== 'all') {
        $current_date = current_time('Y-m-d');
        if ($recruitment_status === 'active') {
            $where[] = $wpdb->prepare("(deadline.meta_value >= %s OR deadline.meta_value IS NULL)", $current_date);
        } elseif ($recruitment_status === 'expired') {
            $where[] = $wpdb->prepare("deadline.meta_value < %s", $current_date);
        }
    }
    
    $where_clause = implode(' AND ', $where);
    
    // ORDER BY句構築
    $orderby_clause = 'CAST(pv.meta_value AS UNSIGNED) DESC';
    if ($sort_by === 'deadline') {
        $orderby_clause = 'deadline.meta_value ASC';
    } elseif ($sort_by === 'modified') {
        $orderby_clause = 'p.post_modified DESC';
    }
    
    // LIMIT/OFFSET
    $offset = ($paged - 1) * $per_page;
    
    // メインクエリ - 記事取得
    $sql = "
        SELECT DISTINCT p.ID, p.post_title, p.post_type, p.post_modified
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pv ON p.ID = pv.post_id AND pv.meta_key = 'views_count'
        LEFT JOIN {$wpdb->postmeta} rev ON p.ID = rev.post_id AND rev.meta_key = '_seo_content_revised'
        LEFT JOIN {$wpdb->postmeta} deadline ON p.ID = deadline.post_id AND deadline.meta_key = 'deadline_date'
        WHERE $where_clause
        ORDER BY $orderby_clause
        LIMIT %d OFFSET %d
    ";
    
    $results = $wpdb->get_results($wpdb->prepare($sql, $per_page, $offset));
    
    // WP_Postオブジェクトに変換
    $posts = array();
    foreach ($results as $result) {
        $posts[] = get_post($result->ID);
    }
    
    // 総数取得（高速化 - COUNT使用）
    $count_sql = "
        SELECT COUNT(DISTINCT p.ID) as total
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pv ON p.ID = pv.post_id AND pv.meta_key = 'views_count'
        LEFT JOIN {$wpdb->postmeta} rev ON p.ID = rev.post_id AND rev.meta_key = '_seo_content_revised'
        LEFT JOIN {$wpdb->postmeta} deadline ON p.ID = deadline.post_id AND deadline.meta_key = 'deadline_date'
        WHERE $where_clause
    ";
    
    $total = intval($wpdb->get_var($count_sql));
    
    // 統計情報取得（高速化 - SQLで集計）
    $stats_sql = "
        SELECT 
            COUNT(DISTINCT p.ID) as total,
            SUM(CASE WHEN rev.meta_value = 'yes' THEN 1 ELSE 0 END) as revised,
            SUM(CASE WHEN rev.meta_value != 'yes' OR rev.meta_value IS NULL THEN 1 ELSE 0 END) as unrevised,
            SUM(CAST(pv.meta_value AS UNSIGNED)) as total_pv,
            SUM(CASE WHEN p.post_type = 'grant' AND (deadline.meta_value >= %s OR deadline.meta_value IS NULL) THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN p.post_type = 'grant' AND deadline.meta_value < %s THEN 1 ELSE 0 END) as expired
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pv ON p.ID = pv.post_id AND pv.meta_key = 'views_count'
        LEFT JOIN {$wpdb->postmeta} rev ON p.ID = rev.post_id AND rev.meta_key = '_seo_content_revised'
        LEFT JOIN {$wpdb->postmeta} deadline ON p.ID = deadline.post_id AND deadline.meta_key = 'deadline_date'
        WHERE $where_clause
    ";
    
    $current_date = current_time('Y-m-d');
    $stats_row = $wpdb->get_row($wpdb->prepare($stats_sql, $current_date, $current_date));
    
    $stats = array(
        'total' => intval($stats_row->total),
        'revised' => intval($stats_row->revised),
        'unrevised' => intval($stats_row->unrevised),
        'total_pv' => intval($stats_row->total_pv),
        'active' => intval($stats_row->active),
        'expired' => intval($stats_row->expired)
    );
    
    return array(
        'posts' => $posts,
        'total' => $total,
        'stats' => $stats
    );
}

/**
 * 投稿編集画面に修正状態メタボックスを追加
 */
add_action('add_meta_boxes', 'gi_seo_revision_metabox');
function gi_seo_revision_metabox() {
    add_meta_box(
        'gi_seo_revision_status',
        '<span class="dashicons dashicons-chart-line" style="margin-right: 5px;"></span>SEO修正状態',
        'gi_seo_revision_metabox_callback',
        array('grant', 'column'),
        'side',
        'high'
    );
}

/**
 * メタボックスのコールバック
 */
function gi_seo_revision_metabox_callback($post) {
    wp_nonce_field('gi_seo_revision_metabox', 'gi_seo_revision_nonce');
    
    $revised = get_post_meta($post->ID, '_seo_content_revised', true);
    $revised_date = get_post_meta($post->ID, '_seo_revised_date', true);
    $revised_by = get_post_meta($post->ID, '_seo_revised_by', true);
    $pv_count = intval(get_post_meta($post->ID, 'views_count', true));
    
    ?>
    <div class="gi-seo-metabox" style="padding: 10px 0;">
        <!-- PV数表示 -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">総PV数</div>
            <div style="font-size: 28px; font-weight: 700;"><?php echo number_format($pv_count); ?></div>
        </div>
        
        <!-- 修正状態 -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">修正状態</label>
            <label style="display: flex; align-items: center; padding: 10px; background: #f0fdf4; border: 2px solid #10b981; border-radius: 6px; cursor: pointer; margin-bottom: 8px;">
                <input type="radio" name="seo_content_revised" value="yes" <?php checked($revised, 'yes'); ?> style="margin-right: 8px;">
                <span class="dashicons dashicons-yes-alt" style="color: #10b981; margin-right: 5px;"></span>
                <strong>修正済み</strong>
            </label>
            <label style="display: flex; align-items: center; padding: 10px; background: #fef2f2; border: 2px solid #ef4444; border-radius: 6px; cursor: pointer;">
                <input type="radio" name="seo_content_revised" value="no" <?php checked($revised !== 'yes'); ?> style="margin-right: 8px;">
                <span class="dashicons dashicons-dismiss" style="color: #ef4444; margin-right: 5px;"></span>
                <strong>未修正</strong>
            </label>
        </div>
        
        <!-- 修正履歴 -->
        <?php if ($revised === 'yes' && $revised_date): ?>
            <div style="background: #f9fafb; border-left: 3px solid #10b981; padding: 12px; border-radius: 4px; font-size: 12px;">
                <div style="font-weight: 600; color: #10b981; margin-bottom: 5px;">
                    <span class="dashicons dashicons-clock" style="font-size: 14px; vertical-align: middle;"></span>
                    修正日時
                </div>
                <div style="color: #6b7280;">
                    <?php echo date('Y年m月d日 H:i', strtotime($revised_date)); ?>
                </div>
                <?php if ($revised_by): 
                    $user = get_user_by('id', $revised_by);
                    if ($user):
                ?>
                    <div style="margin-top: 8px; color: #6b7280;">
                        <strong>修正者:</strong> <?php echo esc_html($user->display_name); ?>
                    </div>
                <?php 
                    endif;
                endif; 
                ?>
            </div>
        <?php endif; ?>
        
        <p style="margin-top: 15px; font-size: 12px; color: #6b7280; line-height: 1.6;">
            <span class="dashicons dashicons-info" style="font-size: 14px; vertical-align: middle;"></span>
            この情報はSEO記事管理画面で一覧管理されます。
        </p>
    </div>
    <?php
}

/**
 * メタボックスのデータ保存
 */
add_action('save_post', 'gi_seo_revision_save_metabox');
function gi_seo_revision_save_metabox($post_id) {
    // ノンス検証
    if (!isset($_POST['gi_seo_revision_nonce']) || !wp_verify_nonce($_POST['gi_seo_revision_nonce'], 'gi_seo_revision_metabox')) {
        return;
    }
    
    // 自動保存の場合はスキップ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // 修正状態保存
    if (isset($_POST['seo_content_revised'])) {
        $revised = sanitize_text_field($_POST['seo_content_revised']);
        update_post_meta($post_id, '_seo_content_revised', $revised);
        
        if ($revised === 'yes') {
            update_post_meta($post_id, '_seo_revised_date', current_time('mysql'));
            update_post_meta($post_id, '_seo_revised_by', get_current_user_id());
        } else {
            delete_post_meta($post_id, '_seo_revised_date');
            delete_post_meta($post_id, '_seo_revised_by');
        }
    }
}
