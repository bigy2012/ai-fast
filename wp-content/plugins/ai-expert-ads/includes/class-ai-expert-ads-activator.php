<?php
/**
 * Plugin activation class
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ai_Expert_Ads_Activator {

    /**
     * Plugin activation
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Clear any cached data
        wp_cache_flush();
    }

    /**
     * Create database tables
     */
    private static function create_tables() {
        require_once AEA_PLUGIN_PATH . 'includes/class-ai-expert-ads-account-manager.php';
        $account_manager = new Ai_Expert_Ads_Account_Manager();
        $account_manager->create_table();
    }

    /**
     * Set default options
     */
    private static function set_default_options() {
        $default_settings = array(
            'enable_plugin' => 1,
            'default_account_type' => 'google-ads',
            'auto_activate_accounts' => 1,
            'debug_mode' => 0,
            'cache_duration' => 24,
            'max_accounts' => 0
        );

        add_option('aea_general_settings', $default_settings);
        add_option('aea_plugin_version', AEA_PLUGIN_VERSION);
        add_option('aea_plugin_activated', current_time('mysql'));
    }
}
