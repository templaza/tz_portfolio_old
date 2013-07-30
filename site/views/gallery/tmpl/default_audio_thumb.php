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

$item   = $this -> item;
$media      = $this -> listMedia;
$params     = $this -> mediaParams;
if(count($media)):
?>

    <?php if($params -> get('show_audio',1)):?>
        <?php
        if($media[0] -> type == 'audio'):
            if($params -> get('portfolio_image_size','S')){
                if(!empty($media[0] -> thumb)){
                    $src    = JURI::root().str_replace('.'.JFile::getExt($media[0] -> thumb),'_'.$params -> get('portfolio_image_size','S')
                                                              .'.'.JFile::getExt($media[0] -> thumb),$media[0] -> thumb);
                }
            }
            $srcAudio   = null;
            $srcAudio   = JURI::root().str_replace('.'.JFile::getExt($media[0] -> thumb),'_'
                            .$params -> get('detail_article_image_size','M')
                            .'.'.JFile::getExt($media[0] -> thumb),$media[0] -> thumb);
        ?>
            <a class="ib-image" href="<?php echo $item ->link;?>">
                <img src="<?php echo $src;?>" data-largesrc="<?php echo $srcAudio;?>"
                     title="<?php echo $media[0] -> imagetitle;?>"
                         alt="<?php echo $media[0] -> imagetitle;?>"/>
            </a>
        <?php endif;?>
    <?php endif;?>
<?php endif;?>
