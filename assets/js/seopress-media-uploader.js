jQuery(document).ready(function ($) {
    //Clear the previous image if a user paste / edit the URL
    $("#seopress_social_fb_img_meta").on('paste change', function () {
        $("#seopress_social_fb_img_attachment_id").val('');
        $("#seopress_social_fb_img_width").val('');
        $("#seopress_social_fb_img_height").val('');
    });
    $("#seopress_social_twitter_img_meta").on('paste change', function () {
        $("#seopress_social_twitter_img_attachment_id").val('');
        $("#seopress_social_twitter_img_width").val('');
        $("#seopress_social_twitter_img_height").val('');
    });

    var mediaUploader;
    $(".button.seopress_social_facebook_img_cpt").click(function (e) {
        e.preventDefault();

        var url_field = $(this).parent().find("input[type=text]");
        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            multiple: false,
        });

        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on("select", function () {
            attachment = mediaUploader
                .state()
                .get("selection")
                .first()
                .toJSON();
            $(url_field).val(attachment.url);
        });
        // Open the uploader dialog
        mediaUploader.open();
    });

    const array_placeholder = [
        "#seopress_social_knowledge_img",
        "#seopress_social_fb_img",
        "#seopress_social_twitter_img",
    ];

    array_placeholder.forEach(function (item) {
        $(item + "_placeholder_upload").click(function (e) {
            e.preventDefault();
            $(item + "_upload").trigger('click');
        });
    });

    const array_upload = [
        "#seopress_social_knowledge_img",
        "#knowledge_img",
        "#seopress_social_fb_img",
        ".seopress_social_fb_img",
        "#seopress_social_twitter_img",
        ".seopress_social_twitter_img"
    ];

    array_upload.forEach(function (item) {
        var mediaUploader;
        $(item + "_upload").click(function (e) {
            e.preventDefault();
            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            // Extend the wp.media object
            mediaUploader = wp.media.frames.file_frame = wp.media({
                multiple: false,
            });

            // When a file is selected, grab the URL and set it as the text field's value
            mediaUploader.on("select", function () {
                attachment = mediaUploader
                    .state()
                    .get("selection")
                    .first()
                    .toJSON();
                $(item + "_meta").val(attachment.url);
                if (
                    (item == "#seopress_social_fb_img" || item == ".seopress_social_fb_img") &&
                    typeof sp_social_img != "undefined"
                ) {
                    sp_social_img("fb");
                }
                if (
                    (item == "#seopress_social_twitter_img" || item == ".seopress_social_twitter_img") &&
                    typeof sp_social_img != "undefined"
                ) {
                    sp_social_img("twitter");
                }

                if ($(item + "_attachment_id").length != 0) {
                    $(item + "_attachment_id").val(attachment.id);
                    $(item + "_width").val(attachment.width);
                    $(item + "_height").val(attachment.height);
                }
                $(item + '_src').attr('src', attachment.url);

                $(item + "_placeholder_src").attr('src', $(item + "_meta").val());
            });

            // Open the uploader dialog
            mediaUploader.open();
        });
    });

    $(".seopress-btn-upload-media").click(function (e) {
        e.preventDefault();

        var mediaUploader;

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            multiple: false,
        });

        var _self = $(this);

        mediaUploader.on("select", function () {
            attachment = mediaUploader
                .state()
                .get("selection")
                .first()
                .toJSON();

            $(_self.data("input-value")).val(attachment.url);
        });

        // Open the uploader dialog
        mediaUploader.open();
    });

    const array_remove = [
        "#seopress_social_knowledge_img",
        "#seopress_social_fb_img",
        "#seopress_social_twitter_img",
    ];

    array_remove.forEach(function (item) {
        $(item + "_remove").click(function (e) {
            e.preventDefault();

            $(item + "_meta").val('');
            $(item + "_placeholder_src").attr('src', '');
        });
    });

    const array_update = [
        "#seopress_social_knowledge_img",
        "#seopress_social_fb_img",
        "#seopress_social_twitter_img",
    ];

    array_update.forEach(function (item) {
        $(item + "_meta").on('keyup paste change click focus input mouseout', function () {
            $(item + "_placeholder_src").attr('src', $(item + "_meta").val());
        });
    });
});
