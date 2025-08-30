# Ai Expert Ads WordPress Plugin

Advanced AI-powered advertising management plugin with intelligent targeting and multiple account support.

## Description

Ai Expert Ads is a comprehensive WordPress plugin designed to help website owners manage multiple advertising accounts efficiently. The plugin provides an intuitive interface for managing account IDs from various advertising platforms including Google Ads, Facebook Ads, Bing Ads, and more.

## Features

- **Multiple Account Management**: Add unlimited advertising account IDs
- **Support for Major Platforms**: Google Ads, Facebook Ads, Bing Ads, and custom platforms
- **Intelligent Account Validation**: Real-time validation of account ID formats
- **Flexible Settings**: Configurable options for cache duration, account limits, and more
- **User-Friendly Interface**: Clean, modern admin interface with responsive design
- **Status Management**: Enable/disable individual accounts as needed
- **Bulk Operations**: Manage multiple accounts simultaneously
- **Debug Mode**: Built-in debugging for troubleshooting

## Installation

1. Upload the `ai-expert-ads` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'Ai Expert Ads' in the admin menu to configure settings
4. Add your advertising account IDs through the 'Account IDs' submenu

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Plugin Structure

```
ai-expert-ads/
├── ai-expert-ads.php              # Main plugin file
├── README.md                      # Documentation
├── admin/                         # Admin functionality
│   └── class-ai-expert-ads-admin.php
├── assets/                        # Static assets
│   ├── css/
│   │   └── admin.css             # Admin styles
│   ├── js/
│   │   └── admin.js              # Admin JavaScript
│   └── images/
│       ├── logo.svg              # Plugin logo (SVG)
│       └── logo.png              # Plugin logo (PNG)
└── includes/                      # Core classes
    ├── class-ai-expert-ads.php          # Main plugin class
    ├── class-ai-expert-ads-activator.php   # Plugin activation
    ├── class-ai-expert-ads-deactivator.php # Plugin deactivation
    ├── class-ai-expert-ads-settings.php    # Settings management
    └── class-ai-expert-ads-account-manager.php # Account management
```

## Usage

### Adding Account IDs

1. Go to **Ai Expert Ads > Account IDs** in your WordPress admin
2. Fill out the form with:
   - **Account Name**: A friendly name for the account
   - **Account ID**: Your advertising account ID
   - **Account Type**: Select the platform (Google Ads, Facebook Ads, etc.)
   - **Status**: Choose whether the account is active
3. Click **Add Account**

### Managing Settings

1. Navigate to **Ai Expert Ads > Settings**
2. Configure:
   - Plugin enable/disable status
   - Default account type for new accounts
   - Auto-activation settings
   - Debug mode
   - Cache duration
   - Maximum account limits

### Account Types Supported

- **Google Ads**: 10-digit account IDs
- **Facebook Ads**: 15-16 digit account IDs  
- **Bing Ads**: 8-9 digit account IDs
- **Other**: Custom account types

## Database Schema

The plugin creates a custom table `wp_aea_accounts` with the following structure:

```sql
CREATE TABLE wp_aea_accounts (
    id int(11) NOT NULL AUTO_INCREMENT,
    account_name varchar(255) NOT NULL,
    account_id varchar(255) NOT NULL,
    account_type varchar(50) NOT NULL DEFAULT 'other',
    status tinyint(1) NOT NULL DEFAULT 1,
    created_at datetime NOT NULL,
    updated_at datetime NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY account_id (account_id)
);
```

## Hooks and Filters

### Actions
- `aea_account_added` - Fired when a new account is added
- `aea_account_deleted` - Fired when an account is deleted
- `aea_settings_updated` - Fired when settings are updated

### Filters
- `aea_account_types` - Modify available account types
- `aea_max_accounts` - Override maximum accounts limit
- `aea_cache_duration` - Modify cache duration

## Customization

### Adding Custom Account Types

```php
add_filter('aea_account_types', function($types) {
    $types['custom-platform'] = __('Custom Platform', 'ai-expert-ads');
    return $types;
});
```

### Modifying Account Validation

```php
add_filter('aea_validate_account_id', function($is_valid, $account_id, $account_type) {
    // Custom validation logic
    return $is_valid;
}, 10, 3);
```

## Security Features

- Nonce verification for all form submissions
- Data sanitization and validation
- Capability checks for admin functions
- SQL injection prevention with prepared statements

## Performance

- Efficient database queries with proper indexing
- Optional caching for account data
- Optimized admin scripts loading
- Minimal frontend impact

## Troubleshooting

### Enable Debug Mode
1. Go to **Ai Expert Ads > Settings**
2. Enable **Debug Mode**
3. Check debug logs in `/wp-content/debug.log`

### Common Issues

**Q: Account ID validation fails**
A: Ensure the account ID format matches the selected platform requirements

**Q: Plugin admin pages not loading**
A: Check for plugin conflicts and ensure WordPress meets minimum requirements

**Q: Database errors on activation**
A: Verify WordPress database permissions and try deactivating/reactivating the plugin

## Changelog

### Version 1.0.0
- Initial release
- Multiple account management
- Settings panel
- Account validation
- Responsive admin interface

## Support

For support and feature requests, please contact our development team.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by the Ai Expert Team with a focus on providing powerful advertising management tools for WordPress users.
