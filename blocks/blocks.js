/* global wp */
// See /cms/class-block-editor.php for where the compiled version of this script is loaded;
// and the dependencies that may need to be added to ensure future modifications work
import lodash from 'https://cdn.jsdelivr.net/npm/lodash@4.17.21/+esm';

const { omit } = lodash;

wp.domReady(() => {
	allowSomeBlocksOnlyOncePerPage();
	addCoreBlockParents();
	addCoreBlockCustomStyles();
	unregisterSomeCoreStylesAndVariations();
	customiseBlockCategories();
	customiseCoreBlockAttributes();
	updateCoreBlockDescriptions();
});

// This supplements  the main shared block registration done in PHP
// TODO: Find a way to pass shared blocks from PHP to JS here
// @see register_shared_blocks() in inc/cms/class-block-editor
// wp.blocks.registerBlockType('doublee-shared/global-call-to-action', {
// 	icon: 'controls-repeat',
// });

function allowSomeBlocksOnlyOncePerPage() {
	wp.hooks.addFilter('blocks.registerBlockType', 'doublee/allow-some-blocks-only-once-per-page', function(settings, name) {
		if (['doublee/page-header'].includes(name)) {
			settings.supports.multiple = false;
		}

		return settings;
	});
}

/**
 * Limit availability of some core to specific parent blocks by adding the parent setting
 * (For custom blocks, the parent can be set in block.json)
 * NOTE: This can be overridden in ACF-powered custom blocks by using a block as an allowed and/or default block in that specific context
 */
function addCoreBlockParents() {
	wp.hooks.addFilter('blocks.registerBlockType', 'doublee/add-core-block-parents', function(settings, name) {
		if (name.includes('doublee/')) {
			return settings;
		}

		if (name.includes('doublee-shared/')) {
			return settings;
		}

		switch (name) {
			case 'core/latest-posts':
				return { ...settings, parent: ['core/group'] };
			case 'custom/tiles':
				return { ...settings, parent: ['core/column', 'core/media-text'] };
			case 'core/columns':
				return { ...settings, parent: ['doublee/columns-wrapper'] };
			case 'core/column':
				return { ...settings, parent: ['core/columns'] };
			case 'core/button':
				return { ...settings, parent: ['core/buttons'] };
			case 'core/image':
			case 'core/embed':
				return { ...settings, parent: ['core/column'] };
			case 'core/media-text':
				return { ...settings, parent: ['doublee/media-text'] };
			case 'core/paragraph':
			case 'core/buttons':
				return { ...settings, parent: ['core/media-text', 'core/column', 'core/cover'] };
			case 'core/list':
			case 'core/table':
				return { ...settings, parent: ['core/media-text', 'core/column'] };
			case 'core/heading': {
				return {
					...settings,
					parent: ['core/media-text', 'core/column', 'core/table', 'core/tiles', 'core/cover'],
				};
			}
		}

		return settings;
	});
}

/**
 * Add some additional style options to some core blocks
 */
function addCoreBlockCustomStyles() {
	wp.blocks.registerBlockStyle('core/paragraph', {
		name: 'lead',
		label: 'Lead',
	});

	wp.blocks.registerBlockStyle('core/latest-posts', {
		name: 'narrow',
		label: 'Narrow',
	});
	wp.blocks.registerBlockStyle('core/latest-posts', {
		name: 'wide',
		label: 'Wide',
	});
}

/**
 * Customise the categories that some core blocks appear in
 */
function customiseBlockCategories() {
	wp.hooks.addFilter('blocks.registerBlockType', 'doublee/customise-block-categories', function(settings, name) {
		const layoutBlocks = ['core/columns', 'core/group', 'core/latest-posts', 'core/separator', 'core/spacer', 'custom/tiles'];
		if (layoutBlocks.includes(name)) {
			return { ...settings, category: 'page-layout' };
		}

		const mediaBlocks = ['core/embed', 'ninja-forms/form'];
		if (mediaBlocks.includes(name)) {
			return { ...settings, category: 'media' };
		}

		const textBlocks = ['core/heading', 'core/paragraph', 'core/list', 'core/quote', 'core/table', 'core/buttons'];
		if (textBlocks.includes(name)) {
			return { ...settings, category: 'formatting' };
		}

		return settings;
	});
}

/**
 * Unregister unwanted core block styles and variations
 * Note: This is only accounts for blocks that are explicitly allowed by the allowed_block_types_all filter in inc/cms/class-block-editor.php
 */
function unregisterSomeCoreStylesAndVariations() {
	setTimeout(() => {
		wp.blocks.unregisterBlockStyle('core/image', 'rounded');
		wp.blocks.unregisterBlockVariation('core/group', 'group-row');
		wp.blocks.unregisterBlockVariation('core/group', 'group-stack');

		(['wordpress', 'soundcloud', 'spotify', 'slideshare', 'twitter', 'flickr', 'animoto', 'cloudup', 'crowdsignal', 'dailymotion', 'imgur', 'issuu', 'kickstarter', 'mixcloud', 'pocket-casts', 'reddit', 'reverbnation', 'screencast', 'scribd', 'smugmug', 'speaker-deck', 'ted', 'tumblr', 'videopress', 'amazon-kindle', 'wolfram-cloud', 'pinterest', 'wordpress-tv']).forEach((embed) => {
			wp.blocks.unregisterBlockVariation('core/embed', embed);
		});
	}, 200);
}

/**
 * Remove unwanted/unused attributes from core blocks
 * and tweak the supports settings for some blocks
 * Note: This doesn't always remove them from the editor sidebar :(
 *       For some things this can be done in theme.json
 *       For others, I may have handled it in my forked Gutenberg plugin
 */
function customiseCoreBlockAttributes() {
	wp.hooks.addFilter('blocks.registerBlockType', 'doublee/remove-unwanted-block-attributes', function(settings, name) {
		if (settings.attributes) {
			settings.attributes = omit(settings.attributes, ['isStackedOnMobile', 'textColor']);
			settings.supports = omit(settings.supports, ['typography', 'spacing', 'html']);
		}

		if (name === 'core/group') {
			settings.supports = omit(settings.supports, ['__experimentalSettings', 'align', 'background', 'color', 'dimensions', 'layout', 'typography', 'anchor', 'spacing', 'position', 'ariaLabel', 'html']);
		}

		if (name === 'core/columns' || name === 'core/media-text') {
			settings.supports = omit(settings.supports, ['align', 'color', 'spacing', 'typography', 'anchor']);
			settings.supports.layout = {
				...settings.supports.layout,
				allowSwitching: false,
				allowEditing: true,
				allowInheriting: false,
				allowSizingOnChildren: false,
				allowVerticalAlignment: false, // Note: Setting to true just seems to double up because the setting for column seems to have no effect at the time of writing (it's there regardless)
				allowJustification: true,
				allowOrientation: false,
				default: {
					type: 'flex',
					alignItems: 'center',
					justifyContent: 'center',
					flexWrap: 'wrap',
				},
			};
		}

		if (name === 'core/column') {
			settings.supports = omit(settings.supports, ['align', 'spacing', 'typography']);
			settings.supports.color = {
				background: true,
				button: false,
			};
			settings.supports.layout = {
				...settings.supports.layout,
				allowSwitching: false,
				allowEditing: false,
				allowInheriting: false,
				allowVerticalAlignment: true, // Note: Setting to false had no effect at the time of writing so just running with it even though I originally wanted to only set this at the columns level, not for an individual colum
				allowJustification: false,
				default: {
					type: 'flow',
				},
			};
		}

		if (name === 'core/latest-posts') {
			settings.supports = omit(settings.supports, ['align']);
		}

		if (name === 'core/buttons') {
			settings.supports = omit(settings.supports, ['width', 'color', 'spacing', 'typography']);
		}

		if (name === 'core/button') {
			settings.supports = omit(settings.supports, ['shadow', 'anchor', 'alignWide']);
			settings.attributes = {
				...settings.attributes, ...{
					buttonColor: {
						type: 'string',
						default: 'primary',
					},
				},
			};

			if (settings.supports?.color) {
				settings.supports.color.button = true;
			}
		}

		return settings;
	});
}

/**
 * Change descriptions of core blocks
 */
function updateCoreBlockDescriptions() {
	wp.hooks.addFilter('blocks.registerBlockType', 'doublee/update-core-block-descriptions', function(settings, name) {
		if (name === 'core/media-text') {
			settings.description = '';
		}

		if (name === 'core/columns') {
			settings.description = 'Combine content into a multi-column layout. Note: Columns will stack on small screens automatically. Column widths will kick in when the visitor\'s viewport is large enough to accommodate them.\'';
		}

		if (name === 'core/column') {
			settings.description = `${settings.description} Note: Columns will stack on small screens automatically. Column widths will kick in when the visitor\'s viewport is large enough to accommodate them.'`;
		}

		return settings;
	});
}
