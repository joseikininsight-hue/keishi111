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
// ===============================================
// AI Chat Functionality
// ===============================================

(function() {
    'use strict';
    
    // AI Chat Elements
    const chatTrigger = document.querySelector('.gus-ai-chat-trigger');
    const chatWindow = document.getElementById('gusAiChatWindow');
    const chatOverlay = document.getElementById('gusAiChatOverlay');
    const chatClose = document.querySelector('.gus-ai-chat-close');
    const chatForm = document.getElementById('gusAiChatForm');
    const chatInput = document.getElementById('gusAiChatInput');
    const chatMessages = document.getElementById('gusAiChatMessages');
    const typingIndicator = document.getElementById('gusAiTypingIndicator');
    
    if (!chatTrigger || !chatWindow) return;
    
    // Toggle Chat Window
    function toggleChatWindow() {
        const isActive = chatWindow.classList.contains('active');
        
        if (isActive) {
            closeChatWindow();
        } else {
            openChatWindow();
        }
    }
    
    function openChatWindow() {
        chatWindow.classList.add('active');
        chatOverlay.classList.add('active');
        chatTrigger.classList.add('active');
        chatInput.focus();
    }
    
    function closeChatWindow() {
        chatWindow.classList.remove('active');
        chatOverlay.classList.remove('active');
        chatTrigger.classList.remove('active');
    }
    
    // Add Message to Chat
    function addMessage(message, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `gus-ai-message ${isUser ? 'gus-ai-message-user' : 'gus-ai-message-bot'}`;
        
        messageDiv.innerHTML = `
            <div class="gus-ai-message-avatar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    ${isUser ? 
                        '<circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path>' :
                        '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>'
                    }
                </svg>
            </div>
            <div class="gus-ai-message-content">
                <p>${message}</p>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Send Message
    async function sendMessage(question) {
        if (!question.trim()) return;
        
        // Add user message
        addMessage(question, true);
        chatInput.value = '';
        
        // Show typing indicator
        typingIndicator.style.display = 'flex';
        
        try {
            const response = await fetch(giSingleGrantSettings.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'gi_ai_question',
                    post_id: giSingleGrantSettings.postId,
                    question: question,
                    nonce: giSingleGrantSettings.nonce
                })
            });
            
            const data = await response.json();
            
            // Hide typing indicator
            typingIndicator.style.display = 'none';
            
            if (data.success) {
                addMessage(data.data.answer, false);
            } else {
                addMessage('申し訳ありません。エラーが発生しました。もう一度お試しください。', false);
            }
        } catch (error) {
            typingIndicator.style.display = 'none';
            addMessage('申し訳ありません。接続エラーが発生しました。', false);
            console.error('AI Chat Error:', error);
        }
    }
    
    // Event Listeners
    chatTrigger.addEventListener('click', toggleChatWindow);
    chatClose.addEventListener('click', closeChatWindow);
    chatOverlay.addEventListener('click', closeChatWindow);
    
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage(chatInput.value);
    });
    
    // Suggestion Buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('gus-ai-suggestion')) {
            const question = e.target.dataset.question;
            sendMessage(question);
        }
    });
    
    // Favorite Button
    document.addEventListener('click', function(e) {
        const favoriteBtn = e.target.closest('.gus-favorite-btn');
        if (!favoriteBtn) return;
        
        e.preventDefault();
        const postId = favoriteBtn.dataset.postId;
        
        fetch(giSingleGrantSettings.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'gi_toggle_favorite',
                post_id: postId,
                nonce: giSingleGrantSettings.nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                favoriteBtn.classList.toggle('is-favorite');
                const svg = favoriteBtn.querySelector('svg');
                const span = favoriteBtn.querySelector('span');
                
                if (data.data.is_favorite) {
                    svg.setAttribute('fill', 'currentColor');
                    span.textContent = 'お気に入り済み';
                } else {
                    svg.setAttribute('fill', 'none');
                    span.textContent = 'お気に入り';
                }
            }
        })
        .catch(error => console.error('Favorite Error:', error));
    });
    
    // Print Button
    const printBtn = document.querySelector('.gus-print-btn');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }
    
    // Share Button
    const shareBtn = document.querySelector('.gus-share-btn');
    if (shareBtn) {
        shareBtn.addEventListener('click', async function() {
            const shareData = {
                title: document.title,
                text: document.querySelector('meta[name="description"]')?.content || '',
                url: window.location.href
            };
            
            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                } else {
                    // Fallback: Copy to clipboard
                    await navigator.clipboard.writeText(window.location.href);
                    alert('URLをコピーしました');
                }
            } catch (error) {
                console.log('Share Error:', error);
            }
        });
    }
})();
