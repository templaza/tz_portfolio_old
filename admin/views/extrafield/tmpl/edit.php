<?php
/*------------------------------------------------------------------------

# JVisualContent Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

// Call chosen js function
JHtml::_('formbehavior.chosen', 'select');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<form name="adminForm" method="post" id="adminForm"
      action="index.php?option=com_jvisualcontent&view=extrafield&layout=edit&id=<?php echo $this -> item -> id?>">
    <fieldset class="adminForm">
        <legend><?php echo JText::_('COM_JVISUALCONTENT_FIELDSET_DETAILS');?></legend>
        <div class="form-horizontal">
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this -> form -> getLabel('title');?></div>
                        <div class="controls"><?php echo $this -> form -> getInput('title');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this -> form -> getLabel('name');?></div>
                        <div class="controls"><?php echo $this -> form -> getInput('name');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this -> form -> getLabel('css_code');?></div>
                        <div class="controls"><?php echo $this -> form -> getInput('css_code');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this -> form -> getLabel('type');?></div>
                        <div class="controls"><?php echo $this -> form -> getInput('type');?></div>
                    </div>
                </div>
                <div class="span6">
                    <?php if(!$this -> item ->protected):?>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this -> form -> getLabel('published');?></div>
                            <div class="controls"><?php echo $this -> form -> getInput('published');?></div>
                        </div>
                    <?php endif;?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this -> form -> getLabel('description');?></div>
                        <div class="controls"><?php echo $this -> form -> getInput('description');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this -> form -> getLabel('id');?></div>
                        <div class="controls"><?php echo $this -> form -> getInput('id');?></div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <input type="hidden" value="com_jvisualcontent" name="option">
    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>