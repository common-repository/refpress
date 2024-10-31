<?php
/**
 * Ajax Actions Class
 */

namespace RefPress\Includes;

defined( 'ABSPATH' ) || exit;

class Ref_Accounts {

	public function __construct() {

		add_refpress_action( 'user_login', [ $this, 'user_login' ] );
		add_refpress_action( 'user_register', [ $this, 'user_register' ] );
		add_refpress_action( 'logged_user_join_request', [ $this, 'logged_user_join_request' ] );
		add_action( 'refpress_user_register_after', [ $this, 'create_account_after_register' ] );
		add_action( 'template_redirect', [ $this, 'logout' ] );

		//Referred Users
		add_action( 'user_register', [ $this, 'action_user_register' ] );

		//Payout Page
		add_filter( 'refpress_admin_accounts_page', [  $this, 'payout_page' ] );
		add_refpress_action( 'pay_to_referer', [ $this, 'pay_to_referer' ] );
	}

	public function user_login() {
		refpress_verify_nonce();

		$config = apply_filters( 'refpress_user_login_form_validation_rules', [
			[
				'field' => 'log',
				'label' => __( 'Username or Email Address', 'refpress' ),
				'rules' => 'required',
			],
			[
				'field' => 'pwd',
				'label' => __( 'Password', 'refpress' ),
				'rules' => 'required',
			]
		] );

		$validator = refpress_form_validate( $config, 'user_login' );

		/**
		 * If validator fail, stop script
		 */
		if ( ! $validator->success ) {
			return;
		}

		$username    = refpress_get_input_text( 'log' );
		$password    = refpress_get_input_text( 'pwd' );
		$redirect_to = apply_filters( 'refpress_login_redirect_url', refpress_get_input_text( 'redirect_to' ) );
		$errors      = [];

		try {
			$creds = array(
				'user_login'    => trim( wp_unslash( $username ) ),
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				'user_password' => $password,
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				'remember'      => isset( $_POST['rememberme'] ),
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			);

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			// Perform the login.
			$user = wp_signon( apply_filters( 'refpress_login_credentials', $creds ), is_ssl() );

			if ( is_wp_error( $user ) ) {
				$message = $user->get_error_message();
				$message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>',
					'<strong>' . esc_html( $creds['user_login'] ) . '</strong>', $message );

				if ( wp_doing_ajax() ) {
					wp_send_json_error( $message );
				} else {
					$errors[] = $message;
					refpress_add_form_errors( $errors, 'user_login' );
				}

			} else {

				if ( wp_doing_ajax() ) {
					wp_send_json_success( [
						'redirect' => $redirect_to
					] );
				} else {
					refpress_redirect( $redirect_to );
				}
			}
		} catch ( \Exception $e ) {
			do_action( 'refpress_login_failed' );

			if ( wp_doing_ajax() ) {
				wp_send_json_error( apply_filters( 'login_errors', $e->getMessage() ) );
			} else {
				$errors[] = $e->getMessage();
				refpress_add_form_errors( $errors, 'user_login' );
			}
		}

	}

	public function user_register(){
		refpress_verify_nonce();

		$config = apply_filters( 'refpress_user_register_form_validation_rules', [
			[
				'field' => 'full_name',
				'label' => __( 'Full Name', 'refpress' ),
				'rules' => 'required',
			],
			[
				'field'  => 'email',
				'label'  => __( 'E-Mail', 'refpress' ),
				'rules'  => 'required|email',
				'errors' => [
					'email' => __( 'You must provide a valid %s.', 'refpress' ),
				],
			],
			[
				'field' => 'user_login',
				'label' => __( 'User Login', 'refpress' ),
				'rules' => 'required',
			],
			[
				'field' => 'password',
				'label' => __( 'Password', 'refpress' ),
				'rules' => 'required|confirm',
			],
			[
				'field' => 'password_confirmation',
				'label' => __( 'Password Confirmation', 'refpress' ),
				'rules' => 'required',
			],
			[
				'field' => 'promotional_strategies',
				'label' => __( 'Promotional Strategies', 'refpress' ),
				'rules' => 'required',
			],
		] );

		$validator = refpress_form_validate( $config, 'user_register' );

		/**
		 * If validator fail, stop script
		 */
		if ( ! $validator->success ) {
			return;
		}

		$full_name = refpress_get_input_text( 'full_name' );
		$user_login = refpress_get_input_text( 'user_login' );
		$email = refpress_get_input_text( 'email' );
		$password = refpress_get_input_text( 'password' );

		$full_name_arr = (array) explode( ' ', $full_name );
		$first_name = array_shift( $full_name_arr ) ;
		$last_name = '';
		if ( is_array( $full_name_arr ) && count( $full_name_arr ) ) {
			$last_name = implode( ' ', $full_name_arr );
		}

		$userdata = apply_filters( 'refpress_user_register_data', [
			'user_login' => $user_login,
			'user_email' => $email,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'role'       => 'subscriber',
			'user_pass'  => $password,
		] );

		do_action( 'refpress_user_register_before', $userdata );

		$user_id = wp_insert_user( $userdata );

		if ( ! is_wp_error( $user_id ) ) {
			do_action( 'refpress_user_register_after', $user_id );

			/**
			 * Should user login right after signup
			 */

			$login_after_register = apply_filters( 'refpress_login_after_register', true );

			if ( $login_after_register && ! is_admin() ) {
				$user = get_user_by( 'id', $user_id );
				if ( $user ) {
					wp_set_current_user( $user_id, $user->user_login );
					wp_set_auth_cookie( $user_id );
				}
			}

		} else {

			refpress_add_form_errors( $user_id->get_error_messages(), 'user_register' );
			return;
		}

		refpress_redirect();
	}

	public function logged_user_join_request(){

		$user_id = get_current_user_id();
		if ( $user_id ) {

			$config = apply_filters( 'logged_user_join_request_form_validation_rules', [
				[
					'field' => 'promotional_strategies',
					'label' => __( 'Promotional Strategies', 'refpress' ),
					'rules' => 'required',
				],
			] );

			$validator = refpress_form_validate( $config );

			/**
			 * If validator fail, stop script
			 */
			if ( ! $validator->success ) {
				return;
			}

			$this->create_affiliate_account( $user_id );
			refpress_redirect();
		}
	}

	public function create_affiliate_account( $user_id = 0 ) {
		global $wpdb;

		if ( ! $user_id ) {
			return false;
		}

		$refpress_account = refpress_get_account( $user_id );

		if ( ! $refpress_account ) {
			do_action( 'refpress_new_account_before', $user_id );

			$wp_user              = get_userdata( $user_id );
			$commission_rate      = refpress_get_setting( 'commission_rate' );
			$commission_rate_type = refpress_get_setting( 'commission_rate_type' );
			$account_status       = refpress_get_input_text( 'account_status', 'pending' );

			$promotional_strategies = refpress_get_input_textarea( 'promotional_strategies', '' );
			$promotional_properties = refpress_get_input_textarea( 'promotional_properties', '' );

			$account_data = [
				'user_id'                       => $user_id,
				'nicename'                      => $wp_user->user_nicename,
				'enable_custom_commission_rate' => 0,
				'commission_rate'               => $commission_rate,
				'commission_rate_type'          => $commission_rate_type,
				'status'                        => $account_status,
				'promotional_strategies'        => $promotional_strategies,
				'promotional_properties'        => $promotional_properties,
				'created_at'                    => current_time( 'mysql' ),
			];

			$wpdb->insert( $wpdb->prefix . 'refpress_accounts', $account_data );
			$account_id = $wpdb->insert_id;

			$refpress_account = refpress_get_account( $user_id );

			do_action( 'refpress_new_account_after', $user_id, $account_id );
		}

		return $refpress_account;
	}

	/**
	 * Create RefPress account after register
	 *
	 *
	 * @since RefPress 1.0.0
	 *
	 * @param $user_id
	 */

	public function create_account_after_register( $user_id ){
		$this->create_affiliate_account( $user_id );
	}

	public function logout(){
		global $wp_query;

		if ( ( ! empty( $wp_query->query_vars['name'] ) && $wp_query->query_vars['name'] === 'logout' ) || ! empty( get_query_var( 'account_page' ) ) && get_query_var( 'account_page' ) === 'logout' ) {
			wp_logout();
			wp_redirect( refpress_account_uri() );
		}

	}

	/**
	 * Fire this method when while new user registration
	 *
	 * This method keep track if any affiliated users has been referred this users
	 *
	 *
	 * @since RefPress 1.0.0
	 *
	 *
	 * @param $user_id
	 */

	public function action_user_register( $user_id ){
		$ref_id = (int) refpress_get_cookie_referer_id();

		if ( $ref_id ) {
			$added_account_id = add_user_meta( $user_id, 'referer_account_id', $ref_id, true );

			$traffic_id = (int) refpress_get_cookie_traffic_id();

			if ( $traffic_id && $added_account_id ) {
				global $wpdb;

				$referer_account = refpress_get_account_by_id( $ref_id );
				add_user_meta( $user_id, 'referer_wp_user_id', $referer_account->user_id, true );

				refpress_db_update( $wpdb->prefix. 'refpress_accounts', [ 'users_registered' => $referer_account->users_registered + 1 ], [ 'account_id' => $ref_id ] );

				/**
				 * Increase total registered value at traffics table
				 */

				$total_registered = $wpdb->get_var( $wpdb->prepare( "SELECT users_registered FROM {$wpdb->prefix}refpress_traffics WHERE traffic_id = %d ",
					$traffic_id ) );

				$wpdb->update( $wpdb->prefix . 'refpress_traffics', [ 'users_registered' => $total_registered + 1 ], [ 'traffic_id' => $traffic_id ] );

			}
		}

	}

	/**
	 * PayOut Page
	 */

	public function payout_page( $page ){
		$sub_page = refpress_get_input_text( 'sub_page' );

		if ( $sub_page === 'pay-referer' ) {
			$page = REFPRESS_ABSPATH . 'includes/admin/payout/pay-referer.php';
		}

		return $page;
	}

	public function pay_to_referer() {
		refpress_verify_nonce();

		global $wpdb;

		$refpress_account_id = (int) refpress_get_input_text( 'refpress_account_id' );
		$pay_amount          = refpress_get_input_text( 'pay_amount' );
		$account             = refpress_get_account_by_id( $refpress_account_id );

		$min_payout_amount = refpress_get_setting( 'minimum_payout_amount', 50 );

		$balance = ( $account->earned - $account->paid );

		$errors = [];

		if ( $min_payout_amount > $pay_amount ) {
			$errors[] = sprintf( __( 'Minimum payable amount is %s', 'refpress' ),
				'<strong> ' . refpress_price( $min_payout_amount ) . ' </strong>' );
		}

		if ( $pay_amount > $balance ) {
			$errors[] = __( 'Insufficient balance to pay', 'refpress' );
		}

		if ( is_array( $errors ) && count( $errors ) ) {
			refpress_add_form_errors( $errors );

			return;
		}

		$payout_method = '';
		$method_data   = '';
		if ( ! empty( $account->payout_method ) ) {
			$referer_payout_method = refpress_get_referer_payout_method( $account );
			$enabled = refpress_array_get( 'enabled', $referer_payout_method );

			if ( $enabled ) {
				$method_data = refpress_array_get( "payout_method_fields." . $account->payout_method, $referer_payout_method );
				$method_data = serialize( $method_data );
			}
		}


		$pay_data = apply_filters( 'refpress_pay_to_referer_data', [
			'referer_id'         => $refpress_account_id,
			'wp_user_id'         => $account->user_id,
			'amount'             => $pay_amount,
			'status'             => 'success',
			'payout_method_id'   => $payout_method,
			'payout_method_data' => $method_data,
			'created_at'         => current_time( 'mysql' ),
		] );

		$wpdb->insert( $wpdb->prefix . 'refpress_payouts', $pay_data );
		refpress_sync_referer_data( $refpress_account_id );

		do_action( 'refpress_payout_after', $pay_data );

		refpress_redirect( admin_url( 'admin.php?page=refpress-payout-history&account_id=' . $refpress_account_id ) );
	}

}