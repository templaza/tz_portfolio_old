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
    protected $item         = null;
    protected $media        = null;
    public $extraFields     = null;
    protected $ajaxLink     = null;
    protected $lang_sef     = '';

    function __construct($config = array()){
        $this -> item           = new stdClass();
        $this -> media          = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
        $this -> extraFields    = JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
        parent::__construct($config);
    }

    
    function display($tpl=null){
        $app        = JFactory::getApplication('site');
        $input      = $app -> input;
        $language   = JLanguageHelper::getLanguages('lang_code');

        JHtml::_('behavior.framework');
        $menus		= JMenu::getInstance('site');
        $active     = $menus->getActive();
        $state      = $this -> get('State');

        // Create ajax link
        $this -> ajaxLink   = JURI::root().'index.php?option=com_tz_portfolio&amp;view=portfolio&amp;task=portfolio.ajax'
            .'&amp;layout=item'.(($state -> get('char'))?'&amp;char='.$state -> get('char'):'');
        // If your site has used multilanguage
        if($lang = $input -> get('lang')){
            $this -> lang_sef   = $language[$lang] -> sef;
            $this -> ajaxLink   .= '&amp;lang='.$language[$lang] -> sef;
        }
        $this -> ajaxLink   .= '&amp;Itemid='.$active -> id.'&amp;page=2';

        $params    = $state -> get('params');

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
        if($params -> get('tz_portfolio_redirect') == 'default'){
            $params -> set('tz_portfolio_redirect','article');
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
        $this -> assign('params',$params);
        $this -> assign('pagination',$this -> get('Pagination'));
        $this -> assign('Itemid',$active -> id);
        $this -> assign('limitstart',$state -> get('list.start'));

        $model  = JModelLegacy::getInstance('Portfolio','TZ_PortfolioModel',array('ignore_request' => true));
        $model -> setState('params',$params);
        $this -> assign('char',$state -> get('char'));
        $this -> assign('availLetter',$model -> getAvailableLetter());

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
            $doc -> addStyleSheet('components/com_tz_portfolio/css/timeline_classic.min.css');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/jquery.easing.1.3.min.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/modernizr.custom.11333.min.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/blog.min.js"></script>');
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
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/jquery.infinitescroll.min.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/jquery.isotope.min.js"></script>');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/html5.js"></script>');
            $doc -> addStyleSheet('components/com_tz_portfolio/css/isotope.min.css');
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
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/jquery.fancybox.pack.js"></script>');
            $doc -> addStyleSheet('components/com_tz_portfolio/css/fancybox.min.css');

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
                $doc -> addStyleSheet('components/com_tz_portfolio/css/tzportfolio.min.css');

                if($params -> get('comment_function_type','default') == 'js'){
                    if($params -> get('tz_show_count_comment',1)){
                        if($params -> get('tz_comment_type') == 'facebook' ||
                                $params -> get('tz_comment_type') == 'disqus'){
                            $doc -> addCustomTag('<script src="components/com_tz_portfolio/js'
                                .'/base64.js" type="text/javascript"></script>');
                        }
                    }
                }

                $doc -> addCustomTag('<script src="components/com_tz_portfolio/js'.
                    '/tz_portfolio.min.js" type="text/javascript"></script>');
            }

            $this->setLayout($layout);
        }

        $this -> _prepareDocument();

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

    protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $title  = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
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