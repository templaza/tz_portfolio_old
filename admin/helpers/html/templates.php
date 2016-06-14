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

defined('_JEXEC') or die;


class JHtmlTemplates
{
    public static function thumb($template, $clientId = 0)
    {
        $client = JApplicationHelper::getClientInfo($clientId);
        $basePath = $client->path . '/components/com_tz_portfolio/templates/' . $template;
        $baseUrl = ($clientId == 0) ? JUri::root(true) : JUri::root(true) . '/administrator';
//        $thumb = $basePath . '/template_thumbnail.png';
        $preview = $basePath . '/template_preview.png';
        $html = '';

//        if (file_exists($thumb))
//        {

//            $clientPath = ($clientId == 0) ? '' : 'administrator/';
//            $thumb = $clientPath . 'components/com_tz_portfolio/templates/' . $template . '/template_thumbnail.png';
//            $html = JHtml::_('image', $thumb, JText::_('COM_TEMPLATES_PREVIEW'));

            if (file_exists($preview))
            {
                JHtml::_('bootstrap.tooltip');
                JHtml::_('behavior.modal');

                $preview = $baseUrl . '/components/com_tz_portfolio/templates/' . $template . '/template_preview.png';
                $html = JHtml::_('image', '/components/com_tz_portfolio/templates/' . $template . '/template_preview.png', JText::_('COM_TEMPLATES_PREVIEW'));
                $html = '<a href="' . $preview . '" class="thumbnail pull-left modal hasTooltip" title="' .
                    JHtml::tooltipText('COM_TEMPLATES_CLICK_TO_ENLARGE') . '">' . $html . '</a>';
            }
//        }

        return $html;
    }
}