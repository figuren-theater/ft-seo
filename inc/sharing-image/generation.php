<?php
/**
 * Figuren_Theater SEO Sharing_Image\Generation.
 *
 * @package figuren-theater/seo/sharing_image\generation
 */

namespace Figuren_Theater\SEO\Sharing_Image\Generation;

use Figuren_Theater\SEO\Sharing_Image; // POST_TYPE_SUPPORT

use Figuren_Theater\Media\Image_Optimzation;
use Sharing_Image as Sharing_Image_Plugin;

// from ft_FEATURES__customizer-powered-login.php
use function ft_get_relevant_colors;

use WP_Post;

use function add_action;
use function delete_post_meta;
use function esc_url;
use function get_attached_file;
use function get_option;
use function get_post_meta;
use function get_post_parent;
use function get_post_thumbnail_id;
use function get_post_type;
use function post_type_supports;
use function site_url;
use function wp_get_shortlink;
use function wp_get_upload_dir;
use function wp_installing;
use function wp_is_post_revision;

const LAYER = [
	'title'   => 0,
	'logo'    => 1,
	'url'     => 2,
	'overlay' => 3,
];

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {
	// add_action( 'admin_init', __NAMESPACE__ . '\\load', 0 );
	add_action( 'init', __NAMESPACE__ . '\\load', 0 ); // Figuren_Theater\Media\Auto_Featured_Image runs on 10
	// add_action( 'admin_init', __NAMESPACE__ . '\\load', 0 ); // Figuren_Theater\Media\Auto_Featured_Image runs on 10
	// add_action( 'rest_api_init', __NAMESPACE__ . '\\load', 0 ); // Figuren_Theater\Media\Auto_Featured_Image runs on 10


	// add_action( 'updated_post_meta', __NAMESPACE__ . '\\trigger_autogeneration', 10, 4 );

}

function load() {

	////////////////////////////////////
	// BACKEND | Autogeneration logic //
	// triggered on 'wp_insert_post'  //
	////////////////////////////////////

	// helper (just) to get current post_id
	// used because it is (one of) the earliest point possible 
	// to hook into the autogeneration-process
	// add_filter( 'sharing_image_disable_autogeneration', __NAMESPACE__ . '\\persist_current_post', 10, 2 );
	
	// update static, with more dynamic, site-specific options
	add_filter( 'pre_option_sharing_image_templates', __NAMESPACE__ . '\\pre_option_sharing_image_templates', 20 );

	// Re-Enables default func.
	// of using the featured-image as background-image
	// which is not happening reliable on autogeneration
	// 
	// BUGGY
	add_filter( 'sharing_image_prepare_template', __NAMESPACE__ . '\\sharing_image_prepare_template', 10, 3 );


	add_filter( 'sharing_image_autogenerated_poster', __NAMESPACE__ . '\\delete_previous_image', 10, 2 );


	////////////////////////////////////
	// BACKEND | Autogeneration logic //
	// triggered on 'updated_post_meta'  //
	////////////////////////////////////

	// Re-Enables default func.
	// of using the featured-image as background-image
	// which is not happening reliable on autogeneration
	// 
	add_action( 'updated_post_meta', __NAMESPACE__ . '\\trigger_autogeneration', 10, 4 );


	//	add_action( 'delete_post', __NAMESPACE__ . '\\delete_generated_image', 10, 2 );
	// 'delete_post' is too late, because the relevant post_meta is already deleted
	/**
	 * Fires before a post is deleted, at the start of wp_delete_post().
	 *
	 * @since 3.2.0
	 * @since 5.5.0 Added the `$post` parameter.
	 *
	 * @see wp_delete_post()
	 *
	 * @param int     $postid Post ID.
	 * @param WP_Post $post   Post object.
	 */
	add_action( 'before_delete_post', __NAMESPACE__ . '\\delete_generated_image', 10, 2 );
}


/**
 * The only way to get into that process 
 * and get the ID of the current edited post
 *
 * @package project_name
 * @version version
 * @author  Carsten Bach
 *
 * @param   bool         $disable_autogeneration [description]
 * @param   [type]       $post_id                [description]
 * @return  [type]                               [description]
 */
function persist_current_post( bool $disable_autogeneration, $post_id) : bool {
	if ( wp_installing() )
		return true;


	//
	// if ( wp_is_post_revision( $post_id ) ) {
		// $this->current_post = get_post_parent( $post_id );
		// __current_post( get_post_parent( $post_id ) );
		// currentPost::init()->get_id( get_post_parent( $post_id ) );
	// THIS COULD ALÖSO BE
	// 
	if ( wp_is_post_revision( $post_id ) ) {
		$post_id = get_post_parent( $post_id )->ID; // NEW

		//
		// TRY OUT
		// disbaling this for all revisiosn
		// return true;#

error_log(var_export([
	__FUNCTION__ . '//wp_is_post_revision()',
	// currentPost::init()->get_id(),
	$post_id
],true));

	} else {
	// persist post_id
	// $this->current_post = $post_id;
	// __current_post( $post_id );
	// currentPost::init()->get_id( $post_id );
error_log(var_export([
	__FUNCTION__,
	// currentPost::init()->get_id(),
	$post_id
],true));

	}

	// THIS COULD ALÖSO BE, v2
	// 
	// Make sure meta is got for the post, not for a revision.
	#$the_post = wp_is_post_revision( $post_id );
	#if ( $the_post ) {
	#	$post_id = $the_post;
	#}



	//
	if ( ! post_type_supports( 
		// get_post_type( $this->current_post ), 
		// get_post_type( __current_post() ), 
		get_post_type( $post_id ), 
		Sharing_Image\POST_TYPE_SUPPORT )
	) {
		return true;
	}

	// and go on
	// nothing to see here
	return $disable_autogeneration;
}

function pre_option_sharing_image_templates( $option ) : array|bool {

global $post;

if (null === $post) {
	$post = \get_post();


	$_request_path = parse_url( $_SERVER['REQUEST_URI'] )['path'];
	$_is_json_route = ( 0 === strpos($_request_path, '/wp-json/wp/v2/posts/') );
}
if (null === $post && $_is_json_route ) {
	$post_id = (int) str_replace(
		'/wp-json/wp/v2/posts/',
		'',
		\untrailingslashit( $_request_path )
	);

	$post = \get_post( $post_id );
}



// global $wp, $wp_query;
error_log(var_export([
	__FUNCTION__ . '\\DOING_AJAX',
	\wp_debug_backtrace_summary(),
	// parse_url( $_SERVER['REQUEST_URI'] ),
	// $GLOBALS,
	// $wp,
	// $wp_query,
	// \get_post(),
	$option,
	'EXTRACTED :::: ',
	$post->ID,

],true));
if ( defined('DOING_AJAX')) {
}

	

	// when this original option is set 
	// during SiteSetup or within our weekly cron job
	// this filter should return false
	if ( ! is_array( $option ) )
		return $option;


	// $template = $this->options[ $this->option_name ][0];
	$template = $option[0];

    // 
    $template = __get_site_logo($template);
    
    // 
    $template = __get_theme_color($template);
 	// $template['fill']               = '#0000ff'; // Testing this filter
 	// $template['layers'][3]['color'] = '#0000ff'; // Testing this filter // ['layers'][3] == rectangle
  
    // 
    $template = __get_featured_image($template, $post);
    
    // 
    $template = __get_shortlink($template, $post);

    // BEWARE
    // do this last
    // because the order of the layers gets modified
    // and so LAYER might break
    // 
	

	/*
	// if ('will-trigger-never' === \get_post_type( $this->current_post )) {
	if ('will-trigger-never' === \get_post_type( __current_post() )) {
    // if ( Post_Types\Post_Type__ft_production::NAME === \get_post_type( $this->current_post )) {

    	$_new_text_layer = array (
			'type' => 'text',
			'dynamic' => 0,
			'title' => 'Dauer',
			'content' => 'Dauer: 75 Minuten', // @TODO
			'sample' => 'Dauer: 75 Minuten', // @TODO
			'preset' => 'title',
			'color' => '#ffffff', // @TODO
			'horizontal' => 'left',
			'vertical' => 'top',
			'fontsize' => 10,
			'lineheight' => 1.5,
			'fontname' => 'open-sans',
			'x' => 100,
			'y' => 595,
			'width' => 1000,
			// 'height' => '',
		);
		// v1
		// $template['layers'][4] = $_new_text_layer;
		// v2
		array_unshift($template['layers'], $_new_text_layer);


    	$_new_text_layer = array (
			'type' => 'text',
			'dynamic' => 0,
			'title' => 'Zielgruppe',
			'content' => 'für alle ab 5 Jahren', // @TODO
			'sample' => 'für alle ab 5 Jahren', // @TODO
			'preset' => 'title',
			'color' => '#ffffff', // @TODO
			'horizontal' => 'left',
			'vertical' => 'top',
			'fontsize' => 10,
			'lineheight' => 1.5,
			'fontname' => 'open-sans',
			'x' => 300,
			'y' => 595,
			'width' => 800,
			// 'height' => '',
		);
		// v1
		// $template['layers'][4] = $_new_text_layer;
		// v2
		array_unshift($template['layers'], $_new_text_layer);
    }
	*/
    
    //
	$option[0] = $template;
/*
error_log(var_export([
	__FUNCTION__,
	\wp_debug_backtrace_summary(),
	// currentPost::init()->get_id(),
	$post->ID,
	$_REQUEST,
	$_POST,
	$_GET,
	$_SERVER,
	// \get_post(),
	// $option
],true));*/
	
	//
	return $option;
}

function sharing_image_prepare_template($template, $fieldset, $index) {
	// $thumbnail_id = \get_post_thumbnail_id( $this->current_post );
	error_log( var_export([
		__FUNCTION__,
		\wp_debug_backtrace_summary(),
		\get_post(),
	],true) );		

	// 0.
	if ( isset($template['image']) && \esc_url( $template['image'] ))
		return $template;

	// 1.'generate_template()' @ plugins\sharing-image\classes\class-generator.php
	// $thumbnail_id = \get_post_thumbnail_id( $this->current_post );
	$thumbnail_id = \get_post_thumbnail_id();
	if ( ! empty( $thumbnail_id ) ) {
		$fieldset['attachment'] = $thumbnail_id;
	}

	// 2.'prepare_template()' @ plugins\sharing-image\classes\class-generator.php
	if ( ! empty( $fieldset['attachment'] ) ) {
		$template['image'] = \get_attached_file( $fieldset['attachment'] );
	}

	return $template;
}

/**
 * Delete previous, old autogenerated-image
 * to save disk-space
 * 
 * The 'sharing_image_autogenerated_poster' filter normally 
 * "Filters autogenerated poster data."
 *
 * but we can use it to check, 
 * if we have a new image 
 * and if so, delete the old one.
 *
 * @since Sharing_Image 2.0.11
 *
 * @param array|false $poster  Poster image, width and height data or false if undefined.
 * @param integer     $post_id Post ID.
 */
function delete_previous_image( $poster, $post_id ) : array|false {
	// return early
	// if we have no new image
	if ( false === $poster )
		return $poster;
error_log(var_export([
	__FUNCTION__,
#	currentPost::init()->get_id(),
	$post_id
],true));
	// compress image
	// this makes 25kb > 15kb and 93kb > 49kb
	$_path = __get_path_from_url( $poster['poster'] );
	// $file_put_contents = ft_proto__image_replace( $_path );
	$file_put_contents = Image_Optimzation\replace( $_path );

	// return early
	// if it is a revision
	// 
	// !! not needed, because 'update_post_meta()'
	// !! does correct the post_id to point to the post_parent
	// 
	// if ( \wp_is_post_revision( $post_id ) )
	// 	return false;

	// grab the last image from post_meta,
	// before it gets updated
	// which happens directly after this filter
	// @plugins\sharing-image\classes\class-widget.php

	// CLONED FROM: // 'update_post_meta()'
	// Make sure meta is got for the post, not for a revision.
	#$the_post = wp_is_post_revision( $post_id );
	#if ( $the_post ) {
	#	$post_id = $the_post;
	#}
	if ( wp_is_post_revision( $post_id ) ) {
		$post_id = get_post_parent( $post_id )->ID; // NEW
	}
	// CLONED FROM: // 'update_post_meta()'
error_log(var_export([
	__FUNCTION__,
	#currentPost::init()->get_id(),
	$post_id
],true));


	// get old image data, if any
	$old_poster = get_post_meta( 
		$post_id, 
		Sharing_Image_Plugin\Widget::WIDGET_META, 
		true
	);
	// could be replaced with 
	// $old_poster = \sharing_image_poster( $post_id );

	// return
	// if there was no image, yet
	if ( ! isset( $old_poster['poster'] ) || ! esc_url( $old_poster['poster'] ) )
		return $poster;

	// here, we know
	// we already had one image,
	// so we have a valid url
	// so find its path
	// and delete it
	$old_poster_path = __get_path_from_url( $old_poster['poster'] );
	if ( file_exists( $old_poster_path ) )
		unlink( $old_poster_path );

	// bye bye
	// and return the un-modified, new image (data)
	return $poster;
}

/**
 * By default the autogenartion is not done, when just swapping featured-images,
 * because it is not the whole $post that is changed, but only some metadata.
 *
 * So we have another action hooked onto the change of the featured-image meta,
 * which triggers the autogenartion.
 *
 * ---
 * 
 * Fires immediately after updating metadata of a specific type.
 *
 * The dynamic portion of the hook name, `$meta_type`, refers to the meta object type
 * (post, comment, term, user, or any other type with an associated meta table).
 *
 * Possible hook names include:
 *
 *  - `updated_post_meta`
 *  - `updated_comment_meta`
 *  - `updated_term_meta`
 *  - `updated_user_meta`
 *
 * @since 2.9.0
 *
 * @param int    $meta_id     ID of updated metadata entry.
 * @param int    $object_id   ID of the object metadata is for.
 * @param string $meta_key    Metadata key.
 * @param mixed  $_meta_value Metadata value.
 */
function trigger_autogeneration( $meta_id, $object_id, $meta_key, $_meta_value ) : void {
	if ('_thumbnail_id' !== $meta_key)
		return;

error_log(var_export([
	__FUNCTION__ . 'BEFORE wp_is_post_revision( $object_id )',
	// currentPost::init()->get_id(),
	$object_id
],true));

	if ( wp_is_post_revision( $object_id ) )
		return;


error_log(var_export([
	__FUNCTION__ . 'AFTER wp_is_post_revision( $object_id )',
	// currentPost::init()->get_id(),
	$object_id
],true));

/*



	// helper (just) to get current post_id
	// used because it is (one of) the earliest point possible 
	// to hook into the autogeneration-process
	add_filter( 'sharing_image_disable_autogeneration', __NAMESPACE__ . '\\persist_current_post', 10, 2 );
	
	// update static, with more dynamic, site-specific options
	add_filter( 'pre_option_sharing_image_templates', __NAMESPACE__ . '\\pre_option_sharing_image_templates', 20 );

	// Re-Enables default func.
	// of using the featured-image as background-image
	// which is not happening reliable on autogeneration
	// 
	// BUGGY
	// add_...('sharing_image_prepare_template', __NAMESPACE__ . '\\sharing_image_prepare_template', 10, 3 );


	add_filter( 'sharing_image_autogenerated_poster', __NAMESPACE__ . '\\delete_previous_image', 10, 2 );



*/




	// autogenerate_poster() does nothing because
	// a meta['poster'] is not empty
	// 
	// so we have to delete this at first
	// to make sure this runs properly
	delete_post_meta( 
		$object_id, 
		Sharing_Image_Plugin\Widget::WIDGET_META 
	);

	// make sure this is set
	// __current_post( $object_id );
#	currentPost::init()->get_id( $object_id );
error_log(var_export([
	__FUNCTION__,
#	currentPost::init()->get_id(),
	$object_id
],true));

	
	// Generate new poster data using post data.
	// ( new Sharing_Image\Meta() )->get_poster( $object_id );
	$SIW = new Sharing_Image_Plugin\Widget();
	$source = ( $SIW )->autogenerate_poster( $object_id );
}


function delete_generated_image( int $post_id, WP_Post $post ) {
	if ( ! post_type_supports( $post->post_type, Sharing_Image\POST_TYPE_SUPPORT ) )
		return;
error_log(var_export([
	__FUNCTION__,
	// currentPost::init()->get_id(),
	$post_id
],true));

	// 
	$_sharing_image = get_post_meta( $post_id, Sharing_Image_Plugin\Widget::WIDGET_META, true );
	// could be replaced with 
	// $_sharing_image = \sharing_image_poster( $post_id );

	//
	if ( ! isset( $_sharing_image['poster'] ) || ! esc_url( $_sharing_image['poster'] ) )
	// if ( ! \esc_url( $_sharing_image ) )
		return;


	$_sharing_image_path = __get_path_from_url( $_sharing_image['poster'] );
	// $_sharing_image_path = $this->__get_path_from_url($_sharing_image);
	if (empty( $_sharing_image_path ) )
		return;

	unlink( $_sharing_image_path );
}


/**
 * UNUSED right now
 * 
 * [__prepare_text_layer description]
 *
 * @subpackage [subpackage]
 * @version    2022-10-05
 * @author     Carsten Bach
 *
 * @param      string       $layer_name [description]
 * @param      string       $new_text   [description]
 * @return     [type]                   [description]
function __prepare_text_layer( $layer_name = 'title', $new_text = '' ) {
	// which index has this layer
	// in the array of saved layers for this template
	$layer_index = LAYER[ $layer_name ];
	
	// update data
	$this->site_specific_options[0]['layers'][$layer_index]['sample']  = $new_text;
	$this->site_specific_options[0]['layers'][$layer_index]['content'] = $new_text;
	
	return $this->site_specific_options;
}
 */


function __get_site_logo( array $template ) : array {
	// which index has this layer
	// in the array of saved layers for this template
	$layer_index = LAYER[ 'logo' ];

	// get site-logo ID
	$logo = get_option( 'site_icon' );

	if (empty($logo)) {
		// the plugin itself checks if 'attachment' isset()
		unset( $template['layers'][$layer_index]['attachment'] );
	} else {
		$template['layers'][$layer_index]['attachment'] = $logo;
	}
				
	return $template;
}

function __get_theme_color( array $template ) : array {
	// get colors from gutenberg
	// from mu-plugins\ft_FEATURES__customizer-powered-login.php
	// 
	// >>> array ( 'ft_background' => '#0f0b0e', 'ft_accent' => '#d20394', 'ft_text' => '#fbf9fa', )
	extract( ft_get_relevant_colors() );
	
	// prepare color options with site-specific stuff
	$template['fill']                                              = $ft_background;
	$template['layers'][ LAYER['title']   ]['color'] = $ft_text;   // ['layers'][0] == text
	$template['layers'][ LAYER['url']     ]['color'] = $ft_text;   // ['layers'][2] == text
	$template['layers'][ LAYER['overlay'] ]['color'] = $ft_accent; // ['layers'][3] == rectangle

	return $template;
}

function __get_featured_image( array $template, WP_Post|null $post ) : array {
	// 0.
	if ( isset($template['image']) && esc_url( $template['image'] ))
		return $template;

	// 1.'generate_template()' @ plugins\sharing-image\classes\class-generator.php
	// $thumbnail_id = get_post_thumbnail_id( __current_post() );
	// $thumbnail_id = get_post_thumbnail_id( currentPost::init()->get_id() );
	$thumbnail_id = get_post_thumbnail_id( $post );
	if ( ! empty( $thumbnail_id ) ) {
		$template['image'] = get_attached_file( $thumbnail_id );
	}

	return $template;
}

function __get_shortlink( array $template, WP_Post|null $post ) : array {
	// prepare url
	$url = null;	

	// get url of current post or site
	if ( empty( $url ) )
		// $url = wp_get_shortlink( __current_post() );
		// $url = wp_get_shortlink( currentPost::init()->get_id() );
		$url = wp_get_shortlink( $post );

	// fallback
	if ( empty( $url ) )
		$url = wp_get_shortlink();

	// still empty ?
	if ( empty( $url ) )
		$url = site_url();

	if ( esc_url( $url ) ) {
		
		// remove protocoll
		$url = str_replace('https://', '', $url);

		// set data
		$template['layers'][ LAYER['url'] ]['sample']  = $url;
		$template['layers'][ LAYER['url'] ]['content'] = $url;
		// __prepare_text_layer( 'url', $url ); // this safes into prop
	}			

	return $template;
}

function __get_path_from_url( string $url='' ) : string {
	//
	$wp_upload_dir = wp_get_upload_dir();

	$path = str_replace(
		$wp_upload_dir['baseurl'],
		$wp_upload_dir['basedir'],
		$url
	);

	if ( ! file_exists( $path ) )
		return '';

	return $path;
}

/*

function __current_post( int $post_id=0 ) : int {
	if ( ! $current_post_id ) {
		$current_post_id = 0;
	}
	if ( 0 < $post_id ) {
		static $current_post_id = $post_id;
	}
	return $current_post_id;
}
 */

/**
 * Ugly helper to persist the post_ID
 * during one request, but one different
 * action hooks
 */
class currentPost {
	
	public $id = 0;

	public function get_id( int $post_id=0 ) {

		if ( 0 < $post_id ) {
			$this->id = $post_id;
		}

error_log( current_filter() );
error_log( spl_object_hash( $this ) );
error_log($this->id);
		return $this->id;
	}

	public static function init()
	{
		static $instance;

		if ( NULL === $instance ) {
			$instance = new self;
		}

		return $instance;
	}
}