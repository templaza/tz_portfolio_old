<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

tzportfolioimport('core/less');
tzportfolioimport('core/jscompress');
jimport('joomla.application.component.controlleradmin');

class TZ_PortfolioControllerAction extends JControllerAdmin{
    function lesscall(){
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app    = JFactory::getApplication();
        $params = JComponentHelper::getParams('com_tz_portfolio');
        $return = base64_decode(JRequest::getString('return'));

        if(!$result = TZLess::compileAll($params)){
            $app -> redirect($return,JText::_('COM_TZ_PORTFOLIO_ENABLE_DEVELOPMENT_MODE_NOT_ENABLE_DEVELOPMENT'),'warning');
        }
        if(isset($result) && $result == 1){
            $app -> redirect($return,JText::_('COM_TZ_PORTFOLIO_ENABLE_DEVELOPMENT_MODE_NOT_FOUND_FILES'),'error');
        }
        $app -> redirect($return,JText::_('COM_TZ_PORTFOLIO_SUCCESSFULL_COMPLIE_LESS_TO_CSS'));

    }

    function jscompress(){
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app    = JFactory::getApplication();
        $params = JComponentHelper::getParams('com_tz_portfolio');
        $return = base64_decode(JRequest::getString('return'));

        if(!$result = TZJScompress::compressAll($params)){
            $app -> redirect($return,JText::_('COM_TZ_PORTFOLIO_ENABLE_DEVELOPMENT_MODE_NOT_ENABLE_DEVELOPMENT'),'warning');

        }
        if(isset($result) && $result == 1){
            $app -> redirect($return,JText::_('COM_TZ_PORTFOLIO_ENABLE_DEVELOPMENT_MODE_NOT_FOUND_FILES'),'error');
        }
        $app -> redirect($return,JText::_('COM_TZ_PORTFOLIO_SUCCESSFULL_COMPRESS_JAVASCRIPT'));
    }
}