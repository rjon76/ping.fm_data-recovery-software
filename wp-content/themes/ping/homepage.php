<?php
/*
	Template Name: Post AI Homepage
*/

	get_header();
?>
	
		<main class="homepage">
            <section class="banner">
                <div class="banner--img">
                    <div class="banner--inner">
                        <h1>A Comprehensive Guide to Router Login and IP Addresses</h1>
                        <p>Dive into our comprehensive guide to understanding router login processes, IP addresses like 192.168.1.1, 10.0.0.1, and more.</p>
                        <p>Learn how to access and manage your router's settings, check your private IP, and optimize your network using our easy step-by-step guide. Perfect for beginners and advanced users alike.</p>
                    </div>
                </div>
            </section>
            <section class="homePosts">
                <div class="container">
                <?php
					$pages = get_pages( [
						'authors' => 2,
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