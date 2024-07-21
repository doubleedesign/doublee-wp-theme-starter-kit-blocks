<?php
// Note: For core blocks, this file controls front-end output only.
if (!isset($args['block'])) {
	return;
}

// Uncomment and use these as needed. Note: Some are only relevant to blocks used at the top level, so code accordingly.
//$is_fullwidth = Doublee_Block_Utils::get_is_fullwidth($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$width_classes = Doublee_Block_Utils::get_width_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$bg_classes = Doublee_Block_Utils::get_background_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$custom_classes = Doublee_Block_Utils::get_custom_classes($is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
//$block_classes = array_merge($bg_classes, $custom_classes);

$tiles = array_filter($args['block']['innerBlocks'], function ($block) {
	return $block['blockName'] === 'custom/tile';
});
$tiles_count = count($tiles);
if ($tiles_count === 0) {
	return;
}
$headings_count = count($args['block']['innerBlocks']) - $tiles_count;
$rows_of_tiles = $args['block']['attrs']['rowCount'] ?? ceil($tiles_count / 2);
$row_count = $rows_of_tiles + $headings_count;
$col_count = ceil($tiles_count / $row_count);
?>
<div class="wp-block-tiles" data-ideal-row-count="<?php echo $row_count; ?>"
     data-ideal-col-count="<?php echo $col_count; ?>"
>
    <?php if (isset($args['block']['innerBlocks'])) {
        Doublee_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], $args['args']);
    } ?>
</div>
