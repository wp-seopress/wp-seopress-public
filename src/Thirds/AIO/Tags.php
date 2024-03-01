<?php

namespace SEOPress\Thirds\AIO;

defined('ABSPATH') or exit('Cheatin&#8217; uh?');

use SEOPress\Helpers\TagCompose;

class Tags {
    protected $variables = [
        '#separator_sa'                                     => 'sep',
        '#site_title'                                       => 'sitetitle',
        '#tagline'                                          => 'tagline',
        '#post_title'                                       => 'post_title',
        '#author_first_name'                                => 'author_first_name',
        '#author_last_name'                                 => 'author_last_name',
        '#author_last_name'                                 => 'author_last_name',
        '#author_name'                                      => 'post_author',
        '#taxonomy_title'                                   => 'term_title',
        '#current_date'                                     => 'currentdate',
        '#current_month'                                    => 'currentmonth',
        '#current_day'                                      => 'currentday',
        '#current_year'                                     => 'currentyear',
        '#permalink'                                        => 'post_url',
        '#post_content'                                     => 'post_content',
        '#post_excerpt_only'                                => 'post_excerpt',
        '#post_excerpt'                                     => 'post_excerpt',
        '#post_date'                                        => 'post_date',
        '#post_day'                                         => '',
        '#post_month'                                       => '',
        '#post_year'                                        => '',
        '#custom_field-'                                    => '_cf_',
        '#tax_name-'                                        => '_ct_',
    ];

    /**
     * @since 5.5.0
     *
     * @param string $input
     *
     * @return string
     */
    public function replaceTags($input) {
        foreach ($this->variables as $key => $value) {
            if ( ! empty($value)) {

                if ( $key === '#custom_field-') {
                    preg_match_all('/#custom_field-[^\s]*/', $input, $matches);

                    if ( ! empty($matches[0])) {
                        foreach($matches[0] as $_key => $_value) {
                            $new_tag = str_replace('#custom_field-', '_cf_', $_value);
                            $new_tag = TagCompose::getValueWithTag($new_tag);
                            $input = str_replace($_value, $new_tag, $input);
                        }
                    }
                } elseif ( $key === '#tax_name-') {
                    preg_match_all('/#tax_name-[^\s]*/', $input, $matches);

                    if ( ! empty($matches[0])) {
                        foreach($matches[0] as $_key => $_value) {
                            $new_tag = str_replace('#tax_name-', '_ct_', $_value);
                            $new_tag = TagCompose::getValueWithTag($new_tag);
                            $input = str_replace($_value, $new_tag, $input);
                        }
                    }
                } else {
                    $value = TagCompose::getValueWithTag($value);
                }
            }
            if ( $key !== '#custom_field-' || $key !=='#tax_name-') {
                $input = str_replace($key, $value, $input);
            }
        }

        return $input;
    }
}
