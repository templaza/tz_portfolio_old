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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class TZ_PortfolioControllerTimeLine extends JControllerLegacy
{
    function display($cachable = false, $urlparams = array()){
        switch ($this -> getTask()){
            case 'timeline.ajax':
            case 'ajax':
                $this -> ajax();
                break;
            case 'timeline.ajaxcategories':
            case 'ajaxcategories':
                $this -> ajaxcategories();
                break;
            case 'timeline.ajaxtags':
            case 'ajaxtags':
                $this -> ajaxtags();
                break;
        }
    }
    function ajax(){

        $model      = $this -> getModel('TimeLine','TZ_PortfolioModel',array('ignore_request'=>true));
        $list       = $model -> ajax();

        echo $list;
        die();
    }
    function ajaxtags(){

        $model      = $this -> getModel('TimeLine','TZ_PortfolioModel',array('ignore_request'=>true));
        $list       = $model -> ajaxtags();

        echo $list;
        die();
    }

    function ajaxcategories(){
        $model      = $this -> getModel('TimeLine','TZ_PortfolioModel',array('ignore_request'=>true));
        $list       = $model -> ajaxCategories();

        echo $list;
        die();
    }
}
?>
