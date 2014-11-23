<?php
/**
 * The sidebar containing the unlimited widget areas.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

$sidebar_id = '';
$sidebar_options = get_option('unlimited_sidebars_settings');

// pick the assigned sidebar to use for the single page
if(is_singular()){
    global $post;
    $sidebar_id = get_post_meta( $post->ID, '_choose_sidebar', true );
}

// if nosidebar is selected use the default
if ($sidebar_id == '') {
    $sidebar_id = 'sidebar-1';
}

if ( has_nav_menu( 'primary' ) || has_nav_menu( 'social' ) || is_active_sidebar( 'sidebar-1' )  ) : ?>
	<div id="secondary" class="secondary">
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
		<nav id="site-navigation" class="main-navigation" role="navigation">
			<?php
				wp_nav_menu( array(
					'menu_class'     => 'nav-menu',
					'theme_location' => 'primary',
				) );
			?>
		</nav><!-- .main-navigation -->
		<?php endif; ?>

		<?php if ( has_nav_menu( 'social' ) ) : ?>
		<nav id="social-navigation" class="social-navigation" role="navigation">
			<?php
				wp_nav_menu( array(
					'theme_location' => 'social',
					'depth'          => 1,
					'link_before'    => '<span class="screen-reader-text">',
					'link_after'     => '</span>',
				) );
			?>
		</nav><!-- .social-navigation -->
		<?php endif; ?>

        	<?php if ( is_active_sidebar( $sidebar_id ) ) : ?>
		<div id="widget-area" class="widget-area" role="complementary">
            		<?php dynamic_sidebar( $sidebar_id ); ?>
		</div><!-- .widget-area -->
		<?php endif; ?>
	</div><!-- .secondary -->
<?php endif; ?>
