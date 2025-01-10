# Custom WP Log

**Custom WP Log** is a WordPress plugin that provides an enhanced logging system with advanced functionality for managing log files. This plugin enables users to create, view, backup, and manage log files with ease.

---

## Plugin Details

- **Plugin Name:** Custom WP Log
- **Version:** 1.0
- **Requires WordPress Version:** 4.8 or higher
- **Requires PHP Version:** 7.4 or higher
- **Author:** Byron Jacobs
- **Author URI:** [https://heavyweightdigital.co.za](https://heavyweightdigital.co.za)
- **License:** GPL v2 or later
- **License URI:** [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Features

- Create and manage custom log files.
- View the current or backup log file content.
- Backup and delete log files.
- Set custom log levels (All, Errors, Warnings, Notices).
- Enable or disable logging dynamically.
- Supports both the default and custom log file names.
- Immediate updates when log files are backed up or deleted.
- REST API integration for advanced programmatic access (if required).

---

## Installation

1. Download the plugin files and upload them to your WordPress plugins directory (`/wp-content/plugins/`).
2. Activate the plugin through the WordPress Admin Plugins menu.
3. Navigate to the "Custom WP Log File" menu in the WordPress Admin Dashboard.

---

## Usage

### Admin Page

The plugin provides an easy-to-use interface accessible via the WordPress admin menu under **"Custom WP Log File"**. From here, you can:

1. **Set Log File Name:**
   - Specify a custom log file name or use the default `plugin-debug.log`.

2. **Log Actions:**
   - Test the log file with sample entries.
   - Clear the contents of the current log file.
   - Create a backup of the current log file.

3. **Manage Log Files:**
   - View available log files and delete selected files or all backup logs.

4. **View Logs:**
   - Select and view the contents of backup log files or the current log file.

5. **Logging Settings:**
   - Set log levels to filter specific types of logs (Errors, Warnings, Notices, or All).
   - Enable or disable logging dynamically.

6. **Download Logs:**
   - Download the current log file as a `.log` file.

---

## Developer Notes

- Ensure the `logs` directory is writable by the server (`/wp-content/plugins/custom-logs/logs`).
- Logs are stored in `.log` files and can be downloaded or managed from the admin interface.
- Error handling has been implemented to ensure secure and robust logging operations.

---

## License

This plugin is licensed under the GNU General Public License v2.0. See the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) file for details.

---

## Support

If you encounter any issues or have questions, feel free to reach out to:

- **Author Website:** [https://heavyweightdigital.co.za](https://heavyweightdigital.co.za)
- **Email:** info@heavyweightdigital.co.za