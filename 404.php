<?php
/**
 * 404 Template
 *
 * @package Mammuts
 */

get_header();
?>

<div style="margin-top:var(--header-height);min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:40px 20px;">
    <div>
        <p style="font-family:var(--font-display);font-size:8rem;font-weight:900;color:var(--color-accent);line-height:1;margin-bottom:16px;">404</p>
        <h1 style="font-size:2rem;margin-bottom:12px;"><?php esc_html_e( 'Page Not Found', 'mammuts' ); ?></h1>
        <p style="color:var(--color-text-muted);margin-bottom:32px;"><?php esc_html_e( 'The page you are looking for does not exist.', 'mammuts' ); ?></p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
            <?php esc_html_e( 'Back to Home', 'mammuts' ); ?>
        </a>
    </div>
</div>

<?php get_footer(); ?>
