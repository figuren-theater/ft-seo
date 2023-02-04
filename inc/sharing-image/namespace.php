<?php
/**
 * Figuren_Theater SEO Sharing_Image.
 *
 * @package figuren-theater/seo/sharing_image
 */

namespace Figuren_Theater\SEO\Sharing_Image;

use Figuren_Theater\SEO\Sharing_Image\Admin_UI;
use Figuren_Theater\SEO\Sharing_Image\Frontend;
use Figuren_Theater\SEO\Sharing_Image\Generation;
use Figuren_Theater\SEO\Sharing_Image\Options;

use FT_VENDOR_DIR;

use function add_action;
use function add_post_type_support;
use function is_network_admin;
use function is_user_admin;


const BASENAME   = 'sharing-image/sharing-image.php';
const PLUGINPATH = FT_VENDOR_DIR . '/wpackagist-plugin/' . BASENAME;

const POST_TYPE_SUPPORT = 'sharing-image';
const NEEDED_CAP        = 'manage_site_options';

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {
	
	Options\bootstrap();

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

function load_plugin() {

	// Do only load in "normal" admin view
	// and public views.
	// Not for:
	// - network-admin views
	// - user-admin views
	if ( is_network_admin() || is_user_admin() )
		return;

	add_post_type_support( 'post', POST_TYPE_SUPPORT );
	add_post_type_support( 'page', POST_TYPE_SUPPORT );
	
	require_once PLUGINPATH;

	//////////////
	// FRONTEND //
	//////////////
	Frontend\bootstrap();

	if ( ! is_admin()  )
		return;

	/////////////
	// BACKEND //
	/////////////
	Admin_UI\bootstrap();

	//////////////////////////////////////////////////////////////////
	// BACKEND | Autogeneration logic                               //
	// triggered on 'wp_insert_post' and/or on 'updated_post_meta'  //
	//////////////////////////////////////////////////////////////////
	Generation\bootstrap();

}

