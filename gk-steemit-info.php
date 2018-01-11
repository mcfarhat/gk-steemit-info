<?php
/*
  Plugin Name: GK Steemit Info
  Plugin URI: http://www.greateck.com/
  Description: A wordpress plugin that allows adding steemit (www.steemit.com) data to wordpress sites via widget or alternatively a shortcode
  Version: 0.1.0
  Author: mcfarhat
  Author URI: http://www.greateck.com
  License: GPLv2
  License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

/* handles tracking if required libraries have been added already */
$libraries_appended = false; 
 
/* Creating widget handling steemit user count */
class steemit_info_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'steemit_info_widget',
		__('Steemit Info Widget', 'gk_steemit_info'),
		array( 'description' => __( 'Widget Allowing Display of Steemit info', 'gk_steemit_info' ), )
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
			$title = __( 'Steemit User Count', 'gk_steemit_info' );
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

	if (!$libraries_appended){
?>
		<!-- including steemjs library for performing calls -->
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<!-- including jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		
<?php
		$libraries_appended = true;
	}
?>
		<script>
			function tick_info(){
				jQuery(document).ready(function($){
					//fix for migration to api.steemit.com
					steem.api.setOptions({ url: 'https://api.steemit.com' });
					
					steem.api.getAccountCount(function(err, result) {
						//result now contains the number of accounts. Display this into the input box
						$('#steemit_accounts').text(result);
						console.log(err, 'account count:'+result);
					});
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
			<ul id="result"></ul>
			<div>Check out <a href="https://www.steemit.com">Steemit.com</div><br/>
			<div><i>Your voice is worth something. Join the community that pays you to post and curate high quality content.</i></div>
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
		array( 'description' => __( 'Widget Allowing Display of Steemit info', 'gk_steemit_info' ), )
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
		$username = apply_filters( 'steemit_username_widget_title', $instance['steemit_username'] );
		$title = apply_filters( 'steemit_post_count_widget_title', $instance['steemit_post_count'] );
		
		//display output in the widget
		steemit_user_posts_renderer($username, $postcount);
		
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
			$title = __( 'Latest Posts on <a href="https://www.steemit.com">Steemit</a>', 'gk_steemit_user_posts' );
		}
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
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_username' ); ?>">Steemit Username:</label>
		@<input class="text" id="<?php echo $this->get_field_id( 'steemit_username' ); ?>" name="<?php echo $this->get_field_name( 'steemit_username' ); ?>" type="text" value="<?php echo esc_attr( $steemit_username ); ?>"></p>
		<p><label for="<?php echo $this->get_field_id( 'steemit_post_count' ); ?>">Max Posts:</label>
		<input class="text" id="<?php echo $this->get_field_id( 'steemit_post_count' ); ?>" name="<?php echo $this->get_field_name( 'steemit_post_count' ); ?>" type="number" step="1" min="1" max="50" value="<?php echo esc_attr( $steemit_post_count ); ?>"></p>		
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
function gk_load_steemit_user_posts_widget() {
    register_widget( 'steemit_user_posts_widget' );
}
add_action( 'widgets_init', 'gk_load_steemit_user_posts_widget' ); 
 
/* shortcode to display steemit user count on front end. 
Use it in format [steemit_user_posts username=USERNAME limit=LIMIT] */
add_shortcode('steemit_user_posts', 'display_steemit_user_posts' );

function display_steemit_user_posts( $atts, $content = "" ) {
	/*$inner_atts = shortcode_atts( array(
        'refresh_frequency' => 5000,
    ), $atts );*/
	$username = $inner_atts['username'];
	$postcount = $inner_atts['limit'];
	steemit_user_posts_renderer($username, $postcount);
}

/* function handling the display of the selected users' posts */
function steemit_user_posts_renderer($username, $postcount){
	if ($username == ''){
		echo 'Steemit username not provided';
		return;
	}
	//if postcount not properly provided and within 1 - 100, default to 10
	if (!is_numeric ($postcount) || (is_numeric($postcount) && ($postcount<1 || $postcount>100))){
		$postcount = 10;
	}
	if (!$libraries_appended){
?>
		<!-- including steemjs library for performing calls -->
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<!-- including jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>	
<?php
		$libraries_appended = true;
	}
?>
	<div>
		<div id="user_posts_container"></div>
	</div>	
	<script>
			/* when properly loaded, call steem API method to grab recent posts by selected username and display them */
			jQuery(document).ready(function($){
				//fix for migration to api.steemit.com
				steem.api.setOptions({ url: 'https://api.steemit.com' });
				
				//setup query to grab proper user with preset limit
				var query = {
					tag: '<?php echo $username;?>',
					limit: <?php echo $postcount;?>,
				};
				var container = document.getElementById('user_posts_container');
				//call getDiscussionsByBlog to grab user's latest posts
				steem.api.getDiscussionsByBlog(query, function (err, posts) {
					// console.log(err, discussions);
					if (!err) {
						posts.map(function (post) {
							console.log(post);
							//create a new entry
							var entry = document.createElement('div');
							entry.setAttribute('class','steemit-post-entry');
							//grab the details of the post
							var post_details = '<a href="https://www.steemit.com'+post.url+'">'+post.title+'</a>';
							//append money value onto it
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
							post_details += '&nbsp; ('+money_val+')';
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
?>
