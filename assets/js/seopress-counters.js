
function sp_titles_counters() {
	var meta_title_val = jQuery("#seopress_titles_title_meta").val();
	var meta_title_placeholder = jQuery("#seopress_titles_title_meta").attr("placeholder");

	jQuery("#seopress_titles_title_counters").after('<div id="seopress_titles_title_counters_val">/ 60</div>'), 
	meta_title_val.length > 0 ? (jQuery("#seopress_titles_title_counters").text(meta_title_val.length), 
	jQuery("#seopress_titles_title_pixel").text(pixelTitle(meta_title_val))) : meta_title_placeholder.length && (jQuery("#seopress_titles_title_counters").text(meta_title_placeholder.length), 
	jQuery("#seopress_titles_title_pixel").text(pixelTitle(meta_title_placeholder))),
	
	meta_title_val.length > 60 ? jQuery("#seopress_titles_title_counters").css("color", "red") : meta_title_placeholder.length > 60 && jQuery("#seopress_titles_title_counters").css("color", "red"), 
	pixelTitle(meta_title_val) > 568 ? jQuery("#seopress_titles_title_pixel").css("color", "red") : pixelTitle(meta_title_placeholder) > 568 && jQuery("#seopress_titles_title_pixel").css("color", "red");
	
	if (meta_title_val.length) {
		var progress = Math.round(pixelTitle(meta_title_val)/568*100);
	} else {
		var progress = Math.round(pixelTitle(meta_title_placeholder)/568*100);
	}

	if (progress >= 100) {
		progress = 100;
	}
	
	jQuery('#seopress_titles_title_counters_progress').attr('aria-valuenow',progress),
	jQuery('#seopress_titles_title_counters_progress').text(progress +'%'),
	jQuery('#seopress_titles_title_counters_progress').css('width',progress +'%'),
	
	jQuery("#seopress_titles_title_meta, #seopress-tag-single-title, #seopress-tag-single-site-title, #seopress-tag-single-sep").on('keyup paste change click', function(e) {
		var meta_title_val = jQuery("#seopress_titles_title_meta").val();
		var meta_title_placeholder = jQuery("#seopress_titles_title_meta").attr("placeholder");

		jQuery("#seopress_titles_title_counters").css("color", "inherit"),
		jQuery("#seopress_titles_title_pixel").css("color", "inherit"),
		
		meta_title_val.length > 60 && jQuery("#seopress_titles_title_counters").css("color", "red"),
		pixelTitle(meta_title_val) > 568 && jQuery("#seopress_titles_title_pixel").css("color", "red");

		if (meta_title_val.length == 0) { 
			meta_title_placeholder.length > 60 && jQuery("#seopress_titles_title_counters").css("color", "red"),
			pixelTitle(meta_title_placeholder) > 568 && jQuery("#seopress_titles_title_pixel").css("color", "red")
		}

		meta_title_val.length > 0 ? (jQuery("#seopress_titles_title_counters").text(meta_title_val.length),
		jQuery("#seopress_titles_title_pixel").text(pixelTitle(meta_title_val))) : meta_title_placeholder.length && (jQuery("#seopress_titles_title_counters").text(meta_title_placeholder.length),
		jQuery("#seopress_titles_title_pixel").text(pixelTitle(meta_title_placeholder))),
		
		meta_title_val.length > 0 ? (jQuery(".snippet-title-custom").text(e.target.value),
		jQuery(".snippet-title").css("display", "none"),
		jQuery(".snippet-title-custom").css("display", "block"),
		jQuery(".snippet-title-default").css("display", "none")) : 0 == meta_title_val.length && (jQuery(".snippet-title-default").css("display", "block"),
		jQuery(".snippet-title-custom").css("display", "none"), jQuery(".snippet-title").css("display", "none"));

		if (meta_title_val.length) {
			var progress = Math.round(pixelTitle(meta_title_val)/568*100);
		} else {
			var progress = Math.round(pixelTitle(meta_title_placeholder)/568*100);
		}

		if (progress >= 100) {
			progress = 100;
		}

		jQuery('#seopress_titles_title_counters_progress').attr('aria-valuenow',progress),
		jQuery('#seopress_titles_title_counters_progress').text(progress +'%'),
		jQuery('#seopress_titles_title_counters_progress').css('width',progress +'%')
    })
}

function sp_meta_desc_counters() {
	var meta_desc_val = jQuery("#seopress_titles_desc_meta").val();
	var meta_desc_placeholder = jQuery("#seopress_titles_desc_meta").attr("placeholder");
	
	jQuery("#seopress_titles_desc_counters").after('<div id="seopress_titles_desc_counters_val">/ 160</div>'), 
	
	meta_desc_val.length > 0 ? (jQuery("#seopress_titles_desc_counters").text(meta_desc_val.length), 
	jQuery("#seopress_titles_desc_pixel").text(pixelDesc(meta_desc_val))) : meta_desc_placeholder.length && (jQuery("#seopress_titles_desc_counters").text(meta_desc_placeholder.length), 
	jQuery("#seopress_titles_desc_pixel").text(pixelDesc(meta_desc_placeholder))), 
	
	meta_desc_val.length > 160 ? jQuery("#seopress_titles_desc_counters").css("color", "red") : meta_desc_placeholder.length > 160 && jQuery("#seopress_titles_desc_counters").css("color", "red"), 
	pixelDesc(meta_desc_val) > 940 ? jQuery("#seopress_titles_desc_pixel").css("color", "red") : pixelDesc(meta_desc_placeholder) > 940 && jQuery("#seopress_titles_desc_pixel").css("color", "red");
	
	if (meta_desc_val.length) {
		var progress = Math.round(pixelDesc(meta_desc_val)/940*100);
	} else {
		var progress = Math.round(pixelDesc(meta_desc_placeholder)/940*100);
	}

	if (progress >= 100) {
		progress = 100;
	}

	jQuery('#seopress_titles_desc_counters_progress').attr('aria-valuenow',progress),
	jQuery('#seopress_titles_desc_counters_progress').text(progress +'%'),
	jQuery('#seopress_titles_desc_counters_progress').css('width',progress +'%'),

	jQuery("#seopress_titles_desc_meta, #seopress-tag-single-excerpt").on('keyup paste change click', function(e) {
		var meta_desc_val = jQuery("#seopress_titles_desc_meta").val();
		var meta_desc_placeholder = jQuery("#seopress_titles_desc_meta").attr("placeholder");

		jQuery("#seopress_titles_desc_counters").css("color", "inherit"),
		jQuery('#seopress_titles_desc_pixel').css('color', 'inherit'),
		
		meta_desc_val.length > 160 && jQuery("#seopress_titles_desc_counters").css("color", "red"),
		pixelDesc(meta_desc_val) > 940 && jQuery("#seopress_titles_desc_pixel").css("color", "red");

		if (meta_desc_val.length == 0) { 
			meta_desc_placeholder.length > 160 && jQuery("#seopress_titles_desc_counters").css("color", "red"),
			pixelDesc(meta_desc_placeholder) > 940 && jQuery("#seopress_titles_desc_pixel").css("color", "red")
		}
		
		meta_desc_val.length > 0 ? (jQuery("#seopress_titles_desc_counters").text(meta_desc_val.length),
		jQuery("#seopress_titles_desc_pixel").text(pixelDesc(meta_desc_val))) : meta_desc_placeholder.length && (jQuery("#seopress_titles_desc_counters").text(meta_desc_placeholder.length),
		jQuery("#seopress_titles_desc_pixel").text(pixelDesc(meta_desc_placeholder))), meta_desc_val.length > 0 ? (jQuery(".snippet-description-custom").text(e.target.value),
		jQuery(".snippet-description").css("display", "none"), 
		jQuery(".snippet-description-custom").css("display", "inline"), 
		jQuery(".snippet-description-default").css("display", "none")) : 0 == meta_desc_val.length && (jQuery(".snippet-description-default").css("display", "inline"), 
		jQuery(".snippet-description-custom").css("display", "none"), 
		jQuery(".snippet-description").css("display", "none"));

		if (meta_desc_val.length) {
			var progress = Math.round(pixelDesc(meta_desc_val)/940*100);
		} else {
			var progress = Math.round(pixelDesc(meta_desc_placeholder)/940*100);
		}
	
		if (progress >= 100) {
			progress = 100;
		}

		jQuery('#seopress_titles_desc_counters_progress').attr('aria-valuenow',progress),
		jQuery('#seopress_titles_desc_counters_progress').text(progress +'%'),
		jQuery('#seopress_titles_desc_counters_progress').css('width',progress +'%')

    }), jQuery("#excerpt, .editor-post-excerpt textarea").keyup(function(e) {
		var meta_desc_val = jQuery("#seopress_titles_desc_meta").val();
		var meta_desc_placeholder = jQuery("#seopress_titles_desc_meta").attr("placeholder");

		0 == meta_desc_val.length && 0 == jQuery(".snippet-description-custom").val().length && (jQuery(".snippet-description-custom").text(e.target.value), 
		jQuery(".snippet-description").css("display", "none"), 
		jQuery(".snippet-description-custom").css("display", "inline"), 
		jQuery(".snippet-description-default").css("display", "none"));

		if (meta_desc_val.length) {
			var progress = meta_desc_val.length;
		} else {
			var progress = meta_desc_placeholder.length;
		}
		if (progress >= 100) {
			progress = 100;
		}

		jQuery('#seopress_titles_desc_counters_progress').attr('aria-valuenow',progress),
		jQuery('#seopress_titles_desc_counters_progress').text(progress +'%'),
		jQuery('#seopress_titles_desc_counters_progress').css('width',progress +'%')
    })
}

function pixelTitle(e) {
    inputText = e; 
    font = "18px Arial"; 

    canvas = document.createElement("canvas"); 
    context = canvas.getContext("2d"); 
    context.font = font; 
    width = context.measureText(inputText).width; 
    formattedWidth = Math.ceil(width); 
    
    return formattedWidth;
}

function pixelDesc(e) {
    inputText = e; 
    font = "14px Arial"; 

    canvas = document.createElement("canvas"); 
    context = canvas.getContext("2d"); 
    context.font = font; 
    width = context.measureText(inputText).width; 
    formattedWidth = Math.ceil(width); 
    
    return formattedWidth;
}

function sp_social() {
	jQuery("#seopress_social_fb_title_meta, #seopress-tag-single-title, #seopress-tag-single-site-title, #seopress-tag-single-sep").on('keyup paste change click', function(e) {
		var meta_fb_title_val = jQuery("#seopress_social_fb_title_meta").val();

		meta_fb_title_val.length > 0 ? (jQuery(".snippet-fb-title-custom").text(e.target.value),
		jQuery(".snippet-fb-title").css("display", "none"),
		jQuery(".snippet-fb-title-custom").css("display", "block"),
		jQuery(".snippet-fb-title-default").css("display", "none")) : 0 == meta_fb_title_val.length && (jQuery(".snippet-fb-title-default").css("display", "block"),
		jQuery(".snippet-fb-title-custom").css("display", "none"), jQuery(".snippet-fb-title").css("display", "none"))
	})

	jQuery("#seopress_social_fb_desc_meta").on('keyup paste change click', function(e) {
		var meta_fb_desc_val = jQuery("#seopress_social_fb_desc_meta").val();

		meta_fb_desc_val.length > 0 ? (jQuery(".snippet-fb-description-custom").text(e.target.value),
		jQuery(".snippet-fb-description").css("display", "none"),
		jQuery(".snippet-fb-description-custom").css("display", "block"),
		jQuery(".snippet-fb-description-default").css("display", "none")) : 0 == meta_fb_desc_val.length && (jQuery(".snippet-fb-description-default").css("display", "block"),
		jQuery(".snippet-fb-description-custom").css("display", "none"), jQuery(".snippet-fb-description").css("display", "none"));
	})

	jQuery("#seopress_social_fb_img_meta").on('keyup paste change click', function(e) {
		var meta_fb_img_val = jQuery("#seopress_social_fb_img_meta").val();

		meta_fb_img_val.length > 0 ? (jQuery(".snippet-fb-img-custom img").attr("src",e.target.value),
		jQuery(".snippet-fb-img").css("display", "none"),
		jQuery(".snippet-fb-img-custom").css("display", "block"),
		jQuery(".snippet-fb-img-default").css("display", "none")) : 0 == meta_fb_img_val.length && (jQuery(".snippet-fb-img-default").css("display", "block"),
		jQuery(".snippet-fb-img-custom").css("display", "none"), jQuery(".snippet-fb-img").css("display", "none"));
	})
}

jQuery(document).ready(function(e) {
    function s() {
        e.ajax({
            method: "GET",
            url: seopressAjaxRealPreview.seopress_real_preview,
            data: {
                action: "seopress_do_real_preview",
                post_id: e("#seopress-tabs").attr("data_id"),
                tax_name: e("#seopress-tabs").attr("data_tax"),
                origin: e("#seopress-tabs").attr("data_origin"),
                post_type: e("#seopress_launch_analysis").attr("data_post_type"),
                seopress_analysis_target_kw: e("#seopress_analysis_target_kw_meta").val(),
                _ajax_nonce: seopressAjaxRealPreview.seopress_nonce
            },
            success: function(s) {
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
					if (data_arr.length) {
						if (data_arr[key].length > 1) {
							key = data_arr[key].slice(-1)[0];
						} else {
							key = data_arr[key][0];
						}
					}
				}

				e("#seopress_cpt .google-snippet-preview .snippet-title").html(s.data.title),
				e("#seopress_cpt .google-snippet-preview .snippet-title-default").html(s.data.title),
				e("#seopress_titles_title_meta").attr("placeholder", s.data.title),
				e("#seopress_cpt .google-snippet-preview .snippet-description").html(s.data.meta_desc),
				e("#seopress_cpt .google-snippet-preview .snippet-description-default").html(s.data.meta_desc),
				e("#seopress_titles_desc_meta").attr("placeholder", s.data.meta_desc),

				e("#seopress_cpt #seopress_social_fb_title_meta").attr("placeholder", data_arr.og_title),
				e("#seopress_cpt .facebook-snippet-preview .snippet-fb-title").html(data_arr.og_title),
				e("#seopress_cpt .facebook-snippet-preview .snippet-fb-title-default").html(data_arr.og_title),

				e("#seopress_cpt #seopress_social_fb_desc_meta").attr("placeholder", data_arr.og_desc),
				e("#seopress_cpt .facebook-snippet-preview .snippet-fb-description").html(data_arr.og_desc),
				e("#seopress_cpt .facebook-snippet-preview .snippet-fb-description-default").html(data_arr.og_desc),

                e("#seopress_cpt #seopress_social_fb_img_meta").attr("placeholder", data_arr.og_img),
                e("#seopress_cpt .facebook-snippet-preview .snippet-fb-img img").attr("src", data_arr.og_img),
                e("#seopress_cpt .facebook-snippet-preview .snippet-fb-img-default img").attr("src", data_arr.og_img),

                e("#seopress_cpt .facebook-snippet-preview .snippet-fb-url").html(data_arr.og_url),
                e("#seopress_cpt .facebook-snippet-preview .snippet-fb-site-name").html(data_arr.og_site_name),

				e("#seopress_cpt #seopress_social_twitter_title_meta").attr("placeholder", data_arr.tw_title),
				e("#seopress_cpt #seopress_social_twitter_desc_meta").attr("placeholder", data_arr.tw_desc),
				e("#seopress_cpt #seopress_social_twitter_img_meta").attr("placeholder", data_arr.tw_img),

				e("#seopress_cpt #seopress_robots_canonical_meta").attr("placeholder", s.data.canonical),
				e("#seopress_analysis_results_state").fadeIn().css("display", "inline-block"),
				e("#seopress_analysis_results_state").delay(3500).fadeOut(),
				e("#seopress-analysis-tabs-1").load(" #seopress-analysis-tabs-1"),
				e("#seopress-analysis-tabs-2").load(" #seopress-analysis-tabs-2"),
				e("#seopress-analysis-tabs-3").load(" #seopress-analysis-tabs-3"),
				e("#seopress-analysis-tabs-4").load(" #seopress-analysis-tabs-4"),
				e(" #seopress_titles_title_counters_val").remove(),
				e(" #seopress_titles_desc_counters_val").remove(),
				sp_titles_counters(), 
				sp_meta_desc_counters(),
				sp_social()
            }
        })
    }
	e("#seopress-analysis-tabs .hidden").removeClass("hidden"), 
	e("#seopress-analysis-tabs").tabs(), 
	s(), 
	e("#seopress_launch_analysis").on("click", function() {
        s()
    })
});