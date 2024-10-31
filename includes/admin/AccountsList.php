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

class AccountsList extends \WP_List_Table {
	function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'account',     //singular name of the listed records
			'plural'   => 'accounts',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		) );

		$this->process_bulk_action();
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'display_name':
				return $item->$column_name;
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * @param $item
	 *
	 * Completed Course by User
	 */
	function column_status( $item ) {
		$text = refpress_account_status( $item->status );

		echo "<span class='refpress-pill text-{$item->status}'> {$text} </span>";
	}

	function column_earned( $item ){

		$output = "<p> " . __( 'Earned', 'refpress' ) . " - " . refpress_price( $item->earned ) . " </p>";
		$output .= "<p> " . __( 'Paid', 'refpress' ) . " - " . refpress_price( $item->paid ) . " </p>";
		$output .= "<hr />";

		$balance = $item->earned - $item->paid;
		$output .= "<p> " . __( 'Balance', 'refpress' ) . " - <strong> " . refpress_price( $balance ) . " </strong> </p>";

		echo $output;
	}

	function column_stats( $item ){
		$output = "<p> " . __( 'Visits', 'refpress' ) . " - {$item->visits} </p>";

		$output .= "<p> " . __( 'Converted', 'refpress' ) . " - {$item->converted} (" . refpress_percentage_of( $item->converted, $item->visits, true ) . ") </p>";

		echo $output;
	}

	function column_actions( $item ){
		$min_payout_amount = refpress_get_setting( 'minimum_payout_amount', 50 );
		$balance = ( $item->earned - $item->paid );

		$actions = [];


		$actions[] = "<a href='admin.php?page=refpress-accounts&sub_page=view-account&account_id={$item->account_id}'> " . __( 'View', 'refpress' ) . " </a>";

		if ( $balance >= $min_payout_amount ) {
			$actions[] = "<a href='admin.php?page=refpress-accounts&sub_page=pay-referer&account_id={$item->account_id}'> <strong> " . __( 'Pay', 'refpress' ) . " </strong> </a>";
		}

		$actions[] = "<a href='admin.php?page=refpress-payout-history&account_id={$item->account_id}'> " . __( 'Payout History', 'refpress' ) . " </a>";

		echo implode( ' | ', $actions );
	}

	function column_display_name( $item ) {
		//Build row actions
		$actions = [];

		$request_page = sanitize_text_field( $_REQUEST['page'] );

		switch ( $item->status ) {
			case 'pending':
			case 'blocked':

				$actions['approved']
					= sprintf( '<a class="" data-action="approve" data-account-id="' . $item->account_id . '" href="?page=%s&action=%s&account_id=%s"> ' . __( 'Approve', 'refpress' ) . ' </a>', $request_page, 'approve', $item->account_id );
				break;
			case 'approved':
				$actions['blocked']
					= sprintf( '<a class="blocked" data-action="blocked" data-account-id="' . $item->account_id . '" href="?page=%s&action=%s&account_id=%s"> ' . __( 'Block', 'refpress' ) . ' </a>', $request_page, 'blocked', $item->account_id );
				break;
		}

		$infoHtml = "<p class='col-info-text'>" . __( 'Affiliate ID', 'refpress' ) . ": <span> #{$item->account_id} </span> </p>";
		$infoHtml .= "<p class='col-info-text'>" . __( 'E-Mail', 'refpress' ) . ": <span> {$item->user_email} </span> </p>";

		$payout_method = $item->payout_method;
		if ( $payout_method ) {
			$payout_method_name = refpress_array_get( "{$payout_method}.method_name", refpress_get_payout_methods() );
			$infoHtml .= "<p class='col-info-text'>" . __( 'Payout Method ', 'refpress' ) . ": <span> {$payout_method_name} </span> </p>";

		}


		$actions = apply_filters( 'refpress_accounts_list_actions', $actions, $item );

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s <div class="refpress-account-list-info-wrap"> %4$s </div>',
			$item->display_name,
			$item->account_id,
			$this->row_actions( $actions, true ),
			$infoHtml
		);
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("account")
			/*$2%s*/ $item->account_id                //The value of the checkbox should be the record's id
		);
	}

	function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />', //Render a checkbox instead of text
			'display_name' => __( 'Name', 'refpress' ),
			'status'       => __( 'Status', 'refpress' ),
			'earned'       => __( 'Earnings', 'refpress' ),
			'stats'        => __( 'Stats', 'refpress' ),
			'actions'      => __( 'Actions', 'refpress' ),
		);

		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'earned'     => array('earned', false),     //true means it's already sorted
		);

		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(//'delete'    => 'Delete'
		);

		return $actions;
	}

	function process_bulk_action() {
		if ( 'approve' === $this->current_action() ) {
			$account_id = (int) refpress_get_input_text( 'account_id' );
			refpress_accounts_status_update( $account_id );
		}

		if ( 'blocked' === $this->current_action() ) {
			$account_id = (int) refpress_get_input_text( 'account_id' );
			refpress_accounts_status_update( $account_id, 'blocked' );
		}

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

		$orderby = refpress_get_input_text( 'orderby' );
		$order = refpress_get_input_text( 'order' );

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$current_page = $this->get_pagenum();

		$args        = [
			'search_term' => $search_term,
			'start'       => ( $current_page - 1 ) * $per_page,
			'per_page'    => $per_page,
			'orderby'    => $orderby,
			'order'    => $order,
		];
		$accounts = refpress_get_accounts( $args );

		$this->items = $accounts->results;

		$this->set_pagination_args( array(
			'total_items' => $accounts->count,
			'per_page'    => $per_page,
			'total_pages' => ceil( $accounts->count / $per_page )
		) );
	}
}