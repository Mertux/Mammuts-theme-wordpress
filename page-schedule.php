<?php
/**
 * Template Name: Schedule / Results
 * Description: Modern schedule & results page with card/table toggle.
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="section schedule-page">
    <div class="container">
        <?php
        while ( have_posts() ) :
            the_post();
            $content = get_the_content();

            if ( ! empty( trim( $content ) ) ) :
                ?>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            <?php
            elseif ( mammuts_has_sportspress() ) :
                $slug = strtolower( get_post_field( 'post_name', get_the_ID() ) );
                $is_results_only  = ( $slug === 'ergebnisse' || $slug === 'results' );
                $is_schedule_only = ( $slug === 'spielplan' || $slug === 'schedule' );

                $now = current_time( 'Y-m-d H:i:s' );

                // Read team/league/season filter from page metabox
                $filter_team   = get_post_meta( get_the_ID(), '_mammuts_sp_team', true );
                $filter_league = get_post_meta( get_the_ID(), '_mammuts_sp_league', true );
                $filter_season = get_post_meta( get_the_ID(), '_mammuts_sp_season', true );

                // Build base query args
                $base_args = array(
                    'post_type'      => 'sp_event',
                    'posts_per_page' => 50,
                );

                // Filter by team (via sp_team meta)
                if ( ! empty( $filter_team ) ) {
                    $base_args['meta_query'] = array(
                        array(
                            'key'     => 'sp_team',
                            'value'   => $filter_team,
                            'compare' => '=',
                        ),
                    );
                }

                // Tax query: league and/or season
                $tax_filters = array();
                if ( ! empty( $filter_league ) ) {
                    $tax_filters[] = array(
                        'taxonomy' => 'sp_league',
                        'field'    => 'slug',
                        'terms'    => $filter_league,
                    );
                }
                // Season: metabox setting or auto-detect
                $season_slug = ! empty( $filter_season ) ? $filter_season : mammuts_get_current_season_slug();
                if ( ! empty( $season_slug ) ) {
                    $tax_filters[] = array(
                        'taxonomy' => 'sp_season',
                        'field'    => 'slug',
                        'terms'    => $season_slug,
                    );
                }
                if ( ! empty( $tax_filters ) ) {
                    if ( count( $tax_filters ) > 1 ) {
                        $tax_filters['relation'] = 'AND';
                    }
                    $base_args['tax_query'] = $tax_filters;
                }

                // ── Upcoming Events ──
                $upcoming = array();
                if ( ! $is_results_only ) {
                    $upcoming_args = array_merge( $base_args, array(
                        'post_status' => array( 'publish', 'future' ),
                        'orderby'     => 'date',
                        'order'       => 'ASC',
                    ) );
                    $all_events = get_posts( $upcoming_args );

                    foreach ( $all_events as $ev ) {
                        $event_date = get_the_date( 'Y-m-d H:i:s', $ev->ID );
                        if ( $event_date >= $now ) {
                            $upcoming[] = $ev;
                        }
                    }
                }

                // ── Past Events (Results) ──
                $past = array();
                if ( ! $is_schedule_only ) {
                    $past_args = array_merge( $base_args, array(
                        'post_status' => 'publish',
                        'orderby'     => 'date',
                        'order'       => 'DESC',
                    ) );
                    $all_past = get_posts( $past_args );

                    foreach ( $all_past as $ev ) {
                        $event_date = get_the_date( 'Y-m-d H:i:s', $ev->ID );
                        if ( $event_date < $now ) {
                            $past[] = $ev;
                        }
                    }
                }

                $has_upcoming = ! empty( $upcoming );
                $has_past     = ! empty( $past );
                ?>

                <!-- View Toggle -->
                <div class="schedule-controls">
                    <?php if ( $has_upcoming && $has_past ) : ?>
                    <div class="schedule-tabs">
                        <button class="schedule-tab active" data-tab="upcoming"><?php esc_html_e( 'Upcoming', 'mammuts' ); ?></button>
                        <button class="schedule-tab" data-tab="results"><?php esc_html_e( 'Results', 'mammuts' ); ?></button>
                    </div>
                    <?php endif; ?>
                    <div class="schedule-view-toggle">
                        <button class="schedule-view-btn active" data-view="cards" aria-label="Cards">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </button>
                        <button class="schedule-view-btn" data-view="table" aria-label="Table">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                        </button>
                    </div>
                </div>

                <!-- ===== UPCOMING ===== -->
                <?php if ( $has_upcoming ) : ?>
                <div class="schedule-section" id="schedule-upcoming">

                    <?php if ( $has_past ) : ?>
                    <div class="schedule-section-title">
                        <h2><?php esc_html_e( 'Upcoming Games', 'mammuts' ); ?></h2>
                        <span class="schedule-count"><?php echo count( $upcoming ); ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Cards View -->
                    <div class="schedule-cards" id="upcoming-cards">
                        <div class="match-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
                            <?php
                            $first = true;
                            foreach ( $upcoming as $event ) :
                                if ( $first ) {
                                    mammuts_match_card( $event->ID, 'next' );
                                    $first = false;
                                } else {
                                    mammuts_match_card( $event->ID, 'upcoming' );
                                }
                            endforeach;
                            ?>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div class="schedule-table-wrap" id="upcoming-table" style="display:none;">
                        <table class="schedule-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Date', 'mammuts' ); ?></th>
                                    <th><?php esc_html_e( 'Time', 'mammuts' ); ?></th>
                                    <th><?php esc_html_e( 'Home', 'mammuts' ); ?></th>
                                    <th></th>
                                    <th><?php esc_html_e( 'Away', 'mammuts' ); ?></th>
                                    <th><?php esc_html_e( 'Venue', 'mammuts' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $upcoming as $event ) :
                                    $teams = get_post_meta( $event->ID, 'sp_team', false );
                                    $teams = array_filter( $teams );
                                    $teams = array_values( $teams );
                                    $venue = wp_get_post_terms( $event->ID, 'sp_venue' );
                                ?>
                                <tr class="schedule-table-row" onclick="window.location='<?php echo esc_url( get_permalink( $event->ID ) ); ?>'">
                                    <td class="schedule-table-date"><?php echo get_the_date( 'd.m.Y', $event->ID ); ?></td>
                                    <td class="schedule-table-time"><?php echo get_the_time( 'H:i', $event->ID ); ?></td>
                                    <td class="schedule-table-team"><?php echo isset( $teams[0] ) ? esc_html( get_the_title( $teams[0] ) ) : '—'; ?></td>
                                    <td class="schedule-table-vs">vs</td>
                                    <td class="schedule-table-team"><?php echo isset( $teams[1] ) ? esc_html( get_the_title( $teams[1] ) ) : '—'; ?></td>
                                    <td class="schedule-table-venue"><?php echo ! empty( $venue ) ? esc_html( $venue[0]->name ) : ''; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ===== RESULTS ===== -->
                <?php if ( $has_past ) : ?>
                <div class="schedule-section <?php echo $has_upcoming ? 'schedule-section--hidden' : ''; ?>" id="schedule-results">

                    <?php if ( $has_upcoming ) : ?>
                    <div class="schedule-section-title">
                        <h2><?php esc_html_e( 'Results', 'mammuts' ); ?></h2>
                        <span class="schedule-count"><?php echo count( $past ); ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Cards View -->
                    <div class="schedule-cards" id="results-cards">
                        <div class="match-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
                            <?php foreach ( $past as $event ) :
                                mammuts_match_card( $event->ID, 'result' );
                            endforeach; ?>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div class="schedule-table-wrap" id="results-table" style="display:none;">
                        <table class="schedule-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Date', 'mammuts' ); ?></th>
                                    <th><?php esc_html_e( 'Home', 'mammuts' ); ?></th>
                                    <th><?php esc_html_e( 'Score', 'mammuts' ); ?></th>
                                    <th><?php esc_html_e( 'Away', 'mammuts' ); ?></th>
                                    <th><?php esc_html_e( 'Venue', 'mammuts' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $past as $event ) :
                                    $ev_obj  = new SP_Event( $event->ID );
                                    $results = $ev_obj->results();
                                    $teams   = get_post_meta( $event->ID, 'sp_team', false );
                                    $teams   = array_values( array_filter( $teams ) );
                                    $venue   = wp_get_post_terms( $event->ID, 'sp_venue' );
                                    $scores  = mammuts_get_event_scores( $results, $teams );
                                ?>
                                <tr class="schedule-table-row" onclick="window.location='<?php echo esc_url( get_permalink( $event->ID ) ); ?>'">
                                    <td class="schedule-table-date"><?php echo get_the_date( 'd.m.Y', $event->ID ); ?></td>
                                    <td class="schedule-table-team"><?php echo isset( $teams[0] ) ? esc_html( get_the_title( $teams[0] ) ) : '—'; ?></td>
                                    <td class="schedule-table-score"><?php echo esc_html( implode( ' : ', $scores ) ); ?></td>
                                    <td class="schedule-table-team"><?php echo isset( $teams[1] ) ? esc_html( get_the_title( $teams[1] ) ) : '—'; ?></td>
                                    <td class="schedule-table-venue"><?php echo ! empty( $venue ) ? esc_html( $venue[0]->name ) : ''; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif;

                if ( ! $has_upcoming && ! $has_past ) : ?>
                    <p style="text-align:center;color:var(--color-text-muted);">
                        <?php esc_html_e( 'No events found.', 'mammuts' ); ?>
                    </p>
                <?php endif;

            endif;
        endwhile;
        ?>
    </div>
</section>

<script>
(function() {
    // Tab switching (Upcoming / Results)
    var tabs = document.querySelectorAll('.schedule-tab');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            var target = this.getAttribute('data-tab');
            var upcoming = document.getElementById('schedule-upcoming');
            var results  = document.getElementById('schedule-results');
            if (upcoming) upcoming.style.display = (target === 'upcoming' || target === 'all') ? '' : 'none';
            if (results)  results.style.display  = (target === 'results' || target === 'all') ? '' : 'none';
        });
    });
    // Initially show upcoming, hide results
    var resultsSection = document.getElementById('schedule-results');
    if (resultsSection && tabs.length > 0) resultsSection.style.display = 'none';

    // If URL hash points to an element inside results, reveal results and scroll
    var hash = window.location.hash;
    if (hash) {
        var target = document.querySelector(hash);
        if (target && resultsSection && resultsSection.contains(target)) {
            // Show results section
            resultsSection.style.display = '';
            // Switch tab to results
            tabs.forEach(function(t) {
                t.classList.remove('active');
                if (t.getAttribute('data-tab') === 'results') t.classList.add('active');
            });
            // Hide upcoming
            var upcomingSection = document.getElementById('schedule-upcoming');
            if (upcomingSection) upcomingSection.style.display = 'none';
            // Scroll to the card
            setTimeout(function() {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        } else if (target) {
            // Hash target is in upcoming — just scroll
            setTimeout(function() {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }
    }

    // View toggle (Cards / Table)
    var viewBtns = document.querySelectorAll('.schedule-view-btn');
    viewBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            viewBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            var view = this.getAttribute('data-view');
            document.querySelectorAll('.schedule-cards').forEach(function(el) {
                el.style.display = (view === 'cards') ? '' : 'none';
            });
            document.querySelectorAll('.schedule-table-wrap').forEach(function(el) {
                el.style.display = (view === 'table') ? '' : 'none';
            });
        });
    });
})();
</script>

<?php get_footer(); ?>
