<?php
/**
 * Front Page Template
 *
 * The main homepage with hero, match center, roster, news, and sponsors.
 *
 * @package Mammuts
 */

get_header();
?>


<!-- ===== EILMELDUNGEN (Breaking News Alerts) ===== -->
<?php mammuts_render_alerts(); ?>


<!-- ===== COUNTDOWN TO NEXT GAME / EVENT ===== -->
<?php
$show_countdown = get_theme_mod( 'mammuts_show_gameday_countdown', true );
if ( $show_countdown ) :
    $now_ts    = current_time( 'timestamp' );
    $max_days  = intval( get_theme_mod( 'mammuts_countdown_max_days', 14 ) );
    $dual_days = intval( get_theme_mod( 'mammuts_countdown_dual_days', 7 ) );

    // ── Gather next SportsPress game ──
    $game_data = null;
    if ( mammuts_has_sportspress() ) {
        $countdown_events = mammuts_get_next_events( 1 );
        if ( ! empty( $countdown_events ) ) {
            $ge         = $countdown_events[0];
            $ge_date    = get_the_date( 'Y-m-d H:i:s', $ge->ID );
            $ge_ts      = strtotime( $ge_date );
            $ge_diff    = $ge_ts - $now_ts;
            $ge_days    = $ge_diff / 86400;
            $ge_show    = ( $ge_diff > 0 ) && ( $max_days === 0 || $ge_days <= $max_days );

            if ( $ge_show ) {
                $game_data = array(
                    'post'     => $ge,
                    'ts'       => $ge_ts,
                    'datetime' => date( 'Y-m-d\TH:i:s', $ge_ts ),
                    'diff'     => $ge_diff,
                    'teams'    => array_values( array_filter( get_post_meta( $ge->ID, 'sp_team', false ) ) ),
                    'venue'    => wp_get_post_terms( $ge->ID, 'sp_venue' ),
                );
            }
        }
    }

    // ── Gather next club event ──
    $event_data = null;
    $next_club  = mammuts_get_next_club_event();
    if ( $next_club ) {
        $ce_datetime = mammuts_club_event_datetime( $next_club->ID );
        if ( ! empty( $ce_datetime ) ) {
            $ce_ts   = strtotime( $ce_datetime );
            $ce_diff = $ce_ts - $now_ts;
            $ce_days = $ce_diff / 86400;
            $ce_show = ( $ce_diff > 0 ) && ( $max_days === 0 || $ce_days <= $max_days );

            if ( $ce_show ) {
                $event_data = array(
                    'post'     => $next_club,
                    'ts'       => $ce_ts,
                    'datetime' => $ce_datetime,
                    'diff'     => $ce_diff,
                    'location' => get_post_meta( $next_club->ID, '_mammuts_event_location', true ),
                );
            }
        }
    }

    // ── Decide what to show ──
    // Both exist and within dual_days of each other → show both
    // Otherwise → show whichever is closest
    $show_both = false;
    if ( $game_data && $event_data && $dual_days > 0 ) {
        $gap_days = abs( $game_data['diff'] - $event_data['diff'] ) / 86400;
        if ( $gap_days <= $dual_days ) {
            $show_both = true;
        }
    }

    // At least one to show?
    if ( $game_data || $event_data ) :
        // If not showing both, pick the closest
        if ( ! $show_both ) {
            if ( $game_data && $event_data ) {
                // Keep the closer one only
                if ( $event_data['diff'] < $game_data['diff'] ) {
                    $game_data = null;
                } else {
                    $event_data = null;
                }
            }
        }
?>
<section class="countdown-section">
    <div class="container">
        <?php
        // Build an array of items to render, sorted by time remaining (ascending)
        $countdown_items = array();
        if ( $game_data ) {
            $countdown_items[] = array( 'type' => 'game', 'data' => $game_data );
        }
        if ( $event_data ) {
            $countdown_items[] = array( 'type' => 'event', 'data' => $event_data );
        }
        // Sort: closest event first
        usort( $countdown_items, function( $a, $b ) {
            return $a['data']['diff'] - $b['data']['diff'];
        } );
        $is_dual = count( $countdown_items ) > 1;
        ?>
        <div class="countdown-grid<?php echo $is_dual ? ' countdown-grid--dual' : ''; ?>">

            <?php foreach ( $countdown_items as $idx => $item ) :
                $uid = 'cd-' . $item['type'] . '-' . $idx;

                if ( $item['type'] === 'game' ) :
                    $gd = $item['data'];
            ?>
            <a href="<?php echo esc_url( get_permalink( $gd['post']->ID ) ); ?>" class="countdown-card">
                <div class="countdown-header">
                    <span class="countdown-badge">Game Day</span>
                    <span class="countdown-date">
                        <?php echo get_the_date( 'D, d. M Y', $gd['post']->ID ); ?> · <?php echo get_the_time( 'H:i', $gd['post']->ID ); ?> Uhr
                    </span>
                </div>

                <div class="countdown-matchup">
                    <?php if ( count( $gd['teams'] ) >= 2 ) : ?>
                        <div class="countdown-team">
                            <?php mammuts_team_logo( $gd['teams'][0] ); ?>
                            <span><?php echo esc_html( get_the_title( $gd['teams'][0] ) ); ?></span>
                        </div>
                        <span class="countdown-vs">VS</span>
                        <div class="countdown-team">
                            <?php mammuts_team_logo( $gd['teams'][1] ); ?>
                            <span><?php echo esc_html( get_the_title( $gd['teams'][1] ) ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="countdown-timer" id="<?php echo esc_attr( $uid ); ?>"
                     data-target="<?php echo esc_attr( $gd['datetime'] ); ?>">
                    <div class="countdown-unit">
                        <span class="countdown-number" data-cd="days">--</span>
                        <span class="countdown-label"><?php esc_html_e( 'Tage', 'mammuts' ); ?></span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit">
                        <span class="countdown-number" data-cd="hours">--</span>
                        <span class="countdown-label"><?php esc_html_e( 'Std', 'mammuts' ); ?></span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit">
                        <span class="countdown-number" data-cd="mins">--</span>
                        <span class="countdown-label"><?php esc_html_e( 'Min', 'mammuts' ); ?></span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit">
                        <span class="countdown-number" data-cd="secs">--</span>
                        <span class="countdown-label"><?php esc_html_e( 'Sek', 'mammuts' ); ?></span>
                    </div>
                </div>

                <?php if ( ! empty( $gd['venue'] ) ) : ?>
                    <div class="countdown-venue">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?php echo esc_html( $gd['venue'][0]->name ); ?>
                    </div>
                <?php endif; ?>
            </a>
            <?php elseif ( $item['type'] === 'event' ) :
                    $ed = $item['data'];
            ?>
            <a href="<?php echo esc_url( get_permalink( $ed['post']->ID ) ); ?>" class="countdown-card countdown-card--event">
                <div class="countdown-header">
                    <span class="countdown-badge countdown-badge--event"><?php esc_html_e( 'Event', 'mammuts' ); ?></span>
                    <span class="countdown-date">
                        <?php
                        $ev_date = get_post_meta( $ed['post']->ID, '_mammuts_event_date', true );
                        $ev_time = get_post_meta( $ed['post']->ID, '_mammuts_event_time', true );
                        echo esc_html( date_i18n( 'D, d. M Y', strtotime( $ev_date ) ) );
                        if ( ! empty( $ev_time ) ) echo ' · ' . esc_html( $ev_time ) . ' Uhr';
                        ?>
                    </span>
                </div>

                <div class="countdown-event-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><circle cx="12" cy="16" r="2"/></svg>
                </div>

                <div class="countdown-event-title"><?php echo esc_html( get_the_title( $ed['post']->ID ) ); ?></div>

                <div class="countdown-timer" id="<?php echo esc_attr( $uid ); ?>"
                     data-target="<?php echo esc_attr( $ed['datetime'] ); ?>">
                    <div class="countdown-unit">
                        <span class="countdown-number" data-cd="days">--</span>
                        <span class="countdown-label"><?php esc_html_e( 'Tage', 'mammuts' ); ?></span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit">
                        <span class="countdown-number" data-cd="hours">--</span>
                        <span class="countdown-label"><?php esc_html_e( 'Std', 'mammuts' ); ?></span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit">
                        <span class="countdown-number" data-cd="mins">--</span>
                        <span class="countdown-label"><?php esc_html_e( 'Min', 'mammuts' ); ?></span>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-unit">
                        <span class="countdown-number" data-cd="secs">--</span>
                        <span class="countdown-label"><?php esc_html_e( 'Sek', 'mammuts' ); ?></span>
                    </div>
                </div>

                <?php if ( ! empty( $ed['location'] ) ) : ?>
                    <div class="countdown-venue">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?php echo esc_html( $ed['location'] ); ?>
                    </div>
                <?php endif; ?>
            </a>
            <?php endif; ?>

            <?php endforeach; ?>

        </div>
    </div>
</section>

<script>
(function(){
    // Generic countdown for all countdown-timer elements on the page
    var timers = document.querySelectorAll('.countdown-timer[data-target]');
    timers.forEach(function(el) {
        var target = new Date(el.getAttribute('data-target')).getTime();
        var els = {
            days:  el.querySelector('[data-cd="days"]'),
            hours: el.querySelector('[data-cd="hours"]'),
            mins:  el.querySelector('[data-cd="mins"]'),
            secs:  el.querySelector('[data-cd="secs"]')
        };
        if (!els.days) return;

        function update() {
            var diff = target - Date.now();
            if (diff <= 0) {
                els.days.textContent = '0';
                els.hours.textContent = '00';
                els.mins.textContent = '00';
                els.secs.textContent = '00';
                return;
            }
            els.days.textContent = Math.floor(diff / 864e5);
            els.hours.textContent = String(Math.floor(diff % 864e5 / 36e5)).padStart(2, '0');
            els.mins.textContent = String(Math.floor(diff % 36e5 / 6e4)).padStart(2, '0');
            els.secs.textContent = String(Math.floor(diff % 6e4 / 1e3)).padStart(2, '0');
        }
        update();
        setInterval(update, 1000);
    });
})();
</script>
<?php
    endif;
endif;
?>


<!-- ===== MATCH CENTER ===== -->
<?php if ( get_theme_mod( 'mammuts_show_match_center', true ) && mammuts_has_sportspress() ) : ?>
<section class="match-center" id="match-center">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e( 'Match Center', 'mammuts' ); ?></h2>
        </div>

        <div class="match-grid match-grid--home">
            <?php
            // Last Result
            $recent = mammuts_get_recent_results( 1 );
            if ( ! empty( $recent ) ) {
                mammuts_match_card( $recent[0]->ID, 'result' );
            }

            // Next Game
            $upcoming = mammuts_get_next_events( 1 );
            if ( ! empty( $upcoming ) ) {
                mammuts_match_card( $upcoming[0]->ID, 'next' );
            } else {
                ?>
                <div class="match-card match-card--featured">
                    <span class="match-label match-label--next"><?php esc_html_e( 'Next Game', 'mammuts' ); ?></span>
                    <p style="color:var(--color-text-muted);margin-top:20px;"><?php esc_html_e( 'No upcoming games scheduled.', 'mammuts' ); ?></p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ===== NEWS ===== -->
<?php
$news_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
) );

if ( $news_query->have_posts() ) : ?>
<section class="news section" id="news">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e( 'Latest News', 'mammuts' ); ?></h2>
            <p class="section-subtitle"><?php esc_html_e( 'Stay up to date', 'mammuts' ); ?></p>
        </div>

        <div class="news-grid">
            <?php
            while ( $news_query->have_posts() ) :
                $news_query->the_post();
                mammuts_news_card();
            endwhile;
            wp_reset_postdata();
            ?>
        </div>

        <div style="text-align:center;margin-top:40px;">
            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="btn btn--outline">
                <?php esc_html_e( 'All News', 'mammuts' ); ?>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ===== STATIC CONTENT (if front page has content) ===== -->
<?php
if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $content = get_the_content();
        if ( ! empty( trim( $content ) ) ) : ?>
            <section class="section page-content">
                <div class="container">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </section>
        <?php endif;
    endwhile;
endif;
?>

<?php get_footer(); ?>
