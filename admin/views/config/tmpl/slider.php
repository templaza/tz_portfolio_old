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

JHtml::_('behavior.formvalidation');
?>

<form id="adminForm" name="adminForm" method="post"
      class="form-horizontal"
      action="index.php?option=com_tz_portfolio&view=config&layout=image&tmpl=component">

    <div class="control-group">
        <div class="btn-toolbar pull-right">
            <button class="btn btn-small btn-success" onclick="Joomla.submitform('config.applyconfig', this.form);" type="button">
                <span class="icon-apply icon-white"></span><?php echo JText::_('JAPPLY');?></button>
            <button class="btn btn-small" onclick="Joomla.submitform('config.saveconfig', this.form);" type="button">
                <span class="icon-save"></span><?php echo JText::_('JSAVE');?></button>
            <button class="btn btn-small" onclick="  window.parent.SqueezeBox.close();" type="button">
                <span class="icon-cancel"></span><?php echo JText::_('JCANCEL');?></button>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label">
            <?php echo $this -> form -> getLabel('tz_image_gallery_xsmall');?>
        </div>
        <div class="controls">
            <?php echo $this -> form -> getInput('tz_image_gallery_xsmall');?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <?php echo $this -> form -> getLabel('tz_image_gallery_small');?>
        </div>
        <div class="controls">
            <?php echo $this -> form -> getInput('tz_image_gallery_small');?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <?php echo $this -> form -> getLabel('tz_image_gallery_medium');?>
        </div>
        <div class="controls">
            <?php echo $this -> form -> getInput('tz_image_gallery_medium');?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <?php echo $this -> form -> getLabel('tz_image_gallery_large');?>
        </div>
        <div class="controls">
            <?php echo $this -> form -> getInput('tz_image_gallery_large');?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
            <?php echo $this -> form -> getLabel('tz_image_gallery_xlarge');?>
        </div>
        <div class="controls">
            <?php echo $this -> form -> getInput('tz_image_gallery_xlarge');?>
        </div>
    </div>
    <input type="hidden" name="task"/>
    <?php echo JHtml::_('form.token');?>
</form>