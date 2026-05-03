<?php
/**
 * Sidebar Template
 *
 * @package Mammuts
 */

if ( ! is_active_sidebar( 'sidebar-main' ) ) {
    return;
}
?>

<aside id="secondary" class="widget-area" role="complementary">
    <?php dynamic_sidebar( 'sidebar-main' ); ?>
</aside>
