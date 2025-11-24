/*!
 * Grant Insight Perfect - フロントエンド統合JavaScript (完全修正版)
 * unified-frontend.js + column.js + grant-viewing-history.js
 * 
 * @version 2.1.0
 * @date 2025-11-21
 * @description 検索機能の問題を完全に解決
 */

// ============================================================================
// PART 1: Grant Viewing History Tracker (Cookie-based)
// ============================================================================
/**
 * Grant Viewing History Tracker
 * 補助金閲覧履歴トラッキングシステム (Cookie-based)
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

(function() {
    'use strict';
    
    const COOKIE_NAME = 'gi_grant_viewing_history';
    const MAX_HISTORY_ITEMS = 50;
    const COOKIE_EXPIRY_DAYS = 90;
    
    function getViewingHistory() {
        const cookie = document.cookie
            .split('; ')
            .find(row => row.startsWith(COOKIE_NAME + '='));
        
        if (!cookie) {
            return [];
        }
        
        try {
            const value = decodeURIComponent(cookie.split('=')[1]);
            const history = JSON.parse(value);
            return Array.isArray(history) ? history : [];
        } catch (e) {
            console.error('[Viewing History] Parse error:', e);
            return [];
        }
    }
    
    function saveViewingHistory(history) {
        try {
            const value = encodeURIComponent(JSON.stringify(history));
            const expiry = new Date();
            expiry.setDate(expiry.getDate() + COOKIE_EXPIRY_DAYS);
            
            document.cookie = `${COOKIE_NAME}=${value}; expires=${expiry.toUTCString()}; path=/; SameSite=Lax`;
            return true;
        } catch (e) {
            console.error('[Viewing History] Save error:', e);
            return false;
        }
    }
    
    function trackGrantView(grantId, grantData = {}) {
        if (!grantId) {
            return false;
        }
        
        let history = getViewingHistory();
        history = history.filter(item => item.id !== grantId);
        
        history.unshift({
            id: grantId,
            title: grantData.title || '',
            category: grantData.category || '',
            prefecture: grantData.prefecture || '',
            timestamp: Date.now(),
            viewCount: 1
        });
        
        if (history.length > MAX_HISTORY_ITEMS) {
            history = history.slice(0, MAX_HISTORY_ITEMS);
        }
        
        return saveViewingHistory(history);
    }
    
    function getFrequentCategories(limit = 3) {
        const history = getViewingHistory();
        const categoryCount = {};
        
        history.forEach(item => {
            if (item.category) {
                categoryCount[item.category] = (categoryCount[item.category] || 0) + 1;
            }
        });
        
        const sorted = Object.entries(categoryCount)
            .sort((a, b) => b[1] - a[1])
            .slice(0, limit)
            .map(entry => entry[0]);
        
        return sorted;
    }
    
    function getFrequentPrefectures(limit = 3) {
        const history = getViewingHistory();
        const prefectureCount = {};
        
        history.forEach(item => {
            if (item.prefecture) {
                prefectureCount[item.prefecture] = (prefectureCount[item.prefecture] || 0) + 1;
            }
        });
        
        const sorted = Object.entries(prefectureCount)
            .sort((a, b) => b[1] - a[1])
            .slice(0, limit)
            .map(entry => entry[0]);
        
        return sorted;
    }
    
    function initSingleGrantTracking() {
        if (!document.body.classList.contains('single-grant')) {
            return;
        }
        
        const grantData = {
            id: document.body.dataset.grantId,
            title: document.body.dataset.grantTitle || document.title,
            category: document.body.dataset.grantCategory || '',
            prefecture: document.body.dataset.grantPrefecture || ''
        };
        
        if (grantData.id) {
            trackGrantView(grantData.id, grantData);
            console.log('[Viewing History] Tracked grant view:', grantData.id);
        }
    }
    
    function fetchPersonalizedGrants(callback) {
        const categories = getFrequentCategories();
        const prefectures = getFrequentPrefectures();
        
        if (categories.length === 0 && prefectures.length === 0) {
            callback(null, { hasHistory: false });
            return;
        }
        
        if (typeof wp !== 'undefined' && wp.ajax) {
            wp.ajax.post('get_personalized_grants', {
                categories: categories,
                prefectures: prefectures
            }).done(function(response) {
                callback(null, { hasHistory: true, grants: response });
            }).fail(function(error) {
                callback(error, null);
            });
        } else {
            fetch(window.giAjaxUrl || '/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'get_personalized_grants',
                    categories: categories.join(','),
                    prefectures: prefectures.join(',')
                })
            })
            .then(response => response.json())
            .then(data => {
                callback(null, { hasHistory: true, grants: data });
            })
            .catch(error => {
                callback(error, null);
            });
        }
    }
    
    function debugHistory() {
        const history = getViewingHistory();
        console.log('[Viewing History] Total items:', history.length);
        console.log('[Viewing History] Categories:', getFrequentCategories());
        console.log('[Viewing History] Prefectures:', getFrequentPrefectures());
        console.table(history.slice(0, 10));
    }
    
    window.giViewingHistory = {
        track: trackGrantView,
        getHistory: getViewingHistory,
        getFrequentCategories: getFrequentCategories,
        getFrequentPrefectures: getFrequentPrefectures,
        fetchPersonalized: fetchPersonalizedGrants,
        debug: debugHistory
    };
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSingleGrantTracking);
    } else {
        initSingleGrantTracking();
    }
    
    console.log('[OK] Grant Viewing History Tracker initialized');
    
})();



// ============================================================================
// PART 2: Column System (Tab Navigation, Infinite Scroll, Search)
// ============================================================================
/**
 * Column System JavaScript
 * タブ切り替え、Ajax読み込み、インタラクション
 * 
 * @package Grant_Insight_Perfect
 * @subpackage Column_System
 * @version 2.0.0
 */

(function() {
    'use strict';

    let currentPage = 1;
    let currentCategory = 'all';
    let isLoading = false;
    let hasMorePosts = true;

    document.addEventListener('DOMContentLoaded', function() {
        console.log('[Column System] Initializing Phase 2...');

        initTabNavigation();
        initInfiniteScroll();
        initSmoothScroll();
        initColumnSearch();

        console.log('[Column System] Initialized successfully (Phase 2)');
    });

    function initTabNavigation() {
        const tabLinks = document.querySelectorAll('.column-tab-link');
        
        if (tabLinks.length === 0) {
            return;
        }

        tabLinks.forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                const category = this.getAttribute('data-category');
                console.log('[Column Tab] Switching to category:', category);

                if (this.classList.contains('active') && category === currentCategory) {
                    return;
                }

                tabLinks.forEach(function(t) {
                    t.classList.remove('active');
                });

                this.classList.add('active');

                currentCategory = category;
                currentPage = 1;
                hasMorePosts = true;

                loadColumnsByCategory(category, true);
            });
        });
    }

    function loadColumnsByCategory(category, replace = true) {
        const grid = document.getElementById('column-article-grid');
        const loading = document.getElementById('column-loading');

        if (!grid || !loading) {
            console.warn('[Column Ajax] Required elements not found');
            return;
        }

        if (isLoading) {
            console.log('[Column Ajax] Already loading, skipping...');
            return;
        }

        isLoading = true;
        loading.classList.remove('hidden');

        if (replace) {
            const gridTop = grid.getBoundingClientRect().top + window.pageYOffset - 100;
            window.scrollTo({
                top: gridTop,
                behavior: 'smooth'
            });
        }
        
        fetch(gi_column_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'gi_get_columns',
                nonce: gi_column_ajax.nonce,
                category: category,
                paged: currentPage,
                per_page: 6
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (replace) {
                    grid.innerHTML = data.data.html;
                    console.log('[Column Ajax] Replaced content:', data.data.found_posts, 'posts');
                } else {
                    grid.insertAdjacentHTML('beforeend', data.data.html);
                    console.log('[Column Ajax] Appended content:', data.data.found_posts, 'posts');
                }

                hasMorePosts = data.data.has_more;
                console.log('[Column Ajax] Has more posts:', hasMorePosts);

                animateCards();
            } else {
                console.error('[Column Ajax] Error:', data.data);
                if (replace) {
                    grid.innerHTML = '<div class="col-span-2 text-center py-12 text-gray-500">' +
                                   '<p class="text-xl mb-2">😔</p>' +
                                   '<p>記事が見つかりませんでした。</p>' +
                                   '</div>';
                }
            }
        })
        .catch(error => {
            console.error('[Column Ajax] Fetch error:', error);
            if (replace) {
                grid.innerHTML = '<div class="col-span-2 text-center py-12 text-red-500">' +
                               '<p class="text-xl mb-2">❌</p>' +
                               '<p>記事の読み込みに失敗しました。</p>' +
                               '<p class="text-sm mt-2">しばらくしてから再度お試しください。</p>' +
                               '</div>';
            }
        })
        .finally(() => {
            loading.classList.add('hidden');
            isLoading = false;
        });
    }

    function animateCards() {
        const cards = document.querySelectorAll('.column-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });
    }

    function initSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                if (!href || href === '#' || href === '#0') {
                    return;
                }

                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    
                    const offset = 80;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    function initInfiniteScroll() {
        const grid = document.getElementById('column-article-grid');
        
        if (!grid) {
            console.log('[Infinite Scroll] Grid not found, skipping initialization');
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading && hasMorePosts) {
                    console.log('[Infinite Scroll] Loading more posts...');
                    currentPage++;
                    loadColumnsByCategory(currentCategory, false);
                }
            });
        }, {
            root: null,
            rootMargin: '200px',
            threshold: 0.1
        });

        const sentinel = document.createElement('div');
        sentinel.id = 'infinite-scroll-sentinel';
        sentinel.style.height = '10px';
        
        const container = document.getElementById('column-grid-container');
        if (container) {
            container.appendChild(sentinel);
            observer.observe(sentinel);
            console.log('[Infinite Scroll] Initialized successfully');
        }
    }

    function initColumnSearch() {
        const searchForm = document.getElementById('column-search-form');
        const searchInput = document.getElementById('column-search-input');
        
        if (!searchForm || !searchInput) {
            console.log('[Column Search] Search elements not found');
            return;
        }

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = searchInput.value.trim();
            
            if (query.length < 2) {
                alert('2文字以上で検索してください');
                return;
            }

            console.log('[Column Search] Searching for:', query);
            performSearch(query);
        });

        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    console.log('[Column Search] Real-time search:', query);
                    performSearch(query);
                }, 500);
            }
        });
    }

    function performSearch(query) {
        const grid = document.getElementById('column-article-grid');
        const loading = document.getElementById('column-loading');

        if (!grid || !loading) {
            return;
        }

        loading.classList.remove('hidden');

        fetch(gi_column_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'gi_search_columns',
                nonce: gi_column_ajax.nonce,
                query: query,
                paged: 1,
                per_page: 12
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                grid.innerHTML = data.data.html;
                
                const resultsCount = data.data.found_posts;
                showSearchResults(query, resultsCount);
                
                animateCards();
            } else {
                grid.innerHTML = '<div class="col-span-2 text-center py-12 text-gray-500">' +
                               '<p class="text-xl mb-2">🔍</p>' +
                               '<p>「' + query + '」の検索結果が見つかりませんでした。</p>' +
                               '</div>';
            }
        })
        .catch(error => {
            console.error('[Column Search] Error:', error);
        })
        .finally(() => {
            loading.classList.add('hidden');
        });
    }

    function showSearchResults(query, count) {
        const container = document.getElementById('column-grid-container');
        
        if (!container) {
            return;
        }

        const existingResult = document.getElementById('search-result-info');
        if (existingResult) {
            existingResult.remove();
        }

        const resultInfo = document.createElement('div');
        resultInfo.id = 'search-result-info';
        resultInfo.className = 'mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg';
        resultInfo.innerHTML = `
            <p class="text-sm text-gray-700">
                <strong class="text-blue-600">"${query}"</strong> の検索結果: 
                <strong>${count}件</strong>
            </p>
            <button onclick="location.reload()" class="text-sm text-blue-600 hover:underline mt-2">
                × 検索をクリア
            </button>
        `;

        container.insertBefore(resultInfo, container.firstChild);
    }

    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            console.log('[Column System] Window resized');
        }, 250);
    });

})();



// ============================================================================
// PART 3: Main Frontend Application (GrantInsight Namespace) - 完全修正版
// ============================================================================
/*!
 * Grant Insight Perfect - 統合フロントエンドJavaScript (完全修正版)
 * 
 * @version 2.1.0
 * @date 2025-11-21
 * @description 検索機能の問題を完全に解決
 */

const GrantInsight = {
    version: '2.1.0',
    
    config: {
        debounceDelay: 300,
        toastDuration: 3000,
        scrollTrackingInterval: 250,
        apiEndpoint: '/wp-admin/admin-ajax.php',
        searchMinLength: 2,
        maxComparisonItems: 3
    },

    initialized: false,
    
    state: {
        lastScrollY: 0,
        headerHeight: 0,
        isScrolling: false,
        activeFilters: new Map(),
        comparisonItems: [],
        touchStartY: 0,
        touchEndY: 0
    },

    elements: {},

    /**
     * 初期化システム
     */
    init() {
        if (this.initialized) return;
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupAll());
        } else {
            this.setupAll();
        }
    },

    /**
     * 全機能のセットアップ
     */
    setupAll() {
        try {
            console.log('[Grant Insight] Starting initialization...');
            
            this.cacheElements();
            this.setupUtils();
            this.setupSearch();
            this.setupFilters();
            this.setupComparison();
            this.setupMobile();
            this.setupAccessibility();
            this.setupPerformance();
            this.setupAnimations();
            this.setupForms();
            this.setupAIButtonListeners();
            
            this.initialized = true;
            console.log('[Grant Insight] ✅ Initialized successfully (v' + this.version + ')');
        } catch (error) {
            console.error('[Grant Insight] ❌ Initialization error:', error);
        }
    },

    /**
     * DOM要素のキャッシュ - 修正版（柔軟な検索）
     */
    cacheElements() {
        console.log('[Grant Insight] Caching DOM elements...');
        
        // 複数のセレクタで検索入力を探す
        const searchSelectors = [
            '#clean-search-input',
            '#gi-search-input',
            '.search-input',
            'input[type="search"]',
            '.gi-search-input',
            '.grant-search-input',
            'input[name="s"]',
            'input[placeholder*="検索"]',
            'input[placeholder*="search" i]'
        ];
        
        let searchInputs = [];
        for (const selector of searchSelectors) {
            const found = document.querySelectorAll(selector);
            if (found.length > 0) {
                searchInputs = Array.from(found);
                console.log(`[Grant Insight] ✅ Found ${found.length} search input(s) with selector: ${selector}`);
                break;
            }
        }
        
        if (searchInputs.length === 0) {
            console.warn('[Grant Insight] ⚠️ No search inputs found. Will create mobile header.');
        }
        
        this.elements = {
            // 検索関連
            searchInputs: searchInputs,
            searchContainer: document.querySelector('.clean-search-wrapper, .search-wrapper, .gi-search-container'),
            searchSuggestions: null,
            
            // フィルター関連
            filterButtons: document.querySelectorAll('.clean-filter-pill, .filter-button, .gi-filter-chip'),
            filterTrigger: document.getElementById('clean-filter-toggle') || document.querySelector('.filter-toggle'),
            
            // コンテンツ関連
            grantsGrid: document.getElementById('clean-grants-container') || document.querySelector('.grants-grid, .gi-grants-grid'),
            
            // UI要素
            header: document.querySelector('.clean-header, .site-header, header'),
            body: document.body,
            
            comparisonBar: null
        };
        
        console.log('[Grant Insight] Cached elements:', {
            searchInputs: this.elements.searchInputs.length,
            filterButtons: this.elements.filterButtons.length,
            hasGrantsGrid: !!this.elements.grantsGrid,
            hasHeader: !!this.elements.header
        });
    },

    /**
     * ユーティリティ関数群
     */
    setupUtils() {
        this.escapeHtml = function(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        };

        this.debounce = function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        this.throttle = function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        };

        this.showToast = function(message, type = 'info') {
            const existingToast = document.querySelector('.gi-toast, .ui-notification');
            if (existingToast) {
                existingToast.remove();
            }
            
            const toast = document.createElement('div');
            toast.className = `gi-toast gi-toast-${type}`;
            toast.innerHTML = `
                <div class="gi-toast-content">
                    <span class="gi-toast-message">${this.escapeHtml(message)}</span>
                    <button class="gi-toast-close" aria-label="閉じる">×</button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            requestAnimationFrame(() => {
                toast.classList.add('gi-toast-show');
            });
            
            toast.querySelector('.gi-toast-close').addEventListener('click', () => {
                this.hideToast(toast);
            });
            
            setTimeout(() => {
                this.hideToast(toast);
            }, this.config.toastDuration);
            
            return toast;
        };

        this.hideToast = function(toast) {
            toast.classList.remove('gi-toast-show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        };

        this.ajax = function(action, data = {}, options = {}) {
            const url = options.url || this.config.apiEndpoint;
            
            const requestData = {
                action: action,
                nonce: window.gi_ajax?.nonce || options.nonce,
                ...data
            };

            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    ...options.headers
                },
                body: new URLSearchParams(requestData).toString(),
                ...options
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            });
        };

        this.debug = function(message, ...args) {
            if (window.location.hostname === 'localhost' || window.location.search.includes('debug=1')) {
                console.log(`[Grant Insight] ${message}`, ...args);
            }
        };
    },

    /**
     * 検索機能（完全修正版）
     */
    setupSearch() {
        console.log('[Grant Insight] Setting up search functionality...');
        console.log('[Grant Insight] Search inputs found:', this.elements.searchInputs.length);
        
        if (this.elements.searchInputs.length === 0) {
            console.warn('[Grant Insight] ⚠️ No search inputs found in DOM');
            console.log('[Grant Insight] Available input elements:', document.querySelectorAll('input'));
            
            // フォールバック：モバイルヘッダーを作成して検索ボックスを追加
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                console.log('[Grant Insight] Creating mobile header with search...');
                this.createMobileSearchHeader();
            }
            return;
        }

        this.elements.searchInputs.forEach((input, index) => {
            console.log(`[Grant Insight] Setting up search input #${index + 1}:`, input);
            
            const debouncedSearch = this.debounce((value) => {
                if (value.length >= this.config.searchMinLength) {
                    this.performSearch(value);
                    this.showSearchSuggestions(value);
                } else {
                    this.hideSearchSuggestions();
                }
            }, this.config.debounceDelay);

            input.addEventListener('input', (e) => {
                console.log('[Grant Insight] Search input changed:', e.target.value);
                debouncedSearch(e.target.value);
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    console.log('[Grant Insight] Search submitted:', e.target.value);
                    this.executeSearch(e.target.value);
                }
                
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    this.handleSuggestionNavigation(e);
                }

                if (e.key === 'Escape') {
                    this.hideSearchSuggestions();
                }
            });

            input.addEventListener('focus', () => {
                this.state.lastFocusedInput = input;
                if (input.value.length >= this.config.searchMinLength) {
                    this.showSearchSuggestions(input.value);
                }
            });

            input.addEventListener('blur', () => {
                setTimeout(() => this.hideSearchSuggestions(), 150);
            });
        });
        
        console.log('[Grant Insight] ✅ Search setup complete');
    },

    /**
     * モバイル検索ヘッダーの作成（新規）
     */
    createMobileSearchHeader() {
        const existingHeader = document.querySelector('.gi-mobile-search-header');
        if (existingHeader) {
            console.log('[Grant Insight] Mobile search header already exists');
            return;
        }
        
        const header = document.createElement('div');
        header.className = 'gi-mobile-search-header';
        header.innerHTML = `
            <div class="gi-mobile-search-inner">
                <div class="gi-search-box">
                    <input type="search" 
                           id="gi-mobile-search-input" 
                           class="gi-search-input"
                           placeholder="助成金を検索..."
                           aria-label="助成金を検索">
                    <button class="gi-search-btn" aria-label="検索">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.insertBefore(header, document.body.firstChild);
        
        // 新しく作成した検索入力を要素キャッシュに追加
        const newSearchInput = header.querySelector('#gi-mobile-search-input');
        if (newSearchInput) {
            this.elements.searchInputs = [newSearchInput];
            console.log('[Grant Insight] ✅ Mobile search header created');
            
            // 検索機能を再セットアップ
            this.setupSearch();
        }
    },

    /**
     * 検索実行
     */
    performSearch(query) {
        console.log('[Grant Insight] Performing search:', query);
        
        this.ajax('gi_search_grants', { query })
            .then(response => {
                if (response.success) {
                    this.updateSearchResults(response.data);
                    this.showToast(`${response.data.total || 0}件の助成金が見つかりました`, 'success');
                } else {
                    this.showToast(response.data || '検索中にエラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('[Grant Insight] Search error:', error);
                this.showToast('検索中にエラーが発生しました', 'error');
            });
    },

    /**
     * 検索候補表示
     */
    showSearchSuggestions(query) {
        this.ajax('gi_get_search_suggestions', { query })
            .then(response => {
                if (response.success) {
                    this.renderSearchSuggestions(response.data);
                }
            })
            .catch(error => {
                this.debug('Search suggestions error:', error);
            });
    },

    /**
     * 検索候補のレンダリング
     */
    renderSearchSuggestions(suggestions) {
        if (!suggestions || !suggestions.length) {
            this.hideSearchSuggestions();
            return;
        }

        let container = this.elements.searchSuggestions;
        if (!container) {
            container = document.createElement('div');
            container.className = 'gi-search-suggestions';
            this.elements.searchSuggestions = container;
            
            if (this.elements.searchContainer) {
                this.elements.searchContainer.appendChild(container);
            } else {
                const firstInput = this.elements.searchInputs[0];
                if (firstInput && firstInput.parentNode) {
                    firstInput.parentNode.appendChild(container);
                }
            }
        }

        container.innerHTML = suggestions.map((item, index) => `
            <div class="gi-suggestion-item" 
                 data-value="${this.escapeHtml(item.value)}"
                 data-index="${index}">
                <svg class="gi-suggestion-icon" width="16" height="16" viewBox="0 0 20 20" fill="none">
                    <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="gi-suggestion-text">${this.escapeHtml(item.label)}</span>
            </div>
        `).join('');

        container.style.display = 'block';
        container.classList.add('gi-suggestions-active');

        container.querySelectorAll('.gi-suggestion-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const value = e.currentTarget.dataset.value;
                this.executeSearch(value);
                this.hideSearchSuggestions();
            });
        });
    },

    /**
     * 検索候補のキーボードナビゲーション
     */
    handleSuggestionNavigation(e) {
        const container = this.elements.searchSuggestions;
        if (!container || !container.classList.contains('gi-suggestions-active')) return;

        const items = container.querySelectorAll('.gi-suggestion-item');
        if (!items.length) return;

        const currentActive = container.querySelector('.gi-suggestion-active');
        let newIndex = 0;

        if (currentActive) {
            const currentIndex = parseInt(currentActive.dataset.index);
            if (e.key === 'ArrowDown') {
                newIndex = (currentIndex + 1) % items.length;
            } else if (e.key === 'ArrowUp') {
                newIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
            }
            currentActive.classList.remove('gi-suggestion-active');
        }

        e.preventDefault();
        items[newIndex].classList.add('gi-suggestion-active');
    },

    /**
     * 検索実行
     */
    executeSearch(query) {
        console.log('[Grant Insight] Executing search:', query);
        
        const input = this.elements.searchInputs[0];
        if (input) {
            input.value = query;
        }
        
        const currentPath = window.location.pathname;
        if (currentPath === '/' || currentPath.includes('grants')) {
            this.performSearch(query);
        } else {
            window.location.href = `/grants/?search=${encodeURIComponent(query)}`;
        }
        
        this.hideSearchSuggestions();
    },

    /**
     * 検索候補を隠す
     */
    hideSearchSuggestions() {
        const container = this.elements.searchSuggestions;
        if (container) {
            container.classList.remove('gi-suggestions-active');
            setTimeout(() => {
                container.style.display = 'none';
            }, 150);
        }
    },

    /**
     * フィルター機能
     */
    setupFilters() {
        console.log('[Grant Insight] Setting up filters...');
        
        this.elements.filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                this.toggleFilter(button);
            });
        });

        if (this.elements.filterTrigger) {
            this.elements.filterTrigger.addEventListener('click', () => {
                this.showFilterBottomSheet();
            });
        }

        document.addEventListener('click', (e) => {
            if (e.target.matches('.execute-comparison, .gi-btn-filter-apply')) {
                e.preventDefault();
                this.handleFilterApply(e.target);
            }

            if (e.target.matches('.clear-comparison, .gi-btn-filter-clear')) {
                e.preventDefault();
                this.clearFilters();
            }

            if (e.target.matches('.gi-filter-sheet-close')) {
                this.hideFilterBottomSheet();
            }
        });
        
        console.log('[Grant Insight] ✅ Filters setup complete');
    },

    toggleFilter(button) {
        const filterType = button.dataset.filter || button.dataset.type;
        const filterValue = button.dataset.value;
        
        if (!filterType || !filterValue) return;

        button.classList.toggle('active');
        button.classList.toggle('selected');
        
        const filterKey = `${filterType}-${filterValue}`;
        
        if (button.classList.contains('active')) {
            this.state.activeFilters.set(filterKey, {
                type: filterType,
                value: filterValue,
                label: button.textContent.trim()
            });
        } else {
            this.state.activeFilters.delete(filterKey);
        }

        this.applyFilters();
    },

    applyFilters() {
        const filters = this.buildFilterObject();
        
        this.ajax('gi_filter_grants', { filters })
            .then(response => {
                if (response.success) {
                    this.updateSearchResults(response.data);
                    const count = response.data.total || response.data.count || 0;
                    this.showToast(`${count}件の助成金が見つかりました`, 'success');
                    this.updateURL(filters);
                } else {
                    this.showToast(response.data || 'フィルター処理中にエラーが発生しました', 'error');
                }
            })
            .catch(error => {
                console.error('[Grant Insight] Filter error:', error);
                this.showToast('フィルター処理中にエラーが発生しました', 'error');
            });

        this.hideFilterBottomSheet();
    },

    buildFilterObject() {
        const filters = {};
        
        this.state.activeFilters.forEach(filter => {
            if (!filters[filter.type]) {
                filters[filter.type] = [];
            }
            filters[filter.type].push(filter.value);
        });

        return filters;
    },

    updateURL(filters) {
        const params = new URLSearchParams();
        
        Object.keys(filters).forEach(type => {
            if (filters[type].length > 0) {
                params.set(type, filters[type].join(','));
            }
        });
        
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
    },

    clearFilters() {
        this.state.activeFilters.clear();
        
        document.querySelectorAll('.gi-filter-chip.active, .filter-button.active, .filter-chip.selected').forEach(button => {
            button.classList.remove('active', 'selected');
        });

        this.applyFilters();
    },

    handleFilterApply(target) {
        if (target.classList.contains('execute-comparison')) {
            this.executeComparison();
        } else {
            this.applyFilters();
        }
    },

    /**
     * 比較機能
     */
    setupComparison() {
        console.log('[Grant Insight] Setting up comparison...');
        
        document.addEventListener('change', (e) => {
            if (e.target.matches('.grant-compare-checkbox')) {
                const grantId = e.target.dataset.grantId;
                const grantTitle = e.target.dataset.grantTitle || e.target.closest('.grant-card')?.querySelector('.card-title, .grant-card-title')?.textContent?.trim();
                
                if (e.target.checked) {
                    this.addComparisonItem(grantId, grantTitle);
                } else {
                    this.removeComparisonItem(grantId);
                }
            }
        });

        this.loadComparisonFromStorage();
        console.log('[Grant Insight] ✅ Comparison setup complete');
    },

    addComparisonItem(id, title) {
        if (this.state.comparisonItems.length >= this.config.maxComparisonItems) {
            this.showToast(`比較は最大${this.config.maxComparisonItems}件までです`, 'warning');
            
            const checkbox = document.querySelector(`[data-grant-id="${id}"]`);
            if (checkbox) checkbox.checked = false;
            return false;
        }
        
        if (this.state.comparisonItems.find(item => item.id === id)) {
            return false;
        }
        
        this.state.comparisonItems.push({ id, title: title || `助成金 ID: ${id}` });
        this.updateComparisonWidget();
        this.saveComparisonToStorage();
        this.showToast('比較リストに追加しました', 'success');
        
        return true;
    },

    removeComparisonItem(id) {
        this.state.comparisonItems = this.state.comparisonItems.filter(item => item.id !== id);
        this.updateComparisonWidget();
        this.saveComparisonToStorage();
        
        const checkbox = document.querySelector(`[data-grant-id="${id}"]`);
        if (checkbox) checkbox.checked = false;
    },

    updateComparisonWidget() {
        if (this.state.comparisonItems.length === 0) {
            this.hideComparisonWidget();
            return;
        }
        
        this.elements.body.classList.add('has-comparison-bar');
        
        let container = this.elements.comparisonBar;
        if (!container) {
            container = document.createElement('div');
            container.className = 'gi-comparison-bar';
            this.elements.comparisonBar = container;
            this.elements.body.appendChild(container);
        }

        container.innerHTML = `
            <div class="gi-comparison-bar-inner">
                <div class="gi-comparison-items">
                    ${this.state.comparisonItems.map(item => `
                        <div class="gi-comparison-item" data-id="${item.id}">
                            <span class="gi-item-title">${this.escapeHtml(item.title)}</span>
                            <button class="gi-remove-item" data-id="${item.id}" aria-label="削除">×</button>
                        </div>
                    `).join('')}
                </div>
                <div class="gi-comparison-actions">
                    <button class="execute-comparison gi-btn gi-btn-primary">
                        比較する (${this.state.comparisonItems.length}件)
                    </button>
                    <button class="clear-comparison gi-btn gi-btn-secondary">クリア</button>
                </div>
            </div>
        `;
        
        container.classList.add('gi-comparison-active');

        container.querySelectorAll('.gi-remove-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                this.removeComparisonItem(id);
            });
        });
    },

    hideComparisonWidget() {
        if (this.elements.comparisonBar) {
            this.elements.comparisonBar.classList.remove('gi-comparison-active');
            this.elements.body.classList.remove('has-comparison-bar');
        }
    },

    executeComparison() {
        if (this.state.comparisonItems.length < 2) {
            this.showToast('比較するには2件以上選択してください', 'warning');
            return;
        }
        
        const ids = this.state.comparisonItems.map(item => item.id).join(',');
        window.location.href = `/compare?grants=${ids}`;
    },

    saveComparisonToStorage() {
        try {
            localStorage.setItem('grant_comparison', JSON.stringify(this.state.comparisonItems));
        } catch (e) {
            this.debug('Failed to save comparison data:', e);
        }
    },

    loadComparisonFromStorage() {
        try {
            const saved = localStorage.getItem('grant_comparison');
            if (saved) {
                this.state.comparisonItems = JSON.parse(saved);
                this.updateComparisonWidget();
                
                this.state.comparisonItems.forEach(item => {
                    const checkbox = document.querySelector(`[data-grant-id="${item.id}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
        } catch (e) {
            this.debug('Failed to load comparison data:', e);
        }
    },

    /**
     * モバイル最適化機能
     */
    setupMobile() {
        console.log('[Grant Insight] Setting up mobile features...');
        
        this.setupMobileHeader();
        this.setupTouchOptimizations();
        this.setupCardInteractions();
        this.setupMobileMenu();
        
        console.log('[Grant Insight] ✅ Mobile setup complete');
    },

    /**
     * モバイルヘッダーのセットアップ（修正版）
     */
    setupMobileHeader() {
        const isMobile = window.innerWidth <= 768;
        
        if (!isMobile) {
            console.log('[Grant Insight] Not mobile, skipping mobile header');
            return;
        }
        
        // 既存のモバイル検索ヘッダーがあればスキップ
        if (document.querySelector('.gi-mobile-search-header')) {
            console.log('[Grant Insight] Mobile search header already exists');
            return;
        }
        
        // 検索入力が存在しない場合のみモバイルヘッダーを作成
        if (this.elements.searchInputs.length === 0) {
            console.log('[Grant Insight] Creating mobile header for search...');
            this.createMobileSearchHeader();
        }
    },

    setupMobileMenu() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.mobile-menu-toggle, .gi-menu-toggle')) {
                this.elements.body.classList.toggle('gi-mobile-menu-open');
                e.target.classList.toggle('gi-menu-active');
            }

            if (!e.target.closest('.gi-mobile-menu, .mobile-menu, .mobile-menu-toggle, .gi-menu-toggle')) {
                this.elements.body.classList.remove('gi-mobile-menu-open');
                document.querySelectorAll('.mobile-menu-toggle, .gi-menu-toggle').forEach(toggle => {
                    toggle.classList.remove('gi-menu-active');
                });
            }
        });
    },

    setupTouchOptimizations() {
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        
        if (isTouchDevice) {
            this.elements.body.classList.add('gi-touch-device');
            this.setupTouchFeedback();
            this.setupPullToRefresh();
        }
    },

    setupTouchFeedback() {
        const touchElements = document.querySelectorAll('button, .btn, .gi-filter-chip, .category-card, .grant-card');
        
        touchElements.forEach(element => {
            element.addEventListener('touchstart', () => {
                element.classList.add('gi-touch-active');
            });

            element.addEventListener('touchend', () => {
                setTimeout(() => {
                    element.classList.remove('gi-touch-active');
                }, 150);
            });
        });
    },

    setupCardInteractions() {
        setTimeout(() => {
            console.log('[Grant Insight] Setting up card interactions...');
            
            document.addEventListener('click', (e) => {
                const aiButton = e.target.closest('.grant-ai-trigger-portal');
                if (aiButton) {
                    console.log('[Grant Insight] AI button clicked');
                    return;
                }

                const card = e.target.closest('.gi-grant-card-enhanced, .grant-card, .category-card, .grant-card-list-portal');
                if (!card) return;

                const clickedInteractive = e.target.closest('button, a, input, .gi-bookmark-btn');
                if (!clickedInteractive) {
                    const detailLink = card.querySelector('a.btn-primary[href]');
                    if (detailLink && detailLink.href) {
                        window.location.href = detailLink.href;
                    }
                }
            }, false);
        }, 100);
    },

    setupPullToRefresh() {
        let startY = 0;
        let currentY = 0;
        let isRefreshing = false;

        document.addEventListener('touchstart', (e) => {
            if (document.querySelector('.portal-ai-modal.active, .gi-modal-active')) {
                return;
            }
            
            if (window.scrollY === 0 && !isRefreshing) {
                startY = e.touches[0].clientY;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (document.querySelector('.portal-ai-modal.active, .gi-modal-active')) {
                return;
            }
            
            if (window.scrollY === 0 && startY > 0) {
                currentY = e.touches[0].clientY;
                const pullDistance = currentY - startY;
                
                if (pullDistance > 100 && !isRefreshing) {
                    this.showPullToRefreshIndicator();
                }
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            if (document.querySelector('.portal-ai-modal.active, .gi-modal-active')) {
                startY = 0;
                currentY = 0;
                return;
            }
            
            if (currentY - startY > 100 && !isRefreshing) {
                this.triggerRefresh();
            }
            startY = 0;
            currentY = 0;
        });
    },

    triggerRefresh() {
        this.showToast('更新中...', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    },

    showPullToRefreshIndicator() {
        this.debug('Pull to refresh triggered');
    },

    /**
     * アニメーション・スクロール効果
     */
    setupAnimations() {
        console.log('[Grant Insight] Setting up animations...');
        
        this.setupScrollAnimations();
        this.setupSmoothScroll();
        this.setupBackToTop();
        
        console.log('[Grant Insight] ✅ Animations setup complete');
    },

    setupScrollAnimations() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('gi-animated', 'gi-fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            const animateElements = document.querySelectorAll('.category-card, .grant-card, .prefecture-item');
            animateElements.forEach(el => {
                el.classList.add('gi-animate-on-scroll');
                observer.observe(el);
            });
        }
    },

    setupSmoothScroll() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="#"]');
            if (!link) return;

            const targetId = link.getAttribute('href');
            const target = document.querySelector(targetId);
            
            if (target) {
                e.preventDefault();
                const headerOffset = this.state.headerHeight || 80;
                const targetPosition = target.offsetTop - headerOffset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    },

    setupBackToTop() {
        let backToTopButton = document.querySelector('.gi-back-to-top, .back-to-top');
        
        if (!backToTopButton) {
            backToTopButton = document.createElement('button');
            backToTopButton.className = 'gi-back-to-top';
            backToTopButton.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 19V5M5 12L12 5L19 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            backToTopButton.setAttribute('aria-label', 'ページトップへ戻る');
            document.body.appendChild(backToTopButton);
        }
        
        const scrollHandler = this.throttle(() => {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('gi-back-to-top-visible');
            } else {
                backToTopButton.classList.remove('gi-back-to-top-visible');
            }
        }, 100);
        
        window.addEventListener('scroll', scrollHandler, { passive: true });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    },

    /**
     * フォーム拡張
     */
    setupForms() {
        console.log('[Grant Insight] Setting up forms...');
        
        this.setupFormValidation();
        this.setupFormEnhancements();
        
        console.log('[Grant Insight] ✅ Forms setup complete');
    },

    setupFormValidation() {
        document.addEventListener('submit', (e) => {
            const form = e.target.closest('form');
            if (!form || form.classList.contains('gi-no-validation')) return;

            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('gi-field-error');
                    
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('gi-field-error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                this.showToast('必須項目を入力してください', 'error');
                
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
            }
        });

        document.addEventListener('input', (e) => {
            if (e.target.matches('input, textarea, select')) {
                e.target.classList.remove('gi-field-error');
            }
        });
    },

    setupFormEnhancements() {
        this.setupAutoSave();
        this.setupFileInputs();
    },

    setupAutoSave() {
        const autoSaveFields = document.querySelectorAll('[data-autosave]');
        
        autoSaveFields.forEach(field => {
            const saveKey = field.dataset.autosave;
            
            const savedValue = localStorage.getItem(`gi_autosave_${saveKey}`);
            if (savedValue && !field.value) {
                field.value = savedValue;
            }
            
            const saveHandler = this.debounce(() => {
                try {
                    localStorage.setItem(`gi_autosave_${saveKey}`, field.value);
                    this.debug(`Auto-saved: ${saveKey}`);
                } catch (e) {
                    this.debug('Auto-save error:', e);
                }
            }, 1000);
            
            field.addEventListener('input', saveHandler);
        });
    },

    setupFileInputs() {
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', (e) => {
                const files = e.target.files;
                if (files.length > 0) {
                    const fileNames = Array.from(files).map(file => file.name).join(', ');
                    this.showToast(`選択されたファイル: ${fileNames}`, 'info');
                }
            });
        });
    },

    /**
     * AIボタンイベントリスナー
     */
    setupAIButtonListeners() {
        if (window._aiButtonListenersSetup) {
            this.debug('AI button listeners already setup, skipping');
            return;
        }
        window._aiButtonListenersSetup = true;
        
        console.log('[Grant Insight] Setting up AI button listeners...');
        
        document.addEventListener('click', (e) => {
            const portalAIButton = e.target.closest('.grant-ai-trigger-portal');
            if (portalAIButton) {
                e.preventDefault();
                e.stopPropagation();
                
                const postId = portalAIButton.dataset.postId || portalAIButton.dataset.grantId;
                const grantTitle = portalAIButton.dataset.grantTitle;
                const grantPermalink = portalAIButton.dataset.grantPermalink;
                
                console.log('[Grant Insight] Portal AI button clicked:', { postId, grantTitle, grantPermalink });
                
                if (postId && grantTitle && grantPermalink && typeof window.showPortalAIModal === 'function') {
                    window.showPortalAIModal(postId, grantTitle, grantPermalink);
                } else {
                    console.error('[Grant Insight] Portal AI modal function not found or missing data');
                }
                return;
            }
            
            const compactAIButton = e.target.closest('.grant-btn-compact--ai');
            if (compactAIButton) {
                e.preventDefault();
                e.stopPropagation();
                
                const postId = compactAIButton.dataset.postId;
                const grantTitle = compactAIButton.dataset.grantTitle;
                
                console.log('[Grant Insight] Compact AI button clicked:', { postId, grantTitle });
                
                if (postId && grantTitle && typeof window.showAIChatModal === 'function') {
                    window.showAIChatModal(postId, grantTitle);
                } else {
                    console.error('[Grant Insight] AI modal function not found or missing data');
                }
                return;
            }
        }, true);
        
        console.log('[Grant Insight] ✅ AI button listeners setup complete');
    },

    /**
     * アクセシビリティ
     */
    setupAccessibility() {
        console.log('[Grant Insight] Setting up accessibility...');
        
        this.setupKeyboardNavigation();
        this.setupFocusManagement();
        this.setupARIALabels();
        
        console.log('[Grant Insight] ✅ Accessibility setup complete');
    },

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideSearchSuggestions();
                this.hideFilterBottomSheet();
                this.closeModals();
            }
            
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                const searchInput = this.elements.searchInputs[0];
                if (searchInput) {
                    searchInput.focus();
                }
            }
        });
    },

    setupFocusManagement() {
        this.setupTabTrap();
        this.setupFocusVisibility();
    },

    setupTabTrap() {
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab') return;

            const modal = document.querySelector('.gi-modal-active, .gi-filter-bottom-sheet.active');
            if (!modal) return;

            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            
            if (focusableElements.length === 0) return;

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
    },

    setupFocusVisibility() {
        document.addEventListener('mousedown', () => {
            this.elements.body.classList.add('gi-using-mouse');
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                this.elements.body.classList.remove('gi-using-mouse');
            }
        });
    },

    setupARIALabels() {
        const updateARIALabels = () => {
            const resultsContainer = this.elements.grantsGrid;
            if (resultsContainer) {
                const count = resultsContainer.querySelectorAll('.grant-card').length;
                resultsContainer.setAttribute('aria-label', `${count}件の助成金が表示されています`);
            }
            
            if (this.elements.comparisonBar) {
                const count = this.state.comparisonItems.length;
                this.elements.comparisonBar.setAttribute('aria-label', `${count}件の助成金が比較リストに追加されています`);
            }
        };

        updateARIALabels();

        const observer = new MutationObserver(updateARIALabels);
        if (this.elements.grantsGrid) {
            observer.observe(this.elements.grantsGrid, { childList: true });
        }
    },

    /**
     * パフォーマンス最適化
     */
    setupPerformance() {
        console.log('[Grant Insight] Setting up performance optimizations...');
        
        this.setupLazyLoading();
        this.setupInfiniteScroll();
        this.setupImageOptimization();
        
        console.log('[Grant Insight] ✅ Performance setup complete');
    },

    setupLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        if (images.length === 0 || !('IntersectionObserver' in window)) return;

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('gi-image-loaded');
                    img.classList.remove('gi-image-loading');
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px'
        });

        images.forEach(img => {
            img.classList.add('gi-image-loading');
            imageObserver.observe(img);
        });
    },

    setupInfiniteScroll() {
        let page = 2;
        let isLoading = false;
        let hasMore = true;

        const loadMoreHandler = this.throttle(() => {
            if (isLoading || !hasMore) return;

            const scrollTop = window.pageYOffset;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;

            if (scrollTop + windowHeight >= documentHeight - 1000) {
                isLoading = true;
                
                this.ajax('gi_load_more_grants', { page })
                    .then(response => {
                        if (response.success && response.data.grants && response.data.grants.length > 0) {
                            const container = this.elements.grantsGrid;
                            if (container) {
                                const newCards = response.data.grants.map(grant => 
                                    this.renderGrantCard(grant)
                                ).join('');
                                container.insertAdjacentHTML('beforeend', newCards);
                                
                                this.setupNewCardEvents(container);
                            }
                            page++;
                        } else {
                            hasMore = false;
                        }
                    })
                    .catch(error => {
                        console.error('[Grant Insight] Load more error:', error);
                        hasMore = false;
                    })
                    .finally(() => {
                        isLoading = false;
                    });
            }
        }, 200);

        window.addEventListener('scroll', loadMoreHandler, { passive: true });
    },

    setupNewCardEvents(container) {
        const newImages = container.querySelectorAll('img[data-src]:not(.gi-image-loading)');
        newImages.forEach(img => {
            img.classList.add('gi-image-loading');
        });

        this.state.comparisonItems.forEach(item => {
            const checkbox = container.querySelector(`[data-grant-id="${item.id}"]:not([data-restored])`);
            if (checkbox) {
                checkbox.checked = true;
                checkbox.dataset.restored = 'true';
            }
        });
    },

    setupImageOptimization() {
        const supportsWebP = this.checkWebPSupport();
        
        if (supportsWebP) {
            this.elements.body.classList.add('gi-supports-webp');
        }
    },

    checkWebPSupport() {
        try {
            return document.createElement('canvas').toDataURL('image/webp').indexOf('data:image/webp') === 0;
        } catch (e) {
            return false;
        }
    },

    /**
     * UI更新・レンダリング
     */
    updateSearchResults(data) {
        const container = this.elements.grantsGrid;
        if (!container) return;

        if (data.grants && data.grants.length > 0) {
            container.innerHTML = data.grants.map(grant => this.renderGrantCard(grant)).join('');
            this.setupNewCardEvents(container);
        } else {
            container.innerHTML = `
                <div class="gi-no-results">
                    <div class="gi-no-results-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3>該当する助成金が見つかりませんでした</h3>
                    <p>検索条件を変更して再度お試しください。</p>
                </div>
            `;
        }

        const countElement = document.querySelector('.gi-results-count, .results-count');
        if (countElement && data.total !== undefined) {
            countElement.textContent = `${data.total}件の助成金`;
        }
    },

    renderGrantCard(grant) {
        return `
            <div class="gi-grant-card-enhanced grant-card" data-grant-id="${grant.id}">
                <div class="gi-card-image-container">
                    <img src="${grant.image || '/assets/images/default-grant.jpg'}" 
                         alt="${this.escapeHtml(grant.title)}" 
                         class="gi-card-image"
                         loading="lazy">
                    <div class="gi-card-badges">
                        ${grant.is_new ? '<span class="gi-card-badge gi-badge-new">新着</span>' : ''}
                        ${grant.is_featured ? '<span class="gi-card-badge gi-badge-featured">注目</span>' : ''}
                    </div>
                    <div class="gi-card-compare">
                        <label class="gi-compare-checkbox-container">
                            <input type="checkbox" 
                                   class="grant-compare-checkbox"
                                   data-grant-id="${grant.id}"
                                   data-grant-title="${this.escapeHtml(grant.title)}">
                            <span class="gi-compare-checkbox-label">比較</span>
                        </label>
                    </div>
                </div>
                <div class="gi-card-content">
                    <h3 class="gi-card-title">${this.escapeHtml(grant.title)}</h3>
                    <div class="gi-card-meta">
                        <div class="gi-card-amount">${grant.amount ? `${grant.amount}円` : '金額未定'}</div>
                        <div class="gi-card-organization">${this.escapeHtml(grant.organization || '')}</div>
                        <div class="gi-card-deadline">${grant.deadline ? `締切: ${grant.deadline}` : ''}</div>
                    </div>
                    ${grant.excerpt ? `<p class="gi-card-excerpt">${this.escapeHtml(grant.excerpt)}</p>` : ''}
                    <div class="gi-card-actions">
                        <a href="${grant.url || '#'}" class="gi-btn gi-btn-primary gi-card-cta">詳細を見る</a>
                        <button class="gi-btn gi-btn-secondary gi-bookmark-btn" 
                                data-grant-id="${grant.id}"
                                aria-label="ブックマーク">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * フィルターUI（ボトムシート）
     */
    showFilterBottomSheet() {
        let sheet = document.querySelector('.gi-filter-bottom-sheet');
        
        if (!sheet) {
            sheet = this.createFilterBottomSheet();
            document.body.appendChild(sheet);
        }
        
        const overlay = document.createElement('div');
        overlay.className = 'gi-filter-overlay';
        overlay.addEventListener('click', () => this.hideFilterBottomSheet());
        document.body.appendChild(overlay);
        
        requestAnimationFrame(() => {
            sheet.classList.add('gi-filter-sheet-active');
            overlay.classList.add('gi-overlay-active');
            this.elements.body.classList.add('gi-filter-sheet-open');
        });
    },

    hideFilterBottomSheet() {
        const sheet = document.querySelector('.gi-filter-bottom-sheet');
        const overlay = document.querySelector('.gi-filter-overlay');
        
        if (sheet) {
            sheet.classList.remove('gi-filter-sheet-active');
        }
        
        if (overlay) {
            overlay.classList.remove('gi-overlay-active');
        }
        
        this.elements.body.classList.remove('gi-filter-sheet-open');
        
        setTimeout(() => {
            if (sheet && sheet.parentNode) {
                sheet.parentNode.removeChild(sheet);
            }
            if (overlay && overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 300);
    },

    createFilterBottomSheet() {
        const sheet = document.createElement('div');
        sheet.className = 'gi-filter-bottom-sheet';
        sheet.innerHTML = `
            <div class="gi-filter-sheet-header">
                <h3 class="gi-filter-sheet-title">フィルター</h3>
                <button class="gi-filter-sheet-close gi-btn-icon" aria-label="閉じる">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
            <div class="gi-filter-sheet-content">
                <div class="gi-filter-group">
                    <div class="gi-filter-group-title">カテゴリー</div>
                    <div class="gi-filter-options">
                        <button class="gi-filter-option" data-filter="category" data-value="business">
                            <span>事業助成</span>
                        </button>
                        <button class="gi-filter-option" data-filter="category" data-value="research">
                            <span>研究助成</span>
                        </button>
                        <button class="gi-filter-option" data-filter="category" data-value="education">
                            <span>教育助成</span>
                        </button>
                    </div>
                </div>
                <div class="gi-filter-group">
                    <div class="gi-filter-group-title">都道府県</div>
                    <div class="gi-filter-options">
                        <button class="gi-filter-option" data-filter="prefecture" data-value="tokyo">
                            <span>東京都</span>
                        </button>
                        <button class="gi-filter-option" data-filter="prefecture" data-value="osaka">
                            <span>大阪府</span>
                        </button>
                        <button class="gi-filter-option" data-filter="prefecture" data-value="kanagawa">
                            <span>神奈川県</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="gi-filter-sheet-footer">
                <button class="gi-btn gi-btn-secondary gi-btn-filter-clear">クリア</button>
                <button class="gi-btn gi-btn-primary gi-btn-filter-apply">適用</button>
            </div>
        `;

        sheet.querySelectorAll('.gi-filter-option').forEach(option => {
            option.addEventListener('click', () => {
                option.classList.toggle('gi-filter-option-selected');
            });
        });

        return sheet;
    },

    /**
     * ユーティリティ・ヘルパー
     */
    closeModals() {
        this.hideSearchSuggestions();
        this.hideFilterBottomSheet();
        
        document.querySelectorAll('.gi-modal-active, .gi-popup-active').forEach(modal => {
            modal.classList.remove('gi-modal-active', 'gi-popup-active');
        });
    }
};

/**
 * 自動初期化
 */
GrantInsight.init();
window.GrantInsight = GrantInsight;

/**
 * CSS-in-JS スタイル（完全版）
 */
document.addEventListener('DOMContentLoaded', () => {
    const styleSheet = document.createElement('style');
    styleSheet.textContent = `
        /* モバイル検索ヘッダー */
        .gi-mobile-search-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 12px 16px;
        }
        
        .gi-mobile-search-inner {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .gi-search-box {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .gi-search-input {
            flex: 1;
            padding: 10px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        
        .gi-search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .gi-search-btn {
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .gi-search-btn:hover {
            background: #2563eb;
        }
        
        .gi-search-btn:active {
            transform: scale(0.98);
        }
        
        /* 検索候補 */
        .gi-search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
        }
        
        .gi-search-suggestions.gi-suggestions-active {
            display: block;
        }
        
        .gi-suggestion-item {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        
        .gi-suggestion-item:hover,
        .gi-suggestion-item.gi-suggestion-active {
            background: #f3f4f6;
        }
        
        .gi-suggestion-icon {
            color: #9ca3af;
            flex-shrink: 0;
        }
        
        .gi-suggestion-text {
            flex: 1;
            color: #1f2937;
        }
        
        /* Toast通知 */
        .gi-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }
        
        .gi-toast-show {
            transform: translateX(0);
        }
        
        .gi-toast-content {
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        
        .gi-toast-success {
            border-left: 4px solid #10b981;
        }
        
        .gi-toast-error {
            border-left: 4px solid #ef4444;
        }
        
        .gi-toast-warning {
            border-left: 4px solid #f59e0b;
        }
        
        .gi-toast-info {
            border-left: 4px solid #3b82f6;
        }
        
        .gi-toast-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #9ca3af;
            padding: 0;
            line-height: 1;
        }
        
        .gi-toast-close:hover {
            color: #6b7280;
        }
        
        /* 比較バー */
        .gi-comparison-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.1);
            padding: 16px;
            z-index: 999;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .gi-comparison-bar.gi-comparison-active {
            transform: translateY(0);
        }
        
        .gi-comparison-bar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        
        .gi-comparison-items {
            display: flex;
            gap: 8px;
            flex: 1;
            overflow-x: auto;
        }
        
        .gi-comparison-item {
            background: #f3f4f6;
            padding: 8px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        
        .gi-item-title {
            font-size: 14px;
            color: #1f2937;
        }
        
        .gi-remove-item {
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            font-size: 18px;
            line-height: 1;
            padding: 0;
        }
        
        .gi-remove-item:hover {
            color: #ef4444;
        }
        
        .gi-comparison-actions {
            display: flex;
            gap: 8px;
        }
        
        .gi-btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        
        .gi-btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .gi-btn-primary:hover {
            background: #2563eb;
        }
        
        .gi-btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
        }
        
        .gi-btn-secondary:hover {
            background: #e5e7eb;
        }
        
        /* 結果なし */
        .gi-no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .gi-no-results-icon {
            margin-bottom: 20px;
            color: #d1d5db;
        }
        
        .gi-no-results h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        /* トップに戻るボタン */
        .gi-back-to-top {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 48px;
            height: 48px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 998;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .gi-back-to-top.gi-back-to-top-visible {
            opacity: 1;
            visibility: visible;
        }
        
        .gi-back-to-top:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }
        
        /* アニメーション */
        .gi-animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .gi-animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* タッチフィードバック */
        .gi-touch-active {
            transform: scale(0.98);
            opacity: 0.8;
        }
        
        /* フォーカス管理 */
        .gi-using-mouse *:focus {
            outline: none;
        }
        
        /* エラー状態 */
        .gi-field-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        /* レスポンシブ */
        @media (max-width: 768px) {
            .gi-mobile-search-header {
                padding: 8px 12px;
            }
            
            .gi-search-input {
                font-size: 14px;
                padding: 8px 12px;
            }
            
            .gi-search-btn {
                padding: 8px 16px;
            }
            
            .gi-comparison-bar-inner {
                flex-direction: column;
                align-items: stretch;
            }
            
            .gi-comparison-actions {
                width: 100%;
            }
            
            .gi-btn {
                flex: 1;
            }
            
            .gi-toast {
                left: 12px;
                right: 12px;
                max-width: none;
            }
        }
    `;
    document.head.appendChild(styleSheet);
    
    console.log('[Grant Insight] ✅ Styles injected');
});

/**
 * エクスポート
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GrantInsight;
}

if (typeof define === 'function' && define.amd) {
    define(() => GrantInsight);
}

console.log('[Grant Insight] 🚀 Script loaded successfully (v' + GrantInsight.version + ')');