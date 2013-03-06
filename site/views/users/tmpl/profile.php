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
defined('_JEXEC') or die('Restricted access');

$doc    = JFactory::getDocument();
$doc -> addStyleSheet('components/com_tz_portfolio/css/tz_portfolio.css');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$lang   = JFactory::getLanguage();
$lang -> load('com_users');

?>

<div class="profile-edit<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>
    <form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>"
          method="post" class="form-validate form-horizontal"
          enctype="multipart/form-data">
    <?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($group);?>
	<?php if (count($fields)):?>
	<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
		<legend><?php echo JText::_($fieldset->label); ?></legend>
		<?php endif;?>
		<?php foreach ($fields as $field):// Iterate through the fields in the set and display them.?>
			<?php if ($field->hidden):// If the field is hidden, just display the input.?>
				<div class="control-group">
					<div class="controls">
						<?php echo $field->input;?>
					</div>
				</div>
			<?php else:?>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $field->label; ?>
                        <?php if (!$field->required && $field->type != 'Spacer'): ?>
                        <span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="controls">
                        <?php echo $field->input; ?>
                    </div>
                </div>

                <?php if(($field-> fieldname) == 'email2'):?>
                    <div class="control-group">
                        <div class="control-label">
                            <label title="<?php echo preg_replace('/\:+$/','',JText::_('COM_TZ_PORTFOLIO_USER_FIELD_GENDER_LABEL'));?>::<?php echo JText::_('COM_TZ_PORTFOLIO_USER_FIELD_GENDER_LABEL_DESC');?>"
                                   class="hasTip required"
                                   >
                                <?php echo JText::_('COM_TZ_PORTFOLIO_USER_FIELD_GENDER_LABEL');?>
                            </label>
                        </div>
                        <div class="controls">
                            <fieldset id="jform_gender" class="TzGroup">
                                <input type="radio"
                                   id="jform_gender0"
                                   name="gender"
                                   value="m"
                                    <?php if($this -> TZUser AND $this -> TZUser -> gender == 'm') echo ' checked="checked"';?>
                                >
                                <label for="jform_gender0"><?php echo JText::_('Male');?></label>

                                <input type="radio"
                                   id="jform_gender1"
                                   name="gender"
                                   value="f"
                                    <?php if($this -> TZUser AND $this -> TZUser -> gender == 'f') echo ' checked="checked';?>
                                >
                                <label for="jform_gender1"><?php echo JText::_('Female');?></label>
                            </fieldset>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label id="jform_client_images-lbl"
                                   class=""
                                   for="jform_client_images"
                                   aria-invalid="false">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_USER_FIELD_CLIENT_IMAGES_LABEL')?>
                            </label>
                        </div>
                        <div class="controls">
                            <input id="jform_client_images"
                                type="file"
                               size="50"
                               name="jform[client_images]"
                               class=""/>
                        </div>
                    </div>
                    <?php if($this -> TZUser AND !empty($this -> TZUser -> images)):?>
                        <div class="control-group">
                            <div class="controls">
                                <img src="<?php echo $this -> TZUser -> images;?>" style="max-width:120px;"/>
                                <input type="hidden" name="current_images"
                                       value="<?php echo $this -> TZUser -> imageName?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <input type="checkbox" name="delete_images" value="1">
                                <label><?php echo JText::_('COM_TZ_PORTFOLIO_DELETE_IMAGES');?></label>
                            </div>
                        </div>
                    <?php endif;?>
                    <div class="control-group">
                        <div class="control-label">
                            <label id="jform_url_images-lbl"
                               class=""
                               for="jform_url_images"
                               aria-invalid="false">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_USER_FIELD_URL_IMAGES_LABEL');?></label>
                        </div>
                        <div class="controls">
                            <input id="jform_url_images"
                               class=""
                               type="text" size="40"
                               value=""
                               name="jform[url_images]"
                               aria-invalid="false"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label><?php echo JText::_('COM_TZ_PORTFOLIO_TWITTER_LABEL');?></label>
                        </div>
                        <div class="controls">
                            <input type="text" name="url_twitter" size="40"
                               value="<?php if($this -> TZUser) echo $this -> TZUser -> twitter?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label><?php echo JText::_('COM_TZ_PORTFOLIO_FACEBOOK_LABEL');?></label>
                        </div>
                        <div class="controls">
                            <input type="text" name="url_facebook" size="40"
                               value="<?php if($this -> TZUser) echo $this -> TZUser -> facebook;?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label><?php echo JText::_('COM_TZ_PORTFOLIO_GOOGLE_PLUS_LABEL');?></label>
                        </div>
                        <div class="controls">
                            <input type="text" name="url_google_one_plus" size="40"
                               value="<?php if($this -> TZUser) echo $this -> TZUser -> google_one;?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label><?php echo JText::_('COM_TZ_PORTFOLIO_DESCRIPTION');?></label>
                        </div>
                        <div class="controls">
                            <textarea rows="10" cols="50" name="description">
                                <?php if($this -> TZUser) echo $this -> TZUser -> description;?>
                            </textarea>
                        </div>
                    </div>

                <?php endif;?>

			<?php endif;?>
		<?php endforeach;?>
	</fieldset>
	<?php endif;?>
<?php endforeach;?>


        <div class="form-actions">
            <button type="submit" class="btn btn-primary validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
            <a class="btn" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

            <input type="hidden" value="<?php echo $this -> user -> id;?>" id="jform_id" name="jform[id]">
            <input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="profile.save" />
            <?php echo JHTML::_('form.token');?>
        </div>
	</form>
</div>