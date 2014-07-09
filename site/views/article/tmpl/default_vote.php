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

<?php if($params -> get('show_vote',1)):?>
<div class="TzVote">
    <span><?php echo JText::_('COM_TZ_PORTFOLIO_RATING');?></span>
    <?php echo $this->item->event->TZPortfolioVote; ?>
</div>
<?php endif;?>