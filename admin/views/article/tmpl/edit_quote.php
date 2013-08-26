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
<div class="tab-pane" id="tztabsQuote">
    <fieldset>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('quote_author');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('quote_author');?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('quote_text');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('quote_text');?></div>
        </div>
    </fieldset>
</div>