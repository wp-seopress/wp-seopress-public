<?php // phpcs:ignore

namespace SEOPress\Services\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SEOPress\Compose\UseArchivePostType;
use SEOPress\Constants\Options;

/**
 * SocialOption
 */
class SocialOption {

	use UseArchivePostType;

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
	 * Get the WordPress user ID linked to the Person knowledge graph.
	 *
	 * @since 9.7
	 *
	 * @return int
	 */
	public function getSocialKnowledgeUserId() {
		$id = $this->searchOptionByKey( 'seopress_social_knowledge_user_id' );
		return ! empty( $id ) ? absint( $id ) : 0;
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
	 * The getSocialKnowledgeLegalName function.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeLegalName() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_legal_name' );
	}

	/**
	 * The getSocialKnowledgeFoundingDate function.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeFoundingDate() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_founding_date' );
	}

	/**
	 * The getSocialKnowledgeEmployees function.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeEmployees() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		$value = $this->searchOptionByKey( 'seopress_social_knowledge_employees' );
		return ! empty( $value ) ? (string) absint( $value ) : '';
	}

	/**
	 * The getSocialKnowledgeStreet function.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeStreet() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_street' );
	}

	/**
	 * The getSocialKnowledgeLocality function.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeLocality() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_locality' );
	}

	/**
	 * The getSocialKnowledgeRegion function.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeRegion() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_region' );
	}

	/**
	 * The getSocialKnowledgePostalCode function.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgePostalCode() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_postal_code' );
	}

	/**
	 * The getSocialKnowledgeCountry function.
	 *
	 * @since 9.8.0
	 *
	 * @return string
	 */
	public function getSocialKnowledgeCountry() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return $this->searchOptionByKey( 'seopress_social_knowledge_country' );
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
		$current_cpt = '';

		if ( null !== $id ) {
			$current_cpt = (string) get_post_type( $id );
		} else {
			/*
			 * On a post type archive the post type is what the query was
			 * resolved against, so read it from the query rather than from the
			 * loop. `global $post` holds the first result there, which works
			 * only for as long as the archive has one: an archive with no
			 * published post left the option unread and the page fell back to
			 * the sitewide image with nothing to show for it. Reading it from
			 * the query also gives the Twitter hook a way to resolve the same
			 * value without duplicating the branch.
			 *
			 * Shared with Titles & Metas > Archives through the trait: the two
			 * settings are keyed on the same post type, read on the same pages,
			 * and the queried object is not always the post type there. On a
			 * WooCommerce shop page a theme or a builder can leave it as the
			 * shop `WP_Post`, and resolving from the queried object alone would
			 * have left this getter reading the loop again, back to the archive
			 * having to hold a post.
			 */
			$current_cpt = $this->getCurrentArchivePostType();

			if ( '' === $current_cpt ) {
				/*
				 * Not an archive, or one the shared resolution stays out of:
				 * the shop page served as a page, where the post type is the
				 * one of the page itself, and the taxonomy archives that are
				 * post type archives at the same time, where the loop is what
				 * this image was already resolved from.
				 */
				global $post;
				if ( ! isset( $post ) ) {
					return;
				}

				$current_cpt = (string) get_post_type( $post );
			}
		}

		$option = $this->searchOptionByKey( 'seopress_social_facebook_img_cpt' );

		if ( '' === $current_cpt || ! isset( $option[ $current_cpt ]['url'] ) ) {
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
	 * The getSocialFacebookAdminID function.
	 *
	 * @deprecated 9.8.0 The "Facebook Admin ID" option was removed.
	 * @todo       Remove after 2027-04-22 (kept for ~1 year to prevent fatal errors in older Pro releases calling this method).
	 *
	 * @return null
	 */
	public function getSocialFacebookAdminID() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return null;
	}

	/**
	 * The getSocialFvCreator function.
	 *
	 * @deprecated 9.8.0 The "Fediverse Creator" tag was removed.
	 * @todo       Remove after 2027-04-22 (kept for ~1 year to prevent fatal errors in older Pro releases calling this method).
	 *
	 * @return null
	 */
	public function getSocialFvCreator() { // phpcs:ignore -- TODO: check if method is outside this class before renaming.
		return null;
	}

}
