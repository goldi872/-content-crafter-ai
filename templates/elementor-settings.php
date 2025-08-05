<?php
if (!defined('ABSPATH')) {
    exit;
}

$elementor_enabled = get_option('ccg_elementor_enabled', false);
$auto_create_pages = get_option('ccg_auto_create_pages', false);
$default_template = get_option('ccg_default_template', '');
$elementor_active = class_exists('Elementor\\Plugin');
?>

<div class="wrap ccg-elementor-settings">
    <h1><?php _e('Elementor Integration Settings', 'contentcrafter-ai'); ?></h1>
    
    <div class="ccg-settings-container">
        <div class="ccg-settings-section">
            <h2><?php _e('Elementor Integration', 'contentcrafter-ai'); ?></h2>
            
            <?php if (!$elementor_active): ?>
                <div class="notice notice-warning">
                    <p><?php _e('Elementor plugin is not installed or activated. Please install and activate Elementor to use this integration. The options below are disabled.', 'contentcrafter-ai'); ?></p>
                </div>
            <?php else: ?>
                <div class="notice notice-success">
                    <p><?php _e('Elementor is active and ready for integration!', 'contentcrafter-ai'); ?></p>
                </div>
            <?php endif; ?>
            
            <form id="ccg-elementor-settings-form">
                <div class="ccg-form-group">
                    <label>
                        <input type="checkbox" id="ccg-elementor-enabled" name="elementor_enabled" <?php checked($elementor_enabled); ?> <?php disabled(!$elementor_active); ?>>
                        <?php _e('Enable Elementor Integration', 'contentcrafter-ai'); ?>
                    </label>
                    <p class="description"><?php _e('When enabled, generated content will be automatically converted to Elementor pages with proper widgets.', 'contentcrafter-ai'); ?></p>
                </div>
                
                <div class="ccg-form-group">
                    <label>
                        <input type="checkbox" id="ccg-auto-create-pages" name="auto_create_pages" <?php checked($auto_create_pages); ?> <?php disabled(!$elementor_active); ?>>
                        <?php _e('Auto-create Elementor Pages', 'contentcrafter-ai'); ?>
                    </label>
                    <p class="description"><?php _e('Automatically create new pages with Elementor builder when generating content.', 'contentcrafter-ai'); ?></p>
                </div>
                
                <div class="ccg-form-group">
                    <label for="ccg-default-template"><?php _e('Default Page Template:', 'contentcrafter-ai'); ?></label>
                    <select id="ccg-default-template" name="default_template" <?php disabled(!$elementor_active); ?>>
                        <option value=""><?php _e('-- Select Template --', 'contentcrafter-ai'); ?></option>
                        <?php
                        $templates = get_page_templates();
                        foreach ($templates as $template_name => $template_filename) {
                            $selected = ($template_filename == $default_template) ? 'selected' : '';
                            echo '<option value="' . esc_attr($template_filename) . '" ' . $selected . '>' . esc_html($template_name) . '</option>';
                        }
                        ?>
                    </select>
                    <p class="description"><?php _e('Choose a default page template for generated Elementor pages.', 'contentcrafter-ai'); ?></p>
                </div>
                
                <div class="ccg-form-actions">
                    <button type="submit" class="button button-primary" id="ccg-save-elementor-settings" <?php disabled(!$elementor_active); ?>>
                        <span class="ccg-btn-text"><?php _e('Save Settings', 'contentcrafter-ai'); ?></span>
                        <span class="ccg-spinner" style="display: none;"></span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="ccg-settings-section">
            <h2><?php _e('Widget Configuration', 'contentcrafter-ai'); ?></h2>
            
            <div class="ccg-widget-settings">
                <h3><?php _e('Heading Widgets', 'contentcrafter-ai'); ?></h3>
                <p><?php _e('Content headings will be automatically converted to Elementor Heading widgets with appropriate styling:', 'contentcrafter-ai'); ?></p>
                <ul>
                    <li><strong>H1:</strong> 40px, Bold, Dark Gray</li>
                    <li><strong>H2:</strong> 30px, Bold, Dark Gray</li>
                    <li><strong>H3:</strong> 25px, Bold, Dark Gray</li>
                    <li><strong>H4:</strong> 20px, Bold, Dark Gray</li>
                    <li><strong>H5:</strong> 18px, Bold, Dark Gray</li>
                    <li><strong>H6:</strong> 16px, Bold, Dark Gray</li>
                </ul>
                
                <h3><?php _e('Text Widgets', 'contentcrafter-ai'); ?></h3>
                <p><?php _e('Content paragraphs will be converted to Elementor Text Editor widgets with:', 'contentcrafter-ai'); ?></p>
                <ul>
                    <li>16px font size</li>
                    <li>1.6 line height</li>
                    <li>Gray text color (#666)</li>
                    <li>Proper spacing and margins</li>
                </ul>
            </div>
        </div>
        
        <div class="ccg-settings-section">
            <h2><?php _e('How It Works', 'contentcrafter-ai'); ?></h2>
            
            <div class="ccg-how-it-works">
                <ol>
                    <li><?php _e('When you generate content with ChatGPT, the plugin analyzes the structure.', 'contentcrafter-ai'); ?></li>
                    <li><?php _e('Headings are converted to Elementor Heading widgets with appropriate styling.', 'contentcrafter-ai'); ?></li>
                    <li><?php _e('Content paragraphs are converted to Text Editor widgets.', 'contentcrafter-ai'); ?></li>
                    <li><?php _e('Each section becomes an Elementor section with proper layout.', 'contentcrafter-ai'); ?></li>
                    <li><?php _e('The page is saved as an Elementor page that you can further customize.', 'contentcrafter-ai'); ?></li>
                </ol>
            </div>
        </div>
    </div>
    
    <div class="ccg-settings-sidebar">
        <div class="ccg-sidebar-section">
            <h3><?php _e('Benefits', 'contentcrafter-ai'); ?></h3>
            <ul>
                <li><?php _e('Professional page layouts automatically', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Consistent styling across all generated pages', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Easy customization with Elementor editor', 'contentcrafter-ai'); ?></li>
                <li><?php _e('SEO-friendly structure', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Mobile-responsive design', 'contentcrafter-ai'); ?></li>
            </ul>
        </div>
        
        <div class="ccg-sidebar-section">
            <h3><?php _e('Tips', 'contentcrafter-ai'); ?></h3>
            <ul>
                <li><?php _e('Use clear headings in your prompts for better structure', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Review and customize generated pages in Elementor', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Add images and other widgets as needed', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Save as templates for future use', 'contentcrafter-ai'); ?></li>
            </ul>
        </div>
        
        <div class="ccg-sidebar-section">
            <h3><?php _e('Support', 'contentcrafter-ai'); ?></h3>
            <p><?php _e('Need help with Elementor integration? Check the documentation or contact support.', 'contentcrafter-ai'); ?></p>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#ccg-elementor-settings-form').on('submit', function(e) {
        e.preventDefault();
        // Prevent saving if Elementor is not active
        if (!<?php echo json_encode($elementor_active); ?>) {
            alert('Elementor is not active. Please activate Elementor to save these settings.');
            return false;
        }
        const data = {
            action: 'ccg_save_elementor_settings',
            nonce: ccg_ajax.nonce,
            elementor_enabled: $('#ccg-elementor-enabled').is(':checked'),
            auto_create_pages: $('#ccg-auto-create-pages').is(':checked'),
            default_template: $('#ccg-default-template').val()
        };
        $('#ccg-save-elementor-settings .ccg-btn-text').hide();
        $('#ccg-save-elementor-settings .ccg-spinner').show();
        $.ajax({
            url: ccg_ajax.ajax_url,
            type: 'POST',
            data: data,
            success: function(response) {
                $('#ccg-save-elementor-settings .ccg-btn-text').show();
                $('#ccg-save-elementor-settings .ccg-spinner').hide();
                if (response.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Error saving settings: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                $('#ccg-save-elementor-settings .ccg-btn-text').show();
                $('#ccg-save-elementor-settings .ccg-spinner').hide();
                alert('Error saving settings: ' + error);
            }
        });
    });
});
</script> 
