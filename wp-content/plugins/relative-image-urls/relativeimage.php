<?php
/*
Plugin Name: Relative Image URLs
Plugin URI: http://scottwernerdesign.com/plugins/relative-image-urls
Description: Replaces absolute URLs with Relative URLs for image paths in posts.
Author: Scott Werner
Author URI: http://scottwernerdesign.com
Tags: relative, image, images, url, link, absolute
Version: 999.0
*/


function image_to_relative($html, $id, $caption, $title, $align, $url, $size, $alt)
{
	$imageurl = wp_get_attachment_image_src($id, $size);
	$relativeurl = wp_make_link_relative($imageurl[0]);   
	$html = str_replace($imageurl[0],$relativeurl,$html);
      
return $html;
}
add_filter('image_send_to_editor','image_to_relative',5,8);

?>