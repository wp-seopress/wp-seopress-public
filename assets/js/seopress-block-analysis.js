jQuery(document).ready(function(){

	const { subscribe, select } = wp.data;
	let hasSaved = false;

	subscribe( () => {

		const editor  = select( 'core/editor' );
		const isSaved = editor && editor.didPostSaveRequestSucceed();

		if ( ! hasSaved && isSaved ) {
			jQuery.ajax({
				method : 'POST',
				url : seopressContentAnalysis.seopress_content_analysis,
				data : {
					action: 'seopress_do_content_analysis',
					post_id: jQuery('#seopress_launch_analysis').attr('data_id'),
					post_type: jQuery('#seopress_launch_analysis').attr('data_post_type'),
					seopress_analysis_target_kw: jQuery('#seopress_analysis_target_kw_meta').val(),
					_ajax_nonce: seopressContentAnalysis.seopress_nonce,
				},
				success : function( data ) {
					jQuery( '#seopress_analysis_results_state' ).fadeIn().css('display', 'inline-block');
					jQuery( '#seopress_analysis_results_state' ).delay(3500).fadeOut();
					jQuery( '#seopress-analysis-tabs-1' ).load(' #seopress-analysis-tabs-1');
					jQuery( '#seopress-analysis-tabs-2' ).load(' #seopress-analysis-tabs-2');
					jQuery( '#seopress-analysis-tabs-3' ).load(' #seopress-analysis-tabs-3');
					jQuery( '#seopress-analysis-tabs-4' ).load(' #seopress-analysis-tabs-4');
				},
			});
		}

		hasSaved = !! isSaved;

	});
});