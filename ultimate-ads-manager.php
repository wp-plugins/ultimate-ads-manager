<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://codeneric.com
 * @since             1.0.0
 * @package           Ultimate_Ads_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Ultimate Ads Manager
 * Plugin URI:        http://codeneric.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Codeneric
 * Author URI:        http://codeneric.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ultimate-ads-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ultimate-ads-manager-activator.php
 */
function activate_ultimate_ads_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-ads-manager-activator.php';
	Ultimate_Ads_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ultimate-ads-manager-deactivator.php
 */
function deactivate_ultimate_ads_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-ads-manager-deactivator.php';
	Ultimate_Ads_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ultimate_ads_manager' );
register_deactivation_hook( __FILE__, 'deactivate_ultimate_ads_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-ads-manager.php';


function cc_ads_manage_author_admin_init() {
	require plugin_dir_path( __FILE__ ) . 'includes/DBUpdater.php';
	\cc_ads_manage\DBUpdater::updateDB();

}
add_action( 'admin_init', 'cc_ads_manage_author_admin_init' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ultimate_ads_manager() {

	$plugin = new Ultimate_Ads_Manager();
	$plugin->run();

}

run_ultimate_ads_manager();
