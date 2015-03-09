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

$params     = $this -> item -> params;
if($params -> get('show_tags',1) && $this -> item && isset($this -> item -> tags)):
    ?>
    <span class="TzLine">|</span>
    <?php echo JText::sprintf('COM_TZ_PORTFOLIO_TAGS',''); ?>
    <?php foreach($this -> item -> tags as $i => $item): ?>
    <a href="<?php echo $item ->link; ?>"><?php echo $item -> name;?></a><?php if($i != count($this -> item -> tags) - 1):?><span><?php echo ','?></span><?php endif;?>
<?php endforeach;?>
<?php endif;?>