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
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');
jimport('joomla.html.pagination');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'autocuttext.php');

class TZ_PortfolioModelPortfolio extends JModelList
{
    protected $pagNav       = null;
    protected $rowsTag      = null;
    protected $categories   = null;
    public $test            = null;

    function populateState($ordering = 'ordering', $direction = 'ASC'){
        $app    = JFactory::getApplication();
        $params = $app -> getParams();

        $offset = JRequest::getUInt('limitstart',0);


        if($params -> get('show_limit_box',0)  && $params -> get('tz_portfolio_layout') == 'default'){
            $limit  = $app->getUserStateFromRequest('com_tz_portfolio.portfolio.limit','limit',$params -> get('tz_article_limit',10));
        }
        else{
            $limit  = $params -> get('tz_article_limit',10);
        }

        $this -> setState('params',$params);
        $this -> setState('offset', $offset);
        $this -> setState('Itemid',$params -> get('id'));
        $this -> setState('limit',$limit);
        $this -> setState('tz_catid',$params -> get('tz_catid'));
        $this -> setState('char',JRequest::getString('char',null));
        $this -> setState('filter.tagId',null);
        $this -> setState('filter.userId',null);
        $this -> setState('filter.featured',null);
        $this -> setState('filter.year',null);
        $this -> setState('filter.month',null);
        parent::populateState($ordering,$direction);
    }

    function ajaxtags($limitstart=null) {

        $params     = JComponentHelper::getParams('com_tz_portfolio');

        $Itemid = JRequest::getInt('Itemid');
        $page   = JRequest::getInt('page');
        $char       = JRequest::getString('char');
        $curTags    = stripslashes(JRequest::getString('tags'));
        $curTags    = json_decode($curTags);

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit      =   $params -> get('tz_article_limit');
        $limitstart =   $limit * ($page-1);

        $offset = (int) $limitstart;

        $this -> setState('limit',$limit);
        $this -> setState('offset',$offset);
        $this -> setState('params',$params);
        $this -> setState('char',$char);

        $this -> getArticle();

        $newTags    = null;
        $tags       = null;

        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'portfolio'.DIRECTORY_SEPARATOR.'view.html.php';
        $view   = new TZ_PortfolioViewPortfolio();

        if($this -> getTags())
            $newTags    = $this ->getTags();

        if(isset($newTags) && count($newTags) > 0){
            foreach($newTags as $key => $newTag){
                $bool   = false;
                if(isset($curTags) && count($curTags) > 0){
                    foreach($curTags as $curTag){
                        if(trim($curTag) == str_replace(' ','-',trim($newTag -> name))){
                            $bool   = true;
                            break;
                        }
                    }
                }
                if($bool == false){
                    $tags[] = $newTag;
                }
            }
        }

        $view -> assign('params',$this -> getState('params'));
        $view -> assign('listsTags',$tags);
        $data    = $view -> loadTemplate('tags');
        if(empty($data))
            return '';

        return $data;
    }

    public function ajax(){

        $data        = null;

        $params     = JComponentHelper::getParams('com_tz_portfolio');

        $Itemid     = JRequest::getInt('Itemid');
        $page       = JRequest::getInt('page');
        $layout     = JRequest::getString('layout');
        $char       = JRequest::getString('char');

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit  = $params -> get('tz_article_limit');

        $offset = $limit * ($page - 1);

        $this -> setState('limit',$limit);
        $this -> setState('offset',$offset);
        $this -> setState('params',$params);
        $this -> setState('char',$char);

        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'portfolio'.DIRECTORY_SEPARATOR.'view.html.php';
        $view   = new TZ_PortfolioViewPortfolio();

        JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

        $list   = $this -> getArticle();

        $view -> assign('listsArticle',$list);
        $view -> assignRef('params',$params);
        $view -> assignRef('mediaParams',$params);
        $view -> assign('Itemid',$Itemid);

        if($layout)
            $data        = $view -> loadTemplate('\''.$layout.'\'');
        else
            $data        = $view -> loadTemplate('item');

        return $data;
    }

    function ajaxCategories(){
        $params     = JComponentHelper::getParams('com_tz_portfolio');

        $Itemid = JRequest::getInt('Itemid');
        $page   = JRequest::getInt('page');
        $curCatids    = JRequest::getString('catIds');
        $curCatids    = json_decode($curCatids);

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit      =   $params -> get('tz_article_limit');
        $limitstart =   $limit * ($page-1);

        $offset = (int) $limitstart;

        $this -> setState('limit',$limit);
        $this -> setState('offset',$offset);
        $this -> setState('params',$params);

        $this -> getArticle();

        $newCatids    = null;
        $catIds       = null;

        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'portfolio'.DIRECTORY_SEPARATOR.'view.html.php';
        $view   = new TZ_PortfolioViewPortfolio();

        if($this -> getCategories())
            $newCatids    = $this -> getCategories();

        if(isset($newCatids) && count($newCatids) > 0){

            foreach($newCatids as $key => $newCatid){
                $bool   = false;
                if(isset($curCatids) && count($curCatids) > 0){
                    foreach($curCatids as $curCatid){
                        if( (int) $curCatid == (int) $newCatid -> id){
                            $bool   = true;
                            break;
                        }
                    }
                }
                if($bool == false){
                    $catIds[] = $newCatid;
                }
            }
        }

        $view -> assign('params',$this -> getState('params'));
        $view -> assign('listsCategories',$catIds);
        $data    = $view -> loadTemplate('Categories');
        if(empty($data))
            return '';

        return $data;
    }
    function getCategories(){
        $catids = $this -> categories;

        if(count($catids) > 1){
            if(empty($catids[0])){
                array_shift($catids);
            }
            $catids = implode(',',$catids);
        }
        else{
            if(!empty($catids[0])){
                $catids = $catids[0];
            }
            else
                $catids = null;
        }

        $where  = null;
        if($catids){
            $where  = ' AND id IN('.$catids.')';
        }
        $query  = 'SELECT id,title FROM #__categories'
                  .' WHERE published=1 AND extension="com_content"'
                  .$where
                  .' GROUP BY id';
        $db = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }

        if($rows = $db -> loadObjectList()){
            return $rows;
        }

        return false;
    }

    public function getAllCategories(){
        $params = $this -> getState('params');
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from('#__categories');
        $query -> where($db -> quoteName('extension').'='.$db -> quote('com_content'));

        if($catid = $params -> get('tz_catid')){
            if(empty($catid[0])){
                array_shift($catid);
            }
            $catid  = implode(',',$catid);
            if(!empty($catid)){
                $query -> where('id IN('.$catid.')');
            }
        }
        $db -> setQuery($query);
        if($rows = $db -> loadObjectList()){
            return $rows;
        }
        return null;
    }

    function getTags(){
        if($this -> rowsTag) {
            return $this-> rowsTag;
        }

        return false;
    }

    public function getAllTags(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('t.*');
        $query -> from('#__tz_portfolio_tags AS t');
        $query -> where('t.published = 1');
        $query -> join('INNER','#__tz_portfolio_tags_xref AS x ON t.id = x.tagsid');
        $query -> group('t.id');
        $db -> setQuery($query);
        if($rows = $db -> loadObjectList()){
            return $rows;
        }
        return null;
    }

    function getTagName($contentId = null){
        if($contentId){
            $query  = 'SELECT t.* FROM #__tz_portfolio_tags AS t'
                      .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.tagsid=t.id'
                      .' WHERE x.contentid='.$contentId
                      .' AND t.published = 1';
            $db     = JFactory::getDbo();
            $db -> setQuery($query);
            $tagName    = array();
            if($db -> query()){
                $rows   = $db -> loadObjectList();
                if(count($rows)){
                    foreach($rows as $row){
                        $tagName[]  = str_replace(' ','-',trim($row -> name));
                    }
                }
            }
            if(count($tagName)>0){
                return implode(' ',$tagName);
            }
        }

        return false;
    }

    function _Tags($contentId=array()){
        if($contentId){
            $contentId  = implode(',',$contentId);
            $query  = 'SELECT t.* FROM #__tz_portfolio_tags AS t'
                .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON t.id=x.tagsid'
                .' WHERE x.contentid IN('.$contentId.') AND t.published = 1'
                .' GROUP BY t.id';
            $db     = JFactory::getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                return false;
            }
            $rows   = $db -> loadObjectList();
            if(count($rows)>0){
                foreach($rows as &$item){
                    $item -> name   = trim($item -> name);
                }
                $this -> rowsTag    = $rows;
            }
        }
        return false;
    }

    function getArticle(){

        $user	= JFactory::getUser();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');

        $params = $this -> getState('params');
//        $params = $state -> get('parameters.menu');

        $catids = $params -> get('tz_catid');

        if(count($catids) > 1){
            if(empty($catids[0])){
                array_shift($catids);
            }
            $catids = implode(',',$catids);
        }
        else{
            if(!empty($catids[0])){
                $catids = $catids[0];
            }
            else
                $catids = null;
        }

        $where  = null;
        if($catids){
            $where  = ' AND c.catid IN('.$catids.')';
        }

        if($char   = $this -> getState('char')){
            $where  .= ' AND ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII("'.mb_strtolower($char).'")';
        }

        $total      = null;
        $limit      = $this -> getState('limit');
        $limitstart = $this -> getState('offset');
        $data       = array();

        $params -> set('access-view',true);

        $this->setState('params', $params);

        $query  = 'SELECT c.*'
                  .' FROM #__content AS c'
                  .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
                  .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.contentid=c.id'
                  .' LEFT JOIN #__tz_portfolio_tags AS t ON t.id=x.tagsid'
                  .' LEFT JOIN #__users AS u ON c.created_by=u.id'
                  .' WHERE c.state=1'
                  .$where
                  .' GROUP BY c.id';
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if($db -> query())
            $total  = $db -> getNumRows($db -> query());
        else
            $total  = 0;

        $this -> pagNav = new JPagination($total,$limitstart,$limit);

        switch ($params -> get('orderby_pri')){
            default:
                $cateOrder  = null;
                break;
            case 'alpha' :
				$cateOrder = 'cc.path, ';
				break;

			case 'ralpha' :
				$cateOrder = 'cc.path DESC, ';
				break;

			case 'order' :
				$cateOrder = 'cc.lft, ';
				break;
        }

        switch ($params -> get('orderby_sec')){
            default:
                $orderby    = 'id DESC';
                break;
            case 'rdate':
                $orderby    = 'created DESC';
                break;
            case 'date':
                $orderby    = 'created ASC';
                break;
            case 'alpha':
                $orderby    = 'title ASC';
                break;
            case 'ralpha':
                $orderby    = 'title DESC';
                break;
            case 'author':
                $orderby    = 'create_by ASC';
                break;
            case 'rauthor':
                $orderby    = 'create_by DESC';
                break;
            case 'hits':
                $orderby    = 'hits DESC';
                break;
            case 'rhits':
                $orderby    = 'hits ASC';
                break;
            case 'order':
                $orderby    = 'ordering ASC';
                break;
        }

        $query  = 'SELECT c.*,t.name AS tagName,cc.title AS category_title,u.name AS author,'
                  .' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug,'
                  .' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug,'
                  .' CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore'
                  .' FROM #__content AS c'
                  .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
                  .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.contentid=c.id'
                  .' LEFT JOIN #__tz_portfolio_tags AS t ON t.id=x.tagsid'
                  .' LEFT JOIN #__users AS u ON u.id=c.created_by'
                  .' WHERE c.state=1'
                  .$where
                  .' GROUP BY c.id'
                  .' ORDER BY '.$cateOrder.$orderby;

        if($params -> get('tz_portfolio_layout') == 'default')
            $db -> setQuery($query,$this -> pagNav -> limitstart,$this -> pagNav -> limit);
        else
            $db -> setQuery($query,$limitstart,$limit);

        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }

        $rows   = $db -> loadObjectList();
        $model  = JModelLegacy::getInstance('Media','TZ_PortfolioModel');

        $contentId  = array();
        if(count($rows)>0){
            if($params -> get('comment_function_type','default') != 'js'){
                if($params -> get('tz_show_count_comment',1) == 1){
                    require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
                    require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');
                    $fetch       = new Services_Yadis_PlainHTTPFetcher();
                }

                $threadLink = null;
                $comments   = null;
                foreach($rows as $key => $item){
                    $tzRedirect = $params -> get('tz_portfolio_redirect','p_article'); //Set params for $tzRedirect
                    $itemParams = new JRegistry($item -> attribs); //Get Article's Params

                    //Check redirect to view article
                    if($itemParams -> get('tz_portfolio_redirect')){
                        $tzRedirect = $itemParams -> get('tz_portfolio_redirect');
                    }

                    if($tzRedirect == 'article'){
                        $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug,$item -> catid), true ,-1);
                    }
                    else{
                        $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug,$item -> catid), true ,-1);
                    }

                    if($params -> get('tz_show_count_comment',1) == 1){
                        if($params -> get('tz_comment_type','disqus') == 'disqus'){
                            $threadLink .= '&thread[]=link:'.$contentUrl;
                        }elseif($params -> get('tz_comment_type','disqus') == 'facebook'){
                            $threadLink .= '&urls[]='.$contentUrl;
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
            }

            //Get Plugins Model
            $pmodel = JModelLegacy::getInstance('Plugins','TZ_PortfolioModel',array('ignore_request' => true));



            foreach($rows as $key => $item){

                $item->text = $item->introtext;

                $tzRedirect = $params -> get('tz_portfolio_redirect','p_article'); //Set params for $tzRedirect
                $itemParams = new JRegistry($item -> attribs); //Get Article's Params

                if($params -> get('comment_function_type','default') != 'js'){
                    //Check redirect to view article
                    if($itemParams -> get('tz_portfolio_redirect')){
                        $tzRedirect = $itemParams -> get('tz_portfolio_redirect');
                    }

                    if($tzRedirect == 'article'){
                        $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug,$item -> catid), true ,-1);
                    }
                    else{
                        $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug,$item -> catid), true ,-1);
                    }

                    if($params -> get('tz_show_count_comment',1) == 1){
                        if($params -> get('tz_comment_type','disqus') == 'disqus' ||
                            $params -> get('tz_comment_type','disqus') == 'facebook'){
                            if($comments){
                                if(array_key_exists($contentUrl,$comments)){
                                    $item -> commentCount   = $comments[$contentUrl];
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

                // Compute the asset access permissions.
                // Technically guest could edit an article, but lets not check that to improve performance a little.
                if (!$guest) {
                    $asset	= 'com_tz_portfolio.article.'.$item->id;

                    // Check general edit permission first.
                    if ($user->authorise('core.edit', $asset)) {
                        $itemParams->set('access-edit', true);
                    }
                    // Now check if edit.own is available.
                    elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                        // Check for a valid user and that they are the owner.
                        if ($userId == $item->created_by) {
                            $itemParams->set('access-edit', true);
                        }
                    }
                }

                //Get plugin Params for this article
                $pmodel -> setState('filter.contentid',$item -> id);
                $pluginItems    = $pmodel -> getItems();
                $pluginParams   = $pmodel -> getParams();
                $item -> pluginparams   = clone($pluginParams);

                // Add feed links
                if (!JRequest::getCmd('format',null) AND !JRequest::getCmd('type',null)) {
                    $dispatcher	= JDispatcher::getInstance();

                    //
                    // Process the content plugins.
                    //
                    JPluginHelper::importPlugin('content');
                    $item->introtext = JHtml::_('content.prepare', $item->introtext, '', 'com_tz_portfolio.portfolio');
//                    $results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('offset')));

                    $item->event = new stdClass();
                    $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('offset')));
                    $item->event->afterDisplayTitle = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('offset')));
                    $item->event->beforeDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('offset')));
                    $item->event->afterDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentTZPortfolioVote', array('com_tz_portfolio.portfolio', &$item, &$params, 0));
				    $item->event->TZPortfolioVote = trim(implode("\n", $results));

                    JPluginHelper::importPlugin('tz_portfolio');
                    $results   = $dispatcher -> trigger('onTZPluginPrepare',array('com_tz_portfolio.portfolio', &$item, &$this->params,&$pluginParams,$this -> getState('offset')));

                    $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.portfolio', &$item, &$params,&$pluginParams, $this -> getState('offset')));
                    $item->event->TZafterDisplayTitle = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.portfolio', &$item, &$params,&$pluginParams, $this -> getState('offset')));
                    $item->event->TZbeforeDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.portfolio', &$item, &$params,&$pluginParams, $this -> getState('offset')));
                    $item->event->TZafterDisplayContent = trim(implode("\n", $results));
                }

                if($introLimit = $params -> get('tz_article_intro_limit')){
                    $text   = new AutoCutText($item -> introtext,$introLimit);
                    $item -> introtext   = $text -> getIntro();
                }

                if(!empty($rows[$key] -> tagName)){
                    $contentId[]    = $rows[$key] -> id;
                    if($tagsName = $this -> getTagName($rows[$key] -> id))
                        $rows[$key] -> tagName  = $tagsName;
                    $data[$key] = $item;
                }

                if(!isset($rows[$key] -> tz_image))
                    $rows[$key] -> tz_image = '';

                $item -> attribs    = $itemParams -> toString();

                //Get Catid
                $this -> categories[]   = $item -> catid;

            }
            $this -> _Tags($contentId);


            return $rows;
        }
    }

    function getAvailableLetter(){
        $params = $this -> getState('params');
        if($params -> get('use_filter_first_letter',1)){
            if($letters = $params -> get('tz_letters','a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z')){
                $db = JFactory::getDbo();
                $letters = explode(',',$letters);
                $arr    = null;
                if($catids = $params -> get('tz_catid')){
                    if(count($catids) > 1){
                        if(empty($catids[0])){
                            array_shift($catids);
                        }
                        $catids = implode(',',$catids);
                    }
                    else{
                        if(!empty($catids[0])){
                            $catids = $catids[0];
                        }
                        else
                            $catids = null;
                    }
                }

                $where  = null;
                if($catids){
                    $where  = ' AND c.catid IN('.$catids.')';
                }

                if($featured = $this -> getState('filter.featured')){
                    if(is_array($featured)){
                        $featured   = implode(',',$featured);
                    }
                    $where  .= ' AND c.featured IN('.$featured.')';
                }

                if($tagId = $this -> getState('filter.tagId')){
                    $where  .= ' AND t.id='.$tagId;
                }

                if($userId = $this -> getState('filter.userId')){
                    $where  .= ' AND c.created_by='.$userId;
                }

                if($year = $this -> getState('filter.year')){
                    $where  .= ' AND YEAR(c.created) = '.$year;
                }

                if($month = $this -> getState('filter.month')){
                    $where  .= ' AND MONTH(c.created) = '.$month;
                }

                foreach($letters as $i => $letter){
                    $query  = 'SELECT c.*'
                          .' FROM #__content AS c'
                          .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
                          .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.contentid=c.id'
                          .' LEFT JOIN #__tz_portfolio_tags AS t ON t.id=x.tagsid'
                          .' LEFT JOIN #__users AS u ON c.created_by=u.id'
                          .' WHERE c.state=1'
                              .$where
                              .' AND ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII("'.mb_strtolower($letter).'")'
                          .' GROUP BY c.id';
                    $db -> setQuery($query);
                    $count  = $db -> loadResult();
                    $arr[$i]    = false;
                    if($count){
                        $arr[$i]  = true;
                    }
                }

                return $arr;

            }
        }
        return false;
    }

    function getPagination(){
        if($this -> pagNav)
            return $this -> pagNav;
        return false;
    }

    public function ajaxComments(){
        $data   = json_decode(base64_decode(JRequest::getString('url')));
        $id     = json_decode(base64_decode(JRequest::getString('id')));
        if($data){
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');

            $params     = JComponentHelper::getParams('com_tz_portfolio');

            $Itemid     = JRequest::getInt('Itemid');

            $menu       = JMenu::getInstance('site');
            $menuParams = $menu -> getParams($Itemid);

            $params -> merge($menuParams);

            $threadLink = null;

            $_id    = null;

            if(is_array($data) && count($data)){
                foreach($data as $i => &$contentUrl){
                    if(!preg_match('/http\:\/\//i',$contentUrl)){
                        $uri    = JUri::getInstance();
                        $contentUrl    = $uri -> getScheme().'://'.$uri -> getHost().$contentUrl;
                    }

                    if(preg_match('/(.*?)(\?tmpl\=component)|(\&tmpl\=component)/i',$contentUrl)){
                        $contentUrl = preg_replace('/(.*?)(\?tmpl\=component)|(\&tmpl\=component)/i','$1',$contentUrl);
                    }

                    $_id[$contentUrl]  = $id[$i];

                    if($params -> get('tz_comment_type','disqus') == 'facebook'){
                        $threadLink .= '&urls[]='.$contentUrl;
                    }elseif($params -> get('tz_comment_type','disqus') == 'disqus'){
                        $threadLink .= '&thread[]=link:'.$contentUrl;
                    }
                }
            }

            if(!is_array($data)){
                $threadLink = $data;
            }

            $fetch       = new Services_Yadis_PlainHTTPFetcher();
            $comments    = null;

            if($params -> get('tz_show_count_comment',1) == 1){
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
                                                    if(in_array($match[1],$data)){
                                                        $comments[$_id[$match[1]]]    = $match[2];
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

                // Disqus Comment count
                if($params -> get('tz_comment_type','disqus') == 'disqus'){

                    $url        = 'https://disqus.com/api/3.0/threads/list.json?api_secret='
                                  .$params -> get('disqusApiSecretKey','4sLbLjSq7ZCYtlMkfsG7SS5muVp7DsGgwedJL5gRsfUuXIt6AX5h6Ae6PnNREMiB')
                                  .'&forum='.$params -> get('disqusSubDomain','templazatoturials')
                                  .$threadLink.'&include=open';

                    if($_content = $fetch -> get($url)){

                        $body    = json_decode($_content -> body);
                        if(isset($body -> response)){
                            if($responses = $body -> response){
                                foreach($responses as $response){
                                    if(in_array($response ->link,$data)){
                                        $comments[$_id[$response ->link]]    = $response -> posts;
                                    }
                                }
                            }

                        }
                    }
                }

                if($comments){
                    if(is_array($comments)){
                        return json_encode($comments);
                    }
                    return 0;
                }
                return 0;
            }
        }
    }
}
?>