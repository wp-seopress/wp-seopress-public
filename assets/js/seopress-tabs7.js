//SEO Tools Tabs
jQuery(document).ready(function(){
	var get_hash = window.location.hash;
	var clean_hash = get_hash.split('$');	

	if(typeof sessionStorage!='undefined') {
		var seopress_admin_tab_session_storage = sessionStorage.getItem("seopress_admin_tab");

		if (clean_hash[1] =='1') { //Notifications Tab
            jQuery('#tab_seopress_notifications-tab').addClass("nav-tab-active");
            jQuery('#tab_seopress_notifications').addClass("active");
        } else if (clean_hash[1] =='2') { //SEO Tools Tab
	    	jQuery('#tab_seopress_seo_tools-tab').addClass("nav-tab-active");
	    	jQuery('#tab_seopress_seo_tools').addClass("active");
        } else if (clean_hash[1] =='3') { //Links Tab
	    	jQuery('#tab_seopress_links-tab').addClass("nav-tab-active");
	    	jQuery('#tab_seopress_links_tools').addClass("active");
        } else if (seopress_admin_tab_session_storage) {
            jQuery('#seopress-admin-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
            jQuery('#seopress-admin-tabs').find('.seopress-tab.active').removeClass("active");    
            jQuery('#'+seopress_admin_tab_session_storage.split('#tab=')+'-tab').addClass("nav-tab-active");
            jQuery('#'+seopress_admin_tab_session_storage.split('#tab=')).addClass("active");
        } else {
            //Default TAB
            jQuery('#tab_seopress_notifications-tab').addClass("nav-tab-active");
            jQuery('#tab_seopress_notifications').addClass("active");
        }
	};
    jQuery("#seopress-admin-tabs").find("a.nav-tab").click(function(e){
    	e.preventDefault();
    	var hash = jQuery(this).attr('href').split('#tab=')[1];

    	jQuery('#seopress-admin-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
    	jQuery('#'+hash+'-tab').addClass("nav-tab-active");
    	
    	if (clean_hash[1]==1) {
            sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_notifications');
        } else if (clean_hash[1]==2) {
    		sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_seo_tools');
    	} else if (clean_hash[1]==3) {
    		sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_links_tools');
    	} else {
    		sessionStorage.setItem("seopress_admin_tab", hash);
    	}    	 
    	
    	jQuery('#seopress-admin-tabs').find('.seopress-tab.active').removeClass("active");
    	jQuery('#'+hash).addClass("active");
    });
});

//Request Alexa Rank
jQuery(document).ready(function(){
	jQuery('#seopress-request-alexa-rank').on('click', function() {
		jQuery.ajax({
			method : 'GET',
			url : seopressAjaxAlexa.seopress_request_alexa_rank,
			data : {
				action: 'seopress_request_alexa_rank',
				_ajax_nonce: seopressAjaxAlexa.seopress_nonce,
			},
			success : function( data ) {
				window.location.reload(true);
			},
		});
	});
});
jQuery(document).ready(function(){
	jQuery('#seopress-request-alexa-rank').on('click', function() {
		jQuery(this).attr("disabled", "disabled");
		jQuery( '#spinner-alexa.spinner' ).css( "visibility", "visible" );
		jQuery( '#spinner-alexa.spinner' ).css( "float", "none" );
	});
});

//Request Reverse Domains
jQuery(document).ready(function(){
	jQuery('#seopress-reverse-submit').on('click', function() {
		jQuery.ajax({
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
});
jQuery(document).ready(function(){
	jQuery('#seopress-reverse-submit').on('click', function() {
		jQuery(this).attr("disabled", "disabled");
		jQuery( '#spinner-reverse.spinner' ).css( "visibility", "visible" );
		jQuery( '#spinner-reverse.spinner' ).css( "float", "none" );
	});
});