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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML Article View class for the Content component.
 */
class TZ_PortfolioViewArticle extends JViewLegacy
{
	protected $item;
	protected $params;
	protected $print;
	protected $state;
	protected $user;

	function display($tpl = null)
	{
        // Initialise variables.
		$app		= JFactory::getApplication();

        $doc    = JFactory::getDocument();
        $tmpl   = JRequest::getString('tmpl');
        if($tmpl){
            JHtml::_('bootstrap.framework');
            JHtml::_('jquery.framework');
        }

        $media          = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
        $listMedia      = $media -> getMedia();

        $attach         = JModelLegacy::getInstance('Attachments','TZ_PortfolioModel');
        $tzUser         = JModelLegacy::getInstance('User','TZ_PortfolioModel');
        $tzTags         = JModelLegacy::getInstance('Tag','TZ_PortfolioModel');

        $this -> assign('listMedia',$listMedia);
        $this -> assign('listAttach',$attach -> getAttachments());
        $this -> assign('listAuthor',$tzUser -> getUser());
        $this -> assign('listTags',$tzTags -> getTag());

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$dispatcher	= JDispatcher::getInstance();

		$this->item		= $this->get('Item');
        $this -> itemMore   = $this -> get('ItemRelated');

		$this->print = $app->input->getBool('print');
		$this->state	= $this->get('State');
		$this->user		= $user;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Create a shortcut for $item.
		$item = &$this->item;
//        var_dump($item -> params);

		// Add router helpers.
		$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
		$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
		$item->parent_slug	= $item->category_alias ? ($item->parent_id.':'.$item->parent_alias) : $item->parent_id;

		// TODO: Change based on shownoauth
		$item->readmore_link = null;

		// Merge article params. If this is single-article view, menu params override article params
		// Otherwise, article params override menu item params
		$this->params	= $this->state->get('params');

        $csscompress    = null;
        if($this -> params -> get('css_compression',0)){
            $csscompress    = '.min';
        }

        $jscompress         = new stdClass();
        $jscompress -> extfile  = null;
        $jscompress -> folder   = null;
        if($this -> params -> get('js_compression',1)){
            $jscompress -> extfile  = '.min';
            $jscompress -> folder   = '/packed';
        }

		$active	= $app->getMenu()->getActive();
		$temp	= clone ($this->params);

//        $item -> params -> merge($active -> params);

        
		// Check to see which parameters should take priority
		if ($active) {
			$currentLink = $active->link;
			// If the current view is the active item and an article view for this article, then the menu item params take priority
			if (strpos($currentLink, 'view=article') && (strpos($currentLink, '&id='.(string) $item->id))) {
				// $item->params are the article params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);
				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout'])) {
					$this->setLayout($active->query['layout']);
				}
			}
			else {
				// Current view is not a single article, so the article params take priority here
				// Merge the menu item params with the article params so that the article params take priority
				$temp->merge($item->params);
				$item->params = $temp;

				// Check for alternative layouts (since we are not in a single-article menu item)
				// Single-article menu item layout takes priority over alt layout for an article
				if ($layout = $item->params->get('article_layout')) {
					$this->setLayout($layout);
				}
			}
		}
		else {
			// Merge so that article params take priority
			$temp->merge($item->params);
			$item->params = $temp;
			// Check for alternative layouts (since we are not in a single-article menu item)
			// Single-article menu item layout takes priority over alt layout for an article
			if ($layout = $item->params->get('article_layout')) {
				$this->setLayout($layout);
			}
		}

        $url    = JURI::getInstance() -> toString();

        $this -> assign('linkCurrent',$url);


		$offset = $this->state->get('list.offset');

		// Check the view access to the article (the model has already computed the values).
		if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true &&  $user->get('guest') ))) {
            JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;

		}

		if ($item->params->get('show_intro', '1')=='1') {
			$item->text = $item->introtext.' '.$item->fulltext;
		}
		elseif ($item->fulltext) {
			$item->text = $item->fulltext;
		}
		else  {
			$item->text = $item->introtext;
		}

        if($item -> params -> get('tz_show_count_comment',1) == 1){
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');
            $fetch       = new Services_Yadis_PlainHTTPFetcher();
        }
        $tzRedirect = $item->params -> get('tz_portfolio_redirect','p_article'); //Set params for $tzRedirect

        if($tzRedirect == 'p_article'){
            $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug,$item -> catid), true ,-1);
        }
        else{
            $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug,$item -> catid), true ,-1);
        }

        if($item->params -> get('tz_comment_type','disqus') == 'facebook'){
            if($item->params -> get('tz_show_count_comment',1) == 1){

                $url    = 'http://graph.facebook.com/?ids='.$contentUrl;

                $content    = $fetch -> get($url);

                if($content)
                    $content    = json_decode($content -> body);

                if(isset($content -> $contentUrl -> comments))
                    $item -> commentCount   = $content -> $contentUrl  -> comments;
                else
                    $item -> commentCount   = 0;
            }
        }
        if($item ->params -> get('tz_comment_type','disqus') == 'disqus'){
            if($item ->params -> get('tz_show_count_comment',1) == 1){
                $url        = 'https://disqus.com/api/3.0/threads/listPosts.json?api_secret='.$item -> params -> get('disqusApiSecretKey')
							  .'&forum='.$item -> params -> get('disqusSubDomain','templazatoturials')
							  .'&thread=link:'.$contentUrl
							  .'&include=approved';

				$content    = $fetch -> get($url);

				if($content)
					$content    = json_decode($content -> body);
				$content    = $content -> response;
				if(is_array($content)){
					$item -> commentCount	= count($content);
				}
				else{
					$item -> commentCount   = 0;
				}
            }
        }
//
//        $tzparams->soundcloud->get('title');
//        $tzparams->soundcloud->get('alias');
//        $tzparams->media->get('title');
//
//        JPluginHelper::importPlugin('tzportfolio');
//        $results = $dispatcher->trigger('onTZPortfolioPrepare', array ('com_tz_portfolio.article', &$item, &$this->tzparams, $offset));


		//
		// Process the content plugins.
		//
		JPluginHelper::importPlugin('content');
		$results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.article', &$item, &$this->params, $offset));

		$item->event = new stdClass();
		$results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.article', &$item, &$this->params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.article', &$item, &$this->params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.article', &$item, &$this->params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

        $results = $dispatcher -> trigger('onTZPortfolioCommentDisplay',array('com_tz_portfolio.comment',&$item,&$item -> params,$offset));
        $item -> event -> onTZPortfolioCommentDisplay  = trim(implode("\n",$results));

        $results = $dispatcher->trigger('onContentTZPortfolioVote', array('com_tz_portfolio.article', &$item, &$item -> params, $offset));
        $item->event->TZPortfolioVote = trim(implode("\n", $results));

        //Get Plugins Model
        $pmodel = JModelLegacy::getInstance('Plugins','TZ_PortfolioModel',array('ignore_request' => true));
        //Get plugin Params for this article
        $pmodel -> setState('filter.contentid',$item -> id);
        $pluginItems    = $pmodel -> getItems();
        $pluginParams   = $pmodel -> getParams();
        $item -> pluginparams    = clone($pluginParams);

        JPluginHelper::importPlugin('tz_portfolio');
        $results   = $dispatcher -> trigger('onTZPluginPrepare',array('com_tz_portfolio.article', &$item, &$item -> params,&$pluginParams,$offset));

        $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.article', &$item, &$item -> params,&$pluginParams, $offset));
        $item->event->TZafterDisplayTitle = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.article', &$item, &$item -> params,&$pluginParams, $offset));
        $item->event->TZbeforeDisplayContent = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.article', &$item, &$item -> params,&$pluginParams, $offset));
        $item->event->TZafterDisplayContent = trim(implode("\n", $results));
        

		// Increment the hit counter of the article.
		if (!$this->params->get('intro_only') && $offset == 0) {
			$model = $this->getModel();
			$model->hit();
		}

        if($_SERVER){
            if(isset($_SERVER['HTTP_REFERER'])){
                $referLink  = $_SERVER['HTTP_REFERER'];
                if(!empty($referLink)){
                    $router     = JSite::getRouter();
                    $url        = JURI::getInstance($referLink);
                    if($url != JUri::root() && JRequest::getCmd('tmpl')){
                        $parseUrl   = $router->parse($url);
                        if($parseUrl){
                            if($parseUrl['option'] == 'com_tz_portfolio'){
                                if(isset($parseUrl['view'])){
                                    $view   = $parseUrl['view'];
                                }

                                if($view == 'users' || $view == 'tags'){
                                    $this -> state -> set('article.catid',$this -> item -> catid);
                                    $itemId = $this -> get('FindItemId');
                                    $menu   = $app -> getMenu('site');

                                    $mParams    = $menu -> getParams($itemId);
                                    if($mParams -> get('fields_order')){
                                        $item -> params -> set('fields_order',$mParams -> get('fields_order'));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
		
		$extraFields    = JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
        $extraFields -> setState('article.id',JRequest::getInt('id'));
        $extraFields -> setState('params',$item -> params);
        $extraFields -> setState('orderby',$item -> params -> get('fields_order'));
        $this -> assign('blogFields',$extraFields -> getExtraFields());

        $params = $media -> getCatParams($item -> catid);
        
        if($listMedia){
            if($listMedia[0] -> type == 'image'){
                if($params -> get('detail_article_image_size'))
                    $params -> set('article_image_resize',strtolower($params -> get('detail_article_image_size')));
            }
            if($listMedia[0] -> type == 'imagegallery'){
                $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                    $jscompress -> folder.'/jquery.flexslider-min'.$jscompress -> extfile.'.js"></script>');
                $doc -> addStyleSheet('components/com_tz_portfolio/css/flexslider'.$csscompress.'.css');

                if($params -> get('detail_article_image_gallery_size'))
                    $params -> set('article_image_gallery_resize',strtolower($params -> get('detail_article_image_gallery_size')));
                if($item -> params -> get('tz_image_gallery_crop'))
                    $params -> set('article_image_gallery_crop',$params -> get('tz_image_gallery_crop'));
            }
        }

        if($item -> params -> get('useCloudZoom',1) == 1){
            $doc -> addStyleSheet('components/com_tz_portfolio/css/cloud-zoom'.$csscompress.'.css');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/cloud-zoom.1.0.3.min'.$jscompress -> extfile.'.js"></script>');
        }

        if($item -> params -> get('tz_use_lightbox',1) == 1 AND !$tmpl){
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                $jscompress -> folder.'/jquery.fancybox.pack'.$jscompress -> extfile.'.js"></script>');
            $doc -> addStyleSheet('components/com_tz_portfolio/css/fancybox'.$csscompress.'.css');

            $width      = null;
            $height     = null;
            $autosize   = null;
            if($item -> params -> get('tz_lightbox_width')){
                if(preg_match('/%|px/',$item -> params -> get('tz_lightbox_width'))){
                    $width  = 'width:\''.$item -> params -> get('tz_lightbox_width').'\',';
                }
                else
                    $width  = 'width:'.$item -> params -> get('tz_lightbox_width').',';
            }
            if($item -> params -> get('tz_lightbox_height')){
                if(preg_match('/%|px/',$item -> params -> get('tz_lightbox_height'))){
                    $height  = 'height:\''.$item -> params -> get('tz_lightbox_height').'\',';
                }
                else
                    $height  = 'height:'.$item -> params -> get('tz_lightbox_height').',';
            }
            if($width || $height){
                $autosize   = 'fitToView: false,autoSize: false,';
            }
            $doc -> addCustomTag('<script type="text/javascript">
                jQuery(\'.fancybox\').fancybox({
                    type:\'iframe\',
                    openSpeed:'.$item -> params -> get('tz_lightbox_speed',350).',
                    openEffect: "'.$item -> params -> get('tz_lightbox_transition','elastic').'",
                    '.$width.$height.$autosize.'
		            helpers:  {
                        title : {
                            type : "inside"
                        },
                        overlay : {
                            opacity:'.$item -> params -> get('tz_lightbox_opacity',0.75).',
                        }
                    }
                });
                </script>
            ');
        }
        
        $params -> merge($temp);
        $params -> merge($item -> params);

        $this -> assign('mediaParams',$params);
        $this -> assign('authorParams',$params);

        $extraFields    = JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
        $extraFields -> setState('article.id',JRequest::getInt('id'));
        $extraFields -> setState('params',$params);
        $this -> assign('listFields',$extraFields -> getExtraFields());

        $doc -> addStyleSheet('components/com_tz_portfolio/css/tzportfolio'.$csscompress.'.css');

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

		$this->_prepareDocument();

		parent::display($tpl);
	}

    protected function FindItemId($_tagid=null)
	{
        $tagid    = $this -> state -> get('tags.catid');
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $tagid        =   intval($tagid);
        if($_tagid){
            $tagid    = intval($_tagid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio');
		$items		= $menus->getItems('component_id', $component->id);

        if($this -> params -> get('menu_active') && $this -> params -> get('menu_active') != 'auto'){
            return $this -> params -> get('menu_active');
        }

        foreach ($items as $item)
        {

            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];


                if (isset($item->query['id'])) {
                    if ($item->query['id'] == $tagid) {
                        return $item -> id;
                    }
                } else {
                    $catids = $item->params->get('tz_catid');
                    if ($view == 'tags' && $catids) {
                        if (is_array($catids)) {
                            for ($i = 0; $i < count($catids); $i++) {
                                if ($catids[$i] == 0 || $catids[$i] == $tagid) {
                                    return $item -> id;
                                }
                            }
                        } else {
                            if ($catids == $tagid) {
                                return $item -> id;
                            }
                        }
                    }
                }
            }
        }

		return $active -> id;
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this article
		if ($menu && ($menu->query['option'] != 'com_tz_portfolio' || $menu->query['view'] != 'article' || $id != $this->item->id))
		{
			// If this is not a single article menu item, set the page title to the article title
			if ($this->item->title) {
				$title = $this->item->title;
			}
			$path = array(array('title' => $this->item->title, 'link' => ''));
			$category = JCategories::getInstance('Content')->get($this->item->catid);
			while ($category && ($menu->query['option'] != 'com_tz_portfolio' || $menu->query['view'] == 'article' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => TZ_PortfolioHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		if (empty($title)) {
			$title = $this->item->title;
		}
		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->getCfg('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}

		$mdata = $this->item->metadata->toArray();
		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				$this->document->setMetadata($k, $v);
			}
		}

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			$this->item->title = $this->item->title . ' - ' . $this->item->page_title;
			$this->document->setTitle($this->item->page_title . ' - ' . JText::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1));
		}

		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
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
