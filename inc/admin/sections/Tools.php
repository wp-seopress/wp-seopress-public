<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_tools_compatibility()
{
    $docs = seopress_get_docs_links(); ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Compatibility Center', 'wp-seopress'); ?>
    </h2>
</div>
<p>
    <?php _e('Our <strong>Compatibility Center</strong> makes it easy to integrate SEOPress with your favorite tools.', 'wp-seopress'); ?>
</p>

<p>
    <?php _e('Even though a lot of things are completely transparent to you and automated, sometimes it is necessary to leave the final choice to you.', 'wp-seopress'); ?>
</p>

<div class="seopress-notice is-warning">
    <p>
        <?php _e('<strong>Warning</strong>: always test your site after activating one of these options. Running shortcodes to automatically generate meta title / description can have side effects. Clear your cache if necessary.', 'wp-seopress'); ?>
        <?php echo seopress_tooltip_link($docs['compatibility']['automatic'], __('Learn more about automatic meta descriptions', 'wp-seopress-pro')); ?>
    </p>
</div>


<?php
}
