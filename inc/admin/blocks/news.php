<?php
    // To prevent calling the plugin directly
    if ( ! function_exists('add_action')) {
        echo 'Please don&rsquo;t call the plugin directly. Thanks :)';
        exit;
    }

    if (defined('SEOPRESS_WL_ADMIN_HEADER') && SEOPRESS_WL_ADMIN_HEADER === false) {
        //do nothing
    } else {

            //News Center
            function seopress_advanced_appearance_news_option() {
                $seopress_advanced_appearance_news_option = get_option('seopress_advanced_option_name');
                if ( ! empty($seopress_advanced_appearance_news_option)) {
                    foreach ($seopress_advanced_appearance_news_option as $key => $seopress_advanced_appearance_news_value) {
                        $options[$key] = $seopress_advanced_appearance_news_value;
                    }
                    if (isset($seopress_advanced_appearance_news_option['seopress_advanced_appearance_news'])) {
                        return $seopress_advanced_appearance_news_option['seopress_advanced_appearance_news'];
                    }
                }
            }

            $docs = seopress_get_docs_links();

            $class = '1' != seopress_advanced_appearance_news_option() ? 'is-active' : '';
        ?>

        <div id="seopress-news-panel"
            class="seopress-card <?php echo $class; ?>"
            style="display: none">
            <div class="seopress-card-title">
                <h2><?php _e('Latest News from SEOPress Blog', 'wp-seopress'); ?>
                </h2>

                <div>
                    <span class="dashicons dashicons-sort"></span>

                    <span class="seopress-item-toggle-options"></span>

                    <div class="seopress-card-popover">
                        <?php
                            $options = get_option('seopress_dashboard_option_name');
                            $value   = isset($options['news_max_items']) ? esc_attr($options['news_max_items']) : 5;
                        ?>

                        <p>
                            <label for="seopress_dashboard_option_name[news_max_items]">
                                <?php _e('How many items would you like to display?', 'wp-seopress'); ?>
                            </label>
                            <input id="news_max_items" name="seopress_dashboard_option_name[news_max_items]" type="number" step="1"
                                min="1" max="5" value="<?php echo $value; ?>">
                        </p>

                        <button id="seopress-news-items" type="submit" class="btn btnSecondary">
                            <?php _e('Save', 'wp-seopress'); ?>
                        </button>
                    </div>
                </div>
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

                $maxitems = 0;

                if ( ! is_wp_error($rss)) { // Checks that the object is created correctly
                    // Figure out how many total items there are, but limit it to 5.
                    $maxitems = $rss->get_item_quantity($value);

                    // Build an array of all the items, starting with element 0 (first element).
                    $rss_items = $rss->get_items(0, $maxitems);
                }
                ?>

                <ul class="seopress-list-items" role="menu">
                    <?php if (0 == $maxitems) { ?>
                    <li class="seopress-item has-action">
                        <?php _e('No items', 'wp-seopress'); ?>
                    </li>
                    <?php } else { ?>
                    <?php // Loop through each feed item and display each item as a hyperlink.?>
                    <?php foreach ($rss_items as $item) { ?>
                    <li class="seopress-item has-action seopress-item-inner">
                        <a href="<?php echo esc_url($item->get_permalink()); ?>"
                            target="_blank" class="seopress-item-inner"
                            title="<?php printf(__('Learn more about %s in a new tab', 'wp-seopress'), esc_html($item->get_title())); ?>">
                            <p class="seopress-item-date"><?php echo $item->get_date('M Y'); ?>
                            </p>

                            <h3 class="seopress-item-title">
                                <?php echo esc_html($item->get_title()); ?><span class="dashicons dashicons-external"></span>
                            </h3>
                            <p class="seopress-item-content"><?php echo $item->get_description(); ?>
                            </p>
                        </a>
                    </li>
                    <?php } ?>
                    <?php } ?>
                </ul>
            </div>
            <div class="seopress-card-footer">
                <a href="<?php echo $docs['blog']; ?>"><?php _e('All news', 'wp-seopress'); ?></a>
                <span class="dashicons dashicons-external"></span>
            </div>
        </div>
<?php }
