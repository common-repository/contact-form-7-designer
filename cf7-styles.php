<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://tinygiantstudios.co.uk
 * @since             1.0.0
 * @package           CF7_Styles
 *
 * @wordpress-plugin
 * Plugin Name:       Contact Form 7 Designer
 * Plugin URI:        https://wordpress.org/plugins/contact-form-7-designer/
 * Description:       Contact Form 7 Designer is an add-on for Contact Form 7 that allows you to add custom designs for your contact form, without requiring any coding knowledge.
 * Version:           2.2
 * Author:            TinyGiantStudios
 * Author URI:        https://tinygiantstudios.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cf7-styles
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
// Add Freemius integration.

if ( !function_exists( 'cf7_styles_freemius' ) ) {
    // Create a helper function for easy SDK access.
    function cf7_styles_freemius()
    {
        global  $cf7_styles_freemius ;
        
        if ( !isset( $cf7_styles_freemius ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $cf7_styles_freemius = fs_dynamic_init( array(
                'id'             => '11514',
                'slug'           => 'cf7-styles',
                'premium_slug'   => 'cf7-styles-pro',
                'type'           => 'plugin',
                'public_key'     => 'pk_c2e280726940d14b3bd3da22892d1',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'    => 'cf7_styles',
                'support' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $cf7_styles_freemius;
    }
    
    // Init Freemius.
    cf7_styles_freemius();
    // Signal that SDK was initiated.
    do_action( 'cf7_styles_freemius_loaded' );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CF7_STYLES_VERSION', '2.2' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cf7-styles.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cf7_styles()
{
    $plugin = new CF7_Styles();
    $plugin->run();
}

run_cf7_styles();