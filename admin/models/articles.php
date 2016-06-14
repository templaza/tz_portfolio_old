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
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of article records.
 */
class TZ_PortfolioModelArticles extends JModelList
{
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
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
                'groupname','g.name'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{

        // List state information.
        parent::populateState('a.id', 'desc');

		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

//        $this-> context = 'com_tz_portfolio.articles';
		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

        $group  = $this -> getUserStateFromRequest($this -> context.'.group','filter_group',0,'int');
        $this -> setState('filter.group',$group);

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$authorId = $app->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
		$this->setState('filter.level', $level);


		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

        // Force a language
        $forcedLanguage = $app->input->get('forcedLanguage');

        if (!empty($forcedLanguage))
        {
            $this->setState('filter.language', $forcedLanguage);
            $this->setState('filter.forcedLanguage', $forcedLanguage);
        }
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.author_id');
		$id	.= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid' .
				', a.state, a.access, a.created, a.created_by, a.ordering, a.featured, a.language, a.hits' .
				', a.publish_up, a.publish_down'
			)
		);


		$query->from('#__content AS a');

        // Join over xref content
        $query -> select('g.name AS groupname,g.id AS groupid');
        if($this -> state -> get('filter.group') != 0){

            $query -> join('LEFT','#__tz_portfolio_xref_content AS xc ON xc.contentid=a.id');
            $query -> join('LEFT','#__tz_portfolio_fields_group AS g ON xc.groupid=g.id');
        }
        else{
            $query -> join('LEFT','#__tz_portfolio_categories AS tc ON tc.catid=a.catid');
            $query -> join('LEFT','#__tz_portfolio_fields_group AS g ON tc.groupid=g.id');
            //$query -> join('LEFT','#__tz_portfolio_xref_content AS xc ON g.id=xc.groupid');
        }

        $query -> select('xc2.type');
        $query -> join('LEFT','#__tz_portfolio_xref_content AS xc2 ON xc2.contentid = a.id');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

         //$query -> join('LEFT','#__tz_portfolio_categories AS tc ON c.id=tc.catid');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            // Join over the associations.
            if (JLanguageAssociations::isEnabled())
            {
                $query->select('COUNT(asso2.id)>1 as association')
                    ->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_content.item'))
                    ->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
                    ->group('a.id');
            }
        }

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		}

        // Filter by fields group
        if($this -> state -> get('filter.group')!=0)
            $query -> where('g.id ='.$this -> getState('filter.group'));

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
		    $groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(a.state = 0 OR a.state = 1)');
		}

		// Filter by a single or group of categories.
		$baselevel = 1;
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where('c.lft >= '.(int) $lft);
			$query->where('c.rgt <= '.(int) $rgt);
		}
		elseif (is_array($categoryId)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN ('.$categoryId.')');
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level')) {
			$query->where('c.level <= '.((int) $level + (int) $baselevel - 1));
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		if (is_numeric($authorId)) {
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
			$query->where('a.created_by '.$type.(int) $authorId);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->escape(substr($search, 7), true).'%');
				$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.title');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'c.title '.$orderDirn.', a.ordering';
		}
		//sqlsrv change
		if($orderCol == 'language')
			$orderCol = 'l.title';
		if($orderCol == 'access_level')
			$orderCol = 'ag.title';
		$query->order($db->escape($orderCol.' '.$orderDirn));

//        var_dump($query); die();

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Build a list of authors
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	public function getAuthors() {
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('u.id AS value, u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__content AS c ON c.created_by = u.id');
		$query->group('u.id, u.name');
		$query->order('u.name');

		// Setup the query
		$db->setQuery($query->__toString());

		// Return the result
		return $db->loadObjectList();
	}

    public function getGroupQuery($catid){
        // Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

        $query -> select('g.*');
        $query -> from('#__tz_portfolio_fields_group AS g');
        $query -> join('LEFT','#__tz_portfolio_categories AS c ON c.groupid = g.id');

        $query -> where('c.catid ='.$catid);
        $query -> order($this -> state -> get('list.groupname','g.name'));
        $query -> group('g.id');

        return $this -> _getList($query);
    }

    function checkGroups($contentid){
        // Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

        $query -> select('xc.*');
        $query -> from('#__content AS c');
        $query -> join('LEFT','#__tz_portfolio_xref_content AS xc ON c.id = xc.contentid');

        $query -> where('c.id ='.$contentid);

        return $this -> _getList($query);
    }

	/**
	 * Method to get a list of articles.
	 * Overridden to add a check for access levels.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6.1
	 */
	public function getItems()
	{


		$items	= parent::getItems();
		$app	= JFactory::getApplication();
        // Get fields group
        $data   = array();

		if ($app->isSite()) {
			$user	= JFactory::getUser();
			$groups	= $user->getAuthorisedViewLevels();

			for ($x = 0, $count = count($items); $x < $count; $x++) {
				//Check the access level. Remove articles the user shouldn't see
				if (!in_array($items[$x]->access, $groups)) {
					unset($items[$x]);
				}
			}
		}

        if($items){
            foreach($items as &$item){
                if(isset($item -> type)){
                    switch (strtolower($item -> type)){
                        default:
                            $item -> type  = JText::_('COM_TZ_PORTFOLIO_OPTION_NONE_MEDIA');
                            break;
                        case 'image':
                            $item -> type  = JText::_('COM_TZ_PORTFOLIO_OPTION_IMAGE');
                            break;
                        case 'imagegallery':
                            $item -> type  = JText::_('COM_TZ_PORTFOLIO_OPTION_IMAGE_GALLERY');
                            break;
                        case 'video':
                            $item -> type  = JText::_('COM_TZ_PORTFOLIO_OPTION_VIDEO');
                            break;
                        case 'audio':
                            $item -> type  = JText::_('COM_TZ_PORTFOLIO_AUDIO');
                            break;
                        case 'quote':
                            $item -> type  = JText::_('COM_TZ_PORTFOLIO_QUOTE');
                            break;
                        case 'link':
                            $item -> type  = JText::_('COM_TZ_PORTFOLIO_LINK');
                            break;
                    }
                }
            }
        }
		return $items;

//        return $data;
	}
}
