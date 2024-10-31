<?php
/**
 * Bootstrap class for admin
 */

namespace RefPress\Includes\Admin;

defined( 'ABSPATH' ) || exit;

class Bootstrap {

	public function __construct(){
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
		add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
	}

	public function register_admin_menu(){
		$pending_account = refpress_pending_account_count();
		$pending_account_i18n = number_format_i18n( $pending_account );

		$refpress_menu_title = sprintf( __( 'RefPress %s', 'refpress' ), "<span class='pending-account-count count-{$pending_account}'>{$pending_account_i18n}</span>" );

		add_menu_page( __( 'RefPress', 'refpress' ), $refpress_menu_title, 'manage_options', 'refpress', [ $this, 'admin_home' ], 'dashicons-networking', 20 );

		do_action( 'refpress_admin_menu_first_level' );

		add_submenu_page( 'refpress', __( 'Accounts', 'refpress' ), __( 'Accounts', 'refpress' ), 'manage_options', 'refpress-accounts', [ $this, 'accounts' ] );

		do_action( 'refpress_admin_menu_second_level' );

		add_submenu_page( 'refpress', __( 'Referral Earnings', 'refpress' ), __( 'Referral Earnings', 'refpress' ), 'manage_options', 'refpress-referrals', [ $this, 'referrals' ] );
		add_submenu_page( 'refpress', __( 'Payout History', 'refpress' ), __( 'Payout History', 'refpress' ), 'manage_options', 'refpress-payout-history', [ $this, 'payout_history' ] );

		add_submenu_page( 'refpress', __( 'All Traffic', 'refpress' ), __( 'All Traffic', 'refpress' ), 'manage_options', 'refpress-traffic', [ $this, 'traffic' ] );

		do_action( 'refpress_admin_menu_third_level' );

		add_submenu_page( 'refpress', __( 'Settings', 'refpress' ), __( 'Settings', 'refpress' ), 'manage_options', 'refpress-settings', [ $this, 'settings' ] );

		do_action( 'refpress_admin_menu_final_level' );
	}

	public function admin_home(){
		include REFPRESS_ABSPATH . 'includes/admin/overview.php';
	}

	public function accounts(){
		$accounts_page = apply_filters( 'refpress_admin_accounts_page', REFPRESS_ABSPATH . 'includes/admin/accounts/list.php' );

		$sub_page = refpress_get_input_text( 'sub_page' );
		if ( ! empty( $sub_page ) ) {
			$accounts_page = apply_filters( 'refpress_admin_accounts_page', REFPRESS_ABSPATH . "includes/admin/accounts/{$sub_page}.php" );
		}

		require $accounts_page;
	}

	public function referrals(){
		$commission_page = apply_filters( 'refpress_admin_referrals_list_page', REFPRESS_ABSPATH . 'includes/admin/accounts/referrals.php' );

		require $commission_page;
	}

	public function payout_history(){
		$payout_history_page = apply_filters( 'refpress_admin_payout_history_page', REFPRESS_ABSPATH . 'includes/admin/payout/payouts.php' );

		require $payout_history_page;
	}

	public function traffic(){
		require REFPRESS_ABSPATH . 'includes/admin/traffic/traffic.php';
	}

	public function settings(){
		$settings = new Settings();
		$settings->generate();
	}

	public function display_post_states( $post_states, $post ) {
		if ( refpress_get_dashboard_page_id() === $post->ID ) {
			$post_states['refpress_lounge'] = __( 'RefPress Account Page', 'refpress' );
		}

		return $post_states;
	}

}