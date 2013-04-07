<?php get_header(); ?>
<div id="content-padding">
<!-- change by tanglei -->

<!--
  <div id="content">
      
    <div class="post">
        <h2>Error 404 - Not Found</h2>
		
		<div class="entry">
		<p>Sorry, the page that you are looking for does not exist.</p>	
		</div><!--/entry -->
	
	</div><!--/post -->
	
  </div><!--/content -->



<STYLE type=text/css>A {
	TEXT-DECORATION: none
}
A:link {
	COLOR: #001111; FONT-FAMILY: 宋体; TEXT-DECORATION: none
}
A:visited {
	COLOR: #001111; FONT-FAMILY: 宋体; TEXT-DECORATION: none
}
A:active {
	FONT-FAMILY: 宋体; TEXT-DECORATION: none
}
A:hover {
	BORDER-TOP-WIDTH: 1px; BORDER-LEFT-WIDTH: 1px; FONT-SIZE: 12px; COLOR: #ff0000; BORDER-BOTTOM: 1px dotted; BORDER-RIGHT-WIDTH: 1px; TEXT-DECORATION: none
}
BODY {
	SCROLLBAR-FACE-COLOR: #e8e7e7; FONT-SIZE: 12px; SCROLLBAR-HIGHLIGHT-COLOR: #ffffff; SCROLLBAR-SHADOW-COLOR: #ffffff; COLOR: #001111; SCROLLBAR-3DLIGHT-COLOR: #cccccc; SCROLLBAR-ARROW-COLOR: #ff6600; SCROLLBAR-TRACK-COLOR: #efefef; FONT-FAMILY: 宋体; SCROLLBAR-DARKSHADOW-COLOR: #b2b2b2; SCROLLBAR-BASE-COLOR: #000000; BACKGROUND-COLOR: #ffffff
}
TABLE {
	FONT-SIZE: 9pt; FONT-FAMILY: 宋体; BORDER-COLLAPSE: collapse
}
.button {
	BORDER-RIGHT: #5589aa 1px solid; BORDER-TOP: #5589aa 1px solid; FONT-SIZE: 9pt; BACKGROUND: url(image/ButtonBg.gif) #f6f6f9; BORDER-LEFT: #5589aa 1px solid; WIDTH: 50px; COLOR: #000000; BORDER-BOTTOM: #5589aa 1px solid; HEIGHT: 18px
}
.lanyu {
	BORDER-RIGHT: #5589aa 1px solid; BORDER-TOP: #5589aa 1px solid; FONT-SIZE: 12px; BORDER-LEFT: #5589aa 1px solid; COLOR: #000000; BORDER-BOTTOM: #5589aa 1px solid
}
.font {
	FONT-SIZE: 9pt; FILTER: DropShadow(Color=#cccccc, OffX=2, OffY=1, Positive=2); TEXT-DECORATION: none
}
</STYLE>

<TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%" align=center border=0  bgColor=#ffffff>
  <TBODY>
  <TR>
    <TD vAlign=center align=middle>
      <DIV align=center>
      <CENTER>
      <TABLE style="BORDER-COLLAPSE: collapse" borderColor=#111111 
      cellSpacing=0 cellPadding=0 width=700 border=0>
        <TBODY>
        <TR>
          <TD vAlign=center width=700>
            <P align=center><IMG src="http://www.tanglei.name/wp-content/uploads/2011/07/404.jpg" border=0></P>
            <P align=center>
	<SPAN lang=zh-cn><B><FONT color=#ff0000>出错啦：</FONT></B>
	<FONT color=#ff0000>文件没找到！<br/></FONT>
1、可能是真的从来都没有这个文件&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br/>
2、可能是曾经有，现在木有了&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>
3、可能是曾经有，现在博主故意隐藏起来了 ：)</SPAN>
</P>
            <P align=center><FONT color=#ff0000><B>建议：&nbsp;&nbsp; 
            </B>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            </FONT></P>
            <P align=center>1.你可以刷新（F5）下下看看，不过估计效果不大。<BR/>
			    2.试着跟博主留言，索取你想要的文件。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <BR/>
			    3.返回主页咯,主页地址：<FONT 
            color=#ff0000> <A href="http://www.tanglei.name/"><U><FONT 
            color=#0000ff>www.tanglei.name</FONT></U></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            </FONT></P>
           
            <P 
  align=center>　</P></TD></TR></TBODY></TABLE></CENTER></DIV></TD></TR></TBODY></TABLE>





<!-- change by tanglei -->

<?php
include_once("real-footer.php");
?>		
	</div><!--/left-col -->

<?php 
$current_page = $post->ID; // Hack to prevent the no sidebar error
include_once("sidebar-right.php"); 
?>
  
<?php get_footer(); ?>