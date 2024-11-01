<?php

/**
 * Plugin Name: Stepped Content
 * Description: Seamlessly organize your information into interactive steps.
 * Version: 1.0.3
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: stepped-content
 */
// ABS PATH
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'stp_fs' ) ) {
    register_activation_hook( __FILE__, function () {
        if ( is_plugin_active( 'stepped-content/index.php' ) ) {
            deactivate_plugins( 'stepped-content/index.php' );
        }
        if ( is_plugin_active( 'stepped-content-pro/index.php' ) ) {
            deactivate_plugins( 'stepped-content-pro/index.php' );
        }
    } );
} else {
    define( 'STP_VERSION', ( isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.3' ) );
    define( 'STP_DIR_URL', plugin_dir_url( __FILE__ ) );
    define( 'STP_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'STP_HAS_FREE', 'stepped-content/index.php' === plugin_basename( __FILE__ ) );
    define( 'STP_HAS_PRO', 'stepped-content-pro/index.php' === plugin_basename( __FILE__ ) );
    if ( !function_exists( 'stp_fs' ) ) {
        function stp_fs() {
            global $stp_fs;
            if ( !isset( $stp_fs ) ) {
                $fsStartPath = dirname( __FILE__ ) . '/freemius/start.php';
                $bSDKInitPath = dirname( __FILE__ ) . '/bplugins_sdk/init.php';
                if ( STP_HAS_PRO && file_exists( $fsStartPath ) ) {
                    require_once $fsStartPath;
                } else {
                    if ( STP_HAS_FREE && file_exists( $bSDKInitPath ) ) {
                        require_once $bSDKInitPath;
                    }
                }
                $stpConfig = [
                    'id'                  => '15887',
                    'slug'                => 'stepped-content',
                    'premium_slug'        => 'stepped-content-pro',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_1ed890cdff6977232e0948f5f2ea8',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'trial'               => [
                        'days'               => 7,
                        'is_require_payment' => true,
                    ],
                    'menu'                => [
                        'slug'    => 'stp-content',
                        'contact' => false,
                        'support' => false,
                        'parent'  => [
                            'slug' => 'tools.php',
                        ],
                    ],
                ];
                $stp_fs = ( STP_HAS_PRO && file_exists( $fsStartPath ) ? fs_dynamic_init( $stpConfig ) : fs_lite_dynamic_init( $stpConfig ) );
            }
            return $stp_fs;
        }

        stp_fs();
        do_action( 'stp_fs_loaded' );
    }
    if ( STP_HAS_PRO ) {
        require_once STP_DIR_PATH . 'inc/Menu.php';
        if ( function_exists( 'stp_fs' ) ) {
            stp_fs()->add_filter( 'freemius_pricing_js_path', function () {
                return STP_DIR_PATH . 'inc/freemius-pricing/freemius-pricing.js';
            } );
        }
    }
    if ( STP_HAS_FREE ) {
        require_once STP_DIR_PATH . 'inc/UpgradePage.php';
    }
    function stpIsPremium() {
        return ( STP_HAS_PRO ? stp_fs()->can_use_premium_code() : false );
    }

    require_once STP_DIR_PATH . 'inc/block.php';
    class STPPlugin {
        function __construct() {
            add_action( 'wp_ajax_stpPipeChecker', [$this, 'stpPipeChecker'] );
            add_action( 'wp_ajax_nopriv_stpPipeChecker', [$this, 'stpPipeChecker'] );
            add_action( 'admin_init', [$this, 'registerSettings'] );
            add_action( 'rest_api_init', [$this, 'registerSettings'] );
        }

        function stpPipeChecker() {
            $nonce = $_POST['_wpnonce'] ?? null;
            if ( !wp_verify_nonce( $nonce, 'wp_ajax' ) ) {
                wp_send_json_error( 'Invalid Request' );
            }
            wp_send_json_success( [
                'isPipe' => stpIsPremium(),
            ] );
        }

        function registerSettings() {
            register_setting( 'stpUtils', 'stpUtils', [
                'show_in_rest'      => [
                    'name'   => 'stpUtils',
                    'schema' => [
                        'type' => 'string',
                    ],
                ],
                'type'              => 'string',
                'default'           => wp_json_encode( [
                    'nonce' => wp_create_nonce( 'wp_ajax' ),
                ] ),
                'sanitize_callback' => 'sanitize_text_field',
            ] );
        }

    }

    new STPPlugin();
}