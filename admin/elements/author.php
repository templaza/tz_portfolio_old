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

defined('_JEXEC') or die;

/**
 * Renders an author element.
 */
class JElementAuthor extends JElement
{
	/**
	 * The name of the element.
	 *
	 * @var		string
	 */
	var	$_name = 'Author';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$access	= JFactory::getACL();

		// Include user in groups that have access to edit their articles, other articles, or manage content.
		$action = array('com_tz_portfolio.article.edit_own', 'com_tz_portfolio.article.edit_article', 'com_tz_portfolio.manage');
		$groups	= $access->getAuthorisedUsergroups($action, true);

		// Check the results of the access check.
		if (!$groups) {
			return false;
		}

		// Clean up and serialize.
		JArrayHelper::toInteger($groups);
		$groups = implode(',', $groups);

		// Build the query to get the users.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('u.id AS value');
		$query->select('u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__user_usergroup_map AS m ON m.user_id = u.id');
		$query->where('u.block = 0');
		$query->where('m.group_id IN ('.$groups.')');

		// Get the users.
		$db->setQuery((string) $query);
		$users = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseNotice(500, $db->getErrorMsg());
			return false;
		}

		return JHtml::_('select.genericlist', $users, $name, 'class="inputbox" size="1"', 'value', 'text', $value);
	}
}
