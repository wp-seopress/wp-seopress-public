import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, CheckboxControl } from "@wordpress/components";

const Inspector = ({ attributes, setAttributes, allowedPostTypes }) => {
    const onChange = slug => {
        let postTypes = [...attributes.postTypes];
        if (postTypes.includes(slug)) {
            postTypes = postTypes.filter(postType => postType !== slug);
        } else {
            postTypes.push(slug);
        }
        setAttributes({ postTypes });
    }
    return (
        <InspectorControls>
            <PanelBody title={__('Post types to display', 'wp-seopress')}>
                {allowedPostTypes &&
                    <>
                        <p>{__('By default, if you have not selected any post types below, we’ll automatically take the ones set from the Sitemap setting page.', 'wp-seopress')}</p>
                        <ul>
                            {allowedPostTypes.map(postType => (
                                <li key={postType.slug}>
                                    <CheckboxControl
                                        label={postType.name}
                                        checked={attributes.postTypes.includes(postType.slug)}
                                        onChange={e => onChange(postType.slug)}
                                    />
                                </li>
                            ))}
                        </ul>
                    </>
                }
            </PanelBody>
        </InspectorControls>
    );
}

export default Inspector;