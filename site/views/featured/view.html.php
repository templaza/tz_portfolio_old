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
require_once(JPATH_SITE.'/components/com_tz_portfolio/helpers/article.php');

/**
 * Frontpage View class.
 */
class TZ_PortfolioViewFeatured extends JViewLegacy
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;

	protected $lead_items = array();
	protected $intro_items = array();
	protected $link_items = array();
	protected $columns = 1;

    protected $media        = null;
    protected $extraFields  = null;

    function __construct($config = array()){
        $this -> media          = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
        $this -> extraFields    = JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
        parent::__construct($config);
    }

	/**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	function display($tpl = null)
	{

		// Initialise variables.
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
        $doc    = JFactory::getDocument();
//        $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js/jquery-1.7.2.min.js"></script>');

		$state 		= $this->get('State');
		$items 		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$params = &$state->params;

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

		// PREPARE THE DATA

		// Get the metrics for the structural page layout.
		$numLeading = $params->def('num_leading_articles', 1);
		$numIntro = $params->def('num_intro_articles', 4);
		$numLinks = $params->def('num_links', 4);

        //Get Plugins Model
        $pmodel = JModelLegacy::getInstance('Plugins','TZ_PortfolioModel',array('ignore_request' => true));

        if($params -> get('comment_function_type','default') != 'js'){
            // Compute the article slugs and prepare introtext (runs content plugins).
            if($params -> get('tz_show_count_comment',1) == 1){
                require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
                require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');
                $fetch       = new Services_Yadis_PlainHTTPFetcher();
            }
            $threadLink = null;
            $comments   = null;
            if($items){
                foreach($items as $key => $item){

                    $slug   = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

                    /*** New source ***/
                    //Check redirect to view article
                    if($item -> params -> get('tz_portfolio_redirect') == 'p_article'){
                        $contentUrl   = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($slug, $item -> catid),true,-1);
                    }
                    else{
                        $contentUrl   = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($slug, $item -> catid),true,-1);
                    }
                    /*** End New Source ***/

                    if($params -> get('tz_show_count_comment',1) == 1){
                        if($params -> get('tz_comment_type','disqus') == 'disqus'){
                            $threadLink .= '&thread[]=link:'.$contentUrl;
                        }elseif($params -> get('tz_comment_type','disqus') == 'facebook'){
                            $threadLink .= '&urls[]='.$contentUrl;
                        }
                    }
                }
            }

            // Get comment counts for all items(articles)
            if($params -> get('tz_show_count_comment',1) == 1){
                // From Disqus
                if($params -> get('tz_comment_type','disqus') == 'disqus'){
                    if($threadLink){
                        $url        = 'https://disqus.com/api/3.0/threads/list.json?api_secret='
                                      .$params -> get('disqusApiSecretKey','4sLbLjSq7ZCYtlMkfsG7SS5muVp7DsGgwedJL5gRsfUuXIt6AX5h6Ae6PnNREMiB')
                                      .'&forum='.$params -> get('disqusSubDomain','templazatoturials')
                                      .$threadLink.'&include=open';

                        $content    = $fetch -> get($url);

                        if($content){
                            if($body    = json_decode($content -> body)){
                                if($responses = $body -> response){
                                    if(is_array($responses) && count($responses)){
                                        foreach($responses as $response){
                                            $comments[$response ->link]   = $response -> posts;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // From Facebook
                if($params -> get('tz_comment_type','disqus') == 'facebook'){
                    if($threadLink){
                        $url        = 'http://api.facebook.com/restserver.php?method=links.getStats'
                                      .$threadLink;
                        $content    = $fetch -> get($url);

                        if($content){
                            if($bodies = $content -> body){
                                if(preg_match_all('/\<link_stat\>(.*?)\<\/link_stat\>/ims',$bodies,$matches)){
                                    if(isset($matches[1]) && !empty($matches[1])){
                                        foreach($matches[1]as $val){
                                            $match  = null;
                                            if(preg_match('/\<url\>(.*?)\<\/url\>.*?\<comment_count\>(.*?)\<\/comment_count\>/msi',$val,$match)){
                                                if(isset($match[1]) && isset($match[2])){
                                                    $comments[$match[1]]    = $match[2];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // End Get comment counts for all items(articles)
        }else{
            // Add facebook script api
            if($params -> get('tz_show_count_comment',1) == 1){
                if($params -> get('tz_comment_type','disqus') == 'facebook'){
                    $doc -> addScriptDeclaration('
                        (function(d, s, id) {
                          var js, fjs = d.getElementsByTagName(s)[0];
                          if (d.getElementById(id)) return;
                          js = d.createElement(s); js.id = id;
                          js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
                          fjs.parentNode.insertBefore(js, fjs);
                        }(document, \'script\', \'facebook-jssdk\'));
                   ');
                }

                // Add disqus script api
                if($params -> get('tz_comment_type','disqus') == 'disqus'){
                    $doc -> addScriptDeclaration('
                        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                        var disqus_shortname = \'templazatoturials\'; // required: replace example with your forum shortname

                        /* * * DON\'T EDIT BELOW THIS LINE * * */
                        (function () {
                        var s = document.createElement(\'script\'); s.async = true;
                        s.type = \'text/javascript\';
                        s.src = \'http://\' + disqus_shortname + \'.disqus.com/count.js\';
                        (document.getElementsByTagName(\'HEAD\')[0] || document.getElementsByTagName(\'BODY\')[0]).appendChild(s);
                        }());
                   ');
                    $doc -> addCustomTag('
                    <script type="text/javascript">
                        window.addEvent("load",function(){
                            var a=document.getElementsByTagName("A");

                            for(var h=0;h<a.length;h++){
                                if(a[h].href.indexOf("#disqus_thread")>=0){
                                var span = document.createElement("span");
                                span.innerHTML  = a[h].innerHTML;
                                a[h].parentNode.appendChild(span);
                                a[h].remove();
                                }
                            }
                        });
                    </script>
                   ');
                }
            }
        }

        if($items){

            $content_ids    = array();
            for ($i = 0, $n = count($items); $i < $n; $i++) {
                $content_ids[]  = $items[$i] -> id;
            }

            $tags   = null;
            if(count($content_ids) && $params -> get('show_tags',1)) {
                $m_tag = JModelLegacy::getInstance('Tag', 'TZ_PortfolioModel', array('ignore_request' => true));
                $m_tag->setState('params',$params);
                $m_tag->setState('article.id', $content_ids);
                $m_tag -> setState('list.ordering','x.contentid');
                $tags   = $m_tag -> getArticleTags();
            }

            foreach ($items as $i => & $item)
            {

                if($tags && count($tags) && isset($tags[$item -> id])){
                    $item -> tags   = $tags[$item -> id];
                }

                $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

                /*** New source ***/
                $tmpl   = null;
                if($item -> params -> get('tz_use_lightbox',1) == 1){
                    $tmpl   = '&amp;tmpl=component';
                }

                //Check redirect to view article
                if($item -> params -> get('tz_portfolio_redirect') == 'p_article'){
                    $item ->link        = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid).$tmpl);
                    $item -> fullLink   = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid),true,-1);
                }
                else{
                    $item ->link        = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid).$tmpl);
                    $item -> fullLink   = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid),true,-1);
                }
                /*** End New Source ***/

                if($params -> get('comment_function_type','default') != 'js'){

                    if($params -> get('tz_show_count_comment',1) == 1){
                        if($params -> get('tz_comment_type','disqus') == 'disqus' ||
                            $params -> get('tz_comment_type','disqus') == 'facebook'){
                            if($comments){
                                if(array_key_exists($item -> fullLink,$comments)){
                                    $item -> commentCount   = $comments[$item -> fullLink];
                                }else{
                                    $item -> commentCount   = 0;
                                }
                            }else{
                                $item -> commentCount   = 0;
                            }

                        }
                    }
                }else{
                    $item -> commentCount   = 0;
                }

                $model  = JModelLegacy::getInstance('Category','TZ_PortfolioModel', array('ignore_request' => true));
                $model -> setState('category.id',$item -> catid);
                $category   = $model -> getCategory();

                $catParams2 = new JRegistry();

                $catParams  = new JRegistry();
                if($category)
                    $catParams -> loadString($category -> params);
                $catParams  = $catParams -> toArray();

                $this -> category   = $category;

                if(count($catParams)>0){
                    foreach($catParams as $key => $val){
                        if(preg_match('/.*?article.*?/',$key)){
                            $catParams2 -> set($key,$val);
                        }
                    }
                }


                $item->catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
                $item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
                // No link for ROOT category
                if ($item->parent_alias == 'root') {
                    $item->parent_slug = null;
                }

                $item->event = new stdClass();

                $dispatcher = JDispatcher::getInstance();

                //Get plugin Params for this article
                $pmodel -> setState('filter.contentid',$item -> id);
                $pluginItems    = $pmodel -> getItems();
                $pluginParams   = $pmodel -> getParams();
                $item -> pluginparams   = clone($pluginParams);

                // Ignore content plugins on links.
                if ($i < $numLeading + $numIntro)
                {
                    // Old plugins: Ensure that text property is available
                    if (!isset($item->text))
                    {
                        $item->text = $item->introtext;
                    }

                    //Call trigger in group content
                    JPluginHelper::importPlugin('content');
                    $results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.category', &$item, &$params, 0));
                    $item->introtext = $item->text;

                    $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.featured', &$item, &$item->params, 0));
                    $item->event->afterDisplayTitle = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.featured', &$item, &$item->params, 0));
                    $item->event->beforeDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.featured', &$item, &$item->params, 0));
                    $item->event->afterDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentTZPortfolioVote', array('com_tz_portfolio.category', &$item, &$item->params, 0));
                    $item->event->TZPortfolioVote = trim(implode("\n", $results));



                    //Call trigger in group tz_portfolio
                    JPluginHelper::importPlugin('tz_portfolio');
                    $item->introtext = JHtml::_('article.tzprepare', $item->introtext, '',$pluginParams, 'com_tz_portfolio.featured');

                    $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.featured', &$item, &$params,&$pluginParams, 0));
                    $item->event->TZafterDisplayTitle = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.featured', &$item, &$params,&$pluginParams, 0));
                    $item->event->TZbeforeDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.featured', &$item, &$params,&$pluginParams, 0));
                    $item->event->TZafterDisplayContent = trim(implode("\n", $results));
                }
            }
		}

		// Preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interogate the arrays.
		$max = count($items);

		// The first group is the leading articles.
		$limit = $numLeading;
		for ($i = 0; $i < $limit && $i < $max; $i++)
		{
			$this->lead_items[$i] = &$items[$i];

            if($items[$i] -> params -> get('article_leading_image_size')){
                $items[$i] -> params -> set('article_leading_image_resize',$items[$i] -> params -> get('article_leading_image_size','L'));
            }
            if($items[$i] -> params -> get('article_leading_image_gallery_size')){
                $items[$i] -> params -> set('article_leading_image_gallery_resize',$items[$i] -> params -> get('article_leading_image_gallery_size','L'));
            }
		}

		// The second group is the intro articles.
		$limit = $numLeading + $numIntro;
		// Order articles across, then down (or single column mode)
		for ($i = $numLeading; $i < $limit && $i < $max; $i++)
		{
			$this->intro_items[$i] = &$items[$i];

            if($items[$i] -> params -> get('article_secondary_image_size')){
                $items[$i] -> params -> set('article_secondary_image_resize',$items[$i] -> params -> get('article_secondary_image_size','M'));
            }
            if($items[$i] -> params -> get('article_secondary_image_gallery_size')){
                $items[$i] -> params -> set('article_secondary_image_gallery_resize',$items[$i] -> params -> get('article_secondary_image_gallery_size','M'));
            }
		}

		$this->columns = max(1, $params->def('num_columns', 1));
		$order = $params->def('multi_column_order', 1);

		if ($order == 0 && $this->columns > 1)
		{
			// call order down helper
			$this->intro_items = TZ_PortfolioHelperQuery::orderDownColumns($this->intro_items, $this->columns);
		}

		// The remainder are the links.
		for ($i = $numLeading + $numIntro; $i < $max; $i++)
		{
			$this->link_items[$i] = &$items[$i];
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->assignRef('params', $params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('user', $user);

        $model  = JModelLegacy::getInstance('Portfolio','TZ_PortfolioModel',array('ignore_request' => true));
        $pParams    = clone($params);
        $pParams -> set('tz_catid',$params -> get('featured_categories'));
        $model -> setState('params',$pParams);
        $model -> setState('filter.featured',1);
        $this -> assign('char',$state -> get('char'));
        $this -> assign('availLetter',$model -> getAvailableLetter());

//        if(isset($catParams2)){
//            if($catParams2){
//                if($catParams2 -> get('article_leading_image_size')){
//                    $catParams2 -> set('article_leading_image_resize'
//                        ,strtolower($catParams2 -> get('article_leading_image_size')));
//                }
//                if($catParams2 -> get('article_secondary_image_size')){
//                    $catParams2 -> set('article_secondary_image_resize'
//                        ,strtolower($catParams2 -> get('article_leading_image_size')));
//                }
//                if($catParams2 -> get('article_leading_image_gallery_size')){
//                    $catParams2 -> set('article_leading_image_gallery_resize'
//                        ,strtolower($catParams2 -> get('article_leading_image_gallery_size')));
//                }
//                if($catParams2 -> get('article_secondary_image_gallery_size')){
//                    $catParams2 -> set('article_secondary_image_gallery_resize'
//                        ,strtolower($catParams2 -> get('article_secondary_image_gallery_size')));
//                }
//
//                $params -> merge($catParams2);
//            }
//        }

        $this -> assign('mediaParams',$params);

        $doc    = JFactory::getDocument();
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
        if($params -> get('tz_use_lightbox',1) == 1){
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
                '/jquery.fancybox.pack.js"></script>');
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
                            opacity:'.$params -> get('tz_lightbox_opacity',0.75).',
                        }
                    }'
                    .$scrollHidden.'
                });
                </script>
            ');
        }

        $doc -> addStyleSheet('components/com_tz_portfolio/css/tzportfolio.min.css');

		$this->_prepareDocument();

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

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');
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

		// Add feed links
		if ($this->params->get('show_feed_link', 1))
		{
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}
	}
}
