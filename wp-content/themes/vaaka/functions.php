<?php
/**
 * @package    Vaaka
 * @author     Sami Keijonen <sami.keijonen@foxnet.fi>
 * @copyright  Copyright (c) 2014, Sami Keijonen
 * @link       http://themehybrid.com/themes/vaaka
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add the child theme setup function to the 'after_setup_theme' hook. */
add_action( 'after_setup_theme', 'vaaka_theme_setup' );

/**
 * Setup function. All child themes should run their setup within this function. The idea is to add/remove 
 * filters and actions after the parent theme has been set up. This function provides you that opportunity.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function vaaka_theme_setup() {

	/*
	 * Add a custom background to overwrite the defaults.
	 *
	 * @link http://codex.wordpress.org/Custom_Backgrounds
	 */
	add_theme_support(
		'custom-background',
		array(
			'default-color' => '343a40',
			'default-image' => ''
		)
	);

	/*
	 * Add a custom header to overwrite the defaults.
	 *
	 * @link http://codex.wordpress.org/Custom_Headers
	 */
	add_theme_support( 
		'custom-header', 
		array(
			'default-text-color' => 'dadada',
			'default-image'      => ''
		)
	);

	/*
	 * Add a custom default color for the "menu" color option.
	 */
	add_filter( 'theme_mod_color_menu', 'vaaka_color_menu' );

	/*
	 * Add a custom default color for the "primary" color option.
	 */
	add_filter( 'theme_mod_color_primary', 'vaaka_color_primary' );
	
	/* 
	 * Add child theme fonts to editor styles.
	 */
	add_editor_style( vaaka_fonts_url() );
	
}

/**
 * Add a default custom color for the theme's "menu" color option.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $hex
 * @return string
 */
function vaaka_color_menu( $hex ) {
	return $hex ? $hex : '2c3238';
}

/**
 * Add a default custom color for the theme's "primary" color option.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $hex
 * @return string
 */
function vaaka_color_primary( $hex ) {
	return $hex ? $hex : 'd84338';
}

/**
 * Enqueue scripts and styles.
 *
 * @since  1.0.0
 * @return void
 */
function vaaka_scripts() {
	
	/* Dequeue parent theme fonts. */
	wp_dequeue_style( 'saga-fonts' );
	
	/* Enqueue child themes fonts. */
	wp_enqueue_style( 'vaaka-fonts', vaaka_fonts_url(), array(), null );
	
}
add_action( 'wp_enqueue_scripts', 'vaaka_scripts', 11 );

/**
 * Enqueue theme fonts in admin header page.
 *
 * @since  1.0.0
 * @return void
 */
function vaaka_custom_header_fonts() {
	wp_enqueue_style( 'vaaka-fonts', vaaka_fonts_url(), array(), null );
}
add_action( 'admin_print_styles-appearance_page_custom-header', 'vaaka_custom_header_fonts' );

/**
 * Return the Google font stylesheet URL.
 *
 * @since  1.0.0
 * @return string
 */
function vaaka_fonts_url() {

	$fonts_url = '';

	/* Translators: If there are characters in your language that are not
	 * supported by Source Sans Pro, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$source_sans_pro = _x( 'on', 'Source Sans Pro font: on or off', 'vaaka' );

	/* Translators: If there are characters in your language that are not
	 * supported by Roboto Condensed, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$roboto_condensed = _x( 'on', 'Roboto Condensed font: on or off', 'vaaka' );

	if ( 'off' !== $source_sans_pro || 'off' !== $roboto_condensed ) {
		$font_families = array();

		if ( 'off' !== $source_sans_pro )
			$font_families[] = 'Source Sans Pro:400,600,700,400italic,600italic,700italic';

		if ( 'off' !== $roboto_condensed )
			$font_families[] = 'Roboto Condensed:300,400,700,300italic,400italic,700italic';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);
		$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
	}

	return $fonts_url;
}