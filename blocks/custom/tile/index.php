<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
	return;
}

// Uncomment and use these as needed. Note: Some are only relevant to blocks used at the top level, so code accordingly.
//$is_fullwidth = Starterkit_Block_Utils::get_is_fullwidth($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$width_classes = Starterkit_Block_Utils::get_width_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$bg_classes = Starterkit_Block_Utils::get_background_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$custom_classes = Starterkit_Block_Utils::get_custom_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$block_classes = array_merge($bg_classes, $custom_classes);
?>
<?php echo apply_filters('the_content', render_block($args['block'])); ?>
