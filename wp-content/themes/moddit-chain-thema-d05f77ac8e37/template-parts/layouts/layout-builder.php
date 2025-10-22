<?php if ( have_rows( 'contentbuilder' ) ) : ?>
    <?php while ( have_rows('contentbuilder' ) ) : the_row(); ?>
        <?php if(get_row_layout()=='content') : ?>
            <section class="builder-content builder-row">
                <div class="container">
                    <h1><?php echo get_sub_field('title') ?></h1>
                    <div class="row">
                        <?php if ( have_rows('content_repeater') ) : ?>
                            <?php while( have_rows('content_repeater') ) : the_row(); ?>
                            <div class="col-lg-6">
                                <?php the_sub_field('text'); ?>
                            </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <?php if(get_row_layout()=='content_media') : ?>
            <section class="builder-contentmedia builder-row">
                <div class="container">
                    <div class="row g-xl-3 g-2 <?php echo get_sub_field('reverse_layout') ? 'flex-row-reverse' : ''; ?>">
                        <div class="col-md-6 order-1 d-flex flex-column justify-content-center ">
                            <?php if(get_sub_field('title_field')): ?>
                                <h2 class="builder-contentmedia__title display-2"> 
                                    <?php echo get_sub_field('title_field');?>
                                </h2>
                            <?php endif;?>
                            <div class="builder-contentmedia__text d-flex">
                                <?php echo get_sub_field('text_field') ?>
                            </div>
                        </div>
                        <div class="col-md-6 order-2">
                            <div class="builder-contentmedia__image position-relative">
                                <img class="img-abs-center" src="<?php echo get_sub_field('image_field')['url']?>" alt="<?php echo get_field('image_field')['alt'] ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <?php if (get_row_layout()=='quote'): ?>
            <section class="builder-quote builder-row">
                <div class="container">
                    <div class="builder-quote-content d-block text-center">
                        <div class="builder-quote-content__text display-3">
                            <?php echo get_sub_field('quote_text'); ?>
                        </div>
                        <div class="builder-quote-content__name">
                            <?php echo get_sub_field('quote_name'); ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif;?>
        <?php if (get_row_layout()=='image_slider') : ?>
            <div class="builder-slider builder-row">
                <div class="container">
                    <?php if ( have_rows('image_repeater') ) : ?>
                        <div class="js-images-slider">
                            
                        <?php while( have_rows('image_repeater') ) : the_row(); ?>
                            <div class="block-image position-relative">
                                <img class="img-abs-center" src="<?php echo get_sub_field('image')['url']; ?>" alt="<?php echo get_sub_field('image')['alt']; ?>">
                            </div>
                        <?php endwhile; ?>
                        </div>
                    
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endwhile; ?>
<?php endif; ?>