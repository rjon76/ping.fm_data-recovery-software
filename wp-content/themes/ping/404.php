<?php
/**
 * The template for displaying the 404 template in the Twenty Twenty theme.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

header("HTTP/1.0 410 Gone");
get_header();
?>

<main id="site-content" role="main" style="flex: 1 0">
	<div class="container">
		<div class="section-inner thin error404-content">
			<h1 class="entry-title"><?php _e( 'Page Not Found', 'twentytwenty' ); ?></h1>
			<div class="intro-text"><p><?php _e( 'The page you were looking for could not be found. <br>Return ', 'twentytwenty' ); ?><a href="/">home</a></p></div>
		</div><!-- .section-inner -->
	</div>
</main><!-- #site-content -->

<?php
get_footer();
