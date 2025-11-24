<?php
/**
 * Template Part: Detail Information
 * 詳細情報（DLタグでPC/SP統合）
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// データを$argsから取得
$grant_data = isset($args['grant_data']) ? $args['grant_data'] : array();
$formatted_amount = isset($args['formatted_amount']) ? $args['formatted_amount'] : '';
$deadline_info = isset($args['deadline_info']) ? $args['deadline_info'] : '';
$deadline_class = isset($args['deadline_class']) ? $args['deadline_class'] : '';
$difficulty_data = isset($args['difficulty_data']) ? $args['difficulty_data'] : array();
$prefecture_display = isset($args['prefecture_display']) ? $args['prefecture_display'] : '';
$municipality_display = isset($args['municipality_display']) ? $args['municipality_display'] : '';
$taxonomies = isset($args['taxonomies']) ? $args['taxonomies'] : array();
?>

<section class="gus-detail-section" id="details">
    <h2 class="gus-section-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        この補助金の詳細情報
    </h2>
    
    <dl class="gus-detail-list">
        <!-- 補助金額 -->
        <?php if ($formatted_amount): ?>
        <div class="gus-detail-item gus-detail-highlight">
            <dt class="gus-detail-term">補助金額（最大）</dt>
            <dd class="gus-detail-desc gus-amount-value"><?php echo esc_html($formatted_amount); ?></dd>
        </div>
        <?php endif; ?>
        
        <!-- 補助率 -->
        <?php if (!empty($grant_data['subsidy_rate'])): ?>
        <div class="gus-detail-item">
            <dt class="gus-detail-term">補助率</dt>
            <dd class="gus-detail-desc">
                <strong><?php echo esc_html($grant_data['subsidy_rate']); ?></strong>
                <?php if (!empty($grant_data['subsidy_rate_detailed'])): ?>
                    <div class="gus-detail-sub"><?php echo wp_kses_post($grant_data['subsidy_rate_detailed']); ?></div>
                <?php endif; ?>
            </dd>
        </div>
        <?php endif; ?>
        
        <!-- 申請締切 -->
        <?php if ($deadline_info): ?>
        <div class="gus-detail-item <?php echo $deadline_class ? 'gus-detail-urgent' : ''; ?>">
            <dt class="gus-detail-term">申請締切</dt>
            <dd class="gus-detail-desc">
                <strong class="<?php echo $deadline_class; ?>"><?php echo esc_html($deadline_info); ?></strong>
                <?php if (!empty($grant_data['application_period'])): ?>
                    <div class="gus-detail-sub">申請期間: <?php echo esc_html($grant_data['application_period']); ?></div>
                <?php endif; ?>
            </dd>
        </div>
        <?php endif; ?>
        
        <!-- 難易度・採択率 -->
        <div class="gus-detail-item">
            <dt class="gus-detail-term">難易度 / 採択率</dt>
            <dd class="gus-detail-desc">
                <div class="gus-difficulty-display">
                    <span class="gus-difficulty-label"><?php echo esc_html($difficulty_data['label']); ?></span>
                    <div class="gus-difficulty-dots">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <span class="gus-difficulty-dot <?php echo $i <= $difficulty_data['dots'] ? 'filled' : ''; ?>"></span>
                        <?php endfor; ?>
                    </div>
                    <span class="gus-difficulty-desc">(<?php echo esc_html($difficulty_data['description']); ?>)</span>
                </div>
                <?php if (!empty($grant_data['adoption_rate']) && $grant_data['adoption_rate'] > 0): ?>
                    <div class="gus-detail-sub">採択実績: <strong><?php echo number_format($grant_data['adoption_rate'], 1); ?>%</strong></div>
                <?php endif; ?>
            </dd>
        </div>
        
        <!-- 主催機関 -->
        <?php if (!empty($grant_data['organization'])): ?>
        <div class="gus-detail-item">
            <dt class="gus-detail-term">主催機関</dt>
            <dd class="gus-detail-desc"><?php echo esc_html($grant_data['organization']); ?></dd>
        </div>
        <?php endif; ?>
        
        <!-- 対象者・対象事業 -->
        <?php if (!empty($grant_data['grant_target'])): ?>
        <div class="gus-detail-item">
            <dt class="gus-detail-term">対象者・対象事業</dt>
            <dd class="gus-detail-desc"><?php echo wp_kses_post($grant_data['grant_target']); ?></dd>
        </div>
        <?php endif; ?>
        
        <!-- 対象経費 -->
        <?php 
        $expenses_content = !empty($grant_data['eligible_expenses_detailed']) 
            ? $grant_data['eligible_expenses_detailed'] 
            : $grant_data['eligible_expenses'];
        ?>
        <?php if ($expenses_content): ?>
        <div class="gus-detail-item">
            <dt class="gus-detail-term">対象経費</dt>
            <dd class="gus-detail-desc"><?php echo wp_kses_post($expenses_content); ?></dd>
        </div>
        <?php endif; ?>
        
        <!-- 必要書類 -->
        <?php 
        $documents_content = !empty($grant_data['required_documents_detailed']) 
            ? $grant_data['required_documents_detailed'] 
            : $grant_data['required_documents'];
        ?>
        <?php if ($documents_content): ?>
        <div class="gus-detail-item">
            <dt class="gus-detail-term">必要書類</dt>
            <dd class="gus-detail-desc"><?php echo wp_kses_post($documents_content); ?></dd>
        </div>
        <?php endif; ?>
        
        <!-- 対象地域 -->
        <?php if ($prefecture_display || $municipality_display): ?>
        <div class="gus-detail-item">
            <dt class="gus-detail-term">対象地域</dt>
            <dd class="gus-detail-desc">
                <?php echo esc_html($prefecture_display); ?>
                <?php if ($municipality_display): ?>
                    <div class="gus-detail-sub">市町村: <?php echo esc_html($municipality_display); ?></div>
                <?php endif; ?>
                <?php if (!empty($grant_data['area_notes'])): ?>
                    <div class="gus-detail-note"><?php echo esc_html($grant_data['area_notes']); ?></div>
                <?php endif; ?>
            </dd>
        </div>
        <?php endif; ?>
        
        <!-- 申請方法 -->
        <?php if (!empty($grant_data['application_method'])): ?>
        <div class="gus-detail-item">
            <dt class="gus-detail-term">申請方法</dt>
            <dd class="gus-detail-desc"><?php echo nl2br(esc_html($grant_data['application_method'])); ?></dd>
        </div>
        <?php endif; ?>
        
        <!-- 更新状況 -->
        <div class="gus-detail-item gus-detail-meta">
            <dt class="gus-detail-term">更新状況</dt>
            <dd class="gus-detail-desc">
                最終更新: <?php echo get_the_modified_date('Y年n月j日'); ?> / 
                閲覧数: <?php echo number_format($grant_data['views_count']); ?> 回
            </dd>
        </div>
    </dl>
    
    <!-- タグセクション -->
    <?php if (!empty($taxonomies['categories']) || !empty($taxonomies['tags'])): ?>
    <div class="gus-tags-section">
        <?php if (!empty($taxonomies['categories'])): ?>
        <div class="gus-tags-group">
            <span class="gus-tags-label">カテゴリー</span>
            <div class="gus-tags-list">
                <?php foreach ($taxonomies['categories'] as $cat): ?>
                    <a href="<?php echo get_term_link($cat); ?>" class="gus-tag gus-tag-category">
                        <?php echo esc_html($cat->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($taxonomies['tags'])): ?>
        <div class="gus-tags-group">
            <span class="gus-tags-label">タグ</span>
            <div class="gus-tags-list">
                <?php foreach ($taxonomies['tags'] as $tag): ?>
                    <a href="<?php echo get_term_link($tag); ?>" class="gus-tag gus-tag-tag">
                        #<?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>
