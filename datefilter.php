<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiverr.com/junaidzx90
 * @since             1.0.0
 * @package           Datefilter
 *
 * @wordpress-plugin
 * Plugin Name:       DateFilter
 * Plugin URI:        https://www.fiverr.com
 * Description:       This plugin is used for filtering posts by date, the output will show through [datefilter]
 * Version:           1.0.0
 * Author:            Developer Junayed
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       datefilter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DATEFILTER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-datefilter-activator.php
 */
function activate_datefilter() {
	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-datefilter-deactivator.php
 */
function deactivate_datefilter() {
	
}

register_activation_hook( __FILE__, 'activate_datefilter' );
register_deactivation_hook( __FILE__, 'deactivate_datefilter' );

add_action("wp_enqueue_scripts", "datefilter_scripts" );
function datefilter_scripts(){
	wp_enqueue_style("jquery-ui", "//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css", array(), "1.0.0", "all" );
	wp_enqueue_style("datefilter", plugin_dir_url(__FILE__ )."public/css/style.css", array(), "1.0.1", "all" );
	wp_enqueue_script("jquery-ui", plugin_dir_url(__FILE__ )."public/js/jquery-ui.js", array("jquery"), "1.0.0", false);
	wp_enqueue_script("datefilter", plugin_dir_url(__FILE__ )."public/js/datefilter.js", array("jquery", "jquery-ui"), "1.0.0", true);
	wp_localize_script("datefilter", "df_filter", array(
		'ajaxurl' => admin_url( "admin-ajax.php" ),
		'nonce'	=> wp_create_nonce( "dfnonce" )
	) );
}

add_action("wp_ajax_dffilter_post", "dffilter_post" );
add_action("wp_ajax_nopriv_dffilter_post", "dffilter_post" );
function dffilter_post(){
	if(!wp_verify_nonce( $_GET['nonce'], 'dfnonce' )){
		die("Invalid request!");
	}

	if(isset($_GET['date'])){
		$query_date = $_GET['date'];

		$query_date = explode("/", $query_date);
		$month = $query_date[0];
		$date = $query_date[1];
		$year = $query_date[2];

		$args = array(
			'post_type' => 'post',
			'numberposts' => '-1',
			'post_status' => 'publish'
		);

		$args = array(
			'date_query' => array(
				array(
					'year'  => $year,
					'month' => $month,
					'day'   => $date,
				),
			),
		);

		$posts = get_posts($args);
		$results = wp_list_pluck($posts,'post_content' );
		echo json_encode(array("success" => $results));
		die;
	}
	echo json_encode(array("error" => "No post found!"));
	die;
}

add_shortcode("datefilter", "datefilter_callback" );
function datefilter_callback(){
	ob_start();
	require_once plugin_dir_path(__FILE__ )."public/partials/datefilter-output.php";
	return ob_get_clean();
}
