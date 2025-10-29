<?php // phpcs:ignore

namespace SEOPress\Constants;

use SEOPress\Helpers\TagCompose;
use SEOPress\Tags\PostTitle;
use SEOPress\Tags\SiteTagline;
use SEOPress\Tags\CategoryTitle;
use SEOPress\Tags\CurrentPagination;
use SEOPress\Tags\SiteTitle;
use SEOPress\Tags\TagTitle;
use SEOPress\Tags\TermTitle;
use SEOPress\Tags\CategoryDescription;
use SEOPress\Tags\TagDescription;
use SEOPress\Tags\TermDescription;
use SEOPress\Tags\PostAuthor;
use SEOPress\Tags\Date\ArchiveDate;
use SEOPress\Tags\CustomPostTypePlural;

/**
 * MetasDefaultValues
 */
abstract class MetasDefaultValues {
	/**
	 * The getPostTypeTitleValue function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public static function getPostTypeTitleValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf(
			'%s %s %s',
			TagCompose::getValueWithTag( PostTitle::NAME ),
			'%%sep%%',
			TagCompose::getValueWithTag( SiteTitle::NAME )
		);
	}

	public static function getPostTypeDescriptionValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return TagCompose::getValueWithTag( 'post_excerpt' );
	}

	public static function getTaxonomyCategoryValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf(
			'%s %s %s %s',
			TagCompose::getValueWithTag( CategoryTitle::NAME ),
			TagCompose::getValueWithTag( CurrentPagination::NAME ),
			'%%sep%%',
			TagCompose::getValueWithTag( SiteTitle::NAME )
		);
	}

	public static function getTagTitleValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf(
			'%s %s %s %s',
			TagCompose::getValueWithTag( TagTitle::NAME ),
			TagCompose::getValueWithTag( CurrentPagination::NAME ),
			'%%sep%%',
			TagCompose::getValueWithTag( SiteTitle::NAME )
		);
	}

	public static function getTermTitleValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf(
			'%s %s %s %s',
			TagCompose::getValueWithTag( TermTitle::NAME ),
			TagCompose::getValueWithTag( CurrentPagination::NAME ),
			'%%sep%%',
			TagCompose::getValueWithTag( SiteTitle::NAME )
		);
	}

	public static function getTaxonomyCategoryDescriptionValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return TagCompose::getValueWithTag( CategoryDescription::NAME );
	}

	public static function getTagDescriptionValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return TagCompose::getValueWithTag( TagDescription::NAME );
	}

	public static function getTermDescriptionValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return TagCompose::getValueWithTag( TermDescription::NAME );
	}

	public static function getAuthorTitleValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf(
			'%s %s %s',
			TagCompose::getValueWithTag( PostAuthor::NAME ),
			'%%sep%%',
			TagCompose::getValueWithTag( SiteTitle::NAME )
		);
	}

	public static function getArchiveDateTitleValue() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf(
			'%s %s %s',
			TagCompose::getValueWithTag( ArchiveDate::NAME ),
			'%%sep%%',
			TagCompose::getValueWithTag( SiteTitle::NAME )
		);
	}

	public static function getArchiveTitlePostType() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return sprintf(
			'%s %s %s',
			TagCompose::getValueWithTag( CustomPostTypePlural::NAME ),
			TagCompose::getValueWithTag( CurrentPagination::NAME ),
			'%%sep%%',
			TagCompose::getValueWithTag( SiteTitle::NAME )
		);
	}
}
