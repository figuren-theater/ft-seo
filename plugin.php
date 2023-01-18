<?php
/**
 * Plugin Name:     figuren.theater | SEO
 * Plugin URI:      https://github.com/figuren-theater/ft-seo
 * Description:     Data integration dedicated to search engines and social media plattforms for all sites of the WordPress multisite network figuren.theater
 * Author:          figuren.theater
 * Author URI:      https://figuren.theater
 * Text Domain:     figurentheater
 * Domain Path:     /languages
 * Version:         1.0.4
 *
 * @package         Figuren_Theater\Seo
 */

namespace Figuren_Theater\SEO;

const DIRECTORY = __DIR__;

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
