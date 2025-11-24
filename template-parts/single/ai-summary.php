<?php
/**
 * Template Part: AI Summary Box
 * AI要約ボックス
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// データを$argsから取得
$ai_summary = isset($args['ai_summary']) ? $args['ai_summary'] : '';

// AI要約がない場合は表示しない
if (empty($ai_summary)) {
    return;
}
?>

<div class="gus-ai-summary">
    <div class="gus-ai-summary-header">
        <div class="gus-ai-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
        </div>
        <h3 class="gus-ai-summary-title">AI要約 - この補助金のポイント</h3>
        <span class="gus-ai-badge">AI生成</span>
    </div>
    
    <div class="gus-ai-summary-content">
        <?php echo wp_kses_post(wpautop($ai_summary)); ?>
    </div>
    
    <div class="gus-ai-summary-footer">
        <p class="gus-ai-disclaimer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            この要約はAIによって生成されています。詳細は公式情報をご確認ください。
        </p>
    </div>
</div>
