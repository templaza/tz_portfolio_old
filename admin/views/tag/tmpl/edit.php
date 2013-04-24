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

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
$published  = $this -> item -> published;
?>
<script type="text/javascript ">
    Joomla.submitbutton = function(task) {
        if (task == 'tag.cancel' || document.formvalidator.isValid(document.id('tag-form'))) {
            Joomla.submitform(task, document.getElementById('tag-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
        }
    }
</script>
<form name="adminForm" id="tag-form" class="form-validate row-fluid" method="post"
      action="index.php?option=com_tz_portfolio&view=tag&layout=edit&id=<?php echo $this -> item -> id?>">

    <!-- Begin Content -->
    <div class="span12 form-horizontal">
        <fieldset class="adminForm">
            <legend><?php echo JText::_('COM_TZ_PORTFOLIO_FIELDSET_DETAILS');?></legend>
            <div class="control-group">
                <div class="control-label">
                    <label width="100" for="jform_name">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_NAME')?><span class="star"> *</span>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" title="" size="50" value="<?php echo $this -> item -> name;?>"
                           id="jform_name" name="jform[name]" aria-required="true"
                           class="inputbox required" required="required"/>
                    <input type="hidden" name="jform[old_name]" value="<?php echo $this -> item -> name;?>"/>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label width="100" for="jform_published">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_FORM_PUBLISHED')?>
                    </label>
                </div>
                <div class="controls">
                    <?php
                    $state = array('' => JText::_('JOPTION_SELECT_PUBLISHED'), 'P' => JText::_('JPUBLISHED'), 'U' => JText::_('JUNPUBLISHED'));
                    echo JHtml::_('select.genericlist',$state,'jform[published]','','value','text',$published,'jform_published');
                    ?>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label for="jform_id"
                           title="<?php echo JText::_('JGLOBAL_FIELD_ID_LABEL');?>::<?php echo JText::_('JGLOBAL_FIELD_ID_DESC')?>">
                    <?php echo JText::_('JGLOBAL_FIELD_ID_LABEL');?></label>
                </div>
                <div class="controls">
                    <input type="text" id="jform_id"
                           readonly="readonly" class="readonly"
                           value="<?php echo ($id = $this -> item -> id)?$id:0?>" name="jform[id]">
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label width="100" for="jform_description">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_DESCRIPTION');?>
                    </label>
                </div>
                <div class="controls">
                    <?php echo $this -> editor -> display('jform[description]',htmlspecialchars_decode($this -> item -> description),'98%', '300', '60', '20', array('pagebreak', 'readmore'),'jform_description');?>
                </div>
            </div>

        </fieldset>
        <?php ?>
<!--        <input type="hidden" value="com_tz_portfolio" name="option">-->
<!--        <input type="hidden" name="return" value="--><?php //echo JRequest::getCmd('return');?><!--" />-->
        <input type="hidden" value="" name="task">
        <?php echo JHTML::_('form.token');?>
    </div>
    <!-- End Content -->
</form>