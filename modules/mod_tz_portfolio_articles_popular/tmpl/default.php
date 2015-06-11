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
$document = JFactory::getDocument();
$document->addStyleSheet('modules/mod_tz_portfolio_articles_popular/css/mod_tz_portfolio_articles_popular.css')
?>
<ul class="TzModPopular TzModPopular<?php echo $moduleclass_sfx; ?>">
<?php
foreach ($list as $item) :
    $media  = $item -> media;
?>
	<li>
    <?php if(!$media OR ($media AND $media -> type != 'quote' AND $media -> type != 'link')):?>
        <?php if($params -> get('show_tz_image',1) AND isset($media -> images)):?>
            <?php if($media -> images):?>
            <a href="<?php echo $item ->link;?>">
                <img src="<?php echo $media -> images;?>"
                     title="<?php echo $media -> imagetitle?>"
                     alt="<?php echo $media -> imagetitle?>">
            </a>
            <?php endif;?>
        <?php endif;?>

        <?php if(!empty($item -> title)):?>
            <a href="<?php echo $item->link; ?>">
                <?php echo $item->title; ?></a>
        <?php endif;?>

        <?php
            if(isset($item -> introtext) && !empty($item -> introtext)):
        ?>
            <p><?php echo $item -> introtext;?></p>
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
    <div class="clearfix"></div>
	</li>
<?php endforeach; ?>
</ul>
