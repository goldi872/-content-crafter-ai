jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize frontend functionality
    initFrontend();
    
    function initFrontend() {
        // Handle any frontend-specific functionality
        setupFrontendInteractions();
    }
    
    function setupFrontendInteractions() {
        // Add any frontend-specific event handlers here
        // For example, handling shortcode output styling
        
        $('.ccg-output').each(function() {
            // Add any specific styling or functionality to shortcode outputs
            $(this).addClass('ccg-frontend-output');
        });
    }
    
    // Public function for external use
    window.CCG = {
        // Function to refresh shortcode content
        refreshOutput: function(shortcodeId) {
            // This could be used to refresh shortcode content via AJAX
            console.log('Refreshing output for:', shortcodeId);
        },
        
        // Function to get shortcode content
        getOutput: function(shortcodeId) {
            // This could be used to get shortcode content via AJAX
            console.log('Getting output for:', shortcodeId);
        }
    };
}); 