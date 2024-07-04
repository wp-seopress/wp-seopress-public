jQuery(document).ready(function () {
    jQuery(document).on("click", "#seopress-seo-tab", function (e) {
        jQuery("#elementor-panel-footer-settings").trigger("click");
        jQuery(".elementor-control-seopress_title_settings").trigger("click");
    });
});

var scriptInitialized = false;
var socialInitialized = false;

const runGooglePreview = function () {
    setTimeout(function () {
        googlePreview();
    }, 1000);
};

var socialPreviewItemView = elementor.modules.controls.BaseData.extend({
    onReady: function () {
        if (
            seopressFiltersElementor.resize_panel &&
            seopressFiltersElementor.resize_panel === "1"
        ) {
            elementor.panel.storage.size.width = "495px";
            elementor.panel.setSize();
        }

        var $ = jQuery;

        const observeFBThumbnail = function () {
            let fbThumbnail = document.querySelectorAll(
                ".elementor-control-_seopress_social_fb_img .elementor-control-media__preview"
            );

            if (!fbThumbnail.length) {
                return;
            }

            fbThumbnail = fbThumbnail[0];

            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (
                        mutation.type == "attributes" &&
                        mutation.attributeName == "style"
                    ) {
                        let img = mutation.target.style.backgroundImage
                            .replace('url("', "")
                            .replace('")', "");
                        if (!img.length) {
                            img = elSocialData.fbDefaultImage;
                        }
                        $(".snippet-fb-img img").attr("src", img);
                        sp_social_img("fb");
                    }
                });
            });

            observer.observe(fbThumbnail, {
                attributes: true,
            });
        };

        const observeTWThumbnail = function () {
            let twThumbnail = document.querySelectorAll(
                ".elementor-control-_seopress_social_twitter_img .elementor-control-media__preview"
            );

            if (!twThumbnail.length) {
                return;
            }

            twThumbnail = twThumbnail[0];

            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (
                        mutation.type == "attributes" &&
                        mutation.attributeName == "style"
                    ) {
                        let img = mutation.target.style.backgroundImage
                            .replace('url("', "")
                            .replace('")', "");
                        if (!img.length) {
                            img = elSocialData.twDefaultImage;
                        }
                        $(".snippet-twitter-img-default img").attr("src", img);
                        sp_social_img("twitter");
                    }
                });
            });

            observer.observe(twThumbnail, {
                attributes: true,
            });
        };

        const updateFbSnippetTitle = function () {
            let value = $(this).val();

            if (value == "") {
                value = $(this).attr("placeholder");
            }

            $(".snippet-fb-title").text(value);
        };

        const updateFbSnippetDesc = function () {
            let value = $(this).val();

            if (value == "") {
                value = $(this).attr("placeholder");
            }

            $(".snippet-fb-description-custom").text(value);
        };

        const updateTwSnippetTitle = function () {
            let value = $(this).val();

            if (value == "") {
                value = $(this).attr("placeholder");
            }

            $(".snippet-twitter-title").text(value);
        };

        const updateTwSnippetDesc = function () {
            let value = $(this).val();

            if (value == "") {
                value = $(this).attr("placeholder");
            }

            $(".snippet-twitter-description").text(value);
        };

        $(document).on(
            "input",
            "input[data-setting=_seopress_social_fb_title]",
            updateFbSnippetTitle
        );
        $(document).on(
            "input",
            "textarea[data-setting=_seopress_social_fb_desc]",
            updateFbSnippetDesc
        );

        $(document).on(
            "input",
            "input[data-setting=_seopress_social_twitter_title]",
            updateTwSnippetTitle
        );
        $(document).on(
            "input",
            "textarea[data-setting=_seopress_social_twitter_desc]",
            updateTwSnippetDesc
        );

        if (!scriptInitialized) {
            if ($("#toggle-preview").attr("data-toggle") == "1") {
                $(
                    ".elementor-control-field.google-snippet-box .google-snippet-preview"
                ).addClass("mobile-preview");
            } else {
                $(
                    ".elementor-control-field.google-snippet-box .google-snippet-preview"
                ).removeClass("mobile-preview");
            }

            $(document).on(
                "click",
                ".elementor-control-field.google-snippet-box #toggle-preview",
                function () {
                    $(
                        ".elementor-control-field.google-snippet-box #toggle-preview"
                    ).attr(
                        "data-toggle",
                        $(
                            ".elementor-control-field.google-snippet-box #toggle-preview"
                        ).attr("data-toggle") == "1"
                            ? "0"
                            : "1"
                    );
                    $(
                        ".elementor-control-field.google-snippet-box .google-snippet-preview"
                    ).toggleClass("mobile-preview");
                }
            );

            scriptInitialized = true;
        }

        $(document).on(
            "click",
            "#elementor-panel-saver-button-publish-label",
            runGooglePreview
        );

        setTimeout(function () {
            observeFBThumbnail();
            observeTWThumbnail();
        }, 1000);

        if (this.model.get("network") !== "google" && !socialInitialized) {
            socialInitialized = true;
            socialPreview();
        }

        if (this.model.get("network") === "google") {
            googlePreview();
        } else if (this.model.get("network") === "facebook") {
            setTimeout(function () {
                sp_social_img("fb");
            }, 1000);
        } else {
            setTimeout(function () {
                sp_social_img("twitter");
            }, 1000);
        }
    },

    onBeforeDestroy() {
        jQuery(document).off(
            "click",
            "#elementor-panel-saver-button-publish-label",
            runGooglePreview
        );
    },
});

elementor.addControlView("seopress-social-preview", socialPreviewItemView);
