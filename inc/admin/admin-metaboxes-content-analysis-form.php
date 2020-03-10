<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );
echo '<div class="wrap-seopress-analysis">
        <p>
            '.__('Enter a few keywords for analysis to help you write optimized content.','wp-seopress').'
        </p>
        <p>
            <span class="label">Did you know?</span> '.__('Writing content for your users is the most important thing! If it doesn‘t feel natural, your visitors will leave your site, Google will know it and your ranking will be affected.','wp-seopress').'
        </p>
        <div class="col-left">
            <p>
                <label for="seopress_analysis_target_kw_meta">'. __( 'Target keywords', 'wp-seopress' ) .'
                <span class="sp-tooltip">
                    <span class="dashicons dashicons-editor-help"></span>
                    <span class="sp-tooltiptext">'.__('Separate target keywords with commas. Do not use spaces after the commas, unless you want to include them (eg: "my super keyword,another keyword,keyword")','wp-seopress').'</span>
                </span>
                </label>
                <input id="seopress_analysis_target_kw_meta" type="text" name="seopress_analysis_target_kw" placeholder="'.esc_html__('Enter your target keywords','wp-seopress').'" aria-label="'.__('Target keywords','wp-seopress').'" value="'.$seopress_analysis_target_kw.'" />
            </p>';
            if (empty($seopress_analysis_data)) {
                echo '<div id="seopress_launch_analysis" class="button" data_id="'.get_the_ID().'" data_post_type="'.get_current_screen()->post_type.'">'.__('Analyze my content','wp-seopress').'</div>';
            } else {
                echo '<div id="seopress_launch_analysis" class="button" data_id="'.get_the_ID().'" data_post_type="'.get_current_screen()->post_type.'">'.__('Refresh analysis','wp-seopress').'</div>';
            }

            echo '<br><p><span class="howto">'.__('To get the most accurate analysis, save your post first.','wp-seopress').'</span></p>';
echo    '</div>';
if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
    echo '<div class="col-right">
            <p>
                <label for="seopress_google_suggest_kw_meta">'. __( 'Google suggestions', 'wp-seopress' ) .'
                    <span class="sp-tooltip">
                        <span class="dashicons dashicons-editor-help"></span>
                        <span class="sp-tooltiptext">'.__('Enter a keyword, or a phrase, to find the top 10 Google suggestions instantly. This is useful if you want to work with the long tail technique.','wp-seopress').'</span>
                    </span>
                </label>
                <input id="seopress_google_suggest_kw_meta" type="text" name="seopress_google_suggest_kw" placeholder="Get suggestions from Google" aria-label="Google suggestions" value="">
            </p>
            <button id="seopress_get_suggestions" class="button">'.__('Get suggestions!','wp-seopress').'</button>
            ';
            echo "<ul id='seopress_suggestions'></ul>";


            if (get_locale() !='') {
                $locale = substr(get_locale(), 0, 2);
                $country_code = substr(get_locale(), -2);
            } else {
                $locale = 'en';
                $country_code = 'US';
            }

            echo "<script>
                function seopress_google_suggest(data){
                    var raw_suggestions = String(data);
                    
                    var suggestions_array = raw_suggestions.split(',');
                    
                    var i;
                    for (i = 0; i < 10; i++) { 
                        document.getElementById('seopress_suggestions').innerHTML += '<li><a href=\"#\" class=\"sp-suggest-btn button button-small\">'+suggestions_array[i]+'</a></li>';
                    }

                    jQuery('.sp-suggest-btn').click(function(e) {
                        e.preventDefault();
                        if(jQuery('#seopress_analysis_target_kw_meta').val().length == 0){
                            jQuery('#seopress_analysis_target_kw_meta').val(jQuery(this).text() + ',');
                        } else {
                            str = jQuery('#seopress_analysis_target_kw_meta').val();
                            str = str.replace(/,\s*$/, '');
                            jQuery('#seopress_analysis_target_kw_meta').val(str+','+jQuery(this).text());
                        }
                    });
                }
                jQuery('#seopress_get_suggestions').on('click', function(data) {
                    data.preventDefault();

                    document.getElementById('seopress_suggestions').innerHTML = '';
                    
                    var kws = jQuery('#seopress_google_suggest_kw_meta').val();

                    if (kws) {
                        var script = document.createElement('script');
                        script.src = 'https://www.google.com/complete/search?client=firefox&hl=".$locale."&q='+kws+'&gl=".$country_code."&callback=seopress_google_suggest';
                        document.body.appendChild(script);
                    }
                });
            </script>
        </div>";
}

//Analyzes
$analyzes = array(
    'schemas'=> array(
        'title' => __('Structured data types','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
    'old_post'=> array(
        'title' => __('Last modified date','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
    'words_counter'=> array(
        'title' => __('Words counter','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
    'keywords_density'=> array(
        'title' => __('Keywords density','wp-seopress-pro'),
        'impact' => NULL,
        'desc' => NULL
    ),
    'keywords_permalink'=> array(
        'title' => __('Keywords in permalink','wp-seopress-pro'),
        'impact' => NULL,
        'desc' => NULL
    ),
    'headings'=> array(
        'title' => __('Headings','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
    'meta_title'=> array(
        'title' => __('Meta title','wp-seopress-pro'),
        'impact' => NULL,
        'desc' => NULL
    ),
    'meta_desc'=> array(
        'title' => __('Meta description','wp-seopress-pro'),
        'impact' => NULL,
        'desc' => NULL
    ),
    'social'=> array(
        'title' => __('Social meta tags','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
    'robots'=> array(
        'title' => __('Meta robots','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
    'img_alt'=> array(
        'title' => __('Alternative texts of images','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
    'nofollow_links'=> array(
        'title' => __('NoFollow Links','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
    'outbound_links'=> array(
        'title' => __('Outbound Links','wp-seopress-pro'),
        'impact' => 'good',
        'desc' => NULL
    ),
);

//Schemas
if (!empty($seopress_analysis_data['0']['json']) && isset($seopress_analysis_data['0']['json'])) {
    $desc = '<p>'.__('We found these schemas in the source code of this page:','wp-seopress').'</p>';

    $desc .= '<ul>';
        $json_ld = array_filter($seopress_analysis_data['0']['json']);
        foreach(array_count_values($json_ld) as $key => $value) {
            $html = NULL;
            if ($value > 1) {
                $html = '<span class="impact high">'.__('duplicated schema - x','wp-seopress').$value.'</span>';
                $analyzes['schemas']['impact'] = 'high';
            }
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$key.$html.'</li>';
        }
    $desc .= '</ul>';
    $analyzes['schemas']['desc'] = $desc;
} else {
    $analyzes['schemas']['impact'] = 'medium';
    $analyzes['schemas']['desc'] = '<p>'.__('No schemas found in the source code of this page.','wp-seopress').'</p>';
}

//Old post
$desc = NULL;
if( strtotime( $post->post_modified ) < strtotime('-365 days') ) {
    $analyzes['old_post']['impact'] = 'medium';
    $desc = '<p><span class="dashicons dashicons-no-alt"></span>'.__('This post is a little old!','wp-seopress').'</p>';
} else {
    $desc = '<p><span class="dashicons dashicons-yes"></span>'.__('The last modified date of this article is less than 1 year. Cool!','wp-seopress').'</p>';
}
$desc .= '<p>'.__('Search engines love fresh content. Regularly update your articles without having to rewrite your content entirely and give them a boost in search rankings. SEOPress takes care of the technical part.','wp-seopress').'</p>';
$analyzes['old_post']['desc'] = $desc;

//Word counters
$desc = NULL;
if (isset($seopress_analysis_data['0']['words_counter']) || isset($seopress_analysis_data['0']['words_counter_unique'])) {
    $desc = '<p>'.__('Words counter is not a direct ranking factor. But, your content must be as qualitative as possible, with relevant and unique information. To fulfill these conditions, your article requires a minimum of paragraphs, so words.','wp-seopress').'</p>
    <ul>
        <li>'.$seopress_analysis_data['0']['words_counter'].' '.__('words found.','wp-seopress').'</li>
        <li>'.$seopress_analysis_data['0']['words_counter_unique'].' '.__('unique words found.','wp-seopress').'</li>';

        if ($seopress_analysis_data['0']['words_counter'] >= 299) {
            $desc .= '<li><span class="dashicons dashicons-yes"></span>'.__('Your content is composed of more than 300 words, which is the minimum for a post.','wp-seopress').'</li>';
        } else {
            $desc .= '<li><span class="dashicons dashicons-no-alt"></span>'.__('Your content is too short. Add a few more paragraphs!','wp-seopress').'</li>';
            $analyzes['words_counter']['impact'] = 'medium';
        }
    $desc .= '</ul>';

    $analyzes['words_counter']['desc'] = $desc;
} else {
    $analyzes['words_counter']['desc'] = '<p><span class="dashicons dashicons-no-alt"></span>'.__('No content? Add a few more paragraphs!','wp-seopress').'</p>';
    $analyzes['words_counter']['impact'] = 'high';
}

//Keywords density
if (!empty($seopress_analysis_data['0']['kws_density']['matches']) && isset($seopress_analysis_data['0']['words_counter'])) {
    $target_kws_density = $seopress_analysis_data['0']['kws_density']['matches'];
    
    $desc = '<ul>';
        foreach ($target_kws_density as $key => $value) {
            foreach ($value as $_key => $_value) {
                $kw_count = count($_value);
            }
            $kw_name = $key;
            $kw_density = round($kw_count/$seopress_analysis_data['0']['words_counter']*100, 2);
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times in your content, a keyword density of %s%%','wp-seopress'), $kw_name, $kw_count, $kw_density).'</li>';
        }
    $desc .= '</ul>';
    $desc .= '<p class="description">'.__('Learn more about <a href="https://www.youtube.com/watch?v=Rk4qgQdp2UA" target="_blank">keywords stuffing</a>.','wp-seopress').'</p>';
    $analyzes['keywords_density']['impact'] = 'good';
    $analyzes['keywords_density']['desc'] = $desc;
} else {
    $analyzes['keywords_density']['desc'] = '<p>'.__('We were unable to calculate the density of your keywords. You probably haven‘t added any content or your target keywords were not find in your post content.','wp-seopress').'</p>';
    $analyzes['keywords_density']['impact'] = 'high';
}

//Keywords in permalink
if (!empty($seopress_analysis_data['0']['kws_permalink']['matches'])) {
    $desc = '<p><span class="dashicons dashicons-yes"></span>'.__('Cool, one of your target keyword is used in your permalink.','wp-seopress').'</p>';

    $target_kws_permalink = $seopress_analysis_data['0']['kws_permalink']['matches'];
    
    $desc .= '<ul>';
    foreach ($target_kws_permalink as $key => $value) {
        $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$key.'</li>';
    }
    $desc .= '</ul>';
    $analyzes['keywords_permalink']['desc'] = $desc;
    $analyzes['keywords_permalink']['impact'] = 'good';
} else {
    $analyzes['keywords_permalink']['desc'] = '<p><span class="dashicons dashicons-no-alt"></span>'.__('You should add one of your target keyword in your permalink.','wp-seopress').'</p>';
    $analyzes['keywords_permalink']['impact'] = 'medium';
}

//Headings
//H1
$desc = NULL;
if (!empty($seopress_analysis_data['0']['h1']['matches'])) {
    $desc .= '<h4>'.__('H1 (Heading 1)','wp-seopress').'</h4>';

    $count = $seopress_analysis_data['0']['h1']['nomatches']['count'];

    $target_kws_h1 = $seopress_analysis_data['0']['h1']['matches'];

    $all_h1 = $seopress_analysis_data['0']['h1']['values'];

    $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in Heading 1 (H1).','wp-seopress').'</p>';
    
    $desc .= '<ul>';

    foreach ($target_kws_h1 as $key => $value) {
        foreach ($value as $_key => $_value) {
            $kw_count = count($value);
        }
        $kw_name = $key;
        $desc .= '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
    }

    $desc .= '</ul>';
    if ($count > 1) {
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %d Heading 1 (H1) in your content.','wp-seopress'), $count).'</p>';
        $desc .= '<p>'.__('You should not use more than one H1 heading in your post content. The rule is simple: only one H1 for each web page. It is better for both SEO and accessibility. Below, the list:','wp-seopress').'</p>';
        $analyzes['headings']['impact'] = 'high';
    }

    if (!empty($all_h1)) {
        $desc .= '<ul>';
        foreach($all_h1 as $h1) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$h1.'</li>';
        }
        $desc .= '</ul>';
    }
}

//H2
$desc .= '<h4>'.__('H2 (Heading 2)','wp-seopress').'</h4>';
if (!empty($seopress_analysis_data['0']['h2']['matches'])) {
    $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in Heading 2 (H2).','wp-seopress').'</p>';
    $desc .= '<ul>';
        $target_kws_h2 = $seopress_analysis_data['0']['h2']['matches'];
        foreach ($target_kws_h2 as $key => $value) {
            foreach ($value as $_key => $_value) {
                $kw_count = count($value);
            }
            $kw_name = $key;
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
        }
    $desc .= '</ul>';
} else {
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in Heading 2 (H2).','wp-seopress').'</p>';
    if ($analyzes['headings']['impact'] != 'high') {
        $analyzes['headings']['impact'] = 'medium';
    }
}

//H3
$desc .= '<h4>'.__('H3 (Heading 3)','wp-seopress').'</h4>';
if (!empty($seopress_analysis_data['0']['h3']['matches'])) {
    $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in Heading 3 (H3).','wp-seopress').'</p>';
    $desc .= '<ul>';
        $target_kws_h3 = $seopress_analysis_data['0']['h3']['matches'];
        foreach ($target_kws_h3 as $key => $value) {
            foreach ($value as $_key => $_value) {
                $kw_count = count($value);
            }
            $kw_name = $key;
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
        }
    $desc .= '</ul>';
} else {
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in Heading 3 (H3).','wp-seopress').'</p>';
    if ($analyzes['headings']['impact'] != 'high') {
        $analyzes['headings']['impact'] = 'medium';
    }
}
$analyzes['headings']['desc'] = $desc;

//Meta Title
if ($seopress_titles_title !='') {
    $desc = NULL;
    if (!empty($seopress_analysis_data['0']['meta_title']['matches'])) {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in the Meta Title.','wp-seopress').'</p>';
        $desc .= '<ul>';
            $target_kws_title = $seopress_analysis_data['0']['meta_title']['matches'];
            foreach ($target_kws_title as $key => $value) {
                foreach ($value as $_key => $_value) {
                    $kw_count = count($_value);
                }
                $kw_name = $key;
                $desc .= '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
            }
        $desc .= '</ul>';
        $analyzes['meta_title']['impact'] = 'good';
    } else {
        $analyzes['meta_title']['impact'] = 'medium';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in the Meta Title.','wp-seopress').'</p>';
    }

    if (mb_strlen($seopress_titles_title) > 65 ) {
        $analyzes['meta_title']['impact'] = 'medium';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your custom title is too long.','wp-seopress').'</p>'; 
    } else {
        $analyzes['meta_title']['impact'] = 'good';
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('The length of your title is correct','wp-seopress').'</p>';
    }
    $analyzes['meta_title']['desc'] = $desc;
} else {
    $analyzes['meta_title']['impact'] = 'medium';
    $analyzes['meta_title']['desc'] = '<p><span class="dashicons dashicons-no-alt"></span>'.__('No custom title is set for this post. If the global meta title suits you, you can ignore this recommendation.','wp-seopress').'</p>';
}

//Meta description
if ($seopress_titles_desc !='') {
    $desc = NULL;
    if (!empty($seopress_analysis_data['0']['meta_description']['matches'])) {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in the Meta description.','wp-seopress').'</p>';
        $desc .= '<ul>';
            $target_kws_desc = $seopress_analysis_data['0']['meta_description']['matches'];
            foreach ($target_kws_desc as $key => $value) {
                foreach ($value as $_key => $_value) {
                    $kw_count = count($_value);
                }
                $kw_name = $key;
                $desc .= '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
            }
        $desc .= '</ul>';
        $analyzes['meta_desc']['impact'] = 'good';
    } else {
        $analyzes['meta_desc']['impact'] = 'medium';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in the Meta description.','wp-seopress').'</p>';
    }

    if (mb_strlen($seopress_titles_desc) > 160 ) {
        $analyzes['meta_desc']['impact'] = 'medium';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('You custom meta description is too long.','wp-seopress').'</p>'; 
    } else {
        $analyzes['meta_desc']['impact'] = 'good';
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('The length of your meta description is correct','wp-seopress').'</p>';
    }
    $analyzes['meta_desc']['desc'] = $desc;
} else {
    $analyzes['meta_desc']['impact'] = 'medium';
    $analyzes['meta_desc']['desc'] = '<p><span class="dashicons dashicons-no-alt"></span>'.__('No custom meta description is set for this post. If the global meta description suits you, you can ignore this recommendation.','wp-seopress').'</p>';
}

//Social tags
//og:title
$desc = NULL;

$desc .= '<h4>'.__('Open Graph Title','wp-seopress').'</h4>';

if (!empty($seopress_analysis_data['0']['og_title']['count'])) {
    $count = $seopress_analysis_data['0']['og_title']['count'];

    $all_og_title = $seopress_analysis_data['0']['og_title']['values'];

    if ($count > 1) {
        $analyzes['social']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %d og:title in your content.','wp-seopress'), $count).'</p>';
        $desc .= '<p>'.__('You should not use more than one og:title in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:title tag from your source code. Below, the list:','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('We found an Open Graph Title tag in your source code.','wp-seopress').'</p>';
    }

    if (!empty($all_og_title)) {
        $desc .= '<ul>';
        foreach($all_og_title as $og_title) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$og_title.'</li>';
        }
        $desc .= '</ul>';
    }
} else {
    $analyzes['social']['impact'] = 'high';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your Open Graph Title is missing!','wp-seopress').'</p>';
}

//og:description
$desc .= '<h4>'.__('Open Graph Description','wp-seopress').'</h4>';

if (!empty($seopress_analysis_data['0']['og_desc']['count'])) {

    $count = $seopress_analysis_data['0']['og_desc']['count'];

    $all_og_desc = $seopress_analysis_data['0']['og_desc']['values'];

    if ($count > 1) {
        $analyzes['social']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %d og:description in your content.','wp-seopress'), $count).'</p>';
        $desc .= '<p>'.__('You should not use more than one og:description in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:description tag from your source code. Below, the list:','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('We found an Open Graph Description tag in your source code.','wp-seopress').'</p>';
    }

    if (!empty($all_og_desc)) {
        $desc .= '<ul>';
        foreach($all_og_desc as $og_desc) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$og_desc.'</li>';
        }
        $desc .= '</ul>';
    }
} else {
    $analyzes['social']['impact'] = 'high';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your Open Graph Description is missing!','wp-seopress').'</p>';
}

//og:image
$desc .= '<h4>'.__('Open Graph Image','wp-seopress').'</h4>';

if (!empty($seopress_analysis_data['0']['og_img']['count'])) {

    $count = $seopress_analysis_data['0']['og_img']['count'];

    $all_og_img = $seopress_analysis_data['0']['og_img']['values'];

    if ($count > 0) {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.sprintf(esc_html__('We found %d og:image in your content.','wp-seopress'), $count).'</p>';
    }

    if (!empty($all_og_img)) {
        $desc .= '<ul>';
        foreach($all_og_img as $og_img) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$og_img.'</li>';
        }
        $desc .= '</ul>';
    }
} else {
    $analyzes['social']['impact'] = 'high';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your Open Graph Image is missing!','wp-seopress').'</p>';
}

//og:url
$desc .= '<h4>'.__('Open Graph URL','wp-seopress').'</h4>';

if (!empty($seopress_analysis_data['0']['og_url']['count'])) {

    $count = $seopress_analysis_data['0']['og_url']['count'];

    $all_og_url = $seopress_analysis_data['0']['og_url']['values'];

    if ($count > 1) {
        $analyzes['social']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %d og:url in your content.','wp-seopress'), $count).'</p>';
        $desc .= '<p>'.__('You should not use more than one og:url in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:url tag from your source code. Below, the list:','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('We found an Open Graph URL tag in your source code.','wp-seopress').'</p>';
    }

    if (!empty($all_og_url)) {
        $desc .= '<ul>';
        foreach($all_og_url as $og_url) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$og_url.'</li>';
        }
        $desc .= '</ul>';
    }
} else {
    $analyzes['social']['impact'] = 'high';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your Open Graph URL is missing!','wp-seopress').'</p>';
}

//og:site_name
$desc .= '<h4>'.__('Open Graph Site Name','wp-seopress').'</h4>';

if (!empty($seopress_analysis_data['0']['og_site_name']['count'])) {

    $count = $seopress_analysis_data['0']['og_site_name']['count'];

    $all_og_site_name = $seopress_analysis_data['0']['og_site_name']['values'];

    if ($count > 1) {
        $analyzes['social']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %d og:site_name in your content.','wp-seopress'), $count).'</p>';
        $desc .= '<p>'.__('You should not use more than one og:site_name in your post content to avoid conflicts when sharing on social networks. Facebook will take the last og:site_name tag from your source code. Below, the list:','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('We found an Open Graph Site Name tag in your source code.','wp-seopress').'</p>';
    }

    if (!empty($all_og_site_name)) {
        $desc .= '<ul>';
        foreach($all_og_site_name as $og_site_name) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$og_site_name.'</li>';
        }
        $desc .= '</ul>';
    }
} else {
    $analyzes['social']['impact'] = 'high';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your Open Graph Site Name is missing!','wp-seopress').'</p>';
}

//twitter:title
$desc .= '<h4>'.__('Twitter Title','wp-seopress').'</h4>';

if (!empty($seopress_analysis_data['0']['tw_title']['count'])) {

    $count = $seopress_analysis_data['0']['tw_title']['count'];

    $all_tw_title = $seopress_analysis_data['0']['tw_title']['values'];

    if ($count > 1) {
        $analyzes['social']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %d twitter:title in your content.','wp-seopress'), $count).'</p>';
        $desc .= '<p>'.__('You should not use more than one twitter:title in your post content to avoid conflicts when sharing on social networks. Twitter will take the last twitter:title tag from your source code. Below, the list:','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('We found a Twitter Title Tag in your source code.','wp-seopress').'</p>';
    }

    if (!empty($all_tw_title)) {
        $desc .= '<ul>';
        foreach($all_tw_title as $tw_title) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$tw_title.'</li>';
        }
        $desc .= '</ul>';
    }
} else {
    $analyzes['social']['impact'] = 'high';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your Twitter Title is missing!','wp-seopress').'</p>';
}

//twitter:description
$desc .= '<h4>'.__('Twitter Description','wp-seopress').'</h4>';

if (!empty($seopress_analysis_data['0']['tw_desc']['count'])) {

    $count = $seopress_analysis_data['0']['tw_desc']['count'];

    $all_tw_desc = $seopress_analysis_data['0']['tw_desc']['values'];

    if ($count > 1) {
        $analyzes['social']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %d twitter:description in your content.','wp-seopress'), $count).'</p>';
        $desc .= '<p>'.__('You should not use more than one twitter:description in your post content to avoid conflicts when sharing on social networks. Twitter will take the last twitter:description tag from your source code. Below, the list:','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('We found a Twitter Description tag in your source code.','wp-seopress').'</p>';
    }

    if (!empty($all_tw_desc)) {
        $desc .= '<ul>';
        foreach($all_tw_desc as $tw_desc) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$tw_desc.'</li>';
        }
        $desc .= '</ul>';
    }
} else {
    $analyzes['social']['impact'] = 'high';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your Twitter Description is missing!','wp-seopress').'</p>';
}

//twitter:image
$desc .= '<h4>'.__('Twitter Image','wp-seopress').'</h4>';

if (!empty($seopress_analysis_data['0']['tw_img']['count'])) {

    $count = $seopress_analysis_data['0']['tw_img']['count'];

    $all_tw_img = $seopress_analysis_data['0']['tw_img']['values'];

    if ($count > 0) {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.sprintf(esc_html__('We found %d twitter:image in your content.','wp-seopress'), $count).'</p>';
    }

    if (!empty($all_tw_img)) {
        $desc .= '<ul>';
        foreach($all_tw_img as $tw_img) {
            $desc .= '<li><span class="dashicons dashicons-minus"></span>'.$tw_img.'</li>';
        }
        $desc .= '</ul>';
    }
} else {
    $analyzes['social']['impact'] = 'high';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your Twitter Image is missing!','wp-seopress').'</p>';
}
$analyzes['social']['desc'] = $desc;

//Robots
$desc = NULL;
if (!empty($seopress_analysis_data['0']['meta_robots'])) {

    $meta_robots = $seopress_analysis_data['0']['meta_robots']['0'];
    
    if (count($seopress_analysis_data['0']['meta_robots']) > 1) {
        $analyzes['robots']['impact'] = 'high';

        $count_meta_robots = count($seopress_analysis_data['0']['meta_robots']);

        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %s meta robots in your page. There is probably something wrong with your theme!','wp-seopress'), $count_meta_robots).'</p>';
    }

    if (preg_match('/noindex/', json_encode($meta_robots))) {
        $analyzes['robots']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('noindex is on! Search engines can\'t index this page.','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('noindex is off. Search engines will index this page.','wp-seopress').'</p>';
    }

    if (preg_match('/nofollow/', json_encode($meta_robots))) {
        $analyzes['robots']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('nofollow is on! Search engines can\'t follow your links on this page.','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('nofollow is off. Search engines will follow links on this page.','wp-seopress').'</p>';
    }

    if (preg_match('/noarchive/', json_encode($meta_robots))) {
        if ($analyzes['robots']['impact'] != 'high') {
            $analyzes['robots']['impact'] = 'medium';
        }
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('noarchive is on! Search engines will not cache your page.','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('noarchive is off. Search engines will probably cache your page.','wp-seopress').'</p>';
    }

    if (preg_match('/nosnippet/', json_encode($meta_robots))) {
        if ($analyzes['robots']['impact'] != 'high') {
            $analyzes['robots']['impact'] = 'medium';
        }
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('nosnippet is on! Search engines will not display a snippet of this page in search results.','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('nosnippet is off. Search engines will display a snippet of this page in search results.','wp-seopress').'</p>';
    }
} else {
    $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('We found no meta robots on this page. It means, your page is index,follow. Search engines will index it, and follow links. ','wp-seopress').'</p>';
}

//Meta Google
if (!empty($seopress_analysis_data['0']['meta_google'])) {
    $meta_google = $seopress_analysis_data['0']['meta_google'];

    if (preg_match('/noimageindex/', json_encode($meta_google))) {
        $analyzes['robots']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('noimageindex is on! Google will not index your images on this page (but if someone makes a direct link to one of your image in this page, it will be indexed).','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('noimageindex is off. Google will index the images on this page.','wp-seopress').'</p>';
    }

    if (preg_match('/nositelinkssearchbox/', json_encode($meta_google))) {
        if ($analyzes['robots']['impact'] != 'high') {
            $analyzes['robots']['impact'] = 'medium';
        }
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('nositelinkssearchbox is on! Google will not display a sitelinks searchbox in search results.','wp-seopress').'</p>';
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('nositelinkssearchbox is off. Google will probably display a sitelinks searchbox in search results.','wp-seopress').'</p>';
    }
} else {
    $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('noimageindex is off. Google will index the images on this page.','wp-seopress').'</p>';
    
    $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('nositelinkssearchbox is off. Google will probably display a sitelinks searchbox in search results.','wp-seopress').'</p>';
}

$analyzes['robots']['desc'] = $desc;

//Img alt
if (!empty($seopress_analysis_data['0']['img'])) {
    $images = isset($seopress_analysis_data['0']['img']['images']) ? $seopress_analysis_data['0']['img']['images'] : NULL;

    $desc = '<div class="wrap-analysis-img">';

    if (isset($images) && !empty($images)) {
        $analyzes['img_alt']['impact'] = 'high';
        $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('No alternative text found for these images. Alt tags are important for both SEO and accessibility. Edit your images using the media library or your favorite page builder and fill in alternative text fields.','wp-seopress').'</p>';
    
        //Standard images & galleries
        if (isset($images) && !empty($images)) {
            $desc .= '<ul class="attachments">';
                foreach($images as $img) {
                    $desc .= '<li class="attachment"><img src="'.$img.'"/></li>';
                }
            $desc .= '</ul>';
        }
    } else {
        $desc .= '<p><span class="dashicons dashicons-yes"></span>'.__('All alternative tags are filled in. Good work!','wp-seopress').'</p>';
    }
    $desc .= '</div>';

    $analyzes['img_alt']['desc'] = $desc;
} else {
    $analyzes['img_alt']['impact'] = 'medium';
    $analyzes['img_alt']['desc'] = '<p><span class="dashicons dashicons-no-alt"></span>'.__('We could not find any image in your content. Content with media is a plus for your SEO.','wp-seopress').'</p>';
}

//Nofollow links
if (!empty($seopress_analysis_data['0']['nofollow_links'])) {
    $count = count($seopress_analysis_data['0']['nofollow_links']);
    
    $desc = '<p>'.sprintf( esc_html__( 'We found %d links with nofollow attribute in your page. Do not overuse nofollow attribute in links. Below, the list:', 'wp-seopress' ), $count ).'</p>';
    $desc .= '<ul>';
        foreach ($seopress_analysis_data['0']['nofollow_links'] as $links) {
            foreach ($links as $href => $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="'.$href.'" target="_blank">'.$link.'</a><span class="dashicons dashicons-external"></span></li>';
            }
        }
    $desc .= '</ul>';
    $analyzes['nofollow_links']['impact'] = 'low';
    $analyzes['nofollow_links']['desc'] = $desc;
} else {
    $analyzes['nofollow_links']['desc'] = '<p><span class="dashicons dashicons-yes"></span>'.__('This page doesn\'t have any nofollow links.','wp-seopress').'</p>';
}

//Outbound links
$desc = '<p>'.__('Internet is built on the principle of hyperlink. It is therefore perfectly normal to make links between different websites. However, avoid making links to low quality sites, SPAM... If you are not sure about the quality of a site, add the attribute "nofollow" to your link.').'</p>';
if (!empty($seopress_analysis_data['0']['outbound_links'])) {
    $count = count($seopress_analysis_data['0']['outbound_links']);

    $desc .= '<p>'.sprintf( __('We found %s outbound links in your page. Below, the list:', 'wp-seopress'), $count ).'</p>';
    $desc .= '<ul>';
        foreach ($seopress_analysis_data['0']['outbound_links'] as $links) {
            foreach ($links as $href => $link) {
                $desc .= '<li><span class="dashicons dashicons-minus"></span><a href="'.$href.'" target="_blank">'.$link.'</a><span class="dashicons dashicons-external"></span></li>';
            }
        }
    $desc .= '</ul>';
} else {
    $analyzes['outbound_links']['impact'] = 'medium';
    $desc .= '<p><span class="dashicons dashicons-no-alt"></span>'.__('This page doesn\'t have any outbound links.','wp-seopress').'</p>';
}
$analyzes['outbound_links']['desc'] = $desc;

        echo '<div id="seopress-analysis-tabs">
                <div id="seopress-analysis-tabs-1">
                    <div class="analysis-score">';
                        $impact = array_unique(array_values(wp_list_pluck($analyzes, 'impact')));
                        $svg = '<svg role="img" aria-hidden="true" focusable="false" width="100%" height="100%" viewBox="0 0 200 200" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <circle r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0"></circle>
                                    <circle id="bar" r="90" cx="100" cy="100" fill="transparent" stroke-dasharray="565.48" stroke-dashoffset="0" style="stroke-dashoffset: 101.788px;"></circle>
                                </svg>';
                        $tooltip = '<span class="sp-tooltip">
                        <span class="dashicons dashicons-editor-help"></span>
                        <span class="sp-tooltiptext">'.__('<strong>Should be improved:</strong> red or orange dots <br> <strong>Good:</strong> yellow or green dots','wp-seopress').'</span>
                    </span>';
                        if (in_array('medium', $impact) || in_array('high', $impact)) {
                            echo '<p class="notgood">'.$svg.'<span>'.__('Should be improved','wp-seopress').$tooltip.'</span></p>';
                            $seopress_analysis_data['0']['score'] = false;
                        } else {
                            echo '<p class="good">'.$svg.'<span>'.__('Good','wp-seopress').$tooltip.'</span></p>';
                            $seopress_analysis_data['0']['score'] = true;
                        }
                        if (!empty($seopress_analysis_data)) {
                            update_post_meta(get_the_ID(), '_seopress_analysis_data', $seopress_analysis_data);
                        }
                        echo '<span><a href="#" id="expand-all">'.__('Expand','wp-seopress').'</a> / <a href="#" id="close-all">'.__('Close','wp-seopress').'</a></span>';
                    echo '</div>';

                    if(!empty($analyzes)) {
                        $order = array('1'=>'high','2'=>'medium','3'=>'low','4'=>'good');

                        usort($analyzes, function ($a, $b) use ($order) {
                            $pos_a = array_search($a['impact'], $order);
                            $pos_b = array_search($b['impact'], $order);
                            return $pos_a - $pos_b;
                         });

                        foreach($analyzes as $key => $value) {
                            echo '<div class="gr-analysis">';
                                if (isset($value['title'])) {
                                    echo '<div class="gr-analysis-title">
                                            <h3>
                                                <button type="button" aria-expanded="true" class="btn-toggle">';
                                                if (isset($value['impact'])) {
                                                    echo '<span class="impact '.$value['impact'].'" aria-hidden="true"></span>';
                                                }
                                                echo '<span class="sp-arrow" aria-hidden="true"></span>
                                                    '.$value['title'].'
                                                </button>
                                            </h3>
                                        </div>';
                                }
                                if (isset($value['desc'])) {
                                    echo '<div class="gr-analysis-content">';
                                        echo $value['desc'];
                                    echo '</div>';
                                }
                            echo '</div>';
                        }
                    }
            echo '</div>';
    echo '</div>
    </div>';