<?php
/**
 * Tutor Child Theme functions and definitions
 */

/**
 * Enqueue styles & scripts
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'tutor-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('main-css'), TUTOR_STARTER_VERSION, 'all' );
	
	wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/script.js', array('jquery'), TUTOR_STARTER_VERSION, true);

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );