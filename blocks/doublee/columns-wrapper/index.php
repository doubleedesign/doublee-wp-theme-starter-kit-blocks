<?php
$is_editor = isset($is_preview) && isset($block) && $is_preview;
// Array of block data should be passed in from get_template_part as $args['block'] or from the editor as $block
if (!$is_editor && !isset($args) || ($is_editor && !isset($block))) {
    return;
}

$allowed_blocks = ['core/columns'];

$default_blocks = array(
    array('core/columns', array()),
);

do_action('doublee_block_layout_start', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');

if ($is_editor) { ?>
    <InnerBlocks template="<?php echo esc_attr(wp_json_encode($default_blocks)); ?>"
                 allowed_blocks="<?php echo esc_attr(wp_json_encode($allowed_blocks)); ?>"
                 templateLock="false"></InnerBlocks>
<?php }
if (!$is_editor && isset($args['block'])) {
    if ($args['block']['innerBlocks']) {
        Doublee_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
            'args'   => $args['args'],
            'parent' => 'doublee/columns'
        ));
    }
}
do_action('doublee_block_layout_end');
