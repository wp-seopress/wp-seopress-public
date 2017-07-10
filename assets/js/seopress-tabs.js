jQuery(document).ready(function($) {
	if(typeof sessionStorage!='undefined') {
		var seopress_tab_session_storage = sessionStorage.getItem("seopress_titles_tab");
		if (seopress_tab_session_storage) {
			jQuery('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
			jQuery('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
			
	    	jQuery('#'+seopress_tab_session_storage+'-tab').addClass("nav-tab-active");
	    	jQuery('#'+seopress_tab_session_storage).addClass("active");
	    } else {
	    	//Default TAB
	    	jQuery('#tab_seopress_titles_home-tab').addClass("nav-tab-active");
	    	jQuery('#tab_seopress_titles_home').addClass("active");
	    }
	};
    jQuery("#seopress-tabs").find("a.nav-tab").click(function(e){
    	e.preventDefault();
    	var hash = jQuery(this).attr('href').split('#tab=')[1];

    	jQuery('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
    	jQuery('#'+hash+'-tab').addClass("nav-tab-active");
    	
		sessionStorage.setItem("seopress_titles_tab", hash);
    	
    	jQuery('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
    	jQuery('#'+hash).addClass("active");
    });
});

jQuery(document).ready(function($) {
    jQuery('#seopress-tag-site-title').click(function() {
        jQuery("#seopress_titles_home_site_title").val(jQuery("#seopress_titles_home_site_title").val() + ' ' + jQuery('#seopress-tag-site-title').attr('data-tag'));
    });
    jQuery('#seopress-tag-site-title-author').click(function() {
        jQuery("#seopress_titles_archive_post_author").val(jQuery("#seopress_titles_archive_post_author").val() + ' ' + jQuery('#seopress-tag-site-title-author').attr('data-tag'));
    });
    jQuery('#seopress-tag-site-title-date').click(function() {
        jQuery("#seopress_titles_archives_date_title").val(jQuery("#seopress_titles_archives_date_title").val() + ' ' + jQuery('#seopress-tag-site-title-date').attr('data-tag'));
    });
    jQuery('#seopress-tag-site-title-search').click(function() {
        jQuery("#seopress_titles_archives_search_title").val(jQuery("#seopress_titles_archives_search_title").val() + ' ' + jQuery('#seopress-tag-site-title-search').attr('data-tag'));
    });
    jQuery('#seopress-tag-site-title-404').click(function() {
        jQuery("#seopress_titles_archives_404_title").val(jQuery("#seopress_titles_archives_404_title").val() + ' ' + jQuery('#seopress-tag-site-title-404').attr('data-tag'));
    });
    jQuery('#seopress-tag-site-desc').click(function() {
        jQuery("#seopress_titles_home_site_title").val(jQuery("#seopress_titles_home_site_title").val() + ' ' + jQuery('#seopress-tag-site-desc').attr('data-tag'));
    });
    jQuery('#seopress-tag-meta-desc').click(function() {
        jQuery("#seopress_titles_home_site_desc").val(jQuery("#seopress_titles_home_site_desc").val() + ' ' + jQuery('#seopress-tag-meta-desc').attr('data-tag'));
    });    
    jQuery('#seopress-tag-post-author').click(function() {
        jQuery("#seopress_titles_archive_post_author").val(jQuery("#seopress_titles_archive_post_author").val() + ' ' + jQuery('#seopress-tag-post-author').attr('data-tag'));
    });     
    jQuery('#seopress-tag-archive-date').click(function() {
        jQuery("#seopress_titles_archives_date_title").val(jQuery("#seopress_titles_archives_date_title").val() + ' ' + jQuery('#seopress-tag-archive-date').attr('data-tag'));
    });    
    jQuery('#seopress-tag-search-keywords').click(function() {
        jQuery("#seopress_titles_archives_search_title").val(jQuery("#seopress_titles_archives_search_title").val() + ' ' + jQuery('#seopress-tag-search-keywords').attr('data-tag'));
    });
    jQuery('.more-tags').click(function() {
        jQuery('#contextual-help-link').click();
    });
});