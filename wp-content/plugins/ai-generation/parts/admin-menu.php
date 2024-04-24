<?php

add_action( 'admin_menu', 'ai_generation_post' );

function ai_generation_post() {
    $page_title = 'AI Article Generation';
    $menu_title = 'AI Generation';
    $capability = 'manage_options';
    $menu_slug  = 'new-post-generation';
    $sub_menu_slug = 'regenerate-post';
    $function   = 'new_post_data';
    $sub_menu_func = 'load_post_data';
    $icon_url   = 'dashicons-media-code';
    $position   = 50;
    
    add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_submenu_page( $menu_slug, 'Generate New Article', 'Generate new', $capability, $menu_slug, $function );
    add_submenu_page( $menu_slug, 'Generate Medium Article', 'Generate Medium', $capability, 'new-medium', 'new_medium_data' );
}
