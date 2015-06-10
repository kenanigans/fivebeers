<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>
<?php if ( is_active_sidebar( 'sidebar-4', 'sidebar-5' ) ) : ?>
<div id="footer-area">
<div class="footer-inside">
<?php
/* footer sidebar */
if ( ! is_404() ) : ?>
	<div id="footer-widgets" class="widget-area three">

		<div class="widget-left">
			<?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-4' ); ?>
			<?php endif; ?>
		</div>

		<div class="widget-right">
			<?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>
			<?php dynamic_sidebar( 'sidebar-5' ); ?>
			<?php endif; ?>
		</div>

	</div><!-- #footer-widgets -->
<?php endif; ?>
</div>
</div>
<?php endif; ?>
	</div><!-- .site-content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
			<?php
				/**
				 * Fires before the Twenty Fifteen footer text for footer customization.
				 *
				 * @since Twenty Fifteen 1.0
				 */
				do_action( 'twentyfifteen_credits' );
			?>
			<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentyfifteen' ) ); ?>"><?php printf( esc_html__( 'Proudly powered by %s', 'twentyfifteen' ), 'WordPress' ); ?></a>

		<div class="footer-copywrite"></div>
		</div><!-- .site-info -->
	</footer><!-- .site-footer -->
</div><!-- .site -->

<?php wp_footer(); ?>

</body>
</html>
