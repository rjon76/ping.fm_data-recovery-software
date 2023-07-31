=== WP Lozad ===
Contributors: john1302
Tags: image, lazyload, lozad, front, posts, html processing
Requires at least: 5.0
Tested up to: 6.1.1
Stable tag: 1.7.1
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Preparation of html content for processing javascript plugin lozad.js on front.

== Description ==

Preparation of html content for processing javascript plugin lozad.js on front.

= Requirements =

* WordPress 5.0 or higher

== Installation ==

Unzip the lozad folder to your WordPress plugins folder. It should work out of the box.


== Changelog ==
= 1.7.1 =
* 2022-12-01: decoding async

= 1.7.0 =
* 2022-11-21: wp 6.1.1

= 1.6.2 =
* 2022-08-18: JS ios < 14

= 1.6.1 =
* 2022-07-22: fix js private identifier in cache

= 1.6.0 =
* 2022-07-12: disable use jQuery. Wp 6.0 support

= 1.5.3 =
* 2021-12-22: jquery > 2.1

= 1.5.2 =
* 2021-12-09: fix php 5 support

= 1.5.1 =
* 2021-09-28: fix template_redirect action hook

= 1.5.0 =
* 2021-09-22: add hook level option

= 1.4.9 =
* 2021-08-09: get_home_path func replace by ABSPATH const

= 1.4.8 =
* 2021-07-16: Check is exist image 2x for background 2x 

= 1.4.7 =
* 2021-06-24: IE img src 

= 1.4.6 =
* 2021-06-23: IE js supported

= 1.4.5 =
* 2021-05-24: Use different lozadContentProcessing actions on different case

= 1.4.4 =
* 2021-05-21: Move base plugin action to wp shutdown action

= 1.4.3 =
* 2021-05-11: Fix space

= 1.4.2 =
* 2021-05-11: Add find by img data-height attribute if present

= 1.4.1 =
* 2021-05-07: Add setting for required img attributes

= 1.4.0 =
* 2021-05-06: PageSpeed Insights required img attributes and load youtube video

= 1.3.2 =
* 2021-01-09: Fix background image2x js

= 1.3.1 =
* 2020-11-10: Handle img sizes attribute

= 1.3.0 =
* 2020-11-07: Update iframe processing wrapper tag

= 1.2.9 =
* 2020-10-28: Process only page with post id

= 1.2.8 =
* 2020-10-26: Fix admin setting page notice

= 1.2.7 =
* 2020-10-23: Fix DOMDocument warnings

= 1.2.6 =
* 2020-10-16: Add skipped class option

= 1.2.5 =
* 2020-10-15: fix

= 1.2.4 =
* 2020-10-15: Settings link fix

= 1.2.2 =
* 2020-10-13: Option where include js scripts

= 1.2.0 =
* 2020-10-07: Lozad js pluggin IE11 and safari browsers

= 1.1.1 =
* 2020-10-06: Different background-image style tag attributes

= 1.1 =
* 2020-10-05: Php 5.x version. Not proccessing ajax request

= 0.3 =
* 2020-10-02: First release.