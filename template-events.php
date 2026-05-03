<?php
/**
 * Template Name: Vereins-Veranstaltungen
 * Description: Displays upcoming and past club events as styled cards.
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="club-events section">
    <div class="container">
        <?php
        $upcoming = mammuts_get_upcoming_club_events( 20 );
        $today    = current_time( 'Y-m-d' );

        // Also get past events
        $past = get_posts( array(
            'post_type'      => 'mammuts_club_event',
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'meta_key'       => '_mammuts_event_date',
            'orderby'        => 'meta_value',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'     => '_mammuts_event_date',
                    'value'   => $today,
                    'compare' => '<',
                    'type'    => 'DATE',
                ),
            ),
        ) );
        ?>

        <?php if ( ! empty( $upcoming ) ) : ?>
            <div class="section-header" style="margin-bottom:32px;">
                <h2 class="section-title"><?php esc_html_e( 'Kommende Veranstaltungen', 'mammuts' ); ?></h2>
            </div>

            <div class="club-events-grid">
                <?php foreach ( $upcoming as $event ) :
                    mammuts_club_event_card( $event->ID, 'upcoming' );
                endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $past ) ) : ?>
            <div class="section-header" style="margin-top:60px;margin-bottom:32px;">
                <h2 class="section-title" style="opacity:0.6;"><?php esc_html_e( 'Vergangene Veranstaltungen', 'mammuts' ); ?></h2>
            </div>

            <div class="club-events-grid club-events-grid--past">
                <?php foreach ( $past as $event ) :
                    mammuts_club_event_card( $event->ID, 'past' );
                endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( empty( $upcoming ) && empty( $past ) ) : ?>
            <p style="text-align:center;color:var(--color-text-muted);padding:40px 0;">
                <?php esc_html_e( 'Noch keine Veranstaltungen eingetragen.', 'mammuts' ); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
