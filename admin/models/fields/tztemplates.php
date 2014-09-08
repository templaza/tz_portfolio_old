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

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 *
 * Provides a pop up date picker linked to a button.
 * Optionally may be filtered to use user's or server's time zone.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldTZTemplates extends JFormFieldList
{
    protected $type = 'TZTemplates';

    protected function getOptions()
    {
        $options = array();

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from('#__tz_portfolio_templates');
        $db -> setQuery($query);
        if($items = $db -> loadObjectList()){
            foreach($items as $i => $item){
                $options[$i] = new stdClass();
                $options[$i] -> text    = $item -> title;
                $options[$i] -> value   = $item -> id;
            }
        }

        return array_merge(parent::getOptions(), $options);
    }
}