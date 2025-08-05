<?php
/**
 * Elementor Integration for ChatGPT Content Generator
 */

if (!defined('ABSPATH')) {
    exit;
}

class CCG_Elementor_Integration {
    
    public function __construct() {
        add_action('elementor/init', array($this, 'init_elementor_integration'));
        add_action('ccg_post_created', array($this, 'create_elementor_page'), 10, 2);
        add_filter('ccg_generated_content', array($this, 'process_for_elementor'), 10, 2);
    }
    
    public function init_elementor_integration() {
        // Check if Elementor is active
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        // Add Elementor template creation option
        // No admin menu/submenu here
    }
    
    public function process_for_elementor($content, $title) {
        if (!get_option('ccg_elementor_enabled', false)) {
            return $content;
        }
        
        // Parse content for Elementor widgets
        $elementor_data = $this->parse_content_for_elementor($content, $title);
        
        return $elementor_data;
    }
    
    public function parse_content_for_elementor($content, $title) {
        $elementor_data = array();
        
        // If content is empty, create a simple text section
        if (empty(trim($content))) {
            $section_data = $this->create_elementor_section($content, 0);
            $elementor_data[] = $section_data;
            return $elementor_data;
        }
        
        // Split content into sections
        $sections = $this->split_content_into_sections($content);
        
        // If no sections were created, create one with the entire content
        if (empty($sections)) {
            $sections = array($content);
        }
        
        foreach ($sections as $index => $section) {
            $section_data = $this->create_elementor_section($section, $index);
            $elementor_data[] = $section_data;
        }
        
        return $elementor_data;
    }
    
    private function split_content_into_sections($content) {
        $sections = array();
        
        // Check if content has headings
        if (preg_match('/<h[1-6][^>]*>.*?<\/h[1-6]>/', $content)) {
            // Split by headings
            $parts = preg_split('/(<h[1-6][^>]*>.*?<\/h[1-6]>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
            
            $current_section = '';
            foreach ($parts as $part) {
                if (preg_match('/<h[1-6][^>]*>.*?<\/h[1-6]>/', $part)) {
                    // This is a heading, start new section
                    if (!empty($current_section)) {
                        $sections[] = trim($current_section);
                    }
                    $current_section = $part;
                } else {
                    $current_section .= $part;
                }
            }
            
            if (!empty($current_section)) {
                $sections[] = trim($current_section);
            }
        } else {
            // No headings found, treat entire content as one section
            $sections[] = trim($content);
        }
        
        return $sections;
    }
    
    private function create_elementor_section($section_content, $index) {
        $section_data = array(
            'id' => 'section_' . $index,
            'elType' => 'section',
            'settings' => array(
                'layout' => 'no-gap',
                'gap' => 'no',
                'height' => 'default',
                'custom_height' => array(
                    'unit' => 'px',
                    'size' => '',
                    'sizes' => array()
                ),
                'height_inner' => 'default',
                'custom_height_inner' => array(
                    'unit' => 'px',
                    'size' => '',
                    'sizes' => array()
                ),
                'structure' => '10'
            ),
            'elements' => array()
        );
        
        // Create column
        $column_data = array(
            'id' => 'column_' . $index,
            'elType' => 'column',
            'settings' => array(
                '_column_size' => 100,
                '_inline_size' => null
            ),
            'elements' => array()
        );
        
        // Parse section content for widgets
        $widgets = $this->parse_section_for_widgets($section_content);
        $column_data['elements'] = $widgets;
        
        $section_data['elements'][] = $column_data;
        
        return $section_data;
    }
    
    private function parse_section_for_widgets($section_content) {
        $widgets = array();
        
        // Check if section starts with a heading
        if (preg_match('/<h([1-6])[^>]*>(.*?)<\/h\1>/', $section_content, $matches)) {
            $heading_level = $matches[1];
            $heading_text = strip_tags($matches[2]);
            
            // Create heading widget
            $heading_widget = $this->create_heading_widget($heading_text, $heading_level);
            $widgets[] = $heading_widget;
            
            // Remove heading from content
            $section_content = preg_replace('/<h[1-6][^>]*>.*?<\/h[1-6]>/', '', $section_content, 1);
        }
        
        // Create text widget for remaining content
        if (!empty(trim($section_content))) {
            $text_widget = $this->create_text_widget($section_content);
            $widgets[] = $text_widget;
        }
        
        return $widgets;
    }
    
    private function create_heading_widget($text, $level) {
        $heading_sizes = array(
            '1' => array('size' => 40, 'unit' => 'px'),
            '2' => array('size' => 30, 'unit' => 'px'),
            '3' => array('size' => 25, 'unit' => 'px'),
            '4' => array('size' => 20, 'unit' => 'px'),
            '5' => array('size' => 18, 'unit' => 'px'),
            '6' => array('size' => 16, 'unit' => 'px')
        );
        
        $size = isset($heading_sizes[$level]) ? $heading_sizes[$level] : $heading_sizes['2'];
        
        return array(
            'id' => 'heading_' . uniqid(),
            'elType' => 'widget',
            'widgetType' => 'heading',
            'settings' => array(
                'title' => $text,
                'header_size' => 'h' . $level,
                'typography_typography' => 'custom',
                'typography_font_size' => array(
                    'unit' => $size['unit'],
                    'size' => $size['size'],
                    'sizes' => array()
                ),
                'typography_font_weight' => '600',
                'typography_text_color' => '#23282d',
                '_margin' => array(
                    'unit' => 'px',
                    'top' => '20',
                    'right' => '0',
                    'bottom' => '15',
                    'left' => '0',
                    'isLinked' => false
                )
            )
        );
    }
    
    private function create_text_widget($content) {
        // Clean up the content for better display
        $content = trim($content);
        
        // If content is empty, add a placeholder
        if (empty($content)) {
            $content = '<p>Content will be displayed here.</p>';
        }
        
        return array(
            'id' => 'text_' . uniqid(),
            'elType' => 'widget',
            'widgetType' => 'text-editor',
            'settings' => array(
                'editor' => $content,
                'typography_typography' => 'custom',
                'typography_font_size' => array(
                    'unit' => 'px',
                    'size' => 16,
                    'sizes' => array()
                ),
                'typography_line_height' => array(
                    'unit' => 'em',
                    'size' => 1.6,
                    'sizes' => array()
                ),
                'typography_text_color' => '#666',
                '_margin' => array(
                    'unit' => 'px',
                    'top' => '0',
                    'right' => '0',
                    'bottom' => '20',
                    'left' => '0',
                    'isLinked' => false
                )
            )
        );
    }
    
    public function create_elementor_page($post_id, $content_data) {
        if (!get_option('ccg_elementor_enabled', false)) {
            return;
        }
        
        if (!class_exists('\Elementor\Plugin')) {
            return;
        }
        
        // Get the post
        $post = get_post($post_id);
        if (!$post) {
            return;
        }
        
        // Use content from content_data if available, otherwise use post content
        $content = isset($content_data['content']) ? $content_data['content'] : $post->post_content;
        $title = isset($content_data['title']) ? $content_data['title'] : $post->post_title;
        
        // Parse content for Elementor
        $elementor_data = $this->parse_content_for_elementor($content, $title);
        
        // Create Elementor page
        $this->save_elementor_page($post_id, $elementor_data);
    }
    
    private function save_elementor_page($post_id, $elementor_data) {
        // Get Elementor plugin instance
        $elementor = \Elementor\Plugin::instance();
        
        // Get the post to preserve the title
        $post = get_post($post_id);
        if (!$post) {
            return;
        }
        
        // Create page template
        $page_template = array(
            'version' => '0.4',
            'title' => $post->post_title,
            'type' => 'page',
            'content' => $elementor_data
        );
        
        // Save Elementor data
        update_post_meta($post_id, '_elementor_edit_mode', 'builder');
        update_post_meta($post_id, '_elementor_data', wp_json_encode($elementor_data));
        update_post_meta($post_id, '_elementor_version', '0.4');
        update_post_meta($post_id, '_elementor_css', '');
        
        // Update post content to be empty (Elementor will handle the display)
        // But preserve the title and other post data
        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => $post->post_title, // Preserve the title
            'post_content' => '', // Clear content as Elementor will handle it
            'post_status' => $post->post_status, // Preserve status
            'post_type' => $post->post_type // Preserve post type
        ));
    }
}

// Initialize Elementor integration
new CCG_Elementor_Integration(); 
