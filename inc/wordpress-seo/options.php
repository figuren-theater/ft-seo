<?php
/**
 * Figuren_Theater SEO Yoast_SEO\Options.
 *
 * @package figuren-theater/seo/yoast_seo\options
 */

namespace Figuren_Theater\SEO\Yoast_SEO\Options;

use Figuren_Theater\SEO\Yoast_SEO;

use Figuren_Theater\API;
use Figuren_Theater\FT;
use Figuren_Theater\Network\Taxonomies;
use Figuren_Theater\Options as FT_Options;

use function __;
use function add_action;
use function get_option;
use function wp_get_attachment_image_url;


/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );
	
	// after the Options_Manager, which runs on 13
	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\re_set_dynamic_options', 15 ); 

}


function filter_options() {
	

	$_options = [
		'yoast_migrations_free' => 0,
		'wpseo_ms' => [
			'access' => 'superadmin',
			'defaultblog' => 1,
			'allow_disableadvanced_meta' => true,
			'allow_ryte_indexability' => false,
			'allow_content_analysis_active' => true,
			'allow_keyword_analysis_active' => true,
			'allow_enable_admin_bar_menu' => false,
			'allow_enable_cornerstone_content' => false, // @TODO enable as advanced FEATURE
			'allow_enable_xml_sitemap' => true,
			'allow_enable_text_link_counter' => true,
			'allow_enable_headless_rest_endpoints' => false,
			'allow_enable_metabox_insights' => false,
			'allow_enable_link_suggestions' => false,
			'allow_tracking' => false,
			'allow_enable_enhanced_slack_sharing' => true,
			'enable_print_qr_code' => true, // This adds a QR code to every page on your site that only shows when that page is printed, so people can find the original URL.
			'allow_semrush_integration_active' => false,
			'allow_zapier_integration_active' => false,
		],
		/**
		 * @see plugins\wordpress-seo\inc\options\class-wpseo-option-titles.php
		 */
		'wpseo_titles' => [
			'forcerewritetitle'                => false,
			'separator'                        => 'sc-dash',

			'title-home-wpseo'                 => '%%sitename%% %%page%% %%sep%% %%sitedesc%%', // Text field.
			'metadesc-home-wpseo'              => '%%sitedesc%%',
			
			'title-author-wpseo'               => '', // WILL BE SET ON ENABLE, due to get_text-calls
			'social-title-author-wpseo'        => '', // WILL BE SET ON ENABLE, due to get_text-calls,
			'metadesc-author-wpseo'            => '', // WILL BE SET ON ENABLE, due to get_text-calls,
			'social-description-author-wpseo'  => '', // WILL BE SET ON ENABLE, due to get_text-calls,
			
			'title-archive-wpseo'              => '%%date%% %%page%% %%sep%% %%sitename%%', // Text field.
			'social-title-archive-wpseo'       => '%%date%% %%page%% %%sep%% %%sitename%%',
			'metadesc-archive-wpseo'           => '', // WILL BE SET ON ENABLE, due to get_text-calls,
			'social-description-archive-wpseo' => '', // WILL BE SET ON ENABLE, due to get_text-calls,

			'title-search-wpseo'               => '', // WILL BE SET ON ENABLE, due to get_text-calls
			'title-404-wpseo'                  => '', // WILL BE SET ON ENABLE, due to get_text-calls

			'social-image-url-author-wpseo'    => '',
			'social-image-url-archive-wpseo'   => '', // WILL BE SET ON ENABLE
			'social-image-id-author-wpseo'     => 0,
			'social-image-id-archive-wpseo'    => 0, // WILL BE SET ON ENABLE
			
			'rssbefore'                        => '',
			'rssafter'                         => '', // WILL BE SET ON ENABLE, due to get_text-calls
			
			'noindex-author-wpseo' => false, // do not use FALSE, because it gets handled as "non existing option", so the query will be done
			'noindex-author-noposts-wpseo' => true,
			'noindex-archive-wpseo' => true,
			'disable-author' => '', // WILL BE SET ON ENABLE
			'disable-date' => false,
			'disable-post_format' => true,
			'disable-attachment' => true,
			#		'is-media-purge-relevant' => false,
			'breadcrumbs-404crumb' => '', // WILL BE SET ON ENABLE, due to get_text-calls
			'breadcrumbs-display-blog-page' => true,
			'breadcrumbs-boldlast' => true,
			'breadcrumbs-archiveprefix' => '', // WILL BE SET ON ENABLE, due to get_text-calls
			'breadcrumbs-enable' => false, // do not use FALSE, because it gets handled as "non existing option", so the query will be done
			'breadcrumbs-home' => '⌂', // 'Start',
			'breadcrumbs-prefix' => '',
			'breadcrumbs-searchprefix' => '', // WILL BE SET ON ENABLE, due to get_text-calls
			'breadcrumbs-sep' => '»',
			'website_name' => '', // WILL BE SET ON ENABLE
			#		'person_name' => '',
			#		'person_logo' => '',
			#		'person_logo_id' => 0,
			#		'alternate_website_name' => '',
			'company_logo' => '', // WILL BE SET ON ENABLE
			'company_logo_id' => '', // WILL BE SET ON ENABLE
			'company_name' => '', // WILL BE SET ON ENABLE
			'company_or_person' => 'company',
			'company_or_person_user_id' => false,
			'stripcategorybase' => true, // IMPORTANT // removes '.' used as category_base // do not use FALSE, because it gets handled as "non existing option", so the query will be done
			'open_graph_frontpage_title' => '%%sitename%%',
			'open_graph_frontpage_desc' => '%%sitedesc%%',
			'open_graph_frontpage_image' => '', // WILL BE SET ON ENABLE
			'open_graph_frontpage_image_id' => 0, // WILL BE SET ON ENABLE

			// PT POST

			'title-post' => '%%title%% %%page%% %%sep%% %%ct_ft_production_shadow%% %%sep%% %%category%% %%sep%% %%sitename%%',
			'metadesc-post' => '%%ft_geolocation_last%%: %%excerpt%%',
			'noindex-post' => false, // false is OK here // do not use FALSE, because it gets handled as "non existing option", so the query will be done
			'display-metabox-pt-post' => true,
			'post_types-post-maintax' => 'category',
			'schema-page-type-post' => 'WebPage',
			'schema-article-type-post' => 'Article',
			'social-title-post' => '%%title%% %%sep%% %%ct_ft_production_shadow%% %%sep%% %%sitename%%',
			'social-description-post' => '%%excerpt%%',
			'social-image-url-post' => '',
			'social-image-id-post' => 0,

			// PT PAGE 

			'title-page' => '%%title%% %%page%% %%sep%% %%parent_title%% %%sep%% %%sitename%%',
			'metadesc-page' => '%%excerpt%%',
			'noindex-page' => false, // THIS HANDLES rel=canonical // false is OK here // do not use FALSE, because it gets handled as "non existing option", so the query will be done
			'display-metabox-pt-page' => true,
			'post_types-page-maintax' => '0',
			'schema-page-type-page' => 'WebPage',
			'schema-article-type-page' => 'None',
			'social-title-page' => '%%title%% %%sep%% %%sitename%%',
			'social-description-page' => '%%excerpt%%',
			'social-image-url-page' => '',
			'social-image-id-page' => 0,

			// PT ATTACHMENT // PT-views are disabled by this plugin 'Yoast SEO'
		
			// 'title-attachment'                => '%%title%% %%sep%% %%sitename%%',
			// 'metadesc-attachment'             => '%%excerpt%%',
			// 'noindex-attachment'              => true, // prevents robots indexing // do not use FALSE, because it gets handled as "non existing option", so the query will be done
			// 'display-metabox-pt-attachment'   => true,
			// 'post_types-attachment-maintax'   => '0',
			// 'schema-page-type-attachment'     => 'WebPage',
			// 'schema-article-type-attachment'  => 'None',

			// TAX CATEGORY

			'title-tax-category'              => '%%term_hierarchy%% %%page%% %%sep%% %%sitename%%',
			'metadesc-tax-category'           => '%%category_description%%',
			'display-metabox-tax-category'    => true,
			'noindex-tax-category'            => false, // false is OK here // do not use FALSE, because it gets handled as "non existing option", so the query will be done
			'social-title-tax-category'       => '%%term_title%% %%sep%% %%sitename%%',
			'social-description-tax-category' => '%%category_description%%',
			'social-image-url-tax-category'   => '',
			'social-image-id-tax-category'    => 0,
			'taxonomy-category-ptparent'      => 'post',

			// TAX POST_TAG
			
			'title-tax-post_tag'              => '%%term_title%% %%page%% %%sep%% %%sitename%%',
			'metadesc-tax-post_tag'           => '%%term_description%%',
			'display-metabox-tax-post_tag'    => true,
			'noindex-tax-post_tag'            => false, // false is OK here // do not use FALSE, because it gets handled as "non existing option", so the query will be done
			'social-title-tax-post_tag'       => '%%term_title%% %%sep%% %%sitename%%',
			'social-description-tax-post_tag' => '%%term_description%%',
			'social-image-url-tax-post_tag'   => '',
			'social-image-id-tax-post_tag'    => 0,
			'taxonomy-post_tag-ptparent'      => 'post',
			
			// TAX POST_FORMAT

			'title-tax-post_format'              => '%%term_title%% %%page%% %%sep%% %%sitename%%',
			'metadesc-tax-post_format'           => '%%term_description%%',
			'display-metabox-tax-post_format'    => false,
			'noindex-tax-post_format'            => true, // prevents robots indexing
			'social-title-tax-post_format'       => '%%term_title%% %%sep%% %%sitename%%',
			'social-description-tax-post_format' => '%%term_description%%',
			'social-image-url-tax-post_format'   => '',
			'social-image-id-tax-post_format'    => 0,
			'taxonomy-post_format-ptparent'      => 'post',


			// PLUGIN 'FORMALITY'

			'title-formality_form' => '%%title%% %%page%% %%sep%% %%sitename%%',
			'metadesc-formality_form' => '',
			'noindex-formality_form' => true,
			'display-metabox-pt-formality_form' => false,
			'post_types-formality_form-maintax' => '0',
			'schema-page-type-formality_form' => 'WebPage',
			'schema-article-type-formality_form' => 'None',
		],

		'wpseo_ryte' => [
			'status' => -1,
			'last_fetch' => 1616325888,
		],
		// 'wpseo' => new Sync\SyncAndMerge( [
		'wpseo' => [
			'dismiss_configuration_workout_notice' => true,
			'tracking' => false,
			// 'license_server_version' => false, // DO NOT HANDLE, because it stores critical data
			'ms_defaults_set' => true,
			'ignore_search_engines_discouraged_notice' => false,
			// 'indexing_first_time' => true, // DO NOT HANDLE, because it stores critical data
			// 'indexing_started' => false, // DO NOT HANDLE, because it stores critical data
			// 'indexing_reason' => 'permalink_settings_changed', // DO NOT HANDLE, because it stores critical data
			// 'indexables_indexing_completed' => false, // DO NOT HANDLE, because it stores critical data
			// 'version' => '16.0.2', // DO NOT HANDLE, because it stores critical data
			// 'previous_version' => '15.5', // DO NOT HANDLE, because it stores critical data
			'disableadvanced_meta' => true,
			'enable_headless_rest_endpoints' => false,
			'ryte_indexability' => false,
			'baiduverify' => '',
			'googleverify' => '',
			'msverify' => '',
			'yandexverify' => '',
			'site_type' => 'smallBusiness', // @see wordpress-seo/inc/options/class-wpseo-option-wpseo.php#L115
			'has_multiple_authors' => '', // WILL BE SET ON ENABLE // will deaktivate the author.php-templates to negletate duplicated-content
			'environment_type' => ('local'=== WP_ENVIRONMENT_TYPE)?'development':WP_ENVIRONMENT_TYPE, // @see wordpress-seo/inc/options/class-wpseo-option-wpseo.php#L130
			'content_analysis_active' => true,
			'keyword_analysis_active' => true,
			'enable_admin_bar_menu' => false,
			'enable_cornerstone_content' => false, // @TODO enable as advanced FEATURE
			'enable_xml_sitemap' => true,
			'enable_text_link_counter' => true,
			'show_onboarding_notice' => false,
			'first_activated_on' => 1616325845,
			'myyoast-oauth' => false,
			'semrush_integration_active' => false,
			'semrush_tokens' => [],
			'semrush_country_code' => 'us',
			'permalink_structure' => '', // WILL BE SET ON ENABLE 
			'home_url' => '', // WILL BE SET ON ENABLE
			'dynamic_permalinks' => false, // Polylang needs it to be TRUE, for many reasons: READ Issue & Commit files of https://github.com/polylang/polylang/pull/907
			'category_base_url' => '.', // WILL BE RE-SET ON ENABLE 
			'tag_base_url' => '!!', // WILL BE RE-SET ON ENABLE 
			'custom_taxonomy_slugs' => array(
				// 'event-venue' => 'events/venues',
				// 'event-category' => 'events/category',
				// 'event-tag' => 'typ',
				'hm-utility' => 'hm-utility',
				Taxonomies\Taxonomy__ft_geolocation::NAME => Taxonomies\Taxonomy__ft_geolocation::SLUG,
				Taxonomies\Taxonomy__ft_site_shadow::NAME => Taxonomies\Taxonomy__ft_site_shadow::SLUG, # 'von',
				'ft_feature_shadow' => 'ft_feature_shadow',
				'ft_level_shadow' => 'ft_level_shadow',
			),
			'enable_enhanced_slack_sharing' => true,
			'enable_print_qr_code' => true, // This adds a QR code to every page on your site that only shows when that page is printed, so people can find the original URL.
			'zapier_integration_active' => false,
			'zapier_subscription' => array(),
			'zapier_api_key' => '',
		// ] ), // former SyncAndMerge
		],
		'wpseo_social' => [
			'facebook_site' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager
			'instagram_url' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager
			'linkedin_url' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager
			'myspace_url' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager
			'og_default_image' => '', // WILL BE SET ON ENABLE
			'og_default_image_id' => '', // WILL BE SET ON ENABLE
			'og_frontpage_title' => '', // WILL BE SET ON ENABLE
			'og_frontpage_desc' => '', // WILL BE SET ON ENABLE
			'og_frontpage_image' => '', // WILL BE SET ON ENABLE
			'og_frontpage_image_id' => '', // WILL BE SET ON ENABLE
			'opengraph' => true,
			'pinterest_url' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager
			// 'pinterestverify' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager (not yet implemented, should be post_meta of 'ft_link')
			'twitter' => true,
			'twitter_site' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager
			'twitter_card_type' => 'summary_large_image',
			'youtube_url' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager
			'wikipedia_url' => '', // DO NOT HANDLE, leave to ft-network-sources-Manager
		],
	];



	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	#new FT_Options\Option(
	#	'wpseo',
	#	$_options['wpseo'],
	#	BASENAME
	#);
	new FT_Options\Option_Merged(
		'wpseo',
		$_options['wpseo'],
		BASENAME
	);

	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new FT_Options\Option(
		'wpseo_ms',
		$_options['wpseo_ms'],
		BASENAME,
		'site_option'
	);


	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new FT_Options\Option(
		'wpseo_titles',
		$_options['wpseo_titles'],
		BASENAME
	);


	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new FT_Options\Option(
		'wpseo_ryte',
		$_options['wpseo_ryte'],
		BASENAME
	);

	// gets added to the 'OptionsCollection' 
	// from within itself on creation
	new FT_Options\Option(
		'wpseo_social',
		$_options['wpseo_social'],
		BASENAME
	);
}


function re_set_dynamic_options() {
	// die(var_export(  $_REQUEST ));
	// die(var_export(  isset($_REQUEST['page']) ));
	// die(var_export(  false !== strpos($_REQUEST['page'], 'wpseo') ));


	$_has_multiple_authors = ( ! Figuren_Theater\FT::site()->has_feature(['einsamer-wolf']) ) ? true : '';

	// fallback
	$_logo_id = $_logo = $_logo_url = '';

	// $_logo_id         = (int) \get_theme_mod( 'custom_logo' );
	// do this only on the frontend 
	// or for the 'wpseo' admin-views
	// 
	// and because we're very early
	// we need to go for REQUEST
	if ( empty($_REQUEST) || ( isset($_REQUEST['page']) && false !== strpos($_REQUEST['page'], 'wpseo')  ) )
	{

		$_logo_id         = (int) get_option( 'site_logo' );
		if (0 < $_logo_id) {
			// 
			// $_logo            = (array) \wp_get_attachment_image_src( $_logo_id , 'full' );
			// $_logo_url        = (string) $_logo[0];
			$_logo_url            = (string) wp_get_attachment_image_url( $_logo_id , 'full' );
		}
	}

	$_blogname            = get_option( 'blogname' );
	$_blogdescription     = get_option( 'blogdescription' );


	$_permalink_structure = Figuren_Theater\API::get('Options')->get( 'permalink_structure' );
	$_category_base_url   = Figuren_Theater\API::get('Options')->get( 'category_base' );
	$_tag_base_url        = Figuren_Theater\API::get('Options')->get( 'tag_base' );
	$_home_url            = get_option( 'home' );

	$_rssafter            = sprintf(
		'<p><small>%1$s</small><!-- %2$s --></p>',
		__('%%POSTLINK%% was published first on %%BLOGLINK%%','figurentheater'),
		sprintf(
			__('using the machines of %1$s','figurentheater'),
			'https://websites.fuer.figuren.theater/'
		)
	);

	// update wpseo
	$_wpseo = Figuren_Theater\API::get('Options')->get( "option_wpseo" );
	//prevent problems with SyncFrom-Options
	// if ($_wpseo->value instanceof Sync\SyncAndMerge__Interface) {
	if ( is_array( $_wpseo->value ) ) {
		// $_wpseo_temp = $_wpseo->value->container;
		$_wpseo_temp = $_wpseo->value;
			$_wpseo_temp['has_multiple_authors']         = $_has_multiple_authors;
			$_wpseo_temp['permalink_structure']          = $_permalink_structure;
			$_wpseo_temp['category_base_url']            = $_category_base_url;
			$_wpseo_temp['tag_base_url']                 = $_tag_base_url;
			$_wpseo_temp['home_url']                     = $_home_url;
		// $_wpseo->set_value( new Sync\SyncAndMerge( $_wpseo_temp ) );
		$_wpseo->set_value( $_wpseo_temp );
	}

	// update wpseo_social
	$_wpseo_social = Figuren_Theater\API::get('Options')->get( "option_wpseo_social" );
	//prevent problems with SyncFrom-Options
	if (is_array( $_wpseo_social->value ) ) {
		$_wpseo_social_temp = $_wpseo_social->value;
			$_wpseo_social_temp['og_default_image']      = $_logo_url;
			$_wpseo_social_temp['og_default_image_id']   = $_logo_id;
			$_wpseo_social_temp['og_frontpage_title']    = $_blogname;
			$_wpseo_social_temp['og_frontpage_desc']     = $_blogdescription;
			$_wpseo_social_temp['og_frontpage_image']    = $_logo_url;
			$_wpseo_social_temp['og_frontpage_image_id'] = $_logo_id;
		$_wpseo_social->set_value( $_wpseo_social_temp );
	}

	// update wpseo_titles
	$_wpseo_titles = Figuren_Theater\API::get('Options')->get( "option_wpseo_titles" );
	//prevent problems with SyncFrom-Options
	if (is_array($_wpseo_titles->value)) {
		$_wpseo_titles_temp = $_wpseo_titles->value;
			$_wpseo_titles_temp['disable-author']               = !$_has_multiple_authors;
			$_wpseo_titles_temp['website_name']                 = $_blogname;
			$_wpseo_titles_temp['company_name']                 = $_blogname;
			
			$_wpseo_titles_temp['company_logo']                 = $_logo_url;
			$_wpseo_titles_temp['open_graph_frontpage_image']   = $_logo_url;
			$_wpseo_titles_temp['social-image-url-archive-wpseo']= $_logo_url;
			$_wpseo_titles_temp['company_logo_id']              = $_logo_id;
			$_wpseo_titles_temp['open_graph_frontpage_image_id']= $_logo_id;
			$_wpseo_titles_temp['social-image-id-archive-wpseo']= $_logo_id;
			
			$_wpseo_titles_temp['rssafter']                     = $_rssafter;
			$_wpseo_titles_temp['breadcrumbs-404crumb']         = __('Error 404: Page not found','figurentheater');
			$_wpseo_titles_temp['breadcrumbs-archiveprefix']    = __('Archives for','figurentheater');
			$_wpseo_titles_temp['breadcrumbs-searchprefix']     = __('You searched for','figurentheater');

			$_wpseo_titles_temp['title-author-wpseo']           = __('%%name%%s publications at %%sitename%% %%page%%','figurentheater');
			$_wpseo_titles_temp['social-title-author-wpseo']    = $_wpseo_titles_temp['title-author-wpseo'];

			$_wpseo_titles_temp['title-search-wpseo']           = __('You searched for %%searchphrase%%, %%page%% %%sep%% %%sitename%%','figurentheater');
			$_wpseo_titles_temp['title-404-wpseo']              = __('%%term404%% not found on %%sitename%%','figurentheater');

			$_wpseo_titles_temp['metadesc-author-wpseo']        = __('All publications for %%sitename%% written by %%name%%. %%user_description%%','figurentheater');
			$_wpseo_titles_temp['social-description-author-wpseo'] = $_wpseo_titles_temp['metadesc-author-wpseo'];

			$_wpseo_titles_temp['metadesc-archive-wpseo']       = __('All publications for %%sitename%%','figurentheater');
			$_wpseo_titles_temp['social-description-archive-wpseo'] = $_wpseo_titles_temp['metadesc-archive-wpseo'];

		$_wpseo_titles->set_value( $_wpseo_titles_temp );
	}
}
