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

define( '_JEXEC', 1 );

define('JPATH_BASE', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
require_once ( JPATH_BASE.'/includes/defines.php' );
require_once ( JPATH_BASE.'/includes/framework.php' );

JFactory::getLanguage() -> load('com_tz_portfolio');

?>

<div class="column">
    <span class="position-name">(<?php echo JText::_('JNONE');?>)</span>
    <div class="columntools">
        <a href="#columnsettingbox" rel="popover" data-placement="bottom" title="<?php echo JText::_('COM_TZ_PORTFOLIO_COLUMN_SETTINGS');?>" class="fa fa-cog rowcolumnspop"></a>
        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW_ROW');?>" class="fa fa-bars add-rowin-column"></a>
        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE_COLUMN');?>" class="fa fa-times columndelete"></a>
        <a href="" title="<?php echo JText::_('COM_TZ_PORTFOLIO_MOVE_COLUMN');?>" class="fa fa-arrows columnmove"></a>
    </div>

    <input type="hidden" class="widthinput-xs" name="" value="">
    <input type="hidden" class="widthinput-sm" name="" value="">
    <input type="hidden" class="widthinput-md" name="" value="">
    <input type="hidden" class="widthinput-lg" name="" value="">
    <input type="hidden" class="offsetinput-xs" name="" value="">
    <input type="hidden" class="offsetinput-sm" name="" value="">
    <input type="hidden" class="offsetinput-md" name="" value="">
    <input type="hidden" class="offsetinput-lg" name="" value="">
    <input type="hidden" class="typeinput" name="" value="none">
<!--    <input type="hidden" class="positioninput" name="" value="">-->
<!--    <input type="hidden" class="styleinput" name="" value="tzxhtml">-->
    <input type="hidden" class="customclassinput" name="" value="">
    <input type="hidden" class="responsiveclassinput" name="" value="">
</div>