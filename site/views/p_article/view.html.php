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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

/**
 * HTML Article View class for the Content component.
 */
class TZ_PortfolioViewP_Article extends JViewLegacy
{
	protected $item;
	protected $params;
	protected $print;
	protected $state;
	protected $user;
    protected $generateLayout;

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

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$dispatcher	= JDispatcher::getInstance();

		$this->item		= $this->get('Item');
        $this -> itemMore   = $this -> get('ItemRelated');
        
		$this->print	= JRequest::getBool('print');
		$this->state	= $this->get('State');
		$this->user		= $user;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Create a shortcut for $item.
		$item = &$this->item;

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

        $media          = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
        $listMedia      = $media -> getMedia();

        $attach         = JModelLegacy::getInstance('Attachments','TZ_PortfolioModel');
        $tzUser         = JModelLegacy::getInstance('User','TZ_PortfolioModel');
        $tzTags         = JModelLegacy::getInstance('Tag','TZ_PortfolioModel');

        $this -> assign('listMedia',$listMedia);
        $this -> assign('listAttach',$attach -> getAttachments());
        $this -> assign('listAuthor',$tzUser -> getUser());
        $this -> assign('listTags',$tzTags -> getTag());

		$active	= $app->getMenu()->getActive();
		$temp	= clone ($this->params);
//        var_dump($active -> params);
//        $item -> params -> merge($active -> params);

        
		// Check to see which parameters should take priority
		if ($active) {
			$currentLink = $active->link;
			// If the current view is the active item and an article view for this article, then the menu item params take priority
			if (strpos($currentLink, 'view=p_article') && (strpos($currentLink, '&id='.(string) $item->id))) {
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

        // Create "link" and "fullLink" for article object
        $tmpl   = null;
        if($item -> params -> get('tz_use_lightbox',1) == 1){
            $tmpl   = '&amp;tmpl=component';
        }

        $item ->link        = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug,$item -> catid).$tmpl,true,-1);
        $item -> fullLink   = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug,$item -> catid),true,-1);

        $item -> parent_link    = JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($item->parent_slug));
        $item -> category_link  = JRoute::_(TZ_PortfolioHelperRoute::getCategoryRoute($item->catslug));

        if($item -> params -> get('tz_portfolio_redirect') == 'article'){
            $configLink =JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug,$item -> catid).$tmpl, true ,-1);
        }
        else{
            $configLink =JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug,$item -> catid).$tmpl, true ,-1);
        }

        // Compare current link and config link to redirect
        if($item ->link != $configLink){
            JFactory::getApplication() -> redirect($configLink);
        }

        $url    = JURI::getInstance() -> toString();

        $this -> assign('linkCurrent',$url);


		$offset = $this->state->get('list.offset');

		// Check the view access to the article (the model has already computed the values).
		if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true &&  $user->get('guest') ))) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return;
		}

        $item -> commentCount   = 0;

        if($item -> params -> get('comment_function_type','default') != 'js'){
            // Compute the article slugs and prepare introtext (runs content plugins).
            if($item -> params -> get('tz_show_count_comment',1) == 1){
                require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
                require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');
                $fetch       = new Services_Yadis_PlainHTTPFetcher();
            }
            $threadLink = null;
            $comments   = null;
            if($item){

                if($item -> params -> get('tz_show_count_comment',1) == 1){
                    if($item -> params -> get('tz_comment_type','disqus') == 'disqus'){
                        $threadLink .= '&thread=link:'.$item -> fullLink;
                    }elseif($item -> params -> get('tz_comment_type','disqus') == 'facebook'){
                        $threadLink .= '&ids='.$item -> fullLink;
                    }
                }
            }

            // Get comment counts for all items(articles)
            if($item -> params -> get('tz_show_count_comment',1) == 1){
                // From Disqus
                if($item -> params -> get('tz_comment_type','disqus') == 'disqus'){
                    if($threadLink){
                        $url        = 'https://disqus.com/api/3.0/threads/listPosts.json?api_secret='
                                      .$item -> params -> get('disqusApiSecretKey','4sLbLjSq7ZCYtlMkfsG7SS5muVp7DsGgwedJL5gRsfUuXIt6AX5h6Ae6PnNREMiB')
                                      .'&forum='.$item -> params -> get('disqusSubDomain','templazatoturials')
                                      .$threadLink.'&include=approved';

                        $content    = $fetch -> get($url);

                        if($content){
                            if($body    = json_decode($content -> body)){
                                if($responses = $body -> response){
                                    $comments   = count($responses);
                                }
                            }
                        }
                    }
                }

                // From Facebook
                if($item -> params -> get('tz_comment_type','disqus') == 'facebook'){
                    if($threadLink){
                        $url        = 'http://graph.facebook.com/?ids='
                                      .$threadLink;
                        $content    = $fetch -> get($url);
                        $contentUrl = $item -> fullLink;

                        if($content){
                            if($body = $content -> body){
                                if(isset($body -> $contentUrl -> comments)){
                                    $comments   = $body -> $contentUrl  -> comments;
                                }
                            }
                        }
                    }
                }
            }
            // End Get comment counts for all items(articles)

            if($comments){
                $item -> commentCount   = $comments;
            }
        }else{
            // Add facebook script api
            if($item -> params -> get('tz_show_count_comment',1) == 1){
                if($item -> params -> get('tz_comment_type','disqus') == 'facebook'){
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
                if($item -> params -> get('tz_comment_type','disqus') == 'disqus'){
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
        
		//
		// Process the content plugins.
		//
		JPluginHelper::importPlugin('content');
        JPluginHelper::importPlugin('tz_portfolio');

        //Get Plugins Model
        $pmodel = JModelLegacy::getInstance('Plugins','TZ_PortfolioModel',array('ignore_request' => true));
        //Get plugin Params for this article
        $pmodel -> setState('filter.contentid',$item -> id);
        $pluginItems    = $pmodel -> getItems();
        $pluginParams   = $pmodel -> getParams();

        $item -> pluginparams   = clone($pluginParams);

        if ($item->params->get('show_intro', '1')=='1') {
            $text = $item->introtext.' '.$item->fulltext;
        }
        elseif ($item->fulltext) {
            $text = $item->fulltext;
        }
        else  {
            $text = $item->introtext;
        }

        if($item -> introtext && !empty($item -> introtext)) {
            $item->text = $item->introtext;
            $results = $dispatcher->trigger('onContentPrepare', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
            $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
            $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
            $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));

            $results = $dispatcher->trigger('onTZPluginPrepare', array('com_tz_portfolio.p_article', &$item, &$item->params, &$pluginParams, $offset));
            $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.p_article', &$item, &$item->params, &$pluginParams, $offset));
            $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.p_article', &$item, &$item->params, &$pluginParams, $offset));
            $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.p_article', &$item, &$item->params, &$pluginParams, $offset));
            $item->introtext = $item->text;
        }
        if($item -> fulltext && !empty($item -> fulltext)) {
            $item->text = $item->fulltext;
            $results = $dispatcher->trigger('onContentPrepare', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
            $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
            $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
            $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));

            $results = $dispatcher->trigger('onTZPluginPrepare', array('com_tz_portfolio.p_article', &$item, &$item->params, &$pluginParams, $offset));
            $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.p_article', &$item, &$item->params, &$pluginParams, $offset));
            $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.p_article', &$item, &$item->params, &$pluginParams, $offset));
            $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.p_article', &$item, &$item->params, &$pluginParams, $offset));
            $item->fulltext = $item->text;
        }

        $item -> text   = $text;
        $results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.p_article', &$item, &$this->params, $offset));

		$item->event = new stdClass();
		$results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.p_article', &$item, &$this->params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

        $results = $dispatcher -> trigger('onTZPortfolioCommentDisplay',array('com_tz_portfolio.p_article',&$item,&$item -> params,$offset));
        $item -> event -> onTZPortfolioCommentDisplay  = trim(implode("\n",$results));

        $results = $dispatcher->trigger('onContentTZPortfolioVote', array('com_tz_portfolio.p_article', &$item, &$item -> params, $offset));
        $item->event->TZPortfolioVote = trim(implode("\n", $results));


        $results   = $dispatcher -> trigger('onTZPluginPrepare',array('com_tz_portfolio.p_article', &$item, &$item -> params,&$pluginParams,$offset));

        $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.p_article', &$item, &$item -> params,&$pluginParams, $offset));
        $item->event->TZafterDisplayTitle = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.p_article', &$item, &$item -> params,&$pluginParams, $offset));
        $item->event->TZbeforeDisplayContent = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.p_article', &$item, &$item -> params,&$pluginParams, $offset));
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
                                    $this -> state -> set('p_article.catid',$this -> item -> catid);
                                    $itemId = $this -> get('FindItemId');
                                    $menu   = $app -> getMenu('site');
                                    $mParams    = $menu -> getParams($itemId);
                                    if($mParams -> get('fields_order')){
                                        $this -> item -> params -> set('fields_order',$mParams -> get('fields_order'));
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
        if($item -> params -> get('fields_option_order')){
            switch($item -> params -> get('fields_option_order')){
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
                $extraFields -> setState('filter.option.order',$fieldsOptionOrder);
            }
        }
        $extraFields -> setState('params',$item -> params);
        $this -> assign('portfolioFields',$extraFields -> getExtraFields());

        $params = $media -> getCatParams($item -> catid);
        
        if($listMedia){
            if($listMedia[0] -> type == 'image'){
                if($params -> get('detail_article_image_size'))
                    $params -> set('article_image_resize',strtolower($params -> get('detail_article_image_size')));
            }
            if($listMedia[0] -> type == 'imagegallery'){
                $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                    .'/jquery.flexslider-min.js"></script>');
                $doc -> addStyleSheet('components/com_tz_portfolio/css/flexslider.min.css');

                if($params -> get('detail_article_image_gallery_size'))
                    $params -> set('article_image_gallery_resize',strtolower($params -> get('detail_article_image_gallery_size')));
                if($item -> params -> get('tz_image_gallery_crop'))
                    $params -> set('article_image_gallery_crop',$params -> get('tz_image_gallery_crop'));
            }
        }

        if($item -> params -> get('useCloudZoom',1) == 1){
            $doc -> addStyleSheet('components/com_tz_portfolio/css/cloud-zoom.min.css');
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/cloud-zoom.1.0.3.min.js"></script>');
        }

        if($item -> params -> get('tz_use_lightbox',1) == 1 AND !$tmpl){
            $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
                .'/jquery.fancybox.pack.js"></script>');
            $doc -> addStyleSheet('components/com_tz_portfolio/css/fancybox.min.css');

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
                            css : {background: "rgba(0,0,0,'.$item -> params -> get('tz_lightbox_opacity',0.75).')"}
                        }
                    }
                });
                </script>
            ');
        }

        /* Add scrollbar script */
        if($item -> params -> get('use_custom_scrollbar',1) && JRequest::getString('tmpl') == 'component' && !$this ->print){
            $doc -> addStyleSheet('components/com_tz_portfolio/css/jquery.mCustomScrollbar.min.css');
            $doc -> addCustomTag('<script src="components/com_tz_portfolio/js'
                .'/jquery.mCustomScrollbar.min.js"  type="text/javascript"></script>');

            if($item -> params -> get('horizontalScroll',0)){
                $horizontalScroll   = 'true';
            }else{
                $horizontalScroll   = 'false';
            }
            if($item -> params -> get('mouseWheel',1)){
                $mouseWheel   = 'true';
            }else{
                $mouseWheel   = 'false';
            }
            if($item -> params -> get('autoDraggerLength',1)){
                $autoDraggerLength   = 'true';
            }else{
                $autoDraggerLength   = 'false';
            }
            if($item -> params -> get('autoHideScrollbar',0)){
                $autoHideScrollbar  = 'true';
            }else{
                $autoHideScrollbar   = 'false';
            }
            if($item -> params -> get('scrollButtons_enable',1)){
                $scrollButtons_enable   = 'true';
            }else{
                $scrollButtons_enable   = 'false';
            }
            if($item -> params -> get('advanced_updateOnBrowserResize',1)){
                $advanced_updateOnBrowserResize = 'true';
            }else{
                $advanced_updateOnBrowserResize = 'false';
            }
            if($item -> params -> get('advanced_updateOnContentResize',0)){
                $advanced_updateOnContentResize = 'true';
            }else{
                $advanced_updateOnContentResize = 'false';
            }
            if($item -> params -> get('advanced_autoExpandHorizontalScroll',0)){
                $advanced_autoExpandHorizontalScroll    = 'true';
            }else{
                $advanced_autoExpandHorizontalScroll    = 'false';
            }
            if($item -> params -> get('advanced_autoScrollOnFocus',1)){
                $advanced_autoScrollOnFocus = 'true';
            }else{
                $advanced_autoScrollOnFocus = 'false';
            }
            if($item -> params -> get('advanced_normalizeMouseWheelDelta',0)){
                $advanced_normalizeMouseWheelDelta  = 'true';
            }else{
                $advanced_normalizeMouseWheelDelta  = 'false';
            }
            if($item -> params -> get('contentTouchScroll',1)){
                $contentTouchScroll = 'true';
            }else{
                $contentTouchScroll = 'false';
            }
            $doc -> addCustomTag('<script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery(".TzPortfolioItemPage").height(jQuery(window).height()).mCustomScrollbar({
                        set_width:'.($item -> params -> get('set_width')?$item -> params -> get('set_width'):'false').', /*optional element width: boolean, pixels, percentage*/
                        set_height:'.($item -> params -> get('set_height')?$item -> params -> get('set_height'):'false').', /*optional element height: boolean, pixels, percentage*/
                        horizontalScroll:'.$horizontalScroll.', /*scroll horizontally: boolean*/
                        scrollInertia:'.$item -> params -> get('scrollInertia',40).', /*scrolling inertia: integer (milliseconds)*/
                        mouseWheel: '.$mouseWheel.', /*mousewheel support: boolean*/
                        mouseWheelPixels:'.($params -> get('mouseWheelPixels')?$params -> get('mouseWheelPixels'):'"auto"').', /*mousewheel pixels amount: integer, "auto"*/
                        autoDraggerLength:'.$autoDraggerLength.', /*auto-adjust scrollbar dragger length: boolean*/
                        autoHideScrollbar: '.$autoHideScrollbar.', /*auto-hide scrollbar when idle*/
                        snapAmount:'.($item -> params -> get('snapAmount')?$item -> params -> get('snapAmount'):'null').', /* optional element always snaps to a multiple of this number in pixels */
                        snapOffset:'.($item -> params -> get('snapOffset')?$item -> params -> get('snapOffset'):0).', /* when snapping, snap with this number in pixels as an offset */
                        scrollButtons:{ /*scroll buttons*/
                            enable: '.$scrollButtons_enable.', /*scroll buttons support: boolean*/
                            scrollType:"'.($item -> params -> get('scrollButtons_snapOffset')?$item -> params -> get('scrollButtons_snapOffset'):'continuous').'", /*scroll buttons scrolling type: "continuous", "pixels"*/
                            scrollSpeed:'.($item -> params -> get('scrollButtons_scrollSpeed')?$item -> params -> get('scrollButtons_scrollSpeed'):'"auto"').', /*scroll buttons continuous scrolling speed: integer, "auto"*/
                            scrollAmount:'.($item -> params -> get('scrollButtons_scrollAmount')?$item -> params -> get('scrollButtons_scrollAmount'):100).' /*scroll buttons pixels scroll amount: integer (pixels)*/
                        },
                        advanced:{
                            updateOnBrowserResize: '.$advanced_updateOnBrowserResize.', /*update scrollbars on browser resize (for layouts based on percentages): boolean*/
                            updateOnContentResize: '.$advanced_updateOnContentResize.', /*auto-update scrollbars on content resize (for dynamic content): boolean*/
                            autoExpandHorizontalScroll: '.$advanced_autoExpandHorizontalScroll.', /*auto-expand width for horizontal scrolling: boolean*/
                            autoScrollOnFocus: '.$advanced_autoScrollOnFocus.', /*auto-scroll on focused elements: boolean*/
                            normalizeMouseWheelDelta: '.$advanced_normalizeMouseWheelDelta.' /*normalize mouse-wheel delta (-1/1)*/
                        },
                        contentTouchScroll: '.$contentTouchScroll.', /*scrolling by touch-swipe content: boolean*/
                        callbacks:{
                            onScrollStart:function(){}, /*user custom callback function on scroll start event*/
                            onScroll:function(){}, /*user custom callback function on scroll event*/
                            onTotalScroll:function(){}, /*user custom callback function on scroll end reached event*/
                            onTotalScrollBack:function(){}, /*user custom callback function on scroll begin reached event*/
                            onTotalScrollOffset:0, /*scroll end reached offset: integer (pixels)*/
                            onTotalScrollBackOffset:0, /*scroll begin reached offset: integer (pixels)*/
                            whileScrolling:function(){} /*user custom callback function on scrolling event*/
                        },
                        theme:"'.$item -> params -> get('scrollbar_theme','dark-thick').'"
                    });
                });
                jQuery(window).resize(function(){
                    jQuery(".TzPortfolioItemPage").height(jQuery(this).height());
                });
            </script>');
        }
        /* End add scrollbar script */

        if($item -> params -> get('show_video',1)){
            $doc -> addCustomTag('<script src="components/com_tz_portfolio/js'
                .'/fluidvids.min.js" type="text/javascript"></script>');
            $doc -> addCustomTag('<script type="text/javascript">
                jQuery(document).ready(function(){
                fluidvids.init({
                    selector: [\'.TzArticleMedia iframe\'],
                    players: [\'www.youtube.com\', \'player.vimeo.com\']
                });
              });
              </script>');
        }

        $params -> merge($temp);
        $params -> merge($item -> params);

        $this -> assign('mediaParams',$params);
        $this -> assign('authorParams',$params);

        $extraFields    = JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
        $extraFields -> setState('article.id',JRequest::getInt('id'));
        $extraFields -> setState('params',$params);
//        $extraFields -> setState('fieldsId',$params -> get('tz_fieldsid'));
        $this -> assign('listFields',$extraFields -> getExtraFields());

        $doc -> addStyleSheet('components/com_tz_portfolio/css/tzportfolio.min.css');

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

		$this->_prepareDocument();

        $this -> generateLayout($item,$params,$dispatcher,$csscompress);

		parent::display($tpl);

//        if($this -> item -> params -> get('show_vote',1)){
//            if($this -> item -> rating){
//                echo $this -> _addRichSnippets();
//            }
//        }
	}



    protected function generateLayout(&$article,&$params,$dispatcher,$csscompress=null){
        JPluginHelper::importPlugin('content');

        $template       = JModelLegacy::getInstance('Template','TZ_PortfolioModel',array('ignore_request' => true));
        $template -> setState('content.id',$this -> item -> id);
        $template -> setState('category.id',$this -> item -> catid);
        $theme  = $template -> getItem();
        $html   = null;

        if($theme){
            if($tplParams  = $theme ->params){
                $this -> document -> addStyleSheet('components/com_tz_portfolio/css/tz.bootstrap.min.css');
                foreach($tplParams as $tplItems){
                    $rows   = null;

                    $background = null;
                    $color      = null;
                    $margin     = null;
                    $padding    = null;

                    if($tplItems -> backgroundcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> backgroundcolor))){
                        $background  = 'background: '.$tplItems -> backgroundcolor.';';
                    }
                    if($tplItems -> textcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> textcolor))){
                        $color      =  'color: '.$tplItems -> textcolor.';';
                    }
                    if($tplItems -> margin){
                        $margin = 'margin: '.$tplItems -> margin.';';
                    }
                    if($tplItems -> padding){
                        $padding = 'padding: '.$tplItems -> padding.';';
                    }
                    if($background || $color || $margin || $padding){
                        $this -> document -> addStyleDeclaration('
                        #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).'{
                            '.$background.$color.$margin.$padding.'
                        }
                    ');
                    }
                    if($tplItems -> linkcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> linkcolor))){
                        $this -> document -> addStyleDeclaration('
                            #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).' a{
                                color: '.$tplItems -> linkcolor.';
                            }
                        ');
                    }
                    if($tplItems -> linkhovercolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> linkhovercolor))){
                        $this -> document -> addStyleDeclaration('
                            #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).' a:hover{
                                color: '.$tplItems -> linkhovercolor.';
                            }
                        ');
                    }
                    $rows[] = '<div id="tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).'"'
                        .' class="tz-container-fluid'.($tplItems -> {"class"}?' '.$tplItems -> {"class"}:'')
                        .($tplItems -> responsive?' '.$tplItems -> responsive:'').'">';
                    if($tplItems -> containertype){
                        $rows[] = '<div class="'.$tplItems -> containertype.'">';
                    }
                    $rows[] = '<div class="tz-row">';
                    foreach($tplItems -> children as $children){
                        $html   = null;

                        if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                            || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                            || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                            || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                            || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                            $rows[] = '<div class="'
                                .(!empty($children -> {"col-lg"})?'tz-col-lg-'.$children -> {"col-lg"}:'')
                                .(!empty($children -> {"col-md"})?' tz-col-md-'.$children -> {"col-md"}:'')
                                .(!empty($children -> {"col-sm"})?' tz-col-sm-'.$children -> {"col-sm"}:'')
                                .(!empty($children -> {"col-xs"})?' tz-col-xs-'.$children -> {"col-xs"}:'')
                                .(!empty($children -> {"col-lg-offset"})?' tz-col-lg-offset-'.$children -> {"col-lg-offset"}:'')
                                .(!empty($children -> {"col-md-offset"})?' tz-col-md-offset-'.$children -> {"col-md-offset"}:'')
                                .(!empty($children -> {"col-sm-offset"})?' tz-col-sm-offset-'.$children -> {"col-sm-offset"}:'')
                                .(!empty($children -> {"col-xs-offset"})?' tz-col-xs-offset-'.$children -> {"col-xs-offset"}:'')
                                .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                                .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                        }

                        if($children -> type && $children -> type !='none'){
                            $html   = $this -> loadTemplate($children -> type);
                            $html   = trim($html);
                        }

                        $rows[] = $html;

                        if( !empty($children -> children) and is_array($children -> children) ){
                            $this -> childrenLayout($rows,$children,$article,$params,$dispatcher);
                        }

                        if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                            || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                            || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                            || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                            || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                            $rows[] = '</div>'; // Close col tag
                        }
                    }

                    if($tplItems -> containertype){
                        $rows[] = '</div>';
                    }
                    $rows[] = '</div>';
                    $rows[] = '</div>';
                    $this -> generateLayout .= implode("\n",$rows);
                }
            }
        }
    }

    protected function childrenLayout(&$rows,$children,&$article,&$params,$dispatcher){
        foreach($children -> children as $children){
            $background = null;
            $color      = null;
            $margin     = null;
            $padding    = null;

            if($children -> backgroundcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> backgroundcolor))){
                $background  = 'background: '.$children -> backgroundcolor.';';
            }
            if($children -> textcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> textcolor))){
                $color      =  'color: '.$children -> textcolor.';';
            }
            if($children -> margin){
                $margin = 'margin: '.$children -> margin.';';
            }
            if($children -> padding){
                $padding = 'padding: '.$children -> padding.';';
            }
            if($background || $color){
                $this -> document -> addStyleDeclaration('
                    #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner{
                        '.$background.$color.$margin.$padding.'
                    }
                ');
            }
            if($children -> linkcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> linkcolor))){
                $this -> document -> addStyleDeclaration('
                        #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner a{
                            color: '.$children -> linkcolor.';
                        }
                    ');
            }
            if($children -> linkhovercolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> linkhovercolor))){
                $this -> document -> addStyleDeclaration('
                        #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner a:hover{
                            color: '.$children -> linkhovercolor.';
                        }
                    ');
            }
            $rows[] = '<div id="tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner" class="tz-container-fluid '
                .$children -> {"class"}.($children -> responsive?' '.$children -> responsive:'').'">';
            $rows[] = '<div class="tz-row">';
            foreach($children -> children as $children){
                $html   = null;

                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                    $rows[] = '<div class="'
                        .(!empty($children -> {"col-lg"})?'tz-col-lg-'.$children -> {"col-lg"}:'')
                        .(!empty($children -> {"col-md"})?' tz-col-md-'.$children -> {"col-md"}:'')
                        .(!empty($children -> {"col-sm"})?' tz-col-sm-'.$children -> {"col-sm"}:'')
                        .(!empty($children -> {"col-xs"})?' tz-col-xs-'.$children -> {"col-xs"}:'')
                        .(!empty($children -> {"col-lg-offset"})?' tz-col-lg-offset-'.$children -> {"col-lg-offset"}:'')
                        .(!empty($children -> {"col-md-offset"})?' tz-col-md-offset-'.$children -> {"col-md-offset"}:'')
                        .(!empty($children -> {"col-sm-offset"})?' tz-col-sm-offset-'.$children -> {"col-sm-offset"}:'')
                        .(!empty($children -> {"col-xs-offset"})?' tz-col-xs-offset-'.$children -> {"col-xs-offset"}:'')
                        .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                        .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                }

                if($children -> type && $children -> type !='none'){
                    $html   = $this -> loadTemplate($children -> type);
                    $html   = trim($html);
                }

                $rows[] = $html;

                if( !empty($children -> children) and is_array($children -> children) ){
                    $this -> childrenLayout($rows,$children,$article,$params,$dispatcher);
                }

                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                    $rows[] = '</div>'; // Close col tag
                }

            }
            $rows[] = '</div>';
            $rows[] = '</div>';
        }
        return;
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
		if ($menu && ($menu->query['option'] != 'com_tz_portfolio' || $menu->query['view'] != 'p_article' || $id != $this->item->id))
		{
			// If this is not a single article menu item, set the page title to the article title
			if ($this->item->title) {
                $this->item->title  = $this->item->title;
				$title = $this->item->title;
			}
			$path = array(array('title' => $this->item->title, 'link' => ''));
			$category = JCategories::getInstance('Content')->get($this->item->catid);
			while ($category && ($menu->query['option'] != 'com_tz_portfolio' || $menu->query['view'] == 'p_article' || $id != $category->id) && $category->id > 1)
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
        if(!empty($title)){
            $title  = htmlspecialchars($title);
        }
		$this->document->setTitle($title);

        $description    = null;
        if ($this->item->metadesc){
            $description    = $this -> item -> metadesc;
        }elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
        {
            $description    = $this -> params -> get('menu-meta_description');
        }elseif(!empty($this -> item -> introtext)){
            $description    = strip_tags($this -> item -> introtext);
            $description    = explode(' ',$description);
            $description    = array_splice($description,0,25);
            $description    = trim(implode(' ',$description));
            if(!strpos($description,'...'))
                $description    .= '...';
        }

        if($description){
            $description    = htmlspecialchars($description);
            $this -> document -> setDescription($description);
        }

        $tags   = null;

		if ($this->item->metakey)
		{
            $tags   = $this->item->metakey;
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
            $tags   = $this->params->get('menu-meta_keywords');
		}elseif($this -> listTags){
		    foreach($this -> listTags as $tag){
		        $tags[] = $tag -> name;
		    }
		    $tags   = implode(',',$tags);
        }

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->getCfg('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}

        $metaImage  = null;
        if($metaMedia = $this -> listMedia):
            $metaImageSize  = $this -> params -> get('detail_article_image_size','L');
            if($metaMedia[0] -> type == 'image' || $metaMedia[0] -> type == 'imagegallery'):
                if(isset($metaMedia[0] -> images) AND !empty($metaMedia[0] -> images)):
                    $metaImage  = trim($metaMedia[0] -> images);
                    $metaImage  = JUri::root().str_replace('.'.JFile::getExt($metaImage),'_'.$metaImageSize.'.'
                            .JFile::getExt($metaImage),$metaImage);
                endif;
            elseif($metaMedia[0] -> type == 'video' || $metaMedia[0] -> type == 'audio'):
                if(isset($metaMedia[0] -> thumb) AND !empty($metaMedia[0] -> thumb)):
                    $metaImage  = trim($metaMedia[0] -> thumb);
                    $metaImage  = JUri::root().str_replace('.'.JFile::getExt($metaImage),'_'.$metaImageSize.'.'
                            .JFile::getExt($metaImage),$metaImage);
                endif;
            endif;
        endif;

        $socialInfo = new stdClass();
        $socialInfo -> title        = $title;
        $socialInfo -> image        = $metaImage;
        $socialInfo -> description  = $description;
        $this -> assign('socialInfo',$socialInfo);

//        $this -> document -> setMetaData('copyright','Copyright © '.date('Y',time()).' TemPlaza. All Rights Reserved.');

        // Set meta tags with prefix property "og:"
        $this -> document -> addCustomTag('<meta property="og:title" content="'.$title.'"/>');
        $this -> document -> addCustomTag('<meta property="og:url" content="'.
        JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($this -> item -> slug, $this -> item -> catid),true,-1).'"/>');
        $this -> document -> addCustomTag('<meta property="og:type" content="article"/>');
        if($metaImage){
            $this -> document -> addCustomTag('<meta property="og:image" content="'.$metaImage.'"/>');
        }
        if($description){
            $this -> document -> addCustomTag('<meta property="og:description" content="'.$description.'"/>');
        }
        //// End set meta tags with prefix property "og:" ////

        // Set meta tags with prefix property "article:"
        $this -> document -> addCustomTag('<meta property="article:author" content="'.$this->item->author.'"/>');
        $this -> document -> addCustomTag('<meta property="article:published_time" content="'
            .$this->item->created.'"/>');
        $this -> document -> addCustomTag('<meta property="article:modified_time" content="'
            .$this->item->modified.'"/>');
        $this -> document -> addCustomTag('<meta property="article:section" content="'
            .$this->escape($this->item->category_title).'"/>');

        if($this->params -> get('show_vote',1)){
            if($this -> item -> rating){
                $this -> document -> addCustomTag('<meta property="article:ratingValue" content="'.$this -> item -> rating.'"/> ');
                $this -> document -> addCustomTag('<meta property="article:reviewCount" content="'.$this -> item -> rating_count.'"/> ');
            }
        }
        if($tags){
            $tags   = htmlspecialchars($tags);
            $this -> document-> setMetaData('keywords',$tags);
            $this -> document -> addCustomTag('<meta property="article:tag" content="'.$tags.'"/>');
        }
        ///// End set meta tags with prefix property "article:" ////

        // Set meta tags with prefix name "twitter:"
        if($author = $this -> listAuthor){
            if(isset($author -> twitter) && !empty($author -> twitter)){
                $this -> document -> setMetaData('twitter:card','summary');
                if(preg_match('/(https)?(:\/\/www\.)?twitter\.com\/(#!\/)?@?([^\/]*)/i',$author -> twitter,$match)){
                    if(count($match) > 1){
                        $this -> document -> setMetaData('twitter:site','@'.$match[count($match) - 1]);
                        $this -> document -> setMetaData('twitter:creator','@'.$match[count($match) - 1]);
                    }
                }
                if($metaImage){
                    $this -> document -> setMetaData('twitter:image',$metaImage);
                }
                if($description){
                    $this -> document -> setMetaData('twitter:description',$description);
                }
            }
        }
        //// End set meta tags with prefix name "twitter:" ////


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

//    protected function _addRichSnippets(){
//
//        $media  = $this -> listMedia;
//        $params = $this -> item -> params;
//
//        $html   = null;
//        $html   = '<div itemscope itemtype="http://schema.org/Article">';
//        $html  .= '<meta itemprop="name" content="'.$this -> item -> title.'"/>';
//        $html  .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
//        $html  .= '<meta itemprop="ratingValue" content="'.$this -> item -> rating.'"/>';
//        $html  .= '<meta itemprop="ratingCount" content="'.$this -> item -> rating_count.'"/>';
//        $html  .= '</div>';
//        $html  .= '</div>';
//        return $html;
//    }
}