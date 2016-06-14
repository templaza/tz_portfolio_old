<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('ContentHelper', JPATH_ADMINISTRATOR . '/components/com_content/helpers/content.php');
JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR . '/components/com_tz_portfolio/helpers/association.php');
//JModelLegacy::addIncludePath(JPATH_SITE.'/administrator/components/com_tz_portfolio/models', 'TZ_PortfolioModel');

/**
 * Content Component Association Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       3.0
 */
abstract class TZ_PortfolioHelperAssociation extends CategoryHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item
	 * @param   string   $view  Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  3.0
	 */

	public static function getAssociations($id = 0, $view = null)
	{
		jimport('helper.route', JPATH_COMPONENT_SITE);

		$app = JFactory::getApplication();
		$jinput = $app->input;
		$view = is_null($view) ? $jinput->get('view') : $view;
		$id = empty($id) ? $jinput->getInt('id') : $id;

		if ($view == 'article')
		{
			if ($id)
			{
				$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $id);

				$return = array();

				foreach ($associations as $tag => $item)
				{
					$return[$tag] = TZ_PortfolioHelperRoute::getArticleRoute($item->id, $item->catid, $item->language);
				}

				return $return;
			}
		}elseif ($view == 'p_article')
		{
			if ($id)
			{
				$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $id);

				$return = array();

				foreach ($associations as $tag => $item)
				{
					$return[$tag] = TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item->id, $item->catid, $item->language);
				}

				return $return;
			}
		}

		if ($view == 'category' || $view == 'categories')
		{
			return self::getCategoryAssociations($id, 'com_content');
		}

		return array();

	}
}
