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

$lang           = JFactory::getLanguage();
$lang -> load('com_tz_portfolio');
$mediaFolder    = 'tz_portfolio';
$mediaFolderPath    = JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.$mediaFolder;
if(JFolder::exists($mediaFolderPath)){
    JFolder::delete($mediaFolderPath);
}
$imageFolderPath    = JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$mediaFolder;
if(JFolder::exists($imageFolderPath)){
    JFolder::delete($imageFolderPath);
}

$status = new JObject();
$status->modules = array ();
$status->plugins = array ();

$modules = & $this->manifest->xpath('modules/module');
$plugins = & $this->manifest->xpath('plugins/plugin');
$languages = &$this->manifest->xpath('languagePackage/language');

if($modules){
    foreach($modules as $module){
        $mname = $module->getAttribute('module');
        $client = $module->getAttribute('client');

        $db = & JFactory::getDBO();
        $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='module' AND `element` = ".$db->Quote($mname)."";
        $db->setQuery($query);
        $IDs = $db->loadResultArray();
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

        $pname = $plugin->getAttribute('plugin');
        $pgroup = $plugin->getAttribute('group');

        $db = & JFactory::getDBO();
        $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND `element` = "
                 .$db->Quote($pname)." AND `folder` = ".$db->Quote($pgroup);
        $db->setQuery($query);
        $IDs = $db->loadResultArray();
        if (count($IDs)) {
            foreach ($IDs as $id) {
                $installer = new JInstaller;
                $result = $installer->uninstall('plugin', $id);
            }
        }
        $status->plugins[] = array ('name'=>$pname, 'group'=>$pgroup, 'result'=>$result);
    }
}

if($languages){
    foreach ($languages as $language) {

        $lname  = $language->getAttribute('language');
        $folder = $language->getAttribute('folder');

        $db = & JFactory::getDBO();
        $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='file' AND `element` = 'install'";
        $db->setQuery($query);
        $IDs = $db->loadResultArray();
        if (count($IDs)) {
            foreach ($IDs as $id) {
                $installer = new JInstaller;
                $result = $installer->uninstall('file', $id);
            }
        }
        $status->languages[] = array ('language'=>$folder, 'country'=>$lname, 'result'=>$result);
    }
}
?>

<?php $rows = 0; ?>
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
			<td><strong><?php echo JText::_('COM_TZ_PORTFOLIO_REMOVED'); ?></strong></td>
		</tr>
		<?php if (count($status->modules)): ?>
		<tr>
			<th><?php echo JText::_('COM_TZ_PORTFOLIO_MODULE'); ?></th>
			<th><?php echo JText::_('COM_TZ_PORTFOLIO_CLIENT'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module): ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo ($module['result'])?JText::_('COM_TZ_PORTFOLIO_REMOVED'):JText::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></strong></td>
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
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo ($plugin['result'])?JText::_('COM_TZ_PORTFOLIO_REMOVED'):JText::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>

        <?php if (count($status->languages)): ?>
		<tr>
			<th><?php echo JText::_('COM_TZ_PORTFOLIO_LANGUAGES'); ?></th>
			<th><?php echo JText::_('COM_TZ_PORTFOLIO_COUNTRY'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->languages as $language): ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($language['language']); ?></td>
			<td class="key"><?php echo ucfirst($language['country']); ?></td>
			<td><strong><?php echo ($language['result'])?JText::_('COM_TZ_PORTFOLIO_REMOVED'):JText::_('COM_TZ_PORTFOLIO_NOT_REMOVED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>