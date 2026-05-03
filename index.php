<?php
/**
 * The main template file (blog index)
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner( __( 'News', 'mammuts' ) ); ?>

<section class="news section">
    <div class="container">
        <?php if ( have_posts() ) : ?>
            <div class="news-grid">
                <?php
                while ( have_posts() ) :
                    the_post();
                    mammuts_news_card();
                endwhile;
                ?>
            </div>

            <div style="text-align:center;margin-top:48px;">
                <?php
                the_posts_pagination( array(
                    'prev_text' => '&larr; ' . __( 'Previous', 'mammuts' ),
                    'next_text' => __( 'Next', 'mammuts' ) . ' &rarr;',
                ) );
                ?>
            </div>
        <?php else : ?>
            <p style="text-align:center;color:var(--color-text-muted);">
                <?php esc_html_e( 'No posts found.', 'mammuts' ); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
