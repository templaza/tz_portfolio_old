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

?>
<?php if ($params->get('show_parent_category') && $this->item->parent_slug != '1:root') : ?>
<span class="TzArticleParentCategory">
    <?php
    $title = $this->escape($this->item->parent_title);
    $url    = $title;
    $target = '';
    if(isset($tmpl) AND !empty($tmpl)):
        $target = ' target="_blank"';
    endif;
    $url = '<a href="'.$this -> item -> parent_link.'"'.$target.' itemprop="genre">'.$title.'</a>';
    ?>
    <?php if ($params->get('link_parent_category') and $this->item->parent_slug) : ?>
        <?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
    <?php else : ?>
        <?php echo JText::sprintf('COM_CONTENT_PARENT',  '<span itemprop="genre">' . $title . '</span>'); ?>
    <?php endif; ?>
</span>
<?php endif; ?>