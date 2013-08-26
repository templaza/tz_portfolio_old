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
jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');
require_once JPATH_SITE.'/components/com_tz_portfolio/router.php';

/**
 * TZ Portfolio Content Search plugin
 */
class plgSearchTZ_Portfolio_Content extends JPlugin
{
    protected static $lookup;
	/**
	 * @return array An array of search areas
	 */
	function onContentSearchAreas()
	{
        JPlugin::loadLanguage('en-GB.plg_search_tz_portfolio_content', JPATH_ADMINISTRATOR);
		static $areas = array(
			'tz_portfolio_content' => 'JGLOBAL_ARTICLES'
			);
			return $areas;
	}

	/**
	 * Content Search method
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if the search it to be restricted to areas, null if search all
	 */
	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$tag = JFactory::getLanguage()->getTag();

		require_once JPATH_SITE . '/components/com_tz_portfolio/helpers/route.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_search/helpers/search.php';

		$searchText = $text;
		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		$sContent		= $this->params->get('search_content',		1);
		$sArchived		= $this->params->get('search_archived',		1);
		$limit			= $this->params->def('search_limit',		50);

		$nullDate		= $db->getNullDate();
		$date = JFactory::getDate();
		$now = $date->toSql();

		$text = trim($text);
		if ($text == '') {
			return array();
		}

		$wheres = array();
		switch ($phrase) {
			case 'exact':
				$text		= $db->Quote('%'.$db->escape($text, true).'%', false);
				$wheres2	= array();
				$wheres2[]	= 'a.title LIKE '.$text;
				$wheres2[]	= 'a.introtext LIKE '.$text;
				$wheres2[]	= 'a.fulltext LIKE '.$text;
				$wheres2[]	= 'a.metakey LIKE '.$text;
				$wheres2[]	= 'a.metadesc LIKE '.$text;
				$where		= '(' . implode(') OR (', $wheres2) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$wheres = array();
				foreach ($words as $word) {
					$word		= $db->Quote('%'.$db->escape($word, true).'%', false);
					$wheres2	= array();
					$wheres2[]	= 'a.title LIKE '.$word;
					$wheres2[]	= 'a.introtext LIKE '.$word;
					$wheres2[]	= 'a.fulltext LIKE '.$word;
					$wheres2[]	= 'a.metakey LIKE '.$word;
					$wheres2[]	= 'a.metadesc LIKE '.$word;
					$wheres[]	= implode(' OR ', $wheres2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		$morder = '';
		switch ($ordering) {
			case 'oldest':
				$order = 'a.created ASC';
				break;

			case 'popular':
				$order = 'a.hits DESC';
				break;

			case 'alpha':
				$order = 'a.title ASC';
				break;

			case 'category':
				$order = 'c.title ASC, a.title ASC';
				$morder = 'a.title ASC';
				break;

			case 'newest':
			default:
				$order = 'a.created DESC';
				break;
		}

		$rows = array();
		$query	= $db->getQuery(true);

		// search articles
		if ($sContent && $limit > 0)
		{
			$query->clear();
			//sqlsrv changes
			$case_when = ' CASE WHEN ';
			$case_when .= $query->charLength('a.alias');
			$case_when .= ' THEN ';
			$a_id = $query->castAsChar('a.id');
			$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
			$case_when .= ' ELSE ';
			$case_when .= $a_id.' END as slug';

			$case_when1 = ' CASE WHEN ';
			$case_when1 .= $query->charLength('c.alias');
			$case_when1 .= ' THEN ';
			$c_id = $query->castAsChar('c.id');
			$case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
			$case_when1 .= ' ELSE ';
			$case_when1 .= $c_id.' END as catslug';

			$query->select('a.title AS title, a.metadesc, a.metakey, a.created AS created');
			$query->select($query->concatenate(array('a.introtext', 'a.fulltext')).' AS text');
			$query->select('c.title AS section, '.$case_when.','.$case_when1.', '.'\'2\' AS browsernav');

			$query->from('#__content AS a');
			$query->innerJoin('#__categories AS c ON c.id=a.catid');
			$query->where('('. $where .')' . 'AND a.state=1 AND c.published = 1 AND a.access IN ('.$groups.') '
						.'AND c.access IN ('.$groups.') '
						.'AND (a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).') '
						.'AND (a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')' );
			$query->group('a.id, a.title, a.metadesc, a.metakey, a.created, a.introtext, a.fulltext, c.title, a.alias, c.alias, c.id');
			$query->order($order);

			// Filter by language
			if ($app->isSite() && $app->getLanguageFilter()) {
				$query->where('a.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
				$query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
			}

			$db->setQuery($query, 0, $limit);
			$list = $db->loadObjectList();
			$limit -= count($list);

			if (isset($list))
			{
				foreach($list as $key => $item)
				{
                    $needles = array(
                        'article'  => array((int) $item -> slug)
                    );
                    $needles['category'] = array($item -> catslug);
                    $itemType   =   $this -> _findType($item -> catslug);
					$list[$key]->href = $itemType ? TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item->slug, $item->catslug) : TZ_PortfolioHelperRoute::getArticleRoute($item->slug, $item->catslug);
				}
			}
			$rows[] = $list;
		}

		// search archived content
		if ($sArchived && $limit > 0)
		{
			$searchArchived = JText::_('JARCHIVED');

			$query->clear();
			//sqlsrv changes
			$case_when = ' CASE WHEN ';
			$case_when .= $query->charLength('a.alias');
			$case_when .= ' THEN ';
			$a_id = $query->castAsChar('a.id');
			$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
			$case_when .= ' ELSE ';
			$case_when .= $a_id.' END as slug';

			$case_when1 = ' CASE WHEN ';
			$case_when1 .= $query->charLength('c.alias');
			$case_when1 .= ' THEN ';
			$c_id = $query->castAsChar('c.id');
			$case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
			$case_when1 .= ' ELSE ';
			$case_when1 .= $c_id.' END as catslug';

			$query->select('a.title AS title, a.metadesc, a.metakey, a.created AS created, '
			.$query->concatenate(array("a.introtext", "a.fulltext")).' AS text,'
			.$case_when.','.$case_when1.', '
			.'c.title AS section, \'2\' AS browsernav');
			//.'CONCAT_WS("/", c.title) AS section, \'2\' AS browsernav' );
			$query->from('#__content AS a');
			$query->innerJoin('#__categories AS c ON c.id=a.catid AND c.access IN ('. $groups .')');
			$query->where('('. $where .') AND a.state = 2 AND c.published = 1 AND a.access IN ('. $groups
				.') AND c.access IN ('. $groups .') '
				.'AND (a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).') '
				.'AND (a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).')' );
			$query->order($order);


			// Filter by language
			if ($app->isSite() && $app->getLanguageFilter()) {
				$query->where('a.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
				$query->where('c.language in (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
			}

			$db->setQuery($query, 0, $limit);
			$list3 = $db->loadObjectList();

			// find an itemid for archived to use if there isn't another one
			$item	= $app->getMenu()->getItems('link', 'index.php?option=com_tz_portfolio&view=archive', true);
			$itemid = isset($item->id) ? '&Itemid='.$item->id : '';

			if (isset($list3))
			{
				foreach($list3 as $key => $item)
				{
					$date = JFactory::getDate($item->created);

					$created_month	= $date->format("n");
					$created_year	= $date->format("Y");

					$list3[$key]->href	= JRoute::_('index.php?option=com_tz_portfolio&view=archive&year='.$created_year.'&month='.$created_month.$itemid);
				}
			}

			$rows[] = $list3;
		}

		$results = array();
		if (count($rows))
		{
			foreach($rows as $row)
			{
				$new_row = array();
				foreach($row as $key => $article) {
					if (searchHelper::checkNoHTML($article, $searchText, array('text', 'title', 'metadesc', 'metakey'))) {
						$new_row[] = $article;
					}
				}
				$results = array_merge($results, (array) $new_row);
			}
		}

		return $results;
	}

    protected static function _findType($cid)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $cid        =   intval($cid);

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
}
