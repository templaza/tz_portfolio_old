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

class TableFields extends JTable
{
     /** @var int Primary key */
    var $id 				= null;
    /** @var string */
    var $name 				= '';
    /** @var string */
    var $title 				= null;
    /** @var string */
    var $type 				= null;
    /** @var string */
    var $value 				= null;
    /** @var string */
    var $default_value		= null;
    /** @var int */
    var $ordering   		= null;
    /** @var int */
    var $published			= null;
    /** @var int */
//    var $groupid    		= null;
    /** @var string*/
    var $description		= null;

    function __construct(&$db) {
        parent::__construct('#__tz_portfolio_fields','id',$db);

    }

    public function publish($pks = null,$state=1,$userId = 0){
        $k      = $this -> _tbl_key;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else
            {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        $query  = $this -> _db -> getQuery(true);
        $query -> update($this -> _db -> quoteName($this -> _tbl));
        $query -> set($this->_db->quoteName('published') . ' = ' . (int) $state);
        $query -> where('(' . $where . ')');
        $this -> _db -> setQuery($query);

        $this -> _db -> execute();

        return true;
    }
    
}