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

	add_action( 'wpseo_og_locale', __NAMESPACE__ . '\\change_og_locale' );

	// 'wpseo_og_locale' => 'preferred_languages_filter_locale',
	// 'locale' => 'change_og_locale', // done pref_lang on prio 5
	// 'locale' => ['change_og_locale', 100],
}


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
