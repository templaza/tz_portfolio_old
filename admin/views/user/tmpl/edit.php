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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/libraries/cms/html');


// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.tabstate');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');


$canDo = TZ_PortfolioHelperUsers::getActions();

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'user.cancel' || document.formvalidator.isValid(document.id('user-form'))) {
			Joomla.submitform(task, document.getElementById('user-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&layout=edit&id='.(int) $this->item->id); ?>"
      method="post" name="adminForm"
      id="user-form"
       class="form-validate form-horizontal"
      enctype="multipart/form-data">
    <fieldset>
        <ul class="nav nav-tabs">
		<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_USERS_USER_ACCOUNT_DETAILS');?></a></li>
			<?php if ($this->grouplist) :?>
				<li><a href="#groups" data-toggle="tab"><?php echo JText::_('COM_USERS_ASSIGNED_GROUPS');?></a></li>
			<?php endif; ?>
			<?php
			foreach ($fieldsets as $fieldset) :
				if ($fieldset->name == 'user_details') :
					continue;
				endif;
				?>
				<li><a href="#settings" data-toggle="tab"><?php echo JText::_($fieldset->label);?></a></li>
			<?php endforeach; ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="details">
                <?php foreach($this->form->getFieldset('user_details') as $field) :?>
                    <?php if($field -> fieldname != 'description'):?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $field->label; ?></div>
                        <div class="controls"><?php echo $field->input; ?></div>
                    </div>
                    <?php endif;?>

                    <?php if($field -> fieldname == 'name'):?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_TZ_PORTFOLIO_USER_LINK_LABEL');?></label>
                            </div>
                            <div class="controls">
                                <input type="text"
                                       name="url"
                                       size="50"
                                       value="<?php if($this -> listsUsers) echo $this -> listsUsers -> url;?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="control-label">
                                <label title="<?php echo JText::_('COM_TZ_PORTFOLIO_USER_FIELD_GENDER_LABEL');?>::<?php echo JText::_('COM_TZ_PORTFOLIO_USER_FIELD_GENDER_LABEL_DESC');?>"
                                   class="hasTip required">
                            </div>

                            <div class="controls">
                                <fieldset id="jform_gender" class="radio btn-group">
                                    <input type="radio"
                                           name="gender"
                                           id="jform_gender0"
                                           value="m"
                                           <?php if($this -> listsUsers AND $this -> listsUsers -> gender == 'm'): ?>
                                            checked="checked"
                                           <?php endif;?>
                                    >
                                    <label class="btn"
                                        for="jform_gender0"><?php echo JText::_('Male');?></label>
                                    <input type="radio"
                                           name="gender"
                                           id="jform_gender1"
                                           value="f"
                                           <?php if($this -> listsUsers AND $this -> listsUsers -> gender == 'f'): ?>
                                            checked="checked"
                                           <?php endif;?>
                                    >
                                    <label class="btn"
                                           for="jform_gender1"><?php echo JText::_('Female');?></label>
                                </fieldset>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if($this -> listsUsers):?>
                        <?php if($field -> fieldname == 'url_images' AND $this -> listsUsers -> images AND !empty($this -> listsUsers -> images)):?>
                            <div class="control-group">
                                <div class="control-label"><label>&nbsp;</label></div>
                                <div class="controls">
                                    <img src="<?php if($this -> listsUsers) echo $this -> listsUsers -> images;?>" style="width:120px;">
                                    <input type="hidden" name="current_images"
                                           value="<?php if($this -> listsUsers) echo $this -> listsUsers -> imageName?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label"><label>&nbsp;</label></div>
                                <div class="controls">
                                    <fieldset>
                                        <input type="checkbox" name="delete_images" id="delete_images" value="1">
                                        <label for="delete_images"><?php echo JText::_('COM_TZ_PORTFOLIO_DELETE_IMAGES');?></label>
                                    </fieldset>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endif;?>
                <?php endforeach; ?>
                <div class="control-group">
                    <div class="control-label"><label><?php echo JText::_('COM_TZ_PORTFOLIO_TWITTER_LABEL');?></div>
                    <div class="controls"><input type="text" name="url_twitter" size="40"
                           value="<?php if($this -> listsUsers) echo $this -> listsUsers -> twitter?>"/>
                   </div>
                </div>
                <div class="control-group">
                    <div class="control-label"><label><?php echo JText::_('COM_TZ_PORTFOLIO_FACEBOOK_LABEL');?></label></div>
                    <div class="controls">
                        <input type="text" name="url_facebook" size="40"
                           value="<?php if($this -> listsUsers) echo $this -> listsUsers -> facebook?>"/>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label><?php echo JText::_('COM_TZ_PORTFOLIO_GOOGLE_PLUS_LABEL');?></label>
                    </div>
                    <div class="controls">
                        <input type="text" name="url_google_one_plus" size="40"
                               value="<?php if($this -> listsUsers) echo $this -> listsUsers -> google_one?>"/>
                    </div>
                </div>
                <div class="control-group">
                    <div class="clr"></div>
                    <div class="control-label"><label><?php echo JText::_('COM_TZ_PORTFOLIO_DESCRIPTION');?></label></div>
                    <div class="controls">
                        <?php echo $this -> editor -> display('description',($this -> listsUsers)?$this -> listsUsers -> description:'','100%',250,50,60,array('article','image','readmore','pagebreak'));?>
                    </div>
                </div>
            </div>
            <?php if ($this->grouplist) :?>
                <div class="tab-pane" id="groups">
                    <?php echo $this->loadTemplate('groups');?>
                </div>
            <?php endif; ?>

            <?php
				foreach ($fieldsets as $fieldset) :
					if ($fieldset->name == 'user_details') :
						continue;
					endif;
				?>
				<div class="tab-pane" id="settings">
					<?php foreach($this->form->getFieldset($fieldset->name) as $field): ?>
						<?php if ($field->hidden): ?>
							<div class="control-group">
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php else: ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $field->label; ?>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
            <input type="hidden" name="task" value="" />
		    <?php echo JHtml::_('form.token'); ?>
        </div>
    </fieldset>
</form>
