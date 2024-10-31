<?php
/**
 * Plugin Name: RefPress
 * Plugin URI: https://www.themeqx.com/refpress/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Description: Easiest way to start affiliate marketing campaign.
 * Author: RefPress Team
 * Author URI: https://www.themeqx.com/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Version: 1.0.0
 * Requires at least: 4.7
 * Tested up to: 5.7
 * License: GPLv2 or later
 * Text Domain: refpress
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'REFPRESS_FILE' ) ) {
	define( 'REFPRESS_FILE', __FILE__ );
}
if ( ! defined( 'REFPRESS_ABSPATH' ) ) {
	define( 'REFPRESS_ABSPATH', trailingslashit( dirname( REFPRESS_FILE ) )  );
}
if ( ! defined( 'REFPRESS_VERSION' ) ) {
	define( 'REFPRESS_VERSION', '1.0.0' );
}
if ( ! defined( 'REFPRESS_URL' ) ) {
	define( 'REFPRESS_URL', trailingslashit( plugin_dir_url( REFPRESS_FILE ) ) );
}
if ( ! defined('REFPRESS_NONCE')){
	define('REFPRESS_NONCE', '_wpnonce');
}

if ( ! defined('REFPRESS_NONCE_ACTION')){
	define('REFPRESS_NONCE_ACTION', 'rp_nonce_action');
}

/**
 * Loading Text Domain
 */

load_plugin_textdomain( 'refpress', false, basename( dirname( __FILE__ ) ) . '/languages' );


/**
 * Load Main application instance
 */

require_once __DIR__ . '/Autoloader.php';

register_activation_hook( REFPRESS_FILE, [ '\RefPress\Includes\Admin\Setup', 'instance' ] );

function refpress(){
	$app = new \RefPress\Includes\App();
	$app->run(); //Kick Start RefPress App
}

add_action( 'init', 'refpress', 9 );