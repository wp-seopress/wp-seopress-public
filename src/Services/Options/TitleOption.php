<?php

namespace SEOPress\Services\Options;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Constants\Options;

class TitleOption {
    /**
     * @since 4.3.0
     *
     * @return array
     */
    public function getOption() {
        return get_option(Options::KEY_OPTION_TITLE);
    }

    /**
     * @since 4.3.0
     *
     * @param string $key
     *
     * @return mixed
     */
    public function searchOptionByKey($key) {
        $data = $this->getOption();

        if (empty($data)) {
            return null;
        }

        if ( ! isset($data[$key])) {
            return null;
        }

        return $data[$key];
    }

    /**
     * @since 4.3.0
     *
     * @param string $path
     *
     * @return string|null
     */
    public function getTitlesCptNoIndexByPath($path) {
        $data = $this->searchOptionByKey('seopress_titles_archive_titles');

        if ( ! isset($data[$path]['noindex'])) {
            return null;
        }

        return $data[$path]['noindex'];
    }

    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function getSeparator() {
        $separator = $this->searchOptionByKey('seopress_titles_sep');
        if ( ! $separator) {
            return '-';
        }

        return $separator;
    }

    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function getHomeSiteTitle() {
        return $this->searchOptionByKey('seopress_titles_home_site_title');
    }

    /**
     * @since 4.4.0
     *
     * @return string
     */
    public function getHomeDescriptionTitle() {
        return $this->searchOptionByKey('seopress_titles_home_site_desc');
    }

    /**
     * @since 5.0.0
     *
     * @param int|null $id
     */
    public function getSingleCptNoIndex($id = null) {
        $arg = $id;

        if (null === $id) {
            global $post;
            if ( ! isset($post)) {
                return;
            }

            $arg = $post;
        }

        $currentCpt = get_post_type($arg);

        $option =  $this->searchOptionByKey('seopress_titles_single_titles');

        if ( ! isset($option[$currentCpt]['noindex'])) {
            return;
        }

        return $option[$currentCpt]['noindex'];
    }

    /**
     * @since 5.0.0
     *
     * @param int|null $id
     */
    public function getSingleCptNoFollow($id = null) {
        $arg = $id;

        if (null === $id) {
            global $post;
            if ( ! isset($post)) {
                return;
            }

            $arg = $post;
        }

        $currentCpt = get_post_type($arg);

        $option =  $this->searchOptionByKey('seopress_titles_single_titles');
        if ( ! isset($option[$currentCpt]['nofollow'])) {
            return;
        }

        return $option[$currentCpt]['nofollow'];
    }

    /**
     * @since 5.0.0
     */
    public function getTitleNoIndex() {
        return $this->searchOptionByKey('seopress_titles_noindex');
    }

    /**
     * @since 5.0.0
     */
    public function getTitleNoFollow() {
        return $this->searchOptionByKey('seopress_titles_nofollow');
    }

    /**
     * @since 5.0.0
     */
    public function getTitleNoOdp() {
        return $this->searchOptionByKey('seopress_titles_noodp');
    }

    /**
     * @since 5.0.0
     */
    public function getTitleNoArchive() {
        return $this->searchOptionByKey('seopress_titles_noarchive');
    }

    /**
     * @since 5.0.0
     */
    public function getTitleNoSnippet() {
        return $this->searchOptionByKey('seopress_titles_nosnippet');
    }

    /**
     * @since 5.0.0
     */
    public function getTitleNoImageIndex() {
        return $this->searchOptionByKey('seopress_titles_noimageindex');
    }

    /**
     * @since 5.4.1
     */
    public function getArchivesAuthorTitle(){
        return $this->searchOptionByKey('seopress_titles_archives_author_title');
    }

    /**
     * @since 5.4.1
     */
    public function getArchivesAuthorDescription(){
        return $this->searchOptionByKey('seopress_titles_archives_author_desc');
    }

    /**
     * @since 5.4.0
     */
    public function getTitleArchivesDate(){
        return $this->searchOptionByKey('seopress_titles_archives_date_title');
    }

    /**
     * @since 5.4.0
     */
    public function getTitleArchivesSearch(){
        return $this->searchOptionByKey('seopress_titles_archives_search_title');
    }

    /**
     * @since 5.4.0
     */
    public function getTitleArchives404(){
        return $this->searchOptionByKey('seopress_titles_archives_404_title');
    }

    /**
     * @since 5.4.0
     */
    public function getPagedRel(){
        return $this->searchOptionByKey('seopress_titles_paged_rel');
    }

    /**
     * @since 5.4.0
     */
    public function getTitleBpGroups(){
        return $this->searchOptionByKey('seopress_titles_bp_groups_title');
    }
}
