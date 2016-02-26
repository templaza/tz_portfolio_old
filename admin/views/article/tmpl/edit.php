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

// No direct access.
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$doc    = JFactory::getDocument();
$doc -> addScript(JUri::base(true).'/components/com_tz_portfolio/js/tz-chosen.min.js');
$doc -> addStyleSheet(JUri::base(true).'/components/com_tz_portfolio/css/tz_portfolio.min.css');
if(!$this -> tagsSuggest){
    $this -> tagsSuggest    = 'null';
}
$doc -> addScriptDeclaration('
    jQuery(document).ready(function(){
        jQuery(".suggest").tzChosen({ source: '.$this -> tagsSuggest.', sourceEdit: '.$this -> listsTags.',keys: ["\,","/"]});
    })
');

// Create shortcut to parameters.
	$params = $this->state->get('params');

//
	$params = $params->toArray();

// This checks if the config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params['show_publishing_options']);

if (!$editoroptions):
	$params['show_publishing_options'] = '1';
	$params['show_article_options'] = '1';
	$params['show_urls_images_backend'] = '0';
	$params['show_urls_images_frontend'] = '0';
endif;

// Check if the article uses configuration settings besides global. If so, use them.
if (!empty($this->item->attribs['show_publishing_options'])):
		$params['show_publishing_options'] = $this->item->attribs['show_publishing_options'];
endif;
if (!empty($this->item->attribs['show_article_options'])):
		$params['show_article_options'] = $this->item->attribs['show_article_options'];
endif;
if (!empty($this->item->attribs['show_urls_images_backend'])):
		$params['show_urls_images_backend'] = $this->item->attribs['show_urls_images_backend'];
endif;

$type       = '';
$mediavalue = '';
$media      = array();
$list       = $this -> listEdit;

if($list){
    $type   = $list -> type;
}

$pluginsTab = $this -> pluginsTab;

$assoc  = false;
if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
    $assoc = JLanguageAssociations::isEnabled();
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'article.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
        else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
            if($('jform_type_of_media').hasClass('invalid')){
                $$('#jform_type_of_media_chzn a').setStyle('border','1px solid red');
            }
            else{
                $$('#jform_type_of_media_chzn a').removeClass('invalid');
            }
		}
	}

    // extra fields
    window.addEvent('load', function() {
        var ajax = function(){
            var jSonRequest  = new Request.JSON({url:"index.php?option=com_tz_portfolio&task=article.extrafields",
               onComplete:function(item){
                   var data = item;
                   $('tz_fieldsid_content').getParent('div').setStyle('display','none');
                   $('jform_attribs_tz_fieldsid_content-lbl').getParent('div').setStyle('display','none');
                   if(data){
                       $('tz_fieldsid_content').set('html',data);

                       $('tz_fieldsid_content').getParent('div').set('style','');
                       $('jform_attribs_tz_fieldsid_content-lbl').getParent('div').set('style','');
                   }
                   jQuery('select').chosen({
                        disable_search_threshold : 10,
                        allow_single_deselect : true
                   });
               },
               data: {
                    json: JSON.encode({
                        'id':<?php echo JRequest::getInt('id');?>,
                        'groupid':$('groupid').value,
                        catid:$('jform_catid').value
                    })
                }
           }).send();
        };

        ajax();

//        $('groupid').addEvent('change',function(e){
//            e.stop();
//            $('tz_fieldsid_content').getParent('div').setStyle('display','none');
//            ajax();
//        });
//        $$('#groupid_chzn .chzn-drop li').addEvent('click',function(e){
        jQuery('#groupid').bind('change',function(e){
            e.preventDefault();
//            e.stop();
            $('tz_fieldsid_content').getParent('div').setStyle('display','none');
            $('jform_attribs_tz_fieldsid_content-lbl').getParent('div').setStyle('display','none');
            ajax();
        });

        var tz_portfolio_extraFields = function(){

            var jSonRequest = new Request.JSON({url: "index.php?option=com_tz_portfolio&task=article.listsfields",
                onComplete: function(item) {
                    $('tz_fields').empty();
                    var myFields = new Element('table',{
                        width:'100%',
                        class:'admintable',
                        id:'fields'
                        //html:item
                    });

                    myFields.inject($('tz_fields'));
                    myFields.innerHTML = item.data;
                    SqueezeBox.initialize({});
                    SqueezeBox.assign(jQuery('a.modal-button').get(), {
                        parse: 'rel'
                    });

                    jQuery('select').chosen({
                        disable_search_threshold : 10,
                        allow_single_deselect : true
                    });

                },
                data: {
                    json: JSON.encode({
                        'groupid':$('groupid').value,
                        'id':$('contentid').value,
                        'catid':$('jform_catid').value
                    })
                }
            }).send();

        }


        var jSonRequest2 = new Request.JSON({url: "index.php?option=com_tz_portfolio&task=article.selectgroup",
            onComplete: function(item){
                tz_portfolio_extraFields();
            },
            data:{
                json2: JSON.encode({
                    'catid':$('jform_catid').value,
                    'id':$('contentid').value,
                    'groupid':$('groupid').value
                })
            }
        }).send();

//        $$('#jform_catid_chzn .chzn-drop li').addEvent('click',function(e){
        jQuery('#jform_catid').bind('change',function(e){
            e.preventDefault();

            ajax();

            var jSonRequest2 = new Request.JSON({url: "index.php?option=com_tz_portfolio&task=article.selectgroup",
                onComplete: function(){
                    tz_portfolio_extraFields();
                },
                data:{
                    json2: JSON.encode({
                        'catid':$('jform_catid').value,
                        'id':$('contentid').value,
                        'groupid':$('groupid').value
                    })
                }
            }).send();
        });

        var tz_portfolio_groupChange = function(){
//            $$('#groupid_chzn .chzn-drop li').addEvent('click',function(e){
            jQuery('#groupid').bind('change',function(e){
                e.preventDefault();
                tz_portfolio_extraFields();
            });
        }
        tz_portfolio_groupChange();

    });

    //Media
    window.addEvent('load',function(){
        function tz_thumb(){
            <?php //if(!empty($list -> video -> thumb)):?>
            if($('tz_thumb'))
                $('tz_thumb').dispose();
            <?php //endif;?>
            var myTr = new Element('tr', {id: 'tz_thumb'})
                    .inject($('tz_media_code_outer'),'after');
            var myThumbInner    = new Element('td',{
                id: 'tz_thumb_inner',
                valign: "top",
                align: "right",
                style: "background: #F6F6F6; min-width:100px;"
            }).inject(myTr);
            var myElement  = new Element('strong');
            myElement.appendText('<?php echo JText::_('COM_TZ_PORTFOLIO_THUMBNAIL');?>');
            myThumbInner.adopt(myElement);

            var myThumbPre  = new Element('td',{
                id: 'tz_thumb_preview',
                class: 'input-prepend input-append'
            }).inject(myThumbInner,'after');

//            var tz_e = location.href.match(/^(.+)\/index\.php.*/i)[1];

            var icon = new Element('div',{
                class: 'add-on',
                html: '<\i class="icon-eye"></i>'
            }).inject($('tz_thumb_preview'));
            var tz_a = new Element('input',{
                type:"text",
                class:"inputbox image-select",
                name:"tz_thumb",
                id:"image-thumb",
                readonly:'true',
                style:"width:200px;"
            });
            tz_a.inject($('tz_thumb_preview'));
            var tz_d = "image-thumb",
                tz_b = (new Element("button", {
                    type: "button",
                    class: "btn",
                    "id": "tz_thumb_button"
                })).set('html', '<i class="icon-file"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BROWSE_SERVER');?>').inject(tz_a,'after'),
                tz_f = (new Element("button", {
                    "name": "tz_thumb_cancel",
                    "id"  : "tz_thumb_cancel",
                    class: 'btn',
                    html:'<i class="icon-refresh"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_RESET');?>'
                })).inject(tz_b,'after'),
                tz_g = (new Element("div", {
                    "class": "tz-image-preview",
                    "style": "clear:both;"
                })).inject(tz_f,'after');

            if(tz_g)
                tz_g.empty();

            tz_a.setProperty("id", tz_d);
            <?php
                if($list -> video -> type == 'default' AND !empty($list -> video -> thumb)):
                    $src    = JURI::root().str_replace('.'.JFile::getExt($list -> video -> thumb)
                        ,'_S.'.JFile::getExt($list -> video -> thumb),$list -> video -> thumb);
                    $src2   = JURI::root().str_replace('.'.JFile::getExt($list -> video -> thumb)
                        ,'_L.'.JFile::getExt($list -> video -> thumb),$list -> video -> thumb);
            ?>
                var tz_hidden   = new Element('input',{
                   type: 'hidden',
                    name: 'tz_thumb_hidden',
                    value: '<?php echo $list -> video -> thumb;?>'
                }).inject(tz_g);
                var tz_img = new Element("img", {
                    src: '<?php echo $src.'?time='.str_replace('.','',microtime(true));?>',
                    style: 'cursor:pointer; max-width: 200px;'
                }).inject(tz_g);
                tz_img.addEvent('click',function(){
                    SqueezeBox.fromElement(this, {
                        handler: "image",
                        url: '<?php echo $src2.'?time='.str_replace('.','',microtime(true));?>'
                    });
                });
                var tz_checkbox = new Element('input',{
                    type: 'checkbox',
                    style:'clear:both;',
                    name: 'tz_thumb_del',
                    id: 'tz_thumb_del'
                }).inject(tz_img,'after');
                var tz_label = new Element('label',{
                    'for': 'tz_thumb_del',
                    style: 'clear: none; margin: 2px 3px;',
                    html: '<?php echo JText::_('COM_TZ_PORTFOLIO_DELETE_IMAGES');?>'
                }).inject(tz_checkbox,'after');


            <?php endif;?>

            tz_f.addEvent("click", function (e) {
                e.stop();
                $('image-thumb').value='';
                tz_a.setProperty("value", "");
            });

            tz_b.addEvent("click", function (h) { h.stop();
                SqueezeBox.fromElement(this, {
                    handler: "iframe",
                    url: "index.php?option=com_media&view=images&tmpl=component&asset=<?php echo JFactory::getUser() -> id;?>&author=<?php echo JFactory::getUser() -> id;?>&e_name=" + tz_d,
                    size: {
                        x: 800,
                        y: 500
                    }
                });

                window.jInsertEditorText = function (text, editor) {
                    if (editor.match(/^image-thumb/)) {

                        var d = $(editor);
                        var src = text.match(/src=\".*?\"/i);
                        src = String.from(src);
                        src = src.replace(/^src=\"/g,'');
                        src = src.replace(/\"$/g,'');
                        d.setProperty("value", src);
                    } else tinyMCE.execInstanceCommand(editor, 'mceInsertContent',false,text);
                };

            });

        }
        switch ($('tz_media_type').value){
            case 'youtube':
                if($('tz_media_code_youtube')){
                    <?php if($list -> video -> type == 'youtube' AND empty($list -> video -> code)):?>
                        $('tz_media_code_youtube').value = '';
                    <?php endif;?>
                }

                if($('tz_thumb'))
                    $('tz_thumb').dispose();
                $('tz_media_code').empty();
                var myCode = new Element('input',{
                    type  : 'text',
                    name  : 'tz_media_code_youtube',
                    size  : '30',
                    value : '<?php if($list -> video -> type == 'youtube')
                        echo $list -> video -> code;?>'
                }).inject($('tz_media_code'));
                var myLabel = new Element('i',{
                    html : '<?php echo JText::_('COM_TZ_PORTFOLIO_PASTE_CODE');?> '+$('tz_media_type').value
                }).inject($('tz_media_code'));
                $('tz_media_title').empty();
                var myTitle = new Element('input',{
                    type:'text',
                    name:'tz_media_title_youtube',
                    value:'<?php if($list -> video -> type == 'youtube')
                        echo $list -> video -> title;?>'
                }).inject($('tz_media_title'));

                if($('tz_media_type').value == 'youtube'){
                    if($('tz_thumb_preview_youtube'))
                        $('tz_thumb_preview_youtube').empty();

                    if(myCode.value.trim().length != 0){
                        var video   = new Element('div',{id:'tz_thumb_preview_youtube'}).inject(myLabel,'after');
                        var iframe  = new Element('img',{
                            style: 'margin-top:10px; cursor:pointer; max-width: 200px;',
                            <?php if(isset($list -> video->thumb)):?>
                                <?php if($list -> video -> thumb):?>
                                src: '<?php echo JUri::root().str_replace('.'.JFile::getExt($list -> video -> thumb),
                                '_S.'.JFile::getExt($list -> video -> thumb),$list -> video -> thumb)
                                .'?time='.str_replace('.','',microtime(true));?>'
                                <?php endif;?>
                            <?php else:?>
                            src:'http://img.youtube.com/vi/'+ myCode.value+'/mqdefault.jpg'
                            <?php endif;?>
                        }).inject(video);
                        iframe.addEvent('click',function(){
                           SqueezeBox.fromElement(this, {
                                handler: "image",
                               <?php if(isset($list -> video->thumb)):?>
                                    <?php if($list -> video -> thumb):?>
                                    url: '<?php echo JUri::root().str_replace('.'.JFile::getExt($list -> video -> thumb),
                                            '_L.'.JFile::getExt($list -> video -> thumb),$list -> video -> thumb)
                                            .'?time='.str_replace('.','',microtime(true));?>'
                                    <?php endif;?>
                                <?php else:?>
                                url: 'http://img.youtube.com/vi/'+ myCode.value+'/mqdefault.jpg'
                                <?php endif;?>
                           });
                        });
                    }
                }
                break;
            case 'vimeo':
                if($('tz_thumb'))
                    $('tz_thumb').dispose();
                $('tz_media_code').empty();
                var myCode = new Element('input',{
                    type  : 'text',
                    name  : 'tz_media_code_vimeo',
                    size  : '30',
                    value : '<?php if($list -> video -> type =='vimeo')
                        echo $list -> video -> code;?>'
                }).inject($('tz_media_code'));
                var myLabel = new Element('i',{
                    html : '<?php echo JText::_('COM_TZ_PORTFOLIO_PASTE_CODE');?> '+$('tz_media_type').value
                }).inject($('tz_media_code'));
                $('tz_media_title').empty();
                var myTitle = new Element('input',{
                    type:'text',
                    name:'tz_media_title_vimeo',
                    value:'<?php if($list -> video -> type =='vimeo')
                        echo $list -> video -> title;?>'
                }).inject($('tz_media_title'));

                if($('tz_thumb_preview_vimeo'))
                    $('tz_thumb_preview_vimeo').empty();
                <?php
                    if($list -> video -> type == 'vimeo' AND !empty($list -> video -> thumb)):
                        $src    = JURI::root().str_replace('.'.JFile::getExt($list -> video -> thumb)
                            ,'_S.'.JFile::getExt($list -> video -> thumb),$list -> video -> thumb);
                        $src2   = JURI::root().str_replace('.'.JFile::getExt($list -> video -> thumb)
                            ,'_L.'.JFile::getExt($list -> video -> thumb),$list -> video -> thumb);
                ?>
                    var video   = new Element('div',{id:'tz_thumb_preview_vimeo'}).inject(myLabel,'after');
                    var iframe  = new Element('img',{
                        style: 'margin-top:10px; max-width:200px; cursor:pointer;',
                        src: '<?php echo $src.'?time='.str_replace('.','',microtime(true));?>'
                    }).inject(video);
                    iframe.addEvent('click',function(){
                       SqueezeBox.fromElement(this, {
                            handler: "image",
                            url: '<?php echo $src2.'?time='.str_replace('.','',microtime(true));?>'
                       });
                    });
                <?php endif;?>
                break;
            default:
                tz_thumb();
                $('tz_media_code').empty();
                var myLabel = new Element('label',{
                    html : '<?php echo JText::_('COM_TZ_PORTFOLIO_PASTE_HTML_CODE');?>'
                }).inject($('tz_media_code'));
                new Element('div',{
                    style:'clear:both'
                }).inject(myLabel,'after');
                var myCode = new Element('textarea',{
                    name  : 'tz_media_code',
                    size  : '30',
                    rows  : '10',
                    cols  : '20',
                    value : '<?php if($list -> video -> type == 'default') echo $list -> video -> code;?>'
                }).inject($('tz_media_code'));
                $('tz_media_title').empty();
                var myTitle = new Element('input',{
                    type:'text',
                    name:'tz_media_title',
                    value:'<?php if($list -> video -> type == 'default') echo $list -> video -> title;?>'
                }).inject($('tz_media_title'));
            break;
        }
        //$$('#tz_media_type_chzn .chzn-drop li').addEvent('click',function(){
        jQuery('#tz_media_type').bind('change',function(){

            switch ($('tz_media_type').value){
                case 'youtube':
                    if($('tz_media_code_youtube')){
                        <?php if($list -> video -> type != 'youtube'):?>
                            $('tz_media_code_youtube').value = '';
                        <?php endif;?>
                    }
                    if($('tz_thumb'))
                        $('tz_thumb').dispose();
                    $('tz_media_code').empty();
                    var myCode = new Element('input',{
                        type  : 'text',
                        name  : 'tz_media_code_youtube',
                        id  : 'tz_media_code_youtube',
                        size  : '30',
                        value : '<?php if($list -> video -> type == 'youtube')
                            echo $list -> video -> code;?>'
                    }).inject($('tz_media_code'));
                    var myLabel = new Element('i',{
                        html : '<?php echo JText::_('COM_TZ_PORTFOLIO_PASTE_CODE');?> '+$('tz_media_type').value
                    }).inject($('tz_media_code'));
                    $('tz_media_title').empty();
                    var myTitle = new Element('input',{
                        type:'text',
                        name:'tz_media_title_youtube',
                        value:'<?php if($list -> video -> type == 'youtube')
                            echo $list -> video -> title;?>'
                    }).inject($('tz_media_title'));

                    if($('tz_media_type').value == 'youtube'){
                        if($('tz_thumb_preview_youtube'))
                            $('tz_thumb_preview_youtube').empty();

                        if(myCode.value.trim().length != 0){
                            var video   = new Element('div',{id:'tz_thumb_preview_youtube'}).inject(myLabel,'after');
                            var iframe  = new Element('img',{
                                style: 'margin-top:10px; cursor:pointer; max-width: 200px;',
                                src:'http://img.youtube.com/vi/'+ myCode.value+'/mqdefault.jpg'
                            }).inject(video);
                            iframe.addEvent('click',function(){
                               SqueezeBox.fromElement(this, {
                                    handler: "image",
                                    url: 'http://img.youtube.com/vi/'+ myCode.value+'/mqdefault.jpg'
                               });
                            });
                        }
                    }

                    myCode.addEvent('change',function(){
                        if($('tz_media_type').value == 'youtube'){
                            if($('tz_thumb_preview_youtube'))
                                $('tz_thumb_preview_youtube').empty();

                            if(myCode.value.trim().length != 0){
                                var video   = new Element('div',{id:'tz_thumb_preview_youtube'}).inject(myLabel,'after');
                                var iframe  = new Element('img',{
                                    style: 'margin-top:10px; cursor:pointer; max-width: 200px;',
                                    src:'http://img.youtube.com/vi/'+ myCode.value+'/mqdefault.jpg'
                                }).inject(video);
                                iframe.addEvent('click',function(){
                                   SqueezeBox.fromElement(this, {
                                        handler: "image",
                                        url: 'http://img.youtube.com/vi/'+ myCode.value+'/mqdefault.jpg'
                                   });
                                });
                            }
                        }
                    });
                    break;
                case 'vimeo':
                    if($('tz_media_code_vimeo')){
                        <?php if($list -> video -> type != 'vimeo'):?>
                            $('tz_media_code_vimeo').value = '';
                        <?php endif;?>
                    }
                    if($('tz_thumb'))
                        $('tz_thumb').dispose();
                    $('tz_media_code').empty();
                    var myCode = new Element('input',{
                        type  : 'text',
                        name  : 'tz_media_code_vimeo',
                        id  : 'tz_media_code_vimeo',
                        size  : '30',
                        value : '<?php if($list -> video -> type =='vimeo')
                            echo $list -> video -> code;?>'
                    }).inject($('tz_media_code'));
                    var myLabel = new Element('i',{
                        html : '<?php echo JText::_('COM_TZ_PORTFOLIO_PASTE_CODE');?> '+$('tz_media_type').value
                    }).inject($('tz_media_code'));
                    $('tz_media_title').empty();
                    var myTitle = new Element('input',{
                        type:'text',
                        name:'tz_media_title_vimeo',
                        value:'<?php if($list -> video -> type =='vimeo')
                            echo $list -> video -> title;?>'
                    }).inject($('tz_media_title'));

                    if($('tz_thumb_preview_vimeo'))
                        $('tz_thumb_preview_vimeo').empty();
                    <?php
                        if($list -> video -> type == 'vimeo' AND !empty($list -> video -> thumb)):
                            $src    = JURI::root().str_replace('.'.JFile::getExt($list -> video -> thumb)
                                ,'_S.'.JFile::getExt($list -> video -> thumb),$list -> video -> thumb);
                            $src2   = JURI::root().str_replace('.'.JFile::getExt($list -> video -> thumb)
                                ,'_L.'.JFile::getExt($list -> video -> thumb),$list -> video -> thumb);
                    ?>
                        var video   = new Element('div',{id:'tz_thumb_preview_vimeo'}).inject(myLabel,'after');
                        var iframe  = new Element('img',{
                            style: 'margin-top:10px; max-width:200px; cursor:pointer;',
                            src: '<?php echo $src;?>'
                        }).inject(video);
                        iframe.addEvent('click',function(){
                           SqueezeBox.fromElement(this, {
                                handler: "image",
                                url: '<?php echo $src2.'?time='.str_replace('.','',microtime(true));?>'
                           });
                        });
                    <?php endif;?>

                    myCode.addEvent('change',function(){

                        if($('tz_media_type').value == 'vimeo'){
                            var vimeoVideoID = myCode.value;

                           var ajaxreg = new Request.JSON({
                               url: 'index.php?option=com_tz_portfolio&task=article.getThumb',
                               onComplete: function(data){
                                    if($('tz_thumb_preview_2'))
                                        $('tz_thumb_preview_2').empty();
                                    if(data && data.length !=0){
                                        var video   = new Element('div',{id:'tz_thumb_preview_vimeo'}).inject(myLabel,'after');
                                        var iframe  = new Element('img',{
                                            style: 'margin-top:10px; max-width:200px; cursor:pointer;',
                                            src: data
                                        }).inject(video);
                                        iframe.addEvent('click',function(){
                                           SqueezeBox.fromElement(this, {
                                                handler: "image",
                                                url: data
                                           });
                                        });
                                    }
                               },
                               data: {
                                   json: JSON.encode({
                                      'videocode': myCode.value
                                   })
                               }
                           }).send();

                        }
                    });

                    break;
                default:
                    tz_thumb();
                    $('tz_media_code').empty();
                    var myLabel = new Element('label',{
                        html : '<?php echo JText::_('COM_TZ_PORTFOLIO_PASTE_HTML_CODE');?>'
                    }).inject($('tz_media_code'));
                    new Element('div',{
                        style:'clear:both'
                    }).inject(myLabel,'after');
                    var myCode = new Element('textarea',{
                        name  : 'tz_media_code',
                        size  : '30',
                        rows  : '10',
                        cols  : '20',
                        value : '<?php if($list -> video -> type == 'default') echo $list -> video -> code;?>'
                    }).inject($('tz_media_code'));
                    $('tz_media_title').empty();
                    var myTitle = new Element('input',{
                        type:'text',
                        name:'tz_media_title',
                        value:'<?php if($list -> video -> type == 'default') echo $list -> video -> title;?>'
                    }).inject($('tz_media_title'));
                break;
            }
        });
    });

    // Image, Image gallery
    window.addEvent("load", function () {
        var tz_count=0;
        var tz_portfolio_image = function(id,name,value,title,i){

//            var tz_e = location.href.match(/^(.+)administrator\/index\.php.*/i)[1];

            var icon = new Element('div',{
                class: 'add-on',
                html: '<\i class="icon-eye"></i>'
            }).inject($(id));
            var tz_a = new Element('input',{
                type:"text",
                class:"inputbox image-select",
                name:name,
                id:"image-select-"+tz_count,
                readonly:'true',
                style:"width:200px;"
            });
            tz_a.inject($(id));
            var tz_d = "image-select-" + tz_count,
                tz_b = (new Element("a", {
                    class: 'btn',
                    "id": "tz_img_button"+tz_count
                })).set('html', '<i class="icon-file"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BROWSE_SERVER');?>').inject(tz_a,'after'),
                tz_f = (new Element("a", {
                    class: 'btn',
                    "name": "tz_img_cancel_"+i,
                    html:'<i class="icon-refresh"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_RESET');?>'
                })).inject(tz_b,'after'),
                tz_g = (new Element("div", {
                    "class": "tz-image-preview",
                    "style": "clear:both;"
                })).inject(tz_f,'after');

            tz_a.setProperty("id", tz_d);
            if(value){
                var $date   = new Date();
                 var tz_h = (new Element("img", {
                    src: value+'?time='+$date.getTime(),
                    style:'max-width:300px; cursor:pointer;',
                    title:title
                })).inject(tz_g,'inside');
                tz_h.addEvent('click',function(){
                   SqueezeBox.fromElement(this, {
                        handler: "image",
                        url: String.from(value.replace(/_S/,'_L')) + '?time='+$date.getTime()
                    });
                });
            }



            tz_f.addEvent("click", function (e) {
                e.stop();
                if(id == 'tz_img_server'){
                    $('tz_img').value='';
                }

                $('tz_img_client_'+i).value='';

                if(id == 'tz_img_hover_server')
                    $('tz_img_hover').value='';
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
                    if (editor.match(/^image-select-/)) {

                        var d = $(editor);
                        var src = text.match(/src=\".*?\"/i);
                        src = String.from(src);
                        src = src.replace(/^src=\"/g,'');
                        src = src.replace(/\"$/g,'');
                        d.setProperty("value", src);
                    } else tinyMCE.execInstanceCommand(editor, 'mceInsertContent',false,text);
                };

            });
            tz_count++;
        }

    $('tz_img_server').empty();
    <?php
    if(!empty($list -> images)){
        $src    = null;
        if($pos = strpos($list -> images,'.')){
            $ext    = substr($list -> images,$pos,strlen($list -> images));
            $src    = JURI::root().str_replace($ext,'_S'.$ext,$list -> images);
        }

    ?>
    tz_portfolio_image('tz_img_server','tz_img_gallery_server[]','<?php echo $src;?>','<?php echo $list -> imagetitle?>',0);
    var tz_hidden = new Element('input',{
        'type': 'hidden',
        'name': 'tz_image_current',
        'value': '<?php echo $list -> images; ?>'
    }).inject($('tz_img_server'));
    var tz_checkbox = new Element("input",{
        type: 'checkbox',
        id: 'tz_del_image',
        'name': 'tz_delete_image',
        'value': 0,
        style: 'clear: both'
    }).inject($('tz_img_server'));
    var tz_label = new Element('label',{
        'for': 'tz_del_image',
        style: 'clear: none; margin: 2px 3px;',
        html: '<?php echo JText::_('COM_TZ_PORTFOLIO_CURRENT_IMAGE_DESC');?>'
    }).inject($('tz_img_server'));

    <?php
    }
    else{
    ?>
        tz_portfolio_image('tz_img_server','tz_img_gallery_server[]','','',0);
    <?php
    }
    ?>

    <?php if(!empty($list -> images_hover)){?>
        <?php
            $src    = null;
            if($pos = strpos($list -> images_hover,'.')){
                $ext    = substr($list -> images_hover,$pos,strlen($list -> images_hover));
                $src    = JURI::root().str_replace($ext,'_S'.$ext,$list -> images_hover);
            }
        ?>
        tz_portfolio_image('tz_img_hover_server','tz_img_hover_server','<?php echo $src;?>','<?php echo $list -> imagetitle?>',0);
        var tz_hidden = new Element('input',{
            'type': 'hidden',
            'name': 'tz_imgHover_current',
            'value': '<?php echo $list -> images_hover; ?>'
        }).inject($('tz_img_hover_server'));
        var tz_checkbox = new Element("input",{
            type: 'checkbox',
            id: 'tz_del_imgHover',
            'name': 'tz_delete_imgHover',
            'value': 0,
            style: 'clear: both'
        }).inject($('tz_img_hover_server'));
        var tz_label = new Element('label',{
            'for': 'tz_del_imgHover',
            style: 'clear: none; margin: 2px 3px;',
            html: '<?php echo JText::_('COM_TZ_PORTFOLIO_CURRENT_IMAGE_DESC');?>'
        }).inject($('tz_img_hover_server'));
    <?php
    }
    else{
    ?>
        tz_portfolio_image('tz_img_hover_server','tz_img_hover_server','','',0);
    <?php } ?>
    $('tz_image_gallery').empty();

    var _tz_portfolio_myGallery = function(name,title,i){
        var myTr = new Element('tr',{});
        myTr.inject($('tz_image_gallery'));
        var myTd = new Element('td',{

            html:'<strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE');?></strong>',
            valign:'top',
            align:'right',
            rowspan:'2',
            styles:{
                background: '#F6F6F6',
                'min-width':'100px'
            }
        });
        myTd.inject(myTr);
        var myTd = new Element('td',{});
        myTd.inject(myTr);
        var myFile = new Element('input',{
            type:'file',
            id:'tz_img_client_'+i,
            name:'tz_img_client[]',
            size:'50px'
        });
        myFile.inject(myTd);

        //row 2
        var myTr2 = new Element('tr',{});
        myTr2.inject($('tz_image_gallery'));
        var myTd = new Element('td',{class: 'input-prepend input-append'
        });
        myTd.inject(myTr2);

        var myField = tz_portfolio_image(myTd,'tz_img_gallery_server[]',name,title,i);

        if(name.length >0){
            var tz_hidden = new Element('input',{
                'type': 'hidden',
                'name': 'tz_image_gallery_current[]',
                'value': name
            }).inject(myTd);
            var tz_checkbox = new Element("input",{
                type: 'checkbox',
                id: 'tz_del_gallery_'+i,
                'name': 'tz_delete_image_gallery[]',
                'value': i,
                style: 'clear: both'
            }).inject(myTd);
            var tz_label = new Element('label',{
                'for': 'tz_del_gallery_'+i,
                style: 'clear: none; margin: 2px 3px;',
                html: '<?php echo JText::_('COM_TZ_PORTFOLIO_CURRENT_IMAGE_DESC');?>'
            }).inject(myTd);
        }

        //row 3
        var myTr3 = new Element('tr',{});
        myTr3.inject($('tz_image_gallery'));
        var myTd = new Element('td',{
            html:'<strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE_TITLE');?></strong>',
            align:'right',
            valign:'top',
            styles:{
                background: '#F6F6F6',
                'min-width':'100px'
            }
        });
        myTd.inject(myTr3);
        var myTd = new Element('td',{});
        myTd.inject(myTr3);

        var myInput = new Element('input',{
            type:'text',
            id:'tz_image_gallery_title_'+i,
            name:'tz_image_gallery_title[]',
            size:'50',
            value:title
        });
        myInput.inject(myTd);

        //row 4
        var myTr4 = new Element('tr',{});
        myTr4.inject($('tz_image_gallery'));
        var myTd = new Element('td',{
        });
        myTd.inject(myTr4);
        var myTd = new Element('td',{
            align:'left',
            styles:{
                //float:'right'
            }
        });
        myTd.inject(myTr4);

        if(tz_count>2){
            var myRemove = new Element('a',{
                class: 'btn',
                style: 'margin-bottom: 15px;',
                name:'tz_remove_image_'+i,
                html:'<i class="icon-cancel"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE');?>',
               events:{
                   click:function(e){
                       e.stop();
                       myTr.dispose();
                       myTr2.dispose();
                       myTr3.dispose();
                       myTr4.dispose();
                   }
               }
            });
            myRemove.inject(myTd);
        }
    };

    var hidden = new Element('input',{
        type:'hidden',
        name:'tz_img_type',
        value:'imagegallery'
    });
    hidden.inject($('tz_image_gallery','after'));
    var k=1;
    var myGallery = new Element('a',{
        class: 'btn',
        style: 'margin-bottom: 7px;',
        html:'<i class="icon-plus"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW');?>',
        events:{
            click: function(e){
                e.stop();
                _tz_portfolio_myGallery('','',k);
                k++;
            }
        }
    });
    myGallery.inject($('tz_image_gallery'));
    var myDiv = (new Element("div", {
        "style": "clear:both;"
    })).inject(myGallery,'after');

     <?php
    if(count($list -> gallery -> images)>1){
        $galleryTitle   = $list -> gallery -> title;
        foreach($list -> gallery -> images as $i => $item):
            $src    = null;
            if($pos = strpos($item,'.')){
                $ext    = substr($item,$pos,strlen($item));
                $src    = JURI::root().str_replace($ext,'_S'.$ext,$item);
            }

    ?>

        _tz_portfolio_myGallery('<?php echo $src;?>','<?php echo $galleryTitle[$i];?>',<?php echo $i;?>);
    <?php
        endforeach;
    }
    else{
        $src    = null;
        if($pos = strpos($list -> gallery -> images,'.')){
            $ext    = substr($list -> gallery -> images,$pos,strlen($list -> gallery -> images));
            $src    = JURI::root().str_replace($ext,'_S'.$ext,$list -> gallery -> images);
        }

    ?>
        _tz_portfolio_myGallery('<?php echo $src;?>','<?php echo $list -> gallery -> title;?>',0);
    <?php

    }

    ?>

    // Attachments

    <?php
        if($this -> listAttach):
    ?>
        var _tz_portfolio_showAttachments = function(){
            <?php
                $i=0;
                foreach($this -> listAttach as $row):
            ?>
                var myTr = new Element('tr',{
                }).inject($('tz_attachments_body'));
                var myTd = new Element('td',{
                    html:'<?php echo $row -> attachfiles;?>'
                }).inject(myTr);
                var myHidden = new Element('input',{
                    type:'hidden',
                    name:'tz_attachments_hidden_file[]',
                    value:'<?php echo $row -> attachfiles;?>'
                }).inject(myTd);
                var myHidden = new Element('input',{
                    type:'hidden',
                    name:'tz_attachments_hidden_old[]',
                    value:'<?php echo htmlentities($row -> attachold,ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,"UTF-8",false);?>'
                }).inject(myTd);
				var myTd = new Element('td',{
                    html:'<?php echo !empty($row -> attachtitle)? htmlentities($row -> attachtitle,ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,"UTF-8",false): htmlentities($row -> attachold, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED, "UTF-8", false);?>'
                }).inject(myTr);
                var myHidden = new Element('input',{
                    type:'hidden',
                    name:'tz_attachments_hidden_title[]',
                    value:'<?php if($row -> attachfiles != $row -> attachtitle) echo htmlentities($row -> attachtitle,ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,"UTF-8",false) ;?>'
                }).inject(myTd);
				var myTd = new Element('td',{
                }).inject(myTr);
                var myInput = new Element('button',{
                    type:'button',
                    class: 'btn',
                    id:'tz_attachments_delete_<?php echo $i;?>',
                    html:'<i class="icon-remove"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BUTTON_DELETE');?>'
                }).inject(myTd);
<!--                $('tz_attachments_delete_--><?php //echo $i;?><!--').addEvent('click',function(){-->
                jQuery('#tz_attachments_delete_<?php echo $i;?>').bind('click',function(){
                    var jSonRequest = new Request.JSON({url: "index.php?option=com_tz_portfolio&task=article.deleteAttachment",
                        onComplete: function(){
                            window.location.reload();
                        },
                        data:{
                            json: JSON.encode({
                                'attachmentsFile':'<?php echo $row -> attachfiles;?>',
                                'id':$('contentid').value,
                                'attachmentsTitle':'<?php echo $row -> attachtitle;?>'
                            })
                        }
                    }).send();
                });
            <?php
                    $i++;
                endforeach;
            ?>
        };
    _tz_portfolio_showAttachments();
    <?php
        endif;
    ?>


    var _tz_portfolio_addAttachments = function(){
//        $('tz_attachments').empty();
        var myTable = new Element('table',{
           id:'tz_attachments_table',
            styles:{
                width:'100%'
            }
        }).inject($('tz_attachments'));

        var myTr0 = new Element('tr',{
        }).inject($('tz_attachments_table'));
        var myTd = new Element('td',{
            'colspan': 2
        }).inject(myTr0);

        var myButton = new Element('button',{
            class: 'btn',
            html:'<i class="icon-plus"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_ATTACHMENT_FIELD');?>'
        }).inject(myTd);
        var myI = new Element('i',{
           html:'<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_ATTACHMENT_FIELD_DESC');?>'
        }).inject(myTd);

        myButton.addEvent('click',function(e){
            e.stop();
            var myTr1 = new Element('tr',{
                styles:{
                    'margin-top':'10px'
                }
            }).inject($('tz_attachments_table'));

            var myTd = new Element('td',{
                html:'<?php echo JText::_('COM_TZ_PORTFOLIO_FILED_ATTACHMENTS');?>',
                align:'right',
                valign:'center',
                styles:{
                    background: '#F6F6F6',
                    'min-width':'100px'
                }
            }).inject(myTr1);
            var myTd = new Element('td',{
                styles:{

                }
            }).inject(myTr1);

            var myFile = new Element('input',{
               type:'file',
                name:'tz_attachments_file[]',
                size:'60%'
            }).inject(myTd);

            var myTr2 = new Element('tr',{
            }).inject($('tz_attachments_table'));
            var myTd = new Element('td',{
                align:'right',
                valign:'center',
                html:'<?php echo JText::_('COM_TZ_PORTFOLIO_FORM_LINK_TITLE');?>',
                styles:{
                    background: '#F6F6F6',
                    'min-width':'100px'
                }
            }).inject(myTr2);
            var myTd = new Element('td',{

            }).inject(myTr2);

            var myInput = new Element('input',{
                type:'text',
                name:'tz_attachments_title[]',
                value:'',
                size:'70%'
            }).inject(myTd);
            var myTr3 = new Element('tr',{
            }).inject($('tz_attachments_table'));

            var myTd = new Element('td',{
               colspan:'2',
                align:'right'
            }).inject(myTr3);
            var myRemove = new Element('a',{
                class: 'btn',
                html:'<i class="icon-cancel"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE');?>',
                events:{
                    click: function(e){
                        e.stop();
                        myTr1.dispose();
                        myTr2.dispose();
                        myTr3.dispose();
                    }
                }
            }).inject(myTd)


        });
    }
        _tz_portfolio_addAttachments();

});
</script>

<?php // If the joomla's version is more than or equal to 3.0?>
<?php if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE):?>
<div class="row-fluid">
<?php endif;?>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&layout=edit&id='.(int) $this->item->id); ?>"
      method="post"
      name="adminForm"
      id="item-form"
      class="form-validate"
      enctype="multipart/form-data">
    <div class="span8 form-horizontal">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_CONTENT_ARTICLE_DETAILS');?></a></li>
            <?php if($assoc):?>
            <li><a href="#associations" data-toggle="tab"><?php echo JText::_('Associations');?></a></li>
            <?php endif;?>
            <li><a href="#permissions" data-toggle="tab"><?php echo JText::_('COM_CONTENT_FIELDSET_RULES');?></a></li>

        </ul>
        <div class="tab-content">
            <!-- Begin Tabs -->
            <div class="tab-pane active" id="general">
                <div class="row-fluid">
                        <div class="span6">
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
                                    <label><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_TAGS');?></label>
                                </div>
                                <div class="controls">
                                    <input type="text" name="tz_tags[]" class="suggest" data-provide="typeahead"/>
                                    <span><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_TAGS_DESC');?></span>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('state'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('state'); ?>
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
                                    <?php echo $this->form->getLabel('catid'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('catid'); ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <label><?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP');?></label>
                                </div>
                                <div class="controls">
                                    <div id="tz_fields_group"><?php echo $this -> listsGroup;?></div>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <label title="<?php echo JText::_('COM_TZ_PORTFOLIO_TYPE_OF_MEDIA')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_TYPE_OF_MEDIA_DESC');?>"
                                       class="hasTip required"
                                       for="jform_type_of_media"
                                       id="jform_type_of_media-lbl">
                                        <?php echo JText::_('COM_TZ_PORTFOLIO_TYPE_OF_MEDIA')?>
                                        <span class="star"> *</span>
                                   </label>
                                </div>
                                <div class="controls">
                                    <select id="jform_type_of_media" name="type_of_media" required="required" class="required">
                                        <option value=""><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_MEDIA_TYPE');?></option>
                                        <option value="none" <?php if($type == 'none') echo ' selected="selected"';?>><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_NONE_MEDIA');?></option>
                                        <option value="image"<?php if($type == 'image') echo ' selected="selected"';?>><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_IMAGE');?></option>
                                        <option value="imageGallery"<?php if($type == 'imagegallery') echo ' selected="selected"';?>>
                                            <?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_IMAGE_GALLERY');?>
                                        </option>
                                        <option value="video"<?php if($type == 'video') echo ' selected="selected"';?>>
                                            <?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_VIDEO');?>
                                        </option>
                                        <option value="audio"<?php if($type == 'audio') echo ' selected="selected"';?>>
                                            <?php echo JText::_('COM_TZ_PORTFOLIO_AUDIO');?>
                                        </option>
                                        <option value="quote"<?php if($type == 'quote') echo ' selected="selected"';?>>
                                            <?php echo JText::_('COM_TZ_PORTFOLIO_QUOTE');?>
                                        </option>
                                        <option value="link"<?php if($type == 'link') echo ' selected="selected"';?>>
                                            <?php echo JText::_('COM_TZ_PORTFOLIO_LINK');?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="control-label">
                                    <?php echo $this->form->getLabel('featured'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('featured'); ?>
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
                                    <?php echo $this->form->getLabel('template_id'); ?>
                                </div>
                                <div class="controls">
                                    <?php echo $this->form->getInput('template_id'); ?>
                                </div>
                            </div>
                        </div>


                </div>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tz_content" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_CONTENT');?></a></li>
                    <li><a href="#tztabsImage" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_IMAGE');?></a></li>
                    <li><a href="#tztabsGallery" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_IMAGE_GALLERY');?></a></li>
                    <li><a href="#tztabsMedia" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_MEDIA');?></a></li>
                    <li><a href="#tztabsAudio" data-toggle="tab" class="hasTip"
                       title="<?php echo JText::_('COM_TZ_PORTFOLIO_AUDIO')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_TAB_AUDIO_DESC')?>"><?php echo JText::_('COM_TZ_PORTFOLIO_AUDIO');?></a></li>
                    <li><a href="#tztabsQuote" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_QUOTE');?></a></li>
                    <li><a href="#tztabsLink" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_LINK');?></a></li>
                    <li><a href="#tztabsFields" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_FIELDS');?></a></li>
                    <li><a href="#tztabsAttachment" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_ATTACHMENTS');?></a></li>
                    <?php echo $this -> loadTemplate('plugin_title_tab');?>
                </ul>
                <!-- Begin Content -->
                 <div class="tab-content">
                     <!-- Begin Tabs -->
                    <div class="tab-pane active" id="tz_content">
                        <?php echo $this->form->getInput('articletext'); ?>
                    </div>
                    <div class="tab-pane" id="tztabsImage">
                        <div class="control-group">
                            <a class="modal btn hasTooltip" href="index.php?option=com_tz_portfolio&view=config&layout=image&tmpl=component"
                               rel="{handler: 'iframe', size: {x: 500, y: 350}, onClose: function() {}}"
                                title="<?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_GLOBAL_CONFIG_DESC');?>">
                                <span class="icon-options"></span><?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_GLOBAL_CONFIG');?>
                                <small style="display: block; font-style: italic; color: #777;"><?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_GLOBAL_CONFIG_DESC');?></small>
                            </a>
                        </div>
                        <div id="tz_images">
                            <table class="admintable" style="width: 100%">
                                <tr>
                                    <td style="background: #F6F6F6; min-width:100px;" align="right" rowspan="2" valign="top">
                                        <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE');?></strong>
                                    </td>
                                    <td>
                                        <input type="file" name="tz_img" id="tz_img" value="">
                                    </td>
                                </tr>
                                <tr class="input-prepend input-append">
                                    <td id="tz_img_server">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background: #F6F6F6; min-width:100px;" align="right">
                                        <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE_TITLE');?></strong>
                                    </td>
                                    <td>
                                        <input type="text" name="tz_image_title" id="tz_image_title"
                                               value="<?php echo stripslashes($list -> imagetitle);?>"
                                               />
                                        <input type="hidden" name="tz_img_image" value="image" style="margin-bottom: 10px;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background: #F6F6F6; min-width:100px;" align="right" rowspan="2" valign="top">
                                        <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FROM_IMAGE_HOVER');?></strong>
                                    </td>
                                    <td>
                                        <input type="file" name="tz_img_hover" id="tz_img_hover" value=""/>
                                    </td>
                                </tr>
                                <tr  class="input-prepend input-append">
                                    <td id="tz_img_hover_server">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="tztabsGallery">
                        <div class="control-group">
                            <a class="modal btn hasTooltip" href="index.php?option=com_tz_portfolio&view=config&layout=slider&tmpl=component"
                               rel="{handler: 'iframe', size: {x: 500, y: 350}, onClose: function() {}}"
                               title="<?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SLIDER_SIZE_GLOBAL_CONFIG_DESC');?>">
                                <span class="icon-options"></span><?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SLIDER_SIZE_GLOBAL_CONFIG');?>
                                <small style="display: block; font-style: italic; color: #777;"><?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SLIDER_SIZE_GLOBAL_CONFIG_DESC');?></small>
                            </a>
                        </div>
                        <table  id="tz_image_gallery">
                            <tr>
                                <td id="tz_img_gallery"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="tab-pane" id="tztabsMedia">
                        <div class="control-group">
                            <a class="modal btn hasTooltip" href="index.php?option=com_tz_portfolio&view=config&layout=image&tmpl=component"
                               rel="{handler: 'iframe', size: {x: 500, y: 350}, onClose: function() {}}"
                               title="<?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_GLOBAL_CONFIG_DESC');?>">
                                <span class="icon-options"></span><?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_GLOBAL_CONFIG');?>
                                <small style="display: block; font-style: italic; color: #777;"><?php echo JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_GLOBAL_CONFIG_DESC');?></small>
                            </a>
                        </div>
                        <div id="tz_media">
                            <table>
                                <tr>
                                    <td style="background: #F6F6F6; min-width:100px;" align="right" valign="top">
                                        <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_MEDIA_TYPE')?></strong>
                                    </td>
                                    <td>
                                        <select name="tz_media_type" id="tz_media_type">
                                            <option value="default"<?php echo ($list -> video -> type =='default')?' selected="selected"':''?>><?php echo JText::_('COM_TZ_PORTFOLIO_DEFAULT');?></option>
                                            <option value="youtube"<?php echo ($list -> video -> type =='youtube')?' selected="selected"':''?>>Youtube</option>
                                            <option value="vimeo"<?php echo ($list -> video -> type =='vimeo')?' selected="selected"':''?>>Vimeo</option>

                                        </select>
                                    </td>
                                </tr>
                                <tr id="tz_media_code_outer">
                                    <td style="background: #F6F6F6; min-width:100px;" align="right" valign="top">
                                        <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_MEDIA_SOURCE')?></strong>
                                    </td>
                                    <td id="tz_media_code">
                                        <?php echo JText::_('COM_TZ_PORTFOLIO_PASTE_HTML_CODE');?><br/>
                                        <textarea rows="10" cols="20" name="tz_media_code">
                                            <?php echo $list -> video -> code;?>
                                        </textarea>
                                    </td>
                                </tr>
                                <tr id="tz_thumb">
                                    <td id="tz_thumb_inner" valign="top" align="right" style="background: #F6F6F6; min-width:100px;">
                                    </td>
                                    <td id="tz_thumb_preview">

                                    </td>
                                </tr>
                                <tr>
                                    <td style="background: #F6F6F6; min-width:100px;" align="right" valign="top">
                                        <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_MEDIA_TITLE');?></strong>
                                    </td>
                                    <td id="tz_media_title">
                                        <input type="text"
                                               name="tz_media_title"
                                               value="<?php echo trim($list -> video -> title);?>">
                                    </td>

                                </tr>

                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="tztabsFields">
                        <div id="tz_fields"></div>
                    </div>
                    <div class="tab-pane" id="tztabsAttachment">
                        <div id="tz_attachments">
                            <?php
                            if($this -> listAttach):
                            ?>
                            <table class="table table-striped" id="tz_attachments_show">
                                <thead style="font-weight: bold;">
                                    <tr>
                                        <td><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_FILENAME');?></td>
                                        <td><?php echo JText::_('COM_TZ_PORTFOLIO_ATTACH_TITLE');?></td>
                                        <td width="15%"><?php echo JText::_('JSTATUS');?></td>
                                    </tr>
                                </thead>
                                <tbody id="tz_attachments_body"></tbody>
                            </table>
                            <?php endif; ?>

                            <table id="tz_attachments_table"></table>
                        </div>
                    </div>

                     <?php echo $this -> loadTemplate('quote');?>

                     <?php echo $this -> loadTemplate('link');?>

                     <?php echo $this -> loadTemplate('audio');?>

                     <?php echo $this -> loadTemplate('plugin_content_tab');?>
                     <!-- End Tabs -->
                 </div>
                <!-- End Content -->
            </div>
            <?php if ($assoc) : ?>
            <div class="tab-pane" id="associations">
                <?php echo $this->loadTemplate('associations'); ?>

            </div>
            <?php endif; ?>
            <div class="tab-pane" id="permissions">
                <?php echo $this->form->getInput('rules'); ?>
            </div>
            <!-- End Tabs -->
        </div>

	</div>
    <div class="span4 form-vertical">
        <?php echo JHtml::_('bootstrap.startAccordion', 'menuOptions', array('active' => 'collapse0'));?>

            <?php // Do not show the publishing options if the edit form is configured not to. ?>
            <?php  if ($params['show_publishing_options'] || ( $params['show_publishing_options'] = '' && !empty($editoroptions)) ): ?>
                <?php echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_('COM_CONTENT_FIELDSET_PUBLISHING'), 'collapse0'); ?>
                <fieldset>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created_by_alias'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created_by_alias'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('created'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('publish_up'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('publish_up'); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('publish_down'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('publish_down'); ?></div>
                    </div>

                        <?php if ($this->item->modified_by) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
                            </div>

                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->item->version) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('version'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('version'); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->item->hits) : ?>
                            <div class="control-group">
                                <div class="control-label"><?php echo $this->form->getLabel('hits'); ?></div>
                                <div class="controls"><?php echo $this->form->getInput('hits'); ?></div>
                            </div>
                        <?php endif; ?>
                </fieldset>
                <?php echo JHtml::_('bootstrap.endSlide');?>
            <?php  endif; ?>

            <?php  $fieldSets = $this->form->getFieldsets('attribs'); ?>
            <?php $i = 1;?>
            <?php foreach ($fieldSets as $name => $fieldSet) : ?>
                <?php // If the parameter says to show the article options or if the parameters have never been set, we will
                      // show the article options. ?>

                <?php if ($params['show_article_options'] || (( $params['show_article_options'] == '' && !empty($editoroptions) ))): ?>
                    <?php // Go through all the fieldsets except the configuration and basic-limited, which are
                          // handled separately below. ?>


                    <?php if ($name != 'editorConfig' && $name != 'basic-limited') :?>
                        <?php //echo JHtml::_('sliders.panel', JText::_($fieldSet->label), $name.'-options'); ?>
                        <?php echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_($fieldSet->label), 'collapse' . $i++); ?>
                        <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                            <p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
                        <?php endif; ?>
                        <fieldset>
                            <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                                <div class="control-group">
                                    <?php if($field -> name != 'jform[attribs][tz_fieldsid_content]'):?>
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="controls"><?php echo $field->input; ?></div>
                                    <?php else: ?>
                                        <div class="control-label"><?php echo $field->label; ?></div>
                                        <div class="controls">
                                            <div id="tz_fieldsid_content"></div>
                                        </div>
                                    <?php endif;?>
                                </div>
                            <?php endforeach; ?>
                        </fieldset>
                    <?php echo JHtml::_('bootstrap.endSlide');?>
                    <?php endif ?>
                    <?php // If we are not showing the options we need to use the hidden fields so the values are not lost.  ?>
                <?php  elseif ($name == 'basic-limited'): ?>
                    <?php foreach ($this->form->getFieldset('basic-limited') as $field) : ?>
                        <?php  echo $field->input; ?>
                    <?php endforeach; ?>

                <?php endif; ?>
            <?php endforeach; ?>
            <?php // We need to make a separate space for the configuration
                  // so that those fields always show to those wih permissions ?>
            <?php if ( $this->canDo->get('core.admin')   ):  ?>
                <?php echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_('COM_CONTENT_SLIDER_EDITOR_CONFIG'), 'configure-sliders' ); ?>
                    <fieldset>
                        <?php $i = 0;?>
                        <?php foreach ($this->form->getFieldset('editorConfig') as $field) : ?>

                            <div class="control-group">
                                <div class="control-label"><?php echo $field->label; ?></div>
                                <div class="controls"><?php echo $field->input; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                <?php echo JHtml::_('bootstrap.endSlide');?>
            <?php endif ?>

            <?php // The url and images fields only show if the configuration is set to allow them.  ?>
		    <?php // This is for legacy reasons. ?>

            <?php if ($params['show_urls_images_backend']): ?>
                <?php echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_('COM_TZ_PORTFOLIO_FIELDSET_URLS_AND_IMAGES'), 'urls_and_images-options' ); ?>
				<fieldset class="panelform">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('images'); ?></div>
					    <div class="controls"><?php echo $this->form->getInput('images'); ?></div>
                    </div>

					<?php foreach($this->form->getGroup('images') as $field): ?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php if (!$field->hidden): ?>
                                    <?php echo $field->label; ?>
                                <?php endif; ?>
                            </div>
							<div class="controls"><?php echo $field->input; ?></div>
						</div>
					<?php endforeach; ?>
						<?php foreach($this->form->getGroup('urls') as $field): ?>
						 <div class="control-group">
                            <div class="control-label">
                                <?php if (!$field->hidden): ?>
                                    <?php echo $field->label; ?>
                                <?php endif; ?>
                            </div>
							<div class="controls"><?php echo $field->input; ?></div>
						</div>
					<?php endforeach; ?>
				</fieldset>
                <?php echo JHtml::_('bootstrap.endSlide');?>
		    <?php endif; ?>

            <?php echo JHtml::_('bootstrap.addSlide', 'menuOptions', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'meta-options' ); ?>
                <fieldset class="panelform">
                    <?php echo $this->loadTemplate('metadata'); ?>
                </fieldset>
            <?php echo JHtml::_('bootstrap.endSlide');?>
        <?php echo JHtml::_('bootstrap.endAccordion');?>

    </div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
        <input type="hidden" name="contentid" id="contentid" value="<?php echo JRequest::getCmd('id');?>">
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE):?>
</div>
<?php endif;?>