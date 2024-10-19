<?php
// I am effectively using the group block as a functional wrapper for other blocks that does not do anything else.
// It is used purely to act as a parent for top-level blocks, because at the time of writing you cannot restrict blocks to ONLY be available at the top level.
// It does not output its own markup, so a bunch of settings and attributes for it are removed in blocks.js and theme.json accordingly.
if (isset($args['block']['innerBlocks'])) {
	Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
		'args'   => $args['args'],
		'parent' => 'core/group'
	));
}
