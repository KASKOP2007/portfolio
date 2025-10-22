<?php /* Template Name: Page - Contact */ ?>
<?php get_header(); ?>
<?php get_template_part('template-parts/layouts/layout', 'hero')?>

<section class="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <?php if (get_field('contact_form')): ?>
					<div class="mt-lg-3 mt-2">
						<?php gravity_form(get_field('contact_form'), false, false, false, null, true); ?>
					</div>
				<?php endif; ?>
            </div>
            <div class="col-lg-5 offset-md-1">
                <div class="contact-content d-flex flex-column mt-8">
                    <span class="display-5 fw-semibold my-2"><?php echo get_field('contact_title') ?></span>
                    <span class="display-6 fw-normal mb-lg-4 mb-md-3 mb-2"><?php echo get_field('contact_info') ?></span>
                    <a class="contact-content__email text-decoration-none " href="mailto:<?php echo get_field('emailadres', 'options');  ?>"><?php echo get_field('emailadres', 'options'); ?></a>
                    <a class="contact-content__phone text-decoration-none mb-lg-7 mb-md-5 mb-3" href="tel:<?php echo get_field('telefoonnummer', 'options'); ?> "><?php echo get_field('telefoonnummer', 'options'); ?></a>
                    <?php echo growww_social(); ?>
                </div>
            </div>
        </div>
    </div>
</section>




<?php get_footer(); ?>