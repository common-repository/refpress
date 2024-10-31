<?php

/**
 * Check if RefPress pro version is exists
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @return bool
 */

function refpress_has_pro() {
	return function_exists( 'refpress_pro' );
}

/**
 * Updates a row in the refpress table table.
 *
 * Examples:
 *     wpdb::update( 'table', array( 'column' => 'foo', 'field' => 'bar' ), array( 'ID' => 1 ) )
 *     wpdb::update( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( 'ID' => 1 ), array( '%s', '%d' ), array( '%d' ) )
 *
 * @since 1.0.0
 *
 * @see wpdb::prepare()
 * @see wpdb::$field_types
 * @see wp_set_wpdb_vars()
 *
 * @param  string  $table  Table name.
 * @param  array  $data  Data to update (in column => value pairs).
 *                                   Both $data columns and $data values should be "raw" (neither should be SQL escaped).
 *                                   Sending a null value will cause the column to be set to NULL - the corresponding
 *                                   format is ignored in this case.
 * @param  array  $where  A named array of WHERE clauses (in column => value pairs).
 *                                   Multiple clauses will be joined with ANDs.
 *                                   Both $where columns and $where values should be "raw".
 *                                   Sending a null value will create an IS NULL comparison - the corresponding
 *                                   format will be ignored in this case.
 * @param  array|string  $format  Optional. An array of formats to be mapped to each of the values in $data.
 *                                   If string, that format will be used for all of the values in $data.
 *                                   A format is one of '%d', '%f', '%s' (integer, float, string).
 *                                   If omitted, all values in $data will be treated as strings unless otherwise
 *                                   specified in wpdb::$field_types.
 * @param  array|string  $where_format  Optional. An array of formats to be mapped to each of the values in $where.
 *                                   If string, that format will be used for all of the items in $where.
 *                                   A format is one of '%d', '%f', '%s' (integer, float, string).
 *                                   If omitted, all values in $where will be treated as strings.
 *
 * @return int|false The number of rows updated, or false on error.
 */

function refpress_db_update( $table, $data, $where, $format = null, $where_format = null ) {
	global $wpdb;

	do_action( 'refpress_db_update_before', $table, $data, $where, $format = null, $where_format = null );

	$response = $wpdb->update( $table, $data, $where, $format = null, $where_format = null );

	do_action( 'refpress_db_update_after', $table, $data, $where, $format = null, $where_format = null );

	return $response;
}

/**
 * Return array value by given key, Get array key by dot notation
 *
 * Get any array value without any error, it will return you a null value if there is no key exists in the array that you wish to access
 * It supports dot notation accessibility
 *
 * Example usage:
 *
 *     refpress_array_get( 'key', $array );
 *     refpress_array_get( 'key.child_dimension_key', $array );
 *     refpress_array_get( 'key', $array, $default_value );
 *
 * Note: you must pass an array in the second parameter.
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  null  $key  Key item
 * @param  array  $array  array
 * @param  bool  $default
 *
 * @return array|bool|mixed
 */

function refpress_array_get( $key = null, $array = [], $default = false ) {
	$array = (array) $array;

	if ( $key === null || ! count( $array ) ) {
		return $default;
	}

	$option_key_array = explode( '.', $key );

	$value = $array;

	foreach ( $option_key_array as $dotKey ) {
		if ( isset( $value[ $dotKey ] ) ) {
			$value = $value[ $dotKey ];
		} else {
			return $default;
		}
	}

	return $value;
}

/**
 * Get all of the given array except for a specified array of keys.
 *
 * @param array $array
 * @param array|string $keys
 *
 * @return array
 *
 * @since RefPress 1.0.0
 */

function refpress_array_except( $array, $keys ) {

	$original = &$array;

	$keys = (array) $keys;

	if ( count( $keys ) === 0 ) {
		return;
	}

	foreach ( $keys as $key ) {
		// if the exact key exists in the top-level, remove it
		if ( array_key_exists( $key, $array ) ) {
			unset( $array[ $key ] );

			continue;
		}

		$parts = explode( '.', $key );

		// clean up before each pass
		$array = &$original;

		while ( count( $parts ) > 1 ) {
			$part = array_shift( $parts );

			if ( isset( $array[ $part ] ) && is_array( $array[ $part ] ) ) {
				$array = &$array[ $part ];
			} else {
				continue 2;
			}
		}

		unset( $array[ array_shift( $parts ) ] );
	}

	return $array;
}

/**
 * Get a subset of the items from the given array.
 *
 *
 * Example usage:
 *
 *     refpress_array_only( $array, array( 'remove_key', 'remove_key_2', 'and_so_on' ) );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  array  $array
 * @param  array|string  $keys
 *
 * @return array
 */

function refpress_array_only( $array, $keys ) {
	return array_intersect_key( $array, array_flip( (array) $keys ) );
}

/**
 * Sanitize and return text field value
 *
 *
 * @since Refpress 1.0.0
 *
 * @see refpress_array_get();
 *
 * @param  null  $field_name Input Field name
 * @param  string  $default Default Value
 *
 * @return string|null
 */

function refpress_get_input_text( $field_name = null, $default = '' ) {
	if ( ! $field_name ) {
		return null;
	}

	$value = sanitize_text_field( refpress_array_get( $field_name, $_POST ) );
	if ( ! $value ) {
		$value = sanitize_text_field( refpress_array_get( $field_name, $_GET ) );
	}

	if ( $value ) {
		return $value;
	}

	return $default;
}

/**
 * Get TextArea field value
 *
 *
 * @since RefPress 1.0.0
 *
 * @param  null  $field_name Textarea field name
 * @param  string  $default Default Value
 *
 * @return mixed|string|null
 */

function refpress_get_input_textarea( $field_name = null, $default = '' ) {
	if ( ! $field_name ) {
		return null;
	}

	$value = sanitize_textarea_field( refpress_array_get( $field_name, $_POST ) );
	if ( ! $value ) {
		$value = sanitize_textarea_field( refpress_array_get( $field_name, $_GET ) );
	}

	if ( $value ) {
		return $value;
	}

	return $default;
}

/**
 * Determine if the application is in debug mode
 *
 *
 * Example usage:
 *
 *     $is_debug_mode = refpress_script_debug();
 *
 * @since RefPress 1.0.0
 *
 *
 * @return bool
 */

function refpress_script_debug() {
	return ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || defined( 'REFPRESS_SCRIPT_DEBUG' ) && REFPRESS_SCRIPT_DEBUG );
}

function refpress_get_wp_pages() {
	$wp_pages    = get_pages();
	$pages_array = wp_list_pluck( $wp_pages, 'post_title', 'ID' );

	return $pages_array;
}

/**
 * Get RefPress settings Value by settings key
 *
 *
 * @since RefPress 1.0.0
 *
 * @param  null  $settings_key
 * @param  null  $defaultValue
 *
 * @return array|bool|mixed
 */

function refpress_get_setting( $settings_key = null, $defaultValue = null ) {
	$settings = maybe_unserialize( get_option( 'refpress_settings' ) );
	return refpress_array_get( $settings_key, $settings, $defaultValue );
}


/**
 * Update RefPress Setting
 *
 *
 * @since RefPress 1.0.0
 *
 * @param  null  $key
 * @param  bool  $value Option Value
 */

function refpress_update_setting( $key = null, $value = false ) {
	$option         = (array) maybe_unserialize( get_option( 'refpress_settings' ) );
	$option[ $key ] = $value;
	update_option( 'refpress_settings', $option );
}

/**
 * Get RefPress Affiliate Dashboard page ID
 *
 * Associates will manage their account through this page.
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_get_setting();
 *
 * @return false|int
 */

function refpress_get_dashboard_page_id() {
	$lounge_page_id = refpress_get_setting( 'refpress_dashboard_page_id' );
	$page = apply_filters( 'refpress_get_dashboard_page_id', $lounge_page_id );

	return $page ? absint( $page ) : false;
}

/**
 * Get privacy policy page ID
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @return int
 */
function refpress_get_privacy_policy_page_id() {
	$page_id = refpress_get_setting( 'privacy_policy_page_id', 0 );

	return apply_filters( 'refpress_get_privacy_policy_page_id', 0 < $page_id ? absint( $page_id ) : 0 );
}

function refpress_is_account_page() {
	if ( is_page() && ( refpress_get_dashboard_page_id() > 0 ) && ( refpress_get_dashboard_page_id() == get_the_ID() ) ) {
		return true;
	}

	return false;
}

/**
 * Retrieve the template path based on priority template.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH and wp-includes/plugins/refpress/{$template}
 * so that themes which inherit from a parent theme can just overload one file.
 *
 * @since 1.0.0
 *
 * @param string $template_name Template file(s) to search for, in order.
 * @param bool         $load           If true the template file will be loaded if it is found.
 *
 * @param array        $args           Optional. Additional arguments passed to the template.
 *                                     Default empty array.
 * @param bool $echo Weather loaded template should echo or return the generated HTML.
 *
 * @return string The template filename if one is located.
 */

function refpress_locate_template( $template_name = null, $load = false, $args = [], $echo = true ) {

	if ( file_exists( STYLESHEETPATH . 'refpress/' . $template_name ) ) {
		$located = STYLESHEETPATH . 'refpress/' . $template_name;
	} elseif ( file_exists( TEMPLATEPATH . 'refpress/' . $template_name ) ) {
		$located = TEMPLATEPATH . 'refpress/' . $template_name;
	} else {
		$located = trailingslashit( REFPRESS_ABSPATH ) . "templates/{$template_name}.php";
	}

	if ( $load && '' !== $located ) {

		extract( $args );

		if ( $echo ) {
			require $located;
		} else {
			ob_start();
			require $located;

			return ob_get_clean();
		}
	}

	if ( $template_name ) {
		return apply_filters( 'refpress_template_path', $located, $template_name );
	}

	return '';
}

function refpress_render_account_page() {

	if ( is_user_logged_in() ) {

		$refpress_account = refpress_get_account();

		if ( $refpress_account ) {

			if ( refpress_is_account_approved( $refpress_account ) ) {
				$output = refpress_locate_template( 'partial/account-content', true, [], false );
			} else {
				$output = refpress_locate_template( 'partial/account-not-approved', true, [], false );
			}

		} else {
			$output = refpress_locate_template( 'partial/request-for-affiliate-account', true, [], false );
		}


	} else {
		$output = refpress_locate_template( 'partial/login-register', true, [], false );
	}

	return apply_filters( 'refpress_render_account_page', $output );
}

/**
 * Redirects to another page.
 *
 * IF leave $location argument, it will redirect you to back.
 *
 * Example usage:
 *
 *     refpress_redirect();
 *
 * Note: Redirect work by Location : Header, so use this function before print anything.
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  string  $location  URL
 */

function refpress_redirect( $location = 'back' ) {

	if ( $location === 'back' ) {
		$location = wp_get_raw_referer();
	}

	if ( $location ) {
		wp_redirect( $location );
		die();
	}
}

/**
 * Get affiliate accounts
 *
 * Filter your results by passing arguments within array at first parameter.
 *
 * Example usage:
 *
 *     refpress_get_accounts( [
 *      'search_term' => 'John',
 *      'not_in_ids' => [29,30],
 *      'start' => 0, 'per_page' => 10
 *      ] );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  array  $args
 *
 * @return object
 */

function refpress_get_accounts( $args = [] ) {
	global $wpdb;

	$defaults = apply_filters( 'refpress_get_accounts_default_args', [
		'search_term' => '',
		'not_in_ids'  => [],
		'start'       => 0,
		'per_page'    => 10,
		'orderby'    => null,
		'order'    => null,
	] );

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$response = [
		'count'   => 0,
		'results' => null,
	];

	$not_in_ids_query = '';
	if ( ! empty( $not_in_ids ) ) {

		$not_in_ids_string = null;

		if ( is_array( $not_in_ids ) && count( $not_in_ids ) ) {
			$not_in_ids_string = implode( ",", $not_in_ids );
		} else {
			$not_in_ids_string = $not_in_ids;
		}

		if ( $not_in_ids_string ) {
			$not_in_ids_query = " AND {$wpdb->users}.ID NOT IN( $not_in_ids_string ) ";
		}
	}


	$limit_query = '';
	if ( $per_page !== '-1' ) {
		$limit_query = " LIMIT {$start}, {$per_page}  ";
	}

	if ( ! empty( $search_term ) ) {
		$search_term
			= " AND ( refpress_accounts.account_id = '{$search_term}' OR users_tbl.display_name LIKE '%{$search_term}%' OR users_tbl.user_nicename LIKE '%{$search_term}%' OR users_tbl.user_email LIKE '%{$search_term}%' ) ";
	}

	$orderby_sql = " ORDER BY refpress_accounts.created_at DESC ";
	if ( ! empty( $orderby ) && ! empty( $order ) ) {
		$orderby_sql = " ORDER BY refpress_accounts.{$orderby} {$order} ";
	}

	$count = (int) $wpdb->get_var( "SELECT COUNT( refpress_accounts.account_id ) 
	FROM {$wpdb->prefix}refpress_accounts as refpress_accounts
LEFT JOIN {$wpdb->users} users_tbl ON refpress_accounts.user_id = users_tbl.ID
WHERE 1 = 1  {$search_term} {$not_in_ids_query} {$orderby_sql} {$limit_query};" );

	if ( $count > 0 ) {

		$results = $wpdb->get_results( "
		SELECT refpress_accounts.*,
		users_tbl.user_nicename,
		users_tbl.user_email,
		users_tbl.display_name
		
		FROM {$wpdb->prefix}refpress_accounts as refpress_accounts
		LEFT JOIN {$wpdb->users} users_tbl ON refpress_accounts.user_id = users_tbl.ID
		WHERE 1 = 1  {$search_term} {$not_in_ids_query} {$orderby_sql} {$limit_query}; " );

		$response = [
			'count'   => $count,
			'results' => $results,
		];

	}

	return (object) $response;
}

/**
 * Get the default account status
 *
 * It will return all default statuses with key and text.
 * Generally it's for translating the dynamic status text comes from DB.
 *
 *
 * Example usage:
 *
 *     refpress_account_status( 'pending' ); //Pending
 *
 *
 * @since RefPress 1.0.0
 *
 * @param  null  $status
 *
 * @return mixed|void
 */

function refpress_account_status( $status = null ) {

	$statuses = apply_filters( 'refpress_account_statuses', [
		'pending'  => __( 'Pending', 'refpress' ),
		'approved' => __( 'Approved', 'refpress' ),
		'blocked'  => __( 'Blocked', 'refpress' ),
	] );

	if ( ! empty( $status ) ) {
		$statusText = refpress_array_get( $status, $statuses );

		return apply_filters( 'refpress_account_status', $statusText, $status );
	}

	return $statuses;
}

/**
 * Get RefPress Affiliate Account menu items
 *
 *
 * @since RefPres 1.0.0
 *
 *
 * @param  null  $key  Optional
 *
 * @return array|bool|mixed|void
 */

function refpress_account_menu_items( $key = null ) {
	$items = apply_filters( 'refpress_account_menu_items', [
		'overview'          => [ 'title' => __( 'Overview', 'refpress' ) ],
		'affiliate_links'   => [ 'title' => __( 'Affiliate Links', 'refpress' ) ],
		'my-affiliate-earnings' => [ 'title' => __( 'My Earnings', 'refpress' ), 'dependency' => 'pro' ],
		'payout-history'    => [ 'title' => __( 'Payout History', 'refpress' ) ],
		'traffic'           => [ 'title' => __( 'Traffic', 'refpress' ) ],
		'payout-settings'   => [ 'title' => __( 'Payout Settings', 'refpress' ) ],
		'logout'            => [ 'title' => __( 'Log Out', 'refpress' ) ],
	] );

	if ( ! empty( $key ) ) {
		return refpress_array_get( $key, $items );
	}

	return $items;
}

/**
 * Generate RefPress Affiliate Account's Menu
 *
 * Example usage:
 *
 *     refpress_account_menu_items_generate();
 *
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_account_menu_items();
 * @see refpress_has_pro();
 * @see refpress_array_get();
 * @see refpress_account_uri();
 *
 * @param  bool  $echo
 *
 * @return bool|mixed
 */

function refpress_account_menu_items_generate( $echo = true ) {
	$menu_items           = refpress_account_menu_items();
	$current_account_page = get_query_var( 'account_page', 'overview' );
	$has_pro              = refpress_has_pro();

	$output = "<ul>";
	foreach ( $menu_items as $menu_slug => $menu ) {
		$title  = refpress_array_get( 'title', $menu );
		$is_pro = refpress_array_get( 'dependency', $menu ) === 'pro';

		if ( $is_pro && ! $has_pro ) {
			continue;
		}

		$menu_uri     = refpress_account_uri( $menu_slug );
		$active_class = ( $current_account_page === $menu_slug ) ? 'active' : '';

		$output .= "<li class='refpress-account-menu-item {$active_class}'><a href='{$menu_uri}'> {$title} </a> </li>";
	}
	$output .= "</ul>";

	if ( $echo ) {
		echo $output;
	}

	return $echo;
}

/**
 * RefPress account uri
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  null  $menu_item
 *
 * @return mixed|void
 */

function refpress_account_uri( $menu_item = null ) {
	$account_page_id  = refpress_get_dashboard_page_id();
	$account_page_uri = trailingslashit( get_the_permalink( $account_page_id ) );

	$structure = get_option( 'permalink_structure' );

	if ( ! empty( $menu_item ) && $menu_item !== 'overview' ) {
		if ( ! empty( $structure ) ) {
			//pretty permalink
			$account_page_uri .= trailingslashit( $menu_item );
		} else {
			$account_page_uri = add_query_arg( [ 'account_page' => $menu_item ], $account_page_uri );
		}
	}

	return apply_filters( 'refpress_account_uri', $account_page_uri, $menu_item );
}

/**
 * Get traffic by when someone visits with referral link
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param int $referer_id Affiliated Referer ID
 * @param array $args Query Args
 *
 * @return array|object|void|null
 */

function refpress_get_traffic( $referer_id, $args ) {
	global $wpdb;

	$defaults = apply_filters( 'refpress_get_traffic_default_args', [
		'referer_url' => '',
		'campaign'     => '',
		'ip_address'   => '',
	] );

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$ip_sql = '';
	if ( ! empty( $ip_address ) ) {
		$ip_sql = "AND ip_address = '{$ip_address}'";
	}

	$referer
		= $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}refpress_traffics WHERE 1 = 1 AND referer_id = {$referer_id} AND referer_url = '{$referer_url}' AND campaign = '{$campaign}' {$ip_sql} " );

	return $referer;
}

/**
 * Get traffic stats by referer
 *
 * Get clicks and visitors in object
 *
 * Example usage:
 *
 *     $stats = refpress_get_traffic_stats_by_referer( $referer_account_id );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param $referer_id
 *
 * @return array|object|void|null
 */

function refpress_get_traffic_stats_by_referer( $referer_id ) {
	global $wpdb;

	$stats
		= $wpdb->get_row( "SELECT COUNT(traffic_id) as visitors, SUM(hits) as hits FROM {$wpdb->prefix}refpress_traffics WHERE 1 = 1 AND referer_id = {$referer_id} ;" );

	return $stats;
}

/**
 * Get single traffic entry by traffic ID
 *
 *
 * @since RefPress 1.0.0
 *
 * @param $traffic_id
 *
 * @return array|object|void|null
 */

function refpress_get_traffic_by_id( $traffic_id ) {
	global $wpdb;

	$traffic
		= $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}refpress_traffics WHERE traffic_id = {$traffic_id} ;" );

	return $traffic;
}

/**
 * Get traffics and filter by arguments
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param $args
 *
 * @return array|object|null
 */

function refpress_get_traffics( $args ) {
	global $wpdb;

	$defaults = apply_filters( 'refpress_get_traffics_default_args', [
		'referer_id' => null,
		'start'      => 0,
		'per_page'   => 10,
		'orderby'    => 'created_at',
		'order'      => 'DESC',
		'start_date' => null,
		'end_date'   => null,
	] );

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$response = [
		'count'   => 0,
		'results' => null,
	];

	$referer_id_query = '';
	if ( ! empty( $referer_id ) ) {
		$referer_id_query = "AND referer_id = {$referer_id}";
	}


	$date_range_sql = '';
	if ( ! empty( $start_date ) && ! empty( $end_date ) ) {

		//Re-confirming mysql date format
		$start_date = date( 'Y-m-d', strtotime( $start_date ) );
		$end_date = date( 'Y-m-d', strtotime( $end_date ) );

		$date_range_sql = " AND traffics.created_at BETWEEN '{$start_date}' AND '{$end_date}' ";
	}

	$limit_query = '';
	if ( $per_page !== '-1' ) {
		$limit_query = " LIMIT {$start}, {$per_page}  ";
	}


	$order_by_sql = " ORDER BY created_at DESC ";

	if ( ! empty( $orderby ) && ! empty( $orderby ) ) {
		$order_by_sql = " ORDER BY {$orderby} {$order} ";
	}

	$count = (int) $wpdb->get_var( "SELECT COUNT(traffic_id) FROM {$wpdb->prefix}refpress_traffics traffics WHERE 1 = 1 {$referer_id_query} {$date_range_sql} ;" );

	if ( $count ) {
		$response['count'] = $count;
		$response['results'] = $wpdb->get_results( "
SELECT traffics.* , SUM(traffics.hits) as hits,
accounts.user_id as wp_user_id,
wp_users.user_email as referer_email,
wp_users.display_name as referer_display_name

FROM {$wpdb->prefix}refpress_traffics traffics
LEFT JOIN {$wpdb->prefix}refpress_accounts accounts ON traffics.referer_id = accounts.account_id
LEFT JOIN {$wpdb->users} wp_users ON accounts.user_id = wp_users.ID
WHERE 1 = 1 {$referer_id_query} {$date_range_sql} GROUP BY traffics.referer_id, traffics.referer_url {$order_by_sql} {$limit_query} ;" );
	}

	return (object) $response;
}


/**
 * Add referral commission earning data
 *
 *
 * Example usage:
 *
 *     refpress_add_referer_commission( [
 *            'referer_id'     => 0,
 *          'traffic_id'     => 0,
 *          'customer_id'    => 0,
 *          'order_id'       => 0,
 *          'order_status'   => 'pending',
 *          'referer_amount' => '0.00',
 *          'process_by'     => '',
 *      ] );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  array  $args  Earning Data
 *
 * @return bool Earning db ID on success, false on failure
 */

function refpress_add_referer_commission( $args ) {
	global $wpdb;

	$defaults = apply_filters( 'refpress_add_referer_commission_default_args', [
		'referer_id'     => 0,
		'traffic_id'     => 0,
		'customer_id'    => 0,
		'customer_ip'    => refpress_get_ip_address(),
		'order_id'       => 0,
		'order_status'   => 'pending',
		'referer_amount' => '0.00',
		'process_by'     => '',
	] );

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( empty( $traffic_id ) ) {
		$traffic_id = refpress_get_cookie_traffic_id();
	}

	if ( ! empty( $customer_id ) ) {
		$earned_for_this_customer
			= $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}refpress_earnings WHERE 1 = 1 AND customer_id = '{$customer_id}' " );
		if ( ! empty( $earned_for_this_customer ) ) {
			$credit_on_recurring_purchase = (bool) refpress_get_setting( 'credit_on_recurring_purchase' );

			if ( ! $credit_on_recurring_purchase ) {
				return false;
			}
		}
	}

	$earning_data = apply_filters( 'refpress_earning_data', [
		'referer_id'     => $referer_id,
		'traffic_id'     => $traffic_id,
		'customer_id'    => $customer_id,
		'customer_ip'    => $customer_ip,
		'order_id'       => $order_id,
		'order_status'   => $order_status,
		'referer_amount' => $referer_amount,
		'process_by'     => $process_by,
		'created_at'     => current_time( 'mysql' ),
	] );

	$valid_earning = apply_filters( 'refpress_referer_valid_earning', true, $earning_data );

	if ( ! $valid_earning ) {
		return false;
	}

	/**
	 * Action before add referral earning data
	 *
	 * @since RefPress v.1.0.0
	 *
	 * @param  array  $earning_data  Earning table raw data as array
	 */

	do_action( 'refpress_add_earning_data_before', $earning_data );

	$inserted = $wpdb->insert( $wpdb->prefix . 'refpress_earnings', $earning_data );
	$earning_id = false;

	if ( $inserted ) {
		$earning_id = $wpdb->insert_id;

		//Increase converted value

		$converted = $wpdb->get_var( $wpdb->prepare( "SELECT converted FROM {$wpdb->prefix}refpress_traffics WHERE traffic_id = %d ", $traffic_id ) );
		$converted = $converted + 1;

		$wpdb->update( $wpdb->prefix . 'refpress_traffics', [ 'converted' => $converted ], [ 'traffic_id' => $traffic_id ] );

	}
	/**
	 * Action after add referral earning data
	 *
	 * @since RefPress v.1.0.0
	 *
	 * @param  int  $earning_id  Earning table Primary ID
	 * @param  array  $earning_data  Earning table raw data as array
	 */

	do_action( 'refpress_add_earning_data_after', $earning_id, $earning_data );

	return $earning_id;
}

/**
 * Get privacy policy text with link
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @return string|void
 */

function refpress_get_privacy_policy_text() {

	$page_id = refpress_get_privacy_policy_page_id();
	if ( ! $page_id ) {
		return '';
	}

	$privacy_policy_text = __( 'Privacy Policy', 'refpress' );
	$page_url            = get_the_permalink( $page_id );

	$link
		= "<a href='{$page_url}' class='refpress-privacy-policy-link' target='_blank'> {$privacy_policy_text} </a>";

	$text
		= sprintf( __( 'Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our %s.',
		'refpress' ), $link );

	return trim( apply_filters( 'refpress_get_privacy_policy_text', $text ) );
}

/**
 * Write logs for debug
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  array  $args  Argument of the log table
 *
 * @return false|int Log id on success, false on error
 */

function refpress_write_log( $args = [] ) {
	global $wpdb;

	//log_type = ACCOUNT_ISSUE, ORGANIC_VISITOR, OLD_CUSTOMER, OLD_IP, SELF_REFER, ZERO_COMMISSION

	$defaults = apply_filters( 'refpress_write_log_default_args', [
		'referer_id'         => 0,
		'referer_wp_user_id' => 0,
		'order_id'           => 0,
		'order_processed_by' => '',
		'log_type'           => 'UNKNOWN',
		'log_text'           => '',
	] );

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	//Check if passed referer_id, if not, check if there any referer_id on the cookie
	if ( empty( $referer_id ) ) {
		$referer_id = refpress_get_cookie_referer_id();

		if ( empty( $referer_id ) ) {
			return false;
		}
	}

	$account = refpress_get_account_by_id( $referer_id );
	if ( empty( $account ) ) {
		return false;
	}

	$log_data = [
		'referer_id'         => $referer_id,
		'referer_wp_user_id' => $account->user_id,
		'order_id'           => $order_id,
		'log_type'           => $log_type,
		'log_text'           => $log_text,
		'order_processed_by' => $order_processed_by,
		'created_at'         => current_time( 'mysql' ),
	];

	$inserted = $wpdb->insert( $wpdb->prefix . 'refpress_logs', $log_data );
	if ( $inserted ) {
		return $wpdb->insert_id;
	}

	return false;
}

/**
 * Get earnings
 *
 * Get all earnings data and filter the results by passing arguments
 *
 * Example usage:
 *
 *     $earnings = refpress_get_earnings( [
 *          'referer_id' => 0,
 *          'search_term' => '',
 *          'start'       => 0,
 *          'per_page'    => 10,
 *      ] );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  array  $args pass argument as array
 *
 * @return object
 */

function refpress_get_earnings( $args = [] ) {
	global $wpdb;

	$defaults = apply_filters( 'refpress_get_accounts_default_args', [
		'referer_id'  => 0,
		'search_term' => '',
		'start'       => 0,
		'per_page'    => 10,
	] );

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$response = [
		'count'   => 0,
		'results' => null,
	];

	$referer_sql = '';
	if ( ! empty( $referer_id ) && $referer_id > 0 ) {
		$referer_sql = " AND earnings.referer_id = {$referer_id} ";
	}


	$limit_query = '';
	if ( $per_page !== '-1' ) {
		$limit_query = " LIMIT {$start}, {$per_page}  ";
	}

	$search_sql = '';
	if ( ! empty( $search_term ) ) {
		$search_sql
			= " AND ( users_tbl.ID = '{$search_term}' OR users_tbl.display_name LIKE '%{$search_term}%' OR users_tbl.user_nicename LIKE '%{$search_term}%' OR users_tbl.user_email LIKE '%{$search_term}%' ) ";
	}

	$count = (int) $wpdb->get_var( "SELECT COUNT( earnings.earning_id ) 
	FROM {$wpdb->prefix}refpress_earnings as earnings
WHERE 1 = 1  {$search_sql} {$referer_sql}
ORDER BY earnings.created_at DESC {$limit_query};" );

	if ( $count > 0 ) {

		$results = $wpdb->get_results( "
		SELECT earnings.*,
		users_tbl.ID as wp_user_id,
		users_tbl.user_nicename,
		users_tbl.user_email,
		users_tbl.display_name
		
		FROM {$wpdb->prefix}refpress_earnings as earnings
		LEFT JOIN {$wpdb->users} users_tbl ON earnings.referer_id = users_tbl.ID
		WHERE 1 = 1  {$search_term} {$referer_sql}
		ORDER BY earnings.created_at DESC {$limit_query}; " );

		$response = [
			'count'   => $count,
			'results' => $results,
		];

	}

	return (object) $response;
}

function refpress_get_order_completed_stats_by_referer( $referer_id ){
	global $wpdb;

	$result = $wpdb->get_row( "SELECT COUNT(earning_id)  as count, SUM(referer_amount) as amount FROM {$wpdb->prefix}refpress_earnings WHERE referer_id = {$referer_id} AND order_status = 'completed' " );

	return $result;
}



/**
 * Get a specific earning data by passing earning id
 *
 * Get earning or commission details by earning or commission ID
 *
 * Example usage:
 *
 *     refpress_get_earning( $earning_id );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  int  $earning_id  Earning ID or Commission ID
 *
 * @return array|object|null|void Database query result in format specified by $output or null on failure.
 */

function refpress_get_earning( $earning_id ) {
	global $wpdb;

	$results = $wpdb->get_row( "
		SELECT earnings.*,
		users_tbl.ID as wp_user_id,
		users_tbl.user_nicename,
		users_tbl.user_email,
		users_tbl.display_name
		
		FROM {$wpdb->prefix}refpress_earnings as earnings
		LEFT JOIN {$wpdb->users} users_tbl ON earnings.referer_id = users_tbl.ID
		WHERE 1 = 1 AND earnings.earning_id = {$earning_id} ;" );

	return $results;
}



/**
 * Get a specific earning data by passing related Order ID
 *
 *
 * Example usage:
 *
 *     refpress_get_earning_by_order_id( $order_id );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  int  $order_id  Order ID
 *
 * @return array|object|null|void Database query result in format specified by $output or null on failure.
 */

function refpress_get_earning_by_order_id( $order_id ) {
	global $wpdb;

	$results = $wpdb->get_row( "
		SELECT earnings.*,
		users_tbl.ID as wp_user_id,
		users_tbl.user_nicename,
		users_tbl.user_email,
		users_tbl.display_name
		
		FROM {$wpdb->prefix}refpress_earnings as earnings
		LEFT JOIN {$wpdb->users} users_tbl ON earnings.referer_id = users_tbl.ID
		WHERE 1 = 1 AND earnings.order_id = {$order_id} ;" );

	return $results;
}


/**
 * Get all available integrations
 *
 * The function will return all of the available integrations as key => Name
 *
 * Example usage:
 *
 *     refpress_get_integrations(); //All integrations
 *     refpress_get_integrations( $key ); //Specific integration's name
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  null  $key
 *
 * @return mixed|void
 */

function refpress_get_integrations( $key = null ) {
	$integrations = apply_filters( 'refpress_get_integrations', [
		'woocommerce' => __( 'WooCommerce', 'refpress' ),
		'edd'         => __( 'Easy Digital Downloads (EDD)', 'refpress' ),
	] );

	if ( ! empty( $key ) ) {
		return refpress_array_get( $key, $integrations );
	}

	return $integrations;
}


/**
 * Get commission related order edit admin link
 *
 *
 * Example usage:
 *
 *     refpress_get_order_edit_link( $order_id, $processed_by );
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @param int $order_id Order ID
 * @param  string  $processed_by Processed by, eg. woocommerce, edd
 *
 * @return mixed|string|void
 */

function refpress_get_order_edit_link( $order_id, $processed_by = '' ) {
	if ( empty( $order_id ) ) {
		return '';
	}

	$link = '';
	switch ( $processed_by ) {
		case 'woocommerce':
			$link = admin_url( "post.php?post={$order_id}&action=edit" );
			break;

		case 'edd':
			$link
				= admin_url( "edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id={$order_id}" );
			break;

		default:
			$link = admin_url( "edit.php?post={$order_id}&action=edit" );
			break;
	}

	/**
	 * Filter order edit admin link
	 *
	 * @since RefPress 1.0.0
	 *
	 * @param  string  $link  Edit Link
	 * @param  int  $order_id  Order ID
	 * @param  string  $processed_by  Process By, eg. woocommerce, edd
	 */

	return apply_filters( 'refpress_get_order_edit_link', $link, $order_id, $processed_by );
}

function refpress_get_payouts( $args = [] ) {
	global $wpdb;

	$defaults = apply_filters( 'refpress_get_payouts_default_args', [
		'search_term' => '',
		'account_id'  => 0,
		'start'       => 0,
		'per_page'    => 10,
	] );

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$response = [
		'count'   => 0,
		'results' => null,
	];

	$limit_query = '';
	if ( $per_page !== '-1' ) {
		$limit_query = " LIMIT {$start}, {$per_page}  ";
	}

	if ( ! empty( $search_term ) ) {
		$search_term = " AND ( refpress_accounts.account_id = '{$search_term}' OR users_tbl.display_name LIKE '%{$search_term}%' OR users_tbl.user_nicename LIKE '%{$search_term}%' OR users_tbl.user_email LIKE '%{$search_term}%' ) ";
	}

	if ( ! empty( $account_id ) ) {
		$search_term = " AND payouts.referer_id = {$account_id} ";
	}

	$count = (int) $wpdb->get_var( "SELECT COUNT( payouts.payout_id ) 
	FROM {$wpdb->prefix}refpress_payouts as payouts
	LEFT JOIN {$wpdb->prefix}refpress_accounts refpress_accounts ON payouts.referer_id = refpress_accounts.account_id	
	LEFT JOIN {$wpdb->users} users_tbl ON refpress_accounts.user_id = users_tbl.ID
	WHERE 1 = 1 {$search_term} {$limit_query} ;" );

	if ( $count > 0 ) {

		$results = $wpdb->get_results( "
		SELECT payouts.*,

		refpress_accounts.account_id as account_id,
		refpress_accounts.user_id as user_id,
		refpress_accounts.nicename as nicename,
		refpress_accounts.commission_rate as commission_rate,
		refpress_accounts.commission_rate_type as commission_rate_type,
		refpress_accounts.earned as earned,
		refpress_accounts.paid as paid,
		refpress_accounts.converted as converted,
		refpress_accounts.visits as visits,
		refpress_accounts.status as account_status,
		
		users_tbl.user_nicename,
		users_tbl.user_email,
		users_tbl.display_name
		
		FROM {$wpdb->prefix}refpress_payouts payouts
		
		LEFT JOIN {$wpdb->prefix}refpress_accounts refpress_accounts ON payouts.referer_id = refpress_accounts.account_id
		LEFT JOIN {$wpdb->users} users_tbl ON refpress_accounts.user_id = users_tbl.ID
		WHERE 1 = 1  {$search_term}
		ORDER BY payouts.created_at DESC {$limit_query}; " );

		$response = [
			'count'   => $count,
			'results' => $results,
		];

	}

	return (object) $response;
}