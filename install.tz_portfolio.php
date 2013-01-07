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
JFactory::getLanguage() -> load('com_tz_portfolio');

$mediaFolder    = 'tz_portfolio';
$mediaFolderPath    = JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.$mediaFolder;
$article    = 'article';
$cache      = 'cache';
$src        = 'src';

if(!JFolder::exists($mediaFolderPath)){
    JFolder::create($mediaFolderPath);
}
if(!JFile::exists($mediaFolderPath.DIRECTORY_SEPARATOR.'index.html')){
    JFile::write($mediaFolderPath.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
}
if(!JFolder::exists($mediaFolderPath.DIRECTORY_SEPARATOR.$article)){
    JFolder::create($mediaFolderPath.DIRECTORY_SEPARATOR.$article);
}
if(!JFile::exists($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.'index.html')){
    JFile::write($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
}
if(!JFolder::exists($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.$cache)){
    JFolder::create($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.$cache);
}
if(!JFile::exists($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.$cache.DIRECTORY_SEPARATOR.'index.html')){
    JFile::write($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.$cache.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
}
if(!JFolder::exists($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.$src)){
    JFolder::create($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.$src);
}
if(!JFile::exists($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.$src.DIRECTORY_SEPARATOR.'index.html')){
    JFile::write($mediaFolderPath.DIRECTORY_SEPARATOR.$article.DIRECTORY_SEPARATOR.$src.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
}


//$imageFolderPath    = JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$mediaFolder;
//if(!JFolder::exists($imageFolderPath)){
//    JFolder::create($imageFolderPath);
//}
//if(!JFile::exists($imageFolderPath.'/index.html')){
//    JFile::write($imageFolderPath.'/index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
//}

$db     = &JFactory::getDbo();
$lang   = &JFactory::getLanguage();
$lang ->load('com_tz_portfolio');
$status = new JObject();
$status->modules = array();
$src = $this->parent->getPath('source');

if(version_compare( JVERSION, '1.6.0', 'ge' )) {
    $modules = &$this->manifest->xpath('modules/module');
    foreach($modules as $module){
        $result = null;
        $mname = $module->getAttribute('module');
        $client = $module->getAttribute('client');
        if(is_null($client)) $client = 'site';
        ($client=='administrator')? $path=$src.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$mname: $path = $src.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$mname;
        $installer = new JInstaller();
        $result = $installer->install($path);
        $status->modules[] = array('name'=>$mname,'client'=>$client, 'result'=>$result);
    }

    $plugins = &$this->manifest->xpath('plugins/plugin');
    foreach($plugins as $plugin){
        $result = null;
        $folder = null;
        $pname  = $plugin->getAttribute('plugin');
        $group  = $plugin->getAttribute('group');
        $folder = $plugin -> getAttribute('folder');
        if(isset($folder)){
            $folder = $plugin -> getAttribute('folder');
        }
        $path   = $src.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$group.DIRECTORY_SEPARATOR.$folder;

        $installer = new JInstaller();
        $result = $installer->install($path);
        $status->plugins[] = array('name'=>$pname,'group'=>$group, 'result'=>$result);
    }

    if($languages = &$this->manifest->xpath('languagePackage/language')){
        foreach($languages as $language){
            $result     = null;
            $country    = null;
            $lname      = $language->getAttribute('folder');
            if($language -> getAttribute('language')){
                $country    = $language -> getAttribute('language');
            }

            $path   = $src.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$lname;
            $installer = new JInstaller();
            $result = $installer->install($path);
            $status-> languages[] = array('language'=>$lname,'country'=>$country, 'result'=>$result);
        }
    }

}
?>

<?php $rows = 0; ?>
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
			<td><strong><?php echo JText::_('COM_TZ_PORTFOLIO_INSTALLED'); ?></strong></td>
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
			<td><strong><?php echo ($module['result'])?JText::_('COM_TZ_PORTFOLIO_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_NOT_INSTALLED'); ?></strong></td>
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
			<td><strong><?php echo ($plugin['result'])?JText::_('COM_TZ_PORTFOLIO_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_NOT_INSTALLED'); ?></strong></td>
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
			<td><strong><?php echo ($language['result'])?JText::_('COM_TZ_PORTFOLIO_INSTALLED'):JText::_('COM_TZ_PORTFOLIO_NOT_INSTALLED'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>

	</tbody>
</table>