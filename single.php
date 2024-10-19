<?php
get_header();
$blocks = parse_blocks(get_the_content(null, null, PAGE_FOR_POSTS));
Starterkit_Block_Utils::output_custom_blocks($blocks, array(
    'args'   => [],
    'parent' => 'archive'
));
?>
    <section class="single-post-section pseudo-block wp-block-copy">
        <div class="row">
            <div class="entry-content col-xs-12 col-lg-10 col-xl-9">
                <?php if (has_post_thumbnail()) { ?>
                    <figure class="single-post-section__image wp-caption">
                        <?php the_post_thumbnail('large'); ?>
                        <figcaption class="single-post-section__image__caption wp-caption-text">
                            <?php the_post_thumbnail_caption(); ?>
                        </figcaption>
                    </figure>
                <?php } ?>
                <div class="single-post-section__content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </section>
<?php
get_footer();
