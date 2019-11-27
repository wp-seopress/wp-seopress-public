jQuery(document).ready(function($) {
	if(typeof sessionStorage!='undefined') {
		var seopress_tab_session_storage = sessionStorage.getItem("seopress_social_tab");
		if (seopress_tab_session_storage) {
			$('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
			$('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
			
	    	$('#'+seopress_tab_session_storage+'-tab').addClass("nav-tab-active");
	    	$('#'+seopress_tab_session_storage).addClass("active");
	    } else {
	    	//Default TAB
	    	$('#tab_seopress_social_knowledge-tab').addClass("nav-tab-active");
	    	$('#tab_seopress_social_knowledge').addClass("active");
	    }
	};
    $("#seopress-tabs").find("a.nav-tab").click(function(e){
    	e.preventDefault();
    	var hash = $(this).attr('href').split('#tab=')[1];

    	$('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
    	$('#'+hash+'-tab').addClass("nav-tab-active");
    	

		sessionStorage.setItem("seopress_social_tab", hash);
    	
    	$('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
    	$('#'+hash).addClass("active");
    });
});