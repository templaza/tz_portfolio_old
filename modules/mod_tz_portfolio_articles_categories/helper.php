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

require_once JPATH_SITE.'/components/com_tz_portfolio/helpers/route.php';
jimport('joomla.application.categories');

abstract class modTZ_PortfolioArticlesCategoriesHelper
{
	public static function getList(&$params)
	{
		$categories = JCategories::getInstance('Content');
		$category = $categories->get($params->get('parent', 'root'));
		$items = $category->getChildren();
		if($params->get('count', 0) > 0 && count($items) > $params->get('count', 0))
		{
			$items = array_slice($items, 0, $params->get('count', 0));
		}

		return $items;
	}

}
