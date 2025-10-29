<?php // phpcs:ignore

namespace SEOPress\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RichSnippetType
 */
abstract class RichSnippetType {
	/**
	 * The option_local_business constant.
	 *
	 * @var string
	 */
	const OPTION_LOCAL_BUSINESS = 'option-local-business';

	/**
	 * The manual constant.
	 *
	 * @var string
	 */
	const MANUAL = 'manual';

	/**
	 * The auto constant.
	 *
	 * @var string
	 */
	const AUTO = 'auto';

	/**
	 * The sub_type constant.
	 *
	 * @var string
	 */
	const SUB_TYPE = 'sub-type';

	/**
	 * The default_snippet constant.
	 *
	 * @var string
	 */
	const DEFAULT_SNIPPET = 'default';
}
