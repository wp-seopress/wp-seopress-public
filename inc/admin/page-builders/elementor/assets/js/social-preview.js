jQuery(document).ready(function() {
    jQuery(document).on('click', '#seopress-seo-tab', function(e) {
        jQuery("#elementor-panel-footer-settings").trigger('click');
        jQuery(".elementor-control-seopress_title_settings").trigger('click');
    });
});

var socialPreviewItemView = elementor.modules.controls.BaseData.extend({
    onReady: function () {
        var $ = jQuery;
        
        const updateFbSnippetImage = function() {
            $('.snippet-fb-img-default img').attr('src', $(this).val());
        }

        const updateFbSnippetTitle = function() {
            $('.snippet-fb-title').html($(this).val());
        }

        const updateFbSnippetDesc = function() {
            $('.snippet-fb-description-custom').html($(this).val());
        }

        const updateTwSnippetImage = function() {
            $('.snippet-twitter-img-default img').attr('src', $(this).val());
        }

        const updateTwSnippetTitle = function() {
            $('.snippet-twitter-title').html($(this).val());
        }

        const updateTwSnippetDesc = function() {
            $('.snippet-twitter-description').html($(this).val());
        }

        $(document).on('input', 'input[data-setting=_seopress_social_fb_img]', updateTwSnippetImage);
        $(document).on('input', 'input[data-setting=_seopress_social_fb_title]', updateFbSnippetTitle);
        $(document).on('input', 'textarea[data-setting=_seopress_social_fb_desc]', updateFbSnippetDesc);

        $(document).on('input', 'input[data-setting=_seopress_social_twitter_img]', updateTwSnippetImage);
        $(document).on('input', 'input[data-setting=_seopress_social_twitter_title]', updateTwSnippetTitle);
        $(document).on('input', 'textarea[data-setting=_seopress_social_twitter_desc]', updateTwSnippetDesc);
    },
});

elementor.addControlView('seopress-social-preview', socialPreviewItemView);