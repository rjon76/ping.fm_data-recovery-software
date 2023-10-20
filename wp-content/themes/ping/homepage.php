<?php
/*
	Template Name: Post AI Homepage
*/

	get_header();
?>
	
		<main class="homepage">
            <?php
                the_content();
            ?>
            <section class="homePosts">
                <div class="container">
                <?php
					$pages = get_pages( [
						'authors' => 2,
                        'exclude' => [9, 277, 571]
					] );
                ?>
                   <?php foreach( $pages as $post ) {
						$date = date_format(date_create($post->post_modified), 'M j, Y');
					?>
                    <div class="article">
                        <div class="innerart">
                            <img srcset="/ip/wp-content/uploads/wifi.png" src="/ip/wp-content/uploads/wifi.png" width="512" height="512" alt="WiFi icon">
                            <h2><a href="<?php echo esc_url( get_permalink($post->id) ); ?>"><?php echo $post->post_title ?></a></h2>
                        </div>
                   </div>
                    <?php }
                        wp_reset_postdata();
                    ?>
                </div>
            </section>
		</main>
<?php
	get_footer();