<?php
/**
 * Single Club Event Template
 *
 * @package Mammuts
 */

get_header();

$event_id = get_the_ID();
$date     = get_post_meta( $event_id, '_mammuts_event_date', true );
$time     = get_post_meta( $event_id, '_mammuts_event_time', true );
$end_date = get_post_meta( $event_id, '_mammuts_event_end_date', true );
$end_time = get_post_meta( $event_id, '_mammuts_event_end_time', true );
$location = get_post_meta( $event_id, '_mammuts_event_location', true );

// ── Find the events overview page (the page using template-events.php) ──
$events_page_url = '';

// 1. Published page with events template
$events_pages = get_pages( array(
    'meta_key'   => '_wp_page_template',
    'meta_value' => 'template-events.php',
    'number'     => 1,
) );
if ( ! empty( $events_pages ) ) {
    $events_page_url = get_permalink( $events_pages[0]->ID );
}

// 2. Fallback: also check drafted / private pages (get_pages only returns published)
if ( empty( $events_page_url ) ) {
    $events_posts = get_posts( array(
        'post_type'   => 'page',
        'post_status' => array( 'publish', 'draft', 'private' ),
        'meta_key'    => '_wp_page_template',
        'meta_value'  => 'template-events.php',
        'numberposts' => 1,
    ) );
    if ( ! empty( $events_posts ) && $events_posts[0]->post_status === 'publish' ) {
        $events_page_url = get_permalink( $events_posts[0]->ID );
    }
}

// 3. Fallback: post type archive
if ( empty( $events_page_url ) ) {
    $events_page_url = get_post_type_archive_link( 'mammuts_club_event' );
}

// 4. Last resort: home
if ( ! $events_page_url ) {
    $events_page_url = home_url( '/' );
}
?>

<nav class="sibling-nav news-back-nav" aria-label="<?php esc_attr_e( 'Zurück zu Veranstaltungen', 'mammuts' ); ?>">
    <div class="container">
        <div class="sibling-nav-inner">
            <a href="<?php echo esc_url( $events_page_url ); ?>" class="sibling-nav-parent">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                <span><?php esc_html_e( 'Veranstaltungen', 'mammuts' ); ?></span>
            </a>
            <div class="sibling-nav-scroll-wrap">
                <div class="sibling-nav-scroll">
                    <span class="news-back-title"><?php the_title(); ?></span>
                </div>
            </div>
        </div>
    </div>
</nav>

<article class="single-post single-club-event">
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="single-post-hero" style="aspect-ratio:21/9;overflow:hidden;max-height:480px;">
            <?php the_post_thumbnail( 'mammuts-hero', array( 'style' => 'width:100%;height:100%;object-fit:cover;' ) ); ?>
        </div>
    <?php else : ?>
        <div style="height:20px;"></div>
    <?php endif; ?>

    <div class="container">
        <div class="page-content" style="padding-top:40px;">
            <div class="entry-content">
                <?php while ( have_posts() ) : the_post(); ?>

                    <!-- Event meta bar -->
                    <div class="club-event-meta-bar">
                        <?php if ( ! empty( $date ) ) : ?>
                            <div class="club-event-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>
                                    <?php echo esc_html( date_i18n( 'l, d. F Y', strtotime( $date ) ) ); ?>
                                    <?php if ( ! empty( $time ) ) : ?>
                                        · <?php echo esc_html( $time ); ?> Uhr
                                    <?php endif; ?>
                                    <?php if ( ! empty( $end_date ) && $end_date !== $date ) : ?>
                                        – <?php echo esc_html( date_i18n( 'd. F Y', strtotime( $end_date ) ) ); ?>
                                        <?php if ( ! empty( $end_time ) ) : ?>
                                            · <?php echo esc_html( $end_time ); ?> Uhr
                                        <?php endif; ?>
                                    <?php elseif ( ! empty( $end_time ) ) : ?>
                                        – <?php echo esc_html( $end_time ); ?> Uhr
                                    <?php endif; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if ( ! empty( $location ) ) : ?>
                            <div class="club-event-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span><?php echo esc_html( $location ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h1 class="page-title" style="margin-bottom:32px;text-align:left;"><?php the_title(); ?></h1>

                    <?php the_content(); ?>

                <?php endwhile; ?>

                <?php // ── Venue / Location Map ── ?>
                <?php if ( ! empty( $location ) ) : ?>
                    <div class="event-venue-card" style="margin-top:48px;">
                        <h3 class="event-section-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <?php esc_html_e( 'Veranstaltungsort', 'mammuts' ); ?>
                        </h3>
                        <div class="event-venue-info">
                            <p class="event-venue-name"><?php echo esc_html( $location ); ?></p>
                        </div>

                        <div class="event-venue-map event-venue-map--embed">
                            <iframe width="100%" height="100%" style="border:0;" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://maps.google.com/maps?q=<?php echo urlencode( $location ); ?>&output=embed"
                                    allowfullscreen></iframe>
                        </div>

                        <div class="event-venue-actions">
                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode( $location ); ?>"
                               target="_blank" rel="noopener noreferrer" class="event-venue-directions">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                                <?php esc_html_e( 'Route planen', 'mammuts' ); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</article>

<?php get_footer(); ?>
