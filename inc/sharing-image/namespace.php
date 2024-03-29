<?php
/**
 * Figuren_Theater SEO Sharing_Image.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO\Sharing_Image;

use FT_VENDOR_DIR;
use function add_action;
use function add_post_type_support;
use function is_network_admin;
use function is_user_admin;


const BASENAME   = 'sharing-image/sharing-image.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

const POST_TYPE_SUPPORT = 'sharing-image';
const NEEDED_CAP        = 'manage_site_options';

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {
	
	Options\bootstrap();

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin(): void {

	// Do only load in "normal" admin view
	// and public views.
	// Not for:
	// - network-admin views
	// - user-admin views.
	if ( is_network_admin() || is_user_admin() ) {
		return;
	}

	add_post_type_support( 'post', POST_TYPE_SUPPORT );
	add_post_type_support( 'page', POST_TYPE_SUPPORT );
	
	\array_map(
		function ( string $post_type ): void {
			add_post_type_support( $post_type, POST_TYPE_SUPPORT );
		},
		\get_post_types(
			[
				'_builtin'           => false,
				'public'             => true,
				'publicly_queryable' => true,
			]
		)
	);  

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	// FRONTEND.
	Frontend\bootstrap();

	// BACKEND.
	Admin_UI\bootstrap();

	// BACKEND | Autogeneration logic.
	Generation\bootstrap();
}
