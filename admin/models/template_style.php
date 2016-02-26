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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');

class TZ_PortfolioModelTemplate_Style extends JModelAdmin
{
    protected function populateState(){
        parent::populateState();

        $this -> setState('template.id',JRequest::getInt('id'));
        $this -> setState('content.id',null);
        $this -> setState('category.id',null);
        $this -> setState('template.template',null);
    }
    public function getTable($type = 'Templates', $prefix = 'TZ_PortfolioTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getForm($data = array(), $loadData = true){
        // The folder and element vars are passed when saving the form.
        if (empty($data))
        {
            $item	   = $this -> getItem();
            $template  = $item -> template;
        }
        else
        {
            $template  = JArrayHelper::getValue($data, 'template');
        }

        // These variables are used to add data from the plugin XML files.
        $this->setState('template.template', $template);

        //        JForm::addFormPath(COM_TZ_PORTFOLIO_PATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.)
        $form = $this->loadForm('com_tz_portfolio.template_style', 'template_style', array('control' => 'jform', 'load_data' => $loadData));



        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        if (empty($data)) {
            $data = $this->getItem();
            $data -> categories_assignment = $this -> getCategoriesAssignment();
            $data -> articles_assignment = $this -> getArticlesAssignment();
        }

        return $data;
    }

    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        $template       = $this->getState('template.template');
        $lang           = JFactory::getLanguage();

        $template_path  = COM_TZ_PORTFOLIO_PATH_SITE.DIRECTORY_SEPARATOR.'templates'
            .DIRECTORY_SEPARATOR.$template;

        jimport('joomla.filesystem.path');

        $formFile = JPath::clean($template_path.DIRECTORY_SEPARATOR.'template.xml');

        // Load the core and/or local language file(s).
        $lang->load('tpl_' . $template, $template_path, null, false, true)
        ||	$lang->load('tpl_' . $template, $template_path . '/templates/' . $template, null, false, true);

        $default_directory  = 'components'.DIRECTORY_SEPARATOR.'com_tz_portfolio'.DIRECTORY_SEPARATOR.'templates';
        $directory          = $default_directory.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html';
        if(JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.$directory)) {
            $form->setFieldAttribute('layout', 'directory', $directory, 'params');
        }elseif ((is_array($data) && array_key_exists('protected', $data) && $data['protected'] == 1)
            || ((is_object($data) && isset($data->protected) && $data->protected == 1)))
        {
            $form -> removeField('layout','params');
        }else{
            $form -> removeField('layout','params');
        }

        if (file_exists($formFile))
        {
            // Get the template form.
            if (!$form->loadFile($formFile, false, '//config'))
            {
                throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
            }
        }

        // Disable home field if it is default style

        if ((is_array($data) && array_key_exists('home', $data) && $data['home'] == '1')
            || ((is_object($data) && isset($data->home) && $data->home == '1')))
        {
            $form->setFieldAttribute('home', 'readonly', 'true');
        }

        // Attempt to load the xml file.
        if (!$xml = simplexml_load_file($formFile))
        {
            throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
        }

        // Get the help data from the XML file if present.
        $help = $xml->xpath('/extension/help');

        if (!empty($help))
        {
            $helpKey = trim((string) $help[0]['key']);
            $helpURL = trim((string) $help[0]['url']);

            $this->helpKey = $helpKey ? $helpKey : $this->helpKey;
            $this->helpURL = $helpURL ? $helpURL : $this->helpURL;
        }

        // Trigger the default form events.
        parent::preprocessForm($form, $data, $group);
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

        if (property_exists($item, 'layout'))
        {
            $item->layout = json_decode($item -> layout);
        }
        if (property_exists($item, 'params'))
        {
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

        $data['layout'] = '';
        if(isset($post['jform']['attrib']) && $attrib = $post['jform']['attrib']){
            $data['layout'] = json_encode($attrib);
        }else{
            $pathfile   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/views/template_style/tmpl/default.json';
            if(JFile::exists($pathfile)){
                $data['layout'] = file_get_contents($pathfile);
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
            $data['id'] = 0;
            $data['home']   = 0;
            unset($data['articles_assignment']);
            unset($data['categories_assignment']);
            unset($post['menus_assignment_old']);
            unset($data['menus_assignment']);
//            unset($post['jform']['articles_assignment_old']);
//            unset($post['jform']['categories_assignment_old']);

        }

        if(isset($data['articles_assignment'])){
            $articlesAssignment  = $data['articles_assignment'];
            unset($data['article_assignment']);
        }

        if(isset($data['categories_assignment']) && count($data['categories_assignment'])){
            $categoriesAssignment  = $data['categories_assignment'];
            unset($data['categories_assignment']);
        }

        if($data['params']){
            $data['params'] = json_encode($data['params']);
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

            $user = JFactory::getUser();

            // Assign template style for menu
            if ($user->authorise('core.edit', 'com_menus'))
            {
                $n    = 0;
                $db   = JFactory::getDbo();
                $user = JFactory::getUser();

                // Assign menu items with this template;
                if(!empty($post['jform']['menus_assignment_old']) && count($post['jform']['menus_assignment_old'])){
                    $query  = $db -> getQuery(true)
                        -> select('*')
                        -> from('#__menu')
                        ->where('id IN (' . implode(',', $post['jform']['menus_assignment_old']) . ')')
                        ->where('checked_out IN (0,' . (int) $user->id . ')');
                    $db -> setQuery($query);
                    if($menu = $db -> loadObjectList()){
                        foreach($menu as $item){
                            $params         = new JRegistry($item -> params);
                            $params -> set('tz_template_style_id',0);
                            $update_query = 'UPDATE '.$db -> quoteName('#__menu').' SET params='
                                .$db -> quote($params -> toString()).
                                ' WHERE id='.$item -> id;
                            if(count($update_query)){
                                $db -> setQuery($update_query);
                                $db ->execute();
                            }
                        }
                    }
                }

                if (!empty($data['menus_assignment']) && is_array($data['menus_assignment']))
                {

                    JArrayHelper::toInteger($data['menus_assignment']);

                    $query  = $db -> getQuery(true)
                        -> select('*')
                        -> from('#__menu')
                        ->where('id IN (' . implode(',', $data['menus_assignment']) . ')')
                        ->where('checked_out IN (0,' . (int) $user->id . ')');

                    $db -> setQuery($query);

                    if($menus = $db -> loadObjectList()){
                        foreach($menus as $menu){
                            $params         = new JRegistry($menu -> params);
                            $params -> set('tz_template_style_id',$table -> id);

                            $update_query = 'UPDATE '.$db -> quoteName('#__menu').' SET params='
                                .$db -> quote($params -> toString()).
                                ' WHERE id='.$menu -> id;
                            if(count($update_query)){
                                $db -> setQuery($update_query);
                                $db ->execute();
                            }
                        }
                    }
                }

            }


            // Assign article with this template;
            if(!empty($articlesAssignment) && count($articlesAssignment)){

                // Update the mapping for article items that this style IS assigned to.
                $query = $db->getQuery(true)
                    ->update('#__tz_portfolio_xref_content')
                    ->set('template_id = ' . (int) $table->id)
                    ->where('contentid IN (' . implode(',', $articlesAssignment) . ')')
                    ->where('template_id != ' . (int) $table->id);
                $db->setQuery($query);
                $db->execute();

                $query  = $db -> getQuery(true);
                $query -> select('contentid');
                $query -> from($db -> quoteName('#__tz_portfolio_xref_content'));
                $query -> where($db -> quoteName('contentid').' IN('
                    .implode(',',$articlesAssignment).')');
                $db -> setQuery($query);

                if(!$updateIds = $db -> loadColumn()){
                    $updateIds  = array();
                }

                // Insert article items with this template if they were created in com_content
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
            }

            // Remove style mappings for article items this style is NOT assigned to.
            // If unassigned then all existing maps will be removed.
            $query = $db->getQuery(true)
                ->update('#__tz_portfolio_xref_content')
                ->set('template_id = 0');

            if (!empty($articlesAssignment) && count($articlesAssignment))
            {
                $query->where('contentid NOT IN (' . implode(',', $articlesAssignment) . ')');
            }

            $query->where('template_id = ' . (int) $table->id);
            $db->setQuery($query);
            $db->execute();



            // Assign categories with this template;
            if(!empty($categoriesAssignment) && count($categoriesAssignment)){

                // Update the mapping for category items that this style IS assigned to.
                $query = $db->getQuery(true)
                    ->update('#__tz_portfolio_categories')
                    ->set('template_id = ' . (int) $table->id)
                    ->where('catid IN (' . implode(',', $categoriesAssignment) . ')')
                    ->where('template_id != ' . (int) $table->id);
                $db->setQuery($query);
                $db->execute();

                $query  = $db -> getQuery(true);
                $query -> select('catid');
                $query -> from($db -> quoteName('#__tz_portfolio_categories'));
                $query -> where($db -> quoteName('catid').' IN('
                    .implode(',',$categoriesAssignment).')');
                $db -> setQuery($query);

                if(!$updateIds = $db -> loadColumn()){
                    $updateIds  = array();
                }

                // Insert category items with this template if they were created in com_content
                if($insertIds  = array_diff($categoriesAssignment,$updateIds)){
                    $query  = $db -> getQuery(true);
                    $query -> insert($db -> quoteName('#__tz_portfolio_categories'));
                    $query ->columns('catid,groupid,template_id');
                    foreach($insertIds as $cid){
                        $query -> values($cid.',0,'.$table -> id);
                    }
                    $db -> setQuery($query);
                    $db -> execute();
                }
            }

            // Remove style mappings for category items this style is NOT assigned to.
            // If unassigned then all existing maps will be removed.
            $query = $db->getQuery(true)
                ->update('#__tz_portfolio_categories')
                ->set('template_id = 0');

            if (!empty($categoriesAssignment) && count($categoriesAssignment))
            {
                $query->where('catid NOT IN (' . implode(',', $categoriesAssignment) . ')');
            }

            $query->where('template_id = ' . (int) $table->id);
            $db->setQuery($query);
            $db->execute();




//            // Assign categories with this template;
//            if(!empty($categoriesAssignmentOld) && count($categoriesAssignmentOld)){
//                $query  = $db -> getQuery(true);
//                $query -> update($db -> quoteName('#__tz_portfolio_categories'));
//                $query -> set($db -> quoteName('template_id').'= 0');
//                $query -> where($db -> quoteName('catid').' IN('.implode(',',$categoriesAssignmentOld).')');
//                $db -> setQuery($query);
//                $db -> execute();
//            }
//
//            if(!empty($categoriesAssignment) && count($categoriesAssignment)){
//                $categoriesAssignment   = array_unique($categoriesAssignment);
//                $query  = $db -> getQuery(true);
//                $query -> select('catid');
//                $query -> from($db -> quoteName('#__tz_portfolio_categories'));
//                $query -> where($db -> quoteName('catid').' IN('.implode(',',$categoriesAssignment).')');
//                $db -> setQuery($query);
//
//                if(!$updateCatIds = $db -> loadColumn()){
//                    $updateCatIds  = null;
//                }
//
//                if($updateCatIds){
//                    // Insert article with this template
//                    if($insertIds  = array_diff($categoriesAssignment,$updateCatIds)){
//                        $query  = $db -> getQuery(true);
//                        $query -> insert($db -> quoteName('#__tz_portfolio_categories'));
//                        $query ->columns('catid,groupid,template_id');
//                        foreach($insertIds as $cid){
//                            $query -> values($cid.',0,'.$table -> id);
//                        }
//                        $db -> setQuery($query);
//                        $db -> execute();
//                    }
//
//                    $query  = $db -> getQuery(true);
//                    $query -> update($db -> quoteName('#__tz_portfolio_categories'));
//                    $query -> set($db -> quoteName('template_id').'='.$table -> id);
//                    $query -> where($db -> quoteName('catid').' IN('.implode(',',$categoriesAssignment).')');
//                    $db -> setQuery($query);
//                    $db -> execute();
//                }else{
//                    $query  = $db -> getQuery(true);
//                    $query -> insert($db -> quoteName('#__tz_portfolio_categories'));
//                    $query ->columns('catid,groupid,template_id');
//                    foreach($categoriesAssignment as $cid){
//                        $query -> values($cid.',0,'.$table -> id);
//                    }
//                    $db -> setQuery($query);
//                    $db -> execute();
//                }
//            }


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
        $params = $item -> layout;
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

//        $style = JTable::getInstance('Templates', 'Table');
        $style = $this -> getTable();

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

        $db     = $this -> getDbo();
        $menus  = array();

        // Process menus links
        if($menuTypes = TZ_PortfolioHelper::getMenuLinks()) {
            if ($links = JArrayHelper::getColumn($menuTypes, 'links')) {
                foreach($links as $link){
                    $menus  = array_merge($menus,$link);
                }
            }
        }

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

                if(isset($table -> home) && (int)$table -> home){
                    $this -> setError(JText::_('COM_TZ_PORTFOLIO_TEMPLATE_STYLE_CANNOT_DELETE_DEFAULT_STYLE'));
                    return false;
                }

//                if (!$table->delete($pk))
//                {
//                    $this->setError($table->getError());
//
//                    return false;
//                }

                // Set Template id is 0 in tz_portfolio_categories
                $query  = $db -> getQuery(true);
                $query -> update($db -> quoteName('#__tz_portfolio_categories'))
                    -> set('template_id = 0')
                    -> where('template_id ='.$pk);
                $db -> setQuery($query);
                $db -> execute();

                // Set Template id is 0 in tz_portfolio_xref_content
                $query  = $db -> getQuery(true);
                $query -> update($db -> quoteName('#__tz_portfolio_xref_content'))
                    -> set('template_id = 0')
                    -> where('template_id ='.$pk);
                $db -> setQuery($query);
                $db -> execute();

                // Set Template id is "" for portfolio's menu
                if(count($menus)){
                    foreach ($menus as $menu){
                        if(isset($menu -> params ) && $params = $menu -> params){
                            if($params -> get('tz_template_style_id','') == $pk){
                                $params -> set('tz_template_style_id','');
                                $query  = $db -> getQuery(true);
                                $query -> update('#__menu')
                                    -> set('params='.$db -> quote($params -> toString()))
                                    -> where('id='.$menu -> value);
                                $db -> setQuery($query);
                                $db -> execute();
                            }
                        }
                    }
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

    public function deleteTemplate(&$template)
    {
        $user	= JFactory::getUser();
        $table	= $this->getTable();

        // Access checks.
        if (!$user->authorise('core.delete', 'com_tz_portfolio'))
        {
            throw new Exception(JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
        }

        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);

        $query -> delete($db -> quoteName('#__tz_portfolio_templates'));
        $query -> where($db -> quoteName('template').'='.$db -> quote($template));
        $db -> setQuery($query);
        if(!$db -> execute()){
            $this -> setError($db -> getError());
            return false;
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