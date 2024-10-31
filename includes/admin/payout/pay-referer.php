<?php
/**
 * Payout Admin Page
 */

defined( 'ABSPATH' ) || exit;

$account_id = (int) refpress_get_input_text( 'account_id' );
$account = refpress_get_account_by_id( $account_id );
$balance = ( $account->earned - $account->paid );
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
		<?php _e('Pay to referer', 'refpress'); ?>
    </h1>

    <p><?php _e( 'Pay to referer from their earnings. The process is manual', 'refpress' ); ?></p>

    <hr class="wp-header-end">


    <div id="refpress-pay-to-referer-wrap">

        <form action="" id="refpress-pay-referer" method="post">

		    <?php refpress_generate_field_action( 'pay_to_referer' ); ?>
		    <?php refpress_nonce_field(); ?>
		    <?php refpress_form_errors(); ?>

            <input type="hidden" id="refpress_account_id" name="refpress_account_id" value="<?php echo $account_id; ?>">


            <table class="form-table" role="presentation">

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
                    <th><?php _e('Pay to', 'refpress'); ?></th>
                    <td>
					    <p>
						    <?php
						    echo $account->display_name." <span class='description'> ({$account->user_email}) </span> ";
						    ?>
                        </p>

                        <p>
                            <br />
                            <strong> <?php _e( 'Legal Details', 'refpress' ); ?> </strong>
                        </p>

                        <?php
                        if ( ! empty( $account->fullname ) ) {
                            echo "<p>" . sprintf( __( 'Full name : %s', 'refpress' ), $account->fullname ) . "</p>";
                        }
                        ?>


	                    <?php
	                    if ( ! empty( $account->country ) ) {
		                    echo "<p>" . sprintf( __( 'Country : %s', 'refpress' ), refpress_get_countries( $account->country ) ) . "</p>" ;
	                    }
	                    ?>

                    </td>
                </tr>

                <tr>
                    <th><?php _e('Earned', 'refpress'); ?></th>
                    <td>
					    <?php
					    echo refpress_price( $account->earned );
					    ?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Paid', 'refpress'); ?></th>
                    <td>
					    <?php
					    echo refpress_price( $account->paid );
					    ?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Balance', 'refpress'); ?></th>
                    <td>
					    <?php
					    echo refpress_price( $balance );
					    ?>
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
                    <th><?php _e('Amount', 'refpress'); ?></th>
                    <td>

                        <input type="text" name="pay_amount" class="regular-text">
                        <p class="description"> <?php echo sprintf( __( 'Maximum payable amount is %s', 'refpress' ), '<strong> ' . refpress_price( $balance ) . ' </strong>' ) ; ?>  </p>
                    </td>
                </tr>

            </table>


            <p>
                <button type="submit" class="button button-primary">
				    <?php _e('Pay', 'refpress'); ?>
                </button>
            </p>



        </form>

    </div>

</div>
