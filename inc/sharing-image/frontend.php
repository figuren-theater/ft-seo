<?php
/**
 * Figuren_Theater SEO Sharing_Image\Frontend.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO\Sharing_Image\Frontend;

use Figuren_Theater\SEO\Sharing_Image\Options;
use Yoast\WP\SEO\Presentations\Indexable_Presentation;

use function sharing_image_poster;

use function add_filter;
use function esc_url;
use function get_option;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {


	/**
	 * Remove <meta og:tags>, which are done by yoast.
	 * 
	 * @see https://wpset.org/sharing-image/hooks/#sharing_image_hide_meta
	 */
	add_filter( 'sharing_image_hide_meta', '__return_true' );

	/**
	 * Because 'wpseo_opengraph_image_size' FILTER IS BUGGY
	 * 
	 * we can not use the already working 
	 * - 'wpseo_opengraph_image' and
	 * - 'wpseo_twitter_image' filters.
	 * 
	 * @see "Wrong image width and height when using "wpseo_opengraph_image" filter" https://github.com/Yoast/wordpress-seo/issues/15052
	 * @see working solution from the coments https://github.com/Yoast/wordpress-seo/issues/15052#issuecomment-695093003
	 */
	add_filter( 'wpseo_frontend_presentation', __NAMESPACE__ . '\\add_image_to_yoast_opengraph', 30 );
}


/**
 * Filter 'wpseo_frontend_presentation' - Allow filtering the presentation used to output our meta values.
 *
 * @see  https://github.com/Yoast/wordpress-seo/blob/76986597983c63c7c66fed3e9d07174f62cb2657/src/integrations/front-end-integration.php#L314
 *
 * @param Indexable_Presentation $presentation The indexable presentation.
 * 
 * @return Indexable_Presentation
 */
function add_image_to_yoast_opengraph( Indexable_Presentation $presentation ) :Indexable_Presentation {
	$generated_image_url = esc_url( sharing_image_poster() );
	if ( $generated_image_url ) {
		
		$si_options = get_option( Options\OPTION_NAME );
		$si_config  = get_option( 'sharing_image_config' );

		if ( 
		  ! \is_array( $si_options ) 
		  || ! \is_array( $si_options[0] )
		  || ! \is_array( $si_config )
		  || ! isset( $si_options[0]['width'] ) 
		  || ! isset( $si_options[0]['height'] ) 
		  || ! isset( $si_config['format'] )
		) {
			return $presentation;
		}
		
		$presentation->open_graph_images = [
			[
				'url'    => $generated_image_url,
				'width'  => $si_options[0]['width'],
				'height' => $si_options[0]['height'],
				'type'   => 'image/' . $si_config['format'],
			],
		];
	}

	return $presentation;
}

