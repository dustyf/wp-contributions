<?php
/**
 * Plugin Name: WP Contributions
 * Plugin URI: http://dustyf.com
 * Description: Show off your WordPress contributions.
 * Author: Dustin Filippini
 * Author URI: http://dustyf.com
 * Version: 1.0.0
 * License: GPLv2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WDS_WP_Contributions' ) ) {

	class WDS_WP_Contributions {

		/**
		 * Construct function to get things started.
		 */
		public function __construct() {
			// Setup some base variables for the plugin
			$this->basename       = plugin_basename( __FILE__ );
			$this->directory_path = plugin_dir_path( __FILE__ );
			$this->directory_url  = plugins_url( dirname( $this->basename ) );

			// Include any required files
			add_action( 'init', array( $this, 'includes' ) );

			// Load Textdomain
			load_plugin_textdomain( 'wp_contributions', false, dirname( $this->basename ) . '/languages' );

			// Activation/Deactivation Hooks
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
			
		}

		/**
		 * Include our plugin dependencies.
		 */
		public function includes() {

			require_once( $this-> directory_path . 'inc/class-wds-wp-contributions-plugins.php' );
			require_once( $this-> directory_path . 'inc/class-wds-wp-contributions-themes.php' );

		}

		/**
		 * Activation hook for the plugin.
		 */
		public function activate() {

		}

		/**
		 * Deactivation hook for the plugin.
		 */
		public function deactivate() {

		}

	}

	$_GLOBALS['wp_contributions'] = new WDS_WP_Contributions();

}
