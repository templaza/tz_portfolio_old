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

abstract class modTZ_PortfolioArticlesPopularHelper
{
	public static function getList(&$params)
	{
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
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		// Ordering
		$model->setState('list.ordering', 'a.hits');
		$model->setState('list.direction', 'DESC');

		$items = $model->getItems();

        $dispatcher = JDispatcher::getInstance();

        $model2 = JModelLegacy::getInstance('Media','TZ_PortfolioModel',array('ignore_request' => true));
		foreach ($items as &$item) {
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid.':'.$item->category_alias;

			if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item->slug, $item->catslug));
			} else {
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}

            if($params -> get('tz_show_title') == '0')
                $item -> title  = '';

            if($params -> get('tz_show_introtext') == '1'){

                $item -> text   = $item -> introtext;

                JPluginHelper::importPlugin('content');
                $results = $dispatcher->trigger('onContentPrepare', array('mod_tz_portfolio_articles_popular.content', &$item, &$params, 0));
                $item->introtext = $item->text;
                $item->event = new stdClass();

                $results = $dispatcher->trigger('onContentAfterTitle', array('mod_tz_portfolio_articles_popular.content', &$item, &$params, 0));
                $item->event->afterDisplayTitle = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentBeforeDisplay', array('mod_tz_portfolio_articles_popular.content', &$item, &$params, 0));
                $item->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplay', array('mod_tz_portfolio_articles_popular.content', &$item, &$params, 0));
                $item->event->afterDisplayContent = trim(implode("\n", $results));

                //Get Plugins Model
                $pmodel = JModelLegacy::getInstance('Plugins', 'TZ_PortfolioModel', array('ignore_request' => true));
                //Get plugin Params for this article
                $pmodel->setState('filter.contentid', $item->id);
                $pluginItems = $pmodel->getItems();
                $pluginParams = $pmodel->getParams();

                $item->pluginparams = clone($pluginParams);

                JPluginHelper::importPlugin('tz_portfolio');
                $results = $dispatcher->trigger('onTZPluginPrepare', array('mod_tz_portfolio_articles_popular.content', &$item, &$params, &$pluginParams, 0));

                $results = $dispatcher->trigger('onTZPluginAfterTitle', array('mod_tz_portfolio_articles_popular.content', &$item, &$params, &$pluginParams, 0));
                $item->event->TZafterDisplayTitle = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onTZPluginBeforeDisplay', array('mod_tz_portfolio_articles_popular.content', &$item, &$params, &$pluginParams, 0));
                $item->event->TZbeforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onTZPluginAfterDisplay', array('mod_tz_portfolio_articles_popular.content', &$item, &$params, &$pluginParams, 0));
                $item->event->TZafterDisplayContent = trim(implode("\n", $results));

                if($params -> get('tz_counter')){
                    $text   = strip_tags($item -> introtext);
                    $text   = explode(' ',$text);
                    $text   = array_splice($text,0,$params -> get('tz_counter'));
                    $text   = implode(' ',$text);
                }
                else
                    $text   = $item -> introtext;

                    $item -> text   = $text;
            }else{
                $item -> introtext  = null;
            }

            $item -> media  = null;
            $model2 -> setState('article.id',$item -> id);
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

            if($model2 && $params -> get('show_tz_image') == '1'){
                if($image  = $model2 -> getMedia($item -> id)){
                    if($image[0] -> type != 'quote' && $image[0] -> type != 'link'){
                        if($image[0] -> type != 'video' && $image[0] -> type != 'audio'){
                            if(!empty($image[0] -> images)){
                                if($params -> get('tz_image_size','S')){
                                    $imageName  = $image[0] -> images;
                                    $item -> tz_image   = str_replace('.'.JFile::getExt($imageName)
                                        ,'_'.$params -> get('tz_image_size').'.'.JFile::getExt($imageName)
                                        ,$imageName);
                                }

                            }
                        }
                        else{
                            if(!empty($image[0] -> thumb)){
                                if($params -> get('tz_image_size','S')){
                                    $imageName  = $image[0] -> thumb;
                                    $item -> tz_image   = str_replace('.'.JFile::getExt($imageName)
                                        ,'_'.$params -> get('tz_image_size').'.'.JFile::getExt($imageName)
                                        ,$imageName);
                                }
                            }
                        }
                        $item -> tz_image_title = $image[0] -> imagetitle;
                    }else{
                        $item -> quote_author   = null;
                        if(isset($image[0] -> quote_author)){
                            $item -> quote_author   = $image[0] -> quote_author;
                        }
                        if(isset($image[0] -> quote_text)){
                            $item -> quote_text = $image[0] -> quote_text;
                        }
                        if(isset($image[0] -> link_title)){
                            $item -> link_title = $image[0] -> link_title;
                        }
                        if(isset($image[0] -> link_url)){
                            $item -> link_url   = $image[0] -> link_url;
                        }
                        if(isset($image[0] -> link_target)){
                            $item -> link_target    = $image[0] -> link_target;
                        }
                        if(isset($image[0] -> link_follow)){
                            $item -> link_follow    = $image[0] -> link_follow;
                        }
                    }
                }
            }
		}

		return $items;
	}
}
