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
defined('_JEXEC') or die;

$params = $this -> item -> params;
?>

<?php if ($params->get('show_author',1) && !empty($this->item->author )) : ?>
    <span class="TzCreatedby" itemprop="author" itemscope itemtype="http://schema.org/Person">
    <?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
    <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
    <?php if ($params->get('link_author') == true): ?>
        <?php
        $target = '';
        if(isset($tmpl) AND !empty($tmpl)):
            $target = ' target="_blank"';
        endif;
        $needle = 'index.php?option=com_tz_portfolio&view=users&created_by=' . $this->item->created_by;
        $item = JMenu::getInstance('site')->getItems('link', $needle, true);
        if(!$userItemid = '&Itemid='.$this -> FindUserItemId($this->item->created_by)){
            $userItemid = null;
        }
        $cntlink = $needle.$userItemid;
        ?>
        <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', JRoute::_($cntlink), $author,$target.' itemprop="url"')); ?>
    <?php else: ?>
        <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
    <?php endif; ?>
</span>
<?php endif; ?>