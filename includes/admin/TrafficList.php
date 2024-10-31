<?php
/**
 * Admin Accounts List Class
 *
 * @since 1.0.0
 * @package RefPress/Classes
 */

namespace RefPress\Includes\Admin;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class TrafficList extends \WP_List_Table {
	function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'traffic',     //singular name of the listed records
			'plural'   => 'traffics',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		) );

		$this->process_bulk_action();
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'campaign':
			case 'hits':
			case 'converted':
				return $item->$column_name;
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	//Direct or unknown

	function column_referer( $item ) {
		return "<p><strong>{$item->referer_display_name}</strong></p><p>{$item->referer_email}</p><p>{$item->ip_address}</p>";
	}

	function column_referer_url( $item ) {
		$url = __( 'Direct or unknown', 'refpress' );
		if ( ! empty( $item->referer_url ) ) {
			$url = $item->referer_url;
		}

		return $url;
	}

	function column_time( $item ) {
		return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
			strtotime( $item->created_at ) );
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("account")
			/*$2%s*/ $item->traffic_id                //The value of the checkbox should be the record's id
		);
	}

	function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
			'referer'     => __( 'Referer', 'refpress' ),
			'referer_url' => __( 'Referer URL', 'refpress' ),
			'campaign'    => __( 'Campaign', 'refpress' ),
			'hits'        => __( 'Hits', 'refpress' ),
			'converted'   => __( 'Converted', 'refpress' ),
			'time'        => __( 'Time', 'refpress' ),
		);

		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'hits'      => array( 'hits', false ),     //true means it's already sorted
			'converted' => array( 'converted', false ),     //true means it's already sorted
		);

		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(//'delete'    => 'Delete'
		);

		return $actions;
	}

	function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			wp_die( 'Items deleted (or they would be if we had items to delete)!' );
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

		$orderby = refpress_get_input_text( 'orderby' );
		$order = refpress_get_input_text( 'order' );

		$args     = [
			'search_term' => $search_term,
			'start'       => ( $current_page - 1 ) * $per_page,
			'per_page'    => $per_page,
			'orderby'     => $orderby,
			'order'       => $order,
		];
		$accounts = refpress_get_traffics( $args );

		$this->items = $accounts->results;

		$this->set_pagination_args( array(
			'total_items' => $accounts->count,
			'per_page'    => $per_page,
			'total_pages' => ceil( $accounts->count / $per_page )
		) );
	}
}