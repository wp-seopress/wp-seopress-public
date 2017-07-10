<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//Titles & metas
//=================================================================================================
//Titles
//IF WP Site is public
if (get_option('blog_public') =='1') {
	//Titles & Metas

	//Homepage Title
	function seopress_titles_home_site_title_option() {
		$seopress_titles_home_site_title_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_home_site_title_option ) ) {
			foreach ($seopress_titles_home_site_title_option as $key => $seopress_titles_home_site_title_value)
				$options[$key] = $seopress_titles_home_site_title_value;
			 if (isset($seopress_titles_home_site_title_option['seopress_titles_home_site_title'])) { 
			 	return $seopress_titles_home_site_title_option['seopress_titles_home_site_title'];
			 }
		}
	};

	//Single CPT Titles
	function seopress_titles_single_titles_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_single_titles_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_single_titles_option ) ) {
			foreach ($seopress_titles_single_titles_option as $key => $seopress_titles_single_titles_value)
				$options[$key] = $seopress_titles_single_titles_value;
			 if (isset($seopress_titles_single_titles_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['title'])) { 
			 	return $seopress_titles_single_titles_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['title'];
			 }
		}
	};

	//Archive CPT Titles
	function seopress_titles_archive_titles_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_archive_titles_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archive_titles_option ) ) {
			foreach ($seopress_titles_archive_titles_option as $key => $seopress_titles_archive_titles_value)
				$options[$key] = $seopress_titles_archive_titles_value;
			 if (isset($seopress_titles_archive_titles_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['title'])) { 
			 	return $seopress_titles_archive_titles_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['title'];
			 }
		}
	};

	//Tax archives Titles
	function seopress_titles_tax_titles_option() {
		$queried_object = get_queried_object();
		$seopress_get_current_tax = $queried_object->taxonomy;

		$seopress_titles_tax_titles_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_tax_titles_option ) ) {
			foreach ($seopress_titles_tax_titles_option as $key => $seopress_titles_tax_titles_value)
				$options[$key] = $seopress_titles_tax_titles_value;
			 if (isset($seopress_titles_tax_titles_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['title'])) { 
			 	return $seopress_titles_tax_titles_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['title'];
			 }
		}
	};

	//Author archive Titles
	function seopress_titles_archives_author_title_option() {
		$seopress_titles_archives_author_title_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_author_title_option ) ) {
			foreach ($seopress_titles_archives_author_title_option as $key => $seopress_titles_archives_author_title_value)
				$options[$key] = $seopress_titles_archives_author_title_value;
			 if (isset($seopress_titles_archives_author_title_option['seopress_titles_archives_author_title'])) { 
			 	return $seopress_titles_archives_author_title_option['seopress_titles_archives_author_title'];
			 }
		}
	};

	//Date archive Titles
	function seopress_titles_archives_date_title_option() {
		$seopress_titles_archives_date_title_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_date_title_option ) ) {
			foreach ($seopress_titles_archives_date_title_option as $key => $seopress_titles_archives_date_title_value)
				$options[$key] = $seopress_titles_archives_date_title_value;
			 if (isset($seopress_titles_archives_date_title_option['seopress_titles_archives_date_title'])) { 
			 	return $seopress_titles_archives_date_title_option['seopress_titles_archives_date_title'];
			 }
		}
	};

	//Search archive Titles
	function seopress_titles_archives_search_title_option() {
		$seopress_titles_archives_search_title_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_search_title_option ) ) {
			foreach ($seopress_titles_archives_search_title_option as $key => $seopress_titles_archives_search_title_value)
				$options[$key] = $seopress_titles_archives_search_title_value;
			 if (isset($seopress_titles_archives_search_title_option['seopress_titles_archives_search_title'])) { 
			 	return $seopress_titles_archives_search_title_option['seopress_titles_archives_search_title'];
			 }
		}
	};

	//404 archive Titles
	function seopress_titles_archives_404_title_option() {
		$seopress_titles_archives_404_title_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_404_title_option ) ) {
			foreach ($seopress_titles_archives_404_title_option as $key => $seopress_titles_archives_404_title_value)
				$options[$key] = $seopress_titles_archives_404_title_value;
			 if (isset($seopress_titles_archives_404_title_option['seopress_titles_archives_404_title'])) { 
			 	return $seopress_titles_archives_404_title_option['seopress_titles_archives_404_title'];
			 }
		}
	};

	//Link rel prev/next
	function seopress_titles_paged_rel_option() {
		$seopress_titles_paged_rel_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_paged_rel_option ) ) {
			foreach ($seopress_titles_paged_rel_option as $key => $seopress_titles_paged_rel_value)
				$options[$key] = $seopress_titles_paged_rel_value;
			 if (isset($seopress_titles_paged_rel_option['seopress_titles_paged_rel'])) { 
			 	return $seopress_titles_paged_rel_option['seopress_titles_paged_rel'];
			 }
		}
	};
	
	//Homepage Description
	function seopress_titles_home_site_desc_option() {
		$seopress_titles_home_site_desc_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_home_site_desc_option ) ) {
			foreach ($seopress_titles_home_site_desc_option as $key => $seopress_titles_home_site_desc_value)
				$options[$key] = $seopress_titles_home_site_desc_value;
			 if (isset($seopress_titles_home_site_desc_option['seopress_titles_home_site_desc'])) { 
			 	return $seopress_titles_home_site_desc_option['seopress_titles_home_site_desc'];
			 }
		}
	};

	//Single CPT Description
	function seopress_titles_single_desc_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_single_desc_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_single_desc_option ) ) {
			foreach ($seopress_titles_single_desc_option as $key => $seopress_titles_single_desc_value)
				$options[$key] = $seopress_titles_single_desc_value;
			 if (isset($seopress_titles_single_desc_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['description'])) { 
			 	return $seopress_titles_single_desc_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['description'];
			 }
		}
	};

	//Archive CPT Description
	function seopress_titles_archive_desc_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_archive_desc_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archive_desc_option ) ) {
			foreach ($seopress_titles_archive_desc_option as $key => $seopress_titles_archive_desc_value)
				$options[$key] = $seopress_titles_archive_desc_value;
			 if (isset($seopress_titles_archive_desc_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['description'])) { 
			 	return $seopress_titles_archive_desc_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['description'];
			 }
		}
	};

	//Tax archives Desc
	function seopress_titles_tax_desc_option() {
		$queried_object = get_queried_object();
		$seopress_get_current_tax = $queried_object->taxonomy;

		$seopress_titles_tax_desc_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_tax_desc_option ) ) {
			foreach ($seopress_titles_tax_desc_option as $key => $seopress_titles_tax_desc_value)
				$options[$key] = $seopress_titles_tax_desc_value;
			 if (isset($seopress_titles_tax_desc_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['description'])) { 
			 	return $seopress_titles_tax_desc_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['description'];
			 }
		}
	};

	//Author archives Desc
	function seopress_titles_archives_author_desc_option() {
		$seopress_titles_archives_author_desc_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_author_desc_option ) ) {
			foreach ($seopress_titles_archives_author_desc_option as $key => $seopress_titles_archives_author_desc_value)
				$options[$key] = $seopress_titles_archives_author_desc_value;
			 if (isset($seopress_titles_archives_author_desc_option['seopress_titles_archives_author_desc'])) { 
			 	return $seopress_titles_archives_author_desc_option['seopress_titles_archives_author_desc'];
			 }
		}
	};

	//Date archives Desc
	function seopress_titles_archives_date_desc_option() {
		$seopress_titles_archives_date_desc_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_date_desc_option ) ) {
			foreach ($seopress_titles_archives_date_desc_option as $key => $seopress_titles_archives_date_desc_value)
				$options[$key] = $seopress_titles_archives_date_desc_value;
			 if (isset($seopress_titles_archives_date_desc_option['seopress_titles_archives_date_desc'])) { 
			 	return $seopress_titles_archives_date_desc_option['seopress_titles_archives_date_desc'];
			 }
		}
	};

	//Search archives Desc
	function seopress_titles_archives_search_desc_option() {
		$seopress_titles_archives_search_desc_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_search_desc_option ) ) {
			foreach ($seopress_titles_archives_search_desc_option as $key => $seopress_titles_archives_search_desc_value)
				$options[$key] = $seopress_titles_archives_search_desc_value;
			 if (isset($seopress_titles_archives_search_desc_option['seopress_titles_archives_search_desc'])) { 
			 	return $seopress_titles_archives_search_desc_option['seopress_titles_archives_search_desc'];
			 }
		}
	};

	//404 archives Desc
	function seopress_titles_archives_404_desc_option() {
		$seopress_titles_archives_404_desc_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_404_desc_option ) ) {
			foreach ($seopress_titles_archives_404_desc_option as $key => $seopress_titles_archives_404_desc_value)
				$options[$key] = $seopress_titles_archives_404_desc_value;
			 if (isset($seopress_titles_archives_404_desc_option['seopress_titles_archives_404_desc'])) { 
			 	return $seopress_titles_archives_404_desc_option['seopress_titles_archives_404_desc'];
			 }
		}
	};

	//THE Title Tag
	function seopress_titles_the_title() {

		global $post;
		global $term;

		//Remove WordPress Filters
		$seopress_array_filters = array('category_description', 'tag_description', 'term_description');
		foreach ($seopress_array_filters as $key => $value) {
			remove_filter($value,'wpautop');
		}
		
		//Template variables
		$seopress_paged ='';
		if (get_query_var('paged') >='1') {
			$seopress_paged = get_query_var('paged');
		}

		$the_author_meta ='';
		if(is_single() || is_author()){
			$the_author_meta = get_the_author_meta('display_name', $post->post_author);
		}

		$seopress_titles_template_variables_array = array(
			'%%sitetitle%%', 
			'%%tagline%%',
			'%%post_title%%',
			'%%post_excerpt%%',
			'%%post_date%%',
			'%%post_author%%',
			'%%_category_title%%',
			'%%_category_description%%',
			'%%tag_title%%',
			'%%tag_description%%',
			'%%term_title%%',
			'%%term_description%%',
			'%%search_keywords%%',
			'%%current_pagination%%',
			'%%cpt_plural%%',
			'%%archive_date%%',
		);
		$seopress_titles_template_replace_array = array(
			get_bloginfo('name'), 
			get_bloginfo('description'),
			the_title_attribute('echo=0'),
			get_the_excerpt(),
			get_the_date(),
			$the_author_meta,
			single_cat_title('', false),
			esc_html(category_description()),
			single_tag_title('', false),
			esc_html(tag_description()),
			single_term_title('', false),
			esc_html(term_description()),
			get_search_query(),
			$seopress_paged,
			post_type_archive_title('', false),
			get_the_archive_title(),
		);

		if ( is_front_page() && is_home() && get_post_meta($post->ID,'_seopress_titles_title',true) =='') { //HOMEPAGE
			if (seopress_titles_home_site_title_option() !='') {
				$seopress_titles_the_title = esc_attr(seopress_titles_home_site_title_option());
	
				$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
				return $seopress_titles_title_template;
			}
		} elseif ( is_front_page() && get_post_meta($post->ID,'_seopress_titles_title',true) ==''){ //STATIC HOMEPAGE
			if (seopress_titles_home_site_title_option() !='') {
				$seopress_titles_the_title = esc_attr(seopress_titles_home_site_title_option());
	
				$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
				return $seopress_titles_title_template;
			}
		} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_title',true) !=''){ //BLOG PAGE
			if (get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_title',true)) { //IS METABOXE
				$seopress_titles_the_title = esc_attr(get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_title',true));

				$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
				return $seopress_titles_title_template;
			}
		} elseif (is_singular()) { //IS SINGULAR
			if (get_post_meta($post->ID,'_seopress_titles_title',true)) { //IS METABOXE
				$seopress_titles_the_title = esc_attr(get_post_meta($post->ID,'_seopress_titles_title',true));

				$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_title);
				return $seopress_titles_title_template;
			}
			else { //DEFAULT GLOBAL
				$seopress_titles_single_titles_option = esc_attr(seopress_titles_single_titles_option());

				$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_single_titles_option);
				return $seopress_titles_title_template;
			}
		} elseif (is_post_type_archive() && seopress_titles_archive_titles_option()) { //IS POST TYPE ARCHIVE
			$seopress_titles_archive_titles_option = esc_attr(seopress_titles_archive_titles_option());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archive_titles_option);
			return $seopress_titles_title_template;
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_tax_titles_option()) { //IS TAX
			if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_titles_title',true)) {
				return esc_attr(get_term_meta(get_queried_object()->{'term_id'},'_seopress_titles_title',true));
			} else {
				$seopress_titles_tax_titles_option = esc_attr(seopress_titles_tax_titles_option());

				$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_tax_titles_option);
				return $seopress_titles_title_template;
			}		
		} elseif (is_author() && seopress_titles_archives_author_title_option()) { //IS AUTHOR
			$seopress_titles_archives_author_title_option = esc_attr(seopress_titles_archives_author_title_option());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_author_title_option);
			return $seopress_titles_title_template;
		} elseif (is_date() && seopress_titles_archives_date_title_option()) { //IS DATE
			$seopress_titles_archives_date_title_option = esc_attr(seopress_titles_archives_date_title_option());

			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_date_title_option);
			return $seopress_titles_title_template;
		} elseif (is_search() && seopress_titles_archives_search_title_option()) { //IS SEARCH
			$seopress_titles_archives_search_title_option = esc_attr(seopress_titles_archives_search_title_option());
			
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_search_title_option);
			return $seopress_titles_title_template;
		} elseif (is_404() && seopress_titles_archives_404_title_option()) { //IS 404
			$seopress_titles_archives_404_title_option = esc_attr(seopress_titles_archives_404_title_option());
			
			$seopress_titles_title_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_archives_404_title_option);
			return $seopress_titles_title_template;
		}
	}
	add_filter( 'pre_get_document_title', 'seopress_titles_the_title', 10 );

	//THE Meta Description
	function seopress_titles_the_description() {
		global $post;

		//Template variables
		$seopress_paged ='';
		$seopress_get_author ='';

		if (get_query_var('paged') >='1') {
			$seopress_paged = get_query_var('paged');
		}

		if (get_the_author_meta() !='') {
			$seopress_get_author = get_the_author_meta( 'display_name', $post->post_author );
		}

		if (get_the_excerpt() !='') {
			$seopress_get_the_excerpt = wp_trim_words(esc_html(get_the_excerpt()), 30);
		} elseif ($post !='') {
			if (get_post_field('post_content', $post->ID) !='') {
				$seopress_get_the_excerpt = wp_trim_words(esc_html(get_post_field('post_content', $post->ID)), 30);
			} else {
				$seopress_get_the_excerpt = null;
			}
		} else {
			$seopress_get_the_excerpt = null;
		}

		$seopress_titles_template_variables_array = array(
			'%%sitetitle%%', 
			'%%tagline%%',
			'%%post_title%%',
			'%%post_excerpt%%',
			'%%post_date%%',
			'%%post_author%%',
			'%%_category_title%%',
			'%%_category_description%%',
			'%%tag_title%%',
			'%%tag_description%%',
			'%%term_title%%',
			'%%term_description%%',
			'%%search_keywords%%',
			'%%current_pagination%%',
			'%%cpt_plural%%',
			'%%archive_date%%',
		);
		$seopress_titles_template_replace_array = array(
			get_bloginfo('name'), 
			get_bloginfo('description'),
			the_title_attribute('echo=0'),
			$seopress_get_the_excerpt,
			get_the_date(),
			$seopress_get_author,
			single_cat_title('', false),
			esc_html(category_description()),
			single_tag_title('', false),
			esc_html(tag_description()),
			single_term_title('', false),
			esc_html(term_description()),
			get_search_query(),
			$seopress_paged,
			post_type_archive_title('', false),
			get_the_archive_title(),
		);

		if ( is_front_page() && is_home() && get_post_meta($post->ID,'_seopress_titles_desc',true) =='' ) { //HOMEPAGE
			if (seopress_titles_home_site_desc_option() !='' && get_post_meta($post->ID,'_seopress_titles_desc',true) =='') { //IS GLOBAL
				$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_home_site_desc_option()).'" />';
				$seopress_titles_the_description .= "\n";

				$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
				echo $seopress_titles_description_template;
			}
		} elseif ( is_front_page() && get_post_meta($post->ID,'_seopress_titles_desc',true) ==''){ //STATIC HOMEPAGE
			if (seopress_titles_home_site_desc_option() !='' && get_post_meta($post->ID,'_seopress_titles_desc',true) =='') { //IS GLOBAL
				$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_home_site_desc_option()).'" />';
				$seopress_titles_the_description .= "\n";

				$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
				echo $seopress_titles_description_template;
			}
		} elseif ( is_home() && get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_desc',true) !=''){ //BLOG PAGE
			if (get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_desc',true)) { //IS METABOXE
				$seopress_titles_the_description_meta = get_post_meta(get_option( 'page_for_posts' ),'_seopress_titles_desc',true);
				$seopress_titles_the_description = '<meta name="description" content="'.esc_attr($seopress_titles_the_description_meta).'" />';
				$seopress_titles_the_description .= "\n";
				
				$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
				echo $seopress_titles_description_template;
			}
		} elseif (is_singular()) { //IS SINGLE
			if (get_post_meta($post->ID,'_seopress_titles_desc',true)) { //IS METABOXE
				$seopress_titles_the_description_meta = get_post_meta($post->ID,'_seopress_titles_desc',true);
				$seopress_titles_the_description = '<meta name="description" content="'.esc_attr($seopress_titles_the_description_meta).'" />';
				$seopress_titles_the_description .= "\n";
				
				$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
				echo $seopress_titles_description_template;
			} elseif (seopress_titles_single_desc_option() !='') { //IS GLOBAL
				$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_single_desc_option()).'" />';
				$seopress_titles_the_description .= "\n";
				
				$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
				echo $seopress_titles_description_template;
			} else {
				setup_postdata( $post );
				if (get_the_excerpt() !='' || get_the_content() !='') { //DEFAULT EXCERPT OR THE CONTENT
					$seopress_titles_the_description = '<meta name="description" content="'.wp_trim_words(esc_html(get_the_excerpt()), 30).'" />';
					$seopress_titles_the_description .= "\n";
					
					$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
					echo $seopress_titles_description_template;
				}
			}
		} elseif (is_post_type_archive() && seopress_titles_archive_desc_option()) { //IS POST TYPE ARCHIVE
			$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_archive_desc_option()).'" />';
			$seopress_titles_the_description .= "\n";
			
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
			echo $seopress_titles_description_template;
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_tax_desc_option()) { //IS TAX
			if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_titles_desc',true)) {
				$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(get_term_meta(get_queried_object()->{'term_id'},'_seopress_titles_desc',true)).'" />';
				$seopress_titles_the_description .= "\n";
				echo $seopress_titles_the_description;
			} else {
				$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_tax_desc_option()).'" />';
				$seopress_titles_the_description .= "\n";
				
				$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
				echo $seopress_titles_description_template;
			}
		} elseif (is_author() && seopress_titles_archives_author_desc_option()) { //IS AUTHOR
			$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_archives_author_desc_option()).'" />';
			$seopress_titles_the_description .= "\n";
			
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
			echo $seopress_titles_description_template;
		} elseif (is_date() && seopress_titles_archives_date_desc_option()) { //IS DATE
			$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_archives_date_desc_option()).'" />';
			$seopress_titles_the_description .= "\n";

			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
			echo $seopress_titles_description_template;
		} elseif (is_search() && seopress_titles_archives_search_desc_option()) { //IS SEARCH
			$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_archives_search_desc_option()).'" />';
			$seopress_titles_the_description .= "\n";
			
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
			echo $seopress_titles_description_template;
		} elseif (is_404() && seopress_titles_archives_404_desc_option()) { //IS 404
			$seopress_titles_the_description = '<meta name="description" content="'.esc_attr(seopress_titles_archives_404_desc_option()).'" />';
			$seopress_titles_the_description .= "\n";
			
			$seopress_titles_description_template = str_replace($seopress_titles_template_variables_array, $seopress_titles_template_replace_array, $seopress_titles_the_description);
			echo $seopress_titles_description_template;
		}
	}	
	add_action( 'wp_head', 'seopress_titles_the_description', 1 );

	//Advanced
	//noindex
	//Single CPT noindex
	function seopress_titles_single_cpt_noindex_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_single_cpt_noindex_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_single_cpt_noindex_option ) ) {
			foreach ($seopress_titles_single_cpt_noindex_option as $key => $seopress_titles_single_cpt_noindex_value)
				$options[$key] = $seopress_titles_single_cpt_noindex_value;
			 if (isset($seopress_titles_single_cpt_noindex_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['noindex'])) { 
			 	return $seopress_titles_single_cpt_noindex_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['noindex'];
			 }
		}
	};

	//Archive CPT noindex
	function seopress_titles_archive_cpt_noindex_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_archive_cpt_noindex_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archive_cpt_noindex_option ) ) {
			foreach ($seopress_titles_archive_cpt_noindex_option as $key => $seopress_titles_archive_cpt_noindex_value)
				$options[$key] = $seopress_titles_archive_cpt_noindex_value;
			 if (isset($seopress_titles_archive_cpt_noindex_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['noindex'])) { 
			 	return $seopress_titles_archive_cpt_noindex_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['noindex'];
			 }
		}
	};

	//Tax archive noindex
	function seopress_titles_tax_noindex_option() {
		$queried_object = get_queried_object();
		$seopress_get_current_tax = $queried_object->taxonomy;

		if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_index',true) == 'yes') {
			return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_index',true);
		} else {
			$seopress_titles_tax_noindex_option = get_option("seopress_titles_option_name");
			if ( ! empty ( $seopress_titles_tax_noindex_option ) ) {
				foreach ($seopress_titles_tax_noindex_option as $key => $seopress_titles_tax_noindex_value)
					$options[$key] = $seopress_titles_tax_noindex_value;
				 if (isset($seopress_titles_tax_noindex_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['noindex'])) { 
				 	return $seopress_titles_tax_noindex_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['noindex'];
				 }
			}
		}
	};

	//noindex Author archives
	function seopress_titles_archives_author_noindex_option() {
		$seopress_titles_archives_author_noindex_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_author_noindex_option ) ) {
			foreach ($seopress_titles_archives_author_noindex_option as $key => $seopress_titles_archives_author_noindex_value)
				$options[$key] = $seopress_titles_archives_author_noindex_value;
			 if (isset($seopress_titles_archives_author_noindex_option['seopress_titles_archives_author_noindex'])) { 
			 	return $seopress_titles_archives_author_noindex_option['seopress_titles_archives_author_noindex'];
			 }
		}
	};

	//noindex Date archives
	function seopress_titles_archives_date_noindex_option() {
		$seopress_titles_archives_date_noindex_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archives_date_noindex_option ) ) {
			foreach ($seopress_titles_archives_date_noindex_option as $key => $seopress_titles_archives_date_noindex_value)
				$options[$key] = $seopress_titles_archives_date_noindex_value;
			 if (isset($seopress_titles_archives_date_noindex_option['seopress_titles_archives_date_noindex'])) { 
			 	return $seopress_titles_archives_date_noindex_option['seopress_titles_archives_date_noindex'];
			 }
		}
	};	

	//noindex Global Advanced tab
	function seopress_titles_noindex_option() {
		$seopress_titles_noindex_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_noindex_option ) ) {
			foreach ($seopress_titles_noindex_option as $key => $seopress_titles_noindex_value)
				$options[$key] = $seopress_titles_noindex_value;
			 if (isset($seopress_titles_noindex_option['seopress_titles_noindex'])) { 
			 	return $seopress_titles_noindex_option['seopress_titles_noindex'];
			 }
		}
	};

	//noindex single CPT
	function seopress_titles_noindex_post_option() {
		$_seopress_robots_index = get_post_meta(get_the_ID(),'_seopress_robots_index',true);
		if ($_seopress_robots_index == 'yes') {
			return $_seopress_robots_index;
		}
	};

	//noindex WooCommerce page
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active('wp-seopress-pro/seopress-pro.php')) {
		//Cart page
		function seopress_woocommerce_cart_page_no_index_option() {
			$seopress_woocommerce_cart_page_no_index_option = get_option("seopress_pro_option_name");
			if ( ! empty ( $seopress_woocommerce_cart_page_no_index_option ) ) {
				foreach ($seopress_woocommerce_cart_page_no_index_option as $key => $seopress_woocommerce_cart_page_no_index_value)
					$options[$key] = $seopress_woocommerce_cart_page_no_index_value;
				 if (isset($seopress_woocommerce_cart_page_no_index_option['seopress_woocommerce_cart_page_no_index'])) { 
				 	return $seopress_woocommerce_cart_page_no_index_option['seopress_woocommerce_cart_page_no_index'];
				 }
			}
		}
		function seopress_woocommerce_checkout_page_no_index_option() {
			$seopress_woocommerce_checkout_page_no_index_option = get_option("seopress_pro_option_name");
			if ( ! empty ( $seopress_woocommerce_checkout_page_no_index_option ) ) {
				foreach ($seopress_woocommerce_checkout_page_no_index_option as $key => $seopress_woocommerce_checkout_page_no_index_value)
					$options[$key] = $seopress_woocommerce_checkout_page_no_index_value;
				 if (isset($seopress_woocommerce_checkout_page_no_index_option['seopress_woocommerce_checkout_page_no_index'])) { 
				 	return $seopress_woocommerce_checkout_page_no_index_option['seopress_woocommerce_checkout_page_no_index'];
				 }
			}
		}
		function seopress_woocommerce_customer_account_page_no_index_option() {
			$seopress_woocommerce_customer_account_page_no_index_option = get_option("seopress_pro_option_name");
			if ( ! empty ( $seopress_woocommerce_customer_account_page_no_index_option ) ) {
				foreach ($seopress_woocommerce_customer_account_page_no_index_option as $key => $seopress_woocommerce_customer_account_page_no_index_value)
					$options[$key] = $seopress_woocommerce_customer_account_page_no_index_value;
				 if (isset($seopress_woocommerce_customer_account_page_no_index_option['seopress_woocommerce_customer_account_page_no_index'])) { 
				 	return $seopress_woocommerce_customer_account_page_no_index_option['seopress_woocommerce_customer_account_page_no_index'];
				 }
			}
		}
	}

	function seopress_titles_noindex_bypass() {
		if (seopress_titles_noindex_option()) { //Single CPT Global Advanced tab
			return seopress_titles_noindex_option(); 
		} elseif (is_singular() && seopress_titles_single_cpt_noindex_option()) { //Single CPT Global
			return seopress_titles_single_cpt_noindex_option();
		} elseif (is_singular() && seopress_titles_noindex_post_option() ) { //Single CPT Metaboxe
			return seopress_titles_noindex_post_option();
		} elseif (is_post_type_archive() && seopress_titles_archive_cpt_noindex_option() ) { //IS POST TYPE ARCHIVE
			return seopress_titles_archive_cpt_noindex_option();
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_tax_noindex_option()) { //IS TAX
			return seopress_titles_tax_noindex_option();
		} elseif (is_author() && seopress_titles_archives_author_noindex_option()) { //IS Author archive
			return seopress_titles_archives_author_noindex_option();
		} elseif (is_date() && seopress_titles_archives_date_noindex_option()) { //IS Date archive
			return seopress_titles_archives_date_noindex_option();
		} elseif (function_exists('is_cart') && function_exists('seopress_woocommerce_cart_page_no_index_option') && (is_cart() && seopress_woocommerce_cart_page_no_index_option())) { //IS WooCommerce Cart page
			return seopress_woocommerce_cart_page_no_index_option();
		} elseif (function_exists('is_checkout') && function_exists('seopress_woocommerce_checkout_page_no_index_option') && (is_checkout() && seopress_woocommerce_checkout_page_no_index_option())) { //IS WooCommerce Checkout page
			return seopress_woocommerce_checkout_page_no_index_option();
		} elseif (function_exists('is_account_page') && function_exists('seopress_woocommerce_customer_account_page_no_index_option') && (is_account_page() && seopress_woocommerce_customer_account_page_no_index_option())) { //IS WooCommerce Customer account pages
			return seopress_woocommerce_customer_account_page_no_index_option();
		} elseif (function_exists('is_wc_endpoint_url') && function_exists('seopress_woocommerce_customer_account_page_no_index_option') && (is_wc_endpoint_url() && seopress_woocommerce_customer_account_page_no_index_option())) { //IS WooCommerce Customer account pages
			return seopress_woocommerce_customer_account_page_no_index_option();
		} elseif(is_404()) { //Is 404 page
			return "noindex";
		}
	}

	//nofollow
	//Single CPT nofollow
	function seopress_titles_single_cpt_nofollow_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_single_cpt_nofollow_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_single_cpt_nofollow_option ) ) {
			foreach ($seopress_titles_single_cpt_nofollow_option as $key => $seopress_titles_single_cpt_nofollow_value)
				$options[$key] = $seopress_titles_single_cpt_nofollow_value;
			 if (isset($seopress_titles_single_cpt_nofollow_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['nofollow'])) { 
			 	return $seopress_titles_single_cpt_nofollow_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['nofollow'];
			 }
		}
	};

	//Archive CPT nofollow
	function seopress_titles_archive_cpt_nofollow_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_archive_cpt_nofollow_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_archive_cpt_nofollow_option ) ) {
			foreach ($seopress_titles_archive_cpt_nofollow_option as $key => $seopress_titles_archive_cpt_nofollow_value)
				$options[$key] = $seopress_titles_archive_cpt_nofollow_value;
			 if (isset($seopress_titles_archive_cpt_nofollow_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['nofollow'])) { 
			 	return $seopress_titles_archive_cpt_nofollow_option['seopress_titles_archive_titles'][$seopress_get_current_cpt]['nofollow'];
			 }
		}
	};

	//Tax archive nofollow
	function seopress_titles_tax_nofollow_option() {
		$queried_object = get_queried_object();
		$seopress_get_current_tax = $queried_object->taxonomy;

		if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_follow',true) == 'yes') {
			return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_follow',true);
		} else {
			$seopress_titles_tax_nofollow_option = get_option("seopress_titles_option_name");
			if ( ! empty ( $seopress_titles_tax_nofollow_option ) ) {
				foreach ($seopress_titles_tax_nofollow_option as $key => $seopress_titles_tax_nofollow_value)
					$options[$key] = $seopress_titles_tax_nofollow_value;
				 if (isset($seopress_titles_tax_nofollow_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['nofollow'])) { 
				 	return $seopress_titles_tax_nofollow_option['seopress_titles_tax_titles'][$seopress_get_current_tax]['nofollow'];
				 }
			}
		}
	};

	//nofollow Global Avanced tab
	function seopress_titles_nofollow_option() {
		$seopress_titles_nofollow_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_nofollow_option ) ) {
			foreach ($seopress_titles_nofollow_option as $key => $seopress_titles_nofollow_value)
				$options[$key] = $seopress_titles_nofollow_value;
			 if (isset($seopress_titles_nofollow_option['seopress_titles_nofollow'])) { 
			 	return $seopress_titles_nofollow_option['seopress_titles_nofollow'];
			 }
		}
	};

	function seopress_titles_nofollow_post_option() {
		$_seopress_robots_follow = get_post_meta(get_the_ID(),'_seopress_robots_follow',true);
		if ($_seopress_robots_follow == 'yes') {
			return $_seopress_robots_follow;
		}
	};

	function seopress_titles_nofollow_bypass() {
		if (seopress_titles_nofollow_option()) { //Single CPT Global Advanced tab
			return seopress_titles_nofollow_option(); 
		} elseif (is_singular() && seopress_titles_single_cpt_nofollow_option()) { //Single CPT Global
			return seopress_titles_single_cpt_nofollow_option();
		} elseif (is_singular() && seopress_titles_nofollow_post_option() ) { //Single CPT Metaboxe
			return seopress_titles_nofollow_post_option();
		} elseif (is_post_type_archive() && seopress_titles_archive_cpt_nofollow_option() ) { //IS POST TYPE ARCHIVE
			return seopress_titles_archive_cpt_nofollow_option();
		} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_tax_nofollow_option()) { //IS TAX
			return seopress_titles_tax_nofollow_option();
		}
	};

	//date in SERPs
	function seopress_titles_single_cpt_date_option() {
		global $post;
		$seopress_get_current_cpt = get_post_type($post);

		$seopress_titles_single_cpt_date_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_single_cpt_date_option ) ) {
			foreach ($seopress_titles_single_cpt_date_option as $key => $seopress_titles_single_cpt_date_value)
				$options[$key] = $seopress_titles_single_cpt_date_value;
			 if (isset($seopress_titles_single_cpt_date_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['date'])) { 
			 	return $seopress_titles_single_cpt_date_option['seopress_titles_single_titles'][$seopress_get_current_cpt]['date'];
			 }
		}
	};

	function seopress_titles_single_cpt_date_hook() {
		if (!is_front_page() && !is_home()) {
			if (is_singular() && seopress_titles_single_cpt_date_option() =='1') {
				$seopress_get_current_pub_post_date = get_the_date('c');
				$seopress_get_current_up_post_date = get_the_modified_date('c');
				echo '<meta property="article:published_time" content="'.$seopress_get_current_pub_post_date.'" />';
				echo "\n";
				echo '<meta property="article:modified_time" content="'.$seopress_get_current_up_post_date.'" />';
				echo "\n";
				echo '<meta property="og:updated_time" content="'.$seopress_get_current_up_post_date.'" />';
				echo "\n";
			}
		}
	}
	add_action( 'wp_head', 'seopress_titles_single_cpt_date_hook', 1 );

	//noodp
	function seopress_titles_noodp_option() {
		$seopress_titles_noodp_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_noodp_option ) ) {
			foreach ($seopress_titles_noodp_option as $key => $seopress_titles_noodp_value)
				$options[$key] = $seopress_titles_noodp_value;
			 if (isset($seopress_titles_noodp_option['seopress_titles_noodp'])) { 
			 	return $seopress_titles_noodp_option['seopress_titles_noodp'];
			 }
		}
	};

	function seopress_titles_noodp_post_option() {
		$_seopress_robots_odp = get_post_meta(get_the_ID(),'_seopress_robots_odp',true);
		if ($_seopress_robots_odp == 'yes') {
			return $_seopress_robots_odp;
		}
	};

	function seopress_titles_noodp_bypass() {
		if (seopress_titles_noodp_option()) {
			return seopress_titles_noodp_option(); 
		}
		elseif (is_singular() && seopress_titles_noodp_post_option()) {
			return seopress_titles_noodp_post_option();
		} elseif (is_tax() || is_category() || is_tag()) {
			if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_odp',true) == 'yes') {
				return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_odp',true);
			}
		}
	};

	//noarchive
	function seopress_titles_noarchive_option() {
		$seopress_titles_noarchive_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_noarchive_option ) ) {
			foreach ($seopress_titles_noarchive_option as $key => $seopress_titles_noarchive_value)
				$options[$key] = $seopress_titles_noarchive_value;
			 if (isset($seopress_titles_noarchive_option['seopress_titles_noarchive'])) { 
			 	return $seopress_titles_noarchive_option['seopress_titles_noarchive'];
			 }
		}
	};

	function seopress_titles_noarchive_post_option() {
		$_seopress_robots_archive = get_post_meta(get_the_ID(),'_seopress_robots_archive',true);
		if ($_seopress_robots_archive == 'yes') {
			return $_seopress_robots_archive;
		}
	};

	function seopress_titles_noarchive_bypass() {
		if (seopress_titles_noarchive_option()) {
			return seopress_titles_noarchive_option(); 
		}
		elseif (is_singular() && seopress_titles_noarchive_post_option()) {
			return seopress_titles_noarchive_post_option();
		} elseif (is_tax() || is_category() || is_tag()) {
			if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_archive',true) == 'yes') {
				return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_archive',true);
			}
		}
	};

	//nosnippet
	function seopress_titles_nosnippet_option() {
		$seopress_titles_nosnippet_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_nosnippet_option ) ) {
			foreach ($seopress_titles_nosnippet_option as $key => $seopress_titles_nosnippet_value)
				$options[$key] = $seopress_titles_nosnippet_value;
			 if (isset($seopress_titles_nosnippet_option['seopress_titles_nosnippet'])) { 
			 	return $seopress_titles_nosnippet_option['seopress_titles_nosnippet'];
			 }
		}
	};

	function seopress_titles_nosnippet_post_option() {
		$_seopress_robots_snippet = get_post_meta(get_the_ID(),'_seopress_robots_snippet',true);
		if ($_seopress_robots_snippet == 'yes') {
			return $_seopress_robots_snippet;
		}
	};

	function seopress_titles_nosnippet_bypass() {
		if (seopress_titles_nosnippet_option()) {
			return seopress_titles_nosnippet_option(); 
		}
		elseif (is_singular() && seopress_titles_nosnippet_post_option()) {
			return seopress_titles_nosnippet_post_option();
		} elseif (is_tax() || is_category() || is_tag()) {
			if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_snippet',true) == 'yes') {
				return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_snippet',true);
			}
		}
	}

	if (seopress_titles_noindex_bypass() || seopress_titles_nofollow_bypass() || seopress_titles_noodp_bypass() || seopress_titles_noarchive_bypass() || seopress_titles_nosnippet_bypass() ) {
		function seopress_titles_advanced_robots_hook() {

			$seopress_comma_array = array();

			if (seopress_titles_noindex_bypass() !='') {
				$seopress_titles_noindex = 'noindex';
				array_push($seopress_comma_array, $seopress_titles_noindex);
			}
			if (seopress_titles_nofollow_bypass() !='') {
				$seopress_titles_nofollow = 'nofollow';
				array_push($seopress_comma_array, $seopress_titles_nofollow);
			}
			if (seopress_titles_noodp_bypass() !='') {
				$seopress_titles_noodp = 'noodp';
				array_push($seopress_comma_array, $seopress_titles_noodp);
			}
			if (seopress_titles_noarchive_bypass() !='') {
				$seopress_titles_noarchive = 'noarchive';
				array_push($seopress_comma_array, $seopress_titles_noarchive);
			}
			if (seopress_titles_nosnippet_bypass() !='') {
				$seopress_titles_nosnippet = 'nosnippet';
				array_push($seopress_comma_array, $seopress_titles_nosnippet);
			}

			echo '<meta name="robots" content="';

			$seopress_comma_count = count($seopress_comma_array);
			for ($i = 0; $i < $seopress_comma_count; $i++) {
				echo $seopress_comma_array[$i];
			   	if ($i < ($seopress_comma_count - 1)) {
			    	echo ', ';
			   	}
			}

			echo '" />'; 
			echo "\n";
		}	
		add_action( 'wp_head', 'seopress_titles_advanced_robots_hook', 1 );
	};

	//noimageindex
	function seopress_titles_noimageindex_option() {
		$seopress_titles_noimageindex_option = get_option("seopress_titles_option_name");
		if ( ! empty ( $seopress_titles_noimageindex_option ) ) {
			foreach ($seopress_titles_noimageindex_option as $key => $seopress_titles_noimageindex_value)
				$options[$key] = $seopress_titles_noimageindex_value;
			 if (isset($seopress_titles_noimageindex_option['seopress_titles_noimageindex'])) { 
			 	return $seopress_titles_noimageindex_option['seopress_titles_noimageindex'];
			 }
		}
	};

	function seopress_titles_noimageindex_post_option() {
		$_seopress_robots_imageindex = get_post_meta(get_the_ID(),'_seopress_robots_imageindex',true);
		if ($_seopress_robots_imageindex == 'yes') {
			return $_seopress_robots_imageindex;
		}
	};

	function seopress_titles_noimageindex_bypass() {
		if (seopress_titles_noimageindex_option()) {
			return seopress_titles_noimageindex_option(); 
		}
		elseif (is_singular() && seopress_titles_noimageindex_post_option()) {
			return seopress_titles_noimageindex_post_option();
		} elseif (is_tax() || is_category() || is_tag()) {
			if (get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_imageindex',true) == 'yes') {
				return get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_imageindex',true);
			}
		}
	};

	if (seopress_titles_noimageindex_bypass()) {
		function seopress_titles_advanced_google_hook() {
			$seopress_titles_noimageindex = '<meta name="google" content="noimageindex" />';
			echo $seopress_titles_noimageindex."\n";
		}	
		add_action( 'wp_head', 'seopress_titles_advanced_google_hook', 1 );
	};

	//link rel prev/next
	if (seopress_titles_paged_rel_option()) {
		function seopress_titles_paged_rel_hook() {
			global $paged;
			if ( get_previous_posts_link() ) { ?>
		        <link rel="prev" href="<?php echo get_pagenum_link( $paged - 1 ); ?>" />
		    <?php } if ( get_next_posts_link() ) { ?>
		        <link rel="next" href="<?php echo get_pagenum_link( $paged + 1 ); ?>" />
		    <?php }
		}
		add_action( 'wp_head', 'seopress_titles_paged_rel_hook', 9 );
	}
} //END blog_public

//canonical
function seopress_titles_canonical_post_option() {
	$_seopress_robots_canonical = get_post_meta(get_the_ID(),'_seopress_robots_canonical',true);
	if ($_seopress_robots_canonical != '') {
		return $_seopress_robots_canonical;
	}
}

function seopress_titles_canonical_term_option() {
	$_seopress_robots_canonical = get_term_meta(get_queried_object()->{'term_id'},'_seopress_robots_canonical',true);
	if ($_seopress_robots_canonical != '') {
		return $_seopress_robots_canonical;
	}
}

if ( is_singular() && seopress_titles_canonical_post_option()) {
	function seopress_titles_canonical_post_hook() {
		$seopress_titles_canonical = '<link rel="canonical" href="'.seopress_titles_canonical_post_option().'" />';
		echo $seopress_titles_canonical."\n";
	}	
	add_action( 'wp_head', 'seopress_titles_canonical_post_hook', 1 );
} elseif ((is_tax() || is_category() || is_tag()) && seopress_titles_canonical_term_option()) {
	function seopress_titles_canonical_term_hook() {
		$seopress_titles_canonical = '<link rel="canonical" href="'.seopress_titles_canonical_term_option().'" />';
		echo $seopress_titles_canonical."\n";
	}	
	add_action( 'wp_head', 'seopress_titles_canonical_term_hook', 1 );
} else {
	function seopress_titles_canonical_hook() {
		global $wp;
		if (seopress_advanced_advanced_trailingslash_option()) {
			$current_url = home_url(add_query_arg(array(), $wp->request));
		} else {
			$current_url = trailingslashit(home_url(add_query_arg(array(), $wp->request)));
		}
		if (is_search()) {
			$seopress_titles_canonical = '<link rel="canonical" href="'.get_home_url().'/search/'.get_search_query().'" />';
		} elseif (is_paged()){
			$seopress_titles_canonical = '<link rel="canonical" href="'.get_pagenum_link('1').'" />';
		} else {
			$seopress_titles_canonical = '<link rel="canonical" href="'.$current_url.'" />';
		}
		echo $seopress_titles_canonical."\n";
	}	
	add_action( 'wp_head', 'seopress_titles_canonical_hook', 1 );
}
