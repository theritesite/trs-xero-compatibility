<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.theritesites.com
 * @since             1.0.0
 * @package           TRS_XC
 *
 * @wordpress-plugin
 * Plugin Name:       TRS Xero Compatibility
 * Plugin URI:        https://www.theritesites.com/plugins/xero-compatibility
 * Description:       Having issues with your xero connection being interrupted? Find which orders might not be synced and resend them!
 * Version:           0.9.0
 * Author:            TheRiteSites
 * Author URI:        https://www.theritesites.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       trs-xero-compatibility
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'TRS_XC_VERSION', '0.9.0' );

define( 'TRS_XC_UPDATER_URL', 'https://www.theritesites.com' );

define( 'TRS_XC_ITEM_ID', 0000 );

define( 'TRS_XC_LICENSE_PAGE', 'the_rite_plugins_settings' );

define( 'TRS_XC_ITEM_NAME', 'TRS Xero Compatibility' );

define( 'TRS_XC_LICENSE_KEY', 'trs_xero_compatibility_license_key' );

define( 'TRS_XC_LICENSE_STATUS', 'trs_xero_compatibility_license_status' );

if ( file_exists( __DIR__ . '/cmb2/init.php' ) ) {
	require_once __DIR__ . '/cmb2/init.php';
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-trs-xc-activator.php
 */
function activate_trs_xc() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trs-xc-activator.php';
	TRS_XC_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-trs-xc-deactivator.php
 */
function deactivate_trs_xc() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-trs-xc-deactivator.php';
	TRS_XC_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_trs_xc' );
register_deactivation_hook( __FILE__, 'deactivate_trs_xc' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-trs-xc.php';


/**
 * Inits updater class to talk to https://www.theritesites.com for updates
 * 
 * @since 1.0.0
 */
function trs_trs_xc_update_check() {

	if( !class_exists( 'TRS_XC_Settings' ) ) {
		// load our custom updater
		include( plugin_dir_path( __FILE__ ) . '/includes/class-trs-xc-settings.php' );
	}

	if( !class_exists( 'TRS_XC_Plugin_Updater' ) ) {
		// load our custom updater
		include( plugin_dir_path( __FILE__ ) . '/includes/class-trs-xc-plugin-updater.php' );
	}

	if( class_exists( 'TRS_XC_Settings' ) ) {
		$license_key = TRS_XC_Settings::get_value(TRS_XC_LICENSE_KEY);
	}
	
	else {
		$opts = trim(get_option('the_rite_plugins_settings', false));

		$key = TRS_XC_LICENSE_KEY;
		if ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
			$license_key = $opts[ $key ];
		}
	}
	
	if( !class_exists( 'TRS_XC_Plugin_Updater' ) ) {
		return;
	}

	$plugin_updater = new TRS_XC_Plugin_Updater( TRS_XC_UPDATER_URL, __FILE__, array(
						'version'	=> TRS_XC_VERSION,
						'license'	=> $license_key,
						'item_id'	=> TRS_XC_ITEM_ID,
						'author'	=> 'TheRiteSites',
						'url'		=> home_url()
			)
	);

}
add_action( 'plugins_loaded', 'trs_trs_xc_update_check');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_trs_xc() {

	$plugin = new TRS_XC();
	$plugin->run();

}
run_trs_xc();
