# Plugin Info

**Plugin Name**: GK Steemit Info
**Contributors**: mcfarhat
**Tags**: wordpress, steemit, widget, user count
**Requires at least**: 4.3
**Tested up to**: 4.9
**Stable tag**: trunk
**License**: GPLv2 or later
**License URI**: https://www.gnu.org/licenses/gpl-2.0.html

# Short Summary

The plugin serves to be an innovative approach towards connecting steemit to wordpress. At its current phase, it allows adding one or more widgets/shortcodes that display steemit related info including specific posts by users and other filtering criteria.

# Plugin Details

Steemit.com is a fast growing social network/blogging platform that rewards authors and curators for quality content via the concept of upvotes (likes)

We believe that providing proper means to integrate information from steemit into wordpress will lead the way for further growth to the platform, particularly via widgets/shortcodes as they are easiest to embed for any wordpress site owner, without needed development skills.

Using GK Steemit Info, you now have the capability to create widgets or embed shortcodes to your wordpress solution.

## Steemit User Count

In its first version, the plugin allowed embedding a widget that displays the current number of users registered on steemit, at this instant over 600,000 users are registered on steemit.

The widget allows you to set a proper title for the widget, but also to set the data refresh frequency. The default value is at 5,000 ms (5 seconds), and can be increased in increments of 500 ms (half a second) each. This flexibility is provided to your as a wordpress site owner if you have any other computing intensive functionality on your site.

Check out screenshots 1, 2, and 3 below for a highlight of the widget, its configuration options, and the sample outcome of the widget.

The widget also includes a link to refer people to steemit.com as a promotional aspect of the widget.

You can alternatively use the shortcode version, through using [steemit_user_count refresh_frequency=5000] whereby refresh_frequency (optional) defines how often to refresh fetching the data. You can skip on setting this param and it defaults to 5,000 ms (5 seconds).

## Steemit User Posts

We decided to add this widget due to its importance in highlighting the steemit posts by the owner of the wordpress site, or any other steemit author the site owner would like to highlight.
This provides the flexibility to display within a widget or more, or within a specific page(s) dedicated for steemit posts, a multitude of posts which are filtered by particular authors, while giving you the flexibility to configure the different params.
The widget will actually display a listing of posts, one on each line, whereby the title is shown along with a link to the original steemit posts, including the number of votes (likes) the post received, as well as the payment amount associated with the post in STEEM units (whether it has been paid or is still pendin payout)

Check out screenshots 1, 2, and 3 below for a highlight of the widget, its configuration options, and the sample outcome of the widget.

In details, once adding the widget to your specific sidebar, you have default pre-selected configuration, which can also be adjusted to configure your widget(s), as follows:
- Title: This refers to the title/header of your widget
- Steemit Username: this refers to the username of the author of the posts on steemit, WITHOUT the leading @. In case the author does not exist, no results will be returned.
- Max Post Count: this limit allows capping the number of posts being returned by the widget. The smaller the amount, the quicker the result. This is due to querying the steemjs API. Default value is set at 10, and the maximum is capped at 100 to avoid timeouts.
- Filter by Tag: this allows filtering the posts of the selected authoer by a specific tag. This can be very useful if under one widget you would like to display a reference to your "photography" tagged posts, while on another you would like to display your "crypto" tagged posts, or "wordpress" tags. 
- Exclude resteems: If you do not want to display posts that you have resteemed, we have given you the option right there. Default value is for resteems to be included. 
- Minimum Pay: Steemit is all about payments for posts. If you would like to only show posts which have yielded a particular minimum amount of gains, you are able to do so here. The value you provide will match the steem value returned by the post, whether it has already been paid or is still pending payout. Default value is 0 meaning all posts will be returned.
Please note that adjusting those filtering values could result in the SteemJS API taking more time to return your posts.

A shortcode version is also available for use on any page's content or within your dynamic code. This is accessible via utilizing the following code: [steemit_user_posts username=USERNAME limit=LIMIT excluderesteem=1 minpay=0 filtertag=TAG]
whereby: 
- username (Required) is the target steemit username
- limit (Optional) is the top limit of the number of posts to be shown
- excluderesteem (Optional) allows to avoid displaying resteemed posts and will only result in originally created posts by this author to be displayed
- minpay (Optional) allows setting a minimum payment amount of the post to be returned back and displayed.
- filtertag (Optional) allows filtering by one specific tag so that posts that do NOT contain this tag will not be returned back

## Installation 

1. Upload the plugin files to the `/wp-content/plugins/gk-steemit-info` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. a. Go to the appearance -> widgets screen, and then select which widget(s) among the ones labeled as "Steemit" you would like to add to your visual display, and then configure them accordingly. You can add multiple entries of each widget with different configurations
3. b. Alternatively use the relevant shortcodes highlighted above on any page or within your code.
4. That's it! 

## Screenshots
1. <a href="https://www.dropbox.com/s/vngf4dt3h9zdgys/steemit_user_posts_widget.png?dl=0">Screenshot showing the new Steemit User Posts widget on the selection screen</a>
![](https://www.dropbox.com/s/vngf4dt3h9zdgys/steemit_user_posts_widget.png?dl=1)
2. <a href="https://www.dropbox.com/s/21q53wkxfsj50ev/steemit_user_posts_configuration.png?dl=0">Screenshot showing the configuration options of the Steemit User Posts widget</a>
![](https://www.dropbox.com/s/21q53wkxfsj50ev/steemit_user_posts_configuration.png?dl=1)
3. <a href="https://www.dropbox.com/s/96q2l09bnqe6uzi/display_steemit_user_posts.png?dl=0">Screenshot of a sample outcome of the Steemit User Posts widget with default configurations</a>
![](https://www.dropbox.com/s/96q2l09bnqe6uzi/display_steemit_user_posts.png?dl=1)
4. <a href="https://www.dropbox.com/s/macix3vv85gme2b/new_widget.png?dl=0">Screenshot showing the new "Steemit Info Widget"</a>
![](https://www.dropbox.com/s/macix3vv85gme2b/new_widget.png?dl=1)
5. <a href="https://www.dropbox.com/s/stgttgdgrvpncx1/widget_options.png?dl=0">Screenshot showing the options of the widget once added</a>
![](https://www.dropbox.com/s/stgttgdgrvpncx1/widget_options.png?dl=1)
6. <a href="https://www.dropbox.com/s/8v86dvzbxo8nfz0/display_steemit_user_count.png?dl=0">Screenshot of a sample outcome of the Steemit Info Widget</a>
![](https://www.dropbox.com/s/8v86dvzbxo8nfz0/display_steemit_user_count.png?dl=1)

## Changelog

### 0.2.0
Adding support for Steemit User Posts widget and shortcode along with filtering and multiple widget support
Fix for steemjs issue breaking functionality due to move to api.steem.com

### 0.1.0
Initial Version
