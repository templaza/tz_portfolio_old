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

$media      = $this -> listMedia;
$params     = $this -> mediaParams;
$item       = $this -> item;

//if($params -> get('show_quote_text',1) OR $params -> get('show_quote_author',1)):
    if(count($media)):
        if($media[0] -> type == 'link'):
?>
    <div class="TzLink">
<!--        --><?php //if($params -> get('show_quote_text',1)):?>
        <h2 class="title">
            <a href="<?php echo $media[0] -> link_url?>"
               rel="<?php echo $media[0] -> link_follow;?>"
               target="<?php echo $media[0] -> link_target?>"><?php echo $media[0] -> link_title;?></a>
        </h2>
        <?php  if ($params->get('show_intro',1) AND !empty($item -> introtext)) :?>
        <div class="introtext">
           <?php echo $item -> introtext;?>
        </div>
        <?php endif; ?>
<!--        --><?php //endif;?>
    </div>
        <?php endif;?>
    <?php endif;?>
<?php //endif;?>