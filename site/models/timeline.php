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
jimport('joomla.application.component.modellist');
jimport('joomla.html.pagination');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'autocuttext.php');

class TZ_PortfolioModelTimeLine extends JModelList
{
    protected   $pagNav     = null;
    protected   $params     = null;
    protected $rowsTag      = null;
    protected $categories   = null;
    protected $articles     = null;

    protected $parameter_fields = array();
    protected $parameter_merge_fields = array();

    public function __construct($config = array()){
        parent::__construct($config);

        $config['parameter_fields'] = array(
            'tz_use_image_hover' => array('tz_image_timeout'),
            'show_image_gallery' => array('image_gallery_animSpeed',
                'image_gallery_animation_duration',
                'image_gallery_startAt', 'image_gallery_itemWidth',
                'image_gallery_itemMargin', 'image_gallery_minItems',
                'image_gallery_maxItems'),
            'show_video' => array('video_width','video_height'),
            'tz_show_gmap' => array('tz_gmap_width', 'tz_gmap_height',
                'tz_gmap_latitude', 'tz_gmap_longitude',
                'tz_gmap_address','tz_gmap_custom_tooltip'),
            'useCloudZoom' => array('zoomWidth','zoomHeight',
                'adjustX','adjustY','tint','tintOpacity',
                'lensOpacity','smoothMove'),
            'show_comment' => array('disqusSubDomain','disqusApiSecretKey'),
            'show_audio' => array('audio_soundcloud_color','audio_soundcloud_theme_color',
                'audio_soundcloud_width','audio_soundcloud_height')
        );
        // Add the parameter fields white list.
        if (isset($config['parameter_fields']))
        {
            $this->parameter_fields = $config['parameter_fields'];
        }

        // Add the parameter fields white list.
        $this -> parameter_merge_fields = array(
            'show_extra_fields', 'field_show_type',
            'tz_portfolio_redirect'
        );
    }

    function populateState($ordering = null, $direction = null){
        parent::populateState($ordering, $direction);
        $app        = JFactory::getApplication();
        $params = $app -> getParams();

        if($params -> get('tz_portfolio_redirect') == 'default'){
            $params -> set('tz_portfolio_redirect','article');
        }

        $user		= JFactory::getUser();

        $this -> params = $params;
        if($params -> get('show_limit_box',0) && $params -> get('tz_timeline_layout','default') == 'classic'){
            $limit  = $app->getUserStateFromRequest('com_tz_portfolio.timeline.limit','limit',$params -> get('tz_article_limit',10));
        }
        else{
            $limit  = $params -> get('tz_article_limit',10);
        }

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this->setState('filter.language', $app->getLanguageFilter());

        $params -> set('useCloudZoom',0);
        $this -> setState('list.start',JRequest::getUInt('limitstart',0));
        $this -> setState('list.limit',$limit);
        $this -> setState('params',$this -> params);
        $this -> setState('char',JRequest::getString('char',null));
        
    }

    function ajax(){
        $data        = null;

        $params     = JComponentHelper::getParams('com_tz_portfolio');

        // Set value again for option tz_portfolio_redirect
        if($params -> get('tz_portfolio_redirect') == 'default'){
            $params -> set('tz_portfolio_redirect','article');
        }

        $Itemid     = JRequest::getInt('Itemid');
        $page       = JRequest::getInt('page');
        $layout     = JRequest::getString('layout');
        $char       = JRequest::getString('char');

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit  = $params -> get('tz_article_limit');

        $offset = $limit * ($page - 1);

        $user   = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $app    = JFactory::getApplication();
        $this->setState('filter.language', $app->getLanguageFilter());

        $this -> setState('list.limit',$limit);
        $this -> setState('list.start',$offset);
        $this -> setState('params',$params);
        $this -> setState('char',$char);

        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'timeline'.DIRECTORY_SEPARATOR.'view.html.php';
        $view   = new TZ_PortfolioViewTimeLine();

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
                $view -> extraFields -> setState('filter.option.order',$fieldsOptionOrder);
            }
        }
        
        JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

        $list   = $this -> getItems();

        $view -> assign('listsArticle',$list);
        if($params -> get('tz_filter_type','tags') == 'categories'){
            $view -> assign('listsCatDate',$this -> getDateCategories());
        }else{
            $view -> assign('listsCatDate',false);
        }
        $view -> assign('params',$params);
        $view -> assign('Itemid',$Itemid);
        $view -> assign('limitstart',$offset);

        if($offset >= $this -> getTotal()){
            return null;
        }

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
        $curTags    = stripslashes(JRequest::getString('tags'));
        $curTags    = json_decode($curTags);

        $menu       = JMenu::getInstance('site');
        $menuParams = $menu -> getParams($Itemid);

        $params -> merge($menuParams);

        $limit      =   $params -> get('tz_article_limit');
        $limitstart =   $limit * ($page-1);

        $offset = (int) $limitstart;

        $user   = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this -> setState('list.limit',$limit);
        $this -> setState('list.start',$offset);
        $this -> setState('params',$params);

        $this -> getItems();

        $newTags    = null;
        $tags       = null;

        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'timeline'.DIRECTORY_SEPARATOR.'view.html.php';
        $view   = new TZ_PortfolioViewTimeLine();

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

        $limit      =   $params -> get('tz_article_limit');
        $limitstart =   $limit * ($page-1);

        $offset = (int) $limitstart;

        $user   = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this -> setState('list.limit',$limit);
        $this -> setState('list.start',$offset);
        $this -> setState('params',$params);

        $list   = $this -> getItems();

        $newCatids    = null;
        $catIds       = null;

        require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'timeline'.DIRECTORY_SEPARATOR.'view.html.php';
        $view   = new TZ_PortfolioViewTimeLine();

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
        $params = $this -> getState('params');
        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('id,title');
        $query -> from($db -> quoteName('#__categories'));
        $query -> where('published = 1');
        $query -> where('extension='.$db -> quote('com_content'));

        if(is_array($catids)){
            $catids = array_filter($catids);
            if(count($catids)){
                $query -> where('id IN('.implode(',',$catids).')');
            }
        }elseif(!empty($catids)){
            $query -> where('id IN('.$catids.')');
        }

        // Order by artilce
        switch ($params -> get('orderby_pri')){
            case 'alpha' :
                $query -> order('title');
                break;

            case 'ralpha' :
                $query -> order('title DESC ');
                break;

            case 'order' :
                $query -> order('lft');
                break;
        }

        $query -> group('id');

        $db -> setQuery($query);

        if($rows = $db -> loadObjectList()){
            if($allCatIds  = $this -> getAllCategories()){
                foreach($allCatIds as &$allCatId){
                    $allCatId   = (int) $allCatId -> id;
                }
            }

            $array      = array();
            $revArray   = array();
            if(is_array($catids)){
                $array      = array_intersect($allCatIds,$catids);
                $revArray   = array_flip($array);
            }

            foreach($rows as $item){
                $item -> order  = 0;
                if(in_array($item -> id,$array)){
                    $item -> order  = $revArray[$item -> id];
                }
            }
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

        // Order by artilce
        switch ($params -> get('orderby_pri')){
            case 'alpha' :
                $query -> order('title');
                break;

            case 'ralpha' :
                $query -> order('title DESC ');
                break;

            case 'order' :
                $query -> order('lft');
                break;
        }

        $db -> setQuery($query);
        if($rows = $db -> loadObjectList()){
            return $rows;
        }
        return null;
    }

    //Get first letter of title
    function getFirstLetter(){
        
    }


    //Get Categories
    function getDateCategories($artItem=null){
        if($this -> articles){
            $articleId  = implode(',',$this -> articles);
        }
        //Get Catid, created by article
        $query  = 'SELECT cc.id,cc.title,YEAR(c.created) AS year,MONTH(c.created) AS month,'
                  .'CONCAT_WS("-",YEAR(c.created),MONTH(c.created)) AS tz_date'
                  .' FROM #__categories AS cc'
                  .' LEFT JOIN #__content AS c ON c.catid=cc.id'
                  .' WHERE cc.extension="com_content" AND cc.published=1 AND c.state=1'
                  .' ORDER BY c.created DESC';
        $db     = JFactory::getDbo();
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
        $limitStart = $this -> getState('list.start');

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

        $db     = JFactory::getDbo();
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
            $db     = JFactory::getDbo();
            $db -> setQuery($query);
            $tagName    = array();
            if($db -> query()){
                $rows   = $db -> loadObjectList();
                if(count($rows)){
                    foreach($rows as $row){
                        $tagName[]  = JApplication::stringURLSafe(trim($row -> name));
                    }
                }
            }
            if(count($tagName)>0){
                return implode(' ',$tagName);
            }
        }

        return false;
    }

    protected function getListQuery(){
        $params = $this -> getState('params');

        $user		= JFactory::getUser();

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $subQuery  = $db -> getQuery(true);

        $query -> select('c.*,t.name AS tagName,YEAR(c.created) AS year,MONTH(c.created) AS month');
        $query -> select('CONCAT_WS("-",YEAR(c.created),MONTH(c.created)) AS tz_date');
        $query -> select('cc.title AS category_title,u.name AS author');
        $query -> select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
        $query -> select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query -> select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

        $query -> from($db -> quoteName('#__content').' AS c');

        $query -> join('LEFT',$db -> quoteName('#__categories').' AS cc ON cc.id=c.catid');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_tags_xref').' AS x ON x.contentid=c.id');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_tags').' AS t ON t.id=x.tagsid');
        $query -> join('LEFT',$db -> quoteName('#__users').' AS u ON u.id=c.created_by');

//        $query -> where('c.state = 1');

         // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state = ' . (int) $published);
        }
        elseif (is_array($published)) {
            JArrayHelper::toInteger($published);
            $published = implode(',', $published);
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state IN ('.$published.')');
        }

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
            // Filter by start and end dates.
            $nullDate = $db->Quote($db->getNullDate());
            $nowDate = $db->Quote(JFactory::getDate()->toSQL());

            $query->where('(c.publish_up = ' . $nullDate . ' OR c.publish_up <= ' . $nowDate . ')');
            $query->where('(c.publish_down = ' . $nullDate . ' OR c.publish_down >= ' . $nowDate . ')');
        }

        // Filter by access level.
        if (!$params->get('show_noauth')) {
            $groups	= implode(',', $user->getAuthorisedViewLevels());
            $query->where('c.access IN ('.$groups.')');
            $query->where('cc.access IN ('.$groups.')');
        }

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

            $query -> where('c.catid IN('.implode(',',$catids).')');

            $subQuery -> where('c.catid IN('.implode(',',$catids).')');
        }

        if($char   = $this -> getState('char')){
            $query -> where('ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII('.$db -> quote(mb_strtolower($char)).')');
            $subQuery -> where('ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII('.$db -> quote(mb_strtolower($char)).')');
        }

        $query -> group('c.id');

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
        $query -> order($cateOrder.'c.created DESC,'.$orderby);

        /** Query get max hits for sort filter **/
        $subQuery -> select('MAX(c.hits)');
        $subQuery -> from($db -> quoteName('#__content').' AS c');

        $subQuery -> join('LEFT',$db -> quoteName('#__categories').' AS cc ON cc.id=c.catid');
        $subQuery -> join('LEFT',$db -> quoteName('#__tz_portfolio_tags_xref').' AS x ON x.contentid=c.id');
        $subQuery -> join('LEFT',$db -> quoteName('#__tz_portfolio_tags').' AS t ON t.id=x.tagsid');
        $subQuery -> join('LEFT',$db -> quoteName('#__users').' AS u ON u.id=c.created_by');

        $query -> select('('.$subQuery -> __toString().') AS maxhits');
        /** End query **/

        // Filter by language
        if ($this->getState('filter.language')) {
            $query->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
//            $query->where('(contact.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR contact.language IS NULL)');
        }

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $user	        = JFactory::getUser();
            $userId	        = $user->get('id');
            $guest	        = $user->get('guest');

            $params         = $this -> getState('params');

            $contentId      = array();
            $tzDate         = array();
            $content_ids    = array();

            $_params        = null;
            $categories     = JCategories::getInstance('Content');

            $threadLink     = null;
            $comments       = null;

            foreach($items as &$item){
                $content_ids[]  = $item -> id;
                $_params        = clone($params);
                $temp           = clone($params);

                // Get the global params
                $globalParams = JComponentHelper::getParams('com_tz_portfolio', true);

                /*** New source ***/
                $category   = $categories->get($item -> catid);
                $catParams  = new JRegistry($category -> params);

                if($this -> parameter_merge_fields){
                    foreach($this -> parameter_merge_fields as $value){
                        if($catParams -> get($value) != ''){
                            $_params -> set($value,$catParams -> get($value));
                        }
                    }
                }

                $item->params   = clone($_params);

                $articleParams = new JRegistry;
                $articleParams->loadString($item->attribs);

                // create an array of just the params set to 'use_article'
                $menuParamsArray = $temp->toArray();
                $articleArray = array();

                foreach ($menuParamsArray as $key => $value)
                {
                    if ($value === 'use_article') {
                        // if the article has a value, use it
                        if ($articleParams->get($key) != '') {
                            // get the value from the article
                            $articleArray[$key] = $articleParams->get($key);
                        }
                        else {
                            if($_params -> get($key) != ''){
                                $articleArray[$key] = $_params -> get($key);
                            }else{
                                if(!$_params -> get($key) || $_params -> get($key) == ''){
                                    // otherwise, use the global value
                                    $articleArray[$key] = $globalParams->get($key);
                                }
                            }
                        }

                        if(count($this -> parameter_fields)){
                            $parameter_fields   = $this -> parameter_fields;
                            if(in_array($key,array_keys($this -> parameter_fields))){
                                if(count($parameter_fields[$key])){
                                    foreach($parameter_fields[$key] as $value_field){
                                        $articleArray[$value_field]   = $articleParams -> get($value_field);
                                    }
                                }
                            }
                        }
                    }
                }

                // merge the selected article params
                if (count($articleArray) > 0) {
                    $articleParams = new JRegistry;
                    $articleParams->loadArray($articleArray);
                    $item->params->merge($articleParams);
                }

                if($params -> get('comment_function_type','default') != 'js'){
                    /*** New source ***/
                    //Check redirect to view article
                    if($item -> params -> get('tz_portfolio_redirect','p_article') == 'article'){
                        $contentUrl   = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid),true,-1);
                    }
                    else{
                        $contentUrl   = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid),true,-1);
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

            if($params -> get('comment_function_type','default') != 'js'){
                if($params -> get('tz_show_count_comment',1) == 1){
                    require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'HTTPFetcher.php');
                    require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'readfile.php');
                    $fetch       = new Services_Yadis_PlainHTTPFetcher();
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

            $tags   = null;
            if(count($content_ids) && $params -> get('show_tags',1)) {
                $m_tag = JModelLegacy::getInstance('Tag', 'TZ_PortfolioModel', array('ignore_request' => true));
                $m_tag->setState('params',$params);
                $m_tag->setState('article.id', $content_ids);
                $m_tag -> setState('list.ordering','x.contentid');
                $tags   = $m_tag -> getArticleTags();
            }

            //Get Plugins Model
            $pmodel = JModelLegacy::getInstance('Plugins','TZ_PortfolioModel',array('ignore_request' => true));

            foreach($items as $i => &$item){
                if($tags && count($tags) && isset($tags[$item -> id])){
                    $item -> tags   = $tags[$item -> id];
                }

                /*** Start New Source ***/
                $tmpl   = null;
                if($item->params -> get('tz_use_lightbox',1) == 1){
                    $tmpl   = '&tmpl=component';
                }

                //Check redirect to view article
                if($item->params -> get('tz_portfolio_redirect') == 'p_article'){
                    $item ->link         = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid).$tmpl);
                    $item -> fullLink    = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid),true,-1);
                }
                else{
                    $item ->link         = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid).$tmpl);
                    $item -> fullLink = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid),true,-1);
                }
                /*** End New Source ***/

                if($params -> get('comment_function_type','default') != 'js'){

                    if($params -> get('tz_show_count_comment',1) == 1){
                        if($params -> get('tz_comment_type','disqus') == 'disqus' ||
                            $params -> get('tz_comment_type','disqus') == 'facebook'){
                            if($comments){
                                if(array_key_exists($item ->fullLink,$comments)){
                                    $item -> commentCount   = $comments[$item ->fullLink];
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
                    $asset = 'com_content.article.' . $item->id;

                    // Check general edit permission first.
                    if ($user->authorise('core.edit', $asset)) {
                        $item->params->set('access-edit', true);
                    }
                    // Now check if edit.own is available.
                    elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
                        // Check for a valid user and that they are the owner.
                        if ($userId == $item->created_by) {
                            $item->params->set('access-edit', true);
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

                    // Old plugins: Ensure that text property is available
                    if (!isset($item->text))
                    {
                        $item->text = $item->introtext;
                    }

                    //
                    // Process the content plugins.
                    //
                    JPluginHelper::importPlugin('content');

                    $results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.timeline', &$item, &$params, $this -> getState('list.start')));
                    $item->introtext = $item->text;

                    $item->event = new stdClass();
                    $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.timeline', &$item, &$params, $this -> getState('list.start')));
                    $item->event->afterDisplayTitle = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.timeline', &$item, &$params, $this -> getState('list.start')));
                    $item->event->beforeDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.timeline', &$item, &$params, $this -> getState('list.start')));
                    $item->event->afterDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onContentTZPortfolioVote', array('com_tz_portfolio.timeline', &$item, &$params, 0));
                    $item->event->TZPortfolioVote = trim(implode("\n", $results));



                    JPluginHelper::importPlugin('tz_portfolio');
                    $results   = $dispatcher -> trigger('onTZPluginPrepare',array('com_tz_portfolio.timeline', &$item, &$this->params,&$pluginParams,$this -> getState('list.start')));

                    $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.timeline', &$item, &$params,&$pluginParams, $this -> getState('list.start')));
                    $item->event->TZafterDisplayTitle = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.timeline', &$item, &$params,&$pluginParams, $this -> getState('list.start')));
                    $item->event->TZbeforeDisplayContent = trim(implode("\n", $results));

                    $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.timeline', &$item, &$params,&$pluginParams, $this -> getState('list.start')));
                    $item->event->TZafterDisplayContent = trim(implode("\n", $results));
                }

                if($introLimit = $params -> get('tz_article_intro_limit')){
                    $text   = new AutoCutText($item -> introtext,$introLimit);
                    $item -> introtext   = $text -> getIntro();
                }

                $this -> articles[]   = $item -> id;
                $model  = null;

                if(!empty($item -> tagName)){
                    $contentId[]    = $item -> id;
                    if($tagsName = $this -> getTagName($item -> id))
                        $item -> tagName  = $tagsName;
                }

                //Get Catid
                $this -> categories[]   = $item -> catid;

                if($model  = JModelLegacy::getInstance('Media','TZ_PortfolioModel')){
                    if($media  = $model -> getMedia($item -> id)){

                        if($media[0] -> type != 'video' && $media[0] -> type != 'audio'){
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

                if($params -> get('tz_filter_type','tags') == 'tags'){
                    $item -> allTags    = $this -> _getAllTags($item -> created);
                }

            }

            $this -> _getTags($contentId);

            return $items;
        }
        return false;
    }

    function getArticle(){
        return $this -> getItems();
    }

    protected function _getAllTags($date = null){
        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $params = $this -> getState('params');

        $query -> select('t.name');
        $query -> from($db -> quoteName('#__content').' AS c');

        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_tags_xref').' AS x ON c.id=x.contentid');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_tags').' AS t ON t.id=x.tagsid');

        $_catid     = $params -> get('tz_catid');
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

        if($catids){
            $query -> where('c.catid IN('.implode(',',$catids).')');
        }

        if($date){

            if($params -> get('tz_timeline_time_type') == 'month-year'){
                $query -> where('CONCAT_WS(":",YEAR(c.created),MONTH(c.created)) ='.$db -> quote(date('Y:n',strtotime($date))));
            }
            elseif($params -> get('tz_timeline_time_type') == 'month'){
                $query -> where('MONTH(c.created) ='.$db -> quote(date('n',strtotime($date))));
            }
            elseif($params -> get('tz_timeline_time_type') == 'year'){
                $query -> where('YEAR(c.created) ='.$db -> quote(date('Y',strtotime($date))));
            }
        }

        $query -> group('t.id');

        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }

        if($rows = $db -> loadColumn()){
            return implode(' ',array_map(function($value){
                return JApplication::stringURLSafe($value);
            },$rows));
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
                    $item -> tagFilter  = JApplication::stringURLSafe($item -> name);
                    $item -> params      = null;
                    if(isset($item -> attribs) && !empty($item -> attribs)){
                        $item -> params  = new JRegistry($item -> attribs);
                    }
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

    public function getAllTags(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('t.*');
        $query -> from('#__tz_portfolio_tags AS t');
        $query -> join('INNER','#__tz_portfolio_tags_xref AS x ON t.id = x.tagsid');
        $query -> group('t.id');
        $db -> setQuery($query);
        if($rows = $db -> loadObjectList()){
            foreach($rows as $row){
                $row -> name    = trim($row -> name);
                $row -> tagFilter   = JApplication::stringURLSafe($row -> name);
                $row -> params      = null;
                if(isset($row -> attribs) && !empty($row -> attribs)){
                    $row -> params  = new JRegistry($row -> attribs);
                }
            }
            return $rows;
        }
        return null;
    }

    function getParams(){
        if($this -> params)
            return $this -> params;
        return false;
    }

    public function ajaxComments(){
        $model  = JModelLegacy::getInstance('Portfolio','TZ_PortfolioModel',array('ignore_request' => true));
        $model -> setState('params',$this -> params);
        return $model -> ajaxComments();
    }
}