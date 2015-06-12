<?php

/**
 * Layout Option
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Add Customizer Option for Left or Right Sidebae
 *
*/
function twentyfifteen_sidebar_theme_customizer( $wp_customize ) {

    	$wp_customize->add_section( 'twentyfifteen_layout_section' , array(
            	'title'       => __( 'Layout Options', 'twentyfifteen' ),
            	'priority'    => 30,
            	'description' => 'Change the layout of your site.',
        ) );

	$wp_customize->add_setting( 'twentyfifteen_pagination', array(
		'default'	        => 'infinite',
	) );

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'twentyfifteen_pagination', array(
		'label'    => __( 'Post Pagination', 'twentyfifteen' ),
		'section'  => 'twentyfifteen_layout_section',
		'settings' => 'twentyfifteen_pagination',
		'type'     => 'radio',
		'choices'  => array(
			'infinite' => 'Infinite Scrolling',
			'default' => 'Default Pagination',
			),
	) ) );

	$wp_customize->add_setting(
		'twentyfifteen_layout_settings[layout_setting]',
		array(
			'default' => 'right-sidebar',
			'type' => 'option'
		)
	);

	$wp_customize->add_control(
		'layout_control',
		array(
			'type' => 'radio',
			'label' => __( 'Sidebar layout', 'twentyfifteen' ),
			'section' => 'twentyfifteen_layout_section',
			'choices' => array(
				'left-sidebar' => __( 'Left sidebar', 'twentyfifteen' ),
				'right-sidebar' => __( 'Right sidebar', 'twentyfifteen' ),
			),
			'settings' => 'twentyfifteen_layout_settings[layout_setting]'
		)
	);
}

add_action('customize_register', 'twentyfifteen_sidebar_theme_customizer');

/**
 * Add Proper class for right or left sidebar
 *
*/
function twentyfifteen_sidebar_body_classes( $classes ) {
	$sidebar_settings = get_option( 'twentyfifteen_layout_settings' );
	$classes[] = $sidebar_settings['layout_setting'];
	return $classes;
}

add_filter( 'body_class', 'twentyfifteen_sidebar_body_classes' );
