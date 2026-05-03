<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ===== STICKY NAV BAR (always on top) ===== -->
<nav class="header-nav-bar" id="header-nav-bar">
    <div class="header-inner">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" rel="home">
            <?php
            if ( has_custom_logo() ) {
                $logo_id  = get_theme_mod( 'custom_logo' );
                $logo_img = wp_get_attachment_image_src( $logo_id, 'full' );
                if ( $logo_img ) {
                    echo '<img src="' . esc_url( $logo_img[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
                }
            }
            ?>
            <span class="logo-text"><?php echo esc_html( get_theme_mod( 'mammuts_club_name', get_bloginfo( 'name' ) ) ); ?></span>
        </a>

        <div class="main-nav" id="main-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'mammuts' ); ?>">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'walker'         => new Mammuts_Nav_Walker(),
                    'depth'          => 3,
                    'fallback_cb'    => false,
                ) );
            } else {
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'mammuts' ); ?></a>
                <a href="#roster"><?php esc_html_e( 'Team', 'mammuts' ); ?></a>
                <a href="#coaches"><?php esc_html_e( 'Coaches', 'mammuts' ); ?></a>
                <a href="#match-center"><?php esc_html_e( 'Schedule', 'mammuts' ); ?></a>
                <a href="#news"><?php esc_html_e( 'News', 'mammuts' ); ?></a>
                <?php
            }
            ?>
        </div>

        <div class="header-actions">
            <?php
            $cta_text = get_theme_mod( 'mammuts_hero_cta_text', __( 'Membership', 'mammuts' ) );
            $cta_url  = get_theme_mod( 'mammuts_hero_cta_url', '#' );
            if ( ! empty( trim( $cta_text ) ) ) : ?>
            <a href="<?php echo esc_url( $cta_url ); ?>" class="btn btn--primary btn--sm header-membership-btn">
                <?php echo esc_html( $cta_text ); ?>
            </a>
            <?php endif; ?>
            <button class="menu-toggle" id="menu-toggle" aria-label="<?php esc_attr_e( 'Toggle Menu', 'mammuts' ); ?>">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>
<div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>

<?php if ( mammuts_show_banner() ) : ?>
<!-- ===== HEADER BANNER (below sticky nav) ===== -->
<header class="header-banner<?php echo esc_attr( mammuts_banner_mobile_class( 'hero' ) ); ?>">
    <div class="header-banner-image">
        <?php
        $hero_bg        = get_theme_mod( 'mammuts_hero_bg', '' );
        $hero_bg_mobile = get_theme_mod( 'mammuts_hero_bg_mobile', '' );
        $main_src       = ! empty( $hero_bg ) ? $hero_bg : $hero_bg_mobile;

        if ( ! empty( $main_src ) ) :
            if ( ! empty( $hero_bg_mobile ) ) : ?>
                <img class="hero-img-mobile" src="<?php echo esc_url( $hero_bg_mobile ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="eager">
            <?php endif;
            if ( ! empty( $hero_bg ) ) : ?>
                <img class="hero-img-desktop" src="<?php echo esc_url( $hero_bg ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="eager">
            <?php endif;
        else : ?>
            <div class="header-banner-image-fallback"></div>
        <?php endif; ?>
    </div>
    <div class="header-banner-fade"></div>
    <div class="header-banner-content">

    </div>
</header>
<?php if ( get_theme_mod( 'mammuts_hero_hide_on_mobile', false ) ) : ?>
<div class="mobile-page-title-bar">
    <p class="mobile-page-title"><?php echo esc_html( is_front_page() ? get_bloginfo( 'name' ) : get_the_title() ); ?></p>
</div>
<?php endif; ?>
<?php endif; ?>

<main id="main-content" class="site-main">
