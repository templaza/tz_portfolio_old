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
jimport('joomla.application.component.view');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');

class TZ_PortfolioViewTimeLine extends JViewLegacy
{
    function display($tpl=null){
        $menus		= JMenu::getInstance('site');
        $active     = $menus->getActive();
        $state      = $this -> get('State');

        $params = $this -> get('Params');

        $list       = $this -> get('Article');
        
        $this -> assign('listsArticle',$list);
        $this -> assign('listsTags',$this -> get('Tags'));
        $this -> assign('listsCategories',$this -> get('Categories'));
        $this -> assign('listsCatDate',$this -> get('DateCategories'));
        $this -> assign('params',$this -> get('Params'));
        $this -> assign('pagination',$this -> get('Pagination'));
        $this -> assign('Itemid',$active -> id);
        $this -> assign('limitstart',$state -> get('offset'));

        $params = $this -> get('Params');

        $doc    = &JFactory::getDocument();
        $doc -> addStyleSheet('components/com_tz_portfolio/css/timeline/blog.css');
        if($params -> get('tz_timeline_layout') == 'default'):

            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js/jquery.easing.1.3.js"></script>');
            $doc -> addCustemTag('<script type="text/javascript" src="components/com_tz_portfolio/js/blog.js"></script>');
        else:
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js/jquery.infinitescroll.min.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js/jquery.isotope.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js/html5.js"></script>');
            $doc -> addStyleSheet('components/com_tz_portfolio/css/style.css');
        endif;

        if($params -> get('tz_use_image_hover',1) == 1):
            $doc -> addStyleDeclaration('
                .tz_image_hover{
                    opacity: 0;
                    position: absolute;
                    top:0;
                    left: 0;
                    transition: opacity '.$params -> get('tz_image_timeout',0.35).'s ease-in-out;
                   -moz-transition: opacity '.$params -> get('tz_image_timeout',0.35).'s ease-in-out;
                   -webkit-transition: opacity '.$params -> get('tz_image_timeout',0.35).'s ease-in-out;
                }
                .tz_image_hover:hover{
                    opacity: 1;
                    margin: 0;
                }
            ');
        endif;
        if($params -> get('tz_timeline_layout') == 'ajaxButton' || $params -> get('tz_timeline_layout') == 'ajaxInfiScroll'){
            if($params -> get('tz_timeline_layout') == 'ajaxButton'){
                $doc->addStyleDeclaration('
                    #infscr-loading {
                        position: absolute;
                        padding: 0;
                        left: 35%;
                        bottom:0;
                        background:none;
                    }
                    #infscr-loading div,#infscr-loading img{
                        display:inline-block;
                    }
                ');
            }
            if($params -> get('tz_timeline_layout') == 'ajaxInfiScroll'){
                $doc->addStyleDeclaration('
                    #tz_append{
                        cursor: auto;
                    }
                    #tz_append a{
                        color:#000;
                        cursor:auto;
                    }
                    #tz_append a:hover{
                        color:#000 !important;
                    }
                    #infscr-loading {
                        position: absolute;
                        padding: 0;
                        left: 38%;
                        bottom:-35px;
                    }

                ');
            }
        }
        if($params -> get('tz_use_lightbox',1) == 1){
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js/jquery.fancybox.pack.js"></script>');
            $doc -> addStyleSheet('components/com_tz_portfolio/assets/jquery.fancybox.css');

            $width      = null;
            $height     = null;
            $autosize   = null;
            if($params -> get('tz_lightbox_width')){
                if(preg_match('/%|px/',$params -> get('tz_lightbox_width'))){
                    $width  = 'width:\''.$params -> get('tz_lightbox_width').'\',';
                }
                else
                    $width  = 'width:'.$params -> get('tz_lightbox_width').',';
            }
            if($params -> get('tz_lightbox_height')){
                if(preg_match('/%|px/',$params -> get('tz_lightbox_height'))){
                    $height  = 'height:\''.$params -> get('tz_lightbox_height').'\',';
                }
                else
                    $height  = 'height:'.$params -> get('tz_lightbox_height').',';
            }
            if($width || $height){
                $autosize   = 'fitToView: false,autoSize: false,';
            }
            $doc -> addCustomTag('<script type="text/javascript">
                jQuery(\'.fancybox\').fancybox({
                    type:\'iframe\',
                    openSpeed:'.$params -> get('tz_lightbox_speed',350).',
                    openEffect: "'.$params -> get('tz_lightbox_transition','elastic').'",
                    '.$width.$height.$autosize.'
		            helpers:  {
                        title : {
                            type : "inside"
                        },
                        overlay : {
                            opacity:'.$params -> get('tz_lightbox_opacity',0.75).',
                        }
                    }
                });
                </script>
            ');
        }

        // Add feed links
		if ($params->get('show_feed_link', 1)) {
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$doc->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$doc->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}
        parent::display($tpl);
    }
}