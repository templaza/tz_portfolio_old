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

jimport('joomla.application.component.controller');

class TZ_PortfolioControllerPortfolio extends JControllerLegacy
{
    function display($cachable = false, $urlparams = array()){
        switch ($this -> getTask()){
            case 'portfolio.ajax':
            case 'ajax':
                $this -> ajax();
                break;
            case 'portfolio.ajaxcategories':
            case 'ajaxcategories':
                $this -> ajaxcategories();
                break;
            case 'portfolio.ajaxtags':
            case 'ajaxtags':
                $this -> ajaxtags();
                break;
        }
    }
    function ajax(){

        $model  = $this -> getModel('Portfolio','TZ_PortfolioModel',array('ignore_request'=>true));
        $list       = $model -> ajax();
        echo $list;
        die();
    }

    function ajaxtags(){
         $model      = $this -> getModel('Portfolio','TZ_PortfolioModel',array('ignore_request'=>true));
        $list       = $model -> ajaxtags();

        echo $list;
        die();
    }

    function ajaxcategories(){
        $model      = $this -> getModel('Portfolio','TZ_PortfolioModel',array('ignore_request'=>true));
        $list       = $model -> ajaxCategories();

        echo $list;
        die();
    }
}