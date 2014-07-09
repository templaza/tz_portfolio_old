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
<?php if ($params->get('show_publish_date')) : ?>
<span class="TZPublished" itemprop="datePublished">
    <?php echo JText::sprintf( JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
</span>
<?php endif; ?>