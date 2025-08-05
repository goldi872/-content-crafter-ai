<?php
/**
 * Plugin Name: ContentCrafter AI
 * Plugin URI: https://wordpress.org/plugins/contentcrafter-ai
 * Description: Generate WordPress posts using OpenAI's ContentCrafter API with advanced features including category selection, title generation, and Gutenberg block integration.
 * Version: 1.0.0
 * Author: Goldie
 * Author URI: https://github.com/goldie
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: contentcrafter-ai
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CCG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CCG_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CCG_PLUGIN_VERSION', '1.0.0');

// Main plugin class
class ContentCrafterContentGenerator {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('wp_ajax_ccg_generate_content', array($this, 'ajax_generate_content'));
        add_action('wp_ajax_nopriv_ccg_generate_content', array($this, 'ajax_generate_content'));
        add_action('wp_ajax_ccg_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_ccg_save_elementor_settings', array($this, 'ajax_save_elementor_settings'));
        add_shortcode('contentcrafter_output', array($this, 'shortcode_output'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        

        
        // Include Elementor integration
        if (file_exists(CCG_PLUGIN_PATH . 'includes/elementor-integration.php')) {
            require_once CCG_PLUGIN_PATH . 'includes/elementor-integration.php';
        }
        
        // Include test file for development (remove for production)
        // if (file_exists(CCG_PLUGIN_PATH . 'test-avada-integration.php')) {
        //     require_once CCG_PLUGIN_PATH . 'test-avada-integration.php';
        // }
    }
    
    public function init() {
        // WordPress automatically loads translations for plugins hosted on WordPress.org
    }
    
    public function activate() {
        // Set default options
        add_option('ccg_openai_api_key', '');
        add_option('ccg_openai_model', 'gpt-3.5-turbo');
        add_option('ccg_max_tokens', 2000);
        add_option('ccg_temperature', 0.7);
        add_option('ccg_default_category', '');
        add_option('ccg_post_status', 'draft');
        add_option('ccg_auto_title', true);
        add_option('ccg_html_cleanup', true);
        add_option('ccg_gutenberg_blocks', false);
        add_option('ccg_elementor_enabled', false);
        add_option('ccg_auto_create_pages', false);
        add_option('ccg_default_template', '');
        add_option('ccg_avada_weight_enabled', false);
        add_option('ccg_avada_weight_value', 'normal');
    }
    
    public function deactivate() {
        // Cleanup if needed
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('ContentCrafter AI', 'contentcrafter-ai'),
            __('ContentCrafter', 'contentcrafter-ai'),
            'manage_options',
            'contentcrafter-ai',
            array($this, 'admin_page'),
            'dashicons-edit',
            30
        );
        
        add_submenu_page(
            'contentcrafter-ai',
            __('Settings', 'contentcrafter-ai'),
            __('Settings', 'contentcrafter-ai'),
            'manage_options',
            'contentcrafter-ai-settings',
            array($this, 'settings_page')
        );
        // Always show Elementor submenu, even if Elementor is not active
        add_submenu_page(
            'contentcrafter-ai',
            __('Elementor Integration', 'contentcrafter-ai'),
            __('Elementor', 'contentcrafter-ai'),
            'manage_options',
            'contentcrafter-elementor',
            array($this, 'elementor_settings_page')
        );
    }

    // public function add_elementor_menu() {
    //     // This function is now handled by add_admin_menu, so we can remove it or comment it out
    //     // if (get_option('ccg_elementor_enabled', false) && class_exists('Elementor\Plugin')) {
    //     //     add_submenu_page(
    //     //         'contentcrafter-ai',
    //     //         __('Elementor Integration', 'contentcrafter-ai'),
    //     //         __('Elementor', 'contentcrafter-ai'),
    //     //         'manage_options',
    //     //         'chatgpt-elementor',
    //     //         array($this, 'elementor_settings_page')
    //     //     );
    //     // }
    // }
    
    public function enqueue_admin_scripts($hook) {
        // Only load on any page that starts with 'contentcrafter-ai' or 'contentcrafter-elementor'
        if (
            strpos($hook, 'contentcrafter-ai') === false &&
            strpos($hook, 'contentcrafter-elementor') === false
        ) {
            return;
        }
        
        wp_enqueue_script('ccg-admin', CCG_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), CCG_PLUGIN_VERSION, true);
        wp_enqueue_style('ccg-admin', CCG_PLUGIN_URL . 'assets/css/admin.css', array(), CCG_PLUGIN_VERSION);
        
        wp_localize_script('ccg-admin', 'ccg_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ccg_nonce'),
            'strings' => array(
                'generating' => __('Generating content...', 'contentcrafter-ai'),
                'error' => __('Error occurred', 'contentcrafter-ai'),
                'success' => __('Content generated successfully!', 'contentcrafter-ai')
            )
        ));
    }
    
    public function enqueue_frontend_scripts() {
        wp_enqueue_script('ccg-frontend', CCG_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), CCG_PLUGIN_VERSION, true);
        wp_enqueue_style('ccg-frontend', CCG_PLUGIN_URL . 'assets/css/frontend.css', array(), CCG_PLUGIN_VERSION);
    }
    
    public function admin_page() {
        include CCG_PLUGIN_PATH . 'templates/admin-page.php';
    }
    
    public function settings_page() {
        include CCG_PLUGIN_PATH . 'templates/settings-page.php';
    }

    // Elementor settings page handler
    public function elementor_settings_page() {
        include CCG_PLUGIN_PATH . 'templates/elementor-settings.php';
    }
    
    public function ajax_generate_content() {
        check_ajax_referer('ccg_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'contentcrafter-ai'));
        }
        
        $prompt = sanitize_textarea_field($_POST['prompt']);
        $category_id = intval($_POST['category_id']);
        $auto_title = isset($_POST['auto_title']) ? true : false;
        $create_post = isset($_POST['create_post']) ? true : false;
        $post_type = sanitize_text_field($_POST['post_type']);
        
        if (empty($prompt)) {
            wp_send_json_error(__('Please enter a prompt', 'contentcrafter-ai'));
        }
        
        $api_key = get_option('ccg_openai_api_key');
        if (empty($api_key)) {
            wp_send_json_error(__('OpenAI API key not configured', 'contentcrafter-ai'));
        }
        
        $response = $this->call_openai_api($prompt, $auto_title);
        // print_r($response);
        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        }
        
        $content = $response['content'];
        $title = $response['title'];
        
        // Debug output
        // echo "=== DEBUG INFO ===\n";
        // echo "Extracted Title: " . $title . "\n";
        // echo "Content Length: " . strlen($content) . "\n";
        // echo "==================\n";
        // Clean up HTML if enabled
        if (get_option('ccg_html_cleanup', true)) {
            $content = $this->cleanup_html($content);
        }
        
        if ($create_post) {
            $post_id = $this->create_post($title, $content, $category_id, $post_type);
            if (is_wp_error($post_id)) {
                wp_send_json_error($post_id->get_error_message());
            }
            wp_send_json_success(array(
                'message' => __('Post created successfully!', 'contentcrafter-ai'),
                'post_id' => $post_id,
                'edit_url' => get_edit_post_link($post_id, 'url')
            ));
        } else {
            wp_send_json_success(array(
                'content' => $content,
                'title' => $title
            ));
        }
    }
    
    public function ajax_save_settings() {
        check_ajax_referer('ccg_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'contentcrafter-ai'));
        }
        
        $api_key = sanitize_text_field($_POST['api_key']);
        $model = sanitize_text_field($_POST['model']);
        $max_tokens = intval($_POST['max_tokens']);
        $temperature = floatval($_POST['temperature']);
        $default_category = intval($_POST['default_category']);
        $post_status = sanitize_text_field($_POST['post_status']);
        $auto_title = isset($_POST['auto_title']) ? true : false;
        $html_cleanup = isset($_POST['html_cleanup']) ? true : false;
        $gutenberg_blocks = isset($_POST['gutenberg_blocks']) ? true : false;
        $avada_weight_enabled = isset($_POST['avada_weight_enabled']) ? true : false;
        $avada_weight_value = sanitize_text_field($_POST['avada_weight_value']);
        
        update_option('ccg_openai_api_key', $api_key);
        update_option('ccg_openai_model', $model);
        update_option('ccg_max_tokens', $max_tokens);
        update_option('ccg_temperature', $temperature);
        update_option('ccg_default_category', $default_category);
        update_option('ccg_post_status', $post_status);
        update_option('ccg_auto_title', $auto_title);
        update_option('ccg_html_cleanup', $html_cleanup);
        update_option('ccg_gutenberg_blocks', $gutenberg_blocks);
        update_option('ccg_avada_weight_enabled', $avada_weight_enabled);
        update_option('ccg_avada_weight_value', $avada_weight_value);
        
        wp_send_json_success(__('Settings saved successfully!', 'contentcrafter-ai'));
    }
    
    public function ajax_save_elementor_settings() {
        check_ajax_referer('ccg_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'contentcrafter-ai'));
        }
        
        $elementor_enabled = isset($_POST['elementor_enabled']) ? true : false;
        $auto_create_pages = isset($_POST['auto_create_pages']) ? true : false;
        $default_template = sanitize_text_field($_POST['default_template']);
        
        update_option('ccg_elementor_enabled', $elementor_enabled);
        update_option('ccg_auto_create_pages', $auto_create_pages);
        update_option('ccg_default_template', $default_template);
        
        wp_send_json_success(__('Elementor settings saved successfully!', 'contentcrafter-ai'));
    }
    
    private function call_openai_api($prompt, $auto_title = false) {
        $api_key = get_option('ccg_openai_api_key');
        $model = get_option('ccg_openai_model', 'gpt-3.5-turbo');
        $max_tokens = intval(get_option('ccg_max_tokens', 2000));
        $temperature = floatval(get_option('ccg_temperature', 0.7));
        
        $system_prompt = "You are a professional content writer. Generate high-quality, engaging content that is well-structured and SEO-friendly. ";
        
        if ($auto_title) {
            $system_prompt .= "Also generate a compelling title for the content. IMPORTANT: Format your response exactly as follows:\n\nTITLE: [Your compelling title here]\n\nCONTENT: [Your content here]\n\nDo not include HTML tags, markdown formatting, or special characters in the title. The title should be clean plain text only.";
        }
        
        $messages = array(
            array(
                'role' => 'system',
                'content' => $system_prompt
            ),
            array(
                'role' => 'user',
                'content' => $prompt
            )
        );
        
        $body = array(
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $max_tokens,
            'temperature' => $temperature
        );
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($body),
            'timeout' => 60
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['error'])) {
            return new WP_Error('openai_error', $data['error']['message']);
        }
        
        // Validate response structure
        if (!isset($data['choices']) || !is_array($data['choices']) || empty($data['choices'])) {
            return new WP_Error('openai_error', 'Invalid response structure from OpenAI API');
        }
        
        if (!isset($data['choices'][0]['message']['content'])) {
            return new WP_Error('openai_error', 'No content received from OpenAI API');
        }
        
        $content = $data['choices'][0]['message']['content'];
        
        if ($auto_title) {
            // First try to parse the expected format: TITLE: [title]\n\nCONTENT: [content]
            if (preg_match('/TITLE:\s*(.+?)\n\nCONTENT:\s*(.+)/s', $content, $matches)) {
                $title = trim($matches[1]);
                $content = trim($matches[2]);
            } 
            // If that fails, try to extract title from HTML <title> tag
            elseif (preg_match('/<title>(.*?)<\/title>/i', $content, $matches)) {
                $title = trim($matches[1]);
                // Remove the title tag from content
                $content = preg_replace('/<title>.*?<\/title>/i', '', $content);
            }
            // If that fails, try to extract title from first <h1> tag
            elseif (preg_match('/<h1[^>]*>(.*?)<\/h1>/i', $content, $matches)) {
                $title = trim(strip_tags($matches[1]));
            }
            // If that fails, try to extract from the first line if it looks like a title
            elseif (preg_match('/^(.+?)(?:\n|$)/', $content, $matches)) {
                $potential_title = trim($matches[1]);
                // Check if this looks like a title (not too long, doesn't contain HTML)
                if (strlen($potential_title) < 100 && !preg_match('/<[^>]+>/', $potential_title)) {
                    $title = $potential_title;
                    // Remove the title from content
                    $content = preg_replace('/^' . preg_quote($potential_title, '/') . '\n?/', '', $content);
                } else {
                    $title = $this->generate_title_from_content($content);
                }
            }
            // If all else fails, generate title from content
            else {
                $title = $this->generate_title_from_content($content);
            }
            
            // Clean up the title - remove markdown, special characters, and extra formatting
            $title = $this->clean_title($title);
        } else {
            $title = '';
        }
        
        return array(
            'content' => $content,
            'title' => $title
        );
    }
    
    private function generate_title_from_content($content) {
        // Simple title generation from first sentence
        $sentences = preg_split('/[.!?]+/', $content, 2);
        $title = trim($sentences[0]);
        
        // Limit title length
        if (strlen($title) > 60) {
            $title = substr($title, 0, 57) . '...';
        }
        
        return $title;
    }
    
    private function cleanup_html($content) {
        // Remove markdown bold/italic special characters
        $content = preg_replace('/\\*\\*|\\*|__|_/', '', $content);

        // Convert markdown-style headings to HTML headings
        $content = $this->convert_headings($content);

        // Add paragraph tags for double line breaks
        $content = preg_replace("/\\n{2,}/", "</p><p>", $content);

        // Add <br> for single line breaks
        $content = preg_replace("/\\n/", "<br>", $content);

        // Wrap in <p> if not already
        if (strpos($content, '<p>') !== 0) {
            $content = '<p>' . $content . '</p>';
        }

        // Remove empty paragraphs
        $content = preg_replace('/<p>\\s*<\\/p>/', '', $content);

        return trim($content);
    }
    
    private function convert_headings($content) {
        // Convert markdown-style headings to HTML
        $content = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $content);
        $content = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $content);
        $content = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $content);

        // Convert numbered headings (1. 2. 3. etc.)
        $content = preg_replace('/^(\\d+)\\.\\s+(.*?)$/m', '<h2>$2</h2>', $content);

        // Convert lettered headings (A. B. C. etc.)
        $content = preg_replace('/^([A-Z])\\.\\s+(.*?)$/m', '<h3>$2</h3>', $content);

        return $content;
    }
    
    private function create_post($title, $content, $category_id = 0, $post_type = 'post') {
        $post_status = get_option('ccg_post_status', 'draft');
        $elementor_enabled = get_option('ccg_elementor_enabled', false) && class_exists('Elementor\Plugin');

        // If Elementor is enabled, we'll create the post with minimal content first
        // and then let Elementor handle the content conversion
        $initial_content = $elementor_enabled ? '' : $content;

        $post_data = array(
            'post_title'   => $title,
            'post_content' => $initial_content,
            'post_status'  => $post_status,
            'post_type'    => $post_type,
            'post_author'  => get_current_user_id()
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        // Set category
        if ($category_id > 0) {
            wp_set_post_categories($post_id, array($category_id));
        }

        // Add custom meta
        update_post_meta($post_id, '_ccg_generated', true);
        update_post_meta($post_id, '_ccg_generated_date', current_time('mysql'));

        // Apply Avada weight if enabled
        $avada_weight_enabled = get_option('ccg_avada_weight_enabled', false);
        if ($avada_weight_enabled) {
            $avada_weight_value = get_option('ccg_avada_weight_value', 'normal');
            update_post_meta($post_id, 'pyre_page_title_bar', 'yes');
            update_post_meta($post_id, 'pyre_page_title_text', $title);
            update_post_meta($post_id, 'pyre_page_title_custom_text', $title);
            update_post_meta($post_id, 'pyre_page_title_text_size', $avada_weight_value);
            update_post_meta($post_id, 'pyre_page_title_text_color', '#333333');
            update_post_meta($post_id, 'pyre_page_title_subheader_text_size', '14px');
            update_post_meta($post_id, 'pyre_page_title_subheader_text_color', '#747474');
            update_post_meta($post_id, 'pyre_page_title_font', 'normal');
            update_post_meta($post_id, 'pyre_page_title_font_size', $avada_weight_value);
            update_post_meta($post_id, 'pyre_page_title_font_color', '#333333');
        }

        // Only trigger Elementor integration if enabled and Elementor is active
        if ($elementor_enabled) {
            // Trigger Elementor processing immediately
            do_action('ccg_post_created', $post_id, array(
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id,
                'post_type' => $post_type
            ));
        }

        return $post_id;
    }
    
    private function clean_title($title) {
        // Remove HTML tags
        $title = strip_tags($title);
        
        // Remove markdown formatting (**bold**, *italic*, etc.)
        $title = preg_replace('/\*\*(.*?)\*\*/', '$1', $title); // Remove **bold**
        $title = preg_replace('/\*(.*?)\*/', '$1', $title); // Remove *italic*
        $title = preg_replace('/__(.*?)__/', '$1', $title); // Remove __bold__
        $title = preg_replace('/_(.*?)_/', '$1', $title); // Remove _italic_
        
        // Remove "TITLE:" prefix if present
        $title = preg_replace('/^TITLE:\s*/i', '', $title);
        
        // Remove emojis and special characters
        $title = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]+/u', '', $title);
        
        // Remove extra special characters but keep basic punctuation
        $title = preg_replace('/[^\w\s\-\.\,\!\?\(\)\[\]]+/u', '', $title);
        
        // Clean up extra whitespace
        $title = preg_replace('/\s+/', ' ', $title);
        
        // Trim and limit length
        $title = trim($title);
        if (strlen($title) > 60) {
            $title = substr($title, 0, 57) . '...';
        }
        
        return $title;
    }
    
    private function sanitize_simple_title($title) {
        // Remove emojis, SVGs, and special characters, keep only letters, numbers, spaces, and basic punctuation
        $title = strip_tags($title); // Remove HTML
        $title = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]+/u', '', $title); // Remove emojis
        $title = preg_replace('/[^\w\s\-\.\,\!\?\(\)\[\]]+/u', '', $title); // Remove special chars except basic punctuation
        $title = trim($title);
        return $title;
    }
    

    
    public function shortcode_output($atts) {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts);
        
        if (empty($atts['id'])) {
            return '';
        }
        
        $post = get_post($atts['id']);
        if (!$post) {
            return '';
        }
        
        return '<div class="ccg-output">' . apply_filters('the_content', $post->post_content) . '</div>';
    }
}

// Initialize the plugin
new ContentCrafterContentGenerator(); 
