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
 
class TZ_PortfolioModelUsers extends JModelList
{
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

        $user   = JFactory::getUser();

        $pk = JRequest::getInt('created_by');
        $this -> setState('users.id',$pk);

        $offset = JRequest::getUInt('limitstart',0);
        $this -> setState('offset', $offset);

        $this->setState('list.start', JRequest::getVar('limitstart', 0, '', 'int'));

        $app    = JFactory::getApplication('site');
        $params = $app -> getParams();

        // Set value again for option tz_portfolio_redirect
        if($params -> get('tz_portfolio_redirect') == 'default'){
            $params -> set('tz_portfolio_redirect','article');
        }

        if($params -> get('show_limit_box',0)){
            $limit  = $app->getUserStateFromRequest('com_tz_portfolio.users.limit','limit',10);
        }else{
            $limit  = $params -> get('tz_article_limit');
        }

        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this->setState('filter.language', $app->getLanguageFilter());

        $params -> set('access-view',true);

        $this -> setState('params',$params);
        $this -> setState('list.limit',$limit);
        $this -> setState('users.catid',null);
        $this -> setState('char',JRequest::getString('char',null));

    }

    protected function getListQuery(){
        $params = $this -> getState('params');

        $user		= JFactory::getUser();

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('c.*,cc.title AS category_title,cc.parent_id,u.name AS author');
        $query -> select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
        $query -> select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
        $query -> select('CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore');

        $query -> from('#__content AS c');

        $query -> join('LEFT','#__categories AS cc ON cc.id = c.catid');
        $query -> join('LEFT','#__users AS u ON u.id=c.created_by');

         // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state = ' . (int) $published);
        }elseif (is_array($published)) {
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

        $query -> where('c.created_by='.$this -> getState('users.id'));

        if($char   = $this -> getState('char')){
            $query -> where('ASCII(SUBSTR(LOWER(c.title),1,1)) = ASCII("'.mb_strtolower($char).'")');
        }

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

        $query -> order($cateOrder.$orderby);

        // Filter by language
        if ($this->getState('filter.language')) {
            $query->where('c.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
//            $query->where('(contact.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR contact.language IS NULL)');
        }

        return $query;
    }

    public function getItems(){
        if($items   = parent::getItems()){
            $categories = JCategories::getInstance('Content');
            foreach($items as &$item){
                $params     = clone($this -> getState('params'));
                $temp       = clone($this -> getState('params'));

                // Get the global params
                $globalParams = JComponentHelper::getParams('com_tz_portfolio', true);

                /*** New source ***/
                $category   = $categories->get($item -> catid);
                $catParams  = new JRegistry($category -> params);

                if($this -> parameter_merge_fields){
                    foreach($this -> parameter_merge_fields as $value){
                        if($catParams -> get($value) != ''){
                            $params -> set($value,$catParams -> get($value));
                        }
                    }
                }

                $item->params   = clone($params);

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
                                $articleArray[$key] = $params -> get($key);
                            }else{
                                if(!$params -> get($key) || $params -> get($key) == ''){
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

                // Create new options "link" and "fullLink" for article
                $tmpl   = null;
                if($item -> params -> get('tz_use_lightbox',1) == 1){
                    $tmpl   = '&amp;tmpl=component';
                }

                //Check redirect to view article
                if($item -> params -> get('tz_portfolio_redirect') == 'p_article'){
                    $item ->link         = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid).$tmpl);
                    $item -> fullLink    = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catid),true,-1);
                }
                else{
                    $item ->link         = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid).$tmpl);
                    $item -> fullLink = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catid),true,-1);
                }
                /** End Create new options **/
            }
            return $items;
        }
        return false;
    }

    function getUsers(){
        return $this -> getItems();
    }

    function getFindType($_cid=null)
	{
        $cid    = $this -> getState('users.catid');
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $cid        =   intval($cid);
        if($_cid){
            $cid    = intval($_cid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio');
		$items		= $menus->getItems('component_id', $component->id);

        foreach ($items as $item)
        {
            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];

                if (isset($item->query['id'])) {
                    if ($item->query['id'] == $cid) {
                        return 0;
                    }
                } else {

                    $catids = $item->params->get('tz_catid');
                    if ($view == 'portfolio' && $catids) {
                        if (is_array($catids)) {
                            for ($i = 0; $i < count($catids); $i++) {
                                if ($catids[$i] == 0 || $catids[$i] == $cid) {
                                    return 1;
                                }
                            }
                        } else {
                            if ($catids == $cid) {
                                return 1;
                            }
                        }
                    }
                }
            }
        }

		return 0;
	}

    function getFindItemId($_cid=null)
	{
        $cid    = $this -> getState('users.catid');
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $cid        =   intval($cid);
        if($_cid){
            $cid    = intval($_cid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio');
		$items		= $menus->getItems('component_id', $component->id);


        foreach ($items as $item)
        {

            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];


                if (isset($item->query['id'])) {
                    if ($item->query['id'] == $cid) {
                        return $item -> id;
                    }
                } else {

                    $catids = $item->params->get('tz_catid');
                    if ($view == 'portfolio' && $catids) {
                        if (is_array($catids)) {
                            for ($i = 0; $i < count($catids); $i++) {
                                if ($catids[$i] == 0 || $catids[$i] == $cid) {
                                    return $item -> id;
                                }
                            }
                        } else {
                            if ($catids == $cid) {
                                return $item -> id;
                            }
                        }
                    }
                    elseif($view == 'category' && $catids){
                        return $item -> id;
                    }
                }
            }
        }

		return $active -> id;
	}
}