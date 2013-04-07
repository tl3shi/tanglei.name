<?php get_header(); ?>
  <div id="content">
  
  <div class="post-nav"> <span class="previous"><?php previous_post_link('%link') ?></span> <span class="next"><?php next_post_link('%link') ?></span></div>
  
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  
    <div class="post" id="post-<?php the_ID(); ?>">
        <h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
		
		<div class="postdata"><span class="left mini-category">Filed in: <?php the_category(', ') ?></span> <span class="right mini-add-comment"><a href="#respond">Add comments</a></span></div>
		
		<div class="entry">
		<?php the_content('<p class="serif">Continue reading &raquo;</p>'); ?>
		
		<?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
		
		<?php edit_post_link('Edit', '', ''); ?>
		
		</div><!--/entry -->
		
		<?php comments_template(); ?>
		
			<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

	</div><!--/post -->

  </div><!--/content -->
  
  <div id="footer"><a href="http://www.ndesign-studio.com/resources/wp-themes/">WP Theme</a> &amp; <a href="http://www.ndesign-studio.com/stock-icons/">Icons</a> by <a href="http://www.ndesign-studio.com">N.Design Studio</a></div>
</div><!--/left-col -->

<?php get_sidebar(); ?>
  
<?php get_footer(); ?>

