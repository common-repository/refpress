<?php
/**
 * Bootstrap class for admin
 */

namespace RefPress\Includes\Admin;

defined( 'ABSPATH' ) || exit;

class Settings {

	private $form_fields;

	public function __construct() {
		$this->form_fields = $this->form_fields();
	}

	public function form_fields() {
		$wp_pages = refpress_get_wp_pages();
		array_walk( $wp_pages, function ( &$page, $page_id ) {
			$page = $page . " (" . __( 'id', 'refpress' ) . ":{$page_id})";
		} );

		$currencies = refpress_get_currencies();

		foreach ( $currencies as $code => $name ) {
			$currencies[ $code ] = $name . ' (' . refpress_get_currency_symbol( $code ) . ')';
		}

		$settings_fields = [];

		$settings_fields['general'] = apply_filters( 'refpress_settings_general', [
			'title'       => __( 'General', 'refpress' ),
			'form_fields' => [

				'general' => [
					'title'        => __( 'General', 'refpress' ),
					'description'  => __( 'General Settings', 'refpress' ),
					'input_fields' => [
						'delete_on_uninstall'      => [
							'type'  => 'checkbox',
							'title' => __( 'Delete all data during uninstallation', 'refpress' ),
							'label' => __( 'Erase upon uninstallation', 'refpress' ),
						],
						'refpress_dashboard_page_id' => [
							'type'        => 'select',
							'label'       => __( 'Affiliate Dashboard', 'refpress' ),
							'description' => __( 'The page where your associates will manage their account.', 'refpress' ),
							'options'     => $wp_pages,
						],

						'privacy_policy_page_id' => [
							'type'        => 'select',
							'label'       => __( 'Privacy Policy Page', 'refpress' ),
							'description' => __( 'Select a privacy policy page', 'refpress' ),
							'options'     => $wp_pages,
						],

					],
				],

				'currency' => [
					'title'        => __( 'Currency options', 'refpress' ),
					'description'  => __( 'The following options affect how prices are displayed.', 'refpress' ),
					'input_fields' => [
						'currency'           => [
							'type'    => 'select',
							'label'   => __( 'Currency', 'refpress' ),
							'default' => 'USD',
							'options' => $currencies,
						],
						'currency_position'  => [
							'type'             => 'select',
							'label'            => __( 'Currency position', 'refpress' ),
							'options'          => [
								'left'        => __( 'Left', 'refpress' ),
								'right'       => __( 'Right', 'refpress' ),
								'left_space'  => __( 'Left with space', 'refpress' ),
								'right_space' => __( 'Right with space', 'refpress' ),
							],
							'no_select_option' => true,
						],
						'thousand_separator' => [
							'type'    => 'text',
							'label'   => __( 'Thousand separator', 'refpress' ),
							'default' => ','
						],
						'decimal_separator'  => [
							'type'    => 'text',
							'label'   => __( 'Decimal separator', 'refpress' ),
							'default' => '.'
						],
						'number_of_decimal'  => [
							'type'    => 'number',
							'label'   => __( 'Number of decimals', 'refpress' ),
							'default' => '0'
						],
					],
				],

			],
		] );

		$settings_fields['referral'] = apply_filters( 'refpress_settings_referral', [

			'title' => __( 'Referral', 'refpress' ),

			'form_fields' => [

				'referral_settings' => [
					'title'        => __( 'Referral Settings', 'refpress' ),
					'description'  => __( 'Review or set referral settings', 'refpress' ),
					'input_fields' => [

						'referral_url_parameter' => [
							'type'        => 'text',
							'label'       => __( 'Referral URL Parameter', 'refpress' ),
							'description' => sprintf( __( 'Set referer URL parameter to track the referred users, some suggestions %s.',
								'refpress' ), '<code>ref, r, referral, a, aff, affiliate</code>' ),
						],

						'cookie_expire_in_days' => [
							'type'        => 'number',
							'label'       => __( 'Cookie Validity', 'refpress' ),
							'description' => __( 'Set the number of days you would like to keep valid for referral tracking cookie.',
								'refpress' ),
						],

						'credit_last_referer' => [
							'type'        => 'checkbox',
							'label'       => __( 'Multiple referred', 'refpress' ),
							'title'       => __( 'Credit Last Referrer', 'refpress' ),
							'description' => __( 'If a customer referred by multiple affiliated users, then credit the last referer.',
								'refpress' ),
						],

						'credit_on_recurring_purchase' => [
							'type'        => 'checkbox',
							'label'       => __( 'Recurring Purchase', 'refpress' ),
							'title'       => __( 'Credit On Recurring Purchase', 'refpress' ),
							'description' => __( 'Credit commission to referer on recurring purchase', 'refpress' ),
						],

					],
				],

			]
		] );

		$settings_fields['commission'] = apply_filters( 'refpress_settings_commission', [
			'title'       => __( 'Commission', 'refpress' ),
			'form_fields' => [

				'general' => [
					'title'        => __( 'Commission', 'refpress' ),
					'description'  => __( 'Commission calculation settings', 'refpress' ),
					'input_fields' => [

						'commission_rate'      => [
							'type'        => 'number',
							'label'       => __( 'Commission Rate', 'refpress' ),
							'description' => __( 'The commission rate which your associates will get', 'refpress' ),
						],
						'commission_rate_type' => [
							'type'        => 'radio',
							'label'       => __( 'Commission Rate Type', 'refpress' ),
							'description' => __( 'Whether you are offering a percentage of sales or a fixed amount.',
								'refpress' ),
							'options'     => [
								'percent' => __( 'Percent', 'refpress' ),
								'fixed'   => __( 'Fixed', 'refpress' ),
							],
						],
						'exclude_tax'          => [
							'type'        => 'checkbox',
							'label'       => __( 'Exclude Tax', 'refpress' ),
							'title'       => __( 'Exclude the tax from commission calculation', 'refpress' ),
							'description' => __( 'Check the option if you would like to exclude the tax from the referral commission calculation.',
								'refpress' ),
						],
						'exclude_shipping'     => [
							'type'        => 'checkbox',
							'label'       => __( 'Exclude Shipping', 'refpress' ),
							'title'       => __( 'Exclude the shipping charge from the commission calculation',
								'refpress' ),
							'description' => __( 'Check the option if you would like to exclude the shipping charge from the referral commission calculation.',
								'refpress' ),
						],
						'exclude_other_charge' => [
							'type'        => 'checkbox',
							'label'       => __( 'Exclude Charges', 'refpress' ),
							'title'       => __( 'Exclude Other Charges', 'refpress' ),
							'description' => __( 'You can exclude the other charges such as payment gateway charges, included vat, or any other charge from the referral commission calculation.',
								'refpress' ),
						],
						'other_charge_amount'  => [
							'type'        => 'number',
							'label'       => __( 'Charges Amount', 'refpress' ),
							'description' => __( 'This amount will be exlcude from the referral commission calculation',
								'refpress' ),
						],
						'other_charge_type'    => [
							'type'        => 'radio',
							'label'       => __( 'Charge Type', 'refpress' ),
							'description' => __( 'Whether you are excluding the charges a percentage of sales or a fixed amount.',
								'refpress' ),
							'options'     => [
								'percent' => __( 'Percent', 'refpress' ),
								'fixed'   => __( 'Fixed', 'refpress' ),
							],
							'default'     => 'percent',
						],

					],
				],
			]
		] );

		$settings_fields['integration'] = apply_filters( 'refpress_settings_integration', [
			'title'       => __( 'Integrations', 'refpress' ),
			'form_fields' => [

				'general' => [
					'title'        => __( 'Integration', 'refpress' ),
					'description'  => __( 'All integrations', 'refpress' ),
					'input_fields' => [
						'enabled_integrations' => [
							'type'        => 'checkbox',
							'title'       => __( 'Choose available integration', 'refpress' ),
							'label'       => __( 'Integrations', 'refpress' ),
							'description' => __( 'Enable integration to process the affiliate commission through selected ways',
								'refpress' ),
							'options'     => [
								'woocommerce' => __( 'WooCommerce', 'refpress' ),
								'edd'         => __( 'Easy Digital Downloads (EDD)', 'refpress' ),
							],
						],
					],
				],

			]
		] );

		$settings_fields['payouts'] = apply_filters( 'refpress_settings_payouts', [

			'title'       => __( 'Payouts', 'refpress' ),
			'form_fields' => [

				'general' => [
					'title'       => __( 'Payouts', 'refpress' ),
					'description' => __( 'Configure payouts for the affiliated users', 'refpress' ),

					'input_fields' => [
						'minimum_payout_amount' => [
							'type'        => 'text',
							'label'       => __( 'Minimum Payout Amount', 'refpress' ),
							'default'     => '50',
							'description' => __( 'The minimum amount for the referrer to process payment.',
								'refpress' ),
						],

						'payout_locking_days' => [
							'type'        => 'text',
							'label'       => __( 'Payout Locking', 'refpress' ),
							'default'     => '30',
							'description' => __( '(days) Lock payout balance for x days from the  credited date to align with any refund.',
								'refpress' ),
						],

					],
				],

				'payout_methods' => [
					'title'       => __( 'Payout Methods', 'refpress' ),
					'description' => __( 'Enable or Disable payouts methods which you are offering currently',
						'refpress' ),
				],

			],
		] );

		return apply_filters( 'refpress_settings_args', $settings_fields );
	}

	public function render_field( $field ){

		$title = refpress_array_get( 'title', $field );
		$type = refpress_array_get( 'type', $field );
		$field_name = refpress_array_get( 'field_name', $field );
		$default = refpress_array_get( 'default', $field );
		$field_options = (array) refpress_array_get( 'options', $field, [] );
		$options = array_filter( $field_options );

		$settings_value = $this->get_settings_value( $field_name );
		if ( $settings_value === false && ( is_numeric( $default ) || ! empty( $default ) ) ) {
			$settings_value = $default;
		}

		switch ( $type ) {
			case 'text':
				?>
                <input name="refpress_settings[<?php echo $field_name; ?>]" type="text" id="<?php echo $field_name; ?>" value="<?php echo $settings_value; ?>" >
				<?php
				break;
			case 'number':
				?>
                <input name="refpress_settings[<?php echo $field_name; ?>]" type="number" id="<?php echo $field_name; ?>" value="<?php echo $settings_value; ?>" >
				<?php
				break;
			case 'checkbox':

				if ( is_array( $options ) && count( $options ) ) {
					$settings_value = (array) $settings_value;

					foreach ( $options as $option_key => $option_value ) {
						?>
                        <p>
                            <label>
                                <input name="refpress_settings[<?php echo $field_name; ?>][]" type="checkbox" value="<?php echo $option_key; ?>" <?php checked( 1, in_array( $option_key, $settings_value ) ); ?> >
								<?php echo $option_value; ?>
                            </label>
                        </p>
						<?php

					}

				} else {
					?>
                    <label>
                        <input name="refpress_settings[<?php echo $field_name; ?>]" type="checkbox" id="<?php echo $field_name; ?>" value="1" <?php checked( '1', $settings_value ) ?> >
						<?php echo $title; ?>
                    </label>
					<?php
				}


				break;
			case 'radio':

				foreach ( $options as $option_key => $option_value ) {
					?>
                    <p>
                        <label>
                            <input name="refpress_settings[<?php echo $field_name; ?>]" type="radio" value="<?php echo $option_key; ?>" <?php checked( $option_key, $settings_value ) ?> >
							<?php echo $option_value; ?>
                        </label>
                    </p>
					<?php
				}
				break;
			case 'select':

				$no_select_option = refpress_array_get( 'no_select_option', $field );

				echo "<select class='refpress_select2' name='refpress_settings[{$field_name}]'>";

				if ( ! $no_select_option ) {
					echo "<option value=''> " . __( 'Select Option', 'refpress' ) . " </option>";
				}

				foreach ( $options as $option_value => $option_text ) {
					echo "<option value='{$option_value}' " . selected( $option_value, $settings_value, false ) . " > {$option_text} </option>";
				}
				echo "</select>";


				break;
		}

	}

	/**
	 * Get Settings value by Key
	 *
	 *
	 * @since RefPress 1.0.0
	 *
	 *
	 * @param  string  $settings_key
	 * @param  false  $default
	 *
	 * @return array|bool|mixed
	 */

	public function get_settings_value( $settings_key = '', $default = false ){
		$settings = maybe_unserialize( get_option( 'refpress_settings' ) );
		return refpress_array_get( $settings_key, $settings, $default );
	}

	public function generate() {
		include REFPRESS_ABSPATH . 'includes/admin/settings/panel.php';
	}


}