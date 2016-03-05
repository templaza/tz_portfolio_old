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

class TZ_PortfolioTableTemplates extends JTable
{
    /** @var int Primary key */
    var $id 				= null;
    /** @var string */
    var $template			= null;
    /** @var string */
    var $title 				= null;
    /** @var int */
    var $home  		        = null;
    /** @var int */
    var $protected	        = null;
    /** @var string*/
    var $layout     		= null;
    /** @var string*/
    var $params		        = null;

    function __construct(&$db) {
        parent::__construct('#__tz_portfolio_templates','id',$db);
    }

    public function hasData(){
        $query = $this->_db->getQuery(true)
            ->select('COUNT(*)')
            ->from($this->_tbl);
        $this->_db->setQuery($query);
        $count = $this->_db->loadResult();
        if($count > 0){
            return true;
        }
        return false;
    }

    public function hasHome(){
        return $this -> _hasHome();
    }

    protected function _hasHome(){
        if($this -> hasData()){
            $query  = $this -> _db -> getQuery(true)
                -> select('COUNT(*)')
                -> from($this -> _tbl)
                -> where($this -> _db -> quoteName('home').'= 1');
            $this -> _db -> setQuery($query);
            $count  = $this -> _db -> loadResult();
            if($count){
                return true;
            }
        }
        return false;
    }
    public function getHome(){
        if($this -> _hasHome()){
            $query  = $this -> _db -> getQuery(true)
                -> select('*')
                -> from($this -> _tbl)
                -> where($this -> _db -> quoteName('home').'= 1');
            $this -> _db -> setQuery($query);
            if(!$data = $this -> _db -> loadObject()){
                $this->setError($this -> _db -> getErrorMsg());
                return false;
            }
            foreach($data as $key => $val){
                $this -> set($key,$val);
            }
            return $data;
        }
        return null;
    }

    public function delete($pk = null)
    {
        $k = $this->_tbl_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        if (!is_null($pk))
        {
            $query = $this->_db->getQuery(true)
                ->from('#__tz_portfolio_templates')
                ->select('id')
                ->where('template=' . $this->_db->quote($this->template));
            $this->_db->setQuery($query);
            $results = $this->_db->loadColumn();

            if (count($results) == 1 && $results[0] == $pk)
            {
                $this->setError(JText::_('COM_TZ_PORTFOLIO_TEMPLATE_STYLE_ERROR_CANNOT_DELETE_LAST_STYLE'));

                return false;
            }
        }

        return parent::delete($pk);
    }
}