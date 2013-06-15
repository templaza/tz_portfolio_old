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

$doc    = JFactory::getDocument();
JFactory::getLanguage()->load('com_content');
JFactory::getLanguage()->load('com_tz_portfolio');
?>

<?php if($this -> listsArticle):?>
    <?php
        $categories     = JCategories::getInstance('Content');
        $media          = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
        $extraFields    = JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
    ?>
    <?php foreach($this -> listsArticle as $i => $row):?>
        <?php
            $category   = $categories->get($row -> catid);
            $params = clone($this -> params);

            $catParams  = new JRegistry($category -> params);

            $params -> merge($catParams);
        
            $itemParams = new JRegistry($row -> attribs); //Get Article's Params
            $params -> merge($itemParams);

            $tmpl   = null;
            if($params -> get('tz_use_lightbox',1) == 1){
                $tmpl   = '&tmpl=component';
            }
            //Check redirect to view article
            if($params -> get('tz_portfolio_redirect','p_article') == 'article'){
                $row ->link   = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($row -> slug, $row -> catid).$tmpl);
                $commentLink   = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($row -> slug, $row -> catid),true,-1);
            }
            else{
                $row ->link   = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($row -> slug, $row -> catid).$tmpl);
                $commentLink   = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($row -> slug, $row -> catid),true,-1);
            }

            if($params -> get('tz_column_width',230))
                $tzItemClass    = ' tz_item';
            else
                $tzItemClass    = null;
        ?>
        <?php
            if($row -> featured == 1)
                $tzItemFeatureClass    = ' tz_feature_item';
            else
                $tzItemFeatureClass    = null;

            $class  = null;
            if($params -> get('tz_filter_type','tags') == 'tags'){
                $class  = $row -> tagName;
            }
            elseif($params -> get('tz_filter_type','tags') == 'categories'){
                $class  = 'category'.$row -> catid;
            }
            elseif($params -> get('tz_filter_type','tags') == 'letters'){
                $class  = mb_strtolower(mb_substr(trim($row -> title),0,1));
            }
        ?>
        <div class="element <?php echo $class.$tzItemClass.$tzItemFeatureClass;?>">
            <div class="TzInner">
                <!-- Begin Icon print, Email or Edit -->
                <?php if($params -> get('show_icons',0)):?>
                    <?php if ($params->get('show_print_icon') || $params->get('show_email_icon') || $params -> get('access-edit')) : ?>
                        <div class="TzIcon">
                            <div class="btn-group pull-right"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog"></i> <span class="caret"></span> </a>
                                <ul class="dropdown-menu">
                                    <?php if ($params->get('show_print_icon')) : ?>
                                    <li class="print-icon"> <?php echo JHtml::_('icon.print_popup', $row, $params); ?> </li>
                                    <?php endif; ?>
                                    <?php if ($params->get('show_email_icon')) : ?>
                                    <li class="email-icon"> <?php echo JHtml::_('icon.email', $row, $params); ?> </li>
                                    <?php endif; ?>
                                    <?php if ($params -> get('access-edit')) : ?>
                                    <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $row, $params); ?> </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- End Icon print, Email or Edit -->

                <?php
                 if($params -> get('show_image',1) == 1 OR $params -> get('show_image_gallery',1) == 1
                         OR $params -> get('show_video',1) == 1):
                ?>

                <?php

                    $listMedia      = $media -> getMedia($row -> id);

                    $this -> assign('mediaParams',$params);
                    $this -> assign('listMedia',$listMedia);
                    $this -> assign('itemLink',$row ->link);
                ?>
                    <?php echo $this -> loadTemplate('media');?>
                <?php endif;?>

                <div class="TzPortfolioDescription">
                    <?php if($params -> get('show_title',1)): ?>
                        <h3 class="TzPortfolioTitle name">
                            <?php if($params->get('link_titles',1)) : ?>
                                <a<?php if($params -> get('tz_use_lightbox') == 1){echo ' class="fancybox fancybox.iframe"';}?> href="<?php echo $row ->link; ?>">
                                    <?php echo $this->escape($row -> title); ?>
                                </a>
                            <?php else : ?>
                                <?php echo $this->escape($row -> title); ?>
                            <?php endif; ?>
                        </h3>
                    <?php endif;?>

                    <?php if(!$params -> get('show_intro')):?>
                        <?php //Call event onContentAfterTitle and TZPluginDisplayTitle on plugin?>
                        <?php echo $row -> event -> afterDisplayTitle;?>
                        <?php echo $row -> event -> TZafterDisplayTitle;?>
                    <?php endif;?>

                    <?php //Show vote?>
                    <?php echo $row -> event -> TZPortfolioVote;?>

                    <?php //Call event onContentBeforeDisplay and onTZPluginBeforeDisplay on plugin?>
                    <?php echo $row -> event -> beforeDisplayContent; ?>
                    <?php echo $row -> event -> TZbeforeDisplayContent; ?>
                    <?php  if ($params->get('show_intro',1) AND !empty($row -> introtext)) :?>
                    <div class="TzPortfolioIntrotext">
                       <?php echo $row -> introtext;?>
                    </div>
                    <?php endif; ?>

                    <div class="TzSeparator"></div>

                    <?php if (($params->get('show_author',1)) or ($params->get('show_category',1)) or ($params->get('show_create_date',1)) or ($params->get('show_modify_date',1)) or ($params->get('show_publish_date',1)) or ($params->get('show_parent_category',1)) or ($params->get('show_hits',1))) : ?>
                        <div class="TzArticle-info">
                    <?php endif; ?>

                    <?php if ($params->get('show_category',1)) : ?>
                    <div class="TZcategory-name">
                        <?php $title = $this->escape($row->category_title);
                        $url = '<a href="' . JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($row->catid)) . '">' . $title . '</a>'; ?>
                        <?php if ($params->get('link_category',1)) : ?>
                        <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
                        <?php else : ?>
                        <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($params->get('show_create_date',1)) : ?>
                    <div class="TzPortfolioDate" data-date="<?php echo strtotime($row -> created); ?>">
                        <?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $row->created, JText::_('DATE_FORMAT_LC2'))); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($params->get('show_modify_date')) : ?>
                    <div class="TzPortfolioModified">
                        <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $row->modified, JText::_('DATE_FORMAT_LC2'))); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($params->get('show_publish_date',1)) : ?>
                    <div class="published">
                        <?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $row->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($params->get('show_author') && !empty($row->author )) : ?>
                    <div class="TzPortfolioCreatedby">
                        <?php $author =  $row->author; ?>
                        <?php $author = ($row->created_by_alias ? $row->created_by_alias : $author);?>
                        <?php $userItemid   = '&Itemid='.$this -> FindUserItemId($row -> created_by);?>

                        <?php if ($params->get('link_author') == true):?>
                        <?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
                            JHtml::_('link', JRoute::_('index.php?option=com_tz_portfolio&amp;view=users&amp;created_by='.$row -> created_by.$userItemid), $author)); ?>

                        <?php else :?>
                        <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($params->get('show_hits')) : ?>
                    <div class="TzPortfolioHits">
                        <?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $row->hits); ?>
                    </div>
                    <?php endif; ?>
                    <?php if($params -> get('tz_show_count_comment',1) == 1):?>
                        <div class="TzPortfolioCommentCount">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_COMMENT_COUNT');?>
                            <?php if($params -> get('tz_comment_type') == 'facebook'): ?>
                                <?php if(isset($row -> commentCount)):?>
                                    <?php echo $row -> commentCount;?>
                                <?php endif;?>
                            <?php endif;?>

                            <?php if($params -> get('tz_comment_type') == 'jcomment'): ?>
                                <?php
                                    $comments = JPATH_SITE.'/components/com_jcomments/jcomments.php';
                                    if (file_exists($comments)){
                                        require_once($comments);
                                        if(class_exists('JComments')){
                                             echo JComments::getCommentsCount((int) $row -> id,'com_tz_portfolio');
                                        }
                                    }
                                ?>
                            <?php endif;?>
                            <?php if($params -> get('tz_comment_type','disqus') == 'disqus'):?>
                                <?php if(isset($row -> commentCount)):?>
                                    <?php echo $row -> commentCount;?>
                                <?php endif;?>
                            <?php endif;?>
                        </div>
                    <?php endif;?>

                    <?php

                        $extraFields -> setState('article.id',$row -> id);
                        $extraFields -> setState('params',$params);

                        $this -> item -> params = clone($params);
                        $this -> assign('listFields',$extraFields -> getExtraFields());
                    ?>
                    <?php echo $this -> loadTemplate('extrafields');?>

                    <?php if (($params->get('show_author',1)) or ($params->get('show_category',1)) or ($params->get('show_create_date',1)) or ($params->get('show_modify_date',1)) or ($params->get('show_publish_date',1)) or ($params->get('show_parent_category',1)) or ($params->get('show_hits',1))) : ?>
                        </div>
                    <?php endif; ?>

                    <?php if($params -> get('show_readmore',1)):?>
                    <a class="TzPortfolioReadmore<?php if($params -> get('tz_use_lightbox') == 1){echo ' fancybox fancybox.iframe';}?>" href="<?php echo $row ->link; ?>">
                        <?php echo JText::sprintf('COM_TZPORTFOLIO_READMORE'); ?>
                    </a>
                    <?php endif;?>

                    <?php //Call event onContentAfterDisplay and onTZPluginAfterDisplay on plugin?>
                    <?php echo $row->event->afterDisplayContent; ?>
                    <?php echo $row->event->TZafterDisplayContent; ?>

                </div>
            </div><!--Inner-->
        </div>

    <?php endforeach;?>

<?php endif;?>