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
							<img src="/wp-content/uploads/2023/07/roman-krop.jpeg" alt="Roman Kropachek Photo" title="Roman Kropachek Photo" width="400" height="400">
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
				<section class="how-test">
					<h2>How do we test?</h2>
					<p>🧑‍💻 Here at <a href="/" rel="follow">Ping.FM</a> (Ping.FM), our editorial team prides itself on delivering concise and precise information based on video tutorials for all your style needs. Why spend valuable time sitting through a 10-20 minute video when you can get the key points in a quick, easily digestible text format? This way, you've got flexibility at your fingertips: you can either watch the full video or simply scan the text for quick tips. Our goal is to make your style journey as efficient and enjoyable as possible.</p>
				</section>
				<aside>
					<div class="h2">More How To Tutorials</div>
					<ul>
						<?php
							$pages = get_pages( [
								'authors' => 5,
								'exclude' => get_the_ID()
							] );
							shuffle($pages);
							$output = array_slice($pages, 0, 12);
							foreach( $output as $post ) {
								$date = date_format(date_create($post->post_modified), 'M j, Y');
							?>
								<li>
									<div class="article">
										<div><?php echo $date ?></div>
										<div class="innerart">
											<img srcset="/wp-content/uploads/webinar.png" src="/wp-content/uploads/webinar.png" width="512" height="512" alt="Webinar icon">
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