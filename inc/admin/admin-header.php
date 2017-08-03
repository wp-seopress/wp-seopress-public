<?php

defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

function seopress_admin_header() { ?>
    <div id="seopress-header">
    	<div id="seopress-admin">
            <div id="seopress-navbar">
    			<h1>
    				<span class="screen-reader-text"><?php _e( 'SEOPress', 'wp-seopress' ); ?></span>
                    <?php if ( is_plugin_active( 'seopress-pro/seopress-pro.php' ) ) { ?>
                        <span class="seopress-info-version">
                            <strong>
                                <?php _e('PRO', 'wp-seopress'); ?>
                                <?php echo SEOPRESSPRO_VERSION; ?>
                            </strong>
                        </span>
                    <?php } else { ?>
                        <span class="seopress-info-version"><?php echo SEOPRESS_VERSION; ?></span>
                    <?php } ?>
    			</h1>
                <div id="seopress-notice">
                    <div class="small">
                        <span class="dashicons dashicons-wordpress"></span>
                        <?php _e( 'You like SEOPress? Don\'t forget to rate it 5 stars!', 'wp-seopress' ); ?>

                        <div class="wporg-ratings rating-stars">
                            <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=1#postform" data-rating="1" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                            <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=2#postform" data-rating="2" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                            <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=3#postform" data-rating="3" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                            <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=4#postform" data-rating="4" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                            <a href="//wordpress.org/support/view/plugin-reviews/wp-seopress?rate=5#postform" data-rating="5" title="" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#e6b800 !important;"></span></a>
                        </div>
                        <script>
                            jQuery(document).ready( function($) {
                                $('.rating-stars').find('a').hover(
                                    function() {
                                        $(this).nextAll('a').children('span').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
                                        $(this).prevAll('a').children('span').removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
                                        $(this).children('span').removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
                                    }, function() {
                                        var rating = $('input#rating').val();
                                        if (rating) {
                                            var list = $('.rating-stars a');
                                            list.children('span').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
                                            list.slice(0, rating).children('span').removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
                                        }
                                    }
                                );
                            });
                        </script>
                    </div>
                    <div class="small">
                        <a href="http://twitter.com/wpcloudy" target="_blank"><div class="dashicons dashicons-twitter"></div></a>
                        <a href="https://www.seopress.org/" target="_blank"><div class="dashicons dashicons-info"></div></a>
                        &nbsp;
                        <a href="https://www.seopress.org/support" target="_blank"><?php _e( 'Support', 'wp-seopress' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>