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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class TZ_PortfolioModelGroups extends JModelList{
    public function __construct($config = array()){
        parent::__construct($config);
    }

    public function populateState($ordering = null, $direction = null){

        parent::populateState('id','desc');

        $app        = JFactory::getApplication();
        $context    = 'com_tz_portfolio.groups';

        $state  = $app -> getUserStateFromRequest($context.'filter_state','filter_state',null,'string');
        $this -> setState('filter_state',$state);
        $search  = $app -> getUserStateFromRequest($context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);
        $order  = $app -> getUserStateFromRequest($context.'.filter_order','filter_order','name','string');
        $this -> setState('filter_order',$order);
        $orderDir  = $app -> getUserStateFromRequest($context.'.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    protected function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from($db -> quoteName('#__tz_portfolio_fields_group'));
        return $query;
    }

    public function getItems(){
        return parent::getItems();
    }

    // Get fields group with type array[key=groupid] = groupname
    public function getItemsArray(){
        $db     = $this -> getDbo();
        $db -> setQuery($this -> getListQuery());

        if($items = $db -> loadObjectList()){
            foreach($items as $item){
                $list[$item -> id]  = $item -> name;
            }
            return $list;
        }
        return array();
    }

    // Get fields group name have had fields
    public function getItemsContainFields(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('g.*,x.fieldsid');
        $query -> from($db -> quoteName('#__tz_portfolio_fields_group').' AS g');
        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_xref').' AS x ON x.groupid=g.id');
        $query -> order('x.fieldsid ASC');
        $db -> setQuery($query);

        if($items = $db -> loadObjectList()){
            $list   = array();
            foreach($items as $i => $item){
                if(isset($items[$i-1]) && ($items[$i - 1] -> fieldsid == $items[$i] -> fieldsid)){
                    $list[$item -> fieldsid]    .= ', '.$item -> name;
                }
                else{
                    $list[$item -> fieldsid]    = $item -> name;
                }
            }
            return $list;
        }
        return;

    }
}