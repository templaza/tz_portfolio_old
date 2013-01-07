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
require_once(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_tz_portfolio'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'media.php');
?>
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $i => $item) :  ?>
	<li<?php if($i == 0) echo ' class="first"'; if($i == count($list)-1 ) echo ' class="last"'?>>
        <?php if($params -> get('show_title',1) == 1):?>
            <a href="<?php echo $item->link; ?>">
                <?php echo $item->title; ?></a>
        <?php endif;?>
        <?php if($params -> get('show_image',0) == 1):?>
            <?php
                $model  = JModelLegacy::getInstance('Media','TZ_PortfolioModel',array('ignore_request' => true));
                $model -> setState('article.id',$item -> id);
                $media  = $model -> getMedia();
                if($media):
                    if($media[0] -> type == 'image' || $media[0] -> type == 'imagegallery'){
                        $image  = JURI::base().str_replace('.'.JFile::getExt($media[0] -> images),
                                              '_'.$params -> get('image_size','XS').'.'
                                              .JFile::getExt($media[0] -> images),$media[0] -> images);
                    }
                    elseif($media[0] -> type == 'video'){
                        $image  = JURI::base().str_replace('.'.JFile::getExt($media[0] -> thumb),
                                              '_'.$params -> get('image_size','XS').'.'
                                              .JFile::getExt($media[0] -> thumb),$media[0] -> thumb);
                    }
            ?>
            <a href="<?php echo $item ->link;?>">
                <img src="<?php echo $image?>" alt="<?php echo $media[0] -> imagetitle?>" title="<?php echo $media[0] -> imagetitle?>"/>
            </a>
            <?php endif;?>
        <?php endif;?>
	</li>
<?php endforeach; ?>
</ul>
