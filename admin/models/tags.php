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
jimport('joomla.application.component.model');
jimport('joomla.html.pagination');

class TZ_PortfolioModelTags extends JModelLegacy
{
    public $_link      = null;
    public $msg        = null;
    var $paNav  = null;

    function populateState(){
        $app    = &JFactory::getApplication();

        $state  = $app -> getUserStateFromRequest('com_tz_portfolio.tags.filter_state','filter_state',null,'string');
        $this -> setState('filter_state',$state);
        $search  = $app -> getUserStateFromRequest('com_tz_portfolio.tags.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);
        $limitstart  = $app -> getUserStateFromRequest('com_tz_portfolio.tags.limitstart','limitstart',0,'string');
        $this -> setState('limitstart',$limitstart);
        $limit  = $app -> getUserStateFromRequest('com_tz_portfolio.tags.limit','limit',20,'string');
        $this -> setState('limit',$limit);
        $order  = $app -> getUserStateFromRequest('com_tz_portfolio.tags.filter_order','filter_order',null,'string');
        $this -> setState('filter_order',$order);
        $orderDir  = $app -> getUserStateFromRequest('com_tz_portfolio.tags.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    function getLists(){
        $total          = 0;

        $limitstart     = $this -> getState('limitstart');
        $limit          = $this -> getState('limit');
        $search         = trim($this -> getState('filter_search'));
        $filter_state   = $this -> getState('filter_state');
        $filter_order   = $this -> getState('filter_order');
        $order_Dir      = $this -> getState('filter_order_Dir');

        $db             = &JFactory::getDbo();

        $where          = array();

        if(!empty($search))
            $where[]    = 'name LIKE "%'.$search.'%"';

        switch ($filter_state){
            default:
                $where[]    = 'published>=0';
                break;
            case 'P':
                $where[]    = 'published=1';
                break;
            case 'U':
                $where[]    = 'published=0';
                break;
        }

        $where      = ' WHERE '.implode(' AND ',$where);

        $order_by   = null;
        if(!in_array($filter_order,array('name','published')))
            $filter_order   = 'id';

        if(!in_array(strtoupper($order_Dir),array('ASC','DESC')))
            $order_Dir      = 'DESC';

        $order_by = ' ORDER BY '.$filter_order.' '.$order_Dir;

        $query  = 'SELECT COUNT(*) FROM #__tz_portfolio_tags'
            .$where;

        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }

        $total  = $db ->loadResult();
        $this -> paNav  = new JPagination($total,$limitstart,$limit);

        $query  = 'SELECT * FROM #__tz_portfolio_tags'
            .$where
            .$order_by;

        $db -> setQuery($query,$this -> paNav -> limitstart,$this -> paNav -> limit);

        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }
        if($rows = $db -> loadObjectList()){
            return $rows;
        }
        return false;
    }

    function getPagination(){
        return $this -> paNav;
    }

    function getEdit(){
        $rows   = array();
        $cids   = JRequest::getVar('cid',array(),'','array');
        if(count($cids)){
            $id     = $cids[0];
        }
        if(JRequest::getInt('id')){
            $id   = JRequest::getInt('id');
        }
        if(JRequest::getCmd('task') == 'edit'){
            if($id){
                $query  = 'SELECT * FROM #__tz_portfolio_tags'
                    .' WHERE id='.$id;
                $db     = &JFactory::getDbo();
                $db -> setQuery($query);

                if(!$rows = $db -> loadObject()){
                    var_dump($this -> db -> getErrorMsg());
                    return false;
                }
            }
        }
        return $rows;
    }

    function publishTags($cids,$state){
        if(count($cids)>0){
            $count  = count($cids);
            $cids   = implode(',',$cids);
            $query  = 'UPDATE #__tz_portfolio_tags SET published='.$state
                .' WHERE id IN('.$cids.')';
            $db     = &JFactory::getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }
            else{
                $this -> msg = ($state == 1)?JText::sprintf('COM_TZ_PORTFOLIO_TAGS_COUNT_PUBLISHED',$count):'';
                $this -> msg = ($state == 0)?JText::sprintf('COM_TZ_PORTFOLIO_TAGS_COUNT_UNPUBLISHED',$count):'';
            }
            return true;
        }
    }

    function removeTags($cids = array()){
        if(count($cids)>0){
            $count      = count($cids);
            $cids       = implode(',',$cids);

            $query  = 'DELETE FROM #__tz_portfolio_tags_xref'
                .' WHERE tagsid IN('.$cids.')';
            $db     = &JFactory::getDbo();

            $db -> setQuery($query);
            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }

            $query  = 'DELETE FROM #__tz_portfolio_tags'
                .' WHERE id IN('.$cids.')';

            $db -> setQuery($query);

            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }
            else
                $this -> msg = JText::sprintf('COM_TZ_PORTFOLIO_TAGS_COUNT_DELETED',$count);
        }
        return true;
    }

    function checkTags($name=null){
        $name   = trim($name);
        if(!empty($name)){
            $name   = strtolower($name);
            $query  = 'SELECT COUNT(*) FROM #__tz_portfolio_tags'
                      .' WHERE name="'.$name.'"';
            $db     = &JFactory::getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }
            $total  = $db -> loadResult();
            if($total > 0){
                $this -> setError(JText::_('COM_TZ_PORTFOLIO_TAG_EXISTS_ALREADY'));
                return false;
            }
            return true;
        }
        return false;

    }
    function saveTags($task){

        $cid                    = JRequest::getVar('cid',array(),'','array');
        $post                   = JRequest::get('post');
        $post['description']    = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW );
        $row                    = & JTable::getInstance('Tags','Table');

        if($cid)
            $post['id'] = $cid[0];

//        $post['name']   = strtolower($post['name']);
        $post['name']   = str_replace(array(',','\'','"','.','?'
                                           ,'/','\\','<','>','(',')','*','&','^','%','$','#','@','!','-','+','|','`','~'),'',$post['name']);

        $post['published'] = $post['published'] == 'P'?1:0;

        if(!$this -> checkTags($post['name'])){
            $this -> _link  = $this -> _link.'&task=add';
            return false;
        }
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
                $this -> _link  = $this -> _link.'&task=edit&cid[]='.$row->id;
                $this -> msg    = JText::_('COM_TZ_PORTFOLIO_TAGS_SUCCESS');
                break;
            case 'save':
                $this -> msg = JText::_('COM_TZ_PORTFOLIO_TAGS_SUCCESS');
                break;
            case 'save2new':
                $this -> _link  = $this -> _link.'&task=add';
                $this -> msg    = JText::_('COM_TZ_PORTFOLIO_TAGS_SUCCESS');
                break;
        }

        return true;
    }
}