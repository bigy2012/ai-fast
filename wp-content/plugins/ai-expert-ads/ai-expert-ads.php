<?php
/**
 * Plugin Name: Ai Expert Ads
 * Plugin URI: https://ai-expertads.com/
 * Description: Advanced AI-powered ads management plugin with multiple account ID support and intelligent targeting.
 * Version: 1.0.0
 * Author: Ai Expert Team
 * Author URI: https://ai-expertads.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-expert-ads
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AEA_PLUGIN_FILE', __FILE__);
define('AEA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AEA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AEA_PLUGIN_VERSION', '1.0.0');

// Include the main plugin class
require_once AEA_PLUGIN_PATH . 'includes/class-ai-expert-ads.php';

/**
 * Initialize the plugin
 */
function ai_expert_ads_init() {
    $ai_expert_ads = new Ai_Expert_Ads();
    $ai_expert_ads->init();
}

// Hook into WordPress
add_action('plugins_loaded', 'ai_expert_ads_init');

/**
 * Activation hook
 */
function ai_expert_ads_activate() {
    // Create database tables if needed
    require_once AEA_PLUGIN_PATH . 'includes/class-ai-expert-ads-activator.php';
    Ai_Expert_Ads_Activator::activate();
}
register_activation_hook(__FILE__, 'ai_expert_ads_activate');

/**
 * Deactivation hook
 */
function ai_expert_ads_deactivate() {
    require_once AEA_PLUGIN_PATH . 'includes/class-ai-expert-ads-deactivator.php';
    Ai_Expert_Ads_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'ai_expert_ads_deactivate');
