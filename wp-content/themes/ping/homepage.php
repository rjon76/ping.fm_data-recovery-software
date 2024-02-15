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
                        'exclude' => [
                            get_the_ID(), 2371, 2373, 2340, 2503, 2499, 2494, 2489, 2484, 2480, 2474,
                            2721, 2719, 2717, 2715, 2713, 2711, 2709, 2805, 2799
                        ]
					] );
                ?>
                   <?php foreach( $pages as $post ) {
						$date = date_format(date_create($post->post_modified), 'M j, Y');
					?>
                    <div class="article">
                        <div class="innerart">
                            <?php if(get_site_url() === 'https://www.ping.fm/howto') { ?>
                                <img srcset="https://www.ping.fm/howto/wp-content/uploads/2024/01/multimedia_9842714.png" src="https://www.ping.fm/howto/wp-content/uploads/2024/01/multimedia_9842714.png" width="512" height="512" alt="Howto icon">
                            <?php } ?>
                            <?php if(get_site_url() === 'https://www.ping.fm/data-recovery-software') { ?>
                                <img srcset="<?php echo get_site_url();?>/wp-content/uploads/note.png" src="<?php echo get_site_url();?>/wp-content/uploads/note.png" width="512" height="512" alt="Note icon">
                            <?php } ?>
                            <?php if(get_site_url() === 'https://kismac-ng.org/blog' || get_site_url() === 'https://www.kismac-ng.org/blog') { ?>
                                <img srcset="https://kismac-ng.org/blog/wp-content/uploads/2024/02/cropped-wifi_9055433.png" src="https://kismac-ng.org/blog/wp-content/uploads/2024/02/cropped-wifi_9055433.png" width="512" height="512" alt="Wifi icon">
                            <?php } ?>
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