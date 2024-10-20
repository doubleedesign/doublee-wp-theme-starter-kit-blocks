<div class="excerpt col-xs-12 col-lg-10 col-xl-9">
	<div href="<?php the_permalink(); ?>" class="excerpt__content">
		<?php if(get_post_thumbnail_id()) { ?>
			<div class="excerpt__content__image">
				<?php the_post_thumbnail('large'); ?>
			</div>
		<?php } ?>
		<div class="excerpt__content__copy entry-content">
			<h2><?php the_title(); ?></h2>
			<span class="post-date">Posted on <?php echo get_the_date(); ?></span>
			<?php echo Starterkit_Theme_Frontend_Utils::get_custom_excerpt(get_the_excerpt(), 25); ?>
			<a class="btn btn--primary" href="<?php the_permalink(); ?>">Read article</a>
		</div>
	</div>
</div>
