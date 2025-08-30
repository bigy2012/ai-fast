<?php
/**
 * Account Manager class for handling account IDs
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Ai_Expert_Ads_Account_Manager {

    /**
     * Database table name
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'aea_accounts';
    }

    /**
     * Add new account
     */
    public function add_account($data) {
        global $wpdb;

        $account_data = array(
            'account_name' => sanitize_text_field($data['account_name']),
            'account_id' => sanitize_text_field($data['account_id']),
            'account_type' => sanitize_text_field($data['account_type']),
            'status' => isset($data['status']) ? 1 : 0,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        );

        $result = $wpdb->insert(
            $this->table_name,
            $account_data,
            array('%s', '%s', '%s', '%d', '%s', '%s')
        );

        if ($result === false) {
            wp_die(__('Error adding account to database', 'ai-expert-ads'));
        }

        return $wpdb->insert_id;
    }

    /**
     * Delete account
     */
    public function delete_account($account_id) {
        global $wpdb;

        $result = $wpdb->delete(
            $this->table_name,
            array('id' => intval($account_id)),
            array('%d')
        );

        if ($result === false) {
            wp_die(__('Error deleting account from database', 'ai-expert-ads'));
        }

        return $result;
    }

    /**
     * Get all accounts
     */
    public function get_all_accounts() {
        global $wpdb;

        $accounts = $wpdb->get_results(
            "SELECT * FROM {$this->table_name} ORDER BY created_at DESC"
        );

        return $accounts;
    }

    /**
     * Get active accounts
     */
    public function get_active_accounts() {
        global $wpdb;

        $accounts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE status = %d ORDER BY created_at DESC",
                1
            )
        );

        return $accounts;
    }

    /**
     * Get account by ID
     */
    public function get_account($id) {
        global $wpdb;

        $account = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE id = %d",
                intval($id)
            )
        );

        return $account;
    }

    /**
     * Update account
     */
    public function update_account($id, $data) {
        global $wpdb;

        // Debug: Log the incoming data
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Update account ID: ' . $id);
            error_log('Update account data: ' . print_r($data, true));
        }

        $account_data = array(
            'account_name' => sanitize_text_field($data['account_name']),
            'account_id' => sanitize_text_field($data['account_id']),
            'account_type' => sanitize_text_field($data['account_type']),
            'status' => isset($data['status']) ? 1 : 0,
            'updated_at' => current_time('mysql')
        );

        $result = $wpdb->update(
            $this->table_name,
            $account_data,
            array('id' => intval($id)),
            array('%s', '%s', '%s', '%d', '%s'),
            array('%d')
        );

        // Debug: Log the result
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Update result: ' . ($result !== false ? 'Success' : 'Failed'));
            if ($result === false) {
                error_log('Database error: ' . $wpdb->last_error);
            }
        }

        return $result;
    }

    /**
     * Get total accounts count
     */
    public function get_total_count() {
        global $wpdb;

        $count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name}"
        );

        return intval($count);
    }

    /**
     * Check if account ID already exists
     */
    public function account_exists($account_id) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE account_id = %s",
                $account_id
            )
        );

        return intval($count) > 0;
    }

    /**
     * Create database table
     */
    public function create_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id int(11) NOT NULL AUTO_INCREMENT,
            account_name varchar(255) NOT NULL,
            account_id varchar(255) NOT NULL,
            account_type varchar(50) NOT NULL DEFAULT 'other',
            status tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY account_id (account_id),
            KEY status (status),
            KEY account_type (account_type)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Show success notice
     */
    public function show_success_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Account added successfully!', 'ai-expert-ads'); ?></p>
        </div>
        <?php
    }

    /**
     * Show delete notice
     */
    public function show_delete_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Account deleted successfully!', 'ai-expert-ads'); ?></p>
        </div>
        <?php
    }

    /**
     * Show update notice
     */
    public function show_update_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Account updated successfully!', 'ai-expert-ads'); ?></p>
        </div>
        <?php
    }
}
