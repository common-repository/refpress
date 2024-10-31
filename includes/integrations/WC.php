<?php
/**
 * WooCommerce Integration class
 */

namespace RefPress\Includes\Integrations;

defined( 'ABSPATH' ) || exit;

class WC {

	public function __construct() {
		//Add RefPress settings data tab
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'product_tab' ], 20 );
		add_action( 'woocommerce_product_data_panels', [ $this, 'render_product_tab' ] );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'add_commission' ] );
		add_filter( 'manage_shop_order_posts_columns', [ $this, 'affiliate_column' ], 20 );
		add_action( 'manage_shop_order_posts_custom_column', [ $this, 'render_affiliate_column' ], 10, 2 );
		//Update the commission status when woocommerce status changed
		add_action( 'woocommerce_order_status_changed', [ $this, 'commission_data_status_change' ], 10, 3 );
	}

	public function product_tab( $tabs ) {

		$tabs['refpress_tab'] = array(
			'label'    => __( 'RefPress', 'refpress' ),
			'priority' => 20,
			'target' => 'refpress_product_tab',
		);

		return $tabs;
	}

	public function render_product_tab(){
		do_action( 'refpress_wc_product_panel_before' );

		?>
        <div id="refpress_product_tab" class="panel woocommerce_options_panel">

			<?php
			$pro_text = '<div class="refpress-get-pro-text-wrap">';
			$pro_text .= '<h4> ' . __( 'Get the pro version to have more controls', 'refpress' ) . ' </h4>';
			$pro_text .= '</div>';

			echo apply_filters( 'refpress_get_pro_text', $pro_text );

			do_action( 'refpress_wc_product_panel' ); ?>

        </div>
		<?php
		do_action( 'refpress_wc_product_panel_after' );
	}

	public function affiliate_column( $columns ){
		$columns[ 'refpress_affiliate' ] = __( 'Affiliate', 'refpress' );

		return $columns;
	}

	public function render_affiliate_column( $column, $post_id ){
		global $wpdb;

		if ( $column === 'refpress_affiliate' ) {
			$commission = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}refpress_earnings WHERE order_id = {$post_id} AND process_by = 'woocommerce' " );

			if ( ! empty( $commission ) ) {
				$amount = wc_price( $commission->referer_amount );
				echo "<a href='admin.php?page=refpress-referrals&action=edit&earning_id={$commission->earning_id}'> {$amount} </a>";

			}

		}

	}


	public function add_commission( $order_id ) {
		$referer_id = refpress_get_cookie_referer_id();
		if ( empty( $referer_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( version_compare( WC()->version, '3.0.0', '>=' ) ) {
			$customer_email = $order->get_billing_email();
			$shipping_total = $order->get_shipping_total();
		} else {
			$customer_email = $order->billing_email;
			$shipping_total = $order->get_total_shipping();
		}

		$has_valid_referer = refpress_has_valid_referer( [ 'order_id' => $order_id, 'email' => $customer_email, 'order_processed_by' => 'woocommerce' ] );

		if ( ! $has_valid_referer ) {
			return;
		}

		$exclude_tax      = (bool) refpress_get_setting( 'exclude_tax' );
		$exclude_shipping = (bool) refpress_get_setting( 'exclude_shipping' );

		$base_amount = 0.00;

		if ( ! $exclude_tax ) {
			$shipping_total += $order->get_shipping_tax();
		}
		if ( $shipping_total > 0 && ! $exclude_shipping ) {
			$base_amount += $shipping_total;
		}

		$ordered_items = $order->get_items();
		foreach ( $ordered_items as $product ) {
			$product_id = $product['product_id'];

			$is_disabled = apply_filters( 'refpress_wc_disabled_specific_product_commission', false, $product_id );

			/**
			 * Check if commission is disabled for this product
			 */
			if ( $is_disabled ) {
				continue;
			}

			$product_total = $product['line_total'];

			if ( ! $exclude_tax ) {
				$product_total += $product['line_tax'];
			}

			$product_total = apply_filters( 'refpress_commission_wc_product_total', $product_total, $product_id, $order_id );

			$base_amount += $product_total;
		}

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
			$wc_customer_id = $order->get_user_id();

			$earning_data = [
				'referer_id'     => $referer_id,
				'customer_id'    => $wc_customer_id,
				'order_id'       => $order_id,
				'order_status'   => $order->get_status(),
				'referer_amount' => $referer_earning,
				'process_by'     => 'woocommerce',
			];

			refpress_add_referer_commission( $earning_data );
		}

	}


	/**
	 * Update Commission and set it to Referer Accounts
	 *
	 *
	 * @since RefPress 1.0.0
     *
	 */

	public function commission_data_status_change( $order_id, $status_from, $status_to ){
	    $earning_data = refpress_get_earning_by_order_id( $order_id );
	    if ( empty( $earning_data ) ) {
	        return;
	    }

	    refpress_earning_status_change( $earning_data->earning_id, $status_to );
	}

}