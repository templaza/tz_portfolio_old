<?php
/**
 * Created by JetBrains PhpStorm.
 * User: duongtvtemplaza
 * Date: 1/22/13
 * Time: 4:19 PM
 * To change this template use File | Settings | File Templates.
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class to fire onContentPrepare for non-article based content.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
class JHtmlArticle
{
    public static function tzprepare($text, $params = null,$pluginParams = null, $context = 'text')
	{
		if ($params === null)
		{
			$params = new JObject;
		}
		$article = new stdClass;
		$article->text = $text;
		JPluginHelper::importPlugin('tz_portfolio');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onTZPluginPrepare', array($context, &$article, &$params,&$pluginParams, 0));

		return $article->text;
	}
}