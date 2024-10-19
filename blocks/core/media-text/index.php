<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
	return;
}

// Uncomment and use these as needed. Note: Some are only relevant to blocks used at the top level, so code accordingly.
//$is_fullwidth = Starterkit_Block_Utils::get_is_fullwidth($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$width_classes = Starterkit_Block_Utils::get_width_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$bg_classes = Starterkit_Block_Utils::get_background_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$custom_classes = Starterkit_Block_Utils::get_custom_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$block_classes = array_merge($bg_classes, $custom_classes);
$inline_style = array();
$media_inline_style = array();
$focalPoint = '';
$fill = isset($args['block']['attrs']['imageFill']) && $args['block']['attrs']['imageFill'];
$mediaWidth = $args['block']['attrs']['mediaWidth'] ?? '50';
$mediaRight = isset($args['block']['attrs']['mediaPosition']) && $args['block']['attrs']['mediaPosition'] === 'right';
if (isset($args['block']['attrs']['focalPoint'])) {
	$focalPoint = $args['block']['attrs']['focalPoint']['x'] . '% ' . $args['block']['attrs']['focalPoint']['y'] . '%';
}
$inline_style = array(
	'grid-template-columns' => $mediaRight ? ('auto ' . $mediaWidth . '%') : ($mediaWidth . '% auto'),
);
$media_inline_style = array(
	'background-image'    => $fill ? 'url(' . wp_get_attachment_image_url($args['block']['attrs']['mediaId'], $args['block']['attrs']['mediaSizeSlug'] ?? 'full') . ')' : '',
	'background-position' => $focalPoint
);

?>
<div class="wp-block-media-text <?php echo $fill ? 'is-image-fill' : ''; ?> <?php echo $mediaRight ? ' has-media-on-the-right' : ''; ?>"
     style="<?php foreach ($inline_style as $property => $value) {
		 echo "$property:$value;";
	 } ?>">
    <figure class="wp-block-media-text__media<?php echo $mediaRight ? ' lg-order-2' : ''; ?>"
            style="<?php foreach ($media_inline_style as $property => $value) {
				echo "$property:$value;";
			} ?>">
		<?php echo wp_get_attachment_image($args['block']['attrs']['mediaId'], $args['block']['attrs']['mediaSizeSlug'] ?? 'full'); ?>
    </figure>
    <div class="wp-block-media-text__content">
		<?php
		if (isset($args['block']['innerBlocks'])) {
			Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], $args['args']);
		} ?>
    </div>
</div>
