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

$title = get_the_title();
if (is_archive() && !is_post_type_archive()) {
    $title = get_the_archive_title();
}
if (is_post_type_archive()) {
    $title = get_post_type_object(get_post_type())->labels->name;
}
if (is_search()) {
    $title = 'Search Results for: ' . get_search_query();
}
if (is_home()) {
    $title = 'News';
}

if (isset($block) || isset($args['block'])) {
    do_action('doublee_block_layout_start', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend');
    do_action('doublee_breadcrumbs'); ?>
    <h1><?php echo $title; ?></h1>
    <?php
    do_action('doublee_block_layout_end');
}
