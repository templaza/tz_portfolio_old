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

jimport('joomla.application.categories');

/**
 * Build the route for the com_content component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
function TZ_PortfolioBuildRoute(&$query)
{
	$segments	= array();

	// get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	$params		= JComponentHelper::getParams('com_tz_portfolio');
	$advanced	= $params->get('sef_advanced_link', 0);

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
	}
	else {
		$menuItem = $menu->getItem((int)$query['Itemid']);
		$menuItemGiven = true;
	}

	if (isset($query['view'])) {
		$view = $query['view'];
	}
	else {
		// we need to have a view in the query or it is an invalid URL
		return $segments;
	}

	// are we dealing with an article or category that is attached to a menu item?
	if (($menuItem instanceof stdClass) && $menuItem->query['view'] == $query['view'] && isset($query['id']) && $menuItem->query['id'] == intval($query['id'])) {
        if(isset($query['char'])){
            $segments[] = $query['char'];
            unset($query['char']);
        }
		unset($query['view']);

		if (isset($query['catid'])) {
			unset($query['catid']);
		}

		unset($query['id']);

		return $segments;
	}

	if ($view == 'category' || $view == 'article' || $view == 'portfolio' || $view == 'p_article')
	{
		if (!$menuItemGiven) {
			$segments[] = $view;
		}

		unset($query['view']);

		if ($view == 'article' || $view == 'p_article') {

			if (isset($query['id']) && isset($query['catid']) && $query['catid']) {
				$catid = $query['catid'];
				// Make sure we have the id and the alias
				if (strpos($query['id'], ':') === false) {
					$db = JFactory::getDbo();
					$aquery = $db->setQuery($db->getQuery(true)
						->select('alias')
						->from('#__content')
						->where('id='.(int)$query['id'])
					);
					$alias = $db->loadResult();
					$query['id'] = $query['id'].':'.$alias;
				}
			} else {
				// we should have these two set for this view.  If we don't, it is an error
				return $segments;
			}
		}
		else{
			if (isset($query['id'])) {
				$catid = $query['id'];
			} else {

                if(isset($query['char'])){
                    $segments[] = $query['char'];
                    unset($query['char']);
                }
				// we should have id set for this view.  If we don't, it is an error
				return $segments;
			}
		}

		if ($menuItemGiven) {
			if ( isset($menuItem->query['id']) ) {
				$mCatid = $menuItem->query['id'];
			} else {
				$catids		=	$menuItem->params->get('tz_catid');
				if ($catids) {
					$mCatid = $catids;
				} else {
                    $mCatid =0;
                }
			}

		} else {
			$mCatid = 0;

		}

		$categories = JCategories::getInstance('TZ_Portfolio');
		$category = $categories->get($catid);

		if (!$category) {
			// we couldn't find the category we were given.  Bail.
			return $segments;
		}

		$path = array_reverse($category->getPath()); 

		$array = array();

		foreach($path as $id) {
			if (isset($mCatid) && is_array($mCatid)) {
				$chkCatidk	=	false;
				for ($i=0; $i<count($mCatid); $i++ ) {
					if ( (int)$id == (int)$mCatid[$i] ) {
						$chkCatidk	=	true;
						break;
					}
				}
				if ($chkCatidk) break;
			} elseif ((int)$id == (int)$mCatid) {
				break;
			}

			list($tmp, $id) = explode(':', $id, 2);

			$array[] = $id;
		}

		$array = array_reverse($array);

		if (!$advanced && count($array)) {
            if($view == 'article'){
                $segments[] = 'item';
            }
			$array[0] = (int)$catid.':'.$array[0];
		}

		$segments = array_merge($segments, $array);

		if ($view == 'article' || $view == 'p_article') {
			if ($advanced) {
				list($tmp, $id) = explode(':', $query['id'], 2);
			}
			else {
				$id = $query['id'];
			}
            if($view == 'article'){
                $segments[] = 'item';
            }
			$segments[] = $id;
		}

		unset($query['id']);
		unset($query['catid']);

	}

    if($view == 'tags'){
        // Make sure we have the id and the alias
        if (strpos($query['id'], ':') == false) {
            $db = JFactory::getDbo();
            $aquery = $db->setQuery($db->getQuery(true)
                ->select('name')
                ->from('#__tz_portfolio_tags')
                ->where('id='.(int)$query['id'])
            );
            $alias = JApplication::stringURLSafe($db->loadResult());
            $query['id'] = $query['id'].':'.$alias;
        }

        $segments[] = $view;
        $segments[] = $query['id'];
        
        if(isset($query['char'])){
            $segments[] = $query['char'];
            unset($query['char']);
        }
        
        unset($query['view']);
        unset($query['id']);
    }



    if($view == 'users'){
        $item       = $menu -> getActive();

        if(isset($query['created_by'])){
            $currentId  = $query['created_by'];
        }

        // Make sure we have the id and the name
        if (strpos($query['created_by'], ':') == false) {
            $db = JFactory::getDbo();
            $aquery = $db->setQuery($db->getQuery(true)
                ->select('name')
                ->from('#__users')
                ->where('id='.(int)$query['created_by'])

            );
            $alias  = $db->loadResult();
            $alias  = strtolower($alias);
            $alias  = trim($alias);
            $alias  = str_replace(' ','-',$alias);
            $query['created_by'] = $query['created_by'].':'.$alias;
        }

        if(isset($item -> query)){
            $query2     = $item -> query;

            if(isset($query2['created_by']) && $query2['created_by'] != $currentId){
                $segments[] = $view;
                $segments[] = $query['created_by'];
            }
            elseif(!isset($query2['created_by'])){

                $segments[] = $view;
                $segments[] = $query['created_by'];
            }

            if(isset($query['char'])){
                $segments[] = $query['char'];
                unset($query['char']);
            }
            unset($query['view']);
            unset($query['created_by']);

            return $segments;
        }

        if(isset($query['view'])){
            $segments[] = $view;
            unset($query['view']);
        }

        if(isset($query['created_by'])){
            $segments[]  = $query['created_by'];
            unset($query['created_by']);
        }

        if(isset($query['char'])){
            $segments[] = $query['char'];
            unset($query['char']);
        }

        return $segments;
    }

	if ($view == 'archive') {
		if (!$menuItemGiven) {
			$segments[] = $view;
			unset($query['view']);
		}

		if (isset($query['year'])) {
			if ($menuItemGiven) {
				$segments[] = $query['year'];
				unset($query['year']);
			}
		}

		if (isset($query['year']) && isset($query['month'])) {
			if ($menuItemGiven) {
				$segments[] = $query['month'];
				unset($query['month']);
			}
		}
	}

	// if the layout is specified and it is the same as the layout in the menu item, we
	// unset it so it doesn't go into the query string.
	if (isset($query['layout'])) {
		if ($menuItemGiven && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {

				unset($query['layout']);
			}
		}
		else {
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	}

	return $segments;
}



/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 * @since	1.5
 */
function TZ_PortfolioParseRoute($segments)
{
	$vars = array();

	//Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = JComponentHelper::getParams('com_tz_portfolio');
	$advanced = $params->get('sef_advanced_link', 0);
	$db = JFactory::getDBO();

	// Count route segments
	$count = count($segments);
	// Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
	// the first segment is the view and the last segment is the id of the article or category.
	if (!isset($item)) {
		$vars['view']	= $segments[0];

        if($vars['view'] == 'users'){
            $vars['created_by'] = (int) $segments[1];
            if($count > 2){
                $vars['char'] = $segments[$count - 1];
            }
        }
        else{
            if (isset($segments[1]) && strlen($segments[1]) == 1){
                $vars['char'] = $segments[1];
            }
            $vars['id']		= $segments[$count - 1];
        }

		return $vars;
	}



	// if there is only one segment, then it points to either an article or a category
	// we test it first to see if it is a category.  If the id and alias match a category
	// then we assume it is a category.  If they don't we assume it is an article
	if ($count == 1) {
        if (strlen($segments[0]) == 1) {
            $vars['char'] = $segments[0];
            $vars['view'] = $item->query["view"];
            if(isset($item -> query['id'])){
                $vars['id'] = $item->query["id"];
            }
            if(isset($item -> query['created_by'])){
                $vars['created_by'] = $item->query["created_by"];
            }
            return $vars;
        }
		// we check to see if an alias is given.  If not, we assume it is an article
        //Old
		if (strpos($segments[0], ':') === false) {
			$vars['view'] = 'p_article';
			$vars['id'] = (int)$segments[0];
			return $vars;
		}

		list($id, $alias) = explode(':', $segments[0], 2);

		// first we check if it is a category
		$category = JCategories::getInstance('TZ_Portfolio')->get($id);

		if ($category && $category->alias == $alias) {
			$vars['view'] = 'category';
			$vars['id'] = $id;

			return $vars;
		} else {
			$query = 'SELECT alias, catid FROM #__content WHERE id = '.(int)$id;
			$db->setQuery($query);
			$article = $db->loadObject();

			if ($article) {
				if ($article->alias == $alias) {
					$vars['view'] = 'p_article';
					$vars['catid'] = (int)$article->catid;
					$vars['id'] = (int)$id;

					return $vars;
				}
			}
		}
	}

	// if there was more than one segment, then we can determine where the URL points to
	// because the first segment will have the target category id prepended to it.  If the
	// last segment has a number prepended, it is an article, otherwise, it is a category.

	if (!$advanced) {

        if($segments[0] == 'tags'){
            $vars['view'] = $segments[0];
            if(isset($segments[count($segments) - 2])){
                if(JString::strlen($segments[count($segments) - 2]) == 1){
                    $vars[] = $segments[count($segments) - 2];
                }
            }
            $vars['id'] = (int) $segments[count($segments)-1];
            return $vars;
        }
        if($segments[0] == 'users'){
                $vars['view'] = $segments[0];
                if(count($segments) > 2){
                    $vars['created_by'] = (int) $segments[1];
                    $vars['char'] = (int) $segments[count($segments)-1];
                }else{
                    $vars['created_by'] = (int) $segments[count($segments)-1];
                }
            return $vars;
        }


		$cat_id = (int)$segments[0];
        if($segments[0] == 'item'){
           $cat_id = (int)$segments[1];
        }

		$article_id = (int)$segments[$count - 1];

		if ($article_id > 0) {
			$vars['view'] = 'p_article';
            if($segments[0] == 'item'){
                $vars['view'] = 'article';
            }
			$vars['catid'] = $cat_id;
			$vars['id'] = $article_id;

		} else {
			$vars['view'] = 'category';
			$vars['id'] = $cat_id;
		}



		return $vars;
	}

	// we get the category id from the menu item and search from there
	$id = $item->query['id'];
	$category = JCategories::getInstance('TZ_Portfolio')->get($id);

	if (!$category) {
		JError::raiseError(404, JText::_('COM_CONTENT_ERROR_PARENT_CATEGORY_NOT_FOUND'));
		return $vars;
	}

	$categories = $category->getChildren();
	$vars['catid'] = $id;
	$vars['id'] = $id;
	$found = 0;

	foreach($segments as $segment)
	{
		$segment = str_replace(':', '-', $segment);

		foreach($categories as $category)
		{
			if ($category->alias == $segment) {
				$vars['id'] = $category->id;
				$vars['catid'] = $category->id;
				$vars['view'] = 'category';
				$categories = $category->getChildren();
				$found = 1;
				break;
			}
		}

		if ($found == 0) {
			if ($advanced) {
				$db = JFactory::getDBO();
				$query = 'SELECT id FROM #__content WHERE catid = '.$vars['catid'].' AND alias = '.$db->Quote($segment);
				$db->setQuery($query);
				$cid = $db->loadResult();
			} else {
				$cid = $segment;
			}

			$vars['id'] = $cid;

			if ($item->query['view'] == 'archive' && $count != 1){
				$vars['year']	= $count >= 2 ? $segments[$count-2] : null;
				$vars['month'] = $segments[$count-1];
				$vars['view']	= 'archive';
			}
			else {
				$vars['view'] = 'article';
			}
		}

		$found = 0;
	}

	return $vars;
}
