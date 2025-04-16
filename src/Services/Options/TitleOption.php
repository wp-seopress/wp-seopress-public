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
    public function getHomeSiteTitleAlt() {
        return $this->searchOptionByKey('seopress_titles_home_site_title_alt');
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
     * @since 6.6.0
     *
     * @param int|null $currentCpt
     */
    public function getSingleCptTitle($id = null) {
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

        if ( ! isset($option[$currentCpt]['title'])) {
            return;
        }

        return $option[$currentCpt]['title'];
    }

    /**
     * @since 6.6.0
     *
     * @param int|null $currentCpt
     */
    public function getSingleCptDesc($id = null) {
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

        if ( ! isset($option[$currentCpt]['description'])) {
            return;
        }

        return $option[$currentCpt]['description'];
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

    public function getArchiveCptTitle($postType = null){

        if($postType === null){
            global $post;
            if ( ! isset($post)) {
                return;
            }

            $postType = get_post_type($post);
        }

        $option = $this->searchOptionByKey('seopress_titles_archive_titles');

        if ( ! isset($option[$postType]['title'])) {
            return;
        }

        return $option[$postType]['title'];

    }

    public function getArchiveCptDescription($postType = null){

        if($postType === null){
            global $post;
            if ( ! isset($post)) {
                return;
            }

            $postType = get_post_type($post);
        }

        $option = $this->searchOptionByKey('seopress_titles_archive_titles');

        if ( ! isset($option[$postType]['description'])) {
            return;
        }

        return $option[$postType]['description'];

    }

    public function getTaxonomyCptTitle($taxonomy = null){

        if($taxonomy === null){
            $queriedObject           = get_queried_object();
            $taxonomy = null !== $queriedObject ? $queriedObject->taxonomy : '';
        }

        $option = $this->searchOptionByKey('seopress_titles_tax_titles');

        if ( ! isset($option[$taxonomy]['title'])) {
            return;
        }

        return $option[$taxonomy]['title'];

    }
    public function getTaxonomyCptDescription($taxonomy = null){

        if($taxonomy === null){
            $queriedObject           = get_queried_object();
            $taxonomy = null !== $queriedObject ? $queriedObject->taxonomy : '';
        }

        $option = $this->searchOptionByKey('seopress_titles_tax_titles');

        if ( ! isset($option[$taxonomy]['description'])) {
            return;
        }

        return $option[$taxonomy]['description'];

    }

        /**
     * @since 5.7
     *
     * @param int|null $id
     */
    public function getSingleCptDate($id = null) {
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

        if ( ! isset($option[$currentCpt]['date'])) {
            return;
        }

        return $option[$currentCpt]['date'];
    }

    public function getTitleFromSingle($postType = null){

        if($postType === null){
            global $post;
            if ( ! isset($post)) {
                return;
            }

            $postType = get_post_type($post);
        }

        $option = $this->searchOptionByKey('seopress_titles_single_titles');

        if ( ! isset($option[$postType]['title'])) {
            return;
        }

        return $option[$postType]['title'];


    }

    /**
     * @since 6.6.0
     *
     * @param int|null $id
     */
    public function getSingleCptThumb($id = null) {
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

        if ( ! isset($option[$currentCpt]['thumb_gcs'])) {
            return;
        }

        return $option[$currentCpt]['thumb_gcs'];
    }

    /**
     * @since 6.5.0
     *
     * @param int|null $currentCpt
     */
    public function getSingleCptEnable($currentCpt) {
        if (null === $currentCpt) {
            return;
        }

        $option =  $this->searchOptionByKey('seopress_titles_single_titles');

        if ( ! isset($option[$currentCpt]['enable'])) {
            return;
        }

        return $option[$currentCpt]['enable'];
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
     * @since 6.5.0
     *
     * @param int|null $currentCpt
     */
    public function getArchivesCPTTitle() {
        $queried_object = get_queried_object();
        $currentCpt = null !== $queried_object ? $queried_object->name : '';

        $option =  $this->searchOptionByKey('seopress_titles_archive_titles');

        if ( ! isset($option[$currentCpt]['title'])) {
            return;
        }

        return $option[$currentCpt]['title'];
    }

    /**
     * @since 6.6.0
     *
     * @param int|null $currentCpt
     */
    public function getArchivesCPTDesc() {
        $queried_object = get_queried_object();
        $currentCpt = null !== $queried_object ? $queried_object->name : '';

        $option =  $this->searchOptionByKey('seopress_titles_archive_titles');

        if ( ! isset($option[$currentCpt]['description'])) {
            return;
        }

        return $option[$currentCpt]['description'];
    }

    /**
     * @since 6.6.0
     *
     * @param int|null $currentCpt
     */
    public function getArchivesCPTNoIndex() {
        $queried_object = get_queried_object();
        $currentCpt = null !== $queried_object ? $queried_object->name : '';

        $option =  $this->searchOptionByKey('seopress_titles_archive_titles');
        if ( ! isset($option[$currentCpt]['noindex'])) {
            return;
        }

        return $option[$currentCpt]['noindex'];
    }

        /**
     * @since 6.6.0
     *
     * @param int|null $currentCpt
     */
    public function getArchivesCPTNoFollow() {
        $queried_object = get_queried_object();
        $currentCpt = null !== $queried_object ? $queried_object->name : '';

        $option =  $this->searchOptionByKey('seopress_titles_archive_titles');
        if ( ! isset($option[$currentCpt]['nofollow'])) {
            return;
        }

        return $option[$currentCpt]['nofollow'];
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
     * @since 6.0.0
     */
    public function getArchiveAuthorDisable(){
        return $this->searchOptionByKey('seopress_titles_archives_author_disable');
    }

    /**
     * @since 6.6.0
     */
    public function getArchiveAuthorNoIndex(){
        return $this->searchOptionByKey('seopress_titles_archives_author_noindex');
    }

    /**
     * @since 6.0.0
     */
    public function getArchiveDateDisable(){
        return $this->searchOptionByKey('seopress_titles_archives_date_disable');
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
     * @since 6.6.0
     *
     * @param int|null $currentTax
     */
    public function getTaxTitle() {
        $queried_object           = get_queried_object();
        $currentTax = null !== $queried_object ? $queried_object->taxonomy : '';

        $option =  $this->searchOptionByKey('seopress_titles_tax_titles');

        if ( ! isset($option[$currentTax]['title'])) {
            return;
        }

        return $option[$currentTax]['title'];
    }

    /**
     * @since 6.6.0
     *
     * @param int|null $currentTax
     */
    public function getTaxDesc() {
        $queried_object           = get_queried_object();
        $currentTax = null !== $queried_object ? $queried_object->taxonomy : '';

        $option =  $this->searchOptionByKey('seopress_titles_tax_titles');

        if ( ! isset($option[$currentTax]['description'])) {
            return;
        }

        return $option[$currentTax]['description'];
    }

    /**
     * @since 6.6.0
     *
     * @param int|null $id
     */
    public function getTaxNoIndex() {
        if (is_search()) {
            return;
        }

        $queried_object = get_queried_object();
        $currentTax = null !== $queried_object ? $queried_object->taxonomy : '';

        if (null === $queried_object) {
            global $tax;
            if ($tax !== null && property_exists($tax, 'name')) {
                $currentTax = $tax->name;
            }
        }

        if (null !== $queried_object && 'yes' === get_term_meta($queried_object->term_id, '_seopress_robots_index', true)) {
            return get_term_meta($queried_object->term_id, '_seopress_robots_index', true);
        }

        $option =  $this->searchOptionByKey('seopress_titles_tax_titles');

        if ( ! isset($option[$currentTax]['noindex'])) {
            return;
        }

        return $option[$currentTax]['noindex'];
    }

    /**
     * @since 6.6.0
     *
     * @param int|null $id
     */
    public function getTaxNoFollow() {
        if (is_search()) {
            return;
        }

        $queried_object = get_queried_object();
        $currentTax = null !== $queried_object ? $queried_object->taxonomy : '';

        if (null === $queried_object) {
            global $tax;
            if ($tax !== null && property_exists($tax, 'name')) {
                $currentTax = $tax->name;
            }
        }

        if (null !== $queried_object && 'yes' === get_term_meta($queried_object->term_id, '_seopress_robots_follow', true)) {
            return get_term_meta($queried_object->term_id, '_seopress_robots_follow', true);
        }

        $option =  $this->searchOptionByKey('seopress_titles_tax_titles');

        if ( ! isset($option[$currentTax]['nofollow'])) {
            return;
        }

        return $option[$currentTax]['nofollow'];
    }

    /**
     * @since 6.6.0
     *
     * @param int|null $currentTax
     */
    public function getTaxEnable($currentTax) {
        if (null === $currentTax) {
            return;
        }

        $option =  $this->searchOptionByKey('seopress_titles_tax_titles');

        if ( ! isset($option[$currentTax]['enable'])) {
            return;
        }

        return $option[$currentTax]['enable'];
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

    /**
     * @since 5.9.0
     */
    public function getBpGroupsDesc(){
        return $this->searchOptionByKey('seopress_titles_bp_groups_desc');
    }

    /**
     * @since 6.6.0
     */
    public function getBpGroupsNoIndex(){
        return $this->searchOptionByKey('seopress_titles_bp_groups_noindex');
    }

    /**
     * @since 5.9.0
     */
    public function getArchivesDateDesc(){
        return $this->searchOptionByKey('seopress_titles_archives_date_desc');
    }

    /**
     * @since 6.6.0
     */
    public function getArchivesDateNoIndex(){
        return $this->searchOptionByKey('seopress_titles_archives_date_noindex');
    }

    /**
     * @since 5.9.0
     */
    public function getArchivesSearchDesc(){
        return $this->searchOptionByKey('seopress_titles_archives_search_desc');
    }

    /**
     * @since 6.6.0
     */
    public function getArchivesSearchNoIndex(){
        return $this->searchOptionByKey('seopress_titles_archives_search_title_noindex');
    }

    /**
     * @since 5.9.0
     */
    public function getArchives404Desc(){
        return $this->searchOptionByKey('seopress_titles_archives_404_desc');
    }

    /**
     * @since 5.9.0
     */
    public function getNoSiteLinksSearchBox(){
        return $this->searchOptionByKey('seopress_titles_nositelinkssearchbox');
    }

    /**
     * @since 6.6.0
     */
    public function getAttachmentsNoIndex(){
        return $this->searchOptionByKey('seopress_titles_attachments_noindex');
    }

    /**
     * @since 6.6.0
     */
    public function getPagedNoIndex(){
        return $this->searchOptionByKey('seopress_titles_paged_noindex');
    }
}
