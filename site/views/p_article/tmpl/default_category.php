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

<?php if ($params->get('show_category',1)) : ?>
<span class="TzArticleCategory">
    <?php
    $title = $this->escape($this->item->category_title);
    $url    = $title;
    $target = '';
    if(isset($tmpl) AND !empty($tmpl)):
        $target = ' target="_blank"';
    endif;
    $url = '<a href="'.$this -> item -> category_link.'"'.$target.' itemprop="genre">'.$title.'</a>';

    ?>
    <?php if ($params->get('link_category',1) and $this->item->catslug) : ?>
        <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
    <?php else : ?>
        <?php echo JText::sprintf('COM_CONTENT_CATEGORY',  '<span itemprop="genre">' . $title . '</span>'); ?>
    <?php endif; ?>
</span>
<?php endif; ?>