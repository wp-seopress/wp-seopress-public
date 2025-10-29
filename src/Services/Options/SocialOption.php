<?php // phpcs:ignore

namespace SEOPress\Services\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Constants\Options;

/**
 * SocialOption
 */
class SocialOption {

	/**
	 * The getOption function.
	 *
	 * @since 4.5.0
	 *
	 * @return array
	 */
	public function getOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return get_option( Options::KEY_OPTION_SOCIAL );
	}

	/**
	 * The searchOptionByKey function.
	 *
	 * @since 4.5.0
	 *
	 * @param string $key The key.
	 *
	 * @return mixed
	 */
	public function searchOptionByKey( $key ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$data = $this->getOption();

		if ( empty( $data ) ) {
			return null;
		}

		if ( ! isset( $data[ $key ] ) ) {
			return null;
		}

		return $data[ $key ];
	}

	/**
	 * The getSocialKnowledgeType function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeType() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_type' );
	}

	/**
	 * The getSocialKnowledgeName function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeName() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_name' );
	}

	/**
	 * The getSocialAccountsFacebook function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialAccountsFacebook() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_accounts_facebook' );
	}

	/**
	 * The getSocialAccountsTwitter function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialAccountsTwitter() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_accounts_twitter' );
	}

	/**
	 * The getSocialAccountsPinterest function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialAccountsPinterest() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_accounts_pinterest' );
	}

	/**
	 * The getSocialAccountsInstagram function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialAccountsInstagram() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_accounts_instagram' );
	}

	/**
	 * The getSocialAccountsYoutube function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialAccountsYoutube() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_accounts_youtube' );
	}

	/**
	 * The getSocialAccountsLinkedin function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialAccountsLinkedin() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_accounts_linkedin' );
	}

	/**
	 * The getSocialAccountsExtra function.
	 *
	 * @since 6.5.0
	 *
	 * @return string
	 */
	public function getSocialAccountsExtra() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_accounts_extra' );
	}

	/**
	 * The getSocialKnowledgeImage function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeImage() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_img' );
	}

	/**
	 * The getSocialKnowledgeDesc function.
	 *
	 * @since 7.4.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeDesc() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_desc' );
	}

	/**
	 * The getSocialKnowledgeEmail function.
	 *
	 * @since 7.4.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeEmail() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_email' );
	}

	/**
	 * The getSocialKnowledgePhone function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgePhone() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_phone' );
	}

	/**
	 * The getSocialKnowledgeContactType function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeContactType() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_contact_type' );
	}

	/**
	 * The getSocialKnowledgeContactOption function.
	 *
	 * @since 4.5.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeContactOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_contact_option' );
	}

	/**
	 * The getSocialKnowledgeTaxID function.
	 *
	 * @since 7.4.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeTaxID() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_tax_id' );
	}

	/**
	 * The getSocialTwitterCard function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getSocialTwitterCard() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_twitter_card' );
	}

	/**
	 * The getSocialTwitterCardOg function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getSocialTwitterCardOg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_twitter_card_og' );
	}

	/**
	 * The getSocialTwitterImg function.
	 *
	 * @since 6.2
	 *
	 * @return string
	 */
	public function getSocialTwitterImg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_twitter_card_img' );
	}

	/**
	 * The getSocialTwitterImgSize function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getSocialTwitterImgSize() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_twitter_card_img_size' );
	}


	/**
	 * The getSocialFacebookOGEnable function.
	 *
	 * @since 6.5.0
	 *
	 * @return string
	 */
	public function getSocialFacebookOGEnable() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_facebook_og' );
	}

	/**
	 * The getSocialFacebookImgDefault function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getSocialFacebookImgDefault() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_facebook_img_default' );
	}

	/**
	 * The getSocialFacebookImg function.
	 *
	 * @since 5.9.0
	 *
	 * @return string
	 */
	public function getSocialFacebookImg() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_facebook_img' );
	}

	/**
	 * The getSocialFacebookImgCpt function.
	 *
	 * @param int|null $id The id.
	 *
	 * @since 6.6.0
	 *
	 * @return string
	 */
	public function getSocialFacebookImgCpt( $id = null ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$arg = $id;

		if ( null === $id ) {
			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$arg = $post;
		}

		$current_cpt = get_post_type( $arg );

		$option = $this->searchOptionByKey( 'seopress_social_facebook_img_cpt' );

		if ( ! isset( $option[ $current_cpt ]['url'] ) ) {
			return;
		}

		return $option[ $current_cpt ]['url'];
	}

	/**
	 * The getSocialFacebookLinkOwnership function.
	 *
	 * @since 6.5.0
	 *
	 * @return string
	 */
	public function getSocialFacebookLinkOwnership() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_facebook_link_ownership_id' );
	}

	/**
	 * The getSocialFacebookAdminID function.
	 *
	 * @since 6.5.0
	 *
	 * @return string
	 */
	public function getSocialFacebookAdminID() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_facebook_admin_id' );
	}

	/**
	 * The getSocialFacebookAppID function.
	 *
	 * @since 6.5.0
	 *
	 * @return string
	 */
	public function getSocialFacebookAppID() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_facebook_app_id' );
	}

	/**
	 * The getFacebookTitlePostOption function.
	 *
	 * @since 6.5.0
	 *
	 * @param int $id The id.
	 *
	 * @return string
	 */
	public function getFacebookTitlePostOption( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_fb_title', true );
		}

		return get_post_meta( $id, '_seopress_social_fb_title', true );
	}

	/**
	 * The getFacebookDescriptionPostOption function.
	 *
	 * @since 6.5.0
	 *
	 * @param int $id The id.
	 *
	 * @return string
	 */
	public function getFacebookDescriptionPostOption( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_fb_desc', true );
		}

		return get_post_meta( $id, '_seopress_social_fb_desc', true );
	}

	/**
	 * The getFacebookImagePostOption function.
	 *
	 * @since 6.5.0
	 *
	 * @param int $id The id.
	 *
	 * @return string
	 */
	public function getFacebookImagePostOption( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_fb_img', true );
		}

		return get_post_meta( $id, '_seopress_social_fb_img', true );
	}

	public function getFacebookImageHomeOption() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$page_id = get_option( 'page_for_posts' );

		$value = get_post_meta( $page_id, '_seopress_social_fb_img', true );
		if ( ! empty( $value ) ) {
			return $value;
		} elseif ( has_post_thumbnail( $page_id ) ) {
			return get_the_post_thumbnail_url( $page_id );
		}
	}

	/**
	 * The getTwitterTitlePostOption function.
	 *
	 * @since 6.5.0
	 *
	 * @param int $id The id.
	 *
	 * @return string
	 */
	public function getTwitterTitlePostOption( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_twitter_title', true );
		}

		return get_post_meta( $id, '_seopress_social_twitter_title', true );
	}

	/**
	 * The getTwitterDescriptionPostOption function.
	 *
	 * @since 6.5.0
	 *
	 * @param int $id The id.
	 *
	 * @return string
	 */
	public function getTwitterDescriptionPostOption( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_twitter_desc', true );
		}

		return get_post_meta( $id, '_seopress_social_twitter_desc', true );
	}

	/**
	 * The getTwitterImagePostOption function.
	 *
	 * @since 6.5.0
	 *
	 * @param int $id The id.
	 *
	 * @return string
	 */
	public function getTwitterImagePostOption( $id ) { // phpcs:ignore -- TODO: check if method is outside this class before renaming.

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_seopress_social_twitter_img', true );
		}

		return get_post_meta( $id, '_seopress_social_twitter_img', true );
	}

	public function getTwitterImageHome() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$id = get_option( 'page_for_posts' );
		if ( ! empty( $_seopress_social_twitter_img ) ) {
			$value = get_post_meta( $id, '_seopress_social_twitter_img', true );
			return $value;
		} elseif ( has_post_thumbnail( $id ) ) {
			return get_the_post_thumbnail_url( $id );
		}
	}

	/**
	 * The getSocialTwitterImgDefault function.
	 *
	 * @since 7.4.0
	 *
	 * @return string
	 */
	public function getSocialTwitterImgDefault() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_twitter_card_img' );
	}

	/**
	 * The getSocialLIImgSize function.
	 *
	 * @since 7.8.0
	 *
	 * @return string
	 */
	public function getSocialLIImgSize() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_li_img_size' );
	}

	/**
	 * The getSocialFvCreator function.
	 *
	 * @since 8.0.0
	 *
	 * @return string
	 */
	public function getSocialFvCreator() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_fv_creator' );
	}
}
