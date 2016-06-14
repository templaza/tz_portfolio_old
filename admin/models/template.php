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

class TZ_PortfolioModelTemplate extends JModelAdmin
{
    protected function populateState(){
        parent::populateState();

        $this -> setState('template.id',JRequest::getInt('id'));
    }
    public function getTable($type = 'Extensions', $prefix = 'TZ_PortfolioTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio.template', 'template', array('control' => ''));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function publish(&$pks, $value = 1)
    {
        $dispatcher = JEventDispatcher::getInstance();
        $user = JFactory::getUser();
        $table = $this->getTable();

        $pks = (array) $pks;
        JFactory::getLanguage() -> load('com_installer');

        if ($user->authorise('core.edit.state', 'com_tz_portfolio')) {
            $result = true;
            // Include the plugins for the change of state event.
            JPluginHelper::importPlugin($this->events_map['change_state']);

            // Access checks.
            foreach ($pks as $i => $pk) {
                $table->reset();

                if ($table->load($pk)) {
                    if (!$this->canEditState($table)) {

                        $result = false;
                        // Prune items that you can't change.
                        unset($pks[$i]);
                        JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');

                        return false;
                    }
                    if($table ->protected ){
                        $result = false;
                        JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
                        unset($pks[$i]);
                        continue;
                    }else{
                        if ($template = $this->getTemplateStyle($table->name)) {
                            if ($template->home || $template->protected) {
                                $result = false;
                                JError::raiseError(403, JText::_('COM_INSTALLER_ERROR_DISABLE_DEFAULT_TEMPLATE_NOT_PERMITTED'));
                                unset($pks[$i]);
                                continue;
                            }
                        }
                    }
                }
            }

            if(!$result){
                if (count($pks)) {
                    // Attempt to change the state of the records.
                    if (!$table->publish($pks, $value, $user->get('id'))) {
                        $this->setError($table->getError());

                        return false;
                    }

                    $context = $this->option . '.' . $this->name;

                    // Trigger the change state event.
                    $result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

                    if (in_array(false, $result, true)) {
                        $this->setError($table->getError());

                        return false;
                    }
                }
            }

            // Clear the component's cache
            $this->cleanCache();
        }else{
            $result = false;
            JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
        }

        return $result;
    }

    function getTemplate($name){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from($db -> quoteName('#__tz_portfolio_extensions'));
        $query -> where($db -> quoteName('type').'='.$db -> quote('tz_portfolio-template'));
        $query -> where($db -> quoteName('name').'='.$db -> quote($name));
        $db -> setQuery($query);
        if($data = $db -> loadObject()){
            return $data;
        }
        return false;
    }

    function getTemplateStyle($template){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from($db -> quoteName('#__tz_portfolio_templates'));
        $query -> where($db -> quoteName('template').'='.$db -> quote($template));
        $query -> group($db -> quoteName('template'));
        $db -> setQuery($query);
        if($data = $db -> loadObject()){
            return $data;
        }
        return false;
    }

    public function install()
    {
        $app        = JFactory::getApplication();
        JFactory::getLanguage() -> load('com_installer');

        $package    = $this -> _getPackageFromUpload();
        $tpl_path   = COM_TZ_PORTFOLIO_PATH_SITE.'/templates';
        $result     = true;
        $msg        = JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_TEMPLATE'));

        // Was the package unpacked?
        if (!$package || !$package['type'])
        {
            JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

            $app->enqueueMessage(JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');

            return false;
        }

        // Get an installer instance.
        $installer  = JInstaller::getInstance($package['dir']);
        $installer -> setPath('source',$package['dir']);

        if($manifest = $installer ->getManifest()){
            $attrib = $manifest -> attributes();

            $name   = (string) $manifest -> name;
            $type   = (string) $attrib -> type;

            if($type != 'tz_portfolio-template'){
                $app->enqueueMessage(JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');
                return false;
            }

            $folder_name    = $name;
            if($folder_name && is_numeric(strpos($folder_name,'tz_portfolio_tpl_'))){
                $folder_name    = preg_replace('/^tz_portfolio_tpl_/','',$folder_name);
            }

            $files_folders  = $manifest -> xPath('files');
            $replace    = false;
            if($attrib -> method == 'upgrade'){
                $replace    = true;
            }

            // Upload folder to templates folder
            if(isset($files_folders[0] -> folder)){
                $folders    = (array)$files_folders[0] -> folder;
                if(count($folders)){
                    foreach($folders as $folder){
                        if(JFolder::exists($package['dir'].DIRECTORY_SEPARATOR.$folder)) {
                            if(JFolder::exists($tpl_path . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $folder)){
                                if(!$replace){
                                    $msg    = JText::_('COM_TZ_PORTFOLIO_FOLDER_EXIST');
                                    $result = false;
                                }else{
                                    JFolder::copy($package['dir'] . DIRECTORY_SEPARATOR . $folder,
                                        $tpl_path . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $folder,
                                        '', $replace);
                                    $result = true;
                                }
                            }else {
                                JFolder::copy($package['dir'] . DIRECTORY_SEPARATOR . $folder,
                                    $tpl_path . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $folder,
                                    '', $replace);
                                $result = true;
                            }
                        }else{
                            $msg    = JText::sprintf('JLIB_INSTALLER_ERROR_NO_FILE',$package['dir'] . DIRECTORY_SEPARATOR . $folder);
                            $result = false;
                            break;
                        }
                    }
                }
            }

            if($result) {
                // Upload files to templates folder
                if (isset($files_folders[0]->filename)) {
                    $files = (array)$files_folders[0]->filename;
                    if (count($files)) {
                        foreach ($files as $file) {
                            if (JFile::exists($package['dir'] . DIRECTORY_SEPARATOR . $file)) {
                                JFile::copy($package['dir'] . DIRECTORY_SEPARATOR . $file,
                                    $tpl_path . DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR . $file,
                                    null, $replace);
                                $result = true;
                            } else {
                                $msg    = JText::sprintf('JLIB_INSTALLER_ERROR_NO_FILE',$package['dir']
                                    . DIRECTORY_SEPARATOR . $file);
                                $result = false;
                                break;
                            }
                        }
                    }
                }
            }


            if($result){
                // Add template's information to table tz_portfolio_extensions
                $data   = null;
                $data ['id']    = 0;
                $data ['name']  = $name;
                $data ['type']  = $type;
                $manifest_cache = $installer -> generateManifestCache();

                $data['protected']      = 0;
                $data['manifest_cache'] = $manifest_cache;
                $data['published']      = 1;
                $data['access']         = 1;
                $data['params']         = $installer -> getParams();

                if($template = $this -> getTemplate($name)){
                    if(isset($template ->protected) && $template ->protected){
                        $app -> enqueueMessage(JText::_('COM_TZ_PORTFOLIO_TEMPLATE_ERROR_INSTALL_PROTECTED'),'error');
                        return false;
                    }
                    $data['id'] = $template -> id;
                }
                $this -> save($data);

                // Add template's information to table tz_portfolio_templates
                $data   = null;
                if(!$this -> getTemplateStyle($name)){
                    $lang   = JFactory::getLanguage();
                    $data['title']  = $name;
                    if($lang -> load('tpl_'.$name,$tpl_path.DIRECTORY_SEPARATOR.$folder_name)){
                        if($lang ->hasKey('TZ_PORTFOLIO_TPL_'.$name)){
                            // ALTER TABLE `jos_tz_portfolio_templates` ADD `template` VARCHAR(100) NOT NULL AFTER `id`;
                            // ALTER TABLE `jos_tz_portfolio_templates` ADD `attribs` TEXT NOT NULL ;
                            $data['title']      = JText::_('TZ_PORTFOLIO_TPL_'.$name);
                        }
                    }
                    $data['id']         = 0;
                    $data['template']   = $name;
                    $data['title']      = JText::sprintf('COM_TZ_PORTFOLIO_TEMPLATE_STYLE_NAME',$data['title']);
                    $data['home']       = 0;
                    $data['params']     = '';

                    $model  = JModelAdmin::getInstance('Template_Style','TZ_PortfolioModel');
                    if($model){
                        $model -> save($data);
                    }
                }
            }

            if(!$result) {
                $app->enqueueMessage($msg, 'warning');
                $app->enqueueMessage(JText::sprintf('JLIB_INSTALLER_ABORT_TPL_INSTALL_COPY_FILES', 'files'),'warning');
                $app->enqueueMessage(JText::sprintf('COM_INSTALLER_INSTALL_ERROR', 'template'), 'error');
            }else {
                $app->enqueueMessage($msg, 'message');
            }
        }

        JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

        return $result;
    }

    public function uninstall($eid = array())
    {
        $user   = JFactory::getUser();
        $lang   = JFactory::getLanguage();
        $app    = JFactory::getApplication();

        $lang -> load('com_installer');

        if ($user->authorise('core.delete', 'com_tz_portfolio'))
        {
            $failed = array();

            /*
             * Ensure eid is an array of extension ids in the form id => client_id
             * TODO: If it isn't an array do we want to set an error and fail?
             */
            if (!is_array($eid))
            {
                $eid = array($eid => 0);
            }

            // Get an installer object for the extension type
            $row = $this -> getTable();

            $template_table     = $this -> getTable('Templates');
            $template_default   = $template_table -> getHome();
            $template_style     = JModelAdmin::getInstance('Template_Style','TZ_PortfolioModel',array('ignore_request' => true));

            // Uninstall the chosen extensions
            $msgs = array();
            $result = false;

            foreach ($eid as $id)
            {
                $id = trim($id);
                $row->load($id);

                $langstring = 'COM_INSTALLER_TYPE_TYPE_' . strtoupper($row->type);
                $rowtype = JText::_($langstring);

                if (strpos($rowtype, $langstring) !== false)
                {
                    $rowtype = $row->type;
                }

                if ($row->type && $row->type == 'tz_portfolio-template')
                {

                    // Is the template we are trying to uninstall a core one?
                    // Because that is not a good idea...
                    if ($row->protected)
                    {
                        JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_TPL_UNINSTALL_WARNCORETEMPLATE', JText::_('COM_TZ_PORTFOLIO_TEMPLATE_LABEL')), JLog::WARNING, 'jerror');

                        return false;
                    }

                    if($template_default -> template == $row -> name){
                        $msg    = JText::_('JLIB_INSTALLER_ERROR_TPL_UNINSTALL_TEMPLATE_DEFAULT');
                        $app->enqueueMessage($msg,'warning');
                        return false;
                    }

                    $tpl_path   = COM_TZ_PORTFOLIO_PATH_SITE.DIRECTORY_SEPARATOR.'templates'
                        .DIRECTORY_SEPARATOR.$row -> name;

                    if(JFolder::exists($tpl_path)){
                        if(!$template_style -> deleteTemplate($row -> name)){
                            $app -> enqueueMessage($template_style -> getError(),'warning');
                            return false;
                        }
                        if(JFolder::delete($tpl_path)){
                            $result = $this->delete($id);
                        }
                    }

                    // Build an array of extensions that failed to uninstall
                    if ($result === false)
                    {
                        // There was an error in uninstalling the package
                        $msgs[] = JText::sprintf('COM_INSTALLER_UNINSTALL_ERROR', JText::_('COM_TZ_PORTFOLIO_TEMPLATE_LABEL'));
                        $result = false;
                    }
                    else
                    {
                        // Package uninstalled sucessfully
                        $msgs[] = JText::sprintf('COM_INSTALLER_UNINSTALL_SUCCESS', JText::_('COM_TZ_PORTFOLIO_TEMPLATE_LABEL'));
                        $result = true;
                    }
                }
            }

            $msg = implode("<br />", $msgs);
            $app->enqueueMessage($msg);

            return $result;
        }
        else
        {
            JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
        }
    }

    protected function _getPackageFromUpload()
    {
        // Get the uploaded file information.
        $input    = JFactory::getApplication()->input;
        // Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
        $userfile = $input->files->get('install_package', null, 'raw');

        // Make sure that file uploads are enabled in php.
        if (!(bool) ini_get('file_uploads'))
        {
            JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));

            return false;
        }

        // Make sure that zlib is loaded so that the package can be unpacked.
        if (!extension_loaded('zlib'))
        {
            JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));

            return false;
        }

        // If there is no uploaded file, we have a problem...
        if (!is_array($userfile))
        {
            JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED'));

            return false;
        }

        // Is the PHP tmp directory missing?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_NO_TMP_DIR))
        {
            JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_INSTALLER_MSG_WARNINGS_PHPUPLOADNOTSET'));

            return false;
        }

        // Is the max upload size too small in php.ini?
        if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_INI_SIZE))
        {
            JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . JText::_('COM_INSTALLER_MSG_WARNINGS_SMALLUPLOADSIZE'));

            return false;
        }

        // Check if there was a different problem uploading the file.
        if ($userfile['error'] || $userfile['size'] < 1)
        {
            JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));

            return false;
        }

        // Build the appropriate paths.
        $tmp_dest	= JPATH_ROOT . '/tmp/tz_portfolio_install/' . $userfile['name'];
        $tmp_src	= $userfile['tmp_name'];

        if(!JFile::exists(JPATH_ROOT . '/tmp/tz_portfolio_install/index.html')){
            JFile::write(JPATH_ROOT . '/tmp/tz_portfolio_install/index.html',
                htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
        }

        // Move uploaded file.
        jimport('joomla.filesystem.file');
        JFile::upload($tmp_src, $tmp_dest, false, true);

        // Unpack the downloaded package file.
        $package = JInstallerHelper::unpack($tmp_dest, true);

        return $package;
    }

}