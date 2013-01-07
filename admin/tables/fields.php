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
    var $name 				= null;
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
    
}