<?php
/**
 * Figuren_Theater SEO Sharing_Image\Admin_UI.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO\Sharing_Image\Admin_UI;

use Figuren_Theater\SEO\Sharing_Image;

use WP_DEBUG;

use function add_action;
use function add_filter;
use function current_user_can;
use function is_super_admin;
use function remove_submenu_page;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );

	// TODO #8
	//
	// use the new webfont API
	// as soon as it supports
	// to get the path(es) of used fonts
	// AND their primary purpose (heading-fonts, body-text, etc.)
	//
	// 'sharing_image_get_fontpath' => ['sharing_image_get_fontpath', 10, 2], // !

	// Disable any UI for the user
	// which is not that reliable
	// and not save to use.
	//
	// Maybe this could be re-added in a future,
	// advanced version of the 'schoener_teilen' feature.
	add_filter( 'sharing_image_hide_metabox', __NAMESPACE__ . '\\sharing_image_hide_metabox' );

	// Disable "Premium" tab on settings page.
	add_filter( 'sharing_image_settings_tabs', __NAMESPACE__ . '\\sharing_image_settings_tabs' );
}

/**
 * Remove the plugins admin-menu.
 *
 * @return void
 */
function remove_menu(): void {

	if ( is_super_admin() && true === constant( 'WP_DEBUG' ) ) {
		return;
	}

	remove_submenu_page( 'options-general.php', 'sharing-image' );
}



/**
 * This filter is used to update settings tabs. You can remove existing or add a new one. 
 *
 * @see     https://wpset.org/sharing-image/hooks/#sharing_image_settings_tabs
 *
 * @param string[] $tabs List of settings tabs.
 *
 * @return string[]
 */
function sharing_image_settings_tabs( array $tabs ): array {
	unset( $tabs['premium'] );
	return $tabs;
}



/**
 * Hides the 'Sharing Image' metabox, 
 * if the current user can not 'manage_site_options' (NEEDED_CAP).
 * 
 * This could be the chance for a selectable, additional feature tzo have this metabox available to the editor,
 * BUT it gets totally buggy and clunky, with this metabox activated.
 * 
 * Keep the metabox disabled, until you did enormous testing!
 * 
 * @see   https://wpset.org/sharing-image/hooks/#sharing_image_hide_metabox
 * 
 * @return bool
 */
function sharing_image_hide_metabox(): bool {
	return true;
	// return ! current_user_can( Sharing_Image\NEEDED_CAP );
	//
	// maybe TODO later
	// This is only relevant (and called), if the metabox is visible!
	//
	// hand over the just yet selected
	// and not yet saved fetaured-image to our autogeneration logic
	// add_filter('sharing_image_update_post_meta', __NAMESPACE__ . '\\mf_update_post_meta_sharing_image', 10, 2 ); // !
}


/**
 * This is only relevant, if the metabox is visible!
 * 
 * This filter allows you to change the array with widget post meta right before the update_post_meta request. 
 * 
 * @see https://wpset.org/sharing-image/hooks/#sharing_image_update_post_meta
 *
 * @param array $meta    Updated post meta.
 * @param int   $post_id Post ID.
 * 
 * @return array
 */
function mf_update_post_meta_sharing_image( array $meta, int $post_id ): array {
	$featured_image = \get_post_thumbnail_id( $post_id );
	if ( $featured_image ) {
		if ( isset( $meta ) && isset( $meta['fieldset'] ) ) {
			// Loop all templates.
			foreach ( $meta['fieldset'] as $index => $template ) {
				// Assign featured image as attachment if no attachment defined.
				if ( ! isset( $template['attachment'] ) || ! $template['attachment'] ) {
					$meta['fieldset'][ $index ]['attachment'] = $featured_image;
				}
			}
		}
	}

	return $meta;
}




/**
 * Use theme specific fonts for the headline.
 * 
 * @todo #8 Use the new webfont-API as soon as it got published.
 *
 * @param string $path  Font file path.
 * @param array  $layer Layer data.
 *
 * @see   https://wpset.org/sharing-image/hooks/#sharing_image_get_fontpath
public static function sharing_image_get_fontpath( $path, $layer ) {

	global $wp_styles, $wp_scripts;
	wp_die(var_export([
		$wp_styles,
		$wp_scripts
	],true));

	return $path;
	// return WP_PLUGIN_DIR . '/my-plugin/font.ttf';
}
 */
