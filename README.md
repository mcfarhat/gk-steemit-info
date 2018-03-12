# Plugin Info

**Plugin Name**: GK Steemit Info<br/>
**Contributors**: mcfarhat<br/>
**Tags**: wordpress, steemit, widget, user count, user registration<br/>
**Requires at least**: 4.3<br/>
**Tested up to**: 4.9<br/>
**Stable tag**: trunk<br/>
**License**: GPLv2 or later<br/>
**License URI**: https://www.gnu.org/licenses/gpl-2.0.html<br/>

# Short Summary

The plugin serves to be an innovative approach towards connecting steemit to wordpress. At its current phase, it allows adding one or more widgets/shortcodes that display steemit related info including some steem stats, user info, user posts, user upvotes (likes), trending posts along with a bulk of filtering options, but also allows instantaneous creation of steemit users via Steem Power delegation

# Plugin Details

Steemit.com is a fast growing social network/blogging platform built on the steem blockchain, and that rewards authors and curators for quality content via the concept of upvotes (likes)

We believe that providing proper means to integrate information from steemit into wordpress will lead the way for further growth to the platform, particularly via widgets/shortcodes as they are easiest to embed for any wordpress site owner, without needed development skills.

Using GK Steemit Info, you now have the capability to create widgets or embed shortcodes to your wordpress solution but also to create new users on steemit, provided you are willing to delegate steem power to them

## Widgets/Shortcodes

### Steemit User Info

This widget allows the display of one or more steemit user(s) info via widgets/shortcodes. 
Upon dragging and dropping widget "Steemit User Info", you have the capability to specificy a user whose info you would like to be displayed.
The information will auto-refresh every 30 seconds, and includes the following details:
- username
- user profile image
- about section
- location
- website
- total post count
- STEEM Power including own steem power (SP), delegated SP, received SP, and Effective SP (after adding and removing received and delegated SP)
- STEEM and SBD balances
- Current Voting Power
- Reputation
- Followers Count
- Following Count
- Estimated Accout Value as pulled from steemit (which is average STEEM/SBD USD value over last 7 days)
- Real Time Account Value calculated real-time against STEEM and SBD current USD market value as pulled from <a href="coinmarketcap.com">coinmarketcap.com's API</a>

Check out screenshots 1, 2, and 3 below for a highlight of the widget, its configuration options, and the sample outcome of the widget.

A shortcode version is also available to be used on any page and/or via code. Via using the code: [steemit_user_info username=USERNAME] whereby username is the username of the steemit account.

### Steemit User Posts

We decided to add this widget due to its importance in highlighting the steemit posts by the owner of the wordpress site, or any other steemit author the site owner would like to highlight.
This provides the flexibility to display within a widget or more, or within a specific page(s) dedicated for steemit posts, a multitude of posts which are filtered by particular authors, while giving you the flexibility to configure the different params.
The widget will actually display a listing of posts, one on each line, whereby the title is shown along with a link to the original steemit posts, including the number of votes (likes) the post received, as well as the payment amount associated with the post in STEEM units (whether it has been paid or is still pendin payout)

Check out screenshots 4, 5, and 6 below for a highlight of the widget, its configuration options, and the sample outcome of the widget.

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

### Steemit Trending Posts

The purpose of this widget is to allow quick access and visibility via your wordpress site/installation into the trending posts currently on display on steemit.
The widget allows several configuration options and can be easily dragged/dropped within the widgets area, but also supports the use of a shortcode to plug the info into a page/code.
Configuration options include:
- Title: This refers to the title/header of your widget
- Max Post Count: to control the max number of posts to be displayed. Default value is 10, max is 100.
- Filter by Tag: if left blank, all trending posts will be retrieved regardless of any tag. If set, only posts belonging to this particular tag will be fetched.

Check out screenshots 7, 8, and 9 below for a highlight of the widget, its configuration options, and the sample outcome of the widget.

In terms of shortcode support, the following shortcode can be used for such display: [steemit_trending_posts limit=LIMIT filtertag=TAG]
whereby:
- limit is an optional attribute that defaults to 10 if not set.
- filtertag is an optional attribute, the lack of which would display all trending posts. Alternatively setting this value to a single tag would allow the display of only posts relevant to this particular tag.

### Steemit Info

In its first version, the plugin allowed embedding a widget that displays the current number of users registered on steemit, at this instant over 740,000 users are registered on steemit.
The widget has been upgraded and improved now, with additional functionality. 
It allows you to set a proper title for the widget, but also to set the data refresh frequency. The default value is at 5,000 ms (5 seconds), and can be increased in increments of 500 ms (half a second) each. This flexibility is provided to your as a wordpress site owner if you have any other computing intensive functionality on your site.
In its current version, the widget allows the display of: 
- steemit user count
- SBD and STEEM current supply, as provided via steem js API.
- STEEM/USD and SBD/USD current price, as provided via <a href="coinmarketcap.com">CoinMarketCap.com's API</a> along with the 1 hour, 24 hour, and 7 day move (upwards or downwards trend), as well as the rank of the currency at the moment in relevance to other crypto currencies.

Check out screenshots 10, 11, and 12 below for a highlight of the widget, its configuration options, and the sample outcome of the widget.

The widget also includes a link to refer people to steemit.com as a promotional aspect of the widget.

You can alternatively use the shortcode version, through using [steemit_user_count refresh_frequency=5000] whereby refresh_frequency (optional) defines how often to refresh fetching the data. You can skip on setting this param and it defaults to 5,000 ms (5 seconds).

### Steemit User Voted Posts

The purpose of this widget is to allow quick access and visibility via your wordpress site/installation into the posts voted by a specific user, allowing the display of each post's link, along with the vote percent cast by the user. This gives a quick history/perspective about the user's likes. The posts are sorted by the most recent ones.
The widget allows several configuration options and can be easily dragged/dropped within the widgets area, but also supports the use of a shortcode to plug the info into a page/code.
Configuration options include:
- Title: This refers to the title/header of your widget.
- Steemit Username: Defining the user whose voted posts will be rendered. Simply enter the steemit username, without the leading @ symbol.
- Max Post Count: To control the max number of posts to be displayed. Default value is 10, max is 100.

Check out screenshots 16, 17, and 18 below for a highlight of the widget, its configuration options, and the sample output of the widget.

In terms of shortcode support, the following shortcode can be used for such display: [steemit_user_voted_posts username=USERNAME limit=LIMIT]
whereby:
- username (Required) is the target steemit username whose voted posts are to be displayed.
- limit (Optional) is the top limit of the number of posts to be shown


## Backend Management

### Create New Steemit User

The plugin provides also a backend menu that allows the instantaneous creation of new steemit users. Upon setup, a new menu will be made available within the wordpress backend management interface, whereby a left menu icon "GK Steemit" will be added (see screenshot 13 below)
Clicking on this menu item will open the interface for the new user creation (see screenshot 14 below).
The interface includes the following field:
- New Account Name: which identifies the new user name to be created. This is WITHOUT the standard @ symbol
- New Account Password: which is the password/WIF to be used for this account. We recommend creating a complex password, preferably using <a href="http://passwordsgenerator.net/">http://passwordsgenerator.net/</a> and setting a minimum of 50 characters. Do make sure NOT to include symbols, as those will cause a validation error on account creation and are not supported by steemit API. You will need to only use caps and small letters and numbers.
- Owner Account Name: which identifies your account, or whoever account that is being used for the creation of the new account, and whose SP will be used for the delegation. Again @ symbol should NOT be included
- Owner WIF/Private Key: which relates to the owner account, and is sent over to the API to create and delegate the amount to the new account
- Fee (in STEEM): this is a required value to be sent over when creating the account, and which will eventually land into the actual account. Recommended amount is 0.200 STEEM. Only enter numeric value in the field.
- Delegation (in VESTS): this is the amount of VESTS that will be delegated from the creator account to the new account. The default value 30663.815330 VESTS equates to 15 SP. While you can increase this amount, yet decreasing it can lead to error creating the account.

Upon clicking the create button, checks will be made to ensure that the user name is of proper format, does not exist, and that the amounts of fee and delegation are proper. The notification area right above the button will either display an error message or a confirmation of what occurred. (Check screenshot 15 below which is showing an existing account message).

Keep in mind that after creating an account and delegating SP to it, you can eventually cancel the delegation, yet an account WITHOUT at least 15 SP cannot function properly on steemit, but also the delegation amount will take 7 days to return to the creator account.

## Installation 

1. Upload the plugin files to the `/wp-content/plugins/gk-steemit-info` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. a. Go to the appearance -> widgets screen, and then select which widget(s) among the ones labeled as "Steemit" you would like to add to your visual display, and then configure them accordingly. You can add multiple entries of each widget with different configurations
3. b. Alternatively use the relevant shortcodes highlighted above on any page or within your code.
4. A new menu will also added to the wordpress backend administration under the name "GK Steemit Info" which provides access to the Steemit User Creation functionality.
5. That's it! 

## Screenshots
1. <a href="https://www.dropbox.com/s/8q2m7prow3ro13h/steemit_user_info_widget.png?dl=0">Screenshot showing the Steemit User Info widget on the selection screen</a>
2. <a href="https://www.dropbox.com/s/dgo0619z1826nts/steemit_user_info_configuration.png?dl=0">Screenshot showing the configuration options of the Steemit User Info widget</a>
3. <a href="https://www.dropbox.com/s/0h4bdhm5oryyaqz/steemit_user_info_sample_display.png?dl=0">Screenshot of a sample outcome of the Steemit User Info widget</a>
4. <a href="https://www.dropbox.com/s/vngf4dt3h9zdgys/steemit_user_posts_widget.png?dl=0">Screenshot showing the new Steemit User Posts widget on the selection screen</a>
5. <a href="https://www.dropbox.com/s/21q53wkxfsj50ev/steemit_user_posts_configuration.png?dl=0">Screenshot showing the configuration options of the Steemit User Posts widget</a>
6. <a href="https://www.dropbox.com/s/96q2l09bnqe6uzi/display_steemit_user_posts.png?dl=0">Screenshot of a sample outcome of the Steemit User Posts widget with default configurations</a>
7. <a href="https://www.dropbox.com/s/qo0qtymzzf1tjma/steemit_trending_posts_widget.png?dl=0">Screenshot showing the Steemit Trending Posts widget on the selection screen</a>
8. <a href="https://www.dropbox.com/s/fl0l2gkbh4mfvct/steemit_trending_posts_configuration.png?dl=0">Screenshot showing the configuration options of the Steemit Trending Posts widget</a>
9. <a href="https://www.dropbox.com/s/rzbwmmif49d2e82/steemit_trending_posts_display.png?dl=0">Screenshot of a sample outcome of the Steemit Trending Posts widget</a>
10. <a href="https://www.dropbox.com/s/macix3vv85gme2b/new_widget.png?dl=0">Screenshot showing the new "Steemit Info Widget"</a>
11. <a href="https://www.dropbox.com/s/stgttgdgrvpncx1/widget_options.png?dl=0">Screenshot showing the options of the widget once added</a>
12. <a href="https://www.dropbox.com/s/8v86dvzbxo8nfz0/display_steemit_user_count.png?dl=0">Screenshot of a sample outcome of the Steemit Info Widget</a>
13. <a href="https://www.dropbox.com/s/ovg9zx5ex62ll5a/create_steemit_user_menu.png?dl=0">Screenshot of the backend GK Steemit Info menu item</a>
14. <a href="https://www.dropbox.com/s/o2nds07etxxjkc6/create_steemit_user_interface.png?dl=0">Screenshot of the new steemit user creation screen</a>
15. <a href="https://www.dropbox.com/s/klyt9a2101s7l0f/create_steemit_user_notification.png?dl=0">Notification Area</a>
16. <a href="https://www.dropbox.com/s/d4x3fomh6ezzqc9/steemit_user_voted_posts_widget.png?dl=0">Screenshot showing the Steemit User Voted Posts widget on the selection screen</a>
17. <a href="https://www.dropbox.com/s/i9xyh5h8cr08c99/steemit_user_voted_posts_configuration.png?dl=0">Screenshot showing the configuration options of the Steemit User Voted Posts widget</a>
18. <a href="https://www.dropbox.com/s/w1tohwteylaumsl/steemit_user_voted_posts_display.png?dl=0">Screenshot of a sample outcome of the Steemit User Voted Posts widget</a>

## Changelog

### 0.6.0
- Adding new widget/shortcode to display specific user's voted posts
- Adding follower and following count under steemit user info widget
- Fixing issue with display of some users' info under steemit user info widget

### 0.5.0
- Adding support for creating steemit users instantaneously via a backend interface while utilizing some delegation from existing accounts.
- Adding color indicators for SBD & STEEM price modifications across 1h, 24h, and 7d
- Refactoring coinmarketcap code into single function
- Changing coinmarketcap reference to smaller text to become less intrusive
- New screenshot uploaded to replace existing Steemit Info widget outdated screenshot

### 0.4.0
- New widget for display of trending posts with optional tag filtering and post count limit
- Additional information for STEEM/SBD pricing including 24h and 7d change indicator for steemit info widget
- New real-time balance calculation for account balance to single user info widget
- Improved formatting for post info (upvotes/payment)

### 0.3.0
- Created new widget/shortcode for steemit user information, including name, image, SP, STEEM, SBD, VP, Reputation,... with backend selection of which user to display info for. Multiple widgets are supported.
- Modified existing Steemit info widget to include new information, including STEEM and SBD current supply, but also more importantly now pulling current STEEM and SBD pricing from coinmarketcap, and being displayed along with 1 hour indicator, as well as currency ranking data

### 0.2.0
Adding support for Steemit User Posts widget and shortcode along with filtering and multiple widget support
Fix for steemjs issue breaking functionality due to move to api.steem.com

### 0.1.0
Initial Version
