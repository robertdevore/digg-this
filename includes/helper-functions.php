<?php
/**
 * Helper functions for the plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Convert HEX color to RGBA.
 *
 * @param string $hex_color The HEX color code (e.g., '#FF0000').
 * @param float  $opacity   The opacity level (0 to 1).
 * @return string The RGBA color format.
 */
function hex_to_rgba( $hex_color = '#000000', $opacity = 1.0 ) {
    // Ensure the color starts with '#'
    $hex_color = ltrim( $hex_color, '#' );

    // Expand shorthand (e.g., 'F00' → 'FF0000')
    if ( strlen( $hex_color ) === 3 ) {
        $hex_color = $hex_color[0] . $hex_color[0] .
                     $hex_color[1] . $hex_color[1] .
                     $hex_color[2] . $hex_color[2];
    }

    // Convert hex to RGB
    if ( strlen( $hex_color ) === 6 ) {
        list( $r, $g, $b ) = sscanf( $hex_color, "%02x%02x%02x" );
        return sprintf( 'rgba(%d,%d,%d,%.2f)', $r, $g, $b, floatval( $opacity ) );
    }

    // Return default black RGBA if invalid input
    return 'rgba(0,0,0,1.0)';
}

/**
 * Darken a HEX color by a given percentage.
 *
 * @param string $hex_color The HEX color code (e.g., '#FF0000').
 * @param float  $percent   The percentage to darken (0 to 100).
 * 
 * 
 * @return string The darkened HEX color.
 */
function darken_hex_color( $hex_color, $percent ) {
    // Ensure the color starts with '#'
    $hex_color = ltrim( $hex_color, '#' );

    // Expand shorthand HEX (e.g., 'F00' → 'FF0000')
    if ( strlen( $hex_color ) === 3 ) {
        $hex_color = $hex_color[0] . $hex_color[0] .
                     $hex_color[1] . $hex_color[1] .
                     $hex_color[2] . $hex_color[2];
    }

    // Convert HEX to RGB
    if ( strlen( $hex_color ) === 6 ) {
        list( $r, $g, $b ) = sscanf( $hex_color, "%02x%02x%02x" );

        // Darken each color channel by the percentage
        $r = max( 0, round( $r * ( 1 - $percent / 100 ) ) );
        $g = max( 0, round( $g * ( 1 - $percent / 100 ) ) );
        $b = max( 0, round( $b * ( 1 - $percent / 100 ) ) );

        // Return the new HEX color
        return sprintf( '#%02X%02X%02X', $r, $g, $b );
    }

    // Return original color if invalid input
    return '#' . $hex_color;
}

/**
 * Lighten a HEX color by a given percentage.
 *
 * @param string $hex_color The HEX color code (e.g., '#FF0000').
 * @param float  $percent   The percentage to lighten (0 to 100).
 * @return string The lightened HEX color.
 */
function lighten_hex_color( $hex_color, $percent ) {
    // Ensure the color starts with '#'
    $hex_color = ltrim( $hex_color, '#' );

    // Expand shorthand HEX (e.g., 'F00' → 'FF0000')
    if ( strlen( $hex_color ) === 3 ) {
        $hex_color = $hex_color[0] . $hex_color[0] .
                     $hex_color[1] . $hex_color[1] .
                     $hex_color[2] . $hex_color[2];
    }

    // Convert HEX to RGB
    if ( strlen( $hex_color ) === 6 ) {
        list( $r, $g, $b ) = sscanf( $hex_color, "%02x%02x%02x" );

        // Lighten each color channel by increasing it towards 255
        $r = min( 255, round( $r + ( 255 - $r ) * ( $percent / 100 ) ) );
        $g = min( 255, round( $g + ( 255 - $g ) * ( $percent / 100 ) ) );
        $b = min( 255, round( $b + ( 255 - $b ) * ( $percent / 100 ) ) );

        // Return the new HEX color
        return sprintf( '#%02X%02X%02X', $r, $g, $b );
    }

    // Return original color if invalid input
    return '#' . $hex_color;
}
