<?php
/**
 * Template Name: Downloads
 * Description: Displays downloadable files as categorized cards with file type icons, sizes and download buttons.
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="section downloads-page">
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

        // ── Category filter: read from page metabox ──
        // If a specific category was chosen in the sidebar filter,
        // show only that category's downloads (no heading needed).
        // If "Alle" or empty → show all, grouped by category.
        $filter_cat_id = get_post_meta( get_the_ID(), '_mammuts_download_category', true );
        $filter_cat_id = ! empty( $filter_cat_id ) ? intval( $filter_cat_id ) : 0;

        if ( $filter_cat_id ) {
            // Single-category mode: only show downloads from this category
            $filter_term = get_term( $filter_cat_id, 'mammuts_download_category' );
            // Use null to skip the category heading (page title already identifies the category)
            $categories = array( null );
            $forced_term_id = $filter_cat_id;
        } else {
            // All-categories mode: get all categories and display grouped
            $categories = get_terms( array(
                'taxonomy'   => 'mammuts_download_category',
                'hide_empty' => true,
                'orderby'    => 'term_order',
                'order'      => 'ASC',
            ) );

            // Fallback: if no categories exist, show all downloads ungrouped
            if ( is_wp_error( $categories ) || empty( $categories ) ) {
                $categories = array( null );
            }
            $forced_term_id = 0;
        }

        $has_any_downloads = false;

        foreach ( $categories as $category ) :
            $query_args = array(
                'post_type'      => 'mammuts_download',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
            );

            if ( $category !== null ) {
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'mammuts_download_category',
                        'field'    => 'term_id',
                        'terms'    => $category->term_id,
                    ),
                );
            } elseif ( $forced_term_id ) {
                // Single-category mode: filter by the chosen category
                $query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'mammuts_download_category',
                        'field'    => 'term_id',
                        'terms'    => $forced_term_id,
                    ),
                );
            }

            $downloads = get_posts( $query_args );

            if ( empty( $downloads ) ) {
                continue;
            }

            $has_any_downloads = true;
            ?>

            <?php if ( $category !== null ) : ?>
                <div class="downloads-category-group">
                    <h2 class="downloads-category-title">
                        <span class="downloads-category-title-text"><?php echo esc_html( $category->name ); ?></span>
                        <?php if ( ! empty( $category->description ) ) : ?>
                            <span class="downloads-category-desc"><?php echo esc_html( $category->description ); ?></span>
                        <?php endif; ?>
                    </h2>
                </div>
            <?php endif; ?>

            <div class="download-cards-grid">
                <?php foreach ( $downloads as $download ) :
                    $file_id    = get_post_meta( $download->ID, '_mammuts_download_file_id', true );
                    $file_url   = $file_id ? wp_get_attachment_url( $file_id ) : '';
                    $file_path  = $file_id ? get_attached_file( $file_id ) : '';
                    $file_size  = '';
                    $file_ext   = '';

                    if ( $file_path && file_exists( $file_path ) ) {
                        $file_size = size_format( filesize( $file_path ), 1 );
                        $file_ext  = strtoupper( pathinfo( $file_path, PATHINFO_EXTENSION ) );
                    } elseif ( $file_url ) {
                        $file_ext = strtoupper( pathinfo( $file_url, PATHINFO_EXTENSION ) );
                    }

                    $excerpt = get_the_excerpt( $download->ID );

                    // Determine icon based on file type
                    $icon_class = 'file-generic';
                    $ext_lower  = strtolower( $file_ext );
                    if ( in_array( $ext_lower, array( 'pdf' ), true ) ) {
                        $icon_class = 'file-pdf';
                    } elseif ( in_array( $ext_lower, array( 'doc', 'docx' ), true ) ) {
                        $icon_class = 'file-doc';
                    } elseif ( in_array( $ext_lower, array( 'xls', 'xlsx' ), true ) ) {
                        $icon_class = 'file-xls';
                    } elseif ( in_array( $ext_lower, array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' ), true ) ) {
                        $icon_class = 'file-img';
                    } elseif ( in_array( $ext_lower, array( 'zip', 'rar', '7z', 'tar', 'gz' ), true ) ) {
                        $icon_class = 'file-archive';
                    }
                ?>
                <div class="download-card">
                    <div class="download-card-icon <?php echo esc_attr( $icon_class ); ?>">
                        <?php if ( $icon_class === 'file-pdf' ) : ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                        <?php elseif ( $icon_class === 'file-doc' ) : ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <line x1="10" y1="9" x2="8" y2="9"/>
                            </svg>
                        <?php elseif ( $icon_class === 'file-xls' ) : ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <rect x="8" y="12" width="8" height="6" rx="1"/>
                                <line x1="12" y1="12" x2="12" y2="18"/>
                                <line x1="8" y1="15" x2="16" y2="15"/>
                            </svg>
                        <?php elseif ( $icon_class === 'file-img' ) : ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        <?php elseif ( $icon_class === 'file-archive' ) : ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 8v13H3V3h12l6 5z"/>
                                <path d="M14 3v5h6"/>
                                <rect x="10" y="13" width="4" height="5" rx="0.5"/>
                                <line x1="12" y1="11" x2="12" y2="13"/>
                            </svg>
                        <?php else : ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        <?php endif; ?>
                        <?php if ( $file_ext ) : ?>
                            <span class="download-card-ext"><?php echo esc_html( $file_ext ); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="download-card-body">
                        <h3 class="download-card-name"><?php echo esc_html( get_the_title( $download->ID ) ); ?></h3>
                        <?php if ( $excerpt ) : ?>
                            <p class="download-card-desc"><?php echo esc_html( $excerpt ); ?></p>
                        <?php endif; ?>
                        <div class="download-card-meta">
                            <?php if ( $file_size ) : ?>
                                <span class="download-card-size"><?php echo esc_html( $file_size ); ?></span>
                            <?php endif; ?>
                            <?php if ( ! empty( $file_url ) ) : ?>
                                <a href="<?php echo esc_url( $file_url ); ?>" download class="download-card-action">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7 10 12 15 17 10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                    <?php esc_html_e( 'Herunterladen', 'mammuts' ); ?>
                                </a>
                            <?php else : ?>
                                <span class="download-card-nofile"><?php esc_html_e( 'Datei folgt', 'mammuts' ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        <?php endforeach;

        if ( ! $has_any_downloads ) : ?>
            <div style="text-align:center;color:var(--color-text-muted);padding:60px 0;">
                <p><?php esc_html_e( 'Noch keine Downloads vorhanden. Downloads können unter „Downloads" im Admin-Menü hinzugefügt werden.', 'mammuts' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
