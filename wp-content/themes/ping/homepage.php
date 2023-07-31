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
                        <h1>📡 <a href="/" rel="follow">Ping.FM</a> (Ping FM) 🏅👤 Your Personal Best Apps / Software Guru 🧭</h1>
                        <p>🚀 Dive into our 📚 library of top-tier apps 🌟 and unearth 5-10 unique alternatives 🎲 for each. Bringing 🔬 innovative solutions for all your 🌐 digital needs. Boost productivity 🚀, simplify tasks 🔄, and boost entertainment 🎭.</p>
                        <p>Don't forget to check our 📺 'how-to' guides 🧑‍🏫, inspired by 🔥 popular videos!</p>
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
                <?php
					$pages = get_pages( [
						'authors' => 4,
					] );
                ?>
                   <?php foreach( $pages as $post ) {
						$date = date_format(date_create($post->post_modified), 'M j, Y');
					?>
                    <div class="article">
                        <div class="innerart">
                            <img srcset="/wp-content/themes/ping/booking.png" src="/wp-content/themes/ping/booking.png" width="512" height="512" alt="App icon">
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