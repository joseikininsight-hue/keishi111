<?php
/**
 * Affiliate Stats Template - Enhanced Edition
 * アフィリエイト広告統計テンプレート - 強化版
 * 
 * 機能:
 * - 期間フィルター（7日/30日/90日/365日）
 * - 広告別フィルター
 * - 詳細統計表示（ページURL、カテゴリー、デバイス別）
 * - 日別グラフ表示
 * - CSVエクスポート機能
 */

if (!defined('ABSPATH')) {
    exit;
}

// 現在の期間とフィルター
$current_period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : '30';
$current_ad_id = isset($_GET['ad_id']) ? intval($_GET['ad_id']) : 0;

$period_labels = array(
    '7' => '過去7日間',
    '30' => '過去30日間',
    '90' => '過去90日間',
    '365' => '過去365日間'
);
?>

<div class="wrap ji-affiliate-admin">
    <h1>広告統計情報 <span style="font-size: 16px; font-weight: normal; color: #666;">- <?php echo esc_html($period_labels[$current_period]); ?></span></h1>
    <hr class="wp-header-end">
    
    <!-- フィルター -->
    <div class="ji-stats-filters" style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #c3c4c7; border-radius: 4px;">
        <form method="get" action="">
            <input type="hidden" name="page" value="ji-affiliate-stats">
            
            <div style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
                <div>
                    <label for="period" style="display: block; margin-bottom: 5px; font-weight: 600;">期間選択</label>
                    <select name="period" id="period" style="min-width: 150px;">
                        <?php foreach ($period_labels as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($current_period, $value); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="ad_id" style="display: block; margin-bottom: 5px; font-weight: 600;">広告選択</label>
                    <select name="ad_id" id="ad_id" style="min-width: 250px;">
                        <option value="0">すべての広告</option>
                        <?php if (!empty($all_ads)): ?>
                            <?php foreach ($all_ads as $ad): ?>
                                <option value="<?php echo esc_attr($ad->id); ?>" <?php selected($current_ad_id, $ad->id); ?>>
                                    <?php echo esc_html($ad->title); ?> (ID: <?php echo esc_html($ad->id); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-search" style="margin-top: 3px;"></span> 表示
                    </button>
                    <a href="?page=ji-affiliate-stats" class="button">
                        <span class="dashicons dashicons-image-rotate" style="margin-top: 3px;"></span> リセット
                    </a>
                </div>
                
                <?php if (!empty($stats)): ?>
                <div style="margin-left: auto;">
                    <button type="button" id="ji-export-csv" class="button">
                        <span class="dashicons dashicons-download" style="margin-top: 3px;"></span> CSVエクスポート
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- 基本統計サマリー -->
    <div class="ji-stats-summary">
        <h2>統計サマリー</h2>
        
        <?php if (!empty($stats)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>広告タイトル</th>
                        <th style="width: 200px;">配置位置</th>
                        <th style="width: 100px;">表示回数</th>
                        <th style="width: 100px;">クリック数</th>
                        <th style="width: 80px;">CTR（%）</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_impressions = 0;
                    $total_clicks = 0;
                    foreach ($stats as $stat): 
                        $total_impressions += $stat->total_impressions;
                        $total_clicks += $stat->total_clicks;
                        
                        $position_labels = explode(',', $stat->positions);
                        $display_positions = array_slice($position_labels, 0, 2);
                        $position_display = implode(', ', $display_positions);
                        if (count($position_labels) > 2) {
                            $position_display .= ' 他' . (count($position_labels) - 2) . '箇所';
                        }
                    ?>
                        <tr>
                            <td><?php echo esc_html($stat->id); ?></td>
                            <td>
                                <strong><?php echo esc_html($stat->title); ?></strong>
                                <?php if ($current_ad_id == 0): ?>
                                <br>
                                <a href="?page=ji-affiliate-stats&period=<?php echo esc_attr($current_period); ?>&ad_id=<?php echo esc_attr($stat->id); ?>" class="button button-small" style="margin-top: 5px;">
                                    詳細を見る
                                </a>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 12px;"><?php echo esc_html($position_display); ?></td>
                            <td><strong><?php echo number_format($stat->total_impressions); ?></strong></td>
                            <td><strong><?php echo number_format($stat->total_clicks); ?></strong></td>
                            <td>
                                <strong style="color: <?php echo $stat->ctr >= 2 ? '#00a32a' : ($stat->ctr >= 1 ? '#f0b849' : '#2c3338'); ?>">
                                    <?php echo number_format($stat->ctr, 2); ?>%
                                </strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f0f0f1; font-weight: bold;">
                        <td colspan="3">合計</td>
                        <td><?php echo number_format($total_impressions); ?></td>
                        <td><?php echo number_format($total_clicks); ?></td>
                        <td>
                            <?php 
                            $overall_ctr = $total_impressions > 0 ? ($total_clicks / $total_impressions) * 100 : 0;
                            echo number_format($overall_ctr, 2); 
                            ?>%
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- 日別グラフ -->
            <?php if (!empty($daily_stats)): ?>
            <div class="ji-stats-charts" style="margin-top: 40px;">
                <h3>日別推移グラフ</h3>
                <div class="ji-chart-container" style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px;">
                    <canvas id="ji-daily-chart" width="800" height="300"></canvas>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 詳細統計（特定広告が選択されている場合） -->
            <?php if ($current_ad_id > 0 && !empty($detailed_stats)): ?>
            <div class="ji-detailed-stats" style="margin-top: 40px;">
                <h3>詳細統計 - <?php 
                    $selected_ad = array_filter($all_ads, function($ad) use ($current_ad_id) {
                        return $ad->id == $current_ad_id;
                    });
                    $selected_ad = reset($selected_ad);
                    echo esc_html($selected_ad->title); 
                ?></h3>
                
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>イベント</th>
                            <th>配置位置</th>
                            <th>カテゴリー</th>
                            <th>デバイス</th>
                            <th>ページURL</th>
                            <th>回数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detailed_stats as $detail): ?>
                            <tr>
                                <td><?php echo esc_html(date('Y/m/d', strtotime($detail->date))); ?></td>
                                <td>
                                    <span class="ji-event-badge <?php echo esc_attr($detail->event_type); ?>">
                                        <?php echo $detail->event_type === 'impression' ? '表示' : 'クリック'; ?>
                                    </span>
                                </td>
                                <td style="font-size: 12px;"><?php echo esc_html($detail->position ?: '-'); ?></td>
                                <td><?php echo esc_html($detail->category_name ?: '-'); ?></td>
                                <td>
                                    <span class="dashicons dashicons-<?php echo $detail->device === 'mobile' ? 'smartphone' : 'desktop'; ?>"></span>
                                    <?php echo esc_html(ucfirst($detail->device)); ?>
                                </td>
                                <td style="font-size: 11px; max-width: 300px; word-break: break-all;">
                                    <?php if ($detail->page_url): ?>
                                        <a href="<?php echo esc_url($detail->page_url); ?>" target="_blank" rel="noopener">
                                            <?php echo esc_html(substr($detail->page_url, 0, 50) . (strlen($detail->page_url) > 50 ? '...' : '')); ?>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo number_format($detail->count); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <p>選択された期間の統計データがありません。</p>
        <?php endif; ?>
    </div>
</div>

<style>
.ji-affiliate-admin h2,
.ji-affiliate-admin h3 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #2271b1;
}

.ji-stats-summary {
    margin-top: 20px;
}

.ji-stats-charts {
    margin-top: 30px;
}

.ji-chart-container {
    max-width: 1000px;
}

.ji-event-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.ji-event-badge.impression {
    background: #e3f2fd;
    color: #1976d2;
}

.ji-event-badge.click {
    background: #fff3e0;
    color: #f57c00;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
jQuery(document).ready(function($) {
    // 日別グラフ
    <?php if (!empty($daily_stats)): ?>
    var ctx = document.getElementById('ji-daily-chart').getContext('2d');
    
    var dates = [<?php echo '"' . implode('", "', array_map(function($s) { return date('m/d', strtotime($s->date)); }, $daily_stats)) . '"'; ?>];
    var impressions = [<?php echo implode(', ', array_map(function($s) { return $s->impressions; }, $daily_stats)); ?>];
    var clicks = [<?php echo implode(', ', array_map(function($s) { return $s->clicks; }, $daily_stats)); ?>];
    
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: '表示回数',
                    data: impressions,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'クリック数',
                    data: clicks,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                title: {
                    display: true,
                    text: '日別推移',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    <?php endif; ?>
    
    // CSVエクスポート機能
    $('#ji-export-csv').on('click', function() {
        var csv = [];
        var headers = ['ID', '広告タイトル', '配置位置', '表示回数', 'クリック数', 'CTR(%)'];
        csv.push(headers.join(','));
        
        <?php foreach ($stats as $stat): ?>
        csv.push([
            '<?php echo $stat->id; ?>',
            '<?php echo addslashes($stat->title); ?>',
            '<?php echo addslashes($stat->positions); ?>',
            '<?php echo $stat->total_impressions; ?>',
            '<?php echo $stat->total_clicks; ?>',
            '<?php echo number_format($stat->ctr, 2); ?>'
        ].join(','));
        <?php endforeach; ?>
        
        var csvContent = '\uFEFF' + csv.join('\n'); // UTF-8 BOM for Excel
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'affiliate_stats_<?php echo date('Ymd'); ?>.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
</script>
