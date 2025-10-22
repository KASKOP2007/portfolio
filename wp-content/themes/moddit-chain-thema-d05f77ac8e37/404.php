<?php get_header(); ?>

<section class="vh-100  d-flex justify-content-center align-items-center">
    <div class="container d-flex flex-column align-items-center">
        <h1 class="display-1 mb-1" ><?php _e('404') ?></h1>
        <h1 class="display-3 mb-4" ><?php _e('Page not found!') ?></h1>
        <h1 class="display-5"><?php _e('Sorry, this page doesn′t exist.', 'chain'); ?></h1>
        <p><?php echo sprintf(__('We tried our best, but it looks like this page doesn′t exist anymore. click <a href="%s">here</a> to go back to the homepage.', 'chain'), home_url()); ?> </p>
    </div>
</section>

<?php get_footer(); ?>