<?php
/**
 * Admin Settings Page for Digg This Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Add the Digg This settings menu to the WordPress admin panel.
 *
 * This function registers the settings page under the "Settings" menu
 * in the WordPress admin dashboard.
 *
 * @return void
 */
function digg_this_add_settings_menu() {
    add_options_page(
        __( 'Digg This Settings', 'digg-this' ),
        __( 'Digg This', 'digg-this' ),
        'manage_options',
        'digg-this-settings',
        'digg_this_render_settings_page'
    );
}
add_action( 'admin_menu', 'digg_this_add_settings_menu' );

/**
 * Register the Digg This plugin settings.
 *
 * This function registers the settings group for storing plugin options
 * in the WordPress® database.
 *
 * @since  1.0.0
 * @return void
 */
function digg_this_register_settings() {
    register_setting( 'digg_this_settings_group', 'digg_this_settings' );
}
add_action( 'admin_init', 'digg_this_register_settings' );

/**
 * Enqueue color picker assets.
 * 
 * @since  1.0.0
 * @return void
 */
function digg_this_enqueue_admin_assets( $hook ) {
    if ( 'settings_page_digg-this-settings' !== $hook ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'digg-this-color-picker', DIGG_THIS_PLUGIN_URL . 'assets/js/color-picker.js', [ 'wp-color-picker' ], false, true );
}
add_action( 'admin_enqueue_scripts', 'digg_this_enqueue_admin_assets' );

/**
 * Render the Digg This settings page.
 *
 * This function generates the HTML output for the plugin's settings page in the WordPress admin panel.
 * It allows administrators to enable/disable social media icons and specify which post types should display them.
 *
 * @return void
 */
function digg_this_render_settings_page() {
    $settings           = get_option( 'digg_this_settings', [] );
    $enabled_icons      = $settings['enabled_icons'] ?? [];
    $enabled_post_types = $settings['enabled_post_types'] ?? ['post'];
    $icon_color         = $settings['icon_color'] ?? '#FFFFFF';
    $bg_color           = $settings['bg_color'] ?? '#000000';
    $public_post_types  = get_post_types( [ 'public' => true ], 'objects' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Digg This Settings', 'digg-this' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'digg_this_settings_group' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable Social Media Icons', 'digg-this' ); ?></th>
                    <td>
                        <?php
                        $icons = ['x', 'bluesky', 'mastodon', 'facebook', 'linkedin', 'whatsapp'];
                        $icons = apply_filters( 'digg_this_share_icons', $icons );
                        foreach ( $icons as $icon ) {
                            $checked = in_array( $icon, $enabled_icons, true ) ? 'checked' : '';
                            ?>
                            <label>
                                <input type="checkbox" name="digg_this_settings[enabled_icons][]" value="<?php echo esc_attr( $icon ); ?>" <?php echo $checked; ?>>
                                <?php echo esc_html( ucfirst( $icon ) ); ?>
                            </label><br>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable for Post Types', 'digg-this' ); ?></th>
                    <td>
                        <?php
                        foreach ( $public_post_types as $post_type ) {
                            $checked = in_array( $post_type->name, $enabled_post_types, true ) ? 'checked' : '';
                            ?>
                            <label>
                                <input type="checkbox" name="digg_this_settings[enabled_post_types][]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php echo $checked; ?>>
                                <?php echo esc_html( $post_type->label ); ?>
                            </label><br>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Icon Color', 'digg-this' ); ?></th>
                    <td>
                        <input type="text" class="digg-this-color-picker" name="digg_this_settings[icon_color]" value="<?php echo esc_attr( $icon_color ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Background Color', 'digg-this' ); ?></th>
                    <td>
                        <input type="text" class="digg-this-color-picker" name="digg_this_settings[bg_color]" value="<?php echo esc_attr( $bg_color ); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
