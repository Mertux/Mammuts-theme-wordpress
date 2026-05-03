<?php
/**
 * Mammuts Theme - Functions and Definitions
 *
 * @package Mammuts
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MAMMUTS_VERSION', '1.37.1' );
define( 'MAMMUTS_DIR', get_template_directory() );
define( 'MAMMUTS_URI', get_template_directory_uri() );

/* ---- SportsPress: "Überalbum" → "Übersicht" ---- */
add_filter( 'gettext', 'mammuts_rename_ueberalbum', 10, 3 );
function mammuts_rename_ueberalbum( $translated, $text, $domain ) {
    if ( false !== strpos( $translated, 'beralbum' ) ) {
        $translated = str_replace(
            array( 'Überalbum', 'Ueberalbum', 'überalbum', 'ueberalbum' ),
            'Übersicht',
            $translated
        );
    }
    return $translated;
}


/* ============================================
 * Theme Setup
 * ============================================ */
function mammuts_setup() {
    // Make theme available for translation
    load_theme_textdomain( 'mammuts', MAMMUTS_DIR . '/languages' );

    // Add default posts and comments RSS feed links
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable featured images
    add_theme_support( 'post-thumbnails' );

    // Custom image sizes
    add_image_size( 'mammuts-hero', 1920, 1080, true );
    add_image_size( 'mammuts-news-card', 640, 360, true );
    add_image_size( 'mammuts-player', 480, 640, true );
    add_image_size( 'mammuts-player-thumb', 220, 293, true );

    // Register nav menus
    register_nav_menus( array(
        'primary'   => __( 'Primary Navigation', 'mammuts' ),
        'footer'    => __( 'Footer Navigation', 'mammuts' ),
    ) );

    // HTML5 support
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // Editor styles
    add_theme_support( 'editor-styles' );

    // Wide alignment support
    add_theme_support( 'align-wide' );

    // SportsPress support
    add_theme_support( 'sportspress' );
}
add_action( 'after_setup_theme', 'mammuts_setup' );


/* ============================================
 * Enqueue Scripts & Styles
 * ============================================ */
function mammuts_scripts() {
    // Google Fonts
    wp_enqueue_style(
        'mammuts-fonts',
        'https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;600;700&family=Oswald:wght@300;400;500;600;700;800;900&display=swap',
        array(),
        null
    );

    // Main stylesheet - use file modification time to bust cache
    wp_enqueue_style(
        'mammuts-style',
        get_stylesheet_uri(),
        array( 'mammuts-fonts' ),
        filemtime( get_stylesheet_directory() . '/style.css' )
    );

    // Custom JS
    wp_enqueue_script(
        'mammuts-main',
        MAMMUTS_URI . '/assets/js/main.js',
        array(),
        filemtime( get_stylesheet_directory() . '/assets/js/main.js' ),
        true
    );

    // Pass data to JS
    wp_localize_script( 'mammuts-main', 'mammuts', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'mammuts-nonce' ),
        'siteUrl' => home_url( '/' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'mammuts_scripts' );


/* ============================================
 * Widget Areas
 * ============================================ */
function mammuts_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'mammuts' ),
        'id'            => 'sidebar-main',
        'description'   => __( 'Main sidebar widget area.', 'mammuts' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 1', 'mammuts' ),
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 2', 'mammuts' ),
        'id'            => 'footer-2',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer 3', 'mammuts' ),
        'id'            => 'footer-3',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Sponsors Bar', 'mammuts' ),
        'id'            => 'sponsors-bar',
        'description'   => __( 'Add a SportsPress Sponsors widget or image widget here.', 'mammuts' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<p class="sponsors-label">',
        'after_title'   => '</p>',
    ) );
}
add_action( 'widgets_init', 'mammuts_widgets_init' );


/* ============================================
 * Customizer Settings
 * ============================================ */
function mammuts_customize_register( $wp_customize ) {

    // ── Hero Section ──
    $wp_customize->add_section( 'mammuts_hero', array(
        'title'    => __( 'Hero Section', 'mammuts' ),
        'priority' => 30,
    ) );

    // Banner Visibility
    $wp_customize->add_setting( 'mammuts_banner_visibility', array(
        'default'           => 'front_only',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'mammuts_banner_visibility', array(
        'label'       => __( 'Banner anzeigen auf', 'mammuts' ),
        'description' => __( 'Wo soll das Banner-Bild mit Text angezeigt werden?', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'select',
        'choices'     => array(
            'front_only' => __( 'Nur Startseite', 'mammuts' ),
            'all_pages'  => __( 'Alle Seiten', 'mammuts' ),
            'none'       => __( 'Nirgends (deaktiviert)', 'mammuts' ),
        ),
    ) );

    $wp_customize->add_setting( 'mammuts_banner_hide_on_mobile', array(
        'default'           => true,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'mammuts_banner_hide_on_mobile', array(
        'label'       => __( 'Unterseiten-Banner auf Mobile ausblenden', 'mammuts' ),
        'description' => __( 'Blendet die Unterseiten-Banner auf Mobilgeräten (unter 768px) aus.', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'checkbox',
    ) );

    $wp_customize->add_setting( 'mammuts_hero_hide_on_mobile', array(
        'default'           => false,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'mammuts_hero_hide_on_mobile', array(
        'label'       => __( 'Hero-Banner auf Mobile ausblenden', 'mammuts' ),
        'description' => __( 'Blendet den Hero-Banner der Startseite auf Mobilgeräten (unter 768px) aus.', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'checkbox',
    ) );

    // Banner Cut/Fade Effect Toggle
    $wp_customize->add_setting( 'mammuts_banner_fade_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'mammuts_banner_fade_enabled', array(
        'label'       => __( 'Diagonaler Fade-Effekt aktiv', 'mammuts' ),
        'description' => __( 'Aktiviert den diagonalen Cut/Fade-Übergang über dem Bannerbild. Wenn deaktiviert, wird das Bild ohne Overlay angezeigt.', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'checkbox',
    ) );

    // Hero Background Image (Desktop)
    $wp_customize->add_setting( 'mammuts_hero_bg', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mammuts_hero_bg', array(
        'label'       => __( 'Hero Bild – Desktop', 'mammuts' ),
        'description' => __( 'Hintergrundbild für Bildschirme ab 769px Breite.', 'mammuts' ),
        'section'     => 'mammuts_hero',
    ) ) );

    // Hero Background Image (Mobile)
    $wp_customize->add_setting( 'mammuts_hero_bg_mobile', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mammuts_hero_bg_mobile', array(
        'label'       => __( 'Hero Bild – Mobil', 'mammuts' ),
        'description' => __( 'Hintergrundbild für Bildschirme bis 768px Breite. Wenn leer, wird das Desktop-Bild verwendet.', 'mammuts' ),
        'section'     => 'mammuts_hero',
    ) ) );

    // ── Hero Card Position & Size (Desktop) ──
    $wp_customize->add_setting( 'mammuts_hero_card_width', array(
        'default'           => 380,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_hero_card_width', array(
        'label'       => __( 'Karten-Breite (px)', 'mammuts' ),
        'description' => __( 'Breite der Countdown-/News-Karte im Hero. Standard: 380px', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 280,
            'max'  => 550,
            'step' => 10,
        ),
    ) );

    $wp_customize->add_setting( 'mammuts_hero_card_top', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_hero_card_top', array(
        'label'       => __( 'Vertikale Position (%)', 'mammuts' ),
        'description' => __( 'Position von oben. 0% = oben, 50% = mittig, 100% = unten. Standard: 50%', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 5,
        ),
    ) );

    $wp_customize->add_setting( 'mammuts_hero_card_right', array(
        'default'           => 5,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_hero_card_right', array(
        'label'       => __( 'Abstand von rechts (%)', 'mammuts' ),
        'description' => __( 'Horizontaler Abstand vom rechten Rand. Standard: 5%', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 40,
            'step' => 1,
        ),
    ) );

    // Banner Cut Position (where full image begins)
    $wp_customize->add_setting( 'mammuts_banner_cut_position', array(
        'default'           => 40,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_banner_cut_position', array(
        'label'       => __( 'Banner Cut Position (%)', 'mammuts' ),
        'description' => __( 'Wo der diagonale Schnitt liegt. 20% = viel Bild sichtbar, 50% = halb/halb, 70% = wenig Bild. Standard: 40%', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 15,
            'max'  => 70,
            'step' => 1,
        ),
    ) );

    // Banner Fade Softness
    $wp_customize->add_setting( 'mammuts_banner_fade_softness', array(
        'default'           => 12,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_banner_fade_softness', array(
        'label'       => __( 'Banner Fade Softness (%)', 'mammuts' ),
        'description' => __( 'Wie weich der Übergang ist. 3% = scharf, 20% = sehr sanft. Standard: 12%', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 3,
            'max'  => 25,
            'step' => 1,
        ),
    ) );

    // Banner Diagonal Angle
    $wp_customize->add_setting( 'mammuts_banner_diagonal_angle', array(
        'default'           => 250,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_banner_diagonal_angle', array(
        'label'       => __( 'Banner Diagonal Angle (°)', 'mammuts' ),
        'description' => __( 'Richtung des Schnitts. 250° = unten-links → oben-rechts (Standard), 270° = gerade nach oben, 290° = unten-rechts → oben-links, 105° = oben-links → unten-rechts', 'mammuts' ),
        'section'     => 'mammuts_hero',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 80,
            'max'  => 300,
            'step' => 5,
        ),
    ) );

    // Hero CTA
    $wp_customize->add_setting( 'mammuts_hero_cta_text', array(
        'default'           => __( 'Join the Pack', 'mammuts' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'mammuts_hero_cta_text', array(
        'label'   => __( 'CTA Button Text', 'mammuts' ),
        'section' => 'mammuts_hero',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'mammuts_hero_cta_url', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'mammuts_hero_cta_url', array(
        'label'   => __( 'CTA Button URL', 'mammuts' ),
        'section' => 'mammuts_hero',
        'type'    => 'url',
    ) );

    // ── Subpage Banner ──
    $wp_customize->add_section( 'mammuts_subpage_banner', array(
        'title'       => __( 'Unterseiten-Banner', 'mammuts' ),
        'description' => __( 'Hintergrundbild hinter der Seitenüberschrift auf Unterseiten (nur Desktop).', 'mammuts' ),
        'priority'    => 31,
    ) );

    $wp_customize->add_setting( 'mammuts_subpage_banner_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'mammuts_subpage_banner_enabled', array(
        'label'       => __( 'Unterseiten-Banner aktivieren', 'mammuts' ),
        'description' => __( 'Zeigt ein Hintergrundbild hinter der Seitenüberschrift auf Unterseiten. Nur im Desktop-Modus sichtbar.', 'mammuts' ),
        'section'     => 'mammuts_subpage_banner',
        'type'        => 'checkbox',
    ) );

    $wp_customize->add_setting( 'mammuts_subpage_banner_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mammuts_subpage_banner_image', array(
        'label'   => __( 'Banner-Bild', 'mammuts' ),
        'section' => 'mammuts_subpage_banner',
    ) ) );

    $wp_customize->add_setting( 'mammuts_subpage_banner_height', array(
        'default'           => 220,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_subpage_banner_height', array(
        'label'       => __( 'Banner-Höhe (px)', 'mammuts' ),
        'description' => __( 'Höhe des Banners auf Desktop. Standard: 220px', 'mammuts' ),
        'section'     => 'mammuts_subpage_banner',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 120,
            'max'  => 400,
            'step' => 10,
        ),
    ) );

    // ── Club Info ──
    $wp_customize->add_section( 'mammuts_club', array(
        'title'    => __( 'Club Information', 'mammuts' ),
        'priority' => 32,
    ) );

    $club_fields = array(
        'club_name'    => array( 'Club Name', 'Mammuts' ),
        'club_city'    => array( 'City', '' ),
        'club_address' => array( 'Address', '' ),
        'club_phone'   => array( 'Phone', '' ),
        'club_email'   => array( 'Email', '' ),
        'club_founded' => array( 'Founded Year', '' ),
    );

    foreach ( $club_fields as $key => $field ) {
        $wp_customize->add_setting( "mammuts_{$key}", array(
            'default'           => $field[1],
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( "mammuts_{$key}", array(
            'label'   => __( $field[0], 'mammuts' ),
            'section' => 'mammuts_club',
            'type'    => 'text',
        ) );
    }

    // Social Media
    $wp_customize->add_section( 'mammuts_social', array(
        'title'    => __( 'Social Media', 'mammuts' ),
        'priority' => 32,
    ) );

    $socials = array( 'facebook', 'instagram', 'twitter', 'youtube', 'tiktok' );
    foreach ( $socials as $social ) {
        $wp_customize->add_setting( "mammuts_social_{$social}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ) );
        $wp_customize->add_control( "mammuts_social_{$social}", array(
            'label'   => ucfirst( $social ) . ' URL',
            'section' => 'mammuts_social',
            'type'    => 'url',
        ) );
    }

    // ── Accent Color ──
    $wp_customize->add_setting( 'mammuts_accent_color', array(
        'default'           => '#c8102e',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'mammuts_accent_color', array(
        'label'   => __( 'Accent Color', 'mammuts' ),
        'section' => 'colors',
    ) ) );

    // ── Card / Tile Backgrounds ──
    $wp_customize->add_section( 'mammuts_cards', array(
        'title'    => __( 'Kachel-Hintergrund', 'mammuts' ),
        'priority' => 45,
    ) );

    // Card background color (hex)
    $wp_customize->add_setting( 'mammuts_card_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'mammuts_card_bg_color', array(
        'label'       => __( 'Kachel-Farbe (RGB)', 'mammuts' ),
        'description' => __( 'Hintergrundfarbe der Kacheln / Cards.', 'mammuts' ),
        'section'     => 'mammuts_cards',
    ) ) );

    // Card background opacity (0–100)
    $wp_customize->add_setting( 'mammuts_card_bg_opacity', array(
        'default'           => 100,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_card_bg_opacity', array(
        'label'       => __( 'Kachel-Deckkraft (%)', 'mammuts' ),
        'description' => __( '100 = voll sichtbar, 0 = komplett transparent.', 'mammuts' ),
        'section'     => 'mammuts_cards',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 5,
        ),
    ) );

    // Card hover background color
    $wp_customize->add_setting( 'mammuts_card_bg_hover_color', array(
        'default'           => '#fafafa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'mammuts_card_bg_hover_color', array(
        'label'       => __( 'Kachel-Farbe bei Hover', 'mammuts' ),
        'description' => __( 'Hintergrund wenn man mit der Maus drüber fährt.', 'mammuts' ),
        'section'     => 'mammuts_cards',
    ) ) );

    // ── Card background gradient ──

    $wp_customize->add_setting( 'mammuts_card_gradient_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_card_gradient_enabled', array(
        'label'       => __( 'Kachel-Farbverlauf aktivieren', 'mammuts' ),
        'description' => __( 'Ersetzt die einfache Hintergrundfarbe durch einen Farbverlauf (Gradient).', 'mammuts' ),
        'section'     => 'mammuts_cards',
        'type'        => 'checkbox',
    ) );

    // Gradient type: linear or radial
    $wp_customize->add_setting( 'mammuts_card_gradient_type', array(
        'default'           => 'linear',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_card_gradient_type', array(
        'label'       => __( 'Verlauf-Typ', 'mammuts' ),
        'description' => __( 'Linear = gerade Linie. Radial = kreisförmig von innen nach außen.', 'mammuts' ),
        'section'     => 'mammuts_cards',
        'type'        => 'select',
        'choices'     => array(
            'linear' => __( 'Linear (Linie)', 'mammuts' ),
            'radial' => __( 'Radial (Kreis)', 'mammuts' ),
        ),
    ) );

    // Gradient angle (linear only, 0–360°)
    $wp_customize->add_setting( 'mammuts_card_gradient_angle', array(
        'default'           => 160,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_card_gradient_angle', array(
        'label'       => __( 'Richtung / Winkel (°) — nur bei Linear', 'mammuts' ),
        'description' => __( '0° = oben→unten, 90° = links→rechts, 135° = diagonal. Nur aktiv wenn Typ = Linear.', 'mammuts' ),
        'section'     => 'mammuts_cards',
        'type'        => 'range',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 360,
            'step' => 5,
        ),
    ) );

    // Gradient colour 1 (start)
    $wp_customize->add_setting( 'mammuts_card_gradient_color1', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'mammuts_card_gradient_color1', array(
        'label'       => __( 'Farbe 1 (Startfarbe)', 'mammuts' ),
        'description' => __( 'Erste Farbe des Verlaufs.', 'mammuts' ),
        'section'     => 'mammuts_cards',
    ) ) );

    // Gradient colour 1 opacity (0–100)
    $wp_customize->add_setting( 'mammuts_card_gradient_opacity1', array(
        'default'           => 100,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_card_gradient_opacity1', array(
        'label'       => __( 'Farbe 1 Deckkraft (%)', 'mammuts' ),
        'section'     => 'mammuts_cards',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
    ) );

    // Gradient colour 1 stop position (0–100%)
    $wp_customize->add_setting( 'mammuts_card_gradient_stop1', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_card_gradient_stop1', array(
        'label'       => __( 'Farbe 1 Position (%)', 'mammuts' ),
        'description' => __( 'Wo Farbe 1 beginnt (0% = ganz am Anfang).', 'mammuts' ),
        'section'     => 'mammuts_cards',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
    ) );

    // Gradient colour 2 (end)
    $wp_customize->add_setting( 'mammuts_card_gradient_color2', array(
        'default'           => '#f0f0f0',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'mammuts_card_gradient_color2', array(
        'label'       => __( 'Farbe 2 (Endfarbe)', 'mammuts' ),
        'description' => __( 'Zweite Farbe des Verlaufs.', 'mammuts' ),
        'section'     => 'mammuts_cards',
    ) ) );

    // Gradient colour 2 opacity (0–100)
    $wp_customize->add_setting( 'mammuts_card_gradient_opacity2', array(
        'default'           => 100,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_card_gradient_opacity2', array(
        'label'       => __( 'Farbe 2 Deckkraft (%)', 'mammuts' ),
        'section'     => 'mammuts_cards',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
    ) );

    // Gradient colour 2 stop position (0–100%)
    $wp_customize->add_setting( 'mammuts_card_gradient_stop2', array(
        'default'           => 100,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_card_gradient_stop2', array(
        'label'       => __( 'Farbe 2 Position (%)', 'mammuts' ),
        'description' => __( 'Wo Farbe 2 endet (100% = ganz am Ende).', 'mammuts' ),
        'section'     => 'mammuts_cards',
        'type'        => 'range',
        'input_attrs' => array( 'min' => 0, 'max' => 100, 'step' => 5 ),
    ) );

    // ── Card text colours (per element) ──
    $card_text_settings = array(
        'mammuts_card_title_color' => array(
            'label'   => __( 'Kachel: Titel-Farbe', 'mammuts' ),
            'default' => '#111111',
        ),
        'mammuts_card_excerpt_color' => array(
            'label'   => __( 'Kachel: Excerpt-Farbe', 'mammuts' ),
            'default' => '#555555',
        ),
        'mammuts_card_category_color' => array(
            'label'   => __( 'Kachel: Kategorie-Farbe', 'mammuts' ),
            'default' => '#c8102e',
        ),
        'mammuts_card_date_color' => array(
            'label'   => __( 'Kachel: Datum-Farbe', 'mammuts' ),
            'default' => '#999999',
        ),
        'mammuts_card_link_color' => array(
            'label'   => __( 'Kachel: Link-Farbe', 'mammuts' ),
            'default' => '#c8102e',
        ),
    );
    foreach ( $card_text_settings as $setting_id => $args ) {
        $wp_customize->add_setting( $setting_id, array(
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting_id, array(
            'label'   => $args['label'],
            'section' => 'mammuts_cards',
        ) ) );
    }

    // ── Typography / Schriftarten ──
    $wp_customize->add_section( 'mammuts_typography', array(
        'title'    => __( 'Schriftarten', 'mammuts' ),
        'priority' => 44,
    ) );

    // Display font (headlines, nav, labels)
    $display_fonts = array(
        'Oswald'         => 'Oswald (Standard)',
        'Bebas Neue'     => 'Bebas Neue',
        'Anton'          => 'Anton',
        'Teko'           => 'Teko',
        'Rajdhani'       => 'Rajdhani',
        'Russo One'      => 'Russo One',
        'Black Han Sans' => 'Black Han Sans',
        'Barlow Condensed' => 'Barlow Condensed',
        'Exo 2'          => 'Exo 2',
        'Chakra Petch'   => 'Chakra Petch',
    );
    $wp_customize->add_setting( 'mammuts_font_display', array(
        'default'           => 'Oswald',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_font_display', array(
        'label'       => __( 'Display-Schrift (Überschriften, Navigation)', 'mammuts' ),
        'description' => __( 'Wird für Überschriften, Menü, Buttons und Labels verwendet.', 'mammuts' ),
        'section'     => 'mammuts_typography',
        'type'        => 'select',
        'choices'     => $display_fonts,
    ) );

    // Body font (text, excerpts, body copy)
    $body_fonts = array(
        'Barlow'        => 'Barlow (Standard)',
        'Open Sans'     => 'Open Sans',
        'Lato'          => 'Lato',
        'Nunito'        => 'Nunito',
        'Source Sans 3' => 'Source Sans 3',
        'Rubik'         => 'Rubik',
        'DM Sans'       => 'DM Sans',
        'Figtree'       => 'Figtree',
        'Noto Sans'     => 'Noto Sans',
        'Mulish'        => 'Mulish',
    );
    $wp_customize->add_setting( 'mammuts_font_body', array(
        'default'           => 'Barlow',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'mammuts_font_body', array(
        'label'       => __( 'Text-Schrift (Fließtext, Excerpts)', 'mammuts' ),
        'description' => __( 'Wird für Fließtext, Excerpts und allgemeinen Seitentext verwendet.', 'mammuts' ),
        'section'     => 'mammuts_typography',
        'type'        => 'select',
        'choices'     => $body_fonts,
    ) );

    // ══════════════════════════════════════════════
    //  Homepage sections
    // ══════════════════════════════════════════════
    $wp_customize->add_section( 'mammuts_homepage', array(
        'title'    => __( 'Startseiten-Bereiche', 'mammuts' ),
        'priority' => 40,
    ) );

    // Toggle: Match Center
    $wp_customize->add_setting( 'mammuts_show_match_center', array(
        'default'           => true,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'mammuts_show_match_center', array(
        'label'       => __( 'Match Center anzeigen', 'mammuts' ),
        'description' => __( 'Zeigt das letzte Ergebnis und das nächste Spiel auf der Startseite.', 'mammuts' ),
        'section'     => 'mammuts_homepage',
        'type'        => 'checkbox',
    ) );

    // Toggle: Next-Game Countdown section
    $wp_customize->add_setting( 'mammuts_show_gameday_countdown', array(
        'default'           => true,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'mammuts_show_gameday_countdown', array(
        'label'       => __( 'Countdown zum nächsten Spiel anzeigen', 'mammuts' ),
        'description' => __( 'Erscheint als eigener Bereich unterhalb des Hero-Banners.', 'mammuts' ),
        'section'     => 'mammuts_homepage',
        'type'        => 'checkbox',
    ) );

    // Countdown: max days ahead
    $wp_customize->add_setting( 'mammuts_countdown_max_days', array(
        'default'           => 14,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_countdown_max_days', array(
        'label'       => __( 'Countdown nur anzeigen wenn Spiel innerhalb (Tage)', 'mammuts' ),
        'description' => __( 'Der Countdown erscheint nur, wenn das nächste Spiel näher als X Tage ist. 0 = immer anzeigen.', 'mammuts' ),
        'section'     => 'mammuts_homepage',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 365,
            'step' => 1,
        ),
    ) );

    // Dual countdown: show both game + event if within N days of each other
    $wp_customize->add_setting( 'mammuts_countdown_dual_days', array(
        'default'           => 7,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'mammuts_countdown_dual_days', array(
        'label'       => __( 'Doppel-Countdown Tagesabstand', 'mammuts' ),
        'description' => __( 'Wenn ein Spiel und eine Veranstaltung innerhalb dieser Tage liegen, werden beide nebeneinander angezeigt. 0 = nie doppelt.', 'mammuts' ),
        'section'     => 'mammuts_homepage',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 60,
            'step' => 1,
        ),
    ) );

    // Select: Current season (only shown if sp_season taxonomy exists)
    if ( taxonomy_exists( 'sp_season' ) ) {
        $seasons = get_terms( array(
            'taxonomy'   => 'sp_season',
            'hide_empty' => false,
        ) );
        if ( ! is_wp_error( $seasons ) && ! empty( $seasons ) ) {
            $choices = array( '' => __( 'Automatisch (neueste Saison)', 'mammuts' ) );
            foreach ( $seasons as $season ) {
                $choices[ $season->slug ] = $season->name;
            }
            $wp_customize->add_setting( 'mammuts_current_season', array(
                'default'           => '',
                'sanitize_callback' => 'sanitize_text_field',
            ) );
            $wp_customize->add_control( 'mammuts_current_season', array(
                'label'       => __( 'Aktuelle Saison', 'mammuts' ),
                'description' => __( 'Bestimmt welche Saison für "nächstes Spiel" berücksichtigt wird.', 'mammuts' ),
                'section'     => 'mammuts_homepage',
                'type'        => 'select',
                'choices'     => $choices,
            ) );
        }
    }

    // ══════════════════════════════════════════════
    //  SportsPress Display Options
    // ══════════════════════════════════════════════
    $wp_customize->add_section( 'mammuts_sportspress', array(
        'title'    => __( 'SportsPress Anzeige', 'mammuts' ),
        'priority' => 42,
    ) );

    // Toggle: Player profile links
    $wp_customize->add_setting( 'mammuts_player_link_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'mammuts_player_link_enabled', array(
        'label'       => __( 'Spieler-Profilseite aktivieren', 'mammuts' ),
        'description' => __( 'Wenn aktiv, können Spieler-Karten angeklickt werden und führen zur Detailseite mit Stats. Wenn deaktiviert, sind Spieler-Karten nicht klickbar.', 'mammuts' ),
        'section'     => 'mammuts_sportspress',
        'type'        => 'checkbox',
    ) );

    // Toggle: Event performance stats
    $wp_customize->add_setting( 'mammuts_event_stats_enabled', array(
        'default'           => true,
        'sanitize_callback' => 'mammuts_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'mammuts_event_stats_enabled', array(
        'label'       => __( 'Spieler-Statistiken bei Ergebnissen anzeigen', 'mammuts' ),
        'description' => __( 'Wenn aktiv, werden Spieler-Statistiken (Performance) auf der Spielergebnis-Seite angezeigt. Wenn deaktiviert, werden nur Punkte und Teams gezeigt.', 'mammuts' ),
        'section'     => 'mammuts_sportspress',
        'type'        => 'checkbox',
    ) );
}
add_action( 'customize_register', 'mammuts_customize_register' );

/**
 * Sanitize checkbox values for the Customizer.
 */
function mammuts_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

/**
 * Enqueue Customizer live-preview JS.
 */
function mammuts_customize_preview_js() {
    wp_enqueue_script(
        'mammuts-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        array( 'customize-preview', 'jquery' ),
        filemtime( get_template_directory() . '/assets/js/customizer-preview.js' ),
        true
    );
}
add_action( 'customize_preview_init', 'mammuts_customize_preview_js' );


/* ============================================
 * Subpage Banner Helper
 * ============================================ */
function mammuts_has_subpage_banner() {
    $enabled = get_theme_mod( 'mammuts_subpage_banner_enabled', false );
    $image   = get_theme_mod( 'mammuts_subpage_banner_image', '' );
    return $enabled && ! empty( $image );
}

/**
 * Renders the subpage page-header-banner with optional background image.
 * Call this in page templates instead of manually writing the banner HTML.
 */
function mammuts_page_header_banner( $title = '' ) {
    if ( empty( $title ) ) {
        $title = get_the_title();
    }
    $has_bg       = mammuts_has_subpage_banner();
    $bg_class     = $has_bg ? ' has-subpage-bg' : '';
    $mobile_class = mammuts_banner_mobile_class();
    $hide_mobile  = get_theme_mod( 'mammuts_banner_hide_on_mobile', true );
    $image_url    = get_theme_mod( 'mammuts_subpage_banner_image', '' );
    $height       = get_theme_mod( 'mammuts_subpage_banner_height', 220 );
    $breadcrumb   = mammuts_get_menu_breadcrumb();
    ?>
    <div class="page-header-banner<?php echo esc_attr( $bg_class . $mobile_class ); ?>"<?php if ( $has_bg ) : ?> style="--subpage-banner-height: <?php echo intval( $height ); ?>px;"<?php endif; ?>>
        <?php if ( $has_bg ) : ?>
            <img class="page-header-banner-bg" src="<?php echo esc_url( $image_url ); ?>" alt="" loading="eager">
            <div class="page-header-banner-overlay"></div>
        <?php endif; ?>
        <div class="container">
            <?php if ( ! empty( $breadcrumb ) ) : ?>
                <nav class="page-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'mammuts' ); ?>">
                    <?php echo $breadcrumb; ?>
                </nav>
            <?php endif; ?>
            <h1 class="page-title"><?php echo wp_kses_post( $title ); ?></h1>
        </div>
    </div>
    <?php if ( $hide_mobile ) : ?>
    <div class="mobile-page-title-bar">
        <?php if ( ! empty( $breadcrumb ) ) : ?>
            <nav class="page-breadcrumb page-breadcrumb--mobile" aria-label="<?php esc_attr_e( 'Breadcrumb', 'mammuts' ); ?>">
                <?php echo $breadcrumb; ?>
            </nav>
        <?php endif; ?>
        <p class="mobile-page-title"><?php echo wp_kses_post( $title ); ?></p>
    </div>
    <?php endif;
}

/**
 * Build a breadcrumb trail from the WordPress navigation menu structure.
 *
 * Finds the current page in the primary menu and walks up the
 * parent chain to build a path like: Teams › Senior Tackle › Coaches
 *
 * Returns HTML string or empty string if the page isn't in a menu
 * or has no ancestors.
 */
function mammuts_get_menu_breadcrumb() {
    // Guard: only meaningful on singular pages
    if ( ! is_singular() && ! is_page() ) {
        return '';
    }

    $locations = get_nav_menu_locations();
    if ( empty( $locations['primary'] ) ) {
        return '';
    }

    $menu_items = wp_get_nav_menu_items( $locations['primary'] );
    if ( ! is_array( $menu_items ) || empty( $menu_items ) ) {
        return '';
    }

    // Match current page by queried object ID — never rely on $_SERVER
    $current_id   = get_queried_object_id();
    $current_item = null;

    foreach ( $menu_items as $item ) {
        if ( isset( $item->object_id, $item->type ) &&
             intval( $item->object_id ) === $current_id &&
             $item->type === 'post_type' ) {
            $current_item = $item;
            break;
        }
    }

    if ( ! $current_item ) {
        return '';
    }

    // Build index of menu items by their menu ID
    $items_by_id = array();
    foreach ( $menu_items as $item ) {
        $items_by_id[ $item->ID ] = $item;
    }

    // Walk up the parent chain (stop after 10 iterations to prevent infinite loops)
    $trail     = array();
    $parent_id = intval( $current_item->menu_item_parent );
    $safety    = 0;

    while ( $parent_id && isset( $items_by_id[ $parent_id ] ) && $safety < 10 ) {
        $safety++;
        $parent_item = $items_by_id[ $parent_id ];
        $url         = isset( $parent_item->url ) ? trim( $parent_item->url ) : '';
        $has_link    = ( ! empty( $url ) && $url !== '#' && $url !== '#/' );

        if ( $has_link ) {
            $trail[] = '<a href="' . esc_url( $url ) . '" class="breadcrumb-link">' . esc_html( $parent_item->title ) . '</a>';
        } else {
            $trail[] = '<span class="breadcrumb-text">' . esc_html( $parent_item->title ) . '</span>';
        }

        $parent_id = intval( $parent_item->menu_item_parent );
    }

    if ( empty( $trail ) ) {
        return '';
    }

    $trail     = array_reverse( $trail );
    $separator = '<span class="breadcrumb-sep" aria-hidden="true">&rsaquo;</span>';

    return implode( $separator, $trail );
}


/* ============================================
 * Custom Accent Color Output
 * ============================================ */
function mammuts_custom_css() {
    $css_parts = array();

    $accent = get_theme_mod( 'mammuts_accent_color', '#c8102e' );
    if ( $accent !== '#c8102e' ) {
        $css_parts[] = "--color-accent: {$accent};";
    }

    $cut_pos = get_theme_mod( 'mammuts_banner_cut_position', 40 );
    if ( $cut_pos != 40 ) {
        $css_parts[] = "--banner-cut-position: {$cut_pos}%;";
    }

    $fade_soft = get_theme_mod( 'mammuts_banner_fade_softness', 12 );
    if ( $fade_soft != 12 ) {
        $css_parts[] = "--banner-fade-softness: {$fade_soft}%;";
    }

    $diagonal = get_theme_mod( 'mammuts_banner_diagonal_angle', 250 );
    if ( $diagonal != 250 ) {
        $css_parts[] = "--banner-diagonal-angle: {$diagonal}deg;";
    }

    // ── Card background: hex → rgba with opacity ──
    $card_hex     = get_theme_mod( 'mammuts_card_bg_color', '#ffffff' );
    $card_opacity = get_theme_mod( 'mammuts_card_bg_opacity', 100 );
    $card_hover   = get_theme_mod( 'mammuts_card_bg_hover_color', '#fafafa' );

    if ( $card_hex !== '#ffffff' || $card_opacity != 100 ) {
        // Convert hex to RGB components
        $r = hexdec( substr( $card_hex, 1, 2 ) );
        $g = hexdec( substr( $card_hex, 3, 2 ) );
        $b = hexdec( substr( $card_hex, 5, 2 ) );
        $a = round( $card_opacity / 100, 2 );
        $css_parts[] = "--color-bg-card: rgba({$r}, {$g}, {$b}, {$a});";
    }

    if ( $card_hover !== '#fafafa' ) {
        $css_parts[] = "--color-bg-card-hover: {$card_hover};";
    }

    // ── Card gradient ──
    $grad_enabled = get_theme_mod( 'mammuts_card_gradient_enabled', false );
    if ( $grad_enabled ) {
        $grad_type    = get_theme_mod( 'mammuts_card_gradient_type', 'linear' );
        $grad_angle   = (int) get_theme_mod( 'mammuts_card_gradient_angle', 160 );
        $grad_c1      = get_theme_mod( 'mammuts_card_gradient_color1', '#ffffff' );
        $grad_op1     = (int) get_theme_mod( 'mammuts_card_gradient_opacity1', 100 );
        $grad_stop1   = (int) get_theme_mod( 'mammuts_card_gradient_stop1', 0 );
        $grad_c2      = get_theme_mod( 'mammuts_card_gradient_color2', '#f0f0f0' );
        $grad_op2     = (int) get_theme_mod( 'mammuts_card_gradient_opacity2', 100 );
        $grad_stop2   = (int) get_theme_mod( 'mammuts_card_gradient_stop2', 100 );

        // Convert hex colours to rgba
        $r1 = hexdec( substr( $grad_c1, 1, 2 ) );
        $g1 = hexdec( substr( $grad_c1, 3, 2 ) );
        $b1 = hexdec( substr( $grad_c1, 5, 2 ) );
        $a1 = round( $grad_op1 / 100, 2 );

        $r2 = hexdec( substr( $grad_c2, 1, 2 ) );
        $g2 = hexdec( substr( $grad_c2, 3, 2 ) );
        $b2 = hexdec( substr( $grad_c2, 5, 2 ) );
        $a2 = round( $grad_op2 / 100, 2 );

        $rgba1 = "rgba({$r1},{$g1},{$b1},{$a1})";
        $rgba2 = "rgba({$r2},{$g2},{$b2},{$a2})";

        if ( $grad_type === 'radial' ) {
            $gradient = "radial-gradient(ellipse at center, {$rgba1} {$grad_stop1}%, {$rgba2} {$grad_stop2}%)";
        } else {
            $gradient = "linear-gradient({$grad_angle}deg, {$rgba1} {$grad_stop1}%, {$rgba2} {$grad_stop2}%)";
        }

        $css_parts[] = "--color-bg-card: {$gradient};";
        // Also push gradient variables for JS live preview reference
        $css_parts[] = "--card-grad-enabled: 1;";
        $css_parts[] = "--card-grad-type: {$grad_type};";
        $css_parts[] = "--card-grad-angle: {$grad_angle}deg;";
        $css_parts[] = "--card-grad-rgba1: {$rgba1};";
        $css_parts[] = "--card-grad-stop1: {$grad_stop1}%;";
        $css_parts[] = "--card-grad-rgba2: {$rgba2};";
        $css_parts[] = "--card-grad-stop2: {$grad_stop2}%;";
    }

    // ── Card text colours ──
    $card_text_defaults = array(
        'mammuts_card_title_color'    => array( 'var' => '--color-card-title',    'default' => '#111111' ),
        'mammuts_card_excerpt_color'  => array( 'var' => '--color-card-excerpt',  'default' => '#555555' ),
        'mammuts_card_category_color' => array( 'var' => '--color-card-category', 'default' => '#c8102e' ),
        'mammuts_card_date_color'     => array( 'var' => '--color-card-date',     'default' => '#999999' ),
        'mammuts_card_link_color'     => array( 'var' => '--color-card-link',     'default' => '#c8102e' ),
    );
    foreach ( $card_text_defaults as $setting_id => $meta ) {
        $val = get_theme_mod( $setting_id, $meta['default'] );
        if ( $val !== $meta['default'] ) {
            $css_parts[] = "{$meta['var']}: {$val};";
        }
    }

    if ( ! empty( $css_parts ) ) {
        $css = ':root { ' . implode( ' ', $css_parts ) . ' }';
        wp_add_inline_style( 'mammuts-style', $css );
    }

    // ── Dynamic Google Fonts based on customizer selection ──
    $font_display = get_theme_mod( 'mammuts_font_display', 'Oswald' );
    $font_body    = get_theme_mod( 'mammuts_font_body', 'Barlow' );

    // Map font names to Google Fonts weight params
    $gf_weights = array(
        'Oswald'           => 'wght@300;400;500;600;700;800;900',
        'Bebas Neue'       => 'wght@400',
        'Anton'            => 'wght@400',
        'Teko'             => 'wght@300;400;500;600;700',
        'Rajdhani'         => 'wght@300;400;500;600;700',
        'Russo One'        => 'wght@400',
        'Black Han Sans'   => 'wght@400',
        'Barlow Condensed' => 'wght@300;400;500;600;700;800;900',
        'Exo 2'            => 'wght@300;400;500;600;700;800;900',
        'Chakra Petch'     => 'wght@300;400;500;600;700',
        'Barlow'           => 'wght@300;400;500;600;700',
        'Open Sans'        => 'wght@300;400;500;600;700',
        'Lato'             => 'wght@300;400;700',
        'Nunito'           => 'wght@300;400;500;600;700;800',
        'Source Sans 3'    => 'wght@300;400;600;700',
        'Rubik'            => 'wght@300;400;500;600;700',
        'DM Sans'          => 'wght@300;400;500;600;700',
        'Figtree'          => 'wght@300;400;500;600;700;800',
        'Noto Sans'        => 'wght@300;400;600;700',
        'Mulish'           => 'wght@300;400;500;600;700;800',
    );

    $is_default_display = ( $font_display === 'Oswald' );
    $is_default_body    = ( $font_body === 'Barlow' );

    // Only enqueue a custom font URL if something changed
    if ( ! $is_default_display || ! $is_default_body ) {
        $families = array();
        if ( ! $is_default_display && isset( $gf_weights[ $font_display ] ) ) {
            $families[] = 'family=' . urlencode( $font_display ) . ':' . $gf_weights[ $font_display ];
        }
        if ( ! $is_default_body && isset( $gf_weights[ $font_body ] ) ) {
            $families[] = 'family=' . urlencode( $font_body ) . ':' . $gf_weights[ $font_body ];
        }
        if ( ! empty( $families ) ) {
            $font_url = 'https://fonts.googleapis.com/css2?' . implode( '&', $families ) . '&display=swap';
            wp_enqueue_style( 'mammuts-custom-fonts', $font_url, array(), null );
        }
    }

    // Output the font CSS variables
    $font_css_parts = array();
    if ( ! $is_default_display ) {
        $font_css_parts[] = "--font-display: '{$font_display}', 'Impact', sans-serif;";
    }
    if ( ! $is_default_body ) {
        $font_css_parts[] = "--font-body: '{$font_body}', 'Helvetica Neue', sans-serif;";
    }
    if ( ! empty( $font_css_parts ) ) {
        wp_add_inline_style( 'mammuts-style', ':root { ' . implode( ' ', $font_css_parts ) . ' }' );
    }

    // If banner fade effect is disabled, hide the overlay and remove text shadow
    $fade_enabled = get_theme_mod( 'mammuts_banner_fade_enabled', true );
    if ( ! $fade_enabled ) {
        $fade_css  = '.header-banner-fade { display: none !important; }';
        $fade_css .= '.header-banner-title { text-shadow: none; }';
        $fade_css .= '.header-banner-content { mix-blend-mode: normal; }';
        wp_add_inline_style( 'mammuts-style', $fade_css );
    }
}
add_action( 'wp_enqueue_scripts', 'mammuts_custom_css', 20 );


/* ============================================
 * Banner Visibility Helper
 * ============================================ */
function mammuts_show_banner() {
    $visibility = get_theme_mod( 'mammuts_banner_visibility', 'front_only' );

    if ( $visibility === 'none' ) {
        return false;
    }

    if ( $visibility === 'all_pages' ) {
        return true;
    }

    // Default: front_only
    return is_front_page();
}

/**
 * Returns CSS classes for hiding any banner on mobile.
 * Pass 'hero' for the front-page hero, anything else for subpage banners.
 */
function mammuts_banner_mobile_class( $type = 'subpage' ) {
    if ( 'hero' === $type ) {
        return get_theme_mod( 'mammuts_hero_hide_on_mobile', false ) ? ' hide-banner-on-mobile' : '';
    }
    return get_theme_mod( 'mammuts_banner_hide_on_mobile', true ) ? ' hide-banner-on-mobile' : '';
}


/* ============================================
 * Subpage Navigation
 * ============================================ */

/**
 * Outputs a navigation strip of child pages if the current page
 * has any published subpages. Renders nothing otherwise.
 *
 * Call after mammuts_page_header_banner() in page templates.
 */
function mammuts_subpage_nav() {
    // Only run on singular page views — guards against archive/search contexts
    if ( ! is_singular( 'page' ) ) {
        return;
    }

    $current_id = get_queried_object_id();
    if ( ! $current_id ) {
        return;
    }

    $children = get_pages( array(
        'parent'      => $current_id,
        'post_status' => 'publish',
        'sort_column' => 'menu_order,post_title',
        'number'      => 0,
    ) );

    if ( empty( $children ) ) {
        // No children → try sibling navigation (for leaf pages like Tabelle, Spiele)
        mammuts_sibling_nav( $current_id );
        return;
    }

    // ── Sort children by their order in the primary nav menu ──
    $menu_order = array();
    $locations  = get_nav_menu_locations();
    if ( ! empty( $locations['primary'] ) ) {
        $menu_items = wp_get_nav_menu_items( $locations['primary'] );
        if ( is_array( $menu_items ) ) {
            foreach ( $menu_items as $pos => $item ) {
                if ( isset( $item->type, $item->object ) &&
                     $item->type === 'post_type' && $item->object === 'page' ) {
                    $menu_order[ intval( $item->object_id ) ] = $pos;
                }
            }
        }
    }

    if ( ! empty( $menu_order ) ) {
        usort( $children, function( $a, $b ) use ( $menu_order ) {
            $pos_a = isset( $menu_order[ $a->ID ] ) ? $menu_order[ $a->ID ] : 9999;
            $pos_b = isset( $menu_order[ $b->ID ] ) ? $menu_order[ $b->ID ] : 9999;
            if ( $pos_a !== $pos_b ) {
                return $pos_a - $pos_b;
            }
            return strcmp( $a->post_title, $b->post_title );
        } );
    }

    // Render using the same pill-tab style as sibling nav
    $page_title = get_the_title( $current_id );
    ?>
    <nav class="sibling-nav" aria-label="<?php echo esc_attr( $page_title ); ?> – <?php esc_attr_e( 'Unterseiten', 'mammuts' ); ?>">
        <div class="container">
            <div class="sibling-nav-inner">
                <span class="sibling-nav-parent sibling-nav-parent--static" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    <span><?php echo esc_html( $page_title ); ?></span>
                </span>
                <div class="sibling-nav-scroll-wrap">
                    <span class="sibling-nav-scroll-indicator sibling-nav-scroll-indicator--left" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></span>
                    <span class="sibling-nav-scroll-indicator sibling-nav-scroll-indicator--right" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg></span>
                    <div class="sibling-nav-scroll">
                        <ul class="sibling-nav-list">
                            <?php foreach ( $children as $child ) : ?>
                            <li class="sibling-nav-item">
                                <a href="<?php echo esc_url( get_permalink( $child->ID ) ); ?>" class="sibling-nav-link">
                                    <?php echo esc_html( $child->post_title ); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php
}




/**
 * Sibling Navigation
 *
 * Displays a compact pill-style tab bar on leaf pages (pages without children).
 * Shows all siblings (pages sharing the same parent), sorted by menu order,
 * with the current page highlighted. Enables quick switching between e.g.
 * "Tabelle" ↔ "Spiele" ↔ "Roster" without reopening the main menu.
 *
 * Called automatically from mammuts_subpage_nav() when the current page has
 * no children but does have a parent.
 *
 * @param int $current_id The current page ID.
 */
function mammuts_sibling_nav( $current_id ) {
    $parent_id = wp_get_post_parent_id( $current_id );
    if ( ! $parent_id ) {
        return; // top-level page with no children — nothing to show
    }

    // Fetch siblings (= children of the same parent)
    $siblings = get_pages( array(
        'parent'      => $parent_id,
        'post_status' => 'publish',
        'sort_column' => 'menu_order,post_title',
        'number'      => 0,
    ) );

    // Need at least 2 siblings to make navigation useful
    if ( count( $siblings ) < 2 ) {
        return;
    }

    // ── Sort siblings by their order in the primary nav menu ──
    $menu_order = array();
    $locations  = get_nav_menu_locations();
    if ( ! empty( $locations['primary'] ) ) {
        $menu_items = wp_get_nav_menu_items( $locations['primary'] );
        if ( is_array( $menu_items ) ) {
            foreach ( $menu_items as $pos => $item ) {
                if ( isset( $item->type, $item->object ) &&
                     $item->type === 'post_type' && $item->object === 'page' ) {
                    $menu_order[ intval( $item->object_id ) ] = $pos;
                }
            }
        }
    }

    if ( ! empty( $menu_order ) ) {
        usort( $siblings, function( $a, $b ) use ( $menu_order ) {
            $pos_a = isset( $menu_order[ $a->ID ] ) ? $menu_order[ $a->ID ] : 9999;
            $pos_b = isset( $menu_order[ $b->ID ] ) ? $menu_order[ $b->ID ] : 9999;
            if ( $pos_a !== $pos_b ) {
                return $pos_a - $pos_b;
            }
            return strcmp( $a->post_title, $b->post_title );
        } );
    }

    $parent_title = get_the_title( $parent_id );
    $parent_url   = get_permalink( $parent_id );
    $current_url  = trailingslashit( get_permalink( $current_id ) );
    ?>
    <nav class="sibling-nav" aria-label="<?php echo esc_attr( $parent_title ); ?> – <?php esc_attr_e( 'Unterseiten', 'mammuts' ); ?>">
        <div class="container">
            <div class="sibling-nav-inner">
                <a href="<?php echo esc_url( $parent_url ); ?>" class="sibling-nav-parent" title="<?php echo esc_attr( $parent_title ); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    <span><?php echo esc_html( $parent_title ); ?></span>
                </a>
                <div class="sibling-nav-scroll-wrap">
                    <span class="sibling-nav-scroll-indicator sibling-nav-scroll-indicator--left" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></span>
                    <span class="sibling-nav-scroll-indicator sibling-nav-scroll-indicator--right" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg></span>
                    <div class="sibling-nav-scroll">
                        <ul class="sibling-nav-list">
                            <?php foreach ( $siblings as $sibling ) :
                                $sib_url    = get_permalink( $sibling->ID );
                                $is_current = trailingslashit( $sib_url ) === $current_url;
                            ?>
                            <li class="sibling-nav-item<?php echo $is_current ? ' is-active' : ''; ?>">
                                <a href="<?php echo esc_url( $sib_url ); ?>" class="sibling-nav-link"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
                                    <?php echo esc_html( $sibling->post_title ); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php
}


/**
 * Check if SportsPress is active
 */
function mammuts_has_sportspress() {
    return class_exists( 'SportsPress' );
}

/**
 * Get upcoming events (next games)
 */
function mammuts_get_next_events( $limit = 3 ) {
    if ( ! mammuts_has_sportspress() ) {
        return array();
    }

    // SportsPress events can live in one of two post statuses:
    //   - 'future'  → scheduled post with post_date in the future (classic SP behaviour)
    //   - 'publish' → already-published post whose post_date sits in the future (also valid)
    // We query both. WP's built-in 'future' status already guarantees post_date > now(),
    // so no extra date_query is needed there. For 'publish' we add an "after now" filter.
    $now_local = current_time( 'mysql' );
    $current_season = mammuts_get_current_season_slug();

    $run_query = function( $with_season ) use ( $limit, $now_local, $current_season ) {
        $args = array(
            'post_type'      => 'sp_event',
            'posts_per_page' => $limit,
            'post_status'    => array( 'publish', 'future' ),
            'orderby'        => 'date',
            'order'          => 'ASC',
            'date_query'     => array(
                array(
                    'after'     => $now_local,
                    'inclusive' => false,
                    'column'    => 'post_date',
                ),
            ),
        );

        if ( $with_season && ! empty( $current_season ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'sp_season',
                    'field'    => 'slug',
                    'terms'    => $current_season,
                ),
            );
        }

        return get_posts( $args );
    };

    // Try with season filter first
    if ( ! empty( $current_season ) ) {
        $events = $run_query( true );
        if ( ! empty( $events ) ) {
            return $events;
        }
    }

    // Fallback: any season
    return $run_query( false );
}

/**
 * Get the current/active SportsPress season slug.
 *
 * Resolves via (in order of preference):
 *   1. Explicit Customizer setting `mammuts_current_season`
 *   2. Most recent sp_season term (by term ID — newest terms have highest ID)
 *   3. Empty string (no filter)
 */
function mammuts_get_current_season_slug() {
    // 1. Customizer override
    $from_customizer = get_theme_mod( 'mammuts_current_season', '' );
    if ( ! empty( $from_customizer ) ) {
        return $from_customizer;
    }

    // 2. Fall back to newest season term
    if ( ! taxonomy_exists( 'sp_season' ) ) {
        return '';
    }

    $seasons = get_terms( array(
        'taxonomy'   => 'sp_season',
        'hide_empty' => false,
        'orderby'    => 'id',
        'order'      => 'DESC',
        'number'     => 1,
    ) );

    if ( ! empty( $seasons ) && ! is_wp_error( $seasons ) ) {
        return $seasons[0]->slug;
    }

    return '';
}

/**
 * Get recent results (past games)
 */
function mammuts_get_recent_results( $limit = 3 ) {
    if ( ! mammuts_has_sportspress() ) {
        return array();
    }

    $args = array(
        'post_type'      => 'sp_event',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return get_posts( $args );
}

/**
 * Get players by position
 */
function mammuts_get_players_by_position( $position_slug = '', $limit = -1 ) {
    if ( ! mammuts_has_sportspress() ) {
        return array();
    }

    $args = array(
        'post_type'      => 'sp_player',
        'posts_per_page' => $limit,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );

    if ( $position_slug ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'sp_position',
                'field'    => 'slug',
                'terms'    => $position_slug,
            ),
        );
    }

    return get_posts( $args );
}

/**
 * Get event details (teams, scores, venue)
 */
function mammuts_get_event_details( $event_id ) {
    if ( ! mammuts_has_sportspress() ) {
        return null;
    }

    $event = new SP_Event( $event_id );
    $results = $event->results();
    $teams   = get_post_meta( $event_id, 'sp_team', false );
    $venue   = wp_get_post_terms( $event_id, 'sp_venue' );

    return array(
        'teams'   => $teams,
        'results' => $results,
        'venue'   => ! empty( $venue ) ? $venue[0]->name : '',
        'date'    => get_the_date( '', $event_id ),
        'time'    => get_the_time( '', $event_id ),
    );
}

/**
 * Get all player positions (taxonomy)
 */
function mammuts_get_positions() {
    if ( ! mammuts_has_sportspress() ) {
        return array();
    }

    return get_terms( array(
        'taxonomy'   => 'sp_position',
        'hide_empty' => true,
    ) );
}


/* ============================================
 * Custom Menu Walker with Dropdown Support
 * ============================================ */
class Mammuts_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            // Level 1 → Level 2: main dropdown panel
            $output .= '<div class="nav-dropdown">';
        } elseif ( $depth === 1 ) {
            // Level 2 → Level 3: fly-out sub-dropdown
            $output .= '<div class="nav-sub-dropdown">';
        }
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</div>';
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes   = array_filter( $item->classes );
        $classes[] = 'nav-item';
        $has_children = in_array( 'menu-item-has-children', $item->classes );

        // Detect if this item has a real link (not just a #-placeholder)
        $url = trim( $item->url );
        $has_real_link = ( ! empty( $url ) && $url !== '#' && $url !== '#/' );

        $chevron_down  = '<svg class="nav-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>';
        $chevron_right = '<svg class="nav-sub-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 6 15 12 9 18"></polyline></svg>';

        if ( $depth === 0 && $has_children ) {
            // Top-level item with dropdown
            $classes[] = 'nav-item--has-dropdown';
            $output .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-link">';
            $output .= esc_html( $item->title );
            $output .= ' ' . $chevron_down;
            $output .= '</a>';
        } elseif ( $depth === 1 && $has_children ) {
            // Level 2 item with sub-children → wrap in nav-sub-parent
            $classes[] = 'nav-sub-parent';
            if ( $has_real_link ) {
                $classes[] = 'nav-dropdown-item--linked';
            }
            $output .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-dropdown-link">';
            $output .= esc_html( $item->title );
            $output .= '</a>';
            $output .= $chevron_right;
        } elseif ( $depth === 1 ) {
            // Level 2 item without sub-children
            if ( $has_real_link ) {
                $classes[] = 'nav-dropdown-item--linked';
            }
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-dropdown-link ' . esc_attr( implode( ' ', $classes ) ) . '">';
            $output .= esc_html( $item->title );
            $output .= '</a>';
        } elseif ( $depth === 2 ) {
            // Level 3 item (inside fly-out)
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-dropdown-link nav-sub-link ' . esc_attr( implode( ' ', $classes ) ) . '">';
            $output .= esc_html( $item->title );
            $output .= '</a>';
        } else {
            // Top-level item without dropdown
            $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-link ' . esc_attr( implode( ' ', $classes ) ) . '">';
            $output .= esc_html( $item->title );
            $output .= '</a>';
        }
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $has_children = in_array( 'menu-item-has-children', $item->classes );
        if ( $depth === 0 && $has_children ) {
            $output .= '</div>'; // close nav-item--has-dropdown
        } elseif ( $depth === 1 && $has_children ) {
            $output .= '</div>'; // close nav-sub-parent
        }
    }
}


/* ============================================
 * Excerpt Length
 * ============================================ */
function mammuts_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'mammuts_excerpt_length' );

function mammuts_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'mammuts_excerpt_more' );


/* ============================================
 * Body Classes
 * ============================================ */
function mammuts_body_classes( $classes ) {
    if ( mammuts_has_sportspress() ) {
        $classes[] = 'has-sportspress';
    }
    if ( is_front_page() ) {
        $classes[] = 'is-front-page';
    }
    if ( mammuts_show_banner() ) {
        $classes[] = 'has-hero-banner';
    }
    return $classes;
}
add_filter( 'body_class', 'mammuts_body_classes' );


/* ============================================
 * Include Template Parts
 * ============================================ */
require_once MAMMUTS_DIR . '/inc/template-tags.php';


/* ============================================
 * Sponsor Custom Post Type
 * ============================================ */
function mammuts_register_sponsors() {
    register_post_type( 'mammuts_sponsor', array(
        'labels' => array(
            'name'               => __( 'Sponsors', 'mammuts' ),
            'singular_name'      => __( 'Sponsor', 'mammuts' ),
            'add_new'            => __( 'Add Sponsor', 'mammuts' ),
            'add_new_item'       => __( 'Add New Sponsor', 'mammuts' ),
            'edit_item'          => __( 'Edit Sponsor', 'mammuts' ),
            'all_items'          => __( 'All Sponsors', 'mammuts' ),
            'menu_name'          => __( 'Sponsors', 'mammuts' ),
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-star-filled',
        'menu_position' => 26,
        'supports'      => array( 'title', 'thumbnail', 'excerpt' ),
        'has_archive'   => false,
    ) );
}
add_action( 'init', 'mammuts_register_sponsors' );


/* ============================================
 * Eilmeldungen (Breaking News Alerts) CPT
 *
 * Timed alerts displayed on the front page above
 * the countdown. Each alert has a start/end datetime,
 * a type (info, warning, urgent), and an optional link.
 * ============================================ */
function mammuts_register_alerts() {
    register_post_type( 'mammuts_alert', array(
        'labels' => array(
            'name'               => __( 'Eilmeldungen', 'mammuts' ),
            'singular_name'      => __( 'Eilmeldung', 'mammuts' ),
            'add_new'            => __( 'Neue Eilmeldung', 'mammuts' ),
            'add_new_item'       => __( 'Neue Eilmeldung erstellen', 'mammuts' ),
            'edit_item'          => __( 'Eilmeldung bearbeiten', 'mammuts' ),
            'all_items'          => __( 'Alle Eilmeldungen', 'mammuts' ),
            'menu_name'          => __( 'Eilmeldungen', 'mammuts' ),
            'not_found'          => __( 'Keine Eilmeldungen gefunden.', 'mammuts' ),
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-megaphone',
        'menu_position' => 5,
        'supports'      => array( 'title' ),
        'has_archive'   => false,
    ) );
}
add_action( 'init', 'mammuts_register_alerts' );

/**
 * Alert Metabox: Settings (type, message, start/end, link)
 */
function mammuts_alert_meta_boxes() {
    add_meta_box(
        'mammuts_alert_settings',
        __( 'Eilmeldung — Einstellungen', 'mammuts' ),
        'mammuts_alert_settings_callback',
        'mammuts_alert',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mammuts_alert_meta_boxes' );

function mammuts_alert_settings_callback( $post ) {
    wp_nonce_field( 'mammuts_alert_nonce', 'mammuts_alert_nonce_field' );

    $message    = get_post_meta( $post->ID, '_mammuts_alert_message', true );
    $type       = get_post_meta( $post->ID, '_mammuts_alert_type', true ) ?: 'info';
    $badge_text = get_post_meta( $post->ID, '_mammuts_alert_badge_text', true );
    $color      = get_post_meta( $post->ID, '_mammuts_alert_color', true );
    $start      = get_post_meta( $post->ID, '_mammuts_alert_start', true );
    $end        = get_post_meta( $post->ID, '_mammuts_alert_end', true );
    $link_url   = get_post_meta( $post->ID, '_mammuts_alert_link_url', true );
    $link_text  = get_post_meta( $post->ID, '_mammuts_alert_link_text', true );

    $types = array(
        'info'    => '🔵 Info — Allgemeine Nachricht',
        'warning' => '🟡 Warnung — Wichtige Änderung',
        'urgent'  => '🔴 Dringend — Eilmeldung',
        'custom'  => '🎨 Eigene Farbe',
    );

    // Default colors per type for the color picker initial value
    $type_default_colors = array(
        'info'    => '#5b7fa4',
        'warning' => '#d4a017',
        'urgent'  => '#c8102e',
        'custom'  => '#c8102e',
    );

    // Default badge labels per type
    $type_default_labels = array(
        'info'    => 'Info',
        'warning' => 'Achtung',
        'urgent'  => 'Eilmeldung',
        'custom'  => '',
    );

    // Determine effective color
    $effective_color = ! empty( $color ) ? $color : ( $type_default_colors[ $type ] ?? '#5b7fa4' );

    ?>
    <style>
        .mammuts-alert-field { margin-bottom: 16px; }
        .mammuts-alert-field label { display: block; font-weight: 600; margin-bottom: 4px; }
        .mammuts-alert-field input, .mammuts-alert-field select, .mammuts-alert-field textarea {
            width: 100%; max-width: 500px;
        }
        .mammuts-alert-field textarea { min-height: 60px; }
        .mammuts-alert-row { display: flex; gap: 20px; flex-wrap: wrap; }
        .mammuts-alert-row > div { flex: 1; min-width: 200px; }
        .mammuts-color-row { display: flex; align-items: center; gap: 12px; }
        .mammuts-color-preview {
            width: 36px; height: 36px; border-radius: 6px; border: 2px solid #ddd;
            flex-shrink: 0; cursor: pointer; position: relative;
        }
        .mammuts-color-preview input[type="color"] {
            position: absolute; inset: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer; border: none; padding: 0;
        }
        .mammuts-alert-badge-preview {
            display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px;
            border-radius: 3px; font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.12em; color: #fff; margin-top: 8px;
        }
    </style>

    <div class="mammuts-alert-field">
        <label for="mammuts_alert_message"><?php esc_html_e( 'Nachricht:', 'mammuts' ); ?></label>
        <textarea id="mammuts_alert_message" name="mammuts_alert_message" placeholder="<?php esc_attr_e( 'z.B. Spielverlegung: Auswärtsspiel gegen Eagles auf Samstag verschoben!', 'mammuts' ); ?>"><?php echo esc_textarea( $message ); ?></textarea>
        <p class="description"><?php esc_html_e( 'Der Text der Eilmeldung. Kurz und knackig halten.', 'mammuts' ); ?></p>
    </div>

    <div class="mammuts-alert-field">
        <label for="mammuts_alert_type"><?php esc_html_e( 'Vorlage:', 'mammuts' ); ?></label>
        <select id="mammuts_alert_type" name="mammuts_alert_type" style="max-width:350px;">
            <?php foreach ( $types as $val => $label ) : ?>
                <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $type, $val ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php esc_html_e( 'Vorlage wählen oder „Eigene Farbe" für volle Kontrolle.', 'mammuts' ); ?></p>
    </div>

    <div class="mammuts-alert-field">
        <label for="mammuts_alert_badge_text"><?php esc_html_e( 'Kategorie / Badge-Text:', 'mammuts' ); ?></label>
        <input type="text" id="mammuts_alert_badge_text" name="mammuts_alert_badge_text"
               value="<?php echo esc_attr( $badge_text ); ?>"
               placeholder="<?php echo esc_attr( $type_default_labels[ $type ] ?? 'Info' ); ?>"
               style="max-width:300px;">
        <p class="description"><?php esc_html_e( 'Freitext, z.B. „Spielverlegung", „Absage", „Neuzugang", „Trainingsinfo". Leer = Standard je nach Vorlage.', 'mammuts' ); ?></p>
    </div>

    <div class="mammuts-alert-field">
        <label><?php esc_html_e( 'Farbe:', 'mammuts' ); ?></label>
        <div class="mammuts-color-row">
            <div class="mammuts-color-preview" id="mammuts_color_preview" style="background:<?php echo esc_attr( $effective_color ); ?>;">
                <input type="color" id="mammuts_alert_color" name="mammuts_alert_color"
                       value="<?php echo esc_attr( $effective_color ); ?>">
            </div>
            <code id="mammuts_color_hex" style="font-size:13px;"><?php echo esc_html( $effective_color ); ?></code>
            <button type="button" class="button button-small" id="mammuts_color_reset"><?php esc_html_e( 'Vorlagen-Farbe', 'mammuts' ); ?></button>
        </div>
        <div class="mammuts-alert-badge-preview" id="mammuts_badge_preview" style="background:<?php echo esc_attr( $effective_color ); ?>;">
            <?php echo esc_html( ! empty( $badge_text ) ? $badge_text : ( $type_default_labels[ $type ] ?? 'Info' ) ); ?>
        </div>
        <p class="description" style="margin-top:6px;"><?php esc_html_e( 'Bestimmt die Farbe des Balkens und des Badges. Bei Vorlagen-Typen wird die Standard-Farbe vorbelegt.', 'mammuts' ); ?></p>
    </div>

    <div class="mammuts-alert-row">
        <div class="mammuts-alert-field">
            <label for="mammuts_alert_start"><?php esc_html_e( 'Anzeigen ab:', 'mammuts' ); ?></label>
            <input type="datetime-local" id="mammuts_alert_start" name="mammuts_alert_start"
                   value="<?php echo esc_attr( $start ); ?>">
        </div>
        <div class="mammuts-alert-field">
            <label for="mammuts_alert_end"><?php esc_html_e( 'Anzeigen bis:', 'mammuts' ); ?></label>
            <input type="datetime-local" id="mammuts_alert_end" name="mammuts_alert_end"
                   value="<?php echo esc_attr( $end ); ?>">
        </div>
    </div>
    <p class="description" style="margin-top:-8px;"><?php esc_html_e( 'Leer lassen = sofort anzeigen / nie ausblenden.', 'mammuts' ); ?></p>

    <div class="mammuts-alert-row" style="margin-top:12px;">
        <div class="mammuts-alert-field">
            <label for="mammuts_alert_link_url"><?php esc_html_e( 'Link-URL (optional):', 'mammuts' ); ?></label>
            <input type="url" id="mammuts_alert_link_url" name="mammuts_alert_link_url"
                   value="<?php echo esc_url( $link_url ); ?>" placeholder="https://...">
        </div>
        <div class="mammuts-alert-field">
            <label for="mammuts_alert_link_text"><?php esc_html_e( 'Link-Text:', 'mammuts' ); ?></label>
            <input type="text" id="mammuts_alert_link_text" name="mammuts_alert_link_text"
                   value="<?php echo esc_attr( $link_text ); ?>" placeholder="<?php esc_attr_e( 'Mehr erfahren', 'mammuts' ); ?>">
        </div>
    </div>

    <script>
    (function(){
        var typeSelect   = document.getElementById('mammuts_alert_type');
        var colorInput   = document.getElementById('mammuts_alert_color');
        var colorPreview = document.getElementById('mammuts_color_preview');
        var colorHex     = document.getElementById('mammuts_color_hex');
        var badgeText    = document.getElementById('mammuts_alert_badge_text');
        var badgePreview = document.getElementById('mammuts_badge_preview');
        var resetBtn     = document.getElementById('mammuts_color_reset');

        var defaultColors = <?php echo wp_json_encode( $type_default_colors ); ?>;
        var defaultLabels = <?php echo wp_json_encode( $type_default_labels ); ?>;

        function updateColor(hex) {
            colorInput.value = hex;
            colorPreview.style.background = hex;
            colorHex.textContent = hex;
            badgePreview.style.background = hex;
        }

        function updateBadgeText() {
            var text = badgeText.value || defaultLabels[typeSelect.value] || 'Info';
            badgePreview.textContent = text;
        }

        typeSelect.addEventListener('change', function() {
            var t = this.value;
            updateColor(defaultColors[t] || '#5b7fa4');
            badgeText.placeholder = defaultLabels[t] || '';
            updateBadgeText();
        });

        colorInput.addEventListener('input', function() {
            updateColor(this.value);
        });

        badgeText.addEventListener('input', updateBadgeText);

        resetBtn.addEventListener('click', function() {
            updateColor(defaultColors[typeSelect.value] || '#5b7fa4');
        });
    })();
    </script>
    <?php
}

function mammuts_save_alert_meta( $post_id ) {
    if ( ! isset( $_POST['mammuts_alert_nonce_field'] ) ||
         ! wp_verify_nonce( $_POST['mammuts_alert_nonce_field'], 'mammuts_alert_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $fields = array(
        'mammuts_alert_message'    => 'textarea',
        'mammuts_alert_type'       => 'text',
        'mammuts_alert_badge_text' => 'text',
        'mammuts_alert_color'      => 'text',
        'mammuts_alert_start'      => 'text',
        'mammuts_alert_end'        => 'text',
        'mammuts_alert_link_url'   => 'url',
        'mammuts_alert_link_text'  => 'text',
    );

    foreach ( $fields as $field => $sanitize ) {
        if ( isset( $_POST[ $field ] ) ) {
            $value = wp_unslash( $_POST[ $field ] );
            if ( $sanitize === 'url' ) {
                $value = esc_url_raw( $value );
            } elseif ( $sanitize === 'textarea' ) {
                $value = sanitize_textarea_field( $value );
            } else {
                $value = sanitize_text_field( $value );
            }
            update_post_meta( $post_id, '_' . $field, $value );
        }
    }
}
add_action( 'save_post_mammuts_alert', 'mammuts_save_alert_meta' );

/**
 * Get currently active alerts.
 *
 * @return array Array of alert post objects with meta.
 */
/**
 * Parse a datetime string from datetime-local input.
 * Handles various formats: 2026-04-24T00:53, 2026-04-24 00:53, etc.
 *
 * @param string       $str  The datetime string.
 * @param DateTimeZone $tz   The timezone to interpret it in.
 * @return int|false   Unix timestamp, or false on failure.
 */
function mammuts_parse_alert_datetime( $str, $tz ) {
    // Normalize: replace T with space
    $str = str_replace( 'T', ' ', trim( $str ) );

    // Try parsing with DateTime
    $formats = array(
        'Y-m-d H:i:s',
        'Y-m-d H:i',
        'd.m.Y H:i:s',
        'd.m.Y H:i',
        'd.m.Y, H:i',
    );

    foreach ( $formats as $fmt ) {
        $dt = DateTime::createFromFormat( $fmt, $str, $tz );
        if ( $dt !== false ) {
            return $dt->getTimestamp();
        }
    }

    // Last resort: let PHP figure it out
    $ts = strtotime( $str );
    if ( $ts !== false ) {
        // Adjust for WP timezone offset since strtotime uses server TZ
        $server_offset = date( 'Z' );
        $wp_offset     = $tz->getOffset( new DateTime( 'now', $tz ) );
        return $ts - $server_offset + $wp_offset;
    }

    return false;
}

/**
 * Get currently active alerts.
 *
 * @return array Array of alert data arrays.
 */
function mammuts_get_active_alerts() {
    $wp_tz  = wp_timezone();
    $now_dt = new DateTime( 'now', $wp_tz );
    $now_ts = $now_dt->getTimestamp();

    $alerts = get_posts( array(
        'post_type'      => 'mammuts_alert',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    $active = array();
    foreach ( $alerts as $alert ) {
        $start   = get_post_meta( $alert->ID, '_mammuts_alert_start', true );
        $end     = get_post_meta( $alert->ID, '_mammuts_alert_end', true );
        $message = get_post_meta( $alert->ID, '_mammuts_alert_message', true );

        if ( empty( $message ) ) continue;

        // Auto-expire: if no end date set, hide alerts older than 14 days
        if ( empty( $end ) ) {
            $post_date    = get_post_field( 'post_date', $alert->ID );
            $post_date_ts = strtotime( $post_date );
            if ( $post_date_ts && ( $now_ts - $post_date_ts ) > ( 14 * DAY_IN_SECONDS ) ) {
                continue;
            }
        }

        // Parse datetime — normalize the T separator and try multiple formats
        if ( ! empty( $start ) ) {
            $start_ts = mammuts_parse_alert_datetime( $start, $wp_tz );
            if ( $start_ts && $now_ts < $start_ts ) continue;
        }
        if ( ! empty( $end ) ) {
            $end_ts = mammuts_parse_alert_datetime( $end, $wp_tz );
            if ( $end_ts && $now_ts > $end_ts ) continue;
        }

        $active[] = array(
            'id'         => $alert->ID,
            'title'      => get_the_title( $alert->ID ),
            'message'    => $message,
            'type'       => get_post_meta( $alert->ID, '_mammuts_alert_type', true ) ?: 'info',
            'badge_text' => get_post_meta( $alert->ID, '_mammuts_alert_badge_text', true ),
            'color'      => get_post_meta( $alert->ID, '_mammuts_alert_color', true ),
            'link_url'   => get_post_meta( $alert->ID, '_mammuts_alert_link_url', true ),
            'link_text'  => get_post_meta( $alert->ID, '_mammuts_alert_link_text', true ) ?: __( 'Mehr erfahren', 'mammuts' ),
        );
    }

    return $active;
}

/**
 * Render alerts ticker on the front page.
 */
function mammuts_render_alerts() {
    $alerts = mammuts_get_active_alerts();
    if ( empty( $alerts ) ) return;

    $count = count( $alerts );
    ?>
    <div class="mammuts-alerts" data-count="<?php echo intval( $count ); ?>">
        <div class="mammuts-alerts-inner">
            <?php foreach ( $alerts as $i => $alert ) :
                $type_icons = array(
                    'info'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
                    'warning' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                    'urgent'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                    'custom'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
                );
                $type_default_labels = array(
                    'info'    => __( 'Info', 'mammuts' ),
                    'warning' => __( 'Achtung', 'mammuts' ),
                    'urgent'  => __( 'Eilmeldung', 'mammuts' ),
                    'custom'  => __( 'Info', 'mammuts' ),
                );
                $icon  = $type_icons[ $alert['type'] ] ?? $type_icons['info'];

                // Badge text: use custom text if set, otherwise fall back to type default
                $label = ! empty( $alert['badge_text'] )
                    ? $alert['badge_text']
                    : ( $type_default_labels[ $alert['type'] ] ?? __( 'Info', 'mammuts' ) );

                // Determine if we use custom color styling
                $custom_color = $alert['color'] ?? '';
                $use_custom_style = ! empty( $custom_color );

                // Build inline style for custom-colored alerts
                $inline_style = '--alert-index: ' . $i . ';';
                if ( $use_custom_style ) {
                    $inline_style .= ' --alert-custom-color: ' . esc_attr( $custom_color ) . ';';
                }

                // Determine CSS class: use custom class when color is set
                $alert_class = 'mammuts-alert';
                if ( $use_custom_style ) {
                    // Check if color is dark to decide text color
                    $hex = ltrim( $custom_color, '#' );
                    $r = hexdec( substr( $hex, 0, 2 ) );
                    $g = hexdec( substr( $hex, 2, 2 ) );
                    $b = hexdec( substr( $hex, 4, 2 ) );
                    $luminance = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;
                    $is_dark = $luminance < 0.55;

                    if ( $is_dark ) {
                        $alert_class .= ' mammuts-alert--custom-dark';
                    } else {
                        $alert_class .= ' mammuts-alert--custom-light';
                    }
                } else {
                    $alert_class .= ' mammuts-alert--' . esc_attr( $alert['type'] );
                }
            ?>
            <div class="<?php echo esc_attr( $alert_class ); ?>"
                 style="<?php echo $inline_style; ?>">
                <div class="mammuts-alert-badge">
                    <?php echo $icon; ?>
                    <span><?php echo esc_html( $label ); ?></span>
                </div>
                <p class="mammuts-alert-text"><?php echo esc_html( $alert['message'] ); ?></p>
                <?php if ( ! empty( $alert['link_url'] ) ) : ?>
                    <a href="<?php echo esc_url( $alert['link_url'] ); ?>" class="mammuts-alert-link">
                        <?php echo esc_html( $alert['link_text'] ); ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Sponsor Meta Box: Website URL
 */
function mammuts_sponsor_meta_boxes() {
    add_meta_box(
        'mammuts_sponsor_url',
        __( 'Sponsor Links', 'mammuts' ),
        'mammuts_sponsor_url_callback',
        'mammuts_sponsor',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mammuts_sponsor_meta_boxes' );

function mammuts_sponsor_url_callback( $post ) {
    wp_nonce_field( 'mammuts_sponsor_url_nonce', 'mammuts_sponsor_nonce' );
    $url       = get_post_meta( $post->ID, '_mammuts_sponsor_url', true );
    $facebook  = get_post_meta( $post->ID, '_mammuts_sponsor_facebook', true );
    $instagram = get_post_meta( $post->ID, '_mammuts_sponsor_instagram', true );
    ?>
    <p>
        <label for="mammuts_sponsor_url"><strong><?php esc_html_e( 'Website URL:', 'mammuts' ); ?></strong></label><br>
        <input type="url" id="mammuts_sponsor_url" name="mammuts_sponsor_url"
               value="<?php echo esc_url( $url ); ?>"
               style="width:100%;margin-top:6px;" placeholder="https://www.sponsor-website.de">
    </p>
    <p>
        <label for="mammuts_sponsor_facebook"><strong><?php esc_html_e( 'Facebook URL:', 'mammuts' ); ?></strong></label><br>
        <input type="url" id="mammuts_sponsor_facebook" name="mammuts_sponsor_facebook"
               value="<?php echo esc_url( $facebook ); ?>"
               style="width:100%;margin-top:6px;" placeholder="https://www.facebook.com/sponsor">
    </p>
    <p>
        <label for="mammuts_sponsor_instagram"><strong><?php esc_html_e( 'Instagram URL:', 'mammuts' ); ?></strong></label><br>
        <input type="url" id="mammuts_sponsor_instagram" name="mammuts_sponsor_instagram"
               value="<?php echo esc_url( $instagram ); ?>"
               style="width:100%;margin-top:6px;" placeholder="https://www.instagram.com/sponsor">
    </p>
    <?php
}

function mammuts_save_sponsor_meta( $post_id ) {
    if ( ! isset( $_POST['mammuts_sponsor_nonce'] ) ||
         ! wp_verify_nonce( $_POST['mammuts_sponsor_nonce'], 'mammuts_sponsor_url_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['mammuts_sponsor_url'] ) ) {
        update_post_meta( $post_id, '_mammuts_sponsor_url', esc_url_raw( $_POST['mammuts_sponsor_url'] ) );
    }
    if ( isset( $_POST['mammuts_sponsor_facebook'] ) ) {
        update_post_meta( $post_id, '_mammuts_sponsor_facebook', esc_url_raw( $_POST['mammuts_sponsor_facebook'] ) );
    }
    if ( isset( $_POST['mammuts_sponsor_instagram'] ) ) {
        update_post_meta( $post_id, '_mammuts_sponsor_instagram', esc_url_raw( $_POST['mammuts_sponsor_instagram'] ) );
    }
}
add_action( 'save_post', 'mammuts_save_sponsor_meta' );


/* ============================================
 * SportsPress Filter Metabox for Pages
 *
 * Adds a "SportsPress Filter" box on page edit screens
 * where editors can select a specific Team and/or League.
 * Templates (Schedule, Roster) use these values to show
 * only the relevant events/players.
 * ============================================ */
function mammuts_page_sp_filter_metabox() {
    if ( ! mammuts_has_sportspress() ) return;

    add_meta_box(
        'mammuts_sp_filter',
        __( 'SportsPress — Team & Liga Filter', 'mammuts' ),
        'mammuts_sp_filter_callback',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'mammuts_page_sp_filter_metabox' );

function mammuts_sp_filter_callback( $post ) {
    wp_nonce_field( 'mammuts_sp_filter_nonce', 'mammuts_sp_filter_nonce_field' );

    $selected_team   = get_post_meta( $post->ID, '_mammuts_sp_team', true );
    $selected_league = get_post_meta( $post->ID, '_mammuts_sp_league', true );
    $selected_season = get_post_meta( $post->ID, '_mammuts_sp_season', true );
    $selected_role   = get_post_meta( $post->ID, '_mammuts_sp_role', true );

    // Get all SportsPress teams
    $teams = get_posts( array(
        'post_type'      => 'sp_team',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    // Get all leagues
    $leagues = get_terms( array(
        'taxonomy'   => 'sp_league',
        'hide_empty' => false,
    ) );

    // Get all seasons
    $seasons = get_terms( array(
        'taxonomy'   => 'sp_season',
        'hide_empty' => false,
        'orderby'    => 'id',
        'order'      => 'DESC',
    ) );

    // Get all staff roles/jobs
    $roles = get_terms( array(
        'taxonomy'   => 'sp_role',
        'hide_empty' => false,
    ) );

    ?>
    <p>
        <label for="mammuts_sp_team"><strong><?php esc_html_e( 'Mannschaft:', 'mammuts' ); ?></strong></label><br>
        <select id="mammuts_sp_team" name="mammuts_sp_team" style="width:100%;margin-top:4px;">
            <option value=""><?php esc_html_e( '— Alle Mannschaften —', 'mammuts' ); ?></option>
            <?php foreach ( $teams as $team ) : ?>
                <option value="<?php echo esc_attr( $team->ID ); ?>" <?php selected( $selected_team, $team->ID ); ?>>
                    <?php echo esc_html( $team->post_title ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="mammuts_sp_league"><strong><?php esc_html_e( 'Liga:', 'mammuts' ); ?></strong></label><br>
        <select id="mammuts_sp_league" name="mammuts_sp_league" style="width:100%;margin-top:4px;">
            <option value=""><?php esc_html_e( '— Alle Ligen —', 'mammuts' ); ?></option>
            <?php if ( ! is_wp_error( $leagues ) ) : foreach ( $leagues as $league ) : ?>
                <option value="<?php echo esc_attr( $league->slug ); ?>" <?php selected( $selected_league, $league->slug ); ?>>
                    <?php echo esc_html( $league->name ); ?>
                </option>
            <?php endforeach; endif; ?>
        </select>
    </p>
    <p>
        <label for="mammuts_sp_season"><strong><?php esc_html_e( 'Saison:', 'mammuts' ); ?></strong></label><br>
        <select id="mammuts_sp_season" name="mammuts_sp_season" style="width:100%;margin-top:4px;">
            <option value=""><?php esc_html_e( '— Aktuelle Saison (automatisch) —', 'mammuts' ); ?></option>
            <?php if ( ! is_wp_error( $seasons ) ) : foreach ( $seasons as $season ) : ?>
                <option value="<?php echo esc_attr( $season->slug ); ?>" <?php selected( $selected_season, $season->slug ); ?>>
                    <?php echo esc_html( $season->name ); ?>
                </option>
            <?php endforeach; endif; ?>
        </select>
    </p>
    <p>
        <label for="mammuts_sp_role"><strong><?php esc_html_e( 'Rolle / Job (Staff):', 'mammuts' ); ?></strong></label><br>
        <select id="mammuts_sp_role" name="mammuts_sp_role" style="width:100%;margin-top:4px;">
            <option value=""><?php esc_html_e( '— Alle Rollen —', 'mammuts' ); ?></option>
            <?php if ( ! is_wp_error( $roles ) ) : foreach ( $roles as $role ) : ?>
                <option value="<?php echo esc_attr( $role->slug ); ?>" <?php selected( $selected_role, $role->slug ); ?>>
                    <?php echo esc_html( $role->name ); ?>
                </option>
            <?php endforeach; endif; ?>
        </select>
    </p>
    <p class="description" style="font-size:11px;color:#888;">
        <?php esc_html_e( 'Filtert Inhalte auf dieser Seite. Wird von Schedule, Roster, Staff und Standings Templates verwendet. Nicht benötigte Felder einfach auf "Alle" lassen.', 'mammuts' ); ?>
    </p>
    <?php
}

function mammuts_save_sp_filter_meta( $post_id ) {
    if ( ! isset( $_POST['mammuts_sp_filter_nonce_field'] ) ||
         ! wp_verify_nonce( $_POST['mammuts_sp_filter_nonce_field'], 'mammuts_sp_filter_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $fields = array( 'mammuts_sp_team', 'mammuts_sp_league', 'mammuts_sp_season', 'mammuts_sp_role' );
    foreach ( $fields as $field ) {
        if ( isset( $_POST[ $field ] ) ) {
            update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
        }
    }
}
add_action( 'save_post', 'mammuts_save_sp_filter_meta' );


/* ============================================
 * Page-level Staff Order Metabox
 *
 * Lets editors drag-and-drop the display order of
 * staff members ON THIS PAGE. Stored as a comma-
 * separated list of sp_staff post IDs.
 *
 * This is per-page, so the "Coaches" page can have
 * a different order than the "Vorstand" page.
 * ============================================ */
function mammuts_staff_order_metabox() {
    if ( ! mammuts_has_sportspress() ) return;
    if ( ! post_type_exists( 'sp_staff' ) ) return;

    add_meta_box(
        'mammuts_staff_order',
        __( 'Staff-Reihenfolge auf dieser Seite', 'mammuts' ),
        'mammuts_staff_order_callback',
        'page',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'mammuts_staff_order_metabox' );

/**
 * Enqueue jQuery UI Sortable + custom admin script on screens that need it.
 */
function mammuts_admin_enqueue_sortable( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen ) return;

    if ( in_array( $screen->post_type, array( 'page', 'sp_staff' ), true ) ) {
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script(
            'mammuts-admin-sortable',
            get_template_directory_uri() . '/assets/js/admin-sortable.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            filemtime( get_template_directory() . '/assets/js/admin-sortable.js' ),
            true
        );

        // Pass AJAX URL, post ID and nonce to JS
        // Use $_GET['post'] (reliable in editor) with global $post as fallback
        $post_id = 0;
        if ( isset( $_GET['post'] ) ) {
            $post_id = intval( $_GET['post'] );
        } elseif ( isset( $_POST['post_ID'] ) ) {
            $post_id = intval( $_POST['post_ID'] );
        } else {
            global $post;
            $post_id = isset( $post->ID ) ? $post->ID : 0;
        }

        wp_localize_script( 'mammuts-admin-sortable', 'mammuts_sortable_data', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'post_id'  => $post_id,
            'nonce'    => wp_create_nonce( 'mammuts_sortable_nonce' ),
        ) );
    }
}
add_action( 'admin_enqueue_scripts', 'mammuts_admin_enqueue_sortable' );

/**
 * AJAX handler: Save staff display order (per page).
 */
function mammuts_ajax_save_staff_order() {
    check_ajax_referer( 'mammuts_sortable_nonce', '_nonce' );

    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( 'Keine Berechtigung.' );
    }

    $order = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '';
    update_post_meta( $post_id, '_mammuts_staff_order', $order );
    wp_send_json_success();
}
add_action( 'wp_ajax_mammuts_save_staff_order', 'mammuts_ajax_save_staff_order' );

/**
 * AJAX handler: Save role priority order (per staff member).
 */
function mammuts_ajax_save_role_priority() {
    check_ajax_referer( 'mammuts_sortable_nonce', '_nonce' );

    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( 'Keine Berechtigung.' );
    }

    $order = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '';
    update_post_meta( $post_id, '_mammuts_role_priority', $order );
    wp_send_json_success();
}
add_action( 'wp_ajax_mammuts_save_role_priority', 'mammuts_ajax_save_role_priority' );

/**
 * Filter: Re-order sp_staff query results to match the
 * page-level custom order. Used when the page has a
 * SportsPress shortcode in its content.
 */
function mammuts_reorder_staff_posts( $posts, $query ) {
    // Only act on sp_staff queries
    if ( empty( $posts ) || $posts[0]->post_type !== 'sp_staff' ) {
        return $posts;
    }

    $order_ids = isset( $GLOBALS['mammuts_staff_order_ids'] ) ? $GLOBALS['mammuts_staff_order_ids'] : array();
    if ( empty( $order_ids ) ) {
        return $posts;
    }

    $by_id  = array();
    foreach ( $posts as $p ) {
        $by_id[ $p->ID ] = $p;
    }

    $sorted = array();
    foreach ( $order_ids as $oid ) {
        if ( isset( $by_id[ $oid ] ) ) {
            $sorted[] = $by_id[ $oid ];
            unset( $by_id[ $oid ] );
        }
    }
    foreach ( $by_id as $p ) {
        $sorted[] = $p;
    }

    return $sorted;
}

function mammuts_staff_order_callback( $post ) {
    $saved_order = get_post_meta( $post->ID, '_mammuts_staff_order', true );

    // Read the current filter settings to show the right staff
    $filter_team = get_post_meta( $post->ID, '_mammuts_sp_team', true );
    $filter_role = get_post_meta( $post->ID, '_mammuts_sp_role', true );

    $args = array(
        'post_type'      => 'sp_staff',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    );

    if ( ! empty( $filter_team ) ) {
        $args['meta_query'] = array(
            array(
                'key'     => 'sp_team',
                'value'   => $filter_team,
                'compare' => '=',
            ),
        );
    }

    $role = null;
    if ( ! empty( $filter_role ) ) {
        $role = get_term_by( 'slug', $filter_role, 'sp_role' );
    }
    if ( ! $role ) {
        $slug = get_post_field( 'post_name', $post->ID );
        $role = get_term_by( 'slug', $slug, 'sp_role' );
    }

    if ( $role ) {
        $role_ids = array( $role->term_id );
        $children = get_terms( array(
            'taxonomy'   => 'sp_role',
            'child_of'   => $role->term_id,
            'hide_empty' => false,
        ) );
        if ( ! is_wp_error( $children ) ) {
            foreach ( $children as $c ) {
                $role_ids[] = $c->term_id;
            }
        }
        $args['tax_query'] = array(
            array(
                'taxonomy'         => 'sp_role',
                'field'            => 'term_id',
                'terms'            => $role_ids,
                'include_children' => true,
            ),
        );
    }

    $staff = get_posts( $args );

    if ( empty( $staff ) ) {
        echo '<p class="description">' . esc_html__( 'Keine Staff-Mitglieder gefunden. Prüfe die Filter-Einstellungen oben.', 'mammuts' ) . '</p>';
        return;
    }

    // Build ordered list
    $saved_ids = ! empty( $saved_order ) ? array_map( 'intval', array_filter( explode( ',', $saved_order ) ) ) : array();
    $staff_by_id = array();
    foreach ( $staff as $m ) {
        $staff_by_id[ $m->ID ] = $m;
    }

    $ordered = array();
    foreach ( $saved_ids as $sid ) {
        if ( isset( $staff_by_id[ $sid ] ) ) {
            $ordered[] = $staff_by_id[ $sid ];
            unset( $staff_by_id[ $sid ] );
        }
    }
    foreach ( $staff_by_id as $m ) {
        $ordered[] = $m;
    }

    $total   = count( $ordered );
    $ajax    = admin_url( 'admin-ajax.php' );
    $nonce   = wp_create_nonce( 'mammuts_sortable_nonce' );
    $page_id = $post->ID;

    ?>
    <p class="description" style="margin-bottom:10px;">
        <?php esc_html_e( 'Mit den Pfeilen die Reihenfolge ändern. Wird sofort gespeichert.', 'mammuts' ); ?>
    </p>
    <p id="mammuts-order-status" style="font-size:12px;margin-bottom:8px;display:none;"></p>
    <div id="mammuts-staff-list" style="max-width:520px;">
        <?php foreach ( $ordered as $i => $m ) :
            $thumb    = get_the_post_thumbnail_url( $m->ID, 'thumbnail' );
            $roles    = wp_get_post_terms( $m->ID, 'sp_role' );
            $role_str = ! empty( $roles ) ? implode( ', ', wp_list_pluck( $roles, 'name' ) ) : '';
        ?>
        <div class="mammuts-so-row" data-id="<?php echo intval( $m->ID ); ?>" style="display:flex;align-items:center;gap:8px;padding:6px 8px;margin:2px 0;background:#f6f7f7;border:1px solid #ddd;border-radius:4px;font-size:13px;">
            <span class="mammuts-so-num" style="background:#c8102e;color:#fff;font-weight:700;font-size:11px;min-width:22px;height:22px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;"><?php echo $i + 1; ?></span>
            <?php if ( $thumb ) : ?>
                <img src="<?php echo esc_url( $thumb ); ?>" style="width:28px;height:28px;border-radius:3px;object-fit:cover;flex-shrink:0;">
            <?php endif; ?>
            <span style="flex:1;font-weight:600;"><?php echo esc_html( get_the_title( $m->ID ) ); ?></span>
            <?php if ( $role_str ) : ?>
                <span style="color:#999;font-size:10px;text-transform:uppercase;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo esc_html( $role_str ); ?></span>
            <?php endif; ?>
            <button type="button" class="mammuts-so-up button-link" title="Nach oben" style="padding:2px 6px;font-size:16px;line-height:1;<?php echo $i === 0 ? 'visibility:hidden;' : ''; ?>">▲</button>
            <button type="button" class="mammuts-so-down button-link" title="Nach unten" style="padding:2px 6px;font-size:16px;line-height:1;<?php echo $i === $total - 1 ? 'visibility:hidden;' : ''; ?>">▼</button>
        </div>
        <?php endforeach; ?>
    </div>
    <script>
    (function(){
        var list   = document.getElementById('mammuts-staff-list');
        var status = document.getElementById('mammuts-order-status');
        if (!list) return;

        function showStatus(msg, color) {
            status.textContent = msg;
            status.style.color = color;
            status.style.display = 'block';
        }

        function getOrder() {
            var ids = [];
            var rows = list.querySelectorAll('.mammuts-so-row');
            rows.forEach(function(r, i) {
                ids.push(r.getAttribute('data-id'));
                r.querySelector('.mammuts-so-num').textContent = i + 1;
                var up = r.querySelector('.mammuts-so-up');
                var dn = r.querySelector('.mammuts-so-down');
                up.style.visibility = i === 0 ? 'hidden' : '';
                dn.style.visibility = i === rows.length - 1 ? 'hidden' : '';
            });
            return ids.join(',');
        }

        function saveOrder(orderStr) {
            showStatus('⏳ Speichere...', '#dba617');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo esc_js( $ajax ); ?>');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.success) {
                        showStatus('✓ Reihenfolge gespeichert!', '#00a32a');
                        setTimeout(function(){ status.style.display = 'none'; }, 3000);
                    } else {
                        showStatus('✗ Fehler: ' + (resp.data || 'unbekannt'), '#d63638');
                    }
                } catch(e) {
                    showStatus('✗ Antwort-Fehler: ' + xhr.responseText.substring(0, 100), '#d63638');
                }
            };
            xhr.onerror = function() {
                showStatus('✗ Netzwerkfehler', '#d63638');
            };
            xhr.send('action=mammuts_save_staff_order&post_id=<?php echo intval( $page_id ); ?>&order=' + encodeURIComponent(orderStr) + '&_nonce=<?php echo esc_js( $nonce ); ?>');
        }

        list.addEventListener('click', function(e) {
            var btn = e.target.closest('.mammuts-so-up, .mammuts-so-down');
            if (!btn) return;
            e.preventDefault();

            var row = btn.closest('.mammuts-so-row');
            if (!row) return;

            if (btn.classList.contains('mammuts-so-up') && row.previousElementSibling) {
                list.insertBefore(row, row.previousElementSibling);
            } else if (btn.classList.contains('mammuts-so-down') && row.nextElementSibling) {
                list.insertBefore(row.nextElementSibling, row);
            }

            saveOrder(getOrder());
        });
    })();
    </script>
    <?php
}

function mammuts_save_staff_order_meta( $post_id ) {
    // Skip if our field is not in the submission
    if ( ! isset( $_POST['mammuts_staff_order'] ) ) {
        return;
    }

    // Verify nonce
    if ( ! isset( $_POST['mammuts_staff_order_nonce_field'] ) ||
         ! wp_verify_nonce( $_POST['mammuts_staff_order_nonce_field'], 'mammuts_staff_order_nonce' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $value = sanitize_text_field( wp_unslash( $_POST['mammuts_staff_order'] ) );
    update_post_meta( $post_id, '_mammuts_staff_order', $value );
}
add_action( 'save_post_page', 'mammuts_save_staff_order_meta', 20 );


/* ============================================
 * Staff: Role Priority / Order Metabox
 *
 * Allows setting the display order of roles for
 * staff members who have multiple roles.
 * Stored as comma-separated slugs, e.g. "defense-coordinator,head-coach"
 * ============================================ */
function mammuts_staff_role_priority_metabox() {
    if ( ! mammuts_has_sportspress() ) return;
    if ( ! post_type_exists( 'sp_staff' ) ) return;

    add_meta_box(
        'mammuts_role_priority',
        __( 'Rollen-Reihenfolge', 'mammuts' ),
        'mammuts_role_priority_callback',
        'sp_staff',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'mammuts_staff_role_priority_metabox' );

function mammuts_role_priority_callback( $post ) {
    wp_nonce_field( 'mammuts_role_priority_nonce', 'mammuts_role_priority_nonce_field' );

    $saved = get_post_meta( $post->ID, '_mammuts_role_priority', true );

    // Get this member's current roles
    $member_roles = wp_get_post_terms( $post->ID, 'sp_role', array(
        'orderby' => 'name',
        'order'   => 'ASC',
    ) );

    if ( empty( $member_roles ) || is_wp_error( $member_roles ) ) {
        echo '<p class="description">' . esc_html__( 'Erst Rollen zuweisen, dann kann die Reihenfolge festgelegt werden.', 'mammuts' ) . '</p>';
        return;
    }

    // Build ordered list: saved order first, then any remaining
    $saved_slugs = ! empty( $saved ) ? array_map( 'trim', explode( ',', $saved ) ) : array();
    $roles_by_slug = array();
    foreach ( $member_roles as $r ) {
        $roles_by_slug[ $r->slug ] = $r;
    }

    $ordered = array();
    foreach ( $saved_slugs as $slug ) {
        if ( isset( $roles_by_slug[ $slug ] ) ) {
            $ordered[] = $roles_by_slug[ $slug ];
            unset( $roles_by_slug[ $slug ] );
        }
    }
    foreach ( $roles_by_slug as $r ) {
        $ordered[] = $r;
    }

    ?>
    <div id="mammuts-role-sortable" style="margin-bottom:8px;">
        <?php foreach ( $ordered as $i => $r ) : ?>
            <div class="mammuts-role-sort-item" data-slug="<?php echo esc_attr( $r->slug ); ?>"
                 style="display:flex;align-items:center;gap:8px;padding:6px 8px;margin:4px 0;background:#f6f7f7;border:1px solid #ddd;border-radius:4px;cursor:grab;font-size:13px;">
                <span style="color:#999;font-size:16px;line-height:1;">&#x2630;</span>
                <span style="flex:1;"><?php echo esc_html( $r->name ); ?></span>
                <span style="color:#999;font-size:11px;">#<?php echo intval( $i + 1 ); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <input type="hidden" id="mammuts_role_priority" name="mammuts_role_priority"
           value="<?php echo esc_attr( implode( ',', wp_list_pluck( $ordered, 'slug' ) ) ); ?>">
    <p class="description" style="font-size:11px;color:#888;">
        <?php esc_html_e( 'Per Drag & Drop die Reihenfolge der Rollen festlegen. Die erste Rolle wird hervorgehoben angezeigt.', 'mammuts' ); ?>
    </p>
    <?php
}

function mammuts_save_role_priority_meta( $post_id ) {
    if ( ! isset( $_POST['mammuts_role_priority_nonce_field'] ) ||
         ! wp_verify_nonce( $_POST['mammuts_role_priority_nonce_field'], 'mammuts_role_priority_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['mammuts_role_priority'] ) ) {
        $value = sanitize_text_field( $_POST['mammuts_role_priority'] );
        update_post_meta( $post_id, '_mammuts_role_priority', $value );
    }
}
add_action( 'save_post_sp_staff', 'mammuts_save_role_priority_meta' );


/* ============================================
 * SportsPress: Remove ALL auto-generated content on event pages
 * Our single-sp_event.php handles everything itself:
 * - Match header (teams, score, date)
 * - Quarter scores (from $event->results())
 * - Performance tables (from $event->performance())
 * - Venue + Map (from venue taxonomy meta)
 * ============================================ */
function mammuts_sp_remove_all_event_content() {
    if ( is_singular( 'sp_event' ) ) {
        // Remove all SportsPress event content hooks
        remove_all_actions( 'sportspress_single_event_content' );

        // Also remove the content filter that SportsPress uses
        // to inject its blocks into the_content()
        if ( class_exists( 'SP_Event' ) ) {
            global $wp_filter;
            // Remove SportsPress content filter if present
            if ( isset( $wp_filter['the_content'] ) ) {
                foreach ( $wp_filter['the_content']->callbacks as $priority => $hooks ) {
                    foreach ( $hooks as $key => $hook ) {
                        if ( is_array( $hook['function'] ) && is_object( $hook['function'][0] ) ) {
                            $class_name = get_class( $hook['function'][0] );
                            if ( strpos( $class_name, 'SP_' ) === 0 || strpos( $class_name, 'SportsPress' ) === 0 ) {
                                remove_filter( 'the_content', $hook['function'], $priority );
                            }
                        }
                    }
                }
            }
        }
    }
}
add_action( 'wp', 'mammuts_sp_remove_all_event_content', 99 );

/**
 * SportsPress: Private Events NUR in der Ligatabelle einschließen.
 *
 * SP_League_Table feuert den Hook 'sportspress_table_data_event_args'
 * direkt vor get_posts() - das ist der einzige Ort wo dieser Filter
 * vorkommt. Wir nutzen ihn um post_status um 'private' zu erweitern.
 * Spielplan, Upcoming und Countdown durchlaufen diesen Hook nie,
 * deshalb bleiben dort nur 'publish'/'future'-Events sichtbar.
 */
add_filter( 'sportspress_table_data_event_args', function( $args ) {
    // Alle Post-Stati einschließen: publish, private, future
    $args['post_status'] = array( 'publish', 'private', 'future' );

    // Kein Limit – alle Spiele der Saison zählen
    $args['posts_per_page'] = -1;
    $args['numberposts']    = -1;

    return $args;
} );

/**
 * Helper: Render raw post content without SportsPress filters.
 * Used in single-sp_event.php for the recap section.
 */
function mammuts_render_recap_content( $content ) {
    // Apply only basic WordPress content filters, not SportsPress
    $content = wptexturize( $content );
    $content = convert_smilies( $content );
    $content = wpautop( $content );
    $content = shortcode_unautop( $content );
    $content = do_shortcode( $content );
    return $content;
}

/**
 * SportsPress: Alle konfigurierten Spalten in der Ligatabelle anzeigen.
 *
 * SportsPress blendet Spalten aus wenn:
 *   (a) alle Werte 0 sind  → sportspress_table_show_columns_if_empty: false
 *   (b) PCT = 0/0 (NaN)    → wird als leer behandelt und weggelassen
 *
 * Beide Filter werden hier überschrieben damit PCT, PF, PA immer erscheinen.
 */

// (1) Leere Spalten (alle Werte = 0) trotzdem anzeigen
add_filter( 'sportspress_table_show_columns_if_empty', '__return_true' );

// (2) Einzelne Spalten nicht ausblenden – alle anzeigen
add_filter( 'sportspress_table_show_column', '__return_true', 10, 3 );

// (3) PCT: NaN/Division-by-Zero auf 0.000 setzen statt Spalte zu löschen
add_filter( 'sportspress_table_data', function( $data ) {
    if ( ! is_array( $data ) ) {
        return $data;
    }
    foreach ( $data as $team_id => $columns ) {
        if ( ! is_array( $columns ) ) {
            continue;
        }
        foreach ( $columns as $col_key => $value ) {
            // NaN, INF oder null → 0
            if ( is_float( $value ) && ( is_nan( $value ) || is_infinite( $value ) ) ) {
                $data[ $team_id ][ $col_key ] = 0;
            }
            if ( is_null( $value ) ) {
                $data[ $team_id ][ $col_key ] = 0;
            }
        }
    }
    return $data;
}, 10, 1 );


/* ============================================
 * Links Custom Post Type
 *
 * Collects useful external links (federation,
 * league, friendly teams, rules, etc.) and
 * displays them on the Links page template.
 * ============================================ */
function mammuts_register_links() {
    register_post_type( 'mammuts_link', array(
        'labels' => array(
            'name'               => __( 'Links', 'mammuts' ),
            'singular_name'      => __( 'Link', 'mammuts' ),
            'add_new'            => __( 'Link hinzufügen', 'mammuts' ),
            'add_new_item'       => __( 'Neuen Link hinzufügen', 'mammuts' ),
            'edit_item'          => __( 'Link bearbeiten', 'mammuts' ),
            'all_items'          => __( 'Alle Links', 'mammuts' ),
            'menu_name'          => __( 'Links', 'mammuts' ),
            'not_found'          => __( 'Keine Links gefunden.', 'mammuts' ),
            'not_found_in_trash' => __( 'Keine Links im Papierkorb.', 'mammuts' ),
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-admin-links',
        'menu_position' => 27,
        'supports'      => array( 'title', 'thumbnail', 'excerpt', 'page-attributes' ),
        'has_archive'   => false,
    ) );

    // Link Category taxonomy
    register_taxonomy( 'mammuts_link_category', 'mammuts_link', array(
        'labels' => array(
            'name'          => __( 'Link-Kategorien', 'mammuts' ),
            'singular_name' => __( 'Link-Kategorie', 'mammuts' ),
            'add_new_item'  => __( 'Neue Kategorie hinzufügen', 'mammuts' ),
            'edit_item'     => __( 'Kategorie bearbeiten', 'mammuts' ),
            'search_items'  => __( 'Kategorien suchen', 'mammuts' ),
            'all_items'     => __( 'Alle Kategorien', 'mammuts' ),
        ),
        'public'       => false,
        'show_ui'      => true,
        'hierarchical' => true,
        'show_admin_column' => true,
    ) );
}
add_action( 'init', 'mammuts_register_links' );

/**
 * Link Meta Box: URL & Options
 */
function mammuts_link_meta_boxes() {
    add_meta_box(
        'mammuts_link_settings',
        __( 'Link — Einstellungen', 'mammuts' ),
        'mammuts_link_settings_callback',
        'mammuts_link',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mammuts_link_meta_boxes' );

function mammuts_link_settings_callback( $post ) {
    wp_nonce_field( 'mammuts_link_nonce', 'mammuts_link_nonce_field' );
    $url     = get_post_meta( $post->ID, '_mammuts_link_url', true );
    $new_tab = get_post_meta( $post->ID, '_mammuts_link_new_tab', true );
    ?>
    <style>
        .mammuts-link-field { margin-bottom: 16px; }
        .mammuts-link-field label { display: block; font-weight: 600; margin-bottom: 4px; }
        .mammuts-link-field input[type="url"] { width: 100%; max-width: 500px; }
    </style>
    <div class="mammuts-link-field">
        <label for="mammuts_link_url"><strong><?php esc_html_e( 'Ziel-URL:', 'mammuts' ); ?></strong></label>
        <input type="url" id="mammuts_link_url" name="mammuts_link_url"
               value="<?php echo esc_url( $url ); ?>"
               style="width:100%;margin-top:6px;" placeholder="https://www.example.de">
        <p class="description"><?php esc_html_e( 'Die URL, auf die der Link verweisen soll.', 'mammuts' ); ?></p>
    </div>
    <div class="mammuts-link-field">
        <label>
            <input type="checkbox" name="mammuts_link_new_tab" value="1" <?php checked( $new_tab, '1' ); ?>>
            <?php esc_html_e( 'In neuem Tab öffnen', 'mammuts' ); ?>
        </label>
    </div>
    <?php
}

function mammuts_save_link_meta( $post_id ) {
    if ( ! isset( $_POST['mammuts_link_nonce_field'] ) ||
         ! wp_verify_nonce( $_POST['mammuts_link_nonce_field'], 'mammuts_link_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['mammuts_link_url'] ) ) {
        update_post_meta( $post_id, '_mammuts_link_url', esc_url_raw( $_POST['mammuts_link_url'] ) );
    }
    update_post_meta( $post_id, '_mammuts_link_new_tab', isset( $_POST['mammuts_link_new_tab'] ) ? '1' : '' );
}
add_action( 'save_post', 'mammuts_save_link_meta' );


/* ============================================
 * Downloads Custom Post Type
 *
 * Manages downloadable files (membership forms,
 * bylaws, training schedules, flyers, etc.)
 * with automatic file-type detection and size
 * display on the Downloads page template.
 * ============================================ */
function mammuts_register_downloads() {
    register_post_type( 'mammuts_download', array(
        'labels' => array(
            'name'               => __( 'Downloads', 'mammuts' ),
            'singular_name'      => __( 'Download', 'mammuts' ),
            'add_new'            => __( 'Download hinzufügen', 'mammuts' ),
            'add_new_item'       => __( 'Neuen Download hinzufügen', 'mammuts' ),
            'edit_item'          => __( 'Download bearbeiten', 'mammuts' ),
            'all_items'          => __( 'Alle Downloads', 'mammuts' ),
            'menu_name'          => __( 'Downloads', 'mammuts' ),
            'not_found'          => __( 'Keine Downloads gefunden.', 'mammuts' ),
            'not_found_in_trash' => __( 'Keine Downloads im Papierkorb.', 'mammuts' ),
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-download',
        'menu_position' => 28,
        'supports'      => array( 'title', 'excerpt', 'page-attributes' ),
        'has_archive'   => false,
    ) );

    // Download Category taxonomy
    register_taxonomy( 'mammuts_download_category', 'mammuts_download', array(
        'labels' => array(
            'name'          => __( 'Download-Kategorien', 'mammuts' ),
            'singular_name' => __( 'Download-Kategorie', 'mammuts' ),
            'add_new_item'  => __( 'Neue Kategorie hinzufügen', 'mammuts' ),
            'edit_item'     => __( 'Kategorie bearbeiten', 'mammuts' ),
            'search_items'  => __( 'Kategorien suchen', 'mammuts' ),
            'all_items'     => __( 'Alle Kategorien', 'mammuts' ),
        ),
        'public'       => false,
        'show_ui'      => true,
        'hierarchical' => true,
        'show_admin_column' => true,
    ) );
}
add_action( 'init', 'mammuts_register_downloads' );

/**
 * Download Meta Box: File Upload
 *
 * Uses the WordPress Media Library uploader so editors
 * can pick or upload a file without any technical knowledge.
 */
function mammuts_download_meta_boxes() {
    add_meta_box(
        'mammuts_download_settings',
        __( 'Download — Datei', 'mammuts' ),
        'mammuts_download_settings_callback',
        'mammuts_download',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mammuts_download_meta_boxes' );

function mammuts_download_settings_callback( $post ) {
    wp_nonce_field( 'mammuts_download_nonce', 'mammuts_download_nonce_field' );
    $file_id = get_post_meta( $post->ID, '_mammuts_download_file_id', true );
    $file_url = $file_id ? wp_get_attachment_url( $file_id ) : '';
    $file_name = $file_id ? basename( get_attached_file( $file_id ) ) : '';

    // Enqueue media uploader
    wp_enqueue_media();
    ?>
    <style>
        .mammuts-download-field { margin-bottom: 16px; }
        .mammuts-download-field label { display: block; font-weight: 600; margin-bottom: 4px; }
        .mammuts-download-preview { display: flex; align-items: center; gap: 12px; padding: 12px; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 6px; margin-top: 8px; }
        .mammuts-download-preview .filename { font-weight: 500; word-break: break-all; }
        .mammuts-download-preview .filesize { color: #888; font-size: 0.9em; }
        .mammuts-download-remove { color: #a00; cursor: pointer; text-decoration: underline; font-size: 0.9em; }
        .mammuts-download-hidden { display: none; }
    </style>

    <div class="mammuts-download-field">
        <label><?php esc_html_e( 'Datei:', 'mammuts' ); ?></label>
        <div id="mammuts-download-preview" class="mammuts-download-preview <?php echo empty( $file_id ) ? 'mammuts-download-hidden' : ''; ?>">
            <span class="filename" id="mammuts-download-filename"><?php echo esc_html( $file_name ); ?></span>
            <a href="#" class="mammuts-download-remove" id="mammuts-download-remove"><?php esc_html_e( 'Entfernen', 'mammuts' ); ?></a>
        </div>
        <input type="hidden" id="mammuts_download_file_id" name="mammuts_download_file_id" value="<?php echo esc_attr( $file_id ); ?>">
        <p>
            <button type="button" class="button button-secondary" id="mammuts-download-upload">
                <?php echo $file_id ? esc_html__( 'Datei ändern', 'mammuts' ) : esc_html__( 'Datei hochladen', 'mammuts' ); ?>
            </button>
        </p>
        <p class="description"><?php esc_html_e( 'Datei aus der Mediathek auswählen oder neu hochladen. Unterstützt PDF, Word, Excel, Bilder, ZIP und mehr.', 'mammuts' ); ?></p>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var frame;

        $('#mammuts-download-upload').on('click', function(e) {
            e.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: '<?php echo esc_js( __( 'Datei für Download auswählen', 'mammuts' ) ); ?>',
                button: { text: '<?php echo esc_js( __( 'Datei auswählen', 'mammuts' ) ); ?>' },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#mammuts_download_file_id').val(attachment.id);
                $('#mammuts-download-filename').text(attachment.filename);
                $('#mammuts-download-preview').removeClass('mammuts-download-hidden');
                $('#mammuts-download-upload').text('<?php echo esc_js( __( 'Datei ändern', 'mammuts' ) ); ?>');
            });

            frame.open();
        });

        $('#mammuts-download-remove').on('click', function(e) {
            e.preventDefault();
            $('#mammuts_download_file_id').val('');
            $('#mammuts-download-filename').text('');
            $('#mammuts-download-preview').addClass('mammuts-download-hidden');
            $('#mammuts-download-upload').text('<?php echo esc_js( __( 'Datei hochladen', 'mammuts' ) ); ?>');
        });
    });
    </script>
    <?php
}

function mammuts_save_download_meta( $post_id ) {
    if ( ! isset( $_POST['mammuts_download_nonce_field'] ) ||
         ! wp_verify_nonce( $_POST['mammuts_download_nonce_field'], 'mammuts_download_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['mammuts_download_file_id'] ) ) {
        update_post_meta( $post_id, '_mammuts_download_file_id', absint( $_POST['mammuts_download_file_id'] ) );
    }
}
add_action( 'save_post', 'mammuts_save_download_meta' );


/* ============================================
 * Admin Columns: Links — show URL in list view
 * ============================================ */
function mammuts_link_admin_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'title' ) {
            $new['mammuts_link_url'] = __( 'URL', 'mammuts' );
        }
    }
    return $new;
}
add_filter( 'manage_mammuts_link_posts_columns', 'mammuts_link_admin_columns' );

function mammuts_link_admin_column_data( $column, $post_id ) {
    if ( $column === 'mammuts_link_url' ) {
        $url = get_post_meta( $post_id, '_mammuts_link_url', true );
        if ( $url ) {
            echo '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( wp_parse_url( $url, PHP_URL_HOST ) ) . '</a>';
        } else {
            echo '—';
        }
    }
}
add_action( 'manage_mammuts_link_posts_custom_column', 'mammuts_link_admin_column_data', 10, 2 );


/* ============================================
 * Admin Columns: Downloads — show file info in list view
 * ============================================ */
function mammuts_download_admin_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'title' ) {
            $new['mammuts_download_file'] = __( 'Datei', 'mammuts' );
            $new['mammuts_download_size'] = __( 'Größe', 'mammuts' );
        }
    }
    return $new;
}
add_filter( 'manage_mammuts_download_posts_columns', 'mammuts_download_admin_columns' );

function mammuts_download_admin_column_data( $column, $post_id ) {
    $file_id = get_post_meta( $post_id, '_mammuts_download_file_id', true );

    if ( $column === 'mammuts_download_file' ) {
        if ( $file_id ) {
            $path = get_attached_file( $file_id );
            echo esc_html( basename( $path ) );
        } else {
            echo '<em style="color:#999;">' . esc_html__( 'Keine Datei', 'mammuts' ) . '</em>';
        }
    }

    if ( $column === 'mammuts_download_size' ) {
        if ( $file_id ) {
            $path = get_attached_file( $file_id );
            if ( $path && file_exists( $path ) ) {
                echo esc_html( size_format( filesize( $path ), 1 ) );
            } else {
                echo '—';
            }
        } else {
            echo '—';
        }
    }
}
add_action( 'manage_mammuts_download_posts_custom_column', 'mammuts_download_admin_column_data', 10, 2 );


/* ============================================
 * Download Category Filter Metabox for Pages
 *
 * Adds a sidebar dropdown on page edit screens so
 * editors can choose which download category this
 * page should display — exactly like the SportsPress
 * team filter works for Roster/Schedule pages.
 *
 * When "— Alle Kategorien —" is selected the page
 * shows all downloads grouped by category. When a
 * specific category is chosen only that category's
 * downloads are displayed (without category heading).
 * ============================================ */
function mammuts_download_filter_metabox() {
    add_meta_box(
        'mammuts_download_filter',
        __( 'Downloads — Kategorie-Filter', 'mammuts' ),
        'mammuts_download_filter_callback',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'mammuts_download_filter_metabox' );

function mammuts_download_filter_callback( $post ) {
    wp_nonce_field( 'mammuts_download_filter_nonce', 'mammuts_download_filter_nonce_field' );

    $selected = get_post_meta( $post->ID, '_mammuts_download_category', true );

    // Get all download categories
    $categories = get_terms( array(
        'taxonomy'   => 'mammuts_download_category',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ) );

    ?>
    <p>
        <label for="mammuts_download_category"><strong><?php esc_html_e( 'Download-Kategorie:', 'mammuts' ); ?></strong></label><br>
        <select id="mammuts_download_category" name="mammuts_download_category" style="width:100%;margin-top:4px;">
            <option value=""><?php esc_html_e( '— Alle Kategorien —', 'mammuts' ); ?></option>
            <?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : foreach ( $categories as $cat ) : ?>
                <option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $selected, $cat->term_id ); ?>>
                    <?php echo esc_html( $cat->name ); ?>
                    <?php if ( ! empty( $cat->description ) ) : ?>
                        — <?php echo esc_html( $cat->description ); ?>
                    <?php endif; ?>
                </option>
            <?php endforeach; endif; ?>
        </select>
    </p>
    <p class="description" style="font-size:11px;color:#888;">
        <?php esc_html_e( 'Nur für Seiten mit dem Template „Downloads". Wähle eine Kategorie, um nur deren Downloads auf dieser Seite anzuzeigen. „Alle Kategorien" zeigt alle Downloads gruppiert.', 'mammuts' ); ?>
    </p>
    <?php
}

function mammuts_save_download_filter_meta( $post_id ) {
    if ( ! isset( $_POST['mammuts_download_filter_nonce_field'] ) ||
         ! wp_verify_nonce( $_POST['mammuts_download_filter_nonce_field'], 'mammuts_download_filter_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['mammuts_download_category'] ) ) {
        update_post_meta( $post_id, '_mammuts_download_category', sanitize_text_field( $_POST['mammuts_download_category'] ) );
    }
}
add_action( 'save_post', 'mammuts_save_download_filter_meta' );


/* ============================================
 * Sortable support for Links & Downloads
 *
 * Re-uses the existing admin-sortable.js by
 * extending the enqueue conditions.
 * ============================================ */
function mammuts_extend_sortable_screens( $hook ) {
    if ( ! in_array( $hook, array( 'edit.php' ), true ) ) {
        return;
    }
    $screen = get_current_screen();
    if ( ! $screen ) return;

    if ( in_array( $screen->post_type, array( 'mammuts_link', 'mammuts_download' ), true ) ) {
        wp_enqueue_script( 'jquery-ui-sortable' );
    }
}
add_action( 'admin_enqueue_scripts', 'mammuts_extend_sortable_screens' );

/* ============================================
 * CUSTOM POST TYPE: Vereinsveranstaltungen
 * ============================================ */

/**
 * Register the mammuts_club_event CPT
 */
function mammuts_register_club_event_cpt() {
    register_post_type( 'mammuts_club_event', array(
        'labels' => array(
            'name'               => __( 'Veranstaltungen', 'mammuts' ),
            'singular_name'      => __( 'Veranstaltung', 'mammuts' ),
            'add_new'            => __( 'Neue Veranstaltung', 'mammuts' ),
            'add_new_item'       => __( 'Neue Veranstaltung anlegen', 'mammuts' ),
            'edit_item'          => __( 'Veranstaltung bearbeiten', 'mammuts' ),
            'view_item'          => __( 'Veranstaltung ansehen', 'mammuts' ),
            'all_items'          => __( 'Alle Veranstaltungen', 'mammuts' ),
            'search_items'       => __( 'Veranstaltungen suchen', 'mammuts' ),
            'not_found'          => __( 'Keine Veranstaltungen gefunden', 'mammuts' ),
            'menu_name'          => __( 'Veranstaltungen', 'mammuts' ),
        ),
        'public'             => true,
        'has_archive'        => 'veranstaltungen',
        'rewrite'            => array( 'slug' => 'veranstaltungen' ),
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => false,
        'menu_position'      => 26,
    ) );
}
add_action( 'init', 'mammuts_register_club_event_cpt' );

/**
 * Meta box for club event fields (date/time, location)
 */
function mammuts_club_event_metaboxes() {
    add_meta_box(
        'mammuts_club_event_details',
        __( 'Veranstaltungsdetails', 'mammuts' ),
        'mammuts_club_event_metabox_cb',
        'mammuts_club_event',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mammuts_club_event_metaboxes' );

function mammuts_club_event_metabox_cb( $post ) {
    wp_nonce_field( 'mammuts_club_event_nonce', '_mammuts_event_nonce' );

    $date     = get_post_meta( $post->ID, '_mammuts_event_date', true );
    $time     = get_post_meta( $post->ID, '_mammuts_event_time', true );
    $location = get_post_meta( $post->ID, '_mammuts_event_location', true );
    $end_date = get_post_meta( $post->ID, '_mammuts_event_end_date', true );
    $end_time = get_post_meta( $post->ID, '_mammuts_event_end_time', true );
    ?>
    <p>
        <label for="mammuts_event_date"><strong><?php esc_html_e( 'Datum', 'mammuts' ); ?> *</strong></label><br>
        <input type="date" id="mammuts_event_date" name="_mammuts_event_date"
               value="<?php echo esc_attr( $date ); ?>" style="width:100%;" required>
    </p>
    <p>
        <label for="mammuts_event_time"><strong><?php esc_html_e( 'Uhrzeit', 'mammuts' ); ?></strong></label><br>
        <input type="time" id="mammuts_event_time" name="_mammuts_event_time"
               value="<?php echo esc_attr( $time ); ?>" style="width:100%;">
    </p>
    <p>
        <label for="mammuts_event_end_date"><strong><?php esc_html_e( 'Enddatum', 'mammuts' ); ?></strong></label><br>
        <input type="date" id="mammuts_event_end_date" name="_mammuts_event_end_date"
               value="<?php echo esc_attr( $end_date ); ?>" style="width:100%;">
    </p>
    <p>
        <label for="mammuts_event_end_time"><strong><?php esc_html_e( 'Endzeit', 'mammuts' ); ?></strong></label><br>
        <input type="time" id="mammuts_event_end_time" name="_mammuts_event_end_time"
               value="<?php echo esc_attr( $end_time ); ?>" style="width:100%;">
    </p>
    <p>
        <label for="mammuts_event_location"><strong><?php esc_html_e( 'Ort', 'mammuts' ); ?></strong></label><br>
        <input type="text" id="mammuts_event_location" name="_mammuts_event_location"
               value="<?php echo esc_attr( $location ); ?>" style="width:100%;"
               placeholder="<?php esc_attr_e( 'z.B. Espan Sportplatz, Kuchen', 'mammuts' ); ?>">
    </p>
    <?php
}

function mammuts_club_event_save( $post_id ) {
    if ( ! isset( $_POST['_mammuts_event_nonce'] ) ||
         ! wp_verify_nonce( $_POST['_mammuts_event_nonce'], 'mammuts_club_event_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $fields = array(
        '_mammuts_event_date',
        '_mammuts_event_time',
        '_mammuts_event_end_date',
        '_mammuts_event_end_time',
        '_mammuts_event_location',
    );

    foreach ( $fields as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
        }
    }
}
add_action( 'save_post_mammuts_club_event', 'mammuts_club_event_save' );

/**
 * Get upcoming club events (sorted by event date)
 *
 * @param int $limit Max events to return.
 * @return array Array of post objects.
 */
function mammuts_get_upcoming_club_events( $limit = 5 ) {
    $today = current_time( 'Y-m-d' );

    return get_posts( array(
        'post_type'      => 'mammuts_club_event',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'meta_key'       => '_mammuts_event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => '_mammuts_event_date',
                'value'   => $today,
                'compare' => '>=',
                'type'    => 'DATE',
            ),
        ),
    ) );
}

/**
 * Get the next single upcoming club event
 *
 * @return WP_Post|null
 */
function mammuts_get_next_club_event() {
    $events = mammuts_get_upcoming_club_events( 1 );
    return ! empty( $events ) ? $events[0] : null;
}

/**
 * Build a datetime string from event meta
 *
 * @param int $post_id
 * @return string ISO datetime string
 */
function mammuts_club_event_datetime( $post_id ) {
    $date = get_post_meta( $post_id, '_mammuts_event_date', true );
    $time = get_post_meta( $post_id, '_mammuts_event_time', true );
    if ( empty( $date ) ) return '';
    return $date . 'T' . ( ! empty( $time ) ? $time : '00:00' ) . ':00';
}
