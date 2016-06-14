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

class TZ_PortfolioModelFields extends JModelList{
    public function __construct($config = array()){
        parent::__construct($config);
    }

    public function populateState($ordering = null, $direction = null){

        parent::populateState('id','desc');

        $app        = JFactory::getApplication();
        $context    = 'com_tz_portfolio.fields';

        $group  = $app -> getUserStateFromRequest($context.'.filter_group','filter_group',0,'int');
        $this -> setState('filter_group',$group);
        $state  = $app -> getUserStateFromRequest($context.'filter_state','filter_state',null,'string');
        $this -> setState('filter_state',$state);
        $type  = $app -> getUserStateFromRequest($context.'filter_type','filter_type',null,'string');
        $this -> setState('filter_type',$type);
        $search  = $app -> getUserStateFromRequest($context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);
        $order  = $app -> getUserStateFromRequest($context.'.filter_order','filter_order','f.ordering','string');
        $this -> setState('filter_order',$order);
        $orderDir  = $app -> getUserStateFromRequest($context.'.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    protected function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('f.*');
        $query -> from('#__tz_portfolio_fields AS f');
        $query -> join('LEFT','#__tz_portfolio_xref AS x ON f.id=x.fieldsid');
        $query -> join('INNER','#__tz_portfolio_fields_group AS fg ON fg.id=x.groupid');

        if($search = $this -> getState('filter_search'))
            $query -> where('title LIKE '.$db -> quote('%'.$search.'%'));

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

        if($filter_group = $this -> getState('filter_group')){
            if($filter_group!=-1){
                $query -> where('x.groupid ='.$filter_group);
            }
        }

        if($filter_type = $this -> getState('filter_type')){
            $query -> where('f.type='.$db -> quote($filter_type));
        }

        $query -> group('f.id');

        if($order = $this -> getState('filter_order','f.id')){
            $query -> order($order.' '.$this -> getState('filter_order_Dir','DESC'));
        }

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $groupModel = JModelLegacy::getInstance('Groups','TZ_PortfolioModel');
            if($groups = $groupModel -> getItemsContainFields()){
                foreach($items as $item){
                    if(isset($groups[$item -> id])){
                        $item -> groupname  = $groups[$item -> id];
                    }
                }
            }
            return $items;
        }
    }

}