<?php

/**
 * Plugin Name: Link and Cluster: Automated SEO Link Builder for Your Site
 * Plugin URI: https://linkandcluster.com/linkandcluster
 * Description: An efficient solution for high-performance internal link-building automation.
 * Author: Link and Cluster
 * Author URI: https://linkandcluster.com/
 * Text Domain: laci-link-cluster
 * Domain Path: /languages/i18n
 * Version: 1.0.1
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * @package LACI_InternalLinks
 */

namespace LACI_InternalLinks;

if ( ! defined( 'ABSPATH' ) ) {
    die( 'We\'re sorry, but you can not directly access this file.' );
}

if ( ! defined( 'LACI_INTERNAL_LINKS_VERSION' ) ) {
    define( 'LACI_INTERNAL_LINKS_VERSION', '1.0.1' );
}

if ( ! defined( 'LACI_INTERNAL_LINKS_PLUGIN_PATH' ) ) {
    define( 'LACI_INTERNAL_LINKS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'LACI_INTERNAL_LINKS_PLUGIN_URL' ) ) {
    define( 'LACI_INTERNAL_LINKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'LACI_INTERNAL_LINKS_PLUGIN_BASENAME' ) ) {
    define( 'LACI_INTERNAL_LINKS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'LACI_INTERNAL_LINKS_IS_DEVELOPMENT' ) ) {
    define( 'LACI_INTERNAL_LINKS_IS_DEVELOPMENT', true );
}

if ( ! defined( 'LACI_INTERNAL_LINKS_CUSTOM_TABLE_NAME' ) ) {
    define( 'LACI_INTERNAL_LINKS_CUSTOM_TABLE_NAME', 'laci_custom_table' );
}

if ( ! defined( 'LACI_INTERNAL_LINKS_CUSTOM_NUM_ITEM_LA' ) ) {
    define( 'LACI_INTERNAL_LINKS_CUSTOM_NUM_ITEM_LA', get_option( 'laci_num_item_la', '50' ) );
}

spl_autoload_register(
    function ( $class ) {
        $prefix   = __NAMESPACE__;
        $base_dir = __DIR__ . '/includes';

        $len = strlen( $prefix );
        if ( strncmp( $prefix, $class, $len ) !== 0 ) {
            return;
        }

        $relative_class_name = substr( $class, $len );

        $file = $base_dir . str_replace( '\\', '/', $relative_class_name ) . '.php';

        if ( file_exists( $file ) ) {
            require $file;
        }
    }
);

if ( ! wp_installing() ) {
    if ( ! function_exists( 'LACI_InternalLinks\\init' ) ) {
        \LACI_InternalLinks\Controllers\AdminMenuController::get_instance();
        function init() {
            \LACI_InternalLinks\Initialize::get_instance();
        }
    }

    add_action( 'plugins_loaded', 'LACI_InternalLinks\\init' );
}

// Register deactivation hook
register_deactivation_hook( __FILE__, [ 'LACI_InternalLinks\\Deactivate', 'delete_shortcode' ] );

