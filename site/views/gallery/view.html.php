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

//no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_tz_portfolio'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');

class TZ_PortfolioViewGallery extends JViewLegacy
{
    function display($tpl=null){
        $doc    = JFactory::getDocument();
        $params = $this -> get('State') -> get('params');

        $csscompress    = null;
        if($params -> get('css_compression',0)){
            $csscompress    = '.min';
        }

        $jscompress         = new stdClass();
        $jscompress -> extfile  = null;
        $jscompress -> folder   = null;
        if($params -> get('js_compression',1)){
            $jscompress -> extfile  = '.min';
            $jscompress -> folder   = '/packed';
        }

        $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'
            .'/jquery.tmpl.min.js"></script>');
        $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
            '/jquery.kinetic.min.js"></script>');
        $doc -> addCustomTag('<script type="text/javascript" src="components/com_tz_portfolio/js'.
            $jscompress -> folder.'/jquery.easing.1.3.min.js"></script>');

        $doc -> addStyleSheet('components/com_tz_portfolio/css/portfolio_gallery.min.css');

        
        $this -> assign('lists',$this -> get('Article'));
        $this -> assign('params',$params);

        $doc -> addStyleSheet('components/com_tz_portfolio/css/tzportfolio.min.css');

        parent::display($tpl);
    }
}
