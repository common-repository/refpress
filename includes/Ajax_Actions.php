<?php
/**
 * Ajax Actions Class
 */

namespace RefPress\Includes;

defined( 'ABSPATH' ) || exit;

class Ajax_Actions {

	public function __construct() {
		add_action( 'wp_ajax_refpress_save_settings', [ $this, 'refpress_save_settings' ] );
	}

	/**
	 * Save RefPress settings
	 *
	 *
	 * @since RefPress 1.0.0
	 */

	public function refpress_save_settings(){
		do_action( 'refpress_before_save_settings' );
		$refpress_settings = refpress_get_array_input_field( 'refpress_settings' );
		update_option( 'refpress_settings', $refpress_settings );
		do_action( 'refpress_after_save_settings' );

		wp_send_json_success();
	}
}