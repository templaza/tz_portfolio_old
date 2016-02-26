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

/**
 * Content Component HTML Helper.
 */
class JHtmlIcon
{
	public static function create($category, $params)
	{
		$uri = JURI::getInstance();

		$url = 'index.php?option=com_tz_portfolio&task=article.add&return='.base64_encode($uri).'&a_id=0&catid=' . $category->id;

		if ($params->get('show_icons')) {
			$text = '<i class="icon-plus"></i> ' . JText::_('JNEW') . '&#160;';
		} else {
			$text = JText::_('JNEW').'&#160;';
		}

		$button = JHtml::_('link', JRoute::_($url), $text, 'class="btn btn-primary"');

		$output = '<span class="hasTip" title="'.JText::_('COM_CONTENT_CREATE_ARTICLE').'">'.$button.'</span>';
		return $output;
	}

	public static function email($article, $params, $attribs = array())
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';
		$uri	= JURI::getInstance();
		$base	= $uri->toString(array('scheme', 'host', 'port'));
		$template = JFactory::getApplication()->getTemplate();

        if($params -> get('tz_portfolio_redirect') == 'p_article'){
            $link   = TZ_PortfolioHelperRoute::getPortfolioArticleRoute($article -> slug,$article -> catid);
        }else{
            $link   = TZ_PortfolioHelperRoute::getArticleRoute($article -> slug,$article -> catid);
        }
		$link	= $base . JRoute::_($link,false);

		$url	= 'index.php?option=com_mailto&amp;tmpl=component&amp;template='.$template.'&amp;link='.MailToHelper::addLink($link);

		$status = 'width=400,height=350,menubar=yes,resizable=yes';

		if ($params->get('show_icons')) {
			$text = '<i class="icon-envelope"></i> ' . JText::_('JGLOBAL_EMAIL');
		} else {
			$text = JText::_('JGLOBAL_EMAIL');
		}

		$attribs['title']	= JText::_('JGLOBAL_EMAIL');
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);
		return $output;
	}

	/**
	 * Display an edit icon for the article.
	 *
	 * This icon will not display in a popup window, nor if the article is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$article	The article in question.
	 * @param	object	$params		The article parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the article edit icon.
	 * @since	1.6
	 */
	public static function edit($article, $params, $attribs = array())
	{
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JURI::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup')) {
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($article->state < 0) {
			return;
		}

		JHtml::_('behavior.tooltip');

		// Show checked_out icon if the article is checked out by a different user
		if (property_exists($article, 'checked_out') && property_exists($article, 'checked_out_time') && $article->checked_out > 0 && $article->checked_out != $user->get('id')) {
			$checkoutUser = JFactory::getUser($article->checked_out);
			$button = JHtml::_('image', 'system/checked_out.png', null, null, true);
			$date = JHtml::_('date', $article->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_CONTENT_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
			return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
		}

        $tmpl   = JRequest::getCmd('tmpl',null);
        if($tmpl){
            $tmpl   = '&tmpl=component';
        }

		$url	= 'index.php?option=com_tz_portfolio&amp;task=article.edit&amp;a_id='.$article->id.'&amp;return='.base64_encode($uri)
                  .$tmpl;

		if ($article->state == 0) {
					$overlib = JText::_('JUNPUBLISHED');
				}
				else {
					$overlib = JText::_('JPUBLISHED');
				}

				$date = JHtml::_('date', $article->created);
				$author = $article->created_by_alias ? $article->created_by_alias : $article->author;

				$overlib .= '&lt;br /&gt;';
				$overlib .= $date;
				$overlib .= '&lt;br /&gt;';
				$overlib .= JText::sprintf('COM_CONTENT_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		$icon	= $article->state ? 'edit' : 'eye-close';
		$text = '<i class="hasTip icon-'.$icon.'" title="'.JText::_('COM_CONTENT_EDIT_ITEM').' :: '.$overlib.'"></i> '.JText::_('JGLOBAL_EDIT');

		$output = JHtml::_('link', JRoute::_($url), $text);

		return $output;
	}


	public static function print_popup($article, $params, $attribs = array())
	{
        if($params -> get('tz_portfolio_redirect') == 'p_article'){
            $url    = TZ_PortfolioHelperRoute::getPortfolioArticleRoute($article -> slug,$article -> catid);
        }else{
            $url    = TZ_PortfolioHelperRoute::getArticleRoute($article -> slug,$article -> catid);
        }

		$url .= '&amp;tmpl=component&amp;print=1&amp;layout=default&amp;page='.@ $request->limitstart;

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons')) {
			$text = '<i class="icon-print"></i> '.JText::_('JGLOBAL_PRINT');
		} else {
			$text = JText::_('JGLOBAL_PRINT');
		}

		$attribs['title']	= JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
		$attribs['rel']		= 'nofollow';

		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}

	public static function print_screen($article, $params, $attribs = array())
	{
		// checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons')) {
			$text = $text = '<i class="icon-print"></i> '.JText::_('JGLOBAL_PRINT');
		} else {
			$text = JText::_('JGLOBAL_PRINT');
		}
		return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
	}

}
