<?php
if (!isset($args)) return;
$id = $args['id'];
$location = get_field('location', $id);
$date = get_field('start_date', $id);
?>
<div class="card card-event">
    <a href="<?php echo get_the_permalink($id); ?>" class="card__content card-event__content">
        <div class="card__content__date card-event__content__date col-3">
            <?php get_template_part('components/date-block/date-block', '', array('date' => $date)); ?>
        </div>
        <div class="card__content__copy card-event__content__copy entry-content col-9 has-white-background-color">
            <h3><?php echo get_the_title($id); ?></h3>
            <?php if ($location) { ?>
                <p class="card-event__content__copy__date">
                    <i class="fa-solid fa-location-dot"></i>Location: <?php echo $location; ?>
                </p>
            <?php } ?>
        </div>
    </a>
</div>
