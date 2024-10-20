<?php
the_post();
get_header();
?>

<?php get_template_part('partials/breadcrumbs'); ?>

<header class="pseudo-module post-archive__header page-header">
    <div class="row">
        <div class="col-xs-12 entry-content">
            <h1><?php echo get_the_title(PAGE_FOR_POSTS); ?></h1>
        </div>
    </div>
</header>

<article class="pseudo-module single-post row">
    <div class="col-xs-12 col-lg-10 col-xl-9">
        <header class="single-post__header entry-content">
            <div class="single-post__header__title">
                <h1><?php the_title(); ?></h1>
            </div>
            <div class="single-post__header__meta">
                <?php echo Starterkit_Theme_Frontend_Utils::get_entry_meta(); ?>
            </div>
        </header>
        <?php if(has_post_thumbnail()) { ?>
            <figure class="single-post__image">
                <?php the_post_thumbnail('large'); ?>
                <?php if(get_the_post_thumbnail_caption()) { ?>
                    <figcaption class="single-post__image__caption"><?php the_post_thumbnail_caption(); ?></figcaption>
                <?php } ?>
            </figure>
        <?php } ?>
        <div class="single-post__copy entry-content">
            <?php the_content(); ?>
        </div>
    </div>
</article>

<?php get_footer(); ?>
