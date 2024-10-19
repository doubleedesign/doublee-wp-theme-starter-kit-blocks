<?php
// Output of "shared content" CPT blocks
if (isset($args['block']['innerBlocks'])) {
	Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
		'args'   => $args['args'],
		'parent' => 'shared-content'
	));
}
