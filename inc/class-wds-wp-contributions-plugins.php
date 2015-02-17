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
		 * @return object plugins_api response object on success, WP_Error on failure.
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

	}

}