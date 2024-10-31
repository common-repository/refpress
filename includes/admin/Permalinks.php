<?php
/**
 * Bootstrap class for admin
 */

namespace RefPress\Includes\Admin;

defined( 'ABSPATH' ) || exit;

class Permalinks {

	public function __construct(){
		add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
		add_action( 'generate_rewrite_rules', [ $this, 'add_rewrite_rules' ] );
	}

	public function add_query_vars( $vars ) {
		$vars = array_merge( $vars, [ 'account_page' ] );

		return $vars;
	}

	public function add_rewrite_rules( $wp_rewrite ) {
		$account_menu_items = refpress_account_menu_items();

		foreach ( $account_menu_items as $menu_slug => $menu_title ) {
			$new_rules["(.+?)/{$menu_slug}/?$"] = 'index.php?pagename=' . $wp_rewrite->preg_index( 1 ) . '&account_page=' . $menu_slug;
		}

		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}


}