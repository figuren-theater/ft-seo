<?php
/**
 * Figuren_Theater SEO Yoast_SEO\Admin_UI.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO\Yoast_SEO\Admin_UI;

use function add_action;
use function add_filter;
use function get_role;
use function is_super_admin;
use function remove_menu_page;
use function remove_meta_box;
use function remove_role;
use function remove_submenu_page;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_roles', 11 );

	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_menus', 12 );
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menus', 12 );

	// Remove Yoast SEO dashboard widget.
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets', 0 );

	// Remove Ads, Merch & other bloat.
	add_action( 'admin_head', __NAMESPACE__ . '\\remove_bloat' );

	// Hide some Yoast Editor panels (ort parts of it) that are only ads.
	add_action( 'admin_footer-post.php', __NAMESPACE__ . '\\js_hide_metabox' );
	add_action( 'admin_footer-post-new.php', __NAMESPACE__ . '\\js_hide_metabox' );

	// Remove Helpscout.
	add_filter( 'wpseo_helpscout_show_beacon', '__return_false' );

	// CSS overrides.
	add_action( 'admin_head', __NAMESPACE__ . '\\hide_yoast_editor_sidebar_panels' );
}

/**
 * Remove Yoast SEO user roles
 *
 * @see https://yoast.com/seo-roles/
 * @author Yoast Team
 * Last Tested: Oct 25 2017 using Yoast SEO 5.7.1 on WordPress 4.8.2
 */
function remove_roles(): void {

	// Remove Yoast `SEO Manager` role.
	if ( get_role( 'wpseo_manager' ) ) {
		remove_role( 'wpseo_manager' );
	}

	// Remove Yoast `SEO Editor` role.
	if ( get_role( 'wpseo_editor' ) ) {
		remove_role( 'wpseo_editor' );
	}
}

/**
 * Remove the plugins admin-menu.
 *
 * @return void
 */
function remove_menus(): void {

	remove_submenu_page( 'wpseo_dashboard', 'wpseo_integrations' );
	remove_submenu_page( 'wpseo_dashboard', 'wpseo_workouts' );
	remove_menu_page( 'wpseo_workouts' );
	
	// Remove the Premium submenus.
	remove_submenu_page( 'wpseo_dashboard', 'wpseo_licenses' );
	remove_submenu_page( 'wpseo_dashboard', 'wpseo_redirects' );
	remove_menu_page( 'wpseo_redirects' );
	
	if ( is_super_admin() && true === constant( 'WP_DEBUG' ) ) {
		return;
	}

	remove_menu_page( 'wpseo_dashboard' );
}


/**
 * Remove the Yoast SEO dashboard widget.
 *
 * @see https://plugins.trac.wordpress.org/browser/smntcs-utilities/trunk/smntcs-utilities.php#L74
 * @return void
 */
function remove_dashboard_widgets() {
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'advanced' );
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
	remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'side' );

	add_action(
		'admin_enqueue_scripts',
		function () {
			// This script & style are enqueued by Yoast.
			// And 17 other script are load unnecessarily on the dashboard, which are now gone, too. Yeah!
			\wp_dequeue_script( 'yoast-seo-dashboard-widget' );
			\wp_dequeue_style( 'yoast-seo-wp-dashboard' );
		},
		11 
	);
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
function remove_bloat(): void {
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

/**
 * Un-clutter some UI.
 * 
 * 1. Un-Pin a potentially pinned yoast-sidebar
 * 2. Close metabox by default (buggy)
 * 
 * @todo https://github.com/figuren-theater/ft-seo/issues/14 Close Yoasts metabox by default (buggy)
 *
 * @return void
 */
function js_hide_metabox(): void {
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
				wp.data.dispatch( 'core/edit-post').toggleEditorPanelOpened( 'meta-box-wpseo_meta' );
			}

			// @see  https://github.com/WordPress/gutenberg/blob/4a4e32deb12d2ce104fbfb09734d2b0583315546/packages/interface/README.md#L67
			const _wpseo_plugin =  wp.data.select( 'core/interface' ).isItemPinned( 'core/edit-post', 'yoast-seo/seo-sidebar' );
			if ( _wpseo_plugin ) {
					wp.data.dispatch( 'core/interface' ).unpinItem( 'core/edit-post', 'yoast-seo/seo-sidebar' );
				// setTimeout( function() {
					// wp.data.dispatch( 'core/interface' ).unpinItem( 'core/edit-post', 'yoast-seo/seo-sidebar' );
				// }, 200 ); // yoast uses '100'
			}

		} );
	</script>
	<?php
}

/**
 * Hide some Yoast Editor panels (ort parts of it) that are only ads.
 *
 * @return void
 */
function hide_yoast_editor_sidebar_panels(): void {
	$screen = get_current_screen();

	// Bail early if we aren't on a post edit screen.
	if ( null === $screen || $screen->base !== 'post' ) {
		return;
	}

	// If Yoast ever adds more panels to this sidebar,
	// this will need to be updated.
	$selectors = [

		// 'Get the most out of yoast' Ad
		'div.components-panel div:nth-child(1) div>div.notice-yoast',

		// 'Seo Analysis' editor sidebar panel > Premium-Links and Ad
		'div.components-panel div:nth-child(3) div.yoast.components-panel__body:nth-child(1)>*',

		// 'Premium Seo Analysis' editor sidebar panel
		'div.components-panel div:nth-child(3) div.yoast.components-panel__body:nth-child(2)',

		// 'Add related keywords'
		'div.components-panel div:nth-child(5) div.yoast.components-panel__body',

		// 'Facebook Preview'
		'div.components-panel div:nth-child(7) div.yoast.components-panel__body',
		// 'Twitter Preview'
		'div.components-panel div:nth-child(8) div.yoast.components-panel__body',

	];

	$css = join( ', ', $selectors ) . ' {
		display: none;
	}
	/** Reset "Seo Analysis" editor sidebar panel > clickable title & results */
	div.components-panel div:nth-child(3) div.yoast.components-panel__body:nth-child(1)>h2:first-child,
	div.components-panel div:nth-child(3) div.yoast.components-panel__body:nth-child(1)>div:last-child {
		display: block;
	}
	';

	$escaped_css = strtr(
		wp_filter_nohtml_kses( $css ),
		[
			'&gt;' => '>',
			"\'"   => "'",
			'\"'   => '"',
		]
	);
	\printf(
		'<style>%s</style>',
		$escaped_css // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}
