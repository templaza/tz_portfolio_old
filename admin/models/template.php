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

jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');

class TZ_PortfolioModelTemplate extends JModelAdmin
{
    protected function populateState(){
        parent::populateState();

        $this -> setState('template.id',JRequest::getInt('id'));
        $this -> setState('content.id',null);
        $this -> setState('category.id',null);
    }
    public function getTable($type = 'Templates', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio.template', 'template', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
//        $data = JFactory::getApplication()->getUserState('com_tz_portfolio.edit.template.data', array());

        if (empty($data)) {
            $data = $this->getItem();
            $data -> categories_assignment = $this -> getCategoriesAssignment();
            $data -> articles_assignment = $this -> getArticlesAssignment();
        }

        return $data;
    }

    public function getCategoriesAssignment($pk = null){
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        if($pk > 0){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('catid');
            $query -> from('#__tz_portfolio_categories');
            $query -> where('template_id = '.$pk);
            $db -> setQuery($query);
            if($rows = $db -> loadColumn()){
                return implode(',',$rows);
            }
        }
        return null;
    }

    public function getArticlesAssignment($pk = null){
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        if($pk > 0){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('contentid');
            $query -> from('#__tz_portfolio_xref_content');
            $query -> where('template_id = '.$pk);
            $db -> setQuery($query);
            if($rows = $db -> loadColumn()){
                return implode(',',$rows);
            }
        }
        return null;
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
//            $registry = new JRegistry;
//            $registry->loadString($item->params);
//            $item->params = $registry->toObject();
            $item->params = json_decode($item -> params);
        }

        return $item;
    }

    protected function generateNewTitle($category_id, $alias, $title)
    {
        // Alter the title
        $table = $this->getTable();

        while ($table->load(array('title' => $title)))
        {
            $title = JString::increment($title);
        }

        return $title;
    }

    public function save($data)
    {
        $app        = JFactory::getApplication();

        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $dispatcher = JEventDispatcher::getInstance();
        }else{
            $dispatcher = JDispatcher::getInstance();
        }
        $table = $this->getTable();

        $post   = JRequest::get();
        $articlesAssignment         = null;
        $articlesAssignmentOld      = null;
        $categoriesAssignment       = null;
        $categoriesAssignmentOld    = null;

        $data['params'] = '';
        if(isset($post['jform']['attrib']) && $attrib = $post['jform']['attrib']){
            $data['params'] = json_encode($attrib);
        }else{
            $pathfile   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/views/template/tmpl/default.json';
            if(JFile::exists($pathfile)){
                $data['params'] = file_get_contents($pathfile);
            }
        }
        if(!$data['id'] || $data['id'] == 0){
            $data['title']  = $this ->generateNewTitle(null,null,$data['title']);
        }

        if(!$table -> hasHome()){
            $data['home']   = '1';
        }

        if ($app->input->get('task') == 'save2copy')
        {
            unset($data['articles_assignment']);
            unset($data['categories_assignment']);
            unset($post['jform']['articles_assignment_old']);
            unset($post['jform']['categories_assignment_old']);

        }

        if(isset($data['articles_assignment'])){
            $articlesAssignment  = $data['articles_assignment'];
            unset($data['article_assignment']);
        }

        if(isset($post['jform']['articles_assignment_old'])){
            $articlesAssignmentOld  = $post['jform']['articles_assignment_old'];
        }

        if(isset($post['jform']['categories_assignment_old'])){
            $categoriesAssignmentOld    = $post['jform']['categories_assignment_old'];
        }

        if(isset($data['categories_assignment']) && count($data['categories_assignment'])){
            $categoriesAssignment  = $data['categories_assignment'];
            unset($data['categories_assignment']);
        }

        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');

        // Allow an exception to be thrown.
        try
        {
            // Load the row if saving an existing record.
            if ($pk > 0)
            {
                $table->load($pk);
                $isNew = false;
            }

            // Bind the data.
            if (!$table->bind($data))
            {
                $this->setError($table->getError());

                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);

            // Check the data.
            if (!$table->check())
            {
                $this->setError($table->getError());
                return false;
            }

            // Trigger the onContentBeforeSave event.
            $result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));

            if (in_array(false, $result, true))
            {
                $this->setError($table->getError());
                return false;
            }

            // Store the data.
            if (!$table->store())
            {
                $this->setError($table->getError());
                return false;
            }

            if($data['home'] == '1'){
                $this -> setHome($table -> id);
            }

            $db     = $this -> getDbo();
            // Assign article with this template;
            if(!empty($articlesAssignmentOld) && count($articlesAssignmentOld)){
                $query  = $db -> getQuery(true);
                $query -> update($db -> quoteName('#__tz_portfolio_xref_content'));
                $query -> set($db -> quoteName('template_id').'= 0');
                $query -> where($db -> quoteName('contentid').' IN('.implode(',',$articlesAssignmentOld).')');
                $db -> setQuery($query);
                $db -> execute();
            }

            if(!empty($articlesAssignment) && count($articlesAssignment)){
                $articlesAssignment = array_unique($articlesAssignment);
                $query  = $db -> getQuery(true);
                $query -> select('contentid');
                $query -> from($db -> quoteName('#__tz_portfolio_xref_content'));
                $query -> where($db -> quoteName('contentid').' IN('
                    .implode(',',$articlesAssignment).')');
                $db -> setQuery($query);

                if(!$updateIds = $db -> loadColumn()){
                    $updateIds  = null;
                }

                if($updateIds){
                    // Insert article with this template
                    if($insertIds  = array_diff($articlesAssignment,$updateIds)){

                        $query  = $db -> getQuery(true);
                        $query -> insert($db -> quoteName('#__tz_portfolio_xref_content'));
                        $query ->columns('contentid,type,link_attribs,template_id');
                        foreach($insertIds as $cid){
                            $query -> values($cid.','.$db -> quote('none').','
                                .$db -> quote('{"link_target":"_blank","link_follow":"nofollow"}')
                                .','.$table -> id);
                        }
                        $db -> setQuery($query);
                        $db -> execute();
                    }

                    $query  = $db -> getQuery(true);
                    $query -> update($db -> quoteName('#__tz_portfolio_xref_content'));
                    $query -> set($db -> quoteName('template_id').'='.$table -> id);
                    $query -> where($db -> quoteName('contentid').' IN('.implode(',',$updateIds).')');
                    $db -> setQuery($query);
                    $db -> execute();
                }else{
                    $query  = $db -> getQuery(true);
                    $query -> insert($db -> quoteName('#__tz_portfolio_xref_content'));
                    $query ->columns('contentid,type,link_attribs,template_id');
                    foreach($articlesAssignment as $cid){
                        $query -> values($cid.','.$db -> quote('none').','
                            .$db -> quote('{"link_target":"_blank","link_follow":"nofollow"}')
                            .','.$table -> id);
                    }
                    $db -> setQuery($query);
                    $db -> execute();
                }
            }


            // Assign categories with this template;
            if(!empty($categoriesAssignmentOld) && count($categoriesAssignmentOld)){
                $query  = $db -> getQuery(true);
                $query -> update($db -> quoteName('#__tz_portfolio_categories'));
                $query -> set($db -> quoteName('template_id').'= 0');
                $query -> where($db -> quoteName('catid').' IN('.implode(',',$categoriesAssignmentOld).')');
                $db -> setQuery($query);
                $db -> execute();
            }

            if(!empty($categoriesAssignment) && count($categoriesAssignment)){
                $categoriesAssignment   = array_unique($categoriesAssignment);
                $query  = $db -> getQuery(true);
                $query -> select('catid');
                $query -> from($db -> quoteName('#__tz_portfolio_categories'));
                $query -> where($db -> quoteName('catid').' IN('.implode(',',$categoriesAssignment).')');
                $db -> setQuery($query);

                if(!$updateCatIds = $db -> loadColumn()){
                    $updateCatIds  = null;
                }

                if($updateCatIds){
                    // Insert article with this template
                    if($insertIds  = array_diff($categoriesAssignment,$updateCatIds)){
                        $query  = $db -> getQuery(true);
                        $query -> insert($db -> quoteName('#__tz_portfolio_categories'));
                        $query ->columns('catid,groupid,template_id');
                        foreach($insertIds as $cid){
                            $query -> values($cid.',0,'.$table -> id);
                        }
                        $db -> setQuery($query);
                        $db -> execute();
                    }

                    $query  = $db -> getQuery(true);
                    $query -> update($db -> quoteName('#__tz_portfolio_categories'));
                    $query -> set($db -> quoteName('template_id').'='.$table -> id);
                    $query -> where($db -> quoteName('catid').' IN('.implode(',',$categoriesAssignment).')');
                    $db -> setQuery($query);
                    $db -> execute();
                }else{
                    $query  = $db -> getQuery(true);
                    $query -> insert($db -> quoteName('#__tz_portfolio_categories'));
                    $query ->columns('catid,groupid,template_id');
                    foreach($categoriesAssignment as $cid){
                        $query -> values($cid.',0,'.$table -> id);
                    }
                    $db -> setQuery($query);
                    $db -> execute();
                }
            }


            // Clean the cache.
            $this->cleanCache();

            // Trigger the onContentAfterSave event.
            $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
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

//    function save($data=array()){
//        $post   = JRequest::get();
//        unset($data['tags']);
//        $data['params'] = '';
//        if($attrib = $post['jform']['attrib']){
//            $data['params'] = json_encode($attrib);
//        }
//        if(!$data['id'] || $data['id'] == 0){
//            $data['title']  = $this ->generateNewTitle(null,null,$data['title']);
//        }
//
//        $home   = false;
//        if($data['home'] == '1'){
//            $home   = true;
//        }
//        $table  = $this -> getTable();
//        if(!$table -> hasHome()){
//            $data['home']   = '1';
//        }
//
//        if(parent::save($data)){
//            if($data['id'] && $data['home'] == '1'){
//                $this -> setHome($data['id']);
//            }
//            return true;
//        }
//        return false;
//    }

    public function getTZLayout(){
        $item   = $this -> getItem();
        $params = $item -> params;
        if(empty($params)){
            $pathfile   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/views/template/tmpl/default.json';
            if(JFile::exists($pathfile)){
                $string     = file_get_contents($pathfile);
                return json_decode($string);
            }
        }
        return $params;
    }

    public function setHome($id = 0)
    {
        $user = JFactory::getUser();
        $db   = $this->getDbo();

        // Access checks.
        if (!$user->authorise('core.edit.state', 'com_content'))
        {
            throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
        }

        $style = JTable::getInstance('Templates', 'Table');

        if (!$style->load((int) $id))
        {
            throw new Exception(JText::_('COM_TEMPLATES_ERROR_STYLE_NOT_FOUND'));
        }

        // Detect disabled extension
//        $extension = JTable::getInstance('Extension');

//        if ($extension->load(array('enabled' => 0, 'type' => 'template', 'element' => $style->template, 'client_id' => $style->client_id)))
//        {
//            throw new Exception(JText::_('COM_TEMPLATES_ERROR_SAVE_DISABLED_TEMPLATE'));
//        }

        // Reset the home fields for the client_id.
        $db->setQuery(
            'UPDATE #__tz_portfolio_templates' .
            ' SET home = \'0\'' .
            ' WHERE home = \'1\''
        );
        $db->execute();

        // Set the new home style.
        $db->setQuery(
            'UPDATE #__tz_portfolio_templates' .
            ' SET home = \'1\'' .
            ' WHERE id = ' . (int) $id
        );
        $db->execute();

        // Clean the cache.
        $this->cleanCache();

        return true;
    }

    public function unsetHome($id = 0)
    {
        $user = JFactory::getUser();
        $db   = $this->getDbo();

        // Access checks.
        if (!$user->authorise('core.edit.state', 'com_content'))
        {
            throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
        }

        // Lookup the client_id.
        $db->setQuery(
            'SELECT home' .
            ' FROM #__tz_portfolio_templates' .
            ' WHERE id = ' . (int) $id
        );
        $style = $db->loadObject();

        if ($style->home == '1')
        {
            throw new Exception(JText::_('COM_TEMPLATES_ERROR_CANNOT_UNSET_DEFAULT_STYLE'));
        }

        // Set the new home style.
        $db->setQuery(
            'UPDATE #__tz_portfolio_templates' .
            ' SET home = \'0\'' .
            ' WHERE id = ' . (int) $id
        );
        $db->execute();

        // Clean the cache.
        $this->cleanCache();

        return true;
    }

    public function delete(&$pks)
    {
        $pks	= (array) $pks;
        $user	= JFactory::getUser();
        $table	= $this->getTable();

        // Iterate the items to delete each one.
        foreach ($pks as $pk)
        {
            if ($table->load($pk))
            {
                // Access checks.
                if (!$user->authorise('core.delete', 'com_tz_portfolio'))
                {
                    throw new Exception(JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
                }

                // You should not delete a default style
//                if ($table->home != '0')
//                {
//                    JError::raiseWarning(SOME_ERROR_NUMBER, Jtext::_('COM_TEMPLATES_STYLE_CANNOT_DELETE_DEFAULT_STYLE'));
//
//                    return false;
//                }

                if (!$table->delete($pk))
                {
                    $this->setError($table->getError());

                    return false;
                }
            }
            else
            {
                $this->setError($table->getError());

                return false;
            }
        }

        // Clean cache
        $this->cleanCache();

        return true;
    }



    public function duplicate(&$pks)
    {
        $user	= JFactory::getUser();

        // Access checks.
        if (!$user->authorise('core.create', 'com_tz_portfolio'))
        {
            throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
        }

        $table = $this->getTable();

        foreach ($pks as $pk)
        {
            if ($table->load($pk, true))
            {
                // Reset the id to create a new record.
                $table->id = 0;

                // Reset the home (don't want dupes of that field).
                $table->home = 0;

//                // Alter the title.
                $m = null;
                $table->title = $this -> generateNewTitle(null,null,$table -> title);

                if (!$table->check() || !$table->store())
                {
                    throw new Exception($table->getError());
                }
            }
            else
            {
                throw new Exception($table->getError());
            }
        }

        // Clean cache
        $this->cleanCache();

        return true;
    }



    public function getItemTemplate($artId = null,$catId = null){
        $_artId = !empty($artId)?$artId:$this -> getState('content.id');
        $_catId = !empty($catId)?$catId:$this -> getState('category.id');

        $db         = JFactory::getDbo();
        $templateId = null;

        if($_catId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_categories'));
            $query -> where($db -> quoteName('catid').'='.$_catId);
            $db -> setQuery($query);
            if($crow = $db -> loadObject()){
                if($crow -> template_id){
                    $templateId = $crow -> template_id;
                }
            }
        }
        if($_artId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_xref_content'));
            $query -> where($db -> quoteName('contentid').'='.$_artId);
            $db -> setQuery($query);
            if($row = $db -> loadObject()){
                if($row -> template_id){
                    $templateId = $row -> template_id;
                }
            }
        }
        return (int) $templateId;
    }
}