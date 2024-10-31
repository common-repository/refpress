<?php
/**
 * Payouts class for admin
 */

namespace RefPress\Includes\Admin;

defined( 'ABSPATH' ) || exit;

class Payouts {

	public function __construct() {
		//Show Payout Admin Form
		add_action( 'refpress_settings/field_group/after/payouts/payout_methods', [ $this, 'configure_payouts' ] );

		//Save Pyout Settings
		add_action( 'refpress_after_save_settings', [ $this, 'save_payout_settings' ] );

		add_refpress_action( 'user_save_payout_settings', [ $this, 'user_save_payout_settings' ] );
	}

	public function configure_payouts() {
		include REFPRESS_ABSPATH . 'includes/admin/payout/configure.php';
	}

	/**
	 * Save RefPress Settings
	 *
	 *
	 * @since RefPress 1.0.0
	 *
	 */

	public function save_payout_settings() {
		$payout_settings = (array) refpress_get_array_input_field( 'refpress_payout_settings' );
		update_option( 'refpress_payout_settings', $payout_settings );
	}


	public function user_save_payout_settings() {
		refpress_verify_nonce();

		$ref_account = refpress_get_account();

		if ( ! $ref_account ) {
			return;
		}

		$fullname             = refpress_get_input_text( 'fullname' );
		$country              = refpress_get_input_text( 'country' );
		$method               = refpress_get_input_text( 'payout_method' );
		$payout_method_fields = serialize( refpress_get_array_input_field( 'payout_method_fields' ) );

		$user_data = [
			'fullname'             => $fullname,
			'country'              => $country,
			'payout_method'        => $method,
			'payout_method_fields' => $payout_method_fields,
		];

		global $wpdb;
		refpress_db_update( $wpdb->prefix . 'refpress_accounts', $user_data,
			[ 'account_id' => $ref_account->account_id ] );

		refpress_redirect();
	}

}