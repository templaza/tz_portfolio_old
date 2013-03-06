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
JHtml::_('formbehavior.chosen', 'select');
$published  = $this -> listEdit -> published ==1?'P':'U';
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
            alert( "<?php echo JText::_( 'COM_TZ_PORTFOLIO_INPUT_TAGS_NAME', true ); ?>" );
            form.name.focus();
        } else {
            submitform( pressbutton);
        }
    }
</script>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_tz_portfolio&view=tags">

    <!-- Begin Content -->
    <div class="span12 form-horizontal">
        <fieldset class="adminForm">
            <legend><?php echo JText::_('COM_TZ_PORTFOLIO_FIELDSET_DETAILS');?></legend>
            <div class="control-group">
                <div class="control-label">
                    <label width="100" for="name">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_NAME')?><span class="star"> *</span>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" title="" maxlength="50" size="50" value="<?php echo $this -> listEdit -> name;?>"
                           id="name" name="name"/>
                    <input type="hidden" name="old_name" value="<?php echo $this -> listEdit -> name;?>"/>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label width="100" for="published">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_FORM_PUBLISHED')?>
                    </label>
                </div>
                <div class="controls">
                    <?php
                        $state = array('' => JText::_('JOPTION_SELECT_PUBLISHED'), 'P' => JText::_('JPUBLISHED'), 'U' => JText::_('JUNPUBLISHED'));
                        echo JHtml::_('select.genericlist',$state,'published','','value','text',$published);
                    ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label width="100" for="description">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_DESCRIPTION');?>
                    </label>
                </div>
                <div class="controls">
                    <?php echo $this -> editor -> display('description',htmlspecialchars_decode($this -> listEdit -> description),'100%', '300', '60', '20', array('pagebreak', 'readmore'));?>
                </div>
            </div>

        </fieldset>
        <?php ?>
        <input type="hidden" value="com_tz_portfolio" name="option">
        <input type="hidden" value="<?php $cid=JRequest::getInt('id'); echo $cid;?>" name="id">
        <input type="hidden" value="" name="task">
        <?php echo JHTML::_('form.token');?>
    </div>
    <!-- End Content -->
</form>