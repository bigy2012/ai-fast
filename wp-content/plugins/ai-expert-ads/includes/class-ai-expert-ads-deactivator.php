<?php
/**
 * Plugin deactivation class
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ai_Expert_Ads_Deactivator {

    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        // Clear scheduled hooks
        self::clear_scheduled_hooks();
        
        // Clear cache
        wp_cache_flush();
        
        // Log deactivation
        self::log_deactivation();
    }

    /**
     * Clear scheduled hooks
     */
    private static function clear_scheduled_hooks() {
        // Clear any cron jobs if they exist
        wp_clear_scheduled_hook('aea_daily_cleanup');
        wp_clear_scheduled_hook('aea_cache_cleanup');
    }

    /**
     * Log deactivation
     */
    private static function log_deactivation() {
        update_option('aea_plugin_deactivated', current_time('mysql'));
    }
}
