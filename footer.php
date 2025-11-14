<?php
/**
 * The footer for our theme
 *
 * This template contains the closing of the #content div
 * and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package UpulGamageAuthority
 */
?>

</div><!-- #content .site-content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		
		<div class="site-social-links">
			<ul>
				<li><a href="https://www.linkedin.com/in/upulsgamage/" target="_blank" rel="noopener noreferrer">LinkedIn</a></li>
				<li><a href="https://github.com/upulsgamage/" target="_blank" rel="noopener noreferrer">GitHub</a></li>
			</ul>
		</div><!-- .site-social-links -->

		<div class="site-info">
			&copy; <?php echo esc_attr( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> | AI-Driven Architecture Case Study
		</div><!-- .site-info -->
	
	</footer><!-- #colophon -->

</div><!-- #page .site -->

<?php wp_footer(); ?>

</body>
</html>
