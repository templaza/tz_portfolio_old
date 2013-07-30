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
<div class="tab-pane" id="tztabsAudio">
    <fieldset>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('audio_soundcloud_id');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('audio_soundcloud_id');?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('audio_soundcloud_image_client');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('audio_soundcloud_image_client');?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('audio_soundcloud_image_server');?></div>
            <div class="controls">
                <?php echo $this -> form -> getInput('audio_soundcloud_image_server');?>
                <?php $hiddenImage = $this -> item -> audio_soundcloud_hidden_image;?>
                <?php if($hiddenImage && !empty($hiddenImage)):?>

                    <div class="clearfix"></div>
                    <a class="modal" href="<?php echo JUri::root().str_replace('.'.JFile::getExt($hiddenImage),'_L.'.JFile::getExt($hiddenImage),
                                                        $hiddenImage)?>"
                       rel="{handler: 'image', size: {x: 875, y: 550}, onClose: function() {}}">
                        <img style="max-width: 300px;" src="<?php echo JUri::root().str_replace('.'.JFile::getExt($hiddenImage),'_S.'.JFile::getExt($hiddenImage),
                                    $hiddenImage)?>"/>
                    </a>
                    <div class="clearfix"></div>
                    <?php echo $this -> form -> getInput('audio_souncloud_delete_image');?>
                    <?php echo $this -> form -> getLabel('audio_souncloud_delete_image');?>
                    <?php echo $this -> form -> getInput('audio_soundcloud_hidden_image');?>
                <?php endif;?>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this -> form -> getLabel('audio_soundcloud_title');?></div>
            <div class="controls"><?php echo $this -> form -> getInput('audio_soundcloud_title');?></div>
        </div>
    </fieldset>
</div>