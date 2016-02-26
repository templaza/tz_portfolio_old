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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'autocuttext.php');

class TZ_PortfolioModelPortfolio extends JModelList
{
    protected $pagNav       = null;
    protected $rowsTag      = null;
    protected $categories   = null;
    public $test            = null;

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
        parent::populateState($ordering,$direction);

        $app    = JFactory::getApplication('site');
        $params = $app -> getParams();

        $global_params    = JComponentHelper::getParams('com_tz_portfolio');

        if($layout_type = $params -> get('layout_type',array())){

            if(!count($layout_type)){
                $params -> set('layout_type',$global_params -> get('layout_type',array()));
            }
        }else{
            $params -> set('layout_type',$global_params -> get('layout_type',array()));
        }

        if($params -> get('tz_portfolio_redirect') == 'default'){
            $params -> set('tz_portfolio_redirect','article');
        }

        $user		= JFactory::getUser();

        $offset = JRequest::getUInt('limitstart',0);

        if($params -> get('show_limit_box',0)  && $params -> get('tz_portfolio_layout') == 'default'){
            $limit  = $app->getUserStateFromRequest('com_tz_portfolio.portfolio.limit','limit',$params -> get('tz_article_limit',10));
        }
        else{
            $limit  = (int) $params -> get('tz_article_limit',10);
        }

        $db		= $this->getDbo();
        $query	= $db->getQuery(true);

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
            // Filter by start and end dates.
            $nullDate = $db->Quote($db->getNullDate());
            $nowDate = $db->Quote(JFactory::getDate()->toSQL());

            $query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
            $query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this->setState('filter.language', $app->getLanguageFilter());

        $this -> setState('params',$params);
        $this -> setState('list.start', $offset);
        $this -> setState('Itemid',$params -> get('id'));
        $this -> setState('list.limit',$limit);
        $this -> setState('tz_catid',$params -> get('tz_catid'));
        $this -> setState('char',JRequest::getString('char',null));
        $this -> setState('filter.tagId',null);
        $this -> setState('filter.userId',null);
        $this -> setState('filter.featured',null);
        $this -> setState('filter.year',null);
        $this -> setState('filter.month',null);
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
        $this -> setState('char',$char);

        $this -> getItems();

        $newTags    = null;
        $tags       = null;

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
        return $tags;
    }

    public function ajax(){

        $list   = null;
        $data   = null;

        $params = JComponentHelper::getParams('com_tz_portfolio');

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

        $limit  = (int) $params -> get('tz_article_limit');

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

        if($offset >= $this -> getTotal()){
            return false;
        }

        return true;
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

        $this -> getItems();

        $newCatids    = null;
        $catIds       = null;

        if($this -> getCategories())
            $newCatids    = $this -> getCategories();

        // Filter new tags
        if(isset($newCatids) && count($newCatids) > 0){
            foreach($newCatids as $key => $newCatid){
                if(isset($curCatids) && count($curCatids) > 0){
                    if(!in_array($newCatid -> id,$curCatids)){
                        $catIds[] = $newCatid;
                    }
                }
            }
        }

        return $catIds;
    }

    function getCategories(){
        $catids = $this -> categories;

        $params = $this -> getState('params');
        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('id,title,lft');
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
            $catid  = array_unique($catid);
            $catid  = array_filter($catid);
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
                    $item -> tagFilter  = JApplication::stringURLSafe(trim($item -> name));
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

    protected function getListQuery(){
        $params = $this -> getState('params');

        $user		= JFactory::getUser();

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('c.*,t.name AS tagName,cc.title AS category_title,u.name AS author');
        $query -> select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
        $query -> select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query -> select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

        $query -> from($db -> quoteName('#__content').' AS c');

        $query -> join('LEFT',$db -> quoteName('#__categories').' AS cc ON cc.id=c.catid');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_tags_xref').' AS x ON x.contentid=c.id');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_tags').' AS t ON t.id=x.tagsid');
        $query -> join('LEFT',$db -> quoteName('#__users').' AS u ON u.id=c.created_by');

        $query -> where('cc.published = 1');

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

        $catids = $params -> get('tz_catid');

        if(is_array($catids)){
            $catids = array_filter($catids);
            if(count($catids)){
                $query -> where('c.catid IN('.implode(',',$catids).')');
            }
        }else{
            $query -> where('c.catid IN('.$catids.')');
        }

        if($char   = $this -> getState('char')){
//            $query -> where('ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII('.$db -> quote(mb_strtolower($char)).')');
            $query -> where('c.title LIKE '.$db -> quote(urldecode(mb_strtolower($char)).'%'));
            $query -> where('ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII('.$db -> quote(mb_strtolower($char)).')');
        }

        // Order by artilce
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
                $orderby    = 'u.name ASC';
                break;
            case 'rauthor':
                $orderby    = 'u.name DESC';
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

        $query -> order($cateOrder.$orderby);

        // Filter by language
        if ($this->getState('filter.language')) {
            $query->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
//            $query->where('(contact.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR contact.language IS NULL)');
        }

        $query -> group('c.id');

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){

            $user	        = JFactory::getUser();
            $userId	        = $user->get('id');
            $guest	        = $user->get('guest');

            $params         = $this -> getState('params');
            $contentId      = array();
            $content_ids    = array();

            $_params        = null;
            $categories     = JCategories::getInstance('Content');

            $threadLink     = null;
            $comments       = null;

            if(count($items)>0){
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
                                if($articleParams -> get($key) != ''){
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

                foreach($items as $key => &$item){
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

                    // Compute the asset access permissions.
                    // Technically guest could edit an article, but lets not check that to improve performance a little.
                    if (!$guest) {
                        $asset	= 'com_tz_portfolio.article.'.$item->id;

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

                        $results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('list.start')));
                        $item->introtext = $item->text;

                        $item->event = new stdClass();
                        $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('list.start')));
                        $item->event->afterDisplayTitle = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('list.start')));
                        $item->event->beforeDisplayContent = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.portfolio', &$item, &$params, $this -> getState('list.start')));
                        $item->event->afterDisplayContent = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onContentTZPortfolioVote', array('com_tz_portfolio.portfolio', &$item, &$params, 0));
                        $item->event->TZPortfolioVote = trim(implode("\n", $results));

                        JPluginHelper::importPlugin('tz_portfolio');
                        $results   = $dispatcher -> trigger('onTZPluginPrepare',array('com_tz_portfolio.portfolio', &$item, &$this->params,&$pluginParams,$this -> getState('list.start')));

                        $results = $dispatcher->trigger('onTZPluginAfterTitle', array('com_tz_portfolio.portfolio', &$item, &$params,&$pluginParams, $this -> getState('list.start')));
                        $item->event->TZafterDisplayTitle = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('com_tz_portfolio.portfolio', &$item, &$params,&$pluginParams, $this -> getState('list.start')));
                        $item->event->TZbeforeDisplayContent = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('com_tz_portfolio.portfolio', &$item, &$params,&$pluginParams, $this -> getState('list.start')));
                        $item->event->TZafterDisplayContent = trim(implode("\n", $results));
                    }

                    if($introLimit = $params -> get('tz_article_intro_limit')){
                        $text   = new AutoCutText($item -> introtext,$introLimit);
                        $item -> introtext   = $text -> getIntro();
                    }

                    if(!empty($item -> tagName)){
                        $contentId[]    = $item -> id;
                        if($tagsName = $this -> getTagName($item -> id))
                            $item -> tagName  = $tagsName;
                        $data[$key] = $item;
                    }

                    if(!isset($item -> tz_image))
                        $item -> tz_image = '';

//                    $item -> attribs    = $itemParams -> toString();

                    //Get Catid
                    $this -> categories[]   = (int) $item -> catid;

                }
                $this -> _Tags($contentId);

                return $items;
            }
        }
        return false;
    }

    function getArticle(){
        return $this -> getItems();
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

//    function getPagination(){
//        if($this -> pagNav)
//            return $this -> pagNav;
//        return false;
//    }

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