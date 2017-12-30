<?php
/*
  Plugin Name: GK Steemit Info
  Plugin URI: http://www.greateck.com/
  Description: A wordpress plugin that allows adding steemit (www.steemit.com) data to wordpress sites via widget or alternatively a shortcode
  Version: 0.1.0
  Author: mcfarhat
  Author URI: http://www.greateck.com
  License: GPLv2
 */
 
// Creating the widget for steemit user count
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
		$title = apply_filters( 'widget_title', $instance['title'] );
		$refresh_frequency = apply_filters( 'widget_refresh_frequency', $instance['refresh_frequency'] );
		//before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		//display output in the widget
		steemit_count_renderer($refresh_frequency);
		
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

/* code function for the display of the steemit users count */
function steemit_count_renderer($refresh_frequency){
?>
		<!-- including steemjs library for performing calls -->
		<script src="https://cdn.steemjs.com/lib/latest/steem.min.js"></script>
		<!-- including jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script>
			/* when properly loaded, call steem API method to grab current account count and display it */
			
			function tick_info(){
			
				jQuery(document).ready(function($){
					
					steem.api.getAccountCount(function(err, result) {
						//result now contains the number of accounts. Display this into the input box
						$('#steemit_accounts').text(result);
						//console.log(err, 'account count:'+result);
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
			<div>Check out <a href="https://www.steemit.com">Steemit.com</div><br/>
			<div><i>Your voice is worth something. Join the community that pays you to post and curate high quality content.</i></div>
		</div>
<?php
}

?>