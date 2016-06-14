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

$params		= JComponentHelper::getParams('com_tz_portfolio');

/**
 * Build the route for the com_tz_portfolio component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
if($params -> get('tzSef',1)) {
    function TZ_PortfolioBuildRoute(&$query)
    {
        $segments = array();

        // get a menu item based on Itemid or currently active
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $params = JComponentHelper::getParams('com_tz_portfolio');
        $advanced = $params->get('sef_advanced_link', 0);

        // we need a menu item.  Either the one specified in the query, or the current active one if none specified
        if (empty($query['Itemid'])) {
            $menuItem = $menu->getActive();
            $menuItemGiven = false;
        } else {
            $menuItem = $menu->getItem((int)$query['Itemid']);
            $menuItemGiven = true;
        }

        if (isset($query['view'])) {
            $view = $query['view'];
        } else {
            // we need to have a view in the query or it is an invalid URL
            return $segments;
        }

        // are we dealing with an article or category that is attached to a menu item?
        if (($menuItem instanceof stdClass && isset($menuItem->query['view'])) && $menuItem->query['view'] == $query['view'] && isset($query['id']) && isset($menuItem->query['id']) && $menuItem->query['id'] == intval($query['id'])) {
//            if (isset($query['char'])) {
//                $segments[] = $query['char'];
//                unset($query['char']);
//            }
            unset($query['view']);

            if (isset($query['catid'])) {
                unset($query['catid']);
            }

            unset($query['id']);

            return $segments;
        }

        if ($view == 'category' || $view == 'article' || $view == 'portfolio'
            || $view == 'timeline' || $view == 'p_article') {
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
                            ->where('id=' . (int)$query['id'])
                        );
                        $alias = $db->loadResult();
                        $query['id'] = $query['id'] . ':' . $alias;
                    }
                } else {
                    // we should have these two set for this view.  If we don't, it is an error
                    return $segments;
                }
            } else {
                if (isset($query['id'])) {
                    $catid = $query['id'];
                } else {

//                    if (isset($query['char'])) {
//                        $segments[] = $query['char'];
//                        unset($query['char']);
//                    }
                    // we should have id set for this view.  If we don't, it is an error
                    return $segments;
                }
            }

            if ($menuItemGiven) {
                if (isset($menuItem->query['id'])) {
                    $mCatid = $menuItem->query['id'];
                } else {
                    $catids = $menuItem->params->get('tz_catid');
                    if ($catids) {
                        $mCatid = $catids;
                    } else {
                        $mCatid = 0;
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

            $array = array();

            if($params -> get('sef_use_parent_category',1)) {
                $path = array_reverse($category->getPath());

                foreach ($path as $id) {
                    if (isset($mCatid) && is_array($mCatid)) {
                        $chkCatidk = false;
                        for ($i = 0; $i < count($mCatid); $i++) {
                            if ((int)$id == (int)$mCatid[$i]) {
                                $chkCatidk = true;
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
            }else{
                $array[]  = $category -> alias;
            }

            $opView = 'p_article';
            if ($params->get('view_router_switch', 1) == 2) {
                $opView = 'article';
            }
            if ($view == $opView) {
                $segments[] = $params->get('view_router_name', 'item');
            }

            if (!$advanced && count($array)) {

                $array[0] = (int)$catid . ':' . $array[0];
            }
            $segments = array_merge($segments, $array);


            if ($view == 'article' || $view == 'p_article') {

                $sefArticleSep  = $params -> get('sef_article_separator','slash_revert_id');

                list($id, $alias) = explode(':', $query['id'], 2);
//                if ($advanced) {
//                    list($alias, $id) = explode(':', $query['id'], 2);
//                } else {
//                    if($sefArticleSep == 'slash' || $sefArticleSep == 'slash_revert_id') {
//                        list($id, $alias) = explode(':', $query['id'], 2);
//                    }elseif($sefArticleSep == 'dash'){
//                        $id = $query['id'];
//                    }
//                }

                if($sefArticleSep == 'slash') {
                    if($params -> get('sef_use_article_id',1)){
                        $segments[] = $id;
                    }
                    if($params -> get('sef_use_article_alias',1)
                        || (!$params -> get('sef_use_article_id',1) && !$params -> get('sef_use_article_alias',1))) {
                        $segments[] = $alias;
                    }
                }elseif($sefArticleSep == 'slash_revert_id'){
                    if($params -> get('sef_use_article_alias',1)
                        || (!$params -> get('sef_use_article_id',1) && !$params -> get('sef_use_article_alias',1))) {
                        $segments[] = $alias;
                    }
                    if($params -> get('sef_use_article_id',1)) {
                        $segments[] = $id;
                    }
                }else{
                    if($params -> get('sef_use_article_id',1) && $params -> get('sef_use_article_alias',1)){
                        $segments[] = $id.':'.$alias;
                    }elseif($params -> get('sef_use_article_id',1)) {
                        $segments[] = $id;
                    }elseif($params -> get('sef_use_article_alias',1)
                        || (!$params -> get('sef_use_article_id',1) && !$params -> get('sef_use_article_alias',1))){
                        $segments[] = $alias;
                    }
                }
            }

            unset($query['id']);
            unset($query['catid']);

        }

        if ($view == 'tags') {
            $segments[] = $params -> get('sef_tags_prefix','tags');

            // Make sure we have the id and the alias
            if (strpos($query['id'], ':') == false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('name')
                    ->from('#__tz_portfolio_tags')
                    ->where('id=' . (int)$query['id'])
                );

                $alias  = null;
                if($params -> get('sef_use_tag_alias',1)) {
                    if(!$alias = JApplication::stringURLSafe($db->loadResult())){
                        $alias  = null;
                    }
                }
                if($params -> get('sef_tag_separator','slash_revert_id') == 'slash_revert_id'){
                    if($alias && !empty($alias)) {
                        $segments[] = $alias;
                    }
                    $segments[] = $query['id'];

                }elseif($params -> get('sef_tag_separator','slash_revert_id') == 'slash'){
                    $segments[] = $query['id'];
                    if($alias && !empty($alias)) {
                        $segments[] = $alias;
                    }
                }else{
                    $segments[] = (int)$query['id'].(($alias && !empty($alias))?':'.$alias:'');
                }
            }else{
                $segments[] = $query['id'];
            }

//            if (isset($query['char'])) {
//                $segments[] = $query['char'];
//                unset($query['char']);
//            }

            unset($query['view']);
            unset($query['id']);
        }

        if ($view == 'users') {
            $item = $menu->getActive();

            if (isset($query['created_by'])) {
                $currentId = $query['created_by'];
            }

            $userMenuItemGiven = false;
            if (isset($menuItem)) {
                if (isset($menuItem->query)) {
                    $query2 = $menuItem->query;
                    if (isset($query2['created_by'])) {
                        $userMenuItemGiven = true;
                    }
                }
            }

            if (!$userMenuItemGiven) {
                if (isset($query['view'])) {
                    $segments[] = $params -> get('sef_users_prefix','users');
                    unset($query['view']);
                }


                if (isset($query['created_by'])) {
                    // Make sure we have the id and the name
                    if (strpos($query['created_by'], ':') == false) {
                        $db = JFactory::getDbo();
                        $aquery = $db->setQuery($db->getQuery(true)
                            ->select('name')
                            ->from('#__users')
                            ->where('id=' . (int)$query['created_by'])

                        );

                        $alias  = null;
                        if($params -> get('sef_use_user_alias',1)) {
                            $alias = JApplication::stringURLSafe($db->loadResult());
                        }
                        if($params -> get('sef_user_separator','slash_revert_id') == 'slash_revert_id'){
                            if($alias && !empty($alias)) {
                                $segments[] = $alias;
                            }
                            $segments[] = $query['created_by'];

                        }elseif($params -> get('sef_user_separator','slash_revert_id') == 'slash'){
                            $segments[] = $query['created_by'];
                            if($alias && !empty($alias)) {
                                $segments[] = $alias;
                            }
                        }else{
                            $segments[] = (int)$query['created_by'].(($alias && !empty($alias))?':'.$alias:'');
                        }
                    }else{
                        $segments[] = $query['created_by'];
                    }
                    unset($query['created_by']);
                }

//                if (isset($query['char'])) {
//                    $segments[] = $query['char'];
//                    unset($query['char']);
//                }

                return $segments;
            }

//            if (isset($query['char'])) {
//                $segments[] = $query['char'];
//                unset($query['char']);
//            }

            unset($query['view']);
            unset($query['created_by']);

            return $segments;
        }

        if ($view == 'archive' || $view == 'date') {

            if (!$menuItemGiven) {
                $segments[] = $view;
                unset($query['view']);
            }

            if ($view == 'date' && isset($query['view'])) {
                $segments[] = $params -> get('sef_date_prefix','date');
                unset($query['view']);
            }

            $bool = false;
            if (isset($query['year']) && isset($query['month'])) {
                $bool = true;
            }

            if (isset($query['year'])) {
                if ($menuItemGiven) {
                    $segments[] = $query['year'];
                    unset($query['year']);
                }
            }

            if ($bool) {
                if ($menuItemGiven) {
                    $segments[] = $query['month'];
                    unset($query['month']);
                }
            }

//            if (isset($query['char'])) {
//                $segments[] = $query['char'];
//                unset($query['char']);
//            }

        }

        // if the layout is specified and it is the same as the layout in the menu item, we
        // unset it so it doesn't go into the query string.
        if (isset($query['layout'])) {
            if ($menuItemGiven && isset($menuItem->query['layout'])) {
                if ($query['layout'] == $menuItem->query['layout']) {

                    unset($query['layout']);
                }
            } else {
                if ($query['layout'] == 'default') {
                    unset($query['layout']);
                }
            }
        }

//        if (isset($query['char'])) {
//            $segments[] = $query['char'];
//            unset($query['char']);
//        }

        return $segments;
    }


    /**
     * Parse the segments of a URL.
     *
     * @param    array    The segments of the URL to parse.
     *
     * @return    array    The URL attributes to be used by the application.
     * @since    1.5
     */
    function TZ_PortfolioParseRoute($segments)
    {
        $vars = array();

        //Get the active menu item.
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $item = $menu->getActive();
        $params = JComponentHelper::getParams('com_tz_portfolio');
        $advanced = $params->get('sef_advanced_link', 0);
        $db = JFactory::getDBO();

        // Count route segments
        $count = count($segments);
        // Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
        // the first segment is the view and the last segment is the id of the article or category.
        if (!isset($item)) {
            $vars['view'] = $segments[0];

            if ($vars['view'] == $params -> get('sef_users_prefix','users')) {
                $vars['view']   = 'users';
                if (!is_numeric($segments[$count - 1])) {
                    $vars['created_by'] = (int)$segments[$count - 1];
                }
//                elseif (strlen($segments[$count - 1]) == 1) {
//                    $vars['char'] = $segments[$count - 1];
//                }
            } else {
//                if (isset($segments[1]) && strlen($segments[1]) == 1) {
//                    $vars['char'] = $segments[1];
//                }

                $sefArticleSep  = $params -> get('sef_article_separator','slash_revert_id');
                $alias          = null;
                if($sefArticleSep == 'slash') {
                    if($params -> get('sef_use_article_id',1)){
                        $vars['id'] = $segments[$count - 2];
                    }else {
                        if ($params->get('sef_use_article_alias', 1)
                            || (!$params->get('sef_use_article_id', 1) && !$params->get('sef_use_article_alias', 1))
                        ) {
                            $alias = $segments[$count - 1];
                        }
                    }
                }elseif($sefArticleSep == 'slash_revert_id'){
                    if($params -> get('sef_use_article_id',1)) {
                        $vars['id'] = $segments[$count - 1];
                    }else{
                        if($params -> get('sef_use_article_alias',1)
                            || (!$params -> get('sef_use_article_id',1) && !$params -> get('sef_use_article_alias',1))) {
                            $alias  = $segments[$count - 2];
                        }
                    }
                }else{
                    if($params -> get('sef_use_article_id',1)) {
                        $vars['id'] = $segments[$count - 1];
                    }
                }

//                $vars['id'] = $segments[$count - 1];
            }

            return $vars;
        }

        // if there is only one segment, then it points to either an article or a category
        // we test it first to see if it is a category.  If the id and alias match a category
        // then we assume it is a category.  If they don't we assume it is an article
        if ($count == 1) {
            if (strlen($segments[0]) == 1) {
//                $vars['char'] = $segments[0];
                $vars['view'] = $item->query["view"];
                if (isset($item->query['id'])) {
                    $vars['id'] = $item->query["id"];
                }
                if (isset($item->query['created_by'])) {
                    $vars['created_by'] = $item->query["created_by"];
                }
                return $vars;
            }
            // we check to see if an alias is given.  If not, we assume it is an article
            //Old
            if (strpos($segments[0], ':') === false) {
                $vars['view'] = 'article';
                $vars['id'] = (int)$segments[0];
                if($params -> get('sef_use_article_alias',1)
                    || (!$params -> get('sef_use_article_id',1) && !$params -> get('sef_use_article_alias',1))) {
                    $alias  = $segments[0];
                    $query = 'SELECT id FROM #__content WHERE alias=' .$db -> quote($alias).'';
                    $db -> setQuery($query);
                    if($id = $db -> loadResult()) {
                        $vars['id'] = $id;
                    }
                }
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
                $query = 'SELECT alias, catid FROM #__content WHERE id = ' . (int)$id;
                $db->setQuery($query);
                $article = $db->loadObject();

                if ($article) {
                    if ($article->alias == $alias) {
                        $vars['view'] = 'article';
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

            if ($segments[0] == $params -> get('sef_tags_prefix','tags')) {
                $vars['view'] = 'tags';
//                if (isset($segments[count($segments) - 1])) {
//                    if (!is_numeric($segments[count($segments) - 1])) {
//                        if (JString::strlen($segments[count($segments) - 1]) == 1) {
//                            $vars['char'] = $segments[count($segments) - 1];
//                        }
//                        if (isset($segments[count($segments) - 2])) {
//                            if (is_numeric($segments[count($segments) - 2])) {
//                                $vars['id'] = $segments[count($segments) - 2];
//                            }
//                        }
//                    } else {
//                        $vars['id'] = (int)$segments[count($segments) - 1];
//                    }
//                } else {
//                    $vars['id'] = (int)$segments[count($segments) - 1];
//                }
                if($params -> get('sef_tag_separator','slash_revert_id') == 'slash_revert_id'
                    || $params -> get('sef_tag_separator','slash_revert_id') == 'dash'){
                    $vars['id'] = (int) $segments[count($segments) - 1];
                }else{
                    if($params -> get('sef_use_tag_alias',1)){
                        $vars['id'] = (int) $segments[count($segments) - 2];
                    }else{
                        $vars['id'] = (int) $segments[count($segments) - 1];
                    }
                }

                return $vars;
            }
            if ($segments[0] == $params -> get('sef_users_prefix','users')) {
                $vars['view'] = 'users';
//                if (is_numeric($segments[count($segments) - 1])) {
//                    $vars['created_by'] = $segments[count($segments) - 1];
//                } else {
//                    $vars['created_by'] = (int)$segments[count($segments) - 2];
//                    $vars['char'] = $segments[count($segments) - 1];
//                }

                if($params -> get('sef_user_separator','slash_revert_id') == 'slash_revert_id'
                    || $params -> get('sef_user_separator','slash_revert_id') == 'dash'){
                    $vars['created_by'] = (int) $segments[count($segments) - 1];
                }else{
                    if($params -> get('sef_use_user_alias',1)){
                        $vars['created_by'] = (int) $segments[count($segments) - 2];
                    }else{
                        $vars['created_by'] = (int) $segments[count($segments) - 1];
                    }
                }
                return $vars;
            }

            if ($segments[0] == $params -> get('sef_date_prefix','date')) {
                $vars['view'] = 'date';
                if (count($segments) > 1) {
                    if (count($segments) > 2) {
                        $vars['year'] = $segments[1];
                        $vars['month'] = $segments[2];
                    }
//                    if ((isset($vars['year']) && isset($vars['month']) && count($segments) > 3) || (count($segments) < 3))
//                        $vars['char'] = $segments[count($segments) - 1];
                }
                return $vars;
            }

            $temp = $item->params;
            $menuParams = clone($params);
            $menuParams->merge($temp);

            $cat_id = (int)$segments[0];
            if ($segments[0] == $menuParams->get('view_router_name', 'item')) {
                $cat_id = (int)$segments[1];
            }

            $sefArticleSep  = $params -> get('sef_article_separator','slash_revert_id');

            if($sefArticleSep == 'dash' || $sefArticleSep == 'slash_revert_id') {
                $article_id = (int)$segments[$count - 1];
            }else{
                $article_id = (int)$segments[$count - 2];
            }


            if((!$params -> get('sef_use_article_id',1) && $params -> get('sef_use_article_alias',1))
                || (!$params -> get('sef_use_article_id',1) && !$params -> get('sef_use_article_alias',1))) {
                $article_id = (int)$segments[$count - 1];

                $alias = $segments[$count - 1];
                $alias = str_replace(':', '-', $alias);

                $query = 'SELECT id FROM #__content WHERE catid = ' . $cat_id . ' AND alias = ' . $db->Quote($alias);
                $db->setQuery($query);
                if ($_cid = $db->loadResult()) {
                    $article_id = (int)$_cid;
                }
            }

            if ($article_id > 0) {
                $vars['view'] = 'article';

                if ($menuParams->get('view_router_switch', 1) == 2) {
                    $vars['view'] = 'p_article';
                }

                if ($segments[0] == $menuParams->get('view_router_name', 'item')) {
                    $vars['view'] = 'p_article';

                    if ($menuParams->get('view_router_switch', 1) == 2) {
                        $vars['view'] = 'article';
                    }
                }
                $vars['catid'] = $cat_id;
                $vars['id'] = $article_id;

            } else {
                $vars['view'] = 'category';
                $vars['id'] = $cat_id;
            }
//            if (isset($segments[$count - 1]) && is_string($segments[$count - 1]) && strlen($segments[$count - 1]) == 1) {
//                $vars['char'] = $segments[$count - 1];
//            }

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

        foreach ($segments as $segment) {
            $segment = str_replace(':', '-', $segment);

            foreach ($categories as $category) {
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
                    $query = 'SELECT id FROM #__content WHERE catid = ' . $vars['catid'] . ' AND alias = ' . $db->Quote($segment);
                    $db->setQuery($query);
                    $cid = $db->loadResult();
                } else {
                    $cid = $segment;
                }

                $vars['id'] = $cid;

                if ($item->query['view'] == 'archive' && $count != 1) {
                    $vars['year'] = $count >= 2 ? $segments[$count - 2] : null;
                    $vars['month'] = $segments[$count - 1];
                    $vars['view'] = 'archive';
                } else {
                    $vars['view'] = 'article';
                }
            }

            $found = 0;
        }

        return $vars;
    }
}else{
    function TZ_PortfolioBuildRoute(&$query)
    {
        $segments = array();

        // get a menu item based on Itemid or currently active
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $params = JComponentHelper::getParams('com_tz_portfolio');
        $advanced = $params->get('sef_advanced_link', 0);

//    var_dump($query); die();
        // we need a menu item.  Either the one specified in the query, or the current active one if none specified
        if (empty($query['Itemid'])) {
            $menuItem = $menu->getActive();
            $menuItemGiven = false;
        } else {
            $menuItem = $menu->getItem((int)$query['Itemid']);
            $menuItemGiven = true;
        }

        if (isset($query['view'])) {
            $view = $query['view'];
        } else {
            // we need to have a view in the query or it is an invalid URL
            return $segments;
        }

        // are we dealing with an article or category that is attached to a menu item?
        if (($menuItem instanceof stdClass && isset($menuItem->query['view'])) && $menuItem->query['view'] == $query['view'] && isset($query['id']) && isset($menuItem->query['id']) && $menuItem->query['id'] == intval($query['id'])) {
            if (isset($query['char'])) {
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

        if ($view == 'category' || $view == 'article' || $view == 'portfolio' || $view == 'p_article') {
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
                            ->where('id=' . (int)$query['id'])
                        );
                        $alias = $db->loadResult();
                        $query['id'] = $query['id'] . ':' . $alias;
                    }
                } else {
                    // we should have these two set for this view.  If we don't, it is an error
                    return $segments;
                }
            } else {
                if (isset($query['id'])) {
                    $catid = $query['id'];
                } else {

                    if (isset($query['char'])) {
                        $segments[] = $query['char'];
                        unset($query['char']);
                    }
                    // we should have id set for this view.  If we don't, it is an error
                    return $segments;
                }
            }

            if ($menuItemGiven) {
                if (isset($menuItem->query['id'])) {
                    $mCatid = $menuItem->query['id'];
                } else {
                    $catids = $menuItem->params->get('tz_catid');
                    if ($catids) {
                        $mCatid = $catids;
                    } else {
                        $mCatid = 0;
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

            foreach ($path as $id) {
                if (isset($mCatid) && is_array($mCatid)) {
                    $chkCatidk = false;
                    for ($i = 0; $i < count($mCatid); $i++) {
                        if ((int)$id == (int)$mCatid[$i]) {
                            $chkCatidk = true;
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

            $opView = 'p_article';
            if ($params->get('view_router_switch', 1) == 2) {
                $opView = 'article';
            }
            if ($view == $opView) {
                $segments[] = 'item';
            }

            if (!$advanced && count($array)) {

                $array[0] = (int)$catid . ':' . $array[0];
            }
            $segments = array_merge($segments, $array);


            if ($view == 'article' || $view == 'p_article') {

                if ($advanced) {
                    list($alias, $id) = explode(':', $query['id'], 2);
                } else {
                    $id = $query['id'];
                }

                $segments[] = $id;
            }

            unset($query['id']);
            unset($query['catid']);

        }

        if ($view == 'tags') {
            $segments[] = $view;

            // Make sure we have the id and the alias
            if (strpos($query['id'], ':') == false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('name')
                    ->from('#__tz_portfolio_tags')
                    ->where('id=' . (int)$query['id'])
                );
                $alias = JApplication::stringURLSafe($db->loadResult());
                $segments[] = (int)$query['id'].':'.$alias;
            }else {
                $segments[] = $query['id'];
            }

            if (isset($query['char'])) {
                $segments[] = $query['char'];
                unset($query['char']);
            }

            unset($query['view']);
            unset($query['id']);
        }


        if ($view == 'users') {
            $item = $menu->getActive();

            if (isset($query['created_by'])) {
                $currentId = $query['created_by'];
            }

            // Make sure we have the id and the name
            if (strpos($query['created_by'], ':') == false) {
                $db = JFactory::getDbo();
                $aquery = $db->setQuery($db->getQuery(true)
                    ->select('name')
                    ->from('#__users')
                    ->where('id=' . (int)$query['created_by'])

                );
                $alias = JApplication::stringURLSafe($db->loadResult());
                $query['created_by'] = $query['created_by'] . ':' . $alias;

            }
//            list($userid, $alias) = explode(':', $query['created_by'], 2);

            $userMenuItemGiven = false;
            if (isset($menuItem)) {
                if (isset($menuItem->query)) {
                    $query2 = $menuItem->query;
                    if (isset($query2['created_by'])) {
                        $userMenuItemGiven = true;
                    }
                }
            }

            if (!$userMenuItemGiven) {
                if (isset($query['view'])) {
                    $segments[] = $view;
                    unset($query['view']);
                }


                if (isset($query['created_by'])) {
//                    if (isset($alias) && $alias) {
//                        $segments[] = $alias;
//                    }
//                    if (isset($userid) && $userid) {
//                        $segments[] = $userid;
//                    }
                    $segments[] = $query['created_by'];
                    unset($query['created_by']);
                }

                if (isset($query['char'])) {
                    $segments[] = $query['char'];
                    unset($query['char']);
                }

                return $segments;
            }

            if (isset($query['char'])) {
                $segments[] = $query['char'];
                unset($query['char']);
            }

            unset($query['view']);
            unset($query['created_by']);

            return $segments;
        }

        if ($view == 'archive' || $view == 'date') {

            if (!$menuItemGiven) {
                $segments[] = $view;
                unset($query['view']);
            }

            if ($view == 'date' && isset($query['view'])) {
//            if(!$menuItemGiven){
                $segments[] = $view;
//            }
                unset($query['view']);
            }

            $bool = false;
            if (isset($query['year']) && isset($query['month'])) {
                $bool = true;
            }

            if (isset($query['year'])) {
                if ($menuItemGiven) {
                    $segments[] = $query['year'];
                    unset($query['year']);
                }
            }

            if ($bool) {
                if ($menuItemGiven) {
                    $segments[] = $query['month'];
                    unset($query['month']);
                }
            }

            if (isset($query['char'])) {
                $segments[] = $query['char'];
                unset($query['char']);
            }

        }

        // if the layout is specified and it is the same as the layout in the menu item, we
        // unset it so it doesn't go into the query string.
        if (isset($query['layout'])) {
            if ($menuItemGiven && isset($menuItem->query['layout'])) {
                if ($query['layout'] == $menuItem->query['layout']) {

                    unset($query['layout']);
                }
            } else {
                if ($query['layout'] == 'default') {
                    unset($query['layout']);
                }
            }
        }

        if (isset($query['char'])) {
            $segments[] = $query['char'];
            unset($query['char']);
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param    array    The segments of the URL to parse.
     *
     * @return    array    The URL attributes to be used by the application.
     * @since    1.5
     */
    function TZ_PortfolioParseRoute($segments)
    {
        $vars = array();

        //Get the active menu item.
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        $item = $menu->getActive();
        $params = JComponentHelper::getParams('com_tz_portfolio');
        $advanced = $params->get('sef_advanced_link', 0);
        $db = JFactory::getDBO();

        // Count route segments
        $count = count($segments);
        // Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
        // the first segment is the view and the last segment is the id of the article or category.
        if (!isset($item)) {
            $vars['view'] = $segments[0];

            if ($vars['view'] == 'users') {
                if (!is_numeric($segments[$count - 1])) {
                    $vars['created_by'] = (int)$segments[$count - 1];
                } elseif (strlen($segments[$count - 1]) == 1) {
                    $vars['char'] = $segments[$count - 1];
                }
            } else {
                if (isset($segments[1]) && strlen($segments[1]) == 1) {
                    $vars['char'] = $segments[1];
                }
                $vars['id'] = $segments[$count - 1];
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
                if (isset($item->query['id'])) {
                    $vars['id'] = $item->query["id"];
                }
                if (isset($item->query['created_by'])) {
                    $vars['created_by'] = $item->query["created_by"];
                }
                return $vars;
            }
            // we check to see if an alias is given.  If not, we assume it is an article
            //Old
            if (strpos($segments[0], ':') === false) {
                $vars['view'] = 'article';
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
                $query = 'SELECT alias, catid FROM #__content WHERE id = ' . (int)$id;
                $db->setQuery($query);
                $article = $db->loadObject();

                if ($article) {
                    if ($article->alias == $alias) {
                        $vars['view'] = 'article';
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

            if ($segments[0] == 'tags') {
                $vars['view'] = $segments[0];
                if (isset($segments[count($segments) - 1])) {
//                    if(!is_numeric($segments[count($segments) - 1])){
//                        if(JString::strlen($segments[count($segments) - 1]) == 1){
//                            $vars['char'] = $segments[count($segments) - 1];
//                        }
//                        if(isset($segments[count($segments) - 2])){
//                            if(is_numeric($segments[count($segments) - 2])){
//                                $vars['id'] = $segments[count($segments) - 2];
//                            }
//                        }
//                    }else{
//                        $vars['id'] = (int) $segments[count($segments)-1];
//                    }
                    $vars['id'] = (int)$segments[count($segments) - 1];
                } else {
                    $vars['id'] = (int)$segments[count($segments) - 1];
                }

                return $vars;
            }
            if ($segments[0] == 'users') {
                $vars['view'] = $segments[0];
                if (is_numeric($segments[count($segments) - 1])) {
                    $vars['created_by'] = $segments[count($segments) - 1];
                } else {
                    $vars['created_by'] = (int)$segments[count($segments) - 1];
//                    $vars['created_by'] = (int)$segments[count($segments) - 2];
//                    $vars['char'] = $segments[count($segments) - 1];
                }
                return $vars;
            }

            if ($segments[0] == 'date') {
                $vars['view'] = $segments[0];
                if (count($segments) > 1) {
                    if (count($segments) > 2) {
                        $vars['year'] = $segments[1];
                        $vars['month'] = $segments[2];
                    }
                    if ((isset($vars['year']) && isset($vars['month']) && count($segments) > 3) || (count($segments) < 3))
                        $vars['char'] = $segments[count($segments) - 1];
                }
                return $vars;
            }

//        if(preg_match('/.*?\,([0-9]+)/i',$segments[0],$match)){
//            $segments[0]    = $match[1];
//        }

            $temp = $item->params;
            $menuParams = clone($params);
            $menuParams->merge($temp);

            $cat_id = (int)$segments[0];
            if ($segments[0] == 'item') {
                $cat_id = (int)$segments[1];
            }

            $article_id = (int)$segments[$count - 1];

            if ($article_id > 0) {
                $vars['view'] = 'article';

                if ($menuParams->get('view_router_switch', 1) == 2) {
                    $vars['view'] = 'p_article';
                }

                if ($segments[0] == 'item') {
                    $vars['view'] = 'p_article';

                    if ($menuParams->get('view_router_switch', 1) == 2) {
                        $vars['view'] = 'article';
                    }
                }
                $vars['catid'] = $cat_id;
                $vars['id'] = $article_id;

            } else {
                $vars['view'] = 'category';
                $vars['id'] = $cat_id;
            }
            if (isset($segments[$count - 1]) && is_string($segments[$count - 1]) && strlen($segments[$count - 1]) == 1) {
                $vars['char'] = $segments[$count - 1];
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

        foreach ($segments as $segment) {
            $segment = str_replace(':', '-', $segment);

            foreach ($categories as $category) {
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
                    $query = 'SELECT id FROM #__content WHERE catid = ' . $vars['catid'] . ' AND alias = ' . $db->Quote($segment);
                    $db->setQuery($query);
                    $cid = $db->loadResult();
                } else {
                    $cid = $segment;
                }

                $vars['id'] = $cid;

                if ($item->query['view'] == 'archive' && $count != 1) {
                    $vars['year'] = $count >= 2 ? $segments[$count - 2] : null;
                    $vars['month'] = $segments[$count - 1];
                    $vars['view'] = 'archive';
                } else {
                    $vars['view'] = 'article';
                }
            }

            $found = 0;
        }

        return $vars;
    }

}
