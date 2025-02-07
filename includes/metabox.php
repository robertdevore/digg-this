<?php
/**
 * Metabox for Custom Social Share Text and Hashtags
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Adds the Digg This metabox to public post types.
 *
 * This function hooks into the `add_meta_boxes` action to add a metabox 
 * for custom share text and hashtags.
 *
 * @since  1.0.0
 * @return void
 */
function digg_this_add_metabox() {
    add_meta_box(
        'digg_this_meta',
        esc_html__( 'Digg This', 'digg-this' ),
        'digg_this_render_metabox',
        get_post_types( [ 'public' => true ], 'names' ),
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'digg_this_add_metabox' );

/**
 * Renders the Digg This metabox fields.
 *
 * Displays input fields for custom share text and hashtags.
 * Also includes a nonce field for security.
 *
 * @param WP_Post $post The post object.
 * 
 * @since  1.0.0
 * @return void
 */
function digg_this_render_metabox( $post ) {
    $custom_text = get_post_meta( $post->ID, '_digg_this_custom_text', true );
    $hashtags    = get_post_meta( $post->ID, '_digg_this_hashtags', true );
    wp_nonce_field( 'digg_this_save_meta', 'digg_this_meta_nonce' );
    ?>
    <p>
        <label for="digg_this_custom_text"><?php esc_html_e( 'Custom Share Text:', 'digg-this' ); ?></label>
        <textarea id="digg_this_custom_text" name="digg_this_custom_text" class="widefat"><?php echo esc_textarea( $custom_text ); ?></textarea>
    </p>
    <p>
        <label for="digg_this_hashtags"><?php esc_html_e( 'Hashtags (comma separated):', 'digg-this' ); ?></label>
        <input type="text" id="digg_this_hashtags" name="digg_this_hashtags" value="<?php echo esc_attr( $hashtags ); ?>" class="widefat" />
    </p>
    <?php
}

/**
 * Saves the Digg This metabox data.
 *
 * Verifies the nonce, checks user capabilities, and prevents autosaving before
 * updating the post meta fields for custom share text and hashtags.
 *
 * @param int $post_id The ID of the post being saved.
 * 
 * @since  1.0.0
 * @return void
 */
function digg_this_save_metabox( $post_id ) {
    // Verify nonce.
    if ( ! isset( $_POST['digg_this_meta_nonce'] ) || ! wp_verify_nonce( $_POST['digg_this_meta_nonce'], 'digg_this_save_meta' ) ) {
        return;
    }

    // Prevent autosave.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check user permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Sanitize and update custom text.
    if ( isset( $_POST['digg_this_custom_text'] ) ) {
        update_post_meta( $post_id, '_digg_this_custom_text', sanitize_textarea_field( $_POST['digg_this_custom_text'] ) );
    }

    // Sanitize and update hashtags.
    if ( isset( $_POST['digg_this_hashtags'] ) ) {
        update_post_meta( $post_id, '_digg_this_hashtags', sanitize_text_field( $_POST['digg_this_hashtags'] ) );
    }
}
add_action( 'save_post', 'digg_this_save_metabox' );
