<?php

register_sidebar( array(
	'name' => __( 'Footer Widget One', 'twentyfifteen' ),
	'id' => 'sidebar-4',
	'description' => __( 'Found at the bottom of every page (except 404s and optional homepage template) Left Footer Widget.', 'twentyfifteen' ),
	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
	'after_widget' => '</aside>',
	'before_title' => '<h3 class="widget-title">',
	'after_title' => '</h3>',
) );

register_sidebar( array(
	'name' => __( 'Footer Widget Two', 'twentyfifteen' ),
	'id' => 'sidebar-5',
	'description' => __( 'Found at the bottom of every page (except 404s and optional homepage template) Right Footer Widget.', 'twentyfifteen' ),
	'before_widget' => '<aside id="%1$s" class="widget %2$s">',
	'after_widget' => "</aside>",
	'before_title' => '<h3 class="widget-title">',
	'after_title' => '</h3>',
) );
