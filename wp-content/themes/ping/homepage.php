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
                        'exclude' => get_the_ID()
					] );
                ?>
                   <?php foreach( $pages as $post ) {
						$date = date_format(date_create($post->post_modified), 'M j, Y');
					?>
                    <div class="article">
                        <div class="innerart">
                            <img srcset="<?php echo get_site_url();?>/wp-content/uploads/note.png" src="<?php echo get_site_url();?>/wp-content/uploads/note.png" width="512" height="512" alt="Note icon">
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