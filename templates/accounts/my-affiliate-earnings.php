<?php
/**
 * My Affiliate Earnings Page
 *
 * This template can be overridden by copying it to yourtheme/refpress/accounts/my-affiliate-earnings.php
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$per_page = 20;
$current_page = isset( $_GET[ 'pagination' ] ) ? sanitize_text_field( $_GET[ 'pagination' ] ) : 0;
$current_page = max( 1, $current_page );

$referer_account = refpress_get_account( get_current_user_id() );

$args        = [
	'referer_id'  => $referer_account->account_id,
	'start'       => ( $current_page - 1 ) * $per_page,
	'per_page'    => $per_page,
];

$earnings = refpress_get_earnings( $args );

?>

<h1> <?php _e( 'My Payouts', 'refpress' ); ?> </h1>

<?php
if ( $earnings->count ) {
	?>
    <table>
        <thead>
        <tr>
            <th> <?php _e( 'Amount', 'refpress' ); ?> </th>
            <th> <?php _e( 'Status', 'refpress' ); ?> </th>
            <th> <?php _e( 'Order', 'refpress' ); ?> </th>
            <th> <?php _e( 'Time', 'refpress' ); ?> </th>
        </tr>
        </thead>

		<?php
		foreach ( $earnings->results as $earning ) {
			?>
            <tr>
                <td> <?php echo refpress_price( $earning->referer_amount ); ?> </td>
                <td> <?php echo "<span class='refpress-pill {$earning->order_status}'> {$earning->order_status} </span>"; ?> </td>
                <td> #<?php echo $earning->order_id; ?> </td>
                <td> <?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $earning->created_at ) ); ?> </td>
            </tr>
			<?php
		}
		?>
    </table>

	<?php
	echo paginate_links( array(
		'base'      => add_query_arg( 'pagination', '%#%' ),
		'format'    => '',
		'current' => $current_page,
		'total'     => ceil( $earnings->count / $per_page ),
	) );
	?>
<?php } else {
	?>
    <p><?php _e( 'No traffic generated yet', 'refpress' ); ?></p>
	<?php
}?>
