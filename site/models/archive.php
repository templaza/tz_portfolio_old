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

require_once dirname(__FILE__) . '/articles.php';

/**
 * Content Component Archive Model.
 */
class TZ_PortfolioModelArchive extends TZ_PortfolioModelArticles
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_tz_portfolio.archive';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState();

		// Add archive properties
		$params = $this->state->params;

		// Filter on archived articles
		$this->setState('filter.published', 2);

		// Filter on month, year
		$this->setState('filter.month', JRequest::getInt('month'));
		$this->setState('filter.year', JRequest::getInt('year'));

		// Optional filter text
		$this->setState('list.filter', JRequest::getString('filter-search'));

		// Get list limit
		$app = JFactory::getApplication();

		$itemid = JRequest::getInt('Itemid', 0);
		$limit = $app->getUserStateFromRequest('com_tz_portfolio.archive.list' . $itemid . '.limit', 'limit', $params->get('display_num'));
		$this->setState('list.limit', $limit);
	}

	/**
	 * @return	JDatabaseQuery
	 */
	function getListQuery()
	{
		// Set the archive ordering
		$params = $this->state->params;
		$articleOrderby = $params->get('orderby_sec', 'rdate');
		$articleOrderDate = $params->get('order_date');

		// No category ordering
		$categoryOrderby = '';
		$secondary = TZ_PortfolioHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
		$primary = TZ_PortfolioHelperQuery::orderbyPrimary($categoryOrderby);

		$orderby = $primary . ' ' . $secondary . ' a.created DESC ';
		$this->setState('list.ordering', $orderby);
		$this->setState('list.direction', '');
		// Create a new query object.
		$query = parent::getListQuery();

			// Add routing for archive
			//sqlsrv changes
		$case_when = ' CASE WHEN ';
	    $case_when .= $query->charLength('a.alias');
	    $case_when .= ' THEN ';
	    $a_id = $query->castAsChar('a.id');
	    $case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
	    $case_when .= ' ELSE ';
	    $case_when .= $a_id.' END as slug';

		$query->select($case_when);

	    $case_when = ' CASE WHEN ';
	    $case_when .= $query->charLength('c.alias');
	    $case_when .= ' THEN ';
	    $c_id = $query->castAsChar('c.id');
	    $case_when .= $query->concatenate(array($c_id, 'c.alias'), ':');
	    $case_when .= ' ELSE ';
	    $case_when .= $c_id.' END as catslug';
	    $query->select($case_when);

		// Filter on month, year
		// First, get the date field
		$queryDate = TZ_PortfolioHelperQuery::getQueryDate($articleOrderDate);

		if ($month = $this->getState('filter.month')) {
			$query->where('MONTH('. $queryDate . ') = ' . $month);
		}

		if ($year = $this->getState('filter.year')) {
			$query->where('YEAR('. $queryDate . ') = ' . $year);
		}

		//echo nl2br(str_replace('#__','jos_',$query));

		return $query;
	}

	/**
	 * Method to get the archived article list
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		$app = JFactory::getApplication();

		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			// Get the page/component configuration
			$params = $app->getParams();

			// Get the pagination request variables
			$limit		= JRequest::getVar('limit', $params->get('display_num', 20), '', 'int');
			$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

			$query = $this->_buildQuery();

			$this->_data = $this->_getList($query, $limitstart, $limit);
		}

		return $this->_data;
	}

	// JModel override to add alternating value for $odd
	protected function _getList($query, $limitstart=0, $limit=0)
	{
		$result = parent::_getList($query, $limitstart, $limit);

		$odd = 1;
		foreach ($result as $k => $row) {
			$result[$k]->odd = $odd;
			$odd = 1 - $odd;
		}

		return $result;
	}
}
