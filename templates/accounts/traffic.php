<?php
/**
 * Traffic page
 *
 * This template can be overridden by copying it to yourtheme/refpress/accounts/traffic.php
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$per_page = 20;
$current_page = isset( $_GET[ 'pagination' ] ) ? sanitize_text_field( $_GET[ 'pagination' ] ) : 0;
$current_page = max( 1, $current_page );

$account = refpress_get_account( get_current_user_id() );

$args        = [
	'referer_id'  => $account->account_id,
	'start'       => ( $current_page - 1 ) * $per_page,
	'per_page'    => $per_page,
];

$traffics = refpress_get_traffics( $args );
?>

<h1> <?php _e( 'All Traffics', 'refpress' ); ?> </h1>

<?php
if ( $traffics->count ) {
	?>
    <table>
        <thead>
        <tr>
            <th> <?php _e( 'Referral URL', 'refpress' ); ?> </th>
            <th> <?php _e( 'Campaign', 'refpress' ); ?> </th>
            <th> <?php _e( 'Hits', 'refpress' ); ?> </th>
            <th> <?php _e( 'Time', 'refpress' ); ?> </th>
        </tr>
        </thead>

		<?php
		foreach ( $traffics->results as $traffic ) {
			?>
            <tr>
                <td> <?php echo ! empty( $traffic->referer_url ) ? $traffic->referer_url : __( 'Direct or unknown', 'refpress' ); ?> </td>
                <td> <?php echo $traffic->campaign; ?> </td>
                <td> <?php echo $traffic->hits; ?> </td>
                <td> <?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $traffic->created_at ) ); ?> </td>
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
		'total'     => ceil( $traffics->count / $per_page ),

	) );
	?>
<?php } else {
	?>
    <p><?php _e( 'No traffic generated yet', 'refpress' ); ?></p>
	<?php
}?>
