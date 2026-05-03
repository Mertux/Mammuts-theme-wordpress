<?php
/**
 * Template Name: Sponsors
 * Description: Displays sponsors as modern cards with logo, name, description and link.
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="section sponsors-page">
    <div class="container">
        <?php
        // Show page content first (if any)
        while ( have_posts() ) :
            the_post();
            $content = get_the_content();
            if ( ! empty( trim( $content ) ) ) : ?>
                <div class="entry-content" style="max-width:800px;margin:0 auto 48px;">
                    <?php the_content(); ?>
                </div>
            <?php endif;
        endwhile;

        // Get sponsors from custom post type
        $sponsors = get_posts( array(
            'post_type'      => 'mammuts_sponsor',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ) );

        if ( ! empty( $sponsors ) ) : ?>
            <div class="sponsor-cards-grid">
                <?php foreach ( $sponsors as $sponsor ) :
                    $sponsor_url = get_post_meta( $sponsor->ID, '_mammuts_sponsor_url', true );
                    $facebook    = get_post_meta( $sponsor->ID, '_mammuts_sponsor_facebook', true );
                    $instagram   = get_post_meta( $sponsor->ID, '_mammuts_sponsor_instagram', true );
                    $has_logo    = has_post_thumbnail( $sponsor->ID );
                    $excerpt     = get_the_excerpt( $sponsor->ID );
                ?>
                <div class="sponsor-card">
                    <div class="sponsor-card-logo">
                        <?php if ( $has_logo ) : ?>
                            <?php echo get_the_post_thumbnail( $sponsor->ID, 'medium', array( 'class' => 'sponsor-card-img' ) ); ?>
                        <?php else : ?>
                            <div class="sponsor-card-placeholder">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="sponsor-card-body">
                        <h3 class="sponsor-card-name"><?php echo esc_html( get_the_title( $sponsor->ID ) ); ?></h3>
                        <?php if ( $excerpt ) : ?>
                            <p class="sponsor-card-desc"><?php echo esc_html( $excerpt ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $sponsor_url ) || ! empty( $facebook ) || ! empty( $instagram ) ) : ?>
                            <div class="sponsor-card-socials">
                                <?php if ( ! empty( $sponsor_url ) ) : ?>
                                    <a href="<?php echo esc_url( $sponsor_url ); ?>" target="_blank" rel="noopener noreferrer" class="sponsor-social-link sponsor-social-website" title="Website">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    </a>
                                <?php endif; ?>
                                <?php if ( ! empty( $facebook ) ) : ?>
                                    <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer" class="sponsor-social-link sponsor-social-facebook" title="Facebook">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                <?php endif; ?>
                                <?php if ( ! empty( $instagram ) ) : ?>
                                    <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" class="sponsor-social-link sponsor-social-instagram" title="Instagram">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div style="text-align:center;color:var(--color-text-muted);padding:60px 0;">
                <p><?php esc_html_e( 'No sponsors yet. Add sponsors under "Sponsors" in the admin menu.', 'mammuts' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
