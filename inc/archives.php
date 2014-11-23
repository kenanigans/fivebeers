<?php

/**
 * Dropdown Ajax Archives
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */


/**
 * Ajax function for loading posts by date and category
 */
function wp_ajax_load_posts(){

	if(!wp_verify_nonce( $_GET['_wpnonce'], 'nonce-ajax-dropdown'))
		die( 'Go away!' );

	$year = isset($_GET['year']) ? $_GET['year'] : '';
	$month = isset($_GET['month']) ? $_GET['month'] : '';

	$args = array(
		'year' => trim($year),
		'monthnum' => trim($month),
		'posts_per_page' => -1,
		'orderby' => 'date',
		'cat' => trim($_GET['cat'] != "-1") ? trim($_GET['cat']) : 0,
	);

	$ajaxsort = new WP_Query($args);
?>
	    <?php if ($ajaxsort->have_posts()) : ?>
	</div>
		<?php while ($ajaxsort->have_posts()) : $ajaxsort->the_post();?>
			<?php get_template_part('content','archives');?>
	    <?php endwhile;?>

		<?php else:
	        	echo '<div align="center">Nothing Found!</div></nav>';
	        endif;
	    ?>
<?php
    	exit;
}

add_action('wp_ajax_load_posts', 'wp_ajax_load_posts');
add_action('wp_ajax_nopriv_load_posts', 'wp_ajax_load_posts');

