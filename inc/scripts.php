<?php

/**
 * Enqueue's styles and scripts used for the theme
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Enqueue all our styles and scripts used on various parts of the site
 *
 */
function load_fivebeers_scripts() {

	// 2015 parent style
	wp_enqueue_style('style', get_template_directory_uri() . '/style.css');

	// strip css and js
	wp_enqueue_style( 'strip_css', get_stylesheet_directory_uri() . '/css/strip.css');
	wp_enqueue_script('strip_js', get_stylesheet_directory_uri() . '/js/strip.js', array( 'jquery' ), '1.0', true);

	// ajax infinite scroll
	if ( 'infinite' == get_theme_mod( 'twentyfifteen_pagination' ) ) {
		wp_enqueue_script('jquery_ias', get_stylesheet_directory_uri() . '/js/ias.js', array('jquery'));
	}

	// our custom jQuery code for various site functions
	wp_enqueue_script('custom_js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'));

        $jqvars = array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'loading' => get_stylesheet_directory_uri() . '/images/loading.gif',
                'ajaxnonce' => wp_create_nonce( 'live-search' ),
                'custom_nonce' => wp_create_nonce( 'nonce-ajax-dropdown' )
        );

        wp_localize_script('custom_js', 'myajax', $jqvars);
}

add_action('wp_enqueue_scripts', 'load_fivebeers_scripts');

/**
 * Add scroll to top
 *
 */
function twentyfifteen_back_to_top() {
        echo '<a class="totop" href="#"></a>';
}

add_action( 'wp_footer', 'twentyfifteen_back_to_top' );
