<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WDS_WP_Contributions_Plugins' ) ) {

	class WDS_WP_Contributions_Plugins {

		function __construct() {

		}

		/**
		 * Use the WP.org API to return plugin information.
		 *
		 * This is a copy of plugins_api contained in core, but broken out so any filters run
		 * during that process don't also run here.
		 *
		 * @param string       $action Action the API should perform for it's query.
		 * @param array|object $args   Optional. Arguments to serialize for the Plugin Info API.
		 * @return object $res response object on success, WP_Error on failure.
		 */
		function plugins_api( $action, $args = null ) {

			if ( is_array( $args ) ) {
				$args = (object) $args;
			}

			if ( ! isset( $args->per_page ) ) {
				$args->per_page = 24;
			}

			if ( ! isset( $args->locale ) ) {
				$args->locale = get_locale();
			}

			$url = $http_url = 'http://api.wordpress.org/plugins/info/1.0/';
			if ( $ssl = wp_http_supports( array( 'ssl' ) ) )
				$url = set_url_scheme( $url, 'https' );

			$args = array(
				'timeout' => 15,
				'body' => array(
					'action'  => $action,
					'request' => serialize( $args )
				)
			);
			$request = wp_remote_post( $url, $args );

			if ( $ssl && is_wp_error( $request ) ) {
				trigger_error( __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ) . ' ' . __( '(WordPress could not establish a secure connection to WordPress.org. Please contact your server administrator.)' ), headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE );
				$request = wp_remote_post( $http_url, $args );
			}

			if ( is_wp_error($request) ) {
				$res = new WP_Error('plugins_api_failed', __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ), $request->get_error_message() );
			} else {
				$res = maybe_unserialize( wp_remote_retrieve_body( $request ) );
				if ( ! is_object( $res ) && ! is_array( $res ) )
					$res = new WP_Error('plugins_api_failed', __( 'An unexpected error occurred. Something may be wrong with WordPress.org or this server&#8217;s configuration. If you continue to have problems, please try the <a href="https://wordpress.org/support/">support forums</a>.' ), wp_remote_retrieve_body( $request ) );
			}

			return $res;
		}

		/**
		 * Get the plugin object from the WP.org API
		 *
		 * @param $plugin_slug The slug of the plugin hosted on WP.org.
		 * @return object An object of the plugin data returned from the WP.org Plugin API.
		 */
		public function get_plugin( $plugin_slug ) {

			if ( false === ( $plugin = get_transient( 'wp_contributions_plugin_' . $plugin_slug ) ) ) {
				$args   = array(
					'slug' => esc_attr( $plugin_slug ),
				);
				$plugin = $this->plugins_api( 'plugin_information', $args );
				set_transient( 'wp_contributions_plugin_' . $plugin_slug, $plugin, 24 * HOUR_IN_SECONDS );
			}

			return $plugin;

		}

		/**
		 * Get all plugins from WP.org by a certain Author.
		 *
		 * @param $author_name The username of the author you are querying for plugins.
		 * @return object An object of the plugin data returned from the WP.org Plugin API.
		 */
		public function get_author_plugins( $author_name ) {

			if ( false === ( $author = get_transient( 'wp_contributions_plugin_author_' . $author_name ) ) ) {
				$args   = array(
					'author' => esc_attr( $author_name ),
				);
				$author = $this->plugins_api( 'query_plugins', $args );
				set_transient( 'wp_contributions_plugin_' . $author_name, $author, 24 * HOUR_IN_SECONDS );
			}

			return $author;

		}

	}

}