<?php
/**
 * Template Part: AI Chatbot (Floating)
 * AIチャットボット（フローティングアイコン）
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// データを$argsから取得
$post_id = isset($args['post_id']) ? $args['post_id'] : get_the_ID();
?>

<!-- フローティングAIチャットボタン -->
<div class="gus-floating-ai-chat">
    <button class="gus-ai-chat-trigger" aria-label="AIアシスタントに質問">
        <svg class="gus-ai-icon-default" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            <circle cx="9" cy="10" r="1" fill="currentColor"></circle>
            <circle cx="12" cy="10" r="1" fill="currentColor"></circle>
            <circle cx="15" cy="10" r="1" fill="currentColor"></circle>
        </svg>
        <svg class="gus-ai-icon-close" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>
    
    <div class="gus-ai-chat-badge">
        <span>AI</span>
    </div>
</div>

<!-- AIチャットウィンドウ -->
<div class="gus-ai-chat-window" id="gusAiChatWindow">
    <div class="gus-ai-chat-header">
        <div class="gus-ai-chat-header-content">
            <div class="gus-ai-avatar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>
            <div class="gus-ai-chat-title-wrap">
                <h3 class="gus-ai-chat-title">AIアシスタント</h3>
                <p class="gus-ai-chat-subtitle">補助金について質問してください</p>
            </div>
        </div>
        <button class="gus-ai-chat-close" aria-label="閉じる">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
    
    <div class="gus-ai-chat-messages" id="gusAiChatMessages">
        <!-- ウェルカムメッセージ -->
        <div class="gus-ai-message gus-ai-message-bot">
            <div class="gus-ai-message-avatar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
            </div>
            <div class="gus-ai-message-content">
                <p>こんにちは！この補助金について、何でもお気軽にご質問ください。</p>
                <div class="gus-ai-suggestions">
                    <button class="gus-ai-suggestion" data-question="申請条件について教えてください">
                        申請条件は？
                    </button>
                    <button class="gus-ai-suggestion" data-question="必要書類について教えてください">
                        必要書類は？
                    </button>
                    <button class="gus-ai-suggestion" data-question="申請の流れを教えてください">
                        申請方法は？
                    </button>
                    <button class="gus-ai-suggestion" data-question="採択されるためのポイントを教えてください">
                        採択のコツは？
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="gus-ai-chat-input-wrapper">
        <div class="gus-ai-chat-typing" id="gusAiTypingIndicator" style="display: none;">
            <div class="gus-typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <span class="gus-typing-text">AIが回答を作成中...</span>
        </div>
        
        <form class="gus-ai-chat-input-form" id="gusAiChatForm">
            <input type="text" 
                   class="gus-ai-chat-input" 
                   id="gusAiChatInput"
                   placeholder="質問を入力してください..."
                   autocomplete="off"
                   maxlength="500">
            <button type="submit" class="gus-ai-chat-send" aria-label="送信">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </form>
        
        <p class="gus-ai-disclaimer">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            AIが生成した回答です。正確な情報は公式サイトでご確認ください。
        </p>
    </div>
</div>

<!-- オーバーレイ -->
<div class="gus-ai-chat-overlay" id="gusAiChatOverlay"></div>

<script>
// AIチャット機能の初期化（既存のJavaScriptに統合）
if (typeof window.giSingleGrantSettings !== 'undefined') {
    window.giSingleGrantSettings.aiChat = {
        postId: <?php echo intval($post_id); ?>,
        enabled: true
    };
}
</script>
