(function(wp) {
	const { createHigherOrderComponent } = wp.compose;
	const { addFilter } = wp.hooks;
	const { Fragment } = wp.element;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, ToggleControl } = wp.components;

	// Add the attribute to the cover block
	const addAttributes = (settings) => {
		if (settings.name !== 'core/cover') {
			return settings;
		}

		settings.attributes = {
			...settings.attributes,
			hasContainedBackground: {
				type: 'boolean',
				default: false,
			},
		};

		return settings;
	};

	addFilter('blocks.registerBlockType', 'custom/cover-block-attributes', addAttributes);

	// Add the control to the block's inspector controls
	const withInspectorControls = createHigherOrderComponent((BlockEdit) => {
		return (props) => {
			if (props.name !== 'core/cover') {
				return wp.element.createElement(BlockEdit, props);
			}

			const { attributes, setAttributes } = props;
			const { containedBackground } = attributes;

			return wp.element.createElement(
				Fragment,
				{},
				wp.element.createElement(BlockEdit, props),
				wp.element.createElement(
					InspectorControls,
					{},
					wp.element.createElement(
						PanelBody,
						{ title: 'Responsive design' },
						wp.element.createElement(ToggleControl, {
							label: 'Contained image on larger screens',
							checked: containedBackground,
							onChange: (value) => setAttributes({ containedBackground: value }),
						})
					)
				)
			);
		};
	}, 'withInspectorControls');

	addFilter('editor.BlockEdit', 'custom/cover-block-inspector-controls', withInspectorControls);

	// Add the custom class to the block in the front-end
	const applyExtraClass = (extraProps, blockType, attributes) => {
		if (blockType.name !== 'core/cover') {
			return extraProps;
		}

		if (attributes.containedBackground) {
			extraProps.className = (extraProps.className || '') + ' is-style-contained-image-lg';
		}

		return extraProps;
	};

	addFilter('blocks.getSaveContent.extraProps', 'custom/cover-block-extra-class', applyExtraClass);
}(window.wp));
