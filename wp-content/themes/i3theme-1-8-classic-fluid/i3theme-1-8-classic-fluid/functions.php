<?php
if ( function_exists('register_sidebars') )
	register_sidebars(2, array(
        'before_widget' => '<!--sidebox start --><div id="%1$s" class="dbx-box %2$s">',
        'after_widget' => '</div></div><!--sidebox end -->',
        'before_title' => '<h3 class="dbx-handle">',
        'after_title' => '</h3><div class="dbx-content">',
    ));
?>
<?php function widget_itheme_search() {
?><?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Search'), 'widget_itheme_search');
?><?php function widget_itheme_meta() {
?>
      <!--sidebox start -->
      <div id="meta" class="dbx-box">
        <h3 class="dbx-handle">Meta</h3>
        <div class="dbx-content">
          <ul>
          	  <?php wp_register('<li class="site_admin">','</li>'); ?>
              <li class="rss"><a href="<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a></li>
              <li class="rss"><a href="<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a></li>
              <li class="AlreadyHosting"><a href="http://www.alreadyhosting.com" title="Brought to you by: AlreadyHosting.com">Web Hosting</a></li>
              <li class="wordpress"><a href="http://www.wordpress.org" title="Powered by WordPress">WordPress</a></li>
              <li class="login"><?php wp_loginout(); ?></li>
          </ul>
        </div>
      </div>
      <!--sidebox end -->

<?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Meta'), 'widget_itheme_meta');
?><?php function widget_itheme_links() {
?>
      <!--sidebox start -->
      <div id="links" class="dbx-box">
        <h3 class="dbx-handle"><?php _e('Links'); ?></h3>
        <div class="dbx-content">
          <ul>
            <?php get_links('-1', '<li>', '</li>', '<br />', FALSE, 'id', FALSE, FALSE, -1, FALSE); ?>
          </ul>
        </div>
      </div>
      <!--sidebox end --><?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Links'), 'widget_itheme_links');
?><?php function widget_itheme_categories(){
?>
	  <!--sidebox start -->
      <div id="categories" class="dbx-box">
        <h3 class="dbx-handle"><?php _e('Categories'); ?></h3>
        <div class="dbx-content">
          <ul>
            <?php wp_list_cats('sort_column=name&optioncount=1&hierarchical=1'); ?>
          </ul>
        </div>
      </div>
      <!--sidebox end -->
<?php
}
if ( function_exists('register_sidebar_widget') )
    register_sidebar_widget(__('Categories'), 'widget_itheme_categories');
?><?php
function ping_comments ($comments)
{
  global $pings, $comment;
 
  // Initialise the variables
  $pings = $newcomments = array ();
 
  // Loop through existing comments
  foreach ($comments AS $comment)
  {
    if (get_comment_type () == 'comment')
      $newcomments[] = $comment;
    else
      $pings[] = $comment;
  }
 
  // Return the comments without any pings
  return $newcomments;
}
 
// Adjust comments number so comments_number() function is correct
function ping_comments_number ($num)
{
  global $pings;
  return $num - count ($pings);
}

// Display ping numbers
function pings_number($no_ping="No Pings", $one_ping="One Ping", $many_pings="% Pings")
{
	global $pings;
	$num_pings = count($pings);
	
	if ($num_pings == 0)
		echo $no_ping;
	else if ($num_pings == 1)
		echo $one_ping;
	else if ($num_pings > 1)
		echo str_replace("%", $num_pings, $many_pings);
}
 

//begin tanglei 相关文章
//WordPress Related Posts
 $wp_rp=array(
 'limit'=>5,                         //相关文章数量
 'wp_rp_rss'=>true,             //是否在feed 中显示相关文章
 'wp_no_rp'=>'random',      //无相关文章时的选择：text 或random（random-随机文章)
 'wp_rp_date'=>true,          //显示文章发布日期
 'wp_rp_comments'=>false,  //显示文章评论数
 'wp_rp_title_tag'=>'h3',      //选择相关文章标题标签(h2 ,h3 ,h4 ,p ,div)
 );
function wp_get_random_posts ($limitclause="") {
 global $wpdb, $post;

 $q = "SELECT ID, post_title, post_content,post_excerpt, post_date, comment_count FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND ID != $post->ID ORDER BY RAND() $limitclause";
 return $wpdb->get_results($q);
}
function wp_get_category_posts ($limitclause="") {
 global $wpdb, $post;

	//$cid = the_category_ID(false);
     $categories = get_the_category();
     $cid = $categories[0]->term_id;
		
 $q = "SELECT ID, post_title, post_content,post_excerpt, post_date, comment_count FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND ID != $post->ID AND ID in (select object_id  from wp_term_relationships where term_taxonomy_id = ( select term_taxonomy_id from wp_term_taxonomy where term_id = $cid )) ORDER BY RAND() $limitclause";

 return $wpdb->get_results($q);
}

 function wp_get_related_posts()
 { 
 global $wpdb, $post,$wp_rp;
 $limit =$wp_rp["limit"];
 $wp_rp_title='<hr size=1 style="border:1 dashed #5151A2">相关文章';    //相关文章标题
 if(!$post->ID){return;}
 $now = current_time('mysql', 1);
 $tags = wp_get_post_tags($post->ID);

 $taglist = "'" . $tags[0]->term_id. "'";

 $tagcount = count($tags);
 if ($tagcount > 1) {
 for ($i = 1; $i < $tagcount; $i++) {
 $taglist = $taglist . ", '" . $tags[$i]->term_id . "'";
 }
 }

 if ($limit) {
 $limitclause = "LIMIT $limit";
 }   else {
 $limitclause = "LIMIT 10";
 }

 $q = "SELECT p.ID, p.post_title, p.post_content,p.post_excerpt, p.post_date,  p.comment_count, count(t_r.object_id) as cnt FROM $wpdb->term_taxonomy t_t, $wpdb->term_relationships t_r, $wpdb->posts p WHERE t_t.taxonomy ='post_tag' AND t_t.term_taxonomy_id = t_r.term_taxonomy_id AND t_r.object_id  = p.ID AND (t_t.term_id IN ($taglist)) AND p.ID != $post->ID AND p.post_status = 'publish' AND p.post_date_gmt < '$now' GROUP BY t_r.object_id ORDER BY cnt DESC, p.post_date_gmt DESC $limitclause;";

 $related_posts = $wpdb->get_results($q);

 $output = "";

//不存在相关日志则显示随机日志

 if (!$related_posts)
 {
	 //tag搜索不到，再通过目录搜
	 $related_posts =  wp_get_category_posts($limitclause);
	 if (!$related_posts)
	 {
		 if($wp_rp['wp_no_rp'] == "text")

		 {
			 $output  .= '<li>木有相关文章</li>';            //无相关文章时显示标题
		 }
		 else
		{
			 if($wp_rp['wp_no_rp'] == "random")

		 {
			 $wp_no_rp_text= '随机文章';                                        //随机文显示标题
			 $related_posts = wp_get_random_posts($limitclause);
		 }  
		$wp_rp_title = $wp_no_rp_text;
		 }
	 }
 }

 foreach ($related_posts as $related_post )
 {
 $output .= '<li>';
 if($wp_rp['wp_rp_date'])
 {
 $dateformat = get_option('date_format');
 $output .= mysql2date($dateformat, $related_post->post_date) . "—";              //日期和文章标题间隔符，默认是 —
 }
 $output .=  '《<a href="'.get_permalink($related_post->ID).'" title="'.wptexturize($related_post->post_title).'">'.wptexturize($related_post->post_title).'</a>》';
 if ($wp_rp["wp_rp_comments"])
 {
 $output .=  " (" . $related_post->comment_count . ")";
 }
 $output .=  '</li>';
 }
 $output = '<ul>' . $output . '</ul>';
 $wp_rp_title_tag = $wp_rp["wp_rp_title_tag"];

 if(!$wp_rp_title_tag)
 $wp_rp_title_tag ='h3';
 if($wp_rp_title != '')
 $output =  '<'.$wp_rp_title_tag.' >'.$wp_rp_title .'</'.$wp_rp_title_tag.'>'. $output;
 return $output;
}

 function wp_related_posts_attach($content)
 {
 global $wp_rp;
 if (is_single()||(is_feed() && $wp_rp["wp_rp_rss"]))
 {
 $output = wp_get_related_posts();
 $content = $content.$output;
 }

 return $content;
 }
add_filter('the_content', 'wp_related_posts_attach',100);

//end tanglei 相关文章
//no self ping begin;
function no_self_ping( &$links ) {
 $home = get_option( 'home' );
 foreach ( $links as $l => $link )
 if ( 0 === strpos( $link, $home ) )
 unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );
//no self ping end;

// Hook into WordPress filters
add_filter ('get_comments_number', 'ping_comments_number');
add_filter ('comments_array', 'ping_comments');
?>