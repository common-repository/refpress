<?php
/**
 * Account View Page
 */

defined( 'ABSPATH' ) || exit;

$account_id = (int) refpress_get_input_text( 'account_id' );
$account = refpress_get_account_by_id( $account_id );
$balance = ( $account->earned - $account->paid );
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
		<?php _e('Account View', 'refpress'); ?>

        <small>
            <a href="admin.php?page=refpress-accounts&action=edit&account_id=<?php echo $account_id; ?>"> <?php _e( 'Edit Account', 'refpress' ); ?> </a>
        </small>
    </h1>

    <hr class="wp-header-end">


    <div id="refpress-admin-wrap">

        <form action="" id="refpress-pay-referer" method="post">

			<?php refpress_generate_field_action( 'pay_to_referer' ); ?>
			<?php refpress_nonce_field(); ?>
			<?php refpress_form_errors(); ?>

            <input type="hidden" id="refpress_account_id" name="refpress_account_id" value="<?php echo $account_id; ?>">

            <table class="widefat form-table account-view" role="presentation">

                <tr>
                    <th><?php _e('Account Status', 'refpress'); ?></th>
                    <td>
						<?php
						$status_text = refpress_account_status( $account->status );
						echo "<span class='refpress-pill text-{$account->status}'> {$status_text} </span>";
						?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Name', 'refpress'); ?></th>
                    <td>
			            <?php echo $account->display_name; ?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('E-Mail', 'refpress'); ?></th>
                    <td>
			            <?php echo $account->user_email; ?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Legal Details', 'refpress'); ?></th>
                    <td>
			            <?php
			            if ( ! empty( $account->fullname ) ) {
				            echo "<p>" . sprintf( __( 'Full name : %s', 'refpress' ), $account->fullname ) . "</p>";
			            }

			            if ( ! empty( $account->country ) ) {
				            echo "<p>" . sprintf( __( 'Country : %s', 'refpress' ), refpress_get_countries( $account->country ) ) . "</p>";
			            }
			            ?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Commission Rate', 'refpress'); ?></th>
                    <td>
	                    <?php
	                    $commission_type = $account->commission_rate_type === 'percent' ? '%' : __( 'Fixed', 'refpress' );

	                    echo sprintf( "%s %s", number_format( $account->commission_rate ), $commission_type ); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Financial', 'refpress'); ?></th>
                    <td>
						<p>
							<?php

							echo sprintf( __( 'Earned : %s, ', 'refpress' ), "<strong>" . refpress_price( $account->earned ) . "</strong>" );
							echo sprintf( __( 'Paid : %s, ', 'refpress' ), "<strong>" . refpress_price( $account->paid ) . "</strong>" );
							echo sprintf( __( 'Balance : %s, ', 'refpress' ), "<strong>" . refpress_price( $balance ) . "</strong>" );
							?>

                        </p>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Payout Method', 'refpress'); ?></th>
                    <td>
						<?php
						if (  empty( $account->payout_method ) ){
							_e( 'No Payout method found', 'refpress' );
						} else {
							$saved_payout_method = refpress_get_referer_payout_method( $account );
							$enabled = refpress_array_get( 'enabled', $saved_payout_method );

							if ( $enabled ) {
								$payout_method = refpress_array_get( 'payout_method', $saved_payout_method );
								$payout_method_name = refpress_array_get( 'payout_method_name', $saved_payout_method );
								$saved_fields
									= refpress_array_get( "payout_method_fields.{$account->payout_method}",
									$saved_payout_method );
								$enabled_method = refpress_get_enabled_payout_methods( $account->payout_method );
								$method_form_fields = refpress_array_get( 'form_fields', $enabled_method );

								echo "<p class='referer-payout-method-name'> <strong> {$payout_method_name} </strong> </p>";

								foreach ( $saved_fields as $field_key => $field_value ) {
									if ( ! empty( $field_value ) ) {
										$field_title = refpress_array_get( "{$field_key}.title", $method_form_fields );

										echo "<p class='referer-payout-field'> <span class='field-title'> {$field_title} </span> : <span class='field-value'> {$field_value} </span> </p>";
									}
								}

							} else {
								_e( 'The selected payout method is no longer available', 'refpress' );
							}
						}
						?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Promotional Strategies', 'refpress'); ?></th>
                    <td>
			            <?php echo nl2br( $account->promotional_strategies ); ?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Promotional Properties', 'refpress'); ?></th>
                    <td>
			            <?php
                        if ( ! empty( $account->promotional_properties ) ) {

	                        $properties = array_map('trim', explode( "\n", $account->promotional_properties ) );
	                        $properties = array_filter( $properties );

                            if ( count( $properties ) ) {
                                $li_data = implode( "</li><li>", $properties );
                                echo "<ul> <li> {$li_data} </li> </ul>";
                            }
                        }

                         ?>
                    </td>
                </tr>

            </table>

        </form>

    </div>

</div>
