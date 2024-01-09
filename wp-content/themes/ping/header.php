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

$current_language = get_locale();

$aUrl = explode("/", $_SERVER['REQUEST_URI']);
$articleUrl = $aUrl[count($aUrl) - 2];

$path = __DIR__ . '/../../uploads/wpallimport/files/generated-post-German.xml';
$xmlstring = file_get_contents($path);
$xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$aArticles = json_decode($json, TRUE);

if($fileName === 'chromecast.php') {

	$isPageHasTranslate = false;

	if(count($aArticles) > 0) {
		if(!empty($aArticles["page"]) && !empty($aArticles["page"]["post_url"])) {
			$post_url = str_replace("/", "", $aArticles["page"]["post_url"]);
			if($post_url === $articleUrl) {
				$isPageHasTranslate = true;
			}
		} else {
			if(!empty($aArticles["page"]) && count($aArticles["page"]) > 1 && !empty($aArticles["page"][0]["post_url"]) && !empty($aArticles["page"][1]["post_url"])) {
				for($i = 0; $i < count($aArticles["page"]); $i++ ) {
					if(empty($aArticles["page"][$i]["post_url"])) { continue; }

					if(str_replace("/", "", $aArticles["page"][$i]["post_url"]) === $articleUrl) {
						$isPageHasTranslate = true;
						break;
					}
				}
			}
		}
	}
} else {
	$isPageHasTranslate = true;
}

if($current_language == 'de_DE') {
	$curr_url = 'de/';
	$ping_url = '/de';
} elseif ($current_language == 'es_ES') {
	$curr_url = 'es/';
	$ping_url = '/es';
} elseif ($current_language == 'fr_FR') {
	$curr_url = 'fr/';
	$ping_url = '/fr';
}  elseif ($current_language == 'it_IT') {
	$curr_url = 'it/';
	$ping_url = '/it';
} elseif ($current_language == 'ja') {
	$curr_url = 'ja/';
	$ping_url = '/ja';
} elseif ($current_language == 'pt_PT' || $current_language == 'pt-PT' || $current_language == 'pt') {
	$curr_url = 'pt/';
	$ping_url = '/pt';
} elseif ($current_language == 'nl_NL') {
	$curr_url = 'nl/';
	$ping_url = '/nl';
} elseif ($current_language == 'ar') {
	$curr_url = '';
	$ping_url = '/ar';
} elseif ($current_language == 'zh-CN' || $current_language == 'zh_CN' || $current_language == 'zh') {
	$curr_url = 'zh/';
	$ping_url = '/zh';
} elseif ($current_language == 'sv-SE' || $current_language == 'sv_SE') {
	$curr_url = 'sv/';
	$ping_url = '/sv';
} else {
	$curr_url = '';
	$ping_url = '';
}

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
		<script>
			if(jQuery('.language-switcher .wpml-ls-sub-menu').children().length === 0) {
				jQuery('.language-switcher').addClass('hidden');
			}
		</script>
	</head>

	<body <?php body_class(); ?>>
		<header>
			<a href="https://www.ping.fm/<?php echo $curr_url; ?>" rel="follow">
				<img src="/wp-content/uploads/question.png" alt="Ping Fm Logo" width="512" height="512">
				ping.fm
			</a>
			<button class="menu-button" aria-label="Mobile menu"></button>
			<div class="header-menu">
				<div class="language-switcher">
				<?php
					echo '<div class="wpml-floating-language-switcher">';
						do_action('wpml_add_language_selector');
					echo '</div>';
				?>		
				</div>
				<?php if ($current_language !== 'ar') { ?>
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
						<?php if( $current_language == 'en_EN' || $current_language == 'en' || $current_language == 'en_US' ) { ?>
							<li>
								<a href="<?php echo $ping_url; ?>/ip/" rel="dofollow"><?php _e('Router Login & IP Address', 'custom-string-translation'); ?></a>
							</li>
							<li>
								<a href="/app-vs-app/" rel="dofollow"><?php _e('App Vs App', 'custom-string-translation'); ?></a>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</div>
		</header>