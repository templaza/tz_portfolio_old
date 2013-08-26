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

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$field		= JRequest::getCmd('field');
$function	= 'jSelectUser_'.$field;
//var_dump($function); die();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&view=users&layout=modal&tmpl=component&groups='.JRequest::getVar('groups', '', 'default', 'BASE64').'&excluded='.JRequest::getVar('excluded', '', 'default', 'BASE64'));?>"
      method="post" name="adminForm" id="adminForm" class="form-inline">
	<fieldset class="filter">
        
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
                <label for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			    <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="40" title="<?php echo JText::_('COM_USERS_SEARCH_IN_NAME'); ?>" />
			</div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" data-placement="bottom"
                        data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT');?>">
                    <i class="icon-search"></i>
                </button>
			    <button type="button" class="btn hasTooltip" data-placement="bottom"
                        data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>"
                        onclick="document.id('filter_search').value='';this.form.submit();">
                    <i class="icon-refresh"></i>
                </button>
			    <button type="button" class="btn hasTooltip" data-placement="bottom"
                        data-original-title="<?php echo JText::_('JOPTION_NO_USER');?>"
                        onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('', '<?php echo JText::_('JLIB_FORM_SELECT_USER') ?>');">
                    <i class="icon-remove"></i>
                </button>
            </div>
            <div class="btn-group pull-right">
                <?php echo JHtml::_('access.usergroup', 'filter_group_id', $this->state->get('filter.group_id'), 'onchange="this.form.submit()"'); ?>
            </div>
            <div class="clearfix"></div>
		</div>
        <hr class="hr-condensed" />
	</fieldset>

	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_GROUPS', 'group_names', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$i = 0;
			foreach ($this->items as $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a style="cursor: pointer" class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');">
						<?php echo $item->name; ?></a>
				</td>
				<td align="center">
					<?php echo $item->username; ?>
				</td>
				<td align="left">
					<?php echo nl2br($item->group_names); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
