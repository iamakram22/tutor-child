<?php
/**
 * Tutor Child Theme functions and definitions
 */

/**
 * Enqueue styles & scripts
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'tutor-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('main-css'), '1.0.0', 'all' );
	
	wp_enqueue_script( 'custom-script', get_stylesheet_directory_uri() . '/script.js', array('jquery'), '1.0.0', true);

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );