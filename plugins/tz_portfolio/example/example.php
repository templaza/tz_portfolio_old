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

/**
 * Pagenavigation plugin class.
 */
class plgTZ_PortfolioTZ_Portfolio extends JPlugin
{

   protected static $modules = array();
	protected static $mods = array();
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onTZPluginPrepare($context, &$article, &$params,&$pluginParams, $page = 0)
	{
        //Do something
	}

    public function onTZPluginAfterTitle($context, &$article, &$params,&$pluginParams, $page = 0)
	{
        //Do something
    }
    public function onTZPluginBeforeDisplay($context, &$article, &$params,&$pluginParams, $page = 0)
	{
        //Do something
    }
    public function onTZPluginAfterDisplay($context, &$article, &$params,&$pluginParams, $page = 0)
	{
        //Do something
    }
}