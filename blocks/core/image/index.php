<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
    return;
} ?>
<div class="block wp-block-image block--innerblock">
    <?php echo apply_filters('the_content', render_block($args['block'])); ?>
</div>
