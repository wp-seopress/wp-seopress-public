jQuery(document).ready(function($) {

	var get_hash = window.location.hash;
	var clean_hash = get_hash.split('$');	

	if(typeof sessionStorage!='undefined') {
		var seopress_tab_session_storage = sessionStorage.getItem("seopress_robots_tab");

		if (clean_hash[1] =='1') { //Robots Tab
	    	jQuery('#tab_seopress_robots-tab').addClass("nav-tab-active");
	    	jQuery('#tab_seopress_robots').addClass("active");
	    } else if (clean_hash[1] =='2') { //htaccess Tab
            jQuery('#tab_seopress_htaccess-tab').addClass("nav-tab-active");
            jQuery('#tab_seopress_htaccess').addClass("active");
        } else if (clean_hash[1] =='3') { //White Label Tab
            jQuery('#tab_seopress_white_label-tab').addClass("nav-tab-active");
            jQuery('#tab_seopress_white_label').addClass("active");
        } else if (seopress_tab_session_storage) {
			jQuery('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
			jQuery('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
	    	
	    	jQuery('#'+seopress_tab_session_storage.split('#tab=')+'-tab').addClass("nav-tab-active");
	    	jQuery('#'+seopress_tab_session_storage.split('#tab=')).addClass("active");
	    } else {
	    	//Default TAB
	    	jQuery('#tab_seopress_robots-tab').addClass("nav-tab-active");
	    	jQuery('#tab_seopress_robots').addClass("active");
	    }
	};
    jQuery("#seopress-tabs").find("a.nav-tab").click(function(e){
    	e.preventDefault();
    	var hash = jQuery(this).attr('href').split('#tab=')[1];

    	jQuery('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
    	jQuery('#'+hash+'-tab').addClass("nav-tab-active");
    	
    	if (clean_hash[1]==1) {
    		sessionStorage.setItem("seopress_robots_tab", 'tab_seopress_robots');
    	} else if (clean_hash[1]==2) {
            sessionStorage.setItem("seopress_robots_tab", 'tab_seopress_htaccess');
        } else if (clean_hash[1]==3) {
    		sessionStorage.setItem("seopress_white_label", 'tab_seopress_white_label');
    	} else {
    		sessionStorage.setItem("seopress_robots_tab", hash);
    	}    	 
    	
    	jQuery('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
    	jQuery('#'+hash).addClass("active");
    });
    //Robots
    jQuery('#seopress-tag-robots-1, #seopress-tag-robots-2, #seopress-tag-robots-3, #seopress-tag-robots-4, #seopress-tag-robots-5, #seopress-tag-robots-6, #seopress-tag-robots-7').click(function() {
        jQuery(".seopress_robots_file").val(jQuery(".seopress_robots_file").val() +'\n'+ jQuery(this).attr('data-tag'));
    });
    //Flush permalinks
    jQuery('#seopress-flush-permalinks2').on('click', function() {
        jQuery.ajax({
            method : 'GET',
            url : seopressAjaxResetPermalinks.seopress_flush_permalinks,
            data : {
                action: 'seopress_flush_permalinks',
                _ajax_nonce: seopressAjaxResetPermalinks.seopress_nonce,
            },            
            success : function( data ) {
                window.location.reload(true);
            },
        });
    });
    jQuery('#seopress-flush-permalinks2').on('click', function() {
        jQuery(this).attr("disabled", "disabled");
        jQuery( '.spinner' ).css( "visibility", "visible" );
        jQuery( '.spinner' ).css( "float", "none" );
    });
});