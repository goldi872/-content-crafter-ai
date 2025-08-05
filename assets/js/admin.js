jQuery(document).ready(function($) {
    'use strict';
    
    // Global variables
    let generatedContent = '';
    let generatedTitle = '';
    let currentPostId = null;
    
    // Initialize
    init();
    
    function init() {
        bindEvents();
        setupTabs();
        
        // Initialize Avada weight field state
        if ($('#ccg-avada-weight-enabled').length) {
            toggleAvadaWeight();
        }
    }
    
    function bindEvents() {
        // Generate content form
        $('#ccg-generate-form').on('submit', handleGenerateSubmit);
        
        // Clear button
        $('#ccg-clear-btn').on('click', clearForm);
        
        // Tab buttons
        $('.ccg-tab-btn').on('click', switchTab);
        
        // Copy buttons
        $('#ccg-copy-html').on('click', copyToClipboard);
        $('#ccg-copy-shortcode').on('click', copyToClipboard);
        
        // Create post button
        $('#ccg-create-post-btn').on('click', createPost);
        
        // Edit post button
        $('#ccg-edit-post-btn').on('click', editPost);
        
        // Settings form
        $('#ccg-settings-form').on('submit', handleSettingsSubmit);
        
        // Test API button
        $('#ccg-test-api').on('click', testApiConnection);
        
        // Auto-title checkbox
        $('#ccg-auto-title').on('change', toggleTitleGeneration);
        
        // Create post checkbox
        $('#ccg-create-post').on('change', togglePostCreation);
        
        // Avada weight checkbox
        $('#ccg-avada-weight-enabled').on('change', toggleAvadaWeight);
    }
    
    function handleGenerateSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = {
            action: 'ccg_generate_content',
            nonce: ccg_ajax.nonce,
            prompt: formData.get('prompt'),
            category_id: formData.get('category_id'),
            post_type: formData.get('post_type'),
            auto_title: $('#ccg-auto-title').is(':checked'),
            create_post: $('#ccg-create-post').is(':checked')
        };
        
        showLoading();
        
        $.ajax({
            url: ccg_ajax.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    handleSuccess(response.data);
                } else {
                    showError(response.data);
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                showError('Network error: ' + error);
            }
        });
    }
    
    function handleSuccess(data) {
        if (data.post_id) {
            // Post was created
            currentPostId = data.post_id;
            showSuccess(data.message);
            showEditButton(data.edit_url);
            updateRecentPosts();
        } else {
            // Content was generated but not saved
            generatedContent = data.content;
            generatedTitle = data.title;
            displayGeneratedContent();
        }
    }
    
    function displayGeneratedContent() {
        // Show output section
        $('.ccg-output-section').show();
        
        // Update preview
        $('#ccg-preview-content').html(generatedContent);
        
        // Update HTML tab
        $('#ccg-html-content').val(generatedContent);
        
        // Update shortcode tab
        const shortcode = '[chatgpt_output id="' + (currentPostId || 'latest') + '"]';
        $('#ccg-shortcode-content').val(shortcode);
        
        // Show create post button if not already created
        if (!currentPostId) {
            $('#ccg-create-post-btn').show();
            $('#ccg-edit-post-btn').hide();
        }
        
        // Switch to preview tab
        switchTab({ currentTarget: $('.ccg-tab-btn[data-tab="preview"]')[0] });
    }
    
    function createPost() {
        const data = {
            action: 'ccg_generate_content',
            nonce: ccg_ajax.nonce,
            prompt: $('#ccg-prompt').val(),
            category_id: $('#ccg-category').val(),
            post_type: $('#ccg-post-type').val(),
            auto_title: $('#ccg-auto-title').is(':checked'),
            create_post: true
        };
        
        showLoading('#ccg-create-post-btn');
        
        $.ajax({
            url: ccg_ajax.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                hideLoading('#ccg-create-post-btn');
                
                if (response.success) {
                    currentPostId = response.data.post_id;
                    showSuccess(response.data.message);
                    showEditButton(response.data.edit_url);
                    updateRecentPosts();
                    
                    // Update shortcode
                    const shortcode = '[chatgpt_output id="' + currentPostId + '"]';
                    $('#ccg-shortcode-content').val(shortcode);
                    
                    $('#ccg-create-post-btn').hide();
                    $('#ccg-edit-post-btn').show();
                } else {
                    showError(response.data);
                }
            },
            error: function(xhr, status, error) {
                hideLoading('#ccg-create-post-btn');
                showError('Network error: ' + error);
            }
        });
    }
    
    function editPost() {
        if (currentPostId) {
            window.open(ccg_ajax.edit_url.replace('%d', currentPostId), '_blank');
        }
    }
    
    function showEditButton(editUrl) {
        $('#ccg-edit-post-btn').show().off('click').on('click', function() {
            window.open(editUrl, '_blank');
        });
    }
    
    function handleSettingsSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = {
            action: 'ccg_save_settings',
            nonce: ccg_ajax.nonce,
            api_key: formData.get('api_key'),
            model: formData.get('model'),
            max_tokens: formData.get('max_tokens'),
            temperature: formData.get('temperature'),
            default_category: formData.get('default_category'),
            post_status: formData.get('post_status'),
            auto_title: $('#ccg-auto-title').is(':checked'),
            html_cleanup: $('#ccg-html-cleanup').is(':checked'),
            gutenberg_blocks: $('#ccg-gutenberg-blocks').is(':checked'),
            avada_weight_enabled: $('#ccg-avada-weight-enabled').is(':checked'),
            avada_weight_value: $('#ccg-avada-weight-value').val()
        };
        
        showLoading('#ccg-save-settings');
        
        $.ajax({
            url: ccg_ajax.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                hideLoading('#ccg-save-settings');
                
                if (response.success) {
                    showSuccess(response.data);
                } else {
                    showError(response.data);
                }
            },
            error: function(xhr, status, error) {
                hideLoading('#ccg-save-settings');
                showError('Network error: ' + error);
            }
        });
    }
    
    function testApiConnection() {
        const apiKey = $('#ccg-api-key').val();
        
        if (!apiKey) {
            showError('Please enter an API key first.');
            return;
        }
        
        showLoading('#ccg-test-api');
        
        const data = {
            action: 'ccg_generate_content',
            nonce: ccg_ajax.nonce,
            prompt: 'Test connection - please respond with "Connection successful!"',
            create_post: false
        };
        
        $.ajax({
            url: ccg_ajax.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                hideLoading('#ccg-test-api');
                
                if (response.success) {
                    showSuccess('API connection successful!');
                } else {
                    showError('API connection failed: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                hideLoading('#ccg-test-api');
                showError('API connection failed: ' + error);
            }
        });
    }
    
    function setupTabs() {
        $('.ccg-tab-btn').on('click', function() {
            const tab = $(this).data('tab');
            
            // Update active tab button
            $('.ccg-tab-btn').removeClass('active');
            $(this).addClass('active');
            
            // Update active tab content
            $('.ccg-tab-pane').removeClass('active');
            $('#ccg-' + tab + '-tab').addClass('active');
        });
    }
    
    function switchTab(e) {
        const tab = $(e.currentTarget).data('tab');
        
        // Update active tab button
        $('.ccg-tab-btn').removeClass('active');
        $(e.currentTarget).addClass('active');
        
        // Update active tab content
        $('.ccg-tab-pane').removeClass('active');
        $('#ccg-' + tab + '-tab').addClass('active');
    }
    
    function copyToClipboard() {
        const textarea = $(this).prev('textarea');
        textarea.select();
        document.execCommand('copy');
        
        const originalText = $(this).text();
        $(this).text('Copied!');
        
        setTimeout(() => {
            $(this).text(originalText);
        }, 2000);
    }
    
    function clearForm() {
        $('#ccg-generate-form')[0].reset();
        $('.ccg-output-section').hide();
        generatedContent = '';
        generatedTitle = '';
        currentPostId = null;
    }
    
    function toggleTitleGeneration() {
        // Additional logic for title generation toggle
    }
    
    function togglePostCreation() {
        // Additional logic for post creation toggle
    }
    
    function toggleAvadaWeight() {
        const isEnabled = $('#ccg-avada-weight-enabled').is(':checked');
        const weightField = $('#ccg-avada-weight-value').closest('.ccg-form-group');
        
        if (isEnabled) {
            weightField.slideDown(300);
        } else {
            weightField.slideUp(300);
        }
    }
    
    function showLoading(selector = '#ccg-generate-btn') {
        $(selector).prop('disabled', true);
        $(selector + ' .ccg-btn-text').hide();
        $(selector + ' .ccg-spinner').show();
    }
    
    function hideLoading(selector = '#ccg-generate-btn') {
        $(selector).prop('disabled', false);
        $(selector + ' .ccg-btn-text').show();
        $(selector + ' .ccg-spinner').hide();
    }
    
    function showSuccess(message) {
        showNotice(message, 'success');
    }
    
    function showError(message) {
        showNotice(message, 'error');
    }
    
    function showNotice(message, type) {
        const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
        const notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
        
        $('.wrap').first().prepend(notice);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            notice.fadeOut();
        }, 5000);
    }
    
    function updateRecentPosts() {
        // Refresh the recent posts list
        location.reload();
    }
}); 