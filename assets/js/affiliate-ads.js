/*!
 * Grant Insight Perfect - Affiliate Ad Manager JavaScript
 * アフィリエイト広告管理専用スクリプト
 * 
 * @version 1.0.0
 * @date 2025-11-13
 * @description CRUD operations for affiliate ads management
 */

(function($) {
    'use strict';

    /**
     * Affiliate Ad Manager Class
     */
    const AffiliateAdManager = {
        /**
         * Initialize the manager
         */
        init() {
            if (!window.jiAdminAds) {
                console.error('[Affiliate Ads] jiAdminAds configuration not found');
                return;
            }

            this.setupEventListeners();
            console.log('[Affiliate Ads] Initialized successfully');
        },

        /**
         * Setup all event listeners
         */
        setupEventListeners() {
            // Add new ad button
            $('.ji-add-new-ad').on('click', (e) => this.handleAddNew(e));
            
            // Edit ad buttons
            $('.ji-edit-ad').on('click', (e) => this.handleEdit(e));
            
            // Delete ad buttons
            $('.ji-delete-ad').on('click', (e) => this.handleDelete(e));
            
            // Modal close buttons
            $('.ji-modal-close').on('click', () => this.closeModal());
            
            // Close modal on outside click
            $(window).on('click', (e) => {
                if ($(e.target).is('#ji-ad-modal')) {
                    this.closeModal();
                }
            });
            
            // Form submission
            $('#ji-ad-form').on('submit', (e) => this.handleFormSubmit(e));
        },

        /**
         * Handle add new ad button click
         */
        handleAddNew(e) {
            e.preventDefault();
            
            $('#ji-modal-title').text('広告を追加');
            $('#ji-ad-form')[0].reset();
            $('#ad_id').val('');
            this.openModal();
        },

        /**
         * Handle edit ad button click
         */
        handleEdit(e) {
            e.preventDefault();
            
            const adId = $(e.currentTarget).data('ad-id');
            
            if (!adId) {
                this.showError('広告IDが見つかりません。');
                return;
            }
            
            $('#ji-modal-title').text('広告を編集');
            this.loadAdData(adId);
        },

        /**
         * Load ad data via AJAX
         */
        loadAdData(adId) {
            $.ajax({
                url: window.jiAdminAds.ajax_url,
                type: 'POST',
                data: {
                    action: 'ji_get_ad',
                    nonce: window.jiAdminAds.nonce,
                    ad_id: adId
                },
                beforeSend: () => {
                    this.showLoading('広告データを読み込んでいます...');
                },
                success: (response) => {
                    this.hideLoading();
                    
                    if (response.success && response.data) {
                        this.populateForm(response.data);
                        this.openModal();
                    } else {
                        this.showError('エラー: ' + (response.data || '広告データの取得に失敗しました。'));
                    }
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    console.error('[Affiliate Ads] AJAX error:', error);
                    this.showError('通信エラーが発生しました: ' + error);
                }
            });
        },

        /**
         * Populate form with ad data
         */
        populateForm(ad) {
            $('#ad_id').val(ad.id || '');
            $('#title').val(ad.title || '');
            $('#ad_type').val(ad.ad_type || 'html');
            $('#content').val(ad.content || '');
            $('#link_url').val(ad.link_url || '');
            
            // Multiple select: positions
            if (ad.positions_array && ad.positions_array.length > 0) {
                $('#positions').val(ad.positions_array);
            } else {
                $('#positions').val([]);
            }
            
            // Multiple select: target pages
            if (ad.target_pages_array && ad.target_pages_array.length > 0) {
                $('#target_pages').val(ad.target_pages_array);
            } else {
                $('#target_pages').val(['']); // All pages
            }
            
            // Multiple select: target categories
            if (ad.target_categories_array && ad.target_categories_array.length > 0) {
                $('#target_categories').val(ad.target_categories_array);
            } else {
                $('#target_categories').val(['']); // All categories
            }
            
            $('#device_target').val(ad.device_target || 'all');
            $('#status').val(ad.status || 'active');
            $('#priority').val(ad.priority || 0);
            
            // Date-time fields (datetime-local format: YYYY-MM-DDTHH:MM)
            if (ad.start_date) {
                const startDate = new Date(ad.start_date);
                $('#start_date').val(this.formatDateTimeLocal(startDate));
            } else {
                $('#start_date').val('');
            }
            
            if (ad.end_date) {
                const endDate = new Date(ad.end_date);
                $('#end_date').val(this.formatDateTimeLocal(endDate));
            } else {
                $('#end_date').val('');
            }
        },

        /**
         * Handle delete ad button click
         */
        handleDelete(e) {
            e.preventDefault();
            
            if (!confirm('この広告を削除してもよろしいですか？統計データも削除されます。')) {
                return;
            }
            
            const adId = $(e.currentTarget).data('ad-id');
            
            if (!adId) {
                this.showError('広告IDが見つかりません。');
                return;
            }
            
            this.deleteAd(adId);
        },

        /**
         * Delete ad via AJAX
         */
        deleteAd(adId) {
            $.ajax({
                url: window.jiAdminAds.ajax_url,
                type: 'POST',
                data: {
                    action: 'ji_delete_ad',
                    nonce: window.jiAdminAds.nonce,
                    ad_id: adId
                },
                beforeSend: () => {
                    this.showLoading('広告を削除しています...');
                },
                success: (response) => {
                    this.hideLoading();
                    
                    if (response.success) {
                        this.showSuccess(response.data || '広告を削除しました。');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        this.showError('エラー: ' + (response.data || '広告の削除に失敗しました。'));
                    }
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    console.error('[Affiliate Ads] AJAX error:', error);
                    this.showError('通信エラーが発生しました: ' + error);
                }
            });
        },

        /**
         * Handle form submission
         */
        handleFormSubmit(e) {
            e.preventDefault();
            
            // Validate form
            if (!this.validateForm()) {
                return;
            }
            
            const formData = $('#ji-ad-form').serialize();
            const fullData = formData + '&action=ji_save_ad&nonce=' + window.jiAdminAds.nonce;
            
            this.saveAd(fullData);
        },

        /**
         * Validate form before submission
         */
        validateForm() {
            const title = $('#title').val().trim();
            const content = $('#content').val().trim();
            const positions = $('#positions').val();
            
            if (!title) {
                this.showError('タイトルを入力してください。');
                $('#title').focus();
                return false;
            }
            
            if (!content) {
                this.showError('広告コンテンツを入力してください。');
                $('#content').focus();
                return false;
            }
            
            if (!positions || positions.length === 0) {
                this.showError('配置位置を選択してください。');
                $('#positions').focus();
                return false;
            }
            
            return true;
        },

        /**
         * Save ad via AJAX
         */
        saveAd(formData) {
            $.ajax({
                url: window.jiAdminAds.ajax_url,
                type: 'POST',
                data: formData,
                beforeSend: () => {
                    this.showLoading('広告を保存しています...');
                },
                success: (response) => {
                    this.hideLoading();
                    
                    if (response.success) {
                        const message = response.data && response.data.message 
                            ? response.data.message 
                            : '広告を保存しました。';
                        this.showSuccess(message);
                        
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        this.showError('エラー: ' + (response.data || '広告の保存に失敗しました。'));
                    }
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    console.error('[Affiliate Ads] AJAX error:', error);
                    this.showError('通信エラーが発生しました: ' + error);
                }
            });
        },

        /**
         * Format date to datetime-local input format (YYYY-MM-DDTHH:MM)
         */
        formatDateTimeLocal(date) {
            if (!(date instanceof Date) || isNaN(date)) {
                return '';
            }
            
            const year = date.getFullYear();
            const month = ('0' + (date.getMonth() + 1)).slice(-2);
            const day = ('0' + date.getDate()).slice(-2);
            const hours = ('0' + date.getHours()).slice(-2);
            const minutes = ('0' + date.getMinutes()).slice(-2);
            
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        /**
         * Open modal
         */
        openModal() {
            $('#ji-ad-modal').fadeIn(300);
            $('body').addClass('modal-open');
        },

        /**
         * Close modal
         */
        closeModal() {
            $('#ji-ad-modal').fadeOut(300);
            $('body').removeClass('modal-open');
        },

        /**
         * Show loading indicator
         */
        showLoading(message = '処理中...') {
            // Remove existing notices
            $('.ji-notice').remove();
            
            // Create loading notice
            const $notice = $('<div class="ji-notice ji-notice-loading"></div>')
                .html(`<div class="ji-notice-content"><span class="spinner is-active"></span> ${message}</div>`);
            
            $('.wrap').prepend($notice);
        },

        /**
         * Hide loading indicator
         */
        hideLoading() {
            $('.ji-notice-loading').remove();
        },

        /**
         * Show error message
         */
        showError(message) {
            this.showNotice(message, 'error');
        },

        /**
         * Show success message
         */
        showSuccess(message) {
            this.showNotice(message, 'success');
        },

        /**
         * Show notice message
         */
        showNotice(message, type = 'info') {
            // Remove existing notices
            $('.ji-notice').remove();
            
            // Create notice
            const $notice = $('<div></div>')
                .addClass('ji-notice notice')
                .addClass(`notice-${type}`)
                .html(`<p>${message}</p>`);
            
            // Auto-dismiss after 5 seconds
            if (type === 'success' || type === 'info') {
                setTimeout(() => {
                    $notice.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
            
            $('.wrap').prepend($notice);
            
            // Scroll to top to show notice
            $('html, body').animate({ scrollTop: 0 }, 300);
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        AffiliateAdManager.init();
    });

})(jQuery);
