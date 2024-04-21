<?php
get_header();
$blocks = parse_blocks(get_the_content(null, null, PAGE_FOR_POSTS));
Doublee_Block_Utils::output_custom_blocks($blocks, array(
	'args'   => [],
	'parent' => 'archive'
));
global $wp_query;
?>
    <section class="archive-section">
		<?php
		$count = 0;
		$total_posts = $wp_query->post_count;
		if (have_posts()) {
			while (have_posts()) {
				the_post();
				$count++;
				if ($count === 1) {
					$class_name = 'has-primary-white-gradient-background';
				}
				else if ($count === $total_posts) {
					$class_name = 'has-white-light-gradient-background';
				}
				else {
					$class_name = 'has-white-background-color';
				}
				?>
                <div class="archive-section__post-wrapper <?php echo $class_name; ?>">
                    <div class="row">
                        <div class="col-xs-12 col-lg-10 col-xl-9">
							<?php
							get_template_part('template-parts/card', '', array(
								'card' => new Card(get_the_id(), true, false, true, 'horizontal', 'Read more', [])
							)); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
		else {
			get_template_part('template-parts/no-content');
		}
		?>
    </section>
<?php
if (have_posts()) {
	get_template_part('template-parts/pagination');
}
get_footer();
