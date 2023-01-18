<?php
/**
 * Figuren_Theater SEO Yoast_SEO.
 *
 * @package figuren-theater/seo/yoast_seo
 */

namespace Figuren_Theater\SEO\Yoast_SEO;

use Figuren_Theater\SEO\Yoast_SEO\Admin_UI;
// use Figuren_Theater\SEO\Yoast_SEO\Open_Graph;
use Figuren_Theater\SEO\Yoast_SEO\Options;


use WP_PLUGIN_DIR;

use function add_action;
use function add_filter;
use function is_admin;

const BASENAME   = 'wordpress-seo/wp-seo.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	Options\bootstrap();

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {
	
	require_once PLUGINPATH;

	Open_Graph\bootstrap();

	// Remove All Yoast HTML Comments
	// https://gist.github.com/paulcollett/4c81c4f6eb85334ba076
	add_filter( 'wpseo_debug_markers', '__return_false' );

	if ( ! is_admin() )
		return;

	add_action( 'admin_menu', __NAMESPACE__ . '\\Admin_UI\\bootstrap', 0 );
}
