<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_tz_portfolio/helpers/categories.php');

/**
 * Category Component Association Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_categories
 * @since       3.0
 */
abstract class CategoryHelperAssociation
{
	public static $category_association = true;

	/**
	 * Method to get the associations for a given category
	 *
	 * @param   integer  $id         Id of the item
	 * @param   string   $extension  Name of the component
	 *
	 * @return  array    Array of associations for the component categories
	 *
	 * @since  3.0
	 */

	public static function getCategoryAssociations($id = 0, $extension = 'com_content',$view = '')
	{
		$return = array();

		if ($id)
		{
//            if(!is_array($id)){
                // Load route helper
                jimport('helper.route', JPATH_COMPONENT_SITE);

                $helperClassname = 'TZ_PortfolioHelperRoute';

                $associations = CategoriesHelper::getAssociations($id, $extension);

                foreach ($associations as $tag => $item)
                {
                    if (class_exists($helperClassname) && is_callable(array($helperClassname, 'getCategoryRoute')))
                    {
                        $return[$tag] = $helperClassname::getCategoryRoute($item, $tag);
                    }
                    else
                    {
                        $return[$tag] = 'index.php?option=com_tz_portfolio&amp;view=category&id=' . $item;
                    }
                }
//            }else{
//                $associations = CategoriesHelper::getAssociations($id, $extension);
//                var_dump($associations);
//            }
        }

		return $return;
	}
}
