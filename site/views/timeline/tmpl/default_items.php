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

$doc    = JFactory::getDocument();
$list   = $this -> listsArticle;
$params = &$this -> params;

JFactory::getLanguage()->load('com_content');
JFactory::getLanguage()->load('com_tz_portfolio');

?>
<?php if($list):?>
    <?php $i=0;?>
    <?php $j=0;?>
    <?php $k=($this -> limitstart+1) %2;?>
    <?php foreach($list as $row):?>
        <?php
            if($row -> tz_image):
                $doc -> addStyleDeclaration('.ss-circle-'.$row -> id.'{
                    background-image: url('.$row -> tz_image.');
                }');
            endif;
        ?>
        <?php
            $strMonthLink   = date('M',strtotime($row -> created));
            $strMonthLink   = strtolower($strMonthLink);
            $strMonth       = date('F',strtotime($row -> created));
            $strMonth       = strtolower($strMonth);
        ?>
        <?php if($j == 0):?>
            <div class="ss-row">
                <div class="ss-left">
                    <h2 id="<?php echo $strMonthLink?><?php echo $row -> year;?>"><?php echo $strMonth;?></h2>
                </div>
                <div class="ss-right">
                    <h2><?php echo $row -> year;?></h2>
                </div>
            </div>
        <?php endif;?>
        <?php if($j != 0 AND $list[$j-1] -> tz_date  != $list[$j] -> tz_date):?>

            <div class="ss-row">
                <div class="ss-left">
                    <h2 id="<?php echo $strMonthLink?><?php echo $row -> year;?>"><?php echo $strMonth;?></h2>
                </div>
                <div class="ss-right">
                    <h2><?php echo $row -> year;?></h2>
                </div>
            </div>
            <?php $i=0;?>
        <?php endif;?>

        <?php
            switch ($i % 3):
                case '0':
                    $class  = 'small';
                    break;
                case '1':
                    $class  = 'medium';
                    break;
                case '2':
                    $class  = 'large';
                    break;
            endswitch;
        ?>
        <div class="ss-row ss-<?php echo $class?>">
            <?php if($k%2 == 0):?>
                <div class="ss-left">
                    <a href="<?php echo $row -> _link;?>"
                       class="ss-circle<?php if($row -> tz_image){ echo ' ss-circle-'.$row -> id;}?><?php if($params -> get('tz_use_lightbox') == 1){echo ' fancybox fancybox.iframe';}?>">
                        <?php echo $row -> title;?>
                    </a>
                </div>
                <div class="ss-right">
                    <h3>
                        <span><?php echo date('F m, Y',strtotime($row -> created));?></span>
                        <a<?php if($params -> get('tz_use_lightbox') == 1){echo ' class="fancybox fancybox.iframe"';}?> href="<?php echo $row -> _link;?>">
                            <?php echo $row -> title;?>
                        </a>
                        <?php echo $row -> event -> beforeDisplayContent;?>

                        <?php if (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date')) or ($params->get('show_parent_category')) or ($params->get('show_hits'))) : ?>
                            <div class="TzArticle-info">
                        <?php endif; ?>

                        <?php if ($params->get('show_category')) : ?>
                            <span class="TZcategory-name">
                                <?php $title = $this->escape($row->category_title);
                                $url = '<a href="' . JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($row->catid)) . '">' . $title . '</a>'; ?>
                                <?php if ($params->get('link_category')) : ?>
                                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
                                <?php else : ?>
                                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($params->get('show_modify_date')) : ?>
                            <span class="TzPortfolioModified">
                                <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $row->modified, JText::_('DATE_FORMAT_LC2'))); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($params->get('show_publish_date')) : ?>
                            <span class="published">
                                <?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $row->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($params->get('show_author') && !empty($row->author )) : ?>
                            <span class="TzPortfolioCreatedby">
                                <?php $author =  $row->author; ?>
                                <?php $author = ($row->created_by_alias ? $row->created_by_alias : $author);?>

                                <?php if ($params->get('link_author') == true):?>
                                <?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
                                    JHtml::_('link', JRoute::_('index.php?option=com_tz_portfolio&amp;view=users&amp;created_by='.$row -> created_by), $author)); ?>

                                <?php else :?>
                                <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($params->get('show_hits')) : ?>
                            <span class="TzPortfolioHits">
                                <?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $row->hits); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date')) or ($params->get('show_parent_category')) or ($params->get('show_hits'))) :?>
                            </div>
                        <?php endif; ?>

                        <?php
                            $extraFields    = JModel::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
                            $extraFields -> setState('article.id',$row -> id);
                            $extraFields -> setState('category.id',$row -> catid);

                            $extraParams    = $extraFields -> getParams();
                            $itemParams     = new JRegistry($row -> attribs);

                            if($itemParams -> get('tz_fieldsid'))
                                $extraParams -> set('tz_fieldsid',$itemParams -> get('tz_fieldsid'));

                            $extraFields -> setState('params',$extraParams);
                            $this -> item -> params = $extraParams;
                            $this -> assign('listFields',$extraFields -> getExtraFields());
                        ?>
                        <?php echo $this -> loadTemplate('extrafields');?>
                    </h3>
                </div>
            <?php else:?>
                <div class="ss-left">
                    <h3>
                        <span><?php echo date('F m, Y',strtotime($row -> created));?></span>
                        <a<?php if($params -> get('tz_use_lightbox') == 1){echo ' class="fancybox fancybox.iframe"';}?> href="<?php echo $row -> _link;?>">
                            <?php echo $row -> title;?>
                        </a>
                        <?php echo $row -> event -> beforeDisplayContent;?>

                        <?php if (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date')) or ($params->get('show_parent_category')) or ($params->get('show_hits'))) : ?>
                            <div class="TzArticle-info">
    <!--                        <dt class="article-info-term">--><?php //echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?><!--</dt>-->
                        <?php endif; ?>

                        <?php if ($params->get('show_category')) : ?>
                            <span class="TZcategory-name">
                                <?php $title = $this->escape($row->category_title);
                                $url = '<a href="' . JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($row->catid)) . '">' . $title . '</a>'; ?>
                                <?php if ($params->get('link_category')) : ?>
                                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
                                <?php else : ?>
                                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($params->get('show_modify_date')) : ?>
                            <span class="TzPortfolioModified">
                                <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $row->modified, JText::_('DATE_FORMAT_LC2'))); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($params->get('show_publish_date')) : ?>
                            <span class="published">
                                <?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $row->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($params->get('show_author') && !empty($row->author )) : ?>
                            <span class="TzPortfolioCreatedby">
                                <?php $author =  $row->author; ?>
                                <?php $author = ($row->created_by_alias ? $row->created_by_alias : $author);?>

                                <?php if ($params->get('link_author') == true):?>
                                <?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' ,
                                    JHtml::_('link', JRoute::_('index.php?option=com_tz_portfolio&amp;view=users&amp;created_by='.$row -> created_by), $author)); ?>

                                <?php else :?>
                                <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($params->get('show_hits')) : ?>
                            <span class="TzPortfolioHits">
                                <?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $row->hits); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date')) or ($params->get('show_parent_category')) or ($params->get('show_hits'))) :?>
                            </div>
                        <?php endif; ?>

                        <?php
                            $extraFields    = JModel::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
                            $extraFields -> setState('article.id',$row -> id);
                            $extraFields -> setState('category.id',$row -> catid);

                            $extraParams    = $extraFields -> getParams();
                            $itemParams     = new JRegistry($row -> attribs);

                            if($itemParams -> get('tz_fieldsid'))
                                $extraParams -> set('tz_fieldsid',$itemParams -> get('tz_fieldsid'));

                            $extraFields -> setState('params',$extraParams);
                            $this -> item -> params = $extraParams;
                            $this -> assign('listFields',$extraFields -> getExtraFields());
                        ?>
                        <?php echo $this -> loadTemplate('extrafields');?>

                    </h3>
                </div>
                <div class="ss-right">
                    <a href="<?php echo $row -> _link;?>"
                       class="ss-circle<?php if($row -> tz_image){ echo ' ss-circle-'.$row -> id; }?><?php if($params -> get('tz_use_lightbox') == 1){echo ' fancybox fancybox.iframe';}?>">
                        <?php echo $row -> title;?>
                    </a>
                </div>
            <?php endif;?>
        </div>

        <?php $k++;?>
        <?php $j++;?>
        <?php $i++;?>
    <?php endforeach;?>
<?php endif;?>