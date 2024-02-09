<?php
/**
 * Figuren_Theater SEO.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO;

use Altis;
// use function Altis\register_module;


/**
 * Register module.
 *
 * @return void
 */
function register(): void {

	$default_settings = [
		'enabled'       => true, // needs to be set
		'sharing-image' => false,
	];
	$options          = [
		'defaults' => $default_settings,
	];

	Altis\register_module(
		'seo',
		DIRECTORY,
		'SEO',
		$options,
		__NAMESPACE__ . '\\bootstrap'
	);
}

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	// Plugins
	// Sharing_Image\bootstrap(); !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	Yoast_SEO\bootstrap();
	
	// Best practices
	// ...\bootstrap();
}
