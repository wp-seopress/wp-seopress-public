<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );
echo '<div class="wrap-seopress-analysis">
        <p>
            '.__('Enter a few keywords for analysis to help you write optimized content.','wp-seopress').'
        </p>
        <div class="col-left">
            <p>
                <label for="seopress_analysis_target_kw_meta">'. __( 'Target keywords', 'wp-seopress' ) .'</label>
                <input id="seopress_analysis_target_kw_meta" type="text" name="seopress_analysis_target_kw" placeholder="'.esc_html__('Enter your target keywords','wp-seopress').'" aria-label="'.__('Target keywords','wp-seopress').'" value="'.$seopress_analysis_target_kw.'" />
                <span class="howto">'.__('Separate target keywords with commas. Do not use spaces after the commas, unless you want to include them.','wp-seopress').'</span>
            </p>';
            if (empty($seopress_analysis_data)) {
                echo '<div id="seopress_launch_analysis" class="button" data_id="'.get_the_ID().'" data_post_type="'.get_current_screen()->post_type.'">'.__('Analyze my content','wp-seopress').'</div>';
            } else {
                echo '<div id="seopress_launch_analysis" class="button" data_id="'.get_the_ID().'" data_post_type="'.get_current_screen()->post_type.'">'.__('Refresh analysis','wp-seopress').'</div>';
            }
            echo '<div id="seopress_analysis_results_state" style="display:none"><span class="dashicons dashicons-yes"></span>'.__('Analysis completed','wp-seopress').'</div>';

            echo '<br><p><span class="howto">'.__('To get the most accurate analysis, save your post first.','wp-seopress').'</span></p>';
echo    '</div>';
if ( is_plugin_active( 'wp-seopress-pro/seopress-pro.php' ) ) {
    echo '<div class="col-right">
            <label for="seopress_google_suggest_kw_meta">'. __( 'Google suggestions', 'wp-seopress' ) .'</label>
            <input id="seopress_google_suggest_kw_meta" type="text" name="seopress_google_suggest_kw" placeholder="Get suggestions from Google" aria-label="Google suggestions" value="">
            <span class="howto">'.__('Enter a keyword, or a phrase, to find the top 10 Google suggestions instantly. This is useful if you want to work with the long tail technique.','wp-seopress').'</span>
            <br>
            <button id="seopress_get_suggestions" class="button">'.__('Get suggestions!','wp-seopress').'</button>
            ';
            echo "<ul id='seopress_suggestions'></ul>";

            if (get_locale() !='') {
                $locale = substr(get_locale(), 0, 2);
            } else {
                $locale = 'en';
            }

            echo "<script>
                function seopress_google_suggest(data){
                    var raw_suggestions = String(data);
                    
                    var suggestions_array = raw_suggestions.split(',');
                    
                    var i;
                    for (i = 0; i < suggestions_array.length; i++) { 
                        document.getElementById('seopress_suggestions').innerHTML += '<li>'+suggestions_array[i]+'</li>';
                    }
                }
                jQuery('#seopress_get_suggestions').on('click', function(data) {
                    data.preventDefault();

                    document.getElementById('seopress_suggestions').innerHTML = '';
                    
                    var kws = jQuery('#seopress_google_suggest_kw_meta').val();

                    if (kws) {
                        var script = document.createElement('script');
                        script.src = 'https://www.google.com/complete/search?client=firefox&hl=".$locale."&q='+kws+'&callback=seopress_google_suggest';
                        document.body.appendChild(script);
                    }
                });
            </script>
        </div>";
}
        echo '<div id="seopress-analysis-tabs">';
            //if (!empty($seopress_analysis_data)) {
                
                echo '<ul class="wrap-analysis-results">
                    <li><a href="#seopress-analysis-tabs-1"><span class="dashicons dashicons-admin-settings"></span>'. __( 'Optimizations', 'wp-seopress' ) .'</a></li>
                    <li><a href="#seopress-analysis-tabs-2"><span class="dashicons dashicons-admin-generic"></span>'. __( 'Configuration', 'wp-seopress' ) .'</a></li>
                    <li><a href="#seopress-analysis-tabs-3"><span class="dashicons dashicons-format-image"></span>'. __( 'Images', 'wp-seopress' ) .'</a></li>
                    <li><a href="#seopress-analysis-tabs-4"><span class="dashicons dashicons-admin-links"></span>'. __( 'Links', 'wp-seopress' ) .'</a></li>
                </ul>

                <div id="seopress-analysis-tabs-1">';
                    //Word counters
                    if (isset($seopress_analysis_data['0']['words_counter']) || isset($seopress_analysis_data['0']['words_counter_unique'])) {
                        echo '<h3>'.__('Words counter','wp-seopress').'</h3>
                        <ul>
                            <li>'.$seopress_analysis_data['0']['words_counter'].' '.__('words found.','wp-seopress').'</li>
                            <li>'.$seopress_analysis_data['0']['words_counter_unique'].' '.__('unique words found.','wp-seopress').'</li>';

                            if ($seopress_analysis_data['0']['words_counter'] >= 299) {
                                echo '<li><span class="dashicons dashicons-yes"></span>'.__('Your content is composed of more than 300 words, which is the minimum for a post.','wp-seopress').'</li>';
                            } else {
                                echo '<li><span class="dashicons dashicons-no-alt"></span>'.__('Your content is too short. Add a few more paragraphs!','wp-seopress').'</li>';
                            }
                    echo '</ul>';
                    }

                    //Post Title
                    echo '<h3>'.__('Post title','wp-seopress').'</h3>';
                    if (get_post_field('post_title', $post->ID) !='') {
                        if (!empty($seopress_analysis_data['0']['post_title']['matches'])) {
                            echo '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in the Post Title.','wp-seopress').'</p>';
                            echo '<ul>';
                                $target_kws_post_title = $seopress_analysis_data['0']['post_title']['matches'];
                                foreach ($target_kws_post_title as $key => $value) {
                                    foreach ($value as $_key => $_value) {
                                        $kw_count = count($_value);
                                    }
                                    $kw_name = $key;
                                    echo '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
                                }
                            echo '</ul>';
                        } else {
                            echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in the Post Title.','wp-seopress').'</p>';
                        }
                    } else {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('No post title set for this post!','wp-seopress').'</p>';
                    }

                    //H1
                    if (!empty($seopress_analysis_data['0']['h1']['matches'])) {
                        echo '<h3>'.__('H1 (Heading 1)','wp-seopress').'</h3>';
                        $count = count($seopress_analysis_data['0']['h1']['matches']);
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.sprintf(esc_html__('We found %d Heading 1 (H1) in your content.','wp-seopress'), $count).'</p>';
                        echo '<p>'.__('You should not use a H1 heading in your post content because your post title is already an H1. The rule is simple: only one H1 for each web page.','wp-seopress').'</p>';
                    }

                    //H2
                    echo '<h3>'.__('H2 (Heading 2)','wp-seopress').'</h3>';
                    if (!empty($seopress_analysis_data['0']['h2']['matches'])) {
                        echo '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in Heading 2 (H2).','wp-seopress').'</p>';
                        echo '<ul>';
                            $target_kws_h2 = $seopress_analysis_data['0']['h2']['matches'];
                            foreach ($target_kws_h2 as $key => $value) {
                                foreach ($value as $_key => $_value) {
                                    $kw_count = count($value);
                                }
                                $kw_name = $key;
                                echo '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
                            }
                        echo '</ul>';
                    } else {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in Heading 2 (H2).','wp-seopress').'</p>';
                    }

                    //H3
                    echo '<h3>'.__('H3 (Heading 3)','wp-seopress').'</h3>';
                    if (!empty($seopress_analysis_data['0']['h3']['matches'])) {
                        echo '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in Heading 3 (H3).','wp-seopress').'</p>';
                        echo '<ul>';
                            $target_kws_h3 = $seopress_analysis_data['0']['h3']['matches'];
                            foreach ($target_kws_h3 as $key => $value) {
                                foreach ($value as $_key => $_value) {
                                    $kw_count = count($value);
                                }
                                $kw_name = $key;
                                echo '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
                            }
                        echo '</ul>';
                    } else {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in Heading 3 (H3).','wp-seopress').'</p>';
                    }

                    //Meta Title
                    echo '<h3>'.__('Meta title','wp-seopress').'</h3>';
                    if ($seopress_titles_title !='') {
                        if (!empty($seopress_analysis_data['0']['title']['matches'])) {
                            echo '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in the Meta Title.','wp-seopress').'</p>';
                            echo '<ul>';
                                $target_kws_title = $seopress_analysis_data['0']['title']['matches'];
                                foreach ($target_kws_title as $key => $value) {
                                    foreach ($value as $_key => $_value) {
                                        $kw_count = count($_value);
                                    }
                                    $kw_name = $key;
                                    echo '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
                                }
                            echo '</ul>';
                        } else {
                            echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in the Meta Title.','wp-seopress').'</p>';
                        }

                        if (mb_strlen($seopress_titles_title) > 65 ) {
                            echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('Your custom title is too long.','wp-seopress').'</p>'; 
                        } else {
                            echo '<p><span class="dashicons dashicons-yes"></span>'.__('The length of your title is correct','wp-seopress').'</p>';
                        }
                    } else {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('No custom title is set for this post.','wp-seopress').'</p>';
                    }

                    //Meta description
                    echo '<h3>'.__('Meta description','wp-seopress').'</h3>';

                    if ($seopress_titles_desc !='') {
                        if (!empty($seopress_analysis_data['0']['desc']['matches'])) {
                            echo '<p><span class="dashicons dashicons-yes"></span>'.__('Target keywords were found in the Meta description.','wp-seopress').'</p>';                        
                            echo '<ul>';
                                $target_kws_desc = $seopress_analysis_data['0']['desc']['matches'];
                                foreach ($target_kws_desc as $key => $value) {
                                    foreach ($value as $_key => $_value) {
                                        $kw_count = count($_value);
                                    }
                                    $kw_name = $key;
                                    echo '<li><span class="dashicons dashicons-minus"></span>'.sprintf(esc_html__('%s was found %d times.','wp-seopress'), $kw_name, $kw_count).'</li>';
                                }
                            echo '</ul>';
                        } else {
                            echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('None of your target keywords were found in the Meta description.','wp-seopress').'</p>';
                        }

                        if (mb_strlen($seopress_titles_desc) > 160 ) {
                            echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('You custom meta description is too long.','wp-seopress').'</p>'; 
                        } else {
                            echo '<p><span class="dashicons dashicons-yes"></span>'.__('The length of your meta description is correct','wp-seopress').'</p>';
                        }
                    } else {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('No custom meta description is set for this post.','wp-seopress').'</p>';
                    }

                echo '</div>

                <div id="seopress-analysis-tabs-2">
                    <h3>'.__('Robots','wp-seopress').'</h3>';

                    //Robots                    
                    if ($seopress_robots_index =='yes' || $seopress_robots_index =='1') {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('noindex is on! Search engines can\'t index this page.','wp-seopress').'</p>';
                    } else {
                        echo '<p><span class="dashicons dashicons-yes"></span>'.__('noindex is off. Search engines will index this page.','wp-seopress').'</p>';
                    }

                    if ($seopress_robots_follow =='yes' || $seopress_robots_follow =='1') {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('nofollow is on! Search engines can\'t follow your links on this page.','wp-seopress').'</p>';
                    } else {
                        echo '<p><span class="dashicons dashicons-yes"></span>'.__('nofollow is off. Search engines will follow links on this page.','wp-seopress').'</p>';
                    }

                    if ($seopress_robots_imageindex =='yes' || $seopress_robots_imageindex =='1') {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('noimageindex is on! Google will not index your images on this page (but if someone makes a direct link to one of your image in this page, it will be indexed).','wp-seopress').'</p>';
                    } else {
                        echo '<p><span class="dashicons dashicons-yes"></span>'.__('noimageindex is off. Google will index the images on this page.','wp-seopress').'</p>';
                    }

                    if ($seopress_robots_archive =='yes' || $seopress_robots_archive =='1') {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('noarchive is on! Search engines will not cache your page.','wp-seopress').'</p>';
                    } else {
                        echo '<p><span class="dashicons dashicons-yes"></span>'.__('noarchive is off. Search engines will probably cache your page.','wp-seopress').'</p>';
                    }

                    if ($seopress_robots_snippet =='yes' || $seopress_robots_snippet =='1') {
                        echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('nosnippet is on! Search engines will not display a snippet of this page in search results.','wp-seopress').'</p>';
                    } else {
                        echo '<p><span class="dashicons dashicons-yes"></span>'.__('nosnippet is off. Search engines will display a snippet of this page in search results.','wp-seopress').'</p>';
                    }
            echo '</div>
                <div id="seopress-analysis-tabs-3">
                    <div class="wrap-analysis-img">';
                        if (!empty($seopress_analysis_data['0']['img'])) {
                            $images = isset($seopress_analysis_data['0']['img']['images']) ? $seopress_analysis_data['0']['img']['images'] : NULL;
                            $post_thumbnail = isset($seopress_analysis_data['0']['img']['post_thumbnail']) ? $seopress_analysis_data['0']['img']['post_thumbnail'] : NULL;

                            $product_imgs = isset($seopress_analysis_data['0']['img']['product_img']) ? $seopress_analysis_data['0']['img']['product_img'] : NULL;

                            if ((isset($images) && !empty($images)) || (isset($post_thumbnail) && !empty($post_thumbnail))) {
                                echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('No alternative text found for these images. Alt tags are important for both SEO and accessibility.','wp-seopress').'</p>';
                            
                                //Standard images & galleries
                                if (isset($images) && !empty($images)) {
                                    echo '<h4>'.__('Post Content','wp-seopress').'</h4>';
                                    echo '<ul class="attachments">';
                                        foreach($images as $img) {
                                            echo '<li class="attachment"><img src="'.$img.'"/></li>';
                                        }
                                    echo '</ul>';
                                }

                                //Post Thumbnail
                                if (isset($post_thumbnail) && !empty($post_thumbnail)) {
                                    echo '<h4>'.__('Post Thumbnail','wp-seopress').'</h4>';
                                    echo '<ul class="attachments">';
                                        echo '<li class="attachment">'.$post_thumbnail.'</li>';
                                    echo '</ul>';
                                }

                                //WooCommerce galleries
                                if (isset($product_imgs) && !empty($product_imgs)) {
                                    echo '<h4>'.__('Product gallery','wp-seopress').'</h4>';
                                    echo '<ul class="attachments">';
                                        foreach ($product_imgs as $product_img) {
                                            echo '<li class="attachment"><img src="'.$product_img.'"/></li>';
                                        }
                                    echo '</ul>';
                                }
                            } else {
                                echo '<p><span class="dashicons dashicons-yes"></span>'.__('All alternative tags are filled in. Good work!','wp-seopress').'</p>';
                            }
                        } else {
                            echo '<p><span class="dashicons dashicons-no-alt"></span>'.__('We could not find any image in your content. Content with media is a plus for your SEO.','wp-seopress').'</p>';
                        }
                    echo '</div>
                </div>
                <div id="seopress-analysis-tabs-4">';
                    //Nofollow links
                    echo '<h3>'.__('NoFollow Links','wp-seopress').'</h3>';
                    
                    if (!empty($seopress_analysis_data['0']['nofollow_links'])) {
                        
                        $count = count($seopress_analysis_data['0']['nofollow_links']);
                        
                        echo '<p>'.sprintf( esc_html__( 'We found %d links with nofollow attribute in your page. Do not overuse nofollow attribute in links. Below, the list:', 'wp-seopress' ), $count ).'</p>';
                        echo '<ul>';
                            foreach ($seopress_analysis_data['0']['nofollow_links'] as $link) {
                                echo '<li><span class="dashicons dashicons-minus"></span>'.$link.'</li>';
                            }
                        echo '</ul>';
                    } else {
                        echo '<p><span class="dashicons dashicons-yes"></span>'.__('This page doesn\'t have any nofollow links.','wp-seopress').'</p>';
                    }
                echo '</div>';
            //}
    echo '</div>
    </div>';