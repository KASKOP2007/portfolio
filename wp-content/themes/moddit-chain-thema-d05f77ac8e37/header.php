<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<?php wp_head(); ?>
		<?php growww_favicon() ?>
		<?php growww_custom_header_code(); ?>
        <?php growww_google_code(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php get_template_part( 'template-parts/layouts/layout', 'header' ); ?>