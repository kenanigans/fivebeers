<?php

/**
 * Beta Live Search (uses template-livesearch.php)
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Ajax Function to return live search results
 *
*/
function live_search_ajax() {

	check_ajax_referer( 'live-search', 'security' );

	global $wpdb;

	// Get Search
	$q = esc_html( $_POST['live_search_query'] );

	$search_query_args = array(
		's' => $q,
		'post_type' => array('post'),
		'posts_per_page' => -1
	);

	// Live Search Query
	$search_query = new WP_Query( $search_query_args );

	if ( $search_query->have_posts() ) {
		$search_html = '';
		while ( $search_query->have_posts() ) {
			$search_query->the_post();
			$search_html .= '<li class="result"><a href="'. get_permalink() .'">'. get_the_title() .'</a></li>';
		}
		echo json_encode($search_html);
	}

	wp_reset_postdata();
	exit;
}

add_action( 'wp_ajax_live_search', 'live_search_ajax' );
add_action( 'wp_ajax_nopriv_live_search', 'live_search_ajax' );

/**
 * Don't load header and sidebar if sitewide livesearch is being executed
 *
*/
function ajax_get_header() {
        if(!isset($_GET['action'])) {
                get_header();
        }
}

/**
 * Don't load footer if sitewide livesearch is being executed
 *
*/
function ajax_get_footer() {
        if(!isset($_GET['action'])) {
                get_footer();
        }
}
