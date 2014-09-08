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

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');

JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_tz_portfolio.article');
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tz_portfolio&task=articles.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();

$assoc		= false;
if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
    $assoc		= JLanguageAssociations::isEnabled();
}
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&view=articles');?>" method="post" name="adminForm" id="adminForm">
    <?php if(!empty( $this->sidebar) AND COM_TZ_PORTFOLIO_JVERSION_COMPARE): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>
            <div id="filter-bar" class="btn-toolbar">
                <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>
                    <input type="text" name="filter_search" placeholder="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
                </div>
                <div class="btn-group pull-left hidden-phone">
                    <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button class="btn hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
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
                        <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                        <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                    </select>
                </div>
                <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                        <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                    </select>
                </div>
                <?php endif;?>
            </div>

            <?php // If the joomla's version is more than or equal to 3.0 ?>
            <?php if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE):?>
            <div class="filter-select">
                <?php echo $this->sidebar; ?>
            </div>
            <?php endif;?>

            <div class="clearfix"> </div>
            <table class="table table-striped" id="articleList">
                <thead>
                    <tr>
                        <th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                        </th>
                        <th width="1%" class="hidden-phone">
                            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                        </th>
                        <th width="1%" style="min-width:55px" class="nowrap center">
                            <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                        </th>
                        <th>
                            <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                        <th width="6%" class="nowrap">
                            <?php echo JHtml::_('grid.sort', 'COM_TZ_PORTFOLIO_TYPE_OF_MEDIA', 'groupname', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('grid.sort', 'COM_TZ_PORTFOLIO_HEADING_GROUP', 'groupname', $listDirn, $listOrder); ?>
                        </th>
                        <?php if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE):?>
                        <th width="10%">
                            <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
                        </th>
                        <?php endif;?>
                        <th width="6%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                        </th>

                        <?php if ($assoc) : ?>
                        <th width="5%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', 'COM_CONTENT_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
                        </th>
                        <?php endif;?>

                        <th width="10%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('grid.sort', 'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
                        </th>
                        <th width="5%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                        </th>
                        <th width="10%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
                        </th>
                        <th width="1%" class="nowrap hidden-phone">
                            <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $i => $item) :
                        $item->max_ordering = 0; //??
                        $ordering	= ($listOrder == 'a.ordering');
                        $canCreate	= $user->authorise('core.create',		'com_content.category.'.$item->catid);
                        $canEdit	= $user->authorise('core.edit',			'com_content.article.'.$item->id);
                        $canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                        $canEditOwn	= $user->authorise('core.edit.own',		'com_content.article.'.$item->id) && $item->created_by == $userId;
                        $canChange	= $user->authorise('core.edit.state',	'com_content.article.'.$item->id) && $canCheckin;
                        ?>
                        <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
                            <td class="order nowrap center hidden-phone">
                            <?php if ($canChange) :
                                $disableClassName = '';
                                $disabledLabel	  = '';

                                if (!$saveOrder) :
                                    $disabledLabel    = JText::_('JORDERINGDISABLED');
                                    $disableClassName = 'inactive tip-top';
                                endif; ?>
                                <span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
                                    <i class="icon-menu"></i>
                                </span>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                            <?php else : ?>
                                <span class="sortable-handler inactive" >
                                    <i class="icon-menu"></i>
                                </span>
                            <?php endif; ?>
                            </td>
                            
                            <td class="center">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center">
                                <div class="btn-group">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                                    <?php echo JHtml::_('contentadministrator.featured', $item->featured, $i, $canChange); ?>
                                </div>
                            </td>
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if ($canEdit || $canEditOwn) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_tz_portfolio&task=article.edit&id='.$item->id);?>">
                                            <?php echo $this->escape($item->title); ?></a>
                                    <?php else : ?>
                                        <?php echo $this->escape($item->title); ?>
                                    <?php endif; ?>
                                    <?php if(COM_TZ_PORTFOLIO_JVERSION_COMPARE):?>
                                    <span class="small">
                                        <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                    </span>
                                    <div class="small">
                                        <?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
                                    </div>
                                    <?php else:?>
                                        <p class="smallsub">
                                            <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
                                    <?php endif;?>
                                </div>
                                <div class="pull-left">
                                    <?php
                                        // Create dropdown items
                                        JHtml::_('dropdown.edit', $item->id, 'article.');
                                        JHtml::_('dropdown.divider');
                                        if ($item->state) :
                                            JHtml::_('dropdown.unpublish', 'cb' . $i, 'articles.');
                                        else :
                                            JHtml::_('dropdown.publish', 'cb' . $i, 'articles.');
                                        endif;

                                        if ($item->featured) :
                                            JHtml::_('dropdown.unfeatured', 'cb' . $i, 'articles.');
                                        else :
                                            JHtml::_('dropdown.featured', 'cb' . $i, 'articles.');
                                        endif;

                                        JHtml::_('dropdown.divider');

                                        if ($archived) :
                                            JHtml::_('dropdown.unarchive', 'cb' . $i, 'articles.');
                                        else :
                                            JHtml::_('dropdown.archive', 'cb' . $i, 'articles.');
                                        endif;

                                        if ($item->checked_out) :
                                            JHtml::_('dropdown.checkin', 'cb' . $i, 'articles.');
                                        endif;

                                        if ($trashed) :
                                            JHtml::_('dropdown.untrash', 'cb' . $i, 'articles.');
                                        else :
                                            JHtml::_('dropdown.trash', 'cb' . $i, 'articles.');
                                        endif;

                                        // Render dropdown list
                                        echo JHtml::_('dropdown.render');
                                    ?>
                                </div>
                            </td>
                            <td class="small hidden-phone">
                                <?php echo $item -> type;?>
                            </td>
                            <td class="small hidden-phone">
                                <a href="index.php?option=com_tz_portfolio&task=group.edit&id=<?php echo $item -> groupid?>">
                                    <?php echo $item -> groupname;?>
                                </a>
                            </td>

                            <?php if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE):?>
                            <td class="small hidden-phone">
                                <?php echo $this->escape($item->category_title); ?>
                            </td>
                            <?php endif;?>
                            
                            <td class="small hidden-phone">
                                <?php echo $this->escape($item->access_level); ?>
                            </td>
                            <?php if ($assoc) : ?>
                            <td class="hidden-phone">
                                <?php if ($item->association) : ?>
                                    <?php echo JHtml::_('contentadministrator.association', $item->id); ?>
                                <?php endif; ?>
                            </td>
                            <?php endif;?>
                            <td class="small hidden-phone">
                                <?php echo $this->escape($item->author_name); ?>
                            </td>
                            <td class="small hidden-phone">
                                <?php if ($item->language=='*'):?>
                                    <?php echo JText::alt('JALL', 'language'); ?>
                                <?php else:?>
                                    <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                                <?php endif;?>
                            </td>
                            <td class="small nowrap hidden-phone">
                                <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
                            </td>
                            <td class="center">
                                <?php echo (int) $item->id; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

            </table>
            <?php echo $this->pagination->getListFooter(); ?>
            <?php //Load the batch processing form. ?>
            <?php echo $this->loadTemplate('batch'); ?>
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
            <input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance() -> toString())?>">
            <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
