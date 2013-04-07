</td>

	<td class="sidebars">

<!-- 用text组件替换
<!-- tl3shi begin
	<iframe frameborder="0" scrolling="no" src="http://v.t.qq.com/show/show.php?n=tl3shi&w=210&h=552&fl=1&l=2&o=31&c=0&si=980ad9b2208025f72a4669cde4de601cb2891294" width="210" height="552"></iframe>
<!-- tl3shi end
-->

<div class="dbx-group" id="sidebar-right">
  <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(2) ) : ?>      

      <!--sidebox start -->
      <div id="links" class="dbx-box">
        <h3 class="dbx-handle"><?php _e('Links'); ?></h3>
        <div class="dbx-content">
          <ul>
            <?php get_links('-1', '<li>', '</li>', '<br />', FALSE, 'id', FALSE, FALSE, -1, FALSE); ?>
            <!-- It's sponsored links -->
            <li><a href="http://www.colombiahosting.com.co/" title="Colombia Hosting" target="_blank">Hosting</a> in Colombia.</li>
            <li><a href="http://www.web-hosting-top.com/" target="_blank">Web Hosting Reviews <br/>& Free
Coupons</a></li>
          </ul>
        </div>
      </div>
      <!--sidebox end -->



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

  <?php endif; ?>

</div><!--/sidebar -->
	</td>
</tr>
</table>
