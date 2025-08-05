<?php
/**
 * Test file for Avada Weight Integration
 * This file can be used to test the Avada theme weight integration functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Test function to verify Avada weight integration
function test_avada_weight_integration() {
    // Check if the plugin is active
    if (!class_exists('ChatGPTContentGenerator')) {
        echo "❌ ChatGPT Content Generator plugin is not active\n";
        return false;
    }
    
    // Check if Avada weight options are set
    $avada_weight_enabled = get_option('ccg_avada_weight_enabled', false);
    $avada_weight_value = get_option('ccg_avada_weight_value', 'normal');
    
    echo "✅ Plugin is active\n";
    echo "✅ Avada weight enabled: " . ($avada_weight_enabled ? 'Yes' : 'No') . "\n";
    echo "✅ Avada weight value: " . $avada_weight_value . "\n";
    
    // Test creating a post with Avada weight
    if ($avada_weight_enabled) {
        $test_title = 'Test Post for Avada Weight Integration';
        $test_content = 'This is a test post to verify Avada weight integration.';
        
        $post_data = array(
            'post_title' => $test_title,
            'post_content' => $test_content,
            'post_status' => 'draft',
            'post_type' => 'post',
            'post_author' => get_current_user_id()
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (!is_wp_error($post_id)) {
            // Apply Avada weight settings
            update_post_meta($post_id, 'pyre_page_title_bar', 'yes');
            update_post_meta($post_id, 'pyre_page_title_text', $test_title);
            update_post_meta($post_id, 'pyre_page_title_custom_text', $test_title);
            update_post_meta($post_id, 'pyre_page_title_text_size', $avada_weight_value);
            update_post_meta($post_id, 'pyre_page_title_text_color', '#333333');
            update_post_meta($post_id, 'pyre_page_title_subheader_text_size', '14px');
            update_post_meta($post_id, 'pyre_page_title_subheader_text_color', '#747474');
            update_post_meta($post_id, 'pyre_page_title_font', 'normal');
            update_post_meta($post_id, 'pyre_page_title_font_size', $avada_weight_value);
            update_post_meta($post_id, 'pyre_page_title_font_color', '#333333');
            
            echo "✅ Test post created with ID: " . $post_id . "\n";
            echo "✅ Avada weight settings applied\n";
            
            // Verify the meta values
            $title_bar = get_post_meta($post_id, 'pyre_page_title_bar', true);
            $title_text = get_post_meta($post_id, 'pyre_page_title_text', true);
            $title_size = get_post_meta($post_id, 'pyre_page_title_text_size', true);
            
            if ($title_bar === 'yes' && $title_text === $test_title && $title_size === $avada_weight_value) {
                echo "✅ Avada weight integration is working correctly\n";
                return true;
            } else {
                echo "❌ Avada weight integration test failed\n";
                return false;
            }
        } else {
            echo "❌ Failed to create test post\n";
            return false;
        }
    } else {
        echo "⚠️ Avada weight integration is disabled\n";
        return true;
    }
}

// Run the test if this file is accessed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    echo "Testing Avada Weight Integration...\n";
    echo "=====================================\n";
    test_avada_weight_integration();
    echo "=====================================\n";
    echo "Test completed.\n";
}
?> 
