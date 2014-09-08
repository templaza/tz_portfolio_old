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

if (JFactory::getApplication()->isSite()) {
    JRequest::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_tz_portfolio/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$function	= JRequest::getCmd('function', 'jSelectArticle');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<script type="text/javascript">
    function tzGetDatas(){
        if (window.parent){
            var j= 0,titles  = new Array(),ids = new Array(),categories = new Array();
            if(document.getElementsByName('cid[]').length){
                var idElems  = document.getElementsByName('cid[]'),
                    titleElems  = document.getElementsByName('tztitles[]'),
                    categoryElems  = document.getElementsByName('tzcategories[]');
                for(var i = 0; i<idElems.length; i++){
                    if(idElems[i].checked){
                        ids[j]  = idElems[i].value;
                        titles[j]  = titleElems[i].value;
                        categories[j]  = categoryElems[i].value;
                        j++;
                    }
                }
            }
            window.parent.<?php echo $this->escape($function);?>(ids,titles,categories);
        }
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&view=articles&layout=modals&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>"
      method="post" name="adminForm" id="adminForm" class="form-inline">
    <fieldset class="filter clearfix">
        <div class="btn-toolbar">
            <div class="btn-group pull-left">
                <label for="filter_search">
                    <?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
                </label>
                <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
                    <i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();">
                    <i class="icon-remove"></i></button>
            </div>
            <div class="pull-right">
                <button type="button" class="btn" onclick="tzGetDatas();">
                    <i class="icon-save-new"></i> <?php echo JText::_('COM_TZ_PORTFOLIO_INSERT');?></button>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr class="hr-condensed" />

        <div class="filters">
            <select name="filter_access" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
            </select>

            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
            </select>

            <select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category_id'));?>
            </select>

            <?php if ($this->state->get('filter.forcedLanguage')) : ?>
                <select name="filter_category_id" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content', array('filter.language' => array('*', $this->state->get('filter.forcedLanguage')))), 'value', 'text', $this->state->get('filter.category_id'));?>
                </select>
                <input type="hidden" name="forcedLanguage" value="<?php echo $this->escape($this->state->get('filter.forcedLanguage')); ?>" />
                <input type="hidden" name="filter_language" value="<?php echo $this->escape($this->state->get('filter.language')); ?>" />
            <?php else : ?>
                <select name="filter_category_id" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category_id'));?>
                </select>

                <select name="filter_language" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
                </select>
            <?php endif; ?>
        </div>
    </fieldset>

    <table class="table table-striped table-condensed">
        <thead>
        <tr>
            <th width="1%">
                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <th class="title">
                <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
            </th>
            <th width="6%" class="nowrap">
                <?php echo JHtml::_('grid.sort', 'COM_TZ_PORTFOLIO_TYPE_OF_MEDIA', 'groupname', $listDirn, $listOrder); ?>
            </th>
            <th width="15%">
                <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
            </th>
            <th width="15%">
                <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort',  'JDATE', 'a.created', $listDirn, $listOrder); ?>
            </th>
            <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
        <?php foreach ($this->items as $i => $item) :?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                    <a style="cursor: pointer;" class="pointer"
                       onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>(['<?php echo $item->id; ?>'], ['<?php echo $this->escape(addslashes($item->title)); ?>'],['<?php echo $this->escape(addslashes($item->category_title)); ?>']);">
                        <?php echo $this->escape($item->title); ?></a>
                    <input type="hidden" name="tztitles[]" value="<?php echo $this->escape(addslashes($item->title));?>">
                </td>
                <td class="small hidden-phone">
                    <?php echo $item -> type;?>
                </td>
                <td class="center small">
                    <?php echo $this->escape($item->access_level); ?>
                </td>
                <td class="center small">
                    <?php echo $this->escape($item->category_title); ?>
                    <input type="hidden" name="tzcategories[]" value="<?php echo $this->escape(addslashes($item->category_title));?>">
                </td>
                <td class="center small">
                    <?php if ($item->language=='*'):?>
                        <?php echo JText::alt('JALL', 'language'); ?>
                    <?php else:?>
                        <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
                    <?php endif;?>
                </td>
                <td class="center small nowrap">
                    <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                <td class="center small">
                    <?php echo (int) $item->id; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
