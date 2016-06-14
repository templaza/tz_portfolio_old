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
?>
<?php if($this -> listsCategories):?>
    <?php foreach($this -> listsCategories as $item):?>
        <a class="btn btn-small" href="#<?php echo str_replace(' ','-',$item -> title)?>"
           data-option-value=".<?php echo 'category'.$item -> id;?>" data-order="<?php echo $item -> order;?>">
            <?php echo $item -> title;?>
        </a>
    <?php endforeach;?>
<?php endif;?>