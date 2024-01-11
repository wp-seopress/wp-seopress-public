jQuery(document).ready(function ($) {

    var get_hash = window.location.hash;
    var clean_hash = get_hash.split('$');

    if (typeof sessionStorage != 'undefined') {
        var seopress_tab_session_storage = sessionStorage.getItem("seopress_robots_tab");

        if (clean_hash[1] == '1') { //Robots Tab
            $('#tab_seopress_robots-tab').addClass("nav-tab-active");
            $('#tab_seopress_robots').addClass("active");
        } else if (clean_hash[1] == '2') { //htaccess Tab
            $('#tab_seopress_htaccess-tab').addClass("nav-tab-active");
            $('#tab_seopress_htaccess').addClass("active");
        } else if (clean_hash[1] == '3') { //White Label Tab
            $('#tab_seopress_white_label-tab').addClass("nav-tab-active");
            $('#tab_seopress_white_label').addClass("active");
        } else if (seopress_tab_session_storage) {
            $('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
            $('#seopress-tabs').find('.seopress-tab.active').removeClass("active");

            $('#' + seopress_tab_session_storage.split('#tab=') + '-tab').addClass("nav-tab-active");
            $('#' + seopress_tab_session_storage.split('#tab=')).addClass("active");
        } else {
            //Default TAB
            $('#tab_seopress_robots-tab').addClass("nav-tab-active");
            $('#tab_seopress_robots').addClass("active");
        }
    };
    $("#seopress-tabs").find("a.nav-tab").click(function (e) {
        e.preventDefault();
        var hash = $(this).attr('href').split('#tab=')[1];

        $('#seopress-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
        $('#' + hash + '-tab').addClass("nav-tab-active");

        if (clean_hash[1] == 1) {
            sessionStorage.setItem("seopress_robots_tab", 'tab_seopress_robots');
        } else if (clean_hash[1] == 2) {
            sessionStorage.setItem("seopress_robots_tab", 'tab_seopress_htaccess');
        } else if (clean_hash[1] == 3) {
            sessionStorage.setItem("seopress_white_label", 'tab_seopress_white_label');
        } else {
            sessionStorage.setItem("seopress_robots_tab", hash);
        }

        $('#seopress-tabs').find('.seopress-tab.active').removeClass("active");
        $('#' + hash).addClass("active");
    });
    //Robots
    $('#seopress-tag-robots-1, #seopress-tag-robots-2, #seopress-tag-robots-3, #seopress-tag-robots-4, #seopress-tag-robots-5, #seopress-tag-robots-6, #seopress-tag-robots-7, #seopress-tag-robots-8, #seopress-tag-robots-9, #seopress-tag-robots-10, #seopress-tag-robots-11, #seopress-tag-robots-12').click(function () {
        $(".seopress_robots_file").val($(".seopress_robots_file").val() + '\n' + $(this).attr('data-tag'));
    });
});
