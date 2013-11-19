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

$option         = JRequest::getCmd('option','com_tz_portfolio');
$view           = JRequest::getCmd('view','articles');
$task           = JRequest::getCmd('task',null);

include_once dirname(__FILE__) . '/libraries/core/defines.php';
include_once dirname(__FILE__) . '/libraries/core/tzportfolio.php';

// Register helper class
JLoader::register('TZ_PortfolioHelper', dirname(__FILE__) . '/helpers/tz_portfolio.php');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_tz_portfolio')) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Execute the task.
$controller	= JControllerLegacy::getInstance('TZ_Portfolio');

    $controller->execute(JFactory::getApplication()->input->get('task'));

$controller->redirect();
