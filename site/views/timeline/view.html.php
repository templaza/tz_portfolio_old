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
    protected $item = null;
    protected $media        = null;
    public $extraFields  = null;

    function __construct($config = array()){
        $this -> item           = new stdClass();
        $this -> media          = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
        $this -> extraFields    = JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
        parent::__construct($config);
    }

    
    function display($tpl=null){
        JHtml::_('behavior.framework');
        $menus		= JMenu::getInstance('site');
        $active     = $menus->getActive();
        $state      = $this -> get('State');

        $_params    = $state -> get('params');

        $params = $this -> get('Params');

        if($params -> get('fields_option_order')){
            switch($params -> get('fields_option_order')){
                case 'alpha':
                    $fieldsOptionOrder  = 't.value ASC';
                    break;
                case 'ralpha':
                    $fieldsOptionOrder  = 't.value DESC';
                    break;
                case 'ordering':
                    $fieldsOptionOrder  = 't.ordering ASC';
                    break;
            }
            if(isset($fieldsOptionOrder)){
                $this -> extraFields -> setState('filter.option.order',$fieldsOptionOrder);
            }
        }

        $list       = $this -> get('Article');
        
        $this -> assign('listsArticle',$list);

        if($params -> get('show_all_filter',0)){
            $this -> assign('listsTags',$this -> get('AllTags'));
            $this -> assign('listsCategories',$this -> get('AllCategories'));
        }
        else{
            $this -> assign('listsTags',$this -> get('Tags'));
            $this -> assign('listsCategories',$this -> get('Categories'));
        }

        // Set value again for option tz_portfolio_redirect
        if($_params -> get('tz_portfolio_redirect') == 'default'){
            $_params -> set('tz_portfolio_redirect','article');
        }

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

        if ($active)
        {
            $params->def('page_heading', $params->get('page_title', $active->title));
        }
        else
        {
            $params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
        }
        
        $this -> assign('listsCatDate',$this -> get('DateCategories'));
        $this -> assign('params',$_params);
        $this -> assign('pagination',$this -> get('Pagination'));
        $this -> assign('Itemid',$active -> id);
        $this -> assign('limitstart',$state -> get('list.start'));

        $model  = JModelLegacy::getInstance('Portfolio','TZ_PortfolioModel',array('ignore_request' => true));
        $model -> setState('params',$params);
        $this -> assign('char',$state -> get('char'));
        $this -> assign('availLetter',$model -> getAvailableLetter());

        $params = $this -> get('Params');

        $csscompress    = null;
        if($params -> get('css_compression',0)){
            $csscompress    = '.min';
        }

        $jscompress         = new stdClass();
        $jscompress -> extfile  = null;
        $jscompress -> folder   = null;
        if($params -> get('js_compression',1)){
            $jscompress -> extfile  = '.min';
            $jscompress -> folder   = '/packed';
        }

        $doc    = JFactory::getDocument();

        if($params -> get('tz_timeline_layout',null) == 'classic'):
            $doc -> addStyleSheet('components/com_tz_portfolio/css/timeline_classic'.$csscompress.'.css');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/jquery.easing.1.3'.$jscompress -> extfile.'.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/modernizr.custom.11333'.$jscompress -> extfile.'.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/blog'.$jscompress -> extfile.'.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery().tzSlideScroll({
                        scollPageSpeed: '.$params -> get('classic_page_speed',2000).',
                        scollPageEasing: "'.$params -> get('classic_page_easing').'",
                        hasPerspective: '.$params -> get('classic_perspective',0).'
                    });
                });
            </script>');
        else:
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/jquery.infinitescroll.min'.$jscompress -> extfile.'.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/jquery.isotope'.$jscompress -> extfile.'.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/html5'.$jscompress -> extfile.'.js"></script>');
            $doc -> addStyleSheet('components/com_tz_portfolio/css/isotope'.$csscompress.'.css');
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
        if($params -> get('tz_timeline_layout',null) != 'classic'){
            if($params -> get('tz_portfolio_layout') == 'ajaxButton' || $params -> get('tz_portfolio_layout') == 'ajaxInfiScroll'){
                if($params -> get('tz_portfolio_layout') == 'ajaxButton'){
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
                if($params -> get('tz_portfolio_layout') == 'ajaxInfiScroll'){
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
        }
        
        if($params -> get('tz_use_lightbox',1) == 1){
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/jquery.fancybox.pack'.$jscompress -> extfile.'.js"></script>');
            $doc -> addStyleSheet('components/com_tz_portfolio/css/fancybox'.$csscompress.'.css');

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
            $scrollHidden   = null;
            if($params -> get('use_custom_scrollbar',1)){
                $scrollHidden   = ',scrolling: "no"
                                    ,iframe: {
                                        scrolling : "no",
                                    }';
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
                            css : {background: "rgba(0,0,0,'.$params -> get('tz_lightbox_opacity',0.75).')"}
                        }
                    }'
                    .$scrollHidden.'
                });
                </script>
            ');
        }

        if ($layout = $params -> get('tz_timeline_layout','default')) {
            if($layout == 'default'){
                $doc -> addStyleSheet('components/com_tz_portfolio/css/tzportfolio'.$csscompress.'.css');

                if($params -> get('comment_function_type','default') == 'js'){
                    if($params -> get('tz_show_count_comment',1)){
                        if($params -> get('tz_comment_type') == 'facebook' ||
                                $params -> get('tz_comment_type') == 'disqus'){
                            $doc -> addCustomTag('<script src="components/com_tz_portfolio/js'.
                            $jscompress -> folder.'/base64'.$jscompress -> extfile.'.js" type="text/javascript"></script>');
                        }
                    }
                }

                if($params -> get('tz_show_filter',1) || ($params -> get('tz_show_count_comment',1) &&
                        ($params -> get('tz_comment_type') == 'facebook' ||
                            $params -> get('tz_comment_type') == 'disqus')) ){
                    $doc -> addCustomTag('<script src="components/com_tz_portfolio/js'.
                        $jscompress -> folder.'/tz_portfolio'.$jscompress -> extfile.'.js" type="text/javascript"></script>');
                }
            }

            $this->setLayout($layout);
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

    protected function FindUserItemId($_userid=null){
        $app		= JFactory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        if($_userid){
            $userid    = intval($_userid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio');
        $items		= $menus->getItems('component_id', $component->id);

        if($this -> params -> get('user_menu_active') && $this -> params -> get('user_menu_active') != 'auto'){
            return $this -> params -> get('user_menu_active');
        }

        foreach ($items as $item)
        {
            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];

                if (isset($item -> query['created_by'])) {
                    if ($item->query['created_by'] == $userid) {
                        return $item -> id;
                    }
                }
                else{
                    if($item -> home == 1){
                        $homeId = $item -> id;
                    }
                }
            }
        }

        if(!isset($active -> id)){
            return $homeId;
        }

        return $active -> id;
    }
}