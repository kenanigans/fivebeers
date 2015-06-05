<?php
/**
 * Template Name: Live Search
 *
 * The template for displaying a list of live searches on the beta search page
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<div align="center"><h3>Live Search Proof of Concept (type lorem)</h3></div>

			<input id="live-search" type="text" autocomplete="off">
			
			<h4 id="live-search-text"><?php _e( 'Showing results for:', 'sorbet' ); ?> <span id="live-search-string"></span></h4>
				
			<ul id="live-search-results"></ul>

		</main><!-- #main -->
	</div><!-- #primary -->

