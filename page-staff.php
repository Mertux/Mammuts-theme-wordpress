<?php
/**
 * Template Name: Staff / Coaches
 * Description: Displays SportsPress staff members in a card grid.
 *              Add a SportsPress staff list shortcode in the page content,
 *              or leave empty to show all staff.
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="coaches section">
    <div class="container">
        <?php
        while ( have_posts() ) :
            the_post();

            // ── Always load custom staff order for this page ──
            $custom_order = get_post_meta( get_the_ID(), '_mammuts_staff_order', true );

            if ( mammuts_has_sportspress() ) :
                // Get filter values from page metabox
                $filter_team = get_post_meta( get_the_ID(), '_mammuts_sp_team', true );
                $filter_role = get_post_meta( get_the_ID(), '_mammuts_sp_role', true );

                $args = array(
                    'post_type'      => 'sp_staff',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                );

                // Filter by team (from metabox)
                if ( ! empty( $filter_team ) ) {
                    $args['meta_query'] = array(
                        array(
                            'key'     => 'sp_team',
                            'value'   => $filter_team,
                            'compare' => '=',
                        ),
                    );
                }

                // Filter by role/job (from metabox, fallback to slug)
                $role = null;
                if ( ! empty( $filter_role ) ) {
                    $role = get_term_by( 'slug', $filter_role, 'sp_role' );
                }
                if ( ! $role ) {
                    // Fallback: try matching page slug to a role
                    $slug = get_post_field( 'post_name', get_the_ID() );
                    $role = get_term_by( 'slug', $slug, 'sp_role' );
                }

                // Collect the filtered role + all its child roles
                $filtered_role_ids = array();
                if ( $role ) {
                    $filtered_role_ids[] = $role->term_id;
                    // Include child roles (e.g. "Vorstand" → "1. Vorstand", "2. Vorstand")
                    $child_roles = get_terms( array(
                        'taxonomy'   => 'sp_role',
                        'child_of'   => $role->term_id,
                        'hide_empty' => false,
                    ) );
                    if ( ! is_wp_error( $child_roles ) ) {
                        foreach ( $child_roles as $child ) {
                            $filtered_role_ids[] = $child->term_id;
                        }
                    }

                    $args['tax_query'] = array(
                        array(
                            'taxonomy'         => 'sp_role',
                            'field'            => 'term_id',
                            'terms'            => $filtered_role_ids,
                            'include_children' => true,
                        ),
                    );
                }

                $staff = get_posts( $args );

                // ── Re-sort by page-level custom order (if set) ──
                $custom_order = get_post_meta( get_the_ID(), '_mammuts_staff_order', true );
                if ( ! empty( $custom_order ) && ! empty( $staff ) ) {
                    $order_ids = array_map( 'intval', array_filter( explode( ',', $custom_order ) ) );
                    $staff_by_id = array();
                    foreach ( $staff as $m ) {
                        $staff_by_id[ $m->ID ] = $m;
                    }
                    $sorted = array();
                    foreach ( $order_ids as $oid ) {
                        if ( isset( $staff_by_id[ $oid ] ) ) {
                            $sorted[] = $staff_by_id[ $oid ];
                            unset( $staff_by_id[ $oid ] );
                        }
                    }
                    // Append any remaining (new staff not yet in saved order)
                    foreach ( $staff_by_id as $m ) {
                        $sorted[] = $m;
                    }
                    $staff = $sorted;
                }

                if ( ! empty( $staff ) ) : ?>
                    <div class="coaches-grid">
                        <?php foreach ( $staff as $member ) :
                            // Collect ALL roles this member has, sorted by
                            // SportsPress "Reihenfolge" (term_order).
                            $member_roles = wp_get_post_terms( $member->ID, 'sp_role' );
                            if ( is_wp_error( $member_roles ) ) {
                                $member_roles = array();
                            }

                            // Sort by term_order if we have multiple roles.
                            // SportsPress stores "Reihenfolge" in wp_terms.term_order.
                            if ( count( $member_roles ) > 1 ) {
                                global $wpdb;
                                $role_ids_list = wp_list_pluck( $member_roles, 'term_id' );
                                $ids_sql = implode( ',', array_map( 'intval', $role_ids_list ) );

                                $order_map = array();

                                // Read term_order directly from wp_terms
                                $wpdb->suppress_errors( true );
                                $rows = $wpdb->get_results(
                                    "SELECT term_id, term_order FROM {$wpdb->terms}
                                     WHERE term_id IN ({$ids_sql})"
                                );
                                $wpdb->suppress_errors( false );

                                if ( ! empty( $rows ) && ! $wpdb->last_error ) {
                                    foreach ( $rows as $row ) {
                                        $order_map[ (int) $row->term_id ] = (int) $row->term_order;
                                    }
                                }

                                // Fallback: try term meta
                                if ( empty( $order_map ) ) {
                                    foreach ( $member_roles as $mr ) {
                                        $sp_order = get_term_meta( $mr->term_id, 'sp_order', true );
                                        if ( $sp_order === '' ) {
                                            $sp_order = get_term_meta( $mr->term_id, 'order', true );
                                        }
                                        $order_map[ $mr->term_id ] = $sp_order !== '' ? (int) $sp_order : 9999;
                                    }
                                }

                                $all_zero = ! empty( $order_map ) && count( array_unique( $order_map ) ) === 1 && reset( $order_map ) === 0;

                                if ( ! empty( $order_map ) && ! $all_zero ) {
                                    usort( $member_roles, function( $a, $b ) use ( $order_map ) {
                                        // term_order 0 means "not set" → sort to end
                                        $oa = isset( $order_map[ $a->term_id ] ) && $order_map[ $a->term_id ] > 0 ? $order_map[ $a->term_id ] : 9999;
                                        $ob = isset( $order_map[ $b->term_id ] ) && $order_map[ $b->term_id ] > 0 ? $order_map[ $b->term_id ] : 9999;
                                        return $oa - $ob;
                                    } );
                                }
                            }

                            // Build display roles from the sorted member_roles,
                            // filtered to those matching the page's role filter.
                            $display_roles = array();
                            foreach ( $member_roles as $mr ) {
                                if ( empty( $filtered_role_ids ) || in_array( $mr->term_id, $filtered_role_ids ) ) {
                                    $display_roles[] = $mr->name;
                                }
                            }
                            // Fallback: if filter excluded everything, show first role
                            if ( empty( $display_roles ) && ! empty( $member_roles ) ) {
                                $display_roles[] = $member_roles[0]->name;
                            }

                            // Check if staff member has a description
                            $staff_content = get_post_field( 'post_content', $member->ID );
                            $has_description = ! empty( trim( $staff_content ) );
                            $thumbnail_url = get_the_post_thumbnail_url( $member->ID, 'mammuts-player' );

                            $card_attrs = '';
                            $card_classes = 'coach-card';
                            if ( $has_description ) {
                                $card_classes .= ' coach-card--has-popup';
                                $card_attrs = ' role="button" tabindex="0"'
                                    . ' data-popup-type="staff"'
                                    . ' data-popup-name="' . esc_attr( get_the_title( $member->ID ) ) . '"'
                                    . ' data-popup-role="' . esc_attr( implode( ', ', $display_roles ) ) . '"'
                                    . ' data-popup-image="' . esc_attr( $thumbnail_url ?: '' ) . '"'
                                    . ' data-popup-content="' . esc_attr( wp_kses_post( apply_filters( 'the_content', $staff_content ) ) ) . '"';
                            }
                            ?>
                            <div class="<?php echo esc_attr( $card_classes ); ?>"<?php echo $card_attrs; ?>>
                                <div class="coach-image">
                                    <?php
                                    if ( has_post_thumbnail( $member->ID ) ) {
                                        echo get_the_post_thumbnail( $member->ID, 'mammuts-player' );
                                    } else {
                                        echo '<div class="coach-image-placeholder"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>';
                                    }
                                    ?>
                                </div>
                                <div class="coach-info">
                                    <h3 class="coach-name"><?php echo esc_html( get_the_title( $member->ID ) ); ?></h3>
                                    <?php if ( ! empty( $display_roles ) ) : ?>
                                        <div class="coach-roles">
                                            <?php foreach ( $display_roles as $ri => $dr ) : ?>
                                                <span class="coach-role<?php echo $ri === 0 ? ' coach-role--primary' : ''; ?>"><?php echo esc_html( $dr ); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( $has_description ) : ?>
                                        <span class="coach-has-bio-hint" aria-hidden="true">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p style="text-align:center;color:var(--color-text-muted);">
                        <?php esc_html_e( 'No staff members found. Add staff in SportsPress → Staff.', 'mammuts' ); ?>
                    </p>
                <?php endif;
            else :
                // SportsPress not active — show page content as fallback
                ?>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            <?php
            endif;
        endwhile;
        ?>
    </div>
</section>

<?php get_footer(); ?>
