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
defined('_JEXEC') or die();

$media      = $this -> listMedia;
$link       = $this -> item ->link;
$params     = $this -> item -> params;
if(count($media)):
?>

    <?php if($params -> get('show_audio',1)):?>
        <?php
        if($media[0] -> type == 'audio'):
            $srcAudio   = null;
            $thbSize    = null;
            if($params -> get('portfolio_image_size','M')){
                $thbSize    = $params -> get('portfolio_image_size','M');
            }
            if($media[0] -> featured && $media[0] -> featured == 1){
                if($params -> get('portfolio_image_featured_size','M')){
                    $thbSize    = $params -> get('portfolio_image_featured_size','L');
                }
            }
            if($thbSize){
                $srcAudio   = JURI::root().str_replace('.'.JFile::getExt($media[0] -> thumb),'_'
                                .$thbSize
                                .'.'.JFile::getExt($media[0] -> thumb),$media[0] -> thumb);
            }

            $class  = null;
            if($params -> get('tz_use_lightbox',1) == 1){
                $class=' class = "fancybox fancybox.iframe"';
            }
        ?>
        <div class="tz_audio_thumbnail">
            <a<?php echo $class;?> href="<?php echo $link?>">
                <img src="<?php echo $srcAudio;?>"
                     title="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                     alt="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                     itemprop="thumbnailUrl"/>
            </a>
        </div>
        <?php endif;?>
    <?php endif;?>
<?php endif;?>
