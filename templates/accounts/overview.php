<?php
/**
 * Account not approved page
 *
 * This template can be overridden by copying it to yourtheme/refpress/accounts/overview.php
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$account = refpress_get_account( get_current_user_id() );
if ( ! $account ) {
    _e( 'Affiliate Account not found', 'refpress' );
    return;
}
$commission = refpress_get_commission_rate( $account );
$traffic_stats = refpress_get_traffic_stats_by_referer( $account->account_id );
$order_completed_stats = refpress_get_order_completed_stats_by_referer( $account->account_id );
?>

<h1> <?php _e( 'Affiliate Overview', 'refpress' ); ?> </h1>


<div class="refpress-dashboard-overview-wrap">

    <h4> <?php _e( 'Commission Details', 'refpress' ); ?> </h4>

    <div class="refpress-cards">

        <div class="refpress-card-item">
            <div class="refpress-single-card">

                <div class="card-title">
					<?php _e( 'Commission Rate', 'refpress' ); ?>
                </div>

                <div class="card-value">
					<?php
					$commission_type = $commission->commission_rate_type === 'percent' ? '%' : __( 'Fixed', 'refpress' );

					echo sprintf( "%s %s", number_format( $commission->commission_rate ), $commission_type ); ?>
                </div>

            </div>

        </div>
        <div class="refpress-card-item">

            <div class="refpress-single-card">

                <div class="card-title">
					<?php _e( 'Affiliate URL Variable', 'refpress' ); ?>
                </div>

                <div class="card-value">
					<?php
					$ref_variable = refpress_referral_url_param();
					echo "?{$ref_variable}=".$account->account_id;
					?>
                </div>

            </div>

        </div>
        <div class="refpress-card-item">
            <div class="refpress-single-card">
                <div class="card-title">
					<?php _e( 'Cookie Period', 'refpress' ); ?>
                </div>

                <div class="card-value">
					<?php
					$cookie_days = (int) refpress_get_setting( 'cookie_expire_in_days' );

					echo sprintf( _n( '%s Day', '%s Days', $cookie_days, 'refpress' ),
						number_format_i18n( $cookie_days ) );
					?>
                </div>
            </div>
        </div>

    </div>

    <h4> <?php _e( 'Earnings', 'refpress' ); ?> </h4>

    <div class="refpress-cards">

        <div class="refpress-card-item">
            <div class="refpress-single-card">

                <div class="card-title">
				    <?php _e( 'Earned', 'refpress' ); ?>
                </div>

                <div class="card-value">
				    <?php
                    echo refpress_price( $account->earned );
                    ?>
                </div>

            </div>

        </div>
        <div class="refpress-card-item">

            <div class="refpress-single-card">

                <div class="card-title">
				    <?php _e( 'Paid', 'refpress' ); ?>
                </div>

                <div class="card-value">
				    <?php
				    echo refpress_price( $account->paid );
				    ?>
                </div>

            </div>

        </div>
        <div class="refpress-card-item">
            <div class="refpress-single-card">
                <div class="card-title">
				    <?php _e( 'Balance', 'refpress' ); ?>
                </div>

                <div class="card-value">
				    <?php
				    echo refpress_price( $account->earned - $account->paid );
				    ?>
                </div>
            </div>
        </div>

    </div>


    <h4> <?php _e( 'Traffics', 'refpress' ); ?> </h4>







    <div class="refpress-cards">

        <div class="refpress-card-item">
            <div class="refpress-single-card">
                <div class="card-title">
					<?php _e( 'Visitors', 'refpress' ); ?>
                </div>
                <div class="card-value">
					<?php
					echo sprintf( _n( '%s Visitor', '%s Visitors', $traffic_stats->visitors, 'refpress' ), $traffic_stats->visitors );
					?>
                </div>
            </div>
        </div>

        <div class="refpress-card-item">
            <div class="refpress-single-card">
                <div class="card-title">
					<?php _e( 'Clicks', 'refpress' ); ?>
                </div>

                <div class="card-value">
					<?php
					echo sprintf( _n( '%s Click', '%s Clicks', $traffic_stats->hits, 'refpress' ), $traffic_stats->hits );
					?>
                </div>
            </div>
        </div>

        <div class="refpress-card-item">
            <div class="refpress-single-card">
                <div class="card-title">
				    <?php _e( 'Orders Completed', 'refpress' ); ?>
                </div>

                <div class="card-value">
				    <?php
				    echo $order_completed_stats->count;
				    ?>
                </div>
            </div>
        </div>


    </div>



</div>