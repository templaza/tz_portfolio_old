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
JHtml::_('bootstrap.tooltip');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$sortFields = $this->getSortFields();

?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this -> order; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="index.php?option=com_tz_portfolio&view=tags" method="post" name="adminForm" id="adminForm">
    <?php if(!empty($this -> sidebar)):?>
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
                <button type="submit" class="btn tip hasTooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button onclick="document.getElementById('filter_search').value='';this.form.submit();"
                         type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" >
                    <i class="icon-remove"></i>
                </button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                    <option value="asc" <?php if ($this -> order_Dir == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                    <option value="desc" <?php if ($this -> order_Dir == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                    <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $this -> order);?>
                </select>
            </div>
        </div>

        <table class="table table-striped" id="tagsList">
            <thead>
            <tr>
                <th width="1%"><?php echo JText::_('#');?></th>
                <th width="1%">
                        <input type="checkbox" name="checkall-toggle"
                               title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                    </th>
                <th width="1%" style="min-width:55px" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $this -> order_Dir, $this -> order); ?>
					</th>
                <th class="title">
                    <?php echo JHtml::_('grid.sort','COM_TZ_PORTFOLIO_HEADING_NAME','name',$this -> order_Dir,$this -> order);?>
                </th>
                <th nowrap="nowrap" width="1%">
                    <?php echo JHtml::_('grid.sort','ID','id',$this -> order_Dir,$this -> order);?>
                </th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <td colspan="11">
                    <?php echo $this -> pagination -> getListFooter();?>
                </td>
            </tr>
            </tfoot>

            <?php if($this -> lists):?>
            <tbody>
            <?php foreach($this -> lists as $i => $row):?>
                <tr class="<?php echo ($i%2==0)?'row0':'row1';?>">
                    <td><?php echo $i+1;?></td>
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $i, $row->id); ?>
                    </td>
                    <td class="center">
                        <div class="btn-group">
                            <?php echo JHtml::_('jgrid.published', $row->published, $i, '', true, 'cb'); ?>
                        </div>
                    </td>
                    <td class="nowrap has-context">
                        <div class="pull-left">
                            <a href="index.php?option=com_tz_portfolio&amp;view=tags&amp;task=edit&amp;id=<?php echo $row -> id;?>">
                                <?php echo $row -> name;?>
                            </a>
                        </div>
                        <div class="pull-left">
                        <?php
                            // Create dropdown items
                            JHtml::_('dropdown.edit', $row -> id, '','index.php?option=com_tz_portfolio&amp;view=tags');
                            JHtml::_('dropdown.divider');
                            if ($row -> published) :
                                JHtml::_('dropdown.unpublish', 'cb' . $i, '');
                            else :
                                JHtml::_('dropdown.publish', 'cb' . $i, '');
                            endif;
                            // render dropdown list
                            echo JHtml::_('dropdown.render');
                        ?>
                        </div>
                    </td>

                    <td align="center"><?php echo $row -> id;?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
            <?php endif;?>

        </table>

        <input type="hidden" name="option" value="com_tz_portfolio">
        <input type="hidden" name="view" value="tags">
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <input type="hidden" name="filter_order" value="<?php echo $this -> order;?>">
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this -> order_Dir;?>">
        <?php echo JHtml::_('form.token');?>
    </div>
</form>