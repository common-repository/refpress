<?php

function refpress_sanitize_html( $text = null ) {
	if ( ! $text ) {
		return '';
	}

	$allowed_tags = [
		'html',
		'body',
		'em',
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
		'p',
		's',
		'br',
		'ul',
		'li',
		'hr',
		'a',
		'abbr',
		'address',
		'b',
		'blockquote',
		'center',
		'cite',
		'code',
		'del',
		'i',
		'ins',
		'strong',
		'sub',
		'sup',
		'time',
		'u',
		'img',
		'iframe',
		'link',
		'nav',
		'ol',
		'table',
		'caption',
		'th',
		'tr',
		'td',
		'thead',
		'tbody',
		'tfoot',
		'col',
		'colgroup',
		'div',
		'span'
	];

	/**
	 * Filter default allowed tags for the function refpress_sanitize_html_allowed_tags();
	 *
	 * @since RefPress 1.0.0
	 *
	 * @see refpress_sanitize_html_allowed_tags
	 *
	 * @param  array  $allowed_tags  Default Allowed Tags
	 */

	$allowed_tags        = apply_filters( 'refpress_sanitize_html_allowed_tags', $allowed_tags );
	$allowed_tags_string = "<" . implode( "><", $allowed_tags ) . ">";

	$text = strip_tags( $text, $allowed_tags_string );
	$text = str_replace( 'javascript:', '', $text );

	return apply_filters( 'refpress_sanitize_html', $text );
}

/**
 * Sanitize the array
 *
 * Usefull for the array data from $_POST, $_GET or $_REQUEST
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_sanitize_html();
 *
 * @param $Array
 *
 * @return array|mixed
 */

function refpress_array_sanitize( $Array ) {
	if ( is_array( $Array ) && count( $Array ) ) {
		foreach ( $Array as $ArrayKey => $ArrayValue ) {
			if ( is_array( $ArrayValue ) ) {
				$arr[ $ArrayKey ] = refpress_array_sanitize( $ArrayValue );
			} else {
				$arr[ $ArrayKey ] = refpress_sanitize_html( $ArrayValue );
			}
		}
	}

	return stripslashes_deep( $Array );
}

/**
 * Getting input array field and sanitizing
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_get_array_input_field();
 *
 * @param  null  $field_name
 *
 * @return array|mixed
 */

function refpress_get_array_input_field( $field_name = null ) {
	if ( ! empty( $_POST[ $field_name ] ) ) {
		return refpress_array_sanitize( $_POST[ $field_name ] );
	}

	if ( ! empty( $_GET[ $field_name ] ) ) {
		return refpress_array_sanitize( $_GET[ $field_name ] );
	}

	return null;
}

/**
 * Retrieve old input value, useful for form submission.
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  string  $input
 * @param  null  $old_data
 *
 * @return array|bool|mixed|string
 */

function refpress_input_old( $input = '', $old_data = null ) {
	if ( ! $input ) {
		return '';
	}

	if ( $old_data ) {
		$value = refpress_array_get( $input, $old_data );
	} else {
		$value = refpress_get_array_input_field( $input );
	}

	if ( $value ) {
		return $value;
	}

	return '';
}


/**
 * Generate Nonce Field for the RefPress Forms
 *
 * Example usage:
 *
 *     refpress_nonce_field();
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  bool  $referer
 * @param  bool  $echo
 *
 * @return string
 */

function refpress_nonce_field( $referer = true, $echo = true ) {
	$action = REFPRESS_NONCE_ACTION;
	$name = REFPRESS_NONCE;

	$name        = esc_attr( $name );
	$nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . wp_create_nonce( $action ) . '" />';

	if ( $referer ) {
		$nonce_field .= wp_referer_field( false );
	}

	if ( $echo ) {
		echo $nonce_field;
	}

	return $nonce_field;
}


/**
 * Generate RefPress form action field
 *
 * Example usage:
 *
 *     refpress_generate_field_action();
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  null  $action_name
 * @param  bool  $echo
 *
 * @return string
 */

function refpress_generate_field_action( $action_name = null, $echo = true ) {
	$actionFieldHTML = "<input name='refpress_action' value='refpress_{$action_name}' type='hidden' />";

	if ( $echo ) {
		echo $actionFieldHTML;
	}

	return $actionFieldHTML;
}


/**
 * Show form errors
 *
 * Example usage:
 *
 *     refpress_form_errors();
 *
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_form_errors_arr();
 *
 * @param  bool  $echo
 *
 * @return mixed|void
 */

function refpress_form_errors( $form = null, $echo = true ) {

	$errors = apply_filters( 'refpress_form_errors', [], $form );

	$output     = '';
	$error_text = sprintf( '%s' . __( 'Error:', 'refpress' ) . '%s', '<strong>', '</strong>' );
	if ( is_array( $errors ) && count( $errors ) ) {
		$output .= '<div class="refpress-form-error-wrap"><ul class="refpress-form-error">';
		foreach ( $errors as $error ) {
			$errorArr = (array) $error;

			if ( count( $errorArr ) ) {
				foreach ( $errorArr as $e ) {
					$output .= "<li> &excl; {$error_text} {$e}</li>";
				}
			}

		}
		$output .= '</ul></div>';
	}

	$html = apply_filters( 'refpress_form_errors_html', $output );

	if ( $echo ) {
		echo $html;
	}

	return $html;

}

/**
 * RefPress Nonce Verification
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  string  $method
 */

function refpress_verify_nonce( $method = 'post' ) {
	if ( $method === 'post' ) {
		if ( ! isset( $_POST[ REFPRESS_NONCE ] )
		     || ! wp_verify_nonce( $_POST[ REFPRESS_NONCE ], REFPRESS_NONCE_ACTION ) ) {
			exit( 'Nonce does not matched, Try refreshing the page or clear cache' );
		}
	} else {
		if ( ! isset( $_GET[ REFPRESS_NONCE ] ) || ! wp_verify_nonce( $_GET[ REFPRESS_NONCE ], REFPRESS_NONCE_ACTION ) ) {
			exit( 'Nonce does not matched, Try refreshing the page or clear cache' );
		}
	}
}

/**
 * RefPress provides a great approaches to validate your incoming data.
 *
 * This function provides a convenient way to validate incoming HTTP requests with a variety of powerful validation rules.
 *
 * Example usage:
 *
 *        $config = apply_filters( 'refpress_user_register_form_validation_rules', [
 *            [
 *                'field' => 'full_name',
 *                'label' => __( 'Full Name', 'refpress' ),
 *                'rules' => 'required',
 *            ],
 *            [
 *                'field' => 'email',
 *                'label' => __( 'E-Mail', 'refpress' ),
 *                'rules' => 'required|email',
 *                'errors' => [
 *                    'email' => __( 'You must provide a valid %s.', 'refpress'),
 *                ],
 *            ],
 *            [
 *                'field' => 'user_login',
 *                'label' => __( 'User Login', 'refpress' ),
 *                'rules' => 'required',
 *            ],
 *            [
 *                'field' => 'password',
 *                'label' => __( 'Password', 'refpress' ),
 *                'rules' => 'required|confirm',
 *            ],
 *            [
 *                'field' => 'password_confirmation',
 *                'label' => __( 'Password Confirmation', 'refpress' ),
 *                'rules' => 'required',
 *            ],
 *        ] );
 *
 *        $validator = refpress_form_validate( $config );
 *
 * //If validator fail, stop script
 *
 * if ( ! $validator->success ){
 *      return;
 * }
 *
 * <h3>Available Rules</h3>
 *
 * <strong> required </strong>
 * The field under validation must be present in the input data and not empty. A field is considered "empty" if one of the following conditions are true:
 * The value is null.
 * The value is an empty string.
 * The value is an empty array or empty Countable object.
 * The value is an uploaded file with no path.
 *
 * <strong> email </strong>
 * The field under validation must be formatted as an E-Mail address.
 *
 * <strong> confirm </strong>
 * The field under validation must have a matching field of foo_confirmation. For example, if the field under validation is password, a matching password_confirmation field must be present in the input.
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  array  $config  Rules config
 *
 * @return object
 */

function refpress_form_validate( $config = [], $form = null ) {
	$response = [
		'success' => true,
		'errors'  => [],
	];

	$errors = [];

	if ( is_array( $config ) && count( $config ) ) {

		foreach ( $config as $item ) {

			$field = refpress_array_get( 'field', $item );
			$label = refpress_array_get( 'label', $item );
			$rules = refpress_array_get( 'rules', $item );
			$rules = array_filter( explode( '|', $rules ) );

			foreach ( $rules as $rule ) {

				$field_value         = refpress_get_array_input_field( $field );
				$given_error_message = refpress_array_get( 'errors.' . $rule, $item );
				$validate            = true;

				$default_error_msg = '';

				switch ( $rule ) {
					case 'required' :
						if ( ! $field_value ) {
							$validate          = false;
							$default_error_msg = sprintf( __( '%s field is required', 'refpress' ), $label );
						}

						break;

					case 'email' :

						if ( ! filter_var( $field_value, FILTER_VALIDATE_EMAIL ) ) {
							$validate          = false;
							$default_error_msg = sprintf( __( 'A valid %s is required', 'refpress' ), $label );
						}

						break;
					case 'confirm' :

						if ( $field_value !== refpress_input_old( $field . '_confirmation' ) ) {
							$validate = false;

							$default_error_msg = sprintf( __( '%1$s field does not matched with confirm %1$s field',
								'refpress' ), $label );
						}

						break;
				}

				/**
				 * Set Error Message
				 */
				if ( ! $validate ) {
					if ( $given_error_message ) {
						$error_message = sprintf( $given_error_message, $label );
					} else {
						$error_message = $default_error_msg;
					}
					$errors[ $field ][ $rule ] = $error_message;
				}

			}

		}

	}

	if ( is_array( $errors ) && count( $errors ) ) {

		$response = [
			'success' => false,
			'errors'  => $errors,
		];

		add_filter( 'refpress_form_errors', function ( $filterError, $filterForm ) use ( $errors, $form ) {
			if ( $filterForm === $form ){
				return $errors;
			}
		}, 10, 2 );

	}

	return (object) $response;
}


/**
 * Add form error manually to show in the form error function
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  array  $errors
 * @param string|null $form
 *
 */

function refpress_add_form_errors( $errors = [], $form = null ) {

	add_filter( 'refpress_form_errors', function ( $filterError, $filterForm ) use ( $errors, $form ) {
		if ( $filterForm === $form ){
			return $errors;
		}
	}, 10, 2 );

}


/**
 * Update RefPress account status => pending|approved|blocked
 *
 *
 * Example usage:
 *
 *     refpress_accounts_status_update( $account_id, 'approved' );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  int  $account_id  Affiliate Account ID
 * @param  string  $new_status  pending|approved|blocked
 *
 * @return false true on success and false on error.
 */

function refpress_accounts_status_update( $account_id, $new_status = 'approved' ) {
	global $wpdb;

	$account = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}refpress_accounts WHERE account_id = {$account_id} " );

	if ( ! empty( $account ) && $account->status !== $new_status ) {
		$old_status = $account->status;

		do_action( 'refpress_accounts_status_update_before', $account, $new_status, $old_status );

		$response = refpress_db_update( $wpdb->prefix . 'refpress_accounts', [ 'status' => $new_status ], [ 'account_id' => $account_id ] );

		do_action( 'refpress_accounts_status_update_after', $account, $new_status, $old_status );

		if ( $response ) {
			return true;
		}
	}

	return false;
}


/**
 * Get referral URL param
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @return mixed|void
 */

function refpress_referral_url_param() {
	$param = trim( refpress_get_setting( 'referral_url_parameter' ) );

	return apply_filters( 'refpress_referral_url_param', $param );
}

/**
 * Get IP Address
 *
 *
 * @since RefPress 1.0.0
 *
 * @return mixed|null
 */

function refpress_get_ip_address() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
}


/**
 * Get referer ID from the cookie
 *
 * @since RefPress 1.0.0
 *
 *
 * @return mixed|void
 */

function refpress_get_cookie_referer_id() {
	$ref_id = ! empty( $_COOKIE['refpress_referer'] ) ? sanitize_text_field( $_COOKIE['refpress_referer'] ) : '';

	return apply_filters( 'refpress_get_cookie_referer_id', $ref_id );
}

/**
 * Get refpress traffic id
 *
 *
 * @since RefPress 1.0.0
 *
 * @return mixed|void
 */

function refpress_get_cookie_traffic_id() {
	$traffic_id = (int) ( ! empty( $_COOKIE['refpress_traffic_id'] )
		? sanitize_text_field( $_COOKIE['refpress_traffic_id'] ) : 0 );

	return apply_filters( 'refpress_get_cookie_traffic_id', $traffic_id );
}

/**
 * Determine if an user visited previously without any referral.
 *
 *
 * Example usage:
 *
 *     $is_returning = refpress_is_organic_visitor();
 *
 * @since RefPress 1.0.0
 *
 *
 * @return false|string Last visited timestamp or false
 */

function refpress_is_organic_visitor() {
	$is_organic_visitor = ! empty( $_COOKIE['refpress_visited'] )
		? sanitize_text_field( $_COOKIE['refpress_visited'] ) : false;

	return $is_organic_visitor;
}


/**
 * Check if a referer is valid, it will also check if there any referer
 *
 *
 * @since RefPress 1.0.0
 *
 * @param  array  $args  Ordered customer E-Mail address
 *
 *
 * @return bool|mixed|void
 */

function refpress_has_valid_referer( $args = [] ) {
	global $wpdb;

	$defaults = apply_filters( 'refpress_has_valid_referer_default_args', [
		'email'              => null,
		'order_id'           => 0,
		'order_processed_by' => null,
	] );

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$credit_on_recurring_purchase = (bool) refpress_get_setting( 'credit_on_recurring_purchase' );

	$log_data = [
		'order_id'           => $order_id,
		'log_type'           => 'UNKNOWN',
		'order_processed_by' => $order_processed_by,
	];

	$referer_id = refpress_get_cookie_referer_id();
	if ( empty( $referer_id ) ) {
		return false;
	}

	$account     = refpress_get_account_by_id( $referer_id );
	$is_approved = refpress_is_account_approved( $account );

	//Check if account approved and valid
	if ( ! $is_approved ) {
		$log_data['log_type'] = 'ACCOUNT_ISSUE';
		refpress_write_log( $log_data );

		return false;
	}

	//Check if the customer is organic
	$organic_visitor = refpress_is_organic_visitor();
	if ( $organic_visitor ) {
		$allow_organic_visitor = apply_filters( 'refpress_allow_referral_for_organic_visitor', false );
		if ( ! $allow_organic_visitor ) {
			//Write Log
			$log_data['log_type'] = 'ORGANIC_VISITOR';
			refpress_write_log( $log_data );

			return false;
		}
	}

	//Check if there is any previous order from this IP
	$customer_ip     = refpress_get_ip_address();
	if ( ! $credit_on_recurring_purchase && ! empty( $customer_ip ) ) {
		$old_ip = (int) $wpdb->get_var( "SELECT COUNT(earning_id) FROM {$wpdb->prefix}refpress_earnings WHERE customer_ip = '{$customer_ip}' " );
		if ( $old_ip > 0 ) {
			//Write Log
			$log_data['log_type'] = 'OLD_IP';
			refpress_write_log( $log_data );

			return false;
		}
	}

	//Check if the billing email is  referral email
	if ( ! empty( $email ) && is_email( $email ) ) {
		$referer_email = refpress_get_referer_email( $account );

		if ( $email == $referer_email ) {

			$log_data['log_type'] = 'SELF_REFER';
			refpress_write_log( $log_data );

			return apply_filters( 'refpress_allow_self_refer', false, $referer_id, $account );
		}
	}

	//Check if the referral person purchasing
	if ( is_user_logged_in() ) {
		$current_user_id = get_current_user_id();
		if ( $current_user_id == $account->user_id ) {

			/**
			 * Filter to allow self refer, default false
			 *
			 *
			 * @since RefPress 1.0.0
			 *
			 * @param  bool false
			 * @param  int  $referer_id  Referer ID from Cookie
			 * @param  object  $account  Referer Account as object from DB
			 */

			$log_data['log_type'] = 'SELF_REFER';
			refpress_write_log( $log_data );

			return apply_filters( 'refpress_allow_self_refer', false, $referer_id, $account );
		}
	}

	return true;
}

/**
 * short description
 *
 * long description
 *
 * Example usage:
 *
 *     refpress_get_referer_email();
 *
 * more description
 * Note: if any
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_get_referer_email();
 *
 * @param  int|object  $referer  Referer Account ID or Object from DB
 *
 * @return string|null
 */

function refpress_get_referer_email( $referer ) {

	$email      = null;
	$wp_user_id = null;

	if ( is_numeric( $referer ) ) {
		$account    = refpress_get_account_by_id( $referer );
		$wp_user_id = $account->user_id;

	} elseif ( is_object( $referer ) && ! empty( $referer->user_id ) ) {
		$wp_user_id = $referer->user_id;
	}

	$wp_user = get_userdata( $wp_user_id );
	if ( ! empty( $wp_user->user_email ) && is_email( $wp_user->user_email ) ) {
		$email = $wp_user->user_email;
	}

	/**
	 * Filter referer email
	 *
	 * @since RefPress 1.0.0
	 *
	 * @param  string  $email  Referer Email
	 * @param  int|object  $referer  Referer Account ID or Object from DB
	 * @param  WP_User  $wp_user  WP User Object
	 */

	return apply_filters( 'refpress_get_referer_email', $email, $referer, $wp_user );
}


/**
 * Sync Referer account's earnings, payout and traffics data
 *
 *
 * @since RefPress 1.0.0
 *
 * @param  int  $referer_id  Referer Account ID
 */

function refpress_sync_referer_data( $referer_id ) {
	global $wpdb;

	$earning = $wpdb->get_row( "SELECT SUM(referer_amount) as total_earning, COUNT(earning_id) AS total_converted FROM {$wpdb->prefix}refpress_earnings WHERE referer_id = {$referer_id} AND order_status = 'completed'  ;" );

	$visits = (int) $wpdb->get_var( "SELECT COUNT(traffic_id) AS total_visited FROM {$wpdb->prefix}refpress_traffics WHERE referer_id = {$referer_id} ;" );

	$earned    = '0.00';
	$converted = 0;

	if ( ! empty( $earning->total_earning ) ) {
		$earned = $earning->total_earning;
	}


	//Get all paid amount
	$paid = '0.00';

	$all_pay_sum = $wpdb->get_var( "SELECT SUM(amount) as pay_sum FROM {$wpdb->prefix}refpress_payouts WHERE referer_id = {$referer_id} AND status != 'rejected' ;" );

	if ( $all_pay_sum > 0 ) {
		$paid = $all_pay_sum;
	}


	if ( ! empty( $earning->total_converted ) ) {
		$converted = $earning->total_converted;
	}

	$referer_data = [
		'earned'    => $earned,
		'paid'      => $paid,
		'converted' => $converted,
		'visits'    => $visits,
	];

	refpress_db_update( $wpdb->prefix . 'refpress_accounts', $referer_data, [ 'account_id' => $referer_id ] );

	$referer = refpress_get_account_by_id( $referer_id );

	return $referer;
}


/**
 * Update earning status
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param int $earning_id Earning ID
 * @param  string  $status_to Status to
 */

function refpress_earning_status_change( $earning_id, $status_to = 'pending' ) {
	global $wpdb;

	$earning_data = refpress_get_earning( $earning_id );
	$status_from  = $earning_data->order_status;

	$wpdb->update( $wpdb->prefix . 'refpress_earnings', [ 'order_status' => $status_to ],
		[ 'earning_id' => $earning_id ] );

	//Update data related referer, such as earning, visits, etc
	refpress_sync_referer_data( $earning_data->referer_id );

	/**
	 * Action when earnings status changed
	 *
	 * @since RefPress 1.0.0
	 *
	 * @param  int  $earning_id  Earning ID
	 * @param  string  $status_from  Status From
	 * @param  string  $status_to  Status To
	 */

	do_action( 'refpress_earning_status_change', $earning_id, $status_from, $status_to );
}

/**
 * Calculate percentage for RefPress various data
 *
 *
 * Example usage:
 *
 *     refpress_percentage_of( 100, 20 );
 *
 * more description
 * Note: if any
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_get_percentage();
 *
 * @param int|float $value Total Value
 * @param int|float $divided_by Divided by value
 * @param bool $formatted Is result will be formatted or number
 * @param  int  $decimals Decimals
 *
 * @return float|int
 */

function refpress_percentage_of( $value, $divided_by, $formatted = false, $decimals = 0 ) {
	$value      = (float) $value;
	$divided_by = (float) $divided_by;

	$percent = 0;

	if ( $divided_by > 0 ) {
		$percent = round( ( $value / $divided_by ) * 100, $decimals );
	}

	$percent_sign = $formatted ? '%' : '';

	return $percent.$percent_sign;
}

/**
 * Update Payout's status
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param int $earning_id Earning ID
 * @param  string  $status_to Status to success|rejected|pending
 */

function refpress_payout_status_change( $payout_id, $status_to = 'success' ) {
	global $wpdb;

	$payout = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}refpress_payouts WHERE payout_id = {$payout_id} ;" );

	$status_from = $payout->status;

	$wpdb->update( $wpdb->prefix . 'refpress_payouts', [ 'status' => $status_to ],
		[ 'payout_id' => $payout_id ] );

	//Update data related referer, such as earning, visits, etc
	refpress_sync_referer_data( $payout->referer_id );

	/**
	 * Action hook after payout status change
	 *
	 * @since RefPress 1.0.0
	 *
	 * @param  int  $earning_id  Payout ID
	 * @param  string  $status_from  Status From
	 * @param  string  $status_to  Status To
	 */

	do_action( 'refpress_payout_status_change', $payout_id, $status_from, $status_to );
}