<?php
/**
 * Template Part: Column Sidebar
 * コラムサイドバー（人気記事・タグ・関連補助金）
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 1.0.0
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit;
}

// 人気記事ランキングを取得
$popular_columns = gi_get_column_ranking(5, 'all');

// 注目タグを取得
$popular_tags = gi_get_column_tag_cloud(10);

// 関連補助金を取得（締切間近・新着・人気）
$urgent_grants = new WP_Query(array(
    'post_type' => 'grant',
    'posts_per_page' => 3,
    'meta_key' => 'application_deadline',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'application_deadline',
            'value' => date('Y-m-d'),
            'compare' => '>=',
            'type' => 'DATE',
        ),
    ),
));

$new_grants = new WP_Query(array(
    'post_type' => 'grant',
    'posts_per_page' => 3,
    'orderby' => 'date',
    'order' => 'DESC',
));
?>

<div class="column-sidebar space-y-6">

    <!-- 人気記事ランキング -->
    <div class="sidebar-widget bg-gray-50 rounded-lg p-5 shadow-sm">
        <h3 class="widget-title text-lg font-bold text-gray-900 mb-4 flex items-center border-b border-gray-200 pb-3">
            <span class="text-2xl mr-2">🔥</span>
            人気記事ランキング
        </h3>

        <?php if (!empty($popular_columns)): ?>
            <ol class="space-y-4">
                <?php foreach ($popular_columns as $index => $column): ?>
                    <li class="flex items-start group">
                        <!-- ランク番号 -->
                        <span class="rank-number bg-primary text-white rounded-full min-w-[28px] w-7 h-7 flex items-center justify-center text-sm font-bold mr-3 flex-shrink-0 shadow-sm group-hover:bg-primary-dark transition-colors">
                            <?php echo $index + 1; ?>
                        </span>
                        
                        <div class="flex-1 min-w-0">
                            <a href="<?php echo esc_url($column['permalink']); ?>" 
                               class="text-sm font-medium text-gray-900 hover:text-primary line-clamp-2 leading-tight transition-colors">
                                <?php echo esc_html($column['title']); ?>
                            </a>
                            <div class="text-xs text-gray-500 mt-1 flex items-center space-x-2">
                                <span>👁️ <?php echo number_format($column['view_count']); ?></span>
                                <span>•</span>
                                <span><?php echo esc_html($column['date']); ?></span>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <p class="text-sm text-gray-500 text-center py-4">まだ閲覧データがありません</p>
        <?php endif; ?>
    </div>

    <!-- 注目キーワード（タグクラウド） -->
    <?php if (!empty($popular_tags) && !is_wp_error($popular_tags)): ?>
        <div class="sidebar-widget bg-gray-50 rounded-lg p-5 shadow-sm">
            <h3 class="widget-title text-lg font-bold text-gray-900 mb-4 flex items-center border-b border-gray-200 pb-3">
                <span class="text-2xl mr-2">🏷️</span>
                注目キーワード
            </h3>

            <div class="tag-cloud flex flex-wrap gap-2">
                <?php foreach ($popular_tags as $tag): ?>
                    <a href="<?php echo get_term_link($tag); ?>" 
                       class="tag-item bg-white border border-gray-200 rounded-full px-3 py-1.5 text-xs text-gray-700 hover:bg-primary hover:text-white hover:border-primary transition-all shadow-sm hover:shadow">
                        #<?php echo esc_html($tag->name); ?>
                        <span class="text-gray-400 ml-1">(<?php echo $tag->count; ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- 関連補助金 -->
    <div class="sidebar-widget bg-gray-50 rounded-lg p-5 shadow-sm">
        <h3 class="widget-title text-lg font-bold text-gray-900 mb-4 flex items-center border-b border-gray-200 pb-3">
            <span class="text-2xl mr-2">💰</span>
            関連補助金
        </h3>

        <!-- タブナビゲーション -->
        <div class="grant-tabs flex border-b border-gray-200 mb-4 -mx-1">
            <button class="grant-tab-btn active flex-1 py-2 px-2 text-sm text-center transition-colors" data-tab="urgent">
                締切間近
            </button>
            <button class="grant-tab-btn flex-1 py-2 px-2 text-sm text-center transition-colors" data-tab="new">
                新着
            </button>
        </div>

        <!-- タブコンテンツ: 締切間近 -->
        <div class="grant-tab-content" id="grant-tab-urgent">
            <?php if ($urgent_grants->have_posts()): ?>
                <ul class="space-y-3">
                    <?php while ($urgent_grants->have_posts()): $urgent_grants->the_post(); ?>
                        <?php
                        $deadline = get_field('application_deadline');
                        $days_left = 0;
                        if ($deadline) {
                            $deadline_date = new DateTime($deadline);
                            $today = new DateTime();
                            $days_left = $today->diff($deadline_date)->days;
                        }
                        ?>
                        <li class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                            <a href="<?php the_permalink(); ?>" 
                               class="block hover:bg-white rounded p-2 -m-2 transition-all group">
                                <div class="text-sm font-medium text-gray-900 mb-1 group-hover:text-primary line-clamp-2 transition-colors">
                                    <?php the_title(); ?>
                                </div>
                                <div class="text-xs text-gray-500 flex items-center space-x-2">
                                    <?php if ($deadline): ?>
                                        <span class="flex items-center">
                                            📅 <?php echo date('Y/m/d', strtotime($deadline)); ?>
                                        </span>
                                        <?php if ($days_left <= 30): ?>
                                            <span class="text-error font-medium">
                                                残り<?php echo $days_left; ?>日
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">締切未定</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </ul>
            <?php else: ?>
                <p class="text-sm text-gray-500 text-center py-4">現在締切間近の補助金はありません</p>
            <?php endif; ?>
        </div>

        <!-- タブコンテンツ: 新着 -->
        <div class="grant-tab-content hidden" id="grant-tab-new">
            <?php if ($new_grants->have_posts()): ?>
                <ul class="space-y-3">
                    <?php while ($new_grants->have_posts()): $new_grants->the_post(); ?>
                        <li class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                            <a href="<?php the_permalink(); ?>" 
                               class="block hover:bg-white rounded p-2 -m-2 transition-all group">
                                <div class="text-sm font-medium text-gray-900 mb-1 group-hover:text-primary line-clamp-2 transition-colors">
                                    <?php the_title(); ?>
                                </div>
                                <div class="text-xs text-gray-500 flex items-center space-x-2">
                                    <span>📅 <?php echo get_the_date('Y/m/d'); ?></span>
                                    <?php
                                    $days_ago = floor((time() - get_the_time('U')) / DAY_IN_SECONDS);
                                    if ($days_ago <= 7):
                                    ?>
                                        <span class="bg-accent text-gray-900 px-2 py-0.5 rounded text-xs font-medium">
                                            NEW
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </ul>
            <?php else: ?>
                <p class="text-sm text-gray-500 text-center py-4">新着補助金はありません</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- コラム一覧へのリンク -->
    <div class="sidebar-cta bg-primary rounded-lg p-5 shadow-md text-center">
        <div class="text-white mb-3">
            <span class="text-3xl">📚</span>
        </div>
        <h4 class="text-white font-bold mb-2">すべてのコラムを見る</h4>
        <p class="text-white text-xs mb-4 opacity-90">
            補助金活用のヒントが満載
        </p>
        <a href="<?php echo get_post_type_archive_link('column'); ?>" 
           class="inline-block bg-white text-primary font-medium px-6 py-2 rounded-lg hover:bg-gray-100 transition-colors shadow-sm">
            コラム一覧へ
        </a>
    </div>

</div>

<script>
// タブ切り替え機能
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.grant-tab-btn');
    const tabContents = document.querySelectorAll('.grant-tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // すべてのタブボタンとコンテンツの active クラスを削除
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.add('hidden'));

            // クリックされたタブをアクティブに
            this.classList.add('active');
            const targetContent = document.getElementById('grant-tab-' + targetTab);
            if (targetContent) {
                targetContent.classList.remove('hidden');
            }
        });
    });
});
</script>

<style>
/* タブボタンスタイル */
.grant-tab-btn {
    color: #6b7280;
    border-bottom: 2px solid transparent;
    cursor: pointer;
}

.grant-tab-btn:hover {
    color: #374151;
    background-color: rgba(0, 0, 0, 0.02);
}

.grant-tab-btn.active {
    color: #059669;
    border-bottom-color: #059669;
    font-weight: 600;
}

/* ランク番号スタイル */
.rank-number {
    min-width: 28px;
}

/* テキスト切り詰め */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
