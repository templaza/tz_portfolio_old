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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<form name="adminForm" method="post" id="template-form"
      action="index.php?option=com_tz_portfolio&view=template&layout=edit&id=<?php echo $this -> item -> id?>">
    <div class="container-fluid" id="plazart_layout_builder">
        <fieldset class="adminForm">
            <legend><?php echo JText::_('COM_TZ_PORTFOLIO_FIELDSET_DETAILS');?></legend>
            <div class="form-horizontal">
                <div class="control-group">
                    <div class="control-label"><?php echo $this -> form -> getLabel('title');?></div>
                    <div class="controls"><?php echo $this -> form -> getInput('title');?></div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('id'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('id'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('home'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('home'); ?>
                    </div>
                </div>

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#layout" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_LAYOUT');?></a></li>
                    <li><a href="#categories_assignment" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_CATEGORIES_ASSIGNMENT');?></a></li>
                    <li><a href="#articles_assignment" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_ARTICLES_ASSIGNMENT');?></a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="layout">
                        <div id="layout_params">
                            <div id="plazart-admin-device">
                                <div class="pull-left plazart-admin-layout-header"><?php echo JText::_('COM_TZ_PORTFOLIO_LAYOUTBUIDER_HEADER')?></div>
                                <div class="pull-right">
                                    <button type="button" class="btn tz-admin-dv-lg active" data-device="lg">
                                        <i class="fa fa-desktop"></i><?php echo JText::_('COM_TZ_PORTFOLIO_LARGE');?>
                                    </button>
                                    <button type="button" class="btn tz-admin-dv-md" data-device="md" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_ONLY_BOOTSTRAP_3');?>">
                                        <i class="fa fa-laptop"></i><?php echo JText::_('COM_TZ_PORTFOLIO_MEDIUM');?>
                                    </button>
                                    <button type="button" class="btn tz-admin-dv-sm" data-device="sm" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_ONLY_BOOTSTRAP_3');?>">
                                        <i class="fa fa-tablet"></i><?php echo JText::_('COM_TZ_PORTFOLIO_SMALL');?>
                                    </button>
                                    <button type="button" class="btn tz-admin-dv-xs" data-device="xs" data-toggle="tooltip"
                                            title="<?php echo JText::_('COM_TZ_PORTFOLIO_ONLY_BOOTSTRAP_3');?>">
                                        <i class="fa fa-mobile"></i><?php echo JText::_('COM_TZ_PORTFOLIO_EXTRA_SMALL');?>
                                    </button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <?php echo $this -> loadTemplate('column_settings');?>
                            <?php echo $this -> loadTemplate('generator');?>
                        </div>
                    </div>
                    <div class="tab-pane assignment" id="categories_assignment">
                        <?php echo $this->form->getInput('categories_assignment'); ?>
                    </div>
                    <div class="tab-pane" id="articles_assignment">
                        <?php echo $this->form->getInput('articles_assignment'); ?>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>

    <input type="hidden" value="com_tz_portfolio" name="option">
    <input type="hidden" value="" name="task">
<!--    <input type="hidden" name="jform[params][generate]">-->
    <?php echo JHTML::_('form.token');?>
</form>