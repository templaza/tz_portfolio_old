<?php
/*------------------------------------------------------------------------

# JVisualContent Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.uri.uri');

class TZ_PortfolioUri extends JUri{
    protected static $jsc_path = 'components/com_tz_portfolio';

    public static function base($pathonly = false,$admin=false)
    {
        $base       = parent::base($pathonly);
        if($pathonly){
            $base   .= '/'.self::$jsc_path;
        }else{
            $base   .= self::$jsc_path;
        }
        return $base;
    }

    public static function root($pathonly = false, $path = null,$admin=false)
    {
        $_path  = $path;
        if(!is_null($_path)){
            $_path   = trim($path);
            if(empty($_path)){
                $_path   = null;
            }
        }
        $root   = parent::root($pathonly,$_path);
        $jsc_path   = self::$jsc_path;
        if($admin){
            $jsc_path   = 'administrator/'.self::$jsc_path;
        }
        if($pathonly){
            $root   .= '/'.$jsc_path;
        }else{
            $root   .= $jsc_path;
        }
        return $root;
    }
}