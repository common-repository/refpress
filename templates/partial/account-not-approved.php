<?php
/**
 * Account not approved page
 *
 * This template can be overridden by copying it to yourtheme/refpress/partial/account-not-approved.php.
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php
$current_user = wp_get_current_user();
if ( ! empty( $current_user->display_name ) ) { ?>
    <div class="refpress-account-not-approved-greetings">
        <p class="refpress-howdy-text">
			<?php
			_e( 'Howdy, ', 'refpress' );
			echo $current_user->display_name;

			$logout_uri = refpress_account_uri( 'logout' );
			?>
            - <a href="<?php echo $logout_uri; ?>"> <?php _e( 'Log Out', 'refpress' ); ?> </a>
        </p>
    </div>
<?php } ?>

<div class="account-not-approved-wrap margin-y-5">

    <h2> <?php _e( 'Your account has not yet been approved', 'refpress' ); ?> </h2>
    <p class="describe-text"> <?php _e( 'Once your account has been approved you can view your account details, earnings, payouts on this page', 'refpress' ); ?> </p>
    <p class="describe-text"> <?php _e( 'If you think we did any mistake with your account, please contact us through the support channel', 'refpress' ); ?> </p>

</div>