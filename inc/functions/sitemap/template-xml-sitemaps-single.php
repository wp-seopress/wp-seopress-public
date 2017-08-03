<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

//XML
Header('Content-type: text/xml');

//Robots
Header("X-Robots-Tag: noindex", true);

function seopress_xml_sitemap_img_enable_option() {
	$seopress_xml_sitemap_img_enable_option = get_option("seopress_xml_sitemap_option_name");
	if ( ! empty ( $seopress_xml_sitemap_img_enable_option ) ) {
		foreach ($seopress_xml_sitemap_img_enable_option as $key => $seopress_xml_sitemap_img_enable_value)
			$options[$key] = $seopress_xml_sitemap_img_enable_value;
		 if (isset($seopress_xml_sitemap_img_enable_option['seopress_xml_sitemap_img_enable'])) { 
		 	return $seopress_xml_sitemap_img_enable_option['seopress_xml_sitemap_img_enable'];
		 }
	}
}

function seopress_xml_sitemap_single() {
	$path = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ".xml");
	$seopress_sitemaps = '<?xml version="1.0" encoding="UTF-8"?>';
	$seopress_sitemaps .='<?xml-stylesheet type="text/xsl" href="'.get_home_url().'/sitemaps_xsl.xsl"?>';
	$seopress_sitemaps .= "\n";
	$seopress_sitemaps .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
	$seopress_sitemaps .= "\n";
	
				$args = array( 'posts_per_page' => 1000, 'order'=> 'DESC', 'orderby' => 'modified', 'post_type' => $path, 'post_status' => 'publish', 'meta_key' => '_seopress_robots_index', 'meta_value' => 'yes', 'meta_compare' => 'NOT EXISTS', 'fields' => 'ids', 'lang' => '' );
				$postslist = get_posts( $args );
				foreach ( $postslist as $post ) {
				  	setup_postdata( $post );

				  	$seopress_sitemaps .= '<url>';
				  	$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '<loc>';
					$seopress_sitemaps .= get_permalink($post);
					$seopress_sitemaps .= '</loc>';
					$seopress_sitemaps .= "\n";
					$seopress_sitemaps .= '<lastmod>';
					$seopress_sitemaps .= get_the_modified_date('c', $post);
					$seopress_sitemaps .= '';
					$seopress_sitemaps .= '</lastmod>';
					$seopress_sitemaps .= "\n";
					
					//XML Image Sitemaps
					if (seopress_xml_sitemap_img_enable_option() =='1') {
						
						//Standard images
						if (!empty (get_the_content($post))) {
							$dom = new domDocument;
							$dom->loadHTML(get_the_content($post));
							$dom->preserveWhiteSpace = false;
							if ($dom->getElementsByTagName('img') !='') {
								$images = $dom->getElementsByTagName('img');
							}
						}

						//WooCommerce
						global $product;
						if ($product !='') {
							$product_img = $product->get_gallery_attachment_ids();
						}

						//Galleries
						if (get_post_galleries_images($post) !='') {
							$galleries = get_post_galleries_images($post);
						}

						//Post Thumbnail
						$post_thumbnail = get_the_post_thumbnail_url($post);

						if ((isset($images) && !empty ($images) && $images->length>=1) || (isset($galleries) && !empty($galleries)) || (isset($product) && !empty($product_img)) || $post_thumbnail !='') { 
							
							//Standard img
							if (isset($images) && !empty ($images)) {
								if ($images->length>=1) {
									foreach($images as $img) {
								        $url = $img->getAttribute('src');
								        $seopress_sitemaps .= '<image:image>';
								        $seopress_sitemaps .= "\n";
								       	$seopress_sitemaps .= '<image:loc>';
										$seopress_sitemaps .= '<![CDATA['.$url.']]>';
								        $seopress_sitemaps .= '</image:loc>';
								        $seopress_sitemaps .= "\n";
								        $seopress_sitemaps .= '</image:image>';
								    }
								}
							}
							//Galleries
							if ($galleries !='') {
								foreach( $galleries as $gallery ) {
									foreach( $gallery as $url ) {
										$seopress_sitemaps .= '<image:image>';
										$seopress_sitemaps .= "\n";
								       	$seopress_sitemaps .= '<image:loc>';
										$seopress_sitemaps .= '<![CDATA['.$url.']]>';
								        $seopress_sitemaps .= '</image:loc>';
								        $seopress_sitemaps .= "\n";
								        $seopress_sitemaps .= '</image:image>';
									}
								}
							}
							//WooCommerce img
							if ($product !='' && $product_img !='') {
								foreach( $product_img as $product_attachment_id ) {
									$seopress_sitemaps .= '<image:image>';
									$seopress_sitemaps .= "\n";
							       	$seopress_sitemaps .= '<image:loc>';
									$seopress_sitemaps .= '<![CDATA['.wp_get_attachment_url( $product_attachment_id ).']]>';
							        $seopress_sitemaps .= '</image:loc>';
							        $seopress_sitemaps .= "\n";
							        $seopress_sitemaps .= '</image:image>';
								}
							}
							//Post thumbnail
							if ($post_thumbnail !='') {
								$seopress_sitemaps .= '<image:image>';
								$seopress_sitemaps .= "\n";
						       	$seopress_sitemaps .= '<image:loc>';
								$seopress_sitemaps .= '<![CDATA['.$post_thumbnail.']]>';
						        $seopress_sitemaps .= '</image:loc>';
						        $seopress_sitemaps .= "\n";
						        $seopress_sitemaps .= '</image:image>';
							}

						    $seopress_sitemaps .= "\n";
						}
					}
					$seopress_sitemaps .= '</url>';
					$seopress_sitemaps .= "\n";
				}
				wp_reset_postdata();

	$seopress_sitemaps .= '</urlset>';
	return $seopress_sitemaps;
} 
echo seopress_xml_sitemap_single();

?>