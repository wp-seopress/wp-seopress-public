var elSocialData = [];
elSocialData.fbDefaultImage = '';
elSocialData.twDefaultImage = '';

function googlePreview() {
    jQuery.ajax({
        method: "GET",
        url: seopressElementorBase.seopress_real_preview,
        data: {
            action: "seopress_do_real_preview",
            post_id: seopressElementorBase.post_id,
            tax_name: seopressElementorBase.post_tax,
            origin: seopressElementorBase.origin,
            post_type: seopressElementorBase.post_type,
            seopress_analysis_target_kw: seopressElementorBase.keywords,
            is_elementor: seopressElementorBase.is_elementor,
            _ajax_nonce: seopressElementorBase.seopress_nonce
        },
        success: function (t) {
            jQuery(".elementor-control-field.google-snippet-box .google-snippet-preview .snippet-title").text(t.data.title.value);
            jQuery(".elementor-control-field.google-snippet-box .google-snippet-preview .snippet-title-default").text(t.data.title.value);
            jQuery(".elementor-control-field.google-snippet-box .google-snippet-preview .snippet-description").text(t.data.description.value);
            jQuery(".elementor-control-field.google-snippet-box .google-snippet-preview .snippet-description-default").text(t.data.description.value);

            const $metaTitle = jQuery("input[data-setting=_seopress_titles_title]");
            const $metaDesc = jQuery("textarea[data-setting=_seopress_titles_desc]");

            $metaTitle.attr('placeholder', t.data.title.value);
            $metaDesc.attr('placeholder', t.data.description.value);

            if ($metaTitle.val() == '') {
                elementor.modules.controls.Seopresstextlettercounter.prototype.countLength(false, $metaTitle);
            }

            if ($metaDesc.val() == '') {
                elementor.modules.controls.Seopresstextlettercounter.prototype.countLength(false, $metaDesc);
            }
        }
    })
}

function socialPreview() {
    jQuery.ajax({
        method: "GET",
        url: seopressElementorBase.seopress_real_preview,
        data: {
            action: "seopress_do_real_preview",
            post_id: seopressElementorBase.post_id,
            tax_name: seopressElementorBase.post_tax,
            origin: seopressElementorBase.origin,
            post_type: seopressElementorBase.post_type,
            seopress_analysis_target_kw: seopressElementorBase.keywords,
            is_elementor: seopressElementorBase.is_elementor,
            _ajax_nonce: seopressElementorBase.seopress_nonce
        },
        success: socialPreviewFillData
    })
}

function socialPreviewFillData(s) {
    typeof s.data["og:title"] === "undefined" ? og_title = "" : og_title = s.data["og:title"].value;
    typeof s.data["og:description"] === "undefined" ? og_desc = "" : og_desc = s.data["og:description"].value;
    typeof s.data["og:image"] === "undefined" ? og_img = "" : og_img = s.data["og:image"].value;
    typeof s.data["og:url"] === "undefined" ? og_url = "" : og_url = s.data["og:url"].value;
    typeof s.data["og:site_name"] === "undefined" ? og_site_name = "" : og_site_name = s.data["og:site_name"].value;
    typeof s.data["twitter:title"] === "undefined" ? tw_title = "" : tw_title = s.data["twitter:title"].value;
    typeof s.data["twitter:description"] === "undefined" ? tw_desc = "" : tw_desc = s.data["twitter:description"].value;
    typeof s.data["twitter:image"] === "undefined" ? tw_img = "" : tw_img = s.data["twitter:image"].value;
    typeof s.data["canonical"] === "undefined" ? canonical = "" : canonical = s.data["canonical"].value;
    typeof s.data.meta_robots === "undefined" ? meta_robots = "" : meta_robots = s.data.meta_robots.value;

    var data_arr = {
        og_title: og_title,
        og_desc: og_desc,
        og_img: og_img,
        og_url: og_url,
        og_site_name: og_site_name,
        tw_title: tw_title,
        tw_desc: tw_desc,
        tw_img: tw_img,
        canonical: canonical
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

    // Facebook Preview
    if (data_arr.og_title) {
        $fbTitle = jQuery('input[data-setting=_seopress_social_fb_title]');

        $fbTitle.attr('placeholder', data_arr.og_title[0]);
    }

    if (data_arr.og_desc) {
        $fbDesc = jQuery('textarea[data-setting=_seopress_social_fb_desc]');

        $fbDesc.attr('placeholder', data_arr.og_desc[0]);
    }

    if (data_arr.og_img) {
        elSocialData.fbDefaultImage = data_arr.og_img[0];
        jQuery('.snippet-fb-img img').attr('src', data_arr.og_img[0]);
    }

    jQuery(".facebook-snippet-preview .snippet-fb-url").text(data_arr.og_url),
        jQuery(".facebook-snippet-preview .snippet-fb-site-name").text(data_arr.og_site_name)

    // Twitter Preview
    if (data_arr.tw_title) {
        $twTitle = jQuery('input[data-setting=_seopress_social_twitter_title]');

        $twTitle.attr('placeholder', data_arr.tw_title[0]);
    }

    if (data_arr.tw_desc) {
        $twDesc = jQuery('textarea[data-setting=_seopress_social_twitter_desc]');

        $twDesc.attr('placeholder', data_arr.tw_desc[0]);
    }

    if (data_arr.tw_img) {
        elSocialData.twDefaultImage = data_arr.tw_img[0];
        jQuery('.snippet-twitter-img-default img').attr('src', data_arr.tw_img[0]);
    }
}

function contentAnalysisToggle() {
    var stop = false;
    jQuery(document).on('click', '.gr-analysis-title .btn-toggle', function (event) {
        if (stop) {
            event.stopImmediatePropagation();
            event.preventDefault();
            stop = false;
        }
        jQuery(this).toggleClass('open');
        jQuery(this).attr('aria-expanded', (jQuery(this).attr('aria-expanded') == "false" ? true : false));
        jQuery(this).parent().parent().next(".gr-analysis-content").toggle();
        jQuery(this).parent().parent().next(".gr-analysis-content").attr('aria-hidden', (jQuery(this).parent().parent().next(".gr-analysis-content").attr('aria-hidden') == "true" ? false : true));
    });

    //Show all
    jQuery(document).on('click', '#expand-all', function (e) {
        e.preventDefault();
        jQuery('.gr-analysis-content').show();
        jQuery(".gr-analysis-title button").attr('aria-expanded', true);
        jQuery(".gr-analysis-content").attr('aria-hidden', false);
    });
    //Hide all
    jQuery(document).on('click', '#close-all', function (e) {
        e.preventDefault();
        jQuery('.gr-analysis-content').hide();
        jQuery(".gr-analysis-title button").attr('aria-expanded', false);
        jQuery(".gr-analysis-content").attr('aria-hidden', true);
    });
}

function contentAnalysis() {
    jQuery.ajax({
        method: "GET",
        url: seopressElementorBase.seopress_real_preview,
        data: {
            action: "seopress_do_real_preview",
            post_id: seopressElementorBase.post_id,
            tax_name: seopressElementorBase.post_tax,
            origin: seopressElementorBase.origin,
            post_type: seopressElementorBase.post_type,
            seopress_analysis_target_kw: seopressElementorBase.keywords,
            is_elementor: seopressElementorBase.is_elementor,
            _ajax_nonce: seopressElementorBase.seopress_nonce
        },
        beforeSend: function () {
            jQuery(".analysis-score p span").fadeIn().text(seopressElementorBase.i18n.progress),
                jQuery(".analysis-score p").addClass('loading')
        },
        success: function (s) {
            typeof s.data.meta_robots === "undefined" ? meta_robots = "" : meta_robots = s.data.meta_robots.value;

            // Meta Robots
            meta_robots = meta_robots.toString();

            jQuery("#sp-advanced-alert").empty();

            var if_noindex = new RegExp('noindex');

            if (if_noindex.test(meta_robots)) {
                jQuery("#sp-advanced-alert").append('<span class="impact high" aria-hidden="true"></span>');
            }

            jQuery("#seopress-analysis-tabs").load("/wp-admin/post.php?post=" + seopressElementorBase.post_id + "&action=edit #seopress-analysis-tabs-1");
            jQuery(".analysis-score p").removeClass('loading');
        }
    })
}

function sp_is_valid_url(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    return (res !== null)
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

