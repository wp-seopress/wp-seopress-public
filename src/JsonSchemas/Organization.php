<?php // phpcs:ignore

namespace SEOPress\JsonSchemas;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Helpers\RichSnippetType;
use SEOPress\Models\GetJsonData;
use SEOPress\Models\JsonSchemaValue;


/**
 * Organization
 */
class Organization extends JsonSchemaValue implements GetJsonData {
	/**
	 * The NAME constant.
	 *
	 * @var string
	 */
	const NAME = 'organization';

	/**
	 * The getName function.
	 *
	 * @return string
	 */
	protected function getName() {
		return self::NAME;
	}

	/**
	 * The getJsonData function.
	 *
	 * @since 4.5.0
	 *
	 * @param array $context The context.
	 *
	 * @return array
	 */
	public function getJsonData( $context = null ) {
		$data = $this->getArrayJson();

		$type_schema = isset( $context['type'] ) ? $context['type'] : RichSnippetType::DEFAULT_SNIPPET;

		switch ( $type_schema ) {
			default:
				$variables = array(
					'type'              => '%%knowledge_type%%',
					'name'              => '%%social_knowledge_name%%',
					'alternate_name'    => '%%site_alternate_name%%',
					'description'       => '%%social_knowledge_description%%',
					'url'               => '%%siteurl%%',
					'logo'              => '%%social_knowledge_image%%',
					'account_facebook'  => '%%social_account_facebook%%',
					'account_twitter'   => '%%social_account_twitter%%',
					'account_pinterest' => '%%social_account_pinterest%%',
					'account_instagram' => '%%social_account_instagram%%',
					'account_youtube'   => '%%social_account_youtube%%',
					'account_linkedin'  => '%%social_account_linkedin%%',
					'account_extra'     => '%%social_account_extra%%',
					'tax_id'            => '%%social_knowledge_tax_id%%',
					'legal_name'        => '%%social_knowledge_legal_name%%',
					'founding_date'     => '%%social_knowledge_founding_date%%',
					'employees'         => '%%social_knowledge_employees%%',
					'street'            => '%%social_knowledge_street%%',
					'locality'          => '%%social_knowledge_locality%%',
					'region'            => '%%social_knowledge_region%%',
					'postal_code'       => '%%social_knowledge_postal_code%%',
					'country'           => '%%social_knowledge_country%%',
				);
				break;

			case RichSnippetType::SUB_TYPE:
				$variables = isset( $context['variables'] ) ? $context['variables'] : array();
				break;
		}

		$data = seopress_get_service( 'VariablesToString' )->replaceDataToString( $data, $variables );

		$type = seopress_get_service( 'SocialOption' )->getSocialKnowledgeType();

		// All Organization subtypes share the same Knowledge Graph fields; only Person diverges.
		$is_person = ( 'Person' === $type );

		if ( ! $is_person ) {
			// Use "contactPoint".
			$schema = seopress_get_service( 'JsonSchemaGenerator' )->getJsonFromSchema( ContactPoint::NAME, $context, array( 'remove_empty' => true ) );
			if ( count( $schema ) > 1 ) {
				$data['contactPoint'][] = $schema;
			}
		} else {
			// Remove Organization-specific keys.
			$organization_only_keys = array(
				'logo',
				'legalName',
				'foundingDate',
				'numberOfEmployees',
				'address',
				'vatID',
			);
			foreach ( $organization_only_keys as $organization_only_key ) {
				if ( array_key_exists( $organization_only_key, $data ) ) {
					unset( $data[ $organization_only_key ] );
				}
			}
		}

		return apply_filters( 'seopress_get_json_data_organization', $data );
	}

	/**
	 * The cleanValues function.
	 *
	 * @since 4.5.0
	 *
	 * @param array $data The data.
	 *
	 * @return array
	 */
	public function cleanValues( $data ) {
		if ( isset( $data['sameAs'] ) ) {
			$data['sameAs'] = array_values( $data['sameAs'] );

			// Create a new empty array to store the updated values.
			$new_array = array();

			// Loop through the original array.
			foreach ( $data['sameAs'] as $value ) {
				// Check if the value contains a line break.
				if ( strpos( $value, PHP_EOL ) !== false ) {
					// If it does, split the value into an array based on the line breaks.
					$split_values = explode( PHP_EOL, $value );
					// Add each split value to the new array.
					foreach ( $split_values as $split_value ) {
						$split_value = str_replace( array( "\r", "\n" ), '', $split_value );
						$new_array[] = $split_value;
					}
				} else {
					// If it doesn't, simply add the original value to the new array.
					$new_array[] = $value;
				}
			}

			$data['sameAs'] = $new_array;

			if ( empty( $data['sameAs'] ) ) {
				unset( $data['sameAs'] );
			}
		}

		// Drop nested PostalAddress / QuantitativeValue blocks when they hold no real data.
		foreach ( array( 'address', 'numberOfEmployees' ) as $nested_key ) {
			if ( ! isset( $data[ $nested_key ] ) || ! is_array( $data[ $nested_key ] ) ) {
				continue;
			}

			$has_value = false;
			foreach ( $data[ $nested_key ] as $sub_key => $sub_value ) {
				if ( '@type' === $sub_key ) {
					continue;
				}
				if ( ! empty( $sub_value ) ) {
					$has_value = true;
					break;
				}
			}

			if ( ! $has_value ) {
				unset( $data[ $nested_key ] );
			}
		}

		return parent::cleanValues( $data );
	}
}
