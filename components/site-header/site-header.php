<header id="masthead" class="site-header">
    <?php
    $name = get_bloginfo('name');
    $logo = wp_get_attachment_image_url(get_option('options_logo'), 'full');
    $menu = [];
    if(class_exists('Starterkit_Menus')) {
        $menu = Starterkit_Menus::get_nav_menu_items_by_location('primary', array('depth' => 2));
    }
    ?>
    <div data-vue-component="site-navigation" xmlns="../vue.xsd">
        <site-navigation
                logourl='<?php echo $logo; ?>'
                sitename='<?php echo $name; ?>'
                menu='<?php echo json_encode($menu); ?>'
                background="primary"
        >
        </site-navigation>
    </div>
</header>
