<?php
/*
	Template Name: Post AI ChromeCast Category
*/

	get_header();
?>
	
		<main class="homepage chromecast">
            <section class="banner">
                <div class="banner--img chromecast--img">
                    <div class="banner--inner">
                        <h1>Unlock the Power of Chromecast with iPhone: Comprehensive Tutorials and Guides at <a href="/" rel="follow">Ping.FM</a></h1>
                        <p><a href="/" rel="follow">Ping.FM</a> is your ultimate resource hub for tutorials that elucidate how to Chromecast from iPhone, iPad, and Google Chromecast with iPhone. It provides an in-depth, step-by-step guide on setting up Chromecast on iPhone and Google Chromecast iPhone usage for beginners and tech-savvy users alike. The site addresses essential topics such as Chromecast iPhone screen mirroring, casting from iPhone to Chromecast, Airplay to Chromecast, and the Chromecast app for iPhone. Ever wondered how to use Chromecast with iPhone or cast iPhone to Chromecast? <a href="/" rel="follow">Ping.FM</a> simplifies these complex processes and allows users to Chromecast iPhone to TV with ease. Furthermore, users interested in screen mirroring Chromecast iPhone or mirroring iPhone Chromecast are covered with a range of easy-to-follow tutorials.</p>
                    </div>
                </div>
            </section>
            <section class="homePosts">
                <div class="container">
                <?php
					$pages = get_pages( [
						'authors' => 5,
					] );
                ?>
                   <?php foreach( $pages as $post ) {
						$date = date_format(date_create($post->post_modified), 'M j, Y');
					?>
                    <div class="article">
                        <div class="innerart">
                            <img srcset="/wp-content/uploads/webinar.png" src="/wp-content/uploads/webinar.png" width="512" height="512" alt="Webinar icon">
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