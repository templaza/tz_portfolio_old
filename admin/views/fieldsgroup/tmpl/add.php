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
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');

?>
<script type="text/javascript ">
    Joomla.submitbutton = function(pressbutton) {
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            submitform( pressbutton );
            return;
        }

        // do field validation
        if ( form.name.value == "" ) {
            alert( "<?php echo JText::_( 'COM_TZ_PORTFOLIO_FIELDS_GROUP_ENTER_NAME', true ); ?>" );
            form.name.focus();
        } else {
            submitform( pressbutton);
        }
    }
</script>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=<?php echo $this -> option;?>&view=<?php echo $this -> view;?>">

    <div class="col width-100">
        <fieldset class="adminform">
            <legend><?php echo JText::_('COM_TZ_PORTFOLIO_FIELDSET_DETAILS');?></legend>
            <table class="admintable">
                <tbody>
                <tr>
                    <td class="key">
                        <label width="100" for="name" class="hasTip"
                                title="<?php echo JText::_('COM_TZ_PORTFOLIO_NAME')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_NAME')?>">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_NAME')?>
                            <span class="star"> *</span>
                        </label>
                    </td>
                    <td colspan="2">
                        <input type="text" title="" maxlength="50" size="50" value="" id="name" name="name">
                    </td>
                </tr>
                <tr>
                    <td class="key" valign="top">
                        <label width="100" for="description">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_DESCRIPTION');?>
                        </label>
                    </td>
                    <td>
                        <?php echo $this -> editor -> display('description','','500', '300', '60', '20', array('pagebreak', 'readmore'));?>
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>

    </div>
    <div class="clr"></div>

    <input type="hidden" value="<?php echo $this -> option;?>" name="option">
    <input type="hidden" value="" name="cid[]">
    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>