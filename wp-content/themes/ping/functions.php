<?php
/**
 * Twenty Twenty functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

function exec_php($matches){
    eval('ob_start();'.$matches[1].'$inline_execute_output = ob_get_contents();ob_end_clean();');
    return $inline_execute_output;
}
function inline_php($content){
    $content = preg_replace_callback('/\[exec\]((.|\n)*?)\[\/exec\]/', 'exec_php', $content);
    $content = preg_replace('/\[exec off\]((.|\n)*?)\[\/exec\]/', '$1', $content);
    return $content;
}
add_filter('the_content', 'inline_php', 0);

add_action( 'template_redirect', 'author_archive_redirect' );
add_filter( 'author_link', 'remove_author_pages_link' );

function author_archive_redirect() {
    if( is_author() || is_category() || is_tag() ) {
       wp_redirect( home_url(), 301 );
       exit;
   }
}

function remove_author_pages_link( $content ) {
    return home_url();
}

function fetch_headers($url) {
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $response = curl_exec($ch); 
    curl_close($ch);
    sleep(5);
    return;
}

// pll_register_string( 'nav1', 'Chromecast Screen Mirroring' );
// pll_register_string( 'nav2', 'Router Login & IP Address' );
// pll_register_string( 'nav3', 'Social Media Tutorials' );
// pll_register_string( 'nav4', 'App Vs App' );
// pll_register_string( 'social1', 'Twitter' );
// pll_register_string( 'social2', 'Facebook' );
// pll_register_string( 'social3', 'YouTube' );
// pll_register_string( 'social4', 'LinkedIn' );
// pll_register_string( 'Copyright', 'Copyright' );
// pll_register_string( 'team', 'Electronic Team, Inc., its affiliates and licensors.' );
// pll_register_string( 'Legal Information', 'Legal Information.' );
// pll_register_string( 'addr', '1800 Diagonal Road, Ste 600, Alexandria, VA 22314, USA' );
// pll_register_string( 'Tutorial', 'Tutorial written from original video by:' );
// pll_register_string( 'More IP', 'More IP / WiFi / Router Tutorials' );
// pll_register_string( 'Last update', 'Last update on ' );
// pll_register_string( 'Chat GPT', 'Chat GPT' );
// pll_register_string( 'Written by', 'Written by' );
// pll_register_string( 'More Articles', 'More Articles' );

add_action('wp_header', 'wpml_floating_language_switcher'); 
  
 function wpml_floating_language_switcher() { 
    echo '<div class="wpml-floating-language-switcher">';
        //PHP action to display the language switcher (see https://wpml.org/documentation/getting-started-guide/language-setup/language-switcher-options/#using-php-actions)
        do_action('wpml_add_language_selector');
    echo '</div>'; 
}