import './editor.scss'
import './style.scss'
import edit from './edit'
import { registerBlockType } from '@wordpress/blocks'
import { __ } from '@wordpress/i18n'

registerBlockType('wpseopress/faq-block', {
    title: __('FAQ', 'wp-seopress'),
    icon: 'index-card',
    category: 'wpseopress',
    example: {},
    edit: edit,
    save() {
        return null
    },
})
