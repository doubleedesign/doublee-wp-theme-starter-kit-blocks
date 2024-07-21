<?php
$is_editor = isset($is_preview) && isset($block) && $is_preview;
// Array of block data should be passed in from get_template_part as $args['block'] or from the editor as $block
if (!$is_editor && !isset($args) || ($is_editor && !isset($block))) {
	return;
}

$default_blocks = array(
	array('', array())
);

$allowed_blocks = [];

do_action('doublee_block_layout_start', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend', $args['args']['parent'] ?? null);
    echo Newport_Theme_Frontend_Utils::get_address('expanded');
do_action('doublee_block_layout_end');
