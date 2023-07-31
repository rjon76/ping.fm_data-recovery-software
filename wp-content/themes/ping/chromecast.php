<?php
/*
	Template Name: ChromeCast Page
*/
	get_header();
?>
		<main>
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
							<img src="/ip/wp-content/uploads/roman-krop.jpeg" alt="Roman Kropachek Photo" title="Roman Kropachek Photo" width="400" height="400">
						</div>
						<div class="auhordata">
							<div class="authorpost">Tutorial written from original video by:</div>
							<div class="title">
								<a href="https://www.linkedin.com/in/kropachek/" rel="nofollow" target="_blank">Roman Kropachek</a> & <a href="https://openai.com/" rel="nofollow" target="_blank">Chat GPT</a>
							</div>
						</div>
					</div>
					<p class="updated"><?php if ( get_the_modified_time() != get_the_time()) echo 'Last update on '; ?><time datetime="<?php echo the_modified_time( 'c' ); ?>"><?php echo the_modified_date('F d, Y'); ?></time></p>
				</div>
				<?php
					the_content();
				?>
				<aside>
					<div class="h2">More IP / WiFi /  Router Tutorials</div>
					<ul>
						<?php
							$pages = get_pages( [
								'authors' => 2,
								'exclude' => get_the_ID()
							] );
							shuffle($pages);
							$output = array_slice($pages, 0, 14);
							foreach( $output as $post ) {
								$date = date_format(date_create($post->post_modified), 'M j, Y');
							?>
								<li>
									<div class="article">
										<div><?php echo $date ?></div>
										<div class="innerart">
											<img srcset="/ip/wp-content/uploads/wifi.png" src="/ip/wp-content/uploads/wifi.png" width="512" height="512" alt="WiFi icon">
											<a href="<?php echo esc_url( get_permalink($post->id) ); ?>" rel="follow"><?php echo $post->post_title ?></a>
										</div>
									</div>
								</li>
							<?php }
							wp_reset_postdata();
						?>
					</ul>
				</aside>
			</div>
		</main>
<?php
	get_footer();