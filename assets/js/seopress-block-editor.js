//Retrieve title / meta-desc from source code
jQuery(document).ready(function($) {
const { subscribe, select } = wp.data;
let hasSaved = false;

    subscribe( () => {

        const editor  = select( 'core/editor' );
        const isSaved = editor && editor.didPostSaveRequestSucceed();

        if ( ! hasSaved && isSaved ) {
            $.ajax({
                method : 'GET',
                url : seopressAjaxRealPreview.seopress_real_preview,
                data: {
                    action: 'seopress_do_real_preview',
                    post_id: $('#seopress-tabs').attr('data_id'),
                    tax_name: $('#seopress-tabs').attr('data_tax'),
                    origin: $('#seopress-tabs').attr('data_origin'),
                    post_type: $('#seopress_launch_analysis').attr('data_post_type'),
                    seopress_analysis_target_kw: $('#seopress_analysis_target_kw_meta').val(),
                    _ajax_nonce: seopressAjaxRealPreview.seopress_nonce,
                },
                success : function( data ) {
                    $( '#seopress_cpt .google-snippet-preview .snippet-title' ).html(data.data.title);
                    $( '#seopress_cpt .google-snippet-preview .snippet-title-default' ).html(data.data.title);
                    $( '#seopress_titles_title_meta' ).attr("placeholder", data.data.title);
                    $( '#seopress_cpt .google-snippet-preview .snippet-description' ).html(data.data.meta_desc);
                    $( '#seopress_cpt .google-snippet-preview .snippet-description-default' ).html(data.data.meta_desc);
                    $( '#seopress_titles_desc_meta' ).attr("placeholder", data.data.meta_desc);
                    $( '#seopress_cpt #seopress_social_fb_title_meta' ).attr("placeholder", data.data.og_title);
                    $( '#seopress_cpt #seopress_social_fb_desc_meta' ).attr("placeholder", data.data.og_desc);
                    $( '#seopress_cpt #seopress_social_fb_img_meta' ).attr("placeholder", data.data.og_img);
                    $( '#seopress_cpt #seopress_social_twitter_title_meta' ).attr("placeholder", data.data.tw_title);
                    $( '#seopress_cpt #seopress_social_twitter_desc_meta' ).attr("placeholder", data.data.tw_desc);
                    $( '#seopress_cpt #seopress_social_twitter_img_meta' ).attr("placeholder", data.data.tw_img);
                    $( '#seopress_analysis_results_state' ).fadeIn().css('display', 'inline-block');
                    $( '#seopress_analysis_results_state' ).delay(3500).fadeOut();
                    $( '#seopress-analysis-tabs-1' ).load(' #seopress-analysis-tabs-1');
                    $( '#seopress-analysis-tabs-2' ).load(' #seopress-analysis-tabs-2');
                    $( '#seopress-analysis-tabs-3' ).load(' #seopress-analysis-tabs-3');
                    $( '#seopress-analysis-tabs-4' ).load(' #seopress-analysis-tabs-4');
                },
            });
        }
        hasSaved = !! isSaved;
    });
});