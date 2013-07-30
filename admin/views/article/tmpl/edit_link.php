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

// No direct access.
defined('_JEXEC') or die;

?>
<div class="tab-pane" id="tztabsLink">
    <fieldset>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('link_title');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('link_title');?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('link_url');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('link_url');?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('link_follow');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('link_follow');?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('link_target');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('link_target');?></div>
        </div>
    </fieldset>
</div>