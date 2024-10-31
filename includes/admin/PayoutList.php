<?php
/**
 * Admin Payout List Class
 *
 * @since 1.0.0
 * @package RefPress/Classes
 */

namespace RefPress\Includes\Admin;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class PayoutList extends \WP_List_Table {
	function __construct() {
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'payout',     //singular name of the listed records
			'plural'   => 'payouts',    //plural name of the listed records
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

	function column_paid_to( $item ){
		echo $item->display_name . '<br />';
		echo $item->user_email . '<br />';

		echo '<hr />';

		$actions = [];

		if ( $item->status !== 'success' ) {
			$actions[ 'success' ] = "<a href='" . admin_url( "admin.php?page=refpress-payout-history&action=success&payout_id=".$item->payout_id ) . "'> " . __( 'Approve', 'refpress' ) . " </a>";
		}

		if ( $item->status !== 'rejected' ) {
			$actions[ 'rejected' ] = "<a href='" . admin_url( "admin.php?page=refpress-payout-history&action=rejected&payout_id=".$item->payout_id ) . "'> "
			     . __( 'Reject', 'refpress' ) . " </a>";
		}

		if ( is_array( $actions ) && count( $actions ) ) {
			$action_i = 0;

			echo '<div class="row-actions visible">';
			foreach ( $actions as $action_key => $action ) {
				$action_i++;

				$separator = $action_i > 1 ? ' | ' : '';
				echo "<span class='{$action_key}'> $separator.$action </span>";
			}
			echo "</div>";
		}

	}

	function column_amount( $item ){

		/*
		echo sprintf( __( 'Earned - %s', 'refpress' ), refpress_price( $item->earned ) ) . '<br />';
		echo sprintf( __( 'Paid - %s', 'refpress' ), refpress_price( $item->paid ) ) . '<br />';
		echo '<hr />';

		echo sprintf( __( 'Balance - %s', 'refpress' ), '<strong> ' . refpress_price( $item->earned - $item->paid ) . ' </strong>' );
		*/

		echo refpress_price( $item->amount );
	}

	function column_payout_method( $item ){

		if ( ! $item->payout_method_id ) {
			return;
		}

		$method = $item->payout_method_id;
		$method_data = maybe_unserialize( $item->payout_method_data );

		$payout_method = refpress_get_payout_methods( $method );
		$method_form_fields = refpress_array_get( 'form_fields', $payout_method );

		$payout_method_name = refpress_array_get( 'method_name', $payout_method );

		echo "<p class='payout-method-name'> <strong> {$payout_method_name} </strong> </p>";


		foreach ( $method_data as $field_key => $field_value ) {
			if ( ! empty( $field_value ) ) {
				$field_title = refpress_array_get( "{$field_key}.title", $method_form_fields );

				echo "<p class='payout-field'> <span class='field-title'> {$field_title} </span> : <span class='field-value'> {$field_value} </span> </p>";
			}
		}

	}

	function column_status( $item ){
		echo "<span class='refpress-pill text-{$item->status}'> {$item->status} </span>";
	}

	function column_time( $item ){
		echo date_i18n( get_option( 'date_format' ), strtotime( $item->created_at ) );
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
			//'cb'           => '<input type="checkbox" />', //Render a checkbox instead of text
			'paid_to' => __( 'Paid To', 'refpress' ),
			'amount'       => __( 'Amount', 'refpress' ),
			'payout_method'       => __( 'Payout Method', 'refpress' ),
			'status'       => __( 'Status', 'refpress' ),
			'time'       => __( 'Time', 'refpress' ),
		);

		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			//'visits'     => array('visits',false),     //true means it's already sorted
		);

		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(//'delete'    => 'Delete'
		);

		return $actions;
	}

	function process_bulk_action() {
		if ( 'success' === $this->current_action() || 'rejected' === $this->current_action() ) {
			$payout_id = (int) refpress_get_input_text( 'payout_id' );
			refpress_payout_status_change( $payout_id, $this->current_action() );
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

		$account_id = (int) refpress_get_input_text( 'account_id' );

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$current_page = $this->get_pagenum();

		$args        = [
			'search_term' => $search_term,
			'account_id'  => $account_id,
			'start'       => ( $current_page - 1 ) * $per_page,
			'per_page'    => $per_page,
		];
		$payouts = refpress_get_payouts( $args );

		$this->items = $payouts->results;

		$this->set_pagination_args( array(
			'total_items' => $payouts->count,
			'per_page'    => $per_page,
			'total_pages' => ceil( $payouts->count / $per_page )
		) );
	}
}