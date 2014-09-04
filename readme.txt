=== GA Nav Menus Tracking ===
Contributors: pauldewouters, secretstache
Tags: google analytics,navigation,menus
Requires at least: 3.2
Tested up to: 4.0
Stable tag: 1.0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Google analytics tracking events to your WordPress navigation menus.

== Description ==

Add tracking events to your WordPress navigation menu items!

Reference: https://developers.google.com/analytics/devguides/collection/gajs/eventTrackerGuide

This plugin adds custom fields to your nav menu items that represent the parameters for the _trackEvent method of Google Analytics.

This plugin requires that you already have Google Analytics tracking active on your WordPress website. For example you have Yoast's Google Analytics for WordPress installed and activated.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Appearance > Menus and enter your _trackEvent parameters for the nav items you wish to track.

== Frequently Asked Questions ==

= What are the requirements for this plugin to work? =

This plugin requires a valid Google Analytics tracking code to be installed. 
I recommend this one: http://wordpress.org/extend/plugins/google-analytics-for-wordpress/

== Screenshots ==

1. The nav menu items fields added by plugin to enter your tracking event parameters (category, action and label)

== Changelog ==

= 1.0.6 =

Remove the dust and test if works with 4.0

= 1.0.5 =
fixed plugin description

= 1.0.4 =
added pot file for translators

= 1.0.3 =
Fixed a bug in javascript

= 1.0 =
* Initial version relased to the public.
