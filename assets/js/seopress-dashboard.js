jQuery(document).ready(function ($) {
    //If no notices
    if (!$.trim($("#seopress-notifications-center").html())) {
        $('#seopress-notifications-center').remove();
    }
    const notices = [
        "notice-get-started",
        "notice-usm",
        "notice-wizard",
        "notice-insights-wizard",
        "notice-tagdiv",
        "notice-divide-comments",
        "notice-review",
        "notice-trailingslash",
        "notice-posts-number",
        "notice-rss-use-excerpt",
        "notice-ga-ids",
        "notice-search-console",
        "notice-google-business",
        "notice-ssl",
        "notice-title-tag",
        "notice-enfold",
        "notice-themes",
        "notice-page-builders",
        "notice-go-pro",
        "notice-noindex",
        "notice-tasks",
        "notice-insights",
        "notice-robots-txt",
        "notice-robots-txt-valid",
    ]
    notices.forEach(function (item) {
        $('#' + item).on('click', function () {
            $('#' + item).attr('data-notice', $('#' + item).attr('data-notice') == '1' ? '0' : '1');
            $.ajax({
                method: 'POST',
                url: seopressAjaxHideNotices.seopress_hide_notices,
                data: {
                    action: 'seopress_hide_notices',
                    notice: item,
                    notice_value: $('#' + item).attr('data-notice'),
                    _ajax_nonce: seopressAjaxHideNotices.seopress_nonce,
                },
                success: function (data) {
                    $('#seopress-notice-save').css('display', 'block');
                    $('#seopress-notice-save .html').html('Notice successfully removed');
                    $('#' + item + '-alert').fadeOut();
                    $('#seopress-notice-save').delay(3500).fadeOut();
                },
            });
        });
    });

    const features = [
        "titles",
        "xml-sitemap",
        "social",
        "google-analytics",
        "instant-indexing",
        "advanced",
        "local-business",
        "woocommerce",
        "edd",
        "dublin-core",
        "rich-snippets",
        "breadcrumbs",
        "robots",
        "news",
        "404",
        "bot",
        "rewrite",
        "white-label"
    ]
    features.forEach(function (item) {
        $('#toggle-' + item).on('click', function () {
            $('#toggle-' + item).attr('data-toggle', $('#toggle-' + item).attr('data-toggle') == '1' ? '0' : '1');
            $.ajax({
                method: 'POST',
                url: seopressAjaxToggleFeatures.seopress_toggle_features,
                data: {
                    action: 'seopress_toggle_features',
                    feature: 'toggle-' + item,
                    feature_value: $('#toggle-' + item).attr('data-toggle'),
                    _ajax_nonce: seopressAjaxToggleFeatures.seopress_nonce,
                },
                success: function (data) {
                    window.history.pushState("", "", window.location.href + "&settings-updated=true");
                    $('#seopress-notice-save').show();
                    $('#seopress-notice-save').delay(3500).fadeOut();
                    window.history.pushState("", "", window.location.href)
                },
            });
        });
    });
    $('#seopress-activity-panel button').on('click', function () {
        $(this).toggleClass('is-active');
        $('#seopress-activity-panel-' + $(this).data('panel')).toggleClass('is-open');
    });
    $('#seopress-content').on('click', function () {
        $('#seopress-activity-panel').find('.is-open').toggleClass('is-open');
        $('#seopress-activity-panel').find('.is-active').toggleClass('is-active');
    });
    $('.seopress-item-toggle-options').on('click', function () {
        $(this).next('.seopress-card-popover').toggleClass('is-open');
    });

    $('#seopress-news-items').on('click', function () {
        $.ajax({
            method: 'POST',
            url: seopressAjaxNews.seopress_news,
            data: {
                action: 'seopress_news',
                news_max_items: $('#news_max_items').val(),
                _ajax_nonce: seopressAjaxNews.seopress_nonce,
            },
            success: function (data) {
                $('#seopress-news-panel .seopress-card-content').load(' #seopress-news-panel .seopress-card-content');
                $('#seopress-news-panel .seopress-card-popover').toggleClass('is-open');
            },
        });
    });
    $('#seopress_news').on('click', function () {
        $('#seopress-news-panel').toggleClass('is-active');
        $('#seopress_news').attr('data-toggle', $('#seopress_news').attr('data-toggle') == '1' ? '0' : '1');
        $.ajax({
            method: 'POST',
            url: seopressAjaxDisplay.seopress_display,
            data: {
                action: 'seopress_display',
                news_center: $('#seopress_news').attr('data-toggle'),
                _ajax_nonce: seopressAjaxDisplay.seopress_nonce,
            },
        });
    });
    $('#seopress_tools').on('click', function () {
        $('#notice-insights-alert').toggleClass('is-active');
        $('#seopress_tools').attr('data-toggle', $('#seopress_tools').attr('data-toggle') == '1' ? '0' : '1');
        $.ajax({
            method: 'POST',
            url: seopressAjaxDisplay.seopress_display,
            data: {
                action: 'seopress_display',
                tools_center: $('#seopress_tools').attr('data-toggle'),
                _ajax_nonce: seopressAjaxDisplay.seopress_nonce,
            },
        });
    });
    $('#notifications_center').on('click', function () {
        $('#seopress-notifications-center').toggleClass('is-active');
        $('#notifications_center').attr('data-toggle', $('#notifications_center').attr('data-toggle') == '1' ? '0' : '1');
        $.ajax({
            method: 'POST',
            url: seopressAjaxDisplay.seopress_display,
            data: {
                action: 'seopress_display',
                notifications_center: $('#notifications_center').attr('data-toggle'),
                _ajax_nonce: seopressAjaxDisplay.seopress_nonce,
            },
        });
    });
});

//SEO Tools Tabs
jQuery(document).ready(function ($) {
    var get_hash = window.location.hash;
    var clean_hash = get_hash.split('$');

    if (typeof sessionStorage != 'undefined') {
        var seopress_admin_tab_session_storage = sessionStorage.getItem("seopress_admin_tab");

        if (clean_hash[1] == '1') { //Notifications Tab
            $('#tab_seopress_analytics-tab').addClass("nav-tab-active");
            $('#tab_seopress_analytics').addClass("active");
        } else if (clean_hash[1] == '2') { //SEO Tools Tab
            $('#tab_seopress_seo_tools-tab').addClass("nav-tab-active");
            $('#tab_seopress_seo_tools').addClass("active");
        } else if (clean_hash[1] == '3') { //Page Speed Tab
            $('#tab_seopress_ps-tab').addClass("nav-tab-active");
            $('#tab_seopress_ps_tools').addClass("active");
        } else if (seopress_admin_tab_session_storage) {
            $('#seopress-admin-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
            $('#seopress-admin-tabs').find('.seopress-tab.active').removeClass("active");
            $('#' + seopress_admin_tab_session_storage.split('#tab=') + '-tab').addClass("nav-tab-active");
            $('#' + seopress_admin_tab_session_storage.split('#tab=')).addClass("active");
        } else {
            //Default TAB
            $('#tab_seopress_analytics-tab').addClass("nav-tab-active");
            $('#tab_seopress_analytics').addClass("active");
        }
    };
    $("#seopress-admin-tabs").find("a.nav-tab").click(function (e) {
        e.preventDefault();
        var hash = $(this).attr('href').split('#tab=')[1];

        $('#seopress-admin-tabs').find('.nav-tab.nav-tab-active').removeClass("nav-tab-active");
        $('#' + hash + '-tab').addClass("nav-tab-active");

        if (clean_hash[1] == 1) {
            sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_analytics');
        } else if (clean_hash[1] == 2) {
            sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_seo_tools');
        } else if (clean_hash[1] == 3) {
            sessionStorage.setItem("seopress_admin_tab", 'tab_seopress_ps_tools');
        } else {
            sessionStorage.setItem("seopress_admin_tab", hash);
        }

        $('#seopress-admin-tabs').find('.seopress-tab.active').removeClass("active");
        $('#' + hash).addClass("active");
    });
    //Request Reverse Domains
    $('#seopress-reverse-submit').on('click', function () {
        $.ajax({
            method: 'GET',
            url: seopressAjaxReverse.seopress_request_reverse,
            data: {
                action: 'seopress_request_reverse',
                _ajax_nonce: seopressAjaxReverse.seopress_nonce,
            },
            success: function (data) {
                window.location.reload(true);
            },
        });
    });
    $('#seopress-reverse-submit').on('click', function () {
        $(this).attr("disabled", "disabled");
        $('#spinner-reverse.spinner').css("visibility", "visible");
        $('#spinner-reverse.spinner').css("float", "none");
    });

    //Drag and drop for cards
    $(".seopress-dashboard-columns .seopress-dashboard-column:last-child").sortable({
        items: ".seopress-card",
        placeholder: "sp-dashboard-card-highlight",
        cancel: ".seopress-intro, .seopress-card-popover",
        handle: ".seopress-card-title",
        opacity: 0.9,
        forcePlaceholderSize: true,
        update: function (e) {
            const item = jQuery(e.target);

            var postData = item.sortable("toArray", {
                attribute: "id",
            });

            $.ajax({
                method: "POST",
                url: seopressAjaxDndFeatures.seopress_dnd_features,
                data: {
                    action: "seopress_dnd_features",
                    order: postData,
                    _ajax_nonce: seopressAjaxDndFeatures.seopress_nonce,
                },
            });
        },
    });
});
