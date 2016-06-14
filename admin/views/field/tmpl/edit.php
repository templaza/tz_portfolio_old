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
$fields = $this -> item -> defvalue;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');

//$saveOrderingUrl = 'index.php?option=com_tz_portfolio&task=fields.saveOrderAjax&tmpl=component';
//JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($this -> state -> filter_order_Dir), $saveOrderingUrl);
?>

<script type="text/javascript ">
Joomla.submitbutton = function(task) {
    if (task == 'field.cancel' || document.formvalidator.isValid(document.id('field-form'))) {
        Joomla.submitform(task, document.getElementById('field-form'));
    }
    else {
        alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED', true));?>');
    }
}

window.addEvent('load', function() {
    var createBox = function(object,name,tz_count,imageurl){
        var myDiv = new Element('div',{ class: 'clearfix'}).inject(object);
        var myDiv = new Element('div',{ style:"clear:both;display: block; margin-top: 10px;",class: 'input-prepend input-append'}).inject(object);
        var tz_e = location.href.match(/^(.+)administrator\/index\.php.*/i)[1];

        var icon = new Element('div',{
            class: 'add-on',
            html: '<\i class="icon-eye"></i>'
        }).inject(myDiv);
        var tz_a = new Element('input',{
            type:"text",
            class:"inputbox image-select",
            name: name,
            value:imageurl,
            id:"image-select-"+tz_count,
            readonly:'true',
            style:"width:200px;"
        });
        tz_a.inject(myDiv);
        var tz_d = "image-select-" + tz_count,
            tz_b = (new Element("a", {
                class: "btn",
                "id": "tz_img_button"+tz_count
            })).set('html', "<\i class=\"icon-file\"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BROWSE_SERVER', true);?>").inject(tz_a,'after'),
            tz_f = (new Element("a", {
                class: 'btn',
                "name": "tz_img_cancel_"+tz_count,
                html:'<i class="icon-refresh"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_RESET', true);?>'
            })).inject(tz_b,'after'),
            tz_g = (new Element("div", {
                "class": "tz-image-preview",
                "style": "clear:both;max-width:300px"
            })).inject(tz_f,'after');

        tz_a.setProperty("id", tz_d);
        tz_a.getProperty("value") && (new Element("img", {
            src: tz_e + tz_a.getProperty("value"),
            style:'max-width:150px'
        })).inject(tz_g,'inside');
        tz_f.addEvent("click", function (e) {
            e.stop();
            tz_a.setProperty("value", "");
            tz_a.getParent().getElement("div.tz-image-preview").empty()
        });

        tz_b.addEvent("click", function (h) {
            (h).stop();
            SqueezeBox.fromElement(this, {
                handler: "iframe",
                url: "index.php?option=com_media&view=images&tmpl=component&e_name=" + tz_d,
                size: {
                    x: 800,
                    y: 500
                }
            });

            window.jInsertEditorText = function (text, editor) {
                if (editor.match(/^image-select-/)) {
                    var d = $(editor),
                        tz_b = d.getParent().getElement("div.tz-image-preview").set('html',text ).getElement("img");
                    d.setProperty("value", tz_b.getProperty('src'));
                    tz_b.setProperty("src", tz_e + tz_b.getProperty("src"));
                } else tinyMCE.execInstanceCommand(editor, 'mceInsertContent',false,text);
            };

        });
        return myDiv;
    };
    var renderElement=function(tz_count_gb){

        $('fields').empty();
        var optionFields = function(){
            var tz_count = 0;
            var myButton = new Element('a', {
                class: 'btn',
                html: '<i class="icon-plus"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW', true);?>',
                events: {
                    click: function(e){
                        e.stop();

                        var myDivm = new Element('div');
                        myDivm.inject($('fieldvalue'));

                        var myDiv = new Element('div',{
                            styles:{
                                display:'block',
                                width:'100%',
                                float:'left',
                                'padding-top':'25px'
                            }
                        });
                        myDiv.inject(myDivm);
                        //value
                        var myValue = new Element('input',{
                            type: 'text',
                            'name': 'jform[option_name][]'
                        });
                        myValue.inject(myDiv);

                        var myRemove = new Element('a',{
                            class: 'btn',
                            html:'<i class="icon-remove"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE', true);?>',
                            events:{
                                click: function(e){
                                    e.stop();
                                    this.getParent().getParent().dispose();
//                                    tz_count--;
                                }
                            }
                        });
                        myRemove.inject(myDiv);

                        if($('type').value == 'multipleSelect' || $('type').value == 'checkbox'){
                            var myDefault = new Element('div',{
                                style:"display:inline-block; padding-left:10px; width:20%;",
                                html:'<\input type="checkbox" name="jform[default][]" value="'+tz_count+'" \style="margin:0;"/><\span style="padding-left:5px; font-size: 11px;">'+
                                    '<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUES', true);?></i></span>'
                            }).inject(myRemove,'after');
                        }
                        if($('type').value == 'select' || $('type').value == 'radio'){
                            var myDefault = new Element('div',{
                                style:"display:inline-block; padding-left:10px; width:20%;",
                                html:'<\input type="radio" name="jform[default][]" value="'+tz_count+'" \style="margin:0;"/><\span style="padding-top:5px; font-size: 11px;">'+
                                    '<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUE', true);?></i></span>'
                            }).inject(myRemove,'after');
                        }

                        var myBox = createBox(myDivm,'jform[option_icon][]',tz_count+1,'');

                        var myOrder = new Element('div',{
                            style: 'padding-top: 10px; clear: both;',
                            html: '<span style="padding-right: 10px;"><?php echo JText::_('JFIELD_ORDERING_LABEL', true);?></span><input type="text" name="jform[ordering][]">'
                        }).inject(myBox,'after');
                        tz_count++;
                    }
                }
            });
            myButton.inject($('fieldvalue'));
        <?php
        $i=0;
        foreach($fields as $value):
            ?>
            var type        = '<?php echo $fields[0] -> type;?>';
            var optionName  = '';
            var image       = '';
            if(type == $('type').value){
                optionName  = '<?php echo addslashes(htmlspecialchars_decode($fields[$i] -> name));?>';
                image       = '<?php echo $fields[$i] -> image;?>';
            }

            var myDivm = new Element('div');
            myDivm.inject($('fieldvalue'));

            var myDiv = new Element('div',{
                styles:{
                    float:'left',
                    width:'100%',
                    'padding-top':'25px'
                }
            });
            myDiv.inject(myDivm);
            var myValue = new Element('input',{
                type: 'text',
                'name': 'jform[option_name][]',
                value:optionName
            });
            myValue.inject(myDiv);
            var myRemove = new Element('a',{
                class: 'btn',
                html:'<i class="icon-remove"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE');?>',
                events:{
                    click: function(e){
                        e.stop();
                        this.getParent().getParent().dispose();
//                        tz_count--;
                    }
                }
            });
            myRemove.inject(myDiv);

            if($('type').value == 'multipleSelect' || $('type').value == 'checkbox'){
                var myDefault = new Element('div',{
                    style:"display:inline-block; padding-left:10px; width:20%;",
                    html:'<input type="checkbox" name="jform[default][]" value="<?php echo $i;?>"'+
                        '<?php if(in_array($i,$fields[$i] -> default_value)) echo ' checked="checked"';?>'+
                        ' \style="margin:0;"/><\span style="padding-left:5px; font-size: 11px;">'+
                        '<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUES');?></i></span>'
                }).inject(myRemove,'after');

            }

            if($('type').value == 'select' || $('type').value == 'radio'){
                var myDefault = new Element('div',{
                    style:"display:inline-block; padding-left:10px; width:20%;",
                    html:'<input type="radio" name="jform[default][]" value="<?php echo $i;?>"'+
                        '<?php if(in_array($i,$fields[$i] -> default_value)) echo ' checked="checked"';?>'
                        +' \style="margin:0;"/><span style="padding-left:5px; font-size: 11px;">'+
                        '<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUE');?></i></span>'
                }).inject(myRemove,'after');
            }

            var myBox = createBox(myDivm,'jform[option_icon][]',<?php echo $i+1;?>,image);
            var myOrder = new Element('div',{
                style: 'padding-top: 10px; clear: both;',
                html: '<span style="padding-right: 10px;"><?php echo JText::_('JFIELD_ORDERING_LABEL', true)?></span>'
                    +'<input type="text" name="jform[ordering][]" value="<?php echo $fields[$i] -> ordering;?>">'
            }).inject(myBox,'after');

            tz_count++;
            <?php $i++;?>
            <?php endforeach;?>
        };

        var myField =   new Element('div', {
            id : 'fieldvalue'
        });
        myField.inject($('fields'));
        switch (document.adminForm.type.value) {
            case 'textfield':
                var myField = new Element('input', {
                    type: 'text',
                    'name': 'jform[option_value][]',
                    value: '<?php echo ($fields[0] -> type == 'textfield')?$fields[0] -> name:'';?>'
                });
                myField.inject($('fieldvalue'));
                var myDefault   = new Element('div',{
                    style:"font-size:11px; padding-top:5px;",
                    html:'<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUE');?></i>'
                }).inject(myField,'after');
                createBox($('fieldvalue'),'jform[option_icon][]',0,'<?php echo ($fields[0] -> type == 'textfield')?$fields[0] -> image:'';?>');
                break;
            case 'multipleSelect':
            case 'checkbox':
            case 'radio':
            case 'select':
                optionFields();
                break;
            case 'textarea':
                var myDefault   = new Element('div',{
                    style:"font-size:11px; padding-top:5px;",
                    html:'<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUE');?></i>'
                }).inject($('fieldvalue'));
                var myField = new Element('textarea',{
                    'name':'jform[option_value][]',
                    styles:{
                        display:'block',
                        width: '300px',
                        height: '100px'
                    },
                    value:'<?php echo ($fields[0] -> type == 'textarea')?$fields[0] -> name:'';?>'
                });
                myField.inject($('fieldvalue'));
                var myDiv = new Element('div',{
                html:<?php echo '\'<strong><i>'.JText::_('COM_TZ_PORTFOLIO_USE_EDITOR').'</i></strong>\'';?>,
                        styles:{
                            display:'block',
                            float:'left',
                            width:'100%'
                        }
                    });
                    myDiv.inject($('fieldvalue'));
                    var myField = new Element('input',{
                       type:'checkbox',
                       value : '1',
                       'name':'jform[option_editor]',
                        checked:'<?php echo ($fields[0]-> editor == '1')?'checked':'';?>'
                    });
                    myField.inject(myDiv);
                createBox($('fieldvalue'),'jform[option_icon][]',0,'<?php echo ($fields[0] -> type == 'textarea')?$fields[0] -> image:'';?>');
                break;
            case 'link':
                var myDefault   = new Element('div',{
                    style:"font-size:11px; padding-top:5px;",
                    html:'<label></label><i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUES');?></i>'
                }).inject($('fieldvalue'));
                var linkDiv = new Element('div',{});
                linkDiv.inject($('fieldvalue'));
                var myLabel = new Element('label',{
                    html:'<strong><?php echo JText::_('COM_TZ_PORTFOLIO_LINK_TEXT');?></strong>',
                    styles:{
                        'font-size':'11px'
                    }
                });
                myLabel.inject(linkDiv);
                var myField = new Element('input',{
                    type: 'text',
                    'name':'jform[option_name][]',
                    value:'<?php echo ($fields[0] -> type == 'link')?$fields[0] -> name:'';?>'
                });
                myField.inject(linkDiv);
                var linkDiv = new Element('div',{});
                linkDiv.inject($('fieldvalue'));
                var myLabel = new Element('label',{
                    html:'<strong><?php echo JText::_('COM_TZ_PORTFOLIO_LINK_URL');?></strong>',
                    styles:{
                        'font-size':'11px'
                    }
                });
                myLabel.inject(linkDiv);
                var myField = new Element('input',{
                    type: 'text',
                    'name':'jform[option_value][]',
                    value:'<?php echo ($fields[0] -> type == 'link')?$fields[0] -> value:'';?>'
                });
                myField.inject(linkDiv);
                var linkDiv = new Element('div',{});
                linkDiv.inject($('fieldvalue'));
                var myLabel = new Element('label',{
                    html:'<strong><?php echo JText::_('COM_TZ_PORTFOLIO_OPEN_IN');?></strong>',
                    styles:{
                        'font-size':'11px'
                    }
                });
                myLabel.inject(linkDiv);
                var myField = new Element('select',{
                    html:'<option value="_self" <?php echo ($fields[0] -> target == '_self')?' selected="selected"':'';?>><?php echo JText::_('Same window');?></option>'
                        +'<option value="_blank"<?php echo ($fields[0] -> target == '_blank')?'selected="selected"':'';?>><?php echo JText::_('New window');?></option> ',
                    'name':'jform[option_target][]'
                });
                myField.inject(linkDiv);
                createBox($('fieldvalue'),'jform[option_icon][]',0,'<?php echo $fields[0] -> image;?>');
                jQuery('select').chosen({
                    disable_search_threshold : 10,
                    allow_single_deselect : true
                });
                break;
            default:
                $('fields').set('html', '<label><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_FIELD_VALUES_DESC');?></label>');
                break;
        }
    }

    renderElement(0);

    $$('.chzn-drop li').addEvent('click',function(e){
        e.stop();
        var tz_count_gb = 0;
        renderElement(tz_count_gb);
    })
    jQuery('#type').bind('change',function(e){
        e.preventDefault();
        var tz_count_gb = 0;
        renderElement(tz_count_gb);
    })
});
</script>
<form name="adminForm" method="post" id="field-form"
      action="index.php?option=com_tz_portfolio&view=field&layout=edit&id=<?php echo $this -> item -> id?>">

    <!-- Begin Content -->
    <div class="span10 form-horizontal">
        <fieldset class="adminform">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="details">
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100" for="title" class="hasTip"
                                   title="<?php echo JText::_('COM_TZ_PORTFOLIO_LABEL_TITLE')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_LABEL_TITLE')?>">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_LABEL_TITLE')?>
                                <span class="star"> *</span>
                            </label>
                        </div>
                        <div class="controls">
                            <input type="text" title="Title" maxlength="50"
                                   size="50" class="required"
                                   required="required"
                                   value="<?php echo $this -> item-> title;?>"
                                   id="jform_title" name="jform[title]"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100" for="groups" class="hasTip"
                                   title="<?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED_DESC')?>">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED')?>
                                <span class="star"> *</span>
                            </label>
                        </div>
                        <div class="controls">
                            <select multiple="multiple" size="10" id="groups"
                                     class="required" required="required"
                                    name="jform[groups][]" style="width:150px;">
                                <option value="-1">
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_GROUP');?>
                                </option>
                                <?php
                                if($this -> groups AND count($this -> groups)>0):
                                    foreach($this -> groups as $row):
                                        ?>
                                        <option value="<?php echo $row -> id;?>"
                                            <?php
                                            if(in_array($row -> id,array_keys($this -> item -> groups))):
                                                echo ' selected="selected"';
                                            endif;
                                            ?>
                                            >
                                            <?php echo $row -> name;?>
                                        </option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100" for="type" class="hasTip"
                                   title="<?php echo JText::_('COM_TZ_PORTFOLIO_TYPE')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_TYPE_DESC')?>">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_TYPE')?>
                                <span class="star"> *</span>
                            </label>
                        </div>
                        <div class="controls">
                            <select name="jform[type]" id="type" class="required" required="required">
                                <option value=""><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_TYPE');?></option>
                                <option value="textfield"<?php echo ($this -> item-> type == 'textfield')?' selected="selected"':'';?>>
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_TEXT_FIELD');?>
                                </option>
                                <option value="textarea"<?php echo ($this -> item-> type == 'textarea')?' selected="selected"':'';?>>
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_TEXTAREA');?>
                                </option>
                                <option value="select"<?php echo ($this -> item-> type == 'select')?' selected="selected"':'';?>>
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_DROP_DOWN_SELECTION');?>
                                </option>
                                <option value="multipleSelect"<?php echo (strtolower($this -> item-> type) == 'multipleselect')?' selected="selected"':'';?>>
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_MULTI_SELECT_LIST');?>
                                </option>
                                <option value="radio"<?php echo ($this -> item-> type == 'radio')?' selected="selected"':'';?>>
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_RADIO_BUTTONS');?>
                                </option>
                                <option value="checkbox"<?php echo ($this -> item-> type == 'checkbox')?' selected="selected"':'';?>>
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_CHECK_BOX');?>
                                </option>
                                <option value="link"<?php echo ($this -> item-> type == 'link')?' selected="selected"':'';?>>
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_LINK');?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100" for="defaultvalue">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_FIELD_VALUES')?>:
                            </label>
                        </div>
                        <div class="controls" id="fields">
                            <label><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_FIELD_VALUES_DESC');?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label for="jform_id"
                           title="<?php echo JText::_('JGLOBAL_FIELD_ID_LABEL');?>::<?php echo JText::_('JGLOBAL_FIELD_ID_DESC')?>">
                            <?php echo JText::_('JGLOBAL_FIELD_ID_LABEL');?></label>
                        </div>
                        <div class="controls">
                            <input type="text" id="jform_id"
                           readonly="readonly" class="readonly"
                           value="<?php echo ($id = $this -> item -> id)?$id:0?>" name="jform[id]">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100" for="jform_description">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_DESCRIPTION');?>
                            </label>
                        </div>
                        <div class="controls">
                            <?php echo $this -> editor -> display('jform[description]',htmlspecialchars_decode($this -> item-> description),'100%', '300', '60', '20', array('pagebreak', 'readmore'),'jform_description');?>
                        </div>
                    </div>



                </div>
            </div>
        </fieldset>
    </div>
    <!-- End Content -->
    <!-- Begin Sidebar -->
    <div class="span2">
        <h4><?php echo JText::_('JDETAILS');?></h4>
        <fieldset class="form-vertical">
            <div class="control-group">
                <label width="100" for="jform_published">
                    <?php echo JText::_('JPUBLISHED')?>:
                </label>
                <div class="controls">
                    <?php
                    $state = array('' => JText::_('JOPTION_SELECT_PUBLISHED'), 'P' => JText::_('JPUBLISHED'), 'U' => JText::_('JUNPUBLISHED'));
                    echo JHtml::_('select.genericlist',$state,'jform[published]','','value','text',$this -> item -> published,'jform_published');
                    ?>
                </div>
            </div>
        </fieldset>
    </div>
    <!-- End Sidebar -->
    <input type="hidden" value="com_tz_portfolio" name="option">
<!--    <input type="hidden" value="--><?php //$cid=JRequest::getInt('id'); echo $cid;?><!--" name="id">-->
    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>