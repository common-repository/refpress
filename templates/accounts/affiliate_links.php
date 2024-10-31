<?php
/**
 * Statistics page
 *
 * This template can be overridden by copying it to yourtheme/refpress/accounts/statistics.php
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$account = refpress_get_account();
$ref_param = refpress_referral_url_param();
?>

<div class="refpress-link-generator-wrap">

    <h1> <?php _e( 'Affiliate Links', 'refpress' ); ?> </h1>
    <p> <?php _e( 'Promote us with a simple link', 'refpress' ); ?> </p>

    <hr />

    <p> <?php _e( 'Your affiliate account ID', 'refpress' ); ?> : <?php echo $account->account_id; ?> </p>
    <p> <?php _e( 'Your default affiliate link', 'refpress' ); ?> : <?php echo esc_url( add_query_arg( [ $ref_param => $account->account_id ], trailingslashit( home_url() ) ) ); ?> </p>

    <h3> <?php _e( 'Create a Link', 'refpress' ); ?> </h3>

    <div class="refpress-link-generator-form-wrap">
        <form id="refpressLinkGeneratorForm" action="" method="post">
            <div class="refpress-link-generator-field-wrap landing-page-url-input-wrap">
                <label for="landing-page-url-input">
				    <?php _e( 'Enter a landing page url from this site', 'refpress' ); ?>
                </label>

                <div class="refpress-link-generator-form-input-wrap">
                    <input type="text" name="landing-page-url-input" id="landing-page-url-input" value="" placeholder="<?php _e( 'Landing page url', 'refpress' ); ?>">
                </div>
            </div>

            <div class="refpress-link-generator-field-wrap refpress-campaign-field-wrap">
                <label for="refpress-campaign-input">
				    <?php _e( 'Campaign', 'refpress' ); ?>
                </label>

                <div class="refpress-link-generator-form-input-wrap">
                    <input type="text" name="refpress-campaign-input" id="refpress-campaign-input" value="" placeholder="<?php _e( 'Campaign Name', 'refpress' ); ?>">
                </div>
            </div>

            <div class="refpress-link-generator-field-wrap refpress-link-generator-btn-wrap">
                <button type="submit" name="refpress_register_instructor_btn" value="register" class="refpress-btn  refpress-btn-primary">
				    <?php _e('Generate Affiliate Link', 'refpress'); ?>
                </button>
            </div>
        </form>
    </div>

    <div id="refpressGeneratorlinkWrap" style="display: none;">
        <p> <?php _e( 'Use this link to promote us', 'refpress' ); ?> </p>

        <p>
            <input type="text" name="repressGeneratedLink" id="repressGeneratedLink" value="" />
        </p>
        <p class="refpressLinkCopyWrap">
            <a href="javascript:;" id="refpressCopyLinkSelector"> <?php _e( 'Copy link', 'refpress' ); ?> </a>
        </p>
    </div>

</div>