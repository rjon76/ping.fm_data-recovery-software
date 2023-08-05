<?php
/**
 * Header file for the Twenty Twenty WordPress default theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */
$currenturl = preg_replace('#(/[a-z]{2})?(/.*)#', '${2}', $_SERVER['REQUEST_URI']);
$fileName = get_post_meta(get_the_ID(), '_wp_page_template', true);

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >

		<link rel="profile" href="https://gmpg.org/xfn/11">

		<?php wp_head(); ?>
		<meta name="author" content="Olga Weis">
		<link rel="stylesheet" id="stylecss-css" href="/ip/wp-content/themes/ping/css/header.css" media="all">
		<?php
			if($fileName === 'chromecast.php') {
		?>
			<link rel="stylesheet" id="youtubecss-css" href="/ip/wp-content/themes/ping/css/youtube-style.css" media="all">
		<?php } else { ?>
			<link rel="stylesheet" id="stylecss-css" href="/ip/wp-content/themes/ping/css/style.css" media="all">
		<?php } ?>
		<!-- Google tag (gtag.js) -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-H1RBTQZ91E"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', 'G-H1RBTQZ91E');
		</script>

	</head>

	<body <?php body_class(); ?>>
		<header>
			<a href="/" rel="follow">
				<img src="/wp-content/uploads/question.png" alt="Ping Fm Logo" width="512" height="512">
				ping.fm
			</a>
			<button class="menu-button" aria-label="Mobile menu"></button>
			<div class="header-menu">
				<ul>
					<li><a href="/chromecast-screen-mirroring/" rel="dofollow">Chromecast Screen Mirroring</a></li>
					<li><a href="/ip/" rel="dofollow">Router Login & IP address</a></li>
					<li><a href="/social-media-tutorials/" rel="dofollow">Social Media Tutorials</a></li>
				</ul>
			</div>
		</header>