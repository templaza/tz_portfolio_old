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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Frontpage View class.
 */
class TZ_PortfolioViewFeatured extends JViewLegacy
{
	function display($tpl = null)
	{
		// parameters
		$app		= JFactory::getApplication();
		$db			= JFactory::getDbo();
		$document	= JFactory::getDocument();
		$params		= $app->getParams();
		$feedEmail	= (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'author';
		$siteEmail	= $app->getCfg('mailfrom');
		$document->link = JRoute::_('index.php?option=com_tz_portfolio&view=featured');

		// Get some data from the model
		JRequest::setVar('limit', $app->getCfg('feed_limit'));
		$categories = JCategories::getInstance('Content');
		$rows		= $this->get('Items');

        if($params -> get('show_feed_image',1) == 1){
            $model      = JModelLegacy::getInstance('Media','TZ_PortfolioModel');
        }

        $blogItemParams = $params;

		foreach ($rows as $row)
		{
            $media  = $model -> getMedia($row -> id);

			// strip html from feed item title
			$title = $this->escape($row->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			// Compute the article slug
			$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;

			// url link to article
            $blogItemParams -> merge($row -> params);
            if($blogItemParams -> get('tz_portfolio_redirect') == 'p_article'){
                $link = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($row->slug, $row->catid));
            }
            else{
                $link = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($row->slug, $row->catid));
            }

            // image to article
            $image  = null;
            if($params -> get('show_feed_image',1) == 1){
                $size   = $params -> get('feed_image_size','S');
                if(strtolower($media[0] -> type) == 'video'){
                    $image  = $media[0] -> thumb;
                }
                else{
                    $image  = $media[0] -> images;
                }
                if($image){
                    $image  = str_replace('.'.JFile::getExt($image),'_'.$size.'.'.JFile::getExt($image),$image);
                    $_link  = $link;
                    if(!preg_match('/'.JURI::base().'/',$link))
                        $_link  = str_replace(JURI::base(true).'/',JURI::base(),$link);
                    $image  = '<a href="'.$_link.'"><img src="'.$image.'" alt="'.$title.'"/></a>';
                }
            }


			// strip html from feed item description text
			// TODO: Only pull fulltext if necessary (actually, just get the necessary fields).
			$description	= ($params->get('feed_summary', 0) ? $row->introtext/*.$row->fulltext*/ : $row->introtext);
			$author			= $row->created_by_alias ? $row->created_by_alias : $row->author;

            if(isset($media[0] -> type) && (strtolower($media[0] -> type) == 'quote')){
                $author = $media[0] -> quote_author;
            }

            if(isset($media[0] -> type) && (strtolower($media[0] -> type) == 'quote' ||
                                            strtolower($media[0] -> type) == 'link')){
                if(strtolower($media[0] -> type) == 'quote'){
                    $description    = $media[0] -> quote_text.'<span class="author">'.$author.'</span>';
                }
            }

			// load individual item creator class
			$item = new JFeedItem();
            if((isset($media[0] -> type) && (strtolower($media[0] -> type) != 'quote' &&
                                strtolower($media[0] -> type) != 'link')) || !isset($media[0] -> type)){
                $item->title		= $title;
                $item->link			= $link;
            }else{
                if(strtolower($media[0] -> type) == 'link'){
                    $item->title	= $media[0] -> link_title;
                    $item ->link    = $media[0] -> link_url;
                }
            }
			$item->description	= $image.$description;
			$item->date			= $row->created;

			$item_category		= $categories->get($row->catid);
			$item->category		= array();
			$item->category[]	= JText::_('JFEATURED'); // All featured articles are categorized as "Featured"
			for ($item_category = $categories->get($row->catid); $item_category !== null; $item_category = $item_category->getParent()) {
				if ($item_category->id > 1) { // Only add non-root categories
					$item->category[] = $item_category->title;
				}
			}

			$item->author		= $author;
			if ($feedEmail == 'site') {
				$item->authorEmail = $siteEmail;
			}
			else {
				$item->authorEmail = $row->author_email;
			}
			// loads item info into rss array
			$document->addItem($item);
		}
	}
}
?>
