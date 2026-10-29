<?php
/**
 * File: ContentAnalysis.php
 *
 * Description: This file contains the function for the content analysis real preview.
 *
 * @package SEOPress
 * @subpackage Ajax
 */

defined( 'ABSPATH' ) || exit( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

/**
 * Content analysis real preview
 */
function seopress_do_real_preview() {
	check_ajax_referer( 'seopress_real_preview_nonce', '_ajax_nonce', true );

	if ( ! isset( $_GET['post_id'] ) ) {
		return;
	}

	$id      = absint( $_GET['post_id'] );
	$taxname = isset( $_GET['tax_name'] ) ? sanitize_key( $_GET['tax_name'] ) : null;

	if ( ! $id ) {
		return;
	}

	// Object-level capability check. The generic edit_posts cap is not enough:
	// the caller must be allowed to edit the specific object being analysed,
	// otherwise a low-privileged user could mutate analysis metadata for posts
	// or terms they do not own. For a taxonomy preview, $id is a term ID, so we
	// gate on the taxonomy's own edit_terms capability instead.
	if ( ! empty( $taxname ) ) {
		$taxonomy = get_taxonomy( $taxname );
		if ( ! $taxonomy || ! current_user_can( $taxonomy->cap->edit_terms ) ) {
			return;
		}
	} elseif ( ! current_user_can( 'edit_post', $id ) ) {
		return;
	}

	// Like the metabox routes: nothing for a role both metabox areas are
	// closed to by the Advanced > Security settings.
	if ( seopress_metabox_role_is_blocked( 'GLOBAL' ) && seopress_metabox_role_is_blocked( 'CONTENT_ANALYSIS' ) ) {
		return;
	}

	if ( 'yes' === get_post_meta( $id, '_seopress_redirections_enabled', true ) ) {
		$data['title'] = __( 'A redirect is active for this URL. Turn it off to get the Google preview and content analysis.', 'wp-seopress' );
		wp_send_json_error( $data );
		return;
	}

	$request_preview = seopress_get_service( 'RequestPreview' );
	$dom_result      = $request_preview->getDomById( $id, $taxname );

	if ( ! $dom_result['success'] ) {
		wp_send_json_success(
			array(
				'title'     => $request_preview->getFailureMessage( $dom_result['code'] ),
				'meta_desc' => '...',
			)
		);
		return;
	}

	$str = $dom_result['body'];

	$data = seopress_get_service( 'DomFilterContent' )->getData( $str, $id );

	if ( ! empty( $taxname ) ) {
		wp_send_json_success( $data );
	}

	$data = seopress_get_service( 'DomAnalysis' )->getDataAnalyze(
		$data,
		array(
			'id' => $id,
		)
	);

	$keywords = seopress_get_service( 'DomAnalysis' )->getKeywords(
		array(
			'id' => $id,
		)
	);

	// Save analysis data first so getScore() reads fresh values from the database.
	seopress_get_service( 'ContentAnalysisDatabase' )->saveData( $id, $data, $keywords );

	$post          = get_post( $id );
	$score         = seopress_get_service( 'DomAnalysis' )->getScore( $post );
	$data['score'] = $score;
	seopress_get_service( 'ContentAnalysisDatabase' )->saveData( $id, $data, $keywords );

	/**
	 * We delete old values because we have a new structure
	 *
	 * @deprecated
	 * @since 7.3.0
	 */
	delete_post_meta( $id, '_seopress_content_analysis_api' );
	delete_post_meta( $id, '_seopress_analysis_data' );

	// Re-enable QM.
	remove_filter( 'user_has_cap', 'seopress_disable_qm', 10, 3 );

	wp_send_json_success( $data );
}
add_action( 'wp_ajax_seopress_do_real_preview', 'seopress_do_real_preview' );
