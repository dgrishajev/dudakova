<?php
/*
Plugin Name: WPML Translation Management
Plugin URI: https://wpml.org/
Description: Add a complete translation process for WPML | <a href="https://wpml.org">Documentation</a> | <a href="https://wpml.org/version/wpml-3-2/">WPML 3.2 release notes</a>
Author: OnTheGoSystems
Author URI: http://www.onthegosystems.com/
Version: 2.0.6-b1
Plugin Slug: wpml-translation-management
*/

if ( defined( 'WPML_TM_VERSION' ) ) {
	return;
}

$bundle = json_decode( file_get_contents( dirname( __FILE__ ) . '/wpml-dependencies.json' ), true );
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
	$sp_version_stripped = ICL_SITEPRESS_VERSION;
	$dev_or_beta_pos = strpos( ICL_SITEPRESS_VERSION, '-' );
	if ( $dev_or_beta_pos > 0 ) {
		$sp_version_stripped = substr( ICL_SITEPRESS_VERSION, 0, $dev_or_beta_pos );
	}
	if ( version_compare( $sp_version_stripped, $bundle[ 'sitepress-multilingual-cms' ], '<' ) ) {
		return;
	}
}

define( 'WPML_TM_VERSION', '2.0.6' );

// Do not uncomment the following line!
// If you need to use this constant, use it in the wp-config.php file
//define( 'WPML_TM_DEV_VERSION', '2.0.3-dev' );

define( 'WPML_TM_PATH', dirname( __FILE__ ) );

require_once "lib/wpml-tm-autoloader.class.php";

require WPML_TM_PATH . '/inc/wpml-dependencies-check/wpml-bundle-check.class.php';
require WPML_TM_PATH . '/inc/constants.php';
require WPML_TM_PATH . '/inc/translation-proxy/wpml-pro-translation.class.php';
require WPML_TM_PATH . '/inc/wpml-translation-management.class.php';
require WPML_TM_PATH . '/inc/wpml-translation-management-xliff.class.php';
require WPML_TM_PATH . '/inc/functions-load.php';

function wpml_tm_load_ui() {
	global $sitepress, $wpdb, $WPML_Translation_Management;
	$WPML_Translation_Management = new WPML_Translation_Management();

	$wpml_wp_api      = new WPML_WP_API();
	$TranslationProxy = new WPML_Translation_Proxy_API();
	new WPML_TM_Troubleshooting_Reset_Pro_Trans_Config( $sitepress, $TranslationProxy, $wpml_wp_api, $wpdb );
	new WPML_TM_Troubleshooting_Clear_TS( $wpml_wp_api );
}

add_action( 'wpml_loaded', 'wpml_tm_load_ui' );

function wpml_tm_word_count_init() {
	global $sitepress, $wpdb;
	$wpml_wp_api = new WPML_WP_API();

	new WPML_Twig();
	$wpml_tm_words_count = new WPML_TM_Words_Count( $sitepress, $wpml_wp_api, $wpdb );
	new WPML_TM_Words_Count_Resources( $wpml_wp_api );
	new WPML_TM_Words_Count_Box_UI( $wpml_tm_words_count, $wpml_wp_api );
	$wpml_tm_words_count_summary = new WPML_TM_Words_Count_Summary_UI( $wpml_tm_words_count, $wpml_wp_api );
	new WPML_TM_Words_Count_AJAX( $wpml_tm_words_count, $wpml_tm_words_count_summary, $wpml_wp_api );
}

add_action( 'wpml_tm_loaded', 'wpml_tm_word_count_init' );