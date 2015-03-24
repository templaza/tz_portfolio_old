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
if($list):
?>
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $i => $item) :
    $media = $item -> media;
?>
    <li<?php if($i == 0) echo ' class="first"'; if($i == count($list)-1 ) echo ' class="last"'?>>
    <?php if(!$media OR ($media AND $media -> type != 'quote' AND $media -> type != 'link')):?>
        <?php if($params -> get('show_article',1)):?>
            <?php if($params -> get('show_title',1) == 1):?>
                <a class="title" href="<?php echo $item->link; ?>">
                    <?php echo $item->title; ?></a>
            <?php endif;?>
            <?php if($params -> get('show_created_date',0)):?>
            <div class="created">
                <?php echo JHtml::_('date',$item -> created,'DATE_FORMAT_LC2'); ?>
            </div>
            <?php endif; ?>
            <?php if($params -> get('show_image',0) == 1):?>
                <?php
                    if($media):
                        $image  = null;
                        if($media -> type == 'image' || $media -> type == 'imagegallery'){
                            $image  = JURI::base().str_replace('.'.JFile::getExt($media -> images),
                                                  '_'.$params -> get('image_size','XS').'.'
                                                  .JFile::getExt($media -> images),$media -> images);
                        }
                        elseif($media -> type == 'video'){
                            $image  = JURI::base().str_replace('.'.JFile::getExt($media -> thumb),
                                                  '_'.$params -> get('image_size','XS').'.'
                                                  .JFile::getExt($media -> thumb),$media -> thumb);
                        }
                        if($image):
                ?>
                <a href="<?php echo $item ->link;?>">
                    <img src="<?php echo $image?>" alt="<?php echo $media -> imagetitle?>" title="<?php echo $media -> imagetitle?>"/>
                </a>
                    <?php endif;?>
                <?php endif;?>
            <?php endif;?>
            <?php if($params -> get('show_introtext',1)):?>
            <div class="introtext"><?php echo $item -> introtext; ?></div>
            <?php endif;?>
        <?php endif;?>
    <?php else:?>
        <?php if($params -> get('show_quote',1)):?>
            <?php if($media -> type == 'quote'):?>
            <div class="quote">
                <div class="text"><i class="icon-quote"></i><?php echo $media -> quote_text?></div>
                <?php if($params -> get('show_quote_author',1)):?>
                <div class="muted author"><?php echo $media -> quote_author; ?></div>
                <?php endif;?>
            </div>
            <?php endif;?>
        <?php endif;?>

        <?php if($params -> get('show_link',1)):?>
            <?php if($media -> type == 'link'):?>
            <div class="link">
                <span class="icon-link"></span>
                <a class="title" href="<?php echo $media -> link_url;?>"
                    target="<?php echo $media -> link_target;?>"
                    rel="<?php echo $media -> link_follow;?>"><?php echo $media -> link_title?></a>
                <?php if($params -> get('show_introtext',1)):?>
                <div class="introtext"><?php echo $item -> introtext; ?></div>
                <?php endif;?>
            </div>
            <?php endif;?>
        <?php endif;?>
    <?php endif;?>
    </li>
<?php endforeach; ?>
</ul>
<?php endif;?>