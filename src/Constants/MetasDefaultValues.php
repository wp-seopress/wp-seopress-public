<?php

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

abstract class MetasDefaultValues {

    public static function getPostTypeTitleValue(){
        return sprintf(
            '%s %s %s',
            TagCompose::getValueWithTag(PostTitle::NAME),
            '%%sep%%',
            TagCompose::getValueWithTag(SiteTitle::NAME)
        );
    }

    public static function getPostTypeDescriptionValue(){
        return TagCompose::getValueWithTag('post_excerpt');
    }

    public static function getTaxonomyCategoryValue(){
        return sprintf(
            '%s %s %s %s',
            TagCompose::getValueWithTag(CategoryTitle::NAME),
            TagCompose::getValueWithTag(CurrentPagination::NAME),
            '%%sep%%',
            TagCompose::getValueWithTag(SiteTitle::NAME)
        );
    }

    public static function getTagTitleValue(){
        return sprintf(
            '%s %s %s %s',
            TagCompose::getValueWithTag(TagTitle::NAME),
            TagCompose::getValueWithTag(CurrentPagination::NAME),
            '%%sep%%',
            TagCompose::getValueWithTag(SiteTitle::NAME)
        );
    }

    public static function getTermTitleValue(){
        return sprintf(
            '%s %s %s %s',
            TagCompose::getValueWithTag(TermTitle::NAME),
            TagCompose::getValueWithTag(CurrentPagination::NAME),
            '%%sep%%',
            TagCompose::getValueWithTag(SiteTitle::NAME)
        );
    }

    public static function getTaxonomyCategoryDescriptionValue(){
        return TagCompose::getValueWithTag(CategoryDescription::NAME);
    }

    public static function getTagDescriptionValue(){
        return TagCompose::getValueWithTag(TagDescription::NAME);
    }

    public static function getTermDescriptionValue(){
        return TagCompose::getValueWithTag(TermDescription::NAME);
    }

    public static function getAuthorTitleValue(){
        return sprintf(
            '%s %s %s',
            TagCompose::getValueWithTag(PostAuthor::NAME),
            '%%sep%%',
            TagCompose::getValueWithTag(SiteTitle::NAME)
        );
    }

    public static function getArchiveDateTitleValue(){
        return sprintf(
            '%s %s %s',
            TagCompose::getValueWithTag(ArchiveDate::NAME),
            '%%sep%%',
            TagCompose::getValueWithTag(SiteTitle::NAME)
        );
    }

    public static function getArchiveTitlePostType(){
        return sprintf(
            '%s %s %s',
            TagCompose::getValueWithTag(CustomPostTypePlural::NAME),
            TagCompose::getValueWithTag(CurrentPagination::NAME),
            '%%sep%%',
            TagCompose::getValueWithTag(SiteTitle::NAME)
        );
    }

}
