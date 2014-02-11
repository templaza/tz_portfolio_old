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

jimport('joomla.application.component.modellist');

class TZ_PortfolioModelTags extends JModelList
{
    function populateState($ordering = null, $direction = null){

        parent::populateState('id','desc');

        $app    = JFactory::getApplication();

        $state  = $this -> getUserStateFromRequest('com_tz_portfolio.tags.filter_state','filter_state',null,'string');
        $this -> setState('filter_state',$state);

        $search  = $this -> getUserStateFromRequest('com_tz_portfolio.tags.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);

//        $limitstart  = $this -> getUserStateFromRequest('com_tz_portfolio.tags.limitstart','limitstart',0,'string');
//        $this -> setState('limitstart',$limitstart);
//
//        $limit  = $this -> getUserStateFromRequest('com_tz_portfolio.tags.limit','limit',20,'string');
//        $this -> setState('limit',$limit);

        $order  = $this -> getUserStateFromRequest('com_tz_portfolio.tags.filter_order','filter_order',null,'string');
        $this -> setState('filter_order',$order);

        $orderDir  = $this -> getUserStateFromRequest('com_tz_portfolio.tags.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    protected function getListQuery(){
        $db = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from('#__tz_portfolio_tags');

        if($search = $this -> getState('filter_search'))
            $query -> where('name LIKE "%'.$search.'%"');

        switch ($this -> getState('filter_state')){
            default:
                $query -> where('published>=0');
                break;
            case 'P':
                $query -> where('published=1');
                break;
            case 'U':
                $query -> where('published=0');
                break;
        }

        if($order = $this -> getState('filter_order','id')){
            $query -> order($order.' '.$this -> getState('filter_order_Dir','DESC'));
        }

        return $query;

    }

    public function getItems(){
        return parent::getItems();
    }

    function getTagsName(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('name');
        $query -> from('#__tz_portfolio_tags');
        $db -> setQuery($query);

        if($rows = $db -> loadColumn()){
            return json_encode($rows);
        }
        return null;
    }

//    public function getPagination(){
//        var_dump($this);
//        parent::getPagination();
//    }


}