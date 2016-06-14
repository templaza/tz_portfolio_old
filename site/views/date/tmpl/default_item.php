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

// Create a shortcut for params.
$params = &$this->item->params;

$images = json_decode($this->item->images);
$canEdit	= $this->item->params->get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

$blogLink   = $this -> item ->link;

$media  = $this -> media;
$media -> setParams($params);
$listMedia      = $media -> getMedia($this -> item -> id);
$this -> assign('listMedia',$listMedia);
?>

<?php if ($this->item->state == 0) : ?>
<div class="system-unpublished">
<?php endif; ?>

<?php if( (isset($listMedia[0] -> type) AND $listMedia[0] -> type != 'quote'
    AND $listMedia[0] -> type != 'link') OR !isset($listMedia[0] -> type)):?>

    <?php
     if($params -> get('show_image',1) OR $params -> get('show_image_gallery',1)
             OR $params -> get('show_video',1) OR $params -> get('show_audio',1)):
    ?>
        <?php
            echo $this -> loadTemplate('media');
        ?>
    <?php endif;?>

        <?php if (($params->get('show_author',1)) or ($params->get('show_category',1))
        or ($params->get('show_create_date',1)) or ($params->get('show_modify_date',1))
        or ($params->get('show_publish_date',1))
        or ($params->get('show_parent_category',1))
        or ($params->get('show_hits',1)) or ($params->get('show_tags',1))) : ?>
         <div class="muted TzArticleBlogInfo">
        <?php endif; ?>

            <?php if ($params->get('show_create_date')) : ?>
            <span class="TzBlogCreate">
              <span class="date" itemprop="dateCreated"> <?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2'))); ?></span>
            </span>
            <?php endif; ?>

            <?php if($params -> get('show_vote',1) AND $this -> item -> event -> TZPortfolioVote):?>
                <span class="TzVote">
                    <span class="TzLine">|</span>
                    <span><?php echo JText::_('COM_TZ_PORTFOLIO_RATING');?></span>
                    <?php echo $this -> item -> event -> TZPortfolioVote;?>
                </span>
            <?php endif;?>

            <?php if ($params->get('show_author',1) && !empty($this->item->author )) : ?>
            <span class="TzBlogCreatedby" itemprop="author" itemscope itemtype="http://schema.org/Person">
                <span class="TzLine">|</span>
                <?php $author =  $this->item->author; ?>
                <?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>
                <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
                <?php
                $userItemid = null;
                if($this -> FindUserItemId($this->item->created_by)){
                    $userItemid = '&Itemid='.$this -> FindUserItemId($this->item->created_by);
                }
                ?>

                    <?php if ($params->get('link_author') == true):?>
                        <?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
                         JHtml::_('link', JRoute::_('index.php?option=com_tz_portfolio&amp;view=users&amp;created_by='.$this -> item -> created_by.$userItemid), $author, array('itemprop' => 'url'))); ?>

                    <?php else :?>
                        <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
                    <?php endif; ?>
            </span>
        <?php endif; ?>

            <?php if ($params->get('show_hits')) : ?>
                <span class="TzLine">|</span>
                <span class="TzBlogHits">
                    <span class="numbers"><?php echo  $this->item->hits; ?></span>
                    <span class="hits"><?php echo JText::_('ARTICLE_HITS'); ?></span>
                    <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>" />
                </span>
            <?php endif; ?>

        <?php if ($params->get('show_parent_category') && $this->item->parent_id != 1) : ?>
                <span class="TzLine">|</span>
                <span class="TzParentCategoryName">
                    <?php $title = $this->escape($this->item->parent_title);
                        $url = '<a href="' . JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($this->item->parent_id)) . '" itemprop="genre">' . $title . '</a>'; ?>
                    <?php if ($params->get('link_parent_category')) : ?>
                        <?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
                        <?php else : ?>
                        <?php echo JText::sprintf('COM_CONTENT_PARENT', '<span itemprop="genre">' . $title . '</span>'); ?>
                    <?php endif; ?>
                </span>
        <?php endif; ?>

        <?php if ($params->get('show_category',1)) : ?>
                <span class="TzLine">|</span>
                <span class="TzBlogCategory">
                    <?php $title = $this->escape($this->item->category_title);
                            $url = '<a href="' . JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($this->item->catid)) . '" itemprop="genre">' . $title . '</a>'; ?>
                    <?php if ($params->get('link_category',1)) : ?>
                        <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
                        <?php else : ?>
                        <?php echo JText::sprintf('COM_CONTENT_CATEGORY', '<span itemprop="genre">' . $title . '</span>'); ?>
                    <?php endif; ?>
                </span>
        <?php endif; ?>

    <?php if($params -> get('show_tags',1)):
        echo $this -> loadTemplate('tag');
    endif;
    ?>


        <?php if ($params->get('show_modify_date',1)) : ?>
            <span class="TzLine">|</span>
            <span class="TzBlogModified" itemprop="dateModified">
            <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
            </span>
          <?php endif; ?>
        <?php if ($params->get('show_publish_date',1)) : ?>
                <span class="TzLine">|</span>
                <span class="TzBlogPublished" itemprop="datePublished">
                <?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
                </span>
        <?php endif; ?>

        <?php if($params -> get('tz_show_count_comment',1) == 1):?>
            <span class="TzLine">|</span>
            <span class="TzPortfolioCommentCount" itemprop="comment" itemscope itemtype="http://schema.org/Comment">
                <?php echo JText::_('COM_TZ_PORTFOLIO_COMMENT_COUNT');?>

                <?php if($params -> get('comment_function_type','js') == 'js'):?>
                    <?php if($params -> get('tz_comment_type') == 'disqus'): ?>
                        <a href="<?php echo $this -> item -> fullLink;?>#disqus_thread" itemprop="commentCount"><?php echo $this -> item -> commentCount;?></a>
                    <?php elseif($params -> get('tz_comment_type') == 'facebook'):?>
                        <span class="fb-comments-count" data-href="<?php echo $this -> item -> fullLink;?>" itemprop="commentCount"></span>
                    <?php endif;?>
                <?php else:?>
                    <?php if($params -> get('tz_comment_type') == 'facebook'): ?>
                        <?php if(isset($this -> item -> commentCount)):?>
                            <span itemprop="commentCount"><?php echo $this -> item -> commentCount;?></span>
                        <?php endif;?>
                    <?php endif;?>
                    <?php if($params -> get('tz_comment_type') == 'disqus'):?>
                        <?php if(isset($this -> item -> commentCount)):?>
                            <span itemprop="commentCount"><?php echo $this -> item -> commentCount;?></span>
                        <?php endif;?>
                   <?php endif;?>
                <?php endif;?>

                <?php if($params -> get('tz_comment_type') == 'jcomment'): ?>
                    <?php
                        $comments = JPATH_SITE.'/components/com_jcomments/jcomments.php';
                        if (file_exists($comments)){
                            require_once($comments);
                            if(class_exists('JComments')){
                    ?>
                        <span itemprop="commentCount"><?php echo JComments::getCommentsCount((int) $this -> item -> id,'com_tz_portfolio');?></span>
                    <?php
                            }
                        }
                    ?>
                <?php endif;?>


            </span>
        <?php endif;?>

        <?php if (($params->get('show_author',1)) or ($params->get('show_category',1)) or ($params->get('show_create_date',1)) or ($params->get('show_modify_date',1)) or ($params->get('show_publish_date',1)) or ($params->get('show_parent_category',1)) or ($params->get('show_hits',1))) :?>
        </div>
        <?php endif; ?>

        <?php if ($params->get('show_print_icon') || $params->get('show_email_icon') || $canEdit) : ?>
            <div class="TzIcon">
                <div class="btn-group pull-right"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog"></i> <span class="caret"></span> </a>
                    <ul class="dropdown-menu">
                        <?php if ($params->get('show_print_icon')) : ?>
                        <li class="print-icon"> <?php echo JHtml::_('icon.print_popup', $this->item, $params); ?> </li>
                        <?php endif; ?>
                        <?php if ($params->get('show_email_icon')) : ?>
                        <li class="email-icon"> <?php echo JHtml::_('icon.email', $this->item, $params); ?> </li>
                        <?php endif; ?>
                        <?php if ($canEdit) : ?>
                        <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $this->item, $params); ?> </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($params->get('show_title',1)) : ?>
        <h3 class="TzBlogTitle" itemprop="name">
            <?php if ($params->get('link_titles',1) && $params->get('access-view')) : ?>
                <a<?php if($params -> get('tz_use_lightbox') == 1) echo ' class="fancybox fancybox.iframe"';?>
                    href="<?php echo $blogLink; ?>" itemprop="url">
                <?php echo $this->escape($this->item->title); ?></a>
            <?php else : ?>
                <?php echo $this->escape($this->item->title); ?>
            <?php endif; ?>
            <?php if($this -> item -> featured == 1):?>
            <span class="label label-important TzFeature"><?php echo JText::_('COM_TZ_PORTFOLIO_FEATURE');?></span>
            <?php endif;?>
        </h3>
        <?php endif; ?>

        <?php if (!$params->get('show_intro',1)) : ?>
            <?php
                //Call event onContentAfterTitle and TZPluginDisplayTitle on plugin
                echo $this->item->event->afterDisplayTitle;
                echo $this->item->event->TZafterDisplayTitle;
            ?>
        <?php endif; ?>

        <?php // to do not that elegant would be nice to group the params ?>

        <?php
            $extraFields    = $this -> extraFields;
            $extraFields -> setState('article.id',$this -> item -> id);
            $extraFields -> setState('category.id',$this -> item -> catid);
            $extraFields -> setState('orderby',$params -> get('fields_order'));
            $extraParams    = $extraFields -> getParams();
            $extraFields -> setState('params',$params);
            $this -> assign('listFields',$extraFields -> getExtraFields());
        ?>
        <?php echo $this -> loadTemplate('extrafields');?>

        <?php
            //Call event onContentBeforeDisplay and onTZPluginBeforeDisplay on plugin
            echo $this->item->event->beforeDisplayContent;
            echo $this->item->event->TZbeforeDisplayContent;
        ?>

        <?php if($this -> item -> introtext):?>
            <div class="TzDescription" itemprop="description">
            <?php echo $this->item->introtext; ?>
            </div>
        <?php endif;?>

        <?php if ($params->get('show_readmore',1) && $this->item->readmore) :
            if ($params->get('access-view')) :
                $link = $blogLink;
            else :
                $menu = JFactory::getApplication()->getMenu();
                $active = $menu->getActive();
                $itemId = $active->id;
                $link1 = JRoute::_('index.php?option=com_users&amp;view=login&amp;Itemid=' . $itemId);

                $returnURL = $blogLink;

                $link = new JURI($link1);
                $link->setVar('return', base64_encode($returnURL));
            endif;
        ?>
        <?php if($params -> get('show_readmore',1) == 1):?>
            <a class="btn TzReadmore<?php if($params -> get('tz_use_lightbox') == 1) echo ' fancybox fancybox.iframe';?>" href="<?php echo $link; ?>"> <i class="icon-chevron-right"></i>
            <?php if (!$params->get('access-view')) :
                    echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
                elseif ($readmore = $this->item->alternative_readmore) :
                    echo $readmore;
                    if ($params->get('show_readmore_title', 0) != 0) :
                        echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
                    endif;
                elseif ($params->get('show_readmore_title', 0) == 0) :
                    echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
                else :
                    echo JText::_('COM_CONTENT_READ_MORE');
                    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
                endif; ?>
            </a>
        <?php endif;?>
    <?php endif; ?>

            <?php
                //Call event onContentAfterDisplay and onTZPluginAfterDisplay on plugin
                echo $this->item->event->afterDisplayContent;
                echo $this->item->event->TZafterDisplayContent;
            ?>

    <?php if ($this->item->state == 0) : ?>
    </div>
    <?php endif; ?>



    <div class="item-separator"></div>
<?php else:?>

    <?php if ($canEdit) : ?>
        <div class="TzIcon">
            <div class="btn-group pull-right"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog"></i> <span class="caret"></span> </a>
                <ul class="dropdown-menu">
                    <?php if ($canEdit) : ?>
                    <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $this->item, $params); ?> </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <?php echo $this -> loadTemplate('link');?>

    <?php echo $this -> loadTemplate('quote');?>
<?php endif;?>