<?php
if (!isset($args) || !$args['date']) return;
try {
    $date = new DateTime($args['date']);
    ?>
    <div class="date-block">
        <span class="date-block__day"><?php echo $date->format('D'); ?></span>
        <span class="date-block__date"><?php echo $date->format('d'); ?></span>
        <span class="date-block__month"><?php echo $date->format('M'); ?></span>
        <span class="date-block__year"><?php echo $date->format('Y'); ?></span>
    </div>
<?php } catch (Exception $e) {
} ?>
