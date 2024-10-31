<?php
/**
 * Commissions List Class
 *
 * @since 1.0.0
 * @package RefPress/Classes
 */

namespace RefPress\Includes\Admin;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class CommissionsList extends \WP_List_Table {
	function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'earning_id',     //singular name of the listed records
			'plural'   => 'earning_ids',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		) );

		$this->process_bulk_action();
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'test_column':
				return $item->$column_name;
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	function column_referer( $item ) {
		echo "<p> <strong> {$item->display_name} </strong> </p>";

		$actions      = [];
		$request_page = sanitize_text_field( $_REQUEST['page'] );

		if ( refpress_has_pro() ) {
			$actions['edit'] = sprintf( '<a href="?page=%s&action=%s&earning_id=%s"> ' . __( 'Edit', 'refpress' ) . ' </a>', $request_page, 'edit', $item->earning_id );

			echo $this->row_actions( $actions, true );
		}
	}

	function column_amount( $item ) {
		echo refpress_price( $item->referer_amount );
	}

	function column_status( $item ) {
		echo "<span class='refpress-pill {$item->order_status}'> {$item->order_status} </span>";
	}

	function column_order_id( $item ) {
		return " <a href='" . refpress_get_order_edit_link( $item->order_id, $item->process_by ) . "'> #{$item->order_id} </a>";
	}

	function column_time( $item ) {
		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
			strtotime( $item->created_at ) );
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("account")
			/*$2%s*/ $item->earning_id                //The value of the checkbox should be the record's id
		);
	}

	function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />', //Render a checkbox instead of text
			'referer'  => __( 'Referer', 'refpress' ),
			'amount'   => __( 'Amount', 'refpress' ),
			'order_id' => __( 'Order ID', 'refpress' ),
			'status'   => __( 'Status', 'refpress' ),
			'time'     => __( 'Time', 'refpress' ),
		);

		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(//'visits'     => array('visits',false),     //true means it's already sorted
		);

		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = [];

		if ( refpress_has_pro() ) {
			$actions['delete'] = __( 'Delete', 'refpress' );
		}

		return $actions;
	}

	function process_bulk_action() {

		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-earning_ids' ) ) {
			return;
		}

		$ids = isset( $_REQUEST['earning_id'] ) ? wp_parse_id_list( wp_unslash( $_REQUEST['earning_id'] ) )
			: array();  // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids = array_map( 'absint', $ids );

		if ( empty( $ids ) ) {
			return;
		}

		global $wpdb;

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			if ( is_array( $ids ) && count( $ids ) ) {
				foreach ( $ids as $id ) {
					$wpdb->delete( $wpdb->prefix . 'refpress_earnings', [ 'earning_id' => $id ] );
				}
			}

		}
	}

	function prepare_items() {
		$per_page = 20;

		$search_term = '';
		if ( isset( $_REQUEST['s'] ) ) {
			$search_term = sanitize_text_field( $_REQUEST['s'] );
		}

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$current_page = $this->get_pagenum();

		$args     = [
			'search_term' => $search_term,
			'start'       => ( $current_page - 1 ) * $per_page,
			'per_page'    => $per_page,
		];
		$accounts = refpress_get_earnings( $args );

		$this->items = $accounts->results;

		$this->set_pagination_args( array(
			'total_items' => $accounts->count,
			'per_page'    => $per_page,
			'total_pages' => ceil( $accounts->count / $per_page )
		) );
	}
}