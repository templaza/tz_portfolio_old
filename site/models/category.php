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

jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving a category, the articles associated with the category,
 * sibling, child and parent categories.
 */
class TZ_PortfolioModelCategory extends JModelList
{
	/**
	 * Category items data
	 *
	 * @var array
	 */
	protected $_item = null;

	protected $_articles = null;

	protected $_siblings = null;

	protected $_children = null;

	protected $_parent = null;

	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_tz_portfolio.category';

	/**
	 * The category that applies.
	 *
	 * @access	protected
	 * @var		object
	 */
	protected $_category = null;

	/**
	 * The list of other newfeed categories.
	 *
	 * @access	protected
	 * @var		array
	 */
	protected $_categories = null;

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'modified', 'a.modified',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'author', 'a.author'
			);
		}

		parent::__construct($config);
	}

    function getChildrenImages(){
        if($this -> _children && count($this -> _children)){
            $child_ids  = array();
            foreach($this -> _children as $i => $child){
                $child_ids[]    = $child -> id;
            }
            if(count($child_ids)) {
                $db     = JFactory::getDbo();
                $query  = $db->getQuery(true);

                $query -> select('*');
                $query -> from('#__tz_portfolio_categories');
                $query -> where('catid IN('.implode(',',$child_ids).')');
                $db -> setQuery($query);
                if($child_images = $db -> loadObjectList()){
                    $childImages    = array();
                    foreach($child_images as $item){
                        $childImages[$item -> catid]    = $item;
                    }
                    foreach($this -> _children as $i => &$child){
                        $child -> tz_image  = null;
                        if(count($childImages) && isset($childImages[$child -> id])){
                            $child -> tz_image  = $childImages[$child -> id] -> images;
                        }
                    }
                }
            }
        }
    }

    function getCatImages(){
        $query  = 'SELECT * FROM #__tz_portfolio_categories'
                  .' WHERE catid='.$this -> getState('category.id');
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }
        if($row = $db -> loadObject()){
            return $row;
        }
        return false;
    }

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initiliase variables.
		$app	= JFactory::getApplication('site');
		$pk		= JRequest::getInt('id');

		$this->setState('category.id', $pk);

		// Load the parameters. Merge Global and Menu Item params into new object
		$params = $app->getParams();

        // Set value again for option tz_portfolio_redirect
        if($params -> get('tz_portfolio_redirect') == 'default'){
            $params -> set('tz_portfolio_redirect','article');
        }
//        $params = JComponentHelper::getParams('com_content');

		$menuParams = new JRegistry;

		if ($menu = $app->getMenu()->getActive()) {
			$menuParams->loadString($menu->params);
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		$this->setState('params', $mergedParams);
		$user		= JFactory::getUser();
				// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$groups	= implode(',', $user->getAuthorisedViewLevels());

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

		// process show_noauth parameter

		if (!$params->get('show_noauth')) {
			$this->setState('filter.access', true);
		}
		else {
			$this->setState('filter.access', false);
		}

		// Optional filter text
		$this->setState('list.filter', JRequest::getString('filter-search'));

		// filter.order
		$itemid = JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);
		$orderCol = $app->getUserStateFromRequest('com_tz_portfolio.category.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->getUserStateFromRequest('com_tz_portfolio.category.list.' . $itemid . '.filter_order_Dir',
			'filter_order_Dir', '', 'cmd');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$this->setState('list.start', JRequest::getVar('limitstart', 0, '', 'int'));

		// set limit for query. If list, use parameter. If blog, add blog parameters for limit.
		if ((JRequest::getCmd('layout') == 'blog') || $params->get('layout_type') == 'blog') {
			$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');
			$this->setState('list.links', $params->get('num_links'));
		}
		else {
			$limit = $app->getUserStateFromRequest('com_tz_portfolio.category.list.' . $itemid . '.limit', 'limit', $params->get('display_num'));
		}

		$this->setState('list.limit', $limit);

		// set the depth of the category query based on parameter
		$showSubcategories = $params->get('show_subcategory_content', '0');

		if ($showSubcategories) {
			$this->setState('filter.max_category_levels', $params->get('show_subcategory_content', '1'));
			$this->setState('filter.subcategories', true);
		}

        //Filter by first letter of article's title
        $this -> setState('filter.char',JRequest::getString('char',null));
        $this -> setState('filter.use_filter_first_letter',$params -> get('use_filter_first_letter',1));

		$this->setState('filter.language', $app->getLanguageFilter());

		$this->setState('layout', JRequest::getCmd('layout'));

	}

	/**
	 * Get the articles in the category
	 *
	 * @return	mixed	An array of articles or false if an error occurs.
	 * @since	1.5
	 */
	function getItems()
	{
		$params = $this->getState('params');

		$limit = $this->getState('list.limit');

		if ($this->_articles === null && $category = $this->getCategory()) {
            $this -> setState('catParams',$params);
			$model = JModelLegacy::getInstance('Articles', 'TZ_PortfolioModel', array('ignore_request' => true));
			$model->setState('params',$params);
			$model->setState('filter.category_id', $category->id);
			$model->setState('filter.published', $this->getState('filter.published'));
			$model->setState('filter.access', $this->getState('filter.access'));
			$model->setState('filter.language', $this->getState('filter.language'));
			$model->setState('list.ordering', $this->_buildContentOrderBy());
			$model->setState('list.start', $this->getState('list.start'));
			$model->setState('list.limit', $limit);
			$model->setState('list.direction', $this->getState('list.direction'));
			$model->setState('list.filter', $this->getState('list.filter'));
			// filter.subcategories indicates whether to include articles from subcategories in the list or blog
			$model->setState('filter.subcategories', $this->getState('filter.subcategories'));
			$model->setState('filter.max_category_levels', $this->setState('filter.max_category_levels'));
			$model->setState('list.links', $this->getState('list.links'));

            //Filter by first letter of article's title
            $model -> setState('filter.tz_use_filter_letter',$this -> getState('filter.use_filter_first_letter'));
            $model -> setState('filter.char',$this -> getState('filter.char'));

			if ($limit >= 0) {
				$this->_articles = $model->getItems();

				if ($this->_articles === false) {
					$this->setError($model->getError());
				}
			}
			else {
				$this->_articles=array();
			}


			$this->_pagination = $model->getPagination();
		}

		return $this -> _articles;
	}

	/**
	 * Build the orderby for the query
	 *
	 * @return	string	$orderby portion of query
	 * @since	1.5
	 */
	protected function _buildContentOrderBy()
	{
		$app		= JFactory::getApplication('site');
		$db			= $this->getDbo();
		$params		= $this->state->params;
		$itemid		= JRequest::getInt('id', 0) . ':' . JRequest::getInt('Itemid', 0);
		$orderCol	= $app->getUserStateFromRequest('com_tz_portfolio.category.list.' . $itemid . '.filter_order', 'filter_order', '', 'string');
		$orderDirn	= $app->getUserStateFromRequest('com_tz_portfolio.category.list.' . $itemid . '.filter_order_Dir', 'filter_order_Dir', '', 'cmd');
		$orderby	= ' ';

		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = null;
		}

		if (!in_array(strtoupper($orderDirn), array('ASC', 'DESC', ''))) {
			$orderDirn = 'ASC';
		}

		if ($orderCol && $orderDirn) {
			$orderby .= $db->escape($orderCol) . ' ' . $db->escape($orderDirn) . ', ';
		}

		$articleOrderby		= $params->get('orderby_sec', 'rdate');
		$articleOrderDate	= $params->get('order_date');
		$categoryOrderby	= $params->def('orderby_pri', '');
		$secondary			= TZ_PortfolioHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
		$primary			= TZ_PortfolioHelperQuery::orderbyPrimary($categoryOrderby);

		$orderby .= $db->escape($primary) . ' ' . $db->escape($secondary) . ' a.created ';

		return $orderby;
	}

	public function getPagination()
	{
		if (empty($this->_pagination)) {
			return null;
		}
		return $this->_pagination;
	}

	/**
	 * Method to get category data for the current category
	 *
	 * @param	int		An optional ID
	 *
	 * @return	object
	 * @since	1.5
	 */
	public function getCategory()
	{
		if (!is_object($this->_item)) {
			if( isset( $this->state->params ) ) {
				$params = $this->state->params;
				$options = array();
				$options['countItems'] = $params->get('show_cat_num_articles', 1) || !$params->get('show_empty_categories_cat', 0);
			}
			else {
				$options['countItems'] = 0;
			}

			$categories = JCategories::getInstance('Content', $options);
			$this->_item = $categories->get($this->getState('category.id', 'root'));

			// Compute selected asset permissions.
			if (is_object($this->_item)) {
				$user	= JFactory::getUser();
				$userId	= $user->get('id');
				$asset	= 'com_tz_portfolio.category.'.$this->_item->id;

				// Check general create permission.
				if ($user->authorise('core.create', $asset)) {
					$this->_item->getParams()->set('access-create', true);
				}

				// TODO: Why aren't we lazy loading the children and siblings?
				$this->_children = $this->_item->getChildren();
				$this->_parent = false;

				if ($this->_item->getParent()) {
					$this->_parent = $this->_item->getParent();
				}

				$this->_rightsibling = $this->_item->getSibling();
				$this->_leftsibling = $this->_item->getSibling(false);
			}
			else {
				$this->_children = false;
				$this->_parent = false;
			}
		}
        if($this -> _item) {
            $this->_item -> tz_image = null;
            if($image = $this -> getCatImages()){
                $this -> _item -> tz_image  = $image -> images;
            }
            $this -> getChildrenImages();
        }

		return $this->_item;
	}

	/**
	 * Get the parent categorie.
	 *
	 * @param	int		An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	public function getParent()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_parent;
	}

	/**
	 * Get the left sibling (adjacent) categories.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getLeftSibling()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_leftsibling;
	}

	/**
	 * Get the right sibling (adjacent) categories.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getRightSibling()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		return $this->_rightsibling;
	}

	/**
	 * Get the child categories.
	 *
	 * @param	int		An optional category id. If not supplied, the model state 'category.id' will be used.
	 *
	 * @return	mixed	An array of categories or false if an error occurs.
	 * @since	1.6
	 */
	function &getChildren()
	{
		if (!is_object($this->_item)) {
			$this->getCategory();
		}

		// Order subcategories
		if (sizeof($this->_children)) {
			$params = $this->getState()->get('params');
			if ($params->get('orderby_pri') == 'alpha' || $params->get('orderby_pri') == 'ralpha') {
				jimport('joomla.utilities.arrayhelper');
				JArrayHelper::sortObjects($this->_children, 'title', ($params->get('orderby_pri') == 'alpha') ? 1 : -1);
			}
		}

		return $this->_children;
	}
}
