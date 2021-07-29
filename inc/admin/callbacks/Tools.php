<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

 function seopress_setting_section_tools_compatibility_oxygen_callback() {
     $options = get_option('seopress_tools_option_name');

     $check = isset($options['seopress_setting_section_tools_compatibility_oxygen']);

     echo '<input id="seopress_setting_section_tools_compatibility_oxygen" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_oxygen]" type="checkbox"';
     if ('1' == $check) {
         echo 'checked="yes"';
     }
     echo ' value="1"/>';

     echo '<label for="seopress_setting_section_tools_compatibility_oxygen">' . __('Enable Oxygen Builder compatibility', 'wp-seopress') . '</label>';

     echo '<p class="description">' . __('Enable automatic meta description with <strong>%%oxygen%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

     if (isset($options['seopress_setting_section_tools_compatibility_oxygen'])) {
         esc_attr($options['seopress_setting_section_tools_compatibility_oxygen']);
     }
 }

 function seopress_setting_section_tools_compatibility_divi_callback() {
     $options = get_option('seopress_tools_option_name');

     $check = isset($options['seopress_setting_section_tools_compatibility_divi']);

     echo '<input id="seopress_setting_section_tools_compatibility_divi" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_divi]" type="checkbox"';
     if ('1' == $check) {
         echo 'checked="yes"';
     }
     echo ' value="1"/>';

     echo '<label for="seopress_setting_section_tools_compatibility_divi">' . __('Enable Divi Builder compatibility', 'wp-seopress') . '</label>';

     echo '<p class="description">' . __('Enable automatic meta description with <strong>%%divi%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

     if (isset($options['seopress_setting_section_tools_compatibility_divi'])) {
         esc_attr($options['seopress_setting_section_tools_compatibility_divi']);
     }
 }

 function seopress_setting_section_tools_compatibility_bakery_callback() {
     $options = get_option('seopress_tools_option_name');

     $check = isset($options['seopress_setting_section_tools_compatibility_bakery']);

     echo '<input id="seopress_setting_section_tools_compatibility_bakery" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_bakery]" type="checkbox"';
     if ('1' == $check) {
         echo 'checked="yes"';
     }
     echo ' value="1"/>';

     echo '<label for="seopress_setting_section_tools_compatibility_bakery">' . __('Enable WP Bakery Builder compatibility', 'wp-seopress') . '</label>';

     echo '<p class="description">' . __('Enable automatic meta description with <strong>%%wpbakery%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

     if (isset($options['seopress_setting_section_tools_compatibility_bakery'])) {
         esc_attr($options['seopress_setting_section_tools_compatibility_bakery']);
     }
 }

 function seopress_setting_section_tools_compatibility_avia_callback() {
     $options = get_option('seopress_tools_option_name');

     $check = isset($options['seopress_setting_section_tools_compatibility_avia']);

     echo '<input id="seopress_setting_section_tools_compatibility_avia" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_avia]" type="checkbox"';
     if ('1' == $check) {
         echo 'checked="yes"';
     }
     echo ' value="1"/>';

     echo '<label for="seopress_setting_section_tools_compatibility_avia">' . __('Enable Avia Layout Builder compatibility', 'wp-seopress') . '</label>';

     echo '<p class="description">' . __('Enable automatic meta description with <strong>%%aviabuilder%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

     if (isset($options['seopress_setting_section_tools_compatibility_avia'])) {
         esc_attr($options['seopress_setting_section_tools_compatibility_avia']);
     }
 }

 function seopress_setting_section_tools_compatibility_fusion_callback() {
     $options = get_option('seopress_tools_option_name');

     $check = isset($options['seopress_setting_section_tools_compatibility_fusion']);

     echo '<input id="seopress_setting_section_tools_compatibility_fusion" name="seopress_tools_option_name[seopress_setting_section_tools_compatibility_fusion]" type="checkbox"';
     if ('1' == $check) {
         echo 'checked="yes"';
     }
     echo ' value="1"/>';

     echo '<label for="seopress_setting_section_tools_compatibility_fusion">' . __('Enable Fusion Builder compatibility', 'wp-seopress') . '</label>';

     echo '<p class="description">' . __('Enable automatic meta description with <strong>%%fusionbuilder%%</strong> dynamic variable.', 'wp-seopress') . '</p>';

     if (isset($options['seopress_setting_section_tools_compatibility_fusion'])) {
         esc_attr($options['seopress_setting_section_tools_compatibility_fusion']);
     }
 }
