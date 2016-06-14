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

defined('_JEXEC') or die();

$media  = $this -> listMedia;
$link   = $this -> item ->link;
$params = $this -> item -> params;
$imgSize    = null;
$src    = null;

if($params -> get('portfolio_image_size','M')){
    $imgSize    = $params -> get('portfolio_image_size','M');
}
if(isset($media[0] -> featured) && $media[0] -> featured == 1){
    if($params -> get('portfolio_image_featured_size','M')){
        $imgSize    = $params -> get('portfolio_image_featured_size','L');
    }
}

if($imgSize):
    if(!empty($media[0] -> images))
        $src    = JURI::root().str_replace('.'.JFile::getExt($media[0] -> images),'_'.$imgSize
                                                  .'.'.JFile::getExt($media[0] -> images),$media[0] -> images);

    if(!empty($media[0] -> images_hover))
        $srcHover   = JURI::root().str_replace('.'.JFile::getExt($media[0] -> images_hover),'_'
                                           .$imgSize
                                          .'.'.JFile::getExt($media[0] -> images_hover),$media[0] -> images_hover);
endif;
$class  = null;
if($params -> get('tz_use_lightbox',1) == 1){
    $class=' class = "fancybox fancybox.iframe"';
}

?>
<?php if($media):?>
    <?php if(!empty($media[0] -> images) OR !empty($media[0] -> thumb)):?>
    <div class="TzTimeLineMedia">
        <?php if($media[0] -> type == 'image'):?>
            <?php if($src):?>
                <div class="tz_portfolio_image" style="position: relative;">
                    <a<?php echo $class;?> href="<?php echo $link?>">
                        <img src="<?php echo $src;?>"
                             alt="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                             title="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                             itemprop="thumbnailUrl"/>
                        <?php if($params -> get('tz_use_image_hover',1) == 1 AND $params -> get('show_image',1) == 1):?>
                            <?php if(isset($srcHover)):?>
                                <img class="tz_image_hover"
                                    src="<?php echo $srcHover;?>"
                                 alt="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                                 title="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"/>
                            <?php endif;?>
                        <?php endif;?>
                    </a>
                </div>
            <?php endif;?>
        <?php endif;?>

        <?php if($media[0] -> type == 'imagegallery'):?>
            <?php if($src):?>
            <div class="tz_portfolio_image_gallery">
                <a<?php echo $class;?> href="<?php echo $link?>">
                    <img src="<?php echo $src;?>"
                         alt="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                         title="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                         itemprop="thumbnailUrl"/>
                </a>
            </div>
            <?php endif;?>
        <?php endif;?>

         <?php
        if($media[0] -> type == 'video'):
            if($params -> get('show_video',1) == 1){
                $thbSize    = null;
                $srcVideo   = null;
                if($params -> get('portfolio_image_size','M')){
                    $thbSize    = $params -> get('portfolio_image_size','M');
                }
                if($media[0] -> featured && $media[0] -> featured == 1){
                    if($params -> get('portfolio_image_featured_size','M')){
                        $thbSize    = $params -> get('portfolio_image_featured_size','L');
                    }
                }
                if($thbSize){
                    $srcVideo   = JURI::root().str_replace('.'.JFile::getExt($media[0] -> thumb),'_'
                                                .$thbSize
                                                .'.'.JFile::getExt($media[0] -> thumb),$media[0] -> thumb);
                }
            }
            if($srcVideo):
        ?>
            <div class="tz_portfolio_video">
                <a<?php echo $class;?> href="<?php echo $link?>">
                    <img src="<?php echo $srcVideo;?>"
                         title="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                         alt="<?php echo ($media[0] -> imagetitle)?($media[0] -> imagetitle):($this -> item -> title);?>"
                         itemprop="thumbnailUrl"/>
                </a>
            </div>
            <?php endif;?>
        <?php endif;?>


        <?php // Require audio?>
        <?php if($params -> get('audio_layout_type','thumbnail') == 'thumbnail'):?>
            <?php echo $this -> loadTemplate('audio_thumb');?>
        <?php else: ?>
            <?php echo $this -> loadTemplate('audio');?>
        <?php endif;?>
    </div>
    <?php endif;?>
<?php endif;?>