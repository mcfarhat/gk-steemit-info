=== Plugin Name ===

Plugin Name: GK Steemit Info
Contributors: mcfarhat
Tags: wordpress, steemit, widget, user count
Requires at least: 4.3
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

=== Short Summary ===

The plugin serves as a means to add, into your current wordpress installation, one or more widgets/shortcodes that allow displaying steemit related info

== Description ==

Steemit.com is a fast growing social network/blogging platform that rewards authors and curators for quality content via the concept of upvotes (likes)

We believe that providing proper means to integrate information from steemit into wordpress will lead the way for further growth to the platform, particularly via widgets/shortcodes as they are easiest to embed for any wordpress site owner, without needed development skills.

Using GK Steemit Info, you now have the capability to create widgets or embed shortcodes to your wordpress solution.

In this initial version, you can now embed a widget that allows displaying the current number of users registered on steemit, at this instant over 500,000 users are signed up on steemit.

The widget allows you to set a proper title for the widget, but also to set the data refresh frequency. The default value is at 5,000 ms (5 seconds), and can be increased in increments of 500 ms (half a second) each depending if you have any other computing intensive functionality on your site.

The widget also includes a link to refer people to steemit.com as a promotional aspect of the widget.

You can alternatively use the shortcode version, through using [steemit_user_count refresh_frequency=5000] whereby refresh_frequency defines how often to refresh fetching the data. You can skip on setting this param and it defaults to 5,000 ms (5 seconds) as well. 

If you would like some custom work done, or have an idea for a plugin you're ready to fund, check our site at www.greateck.com or contact us at info@greateck.com

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/gk-steemit-info` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. a. Go to the appearance -> widgets screen, and add in the "Steemit Info Widget" widget, and set the title and/or refresh frequency.
3. b. Alternatively use the shortcode steemit_user_count highlighted above on any page or within your code.
4. That's it! 

== Screenshots ==
1. Screenshot showing the new "Steemit Info Widget" <a href="https://www.dropbox.com/s/macix3vv85gme2b/new_widget.png?dl=0">https://www.dropbox.com/s/macix3vv85gme2b/new_widget.png?dl=0</a>
2. Screenshot showing the options of the widget once added <a href="https://www.dropbox.com/s/stgttgdgrvpncx1/widget_options.png?dl=0">https://www.dropbox.com/s/stgttgdgrvpncx1/widget_options.png?dl=0</a>


== Changelog ==

= 0.1.0 =
Initial Version
