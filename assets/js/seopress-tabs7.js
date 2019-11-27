//SEO Tools Tabs
jQuery(document).ready(function($) {
	var get_hash = window.location.hash;
	var clean_hash = get_hash.split('$');	

	if(typeof sessionStorage!='undefined') {
		var seopress_admin_tab_session_storage = sessionStorage.getItem("seopress_admin_tab");

		if (clean_hash[1] =='1') { //Notifications Tab
            $('#tab_seopress_notifications-tab').addClass("nav-tab-active");
            $('#tab_seopress_notifications').addClass("active");
        } else if (clean_hash[1] =='2') { //SEO Tools Tab
	    	$('#tab_seopress_seo_tools-tab').addClass("nav-tab-active");
	    	$('#tab_seopress_seo_tools').addClass("active");
        } else if (clean_hash[1] =='3') { //Links Tab
	    	$('#tab_seopress_links-tab').addClass("nav-tab-active");
	    	$('#tab_seopress_links_tools').addClass("active");
        } else if (seopress_admin_tab_session_storage) {
            $('#seopress-admin-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
            $('#seopress-admin-tabs').find('.seopress-tab.active').removeClass("active");    
            $('#'+seopress_admin_tab_session_storage.split('#tab=')+'-tab').addClass("nav-tab-active");
            $('#'+seopress_admin_tab_session_storage.split('#tab=')).addClass("active");
        } else {
            //Default TAB
            $('#tab_seopress_notifications-tab').addClass("nav-tab-active");
            $('#tab_seopress_notifications').addClass("active");
        }
	};
    $("#seopress-admin-tabs").find("a.nav-tab").click(function(e){
    	e.preventDefault();
    	var hash = $(this).attr('href').split('#tab=')[1];

    	$('#seopress-admin-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
    	$('#'+hash+'-tab').addClass("nav-tab-active");
    	
    	if (clean_hash[1]==1) {
            sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_notifications');
        } else if (clean_hash[1]==2) {
    		sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_seo_tools');
    	} else if (clean_hash[1]==3) {
    		sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_links_tools');
    	} else {
    		sessionStorage.setItem("seopress_admin_tab", hash);
    	}    	 
    	
    	$('#seopress-admin-tabs').find('.seopress-tab.active').removeClass("active");
    	$('#'+hash).addClass("active");
    });
	//Request Reverse Domains
	$('#seopress-reverse-submit').on('click', function() {
		$.ajax({
			method : 'GET',
			url : seopressAjaxReverse.seopress_request_reverse,
			data : {
				action: 'seopress_request_reverse',
				_ajax_nonce: seopressAjaxReverse.seopress_nonce,
			},
			success : function( data ) {
				window.location.reload(true);
			},
		});
	});
	$('#seopress-reverse-submit').on('click', function() {
		$(this).attr("disabled", "disabled");
		$( '#spinner-reverse.spinner' ).css( "visibility", "visible" );
		$( '#spinner-reverse.spinner' ).css( "float", "none" );
	});
});