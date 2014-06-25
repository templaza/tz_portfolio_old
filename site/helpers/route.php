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
    protected static $lookup = array();

    protected static $lang_lookup = array();

	/**
	 * @param	int	The route of the content item
	 */
	public static function getArticleRoute($id, $catid = 0, $language = 0)
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
				$link .= '&amp;catid='.$catid;
			}
		}

        if ($language && $language != "*" && JLanguageMultilang::isEnabled())
        {
            self::buildLanguageLookup();

            if (isset(self::$lang_lookup[$language]))
            {
                $link .= '&lang=' . self::$lang_lookup[$language];
                $needles['language'] = $language;
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

    /**
	 * @param	int	The route of the content item
	 */
	public static function getPortfolioArticleRoute($id, $catid = 0,$language = 0)
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

        if ($language && $language != "*" && JLanguageMultilang::isEnabled())
        {
            self::buildLanguageLookup();

            if (isset(self::$lang_lookup[$language]))
            {
                $link .= '&lang=' . self::$lang_lookup[$language];
                $needles['language'] = $language;
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

	public static function getCategoryRoute($catid, $language = 0)
	{
        if ($catid instanceof JCategoryNode)
        {
            $id       = $catid->id;
            $category = $catid;
        }
        else
        {
            $id       = (int) $catid;
            $category = JCategories::getInstance('Content')->get($id);
        }

        if ($id < 1 || !($category instanceof JCategoryNode))
        {
            $link = '';
        }
        else
        {
            $needles               = array();
            $link                  = 'index.php?option=com_tz_portfolio&amp;view=category&amp;id=' . $id;
            $catids                = array_reverse($category->getPath());
            $needles['category']   = $catids;
            $needles['categories'] = $catids;

            if ($language && $language != "*" && JLanguageMultilang::isEnabled())
            {
                self::buildLanguageLookup();

                if(isset(self::$lang_lookup[$language]))
                {
                    $link .= '&lang=' . self::$lang_lookup[$language];
                    $needles['language'] = $language;
                }
            }

            if ($item = self::_findItem($needles))
            {
                $link .= '&amp;Itemid=' . $item;
            }elseif ($item = self::_findItem()) {
                $link .= '&amp;Itemid='.$item;
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

    protected static function buildLanguageLookup()
    {
        if (count(self::$lang_lookup) == 0)
        {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('a.sef AS sef')
                ->select('a.lang_code AS lang_code')
                ->from('#__languages AS a');

            $db->setQuery($query);
            $langs = $db->loadObjectList();

            foreach ($langs as $lang)
            {
                self::$lang_lookup[$lang->lang_code] = $lang->sef;
            }
        }
    }

	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $language = isset($needles['language']) ? $needles['language'] : '*';

        // Prepare the reverse lookup array.
        if (!isset(self::$lookup[$language]))
        {
//		// Prepare the reverse lookup array.
//		if (self::$lookup === null)
//		{
//			self::$lookup = array();
            self::$lookup[$language] = array();

			$component	= JComponentHelper::getComponent('com_tz_portfolio');

            $attributes = array('component_id');
            $values     = array($component->id);

            if ($language != '*')
            {
                $attributes[] = 'language';
                $values[]     = array($needles['language'], '*');
            }

            $items = $menus->getItems($attributes, $values);

//			$items		= $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];

					if (!isset(self::$lookup[$language][$view])) {
						self::$lookup[$language][$view] = array();
					}
					if (isset($item->query['id'])) {
                        if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
                        {
                            self::$lookup[$language][$view][$item->query['id']] = $item->id;
                        }
					} else {
						$catids		=	$item->params->get('tz_catid');
						if ($catids) {
							if (is_array($catids)) {
								for ($i =0; $i<count($catids); $i++){
									self::$lookup[$language][$view][$catids[$i]] = $item->id;
								}
							} else {
								self::$lookup[$language][$view][$catids] = $item->id;
							}
						}
					}

                    if ($active && $active->component == 'com_tz_portfolio') {
                        if (isset($active->query) && isset($active->query['view'])){

                            if (isset($active->query['id'])) {
                                self::$lookup[$language][$active->query['view']][$active->query['id']] = $active->id;
                            }
                        }
                    }
				}
			} // End for
		}

        if ($needles)
        {
            foreach ($needles as $view => $ids)
            {
                if (isset(self::$lookup[$language][$view]))
                {
                    foreach ($ids as $id)
                    {
                        if (isset(self::$lookup[$language][$view][(int) $id]))
                        {
                            return self::$lookup[$language][$view][(int) $id];
                        }
                    }
                }
            }
        }

        if ($active && $active->component == 'com_tz_portfolio' && ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled()))
        {
            return $active->id;
        }

//        if ($active && $active->component == 'com_tz_portfolio') {
//            return $active->id;
//        }
        // If not found, return language specific home link
        $default = $menus->getDefault($language);

        return !empty($default->id) ? $default->id : null;
	}
}
