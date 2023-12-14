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
							<img src="<?php echo get_site_url();?>/wp-content/uploads/roman-krop.jpeg" alt="Roman Kropachek Photo" title="Roman Kropachek Photo" width="400" height="400">
						</div>
						<div class="auhordata">
							<div class="authorpost">Written by:</div>
							<div class="title">
								<a href="https://www.linkedin.com/in/kropachek/" rel="nofollow" target="_blank">Roman Kropachek</a>
							</div>
						</div>
					</div>
					<p class="updated"><?php if ( get_the_modified_time() != get_the_time()) pll_e('Last update on '); ?> <time datetime="<?php echo the_modified_time( 'c' ); ?>"><?php echo ' '. the_modified_date('F d, Y'); ?></time></p>
				</div>
				<article>
					<?php
						the_content();
					?>
					<?php if ( get_field('faq' ) ): ?>
						<?php echo get_field('faq'); ?>
					<?php endif; ?>
				</article>
				<aside>
					<div class="h2">
						<?php pll_e('More Articles'); ?>
					</div>
					<ul>
						<?php
							$pages = get_pages( [
								'exclude' => [get_the_ID(), 9, 2074, 2076]
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
											<img srcset="<?php echo get_site_url();?>/wp-content/uploads/note.png" src="<?php echo get_site_url();?>/wp-content/uploads/note.png" width="512" height="512" alt="Note icon">
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