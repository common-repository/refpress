<?php


/**
 * Retrieve the RefPress Account ID
 *
 * In Short details, RefPress Account is an alias of Affiliate Account
 *
 * Example usage:
 *
 *     refpress_get_account();
 *
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_get_account();
 *
 * @param  int  $user_id WP User ID
 *
 * @return bool|object Returns Account object on success, or false on failure.
 */

function refpress_get_account( $user_id = 0 ) {
	global $wpdb;

	if ( ! $user_id && is_user_logged_in() ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id ) {
		return false;
	}

	$account = wp_cache_get( $user_id, 'refpress_accounts' );

	if ( ! $account ) {

		$account = $wpdb->get_row( " 
		SELECT refpress_accounts.*,
		users_tbl.user_nicename,
		users_tbl.user_email,
		users_tbl.display_name
		
		FROM {$wpdb->prefix}refpress_accounts as refpress_accounts
		LEFT JOIN {$wpdb->users} users_tbl ON refpress_accounts.user_id = users_tbl.ID
		WHERE refpress_accounts.user_id = {$user_id} ;" );

		if ( empty( $account ) ) {
			return false;
		}

		wp_cache_add( $user_id, $account, 'refpress_accounts' );
	}

	return $account;
}

/**
 * Get refpress referral account by account_id
 *
 *
 * Example usage:
 *
 *     refpress_get_account_by_id( $account_id );
 *
 *
 * @since RefPress 1.0.0
 *
 * @param  int  $account_id  Account ID | Referer ID
 *
 * @return bool|array|object|void|null
 */

function refpress_get_account_by_id( $account_id ) {
	global $wpdb;

	$account = wp_cache_get( $account_id, 'refpress_accounts_by_id' );

	if ( ! $account ) {
		$account = $wpdb->get_row( " 
		SELECT refpress_accounts.*,
		users_tbl.user_nicename,
		users_tbl.user_email,
		users_tbl.display_name
		
		FROM {$wpdb->prefix}refpress_accounts as refpress_accounts
		LEFT JOIN {$wpdb->users} users_tbl ON refpress_accounts.user_id = users_tbl.ID
		WHERE refpress_accounts.account_id = {$account_id} ;" );

		if ( empty( $account ) ) {
			return false;
		}

		wp_cache_add( $account_id, $account, 'refpress_accounts_by_id' );
	}

	return $account;
}


/**
 * Get Global or account specific commission rate
 *
 *
 * Example usage:
 *
 *     $global_commission = refpress_get_commission_rate();
 *     $account_specific_commission = refpress_get_commission_rate( $account );
 *
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_get_account();
 * @see refpress_get_account_by_id();
 *
 * @param  object|integer|null  $account  Account Object or Account ID
 * @param  string  $where_column  If pass id in $account, Which Columns should lookup, account_id|user_id
 *
 * @return object
 */

function refpress_get_commission_rate( $account = null, $where_column = 'account_id' ) {

	$global_commission_rate      = refpress_get_setting( 'commission_rate' );
	$global_commission_rate_type = refpress_get_setting( 'commission_rate_type' );

	$commission = [
		'commission_rate'      => $global_commission_rate,
		'commission_rate_type' => $global_commission_rate_type,
	];

	if ( ! empty( $account ) ) {
		if ( is_numeric( $account ) ) {
			if ( $where_column === 'user_id' ) {
				$account = refpress_get_account( $account );
			} else {
				$account = refpress_get_account_by_id( $account );
			}
		}

		if ( isset( $account->enable_custom_commission_rate ) && $account->enable_custom_commission_rate ) {

			$commission = [
				'commission_rate'      => $account->commission_rate,
				'commission_rate_type' => $account->commission_rate_type,
			];

		}
	}

	return (object) $commission;
}

/**
 * Determine if an account is approved or not.
 *
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_is_account_approved();
 *
 * @param int|object $account WP User ID or affiliate account Object
 *
 * @return bool
 */

function refpress_is_account_approved( $account = null ){
	if ( empty( $account ) ) {
		return false;
	}

	if ( is_numeric( $account ) ) {
		$account = refpress_get_account( $account );

		if ( ! empty( $account->status ) ) {
			return ( $account->status === 'approved' );
		}

	} elseif  ( is_object( $account ) && ! empty( $account->status ) ) {
		return ( $account->status === 'approved' );
	}

	return false;
}