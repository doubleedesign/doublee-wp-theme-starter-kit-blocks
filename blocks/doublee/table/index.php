<?php
$is_editor = isset($is_preview) && isset($block) && $is_preview;
// Array of block data should be passed in from get_template_part as $args['block'] or from the editor as $block
if (!$is_editor && !isset($args) || ($is_editor && !isset($block))) {
	return;
}

// If this block's output is overridden in the child theme, load that in the editor
// For front-end output this is handled by the output_custom_blocks() function in class-block-utils.php
if ($is_editor && isset($block)) {
	$theme = get_option('stylesheet');
	if (file_exists(dirname(__DIR__, 4) . '/' . $theme . '/components/blocks/' . $block['name'] . '/index.php')) {
		include(dirname(__DIR__, 4) . '/' . $theme . '/components/blocks/' . $block['name'] . '/index.php');
		return;
	}
}

$allowed_blocks = ['core/heading', 'core/table', 'core/paragraph'];
$default_blocks = array(
	array('core/heading', array(
		'level'   => 2,
		'content' => 'Table section heading'
	)),
	array('core/table', array())
);

do_action('doublee_block_layout_start', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend', $args['args']['parent'] ?? null);

if ($is_editor) { ?>
    <InnerBlocks template="<?php echo esc_attr(wp_json_encode($default_blocks)); ?>"
                 allowedBlocks="<?php echo esc_attr(wp_json_encode($allowed_blocks)); ?>"
                 templateLock="false">
    </InnerBlocks>
<?php }
if (!$is_editor && isset($args['block'])) {
	if ($args['block']['innerBlocks']) {
		Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
			'args'   => $args['args'],
			'parent' => 'doublee/table'
		));
	}
}
do_action('doublee_block_layout_end');
