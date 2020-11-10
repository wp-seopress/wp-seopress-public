const { Component, Fragment } = wp.element;

import {
	PanelBody,
	Button,
	ButtonGroup,
	PanelRow,
	ToggleControl
} from '@wordpress/components'

const { __, _x } = wp.i18n;

import { RichText, InspectorControls } from '@wordpress/block-editor';
import ImageControl from './image-control';

const { compose } = wp.compose;
const { withSelect, select } = wp.data;

class WPSeopress_FAQ extends Component {
    constructor() {
		super( ...arguments );

		this.state = {
            faqs: this.props.attributes.faqs || [''],
            listStyle: this.props.attributes.listStyle || 'none',
            titleWrapper: this.props.attributes.titleWrapper || 'p',
			imageSize: this.props.attributes.imageSize || 'thumbnail',
			showFAQScheme: this.props.attributes.showFAQScheme || false,
            selectedImageId: this.props.attributes.selectedImageId || 0
		};
        
        this.props.attributes.faqs = this.state.faqs;
        this.props.attributes.listStyle = this.state.listStyle;
        this.props.attributes.titleWrapper = this.state.titleWrapper;
        this.props.attributes.imageSize = this.state.imageSize;
        this.props.attributes.selectedImageId = this.state.selectedImageId;
		this.props.attributes.showFAQScheme = this.state.showFAQScheme;
	};
	
	getImage(imageID) {
		return imageID ? select('core').getMedia(imageID) : 0;
	}

    showFAQs() {
        return ( 
			( this.props.attributes.listStyle === 'none' &&
				this.state.faqs.map((el, i) => 
					<div key={i} className="wpseopress-faqs-area">
						<div className="wpseopress-faq">
							<RichText
								tagName={this.props.attributes.titleWrapper}
								className='wpseopress-faq-question'
								placeholder={ __( 'Question...', 'wp-seopress' ) }
								value={undefined != this.props.attributes.faqs[i] ? this.props.attributes.faqs[i].question : ''}
								onChange={this.handleQuestionChange.bind(this, i)}
							/>
							<div className="wpseopress-answer-meta">
								<ImageControl 
									value={undefined != this.props.attributes.faqs[i] ? this.props.attributes.faqs[i].image : ''} 
									onSelect={this.handleImageChange.bind(this)} 
									onRemoveImage={this.onRemoveImage.bind(this)}
									imageSize={this.props.attributes.imageSize}
									index={i}/>
								<RichText
									tagName='div'
									className='wpseopress-faq-answer'
									placeholder={ __( 'Answer...', 'wp-seopress' ) }
									value={undefined != this.props.attributes.faqs[i] ? this.props.attributes.faqs[i].answer : ''}
									onChange={this.handleAnswerChange.bind(this, i)}
								/>
							</div>
						</div>
						<div className="wpseopress-faq-cta">
							<button class='button button-link-delete' value={__('Remove', 'wp-seopress')} onClick={this.removeFAQ.bind(this, i)}>
								{__('Remove', 'wp-seopress')}
							</button>
						</div>
					</div>
				)
			) 
			|| 
			( this.props.attributes.listStyle === 'ul' || this.props.attributes.listStyle === 'ol' )  &&
				this.state.faqs.map((el, i) => 
					<li key={i} className="wpseopress-faqs-area">
						<div className="wpseopress-faq">
							<RichText
								tagName={this.props.attributes.titleWrapper}
								className='wpseopress-faq-question'
								placeholder={ __( 'Question...', 'wp-seopress' ) }
								value={undefined != this.props.attributes.faqs[i] ? this.props.attributes.faqs[i].question : ''}
								onChange={this.handleQuestionChange.bind(this, i)}
							/>
							<div className="wpseopress-answer-meta">
								<ImageControl 
									value={undefined != this.props.attributes.faqs[i] ? this.props.attributes.faqs[i].image : ''} 
									onSelect={this.handleImageChange.bind(this)} 
									onRemoveImage={this.onRemoveImage.bind(this)}
									imageSize={this.props.attributes.imageSize}
									index={i}/>
								<RichText
									tagName='div'
									className='wpseopress-faq-answer'
									placeholder={ __( 'Answer...', 'wp-seopress' ) }
									value={undefined != this.props.attributes.faqs[i] ? this.props.attributes.faqs[i].answer : ''}
									onChange={this.handleAnswerChange.bind(this, i)}
								/>
							</div>
						</div>
						<div className="wpseopress-faq-cta">
							<button class='button button-link-delete' value={__('Remove', 'wp-seopress')} onClick={this.removeFAQ.bind(this, i)}>
								{__('Remove', 'wp-seopress')}
							</button>
						</div>
					</li>
				)
		); // End return
    }

    addFAQ() {
		this.setState(prevState => ({ faqs: [...prevState.faqs, '']}));
    }
    
    removeFAQ(i){
		let faqs = [...this.state.faqs];
		faqs.splice(i,1);
		this.props.setAttributes( { faqs: faqs } );
		this.setState({ faqs });
	}

    handleQuestionChange(i, event) {
        if ( undefined == event ) {
			return;
        }
        
		let faqs = [...this.state.faqs];
        
        if ( faqs.length == 0 ) {
			return;
        }
        
		faqs[i] = { question: event || '', answer: faqs[i].asnwer || '', image: faqs[i].image || '' };
		this.props.setAttributes( { faqs: faqs } );
		this.setState({ faqs });
    }

    handleAnswerChange(i, event) {
        if ( undefined == event ) {
			return;
        }
        
		let faqs = [...this.state.faqs];
        
        if ( faqs.length == 0 ) {
			return;
        }
        
		faqs[i] = { question: faqs[i].question || '', answer: event || '', image: faqs[i].image || '' };
		this.props.setAttributes( { faqs: faqs } );
		this.setState({ faqs });
    }

    handleImageChange(i, event) {
        if ( undefined == event ) {
			return;
        }
        
		let faqs = [...this.state.faqs];
		
        if ( faqs.length == 0 ) {
			return;
        }
		faqs[i] = { question: faqs[i].question || '', answer: faqs[i].answer || '', image: event || '' };
		this.props.setAttributes( { faqs: faqs, selectedImageId: event } );
		this.setState({ faqs });
    }

    onRemoveImage(i) {
        let faqs = [...this.state.faqs];
		faqs[i].image = '';
		this.props.setAttributes( { faqs: faqs } );
		this.setState({ faqs });
    }
    
    render() {
        const { listStyle, titleWrapper, imageSize, showFAQScheme } = this.props.attributes;

        const inspectorControls = (
            <InspectorControls>
                <PanelBody title={__( 'FAQ Settings', 'wp-seopress' )}>
                    <p>{__( 'List Style', 'wp-seopress' )}</p>
                    <PanelRow className="wpseopress-faqs-list-style">
                        <ButtonGroup>
                            <Button
								isSecondary
								isPrimary={'none' == listStyle ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												listStyle: 'none',
											}
                                        );
									}
								}
							>
								{_x( 'NONE', 'Div tag List', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'ol' == listStyle ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												listStyle: 'ol',
											}
                                        );
									}
								}
							>
								{_x( 'OL', 'Numbered List', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'ul' == listStyle ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												listStyle: 'ul',
											}
                                        );
									}
								}
							>
								{_x( 'UL', 'Unordered List', 'wp-seopress' )}
							</Button>
                        </ButtonGroup>
                    </PanelRow>
                    <p>{__( 'Title Wrapper', 'wp-seopress' )}</p>
                    <PanelRow className="wpseopress-faqs-title-wrapper">
                        <ButtonGroup>
                            <Button
								isSecondary
								isPrimary={'h2' == titleWrapper ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												titleWrapper: 'h2',
											}
                                        );
									}
								}
							>
								{_x( 'H2', 'H2 title tag', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'h3' == titleWrapper ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												titleWrapper: 'h3',
											}
                                        );
									}
								}
							>
								{_x( 'H3', 'H3 title tag', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'h4' == titleWrapper ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												titleWrapper: 'h4',
											}
                                        );
									}
								}
							>
								{_x( 'H4', 'H4 title tag', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'h5' == titleWrapper ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												titleWrapper: 'h5',
											}
                                        );
									}
								}
							>
								{_x( 'H5', 'H5 title tag', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'h6' == titleWrapper ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												titleWrapper: 'h6',
											}
                                        );
									}
								}
							>
								{_x( 'H6', 'H6 title tag', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'p' == titleWrapper ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												titleWrapper: 'p',
											}
                                        );
									}
								}
							>
								{_x( 'P', 'P title tag', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'div' == titleWrapper ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												titleWrapper: 'div',
											}
                                        );
									}
								}
							>
								{_x( 'DIV', 'DIV title tag', 'wp-seopress' )}
							</Button>
                        </ButtonGroup>
                    </PanelRow>
                    <p>{__( 'Image Size', 'wp-seopress' )}</p>
                    <PanelRow className="wpseopress-faqs-image-size">
                        <ButtonGroup>
                            <Button
								isSecondary
								isPrimary={'thumbnail' == imageSize ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												imageSize: 'thumbnail',
											}
                                        );
									}
								}
							>
								{_x( 'S', 'Thubmnail Size', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'medium' == imageSize ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												imageSize: 'medium',
											}
                                        );
									}
								}
							>
								{_x( 'M', 'Medium Size', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'large' == imageSize ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												imageSize: 'large',
											}
                                        );
									}
								}
							>
								{_x( 'L', 'Large Size', 'wp-seopress' )}
							</Button>
                            <Button
								isSecondary
								isPrimary={'full' == imageSize ? true : false}
								onClick={
									( e ) => {
										this.props.setAttributes(
											{
												imageSize: 'full',
											}
                                        );
									}
								}
							>
								{_x( 'XL', 'Original Size', 'wp-seopress' )}
							</Button>
                        </ButtonGroup>
                    </PanelRow>
					<p>{ __( 'SEO Settings', 'wp-seopress' ) }</p>
					<PanelRow>
						<ToggleControl
							label={__( 'Enable FAQ Scheme', 'wp-seopress' )}
							checked={ !! showFAQScheme }
							onChange={ 
								( e ) => {
										this.props.setAttributes(
										{
											showFAQScheme: ! showFAQScheme,
										}
									)
								}
							}
						/>
					</PanelRow>
                </PanelBody>
            </InspectorControls>
        )

        return (
            <Fragment>
                {inspectorControls}
                <div className="wpseopress-faqs">
					{listStyle === 'ul' && <ul>{this.showFAQs()}</ul>}
					{listStyle === 'ol' && <ol>{this.showFAQs()}</ol>}
					{listStyle === 'none' && this.showFAQs()}
                    <div className="wpseopress-faqs-actions">
                        <button
                            type="button"
                            title={__( 'Add FAQ', 'wp-seopress' )}
                            className="add-faq button button-link-add"
                            onClick={
                                ( e ) => {
                                    e.preventDefault();
                                    this.addFAQ();
                                }
                            }
                        >
                            {__('Add FAQ', 'wp-seopress')}
                        </button>
                    </div>
                </div>
            </Fragment>
        )
    }
}

export default compose(
    withSelect( ( select, props ) => {
        const { getMedia } = select( 'core' );
        const { selectedImageId } = props.attributes;

        return {
            selectedImage: selectedImageId ? getMedia( selectedImageId ) : 0,
        };
    } ),
)( WPSeopress_FAQ );