<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
	return;
}

if (!isset($args['args']['parent']) || $args['args']['parent'] === 'core/group') {
	?>
    <section class="block block__cover">
		<?php echo apply_filters('the_content', render_block($args['block'])); ?>
    </section>
<?php } else { ?>
    <div class="block block__cover">
		<?php echo apply_filters('the_content', render_block($args['block'])); ?>
    </div>
<?php }
