<?php
/**
 * Page Template
 *
 * @package Mammuts
 */

get_header();
?>

<?php mammuts_page_header_banner(); ?>
<?php mammuts_subpage_nav(); ?>

<section class="page-content">
    <div class="container">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php get_footer(); ?>
