<?php
/**
 * Template Name: Ligatabelle / Standings
 * Description: Displays a SportsPress league table. Automatically finds the
 *              right table based on the Team & Liga filter set in the page sidebar.
 *              Falls back to page content (shortcodes) if present.
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="section standings-page">
    <div class="container">
        <?php
        while ( have_posts() ) :
            the_post();
            $content = get_the_content();

            if ( ! empty( trim( $content ) ) ) :
                // Page has content (likely a [team_standings ID] shortcode)
                ?>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            <?php
            elseif ( mammuts_has_sportspress() ) :
                // No content: auto-detect league table from metabox filter
                $filter_league = get_post_meta( get_the_ID(), '_mammuts_sp_league', true );
                $filter_team   = get_post_meta( get_the_ID(), '_mammuts_sp_team', true );
                $filter_season = get_post_meta( get_the_ID(), '_mammuts_sp_season', true );

                // Season: metabox or auto-detect
                $season_slug = ! empty( $filter_season ) ? $filter_season : mammuts_get_current_season_slug();

                // Build query to find matching sp_table posts
                $table_args = array(
                    'post_type'      => 'sp_table',
                    'posts_per_page' => 1,
                    'post_status'    => 'publish',
                );

                // Filter by league taxonomy
                if ( ! empty( $filter_league ) ) {
                    $table_args['tax_query'][] = array(
                        'taxonomy' => 'sp_league',
                        'field'    => 'slug',
                        'terms'    => $filter_league,
                    );
                }

                // Filter by season
                if ( ! empty( $season_slug ) ) {
                    $table_args['tax_query'][] = array(
                        'taxonomy' => 'sp_season',
                        'field'    => 'slug',
                        'terms'    => $season_slug,
                    );
                }

                // If we have both league and season filters, use AND relation
                if ( isset( $table_args['tax_query'] ) && count( $table_args['tax_query'] ) > 1 ) {
                    $table_args['tax_query']['relation'] = 'AND';
                }

                $tables = get_posts( $table_args );

                // Fallback: if no table found with season filter, try without season
                if ( empty( $tables ) && ! empty( $current_season ) ) {
                    unset( $table_args['tax_query'] );
                    if ( ! empty( $filter_league ) ) {
                        $table_args['tax_query'] = array(
                            array(
                                'taxonomy' => 'sp_league',
                                'field'    => 'slug',
                                'terms'    => $filter_league,
                            ),
                        );
                    }
                    $tables = get_posts( $table_args );
                }

                // Fallback 2: if team is set but no league, find any table containing this team
                if ( empty( $tables ) && ! empty( $filter_team ) ) {
                    $all_tables = get_posts( array(
                        'post_type'      => 'sp_table',
                        'posts_per_page' => 20,
                        'post_status'    => 'publish',
                    ) );
                    foreach ( $all_tables as $tbl ) {
                        $table_teams = get_post_meta( $tbl->ID, 'sp_team', false );
                        if ( in_array( $filter_team, $table_teams ) ) {
                            $tables = array( $tbl );
                            break;
                        }
                    }
                }

                if ( ! empty( $tables ) ) :
                    $table_id = $tables[0]->ID;
                    ?>
                    <div class="standings">
                        <div class="standings-header">
                            <h2 class="standings-title">
                                <?php echo esc_html( get_the_title( $table_id ) ); ?>
                            </h2>
                        </div>
                        <div class="sp-template-league-table">
                            <?php
                            // Use SportsPress shortcode to render the table
                            echo do_shortcode( '[team_standings ' . intval( $table_id ) . ']' );
                            ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div style="text-align:center;padding:40px 0;">
                        <p style="color:var(--color-text-muted);">
                            <?php esc_html_e( 'Keine Ligatabelle gefunden.', 'mammuts' ); ?>
                        </p>
                        <p style="color:var(--color-text-muted);font-size:0.85rem;margin-top:8px;">
                            <?php esc_html_e( 'Bitte wähle in der Seitenleiste (SportsPress Filter) die richtige Liga aus, oder füge einen [team_standings ID] Shortcode als Seiteninhalt ein.', 'mammuts' ); ?>
                        </p>
                    </div>
                <?php endif;

            endif;
        endwhile;
        ?>
    </div>
</section>

<?php get_footer(); ?>
