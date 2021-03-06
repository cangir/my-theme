<?php
/**
 * Template part for displaying page content in single.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package My/Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="entry-header">

        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

        <?php if ('post' === get_post_type()) : ?>
        <div class="entry-meta">

            <?php My\Theme\Templates::postedOn(); ?>

        </div><!-- .entry-meta -->

        <?php endif; ?>

    </header><!-- .entry-header -->

    <?php My\Theme\Templates::postThumbnail(); ?>

    <div class="entry-content">

        <?php the_content(); ?>

    </div><!-- .entry-content -->

    <footer class="entry-footer">

        <?php My\Theme\Templates::entryFooter(); ?>

    </footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->
