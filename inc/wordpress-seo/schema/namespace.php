<?php
/**
 * Figuren_Theater SEO Yoast_SEO.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO\Yoast_SEO\Schema;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {
	// 1. Remove 'Oragnisation' schema.
	add_filter( 'wpseo_schema_graph_pieces', 'remove_organization_from_schema', 11, 2 );
}


function remove_organization_from_schema( $pieces, $context ) {
	return \array_filter(
		$pieces,
		function ( $piece ) {
			return ! $piece instanceof \Yoast\WP\SEO\Generators\Schema\Organization;
		}
	);
}
