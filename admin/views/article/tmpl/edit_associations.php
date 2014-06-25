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

defined('_JEXEC') or die;
$displayData    = $this -> form;
?>
<?php
foreach($displayData -> getFieldset('item_associations') as $field):
?>
<div class="control-group">
    <div class="control-label"><?php echo $field -> label;?></div>
    <div class="controls"><?php echo $field -> input;?></div>
</div>
<?php endforeach;?>
