<?php

defined('ABSPATH') or exit('Please don&rsquo;t call the plugin directly. Thanks :)');

///////////////////////////////////////////////////////////////////////////////////////////////////
//Display metabox in Custom Post Type
///////////////////////////////////////////////////////////////////////////////////////////////////
function seopress_display_date_snippet()
{
	if (seopress_get_service('TitleOption')->getSingleCptDate() === '1') {
		return '<div class="snippet-date">' . get_the_modified_date('M j, Y') . ' - </div>';
	}
}

function seopress_primary_category_select( $echo = true, $with_description = true ){
	global $post;
	global $typenow;
	$seopress_robots_primary_cat = get_post_meta($post->ID, '_seopress_robots_primary_cat', true);

	$cats = 'product' == $typenow && seopress_get_service('WooCommerceActivate')->isActive() ? get_the_terms( $post, 'product_cat' ) : get_categories();
	$cats = apply_filters( 'seopress_primary_category_list', $cats );
	$options = '';

	if( ! empty( $cats ) ){
		$options .= sprintf(
			'<option value="none" %s>%s</option>',
			selected( 'none', $seopress_robots_primary_cat, false),
			__('None (will disable this feature)', 'wp-seopress')
		);
		foreach ( $cats as $category ) {
			$options .= sprintf(
				'<option value="%s" %s>%s</option>',
				(int) $category->term_id,
				selected( $category->term_id, $seopress_robots_primary_cat, false),
				esc_html( $category->name )
			);
		}
	}

	$html = '';
	$description = sprintf(
		/* translators: category permalink structure */
		__('Set the category that gets used in the %s permalink and in our breadcrumbs if you have multiple categories.', 'wp-seopress'),
		'<code>%category%</code>'
	);
	if( ! empty( $options ) ){
		$html = sprintf(
			'<p>
				<label for="seopress_robots_primary_cat" style="display:block; margin-bottom:8px;">%s</label>
				%s
				<br><select id="seopress_robots_primary_cat" name="seopress_robots_primary_cat">%s</select>
			</p>',
			__( 'Select a primary category', 'wp-seopress' ),
			$with_description ? sprintf( '<span class="description">%s</span>', $description ) : '',
			$options
		);
	}

	if ( $echo ) echo $html;
	return $html;
}

function seopress_metaboxes_init()
{
	global $typenow;
	global $pagenow;

	$data_attr             = [];
	$data_attr['data_tax'] = '';
	$data_attr['termId']   = '';

	if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
		$data_attr['current_id'] = get_the_id();
		$data_attr['origin']     = 'post';
		$data_attr['title']      = get_the_title($data_attr['current_id']);
	} elseif ('term.php' == $pagenow || 'edit-tags.php' == $pagenow) {
		global $tag;
		$data_attr['current_id'] = $tag->term_id;
		$data_attr['termId']     = $tag->term_id;
		$data_attr['origin']     = 'term';
		$data_attr['data_tax']   = $tag->taxonomy;
		$data_attr['title']      = $tag->name;
	}

	$data_attr['isHomeId'] = get_option('page_on_front');
	if ('0' === $data_attr['isHomeId']) {
		$data_attr['isHomeId'] = '';
	}

	return $data_attr;
}

function seopress_display_seo_metaboxe()
{
	add_action('add_meta_boxes', 'seopress_init_metabox');
	function seopress_init_metabox()
	{
		if (seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition() !== null) {
			$metaboxe_position = seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition();
		} else {
			$metaboxe_position = 'default';
		}

		$seopress_get_post_types = seopress_get_service('WordPressData')->getPostTypes();

		$seopress_get_post_types = apply_filters('seopress_metaboxe_seo', $seopress_get_post_types);

		if (!empty($seopress_get_post_types) && !seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) {
			foreach ($seopress_get_post_types as $key => $value) {
				add_meta_box('seopress_cpt', __('SEO', 'wp-seopress'), 'seopress_cpt', $key, 'normal', $metaboxe_position);
			}
		}
		add_meta_box('seopress_cpt', __('SEO', 'wp-seopress'), 'seopress_cpt', 'seopress_404', 'normal', $metaboxe_position);
	}

	function seopress_cpt($post)
	{
		global $typenow;
		global $wp_version;
		$prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_nonce_field(plugin_basename(__FILE__), 'seopress_cpt_nonce');

		//init
		$disabled = [];

		wp_enqueue_script('seopress-cpt-tabs', SEOPRESS_ASSETS_DIR . '/js/seopress-metabox' . $prefix . '.js', ['jquery-ui-tabs'], SEOPRESS_VERSION, true);

		if ('seopress_404' != $typenow) {
			wp_enqueue_script('jquery-ui-accordion');

			//Tagify
			wp_enqueue_script('seopress-tagify', SEOPRESS_ASSETS_DIR . '/js/tagify' . $prefix . '.js', ['jquery'], SEOPRESS_VERSION, true);
			wp_register_style('seopress-tagify', SEOPRESS_ASSETS_DIR . '/css/tagify' . $prefix . '.css', [], SEOPRESS_VERSION);
			wp_enqueue_style('seopress-tagify');

			//Register Google Snippet Preview / Content Analysis JS
			wp_enqueue_script('seopress-cpt-counters', SEOPRESS_ASSETS_DIR . '/js/seopress-counters' . $prefix . '.js', ['jquery', 'jquery-ui-tabs', 'jquery-ui-accordion'], SEOPRESS_VERSION, ['strategy' => 'defer', 'in_footer' => true]);

			//If Gutenberg ON
			if (function_exists('get_current_screen')) {
				$get_current_screen = get_current_screen();
				if (isset($get_current_screen->is_block_editor)) {
					if ($get_current_screen->is_block_editor) {
						wp_enqueue_script('seopress-block-editor', SEOPRESS_ASSETS_DIR . '/js/seopress-block-editor' . $prefix . '.js', ['jquery'], SEOPRESS_VERSION, true);
						if (post_type_supports(get_post_type($post), 'custom-fields')) {
							wp_enqueue_script('seopress-pre-publish-checklist', SEOPRESS_URL_PUBLIC . '/editor/pre-publish-checklist/index.js', [], SEOPRESS_VERSION, true);
						}
						if (version_compare($wp_version, '5.8', '>=')) {
							global $pagenow;

							if (('post' == $typenow || 'product' == $typenow) && ('post.php' == $pagenow || 'post-new.php' == $pagenow)) {
								wp_enqueue_script('seopress-primary-category', SEOPRESS_URL_PUBLIC . '/editor/primary-category-select/index.js', ['wp-hooks'], SEOPRESS_VERSION, true);
							}
						}
					} else {
						if ( 'post' === $typenow || 'product' === $typenow ) {
							$seopress_robots_primary_cat = get_post_meta($post->ID, '_seopress_robots_primary_cat', true);
							wp_enqueue_script( 'seopress-primary-category-classic', SEOPRESS_ASSETS_DIR . '/js/seopress-primary-category-classic.js', [], SEOPRESS_VERSION, true);
							wp_localize_script( 'seopress-primary-category-classic', 'seopressPrimaryCategorySelectData', array(
								'selectHTML' => seopress_primary_category_select(false, false),
								'primaryCategory' => $seopress_robots_primary_cat,
							) );
						}
					}
				}
			}

			wp_enqueue_script('seopress-cpt-video-sitemap', SEOPRESS_ASSETS_DIR . '/js/seopress-sitemap-video' . $prefix . '.js', ['jquery', 'jquery-ui-accordion'], SEOPRESS_VERSION, true);

			$seopress_real_preview = [
				'seopress_nonce'         => wp_create_nonce('seopress_real_preview_nonce'), // @deprecated 4.4.0
				'seopress_real_preview'  => admin_url('admin-ajax.php'), // @deprecated 4.4.0
				'i18n'                   => ['progress'  => __('Analysis in progress...', 'wp-seopress')],
				'ajax_url'               => admin_url('admin-ajax.php'),
				'get_preview_meta_title' => wp_create_nonce('get_preview_meta_title'),
				'get_preview_meta_description' => wp_create_nonce('get_preview_meta_description'),
			];
			wp_localize_script('seopress-cpt-counters', 'seopressAjaxRealPreview', $seopress_real_preview);

			wp_enqueue_script('seopress-media-uploader', SEOPRESS_ASSETS_DIR . '/js/seopress-media-uploader' . $prefix . '.js', ['jquery'], SEOPRESS_VERSION, false);
			wp_enqueue_media();
		}

		$seopress_titles_title                  = get_post_meta($post->ID, '_seopress_titles_title', true);
		$seopress_titles_desc                   = get_post_meta($post->ID, '_seopress_titles_desc', true);

		$disabled['robots_index'] = '';
		if (seopress_get_service('TitleOption')->getSingleCptNoIndex() || seopress_get_service('TitleOption')->getTitleNoIndex() || true === post_password_required($post->ID)) {
			$seopress_robots_index              = 'yes';
			$disabled['robots_index']           = 'disabled';
		} else {
			$seopress_robots_index              = get_post_meta($post->ID, '_seopress_robots_index', true);
		}

		$disabled['robots_follow'] = '';
		if (seopress_get_service('TitleOption')->getSingleCptNoFollow() || seopress_get_service('TitleOption')->getTitleNoFollow()) {
			$seopress_robots_follow             = 'yes';
			$disabled['robots_follow']          = 'disabled';
		} else {
			$seopress_robots_follow             = get_post_meta($post->ID, '_seopress_robots_follow', true);
		}

		$disabled['snippet'] = '';
		if (seopress_get_service('TitleOption')->getTitleNoSnippet()) {
			$seopress_robots_snippet            = 'yes';
			$disabled['snippet']                = 'disabled';
		} else {
			$seopress_robots_snippet            = get_post_meta($post->ID, '_seopress_robots_snippet', true);
		}

		$disabled['imageindex'] = '';
		if (seopress_get_service('TitleOption')->getTitleNoImageIndex()) {
			$seopress_robots_imageindex         = 'yes';
			$disabled['imageindex']             = 'disabled';
		} else {
			$seopress_robots_imageindex         = get_post_meta($post->ID, '_seopress_robots_imageindex', true);
		}

		$seopress_robots_canonical              = get_post_meta($post->ID, '_seopress_robots_canonical', true);
		$seopress_robots_primary_cat            = get_post_meta($post->ID, '_seopress_robots_primary_cat', true);
		$seopress_social_fb_title               = get_post_meta($post->ID, '_seopress_social_fb_title', true);
		$seopress_social_fb_desc                = get_post_meta($post->ID, '_seopress_social_fb_desc', true);
		$seopress_social_fb_img                 = get_post_meta($post->ID, '_seopress_social_fb_img', true);
		$seopress_social_fb_img_attachment_id   = get_post_meta($post->ID, '_seopress_social_fb_img_attachment_id', true);
		$seopress_social_fb_img_width           = get_post_meta($post->ID, '_seopress_social_fb_img_width', true);
		$seopress_social_fb_img_height          = get_post_meta($post->ID, '_seopress_social_fb_img_height', true);
		$seopress_social_twitter_title          = get_post_meta($post->ID, '_seopress_social_twitter_title', true);
		$seopress_social_twitter_desc           = get_post_meta($post->ID, '_seopress_social_twitter_desc', true);
		$seopress_social_twitter_img            = get_post_meta($post->ID, '_seopress_social_twitter_img', true);
		$seopress_social_twitter_img_attachment_id            = get_post_meta($post->ID, '_seopress_social_twitter_img_attachment_id', true);
		$seopress_social_twitter_img_width      = get_post_meta($post->ID, '_seopress_social_twitter_img_width', true);
		$seopress_social_twitter_img_height     = get_post_meta($post->ID, '_seopress_social_twitter_img_height', true);
		$seopress_redirections_enabled          = get_post_meta($post->ID, '_seopress_redirections_enabled', true);
		$seopress_redirections_enabled_regex    = get_post_meta($post->ID, '_seopress_redirections_enabled_regex', true);
		$seopress_redirections_logged_status    = get_post_meta($post->ID, '_seopress_redirections_logged_status', true);
		$seopress_redirections_type             = get_post_meta($post->ID, '_seopress_redirections_type', true);
		$seopress_redirections_value            = get_post_meta($post->ID, '_seopress_redirections_value', true);
		$seopress_redirections_param            = get_post_meta($post->ID, '_seopress_redirections_param', true);

		require_once dirname(dirname(__FILE__)) . '/admin-dyn-variables-helper.php'; //Dynamic variables

		require_once dirname(__FILE__) . '/admin-metaboxes-form.php'; //Metaboxe HTML

		do_action('seopress_seo_metabox_init');
	}

	add_action('save_post', 'seopress_save_metabox', 10, 2);
	function seopress_save_metabox($post_id, $post)
	{
		//Nonce
		if (!isset($_POST['seopress_cpt_nonce']) || !wp_verify_nonce($_POST['seopress_cpt_nonce'], plugin_basename(__FILE__))) {
			return $post_id;
		}

		//Post type object
		$post_type = get_post_type_object($post->post_type);

		//Check permission
		if (!current_user_can($post_type->cap->edit_post, $post_id)) {
			return $post_id;
		}

		if ('attachment' !== get_post_type($post_id)) {
			$seo_tabs = isset($_POST['seo_tabs']) ? json_decode(stripslashes(htmlspecialchars_decode($_POST['seo_tabs'])), true) : [];

			if (json_last_error() !== JSON_ERROR_NONE) {
				return $post_id;
			}

			if (in_array('title-tab', $seo_tabs)) {
				if (!empty($_POST['seopress_titles_title'])) {
					update_post_meta($post_id, '_seopress_titles_title', sanitize_text_field($_POST['seopress_titles_title']));
				} else {
					delete_post_meta($post_id, '_seopress_titles_title');
				}
				if (!empty($_POST['seopress_titles_desc'])) {
					update_post_meta($post_id, '_seopress_titles_desc', sanitize_textarea_field($_POST['seopress_titles_desc']));
				} else {
					delete_post_meta($post_id, '_seopress_titles_desc');
				}
			}
			if (in_array('advanced-tab', $seo_tabs)) {
				if (isset($_POST['seopress_robots_index'])) {
					update_post_meta($post_id, '_seopress_robots_index', 'yes');
				} else {
					delete_post_meta($post_id, '_seopress_robots_index');
				}
				if (isset($_POST['seopress_robots_follow'])) {
					update_post_meta($post_id, '_seopress_robots_follow', 'yes');
				} else {
					delete_post_meta($post_id, '_seopress_robots_follow');
				}
				if (isset($_POST['seopress_robots_imageindex'])) {
					update_post_meta($post_id, '_seopress_robots_imageindex', 'yes');
				} else {
					delete_post_meta($post_id, '_seopress_robots_imageindex');
				}
				if (isset($_POST['seopress_robots_snippet'])) {
					update_post_meta($post_id, '_seopress_robots_snippet', 'yes');
				} else {
					delete_post_meta($post_id, '_seopress_robots_snippet');
				}
				if (!empty($_POST['seopress_robots_canonical'])) {
					update_post_meta($post_id, '_seopress_robots_canonical', sanitize_url($_POST['seopress_robots_canonical']));
				} else {
					delete_post_meta($post_id, '_seopress_robots_canonical');
				}
				if (!empty($_POST['seopress_robots_primary_cat'])) {
					update_post_meta($post_id, '_seopress_robots_primary_cat', sanitize_text_field($_POST['seopress_robots_primary_cat']));
				} else {
					delete_post_meta($post_id, '_seopress_robots_primary_cat');
				}
			}
			if (in_array('social-tab', $seo_tabs)) {
				//Facebook
				if (!empty($_POST['seopress_social_fb_title'])) {
					update_post_meta($post_id, '_seopress_social_fb_title', sanitize_text_field($_POST['seopress_social_fb_title']));
				} else {
					delete_post_meta($post_id, '_seopress_social_fb_title');
				}
				if (!empty($_POST['seopress_social_fb_desc'])) {
					update_post_meta($post_id, '_seopress_social_fb_desc', sanitize_textarea_field($_POST['seopress_social_fb_desc']));
				} else {
					delete_post_meta($post_id, '_seopress_social_fb_desc');
				}
				if (!empty($_POST['seopress_social_fb_img'])) {
					update_post_meta($post_id, '_seopress_social_fb_img', sanitize_url($_POST['seopress_social_fb_img']));
				} else {
					delete_post_meta($post_id, '_seopress_social_fb_img');
				}
				if (!empty($_POST['seopress_social_fb_img_attachment_id']) && !empty($_POST['seopress_social_fb_img'])) {
					update_post_meta($post_id, '_seopress_social_fb_img_attachment_id', sanitize_text_field($_POST['seopress_social_fb_img_attachment_id']));
				} else {
					delete_post_meta($post_id, '_seopress_social_fb_img_attachment_id');
				}
				if (!empty($_POST['seopress_social_fb_img_width']) && !empty($_POST['seopress_social_fb_img'])) {
					update_post_meta($post_id, '_seopress_social_fb_img_width', sanitize_text_field($_POST['seopress_social_fb_img_width']));
				} else {
					delete_post_meta($post_id, '_seopress_social_fb_img_width');
				}
				if (!empty($_POST['seopress_social_fb_img_height']) && !empty($_POST['seopress_social_fb_img'])) {
					update_post_meta($post_id, '_seopress_social_fb_img_height', sanitize_text_field($_POST['seopress_social_fb_img_height']));
				} else {
					delete_post_meta($post_id, '_seopress_social_fb_img_height');
				}

				//Twitter
				if (!empty($_POST['seopress_social_twitter_title'])) {
					update_post_meta($post_id, '_seopress_social_twitter_title', sanitize_text_field($_POST['seopress_social_twitter_title']));
				} else {
					delete_post_meta($post_id, '_seopress_social_twitter_title');
				}
				if (!empty($_POST['seopress_social_twitter_desc'])) {
					update_post_meta($post_id, '_seopress_social_twitter_desc', sanitize_textarea_field($_POST['seopress_social_twitter_desc']));
				} else {
					delete_post_meta($post_id, '_seopress_social_twitter_desc');
				}
				if (!empty($_POST['seopress_social_twitter_img'])) {
					update_post_meta($post_id, '_seopress_social_twitter_img', sanitize_url($_POST['seopress_social_twitter_img']));
				} else {
					delete_post_meta($post_id, '_seopress_social_twitter_img');
				}
				if (!empty($_POST['seopress_social_twitter_img_attachment_id']) && !empty($_POST['seopress_social_twitter_img'])) {
					update_post_meta($post_id, '_seopress_social_twitter_img_attachment_id', sanitize_text_field($_POST['seopress_social_twitter_img_attachment_id']));
				} else {
					delete_post_meta($post_id, '_seopress_social_twitter_img_attachment_id');
				}
				if (!empty($_POST['seopress_social_twitter_img_width']) && !empty($_POST['seopress_social_twitter_img'])) {
					update_post_meta($post_id, '_seopress_social_twitter_img_width', sanitize_text_field($_POST['seopress_social_twitter_img_width']));
				} else {
					delete_post_meta($post_id, '_seopress_social_twitter_img_width');
				}
				if (!empty($_POST['seopress_social_twitter_img_height']) && !empty($_POST['seopress_social_twitter_img'])) {
					update_post_meta($post_id, '_seopress_social_twitter_img_height', sanitize_text_field($_POST['seopress_social_twitter_img_height']));
				} else {
					delete_post_meta($post_id, '_seopress_social_twitter_img_height');
				}
			}
			if (in_array('redirect-tab', $seo_tabs)) {
				if (isset($_POST['seopress_redirections_type'])) {
					$redirection_type = intval($_POST['seopress_redirections_type']);

					if (in_array($redirection_type, [301, 302, 307, 410, 451])) {
						update_post_meta($post_id, '_seopress_redirections_type', $redirection_type);
					} else {
						delete_post_meta($post_id, '_seopress_redirections_type');
					}
				}
				if (!empty($_POST['seopress_redirections_value'])) {
					update_post_meta($post_id, '_seopress_redirections_value', sanitize_url($_POST['seopress_redirections_value']));
				} else {
					delete_post_meta($post_id, '_seopress_redirections_value');
				}
				if (isset($_POST['seopress_redirections_enabled'])) {
					update_post_meta($post_id, '_seopress_redirections_enabled', 'yes');
				} else {
					delete_post_meta($post_id, '_seopress_redirections_enabled', '');
				}
				if (isset($_POST['seopress_redirections_enabled_regex'])) {
					update_post_meta($post_id, '_seopress_redirections_enabled_regex', 'yes');
				} else {
					delete_post_meta($post_id, '_seopress_redirections_enabled_regex');
				}
				if (isset($_POST['seopress_redirections_logged_status'])) {
					$logged_status = sanitize_text_field($_POST['seopress_redirections_logged_status']);

					$allowed_options = ['both', 'only_logged_in', 'only_not_logged_in'];

					if (in_array($logged_status, $allowed_options, true)) {
						update_post_meta($post_id, '_seopress_redirections_logged_status', $logged_status);
					} else {
						delete_post_meta($post_id, '_seopress_redirections_logged_status');
					}
				}
				if (isset($_POST['seopress_redirections_param'])) {
					$redirections_param = sanitize_text_field($_POST['seopress_redirections_param']);

					$allowed_options = ['exact_match', 'without_param', 'with_ignored_param'];

					if (in_array($redirections_param, $allowed_options, true)) {
						update_post_meta($post_id, '_seopress_redirections_param', $redirections_param);
					} else {
						delete_post_meta($post_id, '_seopress_redirections_param');
					}
				}
			}
			if (did_action('elementor/loaded')) {
				$elementor = get_post_meta($post_id, '_elementor_page_settings', true);

				if (!empty($elementor)) {
					if (isset($_POST['seopress_titles_title'])) {
						$elementor['_seopress_titles_title'] = sanitize_text_field($_POST['seopress_titles_title']);
					}
					if (isset($_POST['seopress_titles_desc'])) {
						$elementor['_seopress_titles_desc'] = sanitize_textarea_field($_POST['seopress_titles_desc']);
					}
					if (isset($_POST['seopress_robots_index'])) {
						$elementor['_seopress_robots_index'] = 'yes';
					} else {
						$elementor['_seopress_robots_index'] = '';
					}
					if (isset($_POST['seopress_robots_follow'])) {
						$elementor['_seopress_robots_follow'] = 'yes';
					} else {
						$elementor['_seopress_robots_follow'] = '';
					}
					if (isset($_POST['seopress_robots_imageindex'])) {
						$elementor['_seopress_robots_imageindex'] = 'yes';
					} else {
						$elementor['_seopress_robots_imageindex'] = '';
					}
					if (isset($_POST['seopress_robots_snippet'])) {
						$elementor['_seopress_robots_snippet'] = 'yes';
					} else {
						$elementor['_seopress_robots_snippet'] = '';
					}
					if (isset($_POST['seopress_robots_canonical'])) {
						$elementor['_seopress_robots_canonical'] = sanitize_url($_POST['seopress_robots_canonical']);
					}
					if (isset($_POST['seopress_robots_primary_cat'])) {
						$elementor['_seopress_robots_primary_cat'] = sanitize_text_field($_POST['seopress_robots_primary_cat']);
					}
					if (isset($_POST['seopress_social_fb_title'])) {
						$elementor['_seopress_social_fb_title'] = sanitize_text_field($_POST['seopress_social_fb_title']);
					}
					if (isset($_POST['seopress_social_fb_desc'])) {
						$elementor['_seopress_social_fb_desc'] = sanitize_textarea_field($_POST['seopress_social_fb_desc']);
					}
					if (isset($_POST['seopress_social_fb_img'])) {
						$elementor['_seopress_social_fb_img'] = sanitize_url($_POST['seopress_social_fb_img']);
					}
					if (isset($_POST['seopress_social_twitter_title'])) {
						$elementor['_seopress_social_twitter_title'] = sanitize_text_field($_POST['seopress_social_twitter_title']);
					}
					if (isset($_POST['seopress_social_twitter_desc'])) {
						$elementor['_seopress_social_twitter_desc'] = sanitize_textarea_field($_POST['seopress_social_twitter_desc']);
					}
					if (isset($_POST['seopress_social_twitter_img'])) {
						$elementor['_seopress_social_twitter_img'] = sanitize_url($_POST['seopress_social_twitter_img']);
					}
					if (isset($_POST['seopress_redirections_type'])) {
						$redirection_type = intval($_POST['seopress_redirections_type']);

						if (in_array($redirection_type, [301, 302, 307, 410, 451])) {
							$elementor['_seopress_redirections_type'] = $redirection_type;
						}
					}
					if (isset($_POST['seopress_redirections_value'])) {
						$elementor['_seopress_redirections_value'] = sanitize_url($_POST['seopress_redirections_value']);
					}
					if (isset($_POST['seopress_redirections_enabled'])) {
						$elementor['_seopress_redirections_enabled'] = 'yes';
					} else {
						$elementor['_seopress_redirections_enabled'] = '';
					}
					update_post_meta($post_id, '_elementor_page_settings', $elementor);
				}
			}

			do_action('seopress_seo_metabox_save', $post_id, $seo_tabs);
		}
	}
}

function seopress_display_ca_metaboxe()
{
	add_action('add_meta_boxes', 'seopress_init_ca_metabox');
	function seopress_init_ca_metabox()
	{
		if (seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition() !== null) {
			$metaboxe_position = seopress_get_service('AdvancedOption')->getAppearanceMetaboxePosition();
		} else {
			$metaboxe_position = 'default';
		}

		$seopress_get_post_types = seopress_get_service('WordPressData')->getPostTypes();

		$seopress_get_post_types = apply_filters('seopress_metaboxe_content_analysis', $seopress_get_post_types);

		if (!empty($seopress_get_post_types) && !seopress_get_service('EnqueueModuleMetabox')->canEnqueue()) {
			foreach ($seopress_get_post_types as $key => $value) {
				add_meta_box('seopress_content_analysis', __('Content analysis', 'wp-seopress'), 'seopress_content_analysis', $key, 'normal', $metaboxe_position);
			}
		}
	}

	function seopress_content_analysis($post)
	{
		$prefix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		wp_nonce_field(plugin_basename(__FILE__), 'seopress_content_analysis_nonce');

		//Tagify
		wp_enqueue_script('seopress-tagify', SEOPRESS_ASSETS_DIR . '/js/tagify' . $prefix . '.js', ['jquery'], SEOPRESS_VERSION, true);
		wp_register_style('seopress-tagify', SEOPRESS_ASSETS_DIR . '/css/tagify' . $prefix . '.css', [], SEOPRESS_VERSION);
		wp_enqueue_style('seopress-tagify');

		wp_enqueue_script('seopress-cpt-counters', SEOPRESS_ASSETS_DIR . '/js/seopress-counters' . $prefix . '.js', ['jquery', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-autocomplete'], SEOPRESS_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
		$seopress_real_preview = [
			'seopress_nonce'         => wp_create_nonce('seopress_real_preview_nonce'),
			'seopress_real_preview'  => admin_url('admin-ajax.php'),
			'i18n'                   => ['progress' => __('Analysis in progress...', 'wp-seopress')],
			'ajax_url'               => admin_url('admin-ajax.php'),
			'get_preview_meta_title' => wp_create_nonce('get_preview_meta_title'),
			'get_preview_meta_description' => wp_create_nonce('get_preview_meta_description'),
		];
		wp_localize_script('seopress-cpt-counters', 'seopressAjaxRealPreview', $seopress_real_preview);

		$seopress_inspect_url = [
			'seopress_nonce'            => wp_create_nonce('seopress_inspect_url_nonce'),
			'seopress_inspect_url'      => admin_url('admin-ajax.php'),
		];
		wp_localize_script('seopress-cpt-counters', 'seopressAjaxInspectUrl', $seopress_inspect_url);

		$seopress_analysis_target_kw            = get_post_meta($post->ID, '_seopress_analysis_target_kw', true);
		$seopress_analysis_data                 = get_post_meta($post->ID, '_seopress_analysis_data', true);
		$seopress_titles_title                  = get_post_meta($post->ID, '_seopress_titles_title', true);
		$seopress_titles_desc                   = get_post_meta($post->ID, '_seopress_titles_desc', true);

		if (seopress_get_service('TitleOption')->getSingleCptNoIndex() || seopress_get_service('TitleOption')->getTitleNoIndex() || true === post_password_required($post->ID)) {
			$seopress_robots_index              = 'yes';
		} else {
			$seopress_robots_index              = get_post_meta($post->ID, '_seopress_robots_index', true);
		}

		if (seopress_get_service('TitleOption')->getSingleCptNoFollow() || seopress_get_service('TitleOption')->getTitleNoFollow()) {
			$seopress_robots_follow             = 'yes';
		} else {
			$seopress_robots_follow             = get_post_meta($post->ID, '_seopress_robots_follow', true);
		}

		if (seopress_get_service('TitleOption')->getTitleNoSnippet()) {
			$seopress_robots_snippet            = 'yes';
		} else {
			$seopress_robots_snippet            = get_post_meta($post->ID, '_seopress_robots_snippet', true);
		}

		if (seopress_get_service('TitleOption')->getTitleNoImageIndex()) {
			$seopress_robots_imageindex         = 'yes';
		} else {
			$seopress_robots_imageindex         = get_post_meta($post->ID, '_seopress_robots_imageindex', true);
		}

		require_once dirname(__FILE__) . '/admin-metaboxes-content-analysis-form.php'; //Metaboxe HTML
	}

	add_action('save_post', 'seopress_save_ca_metabox', 10, 2);
	function seopress_save_ca_metabox($post_id, $post)
	{
		//Nonce
		if (!isset($_POST['seopress_content_analysis_nonce']) || !wp_verify_nonce($_POST['seopress_content_analysis_nonce'], plugin_basename(__FILE__))) {
			return $post_id;
		}

		//Post type object
		$post_type = get_post_type_object($post->post_type);

		//Check permission
		if (!current_user_can($post_type->cap->edit_post, $post_id)) {
			return $post_id;
		}

		if ('attachment' !== get_post_type($post_id)) {
			if (isset($_POST['seopress_analysis_target_kw'])) {
				$target_kw = sanitize_text_field($_POST['seopress_analysis_target_kw']);
				update_post_meta($post_id, '_seopress_analysis_target_kw', $target_kw);
			}
			if (did_action('elementor/loaded')) {
				$elementor = get_post_meta($post_id, '_elementor_page_settings', true);

				if (!empty($elementor)) {
					$elementor_updated = $elementor;

					if (isset($_POST['seopress_analysis_target_kw'])) {
						$target_kw = sanitize_text_field($_POST['seopress_analysis_target_kw']);

						$elementor_updated['_seopress_analysis_target_kw'] = $target_kw;
					}

					update_post_meta($post_id, '_elementor_page_settings', $elementor_updated);
				}
			}
		}
	}

	//Save metabox values in elementor
	add_action('save_post', 'seopress_update_elementor_fields', 999, 2);
	function seopress_update_elementor_fields($post_id, $post)
	{
		do_action('seopress/page-builders/elementor/save_meta', $post_id);
	}
}

if (is_user_logged_in()) {
	if (is_super_admin()) {
		echo seopress_display_seo_metaboxe();
		echo seopress_display_ca_metaboxe();
	} else {
		global $wp_roles;
		$user = wp_get_current_user();
		//Get current user role
		if (isset($user->roles) && is_array($user->roles) && !empty($user->roles)) {
			$seopress_user_role = current($user->roles);

			//If current user role matchs values from Security settings then apply -- SEO Metaboxe
			if (!empty(seopress_get_service('AdvancedOption')->getSecurityMetaboxRole())) {
				if (array_key_exists($seopress_user_role, seopress_get_service('AdvancedOption')->getSecurityMetaboxRole())) {
					//do nothing
				} else {
					echo seopress_display_seo_metaboxe();
				}
			} else {
				echo seopress_display_seo_metaboxe();
			}

			//If current user role matchs values from Security settings then apply -- SEO Content Analysis
			if (!empty(seopress_get_service('AdvancedOption')->getSecurityMetaboxRoleContentAnalysis())) {
				if (array_key_exists($seopress_user_role, seopress_get_service('AdvancedOption')->getSecurityMetaboxRoleContentAnalysis())) {
					//do nothing
				} else {
					echo seopress_display_ca_metaboxe();
				}
			} else {
				echo seopress_display_ca_metaboxe();
			}
		}
	}
}
