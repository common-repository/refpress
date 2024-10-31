<?php

add_filter( 'template_include', 'refpress_account_page_template' );

function refpress_account_page_template( $template_path ){

	if ( refpress_is_account_page() ) {
		$current_account_page = get_query_var( 'account_page', 'overview' );
		$menu_item = refpress_account_menu_items( $current_account_page );
		$is_for_pro = refpress_array_get( 'dependency', $menu_item );
		$has_pro = refpress_has_pro();

		if ( $is_for_pro && ! $has_pro ) {
			return $template_path;
		}

		$template_path = refpress_locate_template( 'account' );
	}

	return $template_path;
}


/**
 * Hooks a function on to a specific RefPress action.
 *
 * @since 1.0.0
 *
 * @param  string  $tag  The name of the action to which the $function_to_add is hooked.
 * @param  callable  $function_to_add  The name of the function you wish to be called.
 * @param  int  $priority  Optional. Used to specify the order in which the functions
 *                                  associated with a particular action are executed. Default 10.
 *                                  Lower numbers correspond with earlier execution,
 *                                  and functions with the same priority are executed
 *                                  in the order in which they were added to the action.
 * @param  int  $accepted_args  Optional. The number of arguments the function accepts. Default 1.
 *
 * @return true Will always return true.
 */

function add_refpress_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
	add_action( 'refpress_' . $tag, $function_to_add, $priority, $accepted_args );
}

add_action( 'refpress_db_update_after', 'refpress_clean_cache', 10, 3 );

function refpress_clean_cache( $table, $data, $where ){
	global $wpdb;

	if ( $wpdb->prefix . 'refpress_accounts' === $table ) {
		//Delete Account cache after update

		$account_id = refpress_array_get( 'account_id', $where );
		if ( $account_id ) {
			$account = refpress_get_account_by_id( $account_id );
			wp_cache_delete( $account->user_id, 'refpress_accounts' );
			wp_cache_delete( $account->account_id, 'refpress_accounts_by_id' );
		}
	}
}

add_action( 'refpress_new_account_after', 'refpress_pending_account_count_cache_clear' );
add_action( 'refpress_accounts_status_update_after', 'refpress_pending_account_count_cache_clear' );

function refpress_pending_account_count_cache_clear(){
	wp_cache_delete( 'refpress_pending_account_count' );
}