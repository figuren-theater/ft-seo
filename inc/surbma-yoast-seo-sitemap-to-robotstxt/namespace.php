<?php
/**
 * Figuren_Theater SEO Surbma_Yoast_Seo_Sitemap_To_Robotstxt.
 *
 * @package figuren-theater/seo/surbma_yoast_seo_sitemap_to_robotstxt
 */

namespace Figuren_Theater\SEO\Surbma_Yoast_Seo_Sitemap_To_Robotstxt;

use WP_PLUGIN_DIR;

use function add_action;

const BASENAME   = 'surbma-yoast-seo-sitemap-to-robotstxt/surbma-yoast-seo-sitemap-to-robotstxt.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {
	
	require_once PLUGINPATH;

	// useless, as there are no strings, no folder and no po/mo files
	remove_action( 'plugins_loaded', 'surbma_yoast_seo_sitemap_to_robotstxt_init' );
}
