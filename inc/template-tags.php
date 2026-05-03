<?php
/**
 * Custom template tags for the Mammuts theme.
 *
 * @package Mammuts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Output social media links from Customizer
 */
function mammuts_social_links() {
    $socials = array(
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'twitter'   => 'X / Twitter',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
    );

    $icons = array(
        'facebook'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'instagram' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
        'twitter'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'youtube'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'tiktok'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
    );

    echo '<div class="footer-social">';
    foreach ( $socials as $key => $label ) {
        $url = get_theme_mod( "mammuts_social_{$key}", '' );
        if ( $url ) {
            printf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">%s</a>',
                esc_url( $url ),
                esc_attr( $label ),
                $icons[ $key ]
            );
        }
    }
    echo '</div>';
}

/**
 * Render a single match card
 */
function mammuts_match_card( $event_id, $type = 'result' ) {
    if ( ! mammuts_has_sportspress() ) {
        return;
    }

    $event   = new SP_Event( $event_id );
    $results = $event->results();
    $teams   = get_post_meta( $event_id, 'sp_team', false );
    $venue   = wp_get_post_terms( $event_id, 'sp_venue' );

    // Filter out empty team IDs
    $teams = array_filter( $teams );
    $teams = array_values( $teams );

    $is_featured = ( $type === 'next' );
    $card_class  = 'match-card' . ( $is_featured ? ' match-card--featured' : '' );
    $event_url   = get_permalink( $event_id );
    ?>
    <a href="<?php echo esc_url( $event_url ); ?>" id="event-<?php echo esc_attr( $event_id ); ?>" class="<?php echo esc_attr( $card_class ); ?>">
        <span class="match-label <?php echo $is_featured ? 'match-label--next' : 'match-label--result'; ?>">
            <?php
            if ( $type === 'next' ) {
                esc_html_e( 'Next Game', 'mammuts' );
            } elseif ( $type === 'upcoming' ) {
                esc_html_e( 'Upcoming', 'mammuts' );
            } else {
                esc_html_e( 'Result', 'mammuts' );
            }
            ?>
        </span>

        <div class="match-date">
            <?php echo get_the_date( 'd.m', $event_id ); ?>
            <span class="match-year"><?php echo get_the_date( 'Y', $event_id ); ?></span>
        </div>
        <div class="match-time"><?php echo get_the_time( 'H:i', $event_id ); ?> Uhr</div>

        <?php if ( ! empty( $teams ) && count( $teams ) >= 2 ) : ?>
            <div class="match-teams">
                <div class="match-team">
                    <?php mammuts_team_logo( $teams[0] ); ?>
                    <span class="match-team-name"><?php echo esc_html( get_the_title( $teams[0] ) ); ?></span>
                </div>

                <?php if ( $type === 'result' && ! empty( $results ) ) : ?>
                    <span class="match-score">
                        <?php
                        $scores = mammuts_get_event_scores( $results, $teams );
                        echo esc_html( implode( ' : ', $scores ) );
                        ?>
                    </span>
                <?php else : ?>
                    <span class="match-vs">VS</span>
                <?php endif; ?>

                <div class="match-team">
                    <?php mammuts_team_logo( $teams[1] ); ?>
                    <span class="match-team-name"><?php echo esc_html( get_the_title( $teams[1] ) ); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $venue ) ) : ?>
            <div class="match-venue"><?php echo esc_html( $venue[0]->name ); ?></div>
        <?php endif; ?>
    </a>
    <?php
}

/**
 * Get the actual scores from SportsPress event results.
 * For American Football: quarters (1,2,3,4,ot) + total (t).
 * If total is empty, we sum all numeric quarter values.
 */
function mammuts_get_event_scores( $results, $teams ) {
    $scores = array();

    foreach ( $teams as $team_id ) {
        $team_id = intval( $team_id );
        if ( isset( $results[ $team_id ] ) && is_array( $results[ $team_id ] ) ) {
            $team_result = $results[ $team_id ];

            // Priority 1: Look for total/summary columns
            $total_keys = array( 't', 'total', 'points', 'pts', 'score', 'goals' );
            $found = false;

            foreach ( $total_keys as $key ) {
                if ( isset( $team_result[ $key ] ) && $team_result[ $key ] !== '' && $team_result[ $key ] !== null && $team_result[ $key ] !== '0' ) {
                    $scores[] = $team_result[ $key ];
                    $found = true;
                    break;
                }
            }

            // Priority 2: Sum all numeric values (excluding 'outcome')
            if ( ! $found ) {
                $sum = 0;
                $has_values = false;
                foreach ( $team_result as $key => $value ) {
                    if ( $key === 'outcome' ) {
                        continue;
                    }
                    if ( is_numeric( $value ) ) {
                        $sum += intval( $value );
                        $has_values = true;
                    }
                }
                $scores[] = $has_values ? strval( $sum ) : '0';
            }
        } else {
            $scores[] = '0';
        }
    }

    return $scores;
}

/**
 * Render a team logo.
 * SportsPress stores logo as the post thumbnail of the team post,
 * or as sp_logo meta (attachment ID).
 */
function mammuts_team_logo( $team_id ) {
    $logo_html = '';

    // Method 1: Team has a featured image (post thumbnail)
    if ( has_post_thumbnail( $team_id ) ) {
        $logo_html = get_the_post_thumbnail( $team_id, 'medium', array(
            'class' => 'match-team-logo-img',
        ) );
    }

    // Method 2: sp_logo meta (some SportsPress setups)
    if ( ! $logo_html ) {
        $logo_id = get_post_meta( $team_id, 'sp_logo', true );
        if ( $logo_id ) {
            $logo_html = wp_get_attachment_image( $logo_id, 'medium', false, array(
                'class' => 'match-team-logo-img',
            ) );
        }
    }

    // Method 3: sp_url or custom field with URL
    if ( ! $logo_html ) {
        $logo_url = get_post_meta( $team_id, 'sp_url', true );
        if ( $logo_url ) {
            $logo_html = '<img src="' . esc_url( $logo_url ) . '" class="match-team-logo-img" alt="">';
        }
    }

    echo '<div class="match-team-logo">';
    if ( $logo_html ) {
        echo $logo_html;
    } else {
        // Fallback: show first letter
        $initial = mb_substr( get_the_title( $team_id ), 0, 1 );
        echo '<span style="font-family:var(--font-display);font-size:1.4rem;font-weight:700;color:var(--color-text-muted);">' . esc_html( $initial ) . '</span>';
    }
    echo '</div>';
}

/**
 * Render a player card
 */
function mammuts_player_card( $player_id ) {
    if ( ! mammuts_has_sportspress() ) {
        return;
    }

    $positions  = wp_get_post_terms( $player_id, 'sp_position' );
    if ( is_wp_error( $positions ) ) {
        $positions = array();
    }

    // Sort positions by SportsPress "Reihenfolge" (term_order)
    if ( count( $positions ) > 1 ) {
        global $wpdb;
        $pos_ids = wp_list_pluck( $positions, 'term_id' );
        $ids_sql = implode( ',', array_map( 'intval', $pos_ids ) );

        $wpdb->suppress_errors( true );
        $rows = $wpdb->get_results(
            "SELECT term_id, term_order FROM {$wpdb->terms}
             WHERE term_id IN ({$ids_sql})"
        );
        $wpdb->suppress_errors( false );

        $order_map = array();
        if ( ! empty( $rows ) && ! $wpdb->last_error ) {
            foreach ( $rows as $row ) {
                $order_map[ (int) $row->term_id ] = (int) $row->term_order;
            }
        }

        $all_zero = ! empty( $order_map ) && count( array_unique( $order_map ) ) === 1 && reset( $order_map ) === 0;

        if ( ! empty( $order_map ) && ! $all_zero ) {
            usort( $positions, function( $a, $b ) use ( $order_map ) {
                // term_order 0 means "not set" → sort to end
                $oa = isset( $order_map[ $a->term_id ] ) && $order_map[ $a->term_id ] > 0 ? $order_map[ $a->term_id ] : 9999;
                $ob = isset( $order_map[ $b->term_id ] ) && $order_map[ $b->term_id ] > 0 ? $order_map[ $b->term_id ] : 9999;
                return $oa - $ob;
            } );
        }
    }

    $number     = get_post_meta( $player_id, 'sp_number', true );
    $link_enabled = get_theme_mod( 'mammuts_player_link_enabled', true );

    // Check if player has a description (post content)
    $player_content = get_post_field( 'post_content', $player_id );
    $has_description = ! empty( trim( $player_content ) );

    // Gather data for the modal
    $metrics = get_post_meta( $player_id, 'sp_metrics', true );
    $height  = is_array( $metrics ) && isset( $metrics['height'] ) ? $metrics['height'] : '';
    $weight  = is_array( $metrics ) && isset( $metrics['weight'] ) ? $metrics['weight'] : '';
    $teams   = wp_get_post_terms( $player_id, 'sp_team' );
    $team_name = ! empty( $teams ) ? implode( ', ', wp_list_pluck( $teams, 'name' ) ) : '';
    $pos_name  = ! empty( $positions ) ? implode( ' / ', wp_list_pluck( $positions, 'name' ) ) : '';
    $thumbnail_url = get_the_post_thumbnail_url( $player_id, 'mammuts-player' );

    if ( $has_description ) {
        // Card opens a modal popup
        $tag_open  = '<div class="player-card player-card--has-popup" role="button" tabindex="0"'
            . ' data-popup-type="player"'
            . ' data-popup-name="' . esc_attr( get_the_title( $player_id ) ) . '"'
            . ' data-popup-number="' . esc_attr( $number ) . '"'
            . ' data-popup-position="' . esc_attr( $pos_name ) . '"'
            . ' data-popup-team="' . esc_attr( $team_name ) . '"'
            . ' data-popup-height="' . esc_attr( $height ) . '"'
            . ' data-popup-weight="' . esc_attr( $weight ) . '"'
            . ' data-popup-image="' . esc_attr( $thumbnail_url ?: '' ) . '"'
            . ' data-popup-content="' . esc_attr( wp_kses_post( apply_filters( 'the_content', $player_content ) ) ) . '"'
            . ' data-popup-link="' . esc_attr( get_permalink( $player_id ) ) . '"'
            . '>';
        $tag_close = '</div>';
    } elseif ( $link_enabled ) {
        $tag_open  = '<a href="' . get_permalink( $player_id ) . '" class="player-card">';
        $tag_close = '</a>';
    } else {
        $tag_open  = '<div class="player-card player-card--no-link">';
        $tag_close = '</div>';
    }
    ?>
    <?php echo $tag_open; ?>
        <div class="player-image">
            <?php
            if ( has_post_thumbnail( $player_id ) ) {
                echo get_the_post_thumbnail( $player_id, 'mammuts-player' );
            } else {
                echo '<div style="width:100%;height:100%;background:var(--color-bg-secondary);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:4rem;color:var(--color-border-light);">' . esc_html( $number ?: '?' ) . '</div>';
            }
            ?>
            <?php if ( $number ) : ?>
                <span class="player-number">#<?php echo esc_html( $number ); ?></span>
            <?php endif; ?>
            <?php if ( ! empty( $positions ) ) : ?>
                <div class="player-position-badge">
                    <span><?php echo esc_html( $positions[0]->name ); ?></span>
                </div>
            <?php endif; ?>
        </div>
        <div class="player-info">
            <h3 class="player-name"><?php echo esc_html( get_the_title( $player_id ) ); ?></h3>
            <div class="player-meta">
                <span>
                <?php
                $meta_parts = array();
                if ( ! empty( $positions ) ) {
                    foreach ( $positions as $pos ) {
                        $meta_parts[] = $pos->name;
                    }
                }
                if ( $number ) {
                    $meta_parts[] = '#' . $number;
                }
                echo esc_html( implode( ' · ', $meta_parts ) );
                ?>
                </span>
            </div>
            <?php if ( $has_description ) : ?>
                <span class="player-has-bio-hint" aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                </span>
            <?php endif; ?>
        </div>
    <?php echo $tag_close; ?>
    <?php
}

/**
 * Render a news card (post)
 */
function mammuts_news_card( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    ?>
    <article class="news-card" id="post-<?php echo intval( $post_id ); ?>">
        <?php if ( has_post_thumbnail( $post_id ) ) : ?>
            <a href="<?php echo get_permalink( $post_id ); ?>" class="news-card-image">
                <?php echo get_the_post_thumbnail( $post_id, 'mammuts-news-card' ); ?>
            </a>
        <?php endif; ?>
        <div class="news-card-body">
            <div class="news-card-meta">
                <?php
                $categories = get_the_category( $post_id );
                if ( ! empty( $categories ) ) : ?>
                    <span class="news-card-category"><?php echo esc_html( $categories[0]->name ); ?></span>
                <?php endif; ?>
                <span><?php echo get_the_date( 'd.m.Y', $post_id ); ?></span>
            </div>
            <h3 class="news-card-title">
                <a href="<?php echo get_permalink( $post_id ); ?>"><?php echo get_the_title( $post_id ); ?></a>
            </h3>
            <p class="news-card-excerpt"><?php echo get_the_excerpt( $post_id ); ?></p>
            <a href="<?php echo get_permalink( $post_id ); ?>" class="news-card-link">
                <?php esc_html_e( 'Read More', 'mammuts' ); ?> →
            </a>
        </div>
    </article>
    <?php
}

/**
 * Render a club event card
 *
 * @param int    $event_id  Post ID
 * @param string $context   'upcoming' or 'past'
 */
function mammuts_club_event_card( $event_id, $context = 'upcoming' ) {
    $date     = get_post_meta( $event_id, '_mammuts_event_date', true );
    $time     = get_post_meta( $event_id, '_mammuts_event_time', true );
    $location = get_post_meta( $event_id, '_mammuts_event_location', true );
    $datetime = mammuts_club_event_datetime( $event_id );
    $is_future = ! empty( $date ) && $date >= current_time( 'Y-m-d' );

    // Formatted date parts for the date badge
    $day   = ! empty( $date ) ? date_i18n( 'd', strtotime( $date ) ) : '';
    $month = ! empty( $date ) ? date_i18n( 'M', strtotime( $date ) ) : '';
    $year  = ! empty( $date ) ? date_i18n( 'Y', strtotime( $date ) ) : '';
    $weekday = ! empty( $date ) ? date_i18n( 'D', strtotime( $date ) ) : '';
    ?>
    <a href="<?php echo esc_url( get_permalink( $event_id ) ); ?>"
       class="club-event-card<?php echo $context === 'past' ? ' club-event-card--past' : ''; ?>">

        <div class="club-event-card-date">
            <span class="club-event-card-day"><?php echo esc_html( $day ); ?></span>
            <span class="club-event-card-month"><?php echo esc_html( strtoupper( $month ) ); ?></span>
            <?php if ( $is_future && ! empty( $datetime ) ) : ?>
                <span class="club-event-card-badge"><?php esc_html_e( 'Bald', 'mammuts' ); ?></span>
            <?php endif; ?>
        </div>

        <div class="club-event-card-body">
            <h3 class="club-event-card-title"><?php echo esc_html( get_the_title( $event_id ) ); ?></h3>

            <div class="club-event-card-meta">
                <?php if ( ! empty( $time ) ) : ?>
                    <span class="club-event-card-meta-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?php echo esc_html( $time ); ?> Uhr
                    </span>
                <?php endif; ?>
                <?php if ( ! empty( $location ) ) : ?>
                    <span class="club-event-card-meta-item">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?php echo esc_html( $location ); ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php
            $excerpt = get_the_excerpt( $event_id );
            if ( ! empty( $excerpt ) ) : ?>
                <p class="club-event-card-excerpt"><?php echo esc_html( wp_trim_words( $excerpt, 15 ) ); ?></p>
            <?php endif; ?>
        </div>

        <div class="club-event-card-arrow">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </div>
    </a>
    <?php
}
