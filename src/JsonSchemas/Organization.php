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
				);
				break;

			case RichSnippetType::SUB_TYPE:
				$variables = isset( $context['variables'] ) ? $context['variables'] : array();
				break;
		}

		$data = seopress_get_service( 'VariablesToString' )->replaceDataToString( $data, $variables );

		$type = seopress_get_service( 'SocialOption' )->getSocialKnowledgeType();

		if ( 'Organization' === $type ) {
			// Use "contactPoint".
			$schema = seopress_get_service( 'JsonSchemaGenerator' )->getJsonFromSchema( ContactPoint::NAME, $context, array( 'remove_empty' => true ) );
			if ( count( $schema ) > 1 ) {
				$data['contactPoint'][] = $schema;
			}
		} else { // Not Organization -> Like Is Person
			// Remove "logo".
			if ( array_key_exists( 'logo', $data ) ) {
				unset( $data['logo'] );
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

		return parent::cleanValues( $data );
	}
}
