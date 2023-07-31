<?php
/*
Plugin Name: WP Lozad
Description: lazy loading of images by baroda algorithm
Author: Evgeniy Kozenok
Version: 1.7.1
*/

include_once __DIR__ . '/vendor/autoload.php';

use Lozad\FrontPageProcessing;
use Lozad\Settings\AdminSettingsPage;

if (!defined('LOZAD_INDEX_FILE')) {
    define('LOZAD_INDEX_FILE', basename(__FILE__));
}

FrontPageProcessing::start();

AdminSettingsPage::start();