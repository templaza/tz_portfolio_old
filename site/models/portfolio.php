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
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'autocuttext.php');

class TZ_PortfolioModelPortfolio extends JModelList
{
    protected $pagNav       = null;
    protected $rowsTag      = null;
    protected $categories   = null;
    public $test            = null;

    function populateState(){
        $app    = &JFactory::getApplication();
//        $params = JComponentHelper::getParams('com_tz_portfolio');
        $params = $app -> getParams();

        $offset = JRequest::getUInt('limitstart',0);


        if($params -> get('show_limit_box',0)){
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
    }

    function ajaxtags($limitstart=null) {

        $params     = JComponentHelper::getParams('com_tz_portfolio');

        $Itemid = JRequest::getInt('Itemid');
        $page   = JRequest::getInt('page');
        $curTags    = JRequest::getString('tags');
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

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit  = $params -> get('tz_article_limit');

        $offset = $limit * ($page - 1);

        $this -> setState('limit',$limit);
        $this -> setState('offset',$offset);
        $this -> setState('params',$params);

        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'portfolio'.DIRECTORY_SEPARATOR.'view.html.php';
        $view   = new TZ_PortfolioViewPortfolio();

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
        $db = &JFactory::getDbo();
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

    function getTags(){
        if($this -> rowsTag) {
            return $this-> rowsTag;
        }

        return false;
    }

    function getTagName($contentId = null){
        if($contentId){
            $query  = 'SELECT t.* FROM #__tz_portfolio_tags AS t'
                      .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.tagsid=t.id'
                      .' WHERE x.contentid='.$contentId;
            $db     = &JFactory::getDbo();
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
                .' WHERE x.contentid IN('.$contentId.')'
                .' GROUP BY t.id';
            $db     = &JFactory::getDbo();
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
        $db     = &JFactory::getDbo();
        $db -> setQuery($query);
        if($db -> query())
            $total  = $db -> getNumRows($db -> query());
        else
            $total  = 0;

        $this -> pagNav = new JPagination($total,$limitstart,$limit);

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
                  .' ORDER BY '.$orderby;

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
            if($params -> get('tz_show_count_comment',1) == 1){
                require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
                require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');
                $fetch       = new Services_Yadis_PlainHTTPFetcher();
            }
            foreach($rows as $key => $item){
                if ($params->get('show_intro', '1')=='1') {
                    $item->text = $item->introtext.' '.$item->fulltext;
                }
                elseif ($item->fulltext) {
                    $item->text = $item->fulltext;
                }
                else  {
                    $item->text = $item->introtext;
                }

                if($params -> get('tz_article_intro_limit',20)){
                    $intro  = strip_tags($item -> introtext);
                    $intro  = explode(' ',$intro);
                    $item -> text    = implode(' ',array_splice($intro,0,$params -> get('tz_article_intro_limit',20)));
                }
                
                $tzRedirect = $params -> get('tz_portfolio_redirect','p_article'); //Set params for $tzRedirect
                $itemParams = new JRegistry($item -> attribs); //Get Article's Params
                //Check redirect to view article
                if($itemParams -> get('tz_portfolio_redirect')){
                    $tzRedirect = $itemParams -> get('tz_portfolio_redirect');
                }

                if($tzRedirect == 'p_article'){
                    $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug,$item -> catid), true ,-1);
                }
                else{
                    $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug,$item -> catid), true ,-1);
                }

                if($params -> get('tz_comment_type','disqus') == 'facebook'){
                    if($params -> get('tz_show_count_comment',1) == 1){

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

                if($params -> get('tz_comment_type','disqus') == 'disqus'){
                    if($params -> get('tz_show_count_comment',1) == 1){

                        $url        = 'https://disqus.com/api/3.0/threads/listPosts.json?api_secret='
                                      .$params -> get('disqusApiSecretKey','DGBlgtq5QMvrAKQaiLh0yqC9GE82jYIHrF3W43go0rks9UBeiho4sLAYadcMks4xs')
                                      .'&forum='.$params -> get('disqusSubDomain','templazatoturials')
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

                if ($params->get('show_intro', '1')=='1') {
                    $item->text = $item->introtext.' '.$item->fulltext;
                }
                elseif ($item->fulltext) {
                    $item->text = $item->fulltext;
                }
                else  {
                    $item->text = $item->introtext;
                }

                // Add feed links
                if (!JRequest::getCmd('format',null) AND !JRequest::getCmd('type',null)) {
                    $dispatcher	= JDispatcher::getInstance();
                    //
                    // Process the content plugins.
                    //
                    JPluginHelper::importPlugin('content');
                    $results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('offset')));

                    $item->event = new stdClass();
                    $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('offset')));
                    $item->event->afterDisplayTitle = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('offset')));
                    $item->event->beforeDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('offset')));
                    $item->event->afterDisplayContent = trim(implode("\n", $results));
                }

                $text   = new AutoCutText($item -> text,$params -> get('tz_article_intro_limit',20));
                $item -> text   = $text -> getIntro();
//                if($params -> get('tz_article_intro_limit',20)){
//                    $intro  = strip_tags($item -> text);
//                    $intro  = explode(' ',$intro);
//                    $item -> text    = implode(' ',array_splice($intro,0,$params -> get('tz_article_intro_limit',20)));
//                }

                if(!empty($rows[$key] -> tagName)){
                    $contentId[]    = $rows[$key] -> id;
                    if($tagsName = $this -> getTagName($rows[$key] -> id))
                        $rows[$key] -> tagName  = $tagsName;
                    $data[$key] = $item;
                }
                
                if($model){
                    if($image  = $model -> getMedia($rows[$key] -> id)){
                        if($image[0] -> type == 'video')
                            $rows[$key] -> tz_image_type = $image[0] -> type;
                        else
                            $rows[$key] -> tz_image_type = null;

                        if(JFile::exists(JURI::base(JPATH_SITE).DIRECTORY_SEPARATOR.$image[0] -> images))
                            $rows[$key] -> tz_image = JURI::base(JPATH_SITE).DIRECTORY_SEPARATOR.$image[0] -> images;
                        else
                            $rows[$key] -> tz_image = null;
                    }
                }

                if(!isset($rows[$key] -> tz_image))
                    $rows[$key] -> tz_image = '';

                //Get Catid
                $this -> categories[]   = $item -> catid;

            }
            $this -> _Tags($contentId);

            return $rows;
        }
    }

    function getPagination(){
        if($this -> pagNav)
            return $this -> pagNav;
        return false;
    }
}
?>