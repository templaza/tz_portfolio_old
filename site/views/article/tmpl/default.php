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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$app    = JFactory::getApplication();

// Create shortcuts to some parameters.
$params		= $this->item->params;
$images     = json_decode($this->item->images);
$urls       = json_decode($this->item->urls);
$canEdit	= $this->item->params->get('access-edit');
JHtml::_('behavior.caption');
$user		= JFactory::getUser();
$doc        = JFactory::getDocument();

$tmpl    = JRequest::getString('tmpl',null);
if($tmpl){
    $tmpl   = '&tmpl=component';
}
//echo $this->generateLayout;
?>

<div class="TzItemPage item-page<?php echo $this->pageclass_sfx?>"  itemscope itemtype="http://schema.org/Article">
    <div class="TzItemPageInner">
        <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
        <?php if ($this->params->get('show_page_heading', 1)) : ?>
            <h1 class="TzHeadingTitle">
            <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        <?php endif; ?>

        <?php
        if($this -> generateLayout && !empty($this -> generateLayout)) {
            echo $this->generateLayout;
        }else{
//            echo $this -> loadTemplate();
//            echo $this -> loadTemplate();
        }

        ?>

        <?php
        //Call event onContentAfterDisplay and onTZPluginAfterDisplay on plugin
        echo $this->item->event->afterDisplayContent;
        echo $this->item->event->TZafterDisplayContent;
        ?>

    </div>
</div>
