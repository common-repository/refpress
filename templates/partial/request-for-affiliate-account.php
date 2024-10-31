<?php
/**
 * Request for Affiliate account page
 *
 * This template can be overridden by copying it to yourtheme/refpress/partial/request-for-affiliate-account.php.
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$join_image_src = apply_filters( 'refpress_join_promote_earn_image_src', REFPRESS_URL . 'public/images/join-promote-earn.png' ) ;
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

<div class="refpress-request-for-account-wrap margin-y-5">

    <div class="refpress-request-account-image">
        <img src="<?php echo $join_image_src; ?>" />
    </div>

    <div class="refpress-request-account-cta">

        <h2> <?php _e( 'Join, Promote, Earn', 'refpress' ); ?> </h2>
        <p class="refpress-request-account-cta-desc" >
			<?php _e( 'Once you have created your affiliate account, you will have a unique referral URL to redirect traffic to our website. Share your affiliate link using a banner, images, content, or a video. You will receive commissions once your redirected users make any purchase. The more your share, the more opportunity you create', 'refpress' ); ?>
        </p>

        <div class="refpress-join-button-wrap">
            <form action="" method="post">
				<?php refpress_generate_field_action( 'logged_user_join_request' ) ?>
                <?php refpress_form_errors(); ?>

                <fieldset>
                    <legend>
			            <?php _e( 'Promotional Information', 'refpress' ); ?>
                    </legend>

                    <div class="refpress-register-field-wrap">
                        <label>
				            <?php _e('How you will promot us?', 'refpress'); ?>
                        </label>

                        <div class="refpress-register-field-wrap">
                            <textarea name="promotional_strategies" class="refpress-form-control" placeholder="<?php _e( 'Write your promotional strategies', 'refpress' ) ?>"><?php echo refpress_input_old('promotional_strategies'); ?></textarea>
                        </div>
                    </div>

                    <div class="refpress-register-field-wrap">
                        <label>
				            <?php _e('Promotional Properties', 'refpress'); ?>
                        </label>

                        <div class="refpress-register-field-wrap">
                            <textarea name="promotional_properties" class="refpress-form-control" placeholder="<?php _e( 'Multiple URLs should be in separate lines.', 'refpress' ); ?>"><?php echo refpress_input_old('promotional_properties'); ?></textarea>

                            <p class="repress-form-help"> <?php _e( 'Enter URLs where you will promote us. It could be websites, apps, YouTube channels, social accounts, pages, groups, or anything else. Multiple URLs should be in separate lines.', 'refpress' ); ?> </p>
                        </div>
                    </div>

                </fieldset>


                <button class=""> <?php _e( 'Join Now', 'refpress' ); ?>  </button>
            </form>
        </div>

    </div>
</div>