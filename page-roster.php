<?php
/**
 * Template Name: Roster / Team
 * Description: Displays a SportsPress player list as a styled card grid.
 *              Set the team via the page content using SportsPress shortcodes,
 *              or the template auto-detects from the page slug.
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="roster section">
    <div class="container">
        <?php
        while ( have_posts() ) :
            the_post();
            $content = get_the_content();

            if ( ! empty( trim( $content ) ) ) :
                // Page has content (likely SportsPress shortcodes)
                ?>
                <div class="entry-content roster-shortcode-content">
                    <?php the_content(); ?>
                </div>
            <?php
            elseif ( mammuts_has_sportspress() ) :
                // Try to detect team: 1) Metabox filter, 2) Page slug match
                $team_id = 0;

                // 1. Metabox filter
                $filter_team = get_post_meta( get_the_ID(), '_mammuts_sp_team', true );
                if ( ! empty( $filter_team ) ) {
                    $team_id = intval( $filter_team );
                }

                // 2. Fallback: auto-detect from page slug
                if ( ! $team_id ) {
                    $slug  = get_post_field( 'post_name', get_the_ID() );
                    $teams = get_posts( array(
                        'post_type'      => 'sp_team',
                        'posts_per_page' => 1,
                        'name'           => $slug,
                    ) );
                    if ( ! empty( $teams ) ) {
                        $team_id = $teams[0]->ID;
                    }
                }

                if ( $team_id ) :
                    $players = get_posts( array(
                        'post_type'      => 'sp_player',
                        'posts_per_page' => -1,
                        'orderby'        => 'meta_value_num',
                        'meta_key'       => 'sp_number',
                        'order'          => 'ASC',
                        'meta_query'     => array(
                            array(
                                'key'   => 'sp_team',
                                'value' => $team_id,
                            ),
                        ),
                    ) );

                    if ( ! empty( $players ) ) :
                        // Collect positions that exist in THIS team only
                        $team_positions = array();
                        foreach ( $players as $player ) {
                            $pp = wp_get_post_terms( $player->ID, 'sp_position' );
                            foreach ( $pp as $p ) {
                                $team_positions[ $p->slug ] = $p;
                            }
                        }

                        // Position filter (only relevant positions)
                        if ( count( $team_positions ) > 1 ) : ?>
                            <div class="roster-filters">
                                <button class="roster-filter active" data-filter="all">
                                    <?php esc_html_e( 'All', 'mammuts' ); ?>
                                </button>
                                <?php foreach ( $team_positions as $pos ) : ?>
                                    <button class="roster-filter" data-filter="<?php echo esc_attr( $pos->slug ); ?>">
                                        <?php echo esc_html( $pos->name ); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="roster-grid" id="roster-grid">
                            <?php
                            foreach ( $players as $player ) :
                                $player_positions = wp_get_post_terms( $player->ID, 'sp_position' );
                                $pos_slugs = wp_list_pluck( $player_positions, 'slug' );
                                ?>
                                <div class="player-card-wrapper" data-positions="<?php echo esc_attr( implode( ',', $pos_slugs ) ); ?>">
                                    <?php mammuts_player_card( $player->ID ); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p style="text-align:center;color:var(--color-text-muted);">
                            <?php esc_html_e( 'No players found for this team.', 'mammuts' ); ?>
                        </p>
                    <?php endif; ?>
                <?php else : ?>
                    <p style="text-align:center;color:var(--color-text-muted);">
                        <?php esc_html_e( 'Add a SportsPress player list shortcode to this page, or name the page slug to match a team.', 'mammuts' ); ?>
                    </p>
                <?php endif;
            endif;
        endwhile;
        ?>
    </div>
</section>

<?php get_footer(); ?>
