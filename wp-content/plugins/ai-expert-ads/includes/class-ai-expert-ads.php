<?php
/**
 * Main plugin class
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ai_Expert_Ads {

    /**
     * Plugin version
     */
    public $version = '1.0.0';

    /**
     * Plugin instance
     */
    private static $instance = null;

    /**
     * Get plugin instance
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Constructor logic
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Load plugin components
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // Load admin class
        require_once AEA_PLUGIN_PATH . 'admin/class-ai-expert-ads-admin.php';
        
        // Load settings class
        require_once AEA_PLUGIN_PATH . 'includes/class-ai-expert-ads-settings.php';
        
        // Load account manager
        require_once AEA_PLUGIN_PATH . 'includes/class-ai-expert-ads-account-manager.php';
    }

    /**
     * Define admin hooks
     */
    private function define_admin_hooks() {
        $admin = new Ai_Expert_Ads_Admin();
        
        // Admin menu hooks
        add_action('admin_menu', array($admin, 'create_admin_menu'));
        add_action('admin_init', array($admin, 'init_settings'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_admin_scripts'));
    }

    /**
     * Define public hooks
     */
    private function define_public_hooks() {
        // Frontend hooks can be added here
    }

    /**
     * Get plugin path
     */
    public function plugin_path() {
        return untrailingslashit(plugin_dir_path(AEA_PLUGIN_FILE));
    }

    /**
     * Get plugin URL
     */
    public function plugin_url() {
        return untrailingslashit(plugin_dir_url(AEA_PLUGIN_FILE));
    }
}
