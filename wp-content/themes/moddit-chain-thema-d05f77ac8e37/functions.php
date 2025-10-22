<?php

if ( ! function_exists( 'chain_setup' ) ) :
	function chain_setup() {
		load_theme_textdomain( 'chain', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption'] );
		add_theme_support( 'customize-selective-refresh-widgets' );

		register_nav_menus( array(
			'hoofdmenu' => esc_html__( 'Hoofdmenu', 'chain' ),
			'footermenu' => esc_html__( 'Footer menu', 'chain' ),
			'privacymenu' => esc_html__( 'Privacy menu', 'chain' ),
		) );
		
	}
endif;
add_action( 'after_setup_theme', 'chain_setup' );

function chain_hoofdmenu() {
	wp_nav_menu(array(
        'container' => false,
		'menu' => 'hoofdmenu',
		'menu_class' => 'header-nav reset-list d-lg-flex py-lg-3 display-4 fw-medium',
		'theme_location' => 'hoofdmenu',
		'depth' => 2
	));
}

function chain_footermenu() {
	wp_nav_menu(array(
        'container' => false,
		'menu' => 'footermenu',
		'menu_class' => 'footer__menu',
		'theme_location' => 'footermenu',
		'depth' => 1
	));
}

function chain_privacymenu() {
	wp_nav_menu(array(
        'container' => false,
		'menu' => 'privacymenu',
		'menu_class' => 'footer__privacy',
		'theme_location' => 'privacymenu',
		'depth' => 1
	));
}

function chain_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'chain_content_width', 640 );
}
add_action( 'after_setup_theme', 'chain_content_width', 0 );

function chain_scripts() {
	wp_enqueue_style( 'blue-css', get_template_directory_uri() .  '/dist/css/app.css' );
	wp_enqueue_script( 'blue-manifest', get_template_directory_uri() . '/dist/js/manifest.js', array(), '', true );
	wp_enqueue_script( 'blue-vendor', get_template_directory_uri() . '/dist/js/vendor.js', array(), '', true );
	wp_enqueue_script( 'blue-app', get_template_directory_uri() . '/dist/js/app.js', array(), '', true );
	wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/assets/fontawesome/css/all.min.css' );
}
add_action( 'wp_enqueue_scripts', 'chain_scripts' );

function chain_remove_metabox() {
    if ( ! current_user_can( 'edit_others_posts' ) )
        remove_meta_box( 'wpseo_meta', 'post', 'normal' );
}
add_action( 'add_meta_boxes', 'chain_remove_metabox', 11 );
add_filter( 'use_block_editor_for_post', '__return_false' );

/**
 * Add Buttons
 *
 * @return void
 */
function growww_add_btns($btns = [], $parent_class = '') 
{
	if($parent_class) $parent_class = ' ' . $parent_class;
	if($btns):
		echo '<div class="btns'.$parent_class.'">';
		foreach($btns as $btn):
			get_template_part( 'template-parts/components/component', 'button', $btn );
		endforeach;
		echo '</div>';
	endif;
}

// Gravity forms Bootstrap
add_action('wp', function (){
	add_filter( 'gform_field_container', 'chain_change_gform_class_to_bootstrap', 10, 6 );
	add_filter( 'gform_get_form_filter', 'chain_change_ul_to_div', 10, 2 );
	add_filter( 'gform_field_content', 'chain_change_field_content', 10, 5 );
	add_filter( 'gform_submit_button', 'chain_change_submit_button', 10, 2 );
	add_filter( 'gform_previous_button', 'chain_change_prev_btn', 10, 2 );
	add_filter( 'gform_next_button', 'chain_change_next_btn', 10, 2 );
	add_filter('gform_disable_css', '__return_true');
});

function chain_change_gform_class_to_bootstrap( $field_container, $field, $form, $css_class, $style, $field_content ) {
	$class = 'col-lg-12 form-group';
	if($field->type == 'date' || $field->type == 'fileupload'){
		switch($field->size) {
			case "medium":
				$class = 'col-lg-12 form-group';
				break;
			default:
				$class = 'col-lg-6 form-group';
				break;
		} 
	} elseif($field->type != 'checkbox' && $field->type != 'radio') {
		switch($field->size) {
			case "small":
				$class = 'col-lg-4 form-group';
				break;
			case "medium":
				$class = 'col-lg-6 form-group';
				break;
			case "large":
				$class = 'col-lg-12 form-group';
				break;
		}
	}
	return "<div id='field_".$field->formId."_".$field->id."' class='$class $css_class'>{FIELD_CONTENT}</div>";
}
function chain_change_ul_to_div( $form_string, $form ) {
	$str = str_replace('<ul', '<div', $form_string);
	$str = str_replace('</ul', '</div', $str);
	$str = str_replace('gform_fields', 'row g-1 gform_fields', $str);
	$str = str_replace('gfield_label', 'gfield_label', $str);
	$str = str_replace('gfield_description validation_message', 'gfield_description validation_message text-danger font-weight-bold', $str);
	return $str;
}
function chain_change_field_content( $content, $field, $value, $lead_id, $form_id ) {
	if($field->type == 'text' || $field->type == 'number' || $field->type == 'phone' || $field->type == 'email' || $field->type == 'date') {
		$content = str_replace('<input', '<input class="form-control"', $content);
	}
	if($field->type == 'textarea') {
		$content = str_replace('<textarea', '<textarea class="form-control"', $content);
	}
	if($field->type == 'select' || $field->type == 'multiselect') {
		$content = str_replace('<select', '<select class="form-control"', $content);
	}
	if($field->type == 'checkbox') {
		$content = str_replace('gchoice_', 'custom-control custom-checkbox gchoice_', $content);
		$content = str_replace('<input', '<input class="custom-control-input"', $content);
		$content = str_replace('<label for', '<label class="custom-control-label" for', $content);
	}
	if($field->type == 'radio') {
		$content = str_replace('gchoice_', 'custom-control custom-radio gchoice_', $content);
		$content = str_replace('<input', '<input class="custom-control-input"', $content);
		$content = str_replace('<label for', '<label class="custom-control-label" for', $content);
	}
	$content = str_replace('gfield_required', 'gfield_required text-danger', $content);

    return $content;
}
function chain_change_submit_button( $button, $form ) {
	$text = $form['button']['text'] ?: __('Versturen', 'chain');
    return "<button class='gform_button btn btn-primary mt-2' id='gform_submit_button_{$form['id']}'>{$text}<i class='fa-solid fa-chevron-right ms-1'></i></button>";
}
function chain_change_prev_btn( $button, $form ) {
	return "<button class='button gform_previous_button btn btn-outline-secondary' id='gform_previous_button_{$form['id']}'><i class='fa-solid fa-chevron-left me-1'></i><span>" . __('Vorige', 'chain') . "</span></button>";
}
function chain_change_next_btn( $button, $form ) {
	return "<button class='button gform_next_button btn btn-secondary' id='gform_next_button_{$form['id']}'><span>" . __('Volgende', 'chain') . "</span><i class='fa-solid fa-chevron-right ms-1'></i></button>";
}

// Breadcrumbs
function growww_breadcrumb() {
	if ( !function_exists( 'yoast_breadcrumb' ) ) return '';
    return yoast_breadcrumb();
}

/**
 * Remove the admin login header.
 *
 * @return void
 */
function remove_admin_login_header(): void
{
	remove_action('wp_head', '_admin_bar_bump_cb');
}
add_action('get_header', 'remove_admin_login_header');


function enqueue_dashicons_frontend() {
  wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'enqueue_dashicons_frontend');
