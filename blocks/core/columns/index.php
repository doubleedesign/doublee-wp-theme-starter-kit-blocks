<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
    return;
} ?>
<div class="block block__columns block--innerblock row row--inner">
    <?php
    if (isset($args['block']['innerBlocks'])) {
        Doublee_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
            'args'       => $args['args'],
            'parent'     => 'core/columns',
            'total_cols' => count($args['block']['innerBlocks'])
        ));
    } ?>
</div>
