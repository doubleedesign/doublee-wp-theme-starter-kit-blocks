<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
    return;
}

// do_action('doublee_block_layout_start', $args['block'], 'frontend'); // remove the block__template-name div below if using this ?>
    <?php
    // Pick your poison:
    // render_block is useful for things like embeds that I want to let the built-in rendering take care of
    // apply_filters('the_content') is needed for some block types to render properly, others just render_block is fine
    echo apply_filters('the_content', render_block($args['block']));
    // This is for core blocks that have inner blocks that I want full control over
    //    if (isset($args['block']['innerBlocks'])) {
    //        Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], $args['args']);
    //    }
    ?>
<?php // do_action('doublee_block_layout_end'); ?>
