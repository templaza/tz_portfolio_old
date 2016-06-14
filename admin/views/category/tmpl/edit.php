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

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');
$listImage  = $this -> listImage;

$assoc = false;
if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
    $assoc = JLanguageAssociations::isEnabled();
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
    window.addEvent('load',function(){
        var jSonRequest  = new Request.JSON({url:"index.php?option=com_tz_portfolio&task=category.extrafields",
           onComplete:function(item){
               $('tz_fieldsid_content').set('html',item);
               $('tz_fieldsid_content').getParent('div').set('style','');
               $('jform_params_tz_fieldsid_content-lbl').getParent('div').set('style','');
               if(!$('groupid').value.length){
                    $('tz_fieldsid_content').getParent('div').setStyle('display','none');
                    $('jform_params_tz_fieldsid_content-lbl').getParent('div').setStyle('display','none');
               }
               jQuery('select').chosen({
                    disable_search_threshold : 10,
                    allow_single_deselect : true
                });
           },
           data: {
                json: JSON.encode({
                    'id':<?php echo JRequest::getInt('id');?>,
                    'groupid':$('groupid').value
                })
            }
       }).send();
        $$('#groupid_chzn .chzn-drop li').addEvent('click',function(e){
            e.stop();
            $('tz_fieldsid_content').getParent('div').setStyle('display','none');
            $('jform_params_tz_fieldsid_content-lbl').getParent('div').set('style','');
           var jSonRequest  = new Request.JSON({url:"index.php?option=com_tz_portfolio&task=category.extrafields",
               onComplete:function(item){
                   $('tz_fieldsid_content').set('html',item);
                   $('tz_fieldsid_content').getParent('div').set('style','');
                   $('jform_params_tz_fieldsid_content-lbl').getParent('div').set('style','');
                   if(!$('groupid').value.length){
                        $('tz_fieldsid_content').getParent('div').setStyle('display','none');
                        $('jform_params_tz_fieldsid_content-lbl').getParent('div').setStyle('display','none');
                   }
                   jQuery('select').chosen({
                        disable_search_threshold : 10,
                        allow_single_deselect : true
                    });
               },
               data: {
                    json: JSON.encode({
                        'id':<?php echo JRequest::getInt('id');?>,
                        'groupid':$('groupid').value
                    })
                }
           }).send();

        });


        var tz_e = location.href.match(/^(.+)administrator\/index\.php.*/i)[1];
        var icon = new Element('div',{
                class: 'add-on',
                html: '<\i class="icon-eye"></i>'
        }).inject($('tz_category_image_server'));

        var tz_a = new Element('input',{
            type:"text",
            class:"inputbox image-select",
            name:"tz_category_image_server",
            id:"image-select",
            readonly:'true',
            style:"width:200px;"
        });
        tz_a.inject($('tz_category_image_server'));
        var tz_d = "image-select",
            tz_b = (new Element("a", {
                class: 'btn',
                "id": "tz_img_button"
            })).set('html', '<i class="icon-file"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BROWSE_SERVER');?>').inject(tz_a,'after'),
            tz_f = (new Element("a", {
                class: 'btn',
                "name": "tz_img_cancel",
                html:'<i class="icon-refresh"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_RESET');?>'
            })).inject(tz_b,'after'),
            tz_g = (new Element("div", {
                "class": "tz-image-preview",
                "style": "clear:both;"
            })).inject(tz_f,'after');

        tz_a.setProperty("id", tz_d);

        tz_f.addEvent("click", function (e) {
            e.stop();
            //$('tz_category_image').value='';
            tz_a.setProperty("value", "");
        });

        tz_b.addEvent("click", function (h) { (h).stop();
            SqueezeBox.fromElement(this, {
                handler: "iframe",
                url: "index.php?option=com_media&view=images&tmpl=component&e_name=" + tz_d,
                size: {
                    x: 800,
                    y: 500
                }
            });

            window.jInsertEditorText = function (text, editor) {
                if (editor.match(/^image-select/)) {

                    var d = $(editor);
                    var src = text.match(/src=\".*?\"/i);
                    src = String.from(src);
                    src = src.replace(/^src=\"/g,'');
                    src = src.replace(/\"$/g,'');
                    d.setProperty("value", src);
                } else tinyMCE.execInstanceCommand(editor, 'mceInsertContent',false,text);
            };
        });

        var tz_label = new Element('label',{
            html: '<?php echo JText::_('COM_TZ_PORTFOLIO_FORM_URL_IMAGE');?>'
        }).inject($('tz_category_image_server'));
        var tz_input = new Element('input',{
            type: 'text',
            name: 'tz_image_url',
            size: 50,
            style: 'width: 240px; border-radius: 3px;'
        }).inject(tz_label,'after');
        <?php if($listImage AND !empty($listImage -> images)):?>
            var tz_label2 = new Element('label').inject($('tz_category_image_server'));
            var tz_hidden = new Element('input',{
                type: 'hidden',
                name: 'tz_category_hidden',
                value: '<?php echo $listImage -> images;?>'
            }).inject(tz_label2,'after');

             var tz_h = (new Element("img", {
                src: '<?php echo JURI::root().$listImage -> images;?>',
                style:'max-width:300px; cursor:pointer;'
            })).inject(tz_hidden,'after');
            tz_h.addEvent('click',function(){
               SqueezeBox.fromElement(this, {
                    handler: "image",
                    url: '<?php echo JURI::root().$listImage -> images;?>'
                });
            });
            var tz_label3 = new Element('label').inject(tz_h,'after');
            var tz_checkbox = new Element('input',{
                type: 'checkbox',
                name: 'tz_category_del_image',
                id: 'tz_category_del_image',
                value: 1
            }).inject(tz_label3,'after');
            var tz_label4 = new Element('label',{
                html:'<?php echo JText::_('COM_TZ_PORTFOLIO_CURRENT_IMAGE_DESC');?>',
                'for': 'tz_category_del_image',
                style: 'clear:none;'
            }).inject(tz_checkbox,'after');

        <?php endif;?>
    });
</script>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&extension='.JRequest::getCmd('extension', 'com_content').'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate form-horizontal" enctype="multipart/form-data">
    <fieldset>
        <ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_CATEGORIES_FIELDSET_DETAILS');?></a></li>
			<li><a href="#options" data-toggle="tab"><?php echo JText::_('CATEGORIES_FIELDSET_OPTIONS');?></a></li>
            <?php if($assoc):?>
                <li><a href="#associations" data-toggle="tab"><?php echo JText::_('Associations');?></a></li>
            <?php endif;?>
			<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS');?></a></li>
			<?php if ($this->canDo->get('core.admin')): ?>
				<li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_CATEGORIES_FIELDSET_RULES');?></a></li>
			<?php endif; ?>
		</ul>
        <div class="tab-content">
			<div class="tab-pane active" id="details">
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('title'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('title'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('alias'); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('alias'); ?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label><?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP');?></label>
                    </div>
                    <div class="controls">
                        <?php echo $this -> groups;?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label for="image-select"><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE');?></label>
                    </div>
                    <div class="controls input-prepend input-append" id="tz_category_image_server" style="display: block;">
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this -> form -> getLabel('template_id');?>
                    </div>
                    <div class="controls">
                        <?php echo $this -> form -> getInput('template_id');?>
                    </div>
                </div>

				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('description'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('description'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('extension'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('extension'); ?>
					</div>
				</div>

                <div class="row-fluid">
					<h4><?php echo JText::_('JDETAILS');?></h4>
					<hr />

					<div class="span6">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('parent_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('parent_id'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('published'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('published'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('access'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('access'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('language'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('language'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('id'); ?>
							</div>
						</div>
					</div>
					<div class="span6">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('created_user_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('created_user_id'); ?>
							</div>
						</div>
						<?php if (intval($this->item->created_time)) : ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('created_time'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('created_time'); ?>
								</div>
							</div>
						<?php endif; ?>
						<?php if ($this->item->modified_user_id) : ?>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('modified_user_id'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('modified_user_id'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('modified_time'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('modified_time'); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>

            </div>
            <div class="tab-pane" id="options">
                <?php echo $this->loadTemplate('options'); ?>
            </div>
            <?php if ($assoc) : ?>
            <div class="tab-pane" id="associations">
                <?php echo $this->loadTemplate('associations'); ?>
            </div>
            <?php endif; ?>
            <div class="tab-pane" id="metadata">
                <?php echo $this->loadTemplate('metadata'); ?>
            </div>
            <?php if ($this->canDo->get('core.admin')): ?>
                <div class="tab-pane" id="permissions">
                    <?php echo $this->form->getInput('rules'); ?>
                </div>
            <?php endif; ?>
        </div>
    </fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
</form>
