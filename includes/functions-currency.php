<?php


/**
 * Get all available currencies.
 *
 *
 * @since 1.0.0
 * @return array
 */

function refpress_get_currencies() {
	static $currencies;

	if ( ! isset( $currencies ) ) {
		$currencies = array_unique(
			apply_filters(
				'refpress_currencies',
				array(
					'AED' => __( 'United Arab Emirates dirham', 'refpress' ),
					'AFN' => __( 'Afghan afghani', 'refpress' ),
					'ALL' => __( 'Albanian lek', 'refpress' ),
					'AMD' => __( 'Armenian dram', 'refpress' ),
					'ANG' => __( 'Netherlands Antillean guilder', 'refpress' ),
					'AOA' => __( 'Angolan kwanza', 'refpress' ),
					'ARS' => __( 'Argentine peso', 'refpress' ),
					'AUD' => __( 'Australian dollar', 'refpress' ),
					'AWG' => __( 'Aruban florin', 'refpress' ),
					'AZN' => __( 'Azerbaijani manat', 'refpress' ),
					'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'refpress' ),
					'BBD' => __( 'Barbadian dollar', 'refpress' ),
					'BDT' => __( 'Bangladeshi taka', 'refpress' ),
					'BGN' => __( 'Bulgarian lev', 'refpress' ),
					'BHD' => __( 'Bahraini dinar', 'refpress' ),
					'BIF' => __( 'Burundian franc', 'refpress' ),
					'BMD' => __( 'Bermudian dollar', 'refpress' ),
					'BND' => __( 'Brunei dollar', 'refpress' ),
					'BOB' => __( 'Bolivian boliviano', 'refpress' ),
					'BRL' => __( 'Brazilian real', 'refpress' ),
					'BSD' => __( 'Bahamian dollar', 'refpress' ),
					'BTC' => __( 'Bitcoin', 'refpress' ),
					'BTN' => __( 'Bhutanese ngultrum', 'refpress' ),
					'BWP' => __( 'Botswana pula', 'refpress' ),
					'BYR' => __( 'Belarusian ruble (old)', 'refpress' ),
					'BYN' => __( 'Belarusian ruble', 'refpress' ),
					'BZD' => __( 'Belize dollar', 'refpress' ),
					'CAD' => __( 'Canadian dollar', 'refpress' ),
					'CDF' => __( 'Congolese franc', 'refpress' ),
					'CHF' => __( 'Swiss franc', 'refpress' ),
					'CLP' => __( 'Chilean peso', 'refpress' ),
					'CNY' => __( 'Chinese yuan', 'refpress' ),
					'COP' => __( 'Colombian peso', 'refpress' ),
					'CRC' => __( 'Costa Rican col&oacute;n', 'refpress' ),
					'CUC' => __( 'Cuban convertible peso', 'refpress' ),
					'CUP' => __( 'Cuban peso', 'refpress' ),
					'CVE' => __( 'Cape Verdean escudo', 'refpress' ),
					'CZK' => __( 'Czech koruna', 'refpress' ),
					'DJF' => __( 'Djiboutian franc', 'refpress' ),
					'DKK' => __( 'Danish krone', 'refpress' ),
					'DOP' => __( 'Dominican peso', 'refpress' ),
					'DZD' => __( 'Algerian dinar', 'refpress' ),
					'EGP' => __( 'Egyptian pound', 'refpress' ),
					'ERN' => __( 'Eritrean nakfa', 'refpress' ),
					'ETB' => __( 'Ethiopian birr', 'refpress' ),
					'EUR' => __( 'Euro', 'refpress' ),
					'FJD' => __( 'Fijian dollar', 'refpress' ),
					'FKP' => __( 'Falkland Islands pound', 'refpress' ),
					'GBP' => __( 'Pound sterling', 'refpress' ),
					'GEL' => __( 'Georgian lari', 'refpress' ),
					'GGP' => __( 'Guernsey pound', 'refpress' ),
					'GHS' => __( 'Ghana cedi', 'refpress' ),
					'GIP' => __( 'Gibraltar pound', 'refpress' ),
					'GMD' => __( 'Gambian dalasi', 'refpress' ),
					'GNF' => __( 'Guinean franc', 'refpress' ),
					'GTQ' => __( 'Guatemalan quetzal', 'refpress' ),
					'GYD' => __( 'Guyanese dollar', 'refpress' ),
					'HKD' => __( 'Hong Kong dollar', 'refpress' ),
					'HNL' => __( 'Honduran lempira', 'refpress' ),
					'HRK' => __( 'Croatian kuna', 'refpress' ),
					'HTG' => __( 'Haitian gourde', 'refpress' ),
					'HUF' => __( 'Hungarian forint', 'refpress' ),
					'IDR' => __( 'Indonesian rupiah', 'refpress' ),
					'ILS' => __( 'Israeli new shekel', 'refpress' ),
					'IMP' => __( 'Manx pound', 'refpress' ),
					'INR' => __( 'Indian rupee', 'refpress' ),
					'IQD' => __( 'Iraqi dinar', 'refpress' ),
					'IRR' => __( 'Iranian rial', 'refpress' ),
					'IRT' => __( 'Iranian toman', 'refpress' ),
					'ISK' => __( 'Icelandic kr&oacute;na', 'refpress' ),
					'JEP' => __( 'Jersey pound', 'refpress' ),
					'JMD' => __( 'Jamaican dollar', 'refpress' ),
					'JOD' => __( 'Jordanian dinar', 'refpress' ),
					'JPY' => __( 'Japanese yen', 'refpress' ),
					'KES' => __( 'Kenyan shilling', 'refpress' ),
					'KGS' => __( 'Kyrgyzstani som', 'refpress' ),
					'KHR' => __( 'Cambodian riel', 'refpress' ),
					'KMF' => __( 'Comorian franc', 'refpress' ),
					'KPW' => __( 'North Korean won', 'refpress' ),
					'KRW' => __( 'South Korean won', 'refpress' ),
					'KWD' => __( 'Kuwaiti dinar', 'refpress' ),
					'KYD' => __( 'Cayman Islands dollar', 'refpress' ),
					'KZT' => __( 'Kazakhstani tenge', 'refpress' ),
					'LAK' => __( 'Lao kip', 'refpress' ),
					'LBP' => __( 'Lebanese pound', 'refpress' ),
					'LKR' => __( 'Sri Lankan rupee', 'refpress' ),
					'LRD' => __( 'Liberian dollar', 'refpress' ),
					'LSL' => __( 'Lesotho loti', 'refpress' ),
					'LYD' => __( 'Libyan dinar', 'refpress' ),
					'MAD' => __( 'Moroccan dirham', 'refpress' ),
					'MDL' => __( 'Moldovan leu', 'refpress' ),
					'MGA' => __( 'Malagasy ariary', 'refpress' ),
					'MKD' => __( 'Macedonian denar', 'refpress' ),
					'MMK' => __( 'Burmese kyat', 'refpress' ),
					'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'refpress' ),
					'MOP' => __( 'Macanese pataca', 'refpress' ),
					'MRU' => __( 'Mauritanian ouguiya', 'refpress' ),
					'MUR' => __( 'Mauritian rupee', 'refpress' ),
					'MVR' => __( 'Maldivian rufiyaa', 'refpress' ),
					'MWK' => __( 'Malawian kwacha', 'refpress' ),
					'MXN' => __( 'Mexican peso', 'refpress' ),
					'MYR' => __( 'Malaysian ringgit', 'refpress' ),
					'MZN' => __( 'Mozambican metical', 'refpress' ),
					'NAD' => __( 'Namibian dollar', 'refpress' ),
					'NGN' => __( 'Nigerian naira', 'refpress' ),
					'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'refpress' ),
					'NOK' => __( 'Norwegian krone', 'refpress' ),
					'NPR' => __( 'Nepalese rupee', 'refpress' ),
					'NZD' => __( 'New Zealand dollar', 'refpress' ),
					'OMR' => __( 'Omani rial', 'refpress' ),
					'PAB' => __( 'Panamanian balboa', 'refpress' ),
					'PEN' => __( 'Sol', 'refpress' ),
					'PGK' => __( 'Papua New Guinean kina', 'refpress' ),
					'PHP' => __( 'Philippine peso', 'refpress' ),
					'PKR' => __( 'Pakistani rupee', 'refpress' ),
					'PLN' => __( 'Polish z&#x142;oty', 'refpress' ),
					'PRB' => __( 'Transnistrian ruble', 'refpress' ),
					'PYG' => __( 'Paraguayan guaran&iacute;', 'refpress' ),
					'QAR' => __( 'Qatari riyal', 'refpress' ),
					'RON' => __( 'Romanian leu', 'refpress' ),
					'RSD' => __( 'Serbian dinar', 'refpress' ),
					'RUB' => __( 'Russian ruble', 'refpress' ),
					'RWF' => __( 'Rwandan franc', 'refpress' ),
					'SAR' => __( 'Saudi riyal', 'refpress' ),
					'SBD' => __( 'Solomon Islands dollar', 'refpress' ),
					'SCR' => __( 'Seychellois rupee', 'refpress' ),
					'SDG' => __( 'Sudanese pound', 'refpress' ),
					'SEK' => __( 'Swedish krona', 'refpress' ),
					'SGD' => __( 'Singapore dollar', 'refpress' ),
					'SHP' => __( 'Saint Helena pound', 'refpress' ),
					'SLL' => __( 'Sierra Leonean leone', 'refpress' ),
					'SOS' => __( 'Somali shilling', 'refpress' ),
					'SRD' => __( 'Surinamese dollar', 'refpress' ),
					'SSP' => __( 'South Sudanese pound', 'refpress' ),
					'STN' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'refpress' ),
					'SYP' => __( 'Syrian pound', 'refpress' ),
					'SZL' => __( 'Swazi lilangeni', 'refpress' ),
					'THB' => __( 'Thai baht', 'refpress' ),
					'TJS' => __( 'Tajikistani somoni', 'refpress' ),
					'TMT' => __( 'Turkmenistan manat', 'refpress' ),
					'TND' => __( 'Tunisian dinar', 'refpress' ),
					'TOP' => __( 'Tongan pa&#x2bb;anga', 'refpress' ),
					'TRY' => __( 'Turkish lira', 'refpress' ),
					'TTD' => __( 'Trinidad and Tobago dollar', 'refpress' ),
					'TWD' => __( 'New Taiwan dollar', 'refpress' ),
					'TZS' => __( 'Tanzanian shilling', 'refpress' ),
					'UAH' => __( 'Ukrainian hryvnia', 'refpress' ),
					'UGX' => __( 'Ugandan shilling', 'refpress' ),
					'USD' => __( 'United States (US) dollar', 'refpress' ),
					'UYU' => __( 'Uruguayan peso', 'refpress' ),
					'UZS' => __( 'Uzbekistani som', 'refpress' ),
					'VEF' => __( 'Venezuelan bol&iacute;var', 'refpress' ),
					'VES' => __( 'Bol&iacute;var soberano', 'refpress' ),
					'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'refpress' ),
					'VUV' => __( 'Vanuatu vatu', 'refpress' ),
					'WST' => __( 'Samoan t&#x101;l&#x101;', 'refpress' ),
					'XAF' => __( 'Central African CFA franc', 'refpress' ),
					'XCD' => __( 'East Caribbean dollar', 'refpress' ),
					'XOF' => __( 'West African CFA franc', 'refpress' ),
					'XPF' => __( 'CFP franc', 'refpress' ),
					'YER' => __( 'Yemeni rial', 'refpress' ),
					'ZAR' => __( 'South African rand', 'refpress' ),
					'ZMW' => __( 'Zambian kwacha', 'refpress' ),
				)
			)
		);
	}

	return $currencies;
}


/**
 * Get all available Currency symbols.
 *
 *
 * @since 1.0.0
 * @return array
 */

function refpress_get_currency_symbols() {

	$symbols = apply_filters(
		'refpress_currency_symbols',
		array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => 'Afl.',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BYN' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x20be;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => '&#8376;',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRU' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => 'N&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#1088;&#1089;&#1076;',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STN' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VES' => 'Bs.S',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'CFA',
			'XCD' => '&#36;',
			'XOF' => 'CFA',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		)
	);

	return $symbols;
}


/**
 * Get Currency symbol.
 *
 *
 * @since 1.0.0
 *
 * @param  string  $currency  Currency. (default: '').
 *
 * @return string
 */

function refpress_get_currency_symbol( $currency = '' ) {
	if ( ! $currency ) {
		$currency = 'USD';
	}

	$symbols = refpress_get_currency_symbols();

	$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

	return apply_filters( 'refpress_currency_symbol', $currency_symbol, $currency );
}


/**
 * Get the price decimal separator
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @return string
 */

function refpress_get_price_decimal_separator() {
	$separator = apply_filters( 'refpress_get_price_decimal_separator',
		refpress_get_setting( 'decimal_separator' ) );

	return $separator ? stripslashes( $separator ) : '.';
}


/**
 * Get thousand separator from the refpress settings
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @return string
 */

function refpress_get_price_thousand_separator() {
	return stripslashes( apply_filters( 'refpress_get_price_thousand_separator',
		refpress_get_setting( 'thousand_separator' ) ) );
}

/**
 * Get the price decimal
 *
 *
 * @since RefPress 1.0.0
 *
 * @return int
 */

function refpress_get_price_decimals() {
	return absint( apply_filters( 'refpress_get_price_decimals', refpress_get_setting( 'number_of_decimal', 2 ) ) );
}


/**
 * Render any number to price format
 *
 *
 * @since RefPress 1.0.0
 *
 * @param $price
 * @param  array  $args
 *
 * @return mixed|void
 */

function refpress_price( $price, $args = [] ) {
	$args = apply_filters(
		'refpress_price_args',
		wp_parse_args(
			$args,
			[
				'currency'           => '',
				'decimal_separator'  => refpress_get_price_decimal_separator(),
				'thousand_separator' => refpress_get_price_thousand_separator(),
				'decimals'           => refpress_get_price_decimals(),
				'price_format'       => refpress_get_price_format(),
			]
		)
	);

	$original_price = $price;

	// Convert to float to avoid issues on PHP 8.
	$price = (float) $price;

	$unformatted_price = $price;
	$negative          = $price < 0;

	$price = apply_filters( 'refpress_raw_price', $negative ? $price * - 1 : $price, $original_price );

	$price = apply_filters( 'refpress_formatted_price',
		number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price,
		$args['decimals'], $args['decimal_separator'], $args['thousand_separator'], $original_price );

	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'],
			'<span class="refpress-price-currency-symbol">' . refpress_get_currency_symbol( $args['currency'] )
			. '</span>', $price );
	$return          = '<span class="refpress-price-amount refpress-amount"><bdi>' . $formatted_price
	                   . '</bdi></span>';

	return apply_filters( 'refpress_price', $return, $price, $args, $unformatted_price, $original_price );
}


/**
 * Get the price format
 *
 *
 * @since RefPress 1.0.0
 *
 *
 * @return mixed|void
 */

function refpress_get_price_format() {
	$position = refpress_get_setting( 'currency_position' );
	$format   = '%1$s%2$s';

	switch ( $position ) {
		case 'left':
			$format = '%1$s%2$s';
			break;
		case 'right':
			$format = '%2$s%1$s';
			break;
		case 'left_space':
			$format = '%1$s&nbsp;%2$s';
			break;
		case 'right_space':
			$format = '%2$s&nbsp;%1$s';
			break;
	}

	return apply_filters( 'refpress_price_format', $format, $position );
}