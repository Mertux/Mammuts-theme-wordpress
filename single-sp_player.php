<?php
/**
 * Single Player Template (SportsPress)
 * Modern card-style layout with photo and stats.
 *
 * @package Mammuts
 */

get_header();

while ( have_posts() ) :
    the_post();

    $player_id = get_the_ID();
    $number    = get_post_meta( $player_id, 'sp_number', true );
    $positions = wp_get_post_terms( $player_id, 'sp_position' );
    $teams     = wp_get_post_terms( $player_id, 'sp_team' );

    $metrics = get_post_meta( $player_id, 'sp_metrics', true );
    $height  = is_array( $metrics ) && isset( $metrics['height'] ) ? $metrics['height'] : '';
    $weight  = is_array( $metrics ) && isset( $metrics['weight'] ) ? $metrics['weight'] : '';

    $pos_name  = ! empty( $positions ) ? implode( ' / ', wp_list_pluck( $positions, 'name' ) ) : '';
    $team_name = ! empty( $teams ) ? implode( ', ', wp_list_pluck( $teams, 'name' ) ) : '';
    $has_photo = has_post_thumbnail();
?>

<div class="sp-player-page">
    <!-- Player Header Card -->
    <div class="sp-player-header">
        <div class="container">
            <div class="sp-player-card <?php echo $has_photo ? 'has-photo' : 'no-photo'; ?>">

                <?php if ( $has_photo ) : ?>
                <div class="sp-player-photo">
                    <?php the_post_thumbnail( 'mammuts-player' ); ?>
                    <?php if ( $number ) : ?>
                        <span class="sp-player-photo-number">#<?php echo esc_html( $number ); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="sp-player-details">
                    <?php if ( $pos_name ) : ?>
                        <span class="sp-player-pos-badge"><?php echo esc_html( $pos_name ); ?></span>
                    <?php endif; ?>

                    <h1 class="sp-player-name">
                        <?php if ( $number && ! $has_photo ) : ?>
                            <span class="sp-player-inline-number">#<?php echo esc_html( $number ); ?></span>
                        <?php endif; ?>
                        <?php the_title(); ?>
                    </h1>

                    <?php if ( $team_name ) : ?>
                        <p class="sp-player-team"><?php echo esc_html( $team_name ); ?></p>
                    <?php endif; ?>

                    <!-- Inline Stats -->
                    <?php if ( $number || $height || $weight ) : ?>
                    <div class="sp-player-stats-inline">
                        <?php if ( $number ) : ?>
                        <div class="sp-player-stat">
                            <span class="sp-player-stat-val"><?php echo esc_html( $number ); ?></span>
                            <span class="sp-player-stat-lbl"><?php esc_html_e( 'Number', 'mammuts' ); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ( $height ) : ?>
                        <div class="sp-player-stat">
                            <span class="sp-player-stat-val"><?php echo esc_html( $height ); ?> cm</span>
                            <span class="sp-player-stat-lbl"><?php esc_html_e( 'Height', 'mammuts' ); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ( $weight ) : ?>
                        <div class="sp-player-stat">
                            <span class="sp-player-stat-val"><?php echo esc_html( $weight ); ?> kg</span>
                            <span class="sp-player-stat-lbl"><?php esc_html_e( 'Weight', 'mammuts' ); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Content & Statistics -->
    <div class="sp-player-body">
        <div class="container">
            <?php
            $content = get_the_content();
            if ( ! empty( trim( $content ) ) ) : ?>
                <div class="sp-player-bio">
                    <h2><?php esc_html_e( 'About', 'mammuts' ); ?></h2>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ( mammuts_has_sportspress() ) :
                // Get statistics via SportsPress object
                $sp_player = new SP_Player( $player_id );
                $data = $sp_player->data( 0, false );

                if ( ! empty( $data ) ) : ?>
                <div class="sp-player-statistics">
                    <h2><?php esc_html_e( 'Statistics', 'mammuts' ); ?></h2>
                    <?php sp_get_template( 'player-statistics.php', array( 'id' => $player_id ) ); ?>
                </div>
                <?php endif;
            endif; ?>
        </div>
    </div>
</div>

<?php
endwhile;
get_footer();
?>
