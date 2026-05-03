<?php
/**
 * Template Name: Links
 * Description: Displays useful links as categorized cards with icons, descriptions and external URLs.
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="section links-page">
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

        // Get all link categories (taxonomy terms)
        $categories = get_terms( array(
            'taxonomy'   => 'mammuts_link_category',
            'hide_empty' => true,
            'orderby'    => 'term_order',
            'order'      => 'ASC',
        ) );

        // Fallback: if no categories or error, get all links ungrouped
        if ( is_wp_error( $categories ) || empty( $categories ) ) {
            $categories = array( null ); // null = ungrouped
        }

        $has_any_links = false;

        foreach ( $categories as $category ) :
            $query_args = array(
                'post_type'      => 'mammuts_link',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            );

            if ( $category !== null ) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'mammuts_link_category',
                        'field'    => 'term_id',
                        'terms'    => $category->term_id,
                    ),
                );
            }

            $links = get_posts( $query_args );

            if ( empty( $links ) ) {
                continue;
            }

            $has_any_links = true;
            ?>

            <?php if ( $category !== null ) : ?>
                <div class="links-category-group">
                    <h2 class="links-category-title">
                        <span class="links-category-title-text"><?php echo esc_html( $category->name ); ?></span>
                        <?php if ( ! empty( $category->description ) ) : ?>
                            <span class="links-category-desc"><?php echo esc_html( $category->description ); ?></span>
                        <?php endif; ?>
                    </h2>
                </div>
            <?php endif; ?>

            <div class="link-cards-grid">
                <?php foreach ( $links as $link ) :
                    $link_url   = get_post_meta( $link->ID, '_mammuts_link_url', true );
                    $new_tab    = get_post_meta( $link->ID, '_mammuts_link_new_tab', true );
                    $has_thumb  = has_post_thumbnail( $link->ID );
                    $excerpt    = get_the_excerpt( $link->ID );
                    $target     = $new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
                ?>
                <div class="link-card">
                    <div class="link-card-icon">
                        <?php if ( $has_thumb ) : ?>
                            <?php echo get_the_post_thumbnail( $link->ID, 'thumbnail', array( 'class' => 'link-card-img' ) ); ?>
                        <?php else : ?>
                            <div class="link-card-placeholder">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="link-card-body">
                        <h3 class="link-card-name"><?php echo esc_html( get_the_title( $link->ID ) ); ?></h3>
                        <?php if ( $excerpt ) : ?>
                            <p class="link-card-desc"><?php echo esc_html( $excerpt ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $link_url ) ) : ?>
                            <a href="<?php echo esc_url( $link_url ); ?>"<?php echo $target; ?> class="link-card-action">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                    <polyline points="15 3 21 3 21 9"/>
                                    <line x1="10" y1="14" x2="21" y2="3"/>
                                </svg>
                                <?php esc_html_e( 'Öffnen', 'mammuts' ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        <?php endforeach;

        if ( ! $has_any_links ) : ?>
            <div style="text-align:center;color:var(--color-text-muted);padding:60px 0;">
                <p><?php esc_html_e( 'Noch keine Links vorhanden. Links können unter „Links" im Admin-Menü hinzugefügt werden.', 'mammuts' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
