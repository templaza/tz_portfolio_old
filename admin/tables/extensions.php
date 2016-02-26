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

class TZ_PortfolioTableExtensions extends JTable
{
    /** @var int Primary key */
    var $id = null;
    /** @var string */
    var $name = null;
    /** @var int */
    var $protected = null;
    /** @var string */
    var $manifest_cache = null;
    /** @var int */
    var $published = null;
    /** @var int */
    var $access = null;
    /** @var string */
    var $params = null;

    function __construct(&$db)
    {
        parent::__construct('#__tz_portfolio_extensions', 'id', $db);
    }
}
?>