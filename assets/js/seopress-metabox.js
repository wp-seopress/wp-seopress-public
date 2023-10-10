document.addEventListener("DOMContentLoaded", function () {
    const $ = jQuery;

    $("#seopress-tabs .hidden").removeClass("hidden");
    $("#seopress-tabs").tabs({
        classes: {
            "ui-tabs": "seopress-ui-tabs"
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

    /**
     * Execute a function given a delay time
     *
     * @param {type} func
     * @param {type} wait
     * @param {type} immediate
     * @returns {Function}
     */
    var debounce = function (func, wait, immediate) {
        var timeout;
        return function () {
            var context = this,
                args = arguments;
            var later = function () {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    /**
     * Get Preview meta title
     */
    $("#seopress_titles_title_meta").on(
        "change paste keyup",
        debounce(function (e) {
            const template = $(this).val();
            const termId = $("#seopress-tabs").data("term-id");
            const homeId = $("#seopress-tabs").data("home-id");

            $.ajax({
                method: "GET",
                url: seopressAjaxRealPreview.ajax_url,
                data: {
                    action: "get_preview_meta_title",
                    template: template,
                    post_id: $("#seopress-tabs").attr("data_id"),
                    term_id: termId.length === 0 ? undefined : termId,
                    home_id: homeId.length === 0 ? undefined : homeId,
                    nonce: seopressAjaxRealPreview.get_preview_meta_title,
                },
                success: function (response) {
                    const { data } = response;

                    if (data.length > 0) {
                        $(".snippet-title").hide();
                        $(".snippet-title-default").hide();
                        $(".snippet-title-custom").text(data);
                        $(".snippet-title-custom").show();
                        if ($("#seopress_titles_title_counters").length > 0) {
                            $("#seopress_titles_title_counters").text(
                                data.length
                            );
                        }
                        if ($("#seopress_titles_title_pixel").length > 0) {
                            $("#seopress_titles_title_pixel").text(
                                pixelTitle(data)
                            );
                        }
                    } else {
                        $(".snippet-title").hide();
                        $(".snippet-title-custom").hide();
                        $(".snippet-title-default").show();
                    }
                },
            });
        }, 300)
    );

    $("#seopress-tag-single-title").click(function () {
        $("#seopress_titles_title_meta").val(
            sp_get_field_length($("#seopress_titles_title_meta")) +
            $("#seopress-tag-single-title").attr("data-tag")
        );
        $("#seopress_titles_title_meta").trigger("paste");
    });
    $("#seopress-tag-single-site-title").click(function () {
        $("#seopress_titles_title_meta").val(
            sp_get_field_length($("#seopress_titles_title_meta")) +
            $("#seopress-tag-single-site-title").attr("data-tag")
        );
        $("#seopress_titles_title_meta").trigger("paste");
    });
    $("#seopress-tag-single-excerpt").click(function () {
        $("#seopress_titles_desc_meta").val(
            sp_get_field_length($("#seopress_titles_desc_meta")) +
            $("#seopress-tag-single-excerpt").attr("data-tag")
        );
        $("#seopress_titles_title_meta").trigger("paste");
    });
    $("#seopress-tag-single-sep").click(function () {
        $("#seopress_titles_title_meta").val(
            sp_get_field_length($("#seopress_titles_title_meta")) +
            $("#seopress-tag-single-sep").attr("data-tag")
        );
        $("#seopress_titles_title_meta").trigger("paste");
    });

    let alreadyBind = false;

    //All variables
    $(".seopress-tag-dropdown").each(function (item) {
        const _self = $(this);

        function handleClickLi(current) {
            if (_self.hasClass("tag-title")) {
                $("#seopress_titles_title_meta").val(
                    sp_get_field_length($("#seopress_titles_title_meta")) +
                    $(current).attr("data-value")
                );
                $("#seopress_titles_title_meta").trigger("paste");
            }
            if (_self.hasClass("tag-description")) {
                $("#seopress_titles_desc_meta").val(
                    sp_get_field_length($("#seopress_titles_desc_meta")) +
                    $(current).attr("data-value")
                );
                $("#seopress_titles_desc_meta").trigger("paste");
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
});
