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

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

class TZ_PortfolioControllerTemplate extends JControllerForm
{
    public function __construct($config = array()){
        parent::__construct($config);
    }
    public function display($cachable = false, $urlparams = false)
    {
        parent::display($cachable,$urlparams);
    }

}