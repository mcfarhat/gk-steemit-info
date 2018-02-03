<?php
/*
  Plugin Name: GK Steemit Info
  Plugin URI: http://www.greateck.com/
  Description: A wordpress plugin that allows adding steem(it) (www.steemit.com) data to wordpress sites via widget or alternatively a shortcode
  Version: 0.3.0
  Author: mcfarhat
  Author URI: http://www.greateck.com
  License: GPLv2
  License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */
 
/* handles tracking if required libraries have been added already */
global $libraries_appended;
$libraries_appended = false; 
 
add_action('wp_head','include_js_func');
 
function include_js_func(){
	?>
	<script>
		/* function for formatting numbers with extra commas */
		function gk_add_commas(nStr) {
			if (isNaN(nStr)){ 
				return nStr;
			}
			nStr += '';
			var x = nStr.split('.');
			var x1 = x[0];
			var x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}
	</script>
	<?php
}
 
/* Creating widget handling steemit user count */
class steemit_info_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'steemit_info_widget',
		__('Steemit Info Widget', 'gk_steemit_info'),
		array( 'description' => __( 'Widget Allowing Display of Steem(it) info', 'gk_steemit_info' ), )
		);
	}
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'steemit_info_widget_title', $instance['title'] );
		$refresh_frequency = apply_filters( 'steemit_info_widget_refresh_frequency', $instance['refresh_frequency'] );
		//making room for hook display by any theme
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		//display output in the widget
		steemit_count_renderer($refresh_frequency);
		//making room for hook display by any theme
		echo $args['after_widget'];
	}
	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Steem(it) Info', 'gk_steemit_info' );
		}
		if ( isset( $instance[ 'refresh_frequency' ] ) ) {
			$refresh_frequency = $instance[ 'refresh_frequency' ];
		}
		else {
			//default value
			$refresh_frequency = 5000;
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id( 'refresh_frequency' ); ?>">How often to refresh data:</label>
		<input class="text" id="<?php echo $this->get_field_id( 'refresh_frequency' ); ?>" name="<?php echo $this->get_field_name( 'refresh_frequency' ); ?>" type="number" step="500" min="5000" value="<?php echo esc_attr( $refresh_frequency ); ?>" size="3"></p>
		<?php
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['refresh_frequency'] = ( ! empty( $new_instance['refresh_frequency'] ) ) ? $new_instance['refresh_frequency'] : '5000';
		return $instance;
	}
}

/* Register and load the widget*/
function gk_load_steemit_info_widget() {
    register_widget( 'steemit_info_widget' );
}
add_action( 'widgets_init', 'gk_load_steemit_info_widget' ); 
 
/* shortcode to display steemit user count on front end. 
Use it in format [steemit_user_count refresh_frequency=8000] */
add_shortcode('steemit_user_count', 'display_steemit_user_count' );

function display_steemit_user_count( $atts, $content = "" ) {
	$inner_atts = shortcode_atts( array(
        'refresh_frequency' => 5000,
    ), $atts );
	$refresh_frequency = $inner_atts['refresh_frequency'];
	if (!is_numeric ($refresh_frequency) || (is_numeric($refresh_frequency) && $refresh_frequency<5000)){
		$refresh_frequency = 5000;
	}
	steemit_count_renderer($refresh_frequency);
}

/* function handling the display of the steemit users count widget */
function steemit_count_renderer($refresh_frequency){
	global $libraries_appended;
	if (!$libraries_appended){
?>
		<!-- including fontawesome -->
		<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
		<!-- including steemjs library for performing calls -->
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<!-- including jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		
<?php
		$libraries_appended = true;
	}
?>
		<script>
			var skip_price_pull = 0;
			function tick_info(){
				jQuery(document).ready(function($){
					//fix for migration to api.steemit.com
					steem.api.setOptions({ url: 'https://api.steemit.com' });
					
					steem.api.getAccountCount(function(err, result) {
						//result now contains the number of accounts. Display this into the input box
						$('#steemit_accounts').text(gk_add_commas(result));
						// console.log(err, 'account count:'+gk_add_commas(result));
					});
					
					//grab steem dynamic properties to allow converting vesting shares to STEEM Power
					steem.api.getDynamicGlobalProperties(function(err, result) {
						// console.log(result);
						$('#sbd_supply').text("SBD Supply: "+gk_add_commas(result.current_sbd_supply.replace(' SBD',''))+' SBD');
						$('#steem_supply').text("STEEM Supply: "+gk_add_commas(result.current_supply.replace(' STEEM',''))+' STEEM');
					});
					
					<?php 
					//for price feed, we need to minimize calls not to overuse coinmarketcap API. restrict to once per every 20 seconds.
					if ($refresh_frequency <= 20000){
					?>
						if (skip_price_pull == 0){
							console.log('>>grab');
							//grab steem values
							$.ajax({
								url: 'https://api.coinmarketcap.com/v1/ticker/steem/',
								dataType: 'json',
								success: function (data) { 
									// console.log(parseFloat(data[0].price_usd).toFixed(2));
									var content = 'STEEM: $'+parseFloat(data[0].price_usd).toFixed(2);
									if (parseFloat(data[0].percent_change_1h) > 0){
										content += ' <i class="fas fa-arrow-up"></i>'
									}else{
										content += ' <i class="fas fa-arrow-down"></i>'
									}
									content += ' (Rank: '+data[0].rank+')';
									$('#steem_price').html(content);
								}
							});
							//grab SBD values
							$.ajax({
								url: 'https://api.coinmarketcap.com/v1/ticker/steem-dollars/',
								dataType: 'json',
								success: function (data) { 
									// console.log(parseFloat(data[0].price_usd).toFixed(2));
									var content = 'SBD: $'+parseFloat(data[0].price_usd).toFixed(2);
									if (parseFloat(data[0].percent_change_1h) > 0){
										content += ' <i class="fas fa-arrow-up"></i>'
									}else{
										content += ' <i class="fas fa-arrow-down"></i>'
									}
									content += ' (Rank: '+data[0].rank+')';
									$('#sbd_price').html(content);
								}
							});
							skip_price_pull ++;
						}else{
							//skip trice
							if (skip_price_pull < 3){
								skip_price_pull ++;
							}else{
								skip_price_pull = 0;
							}
						}
						
					<?php
					}
					?>
				});
			}
			//first call
			tick_info();
			//subsequent recurring calls
			setInterval(tick_info, parseInt(<?php echo $refresh_frequency; ?>));
		</script>
		<div>
			<span>Steemit Current Users Count:</span>
			<span id="steemit_accounts"></span>
			<div id="sbd_supply"></div>
			<div id="steem_supply"></div>
			<div id="steem_price"></div>
			<div id="sbd_price"></div>
			<span><i>Current Price per <a href="https://coinmarketcap.com/">CoinMarketCap.com</a></i></span>
			<br/>
			<div><i>Your voice is worth something. Join the community that pays you to post and curate high quality content.<br/>
			Check out <a href="https://www.steemit.com">Steemit.com</a></i></div>
		</div>
<?php
}


/**************************************************************/

/* Creating widget handling steemit user count */
class steemit_user_posts_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'steemit_user_posts_widget',
		__('Steemit User Posts Widget', 'gk_steemit_info'),
		array( 'description' => __( 'Widget Allowing Display of Specific User Posts while providing filtering criteria', 'gk_steemit_info' ), )
		);
	}
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'steemit_user_posts_widget_title', $instance['title'] );
		
		//making room for hook display by any theme
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
			
		//grab username and post count params
		$username = apply_filters( 'steemit_username_widget', $instance['steemit_username'] );
		$postcount = apply_filters( 'steemit_post_count_widget', $instance['steemit_post_count'] );
		$excluderesteem = $instance['steemit_exclude_resteem'] ? true : false ;
		$postminpay = apply_filters( 'steemit_post_min_pay_widget', $instance['steemit_post_min_pay'] );
		$posttag = apply_filters( 'steemit_post_tag_widget', $instance['steemit_post_tag'] );
		
		//set as false by default
		if ($excluderesteem==''){
			$excluderesteem = false;
		}
		//widget container unique identifier based on timestamp
		$date = new DateTime();
		$contentid = $date->getTimestamp().mt_rand(1,4000);
		//display output in the widget
		steemit_user_posts_renderer($username, $postcount, $excluderesteem, $postminpay, $posttag, $contentid);
		
		//making room for hook display by any theme
		echo $args['after_widget'];
	}
	// Widget Backend
	public function form( $instance ) {
		//grab presaved values
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Posts on <a href="https://www.steemit.com">Steemit</a>', 'gk_steemit_user_posts' );
		}
		$steemit_username = "";
		$steemit_post_count = "";
		$steemit_exclude_resteem = "";
		$steemit_post_min_pay = "";
		$steemit_post_tag = "";
		if ( isset( $instance[ 'steemit_username' ] ) ) {
			$steemit_username = $instance[ 'steemit_username' ];
		}
		if ( isset( $instance[ 'steemit_post_count' ] ) ) {
			$steemit_post_count = $instance[ 'steemit_post_count' ];
		}
		if ( isset( $instance[ 'steemit_exclude_resteem' ] ) ) {
			$steemit_exclude_resteem = $instance[ 'steemit_exclude_resteem' ];
		}
		if ( isset( $instance[ 'steemit_post_min_pay' ] ) ) {
			$steemit_post_min_pay = $instance[ 'steemit_post_min_pay' ];
		}
		if ( isset( $instance[ 'steemit_post_tag' ] ) ) {
			$steemit_post_tag = $instance[ 'steemit_post_tag' ];
		}
		
		
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_username' ); ?>">Steemit Username:</label>
		@<input type="text" class="text" id="<?php echo $this->get_field_id( 'steemit_username' ); ?>" name="<?php echo $this->get_field_name( 'steemit_username' ); ?>" value="<?php echo esc_attr( $steemit_username ); ?>"></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_count' ); ?>">Max Post Count:</label>
		<input type="number" class="text" id="<?php echo $this->get_field_id( 'steemit_post_count' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_count' ); ?>" step="1" min="1" max="50" value="<?php echo esc_attr( $steemit_post_count ); ?>"></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_tag' ); ?>">Filter by Tag:</label>
		<input type="text" class="text" id="<?php echo $this->get_field_id( 'steemit_post_tag' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_tag' ); ?>" value="<?php echo esc_attr( $steemit_post_tag ); ?>"></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_exclude_resteem' ); ?>">Exclude Resteems:</label>
		<input type="checkbox" id="<?php echo $this->get_field_id( 'steemit_exclude_resteem' ); ?>" name="<?php echo $this->get_field_name( 'steemit_exclude_resteem' ); ?>" <?php if ($steemit_exclude_resteem){ echo "checked";}?>></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_min_pay' ); ?>">Minimum Pay:</label>
		<input type="number" class="text" id="<?php echo $this->get_field_id( 'steemit_post_min_pay' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_min_pay' ); ?>" step="1" min="1" max="200" value="<?php echo esc_attr( $steemit_post_min_pay ); ?>"></p>
		<?php
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['steemit_username'] = ( ! empty( $new_instance['steemit_username'] ) ) ? $new_instance['steemit_username'] : '';
		$instance['steemit_post_count'] = ( ! empty( $new_instance['steemit_post_count'] ) ) ? $new_instance['steemit_post_count'] : '';
		$instance['steemit_exclude_resteem'] = $new_instance['steemit_exclude_resteem'];
		$instance['steemit_post_min_pay'] = ( ! empty( $new_instance['steemit_post_min_pay'] ) ) ? $new_instance['steemit_post_min_pay'] : '0';
		$instance['steemit_post_tag'] = ( ! empty( $new_instance['steemit_post_tag'] ) ) ? $new_instance['steemit_post_tag'] : '';
		return $instance;
	}
}

/* Register and load the widget*/
function gk_load_steemit_user_posts_widget() {
    register_widget( 'steemit_user_posts_widget' );
}
add_action( 'widgets_init', 'gk_load_steemit_user_posts_widget' ); 
 
/* shortcode to display steemit user count on front end. 
Use it in format [steemit_user_posts username=USERNAME limit=LIMIT excluderesteem=1 minpay=0 filtertag=TAG] */
add_shortcode('steemit_user_posts', 'display_steemit_user_posts' );

function display_steemit_user_posts( $atts, $content = "" ) {
	/*$inner_atts = shortcode_atts( array(
        'refresh_frequency' => 5000,
    ), $atts );*/
	$username = $inner_atts['username'];
	$postcount = $inner_atts['limit'];
	$excluderesteem = $inner_atts['excluderesteem'];
	$postminpay = $inner_atts['postminpay'];
	$posttag = $inner_atts['filtertag'];
	//default to true
	if (empty($excluderesteem)){
		$excluderesteem = '';
	}
	if (empty($posttag)){
		$posttag = '';
	}
	if (empty($postminpay) || $postminpay==''){
		$postminpay = 0;
	}
	//widget container unique identifier based on timestamp
	$date = new DateTime();
	$contentid = $date->getTimestamp().mt_rand(1,4000);;
	steemit_user_posts_renderer($username, $postcount, $excluderesteem, $postminpay, $posttag, $contentid);
}

/* function handling the display of the selected users' posts */
function steemit_user_posts_renderer($username, $postcount, $excluderesteem, $postminpay, $posttag, $contentid){
	if ($username == ''){
		echo 'Steemit username not provided';
		return;
	}
	//if postcount not properly provided and within 1 - 100, default to 10
	if (!is_numeric ($postcount) || (is_numeric($postcount) && ($postcount<1 || $postcount>100))){
		$postcount = 10;
	}
	global $libraries_appended;
	if (!$libraries_appended){
?>
		<!-- including fontawesome -->
		<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
		<!-- including steemjs library for performing calls -->
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<!-- including jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>	
<?php
		$libraries_appended = true;
	}
?>

	<style>
		.gk-loader-img{
			display: block;
			margin: auto;
		}
		.steemit_user_info{
			text-align: center;
		}
		.steemit_user_img{
			width: 50px;
		}
	</style>
	<div>
		<div id="user_posts_container<?php echo $contentid;?>">
			<!-- default loader -->
			<img class="gk-loader-img" src="<?php echo plugins_url();?>/gk-steemit-info/img/ajax-loader.gif">
		</div>
		
	</div>	
	<script>
			/* when properly loaded, call steem API method to grab recent posts by selected username and display them */
			jQuery(document).ready(function($){
				//fix for migration to api.steemit.com
				steem.api.setOptions({ url: 'https://api.steemit.com' });
				
				//setup query to grab proper user with preset limit
				var query = {
					/* tag here actually accepts the username as param */
					tag: '<?php echo $username;?>',
					/* setting higher hard-coded limit to allow for further filtering on additional details, since the API seems to fail without a set limit for now. 
					User set limit will be handled manually in the loop..
					For speeing things up, we will test if any filtering criteria is not set, and the limit is less than 100, then use that limit instead for API call */
					<?php
						$hard_limit = 100;
						if (($postminpay=='' || $postminpay == 0 )&& ($excluderesteem == '' || $excluderesteem ==false) && ($posttag == '')){
							$hard_limit = $postcount;
						}
					?>
					limit: <?php echo $hard_limit;?>,
				};
				var container = document.getElementById('user_posts_container<?php echo $contentid;?>');
				//call getDiscussionsByBlog to grab user's latest posts
				steem.api.getDiscussionsByBlog(query, function (err, posts) {
					// console.log(err, discussions);
					if (!err) {
						var included_post_count = 0;
						var post_limit = <?php echo $postcount;?>;
						var exclude_resteem = '<?php echo $excluderesteem; ?>';
						var current_author = '<?php echo $username;?>';
						var min_pay = '<?php echo $postminpay; ?>';
						var filter_tag = '<?php echo $posttag; ?>';
						
						//remove loader / empty container
						container.innerHTML="";
						
						/* replacing map with each to allow breaking out */
						$.each (posts, function (index, post){
						//posts.map(function (post) {
							// console.log(post);
							var post_json_meta = JSON.parse(post.json_metadata);
							// console.log(post_json_meta.tags);
							
							//images: post_json_meta.image
							/* tests for inclusion */
							
							/* test 1: if checkbox to exclude resteems is not set, skip resteems */
							if (exclude_resteem != ''){
								if (post.author != current_author){
									// console.log('exclude_resteem:'+exclude_resteem);
									// console.log('post.author:'+post.author+'>current_author:'+current_author);
									//skip
									return true;
								}
							}
							
							/* test 2: checking if post has correct tag to be included */
							//tags: post_json_meta.tags
							if (filter_tag != ''){
								//if the tag is not to be found within the posts' tags, skip it
								if ($.inArray(filter_tag, post_json_meta.tags)<0){
									// console.log(filter_tag);
									// console.log(post_json_meta.tags);								
									//skip
									return true;
								}
							}
							
							//grab payout value as default, if the post has been paid
							var money_val = post.total_payout_value;
							//check if author rewards have been paid or not, if not we need to grab the pending amount alternatively
							var author_paid = (post.author_rewards>0?true:false);
							if (!author_paid){
								//if not, grab pending payout value
								money_val = post.pending_payout_value;
							}
							money_val = money_val.replace('SBD','$');
							money_val = money_val.replace('STEEM','$');
							
							/* test 3: checking if a min amount is set for inclusion, if so and post is less, skip */
							if (min_pay!=''){
								if (parseInt(min_pay)>0){
									tested_amount = money_val.replace('$','');
									if (parseInt(tested_amount)<parseInt(min_pay)){
										// console.log('found a lesser value, skip');
										// console.log('post amount:'+tested_amount+'>min amount:'+min_pay);
										//skip
										return true;
									}
								}
							}
							
							//create a new entry
							var entry = document.createElement('div');
							entry.setAttribute('class','steemit-post-entry');
							
							//grab the details of the post
							var post_details = '<a href="https://www.steemit.com'+post.url+'">'+post.title;
							
							//append vote count onto it
							// console.log(post.active_votes.length);
							post_details += '&nbsp; ('+post.active_votes.length+' <i class="fa fa-thumbs-up" aria-hidden="true"></i>)';
							
							//append money value onto it
							post_details += '&nbsp; ('+money_val+')';
							
							//close href
							post_details += '</a>';
							entry.innerHTML = post_details;
							//append it to the existing list
							container.appendChild(entry);
							//increase count of included posts
							included_post_count ++;
							if (included_post_count>=post_limit){
								//break out
								return false;
							}
						});
					}
				});
			});
	</script>
<?php
}

/*********************************************************************/


/* Creating widget handling steemit user info */
class steemit_user_info_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'steemit_user_info_widget',
		__('Steemit User Info Widget', 'gk_steemit_info'),
		array( 'description' => __( 'Widget Allowing Display of Steemit User Account info', 'gk_steemit_info' ), )
		);
	}
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'steemit_user_info_widget_title', $instance['title'] );
		$username = apply_filters( 'steemit_username_widget', $instance['steemit_username'] );
		//making room for hook display by any theme
		echo $args['before_widget'];
		if ( ! empty( $title ) ){
			echo $args['before_title'] . $title . $args['after_title'];
		}
		//widget container unique identifier based on timestamp
		$date = new DateTime();
		$contentid = $date->getTimestamp().mt_rand(1,4000);
		//display output in the widget
		steemit_user_info_renderer($username, $contentid);
		//making room for hook display by any theme
		echo $args['after_widget'];
	}
	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Steemit User Info', 'gk_steemit_info' );
		}
		if ( isset( $instance[ 'steemit_username' ] ) ) {
			$steemit_username = $instance[ 'steemit_username' ];
		}else{
			$steemit_username = "";
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_username' ); ?>">Steemit Username:</label>
		@<input type="text" class="text" id="<?php echo $this->get_field_id( 'steemit_username' ); ?>" name="<?php echo $this->get_field_name( 'steemit_username' ); ?>" value="<?php echo esc_attr( $steemit_username ); ?>"></p>
		<?php
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['steemit_username'] = ( ! empty( $new_instance['steemit_username'] ) ) ? $new_instance['steemit_username'] : '';
		return $instance;
	}
}

/* Register and load the widget*/
function gk_load_steemit_user_info_widget() {
    register_widget( 'steemit_user_info_widget' );
}
add_action( 'widgets_init', 'gk_load_steemit_user_info_widget' ); 


/* shortcode to display steemit user info on front end. 
Use it in format [steemit_user_info username=USERNAME] */
add_shortcode('steemit_user_info', 'display_steemit_user_info' );

function display_steemit_user_info( $atts, $content = "" ) {
	$username = $inner_atts['username'];
	//widget container unique identifier based on timestamp
	$date = new DateTime();
	$contentid = $date->getTimestamp().mt_rand(1,4000);
	steemit_user_info_renderer($username, $contentid);
}

/* function handling the display of the steemit users count widget */
function steemit_user_info_renderer($username, $contentid){
	global $libraries_appended;
	if (!$libraries_appended){
?>
		<!-- including fontawesome -->
		<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
		<!-- including steemjs library for performing calls -->
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<!-- including jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		
<?php
		$libraries_appended = true;
	}
?>
		<script>
		
			function grab_user_info(){
				jQuery(document).ready(function($){
					//fix for migration to api.steemit.com
					steem.api.setOptions({ url: 'https://api.steemit.com' });
					
					//function handling fetching of user related info
					steem.api.getAccounts(['<?php echo $username;?>'], function(err, result) {
						//result now contains the account details
						// console.log(err, result);
						//loop through each, we should have a single result either way
						$.each (result, function (index, userinfo){
							$('#account_name<?php echo $contentid;?>').html('<a href="https://www.steemit.com/@'+userinfo.name+'">@'+userinfo.name+'</a>');
							$('#post_count<?php echo $contentid;?>').text('Total Posts: '+gk_add_commas(userinfo.post_count)+' posts');
							//parse JSON content
							var user_json_meta = JSON.parse(userinfo.json_metadata);
							//display different content
							$('#user_img<?php echo $contentid;?>').attr('src',user_json_meta.profile.profile_image);
							(user_json_meta.profile.location!=""?$('#location<?php echo $contentid;?>').text('Location: '+user_json_meta.profile.location):'');
							(user_json_meta.profile.about!=""?$('#about<?php echo $contentid;?>').text('About: '+user_json_meta.profile.about):'');
							(user_json_meta.profile.website!=""?$('#website<?php echo $contentid;?>').html('Website: <a href="'+user_json_meta.profile.website+'">'+user_json_meta.profile.website+'</a>'):'');
							var account_balance = steem.formatter.estimateAccountValue(userinfo);
							//the result of fetching account value is a promise, so we need to wait for it to complete
							$.when(account_balance).done(function(arg){
								// console.log('done');
								// console.log(arg);
								$('#account_balance<?php echo $contentid;?>').text('Estimated Account Value: $'+gk_add_commas(parseFloat(arg).toFixed(2)));
							});
							// console.log(account_balance);
							
							//grab vesting values including delegated and received
							var vest_shares = userinfo.vesting_shares;
							var delg_vesting_shares = userinfo.delegated_vesting_shares;
							var recv_vesting_shares = userinfo.received_vesting_shares;
							
							//grab steem dynamic properties to allow converting vesting shares to STEEM Power
							steem.api.getDynamicGlobalProperties(function(err, result) {
								//as we receive the result, we need total_vesting_shares and total_vesting_fund_steem to convert vesting to STEEM, as follows
								// console.log(result);
								var steem_power = steem.formatter.vestToSteem(vest_shares, result.total_vesting_shares, result.total_vesting_fund_steem);
								$('#steem_power<?php echo $contentid;?>').text('Own STEEM Power: '+gk_add_commas(parseFloat(steem_power).toFixed(2))+' SP');
								var delg_steem_power = steem.formatter.vestToSteem(delg_vesting_shares, result.total_vesting_shares, result.total_vesting_fund_steem);
								var recv_steem_power = steem.formatter.vestToSteem(recv_vesting_shares, result.total_vesting_shares, result.total_vesting_fund_steem);
								var effc_steem_power = steem_power - delg_steem_power + recv_steem_power;
								if (delg_steem_power>0){
									$('#delg_steem_power<?php echo $contentid;?>').text('Delegated STEEM Power: -'+gk_add_commas(parseFloat(delg_steem_power).toFixed(2))+' SP');
								}
								if (recv_steem_power>0){
									$('#recv_steem_power<?php echo $contentid;?>').text('Received STEEM Power: '+gk_add_commas(parseFloat(recv_steem_power).toFixed(2))+' SP');
								}
								$('#effc_steem_power<?php echo $contentid;?>').text('Effective STEEM Power: '+gk_add_commas(parseFloat(effc_steem_power).toFixed(2))+' SP');
							});
							
							
							$('#steem<?php echo $contentid;?>').text('STEEM Balance: '+gk_add_commas(userinfo.balance.replace(' STEEM',''))+' STEEM');
							$('#sbd<?php echo $contentid;?>').text('SBD Balance: '+gk_add_commas(userinfo.sbd_balance.replace(' SBD',''))+' SBD');
							
							$('#voting_power<?php echo $contentid;?>').text('Voting Power: '+(parseInt(userinfo.voting_power)/100)+'%');
							$('#reputation<?php echo $contentid;?>').text('Reputation: '+steem.formatter.reputation(userinfo.reputation));
							
						});
					});

				});
			}
			//first call
			grab_user_info();
			//subsequent recurring calls. Make this update every 30 secs. Not much would change within this timeframe if the page was left loading
			setInterval(grab_user_info, 30000);
		</script>
		<div class="steemit_user_info">
			<div id="account_name<?php echo $contentid;?>"></div>
			<img id="user_img<?php echo $contentid;?>" class="steemit_user_img">
			<div id="about<?php echo $contentid;?>"></div>
			<div id="location<?php echo $contentid;?>"></div>
			<div id="website<?php echo $contentid;?>"></div>
			<div id="post_count<?php echo $contentid;?>"></div>
			<div id="user_posts<?php echo $contentid;?>"></div>
			<div id="steem_power<?php echo $contentid;?>"></div>
			<div id="delg_steem_power<?php echo $contentid;?>"></div>
			<div id="recv_steem_power<?php echo $contentid;?>"></div>
			<div id="effc_steem_power<?php echo $contentid;?>"></div>
			<div id="steem<?php echo $contentid;?>"></div>
			<div id="sbd<?php echo $contentid;?>"></div>
			<div id="voting_power<?php echo $contentid;?>"></div>
			<div id="reputation<?php echo $contentid;?>"></div>
			<div id="account_balance<?php echo $contentid;?>"></div>
		</div>
<?php
}
?>