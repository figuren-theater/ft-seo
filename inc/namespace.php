<?php
/**
 * Figuren_Theater SEO.
 *
 * @package figuren-theater/seo
 */

namespace Figuren_Theater\SEO;

use Altis;
use function Altis\register_module;


/**
 * Register module.
 */
function register() {

	$default_settings = [
		'enabled' => true, // needs to be set
	];
	$options = [
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
 */
function bootstrap() {

	// Plugins
	Yoast_SEO\bootstrap();
	
	// Best practices
	//...\bootstrap();
}
