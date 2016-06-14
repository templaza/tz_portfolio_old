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

$media = $item -> media;
if(!$media OR ($media AND $media -> type != 'quote' AND $media -> type != 'link')):
?>

    <?php if($params -> get('show_image',0)):?>
        <?php if(isset($media -> images) AND !empty($media -> images)):?>
            <a href="<?php echo $item->link;?>">
                <img src="<?php echo $media -> images?>"
                     title="<?php echo $media -> imagetitle;?>"
                     alt="<?php echo $media -> imagetitle;?>">
            </a>
        <?php endif;?>
    <?php endif;?>

    <?php if ($params->get('item_title')) : ?>
        <<?php echo $params->get('item_heading'); ?> class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">
        <?php if ($params->get('link_titles') && $item->link != '') : ?>
            <a href="<?php echo $item->link;?>">
                <?php echo $item->title;?></a>
        <?php else : ?>
            <?php echo $item->title; ?>
        <?php endif; ?>
        </<?php echo $params->get('item_heading'); ?>>

    <?php endif; ?>

    <?php if (!$params->get('intro_only')) :
        echo $item->afterDisplayTitle;
    endif; ?>

    <?php echo $item->beforeDisplayContent; ?>

    <?php echo $item->introtext; ?>

    <?php if (isset($item->link) && $item->readmore && $params->get('readmore')) :
        echo '<a class="readmore" href="'.$item->link.'">'.$item->linkText.'</a>';
    endif; ?>
<?php else: ?>
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
            <<?php echo $params->get('item_heading'); ?> class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">
                <span class="icon-link"></span>
                <a class="title" href="<?php echo $media -> link_url;?>"
                    target="<?php echo $media -> link_target;?>"
                    rel="<?php echo $media -> link_follow;?>"><?php echo $media -> link_title?></a>
            </<?php echo $params->get('item_heading'); ?>>
            <?php if($params -> get('show_introtext',1)):?>
            <div class="introtext"><?php echo $item -> introtext; ?></div>
            <?php endif;?>
        </div>
        <?php endif;?>
    <?php endif;?>
<?php endif;?>
