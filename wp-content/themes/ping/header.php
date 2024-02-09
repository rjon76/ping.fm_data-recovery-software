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
global $sitepress;
$current_language = 'en';
$curr_url = '';
$ping_url = '';

if( isset($sitepress) && is_object($sitepress) ) {
	$languages = $sitepress->get_ls_languages();

	foreach ( $languages as $hreflang_code => $hreflang_url ) {
		if($hreflang_url["active"] && $hreflang_url["language_code"] !== 'en') {
			$current_language = $hreflang_url["language_code"];
			$curr_url = $hreflang_url["language_code"] . '/';
			$ping_url = '/' . $hreflang_url["language_code"];
		}
	}
}

$fileName = get_post_meta(get_the_ID(), '_wp_page_template', true);

?><!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

	<head>

		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >

		<link rel="profile" href="https://gmpg.org/xfn/11">

		<?php wp_head(); ?>
		<meta name="author" content="Olga Weis">
		<link rel="stylesheet" id="stylecss-css" href="<?php echo get_site_url();?>/wp-content/themes/ping/css/header.css" media="all">
		<?php
			if($fileName === 'chromecast.php') {
		?>
			<link rel="stylesheet" id="youtubecss-css" href="<?php echo get_site_url();?>/wp-content/themes/ping/css/youtube-style.css" media="all">
		<?php } else { ?>
			<link rel="stylesheet" id="stylecss-css" href="<?php echo get_site_url();?>/wp-content/themes/ping/css/style.css" media="all">
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
			<?php if(get_site_url() === 'https://kismac-ng.org/blog') { ?>
				<a href="https://kismac-ng.org/" rel="follow" class="logoNonUp">
					<img src="https://kismac-ng.org/blog/wp-content/uploads/2024/02/cropped-wifi_9055433.png" alt="KisMAC Logo" width="512" height="512">
					KisMAC
				</a>
			<?php } else { ?>
				<a href="https://www.ping.fm/<?php echo $curr_url; ?>" rel="follow">
					<img src="/wp-content/uploads/question.png" alt="Ping Fm Logo" width="512" height="512">
					ping.fm
				</a>
			<?php } ?>
			<button class="menu-button <?php echo get_site_url() === 'https://kismac-ng.org/blog' ? 'hidden' : ''; ?>" aria-label="Mobile menu"></button>
			<div class="header-menu">
				<div class="language-switcher">
				<?php
					echo '<div class="wpml-floating-language-switcher">';
						do_action('wpml_add_language_selector');
					echo '</div>';
				?>		
				</div>
				<?php if ($current_language !== 'ar') { ?>
					<?php if(get_site_url() !== 'https://kismac-ng.org/blog') { ?>
					<ul>
						<li>
							<a href="<?php echo $ping_url; ?>/chromecast-screen-mirroring/" rel="dofollow"><?php _e('Chromecast Screen Mirroring', 'custom-string-translation'); ?></a>
						</li>
						<li>
							<a href="/howto/<?php echo $curr_url; ?>" rel="dofollow"><?php _e('How To & Best Software', 'custom-string-translation'); ?></a>
						</li>
						<li>
							<a href="/data-recovery-software/<?php echo $curr_url; ?>" rel="dofollow"><?php _e('Data Recovery Software', 'custom-string-translation'); ?></a>
						</li>
						<?php if( $current_language == 'en' ) { ?>
							<li>
								<a href="<?php echo $ping_url; ?>/ip/" rel="dofollow"><?php _e('Router Login & IP Address', 'custom-string-translation'); ?></a>
							</li>
							<li>
								<a href="/app-vs-app/" rel="dofollow"><?php _e('App Vs App', 'custom-string-translation'); ?></a>
							</li>
						<?php } ?>
					</ul>
					<?php } ?>
				<?php } ?>
			</div>
		</header>
		<script>
			const langSwitcherChildLength = document.querySelector('.language-switcher .wpml-ls-sub-menu').children.length;
			const switcher = document.querySelector('.language-switcher');

			if(langSwitcherChildLength === 0) {
				switcher.classList.add('hidden');
			}
		</script>