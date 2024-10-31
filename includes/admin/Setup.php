<?php
/**
 * Bootstrap class for admin
 */

namespace RefPress\Includes\Admin;

defined( 'ABSPATH' ) || exit;

class Setup {

	public static function instance() {
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new self();
			$instance->install();
		}

		// Always return the instance
		return $instance;
	}


	public function install(){
		$version = get_option( 'refpress_version' );

		if ( ! $version ) {
			$this->create_db_tables();
			$this->default_settings();
		}

		update_option( 'refpress_version', REFPRESS_VERSION );
	}

	public function create_db_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		/**
		 * Create tables
		 */
		$quiz_options_table = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}refpress_accounts (
		  	account_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			user_id int(11) DEFAULT NULL,
			nicename varchar(100) DEFAULT NULL,
			fullname varchar(191) DEFAULT NULL,
			country varchar(10) DEFAULT NULL,
			enable_custom_commission_rate tinyint(1) NOT NULL DEFAULT '0',
			commission_rate decimal(8,2) DEFAULT NULL,			
			commission_rate_type varchar(30) DEFAULT NULL,
  			earned decimal(16,2) DEFAULT '0.00',
  			paid decimal(16,2) DEFAULT '0.00',
			converted int(11) DEFAULT '0',
			visits int(20) DEFAULT '0',
  			status varchar(20) DEFAULT NULL,
  			payout_method varchar(100) DEFAULT NULL,
  			payout_method_fields text DEFAULT NULL,
  			promotional_strategies text DEFAULT NULL,
  			promotional_properties text DEFAULT NULL,
  			users_registered int(11) DEFAULT 0,
  			created_at timestamp NULL DEFAULT NULL,
			KEY  user_id (user_id),
			KEY  nicename (nicename),
			KEY  earned (earned),
			KEY  paid (paid),
			KEY  converted (converted),
			KEY  visits (visits),
			KEY  status (status),
			KEY  users_registered (users_registered)
		) {$charset_collate};";

		dbDelta( $quiz_options_table );

		/**
		 * Traffics Table
		 */

		$quiz_options_table = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}refpress_traffics (
		  	traffic_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			referer_id int(11) DEFAULT NULL,
			referer_url text DEFAULT NULL,
			campaign varchar (100) DEFAULT NULL,
			ip_address varchar(70) DEFAULT NULL,			
			hits int(11) DEFAULT 1 ,
			converted int(11) DEFAULT 0,
			users_registered int(11) DEFAULT 0,
  			created_at timestamp NULL DEFAULT NULL,
			KEY  referer_id (referer_id),
			KEY  campaign (campaign),
			KEY  ip_address (ip_address),
			KEY  hits (hits),
			KEY  converted (converted),
			KEY  users_registered (users_registered),
			KEY  created_at (created_at)
		) {$charset_collate};";

		dbDelta( $quiz_options_table );

		/**
		 * Earn Commission
		 */

		$earning_table = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}refpress_earnings (
			earning_id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			referer_id int(11) unsigned DEFAULT NULL,
			traffic_id int(11) unsigned DEFAULT NULL,
			customer_id int(11) unsigned DEFAULT NULL,
			customer_ip varchar(70) DEFAULT NULL,
			order_id int(11) unsigned DEFAULT NULL,
			order_status varchar(50) DEFAULT NULL,
			referer_amount decimal(16,2) DEFAULT NULL,
			process_by varchar(20) DEFAULT NULL,
  			created_at timestamp NULL DEFAULT NULL,
            KEY  referer_id (referer_id),
            KEY  traffic_id (traffic_id),
            KEY  customer_id (customer_id),
            KEY  customer_ip (customer_ip),
            KEY  order_id (order_id),
            KEY  order_status (order_status),
            KEY  created_at (created_at)
		) {$charset_collate};";

		dbDelta( $earning_table );

		//Create logs table
		$earning_table = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}refpress_logs (
			log_id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			referer_id int(11) unsigned DEFAULT NULL,
			referer_wp_user_id int(11) unsigned DEFAULT NULL,
			order_id int(11) unsigned DEFAULT NULL,
			log_type varchar(50) DEFAULT NULL,
			log_text text DEFAULT NULL,
			order_processed_by varchar(30) DEFAULT NULL,
  			created_at timestamp NULL DEFAULT NULL,
            KEY  referer_id (referer_id),
            KEY  referer_wp_user_id (referer_wp_user_id),
            KEY  order_id (order_id),
            KEY  log_type (log_type),
            KEY  order_processed_by (order_processed_by),
            KEY  created_at (created_at)
		) {$charset_collate};";

		dbDelta( $earning_table );

		/**
		 * Create Payout Table
		 */

		$withdraws_table = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}refpress_payouts (
			payout_id bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			referer_id int(11) DEFAULT NULL,
			wp_user_id int(11) DEFAULT NULL,
			amount decimal(16,2) DEFAULT NULL,
			status varchar(50) DEFAULT NULL,
  			payout_method_id varchar(100) DEFAULT NULL,
  			payout_method_data text DEFAULT NULL,
			created_at datetime DEFAULT NULL,
            KEY  referer_id (referer_id),
            KEY  wp_user_id (wp_user_id),
            KEY  amount (amount),
            KEY  status (status),
            KEY  created_at (created_at)
		) {$charset_collate};";

		dbDelta( $withdraws_table );
	}

	public function default_settings() {
		if ( ! function_exists( 'refpress_update_setting' ) ) {
			require_once REFPRESS_ABSPATH . 'includes/functions.php';
		}

		$settings = [
			'delete_on_uninstall'    => '1',
			'currency'               => 'USD',
			'currency_position'      => 'left',
			'thousand_separator'     => ',',
			'decimal_separator'      => '.',
			'number_of_decimal'      => '0',
			'referral_url_parameter' => 'ref',
			'cookie_expire_in_days'  => '30',
			'commission_rate'        => '30',
			'commission_rate_type'   => 'percent',
			'minimum_payout_amount'  => '100',
			'payout_locking_days'    => '30',
		];

		update_option('refpress_settings', $settings);

		$this->create_pages();
	}

	public function create_pages() {
		$dashboard_args    = array(
			'post_title'   => __( 'Affiliate Dashboard', 'refpress' ),
			'post_content' => '',
			'post_type'    => 'page',
			'post_status'  => 'publish',
		);
		$dashboard_page_id = wp_insert_post( $dashboard_args );
		refpress_update_setting( 'refpress_dashboard_page_id', $dashboard_page_id );
	}


}