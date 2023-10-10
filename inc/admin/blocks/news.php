<?php
    // To prevent calling the plugin directly
    if ( ! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }

    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else {
            $docs = seopress_get_docs_links();

            $class = '1' !== seopress_get_service('AdvancedOption')->getAppearanceNews() ? 'is-active' : '';
        ?>

        <div id="seopress-news-panel" class="seopress-card <?php echo $class; ?>" style="display: none">
            <div class="seopress-card-title">
                <h2><?php _e('Latest News from SEOPress Blog', 'wp-seopress'); ?></h2>
            </div>
            <div class="seopress-card-content">
                <?php
                include_once ABSPATH . WPINC . '/feed.php';

                // Get a SimplePie feed object from the specified feed source.

                $wplang         = get_locale();

                $rss = fetch_feed('https://www.seopress.org/feed');

                $fr = [
                    'fr_FR',
                    'fr_BE',
                    'fr_CA'
                ];

                if (in_array($wplang, $fr)) {
                    $rss = fetch_feed('https://www.seopress.org/fr/feed');
                }

                $maxitems = 4;

                if ( ! is_wp_error($rss)) { // Checks that the object is created correctly
                    // Build an array of all the items, starting with element 0 (first element).
                    $rss_items = $rss->get_items(0, $maxitems);
                }
                ?>

                <div class="seopress-articles">
                    <?php if (0 == $maxitems) { ?>
                        <p>
                            <?php _e('No items', 'wp-seopress'); ?>
                        </p>
                    <?php } else {
                        foreach ($rss_items as $item) {
                            $class = "seopress-article";
                            ?>
                            <article class="<?php echo $class; ?>">
                                <div>
                                    <?php if ($enclosure = $item->get_enclosure()) {
                                        $img = $enclosure->get_link();
                                        echo '<img src="' . $img . '" class="seopress-thumb" alt="'.sprintf(__('Post thumbnail of %s'), esc_html($item->get_title())).'" decoding="async" loading="lazy"/>';
                                    } ?>

                                    <p class="seopress-item-category">
                                        <?php foreach ($item->get_categories() as $category) {
                                            echo $category->get_label();
                                        } ?>
                                    </p>

                                    <h3 class="seopress-item-title">
                                        <a href="<?php echo esc_url($item->get_permalink()); ?>" target="_blank" title="<?php /* translators: %s blog post URL */ printf(__('Learn more about %s in a new tab', 'wp-seopress'), esc_html($item->get_title())); ?>">
                                            <?php echo esc_html($item->get_title()); ?>
                                        </a>
                                    </h3>

                                    <p class="seopress-item-content"><?php echo $item->get_description(); ?></p>
                                </div>
                                <div class="seopress-item-wrap-content">
                                    <a class="btn btnSecondary" href="<?php echo esc_url($item->get_permalink()); ?>" target="_blank" title="<?php /* translators: %s blog post URL */ printf(__('Learn more about %s in a new tab', 'wp-seopress'), esc_html($item->get_title())); ?>">
                                        <?php _e('Learn more', 'wp-seopress'); ?>
                                    </a>
                                </div>
                            </article>
                        <?php
                        }
                    } ?>
                </div>
            </div>
        </div>
<?php }
