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
