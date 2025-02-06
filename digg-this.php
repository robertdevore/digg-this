<?php

/**
  * The plugin bootstrap file
  *
  * @link              https://robertdevore.com
  * @since             1.0.0
  * @package           Digg_This
  *
  * @wordpress-plugin
  *
  * Plugin Name: Digg This
  * Description: A social media sharing plugin with customizable settings.
  * Plugin URI:  https://github.com/robertdevore/digg-this/
  * Version:     1.0.0
  * Author:      Robert DeVore
  * Author URI:  https://robertdevore.com/
  * License:     GPL-2.0+
  * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
  * Text Domain: digg-this
  * Domain Path: /languages
  * Update URI:  https://github.com/robertdevore/digg-this/
  */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

require 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/robertdevore/digg-this/',
	__FILE__,
	'digg-this'
);

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch( 'main' );

// Check if Composer's autoloader is already registered globally.
if ( ! class_exists( 'RobertDevore\WPComCheck\WPComPluginHandler' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use RobertDevore\WPComCheck\WPComPluginHandler;

new WPComPluginHandler( plugin_basename( __FILE__ ), 'https://robertdevore.com/why-this-plugin-doesnt-support-wordpress-com-hosting/' );

// Define constants.
define( 'DIGG_THIS_VERSION', '1.0.0' );
define( 'DIGG_THIS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DIGG_THIS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files.
require_once DIGG_THIS_PLUGIN_DIR . 'includes/admin-settings.php';
require_once DIGG_THIS_PLUGIN_DIR . 'includes/metabox.php';
require_once DIGG_THIS_PLUGIN_DIR . 'includes/display-icons.php';

/**
 * Load plugin text domain for translations
 * 
 * @since  1.0.0
 * @return void
 */
function digg_this_load_textdomain() {
    load_plugin_textdomain( 
        'digg-this',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages/'
    );
}
add_action( 'plugins_loaded', 'digg_this_load_textdomain' );

/**
 * Activation hook for the Digg This plugin.
 *
 * This function sets up default settings upon plugin activation,
 * including enabled social media icons and supported post types.
 *
 * @since  1.0.0
 * @return void
 */
function digg_this_activate() {

    $icons = apply_filters( 'digg_this_share_icons', ['x', 'bluesky', 'mastodon', 'facebook', 'linkedin', 'whatsapp'] );

    $default_settings = [
        'enabled_icons'      => $icons,
        'enabled_post_types' => ['post'],
    ];
    update_option( 'digg_this_settings', $default_settings );
}
register_activation_hook( __FILE__, 'digg_this_activate' );

/**
 * Enqueue frontend styles and dynamically apply user-selected colors.
 *
 * This function ensures that styles are only loaded on singular posts of 
 * the selected post types. It also applies inline CSS to set the icon stroke 
 * color and background color based on admin settings.
 *
 * @since  1.0.0
 * @return void
 */
function digg_this_enqueue_single_assets() {
    if ( ! is_singular() ) {
        return;
    }

    $settings           = get_option( 'digg_this_settings', [] );
    $enabled_post_types = $settings['enabled_post_types'] ?? ['post'];
    $icon_color         = $settings['icon_color'] ?? '#000000';
    $bg_color           = $settings['bg_color'] ?? '#000000';

    if ( in_array( get_post_type(), $enabled_post_types, true ) ) {
        wp_enqueue_style( 'digg-this-style', DIGG_THIS_PLUGIN_URL . 'assets/css/digg-this.css' );
  
        // Inline CSS to dynamically apply the selected colors
        $custom_css = "
            .digg-this-icon svg {
                stroke: {$icon_color} !important;
            }
            a.digg-this-icon {
                background: {$bg_color} !important;
                display: flex;
                border-radius: 50%;
                padding: 12px;
                box-sizing: border-box;
                position: relative;
                margin: 0 6px;
                color: transparent;
            }
            a.digg-this-icon:hover,
            a.digg-this-icon:focus,
            a.digg-this-icon:active {
                color: transparent;
            }
            .digg-this-sharing-icons {
                display: flex;
                flex-direction: row;
                width: auto;
                align-items: start;
                align-content: center;
            }
        ";
        wp_add_inline_style( 'digg-this-style', $custom_css );
    }
}
add_action( 'wp_enqueue_scripts', 'digg_this_enqueue_single_assets' );
