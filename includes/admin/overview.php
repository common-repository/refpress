<?php
defined( 'ABSPATH' ) || exit;

global $wpdb;

/**
 * Get Last 30 days
 */

$start_date = date("Y-m-d 00:00:00", strtotime( '-30 days' ) );
$end_date = date("Y-m-d 23:59:59");
$date_format_sql = " DATE(created_at)  as date_format ";
$date_format = get_option( 'date_format' );

$year = refpress_get_input_text( 'year' );
if ( $year ) {
    $start_date = "{$year}-01-01 00:00:00";
    $end_date = "{$year}-12-31 23:59:59";
	$date_format_sql = " MONTHNAME(created_at)  as date_format ";
	$date_format = 'F';
}

/**
 * Format Date Name
 */

$begin = new DateTime($start_date);
$end = new DateTime($end_date);
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

$datesPeriod = [];
if ( $year ) {

	for ($m=1; $m<=12; $m++) {
		$datesPeriod[ date( 'F', mktime( 0, 0, 0, $m, 1, date( 'Y' ) ) ) ] = 0;
	}

} else {
	foreach ( $period as $dt ) {
		$datesPeriod[ $dt->format( "Y-m-d" ) ] = 0;
	}
}

$referralResults = $wpdb->get_results( "
              SELECT SUM(referer_amount) as amount, 
              COUNT(earning_id)  as referral_count,
              {$date_format_sql}
              from {$wpdb->prefix}refpress_earnings 
              WHERE 1 = 1 AND order_status = 'completed'
              AND (created_at BETWEEN '{$start_date}' AND '{$end_date}')
              GROUP BY date_format
              ORDER BY created_at DESC ;");

$amount = wp_list_pluck( $referralResults, 'amount' );
$queried_date = wp_list_pluck( $referralResults, 'date_format' );
$dateWiseSales = array_combine( $queried_date, $amount );

$dataResults = array_merge($datesPeriod, $dateWiseSales);
$totalReferralAmount = array_sum( $dataResults );

$chartData = [];
foreach ( $dataResults as $date => $value ) {
    if ( $year ) {
	    $chartData['labels'][] = $date;
    } else {
	    $chartData['xAxes'][]  = date( 'd', strtotime( $date ) );
	    $chartData['labels'][] = date( 'd M', strtotime( $date ) );
    }


	$chartData[ 'value' ][]   = $value;
}

/**
 * Section Data Query
 */

$recent_affiliates = refpress_get_accounts( [ 'per_page' => 10 ] );
$traffics = refpress_get_traffics( [ 'per_page' => 10, 'start_date' => $start_date, 'end_date' => $end_date, 'orderby' => 'converted', 'order' => 'DESC' ] );

/**
 * Most Valuable Affiliates
 */
$mostValuableAffiliates = $wpdb->get_results( "
              SELECT referer_id, 
              SUM(referer_amount) as amount, 
              COUNT(earning_id)  as referral_count,
              accounts.user_id as wp_user_id,
              wp_users.user_email as user_email,
              wp_users.display_name as display_name

              from {$wpdb->prefix}refpress_earnings earnings
              LEFT JOIN {$wpdb->prefix}refpress_accounts accounts ON earnings.referer_id = accounts.account_id
              LEFT JOIN {$wpdb->users} wp_users ON accounts.user_id = wp_users.ID

              WHERE order_status = 'completed'
              AND (earnings.created_at BETWEEN '{$start_date}' AND '{$end_date}')
              GROUP BY referer_id
              ORDER BY referer_id DESC ;");

/**
 * Total Payout
 */

$total_paid = $wpdb->get_var( "SELECT SUM(amount) as payout_amount FROM {$wpdb->prefix}refpress_payouts WHERE status = 'success' AND  (created_at BETWEEN '{$start_date}' AND '{$end_date}') " );

/**
 * Filter link
 */

$last_year_link = add_query_arg( [ 'year' => date( 'Y',  strtotime( '-1 year' ) ) ], 'admin.php?page=refpress' );
$year_link = add_query_arg( [ 'year' => date( 'Y' ) ], 'admin.php?page=refpress' );

?>

<div class="wrap">
    <h1>
		<?php _e( 'Statistics - at a glance', 'refpress' ); ?>

        <small>
            <a href="<?php echo $last_year_link; ?>"><?php echo date( 'Y', strtotime( '-1 year' ) ); ?></a> /
            <a href="<?php echo $year_link; ?>"><?php echo date( 'Y' ); ?></a> /
            <a href="admin.php?page=refpress"> <?php _e( 'Last 30 days', 'refpress' ); ?> </a>
        </small>
    </h1>

    <div id="refpress-admin-wrap">



        <div class="refpress-cards">

            <div class="refpress-card-item">
                <div class="refpress-single-card">

                    <div class="card-title">
                        <?php _e( 'Referral Amount', 'refpress' ); ?>
                    </div>

                    <div class="card-value">
                        <?php echo refpress_price( $totalReferralAmount ); ?>
                    </div>

                </div>

            </div>
            <div class="refpress-card-item">

                <div class="refpress-single-card">
                    <div class="card-title"> <?php _e( 'Paid', 'refpress' ); ?> </div>
                    <div class="card-value"> <?php echo refpress_price( $total_paid ); ?> </div>
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


        <p class="refpress-admin-section-header"> <?php _e( 'Referrals in chart', 'refpress' ); ?> </p>

        <div class="refpress-overview-chart-data-wrap">
            <canvas id="RefPressChartCanvas" data-chartValue="<?php echo esc_attr( json_encode( $chartData ) ); ?>" style="width: 100%; height: 300px;"></canvas>
        </div>

        <div id="refpress-admin-overview-columns-wrap">


            <div id="dashboard-widgets" class="metabox-holder">


                <div id="postbox-container-1" class="postbox-container">

                    <div class="box-inside">
                        <p class="refpress-admin-section-header"> <?php _e( 'Referrals within date period', 'refpress' ); ?> </p>

						<?php
						if ( is_array( $referralResults ) && count( $referralResults ) ) {
							?>
                            <table class=' widefat'>
                                <tr>
                                    <th> <?php _e( 'Date', 'refpress' ); ?> </th>
                                    <th> <?php _e( 'Referral Count', 'refpress' ); ?> </th>
                                    <th> <?php _e( 'Referral Amount', 'refpress' ); ?> </th>
                                </tr>
								<?php
								foreach ( $referralResults as $referral ){
									echo "<tr> ";
									echo "<td> " . date_i18n( $date_format, strtotime( $referral->date_format ) ) . " </td>";
									echo "<td> " . refpress_price( $referral->amount ) . " </td>";
									echo "<td> {$referral->referral_count} </td>";
									echo "</tr>";
								}
								?>
                            </table>
							<?php
						} else {
							_e( 'No referral found within current date range', 'refpress' );
						}
						?>

                    </div>

                    <div class="box-inside">
                        <p class="refpress-admin-section-header"> <?php _e( 'Most converted traffics', 'refpress' ); ?> </p>

		                <?php
		                if ( $traffics->count > 0 ) {
			                ?>
                            <table class=' widefat fixed'>
                                <tr>
                                    <th> <?php _e( 'Referer', 'refpress' ); ?> </th>
                                    <th> <?php _e( 'URL', 'refpress' ); ?> </th>
                                    <th> <?php _e( 'Hits / Converted', 'refpress' ); ?> </th>
                                </tr>
				                <?php
				                foreach ( $traffics->results as $traffic ){
					                $referer_url = __( 'Direct or unknown', 'refpress' );
					                if ( ! empty( $traffic->referer_url ) ) {
						                $referer_url = $traffic->referer_url;
					                }

					                echo "<tr> ";
					                echo "<td> <p><strong>{$traffic->referer_display_name}</strong></p> <small class='text-light'>{$traffic->referer_email}</small> </td>";
					                echo "<td> <small class='text-light'>{$referer_url}</small> </td>";
					                echo "<td> {$traffic->hits} / {$traffic->converted} </td>";
					                echo "</tr>";
				                }
				                ?>
                            </table>
			                <?php
		                } else {
			                _e( 'No traffics found', 'refpress' );
		                }
		                ?>
                    </div>





                </div>


                <div id="postbox-container-2" class="postbox-container">

                    <div class="box-inside">
                        <p class="refpress-admin-section-header"> <?php _e( 'Most Valuable Affiliates', 'refpress' ); ?> </p>

		                <?php
		                if ( is_array( $mostValuableAffiliates ) && count( $mostValuableAffiliates ) ) {
			                ?>
                            <table class=' widefat'>
                                <tr>
                                    <th> <?php _e( 'Name', 'refpress' ); ?> </th>
                                    <th> <?php _e( 'E-Mail', 'refpress' ); ?> </th>
                                    <th> <?php _e( 'Earned', 'refpress' ); ?> </th>
                                </tr>
				                <?php

				                foreach ( $mostValuableAffiliates as $affiliate ){
					                echo "<tr> ";

					                echo "<td> <a href='admin.php?page=refpress-accounts&sub_page=view-account&account_id={$affiliate->referer_id}'> <strong> {$affiliate->display_name} </strong> </a> </td>";
					                echo "<td> {$affiliate->user_email} </td>";
					                echo "<td> " . refpress_price( $affiliate->amount ) . " </td>";

					                echo "</tr>";
				                }

				                ?>

                            </table>
			                <?php
		                } else {
			                _e( 'No affiliates found', 'refpress' );
		                }
		                ?>
                    </div>

                    <div class="box-inside">
                        <p class="refpress-admin-section-header"> <?php _e( 'Most Recent Affiliates', 'refpress' ); ?> </p>

						<?php
						if ( $recent_affiliates->count > 0 ) {
							?>
                            <table class=' widefat'>
                                <tr>
                                    <th> <?php _e( 'Name', 'refpress' ); ?> </th>
                                    <th> <?php _e( 'E-Mail', 'refpress' ); ?> </th>
                                </tr>
								<?php

								foreach ( $recent_affiliates->results as $affiliate ){
									echo "<tr> ";

									echo "<td> <a href='admin.php?page=refpress-accounts&sub_page=view-account&account_id={$affiliate->account_id}'> <strong> {$affiliate->display_name} </strong> </a> </td>";
									echo "<td> {$affiliate->user_email} </td>";

									echo "</tr>";
								}

								?>

                            </table>
							<?php
						} else {
							_e( 'No affiliates found', 'refpress' );
						}
						?>
                    </div>

                </div>

            </div>

        </div>

        <div class="clear"></div>



    </div>


</div>