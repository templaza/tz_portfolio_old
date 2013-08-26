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

$item   = $this -> item;
$media  = $this -> listMedia;
$params = $this -> mediaParams;
if($params -> get('portfolio_image_size','S')){
    if(!empty($media[0] -> images)){
        $src    = JURI::root().str_replace('.'.JFile::getExt($media[0] -> images),'_'.$params -> get('portfolio_image_size','S')
                                                  .'.'.JFile::getExt($media[0] -> images),$media[0] -> images);
    }
}
?>
<?php if($media):?>
    <?php if($media[0] -> type == 'image'):?>
        <?php
            $src2   = JURI::root().str_replace('.'.JFile::getExt($media[0] -> images),
                                               '_'.$params -> get('detail_article_image_size','XL')
                                                  .'.'.JFile::getExt($media[0] -> images),$media[0] -> images);
        ?>
        <?php if($params -> get('show_image',1) == 1 AND !empty($media[0] -> images)):?>
                <a class="ib-image" href="<?php echo $item ->link;?>">
                    <img src="<?php echo $src;?>" data-largesrc="<?php echo$src2;?>"
                         alt="<?php if(isset($media[0] -> imagetitle)) echo $media[0] -> imagetitle;?>"
                         title="<?php if(isset($media[0] -> imagetitle)) echo $media[0] -> imagetitle;?>"/>
                    <span><?php echo $item -> title;?></span>
                </a>
        <?php endif;?>
    <?php endif;?>

    <?php if($media[0] -> type == 'imagegallery'):?>
        <?php
        $srcGallery2   = JURI::root().str_replace('.'.JFile::getExt($media[0] -> images),
                                           '_'.$params -> get('detail_article_image_gallery_size','XL')
                                                  .'.'.JFile::getExt($media[0] -> images),$media[0] -> images);
        ?>
        <?php if($params -> get('show_image_gallery',1) == 1 AND !empty($media[0] -> images)):?>
            <a class="ib-image" href="<?php echo $item ->link;?>">
                <img src="<?php echo $src;?>" data-largesrc="<?php echo $srcGallery2;?>"
                     alt="<?php if(isset($media[0] -> imagetitle)) echo $media[0] -> imagetitle;?>"
                     title="<?php if(isset($media[0] -> imagetitle)) echo $media[0] -> imagetitle;?>"/>
                <span><?php echo $item -> title;?></span>
            </a>
        <?php endif;?>
    <?php endif;?>

    <?php
    if($media[0] -> type == 'video'):
        if($params -> get('show_video',1) == 1 AND !empty($media[0] -> thumb)):
            $srcVideo   = str_replace('.'.JFile::getExt($media[0] -> thumb),'_'
                                        .$params -> get('portfolio_image_size','M')
                                        .'.'.JFile::getExt($media[0] -> thumb),$media[0] -> thumb);
            $srcVideo2   = JURI::root().str_replace('.'.JFile::getExt($media[0] -> thumb),
                                                    '_'.$params -> get('detail_article_image_size','XL')
                                              .'.'.JFile::getExt($media[0] -> thumb),$media[0] -> thumb);

    ?>
        <a class="ib-image" href="<?php echo $item ->link;?>">
            <img src="<?php echo $srcVideo;?>"
                  data-largesrc="<?php echo $srcVideo2;?>"
                 title="<?php echo $media[0] -> imagetitle;?>"
                 alt="<?php echo $media[0] -> imagetitle;?>"/>
            <span><?php echo $item -> title;?></span>
        </a>
        <?php  endif;?>
    <?php endif;?>

    <?php if($media[0] -> type == 'audio'):?>
        <?php echo $this -> loadTemplate('audio_thumb');?>
    <?php endif;?>

    <?php if($media[0] -> type == 'quote'):?>
        <?php echo $this -> loadTemplate('quote');?>
    <?php endif;?>
<?php endif;?>