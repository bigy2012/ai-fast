<?php
/**
 * Settings class for plugin configuration
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ai_Expert_Ads_Settings {

    /**
     * Settings group name
     */
    private $settings_group = 'ai_expert_ads_settings';

    /**
     * Initialize settings
     */
    public function init() {
        // Register settings
        register_setting(
            $this->settings_group,
            'aea_general_settings',
            array($this, 'sanitize_general_settings')
        );

        // Add general settings section
        add_settings_section(
            'aea_general_section',
            __('General Settings', 'ai-expert-ads'),
            array($this, 'general_section_callback'),
            $this->settings_group
        );

        // Add individual settings fields
        $this->add_settings_fields();
    }

    /**
     * Add settings fields
     */
    private function add_settings_fields() {
        // Enable/Disable plugin
        add_settings_field(
            'enable_plugin',
            __('Enable Plugin', 'ai-expert-ads'),
            array($this, 'enable_plugin_callback'),
            $this->settings_group,
            'aea_general_section'
        );

        // Default account type
        add_settings_field(
            'default_account_type',
            __('Default Account Type', 'ai-expert-ads'),
            array($this, 'default_account_type_callback'),
            $this->settings_group,
            'aea_general_section'
        );

        // Auto-activate new accounts
        add_settings_field(
            'auto_activate_accounts',
            __('Auto-activate New Accounts', 'ai-expert-ads'),
            array($this, 'auto_activate_accounts_callback'),
            $this->settings_group,
            'aea_general_section'
        );

        // Debug mode
        add_settings_field(
            'debug_mode',
            __('Debug Mode', 'ai-expert-ads'),
            array($this, 'debug_mode_callback'),
            $this->settings_group,
            'aea_general_section'
        );

        // Cache duration
        add_settings_field(
            'cache_duration',
            __('Cache Duration (hours)', 'ai-expert-ads'),
            array($this, 'cache_duration_callback'),
            $this->settings_group,
            'aea_general_section'
        );

        // Maximum accounts
        add_settings_field(
            'max_accounts',
            __('Maximum Accounts Allowed', 'ai-expert-ads'),
            array($this, 'max_accounts_callback'),
            $this->settings_group,
            'aea_general_section'
        );
    }

    /**
     * General section callback
     */
    public function general_section_callback() {
        echo '<p>' . __('Configure general settings for Ai Expert Ads plugin.', 'ai-expert-ads') . '</p>';
    }

    /**
     * Enable plugin callback
     */
    public function enable_plugin_callback() {
        $options = get_option('aea_general_settings');
        $value = isset($options['enable_plugin']) ? $options['enable_plugin'] : 1;
        
        echo '<input type="checkbox" name="aea_general_settings[enable_plugin]" value="1" ' . checked(1, $value, false) . '>';
        echo '<label for="aea_general_settings[enable_plugin]">' . __('Enable the plugin functionality', 'ai-expert-ads') . '</label>';
    }

    /**
     * Default account type callback
     */
    public function default_account_type_callback() {
        $options = get_option('aea_general_settings');
        $value = isset($options['default_account_type']) ? $options['default_account_type'] : 'google-ads';
        
        $account_types = array(
            'google-ads' => __('Google Ads', 'ai-expert-ads'),
            'facebook-ads' => __('Facebook Ads', 'ai-expert-ads'),
            'bing-ads' => __('Bing Ads', 'ai-expert-ads'),
            'other' => __('Other', 'ai-expert-ads')
        );

        echo '<select name="aea_general_settings[default_account_type]">';
        foreach ($account_types as $key => $label) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($key, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . __('Default account type for new accounts', 'ai-expert-ads') . '</p>';
    }

    /**
     * Auto-activate accounts callback
     */
    public function auto_activate_accounts_callback() {
        $options = get_option('aea_general_settings');
        $value = isset($options['auto_activate_accounts']) ? $options['auto_activate_accounts'] : 1;
        
        echo '<input type="checkbox" name="aea_general_settings[auto_activate_accounts]" value="1" ' . checked(1, $value, false) . '>';
        echo '<label for="aea_general_settings[auto_activate_accounts]">' . __('Automatically activate new accounts when added', 'ai-expert-ads') . '</label>';
    }

    /**
     * Debug mode callback
     */
    public function debug_mode_callback() {
        $options = get_option('aea_general_settings');
        $value = isset($options['debug_mode']) ? $options['debug_mode'] : 0;
        
        echo '<input type="checkbox" name="aea_general_settings[debug_mode]" value="1" ' . checked(1, $value, false) . '>';
        echo '<label for="aea_general_settings[debug_mode]">' . __('Enable debug logging', 'ai-expert-ads') . '</label>';
        echo '<p class="description">' . __('Enable this to log debug information for troubleshooting', 'ai-expert-ads') . '</p>';
    }

    /**
     * Cache duration callback
     */
    public function cache_duration_callback() {
        $options = get_option('aea_general_settings');
        $value = isset($options['cache_duration']) ? $options['cache_duration'] : 24;
        
        echo '<input type="number" name="aea_general_settings[cache_duration]" value="' . esc_attr($value) . '" min="1" max="168" class="small-text">';
        echo '<p class="description">' . __('How long to cache account data (1-168 hours)', 'ai-expert-ads') . '</p>';
    }

    /**
     * Max accounts callback
     */
    public function max_accounts_callback() {
        $options = get_option('aea_general_settings');
        $value = isset($options['max_accounts']) ? $options['max_accounts'] : 0;
        
        echo '<input type="number" name="aea_general_settings[max_accounts]" value="' . esc_attr($value) . '" min="0" class="small-text">';
        echo '<p class="description">' . __('Maximum number of accounts allowed (0 = unlimited)', 'ai-expert-ads') . '</p>';
    }

    /**
     * Sanitize general settings
     */
    public function sanitize_general_settings($input) {
        $sanitized = array();

        // Enable plugin
        $sanitized['enable_plugin'] = isset($input['enable_plugin']) ? 1 : 0;

        // Default account type
        $allowed_types = array('google-ads', 'facebook-ads', 'bing-ads', 'other');
        $sanitized['default_account_type'] = in_array($input['default_account_type'], $allowed_types) 
            ? $input['default_account_type'] 
            : 'google-ads';

        // Auto-activate accounts
        $sanitized['auto_activate_accounts'] = isset($input['auto_activate_accounts']) ? 1 : 0;

        // Debug mode
        $sanitized['debug_mode'] = isset($input['debug_mode']) ? 1 : 0;

        // Cache duration
        $sanitized['cache_duration'] = max(1, min(168, intval($input['cache_duration'])));

        // Max accounts
        $sanitized['max_accounts'] = max(0, intval($input['max_accounts']));

        return $sanitized;
    }

    /**
     * Get setting value
     */
    public static function get_setting($key, $default = null) {
        $options = get_option('aea_general_settings');
        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Check if plugin is enabled
     */
    public static function is_plugin_enabled() {
        return self::get_setting('enable_plugin', 1) == 1;
    }

    /**
     * Get default account type
     */
    public static function get_default_account_type() {
        return self::get_setting('default_account_type', 'google-ads');
    }

    /**
     * Check if auto-activate is enabled
     */
    public static function is_auto_activate_enabled() {
        return self::get_setting('auto_activate_accounts', 1) == 1;
    }

    /**
     * Check if debug mode is enabled
     */
    public static function is_debug_enabled() {
        return self::get_setting('debug_mode', 0) == 1;
    }

    /**
     * Get cache duration
     */
    public static function get_cache_duration() {
        return self::get_setting('cache_duration', 24);
    }

    /**
     * Get max accounts limit
     */
    public static function get_max_accounts() {
        return self::get_setting('max_accounts', 0);
    }
}
