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

//JHtml::_('bootstrap.tooltip');
//JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', '#menuOptions select');
?>
<script>
    jQuery(document).ready(function(){
        jQuery('.hasTooltip,[data-toggle=tooltip]').tooltip({
            tooltipClass: "tooltip top in",
            open: function (event, ui) {
                var $el     = jQuery(this),
                    $top    = $el.offset().top - ui.tooltip.outerHeight(true),
                    $left   = $el.offset().left + ($el.width() / 2) - ui.tooltip.width()/2;

                if($el.offset().left < ui.tooltip.width()/2){
                    $left   = 0;
                    if(jQuery(ui.tooltip).find('.tooltip-arrow').length){
                        jQuery(ui.tooltip).find('.tooltip-arrow').css("left",$el.offset().left + ($el.width() / 2));
                    }
                }
                if(($left + ui.tooltip.width()/2) > jQuery(document).width()){
                    $left   -= (($left + ui.tooltip.width()/2) - jQuery(document).width());
                    if(jQuery(ui.tooltip).find('.tooltip-arrow').length){
                        jQuery(ui.tooltip).find('.tooltip-arrow').css("left",$el.offset().left
                            + $left);
                    }
                }
                ui.tooltip.css({
                    'left': $left
                    ,'top': $top
                });
            },
            position: {
                my: "center bottom-20",
                at: "center top",
                collision: "fit",
                using: function( position, feedback ) {
                    var toolinner   = jQuery( this).css(position).find(".ui-tooltip-content").first();
                    toolinner.html(htmlspecialchars_decode(toolinner.html())).addClass("tooltip-inner")
                        .before("<div class=\"tooltip-arrow\"></div>");
                }
            }
        });
    });
</script>
<form name="adminForm" method="post" id="template-form"
      action="index.php?option=com_tz_portfolio&view=template_style&layout=edit&id=<?php echo $this -> item -> id?>">
    <div class="container-fluid" id="plazart_layout_builder">

        <div class="form-horizontal">
            <div class="row-fluid">
                <div class="span8 form-horizontal">
                    <fieldset class="adminForm">
                        <legend><?php echo JText::_('COM_TZ_PORTFOLIO_FIELDSET_DETAILS');?></legend>
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
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('template'); ?>
                            </div>
                            <div class="controls">
                                <?php echo $this->form->getInput('template'); ?>
                            </div>
                        </div>

                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#layout" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_LAYOUT');?></a></li>
                            <li><a href="#menus_assignment" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_MENUS_ASSIGNMENT');?></a></li>
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
                            <div class="tab-pane assignment" id="menus_assignment">
                                <?php echo $this -> loadTemplate('menu_assignment'); ?>
                            </div>
                            <div class="tab-pane assignment" id="categories_assignment">
                                <?php echo $this->form->getInput('categories_assignment'); ?>
                            </div>
                            <div class="tab-pane" id="articles_assignment">
                                <?php echo $this->form->getInput('articles_assignment'); ?>
                            </div>
                        </div>

                    </fieldset>
                </div>
                <div class="span4">
                    <?php echo JHtml::_('bootstrap.startAccordion', 'menuOptions', array('active' => 'collapse0'));?>
                        <?php  $fieldSets = $this->form->getFieldsets('params'); ?>
                        <?php $i = 0;?>
                        <?php foreach ($fieldSets as $name => $fieldSet) :?>
                            <?php // If the parameter says to show the article options or if the parameters have never been set, we will
                            // show the article options. ?>
                            <?php
                            $fields = $this->form->getFieldset($name);
                            if($fields && count($fields)):?>
                            <?php echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_($fieldSet->label), 'collapse' . $i++); ?>
                            <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                                <p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
                            <?php endif; ?>
                            <fieldset>
                                <?php foreach ($fields as $field) : ?>
                                    <div class="control-group">
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="controls"><?php echo $field->input; ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </fieldset>
                            <?php echo JHtml::_('bootstrap.endSlide');?>
                            <?php endif;?>
                        <?php endforeach; ?>
                    <?php echo JHtml::_('bootstrap.endAccordion');?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" value="com_tz_portfolio" name="option">
    <input type="hidden" value="" name="task">
<!--    <input type="hidden" name="jform[params][generate]">-->
    <?php echo JHTML::_('form.token');?>
</form>