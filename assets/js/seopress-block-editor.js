//Retrieve title / meta-desc from source code
jQuery(document).ready(function($) {
const { subscribe, select } = wp.data;
let hasSaved = false;

    subscribe( () => {
        var isSavingPost = wp.data.select('core/editor').isSavingPost();
        var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
      
        if (isSavingPost && !isAutosavingPost && !hasSaved) {
        
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
                success : function( s ) {
                    var data_arr = {og_title : s.data.og_title.values,
                        og_desc : s.data.og_desc.values,
                        og_img : s.data.og_img.values, 
                        og_url : s.data.og_url.host,
                        og_site_name : s.data.og_site_name.values,
                        tw_title : s.data.tw_title.values,
                        tw_desc : s.data.tw_desc.values,
                        tw_img : s.data.tw_img.values
                    };
    
                    for (var key in data_arr) {
                        if (data_arr[key].length) {
                            if (data_arr[key].length > 1) {
                                key = data_arr[key].slice(-1)[0];
                            } else {
                                key = data_arr[key][0];
                            }
                        }
                    }

                    $( '#seopress_cpt .google-snippet-preview .snippet-title' ).html(s.data.title);
                    $( '#seopress_cpt .google-snippet-preview .snippet-title-default' ).html(s.data.title);
                    $( '#seopress_titles_title_meta' ).attr("placeholder", s.data.title);
                    $( '#seopress_cpt .google-snippet-preview .snippet-description' ).html(s.data.meta_desc);
                    $( '#seopress_cpt .google-snippet-preview .snippet-description-default' ).html(s.data.meta_desc);
                    $( '#seopress_titles_desc_meta' ).attr("placeholder", s.data.meta_desc);
                    
                    $( '#seopress_cpt #seopress_social_fb_title_meta' ).attr("placeholder", data_arr.og_title);
                    $( '#seopress_cpt .facebook-snippet-preview .snippet-fb-title').html(data_arr.og_title);
                    $( '#seopress_cpt .facebook-snippet-preview .snippet-fb-title-default').html(data_arr.og_title);
                    
                    $( '#seopress_cpt #seopress_social_fb_desc_meta' ).attr("placeholder", data_arr.og_desc);
                    $( '#seopress_cpt .facebook-snippet-preview .snippet-fb-description').html(data_arr.og_desc);
                    $( '#seopress_cpt .facebook-snippet-preview .snippet-fb-description-default').html(data_arr.og_desc);

                    $( '#seopress_cpt #seopress_social_fb_img_meta' ).attr("placeholder", data_arr.og_img);
                    $( '#seopress_cpt .snippet-fb-img img' ).attr("src", data_arr.og_img);
                    $( '#seopress_cpt .snippet-fb-img-default img' ).attr("src", data_arr.og_img);

                    $("#seopress_cpt .facebook-snippet-preview .snippet-fb-url").html(data_arr.og_url),
                    $("#seopress_cpt .facebook-snippet-preview .snippet-fb-site-name").html(data_arr.og_site_name),

                    $( '#seopress_cpt #seopress_social_twitter_title_meta' ).attr("placeholder", data_arr.tw_title);
                    $( '#seopress_cpt #seopress_social_twitter_desc_meta' ).attr("placeholder", data_arr.tw_desc);
                    $( '#seopress_cpt #seopress_social_twitter_img_meta' ).attr("placeholder", data_arr.tw_img);
                    $( '#seopress_cpt #seopress_robots_canonical_meta').attr('placeholder', s.data.canonical),
                    $( '#seopress_analysis_results_state' ).fadeIn().css('display', 'inline-block');
                    $( '#seopress_analysis_results_state' ).delay(3500).fadeOut();
                    $( '#seopress-analysis-tabs-1' ).load(' #seopress-analysis-tabs-1');
                    $( '#seopress-analysis-tabs-2' ).load(' #seopress-analysis-tabs-2');
                    $( '#seopress-analysis-tabs-3' ).load(' #seopress-analysis-tabs-3');
                    $( '#seopress-analysis-tabs-4' ).load(' #seopress-analysis-tabs-4');
                },
            });
        }
        hasSaved = !! isSavingPost;
    });
});