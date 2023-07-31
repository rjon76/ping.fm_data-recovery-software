<?php
/*
	Template Name: Post AI Page
*/

 	get_header();
?>
		<main>
			<div class="container">
				<div class="info">
					<div class="author">
						<div class="img">
							<img src="/wp-content/uploads/2023/07/roman-krop.jpeg" alt="Roman Kropachek Photo" title="Roman Kropachek Photo" width="400" height="400">
						</div>
						<div class="auhordata">
							<div class="authorpost">Tutorial written by:</div>
							<div class="title">
								<a href="https://www.linkedin.com/in/kropachek/" rel="nofollow" target="_blank">Roman Kropachek</a> & <a href="https://openai.com/" rel="nofollow" target="_blank">Chat GPT</a>
							</div>
						</div>
					</div>
					<p class="updated">Last update on <time datetime="<?php echo the_modified_date('Y-n-j'); ?>"><?php echo the_modified_date('F d, Y'); ?></time></p>
				</div>
		<?php
			the_content();
		?>
				<aside>
					<h2>More Similar & Alternative Apps / Software</h2>
					<ul>
						<?php
							$pages = get_pages( [
								'authors' => 4,
								'exclude' => get_the_ID()
							] );
							shuffle($pages);
							$output = array_slice($pages, 0, 6);
							foreach( $output as $post ) { 
								$date = date_format(date_create($post->post_modified), 'M j, Y');
							?>
								<li>
									<article>
										<div><?php echo $date ?></div>
										<a href="<?php echo esc_url( get_permalink($post->id) ); ?>" rel="follow"><?php echo $post->post_title ?></a>
									</article>
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
