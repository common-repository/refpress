<?php
/**
 * Payout Settings page
 *
 * This template can be overridden by copying it to yourtheme/refpress/accounts/payout-settings.php
 *
 *
 * @package RefPress/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$referer_account = refpress_get_account();
if ( empty( $referer_account ) ) {
    _e( 'Invalid Account', 'refpress' );
    return;
}

$payout_methods = refpress_get_enabled_payout_methods();
$referer_payout_methods = refpress_get_referer_payout_method( $referer_account );
$payout_settings = refpress_get_payout_settings();
$countries = refpress_get_countries();
?>

<h1> <?php _e( 'Payout Settings', 'refpress' ); ?> </h1>


<?php
if ( empty( $payout_methods ) ) {
	_e( 'No payout methods available right now', 'refpress' );
	return;
}
?>

<div class="refpress-payout-settings-wrap">

    <form method="post" enctype="multipart/form-data">

		<?php refpress_nonce_field(); ?>
		<?php refpress_generate_field_action( 'user_save_payout_settings' ); ?>

        <fieldset>
            <legend> <?php _e( 'Legal Details', 'refpress' ); ?> </legend>

            <div class="payout-form-field-wrap">
                <label> <?php _e( 'Country', 'refpress' ); ?> </label>

                <div class="payout-form-field">
                    <select name="country">
                        <option value=""> <?php _e( 'Select Country', 'refpress' ); ?> </option>

                        <?php
                        foreach ( $countries as $country_code => $country_name ) {
                            echo "<option value='{$country_code}' " . selected( $country_code, $referer_account->country, false ) . " > {$country_name} </option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="payout-form-field-wrap">
                <label> <?php _e( 'Full Name', 'refpress' ); ?> </label>

                <div class="payout-form-field">
                    <input type="text" name="fullname" value="<?php echo $referer_account->fullname; ?>" >
                </div>
            </div>
        </fieldset>


        <fieldset>
            <legend> <?php _e( 'How would you like to be paid?', 'refpress' ); ?> </legend>

            <div class="payout-form-field-wrap">
                <label>
					<?php _e('Payment Method', 'refpress'); ?>
                </label>

                <div class="payout-form-field">

                    <select id="refpress_payout_method_select" class="refpress-payout-method-select" name="payout_method">
                        <option value=""> <?php _e( 'Select Payout Method', 'refpress' ); ?> </option>
						<?php foreach ( $payout_methods as $payout_method_id => $payout_method ) {
							$method_name = refpress_array_get( 'method_name', $payout_method );
							echo "<option value='{$payout_method_id}' " . selected( $payout_method_id, $referer_account->payout_method, false ) . " > {$method_name} </option>";
						} ?>

                    </select>

                </div>
            </div>

        </fieldset>


        <div id="refpress-enabled-payouts-wrap">

			<?php foreach ( $payout_methods as $payout_method_id => $payout_method ) {
			    $instruction = refpress_array_get( $payout_method_id . ".instruction", $payout_settings );
				$method_name = refpress_array_get( 'method_name', $payout_method );
				$form_fields = refpress_array_get( 'form_fields', $payout_method );

				$saved_payout_method_fields = refpress_array_get( "payout_method_fields.{$payout_method_id}", $referer_payout_methods );
				?>

                <div id="payout-method-form-<?php echo $payout_method_id; ?>" class="payout-method-form-wrap" style="display: <?php echo ( ! empty( $referer_account->payout_method )  ) ? ( $referer_account->payout_method == $payout_method_id ? 'block' : 'none' ) : 'none'; ?>;">

                    <fieldset>
                        <legend> <?php echo $method_name; ?> </legend>

						<?php
						if ( is_array( $form_fields ) && count( $form_fields ) ) {
							foreach ( $form_fields as $field_key => $field ){
								$field_title = refpress_array_get( 'title', $field );
								$field_type = refpress_array_get( 'type', $field );
								$options = refpress_array_get( 'options', $field );

								$field_name = "payout_method_fields[$payout_method_id][$field_key]";

								$saved_field_value = refpress_array_get( $field_key, $saved_payout_method_fields, '' );
								?>
                                <div class="payout-form-field-wrap">
                                    <label> <?php echo $field_title; ?> </label>

                                    <div class="payout-form-field">

										<?php
										if ( $field_type === 'text' || $field_type === 'email' || $field_type === 'number' ) {
											echo "<input type='{$field_type}' name='$field_name' value='{$saved_field_value}' >";
										}

										if ( $field_type === 'textarea' ) {
											echo "<textarea name='$field_name' >{$saved_field_value}</textarea>";
										}
										if ( $field_type === 'select' ) {
											echo "<select name='{$field_name}'>";

											echo "<option value=''> " . __( 'Select Option', 'refpress' ) . " </option>";
											foreach ( $options as $option_key => $option ) {
												echo "<option value='{$option_key}' " . selected( $option_key, $saved_field_value, false ) . " > {$option} </option>";
											}

											echo "</select>";
										}
										?>

                                    </div>
                                </div>
								<?php
							}
						}


						if ( ! empty( $instruction ) ) {
							echo "<div class='payout-method-instruction'> <p> <strong> " . __( 'Notes', 'refpress' ) . " </strong> </p> <p> {$instruction} </p> </div>";
						}

						?>
                    </fieldset>
                </div>

				<?php
			} ?>
        </div>


        <div class="refpress-register-field-wrap refpress-reg-form-btn-wrap">
            <button type="submit" name="refpress_register_instructor_btn" value="register" class="refpress-btn  refpress-btn-primary">
				<?php _e('Save Changes', 'refpress'); ?>
            </button>
        </div>

    </form>


</div>