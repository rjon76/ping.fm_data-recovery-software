<?php
/*
	Template Name: ChromeCast Page
*/
	get_header();

	$aUrl = explode("/", $_SERVER['REQUEST_URI']);
	$articleUrl = $aUrl[count($aUrl) - 2];

	$current_language = get_locale();

    if($current_language == 'de_DE') {
        $curr_url = 'de/';
    } elseif ($current_language == 'es_ES') {
        $curr_url = 'es/';
    } elseif ($current_language == 'fr_FR') {
        $curr_url = 'fr/';
    } elseif ($current_language == 'it_IT') {
        $curr_url = 'it/';
    } elseif ($current_language == 'ja') {
        $curr_url = 'ja/';
    } elseif ($current_language == 'pt_PT' || $current_language == 'pt-PT' || $current_language == 'pt') {
        $curr_url = 'pt/';
    } elseif ($current_language == 'nl_NL') {
        $curr_url = 'nl/';	
    } elseif ($current_language == 'ar') {
        $curr_url = 'ar/';
    } elseif ($current_language == 'zh-CN' || $current_language == 'zh_CN' || $current_language == 'zh') {
        $curr_url = 'zh/';
    } elseif ($current_language == 'sv-SE' || $current_language == 'sv_SE') {
        $curr_url = 'sv/';
    } else {
        $curr_url = '';
    }
?>
		<main id="mainTag" data-href="<?php echo get_site_url();?>" data-uri="<?php echo $articleUrl; ?>" data-lang="<?php echo $curr_url; ?>">
			<div class="container">
				<div class="breadcrumb">
					<?php
						if(function_exists('bcn_display'))
						{
							bcn_display();
						}
					?>
				</div>
				<div class="info">
					<div class="author">
						<div class="img">
							<img src="<?php echo get_site_url();?>/wp-content/uploads/roman-krop.jpeg" alt="Roman Kropachek Photo" title="Roman Kropachek Photo" width="400" height="400">
						</div>
						<div class="auhordata">
							<div class="authorpost"><?php _e('Written by:', 'custom-string-translation'); ?></div>
							<div class="title">
								<a href="https://www.linkedin.com/in/kropachek/" rel="nofollow" target="_blank">Roman Kropachek</a>
							</div>
						</div>
					</div>
					<p class="updated"><?php if ( get_the_modified_time() != get_the_time()) _e('Last update on ', 'custom-string-translation'); ?> <time datetime="<?php echo the_modified_time( 'c' ); ?>"><?php echo ' '. the_modified_date('F d, Y'); ?></time></p>
				</div>
				<article>
					<?php
						the_content();
					?>
					<?php if ( get_field('faq' ) ): ?>
						<?php echo get_field('faq'); ?>
					<?php endif; ?>
				</article>
				<?php
					$pages = get_pages( [
						'exclude' => [
							get_the_ID(), 9, 2074, 2076, 2103, 2371, 2373, 2340, 2503, 2499, 2494,
							2489, 2484, 2480, 2474, 2721, 2719, 2717, 2715, 2713, 2711, 2709, 2805,
							2799, 15890
						]
					] );

					if(count($pages) > 0) {
				?>

				<aside>
					<div class="h2">
						<?php _e('More Articles', 'custom-string-translation'); ?>
					</div>
					<ul>
						<?php
							shuffle($pages);
							$output = array_slice($pages, 0, 14);
							foreach( $output as $post ) {
								$date = date_format(date_create($post->post_modified), 'M j, Y');
							?>
								<li>
									<div class="article">
										<div><?php echo $date ?></div>
										<div class="innerart">
											<?php if(get_site_url() === 'https://www.ping.fm/howto') { ?>
												<img srcset="https://www.ping.fm/howto/wp-content/uploads/2024/01/multimedia_9842714.png" src="https://www.ping.fm/howto/wp-content/uploads/2024/01/multimedia_9842714.png" width="512" height="512" alt="Howto icon">
											<?php } ?>
											<?php if(get_site_url() === 'https://www.ping.fm/data-recovery-software') { ?>
												<img srcset="<?php echo get_site_url();?>/wp-content/uploads/note.png" src="<?php echo get_site_url();?>/wp-content/uploads/note.png" width="512" height="512" alt="Note icon">
											<?php } ?>
											<?php if(get_site_url() === 'https://kismac-ng.org/blog') { ?>
												<img srcset="https://kismac-ng.org/blog/wp-content/uploads/2024/02/cropped-wifi_9055433.png" src="https://kismac-ng.org/blog/wp-content/uploads/2024/02/cropped-wifi_9055433.png" width="512" height="512" alt="Wifi icon">
											<?php } ?>
											<a href="<?php echo esc_url( get_permalink($post->id) ); ?>" rel="follow"><?php echo $post->post_title ?></a>
										</div>
									</div>
								</li>
							<?php }
							wp_reset_postdata();
						?>
					</ul>
				</aside>
				<?php } ?>
			</div>
		</main>
		<script>
			jQuery('.breadcrumb a').attr('href', jQuery('#mainTag').attr('data-href') + '/' + jQuery('#mainTag').attr('data-lang'));
		</script>
<?php
	get_footer();