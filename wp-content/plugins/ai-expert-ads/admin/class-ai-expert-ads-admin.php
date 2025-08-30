<?php
/**
 * Admin functionality class
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ai_Expert_Ads_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        // Constructor logic
    }

    /**
     * Create admin menu
     */
    public function create_admin_menu() {
        // Add main menu page
        add_menu_page(
            __('Ai Expert Ads', 'ai-expert-ads'),
            __('Ai Expert Ads', 'ai-expert-ads'),
            'manage_options',
            'ai-expert-ads',
            array($this, 'admin_page'),
            'data:image/svg+xml;base64,' . base64_encode($this->get_menu_icon()),
            30
        );

        // Add settings submenu
        add_submenu_page(
            'ai-expert-ads',
            __('Settings', 'ai-expert-ads'),
            __('Settings', 'ai-expert-ads'),
            'manage_options',
            'ai-expert-ads-settings',
            array($this, 'settings_page')
        );

        // Add accounts submenu
        add_submenu_page(
            'ai-expert-ads',
            __('Account IDs', 'ai-expert-ads'),
            __('Account IDs', 'ai-expert-ads'),
            'manage_options',
            'ai-expert-ads-accounts',
            array($this, 'accounts_page')
        );
    }

    /**
     * Main admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <div class="ai-expert-ads-header">
                <img src="<?php echo AEA_PLUGIN_URL . 'assets/images/logo.png'; ?>" alt="Ai Expert Ads" class="aea-logo">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            </div>
            
            <div class="aea-dashboard">
                <div class="aea-card">
                    <h3><?php _e('Welcome to Ai Expert Ads', 'ai-expert-ads'); ?></h3>
                    <p><?php _e('Advanced AI-powered ads management with intelligent targeting and multiple account support.', 'ai-expert-ads'); ?></p>
                    
                    <div class="aea-quick-actions">
                        <a href="<?php echo admin_url('admin.php?page=ai-expert-ads-accounts'); ?>" class="button button-primary">
                            <?php _e('Manage Account IDs', 'ai-expert-ads'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=ai-expert-ads-settings'); ?>" class="button button-secondary">
                            <?php _e('Settings', 'ai-expert-ads'); ?>
                        </a>
                    </div>
                </div>

                <div class="aea-stats">
                    <div class="aea-stat-box">
                        <h4><?php _e('Total Accounts', 'ai-expert-ads'); ?></h4>
                        <span class="aea-stat-number"><?php echo $this->get_total_accounts(); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <div class="ai-expert-ads-header">
                <img src="<?php echo AEA_PLUGIN_URL . 'assets/images/logo.png'; ?>" alt="Ai Expert Ads" class="aea-logo">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            </div>

            <form method="post" action="options.php">
                <?php
                settings_fields('ai_expert_ads_settings');
                do_settings_sections('ai_expert_ads_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Accounts management page
     */
    public function accounts_page() {
        $account_manager = new Ai_Expert_Ads_Account_Manager();
        $edit_account = null;
        $message = '';
        
        // Handle form submissions
        if (isset($_POST['action']) && wp_verify_nonce($_POST['aea_nonce'], 'aea_account_action')) {
            if ($_POST['action'] === 'add_account') {
                $result = $account_manager->add_account($_POST);
                if ($result) {
                    $message = 'added';
                }
            } elseif ($_POST['action'] === 'edit_account') {
                // Debug: Check if edit_id is present
                if (!isset($_POST['edit_id']) || empty($_POST['edit_id'])) {
                    $message = 'error';
                } else {
                    $result = $account_manager->update_account($_POST['edit_id'], $_POST);
                    if ($result !== false) {
                        $message = 'updated';
                        // Clear edit mode after successful update
                        $edit_account = null;
                    } else {
                        $message = 'error';
                    }
                }
            } elseif ($_POST['action'] === 'delete_account') {
                $result = $account_manager->delete_account($_POST['account_id']);
                if ($result) {
                    $message = 'deleted';
                }
            }
        }

        // Check if we're editing an account (only if no POST action was successful)
        if (empty($message) && isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['account_id'])) {
            $edit_account = $account_manager->get_account($_GET['account_id']);
        }

        $accounts = $account_manager->get_all_accounts();
        ?>
        <div class="wrap">
            <div class="ai-expert-ads-header">
                <img src="<?php echo AEA_PLUGIN_URL . 'assets/images/logo.png'; ?>" alt="Ai Expert Ads" class="aea-logo">
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            </div>

            <?php
            // Show success messages
            if (!empty($message)) {
                $message_text = '';
                $message_type = 'success';
                switch ($message) {
                    case 'added':
                        $message_text = __('Account added successfully!', 'ai-expert-ads');
                        break;
                    case 'updated':
                        $message_text = __('Account updated successfully!', 'ai-expert-ads');
                        break;
                    case 'deleted':
                        $message_text = __('Account deleted successfully!', 'ai-expert-ads');
                        break;
                    case 'error':
                        $message_text = __('Error: Unable to process your request. Please try again.', 'ai-expert-ads');
                        $message_type = 'error';
                        break;
                }
                if ($message_text) {
                    echo '<div class="notice notice-' . $message_type . ' is-dismissible"><p>' . esc_html($message_text) . '</p></div>';
                }
            }
            ?>

            <!-- Add New Account Form -->
            <div class="aea-card">
                <h3><?php echo $edit_account ? __('Edit Account ID', 'ai-expert-ads') : __('Add New Account ID', 'ai-expert-ads'); ?></h3>
                <form method="post" class="aea-account-form">
                    <?php wp_nonce_field('aea_account_action', 'aea_nonce'); ?>
                    <input type="hidden" name="action" value="<?php echo $edit_account ? 'edit_account' : 'add_account'; ?>">
                    <?php if ($edit_account): ?>
                        <input type="hidden" name="edit_id" value="<?php echo esc_attr($edit_account->id); ?>">
                    <?php endif; ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="account_name"><?php _e('Account Name', 'ai-expert-ads'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="account_name" id="account_name" class="regular-text" 
                                       value="<?php echo $edit_account ? esc_attr($edit_account->account_name) : ''; ?>" required>
                                <p class="description"><?php _e('Enter a friendly name for this account', 'ai-expert-ads'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="account_id"><?php _e('Account ID', 'ai-expert-ads'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="account_id" id="account_id" class="regular-text" 
                                       value="<?php echo $edit_account ? esc_attr($edit_account->account_id) : ''; ?>" required>
                                <p class="description"><?php _e('Enter your advertising account ID', 'ai-expert-ads'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="account_type"><?php _e('Account Type', 'ai-expert-ads'); ?></label>
                            </th>
                            <td>
                                <select name="account_type" id="account_type">
                                    <option value="facebook-ads" <?php echo ($edit_account && $edit_account->account_type === 'facebook-ads') ? 'selected' : ''; ?>><?php _e('Facebook Ads', 'ai-expert-ads'); ?></option>
                                    <option value="google-ads" <?php echo ($edit_account && $edit_account->account_type === 'google-ads') ? 'selected' : ''; ?>><?php _e('Google Ads', 'ai-expert-ads'); ?></option>
                                    <option value="bing-ads" <?php echo ($edit_account && $edit_account->account_type === 'bing-ads') ? 'selected' : ''; ?>><?php _e('Bing Ads', 'ai-expert-ads'); ?></option>
                                    <option value="other" <?php echo ($edit_account && $edit_account->account_type === 'other') ? 'selected' : ''; ?>><?php _e('Other', 'ai-expert-ads'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="status"><?php _e('Status', 'ai-expert-ads'); ?></label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="status" id="status" value="1" 
                                           <?php echo (!$edit_account || $edit_account->status) ? 'checked' : ''; ?>>
                                    <?php _e('Active', 'ai-expert-ads'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="submit-buttons">
                        <?php if ($edit_account): ?>
                            <?php submit_button(__('Update Account', 'ai-expert-ads')); ?>
                            <a href="<?php echo admin_url('admin.php?page=ai-expert-ads-accounts'); ?>" class="button button-secondary">
                                <?php _e('Cancel', 'ai-expert-ads'); ?>
                            </a>
                        <?php else: ?>
                            <?php submit_button(__('Add Account', 'ai-expert-ads')); ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Existing Accounts List -->
            <div class="aea-card">
                <h3><?php _e('Existing Account IDs', 'ai-expert-ads'); ?></h3>
                
                <?php if (empty($accounts)): ?>
                    <p><?php _e('No accounts found. Add your first account above.', 'ai-expert-ads'); ?></p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th scope="col"><?php _e('Account Name', 'ai-expert-ads'); ?></th>
                                <th scope="col"><?php _e('Account ID', 'ai-expert-ads'); ?></th>
                                <th scope="col"><?php _e('Type', 'ai-expert-ads'); ?></th>
                                <th scope="col"><?php _e('Status', 'ai-expert-ads'); ?></th>
                                <th scope="col"><?php _e('Actions', 'ai-expert-ads'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td><strong><?php echo esc_html($account->account_name); ?></strong></td>
                                    <td><code><?php echo esc_html($account->account_id); ?></code></td>
                                    <td>
                                        <span class="aea-account-type aea-type-<?php echo esc_attr($account->account_type); ?>">
                                            <?php echo esc_html(ucfirst(str_replace('-', ' ', $account->account_type))); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="aea-status aea-status-<?php echo $account->status ? 'active' : 'inactive'; ?>">
                                            <?php echo $account->status ? __('Active', 'ai-expert-ads') : __('Inactive', 'ai-expert-ads'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=ai-expert-ads-accounts&action=edit&account_id=' . $account->id); ?>" 
                                           class="button button-small button-edit">
                                            <?php _e('Edit', 'ai-expert-ads'); ?>
                                        </a>
                                        <form method="post" style="display: inline;">
                                            <?php wp_nonce_field('aea_account_action', 'aea_nonce'); ?>
                                            <input type="hidden" name="action" value="delete_account">
                                            <input type="hidden" name="account_id" value="<?php echo esc_attr($account->id); ?>">
                                            <button type="submit" class="button button-small button-link-delete" 
                                                    onclick="return confirm('<?php _e('Are you sure you want to delete this account?', 'ai-expert-ads'); ?>')">
                                                <?php _e('Delete', 'ai-expert-ads'); ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Initialize settings
     */
    public function init_settings() {
        $settings = new Ai_Expert_Ads_Settings();
        $settings->init();
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'ai-expert-ads') === false) {
            return;
        }

        wp_enqueue_style(
            'ai-expert-ads-admin',
            AEA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            AEA_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'ai-expert-ads-admin',
            AEA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            AEA_PLUGIN_VERSION,
            true
        );
    }

    /**
     * Get menu icon SVG
     */
    private function get_menu_icon() {
        return '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill="#a7aaad" d="M10 2C5.6 2 2 5.6 2 10s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm4.2 6.3l-5.1 5.1c-.3.3-.8.3-1.1 0l-2.4-2.4c-.3-.3-.3-.8 0-1.1.3-.3.8-.3 1.1 0l1.8 1.8 4.5-4.5c.3-.3.8-.3 1.1 0 .4.3.4.8.1 1.1z"/>
        </svg>';
    }

    /**
     * Get total accounts count
     */
    private function get_total_accounts() {
        $account_manager = new Ai_Expert_Ads_Account_Manager();
        return $account_manager->get_total_count();
    }
}
