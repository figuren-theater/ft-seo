<?php
/**
 * Figuren_Theater SEO Sharing_Image\Frontend.
 *
 * @package figuren-theater/seo/sharing_image\frontend
 */

namespace Figuren_Theater\SEO\Sharing_Image\Frontend;

use Figuren_Theater\SEO\Sharing_Image\Options;

use function sharing_image_poster;

use function add_action;
use function esc_url;
use function get_option;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {


	// remove <meta og:tags>, which are done by yoast
	// 'sharing_image_hide_meta' => 'sharing_image_hide_metatags',
	add_filter( 'sharing_image_hide_meta', '__return_true' );

	// Because 'wpseo_opengraph_image_size' FILTER IS BUGGY
	// 
	// we can not use the already working 'wpseo_opengraph_image' 
	// and 'wpseo_twitter_image' filters
	// 'wpseo_opengraph_image' => ['add_image_to_yoast_opengraph', 10, 2 ],
	// 'wpseo_twitter_image'   => ['add_image_to_yoast_opengraph', 10, 2 ],
	// // 'wpseo_opengraph_image_size' => 'add_image_size_to_yoast_opengraph', // this one is buggy
	// 
	// "Wrong image width and height when using "wpseo_opengraph_image" filter"
	// Issue #15052
	// https://github.com/Yoast/wordpress-seo/issues/15052
	// working solution from the coments
	// https://github.com/Yoast/wordpress-seo/issues/15052#issuecomment-695093003
	add_filter( 'wpseo_frontend_presentation', __NAMESPACE__ . '\\add_image_to_yoast_opengraph', 30 );


}


/**
 * Filter 'wpseo_frontend_presentation' - Allow filtering the presentation used to output our meta values.
 *
 * @see  https://github.com/Yoast/wordpress-seo/blob/76986597983c63c7c66fed3e9d07174f62cb2657/src/integrations/front-end-integration.php#L314
 *
 * @yoastapi Indexable_Presention The indexable presentation.
 *
 *
 * @package project_name
 * @version version
 * @author  Carsten Bach
 *
 */
function add_image_to_yoast_opengraph( $presentation ) {
	$generated_image_url = esc_url( sharing_image_poster() );
	if( $generated_image_url ) {
		
		$si_options = get_option( Options\OPTION_NAME );
		$si_config  = get_option( 'sharing_image_config' );
		
		$presentation->open_graph_images = [
			[
				'url'    => $generated_image_url,
				'width'  => $si_options[0]['width'],
				'height' => $si_options[0]['height'],
				'type'   => 'image/'. $si_config['format']
			]
		];
	}

	return $presentation;
}

/**
 * [sharing_image_hide_metatags description]
 * @param bool $hide_header Set true to hide poster meta.
 *
 * @see     https://wpset.org/sharing-image/hooks/#sharing_image_hide_meta
 *
 * @package project_name
 * @version version
 * @author  Carsten Bach
 *
 * @param   bool         $hide_meta [description]
 * @return  [type]                  [description]
function sharing_image_hide_metatags( bool $hide_meta ) : bool {
    return true;
}
 */
