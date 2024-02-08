<?php
/**
 * Figuren_Theater SEO Yoast_SEO\Open_Graph.
 *
 * @package figuren-theater/seo/yoast_seo\open_graph
 */

namespace Figuren_Theater\SEO\Yoast_SEO\Open_Graph;

use function add_action;
use function get_option;


/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	// WARNING: bootstrap() itself is called on 'plugins_loaded', 19.01.2023
	// add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_metadata', 0 );


	// Yoast debug mode will pretty print the yoast-schema-graph HTML.
	defined( 'WPSEO_DEBUG' ) || define( 'WPSEO_DEBUG', WP_DEBUG );

	//
	add_action( 'wpseo_og_locale', __NAMESPACE__ . '\\change_og_locale' );
	// 'wpseo_og_locale' => 'preferred_languages_filter_locale',
	// 'locale' => 'change_og_locale', // done pref_lang on prio 5
	// 'locale' => ['change_og_locale', 100],
}



/**
 * Load the SEO metadata plugin.
 *
 * @return void
function load_metadata() {
	$config = Altis\get_config()['modules']['seo']['metadata'] ?? [];
	$options = get_option( 'wpseo_social' );

	// Only add our custom Opengraph presenters if Opengraph is enabled.
	if ( ( isset( $config['opengraph'] ) && $config['opengraph'] === true ) || $options['opengraph'] ) {
		add_filter( 'wpseo_frontend_presenters', __NAMESPACE__ . '\\opengraph_presenters' );
	}
}
 */

/**
 * Add our custom Opengraph presenters to the array of Yoast Opengraph presenters.
 *
 * @param array $presenters The array of presenters.
 *
 * @return array Updated array of presenters.
function opengraph_presenters( array $presenters ) : array {
	$presenters[] = new Opengraph\Author_Presenter();
	$presenters[] = new Opengraph\Section_Presenter();
	$presenters[] = new Opengraph\Tag_Presenter();

	return $presenters;
}
 */



/**
 * Filter the 'locale' output.
 * Because by default it is based on WPLANG constant
 *
 * @see https://developer.yoast.com/features/opengraph/api/changing-og-locale-output/#change-the-oglocale-tag
 *
 * @param string $locale The current locale.
 *
 * @return string The locale.
 */
function change_og_locale( string $locale ) : string {
	$_lang = get_option('WPLANG');
	return substr( $_lang, 0, 5 );
}


