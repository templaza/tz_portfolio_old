<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php
$list   = $this -> listsArticle;
$params = $this -> params;
?>
<?php if($list):?>
    <div id="TzTimline">
            <div class="TzTimlineWrap">
                <div class="ss-links" id="ss-links">
                    <?php echo $this -> loadTemplate('date');?>
                </div><!-- end main-menu -->
                <div id="ss-container" class="ss-container">
                    <?php echo $this -> loadTemplate('item');?>

                </div>
            </div>
    </div><!-- end contact -->


    <?php //if($params -> get('tz_timeline_layout','default') == 'default'):?>
        <div class="TzTimlinePagination">
            <?php echo $this -> pagination -> getPagesLinks();?>
        </div>
        <script type="text/javascript">
          var tz = jQuery.noConflict();

          next = tz('.TzTimlinePagination .pagination-next').html();
          preview = tz('.TzTimlinePagination .pagination-prev').html();

          tz('#ss-links').append(next);
          tz('#ss-links').append(preview);
        </script>
    <?php //endif;?>
<?php endif;?>