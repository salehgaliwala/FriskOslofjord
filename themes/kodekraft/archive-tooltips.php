<?php
/**
 * The template for displaying the "Tooltips" custom post type archive.
 * Template Name: Post Type Archive Tooltips
 *
 * @package YourTheme
 */

get_header(); ?>

<main id="primary" class="site-main">
    <header class="page-header">
        <h1 class="page-title">
            <?php echo 'Begrepsforklaring' ?>
        </h1>
        <?php if (is_tax() || is_category() || is_tag()): ?>
            <div class="taxonomy-description">
                <?php echo term_description(); ?>
            </div>
        <?php endif; ?>
    </header><!-- .page-header -->

    <?php if (have_posts()) : ?>
        <div class="post-list">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h2 class="entry-title" style="color:#EC5B6D">
                            <?php the_title(); ?>
                        </h2>
                    </header><!-- .entry-header -->

                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div><!-- .entry-content -->
                </article><!-- #post-<?php the_ID(); ?> -->
            <?php endwhile; ?>

            <?php
            the_posts_pagination(array(
                'prev_text' => __('Previous', 'yourtheme'),
                'next_text' => __('Next', 'yourtheme'),
            ));
            ?>
        </div><!-- .post-list -->
    <?php else : ?>
        <div class="no-posts">
            <p><?php _e('No tooltips found.', 'yourtheme'); ?></p>
        </div>
    <?php endif; ?>
</main><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
