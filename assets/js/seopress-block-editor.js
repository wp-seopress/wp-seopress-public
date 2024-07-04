//Retrieve title / meta-desc from source code
jQuery(document).ready(function ($) {
    const { subscribe, select } = wp.data;
    let hasSaved = false;

    subscribe(() => {
        //var isSavingPost = wp.data.select('core/editor').isSavingPost();
        var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
        var isSavingMetaBoxes = wp.data.select('core/edit-post').isSavingMetaBoxes();


        if (isSavingMetaBoxes && !isAutosavingPost && !hasSaved) {

            //Post ID
            if (typeof $("#seopress-tabs").attr("data_id") !== "undefined") {
                var post_id = $("#seopress-tabs").attr("data_id");
            } else if (typeof $("#seopress_content_analysis .wrap-seopress-analysis").attr("data_id") !== "undefined") {
                var post_id = $("#seopress_content_analysis .wrap-seopress-analysis").attr("data_id")
            }

            //Tax origin
            if (typeof $("#seopress-tabs").attr("data_tax") !== "undefined") {
                var tax_name = $("#seopress-tabs").attr("data_tax");
            } else if (typeof $("#seopress_content_analysis .wrap-seopress-analysis").attr("data_tax") !== "undefined") {
                var tax_name = $("#seopress_content_analysis .wrap-seopress-analysis").attr("data_tax")
            }

            //Origin
            if (typeof $("#seopress-tabs").attr("data_origin") !== "undefined") {
                var origin = $("#seopress-tabs").attr("data_origin");
            } else if (typeof $("#seopress_content_analysis .wrap-seopress-analysis").attr("data_origin") !== "undefined") {
                var origin = $("#seopress_content_analysis .wrap-seopress-analysis").attr("data_origin")
            }

            $.ajax({
                method: 'GET',
                url: seopressAjaxRealPreview.seopress_real_preview,
                data: {
                    action: 'seopress_do_real_preview',
                    post_id: post_id,
                    tax_name: tax_name,
                    origin: origin,
                    post_type: $('#seopress_launch_analysis').attr('data_post_type'),
                    seopress_analysis_target_kw: $('#seopress_analysis_target_kw_meta').val(),
                    _ajax_nonce: seopressAjaxRealPreview.seopress_nonce,
                },
                beforeSend: function () {
                    $(".analysis-score p span").fadeIn().text(seopressAjaxRealPreview.i18n.progress),
                        $(".analysis-score p").addClass('loading')
                },
                success: function (s) {
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

                    // Meta Robots
                    meta_robots = meta_robots.toString();

                    $("#sp-advanced-alert").empty();

                    var if_noindex = new RegExp('noindex');

                    if (if_noindex.test(meta_robots)) {
                        $("#sp-advanced-alert").append('<span class="impact high" aria-hidden="true"></span>');
                    }

                    // Google Preview
                    title = '';
                    if (s.data.title) {
                        if (typeof s.data.title.value !== "undefined") {
                            title = s.data.title.value.substr(0, 60);
                        }
                        else {
                            title = s.data.title.substr(0, 60);
                        }
                    }

                    $("#seopress_cpt .google-snippet-preview .snippet-title").text(title),
                        $("#seopress_cpt .google-snippet-preview .snippet-title-default").text(title),
                        $("#seopress_titles_title_meta").attr("placeholder", title);

                    meta_desc = '';
                    if (s.data.description) {
                        meta_desc = s.data.description.value.substr(0, 160);
                    }
                    else if (typeof s.data.meta_desc !== "undefined") {
                        meta_desc = s.data.meta_desc.substr(0, 160);
                    }

                    $("#seopress_cpt .google-snippet-preview .snippet-description").text(meta_desc),
                        $("#seopress_cpt .google-snippet-preview .snippet-description-default").text(meta_desc),
                        $("#seopress_titles_desc_meta").attr("placeholder", meta_desc)

                    // Facebook Preview
                    if (data_arr.og_title) {
                        $("#seopress_cpt #seopress_social_fb_title_meta").attr("placeholder", data_arr.og_title[0]),
                            $("#seopress_cpt .facebook-snippet-preview .snippet-fb-title").text(data_arr.og_title[0]),
                            $("#seopress_cpt .facebook-snippet-preview .snippet-fb-title-default").text(data_arr.og_title[0])
                    }

                    if (data_arr.og_desc) {
                        $("#seopress_cpt #seopress_social_fb_desc_meta").attr("placeholder", data_arr.og_desc[0]),
                            $("#seopress_cpt .facebook-snippet-preview .snippet-fb-description").text(data_arr.og_desc[0]),
                            $("#seopress_cpt .facebook-snippet-preview .snippet-fb-description-default").text(data_arr.og_desc[0])
                    }

                    if (data_arr.og_img) {
                        $("#seopress_cpt #seopress_social_fb_img_meta").attr("placeholder", data_arr.og_img[0]),
                            $("#seopress_cpt .facebook-snippet-preview .snippet-fb-img img").attr("src", data_arr.og_img[0]),
                            $("#seopress_cpt .facebook-snippet-preview .snippet-fb-img-default img").attr("src", data_arr.og_img[0])
                    }

                    $("#seopress_cpt .facebook-snippet-preview .snippet-fb-url").text(data_arr.og_url),
                        $("#seopress_cpt .facebook-snippet-preview .snippet-fb-site-name").text(data_arr.og_site_name)

                    // Twitter Preview
                    if (data_arr.tw_title) {
                        $("#seopress_cpt #seopress_social_twitter_title_meta").attr("placeholder", data_arr.tw_title[0]),
                            $("#seopress_cpt .twitter-snippet-preview .snippet-twitter-title").text(data_arr.tw_title[0]),
                            $("#seopress_cpt .twitter-snippet-preview .snippet-twitter-title-default").text(data_arr.tw_title[0])
                    }

                    if (data_arr.tw_desc) {
                        $("#seopress_cpt #seopress_social_twitter_desc_meta").attr("placeholder", data_arr.tw_desc[0]),
                            $("#seopress_cpt .twitter-snippet-preview .snippet-twitter-description").text(data_arr.tw_desc[0]),
                            $("#seopress_cpt .twitter-snippet-preview .snippet-twitter-description-default").text(data_arr.tw_desc[0])
                    }

                    if (data_arr.tw_img) {
                        $("#seopress_cpt #seopress_social_twitter_img_meta").attr("placeholder", data_arr.tw_img[0]),
                            $("#seopress_cpt .twitter-snippet-preview .snippet-twitter-img img").attr("src", data_arr.tw_img[0]),
                            $("#seopress_cpt .twitter-snippet-preview .snippet-twitter-img-default img").attr("src", data_arr.tw_img[0])
                    }

                    $("#seopress_cpt .twitter-snippet-preview .snippet-twitter-url").text(data_arr.og_url),

                        $('#seopress_cpt #seopress_robots_canonical_meta').attr('placeholder', data_arr.canonical),

                        $('#seopress-analysis-tabs').load(" #seopress-analysis-tabs-1", '', sp_ca_toggle),
                        $('#seopress-wrap-notice-target-kw').load(" #seopress-notice-target-kw", ''),
                        $(".analysis-score p").removeClass('loading')
                },
            });
        }
        hasSaved = !!isSavingMetaBoxes; //isSavingPost != 0;
    });
});
