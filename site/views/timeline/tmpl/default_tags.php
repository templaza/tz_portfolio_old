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
 defined('_JEXEC') or die();

?>
<?php if($this -> listsTags):?>
    <?php foreach($this -> listsTags as $item):?>
        <a class="btn btn-small" href="#<?php echo $item -> tagFilter; ?>"
           data-option-value=".<?php echo $item -> tagFilter; ?>">
            <?php echo $item -> name;?>
        </a><?php endforeach;?>
<?php endif;?>

