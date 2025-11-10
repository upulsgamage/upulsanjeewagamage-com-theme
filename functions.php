<?php
/**
 * Upul Gamage Authority Theme Functions
 *
 * @package UpulGamageAuthority
 */

if ( ! function_exists( 'upulgamage_authority_setup' ) ) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     */
    function upulgamage_authority_setup() {
        // Add theme support for automatic feed links.
        add_theme_support( 'automatic-feed-links' );

        // Add theme support for title tag (WordPress manages the document title).
        add_theme_support( 'title-tag' );

        // Add theme support for post thumbnails (featured images).
        add_theme_support( 'post-thumbnails' );

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(
            array(
                'primary' => esc_html__( 'Primary Menu', 'upulgamage-authority' ),
            )
        );
    }
endif;
add_action( 'after_setup_theme', 'upulgamage_authority_setup' );


/**
 * The definitive script and style manager.
 * This single function will remove all bloat AND enqueue our own stylesheet.
 * It runs at priority 100 to ensure it runs LAST.
 */
function upulgamage_authority_scripts_and_styles() {
    
    // --- 1. REMOVE ALL UNNECESSARY STYLES ---

    // Remove the core block library CSS
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    
    // Remove the global styles that add :root variables
    wp_dequeue_style( 'global-styles' );
    
    // Remove the classic theme styles
    wp_dequeue_style( 'classic-theme-styles' );
    
    // Remove emoji styles
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_head', 'print_emoji_styles', 9 );


    // --- 2. REMOVE ALL UNNECESSARY SCRIPTS ---

    // Remove emoji scripts
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    // Remove other bloat
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );


    // --- 3. ENQUEUE OUR OWN STYLESHEET ---

    // Enqueue our main stylesheet
    wp_enqueue_style(
        'upulgamage-authority-style',
        get_stylesheet_uri(),
        array(),
        '1.2.5' // Version 1.2.5 to force browser cache to update
    );

}
add_action( 'wp_enqueue_scripts', 'upulgamage_authority_scripts_and_styles', 100 );


/**
 * Helper function to remove the emoji DNS prefetch (runs on a different hook).
 */
function upulgamage_authority_disable_emoji_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
        $urls = array_filter( $urls, function( $url ) {
            return strpos( $url, 's.w.org' ) === false;
        } );
    }
    return $urls;
}
add_filter( 'wp_resource_hints', 'upulgamage_authority_disable_emoji_dns_prefetch', 10, 2 );


/**
 * Ensure external links opened in a new tab are safe: add rel="noopener noreferrer".
 *
 * Coverage:
 * - Content links: extend WordPress's default wp_targeted_link_rel to include "noreferrer" as well.
 * - Navigation menu links: enforce rel on external target="_blank" items.
 */

if ( ! function_exists( 'upulgamage_is_external_url' ) ) {
    /**
     * Check if a URL is external to the current site.
     *
     * @param string $url The URL to check.
     * @return bool True if external, false otherwise.
     */
    function upulgamage_is_external_url( $url ) {
        if ( empty( $url ) ) {
            return false;
        }

        // Ignore anchors and non-http(s) schemes (mailto:, tel:, etc.).
        if ( 0 === strpos( $url, '#' ) || ! preg_match( '#^https?://#i', $url ) ) {
            return false;
        }

        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
        $link_host = wp_parse_url( $url, PHP_URL_HOST );

        if ( empty( $link_host ) || empty( $site_host ) ) {
            return false;
        }

        return ! hash_equals( $site_host, $link_host );
    }
}

if ( ! function_exists( 'upulgamage_targeted_link_rel' ) ) {
    /**
     * Add "noreferrer" alongside WordPress's default "noopener" for target=_blank links in content.
     *
     * @param string $rel  Existing rel values WordPress plans to add.
     * @param string $link The link href (unused here but kept for signature parity).
     * @return string Updated rel values.
     */
    function upulgamage_targeted_link_rel( $rel, $link ) {
        $rels = preg_split( '/\s+/', trim( (string) $rel ) );
        $rels = array_filter( is_array( $rels ) ? $rels : array() );

        foreach ( array( 'noopener', 'noreferrer' ) as $required ) {
            if ( ! in_array( $required, $rels, true ) ) {
                $rels[] = $required;
            }
        }

        return trim( implode( ' ', $rels ) );
    }
}
add_filter( 'wp_targeted_link_rel', 'upulgamage_targeted_link_rel', 10, 2 );

if ( ! function_exists( 'upulgamage_nav_menu_link_rel' ) ) {
    /**
     * Ensure nav menu links that open in a new tab and point off-site include safe rel attributes.
     *
     * @param array  $atts The HTML attributes applied to the menu item's <a> element.
     * @param object $item The current menu item object.
     * @param object $args An object of wp_nav_menu() arguments.
     * @return array Possibly modified attributes.
     */
    function upulgamage_nav_menu_link_rel( $atts, $item, $args ) {
        $href   = isset( $atts['href'] ) ? $atts['href'] : '';
        $target = isset( $atts['target'] ) ? $atts['target'] : '';

        if ( '_blank' === $target && upulgamage_is_external_url( $href ) ) {
            $existing = isset( $atts['rel'] ) ? $atts['rel'] : '';
            $rels     = preg_split( '/\s+/', trim( (string) $existing ) );
            $rels     = array_filter( is_array( $rels ) ? $rels : array() );

            foreach ( array( 'noopener', 'noreferrer' ) as $required ) {
                if ( ! in_array( $required, $rels, true ) ) {
                    $rels[] = $required;
                }
            }

            $atts['rel'] = trim( implode( ' ', $rels ) );
        }

        return $atts;
    }
}
add_filter( 'nav_menu_link_attributes', 'upulgamage_nav_menu_link_rel', 10, 3 );

