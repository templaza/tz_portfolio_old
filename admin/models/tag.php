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

class TZ_PortfolioModelTag extends JModelAdmin
{

    public function populateState(){
        parent::populateState();
    }

    public function getTable($type = 'Tags', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }
    public function publish(&$pks,$value=1){
        $table  = $this -> getTable();
        if(!$table -> publish($pks,$value)){
            $this -> setError($table -> getError());
            return false;
        }
        return true;
    }

    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        if (empty($data)) {
            $data = $this->getItem();
            if(isset($data -> attribs) && $data -> attribs){
                $attribs    = new JRegistry($data -> attribs);
                $data -> attribs    = $attribs -> toArray();
            }
        }

        return $data;
    }

    function getItem($pk = null){
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table  = $this -> getTable();

        if ($pk > 0)
        {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = JArrayHelper::toObject($properties, 'JObject');

        if (property_exists($item, 'params'))
        {
            $registry = new JRegistry;
            $registry->loadString($item->params);
            $item->params = $registry->toArray();
        }

        return $item;
    }

    protected function _prepareTable($table){
        if($table -> name){
            $table -> name   = str_replace(array(',',';','\'','"','.','?'
            ,'/','\\','<','>','(',')','*','&','^','%','$','#','@','!','-','+','|','`','~'),' ',$table -> name);
            $table -> name  = trim($table -> name);
            $table -> name  = mb_strtolower($table -> name,'UTF-8');
            $table -> published = ($table -> published == 'P')?1:0;
        }
        if(is_array($table -> attribs)){
            $attribs    = new JRegistry($table -> attribs);
            $table -> attribs   = $attribs -> toString();
        }
    }

    public function delete(&$pks){
        // Initialise variables.
        $dispatcher = JDispatcher::getInstance();
        $pks = (array) $pks;
        $table = $this->getTable();

        // Iterate the items to delete each one.
        foreach ($pks as $i => $pk)
        {

            if ($table->load($pk))
            {

                if ($this->canDelete($table))
                {

                    if (!$table->delete($pk))
                    {
                        $this->setError($table->getError());
                        return false;
                    }



                }
                else
                {

                    // Prune items that you can't change.
                    unset($pks[$i]);
                    $error = $this->getError();
                    if ($error)
                    {
                        JError::raiseWarning(500, $error);
                        return false;
                    }
                    else
                    {
                        JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
                        return false;
                    }
                }

            }
            else
            {
                $this->setError($table->getError());
                return false;
            }
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

    function save($data){

        $olname['old_name'] = mb_strtolower($data['old_name']);
        $data   = array_diff_key($data,$olname);
        $table  = $this -> getTable();
        if(!isset($data['attribs'])){
            $data['attribs']    = '';
        }

        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Allow an exception to be thrown.
        try
        {
            if(!$table -> bind($data)){
                $this -> setError($table -> getError());
                return false;
            }

            // Prepare the row for saving
            $this -> _prepareTable($table);

            // Check tag name
            if(!$table -> check($olname['old_name'])){
                $this -> setError($table -> getError());
                return false;
            }

            // Save tag
            if(!$table -> store()){
                $this -> setError($table -> getError());
                return false;
            }
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        $pkName = $table->getKeyName();

        if (isset($table->$pkName))
        {
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        $this->setState($this->getName() . '.new', $isNew);

        return true;
    }
}