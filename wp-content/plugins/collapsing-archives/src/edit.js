/**
 * WordPress components that create the necessary UI elements for the block
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-components/
 */
//import { map, filter } from 'lodash';
import { TextControl } from '@wordpress/components';
import { withSelect, subscribe } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

import ServerSideRender from "@wordpress/server-side-render";
const { InspectorControls } = wp.blockEditor;
const { ToggleControl, PanelBody, PanelRow, CheckboxControl, SelectControl, ColorPicker, Placeholder, Spinner } = wp.components;

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {WPElement} Element to render.
 */
function Edit( props ) {
	if ( props.attributes.__internalWidgetId ) {
		props.setAttributes( {blockId: props.attributes.__internalWidgetId });
	} else if ( props.clientId ) {
		props.setAttributes( {blockId: props.clientId});
	} else {
		props.setAttributes( {blockId: 'block-1'});
	}

	function excludedPostTypes( postType ) {
		let excludePostTypes = [ 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation', 'nav_menu_item' ];
		if ( ! excludePostTypes.includes( postType.slug ) ) {
			return postType;
		}
	}

	function excludedTaxonomies( taxonomy ) {
		let excludeTaxonomies = [ 'nav_menu' ];
		if ( ! excludeTaxonomies.includes( taxonomy.slug ) ) {
			return taxonomy;
		}
	}

	const getPostTypeOptions = () => {
		const selectOption = {
			label: __( '- Select -' ),
			value: '',
			disabled: true,
		};
		let postTypeOptions = [];
		if ( props.postTypes ) {
			postTypeOptions = props.postTypes.filter(excludedPostTypes).map(
				item => {
					return {
						value: item.slug,
						label: item.name,
					};
				}
			);
		}
		 return [ selectOption, ...postTypeOptions ];
	 }
	 const getTaxonomyOptions = () => {
		const selectOption = {
			label: __( '- Select -' ),
			value: '',
			disabled: true,
		};
		let taxonomyOptions = [];
		if ( props.taxonomies ) {
			taxonomyOptions = props.taxonomies.filter( excludedTaxonomies ).map(
				item => {
					return {
						value: item.slug,
						label: item.name,
					};
				}
			);
		}

    return [ selectOption, ...taxonomyOptions ];
  };
	const blockProps = useBlockProps();
	return (
		<div {...blockProps}>
				<ServerSideRender block="collapsing/archives" attributes={props.attributes} />
				<InspectorControls>
					<PanelBody
						title={__("Collapsing Archives settings", "collapsing-archives")}
						initialOpen={true}
					>
						<PanelRow>
							<TextControl
								label={__("Title", "collapsing-archives")}
								 autoComplete="off"
									value={ props.attributes.widgetTitle || '' }
									onChange={ ( nextValue ) => {
										props.setAttributes( { widgetTitle: nextValue } );
									} }
							/>
						</PanelRow>
						<PanelRow>
							<ToggleControl
								label={__("Show year count")}
								checked={props.attributes.showYearCount}
								onChange={(value) => { props.setAttributes( { showYearCount: value } );} }
							/>
						</PanelRow>
						<PanelRow>
							<ToggleControl
								label={__("Show month count")}
								checked={props.attributes.showMonthCount}
								onChange={(value) => { props.setAttributes( { showMonthCount: value } );} }
							/>
						</PanelRow>
						<PanelRow>
							<ToggleControl
								label={__("Expand Years")}
								checked={props.attributes.expandYears}
								onChange={(value) => { props.setAttributes( { expandYears: value } );} }
							/>
						</PanelRow>
						<PanelRow>
							<ToggleControl
								label={__("Expand Current Year")}
								checked={props.attributes.expandCurrentYear}
								onChange={(value) => { props.setAttributes( { expandCurrentYear: value } );} }
							/>
						</PanelRow>
						<PanelRow>
							<ToggleControl
								label={__("Expand Months to show posts")}
								checked={props.attributes.expandMonths}
								onChange={(value) => { props.setAttributes( { expandMonths: value } );} }
							/>
						</PanelRow>
						<PanelRow>
							<ToggleControl
								label={__("Expand Current Month")}
								checked={props.attributes.expandCurrentMonth}
								onChange={(value) => { props.setAttributes( { expandCurrentMonth: value } );} }
							/>
						</PanelRow>
						<PanelRow>
							<SelectControl
								value={props.attributes.sort}
								label={__("Sort order")}
								options={[
									{label: __("Ascending", 'collapsing-archives'), value: 'ASC'},
									{label: __("Descending", 'collapsing-archives'), value: 'DESC'},
								]}
								onChange={(newval) => { props.setAttributes( {sort: newval })}}
							/>
						</PanelRow>
						<PanelRow>
							<SelectControl
								label={__("Expanding and collapsing characters")}
								value={props.attributes.expand}
								options={[
									{label: __("▶ ▼"), value: '0'},
									{label: __("+ —"), value: '1'},
									{label: __("[+] [—]"), value: '2'},
									{label: __("Images (1)"), value: '3'},
									{label: __("Images (2)"), value: '5'},
								]}
								onChange={(newval) => { props.setAttributes( {expand: newval })}}
							/>
						</PanelRow>
						<PanelRow>
							<SelectControl
								label={__("Taxonomy type", 'collapsing-archives')}
								value={props.attributes.taxonomy}
								options={getTaxonomyOptions()}
								onChange={(newval) => { props.setAttributes( {taxonomy: newval })}}
							/>
						</PanelRow>
						<PanelRow>
							<SelectControl
								label={__("Post type", 'collapsing-archives')}
								value={props.attributes.post_type}
								options={getPostTypeOptions()}
								onChange={(newval) => { props.setAttributes( {post_type: newval })}}
							/>
						</PanelRow>
						<PanelRow>
							<SelectControl
								label={__("Clicking on year or month:")}
								value={props.attributes.linkToArch}
								options={[
									{label: __("Links to archive"), value: 1},
									{label: __("Expands to show month and/or posts"), value: 0},
								]}
								onChange={(newval) => { props.setAttributes( {linkToArch: newval })}}
							/>
						</PanelRow>
						<PanelRow>
							<SelectControl
								label={__("Style")}
								value={props.attributes.style}
								options={[
									{label: __("Theme"), value: 'theme'},
									{label: __("Kubrick"), value: 'kubrick'},
									{label: __("Twenty Ten"), value: 'twentyten'},
									{label: __("No arrows"), value: 'noArrows'},
								]}
								onChange={(newval) => { props.setAttributes( {style: newval })}}
							/>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
				<InspectorControls group="advanced">
						<PanelRow>
							<TextControl
								label={__("Exclude posts older than N days", "collapsing-archives")}
								 autoComplete="off"
									value={ props.attributes.olderThan || '' }
									onChange={ ( nextValue ) => {
										props.setAttributes( { olderThan: nextValue } );
									} }
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={__("Truncate post title to N characters", "collapsing-archives")}
								 autoComplete="off"
									value={ props.attributes.postTitleLength || '' }
									onChange={ ( nextValue ) => {
										props.setAttributes( { postTitleLength: nextValue } );
									} }
							/>
						</PanelRow>
						<PanelRow>
							<SelectControl
								label={__("")}
								value={props.attributes.inExcludeCat}
								options={[
									{label: __("Include"), value: 'include'},
									{label: __("Exclude"), value: 'exclude'},
								]}
								onChange={(newval) => { props.setAttributes( {inExcludeCat: newval })}}
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={__("these categories/terms (input slugs separated by commas)", "collapsing-archives")}
								 autoComplete="off"
									value={ props.attributes.inExcludeCats || '' }
									onChange={ ( nextValue ) => {
										setAttributes( { inExcludeCats: nextValue } );
									} }
							/>
						</PanelRow>
						<PanelRow>
							<SelectControl
								label={__("")}
								value={props.attributes.inExcludeYear}
								options={[
									{label: __("Include"), value: 'include'},
									{label: __("Exclude"), value: 'exclude'},
								]}
								onChange={(newval) => { props.setAttributes( {inExcludeYear: newval })}}
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={__("these years (separated by commas)", "collapsing-archives")}
								 autoComplete="off"
									value={ props.attributes.inExcludeYears || '' }
									onChange={ ( nextValue ) => {
										setAttributes( { inExcludeYears: nextValue } );
									} }
							/>
						</PanelRow>
						<PanelRow>
							<TextControl
								label={__("Auto-expand these archives (input slugs, separated by commas):", "collapsing-archives")}
								 autoComplete="off"
									value={ props.attributes.defaultExpand || '' }
									onChange={ ( nextValue ) => {
										setAttributes( { defaultExpand: nextValue } );
									} }
							/>
						</PanelRow>
						<PanelRow>
							<CheckboxControl
								label={__("Show Post Date")}
								checked={props.attributes.showPostDate}
								onChange={(newval) => { props.setAttributes( {showPostDate: newval })}}
							/>
						</PanelRow>
							<SelectControl
								value={props.attributes.postDateAppend}
								options={[
									{label: __( "Before title" ), value: 'before'},
									{label: __( "After title" ), value: 'after'},
								]}
								onChange={(newval) => { props.setAttributes( {postDateAppend: newval })}}
							/>
						<PanelRow>
							<TextControl
								label={__("Post Date Format", "collapsing-archives")}
								 autoComplete="off"
									value={ props.attributes.postDateFormat || '' }
									onChange={ ( nextValue ) => {
										setAttributes( { postDateFormat: nextValue } );
									} }
									help={ __( "<a href='http://php.net/date' target='_blank'> information about date formatting syntax</a>" ) }
							/>
						</PanelRow>
						<PanelRow>
							<CheckboxControl
								label={__("Show debugging information")}
								checked={props.attributes.debug}
								onChange={(newval) => { props.setAttributes( {debug: newval })}}
							/>
						</PanelRow>
				</InspectorControls>
			</div>
	);
}

export default withSelect( ( select ) => {
	return {
		taxonomies: select( coreStore ).getTaxonomies( { per_page: -1 } ),
		postTypes: select( coreStore ).getPostTypes( { per_page: -1 } ),
	};
} )( Edit );
