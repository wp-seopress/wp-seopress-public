import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';

registerBlockType('wpseopress/sitemap', {
    title: __('Sitemap', 'wp-seopress'),
    description: __('Display an HTML sitemap.', 'wp-seopress'),
    keywords: [__('sitemap', 'wp-seopress'), __('navigation', 'wp-seopress')],
    edit,
    save: () => null
});
