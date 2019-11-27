//Title tag counters / live preview
(function($) {
	function sp_titles_counters(){
		$("#seopress_titles_title_counters").after("<div id=\"seopress_titles_title_counters_val\">/ 60</div>");
		
		//Init values
		if($('#seopress_titles_title_meta').val().length > 0) {
			$("#seopress_titles_title_counters").text($("#seopress_titles_title_meta").val().length);
			$("#seopress_titles_title_pixel").text(pixelTitle($("#seopress_titles_title_meta").val()));
		} else if($('#seopress_titles_title_meta').attr('placeholder').length) {
			$("#seopress_titles_title_counters").text($("#seopress_titles_title_meta").attr('placeholder').length);
			$("#seopress_titles_title_pixel").text(pixelTitle($("#seopress_titles_title_meta").attr('placeholder')));
		}
		
		if($('#seopress_titles_title_meta').val().length > 60){   
			$('#seopress_titles_title_counters').css('color', 'red');
		} else if($('#seopress_titles_title_meta').attr('placeholder').length > 60) {
			$('#seopress_titles_title_counters').css('color', 'red');
		}

		//Pixels
		if(pixelTitle($('#seopress_titles_title_meta').val()) > 568){
			$('#seopress_titles_title_pixel').css('color', 'red');
		} else if(pixelTitle($('#seopress_titles_title_meta').attr('placeholder')) > 568) {
			$('#seopress_titles_title_pixel').css('color', 'red');
		}

		$("#seopress_titles_title_meta").keyup(function(event) {
			$('#seopress_titles_title_counters').css('color', 'inherit');
			$('#seopress_titles_title_pixel').css('color', 'inherit');

			if($(this).val().length > 60){
				$('#seopress_titles_title_counters').css('color', 'red');
			}

			if(pixelTitle($(this).val()) > 568){
				$('#seopress_titles_title_pixel').css('color', 'red');
			}

			if($('#seopress_titles_title_meta').val().length > 0) {
				$("#seopress_titles_title_counters").text($("#seopress_titles_title_meta").val().length);
				$("#seopress_titles_title_pixel").text(pixelTitle($("#seopress_titles_title_meta").val()));
			} else if($('#seopress_titles_title_meta').attr('placeholder').length) {
				$("#seopress_titles_title_counters").text($("#seopress_titles_title_meta").attr('placeholder').length);
				$("#seopress_titles_title_pixel").text(pixelTitle($("#seopress_titles_title_meta").attr('placeholder')));
			}

			if($(this).val().length > 0){
				$(".snippet-title-custom").text(event.target.value);
				$(".snippet-title").css('display', 'none');
				$(".snippet-title-custom").css('display', 'block');
				$(".snippet-title-default").css('display', 'none');
			} else if($(this).val().length == 0) {
				$(".snippet-title-default").css('display', 'block');
				$(".snippet-title-custom").css('display', 'none');
				$(".snippet-title").css('display', 'none');
			};
		});
	};

	//Meta description counters / live preview
	function sp_meta_desc_counters(){
		$("#seopress_titles_desc_counters").after("<div id=\"seopress_titles_desc_counters_val\">/ 160</div>");

		//Init values
		if($('#seopress_titles_desc_meta').val().length > 0) {
			$("#seopress_titles_desc_counters").text($("#seopress_titles_desc_meta").val().length);
			$("#seopress_titles_desc_pixel").text(pixelTitle($("#seopress_titles_desc_meta").val()));
		} else if($('#seopress_titles_desc_meta').attr('placeholder').length) {
			$("#seopress_titles_desc_counters").text($("#seopress_titles_desc_meta").attr('placeholder').length);
			$("#seopress_titles_desc_pixel").text(pixelTitle($("#seopress_titles_desc_meta").attr('placeholder')));
		}

		if($('#seopress_titles_desc_meta').val().length > 160){   
			$('#seopress_titles_desc_counters').css('color', 'red');
		} else if($('#seopress_titles_desc_meta').attr('placeholder').length > 160) {
			$('#seopress_titles_desc_counters').css('color', 'red');
		}

		//Pixels
		if(pixelTitle($('#seopress_titles_desc_meta').val()) > 940){
			$('#seopress_titles_desc_pixel').css('color', 'red');
		} else if(pixelTitle($('#seopress_titles_desc_meta').attr('placeholder')) > 940) {
			$('#seopress_titles_desc_pixel').css('color', 'red');
		}

		$("#seopress_titles_desc_meta").keyup(function(event) {
			$('#seopress_titles_desc_counters').css('color', 'inherit');
			if($(this).val().length > 160){
				$('#seopress_titles_desc_counters').css('color', 'red');
			}

			if(pixelTitle($(this).val()) > 940){
				$('#seopress_titles_desc_pixel').css('color', 'red');
			}

			if($('#seopress_titles_desc_meta').val().length > 0) {
				$("#seopress_titles_desc_counters").text($("#seopress_titles_desc_meta").val().length);
				$("#seopress_titles_desc_pixel").text(pixelTitle($("#seopress_titles_desc_meta").val()));
			} else if($('#seopress_titles_desc_meta').attr('placeholder').length) {
				$("#seopress_titles_desc_counters").text($("#seopress_titles_desc_meta").attr('placeholder').length);
				$("#seopress_titles_desc_pixel").text(pixelTitle($("#seopress_titles_desc_meta").attr('placeholder')));
			}
			
			if($(this).val().length > 0){
				$(".snippet-description-custom").text(event.target.value);
				$(".snippet-description").css('display', 'none');
				$(".snippet-description-custom").css('display', 'inline');
				$(".snippet-description-default").css('display', 'none');
			} else if($(this).val().length == 0) {
				$(".snippet-description-default").css('display', 'inline');
				$(".snippet-description-custom").css('display', 'none');
				$(".snippet-description").css('display', 'none');
			};
		});
		
		$("#excerpt").keyup(function(event) {
			if($('#seopress_titles_desc_meta').val().length == 0){  
				if ($(".snippet-description-custom").val().length == 0) {
					$(".snippet-description-custom").text(event.target.value);
					$(".snippet-description").css('display', 'none');
					$(".snippet-description-custom").css('display', 'inline');
					$(".snippet-description-default").css('display', 'none');
				}
			}
		});
	};

	/*
		Title / meta desc length in Pixels
		Credits: francois@gokam.co.uk + Benjamin Denis
		Note: the first character is a nonbreaking space
	*/
	function pixelTitle(input) {
		var letter = ' ·˙・«»àô€ÀÈÊÉéèê !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuüvwxyz{|}~–\n\r‘’£';
		var pixel = [5,6,6,18,10,10,10,10,10,12,12,12,12,10,10,10,5,5,6,10,10,16,12,3,6,6,7,11,5,6,5,5,10,10,10,10,10,10,10,10,10,10,5,5,11,11,11,10,18,12,12,13,13,12,11,14,13,5,9,12,10,15,13,14,12,14,13,12,11,13,12,17,12,12,11,5,5,5,8,10,6,10,10,9,10,10,5,10,10,4,4,9,4,15,10,10,10,10,6,9,9,5,10,9,13,9,9,9,6,5,6,11,10,0,0,4,4,10]
		var total = 0;
		for (var i = 0; i < input.length; i++) {
			total = total + pixel[letter.indexOf(input.substring(i,i+1))];
		}
		return  total;
	}
})(jQuery);

//Real Preview / Content Analysis
jQuery(document).ready(function($) {
	//Tabs
	$("#seopress-analysis-tabs .hidden").removeClass('hidden');
	$("#seopress-analysis-tabs").tabs();
	//Real Preview
	function seopress_real_preview() {
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
				Object.keys(data.data).forEach(key => {
					let a = document.createElement('textarea');
					a.innerHTML = data.data[key];
					data.data[key] = a.textContent;
				});

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
				$( '#seopress_cpt #seopress_robots_canonical_meta' ).attr("placeholder", data.data.canonical);
				$( '#seopress_analysis_results_state' ).fadeIn().css('display', 'inline-block');
				$( '#seopress_analysis_results_state' ).delay(3500).fadeOut();
				$( '#seopress-analysis-tabs-1' ).load(' #seopress-analysis-tabs-1');
				$( '#seopress-analysis-tabs-2' ).load(' #seopress-analysis-tabs-2');
				$( '#seopress-analysis-tabs-3' ).load(' #seopress-analysis-tabs-3');
				$( '#seopress-analysis-tabs-4' ).load(' #seopress-analysis-tabs-4');
				$(' #seopress_titles_title_counters_val' ).remove();
				$(' #seopress_titles_desc_counters_val' ).remove();
				sp_titles_counters();
				sp_meta_desc_counters();
			},
		});
	};
	seopress_real_preview();
	$('#seopress_launch_analysis').on('click', function() {
		seopress_real_preview();
	});
});