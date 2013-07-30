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

$params = &$this -> params;
?>
<ul class="nav">
<?php if($letters = $params -> get('tz_letters')):
    $letters = explode(',',$letters);
    $availLetter    = $this -> availLetter;
?>
  <?php foreach($letters as $i => $letter):?>
    <?php
        $disabledClass  = null;
        $activeClass    = null;
        if($availLetter[$i] != true):
            $disabledClass  = ' disabled';
        endif;
        if($this -> char == $letter):
            $activeClass    = ' active';
        endif;
    ?>
        <li>
        <a<?php if($availLetter[$i] != false) echo ' href="'.JRoute::_('index.php?option=com_tz_portfolio&'
            .'view=category'.'&id='.JRequest::getInt('id').'&char='
                                      .mb_strtolower(trim($letter)).'&Itemid='.JRequest::getInt('Itemid')).'"';?>
           class="btn-small<?php echo $disabledClass.$activeClass;?>"><?php echo mb_strtoupper(trim($letter));?></a>
        </li>
  <?php endforeach;?>
<?php endif;?>
</ul>