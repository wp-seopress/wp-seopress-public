<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

register_setting(
    'seopress_option_group', // Option group
    'seopress_option_name', // Option name
    'sanitize' // Sanitize
);

register_setting(
    'seopress_titles_option_group', // Option group
    'seopress_titles_option_name', // Option name
    'sanitize' // Sanitize
);

register_setting(
    'seopress_xml_sitemap_option_group', // Option group
    'seopress_xml_sitemap_option_name', // Option name
    'sanitize' // Sanitize
);

register_setting(
    'seopress_social_option_group', // Option group
    'seopress_social_option_name', // Option name
    'sanitize' // Sanitize
);

register_setting(
    'seopress_google_analytics_option_group', // Option group
    'seopress_google_analytics_option_name', // Option name
    'sanitize' // Sanitize
);

register_setting(
    'seopress_advanced_option_group', // Option group
    'seopress_advanced_option_name', // Option name
    'sanitize' // Sanitize
);

register_setting(
    'seopress_tools_option_group', // Option group
    'seopress_tools_option_name', // Option name
    'sanitize' // Sanitize
);

register_setting(
    'seopress_import_export_option_group', // Option group
    'seopress_import_export_option_name', // Option name
    'sanitize' // Sanitize
);
