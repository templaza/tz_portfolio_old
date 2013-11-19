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

$list = modTZ_PortfolioArticlesNewsHelper::getList($params);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

if($list){
    $doc    = JFactory::getDocument();
    $doc -> addStyleSheet('modules/mod_tz_portfolio_articles_news/css/style.css');
}

require JModuleHelper::getLayoutPath('mod_tz_portfolio_articles_news', $params->get('layout', 'horizontal'));
