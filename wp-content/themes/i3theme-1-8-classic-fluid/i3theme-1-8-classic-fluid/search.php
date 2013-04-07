<?php get_header(); ?>
<div id="content-padding">
      <div id="content">
<?php if (have_posts()) : ?>
	
		<?php while (have_posts()) : the_post(); ?>

        <div class="post" id="post-<?php the_ID(); ?>">
		  <table id="post-head">
		  <tr>
		  	<td id="head-date">
			  <div class="date"><span><?php the_time('M') ?></span> <?php the_time('d') ?></div>
		  	</td>
		  	<td>
		  <div class="title">
          <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
          <div class="postdata"><span class="category"><?php the_category(', ') ?></span> <span class="comments"><?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></span></div>
		  </div>
		  	</td>
		  </tr>
		  </table>
		  
          <div class="entry">
            <?php the_excerpt(); ?>
            
            <p class="submeta"><strong><font color="blue">Author:</font> <?php the_author(); ?></strong>
<br/>
            <?php 
				if(function_exists("the_tags"))
					the_tags('<strong><font color="blue">Tags:</font></strong> ', ', ', '<br />'); 					
				?>
          </div><!--/entry -->
        </div><!--/post -->

		<?php endwhile; ?>
		<div class="page-nav-left">
		<div class="page-nav-right">
    <!--add by tl3shi-->

   <div class="page-nav">
<center>
 <?php if(function_exists('wp_pagenavi')){ 
wp_pagenavi(); } else { ?> <div class="prev">
 <?php next_posts_link(__('? Previous Entries')) ?>
 </div> <div class="next">
<?php previous_posts_link(__('Next Entries ?')) ?></div>
  <?php } ?> 
</center>
</div></div>
       

 </div><!-- /page nav -->

	<?php else : ?>

		<h2>Nothing Found</h2>
		<p>Please try another search.</p>

	<?php endif; ?>

      </div><!--/content -->
<?php
include_once("real-footer.php");
?>
    </div><!--/left-col -->

<?php 
$current_page = $post->ID; // Hack to prevent the no sidebar error
include_once("sidebar-right.php"); 
?>


<?php get_footer(); ?>