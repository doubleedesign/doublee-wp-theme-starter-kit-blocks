<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
	return;
} ?>
<div class="wp-block-buttons <?php echo $args['block']['attrs']['className']; ?>
            is-content-justification-<?php echo $args['block']['attrs']['layout']['justifyContent'] ?? 'left'; ?>
            is-layout-<?php echo $args['block']['attrs']['layout']['type'] ?? 'flex'; ?>
            wp-block-buttons-is-layout-<?php echo $args['block']['attrs']['layout']['type'] ?? 'flex'; ?>"
>
	<?php
	if (isset($args['block']['innerBlocks'])) {
		Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
			'args'   => $args['args'],
			'parent' => 'core/buttons'
		));
	} ?>
</div>

