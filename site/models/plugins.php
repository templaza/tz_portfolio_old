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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/models/plugin.php';

/**
 * This models supports retrieving lists of articles.
 */
class TZ_PortfolioModelPlugins extends TZ_PortfolioModelPlugin
{

    protected $params   = null;
    function __construct($config = array()){
        $this -> params = new stdClass();
        parent::__construct($config);
    }

    function populateState(){
        parent::populateState();
        $this -> setState('filter.contentid',null);
    }

    function getPluginItems(){
        return parent::getPluginItems();
    }


    function getListQuery(){
        $db = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('p.*,x.element');
        $query -> from('`#__tz_portfolio_plugin` AS p');
        $query -> join('LEFT','`#__extensions` AS x ON p.pluginid = x.extension_id');
        $query -> where('x.enabled = 1');
        $query -> where('x.folder = '.$db -> quote($this -> pluginGroup));
        if($contentid = $this -> getState('filter.contentid')){
            if(is_array($contentid)){
                $contentid  = implode(',',$contentid);
            }
            $query -> where('p.contentid IN('.$contentid.')');
        }

        return $query;
    }


    function getItems(){
        $params = &$this -> params;
        
         if($plugins = $this -> getPluginItems()){
            foreach($plugins as $plugin){
                $params -> {$plugin -> element} = new JRegistry();
            }
        }
        
         $db = JFactory::getDbo();
        $db -> setQuery($this -> getListQuery());
        
        if($rows = $db -> loadObjectList()){
            $_params = new JRegistry();
            foreach($rows as &$row){
                $element    = $row -> element;
                $_params -> loadString($row -> params);
                $row -> params = new stdClass();
                $row -> params  = $_params;
                $params -> $element = $_params;
            }
            return $rows;
        }
        return false;
    }

    function getParams(){
        if($this -> params){
            return $this -> params;
        }
        return null;
    }

}
 
