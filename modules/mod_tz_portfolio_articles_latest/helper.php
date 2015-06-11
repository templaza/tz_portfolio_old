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

jimport('joomla.application.component.model');

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_tz_portfolio/models', 'TZ_PortfolioModel');

abstract class modTZ_PortfolioArticlesLatestHelper
{
	public static function getList(&$params)
	{
		// Get the dbo
		$db = JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'TZ_PortfolioModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
		$model->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_tz_portfolio')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// User filter
		$userId = JFactory::getUser()->get('id');
		switch ($params->get('user_id'))
		{
			case 'by_me':
				$model->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;

			case '0':
				break;

			default:
				$model->setState('filter.author_id', (int) $params->get('user_id'));
				break;
		}

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		//  Featured switch
		switch ($params->get('show_featured'))
		{
			case '1':
				$model->setState('filter.featured', 'only');
				break;
			case '0':
				$model->setState('filter.featured', 'hide');
				break;
			default:
				$model->setState('filter.featured', 'show');
				break;
		}

		// Set ordering
		$order_map = array(
			'm_dsc' => 'a.modified DESC, a.created',
			'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
			'c_dsc' => 'a.created',
			'p_dsc' => 'a.publish_up',
		);
		$ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
		$dir = 'DESC';

		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $dir);

		$items = $model->getItems();

        $model2 = JModelLegacy::getInstance('Media','TZ_PortfolioModel',array('ignore_request' => true));

        if($items){

            $dispatcher = JDispatcher::getInstance();
            foreach ($items as $i => &$item) {
                $item -> text   = $item -> introtext;

                JPluginHelper::importPlugin('content');
                $results = $dispatcher->trigger('onContentPrepare', array('mod_tz_portfolio_articles_latest.content', &$item, &$params, 0));
                $item->introtext = $item->text;
                $item->event = new stdClass();

                $results = $dispatcher->trigger('onContentAfterTitle', array('mod_tz_portfolio_articles_latest.content', &$item, &$params, 0));
                $item->event->afterDisplayTitle = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentBeforeDisplay', array('mod_tz_portfolio_articles_latest.content', &$item, &$params, 0));
                $item->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplay', array('mod_tz_portfolio_articles_latest.content', &$item, &$params, 0));
                $item->event->afterDisplayContent = trim(implode("\n", $results));

                //Get Plugins Model
                $pmodel = JModelLegacy::getInstance('Plugins', 'TZ_PortfolioModel', array('ignore_request' => true));
                //Get plugin Params for this article
                $pmodel->setState('filter.contentid', $item->id);
                $pluginItems = $pmodel->getItems();
                $pluginParams = $pmodel->getParams();

                $item->pluginparams = clone($pluginParams);

                JPluginHelper::importPlugin('tz_portfolio');
                $results = $dispatcher->trigger('onTZPluginPrepare', array('mod_tz_portfolio_articles_latest.content', &$item, &$params, &$pluginParams, 0));

                $results = $dispatcher->trigger('onTZPluginAfterTitle', array('mod_tz_portfolio_articles_latest.content', &$item, &$params, &$pluginParams, 0));
                $item->event->TZafterDisplayTitle = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('mod_tz_portfolio_articles_latest.content', &$item, &$params, &$pluginParams, 0));
                $item->event->TZbeforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('mod_tz_portfolio_articles_latest.content', &$item, &$params, &$pluginParams, 0));
                $item->event->TZafterDisplayContent = trim(implode("\n", $results));


                $item -> media  = null;

                $item->slug = $item->id.':'.$item->alias;
                $item->catslug = $item->catid.':'.$item->category_alias;

                if ($access || in_array($item->access, $authorised)) {
                    // We know that user has the privilege to view the article
                    $item->link = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item->slug, $item->catslug));
                } else {
                    $item->link = JRoute::_('index.php?option=com_users&view=login');
                }
                $model2 -> setState('article.id',$item -> id);
                if($media  = $model2 -> getMedia()){
                    $item -> media  = $media[0];
                    if( ($media[0] -> type == 'quote' AND !$params -> get('show_quote',1))
                        OR ($media[0] -> type == 'link' AND !$params -> get('show_link',1)) ){
                        unset($items[$i]);
                    }
                }

            }
		}
		return array_reverse($items);
	}
}
