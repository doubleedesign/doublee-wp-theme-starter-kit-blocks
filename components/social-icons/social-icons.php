<?php
$socials = get_field('social_media_links', 'options'); ?>
<?php if ($socials) { ?>
    <ul class="social-icons">
        <?php foreach ($socials as $social) { ?>
            <li class="social-icons__item">
                <a class="social-icons__item__link" href="<?php echo $social['url']; ?>" target="_blank" title="Visit us on <?php echo $social['label']; ?>">
                    <i class="fa-brands <?php echo $social['font_awesome_icon']; ?>"></i>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php }
