<?php
global $post;
$is_editor = isset($is_preview) && isset($block) && $is_preview;
// Array of block data should be passed in from get_template_part as $args['block'] or from the editor as $block
if (!$is_editor && !isset($args) && !isset($args['args']['post_id']) || ($is_editor && !isset($block))) {
    return;
}
if (isset($post) && !in_array($post->post_type, ['page', 'shared_content'])) {
    return;
}
if(!isset($post->ID)) {
    return;
}

if (!function_exists('get_section_pages')) {
    function get_section_pages($page_id): array {
        $current = get_post($page_id);
        $ancestors = get_post_ancestors($current);
        $top = array_reverse($ancestors)[0] ?? $current->ID;
        $query = new WP_Query(array(
            'post_parent'    => $top,
            'post_status'    => array('publish'),
            'post_type'      => 'page',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC'
        ));
        $second = wp_list_pluck($query->posts, 'ID');

        return array(
            'id'       => $top,
            'children' => array_map(function ($id) {
                return array(
                    'id'       => $id,
                    'children' => wp_list_pluck(get_pages(array('child_of' => $id, 'sort_column' => 'menu_order')), 'ID')
                );
            }, $second)
        );
    }
}

$section = get_section_pages($post->ID);
if (!empty($section['children'])) {
    do_action('doublee_block_layout_start', $is_editor ? $block : $args['block'], $is_editor ? 'editor' : 'frontend', $args['args']['parent'] ?? null);
    if (!$is_editor) { ?>
        <div class="wp-block-in-this-section__header row row--inner">
            <div class="col-12">
                <h2>
                    <a href="<?php echo get_the_permalink($section['id']); ?>"><?php echo get_the_title($section['id']); ?></a>
                </h2>
            </div>
        </div>
    <?php } ?>
    <ul class="wp-block-in-this-section__content row row--inner">
        <?php if ($is_editor && $post->post_type === 'shared_content') { ?>
            <div class="alert alert--warning">
                <p>Can't preview a section of pages from shared content configuration. Please visit a page with
                    child pages or a parent page on the front-end to see this block.</p>
            </div>
        <?php } ?>
        <?php foreach ($section['children'] as $child) { ?>
            <div class="wp-block-in-this-section__content__item card-wrapper col-12 col-md-6 col-xl-4">
                <?php get_template_part('template-parts/card', '', array(
                    'card' => new Card($child['id'], false, true, true, 'horizontal', 'View page', $child['children'],)
                )); ?>
            </div>
        <?php } ?>
    </ul>
    <?php do_action('doublee_block_layout_end');
} ?>
