<?php get_header(); ?>

<section>
	<div class="container">
		<h1><?php echo sprintf(__('Zoekresultaten voor: %s', 'chain'), get_search_query()); ?></h1>
		<?php if(have_posts()): ?>
			<div class="row">
				<?php while(have_posts()): the_post(); ?>
					<?php get_template_part( 'template-parts/blocks/block' ); ?>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>