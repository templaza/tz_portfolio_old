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

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';
$document = JFactory::getDocument();
$document->addStyleSheet('modules/mod_tz_portfolio_categories/css/mod_tz_portfolio_categories.css');
$list = modTZ_PortfolioCategoriesHelper::getList($params);
$view = $params->get('views');
$read = $params->get('readmore');
$text = $params->get('readtext');
$title = $params->get('title');
$des = $params->get('des');
$width = $params->get('width');
$sl_width = $params->get('slide_width');
$sl_height = $params->get('slide_height');
$library    = $params -> get('library');

if($view == "menu"){
    require_once JModuleHelper::getLayoutPath('mod_tz_portfolio_categories','default');
} else{

    require_once JModuleHelper::getLayoutPath('mod_tz_portfolio_categories','slider');

}

 ?>
