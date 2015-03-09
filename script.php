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

jimport('joomla.installer.installer');
//jimport('joomla.base.adapterinstance');

class com_tz_portfolioInstallerScript{


    function postflight($type, $parent){


        $manifest   = $parent -> get('manifest');
        $params     = new JRegistry();

        $query  = 'SELECT params FROM #__extensions'
            .' WHERE `type`="component" AND `name`="'.strtolower($manifest -> name).'"';
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        $db -> query();

        $paramNames = array();

        if($db -> loadResult()){
            $params -> loadString($db ->loadResult());
            if(count($params -> toArray())>0){
                foreach($params -> toArray() as $key => $val){
                    $paramNames[]   = $key;
                }
            }
        }

        $fields     = $manifest -> xPath('config/fields/field');

        foreach($fields as $field){
            $attribute  = $field -> attributes();
            if(!in_array((string)$attribute -> name,$paramNames)){
                if($attribute -> multiple == 'true'){
                    $arr   = null;
                    foreach($field -> option as $option){
                        $opAttr = $option -> attributes();
                        $arr[]  = (string)$opAttr -> value;
                    }

                    $params -> set((string) $attribute -> name,$arr);
                }
                else
                    $params -> set((string)$attribute -> name,(string)$attribute ->default);
            }
        }

        $params = $params -> toString();

        $query  = 'UPDATE #__extensions SET `params`=\''.$params.'\''
            .' WHERE `name`="'.strtolower($manifest -> name).'"'
            .' AND `type`="component"';

        $db -> setQuery($query);
        $db -> query();

        JFactory::getLanguage() -> load('com_tz_portfolio');


        //Create folder
        $mediaFolder    = 'tz_portfolio';
        $mediaFolderPath    = JPATH_SITE.'/media/'.$mediaFolder;
        $article    = 'article';
        $cache      = 'cache';
        $src        = 'src';
        $html   = htmlspecialchars_decode('<!DOCTYPE html><title></title>');

        if(!JFolder::exists($mediaFolderPath)){
            JFolder::create($mediaFolderPath);
        }
        if(!JFile::exists($mediaFolderPath.'/index.html')){
            JFile::write($mediaFolderPath.'/index.html',$html);
        }
        if(!JFolder::exists($mediaFolderPath.'/'.$article)){
            JFolder::create($mediaFolderPath.'/'.$article);
        }
        if(!JFile::exists($mediaFolderPath.'/'.$article.'/'.'index.html')){
            JFile::write($mediaFolderPath.'/'.$article.'/'.'index.html',$html);
        }
        if(!JFolder::exists($mediaFolderPath.'/'.$article.'/'.$cache)){
            JFolder::create($mediaFolderPath.'/'.$article.'/'.$cache);
        }
        if(!JFile::exists($mediaFolderPath.'/'.$article.'/'.$cache.'/'.'index.html')){
            JFile::write($mediaFolderPath.'/'.$article.'/'.$cache.'/'.'index.html',$html);
        }
        if(!JFolder::exists($mediaFolderPath.'/'.$article.'/'.$src)){
            JFolder::create($mediaFolderPath.'/'.$article.'/'.$src);
        }
        if(!JFile::exists($mediaFolderPath.'/'.$article.'/'.$src.'/'.'index.html')){
            JFile::write($mediaFolderPath.'/'.$article.'/'.$src.'/'.'index.html',$html);
        }
        //Install plugins
        $status = new stdClass;
        $status->modules = array();
        $src = $parent->getParent()->getPath('source');

        if(version_compare( JVERSION, '1.6.0', 'ge' )) {
            $modules = $parent->getParent()->manifest->xpath('modules/module');

            foreach($modules as $module){
                $result = null;
                $mname = $module->attributes() -> module;
                $mname = (string)$mname;
                $client = $module->attributes() -> client;
                if(is_null($client)) $client = 'site';
                ($client=='administrator')? $path=$src.'/'.'administrator'.'/'.'modules'.'/'.$mname: $path = $src.'/'.'modules'.'/'.$mname;
                $installer = new JInstaller();
                $result = $installer->install($path);
                $status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
            }

            $plugins = $parent->getParent()->manifest->xpath('plugins/plugin');
            foreach($plugins as $plugin){
                $result = null;
                $folder = null;
                $pname  = $plugin->attributes() -> plugin;
                $pname  = (string) $pname;
                $group  = $plugin->attributes() -> group;
                $folder = $plugin -> attributes() -> folder;
                if(isset($folder)){
                    $folder = $plugin -> attributes() -> folder;
                }
                $path   = $src.'/'.'plugins'.'/'.$group.'/'.$folder;

                $installer = new JInstaller();
                $result = $installer->install($path);
                $status->plugins[] = array('name'=>$pname,'group'=>$group, 'result'=>$result);
            }

            $query  = 'UPDATE #__extensions SET `enabled`=1 WHERE `type`="plugin" AND `element`="tz_portfolio" AND `folder`="system"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=1 WHERE `type`="plugin" AND `element`="tz_portfolio" AND `folder`="user"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=1 WHERE `type`="plugin" AND `element`="tz_portfolio_comment" AND `folder`="content"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=1 WHERE `type`="plugin" AND `element`="tz_portfolio_vote" AND `folder`="content"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=1 WHERE `type`="plugin" AND `element`="tz_portfolio_content" AND `folder`="search"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=1 WHERE `type`="plugin" AND `element`="tz_portfolio_categories" AND `folder`="search"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=0 WHERE `type`="plugin" AND `element`="vote" AND `folder`="content"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=0 WHERE `type`="plugin" AND `element`="content" AND `folder`="search"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=0 WHERE `type`="plugin" AND `element`="categories" AND `folder`="search"';
            $db -> setQuery($query);
            $db -> query();

            $query  = 'UPDATE #__extensions SET `enabled`=0 WHERE `type`="plugin" AND `element`="example" AND `folder`="tz_portfolio"';
            $db -> setQuery($query);
            $db -> query();

            // Insert default template
            $template_sql   = 'SELECT COUNT(*) FROM #__tz_portfolio_templates';
            $db -> setQuery($template_sql);
            if(!$db -> loadResult()){
                $def_file   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/views/template/tmpl/default.json';
                if(JFile::exists($def_file)){
                    $def_value      = JFile::read($def_file);
                    $template_sql2  = 'INSERT INTO `#__tz_portfolio_templates`(`id`, `title`, `home`, `params`) VALUES(1, \'Default\', \'1\',\''.$def_value.'\')';
                    $db -> setQuery($template_sql2);
                    $db -> query();
                }
            }
        }

        // Delete menu fields-group in back-end
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from($db -> quoteName('#__menu'));
        $query -> where($db -> quoteName('menutype').'='.$db -> quote('main'));
        $query -> where($db -> quoteName('link').'='.$db -> quote('index.php?option=com_tz_portfolio&view=fieldsgroup'));
        $query -> where($db -> quoteName('type').'='.$db -> quote('component'));
        $db -> setQuery($query);
        if($db -> loadResult()){
            $query2  = $db -> getQuery(true);
            $query2 -> delete($db -> quoteName('#__menu'));
            $query2 -> where($db -> quoteName('menutype').'='.$db -> quote('main'));
            $query2 -> where($db -> quoteName('link').'='.$db -> quote('index.php?option=com_tz_portfolio&view=fieldsgroup'));
            $query2 -> where($db -> quoteName('type').'='.$db -> quote('component'));
            $db -> setQuery($query2);
            $db -> execute();
        }
        // End Delete menu fields-group in back-end

        // Delete files and folder fields group in back-end
        $cadPath    = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio';
        if(JFile::exists($cadPath.'/controllers/fieldgroup.php')){
            JFile::delete($cadPath.'/controllers/fieldgroup.php');
        }
        if(JFile::exists($cadPath.'/controllers/fieldsgroup.php')){
            JFile::delete($cadPath.'/controllers/fieldsgroup.php');
        }
        if(JFile::exists($cadPath.'/models/fieldgroup.php')){
            JFile::delete($cadPath.'/models/fieldgroup.php');
        }
        if(JFile::exists($cadPath.'/models/fieldsgroup.php')){
            JFile::delete($cadPath.'/models/fieldsgroup.php');
        }
        if(JFile::exists($cadPath.'/tables/fieldsgroup.php')){
            JFile::delete($cadPath.'/tables/fieldsgroup.php');
        }
        if(JFolder::exists($cadPath.'/views/fieldgroup')){
            JFolder::delete($cadPath.'/views/fieldgroup');
        }
        if(JFolder::exists($cadPath.'/views/fieldsgroup')){
            JFolder::delete($cadPath.'/views/fieldsgroup');
        }
        // End Delete files and folder fields group in back-end

        $this -> installationResult($status);

    }
    function uninstall($parent){
        $mediaFolder    = 'tz_portfolio';
        $mediaFolderPath    = JPATH_SITE.'/'.'media'.'/'.$mediaFolder;
        if(JFolder::exists($mediaFolderPath)){
            JFolder::delete($mediaFolderPath);
        }
        $imageFolderPath    = JPATH_SITE.'/'.'images'.'/'.$mediaFolder;
        if(JFolder::exists($imageFolderPath)){
            JFolder::delete($imageFolderPath);
        }

        $status = new stdClass();
        $status->modules = array ();
        $status->plugins = array ();

        $_parent    = $parent -> getParent();
        $modules = $_parent -> manifest -> xpath('modules/module');
        $plugins = $_parent -> manifest -> xpath('plugins/plugin');

        $result = null;
        if($modules){
            foreach($modules as $module){
                $mname = (string)$module->attributes() -> module;
                $client = (string)$module->attributes() -> client;

                $db = JFactory::getDBO();
                $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='module' AND `element` = ".$db->Quote($mname)."";
                $db->setQuery($query);
                $IDs = $db->loadColumn();
                if (count($IDs)) {
                    foreach ($IDs as $id) {
                        $installer = new JInstaller;
                        $result = $installer->uninstall('module', $id);
                    }
                }
                $status->modules[] = array ('name'=>$mname, 'client'=>$client, 'result'=>$result);
            }
        }

        if($plugins){
            foreach ($plugins as $plugin) {

                $pname = (string)$plugin->attributes() -> plugin;
                $pgroup = (string)$plugin->attributes() -> group;

                $db = JFactory::getDBO();
                $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND `element` = "
                    .$db->Quote($pname)." AND `folder` = ".$db->Quote($pgroup);
                $db->setQuery($query);
                $IDs = $db->loadColumn();
                if (count($IDs)) {
                    foreach ($IDs as $id) {
                        $installer = new JInstaller;
                        $result = $installer->uninstall('plugin', $id);
                    }
                }
                $status->plugins[] = array ('name'=>$pname, 'group'=>$pgroup, 'result'=>$result);
            }
        }
        $this -> uninstallationResult($status);
    }

    function update($adapter){
        $db     = JFactory::getDbo();
        $arr    = null;
        $listTable  = array(
            $db -> replacePrefix('#__tz_portfolio_xref'),
            $db -> replacePrefix('#__tz_portfolio_fields_group'),
            $db -> replacePrefix('#__tz_portfolio_fields'),
            $db -> replacePrefix('#__tz_portfolio_categories'),
            $db -> replacePrefix('#__tz_portfolio'),
            $db -> replacePrefix('#__tz_portfolio_xref_content'),
            $db -> replacePrefix('#__tz_portfolio_tags'),
            $db -> replacePrefix('#__tz_portfolio_plugin'),
            $db -> replacePrefix('#__tz_portfolio_templates')
        );
        $disableTables  = array_diff($listTable,$db -> getTableList());

        if(count($disableTables)){
            $installer  = JInstaller::getInstance();
            $sql        = $adapter -> getParent() -> manifest;
            $installer ->parseSQLFiles($sql -> install->sql);
        }

        $fields = $db -> getTableColumns('#__tz_portfolio_categories');

        if(!array_key_exists('template_id',$fields)){
            $arr[]  = 'ADD `template_id` INT UNSIGNED NOT NULL';
        }

        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_categories` '.$arr;
                $db -> setQuery($query);
                $db -> query();
            }
        }

        $arr    = null;
        $fields = $db -> getTableColumns('#__tz_portfolio_xref_content');

        if(!array_key_exists('gallery',$fields)){
            $arr[]  = 'ADD `gallery` TEXT NOT NULL';
        }
        if(!array_key_exists('gallerytitle',$fields)){
            $arr[]  = 'ADD `gallerytitle` TEXT NOT NULL';
        }
        if(!array_key_exists('video',$fields)){
            $arr[]  = 'ADD `video` TEXT NOT NULL';
        }
        if(!array_key_exists('videotitle',$fields)){
            $arr[]  = 'ADD `videotitle` TEXT NOT NULL';
        }
        if(!array_key_exists('type',$fields)){
            $arr[]  = 'ADD `type` VARCHAR(25)';
        }
        if(!array_key_exists('videothumb',$fields)){
            $arr[]  = 'ADD `videothumb` TEXT';
        }
        if(!array_key_exists('images_hover',$fields)){
            $arr[]  = 'ADD `images_hover` TEXT';
        }
        if(!array_key_exists('audio',$fields)){
            $arr[]  = 'ADD `audio` TEXT';
        }
        if(!array_key_exists('audiothumb',$fields)){
            $arr[]  = 'ADD `audiothumb` TEXT';
        }
        if(!array_key_exists('audiotitle',$fields)){
            $arr[]  = 'ADD `audiotitle`  VARCHAR(255)';
        }
        if(!array_key_exists('quote_author',$fields)){
            $arr[]  = 'ADD `quote_author`  VARCHAR(255)';
        }
        if(!array_key_exists('quote_text',$fields)){
            $arr[]  = 'ADD `quote_text`  TEXT';
        }
        if(!array_key_exists('link_url',$fields)){
            $arr[]  = 'ADD `link_url`  VARCHAR(1000)';
        }
        if(!array_key_exists('link_title',$fields)){
            $arr[]  = 'ADD `link_title`  VARCHAR(1000)';
        }
        if(!array_key_exists('link_attribs',$fields)){
            $arr[]  = 'ADD `link_attribs`  VARCHAR(5120)';
        }
        if(!array_key_exists('template_id',$fields)){
            $arr[]  = 'ADD `template_id` INT UNSIGNED NOT NULL';
        }
        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_xref_content` '.$arr;
                $db -> setQuery($query);
                $db -> query();
            }
        }

        $fields2 = $db -> getTableColumns('#__tz_portfolio');
        $arr2   = null;
        if(!array_key_exists('ordering',$fields2)){
            $arr2[]  = 'ADD `ordering` INT NOT NULL';
        }
        if($arr2 && count($arr2)>0){
            $arr2    = implode(',',$arr2);
            if($arr2){
                $query  = 'ALTER TABLE `#__tz_portfolio` '.$arr2;
                $db -> setQuery($query);
                $db -> query();
            }
        }

        //TZ Categories
        $fields = $db -> getTableColumns('#__tz_portfolio_categories');
        if(!array_key_exists('images',$fields)){
            $query  = 'ALTER TABLE `#__tz_portfolio_categories` ADD `images` TEXT NOT NULL';
            $db -> setQuery($query);
            $db -> query();
        }

        // extra fields
        $arr    = null;
        $fields = $db -> getTableColumns('#__tz_portfolio_fields');
        if(!array_key_exists('default_value',$fields)){
            $arr[]  = 'ADD `default_value` TEXT NOT NULL';
        }
        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_fields` '.$arr;
                $db -> setQuery($query);
                $db -> query();
            }
        }

        // tags
        $arr    = null;
        $fields = $db -> getTableColumns('#__tz_portfolio_tags');
        if(!array_key_exists('attribs',$fields)){
            $arr[]  = 'ADD `attribs` VARCHAR(5120) NOT NULL ';
        }
        if($arr && count($arr)>0){
            $arr    = implode(',',$arr);
            if($arr){
                $query  = 'ALTER TABLE `#__tz_portfolio_tags` '.$arr;
                $db -> setQuery($query);
                $db -> query();
            }
        }

        // Insert default template
        $template_sql   = 'SELECT COUNT(*) FROM #__tz_portfolio_templates';
        $db -> setQuery($template_sql);
        if(!$db -> loadResult()){
            $def_file   = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/views/template/tmpl/default.json';
            if(JFile::exists($def_file)){
                $def_value      = JFile::read($def_file);
                $template_sql2  = 'INSERT INTO `#__tz_portfolio_templates`(`id`, `title`, `home`, `params`) VALUES(1, \'Default\', \'1\',\''.$def_value.'\')';
                $db -> setQuery($template_sql2);
                $db -> query();
            }
        }

        //Tz Portfolio Plugin table
//        if(!in_array($db -> getPrefix().'tz_portfolio_plugin',$fields = $db ->getTableList())){
//            $query  =  'CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plugin` (';
//            $query  .= '`id`  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,';
//            $query  .= '`contentid` INT NOT NULL ,';
//            $query  .= '`pluginid` INT NOT NULL,';
//            $query  .= '`params` TEXT NULL';
//            $query  .= ') ENGINE = MYISAM  DEFAULT CHARSET=utf8;';
//            $db -> setQuery($query);
//            $db -> query();
//        }

        // Insert portfolio's permission
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from('#__assets');
        $query -> where('name LIKE "com_tz_portfolio.%"');
        $db -> setQuery($query);

        if(!$db -> loadResult()){
            $query  = $db -> getQuery(true);
            $query -> select('*');
            $query -> from('#__assets');
            $query -> where('name LIKE "com_content.%"');
            $db -> setQuery($query);
            if($rows   = $db -> loadAssocList()){
                $query2 = $db -> getQuery(true);
                $query2 -> insert('#__assets');

                foreach($rows as $i => &$item){
                    array_shift($item);
                    if($i == 0){
                        $keys = array_keys($item);
                    }
                    $item['name']   = $db -> quote($item['name']);
                    $item['title'] = $db -> quote($item['title']);
                    $item['rules']  = $db -> quote($item['rules']);
                    $query2 -> values(str_replace('com_content','com_tz_portfolio',implode(',',$item)));
                }
                $query2 -> columns(implode(',',$keys));

                $db -> setQuery($query2);
                $db -> execute();
            }
        }

    }

    public function installationResult($status){
        $lang   = JFactory::getLanguage();
        $lang -> load('com_tz_portfolio');
        $rows   = 0;
        ?>
        <h2><?php echo JText::_('COM_TZ_PORTFOLIO_HEADING_INSTALL_STATUS'); ?></h2>
        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th class="title" colspan="2"><?php echo JText::_('COM_TZ_PORTFOLIO_EXTENSION'); ?></th>
                <th width="30%"><?php echo JText::_('COM_TZ_PORTFOLIO_STATUS'); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="3"></td>
            </tr>
            </tfoot>
            <tbody>
            <tr class="row0">
                <td class="key" colspan="2"><?php echo JText::_('COM_TZ_PORTFOLIO').' '.JText::_('COM_TZ_PORTFOLIO_COMPONENT'); ?></td>
                <td><span style="color: green; font-weight: bold;"><?php echo JText::_('COM_TZ_PORTFOLIO_INSTALLED'); ?></span></td>
            </tr>
            <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                    <?php
                    if(!$lang -> exists((string) $module['name'],JPATH_SITE)):
                        $lang -> load((string)$module['name'],JPATH_SITE);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_($module['name']); ?></td>
                        <td class="key"><?php echo ucfirst($module['client']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($module['result'])?JText::_('COM_TZ_PORTFOLIO_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_NOT_INSTALLED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (count($status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                    <?php
                    if(!$lang -> exists((string)$plugin['name'],JPATH_ADMINISTRATOR, null, true)):
                        $lang -> load((string)$plugin['name'],JPATH_ADMINISTRATOR, null, true);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_(ucfirst($plugin['name'])); ?></td>
                        <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($plugin['result'])?JText::_('COM_TZ_PORTFOLIO_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_NOT_INSTALLED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isset($status -> languages) AND count($status->languages)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_LANGUAGES'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_COUNTRY'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->languages as $language): ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo ucfirst($language['language']); ?></td>
                        <td class="key"><?php echo ucfirst($language['country']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($language['result'])?JText::_('COM_TZ_PORTFOLIO_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_NOT_INSTALLED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            </tbody>
        </table>
    <?php
    }
    function uninstallationResult($status){
        $lang   = JFactory::getLanguage();
        $lang -> load('com_tz_portfolio');
        $rows   = 0;
        ?>
        <h2><?php echo JText::_('COM_TZ_PORTFOLIO_HEADING_REMOVE_STATUS'); ?></h2>
        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th class="title" colspan="2"><?php echo JText::_('COM_TZ_PORTFOLIO_EXTENSION'); ?></th>
                <th width="30%"><?php echo JText::_('COM_TZ_PORTFOLIO_STATUS'); ?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="3"></td>
            </tr>
            </tfoot>
            <tbody>
            <tr class="row0">
                <td class="key" colspan="2"><?php echo JText::_('COM_TZ_PORTFOLIO').' '.JText::_('COM_TZ_PORTFOLIO_COMPONENT'); ?></td>
                <td><span style="color: green; font-weight: bold;"><?php echo JText::_('COM_TZ_PORTFOLIO_REMOVED'); ?></span></td>
            </tr>
            <?php if (count($status->modules)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_MODULE'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_CLIENT'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->modules as $module): ?>
                    <?php
                    if($lang -> exists($module['name'])):
                        $lang -> load($module['name']);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_($module['name']); ?></td>
                        <td class="key"><?php echo ucfirst($module['client']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($module['result'])?JText::_('COM_TZ_PORTFOLIO_REMOVED'):JText::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (count($status->plugins)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_PLUGIN'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_GROUP'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->plugins as $plugin): ?>
                    <?php
                    if($lang -> exists($plugin['name'])):
                        $lang -> load($plugin['name']);
                    endif;
                    ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo JText::_(ucfirst($plugin['name'])); ?></td>
                        <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($plugin['result'])?JText::_('COM_TZ_PORTFOLIO_REMOVED'):JText::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (isset($status -> languages) AND count($status->languages)): ?>
                <tr>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_LANGUAGES'); ?></th>
                    <th><?php echo JText::_('COM_TZ_PORTFOLIO_COUNTRY'); ?></th>
                    <th></th>
                </tr>
                <?php foreach ($status->languages as $language): ?>
                    <tr class="row<?php echo (++ $rows % 2); ?>">
                        <td class="key"><?php echo ucfirst($language['language']); ?></td>
                        <td class="key"><?php echo ucfirst($language['country']); ?></td>
                        <td><span style="color: green; font-weight: bold;"><?php echo ($language['result'])?JText::_('COM_TZ_PORTFOLIO_REMOVED'):JText::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    <?php
    }
}
?>