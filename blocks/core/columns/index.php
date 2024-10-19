<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
    return;
}

do_action('doublee_block_layout_start', $args['block'], 'frontend', $args['args']['parent'] ?? null);
if (isset($args['block']['innerBlocks'])) {
    Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
        'args'       => $args['args'],
        'parent'     => 'core/columns',
        'total_cols' => count($args['block']['innerBlocks'])
    ));
}
do_action('doublee_block_layout_end');
