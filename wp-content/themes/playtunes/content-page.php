<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 */

global $SMTheme;
?>
        
		<article <?php post_class('article-box'); ?>>
		<h1 class="entry-title"><?php the_title(); ?></h1>

		<?php the_post_thumbnail(
			'post-thumbnail',
			array("class" => $SMTheme->get( 'layout','imgpos' ) . " featured_image")
		); ?>
		<div class="entry-content">
                <?php the_content( ); ?>
                <?php wp_link_pages( array(
                                        'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'letheme' ) . '</span>',
                                        'after'       => '</div>',
                                        'link_before' => '<span>',
                                        'link_after'  => '</span>',
                                ) ); ?>

        </div><!-- .entry-content -->
		<div class="clear"></div>
		</article>