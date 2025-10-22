
<?php get_header();?>
<?php get_template_part("template-parts/layouts/layout", "hero")?>
<section class="project">
    <div class="container">		
		<div class="row justify-content-center mt-lg-5 mt-md-4 mt-3">
			<?php
				$args = array(
					'posts_per_page' => 12,
					'post_type' => 'project',
				);	
			$query = new WP_Query( $args );
			?>
			<?php if( $query->have_posts() ) : ?>
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<div class="col-lg-4 col-md-6 mb-md-3 mb-2">
						<?php get_template_part('template-parts/blocks/block', 'project')?>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_query(); ?>
		</div>
	</div>
</section>

<?php get_footer();?>