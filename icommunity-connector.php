<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Icommunity_Connector
 *
 * @wordpress-plugin
 * Plugin Name:       iCommunity Connector
 * Plugin URI:        http://example.com/icommunity-connector-uri/
 * Description:       A module to connect wordpress with the iCommunity API
 * Version:           1.0.0
 * Author:            Toni Ruiz
 * Author URI:        http://toniruiz.es/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       icommunity-connector
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
define( 'ICOMMUNITY_CONNECTOR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-icommunity-connector-activator.php
 */
function activate_icommunity_connector() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-icommunity-connector-activator.php';
	Icommunity_Connector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-icommunity-connector-deactivator.php
 */
function deactivate_icommunity_connector() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-icommunity-connector-deactivator.php';
	Icommunity_Connector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_icommunity_connector' );
register_deactivation_hook( __FILE__, 'deactivate_icommunity_connector' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-icommunity-connector.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_icommunity_connector() {

	$plugin = new Icommunity_Connector();
	$plugin->run();

}

/**
 * Debug HTTP requests in WordPress
 *
 * Fires after an HTTP API response is received and before the response is returned.
 *
 * Output in `wp-content\debug.log` file:
 *
 * [24-Apr-2019 06:50:16 UTC] ------------------------------
 * [24-Apr-2019 06:50:16 UTC] https://downloads.wordpress.org/plugin/elementor.2.5.14.zip
 * [24-Apr-2019 06:50:16 UTC] {"errors":{"http_request_failed":["cURL error 28: Resolving timed out after 10518 milliseconds"]},"error_data":[]}
 * [24-Apr-2019 06:50:16 UTC] Requests
 * [24-Apr-2019 06:50:16 UTC] response
 * [24-Apr-2019 06:50:16 UTC] {"method":"GET","timeout":300,"redirection":5,"httpversion":"1.0","user-agent":"WordPress\/5.1.1; http:\/\/astra-sites-dev-test.sharkz.in","reject_unsafe_urls":true,"blocking":true,"headers":[],"cookies":[],"body":null,"compress":false,"decompress":true,"sslverify":true,"sslcertificates":"\/var\/www\/html\/astra-sites-dev-test.sharkz.in\/public_html\/wp-includes\/certificates\/ca-bundle.crt","stream":true,"filename":"\/tmp\/elementor.2.5.14-FOXodB.tmp","limit_response_size":null,"_redirection":5}
 * [24-Apr-2019 06:50:18 UTC] ------------------------------
 *
 * @todo Change the `prefix_` and with your own unique prefix.
 *
 * @since 1.0.0
 *
 * @param array|WP_Error $response HTTP response or WP_Error object.
 * @param string         $context  Context under which the hook is fired.
 * @param string         $class    HTTP transport used.
 * @param array          $r        HTTP request arguments.
 * @param string         $url      The request URL.
 * @return void
 *
 * @since 1.0.0
 */
if( ! function_exists( 'debug_wp_remote_post_and_get_request' ) ) :
    function debug_wp_remote_post_and_get_request( $response, $context, $class, $r, $url ) {
        error_log( '------------------------------', 0);
        error_log( $url , 0);
        error_log( json_encode( $response ) , 0);
        error_log( $class , 0);
        error_log( $context , 0);
        error_log( json_encode( $r ) , 0);
    }
    add_action( 'http_api_debug', 'debug_wp_remote_post_and_get_request', 10, 5 );
endif;

run_icommunity_connector();
