<?php get_header(); ?>

<main id="main" class="site-main" role="main">

	<?php
	if ( have_posts() ) {

		// Load posts loop.
		while ( have_posts() ) {
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<?php
					// Use an H1 for single posts, keep H2 with link for indexes/archives.
					if ( is_single() ) :
						printf( '<h1 class="entry-title">%s</h1>', esc_html( get_the_title() ) );
					else :
						the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
					endif;
					?>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php the_content(); ?>
				</div><!-- .entry-content -->

			</article><!-- #post-## -->
			<?php
		}
	} else {
		?>
		<p>No content to display.</p>
		<?php
	}
	?>

</main><!-- .site-main -->

<?php get_footer(); ?>
