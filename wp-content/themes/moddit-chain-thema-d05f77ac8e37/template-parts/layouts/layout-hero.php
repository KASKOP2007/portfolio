
<?php get_header(); ?>
<section class="hero mb-xl-4 mb-lg-3 mb-2">
    <div class="hero-image position-relative">
        <img class="img-abs-center" src="<?php echo get_field('hero_image')['url'] ?>" alt="<?php echo get_field('hero_image')['url']; ?>">
    </div>
    <div class="hero-title d-flex flex-column justify-content-center align-items-center">
        <?php if(get_field('hero_title')): ?>
            <h1><?php echo get_field('hero_title'); ?></h1>
        <?php else :  ?>
            <h1><?php echo get_the_title(); ?></h1>
        <?php endif; ?>
    </div>
</section>