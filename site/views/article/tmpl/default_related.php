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

if (!$this->print) :
    $doc    = JFactory::getDocument();

    $lists  = $this -> itemMore;
    // Create shortcuts to some parameters.
    $params		= $this->item->params;
    $tmpl       = null;
    if($params -> get('tz_use_lightbox') == 1){
        $tmpl   = '&tmpl=component';
    }
    if($lists):
        if($params -> get('show_related_article',1)):
?>
<div class="TzRelated">
    <?php if($params -> get('show_related_heading',1)):?>
        <?php
            $title    = JText::_('COM_TZ_PORTFOLIO_RELATED_ARTICLE');
            if($params -> get('related_heading')){
                $title  = $params -> get('related_heading');
            }
        ?>
        <h3 class="TzRelatedTitle"><?php echo $title;?></h3>
    <?php endif;?>
    <ul>
    <?php $media      = JModelLegacy::getInstance('Media','TZ_PortfolioModel');?>
    <?php foreach($lists as $i => $item):?>
        <?php

            $tzRedirect = $params -> get('tz_portfolio_redirect','p_article'); //Set params for $tzRedirect
            $itemParams = new JRegistry($item -> attribs); //Get Article's Params
            //Check redirect to view article
            if($itemParams -> get('tz_portfolio_redirect')){
                $tzRedirect = $itemParams -> get('tz_portfolio_redirect');
            }
            if($tzRedirect == 'p_article'){
                $item -> _link = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid).$tmpl);
            }
            else{
                $item -> _link = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid).$tmpl);
            }

            $listMedia      = $media -> getMedia($item -> id);
            $src    = null;
            if($listMedia){
                if($listMedia[0] -> type != 'quote' && $listMedia[0] -> type != 'link'){
                    if($listMedia[0] -> type == 'video' || $listMedia[0] -> type == 'audio'){
                        $src    = $listMedia[0] -> thumb;
                    }
                    else{
                        $src    = $listMedia[0] -> images;
                    }
                }
            }
        ?>
    <?php if(($params -> get('show_related_type','title_image') == 'image' AND $src)
             OR $params -> get('show_related_type','title_image') == 'title'
            OR $params -> get('show_related_type','title_image') == 'title_image'):?>
    <li class="TzItem<?php if($i == 0) echo ' first'; if($i == count($lists) - 1) echo ' last';?>">
    <?php endif;?>
        <?php if($params -> get('show_related_type','title_image') == 'image'
                 OR $params -> get('show_related_type','title_image') == 'title_image'):?>
            <?php

                if($src):
                    $size   = $params -> get('related_image_size','S');
                    $src    = str_replace('.'.JFile::getExt($src),'_'.$size.'.'.JFile::getExt($src),$src);
            ?>
            <div class="TzImage">
                <a<?php if($params -> get('tz_use_lightbox',1) == 1){echo ' class="fancybox fancybox.iframe"';}?>
                        href="<?php echo $item -> _link;?>">
                    <img src="<?php echo $src;?>" alt="<?php echo $item -> title?>" title="<?php echo $item -> title;?>"/>
                </a>
            </div>
            <?php endif;?>
        <?php endif;?>
        <?php if($params -> get('show_related_type','title_image') == 'title'
                 OR($params -> get('show_related_type','title_image') == 'title_image')):?>
        <a href="<?php echo $item -> _link;?>"
           class="TzTitle<?php if($params -> get('tz_use_lightbox',1) == 1){echo ' fancybox fancybox.iframe';}?>">
            <?php echo $item -> title;?>
        </a>
        <?php endif;?>
    <?php if(($params -> get('show_related_type','title_image') == 'image' AND $src)
             OR $params -> get('show_related_type','title_image') == 'title'
            OR $params -> get('show_related_type','title_image') == 'title_image'):?>
    </li>
    <?php endif;?>

    <?php endforeach;?>
    </ul>
</div>
 
        <?php endif;?>
    <?php endif;?>
<?php endif;?>