<?php
/**
 * Main App File
 */

namespace RefPress\Includes;

use RefPress\Includes\Admin\Bootstrap;
use RefPress\Includes\Admin\Payouts;
use RefPress\Includes\Admin\Permalinks;
use RefPress\Includes\Integrations\EDD;
use RefPress\Includes\Integrations\WC;

defined( 'ABSPATH' ) || exit;

class App {

	private $admin_bootstrap;
	private $ajax_actions;
	private $ref_accounts;
	private $permalinks;
	private $referral;
	private $payouts;

	/**
	 * Integration Modules
	 */

	private $wc;
	private $edd;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'load_resources' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_resources' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'loadLocalization' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'loadLocalization' ] );

		add_action('in_admin_header', [ $this, 'control_notice_in_refpress_page' ], 1000);
	}

	public function load_modules() {
		$this->admin_bootstrap = new Bootstrap();
		$this->ajax_actions    = new Ajax_Actions();
		$this->ref_accounts    = new Ref_Accounts();
		$this->permalinks      = new Permalinks();
		$this->referral = new Referral();
		$this->payouts = new Payouts();

		/**
		 * Load integration Module
		 */

		//Load WooCommerce Class

		$integrations = ( array ) refpress_get_setting( 'enabled_integrations' );
		$has_wc = in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );

		if ( in_array( 'woocommerce', $integrations ) && $has_wc ) {
			$this->wc = new WC();
		}

		$has_edd = in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );

		if ( in_array( 'edd', $integrations ) && $has_edd ) {
			$this->edd = new EDD();
		}

	}

	public function includes() {
		require_once REFPRESS_ABSPATH . 'includes/functions.php';
		require_once REFPRESS_ABSPATH . 'includes/functions-condition.php';
		require_once REFPRESS_ABSPATH . 'includes/functions-currency.php';
		require_once REFPRESS_ABSPATH . 'includes/functions-hooks.php';
		require_once REFPRESS_ABSPATH . 'includes/functions-misc.php';
		require_once REFPRESS_ABSPATH . 'includes/functions-request.php';
	}

	public function run() {
		do_action( 'refpress/run/before' );
		$this->includes();
		$this->load_modules();
		do_action( 'refpress/run/after' );

		//Making sure that the hooks loading when the application is ready to run
		$this->init_hooks();
	}

	public function init_hooks() {
		if ( ! empty( $_POST['refpress_action'] ) ) {
			$action = sanitize_text_field( $_POST['refpress_action'] );
			do_action( $action );
		}
	}

	public function load_resources( $page ) {

		$script_debug      = refpress_script_debug();
		$suffix            = $script_debug ? '' : '.min';
		$rtl_dir           = is_rtl() ? '-rtl' : '';
		$load_select2      = false;
		$script_dependency = [ 'jquery' ];

		if ( is_admin() ) {
			$load_select2 = true;
		}

		$load_select2 = apply_filters( 'refpress_load_select2', $load_select2 );
		if ( $load_select2 ) {
			wp_enqueue_style( 'refpress-select2', REFPRESS_URL . "public/libraries/select2/css/select2.min.css", [],
				REFPRESS_VERSION );
			wp_enqueue_script( 'refpress-select2', REFPRESS_URL . "public/libraries/select2/js/select2.min.js", [],
				REFPRESS_VERSION, true );
		}


		/**
		 * RefPress ChartJS Supported pages in Array
		 *
		 * @since RefPress 1.0.0
		 *
		 * @param array $screen_pages
		 * @param string $current_page
		 */

		$chartjs_supported_pages = apply_filters( 'refpress_chartjs_supported_pages', [ 'toplevel_page_refpress' ] );


		/**
		 * Tell WP if RefPress should load ChartJS
		 *
		 * @since RefPress 1.0.0
		 *
		 * @param bool
		 * @param string $current_page
		 */

		$load_chart_js = apply_filters( 'refpress_load_chart_js', in_array( $page, $chartjs_supported_pages ), $page );
		if ( $load_chart_js ) {
			wp_enqueue_script( 'refpress-chart.js', REFPRESS_URL . "public/libraries/Chart.js/Chart.bundle.min.js", [ 'refpress' ], REFPRESS_VERSION );
		}

		wp_enqueue_style( 'refpress', REFPRESS_URL . "public/css{$rtl_dir}/refpress{$suffix}.css", [], REFPRESS_VERSION );
		wp_enqueue_script( 'refpress', REFPRESS_URL . "public/js/refpress{$suffix}.js", $script_dependency, REFPRESS_VERSION,
			true );
	}


	public function loadLocalization() {
		$ref_param = refpress_referral_url_param();

		$strings = [
			'ajaxurl'              => admin_url( 'admin-ajax.php' ),
			'settings_changed_msg' => __( 'Settings have changed, you should save them!', 'refpress' ),
			'something_wrong_msg'  => __( 'Something went wrong, please try again later', 'refpress' ),
			'copied'               => __( 'Copied!', 'refpress' ),
			'ref_param'            => $ref_param,
			'current_user_id'      => get_current_user_id(),
			'searching_user'      => __( 'Searching user...', 'refpress' ),
			'currency_sign'      => refpress_get_currency_symbol(),
		];

		$strings['current_user'] = is_user_logged_in() ? get_current_user_id() : false;

		$localizeObject = apply_filters( 'refpress_localize_object', $strings );

		wp_localize_script( 'refpress', '_refpress', $localizeObject );
	}

	public function control_notice_in_refpress_page(){
		$page = refpress_get_input_text( 'page' );
		if ( ! in_array( $page, [ 'refpress', 'refpress-settings' ] ) ) {
			return;
		}

		remove_all_actions('network_admin_notices');
		remove_all_actions('user_admin_notices');
		remove_all_actions('admin_notices');
		remove_all_actions('all_admin_notices');
	}

}