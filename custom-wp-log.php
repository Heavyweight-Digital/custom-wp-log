<?php
/*  
 * Plugin Name:       Custom WP Log  
 * Plugin URI:        https://heavyweightdigital.co.za  
 * Description:       A custom plugin for managing and logging messages with enhanced functionality.  
 * Version:           1.0
 * Requires at least: 4.8  
 * Requires PHP:      7.4  
 * Author:            Byron Jacobs  
 * Author URI:        https://heavyweightdigital.co.za  
 * License:           GPL v2 or later  
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html  
 * Text Domain:       custom-logging  
 * Domain Path:       /languages  
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enable logging by default
define('CUSTOM_LOG_ENABLED', true);

add_action('admin_menu', 'custom_log_file_menu');
function custom_log_file_menu() {
    add_menu_page(
        'Custom WP Log File',
        'Custom WP Log File',
        'manage_options',
        'custom-log-file',
        'custom_log_file_page',
        'dashicons-media-text'
    );
}

function custom_log_file_page() {
    $log_dir = plugin_dir_path(__FILE__) . 'logs';
    $default_log_file = $log_dir . '/plugin-debug.log';

    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
    }

    // Fetch available log files
    $log_files = glob($log_dir . '/*.log');
    $backup_files = array_filter($log_files, function ($file) {
        return strpos(basename($file), '') === 0;
    });

    $current_log_file = get_option('custom_log_file_path', $default_log_file);
    $selected_backup_log_file = isset($_POST['view_backup_log_file']) ? sanitize_text_field($_POST['view_backup_log_file']) : '';

    if (isset($_POST['set_log_name']) && !empty($_POST['log_file_name'])) {
        $log_file_name = sanitize_file_name($_POST['log_file_name']);
        $current_log_file = $log_dir . '/' . $log_file_name;
        update_option('custom_log_file_path', $current_log_file); // Save custom log file path
    }

    if (isset($_POST['clear_log']) && file_exists($current_log_file)) {
        file_put_contents($current_log_file, '');
    }

    if (isset($_POST['backup_log']) && file_exists($current_log_file)) {
        $backup_file = $log_dir . '/' . time() . '.log';
        copy($current_log_file, $backup_file);
        $log_files = glob($log_dir . '/*.log'); // Refresh log file list
    }

    if (isset($_POST['delete_log']) && !empty($_POST['delete_log_file'])) {
        $delete_file = sanitize_text_field($_POST['delete_log_file']);
        if (file_exists($delete_file)) {
            unlink($delete_file);
            $log_files = glob($log_dir . '/*.log'); // Refresh log file list
        }
    }

    if (isset($_POST['delete_all_backups'])) {
        foreach ($backup_files as $file) {
            unlink($file);
        }
        $log_files = glob($log_dir . '/*.log'); // Refresh log file list
    }

    if (isset($_POST['test_log'])) {
        custom_log('This is a test log entry.', 'info');
        custom_log('This is a test error entry.', 'error');
    }

    if (isset($_POST['enable_logging'])) {
        update_option('custom_logging_enabled', true);
    }

    if (isset($_POST['disable_logging'])) {
        update_option('custom_logging_enabled', false);
    }

    if (isset($_POST['set_log_level']) && !empty($_POST['log_level'])) {
        update_option('custom_log_level', sanitize_text_field($_POST['log_level']));
    }

    $logging_enabled = get_option('custom_logging_enabled', true);
    $log_level = get_option('custom_log_level', 'all');

    echo '<div class="wrap">';
    echo '<h1>Custom WP Log File</h1>';
    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr><th scope="row">Set Log File Name:</th>';
    echo '<td><input type="text" name="log_file_name" value="' . esc_attr(basename($current_log_file)) . '" placeholder="plugin-debug.log" class="regular-text">';
    echo '<button type="submit" name="set_log_name" class="button button-primary">Set Log File</button></td></tr>';

    echo '<tr><th scope="row">Logging Actions:</th>';
    echo '<td>';
    echo '<button type="submit" name="test_log" class="button button-secondary">Test Log File</button> ';
    echo '<button type="submit" name="clear_log" class="button button-secondary">Clear Current Log</button> ';
    echo '<button type="submit" name="backup_log" class="button button-secondary">Backup Current Log</button>';
    echo '</td></tr>';

    echo '<tr><th scope="row">Delete Log Files:</th>';
    echo '<td>';
    echo '<select name="delete_log_file" class="regular-text">';
    foreach ($log_files as $file) {
        echo '<option value="' . esc_attr($file) . '">' . esc_html(basename($file)) . '</option>';
    }
    echo '</select>';
    echo '<button type="submit" name="delete_log" class="button button-danger">Delete Selected Log File</button> ';
    echo '<button type="submit" name="delete_all_backups" class="button button-secondary">Delete All Backup Logs</button>';
    echo '</td></tr>';

    echo '<tr><th scope="row">View Backup Logs:</th>';
    echo '<td>';
    echo '<select name="view_backup_log_file" class="regular-text">';
    foreach ($log_files as $file) {
        $selected = $file === $selected_backup_log_file ? 'selected' : '';
        echo '<option value="' . esc_attr($file) . '" ' . $selected . '>' . esc_html(basename($file)) . '</option>';
    }
    echo '</select>';
    echo '<button type="submit" class="button button-primary">View Selected Backup Log</button>';
    echo '</td></tr>';

    echo '<tr><th scope="row">Logging Settings:</th>';
    echo '<td>';
    echo '<select name="log_level" class="regular-text">';
    echo '<option value="none"' . selected($log_level, 'none', false) . '>None</option>';
    echo '<option value="all"' . selected($log_level, 'all', false) . '>All</option>';
    echo '<option value="error"' . selected($log_level, 'error', false) . '>Errors</option>';
    echo '<option value="warning"' . selected($log_level, 'warning', false) . '>Warnings</option>';
    echo '<option value="notice"' . selected($log_level, 'notice', false) . '>Notices</option>';
    echo '</select>';
    echo '<button type="submit" name="set_log_level" class="button button-primary">Set Log Level</button>';
    echo '</td></tr>';

    echo '<td>';
    if ($logging_enabled) {
        echo '<button type="submit" name="disable_logging" class="button button-secondary">Disable Logging</button>';
    } else {
        echo '<button type="submit" name="enable_logging" class="button button-primary">Enable Logging</button>';
    }
    echo '</td></tr>';
    echo '</table>';
    echo '</form>';

    if ($selected_backup_log_file && file_exists($selected_backup_log_file)) {
        echo '<h2>Selected Backup Log Contents</h2>';
        echo '<pre style="background: #f1f1f1; padding: 15px; border: 1px solid #ccc; max-height: 500px; overflow-y: scroll;">';
        echo esc_html(file_get_contents($selected_backup_log_file));
        echo '</pre>';
    } elseif (file_exists($current_log_file)) {
        echo '<h2>Current Log File Contents</h2>';
        echo '<pre style="background: #f1f1f1; padding: 15px; border: 1px solid #ccc; max-height: 500px; overflow-y: scroll;">';
        echo esc_html(file_get_contents($current_log_file));
        echo '</pre>';
    } else {
        echo '<p>No log file found.</p>';
    }

    echo '<h2>Download Current Log</h2>';
    echo '<a href="' . esc_url(admin_url('admin-ajax.php?action=download_log_file&log_file=' . urlencode($current_log_file))) . '" class="button button-primary">Download Log File</a>';
    echo '</div>';
}

add_action('wp_ajax_download_log_file', 'download_log_file');
function download_log_file() {
    $log_file = sanitize_text_field($_GET['log_file']);
    if (file_exists($log_file)) {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . basename($log_file) . '"');
        readfile($log_file);
        exit;
    }
    wp_die('Log file not found.');
}

function custom_log($message, $type = 'info') {
    $logging_enabled = get_option('custom_logging_enabled', true);
    $log_level = get_option('custom_log_level', 'all');

    if (!$logging_enabled || !should_log($type, $log_level)) {
        return false;
    }

    try {
        $log_dir = plugin_dir_path(__FILE__) . 'logs';
        $current_log_file = get_option('custom_log_file_path', $log_dir . '/plugin-debug.log');

        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }

        $timestamp = date('Y-m-d H:i:s');
        $formatted_message = sprintf("[%s] [%s]: %s%s", $timestamp, strtoupper($type), $message, PHP_EOL);

        return file_put_contents($current_log_file, $formatted_message, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        error_log('Custom Logging Plugin: ' . $e->getMessage());
        return false;
    }
}

function should_log($type, $log_level) {
    $log_levels = [
        'none' => [],
        'all' => ['error', 'warning', 'notice', 'info'],
        'error' => ['error'],
        'warning' => ['error', 'warning'],
        'notice' => ['error', 'warning', 'notice'],
        'info' => ['error', 'warning', 'notice', 'info']
    ];

    return in_array(strtolower($type), $log_levels[strtolower($log_level)]);
}