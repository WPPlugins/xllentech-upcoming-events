<?php
/*
Plugin Name: XllenTech Upcoming Events
Plugin URI: http://www.xllentech.com
Description: A Plugin to display Upcoming Islamic Events from the current islamic date.
Version: 1.0.3
Author: Abbas Momin
Author URI: http://www.xllentech.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class Xllentech_Upcoming_Events extends WP_Widget {

	// constructor
	public function __construct() {
		/* ... */
		parent::__construct('xllentech_upcoming_events_plugin',__('XllenTech Upcoming Events', 'xllentech_upcoming_events_plugin'),
		array( 'description' => __( 'XllenTech Upcoming Events', 'xllentech_upcoming_events_plugin'), ) ); // Args
	}

	// widget form creation
function form($instance) {
			// Check values
			if( $instance) {
			     $title = esc_attr($instance['title']);
			     $count = esc_attr($instance['count']);
			     $page_id = esc_attr($instance['page_id']);
			} else {
			     $title = '';
			     $count = '';
			     $page_id = '';
			}
			
			?>

			<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'xllentech_upcoming_events'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of events to show', 'xllentech_upcoming_events'); ?>:</label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" size="2" />
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('page_id'); ?>"><?php _e('If any, Calendar Page ID', 'xllentech_upcoming_events'); ?>:</label>
			<input id="<?php echo $this->get_field_id('page_id'); ?>" name="<?php echo $this->get_field_name('page_id'); ?>" type="text" value="<?php echo $page_id; ?>" size="3" />
			</p>

			<?php
		}
	/*
	This code is simply adding 3 fields to the widget. The first one is the widget title, the second a text field, and the last one is a textarea. Let’s see now how to save and update each field value with the update() function.
	*/

// update widget
function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['count'] = strip_tags($new_instance['count']);
      $instance['page_id'] = strip_tags($new_instance['page_id']);
     return $instance;
	}

	// display widget
function widget($args, $instance) {
	global $wpdb;
   extract( $args );
   // these are the widget options
   $title = apply_filters('widget_title', $instance['title']);
   $count = apply_filters('widget_title', $instance['count']);
   $page_id = apply_filters('widget_title', $instance['page_id']);
   
   echo $before_widget;
   // Display the widget
   echo '<div class="widget-text wp_widget_plugin_box">';

   // Check if title is set
   if ( $title ) {
      echo $before_title . $title . $after_title;
   }
	
	date_default_timezone_set('America/Denver');

//include php file with islamic month names and days
include( plugin_dir_path( __FILE__ ) . 'xllentech-calendar-data.php');

	$xc_options = get_option("xc_options");
	if (!is_array($xc_options)) {
		$xc_options = array(
			"islamic_months" => "Islamic Months,Muharram,Safar,Rabi'al Awwal,Rabi'al Thani,Jamaada'al Ula,Jamaada'al Thani,Rajab,Sha'ban,Ramadhan,Shawaal,Zul Qa'dah,Zul Hijjah",
			"islamic_month_days" => "12,30,29,30,29,30,29,30,29,30,29,30,29");
		update_option("xc_options",$xc_options);
	}

$islamic_months = explode(",", $xc_options['islamic_months']);
$islamic_month_days = explode(",", $xc_options['islamic_month_days']);

	$english_currentdate=time();
	$english_currentday=date("j",$english_currentdate);
	$english_currentmonth=date("n",$english_currentdate);
	$english_currentyear=date("Y",$english_currentdate);
	
	$month_days_table = $wpdb->prefix . 'month_days'; 
	$month_firstdate_table = $wpdb->prefix . 'month_firstdate';
	
	$query="SELECT islamic_day,islamic_month,islamic_year FROM $month_firstdate_table WHERE english_year=".$english_currentyear." and english_month=".$english_currentmonth;
	
	$islamic_date_data = $wpdb->get_results($query);

	foreach( $islamic_date_data as $results ) {
			$islamic_day=$islamic_date_data[0]->islamic_day;
			$islamic_month=$islamic_date_data[0]->islamic_month;
			$islamic_year=$islamic_date_data[0]->islamic_year;
		}
		$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$islamic_year ." and month_number=".$islamic_month);
    		if(count($month_data)>0) {
				foreach( $month_data as $xue_data ) {
				$islamic_month_days[$islamic_month]=$xue_data->days;
				}    
			}
 			echo "<div class='xllentech-upcoming-events'><ul>";
 	$counter=0;		
	for ($i=1; $i<=130; $i++) {
		if($i>=$english_currentday) {
			if(!empty($islamic_events[$islamic_month][$islamic_day][0])) {
				echo "<li><span class='xllentech-event-desc'>".$islamic_events[$islamic_month][$islamic_day][1]."</span>";
				echo "<span class='xllentech-event-date'>".$islamic_day."  ".$islamic_months[$islamic_month]."</span></li>";
				$counter++;
				if($counter>=$count) {
					break;
				}
			}
		}
		$islamic_day++;
		If ($islamic_day>$islamic_month_days[$islamic_month]){
		$islamic_day=1;
		$islamic_month++;
			if ($islamic_month>12){
				$islamic_month=1;
				$islamic_year++;
			}
			$month_data = $wpdb->get_results("SELECT days FROM $month_days_table " .
    "WHERE year_number=".$islamic_year ." and month_number=".$islamic_month);
    		if(count($month_data)>0) {
				foreach( $month_data as $xue_data ) {
					$islamic_month_days[$islamic_month]=$xue_data->days;
				}    
			}
		}
	}
		if($page_id!=NULL) {
			$calendar_page=get_page_link($page_id);
			echo '<li><span class="xllentech-bottom-link"><a href="'.$calendar_page.'">VIEW CALENDAR</a></span></li>';
		}
	echo "</ul></div></div>";
	echo $after_widget;
	}
}
// register widget
add_action( 'widgets_init', 'xllentech_upcoming_events_widget' );

function xllentech_upcoming_events_widget() {
	register_widget( 'Xllentech_Upcoming_Events' );
}
/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action( 'wp_enqueue_scripts', 'xllentech_upcoming_events_css' );

/**
 * Enqueue plugin style-file
 */
function xllentech_upcoming_events_css() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'xllentech-upcoming-events-styles', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'xllentech-upcoming-events-styles' );
}
?>