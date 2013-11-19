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

require_once __DIR__ . '/articles.php';

/**
 * Frontpage Component Model.
 */
class TZ_PortfolioModelFeatured extends TZ_PortfolioModelArticles
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_tz_portfolio.frontpage';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);

		// List state information
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		$params = $this->state->params;

        if($params -> get('tz_portfolio_redirect') == 'default'){
            $params -> set('tz_portfolio_redirect','article');
        }

		$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');
		$this->setState('list.limit', $limit);
		$this->setState('list.links', $params->get('num_links'));

		$this->setState('filter.frontpage', true);

		$user		= JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_portfolio'))){
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}
		else {
			$this->setState('filter.published', array(0, 1, 2));
		}

		// check for category selection
		if ($params->get('featured_categories') && implode(',', $params->get('featured_categories'))  == true) {
			$featuredCategories = $params->get('featured_categories');
 			$this->setState('filter.frontpage.categories', $featuredCategories);
 		}

        //Filter by first letter of article's title
        $this -> setState('filter.char',JRequest::getString('char',null));
        $this -> setState('filter.use_filter_first_letter',$params -> get('use_filter_first_letter',1));


	}

	/**
	 * Method to get a list of articles.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
    public function getItems()
    {
        $params = clone $this->getState('params');

        $limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');
        if ($limit > 0)
        {
            $this->setState('list.limit', $limit);

            return parent::getItems();
        }
        return array();

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
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= $this->getState('filter.frontpage');

		return parent::getStoreId($id);
	}

	/**
	 * @return	JDatabaseQuery
	 */
	function getListQuery()
	{
		// Set the blog ordering
		$params = $this->state->params;
		$articleOrderby = $params->get('orderby_sec', 'rdate');
		$articleOrderDate = $params->get('order_date');
		$categoryOrderby = $params->def('orderby_pri', '');
		$secondary = TZ_PortfolioHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
		$primary = TZ_PortfolioHelperQuery::orderbyPrimary($categoryOrderby);

		$orderby = $primary . ' ' . $secondary . ' a.created DESC ';
		$this->setState('list.ordering', $orderby);
		$this->setState('list.direction', '');
		// Create a new query object.
		$query = parent::getListQuery();

		// Filter by frontpage.
		if ($this->getState('filter.frontpage'))
		{
			$query->join('INNER', '#__content_frontpage AS fp ON fp.content_id = a.id');
		}

		// Filter by categories
		if (is_array($featuredCategories = $this->getState('filter.frontpage.categories'))) {
            $featuredCategories = array_filter($featuredCategories);
			$query->where(' a.catid IN (' . implode(',', $featuredCategories) . ')');
		}

		return $query;
	}
}
