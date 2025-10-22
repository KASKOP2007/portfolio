<header class="header d-flex justify-content-between align-items-center">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="header__left">
            <a href="<?php echo get_home_url();?>" aria-label="<?php _e('home', 'chain'); ?>" class="header__logo">
                <?php bloginfo('name'); ?>
            </a>
        </div>
        <div class="js-header-right header__right d-xl-block d-none display-5">
            <nav class="js-header-menu">
                <?php chain_hoofdmenu(); ?>
            </nav>
        </div>
        <div class="js-header-toggle header-toggle"><span class="header-toggle__icon"><span></span></span></div>
    </div>
</header>