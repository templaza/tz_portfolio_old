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

defined('_JEXEC') or die;
?>
<div class="control-group">
	<div class="control-label"><?php echo $this->form->getLabel('metadesc'); ?><?php echo $this->form->getInput('metadesc'); ?></div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('metakey'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('metakey'); ?></div>
</div>


<?php foreach($this->form->getGroup('metadata') as $field): ?>
	<div class="control-group">
        <div class="control-label">
            <?php if (!$field->hidden): ?>
                <?php echo $field->label; ?>
            <?php endif; ?>
        </div>
        <div class="controls"><?php echo $field->input; ?></div>
	</div>
<?php endforeach; ?>
