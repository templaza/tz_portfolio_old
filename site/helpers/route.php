<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Content Component Route Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
abstract class TZ_PortfolioHelperRoute
{
	protected static $lookup;

	/**
	 * @param	int	The route of the content item
	 */
	public static function getArticleRoute($id, $catid = 0)
	{
        $needles = array(
            'article'  => array((int) $id)
        );
        
		//Create the link
		$link = 'index.php?option=com_tz_portfolio&amp;view=article&amp;id='. $id;
		if ((int)$catid > 1)
		{
			$categories = JCategories::getInstance('TZ_Portfolio');
			$category = $categories->get((int)$catid);
			if($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$needles['portfolio'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		} 

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

    /**
	 * @param	int	The route of the content item
	 */
	public static function getPortfolioArticleRoute($id, $catid = 0)
	{
        $needles = array(
            'p_article'  => array((int) $id)
        );

		//Create the link
		$link = 'index.php?option=com_tz_portfolio&amp;view=p_article&amp;id='. $id;
		if ((int)$catid > 1)
		{
			$categories = JCategories::getInstance('TZ_Portfolio');
			$category = $categories->get((int)$catid);
			if($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$needles['portfolio'] = $needles['category'];
				$link .= '&amp;catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&amp;Itemid='.$item;
		}
		elseif ($item = self::_findItem()) {
			$link .= '&amp;Itemid='.$item;
		}

		return $link;
	}

	public static function getCategoryRoute($catid)
	{
		if ($catid instanceof JCategoryNode)
		{
			$id = $catid->id;
			$category = $catid;
		}
		else
		{
			$id = (int) $catid;
			$category = JCategories::getInstance('Content')->get($id);
		}
        
		if($id < 1)
		{
			$link = '';
		}
		else
		{
			$needles = array(
				'category' => array($id)
			);

			if ($item = self::_findItem($needles))
			{
				$link = 'index.php?Itemid='.$item;
			}
			else
			{
				//Create the link
				$link = 'index.php?option=com_tz_portfolio&amp;view=category&amp;id='.$id;
				if($category)
				{
					$catids = array_reverse($category->getPath());
					$needles = array(
						'category' => $catids,
						'categories' => $catids
					);

					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					}
					elseif ($item = self::_findItem()) {
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
    }

	public static function getFormRoute($id)
	{
		//Create the link
		if ($id) {
			$link = 'index.php?option=com_tz_portfolio&amp;task=article.edit&amp;a_id='. $id;
		} else {
			$link = 'index.php?option=com_tz_portfolio&amp;task=article.edit&amp;a_id=0';
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $active     = $menus->getActive();		

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_tz_portfolio');
			$items		= $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];

					if (!isset(self::$lookup[$view])) {
						self::$lookup[$view] = array();
					}
					if (isset($item->query['id'])) {
						self::$lookup[$view][$item->query['id']] = $item->id;
					} else {
						$catids		=	$item->params->get('tz_catid');
						if ($catids) {
							if (is_array($catids)) {
								for ($i =0; $i<count($catids); $i++){
									self::$lookup[$view][$catids[$i]] = $item->id;
								}
							} else {
								self::$lookup[$view][$catids] = $item->id;
							}
						}
					}

                    if ($active && $active->component == 'com_tz_portfolio') {
                        if (isset($active->query) && isset($active->query['view'])){

                            if (isset($active->query['id'])) {
                                self::$lookup[$active->query['view']][$active->query['id']] = $active->id;
                            }
                        }
                    }
				}
			}
		}

		if ($needles)
		{			
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}
		else
		{
			if ($active && $active->component == 'com_tz_portfolio') {
				return $active->id;
			}
		}

		return null;
	}
}
