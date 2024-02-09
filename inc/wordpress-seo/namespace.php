<?php
/**
 * Figuren_Theater SEO Yoast_SEO.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO\Yoast_SEO;

// use Figuren_Theater\SEO\Yoast_SEO\Open_Graph;
use Figuren_Theater\SEO\Yoast_SEO\Options;

use FT_VENDOR_DIR;

use function add_action;
use function add_filter;
// use WP_CLI;
// use WP_Query;
use function get_plugins;

use function get_plugin_data;
use function is_admin;
use function wp_cache_set;
use WPSEO_Menu;
use WPSEO_Network_Admin_Menu;
use Yoast_Network_Admin;

const BASENAME   = 'wordpress-seo/wp-seo.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	Options\bootstrap();

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin(): void {

	// Patch network activated plugin bootstrapping manually.
	add_action( 'wpseo_loaded', __NAMESPACE__ . '\\enable_yoast_network_admin' );

	// Load Yoast SEO.
	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	Open_Graph\bootstrap();

	// Intend to save indexables.
	// @see https://github.com/Yoast/wordpress-seo/blob/trunk/src/builders/indexable-builder.php#L258
	// add_filter( 'wpseo_should_save_indexable', '__return_true' );

	// Add sitemap to robots.txt.
	add_filter( 'robots_txt', __NAMESPACE__ . '\\add_sitemap_index_to_robots', 11, 2 );

	// Remove All Yoast HTML Comments
	// https://gist.github.com/paulcollett/4c81c4f6eb85334ba076
	add_filter( 'wpseo_debug_markers', '__return_false' );

	if ( ! is_admin() ) {
		return;
	}

	if ( isset( $_SERVER['REQUEST_URI'] ) && \is_string( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], 'admin.php?page=wpseo_' ) !== false ) {
		add_action( 'plugins_loaded', __NAMESPACE__ . '\\add_yoast_plugins', 5 );
		add_filter( 'site_option_active_sitewide_plugins', __NAMESPACE__ . '\\active_yoast_plugins' );
	}

	add_action( 'network_admin_menu', __NAMESPACE__ . '\\Admin_UI\\bootstrap', 0 );
	add_action( 'admin_menu', __NAMESPACE__ . '\\Admin_UI\\bootstrap', 0 );
}

/**
 * Allow Yoast to validate subscriptions by faking available plugins list.
 *
 * @return void
 */
function add_yoast_plugins(): void {
	$plugins         = get_plugins();
	$updated_plugins = $plugins;
	$available       = array_keys( $plugins );

	// $plugin_path = Altis\ROOT_DIR . '/vendor/yoast/' . $plugin_file;
	if ( is_readable( PLUGINPATH ) && ! in_array( BASENAME, $available, true ) ) {
		$updated_plugins[ BASENAME ] = get_plugin_data( PLUGINPATH, false, false );
	}

	// Append to the cached value.
	if ( count( $plugins ) < count( $updated_plugins ) ) {
		wp_cache_set( 'plugins', [ '' => $updated_plugins ], 'plugins' );
	}
}

/**
 * Filter Yoast plugins to appear active.
 *
 * @param array<string, int> $active_plugins List of activated plugins.
 * 
 * @return array<string, int>
 */
function active_yoast_plugins( array $active_plugins ) {

	if ( is_readable( PLUGINPATH ) ) {
		$active_plugins[ BASENAME ] = time();
	}

	return $active_plugins;
}

/**
 * Bootstrap network admin features of Yoast SEO.
 *
 * This is done because the plugin's built in check for whether it is network
 * activated relies on `wp_get_active_network_plugins()` which does not
 * work for plugins loaded from the vendor directory.
 *
 * @return void
 */
function enable_yoast_network_admin() {
	$network_admin = new Yoast_Network_Admin();
	$network_admin->register_hooks();
	$admin_menu         = new WPSEO_Menu();
	$network_admin_menu = new WPSEO_Network_Admin_Menu( $admin_menu );
	$network_admin_menu->register_hooks();
}

/**
 * Add the Yoast SEO sitemap index to the robots.txt file.
 *
 * @param string $output The original robots.txt content.
 * @param bool   $public Whether the site is public.
 *
 * @return string The filtered robots.txt content.
 */
function add_sitemap_index_to_robots( string $output, bool $public ): string {
	if ( $public ) {
		$output .= sprintf( "Sitemap: %s\n", site_url( '/sitemap_index.xml' ) );
	}

	return $output;
}
