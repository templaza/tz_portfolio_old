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

jimport('joomla.application.component.modeladmin');
jimport( 'joomla.filesystem.folder');

class TZ_PortfolioModelPlugin extends JModelAdmin{

    protected $pluginGroup    = 'tz_portfolio';
    
    public function __construct($config=array()){
        parent::__construct($config);
    }

    function populateState(){
        parent::populateState();

        $this -> setState('com_tz_portfolio.plugin.articleId',null);
        $this -> setState('com_tz_portfolio.plugin.pluginid',null);

    }
    
    public function getForm($data = array(), $loadData = true)
	{
        $forms  = new stdClass();
        if($plugins    = $this -> _getPluginItem()){
            foreach($plugins as $plugin){

                $this -> setState('com_tz_portfolio.plugin.pluginid',$plugin -> extension_id);
                // Add the search path for the plugin config.xml file.
                JForm::addFormPath($plugin -> path);

                // Get parameters of a plugin in config.xml file.
                if($form = $this->loadForm(
                    'com_tz_portfolio.plugin.'.($plugin -> name),
                    'config',
                    array('control' => 'tzplgform['.$plugin -> element.']', 'load_data' => $loadData),
                    true,
                    '/config'
                )){
                    $forms -> {$plugin -> element} = $form;
                }
            }

        }

        if(empty($forms)){
            return false;
        }

        return $forms;
    }

    protected function loadFormData()
	{
        // Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tz_portfolio.plugin.data', array());

        if(empty($data) || count($data) == 0){
            if($this -> getItem()){
                $data   = $this -> getItem();

            }
        }
        return $data;
	}

    protected function _getPluginQuery(){
        $db = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> where('`enabled`=1');
        $query -> where('`type`='.$db -> quote('plugin'));
        $query -> where('`folder`='.$db -> quote($this -> pluginGroup));
        $query -> from($db -> quoteName('#__extensions'));
        return $query;
    }

    function getPluginItems(){
        $db = JFactory::getDbo();
        $db -> setQuery($this -> _getPluginQuery());
        if($rows = $db -> loadObjectList()){
            return $rows;
        }
        return false;
    }

    function getItem($pluginId=null){
        if($this -> getState('com_tz_portfolio.plugin.pluginid')){
            $pluginId   = $this -> getState('com_tz_portfolio.plugin.pluginid');
        }
        if($pluginId){
            $db     = $this -> getDbo();
            $where  = null;
            if($contentid = $this -> getState('com_tz_portfolio.plugin.articleId')){
                $where  = ' AND contentid='.$contentid;

                $query  = 'SELECT * FROM'.$db -> quoteName('#__tz_portfolio_plugin')
                          .' WHERE pluginid ='.$pluginId
                          .$where;
                $db -> setQuery($query);
                if($row = $db -> loadObject()){
                    $items  = new JRegistry();

                        if(!empty($row -> params)){
                            $items -> loadString($row -> params);
                            if(count($items) > 0){
                                return $items;
                            }
                        }
                }
            }
        }
        return null;
    }

    //Get plugins in group with have config.xml file
    protected function _getPluginItem(){
        if($plugins    = $this -> getPluginItems()){
            foreach($plugins as $i => &$plugin){
                $pPath   = JPATH_SITE.'/plugins/'.$this -> pluginGroup.'/'.$plugin -> element;
                if(!JFile::exists($pPath.'/'.'config.xml')){
                    unset($plugins[$i]);
                }
                $plugin -> path = $pPath;
            }
            return $plugins;
        }
        return false;
    }

    function save($data){
        if($data){
            
            //Get contentid
            $contentid  = $this -> getState('com_tz_portfolio.plugin.articleId',0);

            if($plugins = $this -> _getPluginItem()){
                $db         = JFactory::getDbo();

                $plgParams  = new JRegistry();

                $values     = null;
                foreach($plugins as $plugin){
                    if($data[$name = $plugin -> element]){
                        $_data  = null;
                        $plgParams -> loadArray($data[$name]);

                        $values[]   =  '('.$contentid.','.$plugin -> extension_id.','.$db ->quote($plgParams -> toString()).')';
                    }
                }
                if($values){
                    //Delete item in table tz_portfolio_plugin with contentids;
                    $this -> deleteItem($contentid);

                    //Insert items in table tz_portfolio_plugin
                    $query  = 'INSERT INTO '.$db -> quoteName('#__tz_portfolio_plugin')
                              .'(`contentid`,`pluginid`,`params`)'
                              .' VALUES '.implode(',',$values);
                    $db -> setQuery($query);
                    if($db -> query()){
                        return true;
                    }
                }
            }
        }
        return true;
    }

    //Delete Item in table
    function deleteItem($contentid=null){
        if($contentid){
            if(is_array($contentid)){
                $contentid  = implode(',',$contentid);
            }
            $db = JFactory::getDbo();
            $query  = 'DELETE FROM '.$db -> quoteName('#__tz_portfolio_plugin')
                      .' WHERE contentid IN('.$contentid.')';
            $db -> setQuery($query);
            if($db -> query()){
                return true;
            }
        }
        return true;
    }


    //Check item with contentid
    function _checkItem($contentid = null){
        if($contentid){
            if(is_array($contentid)){
                $contentid  = implode(',',$contentid);
            }
            $db     = JFactory::getDbo();
            $query  = 'SELECT COUNT(*) FROM '.$db -> quoteName('#__tz_portfolio_plugin')
                      .' WHERE contentid IN('.$contentid.')';
            $db -> setQuery($query);
            if($count = $db -> loadResult()){

                return $count;
            }
        }
        return false;
    }
}
?>