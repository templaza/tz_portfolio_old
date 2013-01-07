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
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
?>

<script type="text/javascript ">
    Joomla.submitbutton = function(pressbutton) {
        var form = document.adminForm;

        if (pressbutton == 'cancel') {
            document.location='index.php?option=com_tz_portfolio&view=fields';
            return;
        }

        var total = 0;
        for(i=0;i<form.fieldsgroup.length;i++){
            if(form.fieldsgroup[i].selected){
                total++;
            }

        }
        // do field validation
        if ( form.name.value == "" ) {
            alert( "<?php echo JText::_( 'COM_TZ_PORTFOLIO_INPUT_FIELD_NAME', true ); ?>" );
            form.name.focus();
        }
        else if(form.title.value==""){
            alert( "<?php echo JText::_( 'COM_TZ_PORTFOLIO_INPUT_FIELD_TITLE', true ); ?>" );
            form.title.focus();
        }
        else if(form.fieldsgroup.value==-1 && total==1){
            alert( "<?php echo JText::_( 'COM_TZ_PORTFOLIO_INPUT_FIELD_SELECT_GROUP', true ); ?>" );
            form.fieldsgroup.focus();
        }
        else if(form.type.value==0){
            alert( "<?php echo JText::_( 'COM_TZ_PORTFOLIO_FIELD_SELECT_TYPE', true ); ?>" );
            form.type.focus();
        }
        else {
            submitform( pressbutton);
        }
    }
    window.addEvent('load',function(){
        $$('.chzn-drop li').addEvent('click',function(e){
            e.stop();
            var tz_count_gb = 0;

            var createBox = function(object,name,tz_count){
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
                    id:"image-select-"+tz_count,
                    readonly:'true',
                    style:"width:200px;"
                });
                tz_a.inject(myDiv);
                var tz_d = "image-select-" + tz_count,
                    tz_b = (new Element("a", {
                        class: 'btn',
                        "id": "tz_img_button"+tz_count
                    })).set('html', "<\i class=\"icon-file\"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BROWSE_SERVER');?>").inject(tz_a,'after'),
                    tz_f = (new Element("a", {
                        class: 'btn',
                        "name": "tz_img_cancel_"+tz_count,
                        html:'<i class="icon-refresh"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_RESET');?>'
                    })).inject(tz_b,'after'),
                    tz_k    =(new Element("div",{class: 'clearfix'}))
                    tz_g = (new Element("div", {
                        "class": "tz-image-preview",
                        "style": "margin-top:15px;max-width:300px"
                    })).inject(tz_f,'after');

                tz_a.setProperty("id", tz_d);
                tz_a.getProperty("value") && (new Element("img", {
                    src: tz_e + tz_a.getProperty("value"),
                    style:'max-width:150px'
                })).inject(tz_g);
                tz_f.addEvent("click", function (e) {
                    e.stop();
                    tz_a.setProperty("value", "");
                    tz_a.getParent().getElement("div.tz-image-preview").empty()
                });

                tz_b.addEvent("click", function (h) {
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

            $('fields').empty();
            var optionFields = function(){
                var myButton = new Element('a', {
                    class: 'btn',
                    html: '<i class="icon-plus"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW');?>',
                    events: {

                        click: function(e){
                            e.stop();

                            var myDiv = new Element('div',{
                                id:'option_name_'+tz_count_gb,
                                styles:{
                                    display:'block',
                                    width:'100%',
                                    float:'left',
                                    'padding-top':'20px'
                                }
                            });
                            myDiv.inject($('fieldvalue'));

                            var myBox = createBox($('fieldvalue'),'option_icon[]',tz_count_gb+1);

                            //value
                            var myValue = new Element('input',{
                                'name': 'option_name[]',
                                'type':'text'
                            });
                            myValue.inject(myDiv);
                            var myRemove = new Element('a',{
                                class: 'btn',
                                style: 'margin-left: 5px;',
                                html:'<i class="icon-remove"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE');?>',
                                events:{
                                    click: function(e){
                                        e.stop();
                                        myDiv.dispose();
                                        myValue.dispose();
                                        myDefault.dispose();
                                        myRemove.dispose();
                                        myBox.dispose();
                                        tz_count_gb--;
                                    }
                                }
                            });
                            myRemove.inject(myDiv);

                            if($('type').value == 'multipleSelect' || $('type').value == 'checkbox'){
                                var myDefault = new Element('div',{
                                    style:"display:inline-block; padding-left:10px; width:20%;",
                                    html:'<\input type="checkbox" name="default[]" value="'+(tz_count_gb + 1)+'" \style="margin:0;"/><\span style="padding-left:5px; font-size: 11px;">'+
                                            '<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUES');?></i></span>'
                                }).inject(myDiv);
                            }

                            if($('type').value == 'select' || $('type').value == 'radio'){
                                var myDefault = new Element('div',{
                                    style:"display:inline-block; padding-left:10px; width:20%;",
                                    html:'<\input type="radio" name="default[]" value="'+(tz_count_gb + 1)+'" \style="margin:0;"/><\span style="padding-left:5px; font-size: 11px;">'+
                                            '<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUE');?></i></span>'
                                }).inject(myDiv);
                            }

                            tz_count_gb++;
                        }
                    }
                });
                myButton.inject($('fieldvalue'));
                var myDiv = new Element('div',{
                    styles:{
                        float:'left',
                        width:'100%',
                        'padding-top':'20px'
                    }
                });
                myDiv.inject($('fieldvalue'));
                var myValue = new Element('input',{
                    type: 'text',
                    'name': 'option_name[]'
                });
                myValue.inject(myDiv);

                var myRemove = new Element('a',{
                    class: 'btn',
                    style: 'margin-left: 5px;',
                    html:'<i class="icon-remove"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE');?>',
                    events:{
                        click: function(e){
                            e.stop();
                            myDiv.dispose();
                            myValue.dispose();
                            myDefault.dispose();
                            myRemove.dispose();
                            myBox.dispose();
                            tz_count_gb--;
                        }
                    }
                });
                myRemove.inject(myDiv);
                
                if($('type').value == 'multipleSelect' || $('type').value == 'checkbox'){
                    var myDefault = new Element('div',{
                        style:"display:inline-block; padding-left:10px; width:20%;",
                        html:'<\input type="checkbox" name="default[]" value="0" style="margin:0"/><span style="padding-left:5px; font-size: 11px;">'+
                                '<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUES');?></i></span>'
                    }).inject(myRemove,'after');
                }

                if($('type').value == 'select' || $('type').value == 'radio'){
                    var myDefault = new Element('div',{
                        style:"display:inline-block; padding-left:10px; width:20%;",
                        html:'<\input type="radio" name="default[]" value="0" style="margin:0"/><span style="padding-left: 5px; font-size: 11px;">'+
                                '<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUE');?></i></span>'
                    }).inject(myRemove,'after');
                }

                var myBox = createBox($('fieldvalue'),'option_icon[]',0);


            };
            var myField =   new Element('div', {
                id : 'fieldvalue'
            });
            myField.inject($('fields'));
            switch (document.adminForm.type.value) {
                case 'textfield':
                    var myField = new Element('input', {
                        type: 'text',
                        'name': 'option_value[]',
                        size:'19',
                        'value': ''
                    });
                    myField.inject($('fieldvalue'));
                    var myDefault   = new Element('span',{
                        style:"font-size:11px; padding-left:5px;",
                        html:'<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUE');?></i>'
                    }).inject(myField,'after');
                    createBox($('fieldvalue'),'option_icon[]',0);
                    break;
                case 'multipleSelect':
                case 'radio':
                case 'checkbox':
                case 'select':
                        optionFields();
                    break;
                case 'textarea':
                    var myDefault   = new Element('div',{
                        style:"font-size:11px; padding-top:5px;",
                        html:'<i><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT_VALUE');?></i>'
                    }).inject($('fieldvalue'));
                    var myDiv = new Element('div').inject($('fieldvalue'));
                    var myField = new Element('textarea',{
                        'name':'option_value[]',
                        styles:{
                            display:'block',
                            width: '300px',
                            height: '100px'
                        }
                    });
                    myField.inject(myDiv);
                   /* var myDiv = new Element('div',{
                        html:<?php echo '\'<strong><i>'.JText::_('Use editor').'</i></strong>\'';?>,
                        styles:{
                            display:'block',
                            float:'left',
                            width:'100%'
                        }
                    });
                    myDiv.inject($('fieldvalue'));
                    var myField = new Element('input',{
                       type:'checkbox',
                       'name':'option_editor'
                    });
                    myField.inject(myDiv);*/
                    createBox($('fieldvalue'),'option_icon[]',0);
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
                            'name':'option_name[]'
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
                            'name':'option_value[]'
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
                            html:'<option value="_self"><?php echo JText::_('COM_TZ_PORTFOLIO_SAME_WINDOW');?></option>'
                                    +'<option value="_blank"><?php echo JText::_('COM_TZ_PORTFOLIO_NEW_WINDOW');?></option> ',
                            'name':'option_target[]'
                        });
                        myField.inject(linkDiv);
                        var myDiv = new Element('div',{'class':'clr'}).inject(linkDiv);
                        jQuery('select').chosen({
                            disable_search_threshold : 10,
                            allow_single_deselect : true
                        });
                        createBox($('fieldvalue'),'option_icon[]',0);
                    break;
                default:
                    $('fields').set('html', '<label><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_FIELD_VALUES_DESC');?></label>');
                    break;
            }
        });
    });
</script>
<form name="adminForm" method="post" action="index.php?option=<?php echo $this -> option;?>&view=<?php echo $this -> view;?>">
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
                            <input type="text" maxlength="50" size="50" value="" id="title" name="title"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_ORDERING')?>
                            </label>
                        </div>
                        <div class="controls">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_ORDER_DESC');?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100" for="fieldsgroup" class="hasTip"
                                   title="<?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED_DESC')?>">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED')?>
                                <span class="star"> *</span>
                            </label>
                        </div>
                        <div class="controls">
                            <select multiple="multiple" size="10" id="fieldsgroup" name="fieldsgroup[]" style="width: 220px;">
                                <option value="-1" selected="selected">
                                    <?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_GROUP');?>
                                </option>
                                <?php
                                    if(count($this -> listsGroup)>0){
                                        foreach($this -> listsGroup as $row){
                                ?>
                                <option value="<?php echo $row -> id;?>"><?php echo $row -> name;?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100" class="hasTip"
                                   title="<?php echo JText::_('COM_TZ_PORTFOLIO_TYPE')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_TYPE_DESC')?>">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_TYPE')?>
                                <span class="star"> *</span>
                            </label>
                        </div>
                        <div class="controls">
                            <select name="type" id="type">
                                <option value="0"><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_TYPE');?></option>
                                <option value="textfield"><?php echo JText::_('COM_TZ_PORTFOLIO_TEXT_FIELD');?></option>
                                <option value="textarea"><?php echo JText::_('COM_TZ_PORTFOLIO_TEXTAREA');?></option>
                                <option value="select"><?php echo JText::_('COM_TZ_PORTFOLIO_DROP_DOWN_SELECTION');?></option>
                                <option value="multipleSelect"><?php echo JText::_('COM_TZ_PORTFOLIO_MULTI_SELECT_LIST');?></option>
                                <option value="radio"><?php echo JText::_('COM_TZ_PORTFOLIO_RADIO_BUTTONS');?></option>
                                <option value="checkbox"><?php echo JText::_('COM_TZ_PORTFOLIO_CHECK_BOX');?></option>
                                <option value="link"><?php echo JText::_('COM_TZ_PORTFOLIO_LINK');?></option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_FIELD_VALUES')?>
                            </label>
                        </div>
                        <div class="controls" id="fields">
                            <label><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_FIELD_VALUES_DESC');?></label>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <label width="100">
                                <?php echo JText::_('COM_TZ_PORTFOLIO_DESCRIPTION');?>
                            </label>
                        </div>
                        <div class="controls">
                            <?php echo $this -> editor -> display('description','','100%', '300', '60', '20', array('pagebreak', 'readmore'));?>
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
                <label width="100" for="published">
                    <?php echo JText::_('JPUBLISHED')?>:
                </label>
                <div class="controls">
                    <?php echo JHtml::_('grid.state','*','JPUBLISHED','JUNPUBLISHED');?>
                </div>
            </div>
        </fieldset>
    </div>
    <!-- End Sidebar -->
    <input type="hidden" value="<?php echo $this -> option;?>" name="option">
    <input type="hidden" value="" name="cid[]">
    <input type="hidden" value="" name="task">
    <?php echo JHTML::_('form.token');?>
</form>