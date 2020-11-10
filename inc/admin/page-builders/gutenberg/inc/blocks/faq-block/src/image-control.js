const wp = window.wp
const { withSelect } = wp.data
const { Component } = wp.element
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor'
const { Spinner, Button, ResponsiveWrapper } = wp.components
const { compose } = wp.compose;
const { __ } = wp.i18n;

const ALLOWED_MEDIA_TYPES = ['image']

class ImageControl extends Component {
  constructor() {
    super( ...arguments );
  }

  onUpdateImage(image) {
    this.props.onSelect(this.props.index, image.id);
  };

  onRemoveImage() {
    this.props.onRemoveImage(this.props.index);
  };
  
  getImageSize(image) {
    let imgSize;
    
    switch(this.props.imageSize) {
      case 'thumbnail': imgSize = ( undefined != image ? image.media_details.sizes.thumbnail : null); break;
      case 'medium': imgSize = ( undefined != image ? image.media_details.sizes.medium : null); break;
      case 'large': imgSize = ( undefined != image ? ( undefined != image.media_details.sizes.large ? image.media_details.sizes.large : image.media_details.sizes.medium_large ) : null ); break;
      default: imgSize = ( undefined != image ? image.media_details.sizes.full : null );
    }
    
    return imgSize;
  }

  render() {
    const instructions = <p>{ __( 'To edit the background image, you need permission to upload media.', 'wp-seopress' ) }</p>;
    const { value, image } = this.props

    return (
      <div className="wp-block-wp-seopress-image">
        <MediaUploadCheck fallback={ instructions }>
            <MediaUpload
                title={ __( 'Set Image', 'wp-seopress' ) }
                onSelect={ this.onUpdateImage.bind(this) }
                allowedTypes={ ALLOWED_MEDIA_TYPES }
                value={ value }
                render={ ( { open } ) => (
                    <Button
                        className={ ! value ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview' }
                        onClick={ open }>
                        { ! value && ( __( 'Set Image', 'wp-seopress' ) ) }
                        { !! value && ! image && <Spinner /> }
                        { !! value && image && 
                                <img src={ this.getImageSize(image).source_url } alt={ __( 'Set Image', 'wp-seopress' ) } />
                        }
                    </Button>
                ) }
            />
        </MediaUploadCheck>
        { !! value &&
            <MediaUploadCheck>
                <Button onClick={ this.onRemoveImage.bind(this) } isLink isDestructive>
                    { __( 'Remove Image', 'wp-seopress' ) }
                </Button>
            </MediaUploadCheck>
        }
      </div>
    )
  }
}

export default compose(
    withSelect((select, ownProps) => {
      return {
          image: ownProps.value ? select('core').getMedia(ownProps.value) : null,
      };
    }),
)(ImageControl)