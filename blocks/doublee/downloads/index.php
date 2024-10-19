<?php
$is_editor = isset($is_preview) && isset($block) && $is_preview;
// Array of block data should be passed in from get_template_part as $args['block'] or from the editor as $block
if (!$is_editor && !isset($args) || ($is_editor && !isset($block))) {
	return;
}

$default_blocks = array(
);

$allowed_blocks = [];

do_action('doublee_block_layout_start', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend', $args['args']['parent'] ?? null);
    if ($is_editor) { ?>
        <InnerBlocks template="<?php echo esc_attr(wp_json_encode($default_blocks)); ?>"
                     allowedBlocks="<?php echo esc_attr(wp_json_encode($allowed_blocks)); ?>"></InnerBlocks>
    <?php }
    if (!$is_editor && isset($args['block'])) {
        if ($args['block']['innerBlocks']) {
            Starterkit_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
                'args'   => $args['args'],
                'parent' => 'doublee/downloads'
            ));
        }
    }

    $file_ids = Starterkit_Block_Utils::get_acf_field_for_block('files', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
    if ($file_ids) {
        $files = array_map(fn($file_id) => array(
            'url' => wp_get_attachment_url($file_id),
            'label' => get_the_title($file_id),
            'type' => get_post_mime_type($file_id),
        ), $file_ids); ?>
        <ul class="wp-block-downloads__file-list">
            <?php foreach ($files as $file) { ?>
                <li class="wp-block-downloads__file-list__item">
                    <a href="<?php echo esc_url($file['url']); ?>" class="wp-block-downloads__file-list__item__link" target="_blank">
                        <?php if ($file['type'] === 'application/pdf') { ?>
                            <i class="fa-solid fa-file-pdf"></i>
                        <?php } else { ?>
                            <i class="fa-solid fa-download"></i>
                        <?php } ?>
                        <?php echo $file['label']; ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php }
do_action('doublee_block_layout_end');
