//Title tag counters / live preview
function sp_titles_counters(){
    jQuery("#seopress_titles_title_counters").after("<div id=\"seopress_titles_title_counters_val\">/ 60</div>");
    
    if(jQuery('#seopress_titles_title_meta').val().length > 0) {
        jQuery("#seopress_titles_title_counters").text(jQuery("#seopress_titles_title_meta").val().length);
    } else if(jQuery('#seopress_titles_title_meta').attr('placeholder').length) {
        jQuery("#seopress_titles_title_counters").text(jQuery("#seopress_titles_title_meta").attr('placeholder').length);
    }
    
    if(jQuery('#seopress_titles_title_meta').val().length > 60){   
        jQuery('#seopress_titles_title_counters').css('color', 'red');
    } else if(jQuery('#seopress_titles_title_meta').attr('placeholder').length > 60) {
        jQuery('#seopress_titles_title_counters').css('color', 'red');
    }
    jQuery("#seopress_titles_title_meta").keyup(function(event) {
        jQuery('#seopress_titles_title_counters').css('color', 'inherit');
        if(jQuery(this).val().length > 60){
            jQuery('#seopress_titles_title_counters').css('color', 'red');
        }
        if(jQuery('#seopress_titles_title_meta').val().length > 0) {
            jQuery("#seopress_titles_title_counters").text(jQuery("#seopress_titles_title_meta").val().length);
        } else if(jQuery('#seopress_titles_title_meta').attr('placeholder').length) {
            jQuery("#seopress_titles_title_counters").text(jQuery("#seopress_titles_title_meta").attr('placeholder').length);
        }

        if(jQuery(this).val().length > 0){
            jQuery(".snippet-title-custom").text(event.target.value);
            jQuery(".snippet-title").css('display', 'none');
            jQuery(".snippet-title-custom").css('display', 'block');
            jQuery(".snippet-title-default").css('display', 'none');
        } else if(jQuery(this).val().length == 0) {
            jQuery(".snippet-title-default").css('display', 'block');
            jQuery(".snippet-title-custom").css('display', 'none');
            jQuery(".snippet-title").css('display', 'none');
        };
    });
};

//Meta description counters / live preview
function sp_meta_desc_counters(){
    jQuery("#seopress_titles_desc_counters").after("<div id=\"seopress_titles_desc_counters_val\">/ 160</div>");

    if(jQuery('#seopress_titles_desc_meta').val().length > 0) {
        jQuery("#seopress_titles_desc_counters").text(jQuery("#seopress_titles_desc_meta").val().length);
    } else if(jQuery('#seopress_titles_desc_meta').attr('placeholder').length) {
        jQuery("#seopress_titles_desc_counters").text(jQuery("#seopress_titles_desc_meta").attr('placeholder').length);
    }

    if(jQuery('#seopress_titles_desc_meta').val().length > 160){   
        jQuery('#seopress_titles_desc_counters').css('color', 'red');
    } else if(jQuery('#seopress_titles_desc_meta').attr('placeholder').length > 160) {
        jQuery('#seopress_titles_desc_counters').css('color', 'red');
    }

    jQuery("#seopress_titles_desc_meta").keyup(function(event) {
        jQuery('#seopress_titles_desc_counters').css('color', 'inherit');
        if(jQuery(this).val().length > 160){
            jQuery('#seopress_titles_desc_counters').css('color', 'red');
        }

        if(jQuery('#seopress_titles_desc_meta').val().length > 0) {
            jQuery("#seopress_titles_desc_counters").text(jQuery("#seopress_titles_desc_meta").val().length);
        } else if(jQuery('#seopress_titles_desc_meta').attr('placeholder').length) {
            jQuery("#seopress_titles_desc_counters").text(jQuery("#seopress_titles_desc_meta").attr('placeholder').length);
        }
        
        if(jQuery(this).val().length > 0){
            jQuery(".snippet-description-custom").text(event.target.value);
            jQuery(".snippet-description").css('display', 'none');
            jQuery(".snippet-description-custom").css('display', 'inline');
            jQuery(".snippet-description-default").css('display', 'none');
        } else if(jQuery(this).val().length == 0) {
            jQuery(".snippet-description-default").css('display', 'inline');
            jQuery(".snippet-description-custom").css('display', 'none');
            jQuery(".snippet-description").css('display', 'none');
        };
    });
    
    jQuery("#excerpt").keyup(function(event) {
        if(jQuery('#seopress_titles_desc_meta').val().length == 0){  
            if (jQuery(".snippet-description-custom").val().length == 0) {
                jQuery(".snippet-description-custom").text(event.target.value);
                jQuery(".snippet-description").css('display', 'none');
                jQuery(".snippet-description-custom").css('display', 'inline');
                jQuery(".snippet-description-default").css('display', 'none');
            }
        }
    });
};

//Real Preview / Content Analysis
jQuery(document).ready(function(){
    //Tabs
    jQuery("#seopress-analysis-tabs .hidden").removeClass('hidden');
    jQuery("#seopress-analysis-tabs").tabs();
    //Real Preview
    function seopress_real_preview() {
        jQuery.ajax({
            method : 'GET',
            url : seopressAjaxRealPreview.seopress_real_preview,
            data: {
                action: 'seopress_do_real_preview',
                post_id: jQuery('#seopress-tabs').attr('data_id'),
                tax_name: jQuery('#seopress-tabs').attr('data_tax'),
                origin: jQuery('#seopress-tabs').attr('data_origin'),
                post_type: jQuery('#seopress_launch_analysis').attr('data_post_type'),
                seopress_analysis_target_kw: jQuery('#seopress_analysis_target_kw_meta').val(),
                _ajax_nonce: seopressAjaxRealPreview.seopress_nonce,
            },
            success : function( data ) {
                jQuery( '#seopress_cpt .google-snippet-preview .snippet-title' ).html(data.data.title);
                jQuery( '#seopress_cpt .google-snippet-preview .snippet-title-default' ).html(data.data.title);
                jQuery( '#seopress_titles_title_meta' ).attr("placeholder", data.data.title);
                jQuery( '#seopress_cpt .google-snippet-preview .snippet-description' ).html(data.data.meta_desc);
                jQuery( '#seopress_cpt .google-snippet-preview .snippet-description-default' ).html(data.data.meta_desc);
                jQuery( '#seopress_titles_desc_meta' ).attr("placeholder", data.data.meta_desc);
                jQuery( '#seopress_cpt #seopress_social_fb_title_meta' ).attr("placeholder", data.data.og_title);
                jQuery( '#seopress_cpt #seopress_social_fb_desc_meta' ).attr("placeholder", data.data.og_desc);
                jQuery( '#seopress_cpt #seopress_social_fb_img_meta' ).attr("placeholder", data.data.og_img);
                jQuery( '#seopress_cpt #seopress_social_twitter_title_meta' ).attr("placeholder", data.data.tw_title);
                jQuery( '#seopress_cpt #seopress_social_twitter_desc_meta' ).attr("placeholder", data.data.tw_desc);
                jQuery( '#seopress_cpt #seopress_social_twitter_img_meta' ).attr("placeholder", data.data.tw_img);
                jQuery( '#seopress_analysis_results_state' ).fadeIn().css('display', 'inline-block');
                jQuery( '#seopress_analysis_results_state' ).delay(3500).fadeOut();
                jQuery( '#seopress-analysis-tabs-1' ).load(' #seopress-analysis-tabs-1');
                jQuery( '#seopress-analysis-tabs-2' ).load(' #seopress-analysis-tabs-2');
                jQuery( '#seopress-analysis-tabs-3' ).load(' #seopress-analysis-tabs-3');
                jQuery( '#seopress-analysis-tabs-4' ).load(' #seopress-analysis-tabs-4');
                jQuery(' #seopress_titles_title_counters_val' ).remove();
                jQuery(' #seopress_titles_desc_counters_val' ).remove();
                sp_titles_counters();
                sp_meta_desc_counters();
            },
        });
    };
    seopress_real_preview();
    jQuery('#seopress_launch_analysis').on('click', function() {
        seopress_real_preview();
    });
});