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

jimport('joomla.application.component.model');

class TZ_PortfolioModelExtraFields extends JModelLegacy
{
    var $_param  = null;

    function populateState(){
        $pk = JRequest::getInt('id');
        $this -> setState('article.id',$pk);
        $this -> setState('category.id',null);
        $this -> setState('params',null);
        $this -> setState('orderby',null);
        $this -> setState('filter.option.order',null);
    }

    public function getExtraFields($articleId=null){
        $_articleId     = $this -> getState('article.id');
        if($articleId)
            $_articleId = $articleId;

        $where      = null;
        $params     = $this -> getState('params');

        if($params -> get('tz_fieldsid')){
            $fieldsId   = $params -> get('tz_fieldsid');
            if($fieldsId && !empty($fieldsId)){
                if(is_array($fieldsId)){
                    if(count($fieldsId) > 0){
                        if(empty($fieldsId[0])){
                            array_shift($fieldsId);
                        }

                        $fieldsId   = implode(',',$fieldsId);
                    }
                }
                else{
                    $fieldsId   = null;
                }

                if($fieldsId)
                    $where  = ' AND t.fieldsid IN('.$fieldsId.')';

            }
        }
        $orderBy    = null;
        $order      = null;

        if($this -> getState('orderby')){
            $order  = $this -> getState('orderby');
        }
        else{
            $order  = $params -> get('fields_order');
        }
        switch($order){
            default:
                $orderBy    = 'f.id DESC';
                break;
            case 'rid':
                $orderBy    = 'f.id DESC';
                break;
            case 'id':
                $orderBy    = 'f.id ASC';
                break;
            case 'alpha':
                $orderBy    = 'f.title ASC';
                break;
            case 'ralpha':
                $orderBy    = 'f.title DESC';
                break;
            case 'order':
                $orderBy    = 'f.ordering ASC';
                break;
        }

        if($this ->  getState('filter.option.order')){
            $orderBy    .= ','.$this -> getState('filter.option.order');
        }

        if($orderBy){
            $orderBy    = ' ORDER BY '.$orderBy;
        }

        $data   = array();
        $query  = 'SELECT t.*,f.title FROM #__tz_portfolio AS t'
                  .' LEFT JOIN #__tz_portfolio_fields AS f ON f.id=t.fieldsid'
                  .' LEFT JOIN #__content AS c ON c.id=t.contentid'
                  .' WHERE c.state=1 AND t.contentid='.$_articleId
                  .$where
                  .$orderBy;

        $db     = JFactory::getDbo();
        $db -> setQuery($query);

        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }
        $rows   = $db -> loadObjectList();

        if(count($rows)>0){
            $k  = 0;
            for($i = 0;$i < count($rows);$i ++){
                $tg     = array();
                $images = array();
                $count  = 0;

                for($j=0;$j<count($rows);$j++){
                    if(($rows[$i] -> fieldsid) == ($rows[$j] -> fieldsid) && ($rows[$i] -> contentid) == ($rows[$j] -> contentid)){
                        $tg[$count]     = $rows[$j] -> value;
                        $images[$count] = $rows[$j] -> images;
                        $count++;
                        $i=$j;
                    }
                }
                $data[$k]   = new stdClass();

                $data[$k] -> id             = $rows[$i] -> id;
                $data[$k] -> contentid      = $rows[$i] -> contentid;
                $data[$k] -> fieldsid       = $rows[$i] -> fieldsid;
                $data[$k] -> title          = strip_tags($rows[$i] -> title);
                $data[$k] -> value          = $tg;
                $data[$k] -> images         = $images;


                $k++;
            }
        }

        return $data;
    }

    function _checkArticleGroupId($articleId=null){
        if($articleId){
            $where  =' WHERE contentid='.$articleId;
        }
        $query  = 'SELECT groupid FROM #__tz_portfolio_xref_content'
                  .$where;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($row = $db -> loadObject()){
            return $row;
        }
        return false;
    }

    function getCatParams(){
        $query  = 'SELECT * FROM #__categories'
                  .' WHERE published=1 AND id='.$this -> getState('category.id');
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($row = $db -> loadObject()){
            return $row;
        }
        return false;
    }

    function getParams(){
        $catId          = $this -> getState('category.id');
//        $category       = JModel::getInstance('Category','TZ_PortfolioModel',array('ignore_request' => true));
//        $category -> setState('category.id',$catId);
//        $catParams      = new JRegistry($category -> getCategory() -> params);

        $catParams      = new JRegistry($this -> getCatParams() -> params);

        $groupid        = $this -> _checkArticleGroupId($this -> getState('article.id'));
        if($groupid){
            $groupid =  $groupid -> groupid;
        }

        $extraParams    = JComponentHelper::getParams('com_tz_portfolio');

        if($catParams -> get('show_extra_fields') != '')
            $extraParams -> set('show_extra_fields',$catParams -> get('show_extra_fields'));
        if($catParams -> get('field_show_type') != '')
            $extraParams -> set('field_show_type',$catParams -> get('field_show_type'));
        if($catParams -> get('field_use_resize')!= '')
            $extraParams -> set('field_use_resize',$catParams -> get('field_use_resize'));
        if($catParams -> get('field_width') != '')
            $extraParams -> set('field_width',$catParams -> get('field_width'));
        if($catParams -> get('field_height') != '')
            $extraParams -> set('field_height',$catParams -> get('field_height'));
        if($catParams -> get('field_crop') != '')
            $extraParams -> set('field_crop',$catParams -> get('field_crop'));

        if($groupid == 0){
            $extraParams -> set('tz_fieldsid',$catParams -> get('tz_fieldsid'));
        }

        return $extraParams;
    }

}