<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<META name=keywords content="软件工程,中南大学唐磊,软件学院,数据挖掘">
	<META name="description" content="中南大学软件学院唐磊的博客,主攻软件工程,数据挖掘,这里记录了我的学习，生活和工作，tanglei's blog"/>
	<META name="copyright" content=www.tanglei.name/>
	<META name="author" content="tanglei"/>
	<meta name="Robots" contect="all"/> 


<title>
	<?php 
	if(is_home()) {  
		echo get_bloginfo('description') . " | " . get_bloginfo('name'); 
	} 
	else { 
		echo wp_title('', false) . " |" . get_bloginfo('name'); 
	} 
	?>	
</title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/print.css" type="text/css" media="print" />

<!-- Sidebar docking boxes (dbx) by Brothercake - http://www.brothercake.com/ -->
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/dbx.js"></script>
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/dbx-key.js"></script>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/dbx.css" media="screen, projection" />

<!--[if lt IE 7]>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/ie-gif.css" type="text/css" />
<![endif]-->

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>
</head>
<body>
<div id="page">
  <div id="wrapper">
    <div id="header">
      <h1><a href="<?php echo get_settings('home'); ?>"><?php bloginfo('name'); ?></a></h1>
      <div class="description"><?php bloginfo('description'); ?></div>
      <?php include (TEMPLATEPATH . '/searchform.php'); ?>
    </div><!-- /header -->

<table id="layout">
<tr>
	<td class="sidebars">
	<?php 
	$current_page = $post->ID; // Hack to prevent the no sidebar error
	include_once("sidebar-left.php"); 
	$post->ID = $current_page;
	?>
	</td>
	
	<td>
    <div id="left-col">
   	  <div id="nav-left">
    	<div id="nav-right"> 
	      <div id="nav">
	        <ul>
	          <li class="page_item <?php if ( is_home() ) { ?>current_page_item<?php } ?>"><a href="<?php echo get_settings('home'); ?>/" title="Home">Home</a></li>

			  <?php $children = wp_list_categories('sort_column=menu_order&depth=1&exclude=1&title_li=');
wp_list_pages('depth=1&title_li=0&sort_column=menu_order');
?>


	        </ul>
	      </div><!-- /nav -->
      	</div>
      </div>

    <?php /* Menu for subpages of current page (thanks to K2 theme for this code) */
    global $notfound;
    if (is_page() and ($notfound != '1')) {
        // Code Remove: to prevent the no sidebar error.
        while($current_page) {
            $page_query = $wpdb->get_row("SELECT ID, post_title, post_status, post_parent FROM $wpdb->posts WHERE ID = '$current_page'");
            $current_page = $page_query->post_parent;
        }
        $parent_id = $page_query->ID;
        $parent_title = $page_query->post_title;

        
				// if ($wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = '$parent_id' AND post_status != 'attachment'")) {
        if ($wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = '$parent_id' AND post_type != 'attachment'")) {
        	
    ?>

    <div id="subnav">
      <ul>
      	<?php wp_list_pages('sort_column=menu_order&depth=1&title_li=&child_of='. $parent_id); ?>
      </ul>
    </div><!-- /sub nav -->

    <?php } } ?>
	