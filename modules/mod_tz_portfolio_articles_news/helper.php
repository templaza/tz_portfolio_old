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

abstract class modTZ_PortfolioArticlesNewsHelper
{
	public static function getList(&$params)
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'TZ_PortfolioModel', array('ignore_request' => true));

		// Set application parameters in model
		$appParams = JFactory::getApplication()->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));

		$model->setState('filter.published', 1);

		$model->setState('list.select', 'a.fulltext, a.id, a.title, a.alias, a.introtext, a.state, a.catid, a.created, a.created_by, a.created_by_alias,' .
			' a.modified, a.modified_by,a.publish_up, a.publish_down, a.attribs, a.metadata, a.metakey, a.metadesc, a.access,' .
			' a.hits, a.featured,' .
			' LENGTH(a.fulltext) AS readmore');
		// Access filter
		$access = !JComponentHelper::getParams('com_tz_portfolio')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		// Set ordering
		$ordering = $params->get('ordering', 'a.publish_up');
		$model->setState('list.ordering', $ordering);
		if (trim($ordering) == 'rand()') {
			$model->setState('list.direction', '');
		} else {
			$model->setState('list.direction', 'DESC');
		}

		//	Retrieve Content
		if($items = $model->getItems()){
            $model2 = JModelLegacy::getInstance('Media','TZ_PortfolioModel',array('ignore_request' => true));
            foreach ($items as &$item) {
                $model2 -> setState('article.id',$item -> id);

                $item -> media  = null;
                if($media  = $model2 -> getMedia()){
                    $item -> media  = $media[0];
                    if($media[0] -> type != 'video' && $media[0] -> type != 'audio'){
                        if(!empty($media[0] -> images)){
                            if($params -> get('tz_image_size','S')){
                                $imageName  = $media[0] -> images;
                                $item -> media -> images   = JURI::root().str_replace('.'.JFile::getExt($imageName)
                                    ,'_'.$params -> get('tz_image_size','S').'.'.JFile::getExt($imageName),$imageName);
                            }
                        }
                    }
                    else{
                        if(!empty($media[0] -> thumb)){
                            if($params -> get('tz_image_size','S')){
                                $imageName  = $media[0] -> thumb;
                                $item -> media -> images   = JURI::root().str_replace('.'.JFile::getExt($imageName)
                                    ,'_'.$params -> get('tz_image_size','S').'.'.JFile::getExt($imageName),$imageName);
                            }
                        }
                    }
                    if( ($media[0] -> type == 'quote' AND !$params -> get('show_quote',1))
                        OR ($media[0] -> type == 'link' AND !$params -> get('show_link',1)) ){
                        $item -> media  = null;
                    }
                }

                $item->readmore = (trim($item->fulltext) != '');
                $item->slug = $item->id.':'.$item->alias;
                $item->catslug = $item->catid.':'.$item->category_alias;

                if ($access || in_array($item->access, $authorised))
                {
                    // We know that user has the privilege to view the article
                    $item->link = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item->slug, $item->catid));
                    $item->linkText = JText::_('MOD_ARTICLES_NEWS_READMORE');
                }
                else {
                    $item->link = JRoute::_('index.php?option=com_users&view=login');
                    $item->linkText = JText::_('MOD_ARTICLES_NEWS_READMORE_REGISTER');
                }

                $item->introtext = JHtml::_('content.prepare', $item->introtext, '', 'mod_tz_portfolio_articles_news.content');

                //new
                if (!$params->get('image')) {
                    $item->introtext = preg_replace('/<img[^>]*>/', '', $item->introtext);
                }

                $results = $app->triggerEvent('onContentAfterDisplay', array('com_tz_portfolio.article', &$item, &$params, 1));
                $item->afterDisplayTitle = trim(implode("\n", $results));

                $results = $app->triggerEvent('onContentBeforeDisplay', array('com_tz_portfolio.article', &$item, &$params, 1));
                $item->beforeDisplayContent = trim(implode("\n", $results));
            }

            return $items;
        }
        return null;
	}
}
