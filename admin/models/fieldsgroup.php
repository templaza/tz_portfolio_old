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
jimport('joomla.appliaction.component.model');
jimport('joomla.html.pagination');
require_once(JPATH_COMPONENT.'/tables/fieldsgroup.php');

class TZ_PortfolioModelFieldsGroup extends JModelLegacy
{

    public $_link      = null;
    public $msg        = null;
    public $pagNav     = null;

    function __construct(){
        parent::__construct();
        if(JRequest::getVar('cid',array(),'','array'))
            $this -> cids   = JRequest::getVar('cid',array(),'','array');
        else
            $this -> cids[] = JRequest::getInt('id');
        $this -> db     = JFactory::getDbo();
    }

    function populateState(){
        
        $app        = JFactory::getApplication();
        $context    = 'com_tz_portfolio.fieldsgroup';

        $state  = $app -> getUserStateFromRequest($context.'filter_state','filter_state',null,'string');
        $this -> setState('filter_state',$state);
        $search  = $app -> getUserStateFromRequest($context.'.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);
        $order  = $app -> getUserStateFromRequest($context.'.filter_order','filter_order','name','string');
        $this -> setState('filter_order',$order);
        $orderDir  = $app -> getUserStateFromRequest($context.'.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
        $limitstart  = $app -> getUserStateFromRequest($context.'.limitstart','limitstart',0,'string');
        $this -> setState('limitstart',$limitstart);
        $limit  = $app -> getUserStateFromRequest($context.'.limit','limit',20,'string');
        $this -> setState('limit',$limit);
    }

    function getFieldsGroup(){
        $limit          = $this -> getState('limit');
        $limitstart     = $this -> getState('limitstart');
        $filter_state   = $this -> getState('filter_state');
        $search         = $this -> getState('filter_search');
        $order          = $this -> getState('filter_order');
        $order_dir      = $this -> getState('filter_order_Dir');
        $where          = array();

        if($filter_state){
            if($filter_state=='P')
                $where[]='published=1';
            elseif($filter_state=='U')
                $where[]='published=0';
        }

        if($search)
            $where[]='name LIKE "%'.$search.'%"';

        $where          = $where?implode(' AND ',$where):'';
        $where          = ($where)?' WHERE '.$where:'';

        $order          = (!in_array($order,array('id','name')))?'name':$order;
        $order_dir      = (in_array(strtoupper($order_dir),array('ASC','DESC'))?$order_dir:'ASC');
        $orderby        = ' ORDER BY '.$order.' '.strtoupper($order_dir);

        $query          = 'SELECT COUNT(*)'.
                          ' FROM #__tz_portfolio_fields_group'
                          .$where
                          .$orderby;

        $db             = JFactory::getDBO();
        $db -> setQuery($query);
        $total          = $db->loadResult();

        $this -> pagNav         = new JPagination($total,$limitstart,$limit);

        $query          = 'SELECT *'
                          .' FROM #__tz_portfolio_fields_group'
                          .$where
                          .$orderby;
        
        $row            = null;
        $db -> setQuery($query,$this -> pagNav -> limitstart,$this -> pagNav -> limit);

        if($db -> query()){
            $row    = $db -> loadObjectList();
        }
        
        return $row;
    }

    function getFieldsGroupEdit(){
        if(count($this -> cids)>0){
            $cid    = $this -> cids;
            $query  = 'SELECT * FROM #__tz_portfolio_fields_group'
                      .' WHERE id='.$cid[0];
            $db     = JFactory::getDBO();
            $db -> setQuery($query);
            $row    = $db -> loadObject();
            return $row;
        }
        return false;

    }

    function getPagination(){
        if(!$this->pagNav)
            return '';
        return $this->pagNav;
    }

    function removeFieldsGroup($cids=array()){
        if(count($cids)>0){
            $cids       = implode(',',$cids);
            $db         = JFactory::getDbo();
            $query      = 'SELECT fg.*, COUNT( x.groupid ) AS numcat FROM #__tz_portfolio_fields_group AS fg'
                          .' LEFT JOIN #__tz_portfolio_xref AS x'
                          .' ON fg.id=x.groupid'
                          .' WHERE fg.id IN('.$cids.')'
                          .' GROUP BY fg.id';
            
            $db -> setQuery($query);

            if(!$rows = $db -> loadObjectList()){
                $this -> setError($db -> stderr());
                return false;
            }

            $err    = array();
            $cid    = array();
            $total  = 0;
            foreach($rows as $row){
                if($row->numcat==0){
                    $cid[] = (int) $row->id;
                    $total++;
                }
                else{
                    $err[] = $row->name;

                }
            }
            if(count($cid)){
                $cid        = implode(',',$cid);

                $query      = 'DELETE FROM #__tz_portfolio_xref'
                              .' WHERE groupid IN('.$cid.')';
                $db -> setQuery($query);

                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }

                $query      = 'UPDATE #__tz_portfolio_xref_content SET groupid=0';
                $db -> setQuery($query);

                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }

                $query      = 'DELETE FROM #__tz_portfolio_categories'
                              .' WHERE groupid IN('.$cid.')';
                $db -> setQuery($query);

                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }
                
                $query      = 'DELETE FROM #__tz_portfolio_fields_group'
                              .' WHERE id IN('.$cid.')';

                $db -> setQuery($query);

                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }
                else{
                    $this -> msg    = JText::sprintf('COM_TZ_PORTFOLIO_FIELDS_GROUP_COUNT_DELETED',$total);
                }
            }
        }
        if(count($err)){
            $err    = implode(',',$err);
            $this -> setError(JText::sprintf('COM_TZ_PORTFOLIO_FIELDS_GROUP_NOT_REMOVE',$err));
            return false;
        }
        return true;
    }

    function saveFieldsGroup($task){

        $cid                    = JRequest::getVar('cid',array(),'','array');
        $post                   = JRequest::get('post');
        $post['description']    = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW );
        $row                    = & JTable::getInstance('FieldsGroup','Table');
        if($cid)
            $post['id'] = $cid[0];

        if(!$row -> bind($post)){
            $this -> setError($row -> getError());
            return false;
        }

        if(!$row -> store()){
            $this -> setError($row -> getError());
            return false;
        }

        switch ($task){
            case 'apply':
                $this -> _link  = $this -> _link.'&task=edit&id='.$row->id;
                $this -> msg    = JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_EDIT');
                break;
            case 'save':
                $this -> msg = JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_SAVED');
                break;
            case 'save2new':
                $this -> _link  = $this -> _link.'&task=add';
                $this -> msg    = JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_SUCCESS');
                break;
        }

        return true;
    }
}
