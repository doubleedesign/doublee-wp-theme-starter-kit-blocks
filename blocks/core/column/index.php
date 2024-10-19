<?php
// If column width are not set in the editor, assume we can divide them evenly
if ((empty($args['block']['attrs']['width'])) && isset($args['args']['total_cols'])) {
    $inline_style = array(
        'width'      => 100 / $args['args']['total_cols'] . '%',
        'flex-basis' => 100 / $args['args']['total_cols'] . '%',
        'align-self' => $args['block']['attrs']['verticalAlignment'] ?? 'center',
    );
}
else {
    // If column width is set, use that value (note: this is overridden for small screens in the CSS)
    $inline_style = array(
        'width'      => $args['block']['attrs']['width'] ?? '',
        'flex-basis' => $args['block']['attrs']['width'] ?? '',
        'align-self' => $args['block']['attrs']['verticalAlignment'] ?? 'center',
    );
}

$block_classes = array_merge(Starterkit_Block_Utils::get_background_classes($args['block']), Starterkit_Block_Utils::get_custom_classes($args['block'], 'frontend'));
?>
<div class="block wp-block-column block--innerblock col <?php echo implode(' ', $block_classes); ?>"
     style="<?php foreach ($inline_style as $property => $value) {
         echo "$property:$value;";
     } ?>">
    <?php
    if (isset($args['block']['innerBlocks'])) {
        Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
            'args'   => $args['args'],
            'parent' => 'core/column'
        ));
    }
    ?>
</div>
