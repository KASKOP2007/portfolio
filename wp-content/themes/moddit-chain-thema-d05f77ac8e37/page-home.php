<?php /* Template Name: Homepagina */ ?>
<?php get_header(); ?>
<script src="https://unpkg.com/typed.js@2.0.15/dist/typed.umd.js"></script>
<section class="home-hero">
	<div class="container">
		<div class="block-home-hero my-lg-4">
			<div class="row">
				<div class="col-lg-7 col-md-9 ">
					<span class="display-2 "><?php echo get_field('hero_title_top'); ?></span>
					<h1 class="display-1 "><?php echo get_field('hero_title'); ?></h1>
					<span  class="display-2 "><?php echo get_field('hero_title_bottom'); ?> <span class="text text-primary"> </span></span>
					<p class="display-5 fw-normal my-lg-4 my-md-3 my-2"><?php echo get_field('hero_sub_title')  ?> </p>
					<?php echo growww_social(); ?>
					<a href="<?php echo get_field('hero_button')['url'] ?>" target="<?php echo get_field('hero_button')['target'] ?>" class="btn btn-primary hero-btn rounded-pill fw-medium mt-lg-4 mt-md-3 mt-2 py-lg-1 px-lg-4"><?php echo get_field('hero_button')['title'] ?></a>
				<div class="col-md-6">
				</div>
			</div>
		</div>
	</div>
</section>
<section class="aboutme">	
	<div class="container">
		<div class="row">
			<div class="col-lg-7 col-md-9 offset-lg-5 offset-lg-3">
				<h2 class="display-1 mb-lg-3 mb-2"><?php echo get_field('about_title') ?></h2>
				<span class="display-3 fw-semibold  "><?php echo get_field('about_subtitle') ?></span>
				<p class="display-5 fw-normal mt-lg-4 mt-md-3 mt-2 "><?php echo get_field('about_text') ?></p>
			</div>
		</div>
	</div>
</section>
<section class="services">
	<div class="container">
		<h1 class="services-title my-xl-11 my-lg-7 my-md-4 my-2 "><?php echo get_field('services_title') ?></h1>
		<div class="row justify-content-center">
			<?php if ( have_rows('services_repeater') ) : ?>
				<?php while( have_rows('services_repeater') ) : the_row(); ?>
					<div class="col-xl-4 col-md-6 py-3  px-md-3 px-2">
						<?php get_template_part('template-parts/blocks/block', 'service') ?>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
<section class="skills">
	<div class="container">
		<h1 class="skills-title display-1 my-xl-11 my-lg-7 my-md-4 my-2"><?php echo get_field('skills_titel') ?></h1>
		<div class="row ">
			<div class="col-md-6">
				<h2 class="d-flex justify-content-center"><?php echo get_field('technical_title') ?></h2>
				<?php if ( have_rows('technical_skills') ) : ?>
					<?php while( have_rows('technical_skills') ) : the_row(); ?>
						<div class="skills-technical d-flex flex-column mb-lg-5 mb-md-3 mb-2">
							<i class="skills-technical__icon"><?php echo get_sub_field('technical_skills_icon'); ?></i>
							<span class="skills-technical__title"><?php echo get_sub_field('technical_skills_title'); ?></span>
							<div class="progress">
								<div class="progress-bar" role="progressbar" style="width: <?php echo get_sub_field('technical_skills_percent') ?>%;" aria-valuenow="<?php echo get_sub_field('technical_skills_percent') ?>" aria-valuemin="0" aria-valuemax="100"><?php echo get_sub_field('technical_skills_percent') ?>%</div>
							</div>
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
			<div class="col-md-6">
				<h2 class="d-flex justify-content-center"><?php echo get_field('professional_title') ?></h2>
				<div class="row mt-lg-10 mt-md-7 mt-3">
					<?php if ( have_rows('professional_skills') ) : ?>
						<?php while( have_rows('professional_skills') ) : the_row(); ?>
							<div class="col-6">
								<div class="skills-professional d-flex flex-column align-items-center ">
									<div class="progress-ring" style="--percent: <?php echo get_sub_field('skills-professional__percent'); ?>"></div>
									<?php $percent = (get_sub_field('professional_skills_percentage') * 3.6) ?>
									<div class="skills-professional__progress" style=" background: conic-gradient(#00FFFF <?php echo $percent?>deg, #2C2C2C 0deg);" > 
										<div class="skills-professional__percent">
											<?php echo get_sub_field('professional_skills_percentage'); ?>%
										</div>
									</div>
									<span class="skills-professional__title fw-medium my-md-3 my-2 "><?php echo get_sub_field('professional_skills_title'); ?></span>
								</div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<section class='project'>
	<div class="container">
		<h1 class="project-title display-1 d-flex justify-content-center my-xl-9 my-lg-7 my-md-4 my-3"><?php echo get_field('project_title')?></h1>
		
		<div class="row justify-content-center">
			<?php
				$args = array(
					'posts_per_page' => 3,
					'post_type' => 'project',
				);	
			$query = new WP_Query( $args );
			?>
			<?php if( $query->have_posts() ) : ?>
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<div class="col-lg-4 col-md-6 g-2 g-lg-1 ">
						<?php get_template_part('template-parts/blocks/block', 'project')?>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_query(); ?>
		</div>
	</div>
</section>
<section class="contact">
	<div class="container">
		<div class="row mt-xl-12 mt-lg-9 mt-md-6 mt-3">
			<div class="col-lg-6">
				<h2 class="d-flex justify-content-center fw-bold"><?php echo get_field('contact_title') ?></h2>
				<div class="contact-content d-flex flex-column">
					<span class="display-5 fw-semibold my-2"><?php echo get_field('contact_subtitle') ?></span>
					<span class="display-6 fw-normal mb-lg-4 mb-md-3 mb-2"><?php echo get_field('contact_info') ?></span>
					<a class="contact-content__email text-decoration-none " href="<?php echo get_field('contact_email')['url'] ?>"><?php echo get_field('contact_email')['title'] ?></a>
					<a class="contact-content__phone text-decoration-none mb-lg-7 mb-md-5 mb-3 " href="<?php echo get_field('contact_phone')['url']; ?> "><?php echo get_field('contact_phone')['title']; ?></a>
					<?php echo growww_social(); ?>
				</div>
			</div>
			<div class="col-lg-6">
				<?php if (get_field('contact_form')): ?>
					<div class="mt-lg-3 mt-2">
						<?php gravity_form(get_field('contact_form'), false, false, false, null, true); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>
