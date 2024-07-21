<?php
$is_editor = isset($is_preview) && isset($block) && $is_preview;
// Array of block data should be passed in from get_template_part as $args['block'] or from the editor as $block
if (!$is_editor && !isset($args) || ($is_editor && !isset($block))) {
	return;
}

$default_blocks = array(
	array('doublee/contact-details', array()),
    array('doublee/social-icons', array())
);

$allowed_blocks = ['core/heading', 'core/paragraph', 'doublee/contact-details', 'doublee/social-icons'];

do_action('doublee_block_layout_start', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend', $args['args']['parent'] ?? null);
?>
<div class="row row--inner">
    <div class="wp-block-contact-form__copy col-12 col-lg-5">
        <?php
        if ($is_editor) { ?>
            <InnerBlocks template="<?php echo esc_attr(wp_json_encode($default_blocks)); ?>"
                    allowedBlocks="<?php echo esc_attr(wp_json_encode($allowed_blocks)); ?>"></InnerBlocks>
        <?php }
        if (!$is_editor && isset($args['block'])) {
            if ($args['block']['innerBlocks']) {
                Doublee_Block_Utils::output_custom_blocks($args['block']['innerBlocks'], array(
                    'args'   => $args['args'],
                    'parent' => 'doublee/contact-form'
                ));
            }
        } ?>
    </div>
    <div class="wp-block-contact-form__form col-12 col-lg-7">
        <?php echo do_shortcode(Doublee_Block_Utils::get_acf_field_for_block('form_shortcode', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend')); ?>
    </div>
</div>
<?php
do_action('doublee_block_layout_end');
