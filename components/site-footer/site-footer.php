<footer id="colophon" class="site-footer">
    <div class="site-footer__main">
        <div class="row">
            <?php get_template_part('components/social-icons/social-icons'); ?>
            <?php
            $menu = [];
            if(class_exists('Starterkit_Menus')) {
                $menu = Starterkit_Menus::get_nav_menu_items_by_location('footer', array('depth' => 1));
            } ?>
            <nav class="site-footer__main__nav col-12">
                <ul>
                    <?php foreach($menu as $item) { ?>
                        <li>
                            <a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
    <div class="site-footer__fineprint">
        <div class="row">
            <div class="site-footer__fineprint__item col-xs-12 col-md-6">
                <small>
                    <?php
                    echo get_bloginfo('name') . ' ';
                    if(date('Y') > 2024) {
                        echo '2024-';
                    }
                    echo date('Y') . '.';
                    ?>
                </small>
            </div>
            <div class="site-footer__fineprint__item col-xs-12 col-md-6">
                <small>Website by <a href="https://www.doubleedesign.com.au" target="_blank">Double-E Design</a>.</small>
            </div>
        </div>
    </div>
</footer>
