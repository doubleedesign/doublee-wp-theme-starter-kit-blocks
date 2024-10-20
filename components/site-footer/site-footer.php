<footer id="colophon" class="site-footer">
    <div class="site-footer__main">
        <div class="row">

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
