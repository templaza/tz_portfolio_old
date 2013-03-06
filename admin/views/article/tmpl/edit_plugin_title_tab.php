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

// No direct access.
defined('_JEXEC') or die;
$lang   = JFactory::getLanguage();
$lang -> load('com_plugins');
?>
<?php if($pluginsTab = $this -> pluginsTab):?>
    <?php foreach($pluginsTab as $name => $pFields):?>
        <?php
            $pluginName = substr($pFields -> getName(),strrpos($pFields -> getName(),'.') + 1,strlen($pFields -> getName()));
            //Load this plugin language
            $lang -> load($pluginName,JPATH_BASE,null,false,false);
        ?>
        <li>
            <a href="#tztabsplugins<?php echo $name;?>" data-toggle="tab">
                <?php echo JText::_($pluginName);?>
            </a>
        </li>
    <?php endforeach;?>
<?php endif;?>
 
