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

$user		= JFactory::getUser();
?>
<form action="index.php?option=com_tz_portfolio&view=templates" method="post" name="adminForm" id="adminForm">
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
<!--                <th width="1%">--><?php //echo JText::_('#');?><!--</th>-->
                <th width="1%">
<!--                    <input type="checkbox" name="checkall-toggle" value=""-->
<!--                           title="--><?php //echo JText::_('JGLOBAL_CHECK_ALL'); ?><!--"-->
<!--                           onclick="Joomla.checkAll(this)"/>-->
                </th>
<!--                <th width="1%" style="min-width:55px" class="nowrap center">-->
<!--                    --><?php //echo JHtml::_('grid.sort', 'COM_TEMPLATES_HEADING_TEMPLATE', 'title', $this -> state -> filter_order_Dir, $this -> state -> filter_order); ?>
<!--                </th>-->
                <th class="title">
                    <?php echo JHtml::_('grid.sort','COM_TZ_PORTFOLIO_HEADING_NAME','name',$this -> state -> filter_order_Dir,$this -> state -> filter_order);?>
                </th>
                <th width="1%" style="min-width:55px" class="nowrap center">
                    <?php echo JHtml::_('grid.sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'home', $this -> state -> filter_order_Dir, $this -> state -> filter_order); ?>
                </th>
                <th nowrap="nowrap" width="1%">
                    <?php echo JText::_('COM_TZ_PORTFOLIO_HEADING_ASSIGNED'); ?>
                </th>
                <th nowrap="nowrap" width="1%">
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
                        <td class="nowrap has-context">
                            <div class="pull-left">
                                <a href="index.php?option=com_tz_portfolio&task=template_style.edit&id=<?php echo $item -> id;?>">
                                    <?php echo $item -> title;?>
                                </a>
                            </div>
                        </td>

                        <td class="center">
                            <?php if ($item->home == '0' || $item->home == '1'):?>
                                <?php echo JHtml::_('jgrid.isdefault', $item->home != '0', $i, 'template_styles.', $canChange && $item->home != '1');?>
                            <?php elseif ($canChange):?>
                                <a href="<?php echo JRoute::_('index.php?option=com_tz_portfolio&task=template_styles.unsetDefault&cid[]='.$item->id.'&'.JSession::getFormToken().'=1');?>">
                                    <?php echo JHtml::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title' => JText::sprintf('COM_TEMPLATES_GRID_UNSET_LANGUAGE', $item->language_title)), true);?>
                                </a>
                            <?php else:?>
                                <?php echo JHtml::_('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title' => $item->language_title), true);?>
                            <?php endif;?>
                        </td>

                        <td align="center">
                            <?php if((isset($item -> category_assigned) AND $item -> category_assigned > 0)
                                OR (isset($item -> content_assigned) AND $item -> content_assigned > 0)
                                OR (isset($item -> menu_assigned) AND $item -> menu_assigned > 0)):?>
                            <i class="icon-ok tip hasTooltip" title="<?php echo JText::plural('COM_TZ_PORTFOLIO_ASSIGNED_MORE',$item -> menu_assigned, $item->category_assigned,$item -> content_assigned); ?>"></i>
                            <?php endif;?>
                        </td>
                        <td align="center"><?php echo $item -> id;?></td>
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