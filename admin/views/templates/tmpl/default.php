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

JHtml::_('formbehavior.chosen', 'select');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$user		= JFactory::getUser();
$lang       = JFactory::getLanguage();
$lang -> load('com_installer');
?>
<style>
    .tz_portfolio-templates .thumbnail > img{
        max-width: 80px;
    }
</style>
<form action="index.php?option=com_tz_portfolio&view=templates" method="post" name="adminForm"
      class="tz_portfolio-templates"
      id="adminForm">
    <?php if(!empty($this -> sidebar) AND COM_TZ_PORTFOLIO_JVERSION_COMPARE):?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this -> sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else:?>
        <div id="j-main-container">
    <?php endif;?>

        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL')?></label>
                <input type="text" title="<?php echo JText::_('COM_TZ_PORTFOLIO_TAGS_SEARCH_DESC');?>"
                       value="<?php echo $this -> state -> filter_search;?>"
                       placeholder="<?php echo JText::_('COM_TZ_PORTFOLIO_SEARCH_IN_NAME'); ?>"
                       id="filter_search"
                       name="filter_search"/>
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button onclick="document.getElementById('filter_search').value='';this.form.submit();"
                        type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" >
                    <i class="icon-remove"></i>
                </button>
            </div>
            <?php if(COM_TZ_PORTFOLIO_JVERSION_COMPARE): //If the joomla's version is 3.0 ?>
                <div class="btn-group pull-right hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                    <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                        <option value="asc" <?php if ($this -> state -> filter_order_Dir == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                        <option value="desc" <?php if ($this -> state -> filter_order_Dir == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                    </select>
                </div>
                <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                        <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $this -> state -> filter_order);?>
                    </select>
                </div>
            <?php endif;?>

            <?php // If the joomla's version is more than or equal to 3.0 ?>
            <?php if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE):?>
                <div class="filter-select pull-right">
                    <?php echo $this->sidebar; ?>
                </div>
            <?php endif;?>
        </div>

        <table class="table table-striped" id="templatesList">
            <thead>
            <tr>
                <th width="1%">

                </th>
                <th class="col1template hidden-phone">
                    <?php echo JText::_('COM_TZ_PORTFOLIO_THUMBNAIL');?>
                </th>
                <th class="title">
                    <?php echo JHtml::_('grid.sort','COM_TZ_PORTFOLIO_TEMPLATE_LABEL','name',$this -> state -> filter_order_Dir,$this -> state -> filter_order);?>
                </th>
                <th width="10%" class="nowrap center">
                    <?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $this -> state -> filter_order_Dir, $this -> state -> filter_order); ?>
                </th>
                <th width="10%" class="nowrap center">
                    <?php echo JText::_('COM_TZ_PORTFOLIO_HEADING_TYPE');?>
<!--                    --><?php //echo JHtml::_('grid.sort', 'COM_TZ_PORTFOLIO_HEADING_TYPE', 'type', $this -> state -> filter_order_Dir, $this -> state -> filter_order); ?>
                </th>
                <th class="nowrap center" width="10%">
                    <?php echo JText::_('JVERSION'); ?>
                </th>
                <th class="nowrap center" width="15%">
                    <?php echo JText::_('JDATE'); ?>
                </th>
                <th class="nowrap" width="25%">
                    <?php echo JText::_('JAUTHOR'); ?>
                </th>
                <th class="nowrap" width="1%">
                    <?php echo JHtml::_('grid.sort','JGRID_HEADING_ID','id',$this -> state -> filter_order_Dir,$this -> state -> filter_order);?>
                </th>
            </tr>
            </thead>

            <?php if($this -> items):?>
                <tbody>
                <?php foreach($this -> items as $i => $item):

                    $canCreate = $user->authorise('core.create',     'com_tz_portfolio');
                    $canEdit   = $user->authorise('core.edit',       'com_tz_portfolio');
                    $canChange = $user->authorise('core.edit.state', 'com_tz_portfolio');

                ?>
                    <tr class="<?php echo ($i%2==0)?'row0':'row1';?>">
                        <td class="center">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center hidden-phone">
                            <?php echo JHtml::_('templates.thumb', $item->name); ?>
                        </td>
                        <td class="nowrap has-context">
                            <div class="pull-left">
<!--                                <a href="index.php?option=com_tz_portfolio&task=template.edit&id=--><?php //echo $item -> id;?><!--">-->
                                    <?php echo $item->name; ?>
<!--                                </a>-->
                            </div>
                        </td>

                        <td class="center">
                            <?php
                            $states	= array(
                                2 => array(
                                    '',
                                    'COM_INSTALLER_EXTENSION_PROTECTED',
                                    '',
                                    'COM_INSTALLER_EXTENSION_PROTECTED',
                                    true,
                                    'protected',
                                    'protected',
                                ),
                                1 => array(
                                    'unpublish',
                                    'COM_INSTALLER_EXTENSION_ENABLED',
                                    'COM_INSTALLER_EXTENSION_DISABLE',
                                    'COM_INSTALLER_EXTENSION_ENABLED',
                                    true,
                                    'publish',
                                    'publish',
                                ),
                                0 => array(
                                    'publish',
                                    'COM_INSTALLER_EXTENSION_DISABLED',
                                    'COM_INSTALLER_EXTENSION_ENABLE',
                                    'COM_INSTALLER_EXTENSION_DISABLED',
                                    true,
                                    'unpublish',
                                    'unpublish',
                                ),
                            );
                            if($item ->protected){
                                echo JHtml::_('jgrid.state', $states, 2, $i, 'template.', false, true, 'cb');
                            }else{
                                echo JHtml::_('jgrid.state', $states, $item->published, $i, 'templates.', true, true, 'cb');
                            }
                            ?>
                        </td>
                        <td class="center">
                            <?php echo $item -> type;?>
                        </td>
                        <td class="center hidden-phone">
                            <?php echo @$item -> version != '' ? $item -> version : '&#160;';?>
                        </td>
                        <td class="center hidden-phone">

                            <?php echo @$item-> creationDate != '' ? $item-> creationDate : '&#160;'; ?>
                        </td>
                        <td class="hidden-phone">
                            <?php if ($author = $item-> author) : ?>
                                <p><?php echo $this->escape($author); ?></p>
                            <?php else : ?>
                                &mdash;
                            <?php endif; ?>
                            <?php if ($email = $item->authorEmail) : ?>
                                <p><?php echo $this->escape($email); ?></p>
                            <?php endif; ?>
                            <?php if ($url = $item->authorUrl) : ?>
                                <p><a href="<?php echo $this->escape($url); ?>">
                                        <?php echo $this->escape($url); ?></a></p>
                            <?php endif; ?>
                        </td>

                        <td align="center hidden-phone"><?php echo $item -> id;?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            <?php endif;?>

            <tfoot>
            <tr>
                <td colspan="11">
                    <?php echo $this -> pagination -> getListFooter();?>
                </td>
            </tr>
            </tfoot>

        </table>

        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <input type="hidden" name="filter_order" value="<?php echo $this -> state -> filter_order;?>">
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this -> state -> filter_order_Dir;?>">
        <?php echo JHtml::_('form.token');?>

    </div>
</form>