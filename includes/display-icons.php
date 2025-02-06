<?php
/**
 * Display social media sharing icons.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Append social media sharing icons to the post content.
 *
 * This function hooks into `the_content` filter and appends the enabled sharing icons
 * to the end of the post content for supported post types.
 *
 * @param string $content The post content.
 * 
 * @since  1.0.0
 * @return string The modified content with sharing icons appended.
 */
function digg_this_add_sharing_icons( $content ) {
    if ( is_singular() ) {
        $settings           = get_option( 'digg_this_settings', [] );
        $enabled_icons      = $settings['enabled_icons'] ?? [];
        $enabled_post_types = $settings['enabled_post_types'] ?? ['post'];

        if ( in_array( get_post_type(), $enabled_post_types, true ) ) {
            $icons_html = '<div class="digg-this-sharing-icons">';
            foreach ( $enabled_icons as $icon ) {
                $icons_html .= digg_this_get_icon_html( $icon );
            }
            $icons_html .= '</div>';

            $content .= $icons_html;
        }
    }
    return $content;
}
add_filter( 'the_content', 'digg_this_add_sharing_icons' );

/**
 * Generate the HTML for a social media sharing icon.
 *
 * This function returns the HTML markup for a sharing icon, linking to the respective
 * social media sharing URL for the current post.
 *
 * @param string $icon The name of the social media platform (e.g., 'x', 'facebook').
 * 
 * @since  1.0.0
 * @return string The HTML markup for the sharing icon.
 */
function digg_this_get_icon_html( $icon ) {
    $title       = get_the_title();
    $url         = get_permalink();
    $custom_text = get_post_meta( get_the_ID(), '_digg_this_custom_text', true );
    $hashtags    = get_post_meta( get_the_ID(), '_digg_this_hashtags', true );

    // Build the base text
    $tweet_text = $title . "\n\n";
    if ( ! empty( $custom_text ) ) {
        $tweet_text .= $custom_text . "\n\n";
    }
    $tweet_text .= $url;

    // Format hashtags correctly (Twitter requires comma-separated, Bluesky needs space-separated)
    if ( ! empty( $hashtags ) ) {
        $formatted_hashtags = implode( ',', array_map( function( $tag ) {
            return ltrim( trim( $tag ), '#' ); // Remove # if present
        }, explode( ' ', $hashtags ) ));
        
        $tweet_text .= "\n\n#" . str_replace(',', ' #', $formatted_hashtags); // Add # back for normal display
    }

    // Encode text and URL separately
    $encoded_text = rawurlencode( $tweet_text );
    $encoded_url  = rawurlencode( $url );

    // Social media share URLs
    $urls = [
        'x'        => 'https://twitter.com/intent/tweet?text=' . rawurlencode( $title ) . '&url=' . $encoded_url . ( ! empty( $formatted_hashtags ) ? '&hashtags=' . rawurlencode( $formatted_hashtags ) : '' ),
        'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
        'linkedin' => 'https://www.linkedin.com/shareArticle?url=' . $encoded_url, // LinkedIn only accepts a URL
        'bluesky'  => 'https://bsky.app/intent/compose?text=' . rawurlencode( $tweet_text ),
        'whatsapp' => 'https://wa.me/?text=' . $encoded_text,
        'mastodon' => 'https://mastodonshare.com/?text=' . $encoded_text,
    ];

    if ( ! isset( $urls[ $icon ] ) ) {
        return '';
    }

    // Path to the SVG file
    $svg_file = DIGG_THIS_PLUGIN_DIR . 'assets/icons/' . $icon . '.svg';

    // Read the SVG file content
    if ( file_exists( $svg_file ) ) {
        $svg_content = file_get_contents( $svg_file );

        // Ensure the SVG uses `stroke="currentColor"`
        $svg_content = preg_replace('/stroke="[^"]*"/', 'stroke="currentColor"', $svg_content);
    } else {
        return ''; // Return nothing if the file doesn't exist
    }

    return '<a href="' . esc_url( $urls[ $icon ] ) . '" target="_blank" class="digg-this-icon digg-this-' . esc_attr( $icon ) . '">' .
        $svg_content .
    '</a>';
}
