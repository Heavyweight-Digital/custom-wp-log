<?php
if (!defined('ABSPATH')) {
    die;
}

/**
 * Custom logging function
 * @param string $message The message to log
 * @param string $type The type of log entry (info, error, debug)
 * @return bool
 */
function custom_log($message, $type = 'info') {

    // Only proceed if logging is enabled
    if (!defined('LOG') || !LOG) {
        return false;
    }
    
    try {
        // Define paths for plugin and wp-content log files
        $plugin_dir = plugin_dir_path(__FILE__) . 'logs';
        $plugin_log_file = $plugin_dir . '/plugin-debug.log'; // #added [2024-11-14 12:00 PM]
        $wp_content_log_file = WP_CONTENT_DIR . '/custom-log.txt'; // #added [2024-11-14 12:00 PM]

        // Ensure plugin logs directory exists
        if (!file_exists($plugin_dir)) {
            wp_mkdir_p($plugin_dir);
            // Create .htaccess to protect plugin log files
            file_put_contents($plugin_dir . '/.htaccess', 'deny from all'); // #added [2024-11-14 12:00 PM]
        }

        // Format the log message
        $timestamp = current_time('mysql');
        $formatted_message = sprintf(
            "[%s] [%s]: %s%s",
            $timestamp,
            strtoupper($type),
            $message,
            PHP_EOL
        );

        // Write to plugin log file
        if (file_put_contents($plugin_log_file, $formatted_message, FILE_APPEND | LOCK_EX) === false) {
            error_log('Failed to write to plugin log file: ' . $plugin_log_file);
        }

        // Write to wp-content log file
        if (file_put_contents($wp_content_log_file, $formatted_message, FILE_APPEND | LOCK_EX) === false) {
            error_log('Failed to write to wp-content log file: ' . $wp_content_log_file);
        }

        return true;
    } catch (Exception $e) {
        error_log('Error in custom_log function: ' . $e->getMessage());
        return false;
    }
}

/**
 * Clear the plugin log file
 * @return bool
 */
function clear_custom_log() {
    $plugin_log_file = plugin_dir_path(__FILE__) . 'logs/plugin-debug.log'; // #updated [2024-11-14 12:00 PM]
    
    if (file_exists($plugin_log_file)) {
        return unlink($plugin_log_file);
    }
    
    return true;
}

/**
 * Get the contents of the plugin log file
 * @return string|bool
 */
function get_custom_log() {
    $plugin_log_file = plugin_dir_path(__FILE__) . 'logs/plugin-debug.log'; // #updated [2024-11-14 12:00 PM]
    
    if (file_exists($plugin_log_file)) {
        return file_get_contents($plugin_log_file);
    }
    
    return false;
}