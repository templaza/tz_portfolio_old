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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Categories Component Categories Model
 */
class TZ_PortfolioModelCategories extends JModelList
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
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'created_time', 'a.created_time',
				'created_user_id', 'a.created_user_id',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'level', 'a.level',
				'path', 'a.path',
                'groupname','fg.name'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{

        // List state information.
        parent::populateState('a.lft', 'asc');

		// Initialise variables.
		$app		= JFactory::getApplication();
		$context	= $this->context;

		$extension = $app->getUserStateFromRequest('com_tz_portfolio.categories.filter.extension', 'extension', 'com_content', 'cmd');

		$this->setState('filter.extension', $extension);
		$parts = explode('.', $extension);

		// extract the component name
		$this->setState('filter.component', $parts[0]);

		// extract the optional section name
		$this->setState('filter.section', (count($parts) > 1) ? $parts[1] : null);

        $group  = $this -> getUserStateFromRequest($context.'.group','filter_group',0,'int');
        $this -> setState('filter.group',$group);

		$search = $this->getUserStateFromRequest($context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$level = $this->getUserStateFromRequest($context.'.filter.level', 'filter_level', 0, 'int');
		$this->setState('filter.level', $level);

		$access = $this->getUserStateFromRequest($context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$language = $this->getUserStateFromRequest($context.'.filter.language', 'filter_language', '');
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
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.language');

		return parent::getStoreId($id);
	}

    public function getGroups($default=null){

        $catid          = $this -> getState('filter.group');
        $groups    = array();

        $dbo            = JFactory::getDbo();

        $query          = 'SELECT * FROM #__tz_portfolio_fields_group';
        $dbo -> setQuery($query);

        if(!$dbo -> query()){
            var_dump($dbo -> getErrorMsg());
            return false;
        }

        if($rows2 = $dbo -> loadObjectList()){

            $groups[]  = JText::_('COM_TZ_PORTFOLIO_OPTION_INHERIT_CATEGORY');
            foreach($rows2 as $row){
                $groups[$row -> id]    = $row -> name;
            }
        }


        return $groups;
    }

	/**
	 * @return	string
	 * @since	1.6
	 */
	function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.note, a.published, a.access' .
				', a.checked_out, a.checked_out_time, a.created_user_id' .
				', a.path, a.parent_id, a.level, a.lft, a.rgt' .
				', a.language'
			)
		);
		$query->from('#__categories AS a');

        // Join over the fields group
        $query -> select('fg.name AS groupname,fg.id AS groupid');
        $query -> join('LEFT','#__tz_portfolio_categories AS c ON c.catid = a.id');
        $query -> join('LEFT','#__tz_portfolio_fields_group AS fg ON c.groupid = fg.id');
        
		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_user_id');

        // Filter by fields group
        if($this -> getState('filter.group'))
            $query -> where('c.groupid ='.$this -> getState('filter.group'));
        
		// Filter by extension
		if ($extension = $this->getState('filter.extension')) {
			$query->where('a.extension = '.$db->quote($extension));
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level')) {
			$query->where('a.level <= '.(int) $level);
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
		    $groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by search in title
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
				$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.' OR a.note LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}

		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'a.lft');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));
		if ($listOrdering == 'a.access') {
			$query->order('a.access '.$listDirn.', a.lft '.$listDirn);
		} else {
			$query->order($db->escape($listOrdering).' '.$listDirn);
		}

        // Group by
        $query -> group('a.id');

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

    public function getGroupQuery($catid){
        // Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

        $query -> select('fg.*');
        $query -> from('#__tz_portfolio_fields_group AS fg');
        $query -> join('LEFT','#__tz_portfolio_categories AS c ON c.groupid = fg.id');
        $query -> where('c.catid ='.$catid);
        $query -> group('fg.id');

        return $this -> _getList($query);
    }

    /**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   11.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();
		$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

        // Check categories with fields group
        foreach($items as $item){

            $groupName  = array();

            if($groups = $this -> getGroupQuery($item -> id)){
                foreach($groups as $group){
                    $groupName[]    = $group -> name;
                }
            }
            $groupName  = implode(', ',$groupName);
            $item -> groupname = $groupName;
        }

		// Add the items to the internal cache.
        $this->cache[$store] = $items;

		return $this->cache[$store];
	}
}
