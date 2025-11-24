/*!
 * Grant Insight Perfect - 管理画面統合JavaScript
 * admin-consolidated.js + amount-fixer.js + sheets-admin.js
 * 
 * @version 2.0.0
 * @date 2025-11-12
 * @description 管理画面専用スクリプト（重複削除・最適化済み）
 */

// ============================================================================
// PART 1: Main Admin Application (GrantInsightAdmin Namespace)
// ============================================================================
/*!
 * Grant Insight Perfect - 統合管理画面JavaScript
 * 管理画面専用スクリプト（メタボックス + Google Sheets管理）
 * 
 * @version 1.0.0
 * @date 2025-10-05
 */

/**
 * =============================================================================
 * GRANT INSIGHT ADMIN - 管理画面名前空間
 * =============================================================================
 */
const GrantInsightAdmin = {
    // バージョン情報
    version: '1.0.0',
    
    // 設定
    config: {
        ajaxTimeout: 60000,
        autoSaveDelay: 2000,
        noticeDisplayTime: 5000
    },

    // 初期化フラグ
    initialized: false,

    /**
     * 初期化
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
            this.setupMetaboxes();
            this.setupSheetsAdmin();
            this.setupUtils();
            
            this.initialized = true;
            console.log('[Grant Insight Admin] Initialized successfully');
        } catch (error) {
            console.error('[Grant Insight Admin] Initialization error:', error);
        }
    },

    /**
     * ==========================================================================
     * メタボックス機能
     * ==========================================================================
     */
    setupMetaboxes() {
        this.setupTaxonomyMetaboxes();
        this.setupFieldTracking();
    },

    /**
     * タクソノミーメタボックス
     */
    setupTaxonomyMetaboxes() {
        // 都道府県：全国対象チェックボックス
        const selectAllPrefectures = document.getElementById('select_all_prefectures');
        if (selectAllPrefectures) {
            selectAllPrefectures.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                document.querySelectorAll('.prefecture-checkbox').forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
            });
        }
        
        // 都道府県：個別チェックボックス変更時
        document.querySelectorAll('.prefecture-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const totalPrefectures = document.querySelectorAll('.prefecture-checkbox').length;
                const checkedPrefectures = document.querySelectorAll('.prefecture-checkbox:checked').length;
                if (selectAllPrefectures) {
                    selectAllPrefectures.checked = totalPrefectures === checkedPrefectures;
                }
            });
        });
        
        // 市町村：検索機能（強化版）
        const municipalitySearch = document.getElementById('municipality_search');
        if (municipalitySearch) {
            municipalitySearch.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.municipality-option').forEach(option => {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(searchTerm) ? 'block' : 'none';
                });
                
                // 都道府県グループの表示/非表示も制御
                document.querySelectorAll('.prefecture-group').forEach(group => {
                    const visibleMunicipalities = group.querySelectorAll('.municipality-option[style*="block"], .municipality-option:not([style*="none"])');
                    group.style.display = visibleMunicipalities.length > 0 ? 'block' : 'none';
                });
            });
        }
        
        // 都道府県選択による市町村の自動更新
        this.setupPrefectureMunicipalitySync();
        
        // 新規タームの追加
        this.setupNewTermAddition();
        
        // 初期選択状態チェック
        this.checkInitialSelections();
    },

    /**
     * 新規ターム追加機能
     */
    setupNewTermAddition() {
        // カテゴリー追加
        const addCategoryBtn = document.getElementById('add_grant_category');
        const newCategoryInput = document.getElementById('new_grant_category');
        
        if (addCategoryBtn && newCategoryInput) {
            addCategoryBtn.addEventListener('click', () => {
                const categoryName = newCategoryInput.value.trim();
                if (categoryName) {
                    this.addNewTaxonomyTerm('grant_category', categoryName, 'category');
                }
            });
            
            newCategoryInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addCategoryBtn.click();
                }
            });
        }
        
        // 市町村追加
        const addMunicipalityBtn = document.getElementById('add_municipality');
        const newMunicipalityInput = document.getElementById('new_municipality');
        
        if (addMunicipalityBtn && newMunicipalityInput) {
            addMunicipalityBtn.addEventListener('click', () => {
                const municipalityName = newMunicipalityInput.value.trim();
                if (municipalityName) {
                    this.addNewTaxonomyTerm('grant_municipality', municipalityName, 'municipality');
                }
            });
            
            newMunicipalityInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addMunicipalityBtn.click();
                }
            });
        }
    },

    /**
     * 都道府県と市町村の同期機能
     */
    setupPrefectureMunicipalitySync() {
        // 都道府県チェックボックスの変更を監視
        document.querySelectorAll('.prefecture-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.updateAvailableMunicipalities();
            });
        });
        
        // 地域制限タイプの変更を監視
        document.querySelectorAll('input[name="municipality_selection_type"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.handleRegionalLimitationChange(e.target.value);
            });
        });
        
        // 都道府県フィルターの変更を監視
        const prefectureFilter = document.getElementById('prefecture_filter');
        if (prefectureFilter) {
            prefectureFilter.addEventListener('change', (e) => {
                this.filterMunicipalitiesByPrefecture(e.target.value);
            });
        }
    },
    
    /**
     * 選択された都道府県に基づいて利用可能な市町村を更新
     */
    updateAvailableMunicipalities() {
        const selectedPrefectures = Array.from(document.querySelectorAll('.prefecture-checkbox:checked'))
            .map(cb => cb.dataset.prefectureSlug || cb.value);
        
        // 各都道府県グループの表示/非表示を制御
        document.querySelectorAll('.prefecture-group').forEach(group => {
            const prefectureSlug = group.dataset.prefecture;
            
            if (selectedPrefectures.length === 0 || selectedPrefectures.includes(prefectureSlug)) {
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
                
                // 非表示の都道府県の市町村チェックを外す
                group.querySelectorAll('.municipality-option input[type="checkbox"]:checked').forEach(cb => {
                    cb.checked = false;
                });
            }
        });
        
        // 都道府県フィルターを更新
        const prefectureFilter = document.getElementById('prefecture_filter');
        if (prefectureFilter && selectedPrefectures.length > 0) {
            // 最初に選択された都道府県をデフォルトに設定
            prefectureFilter.value = selectedPrefectures[0];
            this.filterMunicipalitiesByPrefecture(selectedPrefectures[0]);
        }
    },
    
    /**
     * 地域制限タイプ変更の処理
     */
    handleRegionalLimitationChange(limitationType) {
        const prefectureLevelInfo = document.getElementById('prefecture-level-info');
        const municipalityLevelControls = document.getElementById('municipality-level-controls');
        const autoMunicipalityInfo = document.getElementById('auto-municipality-info');
        
        if (limitationType === 'prefecture_level') {
            if (prefectureLevelInfo) prefectureLevelInfo.style.display = 'block';
            if (municipalityLevelControls) municipalityLevelControls.style.display = 'none';
            if (autoMunicipalityInfo) autoMunicipalityInfo.style.display = 'block';
            
            // ACFフィールドの地域制限を更新
            this.updateRegionalLimitationField('prefecture_only');
            
        } else if (limitationType === 'municipality_level') {
            if (prefectureLevelInfo) prefectureLevelInfo.style.display = 'none';
            if (municipalityLevelControls) municipalityLevelControls.style.display = 'block';
            if (autoMunicipalityInfo) autoMunicipalityInfo.style.display = 'none';
            
            // ACFフィールドの地域制限を更新
            this.updateRegionalLimitationField('municipality_only');
        }
    },
    
    /**
     * 地域制限フィールドの更新
     */
    updateRegionalLimitationField(value) {
        // ACFフィールドまたは標準フィールドを更新
        const regionalLimitationField = document.querySelector('select[name*="regional_limitation"], input[name="regional_limitation"]');
        if (regionalLimitationField) {
            regionalLimitationField.value = value;
            
            // changeイベントを発火してACFの処理をトリガー
            regionalLimitationField.dispatchEvent(new Event('change', { bubbles: true }));
        }
    },
    
    /**
     * 都道府県による市町村フィルタリング
     */
    filterMunicipalitiesByPrefecture(prefectureSlug) {
        document.querySelectorAll('.prefecture-group').forEach(group => {
            const groupPrefecture = group.dataset.prefecture;
            
            if (!prefectureSlug || groupPrefecture === prefectureSlug) {
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
            }
        });
    },

    /**
     * 新しいタクソノミータームを追加
     */
    addNewTaxonomyTerm(taxonomy, termName, type) {
        const ajaxData = {
            action: 'gi_add_taxonomy_term',
            taxonomy: taxonomy,
            term_name: termName,
            nonce: window.grantMetaboxes?.nonce
        };

        this.ajax(ajaxData)
            .then(response => {
                if (response.success) {
                    this.addTermToUI(response.data, type);
                    this.showNotice('success', `「${response.data.name}」を追加しました。`);
                } else {
                    this.showNotice('error', `追加に失敗しました: ${response.data}`);
                }
            })
            .catch(error => {
                console.error('Add term error:', error);
                this.showNotice('error', '通信エラーが発生しました。');
            });
    },

    /**
     * UIに新しいタームを追加
     */
    addTermToUI(termData, type) {
        const termId = termData.term_id;
        const termName = termData.name;
        
        let targetContainer = '';
        let inputName = '';
        let inputId = '';
        
        if (type === 'category') {
            targetContainer = '#grant-category-selection';
            inputName = 'grant_categories[]';
            inputId = 'new_grant_category';
        } else if (type === 'municipality') {
            targetContainer = '#grant-municipality-selection';
            inputName = 'grant_municipalities[]';
            inputId = 'new_municipality';
        }
        
        const container = document.querySelector(targetContainer);
        const input = document.getElementById(inputId);
        
        if (container) {
            const newOption = document.createElement('label');
            newOption.style.display = 'block';
            newOption.style.marginBottom = '6px';
            if (type === 'municipality') {
                newOption.classList.add('municipality-option');
            }
            
            newOption.innerHTML = `
                <input type="checkbox" 
                       name="${inputName}" 
                       value="${termId}"
                       checked>
                ${this.escapeHtml(termName)}
                <span style="color: #666;">（0件）</span>
            `;
            
            // 追加ボタンの直前に挿入
            const addButtonContainer = container.querySelector('> div:last-child');
            if (addButtonContainer) {
                container.insertBefore(newOption, addButtonContainer);
            } else {
                container.appendChild(newOption);
            }
        }
        
        // 入力フィールドをクリア
        if (input) {
            input.value = '';
        }
    },

    /**
     * 初期選択状態をチェック
     */
    checkInitialSelections() {
        const selectAllPrefectures = document.getElementById('select_all_prefectures');
        if (selectAllPrefectures) {
            const totalPrefectures = document.querySelectorAll('.prefecture-checkbox').length;
            const checkedPrefectures = document.querySelectorAll('.prefecture-checkbox:checked').length;
            selectAllPrefectures.checked = totalPrefectures === checkedPrefectures && totalPrefectures > 0;
        }
    },

    /**
     * フィールド変更の追跡
     */
    setupFieldTracking() {
        // タクソノミーの変更を検知
        const taxonomyInputs = document.querySelectorAll(
            'input[name="grant_categories[]"], input[name="grant_prefectures[]"], input[name="grant_municipalities[]"]'
        );
        
        taxonomyInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                // 変更を視覚的に表示
                const metaboxContent = e.target.closest('.grant-metabox-content');
                if (metaboxContent) {
                    metaboxContent.style.borderLeft = '3px solid #00a0d2';
                    setTimeout(() => {
                        metaboxContent.style.borderLeft = '';
                    }, 2000);
                }
            });
        });
    },

    /**
     * ==========================================================================
     * Google Sheets管理機能
     * ==========================================================================
     */
    setupSheetsAdmin() {
        this.setupConnectionTest();
        this.setupSyncButtons();
        this.setupLogManagement();
        this.setupSheetOperations();
        this.setupFieldOperations();
        this.setupFormHandling();
        
        // 初回接続テスト
        setTimeout(() => {
            if (document.getElementById('test-connection')) {
                this.testConnection();
            }
        }, 1000);
    },

    /**
     * 接続テスト機能
     */
    setupConnectionTest() {
        const testBtn = document.getElementById('test-connection');
        if (testBtn) {
            testBtn.addEventListener('click', () => this.testConnection());
        }
    },

    /**
     * 接続テスト実行
     */
    testConnection() {
        const btn = document.getElementById('test-connection');
        const status = document.getElementById('connection-status');
        
        if (!btn || !status) return;
        
        // ボタンを無効化
        btn.disabled = true;
        btn.textContent = window.giSheetsAdmin?.strings?.testing || 'テスト中...';
        
        // ステータスを更新中に設定
        this.updateConnectionStatus('testing', 'テスト中...');
        
        const ajaxData = {
            action: 'gi_test_sheets_connection',
            nonce: window.giSheetsAdmin?.nonce
        };

        this.ajax(ajaxData)
            .then(response => {
                if (response.success) {
                    this.updateConnectionStatus('connected', response.data);
                    this.showNotice('success', response.data);
                } else {
                    this.updateConnectionStatus('error', response.data || 'エラーが発生しました');
                    this.showNotice('error', response.data || 'エラーが発生しました');
                }
            })
            .catch(error => {
                console.error('Connection test error:', error);
                const message = 'ネットワークエラー: ' + error.message;
                this.updateConnectionStatus('error', message);
                this.showNotice('error', message);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = '接続をテスト';
            });
    },

    /**
     * 接続ステータス更新
     */
    updateConnectionStatus(status, message) {
        const statusElement = document.getElementById('connection-status');
        if (!statusElement) return;
        
        const textElement = statusElement.querySelector('.gi-status-text');
        
        // クラスをリセット
        statusElement.className = 'gi-connection-status';
        
        // 新しいクラスを追加
        statusElement.classList.add(`gi-status-${status}`);
        
        // テキストを更新
        if (textElement) {
            textElement.textContent = message;
        } else {
            statusElement.textContent = message;
        }
    },

    /**
     * 同期ボタンの設定
     */
    setupSyncButtons() {
        document.querySelectorAll('.gi-sync-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleManualSync(e));
        });
    },

    /**
     * 手動同期処理
     */
    handleManualSync(event) {
        const btn = event.target;
        const direction = btn.dataset.direction;
        const originalText = btn.textContent;
        
        // 確認ダイアログ
        const confirmMessage = window.giSheetsAdmin?.strings?.confirm_sync || 
                              '同期を実行しますか？この操作には時間がかかる場合があります。';
        
        if (!confirm(confirmMessage)) {
            return;
        }
        
        // ボタンを無効化
        btn.disabled = true;
        btn.textContent = window.giSheetsAdmin?.strings?.syncing || '同期中...';
        document.querySelectorAll('.gi-sync-btn').forEach(b => b.disabled = true);
        
        // 結果エリアを初期化
        const syncResult = document.getElementById('sync-result');
        if (syncResult) {
            syncResult.style.display = 'none';
        }
        
        const ajaxData = {
            action: 'gi_manual_sheets_sync',
            direction: direction,
            nonce: window.giSheetsAdmin?.nonce
        };

        this.ajax(ajaxData, { timeout: 120000 }) // 2分タイムアウト
            .then(response => {
                if (response.success) {
                    this.showSyncResult('success', response.data);
                    this.showNotice('success', response.data);
                } else {
                    this.showSyncResult('error', response.data || '同期に失敗しました');
                    this.showNotice('error', response.data || '同期に失敗しました');
                }
            })
            .catch(error => {
                console.error('Sync error details:', {
                    message: error.message,
                    stack: error.stack,
                    error: error
                });
                
                // より詳細なエラーメッセージ
                let message = 'ネットワークエラー: ' + error.message;
                
                // HTTP 500エラーの場合
                if (error.message.includes('HTTP 500')) {
                    message = 'サーバーエラー (HTTP 500): PHPエラーが発生しました。WordPressのエラーログを確認してください。';
                    console.error('HTTP 500 エラー詳細: admin-ajax.phpでPHPの致命的エラーが発生している可能性があります。');
                    console.error('考えられる原因:');
                    console.error('1. Google Sheets APIの認証エラー');
                    console.error('2. メモリ不足またはタイムアウト');
                    console.error('3. 未定義の関数または変数へのアクセス');
                }
                
                this.showSyncResult('error', message);
                this.showNotice('error', message);
            })
            .finally(() => {
                // ボタンを復元
                document.querySelectorAll('.gi-sync-btn').forEach(b => b.disabled = false);
                btn.textContent = originalText;
                
                // ログを自動更新
                setTimeout(() => this.refreshLog(), 2000);
            });
    },

    /**
     * 同期結果表示
     */
    showSyncResult(type, message) {
        const result = document.getElementById('sync-result');
        if (!result) return;
        
        const notice = result.querySelector('.notice');
        const messageElement = document.getElementById('sync-message');
        
        if (notice) {
            // クラスをリセット
            notice.classList.remove('notice-success', 'notice-error');
            
            // 新しいクラスを追加
            notice.classList.add(type === 'success' ? 'notice-success' : 'notice-error');
        }
        
        // メッセージを設定
        if (messageElement) {
            messageElement.textContent = message;
        }
        
        // 表示
        result.style.display = 'block';
        
        // 5秒後に自動で隠す
        setTimeout(() => {
            result.style.display = 'none';
        }, this.config.noticeDisplayTime);
    },

    /**
     * ログ管理の設定
     */
    setupLogManagement() {
        const refreshBtn = document.getElementById('refresh-log');
        const clearBtn = document.getElementById('clear-log');
        
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.refreshLog());
        }
        
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.clearLog());
        }
    },

    /**
     * ログ更新
     */
    refreshLog() {
        const btn = document.getElementById('refresh-log');
        if (!btn) return;
        
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = '更新中...';
        
        // シンプルにページをリロード
        setTimeout(() => {
            window.location.reload();
        }, 500);
    },

    /**
     * ログクリア
     */
    clearLog() {
        if (!confirm('ログをクリアしますか？この操作は取り消せません。')) {
            return;
        }
        
        const btn = document.getElementById('clear-log');
        if (!btn) return;
        
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'クリア中...';
        
        const ajaxData = {
            action: 'gi_clear_sheets_log',
            nonce: window.giSheetsAdmin?.nonce
        };

        this.ajax(ajaxData)
            .then(response => {
                if (response.success) {
                    this.showNotice('success', response.data);
                    
                    // ログエリアをクリア
                    const logElement = document.getElementById('sync-log');
                    if (logElement) {
                        logElement.innerHTML = '<p>まだログがありません。</p>';
                    }
                } else {
                    this.showNotice('error', response.data || 'ログのクリアに失敗しました');
                }
            })
            .catch(error => {
                console.error('Clear log error:', error);
                this.showNotice('error', 'ネットワークエラー: ' + error.message);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = originalText;
            });
    },

    /**
     * シート操作の設定
     */
    setupSheetOperations() {
        const initializeBtn = document.getElementById('initialize-sheet');
        const exportAllBtn = document.getElementById('export-all-posts');
        const clearSheetBtn = document.getElementById('clear-sheet');
        const exportByIdRangeBtn = document.getElementById('export-by-id-range');
        const checkDuplicatesBtn = document.getElementById('check-duplicates');
        const exportDuplicatesBtn = document.getElementById('export-duplicates');
        
        if (initializeBtn) {
            initializeBtn.addEventListener('click', () => this.initializeSheet());
        }
        
        if (exportAllBtn) {
            exportAllBtn.addEventListener('click', () => this.exportAllPosts());
        }
        
        if (clearSheetBtn) {
            clearSheetBtn.addEventListener('click', () => this.clearSheet());
        }
        
        if (exportByIdRangeBtn) {
            exportByIdRangeBtn.addEventListener('click', () => this.exportPostsByIdRange());
        }
        
        if (checkDuplicatesBtn) {
            checkDuplicatesBtn.addEventListener('click', () => this.checkDuplicateTitles());
        }
        
        if (exportDuplicatesBtn) {
            exportDuplicatesBtn.addEventListener('click', () => this.exportDuplicateTitles());
        }
    },

    /**
     * シート初期化
     */
    initializeSheet() {
        if (!confirm('スプレッドシートを初期化しますか？ヘッダー行と既存投稿がエクスポートされます。')) {
            return;
        }
        
        this.executeSheetOperation('initialize-sheet', 'gi_initialize_sheet', '初期化中...');
    },

    /**
     * 全投稿エクスポート
     */
    exportAllPosts() {
        if (!confirm('全投稿をスプレッドシートにエクスポートしますか？')) {
            return;
        }
        
        this.executeSheetOperation('export-all-posts', 'gi_export_all_posts', 'エクスポート中...');
    },

    /**
     * シートクリア
     */
    clearSheet() {
        if (!confirm('⚠️ 注意：スプレッドシートの全データが削除されます。\nこの操作は取り消せません。本当に実行しますか？')) {
            return;
        }
        
        this.executeSheetOperation('clear-sheet', 'gi_clear_sheet', 'クリア中...');
    },

    /**
     * 投稿ID範囲指定エクスポート
     */
    exportPostsByIdRange() {
        const startIdInput = document.getElementById('export-id-start');
        const endIdInput = document.getElementById('export-id-end');
        const btn = document.getElementById('export-by-id-range');
        const resultDiv = document.getElementById('id-range-export-result');
        const messageDiv = document.getElementById('id-range-export-message');
        
        if (!startIdInput || !endIdInput || !btn) return;
        
        const startId = parseInt(startIdInput.value);
        const endId = parseInt(endIdInput.value);
        
        // バリデーション
        if (!startId || !endId || startId <= 0 || endId <= 0) {
            this.showNotice('error', '開始IDと終了IDを入力してください');
            return;
        }
        
        if (startId > endId) {
            this.showNotice('error', '開始IDは終了ID以下にしてください');
            return;
        }
        
        // 確認ダイアログ
        if (!confirm(`ID ${startId} 〜 ${endId} の範囲の投稿をスプレッドシートにエクスポートしますか？`)) {
            return;
        }
        
        // ボタンを無効化
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="dashicons dashicons-update" style="margin-top: 3px; animation: rotation 1s infinite linear;"></span> エクスポート中...';
        
        // 結果エリアを非表示
        if (resultDiv) resultDiv.style.display = 'none';
        
        const ajaxData = {
            action: 'gi_export_posts_by_id_range',
            nonce: window.giSheetsAdmin?.nonce,
            start_id: startId,
            end_id: endId
        };

        this.ajax(ajaxData, { timeout: 120000 })
            .then(response => {
                if (response.success) {
                    const data = response.data;
                    const message = data.message || `${data.count} 件の投稿をエクスポートしました`;
                    
                    if (messageDiv) messageDiv.textContent = message;
                    if (resultDiv) {
                        resultDiv.className = 'notice notice-success';
                        resultDiv.style.display = 'block';
                    }
                    
                    this.showNotice('success', message);
                    
                    // 入力フィールドをクリア
                    startIdInput.value = '';
                    endIdInput.value = '';
                } else {
                    const errorMsg = response.data || 'エクスポートに失敗しました';
                    
                    if (messageDiv) messageDiv.textContent = errorMsg;
                    if (resultDiv) {
                        resultDiv.className = 'notice notice-error';
                        resultDiv.style.display = 'block';
                    }
                    
                    this.showNotice('error', errorMsg);
                }
            })
            .catch(error => {
                console.error('ID range export error:', error);
                const errorMsg = 'ネットワークエラー: ' + error.message;
                
                if (messageDiv) messageDiv.textContent = errorMsg;
                if (resultDiv) {
                    resultDiv.className = 'notice notice-error';
                    resultDiv.style.display = 'block';
                }
                
                this.showNotice('error', errorMsg);
            })
            .finally(() => {
                // ボタンを復元
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    },
    
    /**
     * 重複タイトルチェック
     */
    checkDuplicateTitles() {
        const btn = document.getElementById('check-duplicates');
        const resultDiv = document.getElementById('duplicate-check-result');
        const contentDiv = document.getElementById('duplicate-check-content');
        
        if (!btn) return;
        
        // ボタンを無効化
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = '🔍 チェック中...';
        
        // 結果エリアを非表示
        if (resultDiv) resultDiv.style.display = 'none';
        
        const ajaxData = {
            action: 'gi_check_duplicate_titles',
            nonce: window.giSheetsAdmin?.nonce
        };

        this.ajax(ajaxData)
            .then(response => {
                if (response.success) {
                    const data = response.data;
                    let html = '<strong>' + data.message + '</strong><br><br>';
                    
                    if (data.duplicates && data.duplicates.length > 0) {
                        html += '<table style="width: 100%; border-collapse: collapse; font-size: 13px;">';
                        html += '<thead><tr style="background: #f9f9f9;">';
                        html += '<th style="padding: 8px; border: 1px solid #ddd; text-align: left;">タイトル</th>';
                        html += '<th style="padding: 8px; border: 1px solid #ddd; text-align: center;">重複数</th>';
                        html += '<th style="padding: 8px; border: 1px solid #ddd; text-align: left;">投稿ID / ステータス / 日付</th>';
                        html += '</tr></thead><tbody>';
                        
                        data.duplicates.forEach(dup => {
                            html += '<tr>';
                            html += '<td style="padding: 8px; border: 1px solid #ddd;">' + this.escapeHtml(dup.title) + '</td>';
                            html += '<td style="padding: 8px; border: 1px solid #ddd; text-align: center; font-weight: bold; color: #d63638;">' + dup.count + '</td>';
                            html += '<td style="padding: 8px; border: 1px solid #ddd;">';
                            
                            dup.posts.forEach((post, index) => {
                                if (index > 0) html += '<br>';
                                const statusColors = {
                                    'publish': '#00a32a',
                                    'draft': '#2271b1',
                                    'private': '#dba617',
                                    'pending': '#999'
                                };
                                const color = statusColors[post.status] || '#666';
                                html += '<strong>ID ' + post.id + '</strong> | ';
                                html += '<span style="color: ' + color + ';">' + post.status + '</span> | ';
                                html += post.modified.substring(0, 10);
                            });
                            
                            html += '</td></tr>';
                        });
                        
                        html += '</tbody></table>';
                        html += '<br><p class="description"><strong>💡 ヒント：</strong>インポート時は、タイトルが一致する既存投稿が自動的に上書きされます。</p>';
                    }
                    
                    if (contentDiv) contentDiv.innerHTML = html;
                    if (resultDiv) {
                        resultDiv.className = 'notice ' + (data.duplicates.length > 0 ? 'notice-warning' : 'notice-success');
                        resultDiv.style.display = 'block';
                    }
                    
                } else {
                    const errorMsg = response.data || 'チェックに失敗しました';
                    if (contentDiv) contentDiv.textContent = errorMsg;
                    if (resultDiv) {
                        resultDiv.className = 'notice notice-error';
                        resultDiv.style.display = 'block';
                    }
                    this.showNotice('error', errorMsg);
                }
            })
            .catch(error => {
                console.error('Duplicate check error:', error);
                const errorMsg = 'ネットワークエラー: ' + error.message;
                if (contentDiv) contentDiv.textContent = errorMsg;
                if (resultDiv) {
                    resultDiv.className = 'notice notice-error';
                    resultDiv.style.display = 'block';
                }
                this.showNotice('error', errorMsg);
            })
            .finally(() => {
                // ボタンを復元
                btn.disabled = false;
                btn.textContent = originalText;
            });
    },
    
    /**
     * 重複タイトルをスプレッドシートにエクスポート
     */
    exportDuplicateTitles() {
        const btn = document.getElementById('export-duplicates');
        const resultDiv = document.getElementById('duplicate-export-result');
        const messageDiv = document.getElementById('duplicate-export-message');
        
        if (!btn) return;
        
        // 確認ダイアログ
        if (!confirm('重複している投稿を「重複タイトル」シートにエクスポートしますか？')) {
            return;
        }
        
        // ボタンを無効化
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = '📤 エクスポート中...';
        
        // 結果エリアを非表示
        if (resultDiv) resultDiv.style.display = 'none';
        
        const ajaxData = {
            action: 'gi_export_duplicate_titles',
            nonce: window.giSheetsAdmin?.nonce
        };

        this.ajax(ajaxData, { timeout: 120000 })
            .then(response => {
                if (response.success) {
                    const data = response.data;
                    let html = '<strong>' + data.message + '</strong><br><br>';
                    
                    if (data.count > 0) {
                        html += '<p>';
                        html += '✅ 重複グループ数: <strong>' + data.count + '</strong><br>';
                        html += '📊 総投稿数: <strong>' + data.total_posts + '</strong><br>';
                        html += '</p>';
                        
                        if (data.spreadsheet_url) {
                            html += '<p>';
                            html += '<a href="' + data.spreadsheet_url + '" target="_blank" class="button button-primary">';
                            html += '📊 「' + data.sheet_name + '」シートを開く';
                            html += '</a>';
                            html += '</p>';
                            html += '<p class="description">';
                            html += '<strong>💡 次のステップ：</strong><br>';
                            html += '1. スプレッドシートで重複投稿を確認<br>';
                            html += '2. 削除したい投稿のE列（ステータス）を「deleted」に変更<br>';
                            html += '3. 「Sheets → WordPress 同期」で削除を実行';
                            html += '</p>';
                        }
                    }
                    
                    if (messageDiv) messageDiv.innerHTML = html;
                    if (resultDiv) {
                        resultDiv.className = 'notice notice-success';
                        resultDiv.style.display = 'block';
                    }
                    
                    this.showNotice('success', data.message);
                    
                } else {
                    const errorMsg = response.data || 'エクスポートに失敗しました';
                    if (messageDiv) messageDiv.textContent = errorMsg;
                    if (resultDiv) {
                        resultDiv.className = 'notice notice-error';
                        resultDiv.style.display = 'block';
                    }
                    this.showNotice('error', errorMsg);
                }
            })
            .catch(error => {
                console.error('Export duplicates error:', error);
                const errorMsg = 'ネットワークエラー: ' + error.message;
                if (messageDiv) messageDiv.textContent = errorMsg;
                if (resultDiv) {
                    resultDiv.className = 'notice notice-error';
                    resultDiv.style.display = 'block';
                }
                this.showNotice('error', errorMsg);
            })
            .finally(() => {
                // ボタンを復元
                btn.disabled = false;
                btn.textContent = originalText;
            });
    },

    /**
     * シート操作実行のヘルパー
     */
    executeSheetOperation(btnId, action, loadingText) {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = loadingText;
        
        const ajaxData = {
            action: action,
            nonce: window.giSheetsAdmin?.nonce
        };

        this.ajax(ajaxData, { timeout: 120000 })
            .then(response => {
                if (response.success) {
                    this.showNotice('success', response.data);
                } else {
                    this.showNotice('error', response.data || '操作に失敗しました');
                }
            })
            .catch(error => {
                console.error(`${action} error:`, error);
                this.showNotice('error', 'ネットワークエラー: ' + error.message);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = originalText;
            });
    },

    /**
     * フィールド操作の設定
     */
    setupFieldOperations() {
        const setupValidationBtn = document.getElementById('setup-field-validation');
        const testFieldsBtn = document.getElementById('test-specific-fields');
        
        if (setupValidationBtn) {
            setupValidationBtn.addEventListener('click', () => this.setupFieldValidation());
        }
        
        if (testFieldsBtn) {
            testFieldsBtn.addEventListener('click', () => this.testSpecificFields());
        }
    },

    /**
     * フィールドバリデーション設定
     */
    setupFieldValidation() {
        const btn = document.getElementById('setup-field-validation');
        const result = document.getElementById('validation-result');
        const message = document.getElementById('validation-message');
        
        if (!btn) return;
        
        btn.disabled = true;
        btn.innerHTML = '🔧 設定準備中...';
        
        if (result) result.style.display = 'none';
        
        const ajaxData = {
            action: 'gi_setup_field_validation',
            nonce: window.giSheetsAdmin?.nonce
        };

        this.ajax(ajaxData, { timeout: this.config.ajaxTimeout })
            .then(response => {
                if (response.success) {
                    const data = response.data;
                    const html = `
                        <strong>✅ フィールドバリデーション情報の準備が完了しました</strong><br>
                        ${data.message}<br><br>
                        <strong>📋 次の手順でスプレッドシートにプルダウンを設定してください：</strong><br>
                        ${Object.values(data.next_steps || {}).map((step, index) => `${index + 1}. ${step}`).join('<br>')}
                        <br><br>
                        <em>設定後は、選択肢フィールド（E、M、O、R、U、V列）の背景が薄い青色になり、プルダウンメニューから正しい値を選択できるようになります。</em>
                    `;
                    
                    if (message) message.innerHTML = html;
                    if (result) {
                        result.classList.remove('notice-error', 'notice-warning');
                        result.classList.add('notice-success');
                        result.style.display = 'block';
                    }
                } else {
                    const errorHtml = '❌ フィールドバリデーション設定の準備に失敗しました: ' + (response.data || '不明なエラー');
                    if (message) message.innerHTML = errorHtml;
                    if (result) {
                        result.classList.remove('notice-success', 'notice-warning');
                        result.classList.add('notice-error');
                        result.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Field validation setup error:', error);
                const errorHtml = '❌ フィールドバリデーション設定中にエラーが発生しました: ' + error.message;
                if (message) message.innerHTML = errorHtml;
                if (result) {
                    result.classList.remove('notice-success', 'notice-warning');
                    result.classList.add('notice-error');
                    result.style.display = 'block';
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '🔧 フィールドバリデーション設定を準備';
            });
    },

    /**
     * 特定フィールドテスト
     */
    testSpecificFields() {
        const btn = document.getElementById('test-specific-fields');
        const result = document.getElementById('field-test-result');
        const content = document.getElementById('field-test-content');
        
        if (!btn) return;
        
        btn.disabled = true;
        btn.textContent = '🔍 テスト実行中...';
        
        if (result) result.style.display = 'none';
        
        const ajaxData = {
            action: 'gi_test_specific_fields',
            nonce: window.giSheetsAdmin?.nonce
        };

        this.ajax(ajaxData, { timeout: 30000 })
            .then(response => {
                if (response.success) {
                    const html = this.buildFieldTestResultHtml(response.data);
                    
                    if (content) content.innerHTML = html;
                    if (result) {
                        result.classList.remove('notice-error');
                        result.classList.add('notice-success');
                        result.style.display = 'block';
                    }
                } else {
                    const errorHtml = '❌ フィールドテストに失敗しました: ' + (response.data || '不明なエラー');
                    if (content) content.innerHTML = errorHtml;
                    if (result) {
                        result.classList.remove('notice-success');
                        result.classList.add('notice-error');
                        result.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Field test error:', error);
                const errorHtml = '❌ フィールドテスト中にエラーが発生しました: ' + error.message;
                if (content) content.innerHTML = errorHtml;
                if (result) {
                    result.classList.remove('notice-success');
                    result.classList.add('notice-error');
                    result.style.display = 'block';
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = '🔍 フィールド同期テスト';
            });
    },

    /**
     * フィールドテスト結果HTMLの構築
     */
    buildFieldTestResultHtml(data) {
        let html = `
            <strong>🔍 フィールド同期テスト結果</strong><br>
            <strong>テスト対象行:</strong> ${data.total_rows || 0}行（最初の5行をテスト）<br><br>
        `;
        
        if (!data.test_results || data.test_results.length === 0) {
            html += '<div style="background:#fff3cd;padding:10px;border-radius:3px;margin:5px 0;">⚠️ テスト可能な投稿が見つかりませんでした。スプレッドシートにWordPress投稿IDが設定された行があることを確認してください。</div>';
            return html;
        }
        
        let hasMismatches = false;
        
        data.test_results.forEach(test => {
            html += `
                <div style="border:1px solid #ddd;padding:10px;margin:10px 0;border-radius:5px;">
                    <strong>📝 投稿: ${this.escapeHtml(test.post_title)} (ID: ${test.post_id}, 行: ${test.sheet_row})</strong><br><br>
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <tr style="background:#f2f2f2;">
                            <th style="border:1px solid #ddd;padding:5px;">フィールド</th>
                            <th style="border:1px solid #ddd;padding:5px;">列</th>
                            <th style="border:1px solid #ddd;padding:5px;">スプレッドシート値</th>
                            <th style="border:1px solid #ddd;padding:5px;">WordPress値</th>
                            <th style="border:1px solid #ddd;padding:5px;">同期状況</th>
                        </tr>
            `;
            
            Object.keys(test.fields).forEach(fieldKey => {
                const field = test.fields[fieldKey];
                const statusColor = field.matches ? '#d4edda' : '#f8d7da';
                const statusText = field.matches ? '✅ 一致' : '❌ 不一致';
                
                if (!field.matches) {
                    hasMismatches = true;
                }
                
                html += `
                    <tr style="background:${statusColor};">
                        <td style="border:1px solid #ddd;padding:5px;">${this.escapeHtml(fieldKey)}</td>
                        <td style="border:1px solid #ddd;padding:5px;">${this.escapeHtml(field.column || '')}</td>
                        <td style="border:1px solid #ddd;padding:5px;">${this.escapeHtml(field.sheet_value || '(空)')}</td>
                        <td style="border:1px solid #ddd;padding:5px;">${this.escapeHtml(field.wp_value || '(空)')}</td>
                        <td style="border:1px solid #ddd;padding:5px;">${statusText}</td>
                    </tr>
                `;
            });
            
            html += '</table></div>';
        });
        
        if (hasMismatches) {
            html += `
                <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:3px;margin:10px 0;">
                    <strong>⚠️ 同期の不一致が検出されました</strong><br>
                    上記の表で「❌ 不一致」となっているフィールドは、スプレッドシートとWordPressで値が異なります。<br>
                    「Sheets → WordPress」同期を実行して修正することをお勧めします。
                </div>
            `;
        } else {
            html += `
                <div style="background:#d4edda;color:#155724;padding:10px;border-radius:3px;margin:10px 0;">
                    <strong>✅ すべてのフィールドが正常に同期されています</strong><br>
                    都道府県、カテゴリ、対象市町村のフィールドは正しく同期されています。
                </div>
            `;
        }
        
        return html;
    },

    /**
     * フォーム処理の設定
     */
    setupFormHandling() {
        // 設定フォームの送信処理
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', () => {
                const submitBtn = form.querySelector('input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.value = '保存中...';
                    
                    // フォーム送信後にボタンを復元
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.value = '設定を保存';
                    }, 3000);
                }
            });
        });
        
        // コピーボタンの処理
        document.querySelectorAll('.gi-copy-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleCopyButton(e));
        });
    },

    /**
     * コピーボタンの処理
     */
    handleCopyButton(event) {
        const btn = event.target;
        const textToCopy = btn.dataset.copy;
        const originalText = btn.textContent;
        
        if (!textToCopy) return;
        
        this.copyToClipboard(textToCopy)
            .then(() => {
                btn.textContent = 'コピー済み';
                btn.classList.add('gi-copied');
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.classList.remove('gi-copied');
                }, 2000);
            })
            .catch(error => {
                console.error('Copy error:', error);
                this.showNotice('error', 'コピーに失敗しました');
            });
    },

    /**
     * ==========================================================================
     * ユーティリティ関数
     * ==========================================================================
     */

    /**
     * AJAX関数
     */
    ajax(data, options = {}) {
        const url = options.url || window.giSheetsAdmin?.ajaxurl || '/wp-admin/admin-ajax.php';
        const timeout = options.timeout || this.config.ajaxTimeout;
        
        const requestData = {
            ...data,
            nonce: data.nonce || window.giSheetsAdmin?.nonce
        };

        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                ...options.headers
            },
            body: new URLSearchParams(requestData).toString(),
            signal: AbortSignal.timeout(timeout)
        }).then(async response => {
            if (!response.ok) {
                // エラーレスポンスのボディも取得
                const errorBody = await response.text().catch(() => 'No response body');
                console.error('HTTP Error Response Body:', errorBody);
                
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        });
    },

    /**
     * HTMLエスケープ
     */
    escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    },

    /**
     * 通知表示
     */
    showNotice(type, message) {
        // 既存の通知を削除
        document.querySelectorAll('.gi-admin-notice').forEach(notice => notice.remove());
        
        // 新しい通知を作成
        const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        const notice = document.createElement('div');
        notice.className = `notice ${noticeClass} is-dismissible gi-admin-notice`;
        notice.innerHTML = `
            <p>${this.escapeHtml(message)}</p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">この通知を閉じる</span>
            </button>
        `;
        
        // 通知を挿入
        const wrap = document.querySelector('.wrap h1');
        if (wrap) {
            wrap.parentNode.insertBefore(notice, wrap.nextSibling);
        } else {
            document.body.insertBefore(notice, document.body.firstChild);
        }
        
        // 自動で消す
        setTimeout(() => {
            notice.style.opacity = '0';
            setTimeout(() => notice.remove(), 300);
        }, this.config.noticeDisplayTime);
        
        // 閉じるボタンの処理
        notice.querySelector('.notice-dismiss').addEventListener('click', () => {
            notice.style.opacity = '0';
            setTimeout(() => notice.remove(), 300);
        });
    },

    /**
     * クリップボードにコピー
     */
    async copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            // モダンブラウザ
            return await navigator.clipboard.writeText(text);
        } else {
            // フォールバック
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (!successful) {
                    throw new Error('Copy command failed');
                }
            } finally {
                document.body.removeChild(textarea);
            }
        }
    },

    /**
     * ==========================================================================
     * ユーティリティ機能
     * ==========================================================================
     */
    setupUtils() {
        // ユーティリティ機能の初期化
        // 現時点では特に初期化する内容はないが、将来の拡張のために残しておく
        console.log('[Grant Insight Admin] Utils setup completed');
    }
};

/**
 * =============================================================================
 * 自動初期化・互換性維持
 * =============================================================================
 */

// jQuery互換性ラッパー（既存コードとの互換性のため）
if (typeof jQuery !== 'undefined') {
    (function($) {
        'use strict';
        
        $(document).ready(function() {
            GrantInsightAdmin.init();
            console.log('✅ Grant Insight Admin (jQuery compatible) initialized');
        });
        
    })(jQuery);
} else {
    // Vanilla JS初期化
    GrantInsightAdmin.init();
}

// グローバルアクセス用
window.GrantInsightAdmin = GrantInsightAdmin;

/**
 * =============================================================================
 * 後方互換性サポート
 * =============================================================================
 */

// 既存の変数名をサポート
if (typeof grantMetaboxes === 'undefined' && typeof window.grantMetaboxes !== 'undefined') {
    window.grantMetaboxes = window.grantMetaboxes;
}

if (typeof giSheetsAdmin === 'undefined' && typeof window.giSheetsAdmin !== 'undefined') {
    window.giSheetsAdmin = window.giSheetsAdmin;
}

/**
 * =============================================================================
 * 重複タイトルエクスポート機能
 * =============================================================================
 */
(function($) {
    'use strict';

    $(function() {
        console.log('[Duplicate Exporter] Script loaded');
        
        // #grant-duplicate-exporter IDがページに存在する場合のみ実行
        const $exporter = $('#grant-duplicate-exporter');
        console.log('[Duplicate Exporter] Exporter element found:', $exporter.length > 0);
        
        if (!$exporter.length) {
            console.log('[Duplicate Exporter] Exporter page not detected, skipping initialization');
            return;
        }

        const $button = $('#export-duplicates-btn');
        const $progress = $('#export-progress');
        const $results = $('#export-results');
        
        console.log('[Duplicate Exporter] Button found:', $button.length > 0);
        console.log('[Duplicate Exporter] Progress found:', $progress.length > 0);
        console.log('[Duplicate Exporter] Results found:', $results.length > 0);

        // wp_localize_scriptで渡された 'duplicateExport' オブジェクトがあるか確認
        if (typeof duplicateExport === 'undefined') {
            console.error('[Duplicate Exporter] duplicateExport object is missing!');
            console.error('[Duplicate Exporter] This means wp_localize_script did not run properly');
            return;
        }
        
        console.log('[Duplicate Exporter] duplicateExport object:', duplicateExport);
        console.log('[Duplicate Exporter] AJAX URL:', duplicateExport.ajax_url);
        console.log('[Duplicate Exporter] Nonce:', duplicateExport.nonce);

        $button.on('click', function(e) {
            console.log('[Duplicate Exporter] Button clicked!');
            
            if ($button.is('.disabled')) {
                console.log('[Duplicate Exporter] Button is disabled, ignoring click');
                return;
            }

            // ボタンを無効化し、プログレス表示
            console.log('[Duplicate Exporter] Disabling button and showing progress');
            $button.addClass('disabled').prop('disabled', true);
            $progress.slideDown();
            $results.html('').removeClass('notice notice-success notice-error');

            console.log('[Duplicate Exporter] Sending AJAX request...');
            
            // AJAXリクエストを送信
            $.post(duplicateExport.ajax_url, {
                action: 'export_duplicate_titles',
                nonce: duplicateExport.nonce
            })
            .done(function(response) {
                console.log('[Duplicate Exporter] AJAX response received:', response);
                
                if (response.success) {
                    // 成功
                    console.log('[Duplicate Exporter] Export successful!');
                    let html = '<p>' + response.data.message + '</p>';
                    if (response.data.sheetUrl) {
                        html += '<a href="' + response.data.sheetUrl + '" target="_blank" class="button button-secondary">スプレッドシートを開く</a>';
                    }
                    $results.html(html).addClass('notice notice-success is-dismissible');
                } else {
                    // 失敗
                    console.error('[Duplicate Exporter] Export failed:', response.data);
                    $results.html('<p>エラー: ' + response.data.message + '</p>').addClass('notice notice-error is-dismissible');
                }
            })
            .fail(function(xhr, status, error) {
                // 通信エラー
                console.error('[Duplicate Exporter] AJAX request failed');
                console.error('[Duplicate Exporter] Status:', status);
                console.error('[Duplicate Exporter] Error:', error);
                console.error('[Duplicate Exporter] Response:', xhr.responseText);
                $results.html('<p>エラー: サーバーとの通信に失敗しました。詳細はコンソールをご確認ください。</p>').addClass('notice notice-error is-dismissible');
            })
            .always(function() {
                // ボタンを有効化し、プログレス非表示
                console.log('[Duplicate Exporter] Request completed, re-enabling button');
                $button.removeClass('disabled').prop('disabled', false);
                $progress.slideUp();
            });
        });
        
        console.log('[Duplicate Exporter] Initialization complete');
    });

})(jQuery);


// ============================================================================
// PART 2: Google Sheets Integration Admin
// ============================================================================
/**
 * Google Sheets Admin JavaScript
 * スプレッドシート同期管理画面の機能
 */

(function($) {
    'use strict';

    /**
     * Google Sheets Admin Controller
     */
    const GISheetsAdmin = {
        /**
         * 初期化
         */
        init() {
            console.log('[GI Sheets Admin] Initializing...');
            
            if (typeof giSheetsAdmin === 'undefined') {
                console.error('[GI Sheets Admin] giSheetsAdmin object not found');
                return;
            }
            
            this.bindEvents();
            console.log('[GI Sheets Admin] Initialized successfully');
        },

        /**
         * イベントバインディング
         */
        bindEvents() {
            // 接続テストボタン
            $('#gi-test-connection').on('click', (e) => {
                e.preventDefault();
                this.testConnection();
            });

            // WP to Sheets 同期ボタン
            $('#gi-sync-wp-to-sheets').on('click', (e) => {
                e.preventDefault();
                this.syncData('wp_to_sheets');
            });

            // Sheets to WP 同期ボタン
            $('#gi-sync-sheets-to-wp').on('click', (e) => {
                e.preventDefault();
                this.syncData('sheets_to_wp');
            });
            
            // 都道府県データ検証・エクスポートボタン
            $('#export-invalid-prefectures').on('click', (e) => {
                e.preventDefault();
                this.exportInvalidPrefectures();
            });
            
            // タクソノミーエクスポートボタン
            $('#export-taxonomies').on('click', (e) => {
                e.preventDefault();
                this.exportTaxonomies();
            });
            
            // タクソノミーインポートボタン
            $('#import-taxonomies').on('click', (e) => {
                e.preventDefault();
                this.importTaxonomies();
            });
        },

        /**
         * 接続テスト
         */
        testConnection() {
            console.log('[GI Sheets Admin] Testing connection...');
            
            const $button = $('#gi-test-connection');
            const $result = $('#gi-test-result');
            
            // ボタンを無効化
            $button.prop('disabled', true);
            $button.html('<span class="gi-loading-spinner"></span> ' + giSheetsAdmin.strings.testing);
            
            // 結果エリアをクリア
            $result.removeClass('show gi-test-result-success gi-test-result-error').text('');
            
            // AJAX リクエスト
            $.ajax({
                url: giSheetsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gi_test_sheets_connection',
                    nonce: giSheetsAdmin.nonce
                },
                success: (response) => {
                    console.log('[GI Sheets Admin] Connection test response:', response);
                    
                    if (response.success) {
                        $result
                            .addClass('show gi-test-result-success')
                            .html('<strong>✓ ' + giSheetsAdmin.strings.success + '</strong><br>' + response.data.message);
                    } else {
                        $result
                            .addClass('show gi-test-result-error')
                            .html('<strong>✗ ' + giSheetsAdmin.strings.error + '</strong><br>' + response.data.message);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('[GI Sheets Admin] Connection test error:', error);
                    $result
                        .addClass('show gi-test-result-error')
                        .html('<strong>✗ ' + giSheetsAdmin.strings.error + '</strong><br>AJAX エラー: ' + error);
                },
                complete: () => {
                    // ボタンを再有効化
                    $button.prop('disabled', false);
                    $button.text('接続をテスト');
                }
            });
        },

        /**
         * データ同期
         */
        syncData(direction) {
            console.log('[GI Sheets Admin] Starting sync:', direction);
            
            // 確認ダイアログ
            if (!confirm(giSheetsAdmin.strings.confirm_sync)) {
                return;
            }
            
            const $button = direction === 'wp_to_sheets' 
                ? $('#gi-sync-wp-to-sheets') 
                : $('#gi-sync-sheets-to-wp');
            const $progressContainer = $('#gi-progress-container');
            const $progressBar = $('#gi-progress-fill');
            const $progressText = $('#gi-progress-text');
            const $logContainer = $('#gi-log-messages');
            
            // ボタンを無効化
            $button.prop('disabled', true);
            $button.html('<span class="gi-loading-spinner"></span> ' + giSheetsAdmin.strings.syncing);
            
            // プログレスバーを表示
            $progressContainer.show();
            $progressBar.css('width', '0%');
            $progressText.text('0%');
            
            // ログをクリア
            $logContainer.empty();
            
            // AJAX リクエスト
            $.ajax({
                url: giSheetsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gi_manual_sheets_sync',
                    direction: direction,
                    nonce: giSheetsAdmin.nonce
                },
                success: (response) => {
                    console.log('[GI Sheets Admin] Sync response:', response);
                    
                    if (response.success) {
                        // 成功
                        $progressBar.css('width', '100%');
                        $progressText.text('100%');
                        
                        this.addLogEntry('success', response.data.message);
                        
                        if (response.data.details) {
                            this.addLogEntry('info', '詳細: ' + JSON.stringify(response.data.details));
                        }
                        
                        // 3秒後にプログレスバーを非表示
                        setTimeout(() => {
                            $progressContainer.fadeOut();
                        }, 3000);
                    } else {
                        // エラー
                        $progressBar.css('width', '100%');
                        $progressText.text('エラー');
                        $progressBar.css('background', '#d63638');
                        
                        this.addLogEntry('error', response.data.message || '同期に失敗しました');
                        
                        if (response.data.details) {
                            this.addLogEntry('error', '詳細: ' + JSON.stringify(response.data.details));
                        }
                    }
                },
                error: (xhr, status, error) => {
                    console.error('[GI Sheets Admin] Sync error:', error);
                    
                    $progressBar.css('width', '100%');
                    $progressText.text('エラー');
                    $progressBar.css('background', '#d63638');
                    
                    this.addLogEntry('error', 'AJAX エラー: ' + error);
                    
                    if (xhr.responseText) {
                        this.addLogEntry('error', 'レスポンス: ' + xhr.responseText);
                    }
                },
                complete: () => {
                    // ボタンを再有効化
                    $button.prop('disabled', false);
                    
                    if (direction === 'wp_to_sheets') {
                        $button.html('<i class="dashicons dashicons-upload"></i> WP → Sheets 同期');
                    } else {
                        $button.html('<i class="dashicons dashicons-download"></i> Sheets → WP 同期');
                    }
                }
            });
        },

        /**
         * 都道府県データ検証・エクスポート
         */
        exportInvalidPrefectures() {
            console.log('[GI Sheets Admin] Exporting invalid prefectures...');
            console.log('[GI Sheets Admin] AJAX URL:', giSheetsAdmin.ajaxurl);
            console.log('[GI Sheets Admin] Nonce:', giSheetsAdmin.nonce);
            
            if (!confirm('都道府県データの検証を実行し、問題のある投稿を「都道府県」シートにエクスポートします。よろしいですか？')) {
                console.log('[GI Sheets Admin] User cancelled');
                return;
            }
            
            const $button = $('#export-invalid-prefectures');
            const $result = $('#sync-result');
            const $message = $('#sync-message');
            
            // ボタンを無効化
            $button.prop('disabled', true).text('処理中...');
            
            // 結果エリアをクリア
            $result.hide();
            $message.text('');
            
            console.log('[GI Sheets Admin] Sending AJAX request...');
            
            // AJAX リクエスト
            $.ajax({
                url: giSheetsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gi_export_invalid_prefectures',
                    nonce: giSheetsAdmin.nonce
                },
                beforeSend: function() {
                    console.log('[GI Sheets Admin] AJAX request started');
                },
                success: (response) => {
                    console.log('[GI Sheets Admin] SUCCESS - Response:', response);
                    console.log('[GI Sheets Admin] Response type:', typeof response);
                    console.log('[GI Sheets Admin] Response.success:', response.success);
                    console.log('[GI Sheets Admin] Response.data:', response.data);
                    
                    if (response.success) {
                        $result.removeClass('notice-error').addClass('notice-success');
                        let message = response.data.message || response.data;
                        if (response.data.count) {
                            message += '<br>エクスポート件数: ' + response.data.count + '件';
                        }
                        if (response.data.spreadsheet_id) {
                            message += '<br><a href="https://docs.google.com/spreadsheets/d/' + response.data.spreadsheet_id + '/edit#gid=0" target="_blank">スプレッドシートを開く</a>';
                        }
                        $message.html(message);
                        console.log('[GI Sheets Admin] Success message displayed');
                    } else {
                        $result.removeClass('notice-success').addClass('notice-error');
                        $message.text(response.data || 'エクスポートに失敗しました');
                        console.log('[GI Sheets Admin] Error message displayed:', response.data);
                    }
                    
                    $result.show();
                },
                error: (xhr, status, error) => {
                    console.error('[GI Sheets Admin] ERROR - Status:', status);
                    console.error('[GI Sheets Admin] ERROR - Error:', error);
                    console.error('[GI Sheets Admin] ERROR - XHR:', xhr);
                    console.error('[GI Sheets Admin] ERROR - Response Text:', xhr.responseText);
                    console.error('[GI Sheets Admin] ERROR - Status Code:', xhr.status);
                    console.error('[GI Sheets Admin] ERROR - Status Text:', xhr.statusText);
                    
                    // レスポンステキストをパースしてみる
                    try {
                        const parsedResponse = JSON.parse(xhr.responseText);
                        console.error('[GI Sheets Admin] ERROR - Parsed Response:', parsedResponse);
                    } catch (e) {
                        console.error('[GI Sheets Admin] ERROR - Could not parse response as JSON');
                        console.error('[GI Sheets Admin] ERROR - Raw response (first 500 chars):', xhr.responseText.substring(0, 500));
                    }
                    
                    $result.removeClass('notice-success').addClass('notice-error');
                    
                    let errorMessage = 'エラーが発生しました: ' + error;
                    if (xhr.status === 500) {
                        errorMessage += '<br>サーバーエラー (500): PHPのエラーログを確認してください';
                        if (xhr.responseText) {
                            errorMessage += '<br>詳細: ' + xhr.responseText.substring(0, 200);
                        }
                    }
                    
                    $message.html(errorMessage);
                    $result.show();
                },
                complete: () => {
                    console.log('[GI Sheets Admin] AJAX request completed');
                    // ボタンを再有効化
                    $button.prop('disabled', false).text('🗾 都道府県データ検証・エクスポート');
                }
            });
        },

        /**
         * タクソノミーエクスポート
         */
        exportTaxonomies() {
            console.log('[GI Sheets Admin] Exporting taxonomies...');
            
            if (!confirm('カテゴリ、都道府県、市町村、タグのマスタデータをエクスポートします。よろしいですか？')) {
                console.log('[GI Sheets Admin] User cancelled');
                return;
            }
            
            const $button = $('#export-taxonomies');
            const $result = $('#sync-result');
            const $message = $('#sync-message');
            
            // ボタンを無効化
            $button.prop('disabled', true).text('エクスポート中...');
            
            // 結果エリアをクリア
            $result.hide();
            $message.html('');
            
            console.log('[GI Sheets Admin] Sending AJAX request...');
            
            // AJAX リクエスト
            $.ajax({
                url: giSheetsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gi_export_taxonomies',
                    nonce: giSheetsAdmin.nonce
                },
                beforeSend: function() {
                    console.log('[GI Sheets Admin] Export taxonomies AJAX started');
                },
                success: (response) => {
                    console.log('[GI Sheets Admin] SUCCESS - Response:', response);
                    
                    if (response.success) {
                        $result.removeClass('notice-error').addClass('notice-success');
                        
                        let message = '<strong>' + response.data.message + '</strong><br><br>';
                        
                        if (response.data.results && response.data.results.length > 0) {
                            message += '<table style="width: 100%; border-collapse: collapse;">';
                            message += '<thead><tr style="background: #f0f0f0;">';
                            message += '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">タクソノミー</th>';
                            message += '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">シート名</th>';
                            message += '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">件数</th>';
                            message += '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">ステータス</th>';
                            message += '</tr></thead><tbody>';
                            
                            response.data.results.forEach((result) => {
                                const status = result.success ? '✅ 成功' : '❌ 失敗';
                                const statusColor = result.success ? '#00a32a' : '#d63638';
                                message += '<tr>';
                                message += '<td style="padding: 8px; border: 1px solid #ddd;">' + result.taxonomy + '</td>';
                                message += '<td style="padding: 8px; border: 1px solid #ddd;">' + result.sheet_name + '</td>';
                                message += '<td style="padding: 8px; text-align: center; border: 1px solid #ddd;">' + result.count + '</td>';
                                message += '<td style="padding: 8px; text-align: center; border: 1px solid #ddd; color: ' + statusColor + ';"><strong>' + status + '</strong></td>';
                                message += '</tr>';
                                
                                if (result.error) {
                                    message += '<tr><td colspan="4" style="padding: 8px; border: 1px solid #ddd; color: #d63638;">エラー: ' + result.error + '</td></tr>';
                                }
                            });
                            
                            message += '</tbody></table>';
                        }
                        
                        $message.html(message);
                    } else {
                        $result.removeClass('notice-success').addClass('notice-error');
                        
                        let errorMsg = response.data.message || 'エクスポートに失敗しました';
                        
                        if (response.data.results) {
                            errorMsg += '<br><br><strong>詳細:</strong><br>';
                            response.data.results.forEach((result) => {
                                errorMsg += '- ' + result.taxonomy + ': ' + (result.error || '不明なエラー') + '<br>';
                            });
                        }
                        
                        $message.html(errorMsg);
                    }
                    
                    $result.show();
                },
                error: (xhr, status, error) => {
                    console.error('[GI Sheets Admin] ERROR - XHR:', xhr);
                    console.error('[GI Sheets Admin] ERROR - Status:', status);
                    console.error('[GI Sheets Admin] ERROR - Error:', error);
                    
                    $result.removeClass('notice-success').addClass('notice-error');
                    $message.html('エラーが発生しました: ' + error);
                    $result.show();
                },
                complete: () => {
                    console.log('[GI Sheets Admin] Export taxonomies completed');
                    $button.prop('disabled', false).text('📊 タクソノミーをエクスポート');
                }
            });
        },

        /**
         * タクソノミーインポート
         */
        importTaxonomies() {
            console.log('[GI Sheets Admin] Importing taxonomies...');
            
            if (!confirm('スプレッドシートからタクソノミーをインポートします。\n\n⚠️ 注意: 既存のタクソノミーが更新される可能性があります。\n削除する場合は名前列に「DELETE」または「削除」と入力してください。\n\nよろしいですか？')) {
                console.log('[GI Sheets Admin] User cancelled');
                return;
            }
            
            const $button = $('#import-taxonomies');
            const $result = $('#sync-result');
            const $message = $('#sync-message');
            
            // ボタンを無効化
            $button.prop('disabled', true).text('インポート中...');
            
            // 結果エリアをクリア
            $result.hide();
            $message.html('');
            
            console.log('[GI Sheets Admin] Sending AJAX request...');
            
            // AJAX リクエスト
            $.ajax({
                url: giSheetsAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gi_import_taxonomies',
                    nonce: giSheetsAdmin.nonce
                },
                beforeSend: function() {
                    console.log('[GI Sheets Admin] Import taxonomies AJAX started');
                },
                success: (response) => {
                    console.log('[GI Sheets Admin] SUCCESS - Response:', response);
                    
                    if (response.success) {
                        $result.removeClass('notice-error').addClass('notice-success');
                        
                        let message = '<strong>' + response.data.message + '</strong><br><br>';
                        
                        if (response.data.results && response.data.results.length > 0) {
                            message += '<table style="width: 100%; border-collapse: collapse;">';
                            message += '<thead><tr style="background: #f0f0f0;">';
                            message += '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">タクソノミー</th>';
                            message += '<th style="padding: 8px; text-align: left; border: 1px solid #ddd;">シート名</th>';
                            message += '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">新規作成</th>';
                            message += '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">更新</th>';
                            message += '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">削除</th>';
                            message += '<th style="padding: 8px; text-align: center; border: 1px solid #ddd;">スキップ</th>';
                            message += '</tr></thead><tbody>';
                            
                            response.data.results.forEach((result) => {
                                message += '<tr>';
                                message += '<td style="padding: 8px; border: 1px solid #ddd;">' + result.taxonomy + '</td>';
                                message += '<td style="padding: 8px; border: 1px solid #ddd;">' + result.sheet_name + '</td>';
                                message += '<td style="padding: 8px; text-align: center; border: 1px solid #ddd; color: #00a32a;"><strong>' + result.created + '</strong></td>';
                                message += '<td style="padding: 8px; text-align: center; border: 1px solid #ddd; color: #0073aa;"><strong>' + result.updated + '</strong></td>';
                                message += '<td style="padding: 8px; text-align: center; border: 1px solid #ddd; color: #d63638;"><strong>' + result.deleted + '</strong></td>';
                                message += '<td style="padding: 8px; text-align: center; border: 1px solid #ddd; color: #999;"><strong>' + result.skipped + '</strong></td>';
                                message += '</tr>';
                                
                                if (result.errors && result.errors.length > 0) {
                                    message += '<tr><td colspan="6" style="padding: 8px; border: 1px solid #ddd; color: #d63638;">';
                                    message += '<strong>エラー:</strong><br>';
                                    result.errors.forEach((err) => {
                                        message += '- ' + err + '<br>';
                                    });
                                    message += '</td></tr>';
                                }
                                
                                if (result.error) {
                                    message += '<tr><td colspan="6" style="padding: 8px; border: 1px solid #ddd; color: #d63638;">エラー: ' + result.error + '</td></tr>';
                                }
                            });
                            
                            message += '</tbody></table>';
                        }
                        
                        $message.html(message);
                    } else {
                        $result.removeClass('notice-success').addClass('notice-error');
                        $message.html(response.data || 'インポートに失敗しました');
                    }
                    
                    $result.show();
                },
                error: (xhr, status, error) => {
                    console.error('[GI Sheets Admin] ERROR - XHR:', xhr);
                    console.error('[GI Sheets Admin] ERROR - Status:', status);
                    console.error('[GI Sheets Admin] ERROR - Error:', error);
                    
                    $result.removeClass('notice-success').addClass('notice-error');
                    $message.html('エラーが発生しました: ' + error);
                    $result.show();
                },
                complete: () => {
                    console.log('[GI Sheets Admin] Import taxonomies completed');
                    $button.prop('disabled', false).text('📥 タクソノミーをインポート');
                }
            });
        },
        
        /**
         * ログエントリーを追加
         */
        addLogEntry(type, message) {
            const $logContainer = $('#gi-log-messages');
            const timestamp = new Date().toLocaleTimeString('ja-JP');
            
            let typeClass = '';
            let typeIcon = '';
            
            switch(type) {
                case 'success':
                    typeClass = 'gi-log-success';
                    typeIcon = '✓';
                    break;
                case 'error':
                    typeClass = 'gi-log-error';
                    typeIcon = '✗';
                    break;
                case 'warning':
                    typeClass = 'gi-log-warning';
                    typeIcon = '⚠';
                    break;
                default:
                    typeClass = 'gi-log-message';
                    typeIcon = 'ℹ';
            }
            
            const $entry = $('<div class="gi-log-entry">')
                .html(
                    '<span class="gi-log-timestamp">[' + timestamp + ']</span>' +
                    '<span class="' + typeClass + '">' + typeIcon + ' ' + message + '</span>'
                );
            
            $logContainer.prepend($entry);
            
            // 最大50エントリーまで保持
            if ($logContainer.children().length > 50) {
                $logContainer.children().last().remove();
            }
        }
    };

    // ドキュメント読み込み完了時に初期化
    $(document).ready(() => {
        GISheetsAdmin.init();
    });

})(jQuery);



// ============================================================================
// PART 3: Grant Amount Fixer Tool
// ============================================================================
/**
 * Grant Amount Fixer - JavaScript
 * 助成金額修正ツールのフロントエンド処理
 * 
 * @package Grant_Insight_Perfect
 * @version 1.0.0
 */

(function($) {
    'use strict';
    
    // 状態管理
    let scanResults = null;
    let selectedPostIds = [];
    
    /**
     * 初期化
     */
    $(document).ready(function() {
        initEventHandlers();
    });
    
    /**
     * イベントハンドラー初期化
     */
    function initEventHandlers() {
        // スキャンボタン
        $('#gi-scan-btn').on('click', handleScan);
        
        // 修正ボタン
        $('#gi-fix-btn').on('click', handleFix);
        
        // 全選択チェックボックス
        $(document).on('change', '#gi-select-all', handleSelectAll);
        
        // 個別選択チェックボックス
        $(document).on('change', '.gi-post-checkbox', handlePostSelection);
    }
    
    /**
     * スキャン処理
     */
    function handleScan() {
        const $button = $('#gi-scan-btn');
        const $progress = $('#gi-scan-progress');
        const $results = $('#gi-scan-results');
        
        // ボタン無効化
        $button.prop('disabled', true);
        
        // プログレスバー表示
        $progress.show();
        updateProgress($progress, 0, 'スキャン中...');
        
        // 結果エリアをクリア
        $results.hide().empty();
        
        // AJAX実行
        $.ajax({
            url: giAmountFixer.ajaxUrl,
            type: 'POST',
            data: {
                action: 'gi_scan_grant_amounts',
                nonce: giAmountFixer.nonce
            },
            success: function(response) {
                if (response.success) {
                    scanResults = response.data;
                    displayScanResults(response.data);
                    updateProgress($progress, 100, 'スキャン完了');
                    
                    setTimeout(function() {
                        $progress.fadeOut();
                    }, 1000);
                } else {
                    showError('スキャンに失敗しました: ' + (response.data.message || '不明なエラー'));
                }
            },
            error: function(xhr, status, error) {
                showError('通信エラーが発生しました: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    }
    
    /**
     * スキャン結果表示
     */
    function displayScanResults(data) {
        const $results = $('#gi-scan-results');
        
        let html = '<div class="gi-scan-summary">';
        html += '<h3>スキャン結果</h3>';
        html += '<p>スキャンした投稿数: <strong>' + data.total_scanned + '</strong></p>';
        html += '<p>修正が必要な投稿数: <strong class="gi-highlight">' + data.problematic_count + '</strong></p>';
        html += '</div>';
        
        if (data.problematic_count > 0) {
            html += '<div class="gi-post-list">';
            html += '<h4>修正対象の投稿</h4>';
            html += '<div class="gi-select-all-wrapper">';
            html += '<label><input type="checkbox" id="gi-select-all" checked> すべて選択</label>';
            html += '</div>';
            html += '<table class="wp-list-table widefat fixed striped">';
            html += '<thead><tr>';
            html += '<th class="check-column"><input type="checkbox" id="gi-select-all-header" checked></th>';
            html += '<th>投稿タイトル</th>';
            html += '<th>問題のあるフィールド</th>';
            html += '<th>現在の値</th>';
            html += '<th>修正後の値</th>';
            html += '</tr></thead>';
            html += '<tbody>';
            
            $.each(data.problematic_posts, function(postId, postData) {
                const issuesHtml = postData.issues.map(function(issue) {
                    const fieldLabel = getFieldLabel(issue.field);
                    return '<div class="gi-issue">' +
                           '<strong>' + fieldLabel + ':</strong> ' +
                           '<span class="gi-old-value">' + formatNumber(issue.current_value) + '</span> → ' +
                           '<span class="gi-new-value">' + formatNumber(issue.suggested_value) + '</span>' +
                           '</div>';
                }).join('');
                
                html += '<tr>';
                html += '<td class="check-column"><input type="checkbox" class="gi-post-checkbox" value="' + postId + '" checked></td>';
                html += '<td><strong>' + escapeHtml(postData.title) + '</strong><br><small>ID: ' + postId + '</small></td>';
                html += '<td>' + postData.issues.length + '個</td>';
                html += '<td>' + postData.issues.map(i => formatNumber(i.current_value)).join('<br>') + '</td>';
                html += '<td>' + postData.issues.map(i => formatNumber(i.suggested_value)).join('<br>') + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            html += '</div>';
            
            // プレビューボタン
            html += '<div class="gi-action-buttons">';
            html += '<button id="gi-preview-btn" class="button button-primary">修正内容をプレビュー</button>';
            html += '</div>';
        } else {
            html += '<div class="gi-info-box">';
            html += '<p>修正が必要な投稿は見つかりませんでした。すべての金額は正常です。</p>';
            html += '</div>';
        }
        
        $results.html(html).fadeIn();
        
        // 選択状態の初期化
        selectedPostIds = Object.keys(data.problematic_posts).map(id => parseInt(id));
        
        // プレビューボタンのイベント
        $('#gi-preview-btn').on('click', handlePreview);
        
        // ヘッダーチェックボックスのイベント
        $('#gi-select-all-header').on('change', function() {
            $('#gi-select-all').prop('checked', $(this).prop('checked')).trigger('change');
        });
    }
    
    /**
     * プレビュー処理
     */
    function handlePreview() {
        if (selectedPostIds.length === 0) {
            showError('修正する投稿を選択してください');
            return;
        }
        
        const $button = $('#gi-preview-btn');
        $button.prop('disabled', true).text('プレビュー生成中...');
        
        $.ajax({
            url: giAmountFixer.ajaxUrl,
            type: 'POST',
            data: {
                action: 'gi_preview_fix',
                nonce: giAmountFixer.nonce,
                post_ids: selectedPostIds
            },
            success: function(response) {
                if (response.success) {
                    displayPreview(response.data.preview);
                    $('#gi-fix-section').fadeIn();
                    
                    // プレビューセクションまでスクロール
                    $('html, body').animate({
                        scrollTop: $('#gi-preview-section').offset().top - 50
                    }, 500);
                } else {
                    showError('プレビュー生成に失敗しました: ' + (response.data.message || '不明なエラー'));
                }
            },
            error: function(xhr, status, error) {
                showError('通信エラーが発生しました: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false).text('修正内容をプレビュー');
            }
        });
    }
    
    /**
     * プレビュー表示
     */
    function displayPreview(previewData) {
        const $preview = $('#gi-preview-section');
        let html = '<table class="wp-list-table widefat fixed striped gi-preview-table">';
        html += '<thead><tr>';
        html += '<th>投稿タイトル</th>';
        html += '<th>フィールド</th>';
        html += '<th>現在の値</th>';
        html += '<th></th>';
        html += '<th>修正後の値</th>';
        html += '</tr></thead>';
        html += '<tbody>';
        
        $.each(previewData, function(postId, data) {
            let rowspan = Object.keys(data.current).length;
            let first = true;
            
            $.each(data.current, function(field, currentValue) {
                html += '<tr>';
                
                if (first) {
                    html += '<td rowspan="' + rowspan + '"><strong>' + escapeHtml(data.title) + '</strong></td>';
                    first = false;
                }
                
                html += '<td>' + getFieldLabel(field) + '</td>';
                html += '<td class="gi-old-value">' + formatNumber(currentValue) + '</td>';
                html += '<td class="gi-arrow">→</td>';
                html += '<td class="gi-new-value">' + formatNumber(data.fixed[field]) + '</td>';
                html += '</tr>';
            });
        });
        
        html += '</tbody></table>';
        
        $('#gi-preview-results').html(html);
        $preview.fadeIn();
    }
    
    /**
     * 修正実行処理
     */
    function handleFix() {
        if (selectedPostIds.length === 0) {
            showError('修正する投稿を選択してください');
            return;
        }
        
        // 確認ダイアログ
        if (!confirm('選択した ' + selectedPostIds.length + ' 件の投稿を修正します。\n\nこの操作は元に戻せません。実行しますか？')) {
            return;
        }
        
        const $button = $('#gi-fix-btn');
        const $progress = $('#gi-fix-progress');
        const $results = $('#gi-fix-results');
        
        // ボタン無効化
        $button.prop('disabled', true);
        
        // プログレスバー表示
        $progress.show();
        updateProgress($progress, 0, '修正中...');
        
        // 結果エリアをクリア
        $results.hide().empty();
        
        // AJAX実行
        $.ajax({
            url: giAmountFixer.ajaxUrl,
            type: 'POST',
            data: {
                action: 'gi_fix_grant_amounts',
                nonce: giAmountFixer.nonce,
                post_ids: selectedPostIds
            },
            success: function(response) {
                if (response.success) {
                    updateProgress($progress, 100, '修正完了');
                    displayFixResults(response.data);
                    
                    setTimeout(function() {
                        $progress.fadeOut();
                        $('#gi-complete-section').fadeIn();
                        
                        // 完了セクションまでスクロール
                        $('html, body').animate({
                            scrollTop: $('#gi-complete-section').offset().top - 50
                        }, 500);
                    }, 1000);
                } else {
                    showError('修正に失敗しました: ' + (response.data.message || '不明なエラー'));
                }
            },
            error: function(xhr, status, error) {
                showError('通信エラーが発生しました: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    }
    
    /**
     * 修正結果表示
     */
    function displayFixResults(data) {
        const $results = $('#gi-fix-results');
        
        let html = '<div class="gi-fix-summary">';
        html += '<h3>修正結果</h3>';
        html += '<p>成功: <strong class="gi-success">' + data.success_count + '件</strong></p>';
        if (data.error_count > 0) {
            html += '<p>失敗: <strong class="gi-error">' + data.error_count + '件</strong></p>';
        }
        html += '</div>';
        
        html += '<div class="gi-results-detail">';
        html += '<h4>詳細</h4>';
        html += '<ul>';
        
        $.each(data.results, function(postId, result) {
            if (result.success) {
                html += '<li class="gi-success-item">';
                html += '<span class="dashicons dashicons-yes-alt"></span>';
                html += '<strong>' + escapeHtml(result.title) + '</strong> - ';
                html += Object.keys(result.fixed_fields).length + '個のフィールドを修正';
                html += '</li>';
            } else {
                html += '<li class="gi-error-item">';
                html += '<span class="dashicons dashicons-warning"></span>';
                html += '<strong>' + escapeHtml(result.title) + '</strong> - ' + result.error;
                html += '</li>';
            }
        });
        
        html += '</ul>';
        html += '</div>';
        
        $results.html(html).fadeIn();
    }
    
    /**
     * 全選択処理
     */
    function handleSelectAll() {
        const checked = $(this).prop('checked');
        $('.gi-post-checkbox').prop('checked', checked);
        updateSelectedPostIds();
    }
    
    /**
     * 個別選択処理
     */
    function handlePostSelection() {
        updateSelectedPostIds();
        
        // 全選択チェックボックスの状態更新
        const allChecked = $('.gi-post-checkbox').length === $('.gi-post-checkbox:checked').length;
        $('#gi-select-all, #gi-select-all-header').prop('checked', allChecked);
    }
    
    /**
     * 選択投稿ID更新
     */
    function updateSelectedPostIds() {
        selectedPostIds = [];
        $('.gi-post-checkbox:checked').each(function() {
            selectedPostIds.push(parseInt($(this).val()));
        });
    }
    
    /**
     * プログレスバー更新
     */
    function updateProgress($container, percentage, text) {
        $container.find('.gi-progress-fill').css('width', percentage + '%');
        $container.find('.gi-progress-text').text(text);
    }
    
    /**
     * エラー表示
     */
    function showError(message) {
        const $error = $('<div class="notice notice-error is-dismissible"><p>' + escapeHtml(message) + '</p></div>');
        $('.gi-amount-fixer h1').after($error);
        
        // 自動削除
        setTimeout(function() {
            $error.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    /**
     * フィールドラベル取得
     */
    function getFieldLabel(fieldName) {
        const labels = {
            'grant_amount_max': '助成金額上限',
            'grant_amount_min': '助成金額下限',
            'subsidy_rate_max': '補助率上限',
            'subsidy_rate_min': '補助率下限'
        };
        return labels[fieldName] || fieldName;
    }
    
    /**
     * 数値フォーマット
     */
    function formatNumber(num) {
        if (num === null || num === undefined || num === '') {
            return '-';
        }
        return parseFloat(num).toLocaleString('ja-JP');
    }
    
    /**
     * HTMLエスケープ
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
})(jQuery);
