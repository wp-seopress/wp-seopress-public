jQuery(document).ready(function ($) {
    var hash = $(location).attr("hash").split("#tab=")[1];

    if (typeof hash != "undefined") {
        $("#" + hash + "-tab").addClass("nav-tab-active");
        $("#" + hash).addClass("active");
    } else {
        if (typeof sessionStorage != "undefined") {
            var seopress_tab_session_storage = sessionStorage.getItem(
                "seopress_titles_tab"
            );
            if (seopress_tab_session_storage) {
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
                $("#tab_seopress_titles_home-tab").addClass("nav-tab-active");
                $("#tab_seopress_titles_home").addClass("active");
            }
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

            sessionStorage.setItem("seopress_titles_tab", hash);

            $("#seopress-tabs")
                .find(".seopress-tab.active")
                .removeClass("active");
            $("#" + hash).addClass("active");
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

    //All variables
    $(".seopress-tag-dropdown").each(function (item) {
        const input_title = $(this).parent(".wrap-tags").prev("input");
        const _self = $(this);
        $(this).on("click", function () {
            $(this).next(".sp-wrap-tag-variables-list").toggleClass("open");

            $(this)
                .next(".sp-wrap-tag-variables-list")
                .find("li")
                .on("click", function (e) {
                    if (_self.hasClass("tag-title")) {
                        input_title.val(
                            sp_get_field_length(input_title) +
                                $(this).attr("data-value")
                        );
                        input_title.trigger("paste");
                    }
                    if (_self.hasClass("tag-description")) {
                        $("#seopress_titles_home_site_desc").val(
                            sp_get_field_length(
                                $("#seopress_titles_home_site_desc")
                            ) + $(this).attr("data-value")
                        );
                        $("#seopress_titles_home_site_desc").trigger("paste");
                    }
                    e.stopImmediatePropagation();
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
});
