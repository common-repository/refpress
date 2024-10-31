<?php

/**
 * Get all registered payout methods
 *
 * Pass method key to retrieve specific single payout method
 *
 * @since RefPress 1.0.0
 *
 * @param string $method_key Method Key
 *
 * @return mixed|void
 */

function refpress_get_payout_methods( $method_key = null ) {

	$countries = refpress_get_countries();

	$methods = [

		'paypal' => [
			'method_name' => __( 'PayPal', 'refpress' ),
			'form_admin_fields' => [
				'instruction' => [
					'type'        => 'textarea',
					'title'       => __( 'Notes / Comments', 'refpress' ),
					'description' => __( 'If affiliates manager have any special notes/instructions for affiliated users, write here',
						'refpress' ),
				],
			],
			'form_fields' => [
				'paypal_email' => [
					'type'  => 'email',
					'title' => __( 'PayPal E-Mail', 'refpress' ),
				],
			],
		],

		'Payoneer' => [
			'method_name' => __( 'Payoneer', 'refpress' ),
			'form_admin_fields' => [
				'instruction' => [
					'type'        => 'textarea',
					'title'       => __( 'Notes / Comments', 'refpress' ),
					'description' => __( 'If affiliates manager have any special notes/instructions for affiliated users, write here',
						'refpress' ),
				],
			],
			'form_fields' => [
				'payoneer_email' => [
					'type'  => 'email',
					'title' => __( 'Payoneer E-Mail', 'refpress' ),
				],
			],
		],

		'wire' => [

			'method_name'       => __( 'Bank/Wire Transfer', 'refpress' ),
			'form_admin_fields' => [
				'instruction' => [
					'type'        => 'textarea',
					'title'       => __( 'Notes / Comments', 'refpress' ),
					'description' => __( 'If affiliates manager have any special notes/instructions for affiliated users, write here',
						'refpress' ),
				],
			],

			'form_fields' => [
				'bank_country' => [
					'type'    => 'select',
					'title'   => __( 'Bank Country', 'refpress' ),
					'options' => $countries,
				],

				'bank_name'    => [
					'type'  => 'text',
					'title' => __( 'Bank Name', 'refpress' ),
				],
				'bank_address' => [
					'type'  => 'textarea',
					'title' => __( 'Bank Address', 'refpress' ),
				],

				'account_name' => [
					'type'  => 'text',
					'title' => __( 'Account Holder Name', 'refpress' ),
				],

				'account_number' => [
					'type'  => 'text',
					'title' => __( 'Account Number', 'refpress' ),
				],
				'bic_swift'      => [
					'type'  => 'text',
					'title' => __( 'BIC / SWIFT', 'refpress' ),
				],
				'routing_aba'    => [
					'type'  => 'text',
					'title' => __( 'Routing / ABA', 'refpress' ),
				],
				'iban'           => [
					'type'  => 'text',
					'title' => __( 'IBAN', 'refpress' ),
				],
				'bsb_code'       => [
					'type'  => 'text',
					'title' => __( 'BSB Code', 'refpress' ),
				],

			],
		],

		'echeck' => [
			'method_name' => __( 'E-Check', 'refpress' ),
			'form_admin_fields' => [
				'instruction' => [
					'type'        => 'textarea',
					'title'       => __( 'Notes / Comments', 'refpress' ),
					'description' => __( 'If affiliates manager have any special notes/instructions for affiliated users, write here',
						'refpress' ),
				],
			],
			'form_fields' => [
				'physical_address' => [
					'type'        => 'textarea',
					'title'       => __( 'Your Physical Address', 'refpress' ),
					'description' => __( 'We will send you an E-Check to this address directly.', 'refpress' ),
				],
			],
		],

	];

	$payout_methods = apply_filters( 'refpress_get_payout_methods', $methods );

	if ( ! empty( $method_key ) ) {
		return refpress_array_get( $method_key, $payout_methods );
	}

	return $payout_methods;
}


/**
 * Get only enabled payout methods
 *
 * Pass method key to retrieve specific single payout method
 *
 * @since RefPress 1.0.0
 *
 * @param string $method_key Method Key	 *
 *
 * @return mixed|void
 */

function refpress_get_enabled_payout_methods( $method_key = null ) {
	$methods = refpress_get_payout_methods();

	foreach ( $methods as $method_id => $method ) {
		$is_enable = (bool) refpress_get_payout_settings( $method_id . ".enabled" );

		if ( ! $is_enable ) {
			unset( $methods[ $method_id ] );
		}
	}

	if ( ! empty( $method_key ) ) {
		return refpress_array_get( $method_key, $methods );
	}

	return $methods;
}

/**
 * Get enabled payout methods
 *
 * @since RefPress 1.0.0
 *
 *
 * @param  null  $settings_key  Settings Key
 * @param  null  $payout_method_key  Payout Method Key|ID
 * @param  null  $default  Default Value
 *
 * @return array|bool|mixed|string
 */

function refpress_get_payout_settings( $settings_key = null, $payout_method_key = null, $default = null ) {
	$settings = maybe_unserialize( get_option( 'refpress_payout_settings' ) );

	if ( empty( $settings_key ) ) {
		return $settings;
	}

	if ( ! empty( $payout_method_key ) ) {
		$single_method_settings = refpress_array_get( $payout_method_key, $settings, $default );

		return refpress_array_get( $settings_key, $single_method_settings, $default );
	}

	return refpress_array_get( $settings_key, $settings, $default );
}


/**
 * Get referer saved payout method
 *
 *
 * Example usage:
 *
 *     $method = refpress_get_referer_payout_method( $account );
 *
 *
 * @since RefPress 1.0.0
 *
 * @see refpress_get_account();
 *
 * @param  int|object  $account  Account id or object
 *
 * @return array|false array on success, false on failure
 */

function refpress_get_referer_payout_method( $account ) {
	$_account = null;

	if ( is_numeric( $account ) ) {
		$_account = refpress_get_account( $account );
	} elseif ( is_object( $account ) ) {
		$_account = $account;
	}

	$enabled = false;
	$payout_method_name = null;

	if ( ! empty( $_account->payout_method ) ) {
		$enabled_methods = refpress_get_enabled_payout_methods( $_account->payout_method );
		$payout_method_name     = refpress_array_get( "method_name", $enabled_methods );

		$enabled = (bool) $enabled_methods;
	}

	return [
		'enabled'              => $enabled,
		'payout_method'        => $_account->payout_method,
		'payout_method_name'   => $payout_method_name,
		'payout_method_fields' => maybe_unserialize( $_account->payout_method_fields ),
	];
}


/**
 * Get all countries as array
 *
 * Example usage:
 *
 *     refpress_get_countries();
 *
 *
 * @since RefPress 1.0.0
 *
 * @param string $country_code The ISO Code for the country
 *
 *
 * @return array|string
 */

function refpress_get_countries( $country_code = null ) {

	$all_countries = [
		'US' => 'United States',
		'CA' => 'Canada',
		'GB' => 'United Kingdom',
		'AF' => 'Afghanistan',
		'AX' => '&#197;land Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BQ' => 'Bonaire, Saint Eustatius and Saba',
		'BA' => 'Bosnia and Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darrussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CD' => 'Congo, Democratic People\'s Republic',
		'CG' => 'Congo, Republic of',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote d\'Ivoire',
		'HR' => 'Croatia/Hrvatska',
		'CU' => 'Cuba',
		'CW' => 'Cura&Ccedil;ao',
		'CY' => 'Cyprus',
		'CZ' => 'Czechia',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'TP' => 'East Timor',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'GQ' => 'Equatorial Guinea',
		'SV' => 'El Salvador',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GR' => 'Greece',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard and McDonald Islands',
		'VA' => 'Holy See (City Vatican State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Lao People\'s Democratic Republic',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macau',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia',
		'MD' => 'Moldova, Republic of',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'KP' => 'North Korea',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territories',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn Island',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'XK' => 'Republic of Kosovo',
		'RE' => 'Reunion Island',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'BL' => 'Saint Barth&eacute;lemy',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts and Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin (French)',
		'SX' => 'Saint Martin (Dutch)',
		'PM' => 'Saint Pierre and Miquelon',
		'VC' => 'Saint Vincent and the Grenadines',
		'SM' => 'San Marino',
		'ST' => 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovak Republic',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia',
		'KR' => 'South Korea',
		'SS' => 'South Sudan',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard and Jan Mayen Islands',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'UY' => 'Uruguay',
		'UM' => 'US Minor Outlying Islands',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Vietnam',
		'VG' => 'Virgin Islands (British)',
		'VI' => 'Virgin Islands (USA)',
		'WF' => 'Wallis and Futuna Islands',
		'EH' => 'Western Sahara',
		'WS' => 'Western Samoa',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe'
	];

	if ( $country_code ) {
		return refpress_array_get( $country_code, $all_countries );
	}

	return apply_filters( 'refpress_get_countries', $all_countries );
}

function refpress_pending_account_count() {
	global $wpdb;

	$count = wp_cache_get( 'refpress_pending_account_count' );
	if ( false !== $count ) {
		return (int) $count;
	}

	$count
		= (int) $wpdb->get_var( "SELECT COUNT(account_id) FROM {$wpdb->prefix}refpress_accounts WHERE status = 'pending' " );

	wp_cache_set( 'refpress_pending_account_count', $count );

	return apply_filters( 'refpress_pending_account_count', $count );
}