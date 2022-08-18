import { __ } from '@wordpress/i18n';
import { Notice } from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';
import { withSelect } from '@wordpress/data';
import Inspector from './inspector.js';
import ServerSideRender from '@wordpress/server-side-render';
import './editor.scss';

export default function edit({ attributes, setAttributes }) {
    const { isSiteMapEnabled, optionsPageUrl } = attributes;
    const notice = __('It looks like the Sitemap feature is not enabled from your SEO settings. You must activate it to use this block. Make sure the HTML sitemap option is also enabled.', 'wp-seopress');
    const noticeActions = [{ label: __('Review settings', 'wp-seopress'), url: optionsPageUrl, variant: 'primary', isPrimary: true, noDefaultClasses: true }];
    const excludes = ['attachment', 'wp_navigation', 'nav_menu_item', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation'];

    const Settings = withSelect((select, props) => {
        let allPostTypes = select('core').getPostTypes() || [];
        let allowedPostTypes = [];
        if (allPostTypes && allPostTypes.length) allowedPostTypes = allPostTypes.filter(postType => !excludes.includes(postType.slug));
        return { allowedPostTypes, ...props };
    })(Inspector);

    return (
        <div {...useBlockProps()}>
            <Settings attributes={attributes} setAttributes={setAttributes} />
            {isSiteMapEnabled
                ? <ServerSideRender
                    block="wpseopress/sitemap"
                    attributes={attributes}
                />
                : <Notice status="warning" isDismissible={false} actions={noticeActions}><p>{notice}</p></Notice>
            }
        </div>
    );
}
