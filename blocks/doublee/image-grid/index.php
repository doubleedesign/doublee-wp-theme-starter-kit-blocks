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
if ($is_editor) { ?>
    <InnerBlocks template="<?php echo esc_attr(wp_json_encode($default_blocks)); ?>"
                 allowedBlocks="<?php echo esc_attr(wp_json_encode($allowed_blocks)); ?>"></InnerBlocks>
<?php }
if (!$is_editor && isset($args['block'])) {
	if ($args['block']['innerBlocks']) {
		Doublee_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
			'args'   => $args['args'],
			'parent' => 'doublee/image-grid'
		));
	}
}

$intro_text = Doublee_Block_Utils::get_acf_field_for_block('intro_text', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
$image_ids = Doublee_Block_Utils::get_acf_field_for_block('images', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
?>

<div class="wp-block-image-grid__intro row row--inner">
    <div class="col-12">
        <?php echo wpautop($intro_text); ?>
    </div>
</div>
<div class="wp-block-image-grid__images">
    <?php foreach ($image_ids as $image_id) { ?>
        <div class="wp-block-image-grid__images__image">
            <?php echo wp_get_attachment_image($image_id, 'full'); ?>
        </div>
    <?php } ?>
</div>
<?php
do_action('doublee_block_layout_end');
