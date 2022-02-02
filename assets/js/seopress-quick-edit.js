(function ($) {

    // we create a copy of the WP inline edit post function
    var $wp_inline_edit = inlineEditPost.edit;

    // and then we overwrite the function with our own code
    inlineEditPost.edit = function (id) {

        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        $wp_inline_edit.apply(this, arguments);

        // get the post ID
        var $post_id = 0;
        if (typeof (id) == 'object') {
            $post_id = parseInt(this.getId(id));
        }

        if ($post_id > 0) {
            // define the edit row
            var $edit_row = $('#edit-' + $post_id);
            var $post_row = $('#post-' + $post_id);

            // get the data
            var $seopress_title = $('.column-seopress_title', $post_row).text();
            var $seopress_desc = $('.column-seopress_desc', $post_row).text();
            var $seopress_tkw = $('.column-seopress_tkw', $post_row).text();
            var $seopress_canonical = $('.column-seopress_canonical', $post_row).text();
            var $seopress_noindex = $('.column-seopress_noindex', $post_row).html();
            var $seopress_nofollow = $('.column-seopress_nofollow', $post_row).html();
            var $seopress_redirections_enable = $('.column-seopress_404_redirect_enable', $post_row).html();
            var $seopress_redirections_regex_enable = $('.column-seopress_404_redirect_regex_enable', $post_row).html();
            var $seopress_redirections_type = $('.column-seopress_404_redirect_type', $post_row).text();
            var $seopress_redirections_value = $('.column-seopress_404_redirect_value', $post_row).text();

            // populate the data
            $(':input[name="seopress_title"]', $edit_row).val($seopress_title);
            $(':input[name="seopress_desc"]', $edit_row).val($seopress_desc);
            $(':input[name="seopress_tkw"]', $edit_row).val($seopress_tkw);
            $(':input[name="seopress_canonical"]', $edit_row).val($seopress_canonical);

            if ($seopress_noindex && $seopress_noindex.includes('<span class="dashicons dashicons-hidden"></span>')) {
                $(':input[name="seopress_noindex"]', $edit_row).attr('checked', 'checked');
            }

            if ($seopress_nofollow && $seopress_nofollow.includes('<span class="dashicons dashicons-yes"></span>')) {
                $(':input[name="seopress_nofollow"]', $edit_row).attr('checked', 'checked');
            }

            if ($seopress_redirections_enable && $seopress_redirections_enable == '<span class="dashicons dashicons-yes"></span>') {
                $(':input[name="seopress_redirections_enabled"]', $edit_row).attr('checked', 'checked');
            }
            if ($seopress_redirections_regex_enable && $seopress_redirections_regex_enable == '<span class="dashicons dashicons-yes"></span>') {
                $(':input[name="seopress_redirections_enabled_regex"]', $edit_row).attr('checked', 'checked');
            }
            if ($seopress_redirections_type && $seopress_redirections_type != '404') {
                $('select[name="seopress_redirections_type"] option[value="' + $seopress_redirections_type + '"]', $edit_row).attr('selected', 'selected');
            }
            $(':input[name="seopress_redirections_value"]', $edit_row).val($seopress_redirections_value);
        }
    };

})(jQuery);
