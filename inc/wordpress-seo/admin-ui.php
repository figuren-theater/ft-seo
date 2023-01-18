<?php
/**
 * Figuren_Theater SEO Yoast_SEO\Admin_UI.
 *
 * @package figuren-theater/seo/yoast_seo\admin_ui
 */

namespace Figuren_Theater\SEO\Yoast_SEO\Admin_UI;

use function add_action;
use function get_role;
use function remove_menu_page;
use function remove_meta_box;
use function remove_role;
use function remove_submenu_page;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	//
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_roles', 11 );

	//
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menus', 11 );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\remove_menu__courses' );

	//
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'admin_head'        , __NAMESPACE__ . '\\remove_bloat' );

	//
	add_action( 'admin_footer-post.php'    , __NAMESPACE__ . '\\js_hide_metabox' );
	add_action( 'admin_footer-post-new.php', __NAMESPACE__ . '\\js_hide_metabox' );

}


/**
 * Remove Yoast SEO user roles
 *
 * @see https://yoast.com/seo-roles/
 * @author Yoast Team
 * Last Tested: Oct 25 2017 using Yoast SEO 5.7.1 on WordPress 4.8.2
 */
function remove_roles() : void {

	// Remove Yoast `SEO Manager` role
	if ( get_role('wpseo_manager') ) {
		remove_role( 'wpseo_manager' );
	}

	// Remove Yoast `SEO Editor` role
	if ( get_role('wpseo_editor') ) {
		remove_role( 'wpseo_editor' );
	}
}


function remove_menus() : void {
	
	//
	remove_menu_page( 'wpseo_dashboard' );
	remove_menu_page( 'wpseo_workouts' );
}


/**
 * Yoast » Remove courses.
 *
 * @see https://plugins.trac.wordpress.org/browser/smntcs-utilities/trunk/smntcs-utilities.php#L63
 * @return void
 */
function remove_menu__courses() : void {
	remove_submenu_page( 'wpseo_dashboard', 'wpseo_courses' );
}


/**
 * Yoast » Remove dashboard.
 *
 * @see https://plugins.trac.wordpress.org/browser/smntcs-utilities/trunk/smntcs-utilities.php#L74
 * @return void
 */
function remove_dashboard_widgets() {
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'advanced' );
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'side' );

	// This script & style are enqueued by Yoast.
	\wp_dequeue_script( 'dashboard-widget' );
	\wp_dequeue_style( 'wp-dashboard' );
}


/**
 * Yoast » Remove bloat
 *
 * @see https://wordpress.org/plugins/smntcs-utilities/ A collection of custom snippets to declutter the dashboard.
 * @see https://plugins.trac.wordpress.org/browser/smntcs-utilities/trunk/smntcs-utilities.php#L87
 * @see https://github.com/Yoast/wordpress-seo/issues/3464
 * @see https://wordpress.org/support/topic/please-remove-your-invasive-update-message
 * 
 * @since 1.0.0
 * @return void
 */
function remove_bloat() {
	?>
	<style>
		.yoast_bf_sale,
		.yoast_premium_upsell,
		.yoast_premium_upsell_admin_block,
		body.toplevel_page_wpseo_dashboard #sidebar-container,
		body.seo_page_wpseo_titles #sidebar-container,
		body.seo_page_wpseo_search_console #sidebar-container,
		body.seo_page_wpseo_social #sidebar-container,
		body.seo_page_wpseo_tools #sidebar-container,
		.yoast-notification.notice,
		#yoast-helpscout-beacon,
		/* following from: 
		https://github.com/courtness/wordpack/blob/1a9248a9965fead4a2276bb4f93ff402aacb9963/wp-content/themes/wordpack/inc/admin-functions.php#L12-L41 */
		#wp-admin-bar-wpseo-menu,
		/* classic metaboxes
		#misc-publishing-actions #content-score, 
		#misc-publishing-actions #keyword-score, */
		.yoast-notice,
		[name="seo_filter"],
		[name="readability_filter"]
		{ display: none !important; }
	</style>
	<?php
}


function js_hide_metabox() : string {
	?>
	<script type="text/javascript">
		wp.domReady( function() {
			// completely remove
			// wp.data.dispatch( 'core/edit-post').removeEditorPanel( 'meta-box-wpseo_meta' );
			// just switch off
			// wp.data.dispatch( 'core/edit-post').toggleEditorPanelEnabled( 'meta-box-wpseo_meta' );

			// @see  https://wordpress.stackexchange.com/questions/339436/removing-panels-meta-boxes-in-the-block-editor/339437#339437
			const _wpseo_metabox = wp.data.select( 'core/edit-post').isEditorPanelEnabled( 'meta-box-wpseo_meta' );
			if ( _wpseo_metabox ) {
				wp.data.dispatch( 'core/edit-post').toggleEditorPanelEnabled( 'meta-box-wpseo_meta' );
			}

			// @see  https://github.com/WordPress/gutenberg/blob/4a4e32deb12d2ce104fbfb09734d2b0583315546/packages/interface/README.md#L67
			const _wpseo_plugin =  wp.data.select( 'core/interface' ).isItemPinned( 'core/edit-post', 'yoast-seo/seo-sidebar' );
			if ( _wpseo_plugin ) {
				wp.data.dispatch( 'core/interface' ).unpinItem( 'core/edit-post', 'yoast-seo/seo-sidebar' ); 
			}

		} );
	</script>
	<?php
}

