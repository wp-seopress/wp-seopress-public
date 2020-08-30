jQuery(document).ready(function($) {
	var hash = $(location).attr('hash').split('#tab=')[1];

	if (typeof hash !='undefined') {
		$('#'+hash+'-tab').addClass("nav-tab-active");
		$('#'+hash).addClass("active");
	} else {
		if(typeof sessionStorage!='undefined') {
			var seopress_tab_session_storage = sessionStorage.getItem("seopress_titles_tab");
			if (seopress_tab_session_storage) {
				$('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
				$('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
				
				$('#'+seopress_tab_session_storage+'-tab').addClass("nav-tab-active");
				$('#'+seopress_tab_session_storage).addClass("active");
			} else {
				//Default TAB
				$('#tab_seopress_titles_home-tab').addClass("nav-tab-active");
				$('#tab_seopress_titles_home').addClass("active");
			}
		}
	};
	$("#seopress-tabs").find("a.nav-tab").click(function(e){
		e.preventDefault();
		var hash = $(this).attr('href').split('#tab=')[1];

		$('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
		$('#'+hash+'-tab').addClass("nav-tab-active");
		
		sessionStorage.setItem("seopress_titles_tab", hash);
		
		$('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
		$('#'+hash).addClass("active");
	});

	function sp_get_field_length(e) {
		if (e.val().length > 0) {
			meta = e.val() + ' ';
		} else {
			meta = e.val();
		}
		return meta;
	}

	$('#seopress-tag-site-title').click(function() {
		$("#seopress_titles_home_site_title").val(sp_get_field_length($("#seopress_titles_home_site_title")) + $('#seopress-tag-site-title').attr('data-tag'));
	});
	$('#seopress-tag-site-title-author').click(function() {
		$("#seopress_titles_archive_post_author").val(sp_get_field_length($("#seopress_titles_archive_post_author")) + $('#seopress-tag-site-title-author').attr('data-tag'));
	});
	$('#seopress-tag-site-title-date').click(function() {
		$("#seopress_titles_archives_date_title").val(sp_get_field_length($("#seopress_titles_archives_date_title")) + $('#seopress-tag-site-title-date').attr('data-tag'));
	});
	$('#seopress-tag-site-title-search').click(function() {
		$("#seopress_titles_archives_search_title").val(sp_get_field_length($("#seopress_titles_archives_search_title")) + $('#seopress-tag-site-title-search').attr('data-tag'));
	});
	$('#seopress-tag-site-title-404').click(function() {
		$("#seopress_titles_archives_404_title").val(sp_get_field_length($("#seopress_titles_archives_404_title")) + $('#seopress-tag-site-title-404').attr('data-tag'));
	});
	$('#seopress-tag-site-desc').click(function() {
		$("#seopress_titles_home_site_title").val(sp_get_field_length($("#seopress_titles_home_site_title")) + $('#seopress-tag-site-desc').attr('data-tag'));
	});
	$('#seopress-tag-meta-desc').click(function() {
		$("#seopress_titles_home_site_desc").val(sp_get_field_length($("#seopress_titles_home_site_desc")) + $('#seopress-tag-meta-desc').attr('data-tag'));
	});
	$('#seopress-tag-post-author').click(function() {
		$("#seopress_titles_archive_post_author").val(sp_get_field_length($("#seopress_titles_archive_post_author")) + $('#seopress-tag-post-author').attr('data-tag'));
	});
	$('#seopress-tag-archive-date').click(function() {
		$("#seopress_titles_archives_date_title").val(sp_get_field_length($("#seopress_titles_archives_date_title")) + $('#seopress-tag-archive-date').attr('data-tag'));
	});
	$('#seopress-tag-search-keywords').click(function() {
		$("#seopress_titles_archives_search_title").val(sp_get_field_length($("#seopress_titles_archives_search_title")) + $('#seopress-tag-search-keywords').attr('data-tag'));
	});
	$('#seopress-tag-site-sep').click(function() {
		$("#seopress_titles_home_site_title").val(sp_get_field_length($("#seopress_titles_home_site_title")) + $('#seopress-tag-site-sep').attr('data-tag'));
	});
	$('#seopress-tag-sep-author').click(function() {
		$("#seopress_titles_archive_post_author").val(sp_get_field_length($("#seopress_titles_archive_post_author")) + $('#seopress-tag-sep-author').attr('data-tag'));
	});
	$('#seopress-tag-sep-date').click(function() {
		$("#seopress_titles_archives_date_title").val(sp_get_field_length($("#seopress_titles_archives_date_title")) + $('#seopress-tag-sep-date').attr('data-tag'));
	});
	$('#seopress-tag-sep-search').click(function() {
		$("#seopress_titles_archives_search_title").val(sp_get_field_length($("#seopress_titles_archives_search_title")) + $('#seopress-tag-sep-search').attr('data-tag'));
	});
	$('#seopress-tag-sep-404').click(function() {
		$("#seopress_titles_archives_404_title").val(sp_get_field_length($("#seopress_titles_archives_404_title")) + $('#seopress-tag-sep-404').attr('data-tag'));
	});
	$('.more-tags').click(function() {
		$('#contextual-help-link').click();
	});
});