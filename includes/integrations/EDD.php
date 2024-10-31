<?php
/**
 * Easy Digital Downloads Integration Class
 */

namespace RefPress\Includes\Integrations;

defined( 'ABSPATH' ) || exit;

class EDD {

	public function __construct() {
	    if ( class_exists( 'EDD_Software_Licensing' ) ) {
		    add_filter( 'refpress_settings_integration', [ $this, 'settings_integration' ] );
	    }

		add_action( 'add_meta_boxes', [ $this, 'edd_add_download_meta_box' ] );
		add_action( 'save_post', [ $this, 'save_edd_metabox' ] );

		add_action( 'edd_insert_payment', [ $this, 'add_commission' ], 999 );
		add_action( 'edd_update_payment_status', [ $this, 'commission_status_change' ], 10, 3 );
		add_action( 'edd_payment_delete', [ $this, 'delete_commission_record' ] );

		//Payments Column
		add_filter( 'edd_payments_table_columns', [ $this, 'affiliate_column' ], 20 );
		add_action( 'edd_payments_table_column', [ $this, 'render_affiliate_column' ], 10, 3 );
	}

	public function settings_integration( $args ) {
		$args['form_fields']['edd'] = [
			'title'       => __( 'EDD', 'refpress' ),
			'description' => __( 'Settings depends on the EDD (Easy Digital Downloads)', 'refpress' ),

			'input_fields' => [
				'edd_disable_referrals_if_renewals' => [
					'type'        => 'checkbox',
					'label'       => __( 'Renewals', 'refpress' ),
					'title'       => __( 'Disable Referrals when payment is for renewals', 'refpress' ),
					'description' => __( 'Check if you would like to disable the commissions for referrals when payment is for renewal. RefPress will not disburse the commission on renewing purchase with EDD Software Licensing.',
						'refpress' ),
				],

				'edd_disable_referrals_if_upgrades' => [
					'type'        => 'checkbox',
					'label'       => __( 'Upgrades', 'refpress' ),
					'title'       => __( 'Disable Referrals when payment is for upgrades', 'refpress' ),
					'description' => __( 'Check if you would like to disable the commissions for referrals when payment is made for upgrades. RefPress will not disburse the commission on upgrades purchase with EDD Software Licensing.', 'refpress' ),
				],
			]

		];

		return $args;
	}

	public function edd_add_download_meta_box() {
		$post_types = apply_filters( 'edd_download_metabox_post_types', [ 'download' ] );

		foreach ( $post_types as $post_type ) {
			add_meta_box( 'edd_refpress_metabox', __( 'RefPress Affiliate', 'refpress' ), [ $this, 'render_refpress_metabox' ], $post_type, 'normal', 'high' );
		}

	}

	public function render_refpress_metabox(){
	    $post_ID = get_the_ID();

		if ( ! refpress_has_pro() ) {
			?>
			<div class="refpress-get-pro-text-wrap">
				<h4> <?php _e( 'Get the pro version to have the following features', 'refpress' ); ?> </h4>
				<ul>
					<li> ✅ <?php _e( 'Disable Commission for the specific downloads', 'refpress' ); ?> </li>
				</ul>
			</div>
			<?php
		}

		wp_nonce_field( 'edd_refpress_metabox', 'edd_refpress_metabox' );

		do_action( 'edd_refpress_metabox', $post_ID );
	}


	public function save_edd_metabox( $postID ){
		if ( ! isset( $_POST['edd_refpress_metabox'] ) || ! wp_verify_nonce( $_POST['edd_refpress_metabox'], 'edd_refpress_metabox' ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $postID ) ) || is_int( wp_is_post_autosave( $postID ) ) ) {
			return;
		}

		// Check user has permission to edit.
		if ( ! current_user_can( 'edit_post', $postID ) ) {
			return;
		}

		do_action( 'save_edd_refpress_metabox', $postID );
	}

	public function add_commission( $paymentID ){
		$referer_id = refpress_get_cookie_referer_id();
		if ( empty( $referer_id ) ) {
			return;
		}

		$payment = edd_get_payment( $paymentID );
		$customer_email = edd_get_payment_user_email( $paymentID );

		$has_valid_referer = refpress_has_valid_referer( [ 'order_id' => $paymentID, 'email' => $customer_email, 'order_processed_by' => 'edd' ] );

		if ( ! $has_valid_referer || ! $this->edd_software_license_validation( $paymentID ) ) {
			return;
		}

		$exclude_tax      = (bool) refpress_get_setting( 'exclude_tax' );
		$exclude_shipping = (bool) refpress_get_setting( 'exclude_shipping' );

		$base_amount = 0;

		if ( $exclude_tax ) {
			$base_amount = $payment->subtotal;
		} else {
			$base_amount = floatval( $payment->total );
		}

		$log_data = [
			'order_id'           => $paymentID,
			'order_processed_by' => 'edd',
			'log_type'           => 'UNKNOWN',
			'log_text'           => '',
		];

		if ( $base_amount < 1) {
			$log_data['log_type'] = 'ZERO_AMOUNT';
			$log_data['log_text'] = __( 'Referral amount could not recorded due to zero amount', 'refpress' );
			refpress_write_log( $log_data );

			return;
		}


		/**
		 * Now calculate the commissions
		 */




		//Deduct Charges, if any
		$exclude_other_charge = (bool) refpress_get_setting( 'exclude_other_charge' );
		if ( $exclude_other_charge && $base_amount > 0 ) {
			$other_charge_amount = refpress_get_setting( 'other_charge_amount' );

			if ( $other_charge_amount > 0 ) {
				$other_charge_type = refpress_get_setting( 'other_charge_type' );

				if ( $other_charge_type === 'fixed' ) {
					$base_amount -= $other_charge_amount;
				} elseif ( $other_charge_type === 'percent' ) {
					$charge_percent_amount = ( $base_amount * $other_charge_amount ) / 100;
					$base_amount -= $charge_percent_amount;
				}
			}
		}

		//Calculate the commission
		$account     = refpress_get_account_by_id( $referer_id );
		$commission = refpress_get_commission_rate( $account );

		$commission_rate = $commission->commission_rate;
		$commission_rate_type = $commission->commission_rate_type;

		if ( $commission_rate <= 0 || $base_amount <= 0 ) {
			//There is nothing to calculate...
			return;
		}

		//Get the referer amount

		$referer_earning = '0.00';

		if ( $commission_rate_type === 'percent' ) {
			$referer_earning = ( $base_amount * $commission_rate ) / 100;
		} elseif ( ( $commission_rate_type === 'fixed' ) && ( $base_amount > $commission_rate ) ) {
			$referer_earning = $commission_rate;
		}

		if ( $referer_earning > 0 ) {

			$earning_data = [
				'referer_id'     => $referer_id,
				'customer_id'    => $payment->customer_id,
				'order_id'       => $paymentID,
				'order_status'   => ( $payment->status === 'publish' ) ? 'completed' : $payment->status,
				'referer_amount' => $referer_earning,
				'process_by'     => 'edd',
			];

			refpress_add_referer_commission( $earning_data );
		}

	}


	/**
	 * Validate EDD Payment if made with EDD Software Licensing
	 *
	 *
	 * @since RefPress 1.0.0
	 *
	 * @param $paymentID
	 *
	 * @return bool
	 */

	public function edd_software_license_validation( $paymentID ){
	    //Check if the payment is made for upgrades

		if ( refpress_get_setting( 'edd_disable_referrals_if_upgrades' ) ) {
			$cart_downloads = edd_get_cart_contents();

			if ( is_array( $cart_downloads ) && count( $cart_downloads ) ) {
				foreach ( $cart_downloads as $download ) {
					if ( ! empty( $download['options']['is_upgrade'] ) ) {
						return false;
					}
				}
			}
		}

		//Check if the payment is renewals

		if ( refpress_get_setting( 'edd_disable_referrals_if_renewals' ) ) {
			if ( get_post_meta( $paymentID, '_edd_sl_is_renewal', true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Update the commission status when related EDD payment status has been changed.
	 *
	 *
	 * @since RefPress 1.0.0
	 *
	 *
	 * @param int $payment_id EDD Payment ID
	 * @param string $new_status EDD Payment new status
	 * @param string $old_status EDD payment old status
	 */

	public function commission_status_change( $payment_id, $new_status, $old_status ) {
		$status = $new_status;
		if ( $new_status == 'publish' || $new_status == 'complete' ) {
			$status = 'completed';
		}

		global $wpdb;

		$related_earning = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}refpress_earnings WHERE order_id = {$payment_id} AND process_by = 'edd' " );

		do_action( 'refpress_earnings_pre_update_status', $related_earning, $status, $old_status );

		$wpdb->update( $wpdb->prefix . 'refpress_earnings', [ 'order_status' => $status ], [ 'order_id' => $payment_id, 'process_by' => 'edd' ] );

		do_action( 'refpress_earnings_updated_status', $related_earning, $status, $old_status );
	}

	/**
	 * Delete commission record when delete related Payment from the EDD
	 *
	 * @since RefPress 1.0.0
	 *
	 * @param $payment_id
	 */

	public function delete_commission_record( $payment_id ){
	    global $wpdb;

		$wpdb->delete( $wpdb->prefix . 'refpress_earnings', [ 'order_id' => $payment_id, 'process_by' => 'edd' ] );

		$log_data = [
			'order_id'           => $payment_id,
			'order_processed_by' => 'edd',
			'log_type'           => 'DELETED',
			'log_text'           => __( 'Referral earning has been deleted as related EDD payment has been deleted.', 'refpress' ),
		];

		refpress_write_log( $log_data );
	}


	public function affiliate_column( $columns ){
		$columns[ 'refpress_affiliate' ] = __( 'Affiliate', 'refpress' );

		return $columns;
	}


	public function render_affiliate_column( $value, $payment_ID, $column_name ){
		global $wpdb;

		if ( $column_name === 'refpress_affiliate' ) {
			$commission = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}refpress_earnings WHERE order_id = {$payment_ID} AND process_by = 'edd' " );

			if ( ! empty( $commission ) ) {

				$amount   = edd_currency_filter( edd_format_amount( $commission->referer_amount ), edd_get_payment_currency_code( $payment_ID ) );

				$value = "<a href='admin.php?page=refpress-referrals&action=edit&earning_id={$commission->earning_id}'> {$amount} </a>";
			} else {
			    $value = '-';
			}
		}

		return $value;
	}

}