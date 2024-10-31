

<div id="refpress-admin-payout-methods-settings-wrap" class="refpress-payout-method-configure-wrap">
	<?php
	$payout_methods = refpress_get_payout_methods();

	echo '<div class="refpress-payout-methods-navbar">';
	foreach ( $payout_methods as $method_id => $method ) {
		$method_name = refpress_array_get( 'method_name', $method );
		$target_content_id = esc_attr( "refpress-settings-payout-method-".$method_id );

		echo "<a href='javascript:;' class='refpress-admin-payout-nav-method-item' data-target-id='{$target_content_id}'> {$method_name} </a>";
	}
	echo '</div>';

	?>

	<div id="refpress-settings-payout-method-form">

		<?php
        $enabled_methods = refpress_get_enabled_payout_methods();

		$method_i = 0;
		foreach ( $payout_methods as $method_id => $method ) {
			$method_i++;

			$method_name = refpress_array_get( 'method_name', $method );
			$form_fields = refpress_array_get( 'form_admin_fields', $method );

			$enabled = isset( $enabled_methods[ $method_id ] );
			?>

			<div id="refpress-settings-payout-method-<?php echo $method_id; ?>" class="refpress-settings-payout-method-content" style="display: <?php echo $method_i == 1 ? 'block':'none'; ?>;">

				<table class="form-table" role="presentation">

					<tr>
						<th scope="row"> <label> <?php _e( 'Enable / Disable', 'refpress' ); ?> </label> </th>

						<td>
							<label>
								<input type="checkbox" name="refpress_payout_settings[<?php echo $method_id; ?>][enabled]" value="1" <?php checked( $enabled, 1 ); ?> >

								<?php echo sprintf( __( 'Enable %s', 'refpress' ), $method_name ); ?>
							</label>
						</td>
					</tr>

					<?php if ( is_array( $form_fields ) && count( $form_fields ) ) {
						foreach ( $form_fields as $field_id => $field ) {
							$field_title = refpress_array_get( 'title', $field );
							$field_type = refpress_array_get( 'type', $field );
							$description = refpress_array_get( 'description', $field );
							$field_name = "refpress_payout_settings[{$method_id}][{$field_id}]";

							$field_value = refpress_get_payout_settings( $field_id, $method_id );
						?>

						<tr>
							<th scope="row"> <label> <?php echo $field_title; ?> </label> </th>

							<td>
								<?php
                                if ( $field_type === 'textarea' ){
                                    echo "<textarea name='{$field_name}' class='large-text' rows='5'>{$field_value}</textarea>";
                                }

								if ( ! empty( $description ) ) {
									echo "<p class='description'> {$description} </p>";
								}
								?>

							</td>
						</tr>

						<?php
						}
					} ?>


				</table>

			</div>

			<?php

		}
		?>

	</div>

</div>
