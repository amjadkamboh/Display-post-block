/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import { decodeEntities } from '@wordpress/html-entities';
import { TextControl, PanelBody, SelectControl, NumberControl,CheckboxControl } from '@wordpress/components';
const { InspectorControls } = wp.blockEditor;
import apiFetch from '@wordpress/api-fetch';
import { useBlockProps } from '@wordpress/block-editor';
import './style.scss';
import './editor.scss';



const postSelections = [];

const allPosts = wp.apiFetch({path: "/wp/v2/categories?taxonomy=category&per_page=100"}).then(taxonomy => {
	postSelections.push({label: "Select a Category", value: 0});
	jQuery.each( taxonomy, function( key, val ) {
		postSelections.push({label: val.name, value: val.id});
	});
	return postSelections; 
});
const afterPostSelections = [];
const afterAllPosts = wp.apiFetch({path: "/wp/v2/posts?per_page=499"}).then(posts => {
	jQuery.each( posts, function( key, val ) {
		afterPostSelections.push({label: val.title.rendered, value: val.id});
	});
	return afterPostSelections; 
});

// Get Users List
const usersSelection = [];
const afterUsersSelect = wp.apiFetch({path: "/wp/v2/users?per_page=100"}).then(posts => {
	usersSelection.push({label: "Select Users", value: ""});
	jQuery.each( posts, function( key, val ) {
		usersSelection.push({label: val.name, value: val.id});
	});
	return usersSelection; 
});


registerBlockType( 'wpm-block/category-block-list', {
	title: __( 'Blog Posts List Block' ), // Block title.
	icon: 'list-view', // Block icon
	category: 'common', // Block category
	keywords: [
		__( 'Posts, Blog' ),
	],
	/**
	 * Attributes 
	 */
	attributes: {
		categoryList: {
			type: 'array',
			default: [],
		},
		postList: {
			type: 'array',
			default: [],
		},
		title: {
			type: 'string',
			default: 'Read more about...',
		},
		numberPost: {
			type: 'string',
			default: '',
		},
		authorList: {
			type: 'array',
			default: [],
		}
	},

	edit: (props) => { 
		const { 
			attributes,
			setAttributes,
	
		} = props;
		const { categoryList, title, numberPost, postList, authorList} = attributes;
		//let x = attributes.categoryList;
		var query = {
            per_page: -1,
            status: 'publish',
			categories: attributes.categoryList
        };
		const pages = useSelect(
			select =>
				select( coreDataStore ).getEntityRecords( 'postType', 'post', query),
			[]
		);
		
		const optionSelections = [];
		optionSelections.push({label: "Exclude all posts.", value: 0});
		const  optionSelectionsss =	pages?.map( page => 
			optionSelections.push(({label: page.title.rendered, value: page.id}))
		);
		
		return (
			<div { ...useBlockProps()}  >
			<InspectorControls >
					<PanelBody
						title="Block Settings"
						initialOpen={true}
					>
						<TextControl
							label="Add Title Here"
							value={ title }
							onChange={(newval) => setAttributes({ title: newval })}
						/>
						<TextControl
							type= "number"
							label="Number of Post"
							value={ numberPost }
							onChange={(newval) => setAttributes({ numberPost: newval })}
						/>
						<SelectControl
								multiple
								className = 'multiplewpm-categories'
								label = 'Category List'
								value={ categoryList }
								options={ postSelections }
								help = "Press CTRL + mouse left click for multiple select."
								onChange={(newval) => setAttributes({ categoryList: newval })}
						/>
						<SelectControl
								 multiple
								className = 'multiplewpm-categories'
								label = 'Filter By Author'
								value={ authorList }
								options={ usersSelection }
								help = "Press CTRL + mouse left click for multiple select."
								onChange={(newval) => setAttributes({ authorList: newval })}
						/>
					</PanelBody>

				</InspectorControls>
				<div class="title-categories-wpm">
					<span class="labing">Blog Posts List Block</span>
					<h2> {  attributes.title } </h2>
					<SelectControl
						multiple
						className = 'multiplewpm'
						value={ postList }
						options= {optionSelections} 
						help = "Press CTRL + mouse left click for multiple select."
						onChange={(newval) => setAttributes({ postList: newval })}
					/>
				</div>
			</div>
		);
	},
	save: function(  ){
		return null;
	  },
} );
