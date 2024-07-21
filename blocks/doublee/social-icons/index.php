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
$socials = get_field('social_media_links', 'options'); ?>
<?php if ($socials) { ?>
    <ul class="social-icons">
        <?php foreach ($socials as $social) { ?>
            <li class="social-icons__item">
                <a class="social-icons__item__link" href="<?php echo $social['url']; ?>" target="_blank" title="Visit us on <?php echo $social['label']; ?>">
                    <i class="fa-brands <?php echo $social['font_awesome_icon']; ?>"></i>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php }
do_action('doublee_block_layout_end');
