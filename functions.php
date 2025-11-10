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
        '1.2.3' // Version 1.2.3 to force browser cache to update
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

