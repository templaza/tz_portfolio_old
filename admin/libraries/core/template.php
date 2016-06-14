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

// No direct access
defined('_JEXEC') or die;

class TZ_PortfolioTemplate {


    public static function getTemplate($params = false)
    {
        $templateId = self::getTemplateId();
        $template   = new stdClass;
        JTable::addIncludePath(COM_TZ_PORTFOLIO_ADMIN_PATH.DIRECTORY_SEPARATOR.'tables');

        $table  = JTable::getInstance('Templates','TZ_PortfolioTable');

        $template -> template   = 'system';
        $template -> params     = new JRegistry();
        $template -> layout     = null;
        $template -> home_path  = null;
        $template -> base_path  = null;
        $app                    = JFactory::getApplication('site');
        $input                  = $app -> input;
        $view_layout            = true;
        $home                   = null;

        if($home = $table -> getHome()){
            $default_params = new JRegistry;
            $default_params -> loadString($home -> params);
            $home -> params = clone($default_params);
        }

        if($templateId){
            $table -> load($templateId);
            $template -> id         = $templateId;
            $template -> template   = $table -> template;
            if($table -> params && !empty($table -> params)) {
                $_params    = new JRegistry($table -> params);
                $template->params = $_params;
            }
            if($table -> layout){
                $template -> layout = json_decode($table -> layout);
            }
        }else{
            if($home){
                $template -> id         = $home -> id;
                $template -> template   = $home -> template;
                if($home -> params && !empty($home -> params)) {
                    $_params    = new JRegistry($home -> params);
                    $template->params = $_params;
                }
                if($home -> layout){
                    $template -> layout = json_decode($home -> layout);
                }
            }
        }

        $tplparams      = $template -> params;

        $template -> base_path  = COM_TZ_PORTFOLIO_TEMPLATE_PATH.DIRECTORY_SEPARATOR
            . $template->template. DIRECTORY_SEPARATOR . 'html'. DIRECTORY_SEPARATOR
            . $template->params -> get('layout','default');

        if($home){
            if($home -> template != $template -> template) {
                $template->home_path = COM_TZ_PORTFOLIO_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                    . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                    . $tplparams->get('layout', 'default');
            }else{
                $template->home_path = COM_TZ_PORTFOLIO_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                    . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                    . $home -> params->get('layout', 'default');
            }
        }

        if ($params)
        {
            return $template;
        }

        return $template->template;
    }

    public static function getTemplateDefault(){

        $template   = new stdClass;

        $template -> template   = 'system';
        $template -> params     = new JRegistry();
        $template -> layout     = null;
        $table  = JTable::getInstance('Templates','TZ_PortfolioTable');

        if($home = $table -> getHome()){
            $template -> id         = $home -> id;
            $template -> template   = $home -> template;
            if($home -> params && !empty($home -> params)) {
                $_params    = new JRegistry($home -> params);
                $template->params = $_params;
            }
            if($home -> layout){
                $template -> layout = json_decode($home -> layout);
            }
        }

        return $template;
    }


    protected static function getTemplateId($artId = null,$catId = null){

        $db         = JFactory::getDbo();
        $app        = JFactory::getApplication('site');
        $templateId = null;
        $_catId     = null;
        $_artId     = null;

        $params = $app -> getParams();
        $templateId = $params -> get('tz_template_style_id');

        $input  = $app -> input;
        switch($input -> get('view')){
            case 'article':
                case 'p_article':
                $_artId = $input -> get('id',null,'int');
                $artModel   = JModelItem::getInstance('Article','TZ_PortfolioModel');
                if($artItem    = $artModel -> getItem($_artId)){
                    $_catId = $artItem -> catid;
                }
                break;
        }

        if(!empty($catId)){
            $_catId = $catId;
        }
        if(!empty($artId)){
            $_artId = $artId;
        }

        if($_catId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_categories'));
            $query -> where($db -> quoteName('catid').'='.$_catId);
            $db -> setQuery($query);
            if($crow = $db -> loadObject()){
                if($crow -> template_id){
                    $templateId = $crow -> template_id;
                }
            }
        }
        if($_artId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_xref_content'));
            $query -> where($db -> quoteName('contentid').'='.$_artId);
            $db -> setQuery($query);
            if($row = $db -> loadObject()){
                if($row -> template_id){
                    $templateId = $row -> template_id;
                }
            }
        }
        if(!$templateId){

        }
        return (int) $templateId;
    }
}