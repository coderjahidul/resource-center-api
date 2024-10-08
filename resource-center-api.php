<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://github.com/coderjahidul/
 * @since             1.0.0
 * @package           Resource_Center_Api
 *
 * @wordpress-plugin
 * Plugin Name:       Resource Center API
 * Plugin URI:        https://https://github.com/coderjahidul/resource-center-api
 * Description:       Resource Center API Plugin for WordPress.
 * Version:           1.0.0
 * Author:            Jahidul islam Sabuz
 * Author URI:        https://https://github.com/coderjahidul//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       resource-center-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'RESOURCE_CENTER_API_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-resource-center-api-activator.php
 */
function activate_resource_center_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-resource-center-api-activator.php';
	Resource_Center_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-resource-center-api-deactivator.php
 */
function deactivate_resource_center_api() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-resource-center-api-deactivator.php';
	Resource_Center_Api_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_resource_center_api' );
register_deactivation_hook( __FILE__, 'deactivate_resource_center_api' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-resource-center-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_resource_center_api() {

	$plugin = new Resource_Center_Api();
	$plugin->run();

}
run_resource_center_api();
