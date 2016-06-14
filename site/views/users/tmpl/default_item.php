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
defined('_JEXEC') or die('Restricted access');

$row            = $this -> item;
$params         = $row -> params;
$media          = $this -> media;
$extraFields    = $this -> extraFields;
$canEdit        = $params -> get('access-edit');

$listMedia      = $media -> getMedia($row -> id);

$this -> assign('listMedia',$listMedia);
?>

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
        or ($params->get('show_publish_date',1)) or ($params->get('show_parent_category',1))
        or ($params->get('show_hits',1)) or ($params -> get('show_vote',1))) : ?>
    <div class="muted TzUserArticleInfo">

        <?php if ($params->get('show_create_date',1)) : ?>
        <span class="TzUserCreate">
              <span class="date" itemprop="dateCreated"> <?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $row->created, JText::_('DATE_FORMAT_LC2'))); ?></span>
        </span>
        <?php endif; ?>

        <?php if($params -> get('show_vote',1) AND $row -> event -> TZPortfolioVote):?>
        <span class="TzVote">
            <?php echo $row -> event -> TZPortfolioVote; ?>
            <span class="TzMilling">,&nbsp;</span>
        </span>
        <?php endif;?>

        <?php if ($params->get('show_hits',1)) : ?>
        <span class="TzUserHits">
            <span class="numbers"><?php echo  $row->hits; ?></span>
            <span class="hits"><?php echo JText::_('ARTICLE_HITS'); ?></span>
            <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $row->hits; ?>" />
        </span>
        <?php endif; ?>

        <?php if ($params->get('show_category',1)) : ?>
        <span class="TZUserCategoryName">
            <?php $title = $this->escape($row->category_title);
            $url = '<a href="' . JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($row->catid)) . '" itemprop="genre">' . $title . '</a>'; ?>
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
        <span class="TzUserModified" itemprop="dateModified">
            <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $row->modified, JText::_('DATE_FORMAT_LC2'))); ?>
        </span>
        <?php endif; ?>

        <?php if ($params->get('show_publish_date',1)) : ?>
        <span class="TzUserPublished" itemprop="datePublished">
            <?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $row->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
        </span>
        <?php endif; ?>

        <?php if($params -> get('tz_show_count_comment',1) == 1):?>
        <span class="TzPortfolioCommentCount" itemprop="comment" itemscope itemtype="http://schema.org/Comment">
            <?php echo JText::_('COM_TZ_PORTFOLIO_COMMENT_COUNT');?>

            <?php if($params -> get('comment_function_type','js') == 'js'):?>
                <?php if($params -> get('tz_comment_type') == 'disqus'):?>
                    <a href="<?php echo $row -> fullLink;?>#disqus_thread" itemprop="commentCount"><?php echo $row -> commentCount;?></a>
                <?php elseif($params -> get('tz_comment_type') == 'facebook'):?>
                    <span class="fb-comments-count" data-href="<?php echo $row -> fullLink;?>" itemprop="commentCount"></span>
                <?php endif;?>
            <?php else:?>
                <?php if($params -> get('tz_comment_type') == 'facebook'): ?>
                    <?php if(isset($row -> commentCount)):?>
                        <span itemprop="commentCount"><?php echo $row -> commentCount;?></span>
                    <?php endif;?>
                <?php endif;?>
                <?php if($params -> get('tz_comment_type') == 'disqus'):?>
                    <?php if(isset($row -> commentCount)):?>
                        <span itemprop="commentCount"><?php echo $row -> commentCount;?></span>
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
                    <span itemprop="commentCount"><?php echo JComments::getCommentsCount((int) $row -> id,'com_tz_portfolio');?></span>
                <?php
                        }
                    }
                ?>
            <?php endif;?>

        </span>
        <?php endif;?>

        <?php if ($params->get('show_author',1) && !empty($row->author )) : ?>
        <span class="TzUserCreatedby">
            <?php $author =  $row->author; ?>
            <?php $author = ($row->created_by_alias ? $row->created_by_alias : $author);?>

            <?php if (!empty($row->contactid ) &&  $params->get('link_author') == true):?>
            <?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
                JHtml::_('link', JRoute::_('index.php?option=com_tz_portfolio&amp;view=users&amp;created_by='.$row -> created_by), $author)); ?>

            <?php else :?>
            <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
            <?php endif; ?>
        </span>
        <?php endif; ?>

        </div>
    <?php endif; ?>

    <?php if ($canEdit ||  $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
    <div class="TzIcon">
        <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog"></i> <span class="caret"></span> </a>
            <?php // Note the actions class is deprecated. Use dropdown-menu instead. ?>
            <ul class="dropdown-menu actions">
                <?php if ($params->get('show_print_icon')) : ?>
                <li class="print-icon"> <?php echo JHtml::_('icon.print_popup',  $row, $params); ?> </li>
                <?php endif; ?>
                <?php if ($params->get('show_email_icon')) : ?>
                <li class="email-icon"> <?php echo JHtml::_('icon.email',  $row, $params); ?> </li>
                <?php endif; ?>
                <?php if ($canEdit) : ?>
                <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $row, $params); ?> </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <?php if($params -> get('show_title',1)): ?>
    <h3 class="TzUserTitle" itemprop="name">
        <?php if($params->get('link_titles',1)) : ?>
            <a<?php if($params -> get('tz_use_lightbox') == 1){echo ' class="fancybox fancybox.iframe"';}?>
                href="<?php echo $row ->link; ?>" itemprop="url">
                <?php echo $this->escape($row -> title); ?>
            </a>
        <?php else : ?>
        <?php echo $this->escape($row -> title); ?>
        <?php endif; ?>
        <?php if($row -> featured == 1):?>
        <span class="label label-important TzFeature"><?php echo JText::_('COM_TZ_PORTFOLIO_FEATURE');?></span>
        <?php endif;?>
    </h3>
    <?php endif;?>

    <?php if (!$params->get('show_intro',1)) : ?>
        <?php //Call event onContentAfterTitle and TZPluginDisplayTitle on plugin?>
        <?php echo $row -> event -> afterDisplayTitle; ?>
        <?php echo $row -> event -> TZafterDisplayTitle; ?>
    <?php endif; ?>

    <?php
        $extraFields -> setState('article.id',$row -> id);

        $extraFields -> setState('params',$row -> params);

        $this -> assign('userFields',$extraFields -> getExtraFields());
    ?>

    <?php echo $this -> loadTemplate('extrafields');?>

    <?php //Call event onContentBeforeDisplay and onTZPluginBeforeDisplay on plugin?>
    <?php echo $row -> event -> beforeDisplayContent; ?>
    <?php echo $row -> event -> TZbeforeDisplayContent; ?>

    <?php  if ($params->get('show_intro',1) AND !empty($row -> introtext)) :?>
    <div class="TzDescription" itemprop="description">
       <?php echo $row -> introtext;?>
    </div>
    <?php endif; ?>

    <?php if ($params->get('show_readmore',1) && $row->readmore) :
        if ($params->get('access-view')) :
            $link   = $row ->link;
        else :
            $menu = JFactory::getApplication()->getMenu();
            $active = $menu->getActive();
            $itemId = $active->id;
            $link1 = JRoute::_('index.php?option=com_users&amp;view=login&amp;Itemid=' . $itemId);
            $link = new JURI($link1);
            $link->setVar('return', base64_encode($row ->link));
        endif;
    ?>

    <?php if($params -> get('show_readmore',1) == 1):?>
            <a class="btn TzReadmore<?php if($params -> get('tz_use_lightbox') == 1){echo ' fancybox fancybox.iframe';}?>"
               href="<?php echo $link; ?>">
                <?php if (!$params->get('access-view')) :
                echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
            elseif ($readmore = $params -> get('alternative_readmore')) :
                echo $readmore;
                if ($params->get('show_readmore_title', 0) != 0) :
                    echo JHtml::_('string.truncate', ($row->title), $params->get('readmore_limit'));
                endif;
            elseif ($params->get('show_readmore_title', 0) == 0) :
                echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
            else :
                echo JText::_('COM_CONTENT_READ_MORE');
                echo JHtml::_('string.truncate', ($row->title), $params->get('readmore_limit'));
            endif; ?></a>
        <?php endif;?>

    <?php endif; ?>
    <div class="clr"></div>

    <?php //Call event onContentAfterDisplay and onTZPluginAfterDisplay on plugin?>
    <?php echo $row -> event -> afterDisplayContent; ?>
    <?php echo $row -> event -> TZafterDisplayContent; ?>
<?php else: // Begin quote or link?>
    <?php if ($canEdit) : ?>
    <div class="TzIcon">
        <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog"></i> <span class="caret"></span> </a>
            <?php // Note the actions class is deprecated. Use dropdown-menu instead. ?>
            <ul class="dropdown-menu actions">
                <?php if ($canEdit) : ?>
                <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $row, $params); ?> </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    <?php echo $this -> loadTemplate('link');?>

    <?php echo $this -> loadTemplate('quote');?>
<?php endif;?>