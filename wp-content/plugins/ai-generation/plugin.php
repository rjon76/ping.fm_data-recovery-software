<?php
/**
* Plugin Name: AI Article Generator
* Plugin URI: https://www.your-site.com/
* Description: Generation and translate articles plugin.
* Version: 0.1
* Author: Zamozhnii
* Author URI: https://www.your-site.com/
**/

if (!defined('ABSPATH')) {
    exit;
}

// Create an Admin Menu in the WordPress Dashboard
require_once __DIR__ . '/parts/admin-menu.php';

// ADD REGENERATION/TRANSLATE LINKS
require_once __DIR__ . '/parts/regeneration-link.php';
require_once __DIR__ . '/parts/regeneration-faq-link.php';
require_once __DIR__ . '/parts/add-faq-link.php';
require_once __DIR__ . '/parts/translate-link.php';
require_once __DIR__ . '/parts/translate-faq-link.php';

// Admin pages views
require_once __DIR__ . '/views/new-article.php';

// PAGE GENERATION
require_once __DIR__ . '/generation/funcGenerateArticle.php';
add_action('wp_ajax_chat_gpt_generate_article', 'funcGenerateArticle');

// PAGE REGENERATION
add_action('admin_action_chat_gpt_generate_article', 'funcGenerateArticle');

// PAGE TRANSLATE TO ALL LANGUAGES
require_once __DIR__ . '/generation/funcTranslateArticle.php';
add_action('admin_action_chat_gpt_translate_article', 'funcTranslateArticle');

// CONNECT SCRIPT
add_action('admin_init', 'load_my_script');

function load_my_script() {
    wp_enqueue_script('my-custom-script', get_site_url() . "/wp-content/plugins/ai-generation/assets/js/index.js", array('jquery'));
}

// Modal edit/translate
require_once __DIR__ . '/generation/funcGenerateModal.php';
add_action('admin_footer', 'funcGenerateModal');