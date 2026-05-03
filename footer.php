</main><!-- #main-content -->

<?php
// Sponsors Bar Widget Area - only show if it has widgets with content
if ( is_active_sidebar( 'sponsors-bar' ) ) :
    // Capture widget output to check if it's actually non-empty
    ob_start();
    dynamic_sidebar( 'sponsors-bar' );
    $sponsors_content = ob_get_clean();
    $sponsors_stripped = trim( strip_tags( $sponsors_content ) );

    if ( ! empty( $sponsors_stripped ) ) : ?>
<section class="sponsors">
    <div class="container">
        <?php echo $sponsors_content; ?>
    </div>
</section>
    <?php endif;
endif; ?>

<footer class="site-footer">
    <div class="footer-top">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo" rel="home">
            <?php
            if ( has_custom_logo() ) {
                $logo_id  = get_theme_mod( 'custom_logo' );
                $logo_img = wp_get_attachment_image_src( $logo_id, 'full' );
                if ( $logo_img ) {
                    echo '<img src="' . esc_url( $logo_img[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
                }
            }
            ?>
            <span class="footer-logo-text"><?php echo esc_html( get_theme_mod( 'mammuts_club_name', get_bloginfo( 'name' ) ) ); ?></span>
        </a>

        <?php mammuts_social_links(); ?>
    </div>

    <?php
    $email = get_theme_mod( 'mammuts_club_email' );
    if ( $email ) : ?>
    <div class="footer-contact-line">
        <span class="footer-contact-icon">✉️</span>
        <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
    </div>
    <?php endif; ?>

    <div class="footer-bottom">
        <p class="footer-copyright">
            &copy; <?php echo date( 'Y' ); ?>
            <?php echo esc_html( get_theme_mod( 'mammuts_club_name', get_bloginfo( 'name' ) ) ); ?>.
            <?php esc_html_e( 'All rights reserved.', 'mammuts' ); ?>
            <?php
            $founded = get_theme_mod( 'mammuts_club_founded' );
            if ( $founded ) {
                printf( ' | %s %s', esc_html__( 'Est.', 'mammuts' ), esc_html( $founded ) );
            }
            ?>
        </p>
        <div class="footer-bottom-links">
            <?php
            $privacy_page = get_privacy_policy_url();
            if ( $privacy_page ) : ?>
                <a href="<?php echo esc_url( $privacy_page ); ?>"><?php esc_html_e( 'Privacy Policy', 'mammuts' ); ?></a>
            <?php endif;

            $impressum = get_page_by_path( 'impressum' );
            if ( ! $impressum ) {
                $impressum = get_page_by_path( 'imprint' );
            }
            if ( $impressum ) : ?>
                <a href="<?php echo esc_url( get_permalink( $impressum->ID ) ); ?>"><?php esc_html_e( 'Impressum', 'mammuts' ); ?></a>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
