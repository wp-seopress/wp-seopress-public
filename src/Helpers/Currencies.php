<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Currencies
 */
abstract class Currencies {
	/**
	 * The get_options function.
	 *
	 * @return array
	 */
	public static function getOptions() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return apply_filters(
			'seopress_get_options_schema_currencies',
			array(
				array(
					'value' => 'none',
					'label' => __( 'Select a Currency', 'wp-seopress' ),
				),
				array(
					'value' => 'USD',
					'label' => __( 'U.S. Dollar', 'wp-seopress' ),
				),
				array(
					'value' => 'GBP',
					'label' => __( 'Pound Sterling', 'wp-seopress' ),
				),
				array(
					'value' => 'EUR',
					'label' => __( 'Euro', 'wp-seopress' ),
				),
				array(
					'value' => 'ARS',
					'label' => __( 'Argentina Peso', 'wp-seopress' ),
				),
				array(
					'value' => 'AUD',
					'label' => __( 'Australian Dollar', 'wp-seopress' ),
				),
				array(
					'value' => 'BRL',
					'label' => __( 'Brazilian Real', 'wp-seopress' ),
				),
				array(
					'value' => 'BGN',
					'label' => __( 'Bulgarian lev', 'wp-seopress' ),
				),
				array(
					'value' => 'CAD',
					'label' => __( 'Canadian Dollar', 'wp-seopress' ),
				),
				array(
					'value' => 'CLP',
					'label' => __( 'Chilean Peso', 'wp-seopress' ),
				),
				array(
					'value' => 'CZK',
					'label' => __( 'Czech Koruna', 'wp-seopress' ),
				),
				array(
					'value' => 'DKK',
					'label' => __( 'Danish Krone', 'wp-seopress' ),
				),
				array(
					'value' => 'HKD',
					'label' => __( 'Hong Kong Dollar', 'wp-seopress' ),
				),
				array(
					'value' => 'HUF',
					'label' => __( 'Hungarian Forint', 'wp-seopress' ),
				),
				array(
					'value' => 'INR',
					'label' => __( 'Indian rupee', 'wp-seopress' ),
				),
				array(
					'value' => 'ILS',
					'label' => __( 'Israeli New Sheqel', 'wp-seopress' ),
				),
				array(
					'value' => 'JPY',
					'label' => __( 'Japanese Yen', 'wp-seopress' ),
				),
				array(
					'value' => 'MYR',
					'label' => __( 'Malaysian Ringgit', 'wp-seopress' ),
				),
				array(
					'value' => 'MXN',
					'label' => __( 'Mexican Peso', 'wp-seopress' ),
				),
				array(
					'value' => 'NOK',
					'label' => __( 'Norwegian Krone', 'wp-seopress' ),
				),
				array(
					'value' => 'NZD',
					'label' => __( 'New Zealand Dollar', 'wp-seopress' ),
				),
				array(
					'value' => 'PHP',
					'label' => __( 'Philippine Peso', 'wp-seopress' ),
				),
				array(
					'value' => 'PLN',
					'label' => __( 'Polish Zloty', 'wp-seopress' ),
				),
				array(
					'value' => 'IDR',
					'label' => __( 'Indonesian rupiah', 'wp-seopress' ),
				),
				array(
					'value' => 'RUB',
					'label' => __( 'Russian Ruble', 'wp-seopress' ),
				),
				array(
					'value' => 'SGD',
					'label' => __( 'Singapore Dollar', 'wp-seopress' ),
				),
				array(
					'value' => 'PEN',
					'label' => __( 'Sol', 'wp-seopress' ),
				),
				array(
					'value' => 'ZAR',
					'label' => __( 'South African Rand', 'wp-seopress' ),
				),
				array(
					'value' => 'SEK',
					'label' => __( 'Swedish Krona', 'wp-seopress' ),
				),
				array(
					'value' => 'CHF',
					'label' => __( 'Swiss Franc', 'wp-seopress' ),
				),
				array(
					'value' => 'TWD',
					'label' => __( 'Taiwan New Dollar', 'wp-seopress' ),
				),
				array(
					'value' => 'THB',
					'label' => __( 'Thai Baht', 'wp-seopress' ),
				),
				array(
					'value' => 'UAH',
					'label' => __( 'Ukrainian hryvnia', 'wp-seopress' ),
				),
				array(
					'value' => 'VND',
					'label' => __( 'Vietnamese đồng', 'wp-seopress' ),
				),
			)
		);
	}
}
