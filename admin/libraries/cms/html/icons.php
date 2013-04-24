<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Utility class for icons.
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       2.5
 */
abstract class JHtmlIcons
{
	/**
	 * Method to generate html code for a list of buttons
	 *
	 * @param   array  $buttons  Array of buttons
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	public static function buttons($buttons)
	{
		$html = array();
		foreach ($buttons as $button)
		{
			$html[] = JHtml::_('icons.button', $button);
		}
		return implode($html);
	}

	/**
	 * Method to generate html code for a list of buttons
	 *
	 * @param   array|object  $button  Button properties
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	public static function button($button)
	{
		$user = JFactory::getUser();
		if (!empty($button['access']))
		{
			if (is_bool($button['access']))
			{
				if ($button['access'] == false)
				{
					return '';
				}
			}
			else
			{

				// Take each pair of permission, context values.
				for ($i = 0, $n = count($button['access']); $i < $n; $i += 2)
				{
					if (!$user->authorise($button['access'][$i], $button['access'][$i + 1]))
					{
						return '';
					}
				}
			}
		}

		$html[] = '<div class="row-fluid"' . (empty($button['id']) ? '' : (' id="' . $button['id'] . '"')) . '>';
		$html[] = '<div class="span12">';
		$html[] = '<a href="' . $button['link'] . '"';
		$html[] = (empty($button['target']) ? '' : (' target="' . $button['target'] . '"'));
		$html[] = (empty($button['onclick']) ? '' : (' onclick="' . $button['onclick'] . '"'));
		$html[] = (empty($button['title']) ? '' : (' title="' . htmlspecialchars($button['title']) . '"'));
		$html[] = '>';
		$html[] = '<i class="icon-' . $button['image'] . '"></i> ';
		$html[] = (empty($button['text'])) ? '' : ('<span>' . $button['text'] . '</span>');
		$html[] = '</a>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode($html);
	}
}
