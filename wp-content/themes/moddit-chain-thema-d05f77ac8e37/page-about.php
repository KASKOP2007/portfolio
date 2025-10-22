<?php /* Template Name: Page - Over ons */ ?>
<?php get_header(); ?>
<section class="hero-about"> 
    <?php echo get_template_part('template-parts/layouts/layout', 'hero')?>
</section>

<section class="mt-xl-12 mt-lg-9 mt-md-6 mt-3">
    <div class="container">
        <div class="row">
            <div class="col-xl-2 col-md-3">
               <span class="d-block mb-2 display-4 fw-medium"><?php _e('EXPERIENCES', 'portfolio');?></span>
            </div>
            <div class="col-xl-10 col-md-9">
                <div class="row g-5">
                    <?php if(have_rows('experiences_block')) : ?>
                        <?php while (have_rows('experiences_block')) : the_row(); ?>
                            <div class="col-lg-4">
                                <?php get_template_part( 'template-parts/blocks/block', 'experience' ); ?>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-xl-12 mt-lg-9 mt-md-6 mt-3">
    <div class="container">
        <div class="row">
            <div class="col-xl-2 col-md-3">
               <span class="d-block display-4 fw-medium mb-2"><?php _e('MY SKILLS', 'portfolio');?></span>
            </div>
             <div class="col-xl-10 col-md-9">
                <div class="row g-5">
                <?php if ( have_rows('skills_block') ) : ?>
                    <?php while( have_rows('skills_block') ) : the_row(); ?>
                        <div class="<?php echo (count(get_field('skills_block')) >= 4 ) ? 'col-lg-3' : 'col-lg-4'; ?> col-6">
                            <?php get_template_part( 'template-parts/blocks/block', 'skills' ); ?>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-xl-12 mt-lg-9 mt-md-6 mt-3">
    <div class="container">
        <div class="row">
            <div class="col-xl-2 col-md-3">
               <span class="d-block mb-2 display-4 fw-medium"><?php _e('CODING SKILLS', 'portfolio');?></span>
            </div>
             <div class="col-xl-10 col-md-9">
                <div class="row g-5">
                    <?php if ( have_rows('coding_block') ) : ?>               
                        <?php while( have_rows('coding_block') ) : the_row(); ?>
                            <div class="col-lg-6">
                                <?php get_template_part( 'template-parts/blocks/block', 'progressbar' ); ?>
                            </div>
                        <?php endwhile; ?>             
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<?php get_footer()?>



