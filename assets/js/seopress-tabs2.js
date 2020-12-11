document.addEventListener('DOMContentLoaded', function(){
	const $ = jQuery

	$("#seopress-tabs .hidden").removeClass('hidden');
	$("#seopress-tabs").tabs();


	function sp_get_field_length(e) {
		if (e.val().length > 0) {
			meta = e.val() + ' ';
		} else {
			meta = e.val();
		}
		return meta;
	}

	$('#seopress-tag-single-title').click(function() {
		$("#seopress_titles_title_meta").val(sp_get_field_length($("#seopress_titles_title_meta")) + $('#seopress-tag-single-title').attr('data-tag'));
	});
	$('#seopress-tag-single-site-title').click(function() {
		$("#seopress_titles_title_meta").val(sp_get_field_length($("#seopress_titles_title_meta")) + $('#seopress-tag-single-site-title').attr('data-tag'));
	});
	$('#seopress-tag-single-excerpt').click(function() {
		$("#seopress_titles_desc_meta").val(sp_get_field_length($("#seopress_titles_desc_meta")) + $('#seopress-tag-single-excerpt').attr('data-tag'));
	});
	$('#seopress-tag-single-sep').click(function() {
		$("#seopress_titles_title_meta").val(sp_get_field_length($("#seopress_titles_title_meta")) + $('#seopress-tag-single-sep').attr('data-tag'));
	});



	let alreadyBind = false

	//All variables
	$('.seopress-tag-dropdown').each(function(item){

		const _self = $(this)
		$(this).on("click", function() {

			$(this).next('.sp-wrap-tag-variables-list').toggleClass('open');

			$(this).next('.sp-wrap-tag-variables-list').find('li').on("click", function(e) {
				
				if(_self.hasClass("tag-title")){
					$("#seopress_titles_title_meta").val(sp_get_field_length($("#seopress_titles_title_meta")) + $(this).attr('data-value'));
					$("#seopress_titles_title_meta").trigger('paste')
				}
				if(_self.hasClass("tag-description")){
					$("#seopress_titles_desc_meta").val(sp_get_field_length($("#seopress_titles_desc_meta")) + $(this).attr('data-value'));
					$("#seopress_titles_desc_meta").trigger('paste')
				}
				e.stopImmediatePropagation();
			});


			function closeItem (e){

				if($(e.target).hasClass("dashicons") || $(e.target).hasClass("seopress-tag-single-all")){
					return
				}
				
				alreadyBind = false
				$(document).off("click", closeItem)
				$('.sp-wrap-tag-variables-list').removeClass('open')
			}
			
			if(!alreadyBind){
				alreadyBind = true
				$(document).on("click", closeItem)
			}
	
		});
	})
});