<?php
if (!isset($args['card']) || !$args['card'] instanceof Card) {
	return;
}
$card = $args['card'];
?>
<div class="card">
    <div class="card__inner has-white-background-color row">
		<?php if ($card->isWithImage()) { ?>
            <div class="card__inner__image <?php echo $card->getOrientation() === 'horizontal' ? 'col-4' : 'col-12'; ?>">
				<?php echo get_the_post_thumbnail($card->getPostId(), 'medium'); ?>
            </div>
		<?php } ?>
        <div class="card__inner__content entry-content <?php echo ($card->getOrientation() === 'vertical' || !$card->isWithImage()) ? 'col-12' : 'col-8'; ?>">
            <div>
                <h3>
                    <a href="<?php echo get_permalink($card->getPostId()); ?>"><?php echo get_the_title($card->getPostId()); ?></a>
                </h3>
				<?php if ($card->isWithExcerpt()) { ?>
					<?php echo wpautop(Doublee_Frontend::get_custom_excerpt(get_the_excerpt($card->getPostId()), 25)); ?>
				<?php } ?>
				<?php if ($card->getExtraLinks()) { ?>
                    <ul class="card__inner__content__extra-links">
						<?php foreach ($card->getExtraLinks() as $link_id) { ?>
                            <li>
                                <a href="<?php echo get_the_permalink($link_id); ?>"><?php echo get_the_title($link_id); ?></a>
                            </li>
						<?php } ?>
                    </ul>
				<?php } ?>
            </div>
			<?php if ($card->isWithButton() && $card->getReadMoreText()) { ?>
                <a href="<?php echo get_the_permalink($card->getPostId()); ?>" class="btn btn--primary btn--small">
					<?php echo $card->getReadMoreText(); ?>
                </a>
			<?php } ?>
        </div>
    </div>
</div>
