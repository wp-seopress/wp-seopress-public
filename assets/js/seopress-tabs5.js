jQuery(document).ready(function($) {
	if(typeof sessionStorage!='undefined') {
		var seopress_tab_session_storage = sessionStorage.getItem("tab_seopress_advanced_advanced");
		if (seopress_tab_session_storage) {
			$('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
			$('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
			
	    	$('#'+seopress_tab_session_storage+'-tab').addClass("nav-tab-active");
	    	$('#'+seopress_tab_session_storage).addClass("active");
	    } else {
	    	//Default TAB
	    	$('#tab_seopress_advanced_advanced-tab').addClass("nav-tab-active");
	    	$('#tab_seopress_advanced_advanced').addClass("active");
	    }
	};
    $("#seopress-tabs").find("a.nav-tab").click(function(e){
    	e.preventDefault();
    	var hash = $(this).attr('href').split('#tab=')[1];

    	$('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
    	$('#'+hash+'-tab').addClass("nav-tab-active");
    	

		sessionStorage.setItem("tab_seopress_advanced_advanced", hash);
    	
    	$('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
    	$('#'+hash).addClass("active");
    });
});