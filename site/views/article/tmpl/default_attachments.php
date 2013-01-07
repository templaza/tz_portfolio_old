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
defined('_JEXEC') or die('Restricted access');

$params = $this -> item -> params;

?>
<?php if($params -> get('show_attachments',1)):?>
    <?php if($this -> listAttach):?>
        <div class="clr"></div>
        <div class="TzAttachments">
            <h3 class="TzAttachmentsTitle"><?php echo JText::_('COM_TZ_PORTFOLIO_ATTACHMENTS_TITLE');?></h3>
            <ul>
                <?php foreach($this -> listAttach as $row): ?>
                    <li>
                        <a href="<?php echo $row -> _link;?>"><?php echo $row -> attachtitle;?></a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    <?php endif;?>
<?php endif; ?>