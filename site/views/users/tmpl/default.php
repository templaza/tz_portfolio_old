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

$lists  = $this -> listsUsers;
$params = &$this -> params;
$canEdit    = $params -> get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');
?>
<link rel="stylesheet/less" type="text/css" href="components/com_tz_portfolio/css/tz_lib_style.less">
<script src="components/com_tz_portfolio/js/less-1.3.3.min.js" type="text/javascript"></script>

<?php if($lists):?>

    <div class="TzUser">
        <div class="TzUserInner">

            <?php echo  $this -> loadTemplate('author');?>

            <?php if($params -> get('use_filter_first_letter',1)):?>
                <div class="TzLetters">
                    <div class="breadcrumb">
                        <?php echo $this -> loadTemplate('letters');?>
                    </div>
                </div>
            <?php endif;?>

            <?php if($params -> get('show_limit_box',1)):?>
            <form action="index.php?option=com_tz_portfolio&amp;view=users&amp;created_by=<?php echo JRequest::getCmd('created_by')?>&amp;Itemid=<?php echo JRequest::getInt('Itemid')?>"
                  id="adminForm"
                  name="adminForm"
                  method="post">


                    <div class="display-limit">
                        <fieldset class="filters">
                            <?php echo  JText::_('JGLOBAL_DISPLAY_NUM');?>
                            <?php echo $this -> pagination -> getLimitBox();?>
                        </fieldset>
                    </div>
            <?php endif;?>

                <?php $i=0;?>
                <?php foreach($lists as $row):?>
                    <?php
                        $tmpl   = null;
                        if($params -> get('tz_use_lightbox',1) == 1){
                            $tmpl   = '&tmpl=component';
                        }
                        $this -> get('state') -> set('users.catid',$row -> catid);
                        $itemType   = $this -> get('FindType');

                        $itemParams = new JRegistry($row -> attribs); //Get Article's Params
                
                        $_itemId     = $this -> get('FindItemId');

                        if($itemParams -> get('tz_portfolio_redirect')){
                            $tzRedirect = $itemParams -> get('tz_portfolio_redirect');
                        }

                        if($tzRedirect == 'p_article'){
                            $itemType = 1;
                        }
                        elseif($tzRedirect == 'article'){
                            $itemType = 0;
                        }
                
                        if($itemType){
                            $itemLink       = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($row -> slug, $row -> catid).$tmpl);
                            $commentLink    = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($row -> slug, $row -> catid),true,-1);
                        }
                        else{
                            $itemLink       = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($row -> slug, $row -> catid).$tmpl);
                            $commentLink    = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($row -> slug, $row -> catid),true,-1);
                        }
                    ?>
                    <div class="<?php if($i == 0): echo 'TzItemsLeading'; else: echo 'TzItemsRow row-0'; endif;?>">
                        <div class="TzLeading leading-0">

                            <?php
                                 if($params -> get('show_image',1) == 1 OR $params -> get('show_image_gallery',1) == 1
                                         OR $params -> get('show_video',1) == 1):
                            ?>
                                <?php
                                $media          = &JModelLegacy::getInstance('Media','TZ_PortfolioModel');
                                $mediaParams    = $this -> mediaParams;
                                $mediaParams -> merge($media -> getCatParams($row -> catid));

                                $media -> setParams($mediaParams);
                                $listMedia      = $media -> getMedia($row -> id);

                                $this -> assign('mediaParams',$mediaParams);
                                $this -> assign('listMedia',$listMedia);

                                $this -> assign('itemLink',$itemLink);

                                echo $this -> loadTemplate('media');
                                ?>
                            <?php endif;?>

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
                                <h3 class="TzUserTitle">
                                    <?php if($params->get('link_titles',1)) : ?>
                                        <a<?php if($params -> get('tz_use_lightbox') == 1){echo ' class="fancybox fancybox.iframe"';}?> href="<?php echo $itemLink; ?>">
                                            <?php echo $this->escape($row -> title); ?>
                                        </a>
                                    <?php else : ?>
                                    <?php echo $this->escape($row -> title); ?>
                                    <?php endif; ?>


                                </h3>
                            <?php endif;?>

                            <?php if (!$params->get('show_intro',1)) : ?>
                                <?php //Call event onContentAfterTitle and TZPluginDisplayTitle on plugin?>
                                <?php echo $row -> event -> afterDisplayTitle; ?>
                                <?php echo $row -> event -> TZafterDisplayTitle; ?>
                            <?php endif; ?>

                            <?php if (($params->get('show_author',1)) or ($params->get('show_category',1)) or ($params->get('show_create_date',1)) or ($params->get('show_modify_date',1)) or ($params->get('show_publish_date',1)) or ($params->get('show_parent_category',1)) or ($params->get('show_hits',1))) : ?>
                                <div class="TzUserArticleInfo">
                            <?php endif; ?>

                            <?php if ($params->get('show_create_date',1)) : ?>
                                <span class="TzUserCreate">
                                      <span class="date"> <?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $row->created, JText::_('DATE_FORMAT_LC2'))); ?></span>
                                  </span>
                            <?php endif; ?>

                            <span class="TzVote">
                                <?php echo $row -> event -> TZPortfolioVote; ?>
                                <span class="TzMilling">,&nbsp;</span>
                            </span>

                             <?php if ($params->get('show_hits',1)) : ?>
                            <span class="TzUserHits">
                                <span class="numbers"><?php echo  $row->hits; ?></span>
                                <span class="hits"><?php echo JText::_('ARTICLE_HITS'); ?></span>
                            </span>
                            <?php endif; ?>

                            <?php if ($params->get('show_category',1)) : ?>
                            <span class="TZUserCategoryName">
                                <?php $title = $this->escape($row->category_title);
                                $url = '<a href="' . JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($row->catid)) . '">' . $title . '</a>'; ?>
                                <?php if ($params->get('link_category',1)) : ?>
                                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
                                <?php else : ?>
                                <?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
                                <?php endif; ?>
                            </span>
                            <?php endif; ?>

                            <?php if ($params->get('show_modify_date',1)) : ?>
                            <span class="TzUserModified">
                                <?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $row->modified, JText::_('DATE_FORMAT_LC2'))); ?>
                            </span>
                            <?php endif; ?>
                            <?php if ($params->get('show_publish_date',1)) : ?>
                            <span class="TzUserPublished">
                                <?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $row->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
                            </span>
                            <?php endif; ?>

                            <?php if($params -> get('tz_show_count_comment',1) == 1):?>
                                <span class="TzPortfolioCommentCount">
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
                                    <?php if($params -> get('tz_comment_type') == 'disqus'):?>
                                        <?php if(isset($row -> commentCount)):?>
                                            <?php echo $row -> commentCount;?>
                                        <?php endif;?>
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

                            <?php if (($params->get('show_author',1)) or ($params->get('show_category',1)) or ($params->get('show_create_date',1)) or ($params->get('show_modify_date',1)) or ($params->get('show_publish_date',1)) or ($params->get('show_parent_category',1)) or ($params->get('show_hits',1))) :?>
                                </div>
                            <?php endif; ?>

                            <?php
                                $app    = &JFactory::getApplication();
                                $menus  = $app -> getMenu('site');
                                $_menu  = $menus ->getItem($_itemId);
                                $exParams   = $params;
                                $exParams -> merge($_menu -> params);

                                $extraFields    = &JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
                                $extraFields -> setState('article.id',$row -> id);
                                $extraFields -> setState('category.id',$row -> catid);

                                $extraParams    = $extraFields -> getParams();
                                $itemParams     = new JRegistry($row -> attribs);

                                if($itemParams -> get('tz_fieldsid'))
                                    $extraParams -> set('tz_fieldsid',$itemParams -> get('tz_fieldsid'));

                                $extraFields -> setState('params',$extraParams);
                                $this -> item -> params = $extraParams;
                                $extraFields -> setState('orderby',$exParams -> get('fields_order'));
                                $this -> assign('userFields',$extraFields -> getExtraFields());
                            ?>
                            <?php echo $this -> loadTemplate('extrafields');?>

                            <?php echo $row -> text;?>

                            <?php //Call event onContentBeforeDisplay and onTZPluginBeforeDisplay on plugin?>
                            <?php echo $row -> event -> beforeDisplayContent; ?>
                            <?php echo $row -> event -> TZbeforeDisplayContent; ?>

                            <?php  if (!$params->get('show_intro')) :
                                echo $row->event->afterDisplayTitle;
                            endif; ?>


                            <?php if ($params->get('show_readmore',1) && $row->readmore) :
                                if ($params->get('access-view')) :
                                    $link   = $itemLink;
                                else :
                                    $menu = JFactory::getApplication()->getMenu();
                                    $active = $menu->getActive();
                                    $itemId = $active->id;
                                    $link1 = JRoute::_('index.php?option=com_users&amp;view=login&amp;Itemid=' . $itemId);
                                    $returnURL   = $itemLink;
                                    $link = new JURI($link1);
                                    $link->setVar('return', base64_encode($returnURL));
                                endif;
                            ?>
                                    
                            <?php if($params -> get('show_readmore',1) == 1):?>
                                    <a class="TzReadmore<?php if($params -> get('tz_use_lightbox') == 1){echo ' fancybox fancybox.iframe';}?>"
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
                        
                        </div>
                    </div>
                    <?php $i++;?>
                <?php endforeach;?>

                <?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
                    <div class="TzPagination">

                        <?php echo $this->pagination->getPagesLinks(); ?>

                       <?php  if ($this->params->def('show_pagination_results', 1)) : ?>
                        <p class="TzCounter">
                            <?php echo $this->pagination->getPagesCounter(); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                <?php endif;?>

            <?php if($params -> get('show_limit_box',1)):?>
            </form>
            <?php endif;?>
        </div>
    </div>
<?php endif;?>

