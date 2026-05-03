<?php
/**
 * Single Event Template (SportsPress)
 *
 * PAST:     Header with score → Ergebnis (result columns) → Player Stats per Team
 * UPCOMING: Header with VS → Venue Card with Map
 *
 * Data sources (no the_content() — we render everything ourselves):
 *   - $event->results()     → array( 0 => columns, team_id => values )
 *   - $event->performance() → array( team_id => array( player_id => stats ) )
 *   - sp_result posts       → result column labels
 *   - sp_performance posts  → performance column labels
 *
 * @package Mammuts
 */

get_header();

// Back-to-schedule navigation bar
$schedule_page_url = '';

// Strategy: find the page that links to schedule/games in the primary menu
// 1. Check nav menu for a menu item labeled "Spiele", "Spielplan", "Schedule", or "Ergebnisse"
$locations = get_nav_menu_locations();
if ( ! empty( $locations['primary'] ) ) {
    $menu_items = wp_get_nav_menu_items( $locations['primary'] );
    if ( is_array( $menu_items ) ) {
        $target_labels = array( 'spiele', 'spielplan', 'schedule', 'ergebnisse', 'games' );
        foreach ( $menu_items as $item ) {
            $label = mb_strtolower( trim( $item->title ) );
            if ( in_array( $label, $target_labels, true ) ) {
                $schedule_page_url = $item->url;
                break;
            }
        }
    }
}

// 2. Page with schedule template explicitly assigned
if ( empty( $schedule_page_url ) ) {
    $schedule_pages = get_pages( array(
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'page-schedule.php',
        'number'     => 1,
    ) );
    if ( ! empty( $schedule_pages ) ) {
        $schedule_page_url = get_permalink( $schedule_pages[0]->ID );
    }
}

// 3. Page matched by slug
if ( empty( $schedule_page_url ) ) {
    foreach ( array( 'schedule', 'spielplan', 'spiele', 'ergebnisse' ) as $try_slug ) {
        $page_by_slug = get_page_by_path( $try_slug );
        if ( $page_by_slug && $page_by_slug->post_status === 'publish' ) {
            $schedule_page_url = get_permalink( $page_by_slug->ID );
            break;
        }
    }
}

// 4. Nested child page — try common parent/child combos
if ( empty( $schedule_page_url ) ) {
    foreach ( array( 'saison/spiele', 'saison/spielplan', 'team/spiele', 'teams/spiele' ) as $try_path ) {
        $page_by_path = get_page_by_path( $try_path );
        if ( $page_by_path && $page_by_path->post_status === 'publish' ) {
            $schedule_page_url = get_permalink( $page_by_path->ID );
            break;
        }
    }
}

// 5. SportsPress calendar/event archive
if ( empty( $schedule_page_url ) ) {
    $schedule_page_url = get_post_type_archive_link( 'sp_event' );
}

// 6. Any published page with 'Spiele' or 'Spielplan' in title
if ( empty( $schedule_page_url ) ) {
    foreach ( array( 'Spiele', 'Spielplan' ) as $search_term ) {
        $found = get_posts( array(
            'post_type'   => 'page',
            'post_status' => 'publish',
            's'           => $search_term,
            'numberposts' => 1,
        ) );
        if ( ! empty( $found ) ) {
            $schedule_page_url = get_permalink( $found[0]->ID );
            break;
        }
    }
}

// 7. Last resort: home
if ( ! $schedule_page_url ) {
    $schedule_page_url = home_url( '/' );
}

$back_url = $schedule_page_url . '#event-' . get_the_ID();

// Find the schedule page ID to get its siblings
$schedule_page_id = url_to_postid( $schedule_page_url );
$schedule_parent_id = $schedule_page_id ? wp_get_post_parent_id( $schedule_page_id ) : 0;

// Get siblings of the schedule page (Tabelle, Spiele, Roster, etc.)
$siblings = array();
if ( $schedule_parent_id ) {
    $siblings = get_pages( array(
        'parent'      => $schedule_parent_id,
        'post_status' => 'publish',
        'sort_column' => 'menu_order,post_title',
        'number'      => 0,
    ) );

    // Sort by nav menu order
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
            return $pos_a !== $pos_b ? $pos_a - $pos_b : strcmp( $a->post_title, $b->post_title );
        } );
    }
}

$parent_title = $schedule_parent_id ? get_the_title( $schedule_parent_id ) : '';
$parent_url   = $schedule_parent_id ? get_permalink( $schedule_parent_id ) : $back_url;
$schedule_url_trailing = $schedule_page_id ? trailingslashit( get_permalink( $schedule_page_id ) ) : '';
?>

<?php if ( count( $siblings ) >= 2 ) : ?>
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
                            $is_current = ( trailingslashit( $sib_url ) === $schedule_url_trailing );
                        ?>
                        <li class="sibling-nav-item<?php echo $is_current ? ' is-active' : ''; ?>">
                            <a href="<?php echo esc_url( $is_current ? $back_url : $sib_url ); ?>" class="sibling-nav-link"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
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
<?php else : ?>
<nav class="sibling-nav news-back-nav" aria-label="<?php esc_attr_e( 'Zurück zu Spiele', 'mammuts' ); ?>">
    <div class="container">
        <div class="sibling-nav-inner">
            <a href="<?php echo esc_url( $back_url ); ?>" class="sibling-nav-parent">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                <span><?php esc_html_e( 'Spiele', 'mammuts' ); ?></span>
            </a>
            <div class="sibling-nav-scroll-wrap">
                <div class="sibling-nav-scroll">
                    <span class="news-back-title"><?php echo get_the_title(); ?></span>
                </div>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<?php while ( have_posts() ) :
    the_post();

    $event_id = get_the_ID();
    $event    = new SP_Event( $event_id );
    $teams    = get_post_meta( $event_id, 'sp_team', false );
    $teams    = array_values( array_filter( $teams ) );
    $venue    = wp_get_post_terms( $event_id, 'sp_venue' );
    $leagues  = wp_get_post_terms( $event_id, 'sp_league' );
    $seasons  = wp_get_post_terms( $event_id, 'sp_season' );

    // Results data
    $results_data = $event->results();
    $has_results  = ! empty( $results_data );
    $scores       = $has_results ? mammuts_get_event_scores( $results_data, $teams ) : array();

    // Past or upcoming?
    $event_date  = get_the_date( 'Y-m-d H:i:s' );
    $now         = current_time( 'Y-m-d H:i:s' );
    $is_past     = ( $event_date < $now );
    $event_class = $is_past ? 'event-is-past' : 'event-is-upcoming';

    // Venue meta (SportsPress stores in wp_options as "taxonomy_{term_id}")
    $venue_name    = '';
    $venue_address = '';
    $venue_lat     = '';
    $venue_lng     = '';
    if ( ! empty( $venue ) ) {
        $venue_name = $venue[0]->name;
        $t_id       = $venue[0]->term_id;
        $meta       = get_option( "taxonomy_{$t_id}" );
        if ( is_array( $meta ) ) {
            $venue_address = isset( $meta['sp_address'] )   ? $meta['sp_address']   : '';
            $venue_lat     = isset( $meta['sp_latitude'] )  ? $meta['sp_latitude']  : '';
            $venue_lng     = isset( $meta['sp_longitude'] ) ? $meta['sp_longitude'] : '';
        }
        // Fallback: term_meta
        if ( ! $venue_address ) $venue_address = get_term_meta( $t_id, 'sp_address', true );
        if ( ! $venue_lat )     $venue_lat     = get_term_meta( $t_id, 'sp_latitude', true );
        if ( ! $venue_lng )     $venue_lng     = get_term_meta( $t_id, 'sp_longitude', true );
    }

?>

<!-- ══════════ MATCH HEADER ══════════ -->
<div class="event-header">
    <div class="container">
        <div class="event-meta-line">
            <?php if ( ! empty( $leagues ) ) : ?>
                <span class="event-league"><?php echo esc_html( $leagues[0]->name ); ?></span>
            <?php endif; ?>
            <?php if ( ! empty( $seasons ) ) : ?>
                <span class="event-season"><?php echo esc_html( $seasons[0]->name ); ?></span>
            <?php endif; ?>
        </div>

        <div class="event-matchup">
            <?php if ( count( $teams ) >= 2 ) : ?>
                <div class="event-team event-team--home">
                    <div class="event-team-logo"><?php mammuts_team_logo( $teams[0] ); ?></div>
                    <h2 class="event-team-name"><?php echo esc_html( get_the_title( $teams[0] ) ); ?></h2>
                </div>

                <div class="event-center">
                    <?php if ( $has_results && $is_past ) : ?>
                        <div class="event-score"><?php echo esc_html( implode( ' : ', $scores ) ); ?></div>
                    <?php else : ?>
                        <div class="event-vs">VS</div>
                    <?php endif; ?>
                </div>

                <div class="event-team event-team--away">
                    <div class="event-team-logo"><?php mammuts_team_logo( $teams[1] ); ?></div>
                    <h2 class="event-team-name"><?php echo esc_html( get_the_title( $teams[1] ) ); ?></h2>
                </div>
            <?php endif; ?>
        </div>

        <div class="event-info-bar">
            <?php if ( $venue_name ) : ?>
                <span class="event-info-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?php echo esc_html( $venue_name ); ?>
                </span>
            <?php endif; ?>
            <span class="event-info-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?php echo get_the_date( 'd.m.Y' ); ?>
            </span>
            <span class="event-info-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?php echo get_the_time( 'H:i' ); ?> Uhr
            </span>
        </div>
    </div>
</div>

<!-- ══════════ EVENT BODY ══════════ -->
<div class="event-body <?php echo esc_attr( $event_class ); ?>">
    <div class="container">

<?php if ( $is_past ) : ?>

    <?php
    // ┌─────────────────────────────────────────────────┐
    // │  1) ERGEBNIS TABLE (Result columns from SP)     │
    // └─────────────────────────────────────────────────┘
    //
    // $event->results() returns:
    //   array(
    //     0         => array( 'slug1' => 'Label1', 'slug2' => 'Label2', ... ),  ← column headers
    //     team_id_1 => array( 'slug1' => '7', 'slug2' => '0', 'outcome' => 'win' ),
    //     team_id_2 => array( 'slug1' => '0', 'slug2' => '0', 'outcome' => 'loss' ),
    //   )

    if ( $has_results && count( $teams ) >= 2 ) :
        // Row 0 contains the column labels
        $result_columns = isset( $results_data[0] ) ? $results_data[0] : array();

        // Remove meta columns and any column that represents the final total.
        // SportsPress uses different slugs depending on configuration:
        // 'total', 'points', 'pts', 'tp', 'score' — and labels like 'T', 'Total', 'Gesamt', 'Pts'.
        // We strip all of them and render our own single "GESAMT" column from $scores[].
        $total_slugs  = array( 'outcome', 'total', 'points', 'pts', 'tp', 'score', 'totals' );
        $total_labels = array( 't', 'total', 'gesamt', 'pts', 'punkte', 'score', 'ergebnis' );

        $sp_has_total = false;
        foreach ( $result_columns as $slug => $label ) {
            if ( in_array( strtolower( $slug ), $total_slugs, true ) ||
                 in_array( strtolower( trim( $label ) ), $total_labels, true ) ) {
                $sp_has_total = true;
                unset( $result_columns[ $slug ] );
            }
        }

        // Check which columns actually have data
        $active_result_cols = array();
        foreach ( $result_columns as $slug => $label ) {
            foreach ( $teams as $team_id ) {
                $tid = intval( $team_id );
                if ( isset( $results_data[ $tid ][ $slug ] ) &&
                     $results_data[ $tid ][ $slug ] !== '' &&
                     $results_data[ $tid ][ $slug ] !== null ) {
                    $active_result_cols[ $slug ] = $label;
                    break;
                }
            }
        }

        if ( ! empty( $active_result_cols ) ) :
    ?>
        <div class="event-results-card">
            <h3 class="event-section-title">Ergebnis</h3>
            <div class="event-table-wrap">
                <table class="event-results-table">
                    <thead>
                        <tr>
                            <th class="col-team">Mannschaft</th>
                            <?php foreach ( $active_result_cols as $slug => $label ) : ?>
                                <th><?php echo esc_html( $label ); ?></th>
                            <?php endforeach; ?>
                            <th class="col-total">Gesamt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $teams as $idx => $team_id ) :
                            $tid       = intval( $team_id );
                            $team_data = isset( $results_data[ $tid ] ) ? $results_data[ $tid ] : array();
                        ?>
                        <tr>
                            <td class="col-team">
                                <span class="results-team-name"><?php echo esc_html( get_the_title( $team_id ) ); ?></span>
                            </td>
                            <?php foreach ( $active_result_cols as $slug => $label ) : ?>
                                <td><?php
                                    $val = isset( $team_data[ $slug ] ) ? $team_data[ $slug ] : '';
                                    echo esc_html( $val !== '' ? $val : '-' );
                                ?></td>
                            <?php endforeach; ?>
                            <td class="col-total">
                                <strong><?php echo esc_html( isset( $scores[ $idx ] ) ? $scores[ $idx ] : '0' ); ?></strong>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
        endif;
    endif;
    ?>

    <?php
    // ┌─────────────────────────────────────────────────┐
    // │  2) PLAYER PERFORMANCE per Team                 │
    // └─────────────────────────────────────────────────┘
    //
    // $event->performance() returns:
    //   array(
    //     0         => array( 'slug' => 'Label', ... ),  ← column headers (sometimes)
    //     team_id_1 => array(
    //       player_id => array( 'slug' => value, 'number' => '99', 'position' => term_id ),
    //       0         => array( ... ),  ← totals row
    //     ),
    //   )

    if ( get_theme_mod( 'mammuts_event_stats_enabled', true ) ) :

    $performance = $event->performance();

    if ( ! empty( $performance ) && count( $teams ) >= 1 ) :

        // Get performance column definitions from sp_performance post type
        // These are sorted by menu_order (the order configured in SportsPress)
        $perf_columns = array();
        $sp_perf_posts = get_posts( array(
            'post_type'      => 'sp_performance',
            'posts_per_page' => 100,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ) );
        foreach ( $sp_perf_posts as $pp ) {
            $perf_columns[ $pp->post_name ] = $pp->post_title;
        }

        // Also check if row 0 has header labels (some SP versions)
        if ( isset( $performance[0] ) && is_array( $performance[0] ) ) {
            foreach ( $performance[0] as $slug => $label ) {
                if ( ! isset( $perf_columns[ $slug ] ) && ! in_array( $slug, array( 'number', 'position', 'name', 'status', 'sub' ) ) ) {
                    $perf_columns[ $slug ] = $label;
                }
            }
        }

        foreach ( $teams as $team_id ) :
            $tid       = intval( $team_id );
            $team_perf = isset( $performance[ $tid ] ) ? $performance[ $tid ] : array();

            if ( empty( $team_perf ) ) continue;

            // Separate player rows from totals (key 0)
            $players_data = array();
            $totals_data  = array();

            foreach ( $team_perf as $player_id => $stats ) {
                if ( $player_id === 0 || $player_id === '0' ) {
                    $totals_data = $stats;
                    continue;
                }
                $players_data[ $player_id ] = $stats;
            }

            // Skip team entirely if no players AND no totals
            if ( empty( $players_data ) && empty( $totals_data ) ) continue;

            // Show ALL configured performance columns so teams are comparable
            $active_perf_cols = $perf_columns;

            $show_stats  = ! empty( $active_perf_cols );
            $has_players = ! empty( $players_data );
        ?>
            <div class="event-performance-card">
                <h3 class="event-section-title"><?php echo esc_html( get_the_title( $team_id ) ); ?></h3>
                <div class="event-table-wrap">
                    <table class="event-performance-table">
                        <thead>
                            <tr>
                                <?php if ( $has_players ) : ?>
                                    <th class="col-number">#</th>
                                    <th class="col-player">Spieler</th>
                                    <th class="col-position">Position</th>
                                <?php else : ?>
                                    <th class="col-position">Position</th>
                                <?php endif; ?>
                                <?php if ( $show_stats ) :
                                    foreach ( $active_perf_cols as $slug => $label ) : ?>
                                        <th><?php echo esc_html( $label ); ?></th>
                                    <?php endforeach;
                                endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ( $has_players ) :
                                foreach ( $players_data as $player_id => $stats ) :
                                    $player_id = intval( $player_id );
                                    $number    = get_post_meta( $player_id, 'sp_number', true );

                                    // Position: might be stored as term_id in stats, or from taxonomy
                                    $pos_name = '';
                                    if ( isset( $stats['position'] ) && is_numeric( $stats['position'] ) ) {
                                        $pos_term = get_term( intval( $stats['position'] ), 'sp_position' );
                                        if ( $pos_term && ! is_wp_error( $pos_term ) ) {
                                            $pos_name = $pos_term->name;
                                        }
                                    }
                                    if ( ! $pos_name ) {
                                        $positions = wp_get_post_terms( $player_id, 'sp_position' );
                                        $pos_name  = ! empty( $positions ) ? $positions[0]->name : '';
                                    }
                                ?>
                                <tr>
                                    <td class="col-number"><?php echo esc_html( $number ); ?></td>
                                    <td class="col-player">
                                        <a href="<?php echo get_permalink( $player_id ); ?>">
                                            <?php echo esc_html( get_the_title( $player_id ) ); ?>
                                        </a>
                                    </td>
                                    <td class="col-position"><?php echo esc_html( $pos_name ); ?></td>
                                    <?php if ( $show_stats ) :
                                        foreach ( $active_perf_cols as $slug => $label ) : ?>
                                            <td><?php echo esc_html( isset( $stats[ $slug ] ) && $stats[ $slug ] !== '' ? $stats[ $slug ] : '0' ); ?></td>
                                        <?php endforeach;
                                    endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if ( $show_stats && ! empty( $totals_data ) ) :
                                $colspan = $has_players ? 2 : 0;
                            ?>
                            <tr class="totals-row">
                                <?php if ( $has_players ) : ?>
                                    <td class="col-number"></td>
                                <?php endif; ?>
                                <td class="col-player" <?php if ( $colspan ) echo 'colspan="' . $colspan . '"'; ?>><strong>Gesamt</strong></td>
                                <?php foreach ( $active_perf_cols as $slug => $label ) : ?>
                                    <td><strong><?php echo esc_html( isset( $totals_data[ $slug ] ) && $totals_data[ $slug ] !== '' ? $totals_data[ $slug ] : '0' ); ?></strong></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php endif; // mammuts_event_stats_enabled ?>

<?php else : ?>

    <?php
    // ┌─────────────────────────────────────────────────┐
    // │  UPCOMING: Venue Card + Map                     │
    // └─────────────────────────────────────────────────┘
    if ( $venue_name ) :
        $has_coords = ( ! empty( $venue_lat ) && ! empty( $venue_lng ) );
    ?>
        <div class="event-venue-card">
            <h3 class="event-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Spielort
            </h3>
            <div class="event-venue-info">
                <p class="event-venue-name"><?php echo esc_html( $venue_name ); ?></p>
                <?php if ( $venue_address ) : ?>
                    <p class="event-venue-address"><?php echo esc_html( $venue_address ); ?></p>
                <?php endif; ?>
            </div>

            <?php
            $map_query = $has_coords ? ( $venue_lat . ',' . $venue_lng ) : $venue_address;
            if ( $map_query ) : ?>
                <div class="event-venue-map event-venue-map--embed">
                    <iframe width="100%" height="100%" style="border:0;" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://maps.google.com/maps?q=<?php echo urlencode( $map_query ); ?>&output=embed"
                            allowfullscreen></iframe>
                </div>
            <?php else : ?>
                <div class="event-venue-map event-venue-map--placeholder">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.3">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>Karte nicht verfügbar</span>
                </div>
            <?php endif; ?>

            <?php
            $dest = $has_coords ? ( $venue_lat . ',' . $venue_lng ) : $venue_address;
            if ( $dest ) : ?>
                <div class="event-venue-actions">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode( $dest ); ?>"
                       target="_blank" rel="noopener noreferrer" class="event-venue-directions">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                        Route planen
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="event-no-venue">
            <p>Spielort wird noch bekannt gegeben.</p>
        </div>
    <?php endif; ?>

<?php endif; // Ende $is_past (Venue/Spielort) ?>

<?php
// ┌─────────────────────────────────────────────────┐
// │  SPIELBERICHT: Post-Content (Text, Bilder,      │
// │  Galerien, Links) — nur wenn Inhalt vorhanden   │
// └─────────────────────────────────────────────────┘
if ( $is_past ) :
    $post_content = get_the_content();
    if ( ! empty( trim( $post_content ) ) ) :
?>
    <div class="event-recap-card">
        <h3 class="event-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Spielinformationen
        </h3>
        <div class="event-recap-content">
            <?php echo mammuts_render_recap_content( $post_content ); ?>
        </div>
    </div>
<?php
    endif; // Ende post_content check
endif; // Ende $is_past (Spielbericht)
?>

    </div>
</div>

<?php
// Leaflet Map Script (upcoming events with coordinates only)
if ( ! $is_past && ! empty( $venue_lat ) && ! empty( $venue_lng ) ) : ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    var el = document.getElementById('event-venue-map');
    if (!el) return;
    var lat = parseFloat(el.dataset.lat), lng = parseFloat(el.dataset.lng);
    if (isNaN(lat) || isNaN(lng)) return;
    setTimeout(function() {
        var map = L.map(el).setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup('<strong>' + (el.dataset.venue||'') + '</strong>').openPopup();
    }, 200);
})();
</script>
<?php endif; ?>

<?php
endwhile;
get_footer();
?>
