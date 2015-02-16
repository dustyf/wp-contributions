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
			load_plugin_textdomain( 'wp-contributions', false, dirname( $this->basename ) . '/languages' );

			// Activation/Deactivation Hooks
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

			// Add settings to User Profile Pages.
			add_action( 'show_user_profile', array( $this, 'user_profile' ) );
			add_action( 'edit_user_profile', array( $this, 'user_profile' ) );

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

		/**
		 * Outputs the per user settings on user profile pages.
		 *
		 * @param object $user The WP_User object of the user being displayed.
		 */
		function user_profile( $user ) {

			if ( ! current_user_can( 'edit_users' ) ) {
				return;
			}

			?>
			<h3 id="wp-contributions"><?php esc_html_e( 'WP Contributions Settings', 'wp-contributions'); ?></h3>

			<?php

		}

	}

	$_GLOBALS['wp_contributions'] = new WDS_WP_Contributions();

}
