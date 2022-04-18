const wp = window.wp
const { withSelect } = wp.data
const { Component } = wp.element
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor'
const { Spinner, Button, ResponsiveWrapper } = wp.components
const { compose } = wp.compose
const { __ } = wp.i18n

const ALLOWED_MEDIA_TYPES = ['image']

function ImageControl(props) {
    const { value, image } = props

    const onUpdateImage = (image) => {
        props.onSelect(image.id, props.index)
    }

    const onRemoveImage = () => {
        props.onRemoveImage(props.index)
    }

    const getImageSize = (image) => {
        let imgSize = null

        try {
            if (undefined != image) {
                imgSize = {}
                imgSize.source_url = image.guid.raw

                if (undefined != image.media_details.sizes) {
                    imgSize = null
                    switch (props.imageSize) {
                        case 'thumbnail':
                            imgSize =
                                undefined != image
                                    ? image.media_details.sizes.thumbnail
                                    : null
                            break
                        case 'medium':
                            imgSize =
                                undefined != image
                                    ? image.media_details.sizes.medium
                                    : null
                            break
                        case 'large':
                            imgSize =
                                undefined != image
                                    ? undefined !=
                                      image.media_details.sizes.large
                                        ? image.media_details.sizes.large
                                        : image.media_details.sizes.medium_large
                                    : null
                            break
                        default:
                            imgSize =
                                undefined != image
                                    ? image.media_details.sizes.full
                                    : null
                    }
                }
            }
            return imgSize
        } catch (error) {
            return imgSize
        }
    }

    const instructions = (
        <p>
            {__(
                'To edit the background image, you need permission to upload media.',
                'wp-seopress'
            )}
        </p>
    )

    return (
        <div className="wp-block-wp-seopress-image">
            <MediaUploadCheck fallback={instructions}>
                <MediaUpload
                    title={__('Set Image', 'wp-seopress')}
                    onSelect={onUpdateImage}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    value={value}
                    render={({ open }) => {
                        const imageSize = getImageSize(image)
                        return (
                            <Button
                                className={
                                    !value
                                        ? 'editor-post-featured-image__toggle'
                                        : 'editor-post-featured-image__preview'
                                }
                                onClick={open}
                            >
                                {!value && __('Set Image', 'wp-seopress')}
                                {!!value && !image && <Spinner />}
                                {!!value &&
                                    image &&
                                    imageSize &&
                                    imageSize.source_url && (
                                        <img
                                            src={imageSize.source_url}
                                            alt={__('Set Image', 'wp-seopress')}
                                        />
                                    )}
                            </Button>
                        )
                    }}
                />
            </MediaUploadCheck>
            {!!value && (
                <MediaUploadCheck>
                    <Button onClick={onRemoveImage} isLink isDestructive>
                        {__('Remove Image', 'wp-seopress')}
                    </Button>
                </MediaUploadCheck>
            )}
        </div>
    )
}

export default compose(
    withSelect((select, ownProps) => {
        return {
            image: ownProps.value
                ? select('core').getMedia(ownProps.value)
                : null,
        }
    })
)(ImageControl)
