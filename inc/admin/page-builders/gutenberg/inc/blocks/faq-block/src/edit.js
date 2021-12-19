const { Component, Fragment } = wp.element

import {
    PanelBody,
    Button,
    ButtonGroup,
    PanelRow,
    ToggleControl,
} from '@wordpress/components'

const { __, _x } = wp.i18n

import { RichText, InspectorControls } from '@wordpress/block-editor'
import ImageControl from './image-control'

const { compose } = wp.compose
const { withSelect } = wp.data

function WPSeopress_FAQ(props) {
    const { attributes } = props
    const { listStyle, titleWrapper, imageSize, showFAQScheme, showAccordion } = attributes

    const showFAQs = () => {
        return (
            (attributes.listStyle === 'none' &&
                attributes.faqs.map((faq, i) => (
                    <div key={i} className="wpseopress-faqs-area">
                        <div className="wpseopress-faq">
                            <RichText
                                tagName={attributes.titleWrapper}
                                className="wpseopress-faq-question"
                                placeholder={__('Question...', 'wp-seopress')}
                                value={!!faq ? faq.question : ''}
                                onChange={(value) =>
                                    handleQuestionChange(value, i)
                                }
                            />
                            <div className="wpseopress-answer-meta">
                                <ImageControl
                                    value={!!faq ? faq.image : ''}
                                    onSelect={handleImageChange}
                                    onRemoveImage={onRemoveImage}
                                    imageSize={attributes.imageSize}
                                    index={i}
                                />
                                <RichText
                                    tagName="p"
                                    className="wpseopress-faq-answer"
                                    placeholder={__('Answer...', 'wp-seopress')}
                                    value={!!faq ? faq.answer : ''}
                                    onChange={(value) =>
                                        handleAnswerChange(value, i)
                                    }
                                />
                            </div>
                        </div>
                        <div className="wpseopress-faq-cta">
                            <button
                                className="components-button is-tertiary is-destructive"
                                value={__('Remove', 'wp-seopress')}
                                onClick={() => removeFAQ(i)}
                            >
                                {__('Remove', 'wp-seopress')}
                            </button>
                        </div>
                    </div>
                ))) ||
            ((attributes.listStyle === 'ul' || attributes.listStyle === 'ol') &&
                attributes.faqs.map((faq, i) => (
                    <li key={i} className="wpseopress-faqs-area">
                        <div className="wpseopress-faq">
                            <RichText
                                tagName={attributes.titleWrapper}
                                className="wpseopress-faq-question"
                                placeholder={__('Question...', 'wp-seopress')}
                                value={!!faq ? faq.question : ''}
                                onChange={(value) =>
                                    handleQuestionChange(value, i)
                                }
                            />
                            <div className="wpseopress-answer-meta">
                                <ImageControl
                                    value={!!faq ? faq.image : ''}
                                    onSelect={handleImageChange}
                                    onRemoveImage={onRemoveImage}
                                    imageSize={attributes.imageSize}
                                    index={i}
                                />
                                <RichText
                                    tagName="div"
                                    className="wpseopress-faq-answer"
                                    placeholder={__('Answer...', 'wp-seopress')}
                                    value={!!faq ? faq.answer : ''}
                                    onChange={(value) =>
                                        handleAnswerChange(value, i)
                                    }
                                />
                            </div>
                        </div>
                        <div className="wpseopress-faq-cta">
                            <button
                                className="components-button is-tertiary is-destructive"
                                value={__('Remove', 'wp-seopress')}
                                onClick={() => removeFAQ(i)}
                            >
                                {__('Remove', 'wp-seopress')}
                            </button>
                        </div>
                    </li>
                )))
        ) // End return
    }

    const addFAQ = () => {
        props.setAttributes({
            faqs: [...attributes.faqs, { question: '', answer: '', image: '' }],
        })
    }

    const removeFAQ = (i) => {
        const faqs = attributes.faqs.filter((item, key) => key !== i)
        props.setAttributes({ faqs: faqs })
    }

    const handleQuestionChange = (value, i) => {
        const faqs = attributes.faqs.map((faq, key) => {
            if (key !== i) {
                return faq
            }

            return {
                ...faq,
                question: value,
            }
        })
        props.setAttributes({ faqs: faqs })
    }

    const handleAnswerChange = (value, i) => {
        const faqs = attributes.faqs.map((faq, key) => {
            if (key !== i) {
                return faq
            }

            return {
                ...faq,
                answer: value,
            }
        })

        props.setAttributes({ faqs: faqs })
    }

    const handleImageChange = (value, i) => {
        const faqs = attributes.faqs.map((faq, key) => {
            if (key !== i) {
                return faq
            }

            return {
                ...faq,
                image: value,
            }
        })

        props.setAttributes({ faqs: faqs })
    }

    const onRemoveImage = (i) => {
        const faqs = attributes.faqs.map((faq, key) => {
            if (key !== i) {
                return faq
            }

            return {
                ...faq,
                image: null,
            }
        })
        props.setAttributes({ faqs: faqs })
    }

    const inspectorControls = (
        <InspectorControls>
            <PanelBody title={__('FAQ Settings', 'wp-seopress')}>
                <p>{__('List Style', 'wp-seopress')}</p>
                <PanelRow className="wpseopress-faqs-list-style">
                    <ButtonGroup>
                        <Button
                            isSecondary
                            isPrimary={'none' == listStyle ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    listStyle: 'none',
                                })
                            }}
                        >
                            {_x('NONE', 'Div tag List', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'ol' == listStyle ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    listStyle: 'ol',
                                })
                            }}
                        >
                            {_x('OL', 'Numbered List', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'ul' == listStyle ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    listStyle: 'ul',
                                })
                            }}
                        >
                            {_x('UL', 'Unordered List', 'wp-seopress')}
                        </Button>
                    </ButtonGroup>
                </PanelRow>
                <p>{__('Title Wrapper', 'wp-seopress')}</p>
                <PanelRow className="wpseopress-faqs-title-wrapper">
                    <ButtonGroup>
                        <Button
                            isSecondary
                            isPrimary={'h2' == titleWrapper ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    titleWrapper: 'h2',
                                })
                            }}
                        >
                            {_x('H2', 'H2 title tag', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'h3' == titleWrapper ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    titleWrapper: 'h3',
                                })
                            }}
                        >
                            {_x('H3', 'H3 title tag', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'h4' == titleWrapper ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    titleWrapper: 'h4',
                                })
                            }}
                        >
                            {_x('H4', 'H4 title tag', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'h5' == titleWrapper ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    titleWrapper: 'h5',
                                })
                            }}
                        >
                            {_x('H5', 'H5 title tag', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'h6' == titleWrapper ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    titleWrapper: 'h6',
                                })
                            }}
                        >
                            {_x('H6', 'H6 title tag', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'p' == titleWrapper ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    titleWrapper: 'p',
                                })
                            }}
                        >
                            {_x('P', 'P title tag', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'div' == titleWrapper ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    titleWrapper: 'div',
                                })
                            }}
                        >
                            {_x('DIV', 'DIV title tag', 'wp-seopress')}
                        </Button>
                    </ButtonGroup>
                </PanelRow>
                <p>{__('Image Size', 'wp-seopress')}</p>
                <PanelRow className="wpseopress-faqs-image-size">
                    <ButtonGroup>
                        <Button
                            isSecondary
                            isPrimary={'thumbnail' == imageSize ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    imageSize: 'thumbnail',
                                })
                            }}
                        >
                            {_x('S', 'Thubmnail Size', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'medium' == imageSize ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    imageSize: 'medium',
                                })
                            }}
                        >
                            {_x('M', 'Medium Size', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'large' == imageSize ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    imageSize: 'large',
                                })
                            }}
                        >
                            {_x('L', 'Large Size', 'wp-seopress')}
                        </Button>
                        <Button
                            isSecondary
                            isPrimary={'full' == imageSize ? true : false}
                            onClick={(e) => {
                                props.setAttributes({
                                    imageSize: 'full',
                                })
                            }}
                        >
                            {_x('XL', 'Original Size', 'wp-seopress')}
                        </Button>
                    </ButtonGroup>
                </PanelRow>
                <p>{__('SEO Settings', 'wp-seopress')}</p>
                <PanelRow>
                    <ToggleControl
                        label={__('Enable FAQ Schema', 'wp-seopress')}
                        checked={!!showFAQScheme}
                        onChange={(e) => {
                            props.setAttributes({
                                showFAQScheme: !showFAQScheme,
                            })
                        }}
                    />
                </PanelRow>
                <p>{__('Display', 'wp-seopress')}</p>
                <PanelRow>
                    <ToggleControl
                        label={__('Enable accordion', 'wp-seopress')}
                        checked={!!showAccordion}
                        onChange={(e) => {
                            props.setAttributes({
                                showAccordion: !showAccordion,
                            })
                        }}
                    />
                </PanelRow>
            </PanelBody>
        </InspectorControls>
    )

    return (
        <Fragment>
            {inspectorControls}
            <div className="wpseopress-faqs">
                {listStyle === 'ul' && <ul>{showFAQs()}</ul>}
                {listStyle === 'ol' && <ol>{showFAQs()}</ol>}
                {listStyle === 'none' && showFAQs()}
                <div className="wpseopress-faqs-actions">
                    <button
                        type="button"
                        title={__('Add FAQ', 'wp-seopress')}
                        className="add-faq components-button is-secondary"
                        onClick={(e) => {
                            e.preventDefault()
                            addFAQ()
                        }}
                    >
                        {__('Add FAQ', 'wp-seopress')}
                    </button>
                </div>
            </div>
        </Fragment>
    )
}

export default compose(
    withSelect((select, { attributes }) => {
        const { getMedia } = select('core')
        const { selectedImageId } = attributes

        return {
            selectedImage: selectedImageId ? getMedia(selectedImageId) : 0,
        }
    })
)(WPSeopress_FAQ)
