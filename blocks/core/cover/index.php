<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
	return;
}

do_action('doublee_block_layout_start', $args['block'], 'frontend', $args['args']['parent'] ?? null);
echo apply_filters('the_content', render_block($args['block']));
do_action('doublee_block_layout_end');
