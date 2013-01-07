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
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'autocuttext.php');

class TZ_PortfolioModelTimeLine extends JModelLegacy
{
    protected   $pagNav     = null;
    protected   $params     = null;
    protected $rowsTag      = null;
    protected $categories   = null;
    protected $articles     = null;

    function populateState(){
        $app        = &JFactory::getApplication();
        $this -> params = $app -> getParams();
        $limit  = $this -> params -> get('tz_timeline_article_limit');
        if(empty($limit)){
            $this -> params -> set('tz_timeline_article_limit',10);
        }
        $this -> params -> set('useCloudZoom',0);
        $this -> setState('offset',JRequest::getUInt('limitstart',0));
        $this -> setState('limit',$this -> params -> get('tz_timeline_article_limit'));
        $this -> setState('params',$this -> params);
    }

    function ajax(){
        $data        = null;

        $params     = JComponentHelper::getParams('com_tz_portfolio');

        $Itemid     = JRequest::getInt('Itemid');
        $page       = JRequest::getInt('page');
        $layout     = JRequest::getString('layout');

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit  = $params -> get('tz_timeline_article_limit');

        $offset = $limit * ($page - 1);

        $this -> setState('limit',$limit);
        $this -> setState('offset',$offset);
        $this -> setState('params',$params);

        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'timeline'.DIRECTORY_SEPARATOR.'view.html.php';
        $view   = new TZ_PortfolioViewTimeLine();

        $list   = $this -> getArticle();

        $view -> assign('listsArticle',$list);
        if($params -> get('tz_filter_type','tags') == 'categories'){
            $view -> assign('listsCatDate',$this -> getDateCategories());
        }
        $view -> assign('params',$params);
        $view -> assign('Itemid',$Itemid);
        $view -> assign('limitstart',$offset);

        if($page > $this -> pagNav -> get('pages.stop'))
            return '';

        if($layout)
            $data        = $view -> loadTemplate('\''.$layout.'\'');
        else
            $data        = $view -> loadTemplate('item');

        return $data;
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

        $limit      =   $params -> get('tz_timeline_article_limit');
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

    function ajaxCategories(){
        $params     = JComponentHelper::getParams('com_tz_portfolio');

        $Itemid = JRequest::getInt('Itemid');
        $page   = JRequest::getInt('page');
        $curCatids    = JRequest::getString('catIds');
        $curCatids    = json_decode($curCatids);

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit      =   $params -> get('tz_timeline_article_limit');
        $limitstart =   $limit * ($page-1);

        $offset = (int) $limitstart;

        $this -> setState('limit',$limit);
        $this -> setState('offset',$offset);
        $this -> setState('params',$params);

        $list   = $this -> getArticle();

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
                if($bool != true){
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
            $where  = ' AND cc.id IN('.$catids.')';
        }
        $query  = 'SELECT cc.id,cc.title FROM #__categories AS cc'
                  .' LEFT JOIN #__content AS c ON c.catid=cc.id'
                  .' WHERE cc.published=1 AND cc.extension="com_content" AND c.state=1'
                  .$where
                  .' GROUP BY cc.id';
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

    //Get Categories
    function getDateCategories($artItem=null){
        if($this -> articles){
            $articleId  = implode(',',$this -> articles);
        }
        //Get Catid, created by article
        $query  = 'SELECT cc.id,cc.title,YEAR(c.created) AS year,MONTH(c.created) AS month,'
                  .'CONCAT_WS(":",YEAR(c.created),MONTH(c.created)) AS tz_date'
                  .' FROM #__categories AS cc'
                  .' LEFT JOIN #__content AS c ON c.catid=cc.id'
                  .' WHERE cc.extension="com_content" AND cc.published=1 AND c.state=1'
                  .' ORDER BY tz_date DESC';
        $db     = &JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            echo $db -> getErrorMsg();
            return false;
        }
        if($rows = $db -> loadObjectList()){
            $data   = null;
            foreach($rows as $i => $item){
                if($item -> tz_date && !empty($item -> tz_date)){
                    if($i == 0){
                        $data[] = $item;
                    }
                    else{
                        if($rows[$i-1] -> id != $item -> id || $rows[$i-1] -> tz_date != $item -> tz_date){
                            $data[] = $item;
                        }
                    }
                }
            }
            return $data;
        }
        return false;
    }

    function getTZDate(){
        $params     = $this -> getState('params');
        $_catid     = $params -> get('tz_catid');
        $catids     = null;
        $limit      = $this -> getState('limit');
        $limitStart = $this -> getState('offset');

        if($_catid){
            if(count($_catid) == 1 ){
                if(!empty($_catid[0])){
                    $catids[]   = $_catid[0];
                }
            }
            else{
                if(empty($_catid[0])){
                    array_shift($_catid);
                }
                $catids = $_catid;
            }
        }
        if($catids){
            $catids = ' AND c.catid IN('.implode(',',$catids).')';
        }
//        $query  = 'SELECT c.*'
//                  .' FROM #__content AS c'
//                  .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
//                  .' WHERE c.state = 1'
//                  .$catids;
//
//        $db     = &JFactory::getDbo();
//        $db -> setQuery($query);
//
//        if(!$db -> query()){
//            var_dump($db -> getErrorMsg());
//            die();
//        }
//
//        if($db -> query())
//            $total  = $db -> getNumRows($db -> query());
//        else
//            $total  = 0;
//
//        $this -> pagNav = new JPagination($total,$limitStart,$limit);

        $db     = &JFactory::getDbo();
        $query  = 'SELECT c.created,YEAR(c.created) AS year,MONTH(c.created) AS month,'
                  .'CONCAT_WS(":",YEAR(c.created),MONTH(c.created)) AS tz_date'
                  .' FROM #__content AS c'
                  .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
                  .' WHERE c.state = 1'
                  .$catids
                  .' GROUP BY tz_date'
                  .' ORDER BY tz_date DESC';
        $db -> setQuery($query,$this -> pagNav -> limitstart,$this -> pagNav -> limit);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }
        if($rows = $db -> loadObjectList()){
            return $rows;
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
                        $tagName[]  = trim(str_replace(' ','-',$row -> name));
                    }
                }
            }
            if(count($tagName)>0){
                return implode(' ',$tagName);
            }
        }

        return false;
    }

    function getArticle(){

        $params     = $this -> getState('params');
        $total      = 0;
        $limit      = $this -> getState('limit');
        $limitstart = $this -> getState('offset');

        $_catid     = $params -> get('tz_catid');
        $allCatid   = null;
        $catids     = null;
        $where      = null;
        if($_catid){
            if(count($_catid) == 1 ){
                if(!empty($_catid[0])){
                    $catids[]   = $_catid[0];
                }
            }
            else{
                if(empty($_catid[0])){
                    array_shift($_catid);
                }
                $catids = $_catid;
            }
        }

        if($this -> getCategories()){
            foreach($this -> getCategories() as $item){
                $allCatid[]   = 'category'.$item -> id;
            }
        }

        if($catids){
            //all filter catgory
            foreach($catids as $catId){
                $allCatid[] = 'category'.$catId;
            }

            $where = ' AND c.catid IN('.implode(',',$catids).')';
        }

        $query  = 'SELECT c.*,CONCAT_WS(":",YEAR(c.created),MONTH(c.created)) AS tz_date'
                  .' FROM #__content AS c'
                  .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
                  .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.contentid=c.id'
                  .' LEFT JOIN #__tz_portfolio_tags AS t ON t.id=x.tagsid'
                  .' LEFT JOIN #__users AS u ON u.id=c.created_by'
                  .' WHERE c.state = 1'
                  .$where
                  .' GROUP BY c.id';

        $db     = &JFactory::getDbo();
        $db -> setQuery($query);

        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }

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

        $query  = 'SELECT c.*,t.name AS tagName,YEAR(c.created) AS year,MONTH(c.created) AS month,'
                  .'CONCAT_WS(":",YEAR(c.created),MONTH(c.created)) AS tz_date,'
                  .'cc.title AS category_title,u.name AS author,'
                  .' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug,'
                  .' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug,'
                  .' CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore'
                  .' FROM #__content AS c'
                  .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
                  .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.contentid=c.id'
                  .' LEFT JOIN #__tz_portfolio_tags AS t ON t.id=x.tagsid'
                  .' LEFT JOIN #__users AS u ON u.id=c.created_by'
                  .' WHERE c.state = 1'
                  .$where
                  .' GROUP BY c.id'
                  .' ORDER BY c.created DESC,'.$orderby;

        if($params -> get('tz_timeline_layout') == 'default')
            $db -> setQuery($query,$this -> pagNav -> limitstart,$this -> pagNav -> limit);
        else
            $db -> setQuery($query,$limitstart,$limit);

        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }

        $contentId  = array();
        $tzDate     = array();
        if($rows   = $db -> loadObjectList()){
            if($params -> get('tz_show_count_comment',1) == 1){
                require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
                require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');
                $fetch       = new Services_Yadis_PlainHTTPFetcher();
            }
            foreach($rows as $i => $item){
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
                                      .$params -> get('disqusApiSecretKey','DGBlgtq5QMvrAKQaiLh0yqC9GE82jYIHrF3W43go0rks9UBeiho4sLAYadcMks4x')
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

                $this -> articles[]   = $item -> id;
                $model  = null;

                if(!empty($item -> tagName)){
                    $contentId[]    = $item -> id;
                    if($tagsName = $this -> getTagName($item -> id))
                        $item -> tagName  = $tagsName;
                }

                //Get Catid
                $this -> categories[]   = $item -> catid;

                if($model  = &JModelLegacy::getInstance('Media','TZ_PortfolioModel')){
                    if($media  = $model -> getMedia($item -> id)){

                        if($media[0] -> type != 'video'){
                            if($params -> get('portfolio_image_size','M')){


                            }

                            if(!empty($media[0] -> images)){
                                $item -> tz_image       = JURI::root().str_replace('.'.JFile::getExt($media[0] -> images),
                                                           '_'.$params -> get('portfolio_image_size','M')
                                                          .'.'.JFile::getExt($media[0] -> images),$media[0] -> images);;
                                $item -> tz_image_title = $media[0] -> imagetitle;
                            }
                            else{
                                $item -> tz_image       = null;
                            }
                        }
                        else{
                            if(!empty($media[0] -> thumb)){
                                $item -> tz_image       = JURI::root().str_replace('.'.JFile::getExt($media[0] -> thumb)
                                    ,'_'.$params -> get('portfolio_image_size','M')
                                  .'.'.JFile::getExt($media[0] -> thumb),$media[0] -> thumb);
                                $item -> tz_image_title = $media[0] -> imagetitle;
                            }
                            else
                                $item -> tz_image       = null;
                        }

                    }
                    else{
                        $item -> tz_image       = null;
                    }
                }
                $tzRedirect = $params -> get('tz_portfolio_redirect','p_article'); //Set params for $tzRedirect
                $itemParams = new JRegistry($item -> attribs); //Get Article's Params
                //Check redirect to view article
                if($itemParams -> get('tz_portfolio_redirect')){
                    $tzRedirect = $itemParams -> get('tz_portfolio_redirect');
                }
                if($tzRedirect == 'article'){
                    $item -> _link = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid));
                }
                else{
                    $item -> _link = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid));
                }


                if($params -> get('tz_filter_type','tags') == 'tags'){
                    $item -> allTags    = $this -> _getAllTags($item -> created);
                }

            }

            $this -> _getTags($contentId);

            return $rows;
        }
        return false;
    }

    protected function _getAllTags($date = null){
        $params = $this -> getState('params');
        $query  = 'SELECT c.*,t.name AS tagName FROM #__content AS c'
                  .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON c.id=x.contentid'
                  .' LEFT JOIN #__tz_portfolio_tags AS t ON t.id=x.tagsid';
        $db = &JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        $tags   = null;
        if($rows = $db -> loadObjectList()){
            if($params -> get('tz_timeline_time_type') == 'month-year'){
                $format = 'Y:n';
            }
            elseif($params -> get('tz_timeline_time_type') == 'month'){
                $format = 'n';
            }
            elseif($params -> get('tz_timeline_time_type') == 'year'){
                $format = 'Y';
            }

            foreach($rows as $item){
                if( date($format,strtotime($item -> created)) == date($format,strtotime($date))){
                    $tags[] = $item -> tagName;
                }
            }
        }
        $newTags    = null;

        if($tags){
            for($i=0; $i<count($tags); $i++){
                $bool   = true;
                for($j= $i + 1; $j<count($tags); $j++){
                    if($tags[$i] == $tags[$j]){
                        $bool   = false;
                        break;
                    }
                }
                if($bool != false && !empty($tags[$i])){
                    $newTags[]    = str_replace(' ','-',trim($tags[$i]));
                }
            }
        }
        if($newTags){
            return implode(' ',$newTags);
        }
        return false;

    }

    protected function _getTags($contentId=array()){
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

    function getTags(){
        if($this -> rowsTag) {
            return $this-> rowsTag;
        }

        return false;
    }

    function getPagination(){
        if($this -> pagNav)
            return $this -> pagNav;
        return false;
    }

    function getParams(){
        if($this -> params)
            return $this -> params;
        return false;
    }
}