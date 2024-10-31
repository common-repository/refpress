<?php
/**
 * Referral class
 */

namespace RefPress\Includes;

defined( 'ABSPATH' ) || exit;

class Referral {

	public function __construct() {
		add_action( 'template_redirect', [ $this, 'track_referral' ] );
	}

	public function track_referral() {
		global $wpdb;

		$ref_param = refpress_referral_url_param();
		$ref_id    = refpress_get_input_text( $ref_param );
		if ( empty( $ref_id ) ) {

			//Mark the visitor as organic
			$referer_id = refpress_get_cookie_referer_id();
			if ( empty( $referer_id ) && empty( $_COOKIE[ 'refpress_visited' ] ) ) {
				setcookie( 'refpress_visited', current_time( 'timestamp' ), current_time( 'timestamp' ) + ( 365 * ( 3600 * 24 ) ) );
			}

			return;
		}

		$ref_account = refpress_get_account_by_id( $ref_id );

		if ( empty( $ref_account ) || ! refpress_is_account_approved( $ref_account ) ) {
			return;
		}

		$cookie_days         = (int) refpress_get_setting( 'cookie_expire_in_days' );
		$credit_last_referer = (bool) refpress_get_setting( 'credit_last_referer' );
		$cookie_time         = current_time( 'timestamp' ) + ( $cookie_days * ( 3600 * 24 ) );
		$has_cookie          = sanitize_text_field( $_COOKIE[ 'refpress_referer' ] );

		$campaign    = refpress_get_input_text( 'campaign' );
		$campaign    = ! empty( $campaign ) ? $campaign : '';
		$referer_url = ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';

		$get_traffic = refpress_get_traffic( $ref_id, [
			'referer_url' => $referer_url,
			'campaign'    => $campaign,
			//'ip_address'  => refpress_get_ip_address(),
		] );

		if ( empty( $get_traffic ) ) {
			$traffic_data = [
				'referer_id'  => $ref_id,
				'referer_url' => $referer_url,
				'campaign'    => $campaign,
				'ip_address'  => refpress_get_ip_address(),
				'hits'        => 1,
				'created_at'  => current_time( 'mysql' ),
			];

			$wpdb->insert( $wpdb->prefix . "refpress_traffics", $traffic_data );
			$traffic_id = $wpdb->insert_id;
		} else {
			$traffic_id = $get_traffic->traffic_id;

			$wpdb->update( $wpdb->prefix . "refpress_traffics", [ 'hits' => $get_traffic->hits + 1 ],
				[ 'traffic_id' => $traffic_id ] );
		}

		/**
		 * Set to cookie..
		 */

		if ( ! $has_cookie || $credit_last_referer ) {
			//Set referer
			do_action( 'refpress_set_referer_cookie_before' );

			setcookie( 'refpress_referer', $ref_id, $cookie_time );
			setcookie( 'refpress_traffic_id', $traffic_id, $cookie_time );

			do_action( 'refpress_set_referer_cookie_after' );
		}

	}


}