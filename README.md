# ContentCrafter AI for WordPress

A comprehensive WordPress plugin that integrates with OpenAI's ContentCrafter API to generate high-quality content for your WordPress site.

## Features

### 🚀 Core Features
- **AI-Powered Content Generation**: Generate blog posts, pages, and custom post types using ChatGPT
- **Smart Title Generation**: Automatically generate compelling titles for your content
- **Category Selection**: Choose categories for generated posts
- **Multiple Post Types**: Support for posts, pages, and custom post types
- **HTML Cleanup**: Automatic formatting and cleanup of generated content
- **Gutenberg Block Support**: Optional integration with WordPress block editor

### 🎨 User Interface
- **Modern Admin Interface**: Clean, professional design with intuitive controls
- **Real-time Preview**: Preview generated content before publishing
- **Tabbed Output**: View content in Preview, HTML, or Shortcode format
- **Copy to Clipboard**: Easy copying of HTML and shortcodes
- **Responsive Design**: Works perfectly on all devices

### ⚙️ Advanced Settings
- **API Configuration**: Secure OpenAI API key management
- **Model Selection**: Choose between GPT-3.5 Turbo, GPT-4, and GPT-4 Turbo
- **Token Control**: Adjust max tokens and temperature for optimal results
- **Post Status Options**: Draft, Published, Private, or Pending Review
- **Usage Statistics**: Track your content generation activity

### 🔧 Technical Features
- **Shortcode Support**: Display generated content anywhere with `[contentcrafter_output]`
- **Security Best Practices**: Proper sanitization and nonce verification
- **Error Handling**: Comprehensive error messages and validation
- **AJAX Integration**: Smooth, asynchronous content generation
- **Custom Post Type Support**: Works with any registered post type
- **Avada Theme Integration**: Automatic font weight application for Avada theme posts

## Installation

### Method 1: Manual Installation
1. Download the plugin files
2. Upload the `contentcrafter-ai` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to 'ContentCrafter' → 'Settings' to configure your API key

### Method 2: WordPress Admin Upload
1. Go to WordPress Admin → Plugins → Add New
2. Click 'Upload Plugin'
3. Choose the plugin zip file
4. Click 'Install Now' and then 'Activate'

## Configuration

### 1. API Setup
1. Get your OpenAI API key from [https://platform.openai.com/api-keys](https://platform.openai.com/api-keys)
2. Go to WordPress Admin → ContentCrafter → Settings
3. Enter your API key in the 'OpenAI API Key' field
4. Click 'Save Settings'

### 2. Model Configuration
- **GPT-3.5 Turbo**: Fast and cost-effective (recommended for most use cases)
- **GPT-4**: More capable but higher cost
- **GPT-4 Turbo**: Latest model with improved performance

### 3. Content Settings
- **Max Tokens**: Control the length of generated content (100-4000)
- **Temperature**: Control creativity vs. focus (0-2)
- **Default Category**: Set a default category for generated posts
- **Post Status**: Choose default status (Draft, Published, etc.)

### 4. Avada Theme Integration
- **Enable Avada Weight**: Automatically apply font weight settings to generated posts
- **Font Weight Options**: Choose from 9 different font weights (100-900)
- **Automatic Application**: Font weight is applied to post titles when posts are created
- **Theme Compatibility**: Works seamlessly with Avada theme's typography system

## Usage

### Generating Content

1. **Access the Generator**
   - Go to WordPress Admin → ContentCrafter

2. **Enter Your Prompt**
   - Write a clear, detailed prompt describing the content you want
   - Include target audience, tone, and key points

3. **Configure Options**
   - Select a category (optional)
   - Choose post type (Post, Page, or Custom Post Type)
   - Enable/disable auto-title generation
   - Choose whether to create a WordPress post

4. **Generate Content**
   - Click 'Generate Content'
   - Wait for the AI to process your request
   - Review the generated content in the preview tab

5. **Publish or Edit**
   - If creating a post, it will be saved automatically
   - Use the 'Edit Post' button to modify the content
   - Copy HTML or shortcode for manual use

### Using Shortcodes

Display generated content anywhere on your site:

```php
[chatgpt_output id="123"]
```

Where `123` is the post ID of a generated post.

### Avada Theme Integration

When Avada theme weight integration is enabled:

1. **Automatic Application**: Font weight settings are automatically applied to new posts
2. **Title Styling**: Post titles inherit the selected font weight
3. **Theme Consistency**: Maintains visual consistency with your Avada theme
4. **Customizable**: Choose from 9 different font weight options

**Note**: This feature only works when the Avada theme is active and the integration is enabled in settings.

### Best Practices

#### Writing Effective Prompts
- **Be Specific**: Include target audience, tone, and key points
- **Provide Context**: Mention your industry, brand voice, and goals
- **Include Keywords**: Add relevant SEO keywords naturally
- **Specify Length**: Mention desired word count or structure

#### Example Prompts
```
"Write a 1000-word blog post about sustainable gardening for beginners. Include practical tips, common mistakes to avoid, and eco-friendly practices. Target audience: urban dwellers interested in starting their first garden."

"Create a comprehensive guide about WordPress security best practices. Include sections on plugin management, user roles, backup strategies, and common vulnerabilities. Make it actionable and beginner-friendly."
```

#### Content Optimization
- **Review Before Publishing**: Always check generated content for accuracy
- **Add Personal Touch**: Customize AI-generated content with your voice
- **Include Media**: Add relevant images, videos, or infographics
- **SEO Optimization**: Add meta descriptions, tags, and internal links

## Troubleshooting

### Common Issues

#### API Connection Errors
- **Invalid API Key**: Ensure your OpenAI API key is correct and active
- **Rate Limits**: Check your OpenAI usage limits
- **Network Issues**: Verify your server can reach OpenAI's API

#### Content Generation Problems
- **Empty Responses**: Try a more specific prompt
- **Inappropriate Content**: Adjust temperature setting or add content guidelines
- **Length Issues**: Modify max tokens setting

#### WordPress Integration Issues
- **Permission Errors**: Ensure you have administrator privileges
- **Plugin Conflicts**: Deactivate other plugins temporarily
- **Theme Compatibility**: Test with default WordPress theme

### Getting Help

1. **Check Settings**: Verify API key and configuration
2. **Test Connection**: Use the 'Test API Connection' button
3. **Review Logs**: Check WordPress error logs for details
4. **Contact Support**: Reach out for technical assistance

## Security

### Data Protection
- API keys are stored securely in WordPress database
- All user inputs are properly sanitized
- Nonce verification prevents CSRF attacks
- No sensitive data is logged

### Best Practices
- Never share your API key publicly
- Use HTTPS for all API communications
- Regularly update the plugin
- Monitor your OpenAI usage

## Development

### File Structure
```
chatgpt-content-generator/
├── chatgpt-content-generator.php    # Main plugin file
├── templates/
│   ├── admin-page.php               # Main admin interface
│   └── settings-page.php            # Settings page
├── assets/
│   ├── js/
│   │   ├── admin.js                # Admin JavaScript
│   │   └── frontend.js             # Frontend JavaScript
│   └── css/
│       ├── admin.css               # Admin styles
│       └── frontend.css            # Frontend styles
└── README.md                       # This file
```

### Hooks and Filters
The plugin provides several hooks for customization:

```php
// Modify API request parameters
add_filter('ccg_api_request', function($request) {
    // Customize request
    return $request;
});

// Modify generated content
add_filter('ccg_generated_content', function($content) {
    // Customize content
    return $content;
});

// Add custom post processing
add_action('ccg_post_created', function($post_id) {
    // Custom actions after post creation
});
```

## Changelog

### Version 1.0.0
- Initial release
- OpenAI ChatGPT API integration
- Admin interface for content generation
- Settings page for API configuration
- Shortcode support
- Category and post type selection
- Auto-title generation
- HTML cleanup functionality
- Usage statistics
- Responsive design

## Support

For support, feature requests, or bug reports:

- **Documentation**: Check this README for usage instructions
- **WordPress.org**: Visit the plugin page for community support
- **GitHub**: Submit issues and feature requests
- **Email**: Contact for direct support

## License

This plugin is licensed under the GPL v2 or later.

## Credits

- **OpenAI**: For providing the ChatGPT API
- **WordPress**: For the excellent platform
- **Community**: For feedback and contributions

---

**Note**: This plugin requires an active OpenAI API key and internet connection to function. API usage is subject to OpenAI's terms of service and pricing. 