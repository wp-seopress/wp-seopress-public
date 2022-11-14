jQuery(document).ready(function ($) {
    //Regenerate Video XML sitemap
    $("#seopress-video-regenerate").click(function () {
        url = seopressAjaxVdeoRegenerate.seopress_video_regenerate;
        action = 'seopress_video_xml_sitemap_regenerate';
        _ajax_nonce = seopressAjaxVdeoRegenerate.seopress_nonce;

        self.process_offset2(0, self, url, action, _ajax_nonce);
    });

    process_offset2 = function (
        offset,
        self,
        url,
        action,
        _ajax_nonce
    ) {
        i18n = seopressAjaxMigrate.i18n.video;
        $.ajax({
            method: 'POST',
            url: url,
            data: {
                action: action,
                offset: offset,
                _ajax_nonce: _ajax_nonce,
            },
            success: function (data) {
                if ("done" == data.data.offset) {
                    $("#seopress-video-regenerate").removeAttr(
                        "disabled"
                    );
                    $(".spinner").css("visibility", "hidden");
                    $("#tab_seopress_tool_video .log").css("display", "block");
                    $("#tab_seopress_tool_video .log").html("<div class='seopress-notice is-success'><p>" + i18n + "</p></div>");

                    if (data.data.url != "") {
                        $(location).attr("href", data.data.url);
                    }
                } else {
                    self.process_offset2(
                        parseInt(data.data.offset),
                        self,
                        url,
                        action,
                        _ajax_nonce
                    );
                    if (data.data.total) {
                        progress = (data.data.count / data.data.total * 100).toFixed(2);
                        $("#tab_seopress_tool_video .log").css("display", "block");
                        $("#tab_seopress_tool_video .log").html("<div class='seopress-notice'><p>" + progress + "%</p></div>");
                    }
                }
            },
        });
    };
    $("#seopress-video-regenerate").on("click", function () {
        $(this).attr("disabled", "disabled");
        $("#tab_seopress_tool_video .spinner").css(
            "visibility",
            "visible"
        );
        $("#tab_seopress_tool_video .spinner").css("float", "none");
        $("#tab_seopress_tool_video .log").html("");
    });

    //Select toggle
    $("#select-wizard-redirects, #select-wizard-import")
        .change(function (e) {
            e.preventDefault();

            var select = $(this).val();
            if (select == "none") {
                $(
                    "#select-wizard-redirects option, #select-wizard-import option"
                ).each(function () {
                    var ids_to_hide = $(this).val();
                    $("#" + ids_to_hide).hide();
                });
            } else {
                $(
                    "#select-wizard-redirects option:selected, #select-wizard-import option:selected"
                ).each(function () {
                    var ids_to_show = $(this).val();
                    $("#" + ids_to_show).show();
                });
                $(
                    "#select-wizard-redirects option:not(:selected), #select-wizard-import option:not(:selected)"
                ).each(function () {
                    var ids_to_hide = $(this).val();
                    $("#" + ids_to_hide).hide();
                });
            }
        })
        .trigger("change");

    //Import from SEO plugins
    const seo_plugins = [
        "yoast",
        "aio",
        "seo-framework",
        "rk",
        "squirrly",
        "seo-ultimate",
        "wp-meta-seo",
        "premium-seo-pack",
        "wpseo",
        "platinum-seo",
        "smart-crawl",
        "seopressor",
        "slim-seo",
        "metadata",
    ];
    seo_plugins.forEach(function (item) {
        $("#seopress-" + item + "-migrate").on("click", function (e) {
            e.preventDefault();
            id = item;
            switch (e.target.id) {
                case "seopress-yoast-migrate":
                    url =
                        seopressAjaxMigrate.seopress_yoast_migrate
                            .seopress_yoast_migration;
                    action = "seopress_yoast_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_yoast_migrate
                            .seopress_nonce;
                    break;
                case "seopress-aio-migrate":
                    url =
                        seopressAjaxMigrate.seopress_aio_migrate
                            .seopress_aio_migration;
                    action = "seopress_aio_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_aio_migrate.seopress_nonce;
                    break;
                case "seopress-seo-framework-migrate":
                    url =
                        seopressAjaxMigrate.seopress_seo_framework_migrate
                            .seopress_seo_framework_migration;
                    action = "seopress_seo_framework_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_seo_framework_migrate
                            .seopress_nonce;
                    break;
                case "seopress-rk-migrate":
                    url =
                        seopressAjaxMigrate.seopress_rk_migrate
                            .seopress_rk_migration;
                    action = "seopress_rk_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_rk_migrate.seopress_nonce;
                    break;
                case "seopress-squirrly-migrate":
                    url =
                        seopressAjaxMigrate.seopress_squirrly_migrate
                            .seopress_squirrly_migration;
                    action = "seopress_squirrly_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_squirrly_migrate
                            .seopress_nonce;
                    break;
                case "seopress-seo-ultimate-migrate":
                    url =
                        seopressAjaxMigrate.seopress_seo_ultimate_migrate
                            .seopress_seo_ultimate_migration;
                    action = "seopress_seo_ultimate_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_seo_ultimate_migrate
                            .seopress_nonce;
                    break;
                case "seopress-wp-meta-seo-migrate":
                    url =
                        seopressAjaxMigrate.seopress_wp_meta_seo_migrate
                            .seopress_wp_meta_seo_migration;
                    action = "seopress_wp_meta_seo_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_wp_meta_seo_migrate
                            .seopress_nonce;
                    break;
                case "seopress-premium-seo-pack-migrate":
                    url =
                        seopressAjaxMigrate.seopress_premium_seo_pack_migrate
                            .seopress_premium_seo_pack_migration;
                    action = "seopress_premium_seo_pack_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_premium_seo_pack_migrate
                            .seopress_nonce;
                    break;
                case "seopress-wpseo-migrate":
                    url =
                        seopressAjaxMigrate.seopress_wpseo_migrate
                            .seopress_wpseo_migration;
                    action = "seopress_wpseo_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_wpseo_migrate
                            .seopress_nonce;
                    break;
                case "seopress-platinum-seo-migrate":
                    url =
                        seopressAjaxMigrate.seopress_platinum_seo_migrate
                            .seopress_platinum_seo_migration;
                    action = "seopress_platinum_seo_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_platinum_seo_migrate
                            .seopress_nonce;
                    break;
                case "seopress-smart-crawl-migrate":
                    url =
                        seopressAjaxMigrate.seopress_smart_crawl_migrate
                            .seopress_smart_crawl_migration;
                    action = "seopress_smart_crawl_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_smart_crawl_migrate
                            .seopress_nonce;
                    break;
                case "seopress-seopressor-migrate":
                    url =
                        seopressAjaxMigrate.seopress_seopressor_migrate
                            .seopress_seopressor_migration;
                    action = "seopress_seopressor_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_seopressor_migrate
                            .seopress_nonce;
                    break;
                case "seopress-slim-seo-migrate":
                    url =
                        seopressAjaxMigrate.seopress_slim_seo_migrate
                            .seopress_slim_seo_migration;
                    action = "seopress_slim_seo_migration";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_slim_seo_migrate
                            .seopress_nonce;
                    break;
                case "seopress-metadata-migrate":
                    url =
                        seopressAjaxMigrate.seopress_metadata_csv
                            .seopress_metadata_export;
                    action = "seopress_metadata_export";
                    _ajax_nonce =
                        seopressAjaxMigrate.seopress_metadata_csv
                            .seopress_nonce;
                    break;
                default:
            }
            self.process_offset(0, self, url, action, _ajax_nonce, id);
        });

        process_offset = function (
            offset,
            self,
            url,
            action,
            _ajax_nonce,
            id,
            post_export,
            term_export
        ) {
            i18n = seopressAjaxMigrate.i18n.migration;
            if (id == "metadata") {
                i18n = seopressAjaxMigrate.i18n.export;
            }
            $.ajax({
                method: "POST",
                url: url,
                data: {
                    action: action,
                    offset: offset,
                    post_export: post_export,
                    term_export: term_export,
                    _ajax_nonce: _ajax_nonce,
                },
                success: function (data) {
                    if ("done" == data.data.offset) {
                        $("#seopress-" + id + "-migrate").removeAttr(
                            "disabled"
                        );
                        $(".spinner").css("visibility", "hidden");
                        $("#" + id + "-migration-tool .log").css("display", "block");
                        $("#" + id + "-migration-tool .log").html("<div class='seopress-notice is-success'><p>" + i18n + "</p></div>");

                        if (data.data.url != "") {
                            $(location).attr("href", data.data.url);
                        }
                    } else {
                        self.process_offset(
                            parseInt(data.data.offset),
                            self,
                            url,
                            action,
                            _ajax_nonce,
                            id,
                            data.data.post_export,
                            data.data.term_export
                        );
                        if (data.data.total) {
                            progress = (data.data.count / data.data.total * 100).toFixed(2);
                            $("#" + id + "-migration-tool .log").css("display", "block");
                            $("#" + id + "-migration-tool .log").html("<div class='seopress-notice'><p>" + progress + "%</p></div>");
                        }
                    }
                },
            });
        };
        $("#seopress-" + item + "-migrate").on("click", function () {
            $(this).attr("disabled", "disabled");
            $("#" + item + "-migration-tool .spinner").css(
                "visibility",
                "visible"
            );
            $("#" + item + "-migration-tool .spinner").css("float", "none");
            $("#" + item + "-migration-tool .log").html("");
        });
    });
});
