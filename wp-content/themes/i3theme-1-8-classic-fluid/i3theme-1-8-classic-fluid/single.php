<!--add by tl3shi -->
<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
  {lang: 'zh-CN'}
</script>

<?php get_header(); ?>
<div id="content-padding">
  <div id="content">
  
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  		
      <div class="post-nav"> 
         <span class="previous"><?php previous_post_link('%link') ?></span> 
         <span class="next"><?php next_post_link('%link') ?></span>
      </div>  
   
      <div class="post" id="post-<?php the_ID(); ?>" style="margin-top: 0;">
         <table id="post-head">
		 <tr>
		  	<td id="head-date">
         	<div class="date"><span><?php the_time('M') ?></span> 
         	<?php the_time('d') ?></div>
         	</td>
         	
         	<td>
         	 <div class="title">
	            <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="查看此文：<?php the_title(); ?>">
					<?php the_title(); ?></a>
<!-- add by tl3shi请将以下代码放在您希望呈现 +1 按钮的位置。 -->
<g:plusone size="medium"></g:plusone>
</h2>
	            <div class="postdata">
	               <span class="category"><?php the_category(', ') ?></span>
	               <span class="right mini-add-comment">
	               	  <a href="#respond">Add comments</a>
	               </span>
	            </div>
	         </div>
         	</td>
         </tr>
         </table>
         
         <div class="entry">


<font color="#aaffbb" style="font-size:10pt">
转载请注明来源：<a href='<?php the_permalink() ?>' style="color:#aaffbb">唐磊的个人博客《<?php the_title(); ?>》</a>
</font>
<!--
转载请注明来源：<a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a>——<a href="<?php the_permalink() ?>">《<?php the_title(); ?>》</a>

<div>
本文链接地址：<a href="<?php the_permalink() ?>"><?php the_permalink() ?></a>
</div>
-->

         <?php the_content('Continue reading &raquo;'); ?>

 
         <?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
         
         <p class="submeta"><strong><font color="blue">Author：</font><a href='http://www.tanglei.name/'><?php the_author(); ?></a></strong> 
        <br/> 
	<?php 
         if(function_exists("the_tags"))
        the_tags('<font color="blue"><strong>Tags: </strong></font> ', ', ', '<br />'); 										
         ?>
         </p>

         <?php edit_post_link('Edit', '', ''); ?>
         </div><!--/entry -->
       
         <?php comments_template(); ?>
      </div><!--/post -->
      
	<?php endwhile; else: ?>
                 
         <p style="margin-top:25px;">Sorry, no posts matched your criteria.</p>
         
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
<!--add 侧栏 tl3shi-->
<!-- JiaThis Button BEGIN -->
<script type="text/javascript" src="http://v2.jiathis.com/code/jiathis_r.js?move=0&amp;uid=895059" charset="utf-8"></script>
<!-- JiaThis Button END -->

  
<?php get_footer(); ?>

