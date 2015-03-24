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

jimport('joomla.application.component.modeladmin');

class TZ_PortfolioModelConfig extends JModelAdmin
{
    public function getForm($data = array(), $loadData = true)
    {
        // Add the search path for the plugin config.xml file.
        JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio');

        $form = $this->loadForm('com_tz_portfolio.config', 'config',
            array('control' => 'jform', 'load_data' => $loadData),false,'/config');
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function saveConfig($data){
        if($data){
            $com        = JComponentHelper::getComponent('com_tz_portfolio');
            $c_params   = $com -> params;
            foreach($data as $key => $value){
                $c_params -> set($key,$value);
            }
            $db         = JFactory::getDbo();
            $query      = $db -> getQuery(true);
            $query -> update($db -> quoteName('#__extensions'));
            $query -> set($db -> quoteName('params').'='.$db -> quote($c_params -> toString()));
            $query -> where($db -> quoteName('extension_id').'='.$com -> id);
            $db -> setQuery($query);
            return $db -> query();
        }
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_tz_portfolio.config.data', array());

        if(empty($data) || count($data) == 0){

            $com                            = JComponentHelper::getComponent('com_tz_portfolio');
            $c_params                       = $com -> params;
            $data['tz_image_xsmall']        = $c_params -> get('tz_image_xsmall');
            $data['tz_image_small']         = $c_params -> get('tz_image_small');
            $data['tz_image_medium']        = $c_params -> get('tz_image_medium');
            $data['tz_image_large']         = $c_params -> get('tz_image_large');
            $data['tz_image_xlarge']        = $c_params -> get('tz_image_xlarge');
            $data['tz_image_gallery_xsmall']= $c_params -> get('tz_image_gallery_xsmall');
            $data['tz_image_gallery_small'] = $c_params -> get('tz_image_gallery_small');
            $data['tz_image_gallery_medium']= $c_params -> get('tz_image_gallery_medium');
            $data['tz_image_gallery_large'] = $c_params -> get('tz_image_gallery_large');
            $data['tz_image_gallery_xlarge']= $c_params -> get('tz_image_gallery_xlarge');
        }
        return $data;
    }
}