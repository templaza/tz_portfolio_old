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

JFactory::getLanguage()->load('com_content');
JFactory::getLanguage()->load('com_tz_portfolio');

$list       = $this -> listsArticle;
$categories = $this -> listsCatDate;
 
?>
<?php
    if($list):
        $media          = $this -> media;
        $extraFields    = $this -> extraFields;
?>
    <?php foreach($list as $i => $row):?>
        <?php
            $this -> item   = clone($row);
            $params = clone($row -> params);

            if($params -> get('tz_timeline_time_type','month-year') == 'year'):
                $dataCategory   = $row -> year;
            elseif($params -> get('tz_timeline_time_type','month-year') == 'month'):
                $dataCategory   = $row -> month;
            elseif($params -> get('tz_timeline_time_type','month-year') == 'month-year'):
                $dataCategory   = $row -> tz_date;
            endif;

            if($params -> get('tz_column_width',230))
                $tzItemClass    = ' tz_item';
            else
                $tzItemClass    = null;
            if($row -> featured == 1)
                $tzItemFeatureClass    = ' tz_feature_item';
            else
                $tzItemFeatureClass    = null;

            $class  = null;
            $data       = null;
            if($params -> get('tz_filter_type','tags') == 'tags'){
                $class  = $row -> tagName;
                $data    = $row -> allTags;
            }
            elseif($params -> get('tz_filter_type','tags') == 'categories'){
                $class  = 'category'.$row -> catid;
            }

        ?>
        
        <?php
            $strMonth   = null;
            $year       = null;
    
            $strMonthLink   = date('COM_TZ_PORTFOLIO_DATE_FORMAT_LC1',strtotime($row -> created));
            $strMonthLink   = strtolower($strMonthLink);

            if($params -> get('tz_timeline_time_type') == 'month'):

                if(($i == 0) OR ($i != 0 AND $list[$i-1] -> month  != $list[$i] -> month)):
                    $strMonth       = JHtml::_('date', $row -> created, JText::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC2'));
                    $strMonth       = ucfirst($strMonth);
                    if($params -> get('tz_filter_type','tags') == 'categories'){
                        foreach($categories as $catId){
                            if($list[$i] -> month == $catId -> month){
                                $data[] = 'category'.$catId -> id;
                            }
                        }
                    }
                    
                endif;
            elseif($params -> get('tz_timeline_time_type') == 'year'):
                if(($i == 0) OR ($i != 0 AND $list[$i-1] -> year  != $list[$i] -> year)):
                    $year   = $row -> year;
                    if($params -> get('tz_filter_type','tags') == 'categories'){
                        foreach($categories as $catId){
                            if($list[$i] -> year == $catId -> year){
                                $data[] = 'category'.$catId -> id;
                            }
                        }
                    }

                endif;
            elseif($params -> get('tz_timeline_time_type') == 'month-year'):
                if(($i == 0) OR ($i != 0 AND $list[$i-1] -> tz_date  != $list[$i] -> tz_date)):
                    $strMonth       = JHtml::_('date', $row -> tz_date, JText::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC2'));
                    $strMonth       = ucfirst($strMonth);
                    $year   = $row -> year;
                    if($params -> get('tz_filter_type','tags') == 'categories'){
                        foreach($categories as $catId){
                            if($list[$i] -> tz_date == $catId -> tz_date){
                                $data[] = 'category'.$catId -> id;
                            }
                        }
                    }
                endif;
            endif;
    
            if($params -> get('tz_filter_type','tags') == 'categories'){
                if($data){
                    $data   = array_unique($data);
                    $data   = implode(' ',$data);
                }
            }
        ?>
        <?php if($year OR $strMonth):?>
            <div class="element TzDate <?php if($data) echo $data;?>"
                 data-category="<?php echo $dataCategory?>"
                 data-title=""
                 data-date="<?php echo strtotime($row -> created)+strtotime($dataCategory) + count($list)+1;?>"
                 data-hits="<?php echo ((int) $row -> maxhits) +strtotime($dataCategory) + 1; ?>">
                <h2 id="<?php echo strtolower(date('M',strtotime($row -> created))).$year;?>">
                    <span class="label label-info date"><?php echo JText::_(trim($strMonth)).'&nbsp;'.$year;?></span>
                </h2>
            </div>
        <?php endif;?>

        <?php
            $listMedia      = $media -> getMedia($row -> id);
            $this -> assign('listMedia',$listMedia);
        ?>

        <div id="tzelement<?php echo $row -> id;?>" class="element <?php echo $class.$tzItemClass.$tzItemFeatureClass;?>"
             data-category="<?php echo $dataCategory;?>"
             data-title="<?php echo $this->escape($row -> title); ?>"
             data-date="<?php echo strtotime($row->created) + strtotime($dataCategory) + $i; ?>"
             data-hits="<?php echo ((int) $row -> hits) + strtotime($dataCategory); ?>"
             itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
            <div class="TzInner">
                <!-- Begin Icon print, Email or Edit -->
                <?php if($params -> get('show_icons',0)):?>
                    <?php if ( ( ( (isset($listMedia[0] -> type) AND $listMedia[0] -> type != 'quote'
                                AND $listMedia[0] -> type != 'link') OR !isset($listMedia[0] -> type)) AND
                        ($params->get('show_print_icon') || $params->get('show_email_icon') || $params -> get('access-edit')))
                        OR ((isset($listMedia[0] -> type) AND ($listMedia[0] -> type == 'quote'
                        OR $listMedia[0] -> type == 'link')) AND $params -> get('access-edit'))) : ?>
                        <div class="TzIcon">
                            <div class="btn-group pull-right"> <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog"></i> <span class="caret"></span> </a>
                                <ul class="dropdown-menu">
                                    <?php  if ( ( (isset($listMedia[0] -> type) AND $listMedia[0] -> type != 'quote'
                                                AND $listMedia[0] -> type != 'link') OR !isset($listMedia[0] -> type))
                                        AND ($params->get('show_print_icon') || $params->get('show_email_icon')
                                            || $params -> get('access-edit'))):?>

                                        <?php if ($params->get('show_print_icon')) : ?>
                                        <li class="print-icon"> <?php echo JHtml::_('icon.print_popup', $row, $params); ?> </li>
                                        <?php endif; ?>
                                        <?php if ($params->get('show_email_icon')) : ?>
                                        <li class="email-icon"> <?php echo JHtml::_('icon.email', $row, $params); ?> </li>
                                        <?php endif; ?>
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

                    <div class="TzPortfolioDescription">
                        <?php if($params -> get('show_title',1)): ?>
                            <h3 class="TzPortfolioTitle name" itemprop="name">
                                <?php if($params->get('link_titles',1)) : ?>
                                    <a<?php if($params -> get('tz_use_lightbox') == 1){echo ' class="fancybox fancybox.iframe"';}?>
                                        href="<?php echo $row ->link; ?>" itemprop="url">
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

                        <?php
                         //Show voting
                        if($params -> get('show_vote') AND $row -> event -> TZPortfolioVote):
                        ?>
                        <?php echo $row->event->TZPortfolioVote;?>
                        <?php endif; ?>

                        <?php //Call event onContentBeforeDisplay and onTZPluginBeforeDisplay on plugin?>
                        <?php echo $row -> event -> beforeDisplayContent; ?>
                        <?php echo $row -> event -> TZbeforeDisplayContent; ?>


                        <?php  if ($params->get('show_intro',1) == 1 AND !empty($row -> introtext)) :?>
                        <div class="TzPortfolioIntrotext" itemprop="description">
                            <?php echo $row -> introtext;?>
                        </div>
                        <?php endif; ?>

                        <div class="TzSeparator"></div>

                        <?php if (($params->get('show_author',1)) or ($params->get('show_category',1))
                        or ($params->get('show_create_date',1)) or ($params->get('show_modify_date',1))
                        or ($params->get('show_publish_date',1)) or ($params->get('show_parent_category',1))
                        or ($params->get('show_hits',1)) or ($params->get('show_tags',1))) : ?>
                            <div class="muted TzArticle-info">
                        <?php endif; ?>

                            <?php if ($params->get('show_category',1)) : ?>
                            <div class="TZcategory-name">
                                <?php
                                $title = $this->escape($row->category_title);
                                $url = '<a href="' . JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($row->catid)) . '" itemprop="genre">' . $title . '</a>'; ?>
                                <?php if ($params->get('link_category',1)) : ?>
                                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
                                <?php else : ?>
                                <?php  echo JText::sprintf('COM_CONTENT_CATEGORY', '<span itemprop="genre">' . $title . '</span>'); ?>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($params->get('show_tags',1)) :
                                echo $this -> loadTemplate('tag');
                            endif; ?>

                            <?php if ($params->get('show_create_date',1)) : ?>
                            <div class="TzPortfolioDate" data-date="<?php echo strtotime($row -> created); ?>" itemprop="dateCreated">
                                <?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $row->created, JText::_('DATE_FORMAT_LC2'))); ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($params->get('show_modify_date')) : ?>
                            <div class="TzPortfolioModified" itemprop="dateModified">
                                <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $row->modified, JText::_('DATE_FORMAT_LC2'))); ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($params->get('show_publish_date',1)) : ?>
                            <div class="published" itemprop="datePublished">
                                <?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $row->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($params->get('show_author') && !empty($row->author )) : ?>
                            <div class="TzPortfolioCreatedby" itemprop="author" itemscope itemtype="http://schema.org/Person">
                                <?php $author =  $row->author; ?>
                                <?php $author = ($row->created_by_alias ? $row->created_by_alias : $author);?>
                                <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>
                                <?php
                                if(!$userItemid = '&Itemid='.$this -> FindUserItemId($row -> created_by)){
                                    $userItemid = null;
                                }
                                ?>

                                <?php if ($params->get('link_author') == true):?>
                                <?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
                                    JHtml::_('link', JRoute::_('index.php?option=com_tz_portfolio&amp;view=users&amp;created_by='.$row -> created_by.$userItemid), $author, array('itemprop' => 'url'))); ?>

                                <?php else :?>
                                <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($params->get('show_hits')) : ?>
                            <div class="TzPortfolioHits">
                                <?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $row->hits); ?>
                                <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $row->hits; ?>" />
                            </div>
                            <?php endif; ?>

                            <?php if($params -> get('tz_show_count_comment',1) == 1):?>
                                <div class="TzPortfolioCommentCount" itemprop="comment" itemscope itemtype="http://schema.org/Comment">
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_COMMENT_COUNT');?>
                                    <?php if($params -> get('tz_comment_type') == 'facebook'): ?>
                                        <?php if(isset($row -> commentCount)):?>
                                            <span itemprop="commentCount"><?php echo $row -> commentCount;?></span>
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
                                    <?php if($params -> get('tz_comment_type') == 'disqus'):?>
                                        <?php if(isset($row -> commentCount)):?>
                                            <span itemprop="commentCount"><?php echo $row -> commentCount;?></span>
                                        <?php endif;?>
                                    <?php endif;?>
                                </div>
                            <?php endif;?>

                            <?php
                                $extraFields -> setState('article.id',$row -> id);
                                $extraFields -> setState('params',$row -> params);
                                $this -> assign('listFields',$extraFields -> getExtraFields());
                            ?>
                            <?php echo $this -> loadTemplate('extrafields');?>

                            <?php if (($params->get('show_author',1)) or ($params->get('show_category',1))
                        or ($params->get('show_create_date',1)) or ($params->get('show_modify_date',1))
                        or ($params->get('show_publish_date',1)) or ($params->get('show_parent_category',1))
                        or ($params->get('show_hits',1)) or ($params->get('show_tags',1))) : ?>
                                </div>
                            <?php endif; ?>
                        <?php if($params -> get('show_readmore',1)):?>
                        <a class="btn btn-primary TzPortfolioReadmore<?php if($params -> get('tz_use_lightbox') == 1){echo ' fancybox fancybox.iframe';}?>" href="<?php echo $row ->link; ?>">
                            <?php echo JText::sprintf('COM_TZPORTFOLIO_READMORE'); ?>
                        </a>
                        <?php endif;?>

                        <?php //Call event onContentAfterDisplay and onTZPluginAfterDisplay on plugin?>
                        <?php echo $row->event->afterDisplayContent; ?>
                        <?php echo $row->event->TZafterDisplayContent; ?>
                    </div>
                <?php else:?>
                    <?php echo $this -> loadTemplate('link');?>

                    <?php echo $this -> loadTemplate('quote');?>
                <?php endif;?>
            </div><!--Inner-->
        </div>
    <?php endforeach;?>
<?php endif;?>