<?php
/*
  Plugin Name: GK Steemit Info
  Plugin URI: http://www.greateck.com/
  Description: A wordpress plugin that allows adding steem(it) (www.steemit.com) data to wordpress sites via widget or alternatively a shortcode
  Version: 0.7.0
  Author: mcfarhat
  Author URI: http://www.greateck.com
  License: GPLv2
  License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Add new menu item for GK Steemit Info which would contain any other needed submenus
 */
function gk_steemit_add_info_menu_item(){
	add_menu_page( 
		__( 'GK Steemit Info', 'gk-steemit-info' ),
		'GK Steemit Info',
		'manage_options',
		'create_steemit_user',
		'create_steemit_user_handler',
		plugins_url( 'gk-steemit-info/img/steem-logo.png' ),
		6
	); 
}
add_action( 'admin_menu', 'gk_steemit_add_info_menu_item' );



/* add support for some cool jquery effects */

function gk_steemit_info_scripts_enq(){
	//adding jquery cool effects
	wp_enqueue_script( 'jquery-effects-core' );
	wp_enqueue_script( 'jquery-effects-highlight' );
	
	//adding plugin's stylesheet file for front end use
	wp_register_style('gk_steemit_info_reg', plugins_url('style.css',__FILE__ ));
    wp_enqueue_style('gk_steemit_info_reg');	
}

add_action( 'wp_enqueue_scripts', 'gk_steemit_info_scripts_enq' );

/* enqueuing style.css file for admin section */
function gk_steemit_info_reg(){
	wp_register_style('gk_steemit_info_reg', plugins_url('style.css',__FILE__ ));
    wp_enqueue_style('gk_steemit_info_reg');
}

add_action( 'admin_init','gk_steemit_info_reg');

/**
 * Display content of create steemit user page
 */

function create_steemit_user_handler(){
	?>
	<head>
	<!-- including steemjs library for performing calls -->
	<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
	<script>
		jQuery(document).ready(function($){
			/* hook create button click to account creation function */
			$('#proceed_creation').click(function(){
				//disable button to prevent multiple creation
				$('#proceed_creation').attr("disabled", "disabled");
				//show loader
				$('#proceed_creation_img').show();
				//clear errors
				$('#error_message').html('');
				//set the proper steemit node to be used, otherwise default steem-js node will fail
				steem.api.setOptions({ url: 'https://api.steemit.com' });
				//grab new user account name
				var new_account = $('#new_account').val();
				//grab password
				var wif = $('#new_account_wif').val();
				//grab WIF of the user creating the account
				var owner_wif = $('#owner_wif').val();
				//grab name of user creating the account
				var owner_account = $('#owner_account').val();
				//grab fee amount in STEEM
				var fee = $('#fee_amount').val()+" STEEM";
				//grab VESTS amount
				var delegation = $('#delegation_amount').val() + " VESTS";
				//meta data and extensions can be left blank for now
				var jsonMetadata = "";
				var extensions = "";
				/********************** process ****************************/
				console.log('attempting creation of account:'+new_account);
				//make sure account name is valid
				var account_invalid = steem.utils.validateAccountName(new_account);
				if (account_invalid == null){
					//make sure account does not already exist
					steem.api.getAccounts([new_account], function(err, result) {
						console.log(err, result);
						//no matches found
						if (result.length==0){
							/* if the code doesn't work, you might need to uncomment this, and change the wif at the top to password */
							//var wif = steem.auth.toWif(new_account, pass, 'owner');
							//generate the keys based on the account name and password
							var publicKeys = steem.auth.generateKeys(new_account, wif, ['owner', 'active', 'posting', 'memo']);
							var owner = { weight_threshold: 1, account_auths: [], key_auths: [[publicKeys.owner, 1]] };
							var active = { weight_threshold: 1, account_auths: [], key_auths: [[publicKeys.active, 1]] };
							var posting = { weight_threshold: 1, account_auths: [], key_auths: [[publicKeys.posting, 1]] };
							//console.log(posting);
							console.log(delegation);
							steem.broadcast.accountCreateWithDelegation(owner_wif, fee, delegation, owner_account, new_account, owner, active, posting, publicKeys.memo, jsonMetadata, extensions, function(err, result) {
								if (err != null){
									$('#error_message').html('Creating account '+new_account+' result: ' +err);
								}else{
									$('#error_message').html('Creating account '+new_account+' result: ' +result);
								}
								console.log(err, result);
							});
							/*steem.broadcast.accountCreate(owner_wif, fee, owner_account, new_account, owner, active, posting, publicKeys.memo, jsonMetadata, function(err, result) {
							  console.log(err, result);
							});*/
						}else{
							$('#error_message').html('Account '+new_account+' already exists');
							console.log('Account '+new_account+' already exists');
						}
					});
				}else{
					$('#error_message').html(account_invalid);
					console.log(account_invalid);
				}
				//hide loader
				$('#proceed_creation_img').hide();
				//reneable button
				$('#proceed_creation').removeAttr('disabled');
					
			});
			
		});
	</script>
	</head>
	<div class="wrap"><!--full page container-->
		<h1><?php esc_html_e( 'Create New Steemit User', 'gk_steemit_info' );?></h1>
		<div id="steem_create_container" name="steem_create_container">
			<div class="row"><span class="entry_label"><label for="new_account">New Account Name</label></span>@<input id="new_account" name="new_account" type="text"></div>
			<div class="row"><span class="entry_label"><label for="new_account_wif">New Account Password</label></span><input id="new_account_wif" name="new_account_wif" type="password" size="50"><div><i>Suggest using <a href="http://passwordsgenerator.net/">http://passwordsgenerator.net/</a> for password generation, and set a min size of 50 chars. DO NOT INCLUDE symbols as those are invalid. Only combination of upper and lower case letters & numbers</i></div></div>
			<div class="row"><span class="entry_label"><label for="owner_account">Owner Account Name</label></span>@<input id="owner_account" name="owner_account" type="text"><span id="owner_account_error" class="error_display"></span></div>
			<div class="row"><span class="entry_label"><label for="owner_wif">Owner WIF/Private Key</label></span><input id="owner_wif" name="owner_wif" type="password" size="50"><span id="owner_wif_error" class="error_display"></span></div>
			<div class="row"><span class="entry_label"><label for="fee_amount">Fee (in STEEM)</label></span><input id="fee_amount" name="fee_amount" type="number" value="0.200"> STEEM <i>(Amount will be passed to the new account. Suggested min 0.200 STEEM)</i><span id="fee_amount_error" class="error_display"></span></div>
			<div class="row"><span class="entry_label"><label for="delegation_amount">Delegation (in VESTS)</label></span><input id="delegation_amount" name="delegation_amount" type="number" value="30663.815330"> VESTS <i>(default value 30663.815330 VESTS equates to 15 SP)</i><span id="delegation_amount_error" class="error_display"></span></div>
			<div id="error_message" class="row"></div>
			<div class="row"><input type="button" id="proceed_creation" value="Create"><img id="proceed_creation_img" class="gk-loader-img" src="<?php echo plugins_url();?>/gk-steemit-info/img/ajax-loader.gif"></div>
		</div>
	<div><!--wrap-->
	<?php
}

/* handles tracking if required libraries have been added already */
global $libraries_appended;
$libraries_appended = false; 
 
//function to include required JS adjustments
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

//hook ito wp_head to append JS and CSS functionality
add_action('wp_head', 'include_js_func');
 
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

function display_steemit_user_count( $inner_atts, $content = "" ) {
	$inner_atts = shortcode_atts( array(
        'refresh_frequency' => 5000,
    ), $inner_atts );
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
									var content = '<b>STEEM/USD:</b> <br/>$';
									content += grab_proper_content(data);
									$('#steem_price').html(content);
								}
							});
							//grab SBD values
							$.ajax({
								url: 'https://api.coinmarketcap.com/v1/ticker/steem-dollars/',
								dataType: 'json',
								success: function (data) { 
									// console.log(parseFloat(data[0].price_usd).toFixed(2));
									var content = '<b>SBD/USD:</b> <br/> $'
									content += grab_proper_content(data);
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
					//common function handling grabbing data from coinmarketcap and formatting it properly
					function grab_proper_content(data){
						var content = parseFloat(data[0].price_usd).toFixed(2);
						content += '<div class="gk_steemit_add_info"> ';
						if (parseFloat(data[0].percent_change_1h) > 0){
							content += ' <span class="green-color">';
							content += ' 1h: <i class="fas fa-arrow-up"></i></span>';
						}else{
							content += ' <span class="red-color">';
							content += ' 1h: <i class="fas fa-arrow-down"></i></span>';
						}
						if (parseFloat(data[0].percent_change_24h) > 0){
							content += ' <span class="green-color">';
							content += ' 24h: <i class="fas fa-arrow-up"></i></span>';
						}else{
							content += ' <span class="red-color">';
							content += ' 24h: <i class="fas fa-arrow-down"></i></span>';
						}
						if (parseFloat(data[0].percent_change_7d) > 0){
							content += ' <span class="green-color">';
							content += ' 7d: <i class="fas fa-arrow-up"></i></span>';
						}else{
							content += ' <span class="red-color">';
							content += ' 7d: <i class="fas fa-arrow-down"></i></span>';
						}
						content += ' Rank: '+data[0].rank;
						content += '</div>';
						return content;
					}
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
			<br/>
			<div><i>Your voice is worth something. Join the community that pays you to post and curate high quality content.<br/>
			Check out <a href="https://www.steemit.com">Steemit.com</a></i></div>
			<span class="coinmarketcap-ref-info"><i>Current Prices via <a href="https://coinmarketcap.com/">CoinMarketCap.com</a> API</i></span>
		</div>
<?php
}


/**************************************************************/

/* Creating widget handling steemit user posts */
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
			$title = __( 'Posts On Steemit', 'gk_steemit_user_posts' );
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

function display_steemit_user_posts( $inner_atts, $content = "" ) {
	/*$inner_atts = shortcode_atts( array(
        'refresh_frequency' => 5000,
    ), $inner_atts );*/
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
						
							//grab post's json_meta
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
							var post_details = '<a href="https://www.steemit.com'+post.url+'">'+post.title+'</a>';
							
							//add container div for proper formatting
							post_details += '<div class="gk_steemit_add_info">';
							
							//append vote count onto it
							// console.log(post.active_votes.length);
							post_details += '&nbsp; '+post.active_votes.length+' <i class="fa fa-thumbs-up" aria-hidden="true"></i>';
							
							//append money value onto it
							post_details += '&nbsp; '+money_val+'';
							
							//close href
							post_details += '</div>';
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

function display_steemit_user_info( $inner_atts, $content = "" ) {
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
								
								//grab steem value
								$.ajax({
									url: 'https://api.coinmarketcap.com/v1/ticker/steem/',
									dataType: 'json',
									success: function (data) { 
										// console.log(parseFloat(data[0].price_usd).toFixed(2));
										var steem_price = parseFloat(data[0].price_usd).toFixed(2);
										//grab SBD values
										$.ajax({
											url: 'https://api.coinmarketcap.com/v1/ticker/steem-dollars/',
											dataType: 'json',
											success: function (data) { 
												// console.log(parseFloat(data[0].price_usd).toFixed(2));
												var sbd_price = parseFloat(data[0].price_usd).toFixed(2);
												
												var realtime_balance = (parseFloat(steem_power)+parseFloat(userinfo.balance.replace(' STEEM',''))) * steem_price
																		+ parseFloat(userinfo.sbd_balance.replace(' SBD','')) * sbd_price;
												$('#realtime_balance<?php echo $contentid;?>').text('Real Time Account Value: $'+gk_add_commas(realtime_balance.toFixed(2)));
											}
										});
									}
								});
								
							});
							
							
							$('#steem<?php echo $contentid;?>').text('STEEM Balance: '+gk_add_commas(userinfo.balance.replace(' STEEM',''))+' STEEM');
							$('#sbd<?php echo $contentid;?>').text('SBD Balance: '+gk_add_commas(userinfo.sbd_balance.replace(' SBD',''))+' SBD');
							
							$('#voting_power<?php echo $contentid;?>').text('Voting Power: '+(parseInt(userinfo.voting_power)/100)+'%');
							$('#reputation<?php echo $contentid;?>').text('Reputation: '+steem.formatter.reputation(userinfo.reputation));
							
							//grab and display follower and following count
							steem.api.getFollowCount('<?php echo $username;?>', function(err, result) {
								//console.log(err, result);
								if (!err) {
									$('#followers<?php echo $contentid;?>').text('Followers: '+result.follower_count);
									$('#following<?php echo $contentid;?>').text('Following: '+result.following_count);
								}
							});
							
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
			<div id="followers<?php echo $contentid;?>"></div>
			<div id="following<?php echo $contentid;?>"></div>			
			<div id="account_balance<?php echo $contentid;?>"></div>
			<div id="realtime_balance<?php echo $contentid;?>"></div>
		</div>
<?php
}

/**************************************************************/

/* Creating widget handling steemit user count */
class steemit_trending_posts_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'steemit_trending_posts_widget',
		__('Steemit Trending Posts Widget', 'gk_steemit_info'),
		array( 'description' => __( 'Widget Allowing Display of Trending Posts while also providing filtering criteria', 'gk_steemit_info' ), )
		);
	}
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'steemit_trending_posts_widget_title', $instance['title'] );
		
		//making room for hook display by any theme
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
			
		//grab username and post count params
		$postcount = apply_filters( 'steemit_post_count_widget', $instance['steemit_post_count'] );
		$posttag = apply_filters( 'steemit_post_tag_widget', $instance['steemit_post_tag'] );
		
		//widget container unique identifier based on timestamp
		$date = new DateTime();
		$contentid = $date->getTimestamp().mt_rand(1,4000);
		//display output in the widget
		steemit_trending_posts_renderer($postcount, $posttag, $contentid);
		
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
			$title = __( 'Trending Posts On Steemit', 'gk_steemit_trending_posts' );
		}
		$steemit_post_count = "";
		$steemit_post_tag = "";
		if ( isset( $instance[ 'steemit_post_count' ] ) ) {
			$steemit_post_count = $instance[ 'steemit_post_count' ];
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
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_count' ); ?>">Max Post Count:</label>
		<input type="number" class="text" id="<?php echo $this->get_field_id( 'steemit_post_count' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_count' ); ?>" step="1" min="1" max="50" value="<?php echo esc_attr( $steemit_post_count ); ?>"></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_tag' ); ?>">Filter by Tag:</label>
		<input type="text" class="text" id="<?php echo $this->get_field_id( 'steemit_post_tag' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_tag' ); ?>" value="<?php echo esc_attr( $steemit_post_tag ); ?>"></p>
		<?php
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['steemit_post_count'] = ( ! empty( $new_instance['steemit_post_count'] ) ) ? $new_instance['steemit_post_count'] : '';
		$instance['steemit_post_tag'] = ( ! empty( $new_instance['steemit_post_tag'] ) ) ? $new_instance['steemit_post_tag'] : '';
		return $instance;
	}
}

/* Register and load the widget*/
function gk_load_steemit_trending_posts_widget() {
    register_widget( 'steemit_trending_posts_widget' );
}
add_action( 'widgets_init', 'gk_load_steemit_trending_posts_widget' ); 
 
/* shortcode to display steemit trending posts on front end. 
Use it in format [steemit_trending_posts limit=LIMIT filtertag=TAG] */
add_shortcode('steemit_trending_posts', 'display_steemit_trending_posts' );

function display_steemit_trending_posts( $inner_atts, $content = "" ) {
	$postcount = $inner_atts['limit'];
	$posttag = $inner_atts['filtertag'];
	if (empty($posttag)){
		$posttag = '';
	}
	//widget container unique identifier based on timestamp
	$date = new DateTime();
	$contentid = $date->getTimestamp().mt_rand(1,4000);;
	steemit_trending_posts_renderer($postcount, $posttag, $contentid);
}

/* function handling the display of the selected users' posts */
function steemit_trending_posts_renderer($postcount, $posttag, $contentid){
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

	<div>
		<div id="trending_posts_container<?php echo $contentid;?>">
			<!-- default loader -->
			<img class="gk-loader-img" src="<?php echo plugins_url();?>/gk-steemit-info/img/ajax-loader.gif">
		</div>
		
	</div>	
	<script>
			/* when properly loaded, call steem API method to grab recent posts by selected username and display them */
			jQuery(document).ready(function($){
				//fix for migration to api.steemit.com
				steem.api.setOptions({ url: 'https://api.steemit.com' });
				
				//setup query to grab proper posts with limit and/or tag
				var query = {
					<?php 
					//no need to include tag if not set
					if ($posttag!=''){?>
					tag: '<?php echo $posttag;?>',
					<?php } ?>
					limit: <?php echo $postcount;?>,
				};
				var container = document.getElementById('trending_posts_container<?php echo $contentid;?>');
				//call getDiscussionsByTrending to grab trending posts
				steem.api.getDiscussionsByTrending(query, function(err, posts) {
					// console.log(err, discussions);
					if (!err) {
						//remove loader / empty container
						container.innerHTML="";
						
						/* replacing map with each to allow breaking out */
						$.each (posts, function (index, post){
						//posts.map(function (post) {
							// console.log(post);
							var post_json_meta = JSON.parse(post.json_metadata);
							// console.log(post_json_meta.tags);
							
							//images: post_json_meta.image
							
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
							
							//create a new entry
							var entry = document.createElement('div');
							entry.setAttribute('class','steemit-post-entry');
							
							//grab the details of the post
							var post_details = '<a href="https://www.steemit.com'+post.url+'">'+post.title+'</a>';
							
							//add link to author name
							post_details += '<br><a class="gk_steemit_author_name" href="https://www.steemit.com/@'+post.author+'">@'+post.author+'</a>';
							
							post_details += '<div class="gk_steemit_add_info">';
							//append vote count onto it
							// console.log(post.active_votes.length);
							post_details += '&nbsp; '+post.active_votes.length+' <i class="fa fa-thumbs-up" aria-hidden="true"></i>';
							
							//append money value onto it
							post_details += '&nbsp; '+money_val+'';
							
							//close href
							post_details += '</div>';
							entry.innerHTML = post_details;
							//append it to the existing list
							container.appendChild(entry);

						});
					}
				});
			});
	</script>
<?php
}

/**************************************************************/

/* Creating widget handling display of steemit user voted posts */
class steemit_user_voted_posts_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'steemit_user_voted_posts_widget',
		__('Steemit User Voted Posts Widget', 'gk_steemit_info'),
		array( 'description' => __( 'Widget Allowing Display of User Voted Posts', 'gk_steemit_info' ), )
		);
	}
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'steemit_user_voted_posts_widget_title', $instance['title'] );
		
		//making room for hook display by any theme
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
			
		//grab username and post count params
		$username = apply_filters( 'steemit_username_widget', $instance['steemit_username'] );
		$postcount = apply_filters( 'steemit_post_count_widget', $instance['steemit_post_count'] );
		
		//widget container unique identifier based on timestamp
		$date = new DateTime();
		$contentid = $date->getTimestamp().mt_rand(1,4000);
		//display output in the widget
		steemit_user_voted_posts_renderer($username, $postcount, $contentid);
		
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
			$title = __( 'User Voted Posts On Steemit', 'gk_steemit_user_posts' );
		}
		$steemit_username = "";
		$steemit_post_count = "";
		if ( isset( $instance[ 'steemit_username' ] ) ) {
			$steemit_username = $instance[ 'steemit_username' ];
		}
		if ( isset( $instance[ 'steemit_post_count' ] ) ) {
			$steemit_post_count = $instance[ 'steemit_post_count' ];
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
		<?php
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['steemit_username'] = ( ! empty( $new_instance['steemit_username'] ) ) ? $new_instance['steemit_username'] : '';
		$instance['steemit_post_count'] = ( ! empty( $new_instance['steemit_post_count'] ) ) ? $new_instance['steemit_post_count'] : '';
		return $instance;
	}
}

/* Register and load the widget*/
function gk_load_steemit_user_voted_posts_widget() {
    register_widget( 'steemit_user_voted_posts_widget' );
}
add_action( 'widgets_init', 'gk_load_steemit_user_voted_posts_widget' ); 
 
/* shortcode to display steemit user count on front end. 
Use it in format [steemit_user_voted_posts username=USERNAME limit=LIMIT] */
add_shortcode('steemit_user_voted_posts', 'display_steemit_user_voted_posts' );

function display_steemit_user_voted_posts( $inner_atts, $content = "" ) {
	//grab values
	$username = $inner_atts['username'];
	$postcount = $inner_atts['limit'];
	//widget container unique identifier based on timestamp
	$date = new DateTime();
	$contentid = $date->getTimestamp().mt_rand(1,4000);
	//call rendering function
	steemit_user_voted_posts_renderer($username, $postcount, $contentid);
}

/* function handling the display of the selected users' posts */
function steemit_user_voted_posts_renderer($username, $postcount, $contentid){
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

	<div>
		<div id="user_voted_posts_container<?php echo $contentid;?>">
			<!-- default loader -->
			<img class="gk-loader-img" src="<?php echo plugins_url();?>/gk-steemit-info/img/ajax-loader.gif">
		</div>
		
	</div>	
	<script>
			/* when properly loaded, call steem API method to grab recent voted posts by selected username and display them */
			jQuery(document).ready(function($){
				//fix for migration to api.steemit.com
				steem.api.setOptions({ url: 'https://api.steemit.com' });
				
				//grab account name
				var user_account = '<?php echo $username;?>';
					
				var container = document.getElementById('user_voted_posts_container<?php echo $contentid;?>');
				//call getAccountVotes to grab user's latest voted posts
				steem.api.getAccountVotes(user_account, function (err, posts) {
					// console.log(err, discussions);
					if (!err) {
						var post_limit = <?php echo $postcount;?>;
						
						//remove loader / empty container
						container.innerHTML="";
						
						//reverse the array to display most recent votes first
						posts = posts.reverse();
						
						//the actual max number of posts to be displayed - making sure the user has enough votes to display
						var top_limit = (posts.length > post_limit)?post_limit:posts.length;
							
						/* loop through the voted posts and only display up to set limit */
						for (var i=0;i<top_limit;i++){
							// console.log(posts[i]);
							//create a new entry
							var entry = document.createElement('div');
							entry.setAttribute('class','steemit-post-entry');
							
							//grab the details of the post
							var post_details = '<a href="https://www.steemit.com/@'+posts[i].authorperm+'">'+posts[i].authorperm+'</a>';
							
							//add details about the author
							var post_author = posts[i].authorperm.split('/')[0];
							post_details += '<br><a class="gk_steemit_author_name" href="https://www.steemit.com/@'+post_author+'">@'+post_author+'</a>';
							
							//add additional details here
							post_details += '<div class="gk_steemit_add_info">';
							
							//append vote percentage onto it
							post_details += '&nbsp; '+parseInt(posts[i].percent)/100+'% ';
							
							//close href
							post_details += '</div>';
							
							entry.innerHTML = post_details;
							//append it to the existing list
							container.appendChild(entry);
							
						}
					}
				});
			});
	</script>
<?php
}

/*********************************************************************/

/* Creating widget handling steemit tag filtered with focus on voters */
class steemit_tag_voted_posts_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'steemit_tag_voted_posts_widget',
		__('Steemit Tag Filtered Voted Posts Widget', 'gk_steemit_info'),
		array( 'description' => __( 'Widget Allowing Display of Specific Posts Based on Tags and Upvoters', 'gk_steemit_info' ), )
		);
	}
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title = apply_filters( 'steemit_tag_voted_posts_widget_title', $instance['title'] );
		
		//making room for hook display by any theme
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
			
		//grab post count params
		$postcount = apply_filters( 'steemit_post_count_widget', $instance['steemit_post_count'] );
		$posttag = apply_filters( 'steemit_post_tag_widget', $instance['steemit_post_tag'] );
		$postvoters = apply_filters( 'steemit_post_voters_widget', $instance['steemit_post_voters_tag'] );
		$restrictvotedposts = apply_filters( 'steemit_restrict_voted_posts', $instance['steemit_restrict_voted_posts'] ) ? true : false ;
		$postexcludevoters = apply_filters( 'steemit_post_excluded_voters_tag', $instance['steemit_post_excluded_voters_tag'] );
		$allowfrontendfilter = apply_filters( 'steemit_allow_frontend_filter', $instance['steemit_allow_frontend_filter'] ) ? true : false ;
		$iswidget = 1;
		
		//set as false by default
		if ($restrictvotedposts==''){
			$restrictvotedposts = false;
		}
		
		//widget container unique identifier based on timestamp
		$date = new DateTime();
		$contentid = $date->getTimestamp().mt_rand(1,4000);
		//display output in the widget
		steemit_tag_voted_posts_renderer($posttag, $postcount, $postvoters, $restrictvotedposts, $postexcludevoters, $allowfrontendfilter, $contentid, $iswidget);
		
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
			$title = __( 'Tag Posts Steemit', 'gk_steemit_tag_voted_posts' );
		}
		
		$steemit_post_tag = "";
		$steemit_post_count = "";
		$steemit_post_voters_tag = "";
		$steemit_restrict_voted_posts = "";
		$steemit_post_excluded_voters_tag = "";
		$steemit_allow_frontend_filter = "";

		if ( isset( $instance[ 'steemit_post_count' ] ) ) {
			$steemit_post_count = $instance[ 'steemit_post_count' ];
		}
		if ( isset( $instance[ 'steemit_post_voters_tag' ] ) ) {
			$steemit_post_voters_tag = $instance[ 'steemit_post_voters_tag' ];
		}
		if ( isset( $instance[ 'steemit_post_excluded_voters_tag' ] ) ) {
			$steemit_post_excluded_voters_tag = $instance[ 'steemit_post_excluded_voters_tag' ];
		}
		if ( isset( $instance[ 'steemit_restrict_voted_posts' ] ) ) {
			$steemit_restrict_voted_posts = $instance[ 'steemit_restrict_voted_posts' ];
		}		
		if ( isset( $instance[ 'steemit_post_tag' ] ) ) {
			$steemit_post_tag = $instance[ 'steemit_post_tag' ];
		}
		if ( isset( $instance[ 'steemit_allow_frontend_filter' ] ) ) {
			$steemit_allow_frontend_filter = $instance[ 'steemit_allow_frontend_filter' ];
		}
		
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_count' ); ?>">Max Post Count:</label>
		<input type="number" class="text" id="<?php echo $this->get_field_id( 'steemit_post_count' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_count' ); ?>" step="1" min="1" max="50" value="<?php echo esc_attr( $steemit_post_count ); ?>"></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_tag' ); ?>">Filter by Tag:</label>
		<input type="text" class="text" id="<?php echo $this->get_field_id( 'steemit_post_tag' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_tag' ); ?>" value="<?php echo esc_attr( $steemit_post_tag ); ?>"></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_voters_tag' ); ?>">Voters:</label>
		<input type="text" class="text" id="<?php echo $this->get_field_id( 'steemit_post_voters_tag' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_voters_tag' ); ?>" value="<?php echo esc_attr( $steemit_post_voters_tag ); ?>"></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_restrict_voted_posts' ); ?>">Only Include Voted Posts:</label>
		<input type="checkbox" id="<?php echo $this->get_field_id( 'steemit_restrict_voted_posts' ); ?>" name="<?php echo $this->get_field_name( 'steemit_restrict_voted_posts' ); ?>" <?php if ($steemit_restrict_voted_posts){ echo "checked";}?>></p>
		<?php /*<p><label for="<?php echo $this->get_field_id( 'steemit_post_excluded_voters_tag' ); ?>">Exclude Voters:</label>
		<input type="text" class="text" id="<?php echo $this->get_field_id( 'steemit_post_excluded_voters_tag' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_excluded_voters_tag' ); ?>" value="<?php echo esc_attr( $steemit_post_excluded_voters_tag ); ?>"></p>*/?>
		<p><label for="<?php echo $this->get_field_id( 'steemit_allow_frontend_filter' ); ?>">Allow Front End Filtering:</label>
		<input type="checkbox" id="<?php echo $this->get_field_id( 'steemit_allow_frontend_filter' ); ?>" name="<?php echo $this->get_field_name( 'steemit_allow_frontend_filter' ); ?>" <?php if ($steemit_allow_frontend_filter){ echo "checked";}?>></p>
		<?php
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['steemit_post_tag'] = ( ! empty( $new_instance['steemit_post_tag'] ) ) ? $new_instance['steemit_post_tag'] : '';
		$instance['steemit_post_count'] = ( ! empty( $new_instance['steemit_post_count'] ) ) ? $new_instance['steemit_post_count'] : '';
		$instance['steemit_post_voters_tag'] = ( ! empty( $new_instance['steemit_post_voters_tag'] ) ) ? $new_instance['steemit_post_voters_tag'] : '';
		$instance['steemit_restrict_voted_posts'] = $new_instance['steemit_restrict_voted_posts'];
		$instance['steemit_post_excluded_voters_tag'] = ( ! empty( $new_instance['steemit_post_excluded_voters_tag'] ) ) ? $new_instance['steemit_post_excluded_voters_tag'] : '';
		$instance['steemit_allow_frontend_filter'] = ( ! empty( $new_instance['steemit_allow_frontend_filter'] ) ) ? true : false;
		return $instance;
	}
}

/* Register and load the widget*/
function gk_load_steemit_tag_voted_posts_widget() {
    register_widget( 'steemit_tag_voted_posts_widget' );
}
add_action( 'widgets_init', 'gk_load_steemit_tag_voted_posts_widget' ); 
 
/* shortcode to display steemit user count on front end. 
Use it in format [steemit_tag_voted_posts filtertag=TAG limit=LIMIT voters=VOTER1,VOTER2 restrictvotedonly=0 excludevoters=VOTER1,VOTER2 showfilters=0] */
add_shortcode('steemit_tag_voted_posts', 'display_steemit_tag_voted_posts' );

function display_steemit_tag_voted_posts( $inner_atts, $content = "" ) {
	/*$inner_atts = shortcode_atts( array(
        'refresh_frequency' => 5000,
    ), $inner_atts );*/
	
	//grab the different sent params, making sure to provide default values for params that can be skipped
	$posttag = isset($inner_atts['filtertag']) ? $inner_atts['filtertag'] : '';
	$postcount = isset($inner_atts['limit']) ? $inner_atts['limit'] : '10';
	$postvoters = isset($inner_atts['voters']) ? $inner_atts['voters'] : '';
	$restrictvotedposts = isset($inner_atts['restrictvotedonly']) ? $inner_atts['restrictvotedonly'] : false;
	$postexcludevoters = isset($inner_atts['excludevoters']) ? $inner_atts['excludevoters'] : '';
	$allowfrontendfilter = isset($inner_atts['showfilters']) ? $inner_atts['showfilters'] : false;
	$iswidget = 0;
	
	if (empty($posttag)){
		$posttag = '';
	}
	
	//widget container unique identifier based on timestamp
	$date = new DateTime();
	$contentid = $date->getTimestamp().mt_rand(1,4000);
	steemit_tag_voted_posts_renderer($posttag, $postcount, $postvoters, $restrictvotedposts, $postexcludevoters, $allowfrontendfilter, $contentid, $iswidget);
}

/* function handling the display of the selected users' posts */
function steemit_tag_voted_posts_renderer($posttag, $postcount, $postvoters, $restrictvotedposts, $postexcludevoters, $allowfrontendfilter, $contentid, $iswidget){

	//if postcount not properly provided and within 1 - 100, default to 10
	if (!is_numeric ($postcount) || (is_numeric($postcount) && ($postcount<1 || $postcount>100))){
		$postcount = 10;
	}
	//avoiding pointless re-inclusion of libraries if already included
	global $libraries_appended;
	if (!$libraries_appended){
?>
		<!-- including fontawesome -->
		<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
		<!-- including steemjs library for performing steem related calls -->
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<!-- including jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>	
<?php
		$libraries_appended = true;
	}
?>
	<div>
<?php
		//if front end filtering is enabled, display filtering criteria
		
		if ($allowfrontendfilter){
			//TODO: Preparation for upcoming functionality, to be implemented
			//echo '>Filter on';
		}
?>
		<div id="tag_voted_posts_container<?php echo $contentid;?>">
			<!-- default loader -->
			<img class="gk-loader-img" src="<?php echo plugins_url();?>/gk-steemit-info/img/ajax-loader.gif">
		</div>
		
	</div>	
	<script>
			/* when properly loaded, call steem API method to grab created posts by selected tag and additional params and display them */
			jQuery(document).ready(function($){
				//fix for migration to api.steemit.com
				steem.api.setOptions({ url: 'https://api.steemit.com' });

				//setup query to grab proper posts with limit and/or tag
				var query = {
					<?php 
					//no need to include tag if not set
					if ($posttag!=''){?>
					tag: '<?php echo $posttag;?>',
					<?php } ?>
					limit: <?php echo $postcount;?>,
				};
				
				var container = document.getElementById('tag_voted_posts_container<?php echo $contentid;?>');
				//call getDiscussionsByCreated to grab steemit latest posts by tag
				steem.api.getDiscussionsByCreated(query, function (err, posts) {
					console.log(err, posts);
					if (!err) {
					
						//grab passed vals to be used in JS
						// var included_post_count = 0;
						var post_limit = <?php echo $postcount;?>;
						var filter_tag = '<?php echo $posttag; ?>';
						
						//remove loader / empty container
						container.innerHTML="";
						
						/* loop through all results */
						$.each (posts, function (index, post){
						//posts.map(function (post) {
							// console.log(post);
							var post_json_meta = JSON.parse(post.json_metadata);
							// console.log(post_json_meta.tags);
							
							/* check if any of the post's voters are part of our selection to highlight them */
							//convert list of voters to array
							var voters_list = <?php echo json_encode(explode(",",$postvoters));?>;
							//contains the list of matching voters that we should display
							var displayable_voters = [];
							// console.log(voters_list);
							//go through list and check which items match the post's voters, if any
							$.each(voters_list, function(voter_index, voter_name){
								//another loop to check the voters list
								$.each(post.active_votes, function(post_v_index, post_voter){
									//found match
									if (post_voter.voter == voter_name){
										//add the guy to the list of voters to be displayed
										displayable_voters.push(voter_name);
										//bail out
										return false;
									}
								});
							});
							// console.log(displayable_voters);
							//if the setting for restricting to voted posts is on, we need to make sure we have at least one matching post, otherwise do not include the post
							var skip_unvoted_posts = '<?php echo $restrictvotedposts;?>';
							console.log('skip_unvoted_posts:'+skip_unvoted_posts);
							//if on and no matches, bail
							if (skip_unvoted_posts && displayable_voters.length<1){
								//continue to next element
								return true;
							}
							
							/* below still needs to be implemented in upcoming work */
							//var excluded_voters_list = <?php echo json_encode(explode(",",$postexcludevoters));?>;
							
							//grab post's json_meta
							var post_json_meta = JSON.parse(post.json_metadata);
							// console.log(post_json_meta.tags);
							
							//create a new entry
							var entry = document.createElement('div');
							entry.setAttribute('class','steemit-post-entry');
							
							//grab and setup the details of the post
							
							var post_details = '';
							
							//primary container for content 
							post_details += '<div class="gk_post_content_details">';
							
							//append title/url
							post_details += '<h3><a href="https://www.steemit.com'+post.url+'">'+post.title+'</a></h3>';
							
							<?php
								/* only display following info if in full mode (non-widget) */
								if (!$iswidget){
							?>
							
							//If images exist, use the first one
							if ($.isArray(post_json_meta.image) && post_json_meta.image.length>0){
								post_details += '<span class="steemit-post-img-container">';
								post_details += '<img class="steemit-post-img" src="'+post_json_meta.image[0]+'"></span>';
							}
							
							//add description
							post_details += '<span class="steemit-post-desc">';
							//convert the HTML/markup text into pure text, and cut off text at 200 chars
							post_details += $('<p>'+post.body+'</p>').text().substr(0,200);
							post_details += '</span>';	

							<?php
								}
							?>							

							//add details about the author
							post_details += '<h4><a class="gk_steemit_author_name" href="https://www.steemit.com/@'+post.author+'">@'+post.author+'</a></h4>';
							
							<?php
								/* only display following info if in full mode (non-widget) */
								if (!$iswidget){
							?>							
							//add tags
							$.each(post_json_meta.tags,function(tag_idx,tag_name){
								post_details += '<span class="steemit-post-tags"><a href="https://www.steemit.com/trending/'+tag_name+'">'+tag_name+'</a> </span>';
							});
							
							<?php
								}
							?>							
							
							//add container div for proper formatting
							post_details += '<div class="gk_steemit_add_info">';
							
							//append vote count onto it
							// console.log(post.active_votes.length);
							post_details += '&nbsp; '+post.active_votes.length+' <i class="fa fa-thumbs-up" aria-hidden="true"></i>';
							
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
							
							//append money value onto it
							post_details += '&nbsp; '+money_val+'';
							
							//close gk_steemit_add_info
							post_details += '</div>';
							
							//close gk_post_content_details
							post_details += '</div>';
							
							<?php 
								/* modify placement of voters list in widget mode */
								if (!$iswidget){
							?>
							//append voters names that should be displayed
							post_details += '<div class="steemit-post-voters"><i>Chosen Post Voters:</i><br/>';
							<?php 
								}else{
							?>
							//append voters names that should be displayed - widget mode
							post_details += '<div class="steemit-post-voters-widget"><i>Chosen Post Voters:</i><br/>';
							<?php 
								}
							?>
							$.each(displayable_voters, function(voter_index, d_voter){
								post_details += '<a class="gk_steemit_author_name" href="https://www.steemit.com/@'+d_voter+'">@'+d_voter+'</a><br/>';
								//limit display up to 3 chosen voters
								if (voter_index==2){
									post_details += '+'+(displayable_voters.length-3)+' more...';
									//bail out
									return false;
								}
							});
							
							post_details += '</div>';
							
							entry.innerHTML = post_details;
							//append it to the existing list
							container.appendChild(entry);
							
							//add some cool hovering effect
							$(entry).hover(function(){
								//highlight on hover in
								//$(this).effect('highlight',{color:'#49e8e5'},1000);
								$(this).effect('highlight',{color:'gold'},500);
							},function(){
								//do nothing on hover out
							});
						});
					}
				});
			});
	</script>
<?php
}

/*********************************************************************/