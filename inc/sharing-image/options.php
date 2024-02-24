<?php
/**
 * Figuren_Theater SEO Sharing_Image\Options.
 *
 * @package figuren-theater/ft-seo
 */

namespace Figuren_Theater\SEO\Sharing_Image\Options;

use Figuren_Theater;
use Figuren_Theater\SEO\Sharing_Image;
use WP_DEBUG;
use function add_action;

// Defined at "Sharing_Image_Plugin\Settings::OPTION_TEMPLATES" within the plugin.
const OPTION_NAME = 'sharing_image_templates';

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options(): void {
	

	$_options = [
		'sharing_image_license' => array(
			'premium' => true,
			'develop' => WP_DEBUG,
		),
		'sharing_image_config'  => array(
			'format'       => 'jpg',
			'quality'      => 95,
			'uploads'      => 'default',
			'autogenerate' => 0, // Not a true/false switch, but the index of the templates-array, with the template for autogeneration.
		),
		OPTION_NAME             => array(
			array(
				'preview'    => '',
				'title'      => 'standard',
				'fill'       => '#000000',
				'background' => 'dynamic',
				'width'      => 1200,
				'height'     => 630,
				'layers'     => array(
					0 => array(
						'type'       => 'text',
						'dynamic'    => 'dynamic',
						'title'      => 'Text',
						'content'    => '',
						'sample'     => 'Website mit 💕 für figuren.theater',
						'preset'     => 'title',
						'color'      => '#ffffff',
						'horizontal' => 'left',
						'vertical'   => 'center',
						'fontsize'   => 48,
						'lineheight' => 1.5,
						'fontname'   => 'open-sans',
						'x'          => 100,
						// 'y'          => 100,  is unused & unset by purpose.
						'width'      => 1000,
						// 'height'  is unused & unset by purpose.
					),
					1 => array(
						'type'  => 'image',
						// 'attachment' => 45,   Do not set this at all, to prevent fatal errors.
						'x'     => 25,
						'y'     => 25,
						'width' => 50,
						// 'height' => 50,  is unused & unset by purpose.
					),
					2 => array(
						'type'       => 'text',
						// 'dynamic' => 'dynamic',  is unused & unset by purpose.
						'title'      => 'URL',
						'content'    => 'figuren.theater',
						'sample'     => 'figuren.theater/1234567',
						'preset'     => 'ft_shortlink', // Must be a custom name and not empty.
						'color'      => '#ffffff',
						'horizontal' => 'left',
						'vertical'   => 'top',
						'fontsize'   => 10,
						'lineheight' => 1.5,
						'fontname'   => 'open-sans',
						'x'          => 1000,
						'y'          => 600,
						// 'width'      => 1175,  is unused & unset by purpose.
						// 'height'  => '',  is unused & unset by purpose.
					),
					3 => array(
						'type'      => 'rectangle',
						'color'     => '#ff0000',
						'opacity'   => 35,
						'thickness' => 0,
						'outline'   => 0,
						'x'         => 0,
						'y'         => 0,
						'width'     => 1200,
						'height'    => 630,
					),
					4 => array(
						'type'       => 'filter',
						'blur'       => 'blur',
						'brightness' => 0,
						'contrast'   => 0,
						'blackout'   => 0,
					),
				),
			),
		),
	];

	// Gets added to the 'OptionsCollection' 
	// from within itself on creation.
	new Figuren_Theater\Options\Factory( 
		$_options, 
		'Figuren_Theater\Options\Option', 
		Sharing_Image\BASENAME
	);
}
