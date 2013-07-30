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

    $list   = $this -> listsArticle;
?>
    <?php $i=0;?>
    <?php foreach($list as $row):?>
        <?php
            $strMonth   = date('M',strtotime($row -> created));
            $strMonth   = strtolower($strMonth);
        ?>
        <?php if($i == 0):?>
            <a href="#<?php echo $strMonth?><?php echo $row -> year;?>">
               <?php //echo JText::_($strMonth);?>
                <?php echo JHtml::_('date', strtotime($row -> created), 'M');?>
            </a>
        <?php endif;?>
        <?php
            if($i != 0 && $list[$i-1] -> tz_date != $list[$i] -> tz_date):
        ?>
           <a href="#<?php echo $strMonth?><?php echo $row -> year;?>">
<!--               --><?php //echo JText::_($strMonth);?>
               <?php echo JHtml::_('date', strtotime($row -> created), 'M');?>
           </a>
        <?php endif;?>
        <?php $i++;?>
    <?php endforeach;?>
<?php //endif;?>