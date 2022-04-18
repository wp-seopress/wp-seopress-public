jQuery(document).ready(function ($) {
    const features = [
        ["seopress_titles", "seopress_titles_home"],
        ["seopress_xml_sitemap_tab", "seopress_xml_sitemap_general"],
        ["seopress_social_tab", "seopress_social_knowledge"],
        ["seopress_advanced_tab", "seopress_advanced_image"],
        ["seopress_google_analytics_enable", "seopress_google_analytics_enable"],
        ["seopress_tool_settings", "seopress_tool_settings"],
        ["seopress_instant_indexing_general", "seopress_instant_indexing_general"],
    ];

    features.forEach(function (item) {
        var hash = $(location).attr("hash").split("#tab=")[1];

        if (typeof hash != "undefined") {
            $("#" + hash + "-tab").addClass("nav-tab-active");
            $("#" + hash).addClass("active");
        } else {
            if (
                typeof sessionStorage != "undefined" &&
                typeof sessionStorage != "null"
            ) {
                var seopress_tab_session_storage =
                    sessionStorage.getItem("seopress_save_tab");

                if (
                    seopress_tab_session_storage &&
                    $("#" + seopress_tab_session_storage + "-tab").length
                ) {
                    $("#seopress-tabs")
                        .find(".nav-tab.nav-tab-active")
                        .removeClass("nav-tab-active");
                    $("#seopress-tabs")
                        .find(".seopress-tab.active")
                        .removeClass("active");

                    $("#" + seopress_tab_session_storage + "-tab").addClass(
                        "nav-tab-active"
                    );
                    $("#" + seopress_tab_session_storage).addClass("active");
                } else {
                    //Default TAB
                    $("#tab_" + item[1] + "-tab").addClass("nav-tab-active");
                    $("#tab_" + item[1]).addClass("active");
                }
            }

            $("#seopress-tabs")
                .find("a.nav-tab")
                .click(function (e) {
                    e.preventDefault();
                    var hash = $(this).attr("href").split("#tab=")[1];

                    $("#seopress-tabs")
                        .find(".nav-tab.nav-tab-active")
                        .removeClass("nav-tab-active");
                    $("#" + hash + "-tab").addClass("nav-tab-active");

                    sessionStorage.setItem("seopress_save_tab", hash);

                    $("#seopress-tabs")
                        .find(".seopress-tab.active")
                        .removeClass("active");
                    $("#" + hash).addClass("active");
                });
        }
    });

    function sp_get_field_length(e) {
        if (e.val().length > 0) {
            meta = e.val() + " ";
        } else {
            meta = e.val();
        }
        return meta;
    }

    let alreadyBind = false;

    // Home Binding
    $("#seopress-tag-site-title").click(function () {
        $("#seopress_titles_home_site_title").val(
            sp_get_field_length($("#seopress_titles_home_site_title")) +
            $("#seopress-tag-site-title").attr("data-tag")
        );
    });

    $("#seopress-tag-site-desc").click(function () {
        $("#seopress_titles_home_site_title").val(
            sp_get_field_length($("#seopress_titles_home_site_title")) +
            $("#seopress-tag-site-desc").attr("data-tag")
        );
    });
    $("#seopress-tag-site-sep").click(function () {
        $("#seopress_titles_home_site_title").val(
            sp_get_field_length($("#seopress_titles_home_site_title")) +
            $("#seopress-tag-site-sep").attr("data-tag")
        );
    });

    $("#seopress-tag-meta-desc").click(function () {
        $("#seopress_titles_home_site_desc").val(
            sp_get_field_length($("#seopress_titles_home_site_desc")) +
            $("#seopress-tag-meta-desc").attr("data-tag")
        );
    });

    //Author
    $("#seopress-tag-post-author").click(function () {
        $("#seopress_titles_archive_post_author").val(
            sp_get_field_length($("#seopress_titles_archive_post_author")) +
            $("#seopress-tag-post-author").attr("data-tag")
        );
    });
    $("#seopress-tag-sep-author").click(function () {
        $("#seopress_titles_archive_post_author").val(
            sp_get_field_length($("#seopress_titles_archive_post_author")) +
            $("#seopress-tag-sep-author").attr("data-tag")
        );
    });
    $("#seopress-tag-site-title-author").click(function () {
        $("#seopress_titles_archive_post_author").val(
            sp_get_field_length($("#seopress_titles_archive_post_author")) +
            $("#seopress-tag-site-title-author").attr("data-tag")
        );
    });

    //Date
    $("#seopress-tag-archive-date").click(function () {
        $("#seopress_titles_archives_date_title").val(
            sp_get_field_length($("#seopress_titles_archives_date_title")) +
            $("#seopress-tag-archive-date").attr("data-tag")
        );
    });
    $("#seopress-tag-sep-date").click(function () {
        $("#seopress_titles_archives_date_title").val(
            sp_get_field_length($("#seopress_titles_archives_date_title")) +
            $("#seopress-tag-sep-date").attr("data-tag")
        );
    });
    $("#seopress-tag-site-title-date").click(function () {
        $("#seopress_titles_archives_date_title").val(
            sp_get_field_length($("#seopress_titles_archives_date_title")) +
            $("#seopress-tag-site-title-date").attr("data-tag")
        );
    });

    //Search
    $("#seopress-tag-search-keywords").click(function () {
        $("#seopress_titles_archives_search_title").val(
            sp_get_field_length($("#seopress_titles_archives_search_title")) +
            $("#seopress-tag-search-keywords").attr("data-tag")
        );
    });
    $("#seopress-tag-sep-search").click(function () {
        $("#seopress_titles_archives_search_title").val(
            sp_get_field_length($("#seopress_titles_archives_search_title")) +
            $("#seopress-tag-sep-search").attr("data-tag")
        );
    });
    $("#seopress-tag-site-title-search").click(function () {
        $("#seopress_titles_archives_search_title").val(
            sp_get_field_length($("#seopress_titles_archives_search_title")) +
            $("#seopress-tag-site-title-search").attr("data-tag")
        );
    });

    //404
    $("#seopress-tag-site-title-404").click(function () {
        $("#seopress_titles_archives_404_title").val(
            sp_get_field_length($("#seopress_titles_archives_404_title")) +
            $("#seopress-tag-site-title-404").attr("data-tag")
        );
    });
    $("#seopress-tag-sep-404").click(function () {
        $("#seopress_titles_archives_404_title").val(
            sp_get_field_length($("#seopress_titles_archives_404_title")) +
            $("#seopress-tag-sep-404").attr("data-tag")
        );
    });

    //BuddyPress
    $("#seopress-tag-post-title-bd-groups").click(function () {
        $("#seopress_titles_bp_groups_title").val(
            sp_get_field_length($("#seopress_titles_bp_groups_title")) +
            $("#seopress-tag-post-title-bd-groups").attr("data-tag")
        );
    });
    $("#seopress-tag-sep-bd-groups").click(function () {
        $("#seopress_titles_bp_groups_title").val(
            sp_get_field_length($("#seopress_titles_bp_groups_title")) +
            $("#seopress-tag-sep-bd-groups").attr("data-tag")
        );
    });
    $("#seopress-tag-site-title-bd-groups").click(function () {
        $("#seopress_titles_bp_groups_title").val(
            sp_get_field_length($("#seopress_titles_bp_groups_title")) +
            $("#seopress-tag-site-title-bd-groups").attr("data-tag")
        );
    });

    //All variables
    $(".seopress-tag-dropdown").each(function (item) {
        const input_title = $(this).parent(".wrap-tags").prev("input");
        const input_desc = $(this).parent(".wrap-tags").prev("textarea");

        const _self = $(this);

        function handleClickLi(current) {
            if (_self.hasClass("tag-title")) {
                input_title.val(
                    sp_get_field_length(input_title) +
                    $(current).attr("data-value")
                );
                input_title.trigger("paste");
            }
            if (_self.hasClass("tag-description")) {
                input_desc.val(
                    sp_get_field_length(input_desc) +
                    $(current).attr("data-value")
                );
                input_desc.trigger("paste");
            }
        }

        $(this).on("click", function () {
            $(this).next(".sp-wrap-tag-variables-list").toggleClass("open");

            $(this)
                .next(".sp-wrap-tag-variables-list")
                .find("li")
                .on("click", function (e) {
                    handleClickLi(this);
                    e.stopImmediatePropagation();
                })
                .on("keyup", function (e) {
                    if (e.keyCode === 13) {
                        handleClickLi(this);
                        e.stopImmediatePropagation();
                    }
                });

            function closeItem(e) {
                if (
                    $(e.target).hasClass("dashicons") ||
                    $(e.target).hasClass("seopress-tag-single-all")
                ) {
                    return;
                }

                alreadyBind = false;
                $(document).off("click", closeItem);
                $(".sp-wrap-tag-variables-list").removeClass("open");
            }

            if (!alreadyBind) {
                alreadyBind = true;
                $(document).on("click", closeItem);
            }
        });
    });

    //Instant Indexing: Display keywords counter
    if ($("#seopress_instant_indexing_manual_batch").length) {
        newLines = $('#seopress_instant_indexing_manual_batch').val().split("\n").length;
        $('#seopress_instant_indexing_url_count').text(newLines);
        var lines = 50;
        var linesUsed = $('#seopress_instant_indexing_url_count');

        if (newLines) {
            var progress = Math.round(newLines / 50 * 100);

            if (progress >= 100) {
                progress = 100;
            }

            $('#seopress_instant_indexing_url_progress').attr('aria-valuenow', progress),
                $('#seopress_instant_indexing_url_progress').text(progress + '%'),
                $('#seopress_instant_indexing_url_progress').css('width', progress + '%')
        }

        $("#seopress_instant_indexing_manual_batch").on('keyup paste change click focus mouseout', function (e) {


            newLines = $(this).val().split("\n").length;
            linesUsed.text(newLines);

            if (newLines > lines) {
                linesUsed.css('color', 'red');
            } else {
                linesUsed.css('color', '');
            }

            if (newLines) {
                var progress = Math.round(newLines / 50 * 100);
            }

            if (progress >= 100) {
                progress = 100;
            }
            $('#seopress_instant_indexing_url_progress').attr('aria-valuenow', progress),
                $('#seopress_instant_indexing_url_progress').text(progress + '%'),
                $('#seopress_instant_indexing_url_progress').css('width', progress + '%')
        });
    }


    $('#seopress_instant_indexing_google_action_include[URL_UPDATED]').is(':checked') ? true : false,


        //Instant Indexing: Batch URLs
        $('.seopress-instant-indexing-batch').on('click', function () {
            $('#seopress-tabs .spinner').css(
                "visibility",
                "visible"
            );
            $('#seopress-tabs .spinner').css(
                "float",
                "none"
            );

            $.ajax({
                method: 'POST',
                url: seopressAjaxInstantIndexingPost.seopress_instant_indexing_post,
                data: {
                    action: 'seopress_instant_indexing_post',
                    urls_to_submit: $('#seopress_instant_indexing_manual_batch').val(),
                    indexnow_api: $('#seopress_instant_indexing_bing_api_key').val(),
                    google_api: $('#seopress_instant_indexing_google_api_key').val(),
                    update_action: $('#seopress_instant_indexing_google_action_include_URL_UPDATED').is(':checked') ? 'URL_UPDATED' : false,
                    delete_action: $('#seopress_instant_indexing_google_action_include_URL_DELETED').is(':checked') ? 'URL_DELETED' : false,
                    google: $('#seopress_instant_indexing_engines_google').is(':checked') ? true : false,
                    bing: $('#seopress_instant_indexing_engines_bing').is(':checked') ? true : false,
                    automatic_submission: $('#seopress_instant_indexing_automate_submission').is(':checked') ? true : false,
                    _ajax_nonce: seopressAjaxInstantIndexingPost.seopress_nonce,
                },
                success: function (data) {
                    window.location.reload(true);
                },
            });
        });

    //Instant Indexing: refresh API Key
    $('.seopress-instant-indexing-refresh-api-key').on('click', function () {
        $.ajax({
            method: 'POST',
            url: seopressAjaxInstantIndexingApiKey.seopress_instant_indexing_generate_api_key,
            data: {
                action: 'seopress_instant_indexing_generate_api_key',
                _ajax_nonce: seopressAjaxInstantIndexingApiKey.seopress_nonce,
            },
            success: function (data) {
                window.location.reload(true);
            },
        });
    });
});
