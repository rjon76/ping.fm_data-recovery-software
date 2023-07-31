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

add_action( 'cachepages', 'cache_function' );
function cache_function() {
    
    require('url_array.php');

    foreach ($urls as $url) {
        fetch_headers($url);
    }
}
