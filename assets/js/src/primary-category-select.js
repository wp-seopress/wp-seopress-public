import { __ } from '@wordpress/i18n'
import { Component } from '@wordpress/element'
import { withSelect } from '@wordpress/data';
import { PanelRow, SelectControl } from '@wordpress/components'

class TermSelect extends Component {
    constructor() {
        super(...arguments);
        this.onChange = this.onChange.bind(this);
        this.updateMetabox = this.updateMetabox.bind(this);
        this.onMetaboxChange = this.onMetaboxChange.bind(this);
        this.state = {
            primaryTermId: 'none',
            selectableTerms: [],
        }
    }

    componentDidMount() {
        const primaryTermId = this.props.primaryTermId || 'none';
        this.setState({ primaryTermId });
        this.metaboxField = document.querySelector('#seopress_robots_primary_cat');
        if (this.metaboxField) {
            this.metaboxField.addEventListener('change', this.onMetaboxChange);
        }
    }

    componentWillUnmount() {
        if (this.metaboxField) {
            this.metaboxField.removeEventListener('change', this.onMetaboxChange);
        }
    }

    componentDidUpdate(prevProps, prevState) {
        // If available terms or selected terms have changed, check state.
        if (prevProps.allTerms !== this.props.allTerms || prevProps.selectedTermIds !== this.props.selectedTermIds) {
            const selectableTerms = this.props.allTerms && this.props.allTerms.length ? this.props.allTerms.filter(term => this.props.selectedTermIds.includes(term.id)) : [];
            const primaryTermId = !this.props.selectedTermIds.length || !this.props.selectedTermIds.includes(parseInt(this.state.primaryTermId)) ? 'none' : this.state.primaryTermId;
            this.setState({ selectableTerms, primaryTermId });
        }
        if (prevState.primaryTermId !== this.state.primaryTermId || prevState.selectableTerms !== this.state.selectableTerms) {
            this.updateMetabox(this.state.primaryTermId);
        }
    }

    updateMetabox(selectedTermId) {
        if (this.metaboxField && this.state.selectableTerms && this.state.selectableTerms.length) {
            const options = this.getOptions().map(option => {
                const selected = option.value == selectedTermId ? 'selected="selected"' : '';
                return `<option value="${option.value}" ${selected}>${option.label}</option>`;
            });
            this.metaboxField.value = selectedTermId;
            this.metaboxField.innerHTML = options.join('');
        }
    }

    getOptions() {
        return [
            { value: 'none', label: __('None (will disable this feature)', 'wp-seopress') },
            ...this.state.selectableTerms.map((term) => ({ value: term.id, label: term.name, }))
        ];
    }

    onChange(termId) {
        this.setState({ primaryTermId: termId });
    }

    onMetaboxChange(e) {
        this.setState({ primaryTermId: e.target.value });
    }

    render() {
        return !!this.metaboxField && !!this.state.selectableTerms.length && (
            <SelectControl
                label={__('Select a primary category', 'wp-seopress')}
                value={this.state.primaryTermId}
                options={this.getOptions()}
                onChange={this.onChange}
            />
        );
    }
}


const PrimaryTermSelect = withSelect((select, { slug }) => {
    const taxonomy = select('core').getTaxonomy(slug);
    const selectedTermIds = taxonomy ? select('core/editor').getEditedPostAttribute(taxonomy.rest_base) : [];
    const allTerms = select('core').getEntityRecords('taxonomy', slug, { per_page: -1, context: 'view' }) || [];
    const primaryTermId = select('core/editor').getEditedPostAttribute('meta')['_seopress_robots_primary_cat'] || 'none';
    return { taxonomy, allTerms, primaryTermId, selectedTermIds }
})(TermSelect);


wp.hooks.addFilter(
    'editor.PostTaxonomyType',
    'wpseopress',
    (PostTaxonomies) => (props) => {
        return (
            <>
                <PostTaxonomies {...props} />
                {props.slug && 'category' === props.slug &&
                    <PanelRow className="seopress-primary-term-picker">
                        <PrimaryTermSelect {...props} />
                    </PanelRow>
                }
            </>
        );
    }
)