<?php

$form_fields = $this->form_fields;
?>

<div class="wrap">

    <div class="refpress-settings-wrap">
        <form action="" method="post" id="RefPressSettingsForm">

			<?php wp_nonce_field(REFPRESS_NONCE_ACTION, REFPRESS_NONCE) ?>
            <input type="hidden" name="action" value="refpress_save_settings" >

            <div class="refpress-settings-header">
                <div class="refpress-settings-brandable">
                    <h2>
						<?php _e('RefPress', 'refpress') ?>
                        <small class="version"><?php echo REFPRESS_VERSION; ?></small>
                    </h2>
                </div>
            </div>

            <div class="refpress-settings-fields-wrap">
                <div class="refpress-settings-sidebar">
					<?php
					$first_nav_tab_id = null;

					foreach ( $form_fields as $nav_tab_id => $form_segment ){

						if ( ! $first_nav_tab_id){
							$first_nav_tab_id = $nav_tab_id;
						}

						$current_nav_tab = refpress_get_input_text('nav_tab' );
						$is_current_nav_tab = ( $nav_tab_id == $current_nav_tab );
						if ( ! $is_current_nav_tab && ! $current_nav_tab) {
							$is_current_nav_tab = ( $nav_tab_id == $first_nav_tab_id );
						}

						?>
                        <a href="<?php echo admin_url('admin.php?page=refpress-settings&nav_tab='.$nav_tab_id); ?>" class="option-sidebar-item option-sidebar-item-<?php echo $nav_tab_id ?> <?php echo $is_current_nav_tab ? 'current' : ''; ?> " data-target="#nav_tab-<?php echo $nav_tab_id; ?>">
                            <span class="option-nav_tab-item-title">
                                <?php echo refpress_array_get('title', $form_segment); ?>
                            </span>
                        </a>
						<?php
					}
					?>
                </div>

                <div class="refpress-nav_tab-wrap">
                    <div class="refpress-settings-panel-top-bar">
                        <div class="col-left">
							<?php do_action('refpress_settings_panel_top_bar_left'); ?>
                            <div id="refpress-settings-panel-notice-wrap"></div>
                        </div>

                        <div class="col-right">
                            <button type="submit" name="refpress_settings_save_btn" class="button refpress-button-outline"><?php _e('Save Changes', 'refpress'); ?></button>
                        </div>
                    </div>
					<?php
					foreach ( $form_fields as $nav_tab_id => $form_segment) {

						$current_nav_tab = refpress_get_input_text('nav_tab');
						$is_current_nav_tab = ($nav_tab_id == $current_nav_tab);
						if ( ! $is_current_nav_tab && ! $current_nav_tab){
							$is_current_nav_tab = ($nav_tab_id == $first_nav_tab_id) ;
						}

						?>
                        <div id="nav_tab-<?php echo $nav_tab_id; ?>" class="refpress-settings-panel-nav_tab" style="display: <?php echo $is_current_nav_tab ? 'block' : 'none'; ?>;" >

							<?php
							do_action('refpress_settings/nav_tab/before');
							do_action('refpress_settings/nav_tab/before/'.$nav_tab_id);

							$form_fields = (array) refpress_array_get('form_fields', $form_segment);
							foreach ( $form_fields as $group_id => $fields_group ){

								do_action("refpress_settings/field_group/before/{$nav_tab_id}/$group_id");

								$input_fields = refpress_array_get('input_fields', $fields_group);
								$group_header = refpress_array_get('title', $fields_group);
								$group_desc = refpress_array_get('description', $fields_group);
								?>

                                <div class="refpress-settings-group-wrap">
                                    <h3 class="refpress-settings-group-header"><?php echo $group_header; ?></h3>
                                    <p class="refpress-settings-group-desc"><?php echo $group_desc; ?></p>
                                </div>

                                <table class="form-table" role="presentation">
									<?php
									if ( is_array( $input_fields ) && count( $input_fields ) ) {
										foreach ( $input_fields as $field_name => $field ) {
											$field_type = refpress_array_get('type', $field);
											$field_label = refpress_array_get('label', $field);
											$field_desc = refpress_array_get('description', $field);
											$field['field_name'] = $field_name;
											?>
                                            <tr>
                                                <th scope="row"> <label> <?php echo $field_label; ?> </label> </th>
                                                <td>
													<?php
													$this->render_field( $field );
													if ( ! empty( $field_desc ) ) {
														echo "<p class='description'> {$field_desc} </p>";
													}
													?>
                                                </td>
                                            </tr>
											<?php
										}
									}
									?>
                                </table>
								<?php

								do_action("refpress_settings/field_group/after/{$nav_tab_id}/$group_id");
							}

							do_action('refpress_settings/nav_tab/after');
							do_action('refpress_settings/nav_tab/after/'.$nav_tab_id);
							?>
                        </div>
						<?php
					}
					?>
                    <div class="refpress-settings-panel-bottom-bar">
                        <div class="col-left"><?php do_action('refpress_settings_panel_bottom_bar_left'); ?></div>

                        <div class="col-right">
                            <button type="submit" name="refpress_settings_save_btn" class="button refpress-button-outline"><?php _e('Save Changes', 'refpress'); ?></button>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>




</div>