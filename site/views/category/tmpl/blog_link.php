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

//no direct access
defined('_JEXEC') or die();

$item       = $this -> item;
$media      = $this -> listMedia;
$params     = $this -> item -> params;

if(count($media)):
    if($media[0] -> type == 'link'):
?>
    <div class="TzLink">
        <h3 class="title">
            <i class="icon-link"></i>
            <a href="<?php echo $media[0] -> link_url?>"
               rel="<?php echo $media[0] -> link_follow;?>"
               target="<?php echo $media[0] -> link_target?>"><?php echo $media[0] -> link_title;?></a>
            <?php if($this -> item -> featured == 1):?>
            <span class="TzFeature"><?php echo JText::_('COM_TZ_PORTFOLIO_FEATURE');?></span>
            <?php endif;?>
        </h3>
        <?php  if ($params->get('show_intro',1) AND !empty($item -> introtext)) :?>
        <div class="introtext">
           <?php echo $item -> introtext;?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif;?>
<?php endif;?>