<?php
/**
 * Figuren_Theater SEO Yoast_SEO\Open_Graph.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO\Yoast_SEO\Open_Graph;

use function add_filter;
use function get_option;


/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	// WARNING: bootstrap() itself is called on 'plugins_loaded', 19.01.2023
	// add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_metadata', 0 );


	// Yoast debug mode will pretty print the yoast-schema-graph HTML.
	defined( 'WPSEO_DEBUG' ) || define( 'WPSEO_DEBUG', WP_DEBUG );

		add_filter( 'wpseo_og_locale', __NAMESPACE__ . '\\change_og_locale' );
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
 * @source https://github.com/Yoast/wordpress-seo/blob/9c600ab6ee4575c9a98ed190a03aa6e570b64de0/src/presenters/open-graph/locale-presenter.php#L34
 * @see    https://developer.yoast.com/features/opengraph/api/changing-og-locale-output/#change-the-oglocale-tag (wrong filter-name)
 *
 * @param string $locale The current locale.
 *
 * @return string The locale.
 */
function change_og_locale( string $locale ): string {
	$_lang = get_option( 'WPLANG' );
	
	// Check for valid option value.
	if ( ! \is_string( $_lang) || empty( $_lang ) ) {
		return $locale;
	}

	// Return the first five letters of the locale-string.
	return substr( $_lang, 0, 5 );
}
