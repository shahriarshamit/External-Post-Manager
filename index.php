<?php

/*
  Plugin Name: External Post Manager
  Description: External Post Manager
  Version: 1.0.0
  Author: Khandakar Shahriar Shamit
  Text Domain: external-post-manager
 */

if (!defined('ABSPATH')) {
    exit();
}

if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}
$plugin_data = get_plugin_data(__FILE__);

define('EPM_FILE', __FILE__);
define('EPM_DS', DIRECTORY_SEPARATOR);
define('EPM_NAME', $plugin_data['Name']);
define('EPM_VER', $plugin_data['Version']);
define('EPM_SLUG', $plugin_data['TextDomain']);

define('EPM_PATH', plugin_dir_path(__FILE__));
define('EPM_PATH_ASSETS', EPM_PATH . 'assets' . EPM_DS);
define('EPM_PATH_LANGUAGE', EPM_PATH . 'languages' . EPM_DS);
define('EPM_PATH_INCLUDES', EPM_PATH . 'includes' . EPM_DS);
define('EPM_PATH_LOGS', EPM_PATH . 'logs' . EPM_DS);

require_once EPM_PATH_INCLUDES . 'endpoints.php';

add_action('rest_api_init', 'epm_rest_api_initialize');
