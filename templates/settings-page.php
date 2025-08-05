<?php
if (!defined('ABSPATH')) {
    exit;
}

$api_key = get_option('ccg_openai_api_key', '');
$model = get_option('ccg_openai_model', 'gpt-3.5-turbo');
$max_tokens = get_option('ccg_max_tokens', 2000);
$temperature = get_option('ccg_temperature', 0.7);
$default_category = get_option('ccg_default_category', '');
$post_status = get_option('ccg_post_status', 'draft');
$auto_title = get_option('ccg_auto_title', true);
$html_cleanup = get_option('ccg_html_cleanup', true);
$gutenberg_blocks = get_option('ccg_gutenberg_blocks', false);
$avada_weight_enabled = get_option('ccg_avada_weight_enabled', false);
$avada_weight_value = get_option('ccg_avada_weight_value', 'normal');
?>

<div class="wrap ccg-settings">
    <h1><?php _e('ChatGPT Content Generator Settings', 'contentcrafter-ai'); ?></h1>
    
    <div class="ccg-settings-container">
        <form id="ccg-settings-form">
            <div class="ccg-settings-section">
                <h2><?php _e('OpenAI API Configuration', 'contentcrafter-ai'); ?></h2>
                
                <div class="ccg-form-group">
                    <label for="ccg-api-key"><?php _e('OpenAI API Key:', 'contentcrafter-ai'); ?></label>
                    <input type="password" id="ccg-api-key" name="api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" required>
                    <p class="description">
                        <?php _e('Get your API key from', 'contentcrafter-ai'); ?> 
                        <a href="https://platform.openai.com/api-keys" target="_blank">https://platform.openai.com/api-keys</a>
                    </p>
                </div>
                
                <div class="ccg-form-row">
                    <div class="ccg-form-group">
                        <label for="ccg-model"><?php _e('Model:', 'contentcrafter-ai'); ?></label>
                        <select id="ccg-model" name="model">
                            <option value="gpt-3.5-turbo" <?php selected($model, 'gpt-3.5-turbo'); ?>>GPT-3.5 Turbo</option>
                            <option value="gpt-4" <?php selected($model, 'gpt-4'); ?>>GPT-4</option>
                            <option value="gpt-4-turbo-preview" <?php selected($model, 'gpt-4-turbo-preview'); ?>>GPT-4 Turbo</option>
                        </select>
                    </div>
                    
                    <div class="ccg-form-group">
                        <label for="ccg-max-tokens"><?php _e('Max Tokens:', 'contentcrafter-ai'); ?></label>
                        <input type="number" id="ccg-max-tokens" name="max_tokens" value="<?php echo esc_attr($max_tokens); ?>" min="100" max="4000" class="small-text">
                        <p class="description"><?php _e('Maximum length of the response (100-4000)', 'contentcrafter-ai'); ?></p>
                    </div>
                    
                    <div class="ccg-form-group">
                        <label for="ccg-temperature"><?php _e('Temperature:', 'contentcrafter-ai'); ?></label>
                        <input type="number" id="ccg-temperature" name="temperature" value="<?php echo esc_attr($temperature); ?>" min="0" max="2" step="0.1" class="small-text">
                        <p class="description"><?php _e('Controls randomness (0-2)', 'contentcrafter-ai'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="ccg-settings-section">
                <h2><?php _e('Post Generation Settings', 'contentcrafter-ai'); ?></h2>
                
                <div class="ccg-form-row">
                    <div class="ccg-form-group">
                        <label for="ccg-default-category"><?php _e('Default Category:', 'contentcrafter-ai'); ?></label>
                        <select id="ccg-default-category" name="default_category">
                            <option value="0"><?php _e('-- Select Default Category --', 'contentcrafter-ai'); ?></option>
                            <?php
                            $categories = get_categories(array('hide_empty' => false));
                            foreach ($categories as $category) {
                                $selected = ($category->term_id == $default_category) ? 'selected' : '';
                                echo '<option value="' . $category->term_id . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="ccg-form-group">
                        <label for="ccg-post-status"><?php _e('Default Post Status:', 'contentcrafter-ai'); ?></label>
                        <select id="ccg-post-status" name="post_status">
                            <option value="draft" <?php selected($post_status, 'draft'); ?>><?php _e('Draft', 'contentcrafter-ai'); ?></option>
                            <option value="publish" <?php selected($post_status, 'publish'); ?>><?php _e('Published', 'contentcrafter-ai'); ?></option>
                            <option value="private" <?php selected($post_status, 'private'); ?>><?php _e('Private', 'contentcrafter-ai'); ?></option>
                            <option value="pending" <?php selected($post_status, 'pending'); ?>><?php _e('Pending Review', 'contentcrafter-ai'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="ccg-form-row">
                    <div class="ccg-form-group">
                        <label>
                            <input type="checkbox" id="ccg-auto-title" name="auto_title" <?php checked($auto_title); ?>>
                            <?php _e('Auto-generate titles for posts', 'contentcrafter-ai'); ?>
                        </label>
                    </div>
                    
                    <div class="ccg-form-group">
                        <label>
                            <input type="checkbox" id="ccg-html-cleanup" name="html_cleanup" <?php checked($html_cleanup); ?>>
                            <?php _e('Clean up HTML formatting', 'contentcrafter-ai'); ?>
                        </label>
                    </div>
                    
                    <div class="ccg-form-group">
                        <label>
                            <input type="checkbox" id="ccg-gutenberg-blocks" name="gutenberg_blocks" <?php checked($gutenberg_blocks); ?>>
                            <?php _e('Enable Gutenberg block support', 'contentcrafter-ai'); ?>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="ccg-settings-section">
                <h2><?php _e('Avada Theme Integration', 'contentcrafter-ai'); ?></h2>
                
                <div class="ccg-form-row">
                    <div class="ccg-form-group">
                        <label>
                            <input type="checkbox" id="ccg-avada-weight-enabled" name="avada_weight_enabled" <?php checked($avada_weight_enabled); ?>>
                            <?php _e('Enable Avada theme weight integration', 'contentcrafter-ai'); ?>
                        </label>
                        <p class="description"><?php _e('Automatically apply Avada theme weight settings to generated posts', 'contentcrafter-ai'); ?></p>
                    </div>
                    
                    <div class="ccg-form-group">
                        <label for="ccg-avada-weight-value"><?php _e('Font Weight:', 'contentcrafter-ai'); ?></label>
                        <select id="ccg-avada-weight-value" name="avada_weight_value">
                            <option value="normal" <?php selected($avada_weight_value, 'normal'); ?>><?php _e('Normal (400)', 'contentcrafter-ai'); ?></option>
                            <option value="bold" <?php selected($avada_weight_value, 'bold'); ?>><?php _e('Bold (700)', 'contentcrafter-ai'); ?></option>
                            <option value="100" <?php selected($avada_weight_value, '100'); ?>><?php _e('Thin (100)', 'contentcrafter-ai'); ?></option>
                            <option value="200" <?php selected($avada_weight_value, '200'); ?>><?php _e('Extra Light (200)', 'contentcrafter-ai'); ?></option>
                            <option value="300" <?php selected($avada_weight_value, '300'); ?>><?php _e('Light (300)', 'contentcrafter-ai'); ?></option>
                            <option value="500" <?php selected($avada_weight_value, '500'); ?>><?php _e('Medium (500)', 'contentcrafter-ai'); ?></option>
                            <option value="600" <?php selected($avada_weight_value, '600'); ?>><?php _e('Semi Bold (600)', 'contentcrafter-ai'); ?></option>
                            <option value="800" <?php selected($avada_weight_value, '800'); ?>><?php _e('Extra Bold (800)', 'contentcrafter-ai'); ?></option>
                            <option value="900" <?php selected($avada_weight_value, '900'); ?>><?php _e('Black (900)', 'contentcrafter-ai'); ?></option>
                        </select>
                        <p class="description"><?php _e('Select the font weight to apply to post titles in Avada theme', 'contentcrafter-ai'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="ccg-settings-section">
                <h2><?php _e('Usage Statistics', 'contentcrafter-ai'); ?></h2>
                
                <div class="ccg-stats-grid">
                    <div class="ccg-stat-item">
                        <h3><?php _e('Total Generated Posts', 'contentcrafter-ai'); ?></h3>
                        <p class="ccg-stat-number">
                            <?php
                            $total_posts = get_posts(array(
                                'meta_key' => '_ccg_generated',
                                'meta_value' => '1',
                                'posts_per_page' => -1,
                                'post_status' => 'any',
                                'fields' => 'ids'
                            ));
                            echo count($total_posts);
                            ?>
                        </p>
                    </div>
                    
                    <div class="ccg-stat-item">
                        <h3><?php _e('This Month', 'contentcrafter-ai'); ?></h3>
                        <p class="ccg-stat-number">
                            <?php
                            $month_posts = get_posts(array(
                                'meta_key' => '_ccg_generated',
                                'meta_value' => '1',
                                'posts_per_page' => -1,
                                'post_status' => 'any',
                                'date_query' => array(
                                    array(
                                        'after' => '1 month ago'
                                    )
                                ),
                                'fields' => 'ids'
                            ));
                            echo count($month_posts);
                            ?>
                        </p>
                    </div>
                    
                    <div class="ccg-stat-item">
                        <h3><?php _e('Published Posts', 'contentcrafter-ai'); ?></h3>
                        <p class="ccg-stat-number">
                            <?php
                            $published_posts = get_posts(array(
                                'meta_key' => '_ccg_generated',
                                'meta_value' => '1',
                                'posts_per_page' => -1,
                                'post_status' => 'publish',
                                'fields' => 'ids'
                            ));
                            echo count($published_posts);
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="ccg-form-actions">
                <button type="submit" class="button button-primary" id="ccg-save-settings">
                    <span class="ccg-btn-text"><?php _e('Save Settings', 'contentcrafter-ai'); ?></span>
                    <span class="ccg-spinner" style="display: none;"></span>
                </button>
                
                <button type="button" class="button" id="ccg-test-api">
                    <?php _e('Test API Connection', 'contentcrafter-ai'); ?>
                </button>
            </div>
        </form>
    </div>
    
    <div class="ccg-settings-sidebar">
        <div class="ccg-sidebar-section">
            <h3><?php _e('API Usage Tips', 'contentcrafter-ai'); ?></h3>
            <ul>
                <li><?php _e('GPT-4 is more capable but costs more', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Lower temperature = more focused content', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Higher max tokens = longer content', 'contentcrafter-ai'); ?></li>
                <li><?php _e('Monitor your API usage at OpenAI dashboard', 'contentcrafter-ai'); ?></li>
            </ul>
        </div>
        
        <div class="ccg-sidebar-section">
            <h3><?php _e('Security Note', 'contentcrafter-ai'); ?></h3>
            <p><?php _e('Your API key is stored securely in the WordPress database. Never share it publicly.', 'contentcrafter-ai'); ?></p>
        </div>
        
        <div class="ccg-sidebar-section">
            <h3><?php _e('Support', 'contentcrafter-ai'); ?></h3>
            <p><?php _e('Need help? Check the documentation or contact support.', 'contentcrafter-ai'); ?></p>
        </div>
    </div>
</div> 
