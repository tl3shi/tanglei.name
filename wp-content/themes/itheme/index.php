<?php get_header(); ?>

      <div id="content">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

        <div class="post" id="post-<?php the_ID(); ?>">
          <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
          <div class="postdata"><span class="left"><?php the_time('F jS, Y') ?></span> <span class="right"><?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></span></div>
          <div class="entry">
            <?php the_content('Continue reading &raquo;'); ?>
          </div><!--/entry -->
        </div><!--/post -->

		<?php endwhile; ?>
		
        <div class="page-nav"> <span class="previous-entries"><?php next_posts_link('Previous Entries') ?></span> <span class="next-entries"><?php previous_posts_link('Next Entries') ?></span></div><!-- /page nav -->

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>

      </div><!--/content -->

      <div id="footer">Powered by <a href="http://www.wpcourse.com">WordPress</a></div>
    </div><!--/left-col -->

<?php get_sidebar(); ?>

<?php get_footer(); ?>