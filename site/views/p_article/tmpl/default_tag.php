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

$params = $this -> item -> params;
$tmpl           = JRequest::getString('tmpl');

?>
<?php if($params -> get('show_tags',1)):?>
    <?php if($this -> listTags):?>
        <div class="clr"></div>
        <div class="TzArticleTag">
            <h3 class="title"><?php echo JText::_('COM_TZ_PORTFOLIO_TAG_TITLE');?></h3>
                <?php foreach($this -> listTags as $i => $row):?>
                <?php $itemId   = $this -> FindItemId($row -> id);?>
                <?php $link = JRoute::_('index.php?option=com_tz_portfolio&view=tags&id='.$row -> id.'&Itemid='.$itemId);?>
                <span  class="tag-list<?php echo $i ?>" itemprop="keywords">
                  <a class="label" href="<?php echo $link; ?>"<?php if(isset($tmpl) AND !empty($tmpl)): echo ' target="_blank"'; endif;?>>
                      <?php echo $row -> name;?>
                    </a>
                </span>
                <?php endforeach;?>

        </div>
    <?php endif;?>
<?php endif; ?>
