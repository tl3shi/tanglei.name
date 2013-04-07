<div class="dbx-group" id="sidebar-left">

  <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(1) ) : ?>

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

	  <!--sidebox start -->
      <div id="archives" class="dbx-box">
        <h3 class="dbx-handle"><?php _e('Archives'); ?></h3>
        <div class="dbx-content">
          <ul>
            <?php wp_get_archives('type=monthly'); ?>
          </ul>
        </div>
      </div>
      <!--sidebox end -->

  <?php endif; ?>

</div><!--/sidebar-left -->
