//Init tabs
document.addEventListener("DOMContentLoaded", function () {
    const $ = jQuery;

    if ($("#seopress-ca-tabs").length && $("#seopress-ca-tabs .wrap-ca-list").length) {
        $("#seopress-ca-tabs .hidden").removeClass("hidden");
        $("#seopress-ca-tabs").tabs();
    }
});

function sp_titles_counters() {
    const $ = jQuery;
    let elementTitleMeta = $("#seopress_titles_title_meta");
    if ($("#seopress_titles_title_meta").length) {

        if ($(".snippet-title-custom:visible").length > 0) {
            elementTitleMeta = $(".snippet-title-custom");
        } else if ($(".snippet-title:visible").length > 0) {
            elementTitleMeta = $(".snippet-title");
        } else if ($(".snippet-title-default:visible").length > 0) {
            elementTitleMeta = $(".snippet-title-default");
        }

        var meta_title_val = elementTitleMeta.val();
        var meta_title_placeholder = $("#seopress_titles_title_meta").attr(
            "placeholder"
        );

        $("#seopress_titles_title_counters").after(
            '<div id="seopress_titles_title_counters_val">/ 60</div>'
        ),
            meta_title_val.length > 0
                ? ($("#seopress_titles_title_counters").text(
                    meta_title_val.length
                ),
                    $("#seopress_titles_title_pixel").text(
                        pixelTitle(meta_title_val)
                    ))
                : meta_title_placeholder.length &&
                ($("#seopress_titles_title_counters").text(
                    meta_title_placeholder.length
                ),
                    $("#seopress_titles_title_pixel").text(
                        pixelTitle(meta_title_placeholder)
                    )),
            meta_title_val.length > 60
                ? $("#seopress_titles_title_counters").css("color", "red")
                : meta_title_placeholder.length > 60 &&
                $("#seopress_titles_title_counters").css("color", "red"),
            pixelTitle(meta_title_val) > 568
                ? $("#seopress_titles_title_pixel").css("color", "red")
                : pixelTitle(meta_title_placeholder) > 568 &&
                $("#seopress_titles_title_pixel").css("color", "red");

        if (meta_title_val.length) {
            var progress = Math.round((pixelTitle(meta_title_val) / 568) * 100);
        } else {
            var progress = Math.round(
                (pixelTitle(meta_title_placeholder) / 568) * 100
            );
        }

        if (progress >= 100) {
            progress = 100;
        }

        $("#seopress_titles_title_counters_progress").attr(
            "aria-valuenow",
            progress
        ),
            $("#seopress_titles_title_counters_progress").text(progress + "%"),
            $("#seopress_titles_title_counters_progress").css(
                "width",
                progress + "%"
            ),
            $(
                "#seopress_titles_title_meta, #seopress-tag-single-title, #seopress-tag-single-site-title, #seopress-tag-single-sep"
            ).on("keyup paste change click", function (e) {
                var meta_title_val = $("#seopress_titles_title_meta").val();
                if ($(".snippet-title-custom:visible").length > 0) {
                    meta_title_val = $(".snippet-title-custom").text();
                } else if ($(".snippet-title:visible").length > 0) {
                    meta_title_val = $(".snippet-title").text();
                } else if ($(".snippet-title-default:visible").length > 0) {
                    meta_title_val = $(".snippet-title-default").text();
                }

                var meta_title_placeholder = $(
                    "#seopress_titles_title_meta"
                ).attr("placeholder");

                $("#seopress_titles_title_counters").css("color", "inherit"),
                    $("#seopress_titles_title_pixel").css("color", "inherit"),
                    meta_title_val.length > 60 &&
                    $("#seopress_titles_title_counters").css(
                        "color",
                        "red"
                    ),
                    pixelTitle(meta_title_val) > 568 &&
                    $("#seopress_titles_title_pixel").css("color", "red");

                if (meta_title_val.length == 0) {
                    meta_title_placeholder.length > 60 &&
                        $("#seopress_titles_title_counters").css(
                            "color",
                            "red"
                        ),
                        pixelTitle(meta_title_placeholder) > 568 &&
                        $("#seopress_titles_title_pixel").css(
                            "color",
                            "red"
                        );
                }

                meta_title_val.length > 0
                    ? ($("#seopress_titles_title_counters").text(
                        meta_title_val.length
                    ),
                        $("#seopress_titles_title_pixel").text(
                            pixelTitle(meta_title_val)
                        ))
                    : meta_title_placeholder.length &&
                    ($("#seopress_titles_title_counters").text(
                        meta_title_placeholder.length
                    ),
                        $("#seopress_titles_title_pixel").text(
                            pixelTitle(meta_title_placeholder)
                        ));

                if (meta_title_val.length) {
                    var progress = Math.round(
                        (pixelTitle(meta_title_val) / 568) * 100
                    );
                } else {
                    var progress = Math.round(
                        (pixelTitle(meta_title_placeholder) / 568) * 100
                    );
                }

                if (progress >= 100) {
                    progress = 100;
                }

                $("#seopress_titles_title_counters_progress").attr(
                    "aria-valuenow",
                    progress
                ),
                    $("#seopress_titles_title_counters_progress").text(
                        progress + "%"
                    ),
                    $("#seopress_titles_title_counters_progress").css(
                        "width",
                        progress + "%"
                    );
            });
    }
}

function sp_meta_desc_counters() {
    const $ = jQuery;
    if ($("#seopress_titles_desc_meta").length) {
        var meta_desc_val = $("#seopress_titles_desc_meta").val();
        var meta_desc_placeholder = $("#seopress_titles_desc_meta").attr(
            "placeholder"
        );

        $("#seopress_titles_desc_counters").after(
            '<div id="seopress_titles_desc_counters_val">/ 160</div>'
        ),
            meta_desc_val.length > 0
                ? ($("#seopress_titles_desc_counters").text(
                    meta_desc_val.length
                ),
                    $("#seopress_titles_desc_pixel").text(
                        pixelDesc(meta_desc_val)
                    ))
                : meta_desc_placeholder.length &&
                ($("#seopress_titles_desc_counters").text(
                    meta_desc_placeholder.length
                ),
                    $("#seopress_titles_desc_pixel").text(
                        pixelDesc(meta_desc_placeholder)
                    )),
            meta_desc_val.length > 160
                ? $("#seopress_titles_desc_counters").css("color", "red")
                : meta_desc_placeholder.length > 160 &&
                $("#seopress_titles_desc_counters").css("color", "red"),
            pixelDesc(meta_desc_val) > 940
                ? $("#seopress_titles_desc_pixel").css("color", "red")
                : pixelDesc(meta_desc_placeholder) > 940 &&
                $("#seopress_titles_desc_pixel").css("color", "red");

        if (meta_desc_val.length) {
            var progress = Math.round((pixelDesc(meta_desc_val) / 940) * 100);
        } else {
            var progress = Math.round(
                (pixelDesc(meta_desc_placeholder) / 940) * 100
            );
        }

        if (progress >= 100) {
            progress = 100;
        }

        $("#seopress_titles_desc_counters_progress").attr(
            "aria-valuenow",
            progress
        ),
            $("#seopress_titles_desc_counters_progress").text(progress + "%"),
            $("#seopress_titles_desc_counters_progress").css(
                "width",
                progress + "%"
            ),
            $("#seopress_titles_desc_meta, #seopress-tag-single-excerpt").on(
                "keyup paste change click",
                function (e) {
                    var meta_desc_val = $("#seopress_titles_desc_meta").val();
                    var meta_desc_placeholder = $(
                        "#seopress_titles_desc_meta"
                    ).attr("placeholder");

                    $("#seopress_titles_desc_counters").css(
                        "color",
                        "inherit"
                    ),
                        $("#seopress_titles_desc_pixel").css(
                            "color",
                            "inherit"
                        ),
                        meta_desc_val.length > 160 &&
                        $("#seopress_titles_desc_counters").css(
                            "color",
                            "red"
                        ),
                        pixelDesc(meta_desc_val) > 940 &&
                        $("#seopress_titles_desc_pixel").css(
                            "color",
                            "red"
                        );

                    if (meta_desc_val.length == 0) {
                        meta_desc_placeholder.length > 160 &&
                            $("#seopress_titles_desc_counters").css(
                                "color",
                                "red"
                            ),
                            pixelDesc(meta_desc_placeholder) > 940 &&
                            $("#seopress_titles_desc_pixel").css(
                                "color",
                                "red"
                            );
                    }

                    meta_desc_val.length > 0
                        ? ($("#seopress_titles_desc_counters").text(
                            meta_desc_val.length
                        ),
                            $("#seopress_titles_desc_pixel").text(
                                pixelDesc(meta_desc_val)
                            ))
                        : meta_desc_placeholder.length &&
                        ($("#seopress_titles_desc_counters").text(
                            meta_desc_placeholder.length
                        ),
                            $("#seopress_titles_desc_pixel").text(
                                pixelDesc(meta_desc_placeholder)
                            )),
                        meta_desc_val.length > 0
                            ? ($(".snippet-description-custom").text(
                                e.target.value.substr(0, 160) + '...',
                            ),
                                $(".snippet-description").hide(),
                                $(".snippet-description-custom").css(
                                    "display",
                                    "inline"
                                ),
                                $(".snippet-description-default").hide())
                            : 0 == meta_desc_val.length &&
                            ($(".snippet-description-default").css(
                                "display",
                                "inline"
                            ),
                                $(".snippet-description-custom").hide(),
                                $(".snippet-description").hide());

                    if (meta_desc_val.length) {
                        var progress = Math.round(
                            (pixelDesc(meta_desc_val) / 940) * 100
                        );
                    } else {
                        var progress = Math.round(
                            (pixelDesc(meta_desc_placeholder) / 940) * 100
                        );
                    }

                    if (progress >= 100) {
                        progress = 100;
                    }

                    $("#seopress_titles_desc_counters_progress").attr(
                        "aria-valuenow",
                        progress
                    ),
                        $("#seopress_titles_desc_counters_progress").text(
                            progress + "%"
                        ),
                        $("#seopress_titles_desc_counters_progress").css(
                            "width",
                            progress + "%"
                        );
                }
            ),
            $("#excerpt, .editor-post-excerpt textarea").keyup(function (e) {
                var meta_desc_val = $("#seopress_titles_desc_meta").val();
                var meta_desc_placeholder = $(
                    "#seopress_titles_desc_meta"
                ).attr("placeholder");

                0 == meta_desc_val.length &&
                    0 == $(".snippet-description-custom").val().length &&
                    ($(".snippet-description-custom").text(e.target.value),
                        $(".snippet-description").hide(),
                        $(".snippet-description-custom").css("display", "inline"),
                        $(".snippet-description-default").hide());

                if (meta_desc_val.length) {
                    var progress = meta_desc_val.length;
                } else {
                    var progress = meta_desc_placeholder.length;
                }
                if (progress >= 100) {
                    progress = 100;
                }

                $("#seopress_titles_desc_counters_progress").attr(
                    "aria-valuenow",
                    progress
                ),
                    $("#seopress_titles_desc_counters_progress").text(
                        progress + "%"
                    ),
                    $("#seopress_titles_desc_counters_progress").css(
                        "width",
                        progress + "%"
                    );
            });
    }
}

function pixelTitle(e) {
    inputText = e;
    font = "20px Arial";

    canvas = document.createElement("canvas");
    context = canvas.getContext("2d");
    context.font = font;
    width = context.measureText(inputText).width;
    formattedWidth = Math.ceil(width);

    return formattedWidth;
}

function pixelDesc(e) {
    inputText = e;
    font = "14px Arial";

    canvas = document.createElement("canvas");
    context = canvas.getContext("2d");
    context.font = font;
    width = context.measureText(inputText).width;
    formattedWidth = Math.ceil(width);

    return formattedWidth;
}

function sp_is_valid_url(string) {
    var res = string.match(
        /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g
    );
    return res !== null;
}

function sp_social_img(social_slug) {
    const $ = jQuery;
    if ($("#seopress_social_fb_title_meta").length) {
        $(".snippet-" + social_slug + "-img-alert").hide();
        var meta_img_val = $(
            "#seopress_social_" + social_slug + "_img_meta"
        ).val();

        if (meta_img_val == "") {
            var meta_img_val = $(
                "#seopress_social_" + social_slug + "_img_meta"
            ).attr("placeholder");
        }

        // Check valid URL
        if (sp_is_valid_url(meta_img_val) === true) {
            meta_img_val.length > 0
                ? ($(".snippet-" + social_slug + "-img-custom img").attr("src", meta_img_val),
                    $(".snippet-" + social_slug + "-img").hide(),
                    $(".snippet-" + social_slug + "-img-custom").show(),
                    $(".snippet-" + social_slug + "-img-default").hide())
                : 0 == meta_img_val.length &&
                ($(".snippet-" + social_slug + "-img-default").show(),
                    $(".snippet-" + social_slug + "-img-custom").show(),
                    $(".snippet-" + social_slug + "-img").hide());

            if (meta_img_val.length > 0) {
                // Check file URL
                $
                    .get(meta_img_val)
                    .done(function () {
                        // Extract filetype
                        var meta_img_filetype = meta_img_val
                            .split(/\#|\?/)[0]
                            .split(".")
                            .pop()
                            .trim();
                        var types = ["jpg", "jpeg", "gif", "png", "webp"];

                        if (types.indexOf(meta_img_filetype) == -1) {
                            $(".snippet-" + social_slug + "-img-alert.alert1").show();
                        } else {
                            // Extract image size
                            var tmp_img = new Image();
                            tmp_img.src = meta_img_val;
                            $(tmp_img).one("load", function () {
                                pic_real_width = parseInt(tmp_img.width);
                                pic_real_height = parseInt(tmp_img.height);

                                // Default minimum size
                                if (social_slug == "fb") {
                                    (min_width = 200), (min_height = 200);
                                } else {
                                    (min_width = 144), (min_height = 144);
                                }
                                if (
                                    pic_real_width < min_width ||
                                    pic_real_height < min_height
                                ) {
                                    $(
                                        ".snippet-" +
                                        social_slug +
                                        "-img-alert.alert2"
                                    ).show();
                                }
                                ratio_img = (
                                    pic_real_width / pic_real_height
                                ).toFixed(2);
                                $(
                                    ".snippet-" + social_slug + "-img-alert.alert4"
                                ).show();
                                $(
                                    ".snippet-" +
                                    social_slug +
                                    "-img-alert.alert4 span"
                                ).text(ratio_img);
                            });
                            // check filesize
                            fetch(meta_img_val)
                                .then(response => {
                                    const fileSize = Number(response.headers.get('Content-Length'));
                                    if ((fileSize / 1024) > 300) {
                                        $(".snippet-" + social_slug + "-img-alert.alert6").show();
                                        $(".snippet-" + social_slug + "-img-alert.alert6 span").text(Math.round(fileSize / 1024) + 'KB.');
                                    }
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                        }
                    })
                    .fail(function () {
                        $(".snippet-" + social_slug + "-img-alert.alert3").show();
                    });
            }
        } else {
            $(".snippet-" + social_slug + "-img-alert.alert5").show();
        }
    }
}

async function sp_social() {
    const $ = jQuery;
    if ($("#seopress_social_fb_title_meta").length) {
        // FACEBOOK
        $(
            "#seopress_social_fb_title_meta, #seopress-tag-single-title, #seopress-tag-single-site-title, #seopress-tag-single-sep"
        ).on("keyup paste change click", function (e) {
            var meta_fb_title_val = $("#seopress_social_fb_title_meta").val();

            meta_fb_title_val.length > 0
                ? ($(".snippet-fb-title-custom").text(e.target.value),
                    $(".snippet-fb-title").hide(),
                    $(".snippet-fb-title-custom").show(),
                    $(".snippet-fb-title-default").hide())
                : 0 == meta_fb_title_val.length &&
                ($(".snippet-fb-title-default").show(),
                    $(".snippet-fb-title-custom").hide(),
                    $(".snippet-fb-title").hide());
        });

        $("#seopress_social_fb_desc_meta").on(
            "keyup paste change click",
            function (e) {
                var meta_fb_desc_val = $("#seopress_social_fb_desc_meta").val();

                meta_fb_desc_val.length > 0
                    ? ($(".snippet-fb-description-custom").text(
                        e.target.value
                    ),
                        $(".snippet-fb-description").hide(),
                        $(".snippet-fb-description-custom").show(),
                        $(".snippet-fb-description-default").hide())
                    : 0 == meta_fb_desc_val.length &&
                    ($(".snippet-fb-description-default").show(),
                        $(".snippet-fb-description-custom").hide(),
                        $(".snippet-fb-description").hide());
            }
        );

        sp_social_img("fb");
        $("#seopress_social_fb_img_meta").on(
            "keyup paste change click",
            function () {
                sp_social_img("fb");
            }
        );

        // TWITTER
        $("#seopress_social_twitter_title_meta").on(
            "keyup paste change click",
            function (e) {
                var meta_fb_title_val = $(
                    "#seopress_social_twitter_title_meta"
                ).val();

                meta_fb_title_val.length > 0
                    ? ($(".snippet-twitter-title-custom").text(e.target.value),
                        $(".snippet-twitter-title").hide(),
                        $(".snippet-twitter-title-custom").show(),
                        $(".snippet-twitter-title-default").hide())
                    : 0 == meta_fb_title_val.length &&
                    ($(".snippet-twitter-title-default").show(),
                        $(".snippet-twitter-title-custom").hide(),
                        $(".snippet-twitter-title").hide());
            }
        );

        $("#seopress_social_twitter_desc_meta").on(
            "keyup paste change click",
            function (e) {
                var meta_fb_desc_val = $(
                    "#seopress_social_twitter_desc_meta"
                ).val();

                meta_fb_desc_val.length > 0
                    ? ($(".snippet-twitter-description-custom").text(
                        e.target.value
                    ),
                        $(".snippet-twitter-description").hide(),
                        $(".snippet-twitter-description-custom").show(),
                        $(".snippet-twitter-description-default").hide())
                    : 0 == meta_fb_desc_val.length &&
                    ($(".snippet-twitter-description-default").show(),
                        $(".snippet-twitter-description-custom").hide(),
                        $(".snippet-twitter-description").hide());
            }
        );

        sp_social_img("twitter");
        $("#seopress_social_twitter_img_meta").on(
            "keyup paste change click",
            function () {
                sp_social_img("twitter");
            }
        );
    }
}

//Content Analysis - Toggle
function sp_ca_toggle() {
    const $ = jQuery;
    var stop = false;
    $(".gr-analysis-title .btn-toggle").on("click", function (e) {
        if (stop) {
            event.stopImmediatePropagation();
            event.preventDefault();
            stop = false;
        }
        $(this).toggleClass("open");
        $(this).attr('aria-expanded', ($(this).attr('aria-expanded') == "false" ? true : false));
        $(this).parent().parent().next(".gr-analysis-content").toggle();
        $(this).parent().parent().next(".gr-analysis-content").attr('aria-hidden', ($(this).parent().parent().next(".gr-analysis-content").attr('aria-hidden') == "true" ? false : true));
    });

    //Show all
    $("#expand-all").on("click", function (e) {
        e.preventDefault();
        $(".gr-analysis-content").show();
        $(".gr-analysis-title button").attr('aria-expanded', true);
        $(".gr-analysis-content").attr('aria-hidden', false);
    });
    //Hide all
    $("#close-all").on("click", function (e) {
        e.preventDefault();
        $(".gr-analysis-content").hide();
        $(".gr-analysis-title button").attr('aria-expanded', false);
        $(".gr-analysis-content").attr('aria-hidden', true);
    });
}

//Tagify
var input = document.querySelector(
    "input[id=seopress_analysis_target_kw_meta]"
);

var target_kw = new Tagify(input, {
    originalInputValueFormat: (valuesArr) =>
        valuesArr.map((item) => item.value).join(","),
});

function seopress_google_suggest(data) {
    const $ = jQuery;

    var raw_suggestions = String(data);
    var suggestions_array = raw_suggestions.split(",");

    var i;
    for (i = 0; i < suggestions_array.length; i++) {
        if (
            suggestions_array[i] != null &&
            suggestions_array[i] != undefined &&
            suggestions_array[i] != "" &&
            suggestions_array[i] != "[object Object]"
        ) {
            document.getElementById("seopress_suggestions").innerHTML +=
                '<li><a href="#" class="sp-suggest-btn components-button is-secondary">' +
                suggestions_array[i] +
                "</a></li>";
        }
    }

    $(".sp-suggest-btn").click(function (e) {
        e.preventDefault();

        target_kw.addTags($(this).text());
    });
}

jQuery(document).ready(function (e) {
    const $ = jQuery;

    //default state
    if ($("#toggle-preview").attr("data-toggle") == "1") {
        $("#seopress_cpt .google-snippet-preview").addClass(
            "mobile-preview"
        );
    } else {
        $("#seopress_cpt .google-snippet-preview").removeClass(
            "mobile-preview"
        );
    }
    $("#toggle-preview").on("click", function () {
        $("#toggle-preview").attr(
            "data-toggle",
            $("#toggle-preview").attr("data-toggle") == "1" ? "0" : "1"
        );
        $("#seopress_cpt .google-snippet-preview").toggleClass(
            "mobile-preview"
        );
    });
    function s() {
        //Post ID
        if (typeof e("#seopress-tabs").attr("data_id") !== "undefined") {
            var post_id = e("#seopress-tabs").attr("data_id");
        } else if (typeof e("#seopress_content_analysis .wrap-seopress-analysis").attr("data_id") !== "undefined") {
            var post_id = e("#seopress_content_analysis .wrap-seopress-analysis").attr("data_id")
        }

        //Tax origin
        if (typeof e("#seopress-tabs").attr("data_tax") !== "undefined") {
            var tax_name = e("#seopress-tabs").attr("data_tax");
        } else if (typeof e("#seopress_content_analysis .wrap-seopress-analysis").attr("data_tax") !== "undefined") {
            var tax_name = e("#seopress_content_analysis .wrap-seopress-analysis").attr("data_tax")
        }

        //Origin
        if (typeof e("#seopress-tabs").attr("data_origin") !== "undefined") {
            var origin = e("#seopress-tabs").attr("data_origin");
        } else if (typeof e("#seopress_content_analysis .wrap-seopress-analysis").attr("data_origin") !== "undefined") {
            var origin = e("#seopress_content_analysis .wrap-seopress-analysis").attr("data_origin")
        }

        e.ajax({
            method: "GET",
            url: seopressAjaxRealPreview.seopress_real_preview,
            data: {
                action: "seopress_do_real_preview",
                post_id: post_id,
                tax_name: tax_name,
                origin: origin,
                post_type: e("#seopress_launch_analysis").attr(
                    "data_post_type"
                ),
                seopress_analysis_target_kw: e(
                    "#seopress_analysis_target_kw_meta"
                ).val(),
                _ajax_nonce: seopressAjaxRealPreview.seopress_nonce,
            },
            beforeSend: function () {
                e(".analysis-score p span")
                    .fadeIn()
                    .text(seopressAjaxRealPreview.i18n.progress),
                    e(".analysis-score p").addClass("loading");
            },
            success: function (s) {
                typeof s.data.og_title === "undefined"
                    ? (og_title = "")
                    : (og_title = s.data.og_title.values);
                typeof s.data.og_desc === "undefined"
                    ? (og_desc = "")
                    : (og_desc = s.data.og_desc.values);
                typeof s.data.og_img === "undefined"
                    ? (og_img = "")
                    : (og_img = s.data.og_img.values);
                typeof s.data.og_url === "undefined"
                    ? (og_url = "")
                    : (og_url = s.data.og_url.host);
                typeof s.data.og_site_name === "undefined"
                    ? (og_site_name = "")
                    : (og_site_name = s.data.og_site_name.values);
                typeof s.data.tw_title === "undefined"
                    ? (tw_title = "")
                    : (tw_title = s.data.tw_title.values);
                typeof s.data.tw_desc === "undefined"
                    ? (tw_desc = "")
                    : (tw_desc = s.data.tw_desc.values);
                typeof s.data.tw_img === "undefined"
                    ? (tw_img = "")
                    : (tw_img = s.data.tw_img.values);
                typeof s.data.meta_robots === "undefined"
                    ? (meta_robots = "")
                    : (meta_robots = s.data.meta_robots[0]);

                var data_arr = {
                    og_title: og_title,
                    og_desc: og_desc,
                    og_img: og_img,
                    og_url: og_url,
                    og_site_name: og_site_name,
                    tw_title: tw_title,
                    tw_desc: tw_desc,
                    tw_img: tw_img,
                };

                for (var key in data_arr) {
                    if (data_arr.length) {
                        if (data_arr[key].length > 1) {
                            key = data_arr[key].slice(-1)[0];
                        } else {
                            key = data_arr[key][0];
                        }
                    }
                }

                // Meta Robots
                meta_robots = meta_robots.toString();

                e("#sp-advanced-alert").empty();

                var if_noindex = new RegExp("noindex");

                if (if_noindex.test(meta_robots)) {
                    e("#sp-advanced-alert").append(
                        '<span class="impact high" aria-hidden="true"></span>'
                    );
                }

                // Google Preview
                title = '';
                if (s.data.title) {
                    title = s.data.title.substr(0, 60) + '...';
                }

                e("#seopress_cpt .google-snippet-preview .snippet-title").html(title),
                    e("#seopress_cpt .google-snippet-preview .snippet-title-default").html(title),
                    e("#seopress_titles_title_meta").attr("placeholder", title);

                meta_desc = '';
                if (s.data.meta_desc) {
                    meta_desc = s.data.meta_desc.substr(0, 160) + '...';
                }

                e("#seopress_cpt .google-snippet-preview .snippet-description").html(meta_desc),
                    e("#seopress_cpt .google-snippet-preview .snippet-description-default").html(meta_desc),
                    e("#seopress_titles_desc_meta").attr("placeholder", meta_desc);

                // Facebook Preview
                if (data_arr.og_title) {
                    e("#seopress_cpt #seopress_social_fb_title_meta").attr("placeholder", data_arr.og_title[0]),
                        e("#seopress_cpt .facebook-snippet-preview .snippet-fb-title").html(data_arr.og_title[0]),
                        e("#seopress_cpt .facebook-snippet-preview .snippet-fb-title-default").html(data_arr.og_title[0]);
                }

                if (data_arr.og_desc) {
                    e("#seopress_cpt #seopress_social_fb_desc_meta").attr("placeholder", data_arr.og_desc[0]),
                        e("#seopress_cpt .facebook-snippet-preview .snippet-fb-description").html(data_arr.og_desc[0]),
                        e("#seopress_cpt .facebook-snippet-preview .snippet-fb-description-default").html(data_arr.og_desc[0]);
                }

                if (data_arr.og_img) {
                    e("#seopress_cpt #seopress_social_fb_img_meta").attr(
                        "placeholder",
                        data_arr.og_img[0]
                    ),
                        e(
                            "#seopress_cpt .facebook-snippet-preview .snippet-fb-img img"
                        ).attr("src", data_arr.og_img[0]),
                        e(
                            "#seopress_cpt .facebook-snippet-preview .snippet-fb-img-default img"
                        ).attr("src", data_arr.og_img[0]);
                }

                e(
                    "#seopress_cpt .facebook-snippet-preview .snippet-fb-url"
                ).html(data_arr.og_url),
                    e(
                        "#seopress_cpt .facebook-snippet-preview .snippet-fb-site-name"
                    ).html(data_arr.og_site_name);

                // Twitter Preview
                if (data_arr.tw_title) {
                    e("#seopress_cpt #seopress_social_twitter_title_meta").attr(
                        "placeholder",
                        data_arr.tw_title[0]
                    ),
                        e(
                            "#seopress_cpt .twitter-snippet-preview .snippet-twitter-title"
                        ).html(data_arr.tw_title[0]),
                        e(
                            "#seopress_cpt .twitter-snippet-preview .snippet-twitter-title-default"
                        ).html(data_arr.tw_title[0]);
                }

                if (data_arr.tw_desc) {
                    e("#seopress_cpt #seopress_social_twitter_desc_meta").attr(
                        "placeholder",
                        data_arr.tw_desc[0]
                    ),
                        e(
                            "#seopress_cpt .twitter-snippet-preview .snippet-twitter-description"
                        ).html(data_arr.tw_desc[0]),
                        e(
                            "#seopress_cpt .twitter-snippet-preview .snippet-twitter-description-default"
                        ).html(data_arr.tw_desc[0]);
                }

                if (data_arr.tw_img) {
                    e("#seopress_cpt #seopress_social_twitter_img_meta").attr(
                        "placeholder",
                        data_arr.tw_img[0]
                    ),
                        e(
                            "#seopress_cpt .twitter-snippet-preview .snippet-twitter-img img"
                        ).attr("src", data_arr.tw_img[0]),
                        e(
                            "#seopress_cpt .twitter-snippet-preview .snippet-twitter-img-default img"
                        ).attr("src", data_arr.tw_img[0]);
                }

                e(
                    "#seopress_cpt .twitter-snippet-preview .snippet-twitter-url"
                ).html(data_arr.og_url),
                    e("#seopress_cpt #seopress_robots_canonical_meta").attr(
                        "placeholder",
                        s.data.canonical
                    ),
                    e("#seopress-analysis-tabs").load(
                        " #seopress-analysis-tabs-1",
                        "",
                        sp_ca_toggle
                    ),
                    e('#seopress-wrap-notice-target-kw').load(" #seopress-notice-target-kw", ''),
                    e(".analysis-score p").removeClass("loading"),
                    e(" #seopress_titles_title_counters_val").remove(),
                    e(" #seopress_titles_desc_counters_val").remove(),
                    sp_titles_counters(),
                    sp_meta_desc_counters(),
                    sp_social();
            },
        });
    }
    s(),
        e("#seopress_launch_analysis").on("click", function () {
            s();
        }),
        sp_ca_toggle();

    //Inspect URL
    $('#seopress_inspect_url').on("click", function () {
        $(this).attr("disabled", "disabled");
        $('.spinner').css("visibility", "visible");
        $('.spinner').css("float", "none");

        //Post ID
        if (typeof e("#seopress-tabs").attr("data_id") !== "undefined") {
            var post_id = e("#seopress-tabs").attr("data_id");
        } else if (typeof e("#seopress_content_analysis .wrap-seopress-analysis").attr("data_id") !== "undefined") {
            var post_id = e("#seopress_content_analysis .wrap-seopress-analysis").attr("data_id")
        }

        e.ajax({
            method: "POST",
            url: seopressAjaxInspectUrl.seopress_inspect_url,
            data: {
                action: "seopress_inspect_url",
                post_id: post_id,
                _ajax_nonce: seopressAjaxInspectUrl.seopress_nonce,
            },
            success: function () {
                $('.spinner').css("visibility", "hidden");
                $('#seopress_inspect_url').removeAttr("disabled");
                $("#seopress-ca-tabs-1").load(" #seopress-ca-tabs-1");
            }
        });
    });

});
