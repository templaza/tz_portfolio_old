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

class TZ_PortfolioHelper
{
	public static $extension = 'com_content';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
        $class  = 'JHtmlSidebar';
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $class  = 'JSubMenuHelper';
        }

        call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_GROUP_FIELDS'),
                            'index.php?option=com_tz_portfolio&view=groups',
                            $vName == 'groups'));
        call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_FIELDS'),
                    'index.php?option=com_tz_portfolio&view=fields',
                    $vName == 'fields'));

        call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_CATEGORIES'),
                    'index.php?option=com_tz_portfolio&view=categories',
                    $vName == 'categories'));
        call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_ARTICLES'),
                    'index.php?option=com_tz_portfolio&view=articles',
                    $vName == 'articles'));
        call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_FEATURED_ARTICLES'),
                    'index.php?option=com_tz_portfolio&view=featured',
                    $vName == 'featured'));
        call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_TAGS'),
                    'index.php?option=com_tz_portfolio&view=tags',
                    $vName == 'tags'));
        call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_USERS'),
                    'index.php?option=com_tz_portfolio&view=users',
                    $vName == 'users'));
		call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_TEMPLATE_STYLES'),
			'index.php?option=com_tz_portfolio&view=template_styles',
			$vName == 'template_styles'));
		call_user_func_array($class.'::addEntry',array(JText::_('COM_TZ_PORTFOLIO_SUBMENU_TEMPLATES'),
			'index.php?option=com_tz_portfolio&view=templates',
			$vName == 'templates'));
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @param	int		The article ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = 0, $articleId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($articleId) && empty($categoryId)) {
			$assetName = 'com_tz_portfolio';
		}
		elseif (empty($articleId)) {
			$assetName = 'com_tz_portfolio.category.'.(int) $categoryId;
		}
		else {
			$assetName = 'com_tz_portfolio.article.'.(int) $articleId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	* Applies the content tag filters to arbitrary text as per settings for current user group
	* @param text The string to filter
	* @return string The filtered string
	*/
	public static function filterText($text)
	{
		// Filter settings
		$config		= JComponentHelper::getParams('com_config');
		$user		= JFactory::getUser();
		$userGroups	= JAccess::getGroupsByUser($user->get('id'));

		$filters = $config->get('filters');

		$blackListTags			= array();
		$blackListAttributes	= array();

		$customListTags			= array();
		$customListAttributes	= array();

		$whiteListTags			= array();
		$whiteListAttributes	= array();

		$noHtml				= false;
		$whiteList			= false;
		$blackList			= false;
		$customList			= false;
		$unfiltered			= false;

		// Cycle through each of the user groups the user is in.
		// Remember they are included in the Public group as well.
		foreach ($userGroups as $groupId)
		{
			// May have added a group but not saved the filters.
			if (!isset($filters->$groupId)) {
				continue;
			}

			// Each group the user is in could have different filtering properties.
			$filterData = $filters->$groupId;
			$filterType	= strtoupper($filterData->filter_type);

			if ($filterType == 'NH') {
				// Maximum HTML filtering.
				$noHtml = true;
			}
			elseif ($filterType == 'NONE') {
				// No HTML filtering.
				$unfiltered = true;
			}
			else {
				// Black, white or custom list.
				// Preprocess the tags and attributes.
				$tags			= explode(',', $filterData->filter_tags);
				$attributes		= explode(',', $filterData->filter_attributes);
				$tempTags		= array();
				$tempAttributes	= array();

				foreach ($tags as $tag)
				{
					$tag = trim($tag);

					if ($tag) {
						$tempTags[] = $tag;
					}
				}

				foreach ($attributes as $attribute)
				{
					$attribute = trim($attribute);

					if ($attribute) {
						$tempAttributes[] = $attribute;
					}
				}

				// Collect the black or white list tags and attributes.
				// Each lists is cummulative.
				if ($filterType == 'BL') {
					$blackList				= true;
					$blackListTags			= array_merge($blackListTags, $tempTags);
					$blackListAttributes	= array_merge($blackListAttributes, $tempAttributes);
				}
				elseif ($filterType == 'CBL') {
					// Only set to true if Tags or Attributes were added
					if ($tempTags || $tempAttributes) {
						$customList				= true;
						$customListTags			= array_merge($customListTags, $tempTags);
						$customListAttributes	= array_merge($customListAttributes, $tempAttributes);
					}
				}
				elseif ($filterType == 'WL') {
					$whiteList				= true;
					$whiteListTags			= array_merge($whiteListTags, $tempTags);
					$whiteListAttributes	= array_merge($whiteListAttributes, $tempAttributes);
				}
			}
		}

		// Remove duplicates before processing (because the black list uses both sets of arrays).
		$blackListTags			= array_unique($blackListTags);
		$blackListAttributes	= array_unique($blackListAttributes);
		$customListTags			= array_unique($customListTags);
		$customListAttributes	= array_unique($customListAttributes);
		$whiteListTags			= array_unique($whiteListTags);
		$whiteListAttributes	= array_unique($whiteListAttributes);

		// Unfiltered assumes first priority.
		if ($unfiltered) {
			// Dont apply filtering.
		}
		else {
			// Custom blacklist precedes Default blacklist
			if ($customList) {
				$filter = JFilterInput::getInstance(array(), array(), 1, 1);

				// Override filter's default blacklist tags and attributes
				if ($customListTags) {
					$filter->tagBlacklist = $customListTags;
				}
				if ($customListAttributes) {
					$filter->attrBlacklist = $customListAttributes;
				}
			}
			// Black lists take third precedence.
			elseif ($blackList) {
				// Remove the white-listed attributes from the black-list.
				$filter = JFilterInput::getInstance(
					array_diff($blackListTags, $whiteListTags), 			// blacklisted tags
					array_diff($blackListAttributes, $whiteListAttributes), // blacklisted attributes
					1,														// blacklist tags
					1														// blacklist attributes
				);
				// Remove white listed tags from filter's default blacklist
				if ($whiteListTags) {
					$filter->tagBlacklist = array_diff($filter->tagBlacklist, $whiteListTags);
				}
				// Remove white listed attributes from filter's default blacklist
				if ($whiteListAttributes) {
					$filter->attrBlacklist = array_diff($filter->attrBlacklist);
				}

			}
			// White lists take fourth precedence.
			elseif ($whiteList) {
				$filter	= JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);  // turn off xss auto clean
			}
			// No HTML takes last place.
			else {
				$filter = JFilterInput::getInstance();
			}

			$text = $filter->clean($text, 'html');
		}

		return $text;
	}

	public static function getMenuLinks($menuType = null, $parentId = 0, $mode = 0, $published = array(), $languages = array())
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value, a.title AS text, a.alias, a.level, a.component_id,'
				.' a.menutype, a.type, a.template_style_id, a.checked_out, a.params')
			->from('#__menu AS a')
			->join('LEFT', $db->quoteName('#__menu') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			-> join('LEFT', $db -> quoteName('#__extensions').' AS e ON e.extension_id = a.component_id')
			-> where('e.name='.$db -> quote('com_tz_portfolio'));

		// Filter by the type
		if ($menuType)
		{
			$query->where('(a.menutype = ' . $db->quote($menuType) . ' OR a.parent_id = 0)');
		}

		if ($parentId)
		{
			if ($mode == 2)
			{
				// Prevent the parent and children from showing.
				$query->join('LEFT', '#__menu AS p ON p.id = ' . (int) $parentId)
					->where('(a.lft <= p.lft OR a.rgt >= p.rgt)');
			}
		}

		if (!empty($languages))
		{
			if (is_array($languages))
			{
				$languages = '(' . implode(',', array_map(array($db, 'quote'), $languages)) . ')';
			}

			$query->where('a.language IN ' . $languages);
		}

		if (!empty($published))
		{
			if (is_array($published))
			{
				$published = '(' . implode(',', $published) . ')';
			}

			$query->where('a.published IN ' . $published);
		}

		$query->where('a.published != -2')
			->group('a.id, a.title, a.alias, a.level, a.menutype, a.type, a.template_style_id, a.checked_out, a.lft')
			->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		try
		{
			$links = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());

			return false;
		}

		if (empty($menuType))
		{
			// If the menutype is empty, group the items by menutype.
			$query->clear()
				->select('*')
				->from('#__menu_types')
				->where('menutype <> ' . $db->quote(''))
				->order('title, menutype');
			$db->setQuery($query);

			try
			{
				$menuTypes = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());

				return false;
			}

			// Create a reverse lookup and aggregate the links.
			$rlu = array();

			foreach ($menuTypes as &$type)
			{
				$rlu[$type->menutype] = & $type;
				$type->links = array();
			}

			// Loop through the list of menu links.
			foreach ($links as $i => &$link)
			{
				$registry       = new JRegistry($link -> params);
				$link -> params = $registry;
				if (isset($rlu[$link->menutype]))
				{
					$rlu[$link->menutype]->links[] = &$link;

					// Cleanup garbage.
					unset($link->menutype);
				}
			}

			// Remove all menus group don't have menu items
			if(count($menuTypes)){
				foreach($menuTypes as $i => $item){
					if(!$item -> links || ($item -> links && !count($item -> links))){
						unset($menuTypes[$i]);
					}
				}
			}

			return $menuTypes;
		}
		else
		{
			return $links;
		}
	}
}
