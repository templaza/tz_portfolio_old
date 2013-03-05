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
require_once(JPATH_COMPONENT.'/tables/fields.php');

class TZ_PortfolioModelFields extends JModelLegacy
{
    public $_link   = null;
    public $_task   = null;
    public $msg     = null;
    public $cids    = null;
    protected $db   = null;

    function populateState(){
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
        $limitstart  = $app -> getUserStateFromRequest($context.'.limitstart','limitstart',0,'string');
        $this -> setState('limitstart',$limitstart);
        $limit  = $app -> getUserStateFromRequest($context.'.limit','limit',20,'string');
        $this -> setState('limit',$limit);
    }

    function __construct(){
        parent::__construct();
        if(JRequest::getVar('cid',array(),'','array'))
            $this -> cids   = JRequest::getVar('cid',array(),'','array');
        else
            $this -> cids[] = JRequest::getInt('id');
        $this -> db     = JFactory::getDbo();
    }

    function getArticleGroupFields($articleId=null){
        if(is_array($articleId))
            $articleId  = implode(',',$articleId);
        $query  = 'SELECT groupid, FROM #__tz_portfolio_xref'
                  .' WHERE contentid IN('.$articleId.')';
//                  .' GROUP BY t.contentid';
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        $rows   = $db -> loadObjectList();
        return $rows;
    }
    
    public function getFieldsGroup(){
        $rows       = null;
        $query      = 'SELECT * FROM #__tz_portfolio_fields_group ORDER BY name ASC';
        $this -> db -> setQuery($query);
        if($this -> db -> query())
            $rows       = $this -> db -> loadObjectList();
        else
            var_dump($this -> db -> getErrorMsg());
        return $rows;
    }

    public function getFieldsEdit(){
        $rows   = array();
        if($this -> _task == 'edit'){
            if(count($this -> cids)>0){
                $query  = 'SELECT f.*,x.groupid AS groupid'
                    .' FROM #__tz_portfolio_fields AS f'
                    .' INNER JOIN #__tz_portfolio_xref AS x'
                          .' ON f.id=x.fieldsid'
                          .' LEFT JOIN #__tz_portfolio_fields_group AS fg'
                          .' ON fg.id=x.groupid'
                    .' WHERE f.id='.$this -> cids[0];
                $this -> db     = JFactory::getDbo();
                $this -> db -> setQuery($query);

                if(!$rows = $this -> db -> loadObjectList()){
                    var_dump($this -> db -> getErrorMsg());
                    return false;
                }
            }
        }
        return $rows;
    }

    function _checkArticleFields($fieldsId=null,$articleId=null){
        if($fieldsId)
            $where  = ' WHERE fieldsid ='.$fieldsId;
        if($articleId)
            $where  .= ' AND contentid='.$articleId;
        
        $query  = 'SELECT * FROM #__tz_portfolio'
                  .$where;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($rows = $db -> loadObjectList()){
            
            return $rows;
        }

        return false;
    }

    public function getParams(){

        $list   = array();
        $arr    = array();
        //if($this -> _task == 'edit'){
            if(count($this -> cids)>0){
                $query  = 'SELECT *'
                          .' FROM #__tz_portfolio_fields'
                          .' WHERE id='.(int) $this -> cids[0];

                $this -> db -> setQuery($query);
                $rows   = $this -> db ->loadObject();

                if($rows){
                    $arr    = str_replace('[','',$rows -> value);
                    $arr    = str_replace(']','',$arr);

                    if(preg_match('/.*\},{\.*?/s',$arr,$match)){
                        //var_dump($match);
                        $values     = str_replace('},','}///',$arr);
                        $values     = explode('///',$values);
                    }
                    else
                        $values=(array) $arr;

                    $artOptFields   = $this -> _checkArticleFields($rows -> id);

                    if(count($values)>0){
                        $arr    = array();
                        $i=0;
                        foreach($values as $value){
                            $list[$i]   = new stdClass();
                            $param  = new JRegistry($value);
                            $list[$i] -> type           = $rows -> type;
                            if(!empty($rows -> default_value))
                                $list[$i] -> default_value  = explode(',',$rows -> default_value);
                            else
                                $list[$i] -> default_value  = array(-1);

                            if(isset($artOptFields) && $artOptFields){
                                if(count($artOptFields)>0){
                                    foreach($artOptFields as $j => $item){
                                        if($param -> get('name') == $item -> value){
                                            $arr[]  = $i;
                                        }
                                    }
                                }
                            }
                            if(isset($arr))
                                $list[$i] -> default_value  = $arr;

                            $list[$i] -> name           = $param -> get('name');
                            $list[$i] -> value          = $param -> get('value');
                            $list[$i] -> target         = $param -> get('target');
                            $list[$i] -> editor         = $param -> get('editor');
                            $list[$i] -> image          = $param -> get('image');
                            $i++;
                        }
                        
                    }
                    
                }
                
            }
        //}
        return $list;
    }

    public function getFields(){

        $limit          = $this -> getState('limit');
        $limitstart     = $this -> getState('limitstart');
        $filter_state   = $this -> getState('filter_state');
        $search         = $this -> getState('filter_search');
        $filter_type    = $this -> getState('filter_type');
        $filter_group   = $this -> getState('filter_group');
        $order          = $this -> getState('filter_order');
        $order_dir      = $this -> getState('filter_order_Dir');
        $where          = null;
        $lists          = array();

        // get where
        if($filter_state){
            if($filter_state=='P')
                $where[] = 'f.published=1';
            elseif($filter_state=='U')
                $where[] = 'f.published=0';
        }
        else{
            $where[]    = 'f.published >= 0';
        }

        if($filter_type){
            if($filter_type!=0){
                $where[]    = 'f.type="'.$filter_type.'"';
            }
        }
        if($filter_group){
            if($filter_group!=-1){
                $where[]     = 'x.groupid ='.$filter_group;
            }
        }

        if($search)
            $where[]    = 'f.title LIKE "%'.$search.'%"';

        if($filter_type)
            $where[]    = 'f.type="'.$filter_type.'"';
        
        $order          = (!in_array($order,
            array('f.id','groupname','f.title','f.type','f.published')))?
            'f.ordering':($order);
        $where          = (count($where)>0)?' WHERE '.implode(' AND ',$where):'';
        $order_dir      = (in_array(strtoupper($order_dir),array('ASC','DESC'))?$order_dir:'ASC');
        $orderby        = ' ORDER BY '.$order.' '.strtoupper($order_dir);

        $query          = 'SELECT f.* FROM #__tz_portfolio_fields AS f'
                          .' LEFT JOIN #__tz_portfolio_xref AS x'
                          .' ON f.id=x.fieldsid'
                          .' INNER JOIN #__tz_portfolio_fields_group AS fg'
                          .' ON fg.id=x.groupid'
                          .$where
                          .' GROUP BY f.id';
        $total  =   0;
        $this -> db -> setQuery($query);

        if($query = $this -> db -> query()){
            $total = $this -> db -> getNumrows($query);
        }

        $this -> pagNav         = new JPagination($total,$limitstart,$limit);


        // Get groupid

        $query          = 'SELECT f.*,fg.name as groupname,x.groupid FROM #__tz_portfolio_fields AS f'
                          .' LEFT JOIN #__tz_portfolio_xref AS x'
                          .' ON f.id=x.fieldsid'
                          .' INNER JOIN #__tz_portfolio_fields_group AS fg'
                          .' ON fg.id=x.groupid'
                          .$where
                          .' GROUP BY f.id'
                          .$orderby;

        $this -> db -> setQuery($query,$this -> pagNav -> limitstart,$this -> pagNav -> limit);
        if(!$rows   = $this -> db -> loadObjectList()){
            $this -> setError($this -> db -> getErrorMsg());
            return false;
        }



        $i=0;
        foreach($rows as $row){
            //Get group name
            $where1     = array();
            $where1[]   = 'x.fieldsid='.$row -> id;
            $where1     = ' WHERE '.implode(' AND ',$where1);
            $query1       = 'SELECT DISTINCT fg.name,fg.id FROM #__tz_portfolio_fields AS f'
                          .' INNER JOIN #__tz_portfolio_xref AS x'
                          .' ON f.id=x.fieldsid'
                          .' LEFT JOIN #__tz_portfolio_fields_group AS fg'
                          .' ON fg.id=x.groupid'
                          .$where1
                            .' GROUP BY fg.id';
                          //.$orderby;

            $this -> db -> setQuery($query1);

            if(!$rows1 = $this -> db -> loadObjectList()){
                $this -> setError($this -> db -> getErrorMsg());
                return false;
            }
            $groupName  = array();
            foreach($rows1 as $row1){
                $groupName[]    = $row1 -> name;
            }
            $groupName  = implode(', ',$groupName);
            $rows[$i]-> groupname=$groupName;

            //Get group id
            $where1     = array();
            $where1[]   = 'x.fieldsid='.$row -> id;
            $where1     = ' WHERE '.implode(' AND ',$where1);
            $query1       = 'SELECT fg.id FROM #__tz_portfolio_fields AS f'
                          .' INNER JOIN #__tz_portfolio_xref AS x'
                          .' ON f.id=x.fieldsid'
                          .' LEFT JOIN #__tz_portfolio_fields_group AS fg'
                          .' ON fg.id=x.groupid'
                          .$where1
                            .' GROUP BY fg.id';

            $this -> db -> setQuery($query1);

            if(!$rows1 = $this -> db -> loadObjectList()){
                $this -> setError($this -> db -> getErrorMsg());
                return false;
            }
            $groupId  = array();
            foreach($rows1 as $row1){
                $groupId[]    = $row1 -> id;
            }
            $groupId  = implode(',',$groupId);

            // Set group id
            $rows[$i]-> groupid=$groupId;

            $i++;
        }
        return $rows;
    }

    function getPagination(){
        if(!$this->pagNav)
            return '';
        return $this->pagNav;
    }

    // Get Group id of category
    function getCatGroupId($catid=null){
        $where  = null;
        if($catid)
            $where  = ' WHERE t.catid='.$catid;
        $query  = 'SELECT t.groupid,c.title FROM #__tz_portfolio_categories AS t'
                  .' LEFT JOIN #__categories AS c ON c.id=t.catid'
                  .$where;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($rows = $db -> loadObject()){
            return $rows;
        }

        return false;
    }
    //Get Group id of Article
    function getArticleGroupid($articleId=null){
         $where  = null;
        if($articleId)
            $where  = ' WHERE x.contentid='.$articleId;
        $query  = 'SELECT c.catid,x.contentid,x.groupid FROM #__tz_portfolio_xref_content AS x'
                  .' LEFT JOIN #__content AS c ON c.id=x.contentid'
                  .$where;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($rows = $db -> loadObjectList()){
            return $rows;
        }

        return false;
    }

    protected function _saveArticleFields($groupid,$fieldsId,$articleId=null,$value = array(),$data = array()){
        $db = JFactory::getDbo();
        
        if(!$this -> _checkArticleFields($fieldsId,$articleId)){
            if(is_array($value)&& count($value)>0){
                foreach($value as $val){
                   $_value[] = '('.$articleId.','.$val.')';
                }
                if($_value){
                    $_value = implode(',',$_value);

                    $query  = 'INSERT INTO #__tz_portfolio(`contentid`,`fieldsid`,`value`,`images`)'
                              .' VALUES '.$_value;

                    $db -> setQuery($query);
                    if(!$db -> query()){
                        var_dump($db -> getErrorMsg());
                        die();
                    }
                }
            } //if have $value

        }// if not have article with this field in table tz_portfolio
        else{
            if(count($value)>0){
                foreach($value as $item){
                    $arr    = explode(',',$item);
                    $m  = array_keys($data,str_replace('"','',$arr[1]));
                    $data[$m[0]]    = str_replace('"','',$data[$m[0]]);
                    $query  = 'UPDATE #__tz_portfolio SET value="'.$data[$m[0]].'",images='.$arr[2]
                              .' WHERE fieldsid='.$arr[0].' AND contentid='.$articleId;
                    $db -> setQuery($query);
                    if(!$db -> query()){
                        var_dump($db -> getErrorMsg());
                        die();
                    }
                }
            }
        }
    }

    function saveArticleFields($groupid,$fieldsId,$value = array(),$data = array()){

        if($listArticle = $this -> getArticleGroupid()){
            foreach($listArticle as $item){

                if($item -> groupid == 0){
                    $_groupid   = $this -> getCatGroupId($item -> catid);
                    if($_groupid && in_array($_groupid -> groupid,$groupid)){
                        $this -> _saveArticleFields($groupid,$fieldsId,$item -> contentid,$value,$data);
                    }

                }// if article's groupid = 0
                else{
                    if(in_array($item -> groupid,$groupid)){
                        $this -> _saveArticleFields($groupid,$fieldsId,$item -> contentid,$value,$data);
                    }
                }
            }

        }
        return true;
    }

    public function saveFields(&$task){

        $post                   = array();
        //$post                   = JRequest::get('post');
        $post['description']        = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW );
        $post['option_name']        = JRequest::getVar('option_name',array(),'post','array');
        $post['option_value']       = JRequest::getVar('option_value',array(),'post','array');
        $post['option_editor']      = JRequest::getString('option_editor');
        $post['option_target']      = JRequest::getVar('option_target',array(),'post','array');
        $post['fieldsgroup']        = JRequest::getVar('fieldsgroup',array(),'post','array');
        $post['editor']             = JRequest::getVar('editor',array(),'post','array');

        $icon['image']              = JRequest::getVar('option_icon',array(),'post','array', JREQUEST_ALLOWRAW);

        $data['id']                 = $this -> cids[0];
        $data['name']               = JRequest::getCmd('name',null);
        $data['title']              = JRequest::getString('title',null);
        $data['type']               = JRequest::getCmd('type',null);
        $data['published']          = JRequest::getCmd('published',null);
        $default['default_value']   = '';

        $default                = JRequest::getVar('default',array(),'post','array');

        if($default){
            $_default    = implode(',',$default);
            $data['default_value']  = $_default;
        }

        foreach($icon['image'] as $i => $item){
            if(empty($icon['image'][$i])){
                $icon['image'][$i] = null;
            }
        }

        $groupid    = array();
        if(isset($post['fieldsgroup'])){

            for($i=0;$i<count($post['fieldsgroup']);$i++){
                if($post['fieldsgroup'][$i]!=-1)
                    $groupid[]      = (int) $post['fieldsgroup'][$i];
            }

        }
        $data['description']    = $post['description'];

        switch ($data['type']){
            case 'textfield':
                $data['value']  = '[{"name":"'.$post['option_value'][0].'","value":"0"'
                                  .',"target":"null","editor":"null","image":"'
                                  .$icon['image'][0].'"}]';
                $defautValue[]  = $post['option_value'][0];
                break;
            case 'textarea':
                $data['value']  = '[{"name":"'.$post['option_value'][0]
                                  .'","value":"0","target":"null","editor":"'
                                  .$post['option_editor'].'","image":"'
                                  .$icon['image'][0].'"}]';
                $defautValue[]  = $post['option_value'][0];
                break;
            case 'select':
                case 'multipleSelect':
                case 'radio':
                case 'checkbox':
                    $values     = array();
                    if(isset($post['option_name'])){
                        $count  = 0;
                        for($i=0;$i<count($post['option_name']);$i++){
                            if(isset($post['option_name'][$i]) && !empty($post['option_name'][$i])){
                                $values[]   = '{"name":"'.$post['option_name'][$i]
                                    .'","value":"'.$count.'","target":"null","editor":"null","image":"'
                                              .$icon['image'][$i].'"}';
                                if(in_array($i,$default)){
                                    $defautValue[$i]   =  $post['option_name'][$i];
                                }
                                
                                $count++;
                            }
                        }
                        $values             = '['.implode(',',$values).']';
                    }
                        $data['value']      = $values;
            break;
            case 'link':

                $data['value']  = '[{"name":"'.$post['option_name'][0]
                                  .'","value":"'.$post['option_value'][0]
                                  .'","target":"'.$post['option_target'][0].'","editor":"null","image":"'
                                  .$icon['image'][0].'"}]';

                    if(empty($post['option_name'][0]))
                        $title  = $post['option_value'][0];
                    else
                         $title  = $post['option_name'][0];
                $defautValue[]  = htmlspecialchars('<a href="'.$post['option_value'][0].'" target="'.$post['option_target'][0].'">'.$title.'</a> ');
                break;
            case 'file':
            case 'date':
            default:
                $data['value']='[{"name":"null","value":"null","target":"null","editor":"null","image":"null"}]';
                break;

        }

        // Save
        $row    = &JTable::getInstance('Fields','Table');
        if(!$row -> bind($data)){
            $this -> setError($row -> getError());
            return false;
        }

        // if new item, order last in appropriate group
        if(!$row -> id)
            $row -> ordering = $row -> getNextOrder();

        if(count($groupid)>0){
             //Delete xref with fieldsid
            if($row -> id){
                $query  = 'DELETE FROM #__tz_portfolio_xref WHERE fieldsid ='.$row -> id;
                $this -> db -> setQuery($query);

                if(!$this -> db -> query()){
                    $this -> setError($this -> db -> getErrorMsg());
                        return false;
                }
            }
            if(!$row -> store()){
                $this -> setError($row -> getError());
                return false;
            }

            if($row -> id){
                if(isset($defautValue) && !empty($defautValue)){

                    foreach($defautValue as $i => $val){
                        $arr[]  = $row -> id.',"'.$val.'","'.$icon['image'][$i].'"';
                    }
                    $this -> saveArticleFields($groupid,$row -> id,$arr,$defautValue);
                }
            }
//             die();

            foreach($groupid as $item){
                    $value[] = '('.$row -> id.','.$item.')';
            }

            //Save into xref
            $value     = implode(',',$value);
            $query      = 'INSERT INTO #__tz_portfolio_xref(`fieldsid`,`groupid`)'
                          .' VALUES'.$value;

            $this -> db -> setQuery($query);

            if(!$this -> db -> query()){
                $this -> setError($this -> db -> getErrorMsg());
                return false;
            }

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

    public function publishFields($cids = array(),$state = null){
        if(count($cids)){

            JArrayHelper::toInteger($cids);
            $count  = count($cids);
            $cids   = implode(',',$cids);

            $query  = 'UPDATE #__tz_portfolio_fields'
                .' SET published='.$state
                .' WHERE id IN('.$cids.')';

            $this -> db -> setQuery($query);

            if(!$this -> db -> query()){
                $this -> setError($this -> db -> getErrorMsg());
                return false;
            }
            else{
                $this -> msg = ($state == 1)?JText::sprintf('COM_TZ_PORTFOLIO_TAGS_COUNT_PUBLISHED',$count):'';
                $this -> msg = ($state == 0)?JText::sprintf('COM_TZ_PORTFOLIO_TAGS_COUNT_UNPUBLISHED',$count):'';
            }
        }
        return true;
    }

    public function removeFields($cids = array()){
        if(count($cids)>0){
            $count      = count($cids);
            $cids       = implode(',',$cids);

            $query  = 'DELETE FROM #__tz_portfolio'
                .' WHERE fieldsid IN('.$cids.')';
            $this -> db -> setQuery($query);
            if(!$this -> db -> query()){
                $this -> setError($this -> db -> getErrorMsg());
                return false;
            }

            $query  = 'DELETE FROM #__tz_portfolio_xref'
                .' WHERE fieldsid IN('.$cids.')';
            $this -> db -> setQuery($query);
            if(!$this -> db -> query()){
                $this -> setError($this -> db -> getErrorMsg());
                return false;
            }

            $query  = 'DELETE FROM #__tz_portfolio_fields'
                .' WHERE id IN('.$cids.')';

                $this -> db -> setQuery($query);

                if(!$this -> db -> query()){
                    $this -> setError($this -> db -> getErrorMsg());
                    return false;
                }
            else{
                $this -> msg = $count
                    .' fields successfully deleted';
            }
        }
        return true;
    }

    public function saveOrderFields($cids = array(),$order = array()){
        if(count($cids)>0){
            $row = &JTable::getInstance('Fields','Table');

            for($i = 0; $i<count($cids);$i++){
                $row -> load($cids[$i]);


                $grouping[] = $row -> id;

                if($row -> ordering != $order[$i]){
                    $row -> ordering = $order[$i];

                    if(!$row -> store()){
                        $this -> setError($row -> getError() );
                        return false;
                    }
                }
            }

            // execute updateOrder for each parent group
            $grouping   = array_unique($grouping);
            foreach($grouping as $group){
                $row -> reorder();
            }
            $this -> msg = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
        }
        return true;
    }

    public function moveOrderFields($cids = array(),$des = null){
        if(count($cids)>0){
            $row    = &JTable::getInstance('Fields','Table');
            if(!$row -> load($cids[0])){
                $this -> setError($row -> getError());
                return false;
            }

            if($row -> move($des,'groupid='.$row -> groupid.' AND published>=0')){
                $this -> msg = JText::_('Ordering successfully moved.');
            }
            else{
                $this -> setError($row -> getError());
                return false;
            }
        }
        else{
            $this -> setError(JText::_('Invalid fields id'));
            return false;
        }
        return true;
    }
}
