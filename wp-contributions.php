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

			// Save User Profile Settings
			add_action( 'personal_options_update', array( $this, 'update_user' ) );
			add_action( 'edit_user_profile_update', array( $this, 'update_user' ) );

			if ( isset( $_GET['debug'] ) && $_GET['debug'] == true ) {
				add_action( 'admin_head', array( $this, 'get_plugins' ) );
			}

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

			wp_nonce_field( 'wp_contributions_user_settings', 'wp_contributions_user_settings' );
			?>
			<h3 id="wp-contributions"><?php esc_html_e( 'WP Contributions Settings', 'wp-contributions'); ?></h3>
			<table class="form-table">
			<tr>
				<th>
					<label for="wp_contributions_wporg_username"><?php esc_html_e( 'WordPress.org Username', 'wp-contributions' ); ?></label>
				</th>
				<td><input class="regular-text" type="text" id="wp_contributions_wporg_username" name="wp_contributions_wporg_username"
				           value="<?php echo esc_attr( get_the_author_meta( 'wp_contributions_wporg_username', $user->ID ) ); ?>" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="wp_contributions_show_plugins"><?php esc_html_e( 'Show Plugins?', 'wp-contributions' ); ?></label>
				</th>
				<td>
					<input class="checkbox double" type="checkbox" id="wp_contributions_show_plugins" name="wp_contributions_show_plugins" value="on" <?php echo ( ( esc_attr( get_the_author_meta( 'wp_contributions_show_plugins', $user->ID ) ) == 'on' ) ? 'checked' : '' ); ?> />
				</td>
			</tr>
			<tr>
				<th>
					<label for="wp_contributions_show_themes"><?php esc_html_e( 'Show Themes?', 'wp-contributions' ); ?></label>
				</th>
				<td>
					<input class="checkbox double" type="checkbox" id="wp_contributions_show_themes" name="wp_contributions_show_themes" value="on" <?php echo ( ( esc_attr( get_the_author_meta( 'wp_contributions_show_themes', $user->ID ) ) == 'on' ) ? 'checked' : '' ); ?> />
				</td>
			</tr>
			</table>
			<?php

		}

		/**
		 * Process the updates of user meta.
		 *
		 * @param int $user_id The ID of the user being saved.
		 */
		function update_user( $user_id ) {

			if ( ! isset( $_POST['wp_contributions_wporg_username'] ) ) {
				return;
			}

			check_admin_referer( 'wp_contributions_user_settings', 'wp_contributions_user_settings' );

			update_user_meta( $user_id, 'wp_contributions_wporg_username', isset( $_POST['wp_contributions_wporg_username'] ) ? sanitize_text_field( $_POST['wp_contributions_wporg_username'] ) : '' );
			update_user_meta( $user_id, 'wp_contributions_show_plugins', isset( $_POST['wp_contributions_show_plugins'] ) ? sanitize_text_field( $_POST['wp_contributions_show_plugins'] ) : '' );
			update_user_meta( $user_id, 'wp_contributions_show_themes', isset( $_POST['wp_contributions_show_themes'] ) ? sanitize_text_field( $_POST['wp_contributions_show_themes'] ) : '' );

		}

		function get_plugins() {

			$action = 'query_plugins';
			$args = array(
				'author' => 'dustyf',
			);

			$plugin_api = new WDS_WP_Contributions_Plugins();
			$plugins = $plugin_api->get_author_plugins('dustyf');
			var_dump($plugins);

		}

	}

	$_GLOBALS['wp_contributions'] = new WDS_WP_Contributions();

}
