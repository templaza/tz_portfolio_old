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
defined('_JEXEC') or die;

class TZ_PortfolioTablePlugin extends JTable
{
     /** @var int Primary key */
    var $id 		= null;
    /** @var int */
    var $contentid  = null;
    /** @var int */
    var $pluginid   = null;
    /** @var string */
    var $params     = null;

    function __construct(&$db) {
        parent::__construct('#__tz_portfolio_plugin','id',$db);

    }

    public function deleteItem($contenid){
         if($contenid){
             if(is_array($contenid)){
                 $contenid  = implode(',',$contenid);
             }
            $query  = 'DELETE FROM #__tz_portfolio_plugin'
                .' WHERE contentid IN('.$contenid.')';
            $db     = JFactory::getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                return false;
            }
        }
    }

}
?>