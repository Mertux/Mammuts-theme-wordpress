<?php
/**
 * Single Post Template
 *
 * @package Mammuts
 */

get_header();
?>

<article class="single-post">
    <?php
    // Back-to-news navigation bar
    $news_page_url = get_post_type_archive_link( 'post' );
    if ( ! $news_page_url ) {
        // Fallback: use the page set as "Posts page" in Settings → Reading
        $blog_page_id = get_option( 'page_for_posts' );
        $news_page_url = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/news/' );
    }
    $back_url = $news_page_url . '#post-' . get_the_ID();
    ?>
    <nav class="sibling-nav news-back-nav" aria-label="<?php esc_attr_e( 'Zurück zu News', 'mammuts' ); ?>">
        <div class="container">
            <div class="sibling-nav-inner">
                <a href="<?php echo esc_url( $back_url ); ?>" class="sibling-nav-parent">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    <span><?php esc_html_e( 'News', 'mammuts' ); ?></span>
                </a>
                <div class="sibling-nav-scroll-wrap">
                    <div class="sibling-nav-scroll">
                        <span class="news-back-title"><?php the_title(); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </nav>
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
                <?php
                while ( have_posts() ) :
                    the_post();
                    ?>
                    <div class="news-card-meta" style="margin-bottom:16px;">
                        <?php
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) : ?>
                            <span class="news-card-category"><?php echo esc_html( $categories[0]->name ); ?></span>
                        <?php endif; ?>
                        <span><?php echo get_the_date( 'd.m.Y' ); ?></span>
                    </div>

                    <h1 class="page-title" style="margin-bottom:32px;text-align:left;"><?php the_title(); ?></h1>

                    <?php the_content(); ?>

                    <div style="margin-top:48px;padding-top:24px;border-top:1px solid var(--color-border);">
                        <?php
                        the_post_navigation( array(
                            'prev_text' => '<span style="font-size:0.8rem;color:var(--color-text-muted);text-transform:uppercase;letter-spacing:0.1em;">' . __( 'Previous Post', 'mammuts' ) . '</span><br>%title',
                            'next_text' => '<span style="font-size:0.8rem;color:var(--color-text-muted);text-transform:uppercase;letter-spacing:0.1em;">' . __( 'Next Post', 'mammuts' ) . '</span><br>%title',
                        ) );
                        ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</article>

<?php get_footer(); ?>
