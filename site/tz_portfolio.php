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

// Include dependancies
jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.'/helpers/route.php';
require_once JPATH_COMPONENT.'/helpers/query.php';

include_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/core/defines.php';
include_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/core/tzportfolio.php';
include_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/core/uri.php';
include_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/core/template.php';
include_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/core/controller.php';
include_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/core/view.php';

$controller = JControllerLegacy::getInstance('TZ_Portfolio');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
