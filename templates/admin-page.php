<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap ccg-admin">
    <h1><?php _e('ContentCrafter AI', 'contentcrafter-ai'); ?></h1>
    
    <div class="ccg-container">
        <div class="ccg-main-content">
            <div class="ccg-form-section">
                <h2><?php _e('Generate Content', 'contentcrafter-ai'); ?></h2>
                
                <form id="ccg-generate-form">
                    <div class="ccg-form-group">
                        <label for="ccg-prompt"><?php _e('Prompt:', 'contentcrafter-ai'); ?></label>
                        <textarea id="ccg-prompt" name="prompt" rows="6" placeholder="<?php _e('Enter your prompt here...', 'contentcrafter-ai'); ?>" required></textarea>
                    </div>
                    
                    <div class="ccg-form-row">
                        <div class="ccg-form-group">
                            <label for="ccg-category"><?php _e('Category:', 'contentcrafter-ai'); ?></label>
                            <select id="ccg-category" name="category_id">
                                <option value="0"><?php _e('-- Select Category --', 'contentcrafter-ai'); ?></option>
                                <?php
                                $categories = get_categories(array('hide_empty' => false));
                                foreach ($categories as $category) {
                                    $selected = ($category->term_id == get_option('ccg_default_category')) ? 'selected' : '';
                                    echo '<option value="' . $category->term_id . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="ccg-form-group">
                            <label for="ccg-post-type"><?php _e('Post Type:', 'contentcrafter-ai'); ?></label>
                            <select id="ccg-post-type" name="post_type">
                                <option value="post"><?php _e('Post', 'contentcrafter-ai'); ?></option>
                                <option value="page"><?php _e('Page', 'contentcrafter-ai'); ?></option>
                                <?php
                                $custom_post_types = get_post_types(array('_builtin' => false), 'objects');
                                foreach ($custom_post_types as $post_type) {
                                    echo '<option value="' . $post_type->name . '">' . esc_html($post_type->labels->singular_name) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="ccg-form-row">
                        <div class="ccg-form-group">
                            <label>
                                <input type="checkbox" id="ccg-auto-title" name="auto_title" checked>
                                <?php _e('Auto-generate title', 'contentcrafter-ai'); ?>
                            </label>
                        </div>
                        
                        <div class="ccg-form-group">
                            <label>
                                <input type="checkbox" id="ccg-create-post" name="create_post" checked>
                                <?php _e('Create WordPress post', 'contentcrafter-ai'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="ccg-form-actions">
                        <button type="submit" class="button button-primary" id="ccg-generate-btn">
                            <span class="ccg-btn-text"><?php _e('Generate Content', 'contentcrafter-ai'); ?></span>
                            <span class="ccg-spinner" style="display: none;"></span>
                        </button>
                        
                        <button type="button" class="button" id="ccg-clear-btn">
                            <?php _e('Clear', 'contentcrafter-ai'); ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="ccg-output-section" style="display: none;">
                <h2><?php _e('Generated Content', 'contentcrafter-ai'); ?></h2>
                
                <div class="ccg-output-tabs">
                    <button class="ccg-tab-btn active" data-tab="preview"><?php _e('Preview', 'contentcrafter-ai'); ?></button>
                    <button class="ccg-tab-btn" data-tab="html"><?php _e('HTML', 'contentcrafter-ai'); ?></button>
                    <button class="ccg-tab-btn" data-tab="shortcode"><?php _e('Shortcode', 'contentcrafter-ai'); ?></button>
                </div>
                
                <div class="ccg-tab-content">
                    <div id="ccg-preview-tab" class="ccg-tab-pane active">
                        <div id="ccg-preview-content"></div>
                    </div>
                    
                    <div id="ccg-html-tab" class="ccg-tab-pane">
                        <textarea id="ccg-html-content" rows="10" readonly></textarea>
                        <button type="button" class="button" id="ccg-copy-html"><?php _e('Copy HTML', 'contentcrafter-ai'); ?></button>
                    </div>
                    
                    <div id="ccg-shortcode-tab" class="ccg-tab-pane">
                        <textarea id="ccg-shortcode-content" rows="3" readonly></textarea>
                        <button type="button" class="button" id="ccg-copy-shortcode"><?php _e('Copy Shortcode', 'contentcrafter-ai'); ?></button>
                    </div>
                </div>
                
                <div class="ccg-output-actions">
                    <button type="button" class="button button-primary" id="ccg-create-post-btn" style="display: none;">
                        <?php _e('Create Post', 'contentcrafter-ai'); ?>
                    </button>
                    <button type="button" class="button" id="ccg-edit-post-btn" style="display: none;">
                        <?php _e('Edit Post', 'contentcrafter-ai'); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="ccg-sidebar">
            <div class="ccg-sidebar-section">
                <h3><?php _e('Quick Tips', 'contentcrafter-ai'); ?></h3>
                <ul>
                    <li><?php _e('Be specific in your prompts for better results', 'contentcrafter-ai'); ?></li>
                    <li><?php _e('Include target audience and tone in your prompt', 'contentcrafter-ai'); ?></li>
                    <li><?php _e('Use keywords for SEO-optimized content', 'contentcrafter-ai'); ?></li>
                    <li><?php _e('Check the preview before creating a post', 'contentcrafter-ai'); ?></li>
                </ul>
            </div>
            
            <div class="ccg-sidebar-section">
                <h3><?php _e('Recent Generations', 'contentcrafter-ai'); ?></h3>
                <div id="ccg-recent-posts">
                    <?php
                    $recent_posts = get_posts(array(
                        'meta_key' => '_ccg_generated',
                        'meta_value' => '1',
                        'posts_per_page' => 5,
                        'post_status' => 'any'
                    ));
                    
                    if ($recent_posts) {
                        echo '<ul>';
                        foreach ($recent_posts as $post) {
                            echo '<li><a href="' . get_edit_post_link($post->ID) . '">' . esc_html($post->post_title) . '</a></li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>' . __('No generated posts yet.', 'contentcrafter-ai') . '</p>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="ccg-sidebar-section">
                <h3><?php _e('Settings', 'contentcrafter-ai'); ?></h3>
                <p><a href="<?php echo admin_url('admin.php?page=contentcrafter-ai-settings'); ?>" class="button"><?php _e('Configure API Settings', 'contentcrafter-ai'); ?></a></p>
            </div>
        </div>
    </div>
</div> 
