<?php
$is_editor = isset($is_preview) && isset($block) && $is_preview;
// Array of block data should be passed in from get_template_part as $args['block'] or from the editor as $block
if (!$is_editor && !isset($args) || ($is_editor && !isset($block))) {
	return;
}

// If this block's output is overridden in the child theme, load that in the editor
// For front-end output this is handled by the output_custom_blocks() function in class-block-utils.php
if ($is_editor && isset($block)) {
	$theme = get_option('stylesheet');
	if (file_exists(dirname(__DIR__, 4) . '/' . $theme . '/components/blocks/' . $block['name'] . '/index.php')) {
		include(dirname(__DIR__, 4) . '/' . $theme . '/components/blocks/' . $block['name'] . '/index.php');
		return;
	}
}

do_action('doublee_block_layout_start', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend', $args['args']['parent'] ?? null);
?>
<header class="pseudo-block__header">
    <h2>Upcoming Events</h2>
</header>
<?php
$upcoming = Starterkit_Events_Utils::get_upcoming_event_ids(3);
if ($upcoming) { ?>
    <div class="card-group-events">
		<?php
		foreach ($upcoming as $event_id) {
			get_template_part('template-parts/card-event', '', array('id' => $event_id));
		} ?>
    </div>
	<?php
}
else { ?>
    <div class="row">
        <div class="col-xs-12 col-lg-10">
            <div class="alert alert--info">There are no upcoming events currently scheduled.</div>
        </div>
    </div>
<?php }
do_action('doublee_block_layout_end');
