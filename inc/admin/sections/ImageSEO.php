<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

function print_section_info_advanced_image()
{
    ?>
<div class="sp-section-header">
    <h2>
        <?php _e('Image SEO', 'wp-seopress'); ?>
    </h2>
</div>
<p><?php _e('Images can generate a lot of traffic to your site. Make sure to always add alternative texts, optimize their file size, filename etc.', 'wp-seopress'); ?>
</p>

<?php
}
