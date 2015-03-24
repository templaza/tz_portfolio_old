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
jimport('joomla.filesystem.file');

class TZ_PortfolioViewTags extends JViewLegacy
{
    protected $item         = null;
    protected $params       = null;
    protected $pagination   = null;
    protected $media        = null;
    protected $extraFields  = null;

    function __construct($config = array()){
        $this -> item           = new stdClass();
        $this -> media          = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
        $this -> extraFields    = JModelLegacy::getInstance('ExtraFields','TZ_PortfolioModel',array('ignore_request' => true));
        parent::__construct($config);
    }
    function display($tpl = null){
        $menus		= JMenu::getInstance('site');
        $active     = $menus->getActive();

        $state          = $this -> get('state');
        $params         = $state -> params;

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

        $this -> params = $params;
        $list   = $this -> get('Items');
        $doc    = JFactory::getDocument();

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
            if($list){
                foreach($list as $key => $item){
                    if($params -> get('tz_show_count_comment',1) == 1){
                        if($params -> get('tz_comment_type','disqus') == 'disqus'){
                            $threadLink .= '&thread[]=link:'.urlencode($item -> fullLink);
                        }elseif($params -> get('tz_comment_type','disqus') == 'facebook'){
                            $threadLink .= '&urls[]='.urlencode($item -> fullLink);
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
                                    if(!is_array($responses)){
                                        JError::raiseNotice('300',JText::_('COM_TZ_PORTFOLIO_DISQUS_INVALID_SECRET_KEY'));
                                    }
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

        if($list){
            $user	= JFactory::getUser();
            $userId	= $user->get('id');
            $guest	= $user->get('guest');

            foreach($list as &$row){
                if($params -> get('comment_function_type','default') != 'js'){
                    if($params -> get('tz_show_count_comment',1) == 1){
                        if($params -> get('tz_comment_type','disqus') == 'disqus' ||
                            $params -> get('tz_comment_type','disqus') == 'facebook'){
                            if($comments){
                                if(array_key_exists($row -> fullLink,$comments)){
                                    $row -> commentCount   = $comments[$row -> fullLink];
                                }else{
                                    $row -> commentCount   = 0;
                                }
                            }else{
                                $row -> commentCount   = 0;
                            }

                        }
                    }
                }else{
                    $row -> commentCount   = 0;
                }

                // Compute the asset access permissions.
                // Technically guest could edit an article, but lets not check that to improve performance a little.
                if (!$guest) {
                    $asset	= 'com_tz_portfolio.article.'.$row->id;

                    // Check general edit permission first.
                    if ($user->authorise('core.edit', $asset)) {
                        $row -> params -> set('access-edit', true);
                    }
                    // Now check if edit.own is available.
                    elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                        // Check for a valid user and that they are the owner.
                        if ($userId == $row->created_by) {
                            $row -> params -> set('access-edit', true);
                        }
                    }
                }

                // Old plugins: Ensure that text property is available
                if (!isset($row->text))
                {
                    $row -> text = $row -> introtext;
                }
                if(version_compare(COM_TZ_PORTFOLIO_VERSION,'3.1.7','<')){
                    $row -> text    = null;
                    if ($params->get('show_intro', '1')=='1') {
                        $row -> text = $row -> introtext;
                    }
                }

                $dispatcher	= JDispatcher::getInstance();
                        //
                // Process the content plugins.
                //
                JPluginHelper::importPlugin('content');
                $results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.tags', &$row, &$params, $state -> offset));
                $row -> introtext   = $row -> text;

                $row->event = new stdClass();
                $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.tags', &$row, &$params, $state -> offset));
                $row->event->afterDisplayTitle = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.tags', &$row, &$params,$state -> offset));
                $row->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.tags', &$row, &$params, $state -> offset));
                $row->event->afterDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentTZPortfolioVote', array('com_tz_portfolio.tags', &$row, &$params, $state -> offset));
                $row->event->TZPortfolioVote = trim(implode("\n", $results));

                //Get plugin Params for this article
                $pmodel -> setState('filter.contentid',$row -> id);
                $pluginItems    = $pmodel -> getItems();
                $pluginParams   = $pmodel -> getParams();
                $row -> pluginparams    = clone($pluginParams);

                JPluginHelper::importPlugin('tz_portfolio');
                $results   = $dispatcher -> trigger('onTZPluginPrepare',array('com_tz_portfolio.tags', &$row, &$params,&$pluginParams,$state -> offset));

                $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.tags', &$row, &$params,&$pluginParams, $state -> offset));
                $row->event->TZafterDisplayTitle = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.tags', &$row, &$params,&$pluginParams, $state -> offset));
                $row->event->TZbeforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.tags', &$row, &$params,&$pluginParams, $state -> offset));
                $row->event->TZafterDisplayContent = trim(implode("\n", $results));
            }
        }

        $this -> assign('tag',$this -> get('Tag'));
        $this -> assign('listsTags',$list);
        $this -> pagination = $this -> get('Pagination');

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

        $this -> assign('tagsParams',$params);
        $model  = JModelLegacy::getInstance('Portfolio','TZ_PortfolioModel',array('ignore_request' => true));
        $model -> setState('params',$params);
        $model -> setState('filter.tagId',JRequest::getInt('id'));
        $this -> assign('char',$state -> get('char'));
        $this -> assign('availLetter',$model -> getAvailableLetter());

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
                $jscompress -> folder.'/jquery.fancybox.pack.js"></script>');
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

        $doc -> addStyleSheet('components/com_tz_portfolio/css/tzportfolio.min.css');

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
        }elseif($this -> tag && !$this->params->get('menu-meta_keywords')){
            $this->document->setMetadata('keywords', $this -> tag -> name);
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
