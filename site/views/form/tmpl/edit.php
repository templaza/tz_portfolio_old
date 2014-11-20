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

$tmpl   = JRequest::getString('tmpl');
if($tmpl){
    JHtml::_('bootstrap.framework');
    JHtml::_('jquery.framework');
}
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('stylesheet', 'jui/chosen.css', false, true);

$doc    = JFactory::getDocument();
$lang   = JFactory::getLanguage();

$app    = JFactory::getApplication();
$template   = $app -> getTemplate();

$lang -> load('com_tz_portfolio',JPATH_ADMINISTRATOR);
// Create shortcut to parameters.
$params = $this->state->get('params');

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);
if (!$editoroptions):
    $params->show_urls_images_frontend = '0';
endif;
$list       = $this -> listEdit;
$type   = null;
if($list){
    $type   = $list -> type;
}

$doc -> addCustomTag('<script src="'.JUri::base(true).'/administrator/components/com_tz_portfolio/js/tz-chosen.js"'.
    ' type="text/javascript"></script>');

if(!$this -> tagsSuggest){
    $this -> tagsSuggest    = 'null';
}
$doc -> addScriptDeclaration('
    jQuery(document).ready(function(){
        jQuery(".suggest").tzChosen({ source: '.$this -> tagsSuggest.', sourceEdit: '.$this -> listsTags.',keys: ["\,","/"]});
    })
');
?>

<script type="text/javascript">
    Joomla.submitbutton = function(task) {
        if (task == 'article.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
            <?php echo $this->form->getField('articletext')->save(); ?>
            Joomla.submitform(task);
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
        }
    }
</script>
<div class="TzEdit item-page<?php echo $this->pageclass_sfx; ?>">
<div class="TzEditInner">
<?php if ($params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($params->get('page_heading')); ?>
    </h1>
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio&a_id='.(int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" enctype="multipart/form-data">
<div class="btn-toolbar">
    <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
        <i class="icon-ok"></i><?php echo JText::_('JSAVE') ?>
    </button>
    <button type="button" class="btn" onclick="Joomla.submitbutton('article.cancel')">
        <i class="icon-remove"></i><?php echo JText::_('JCANCEL') ?>
    </button>
</div>
<fieldset class="TzEditor">
<legend><?php echo JText::_('JEDITOR'); ?></legend>

<div class="row-fluid">
    <div class="span12">
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
        </div>

        <?php if (is_null($this->item->id)):?>
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>


<ul class="nav nav-tabs">
    <li class="active"><a href="#tz_content" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_CONTENT');?></a></li>
    <li><a href="#tztabsImage" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_IMAGE');?></a></li>
    <li><a href="#tztabsGallery" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_IMAGE_GALLERY');?></a></li>
    <li><a href="#tztabsMedia" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_MEDIA');?></a></li>
    <li><a href="#tztabsFields" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_FIELDS');?></a></li>
    <li><a href="#tztabsAttachment" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_TAB_ATTACHMENTS');?></a></li>
    <li><a href="#tztabsQuote" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_QUOTE');?></a></li>
    <li><a href="#tztabsLink" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_LINK');?></a></li>
    <li><a href="#tztabsAudio" data-toggle="tab" class="hasTooltip"
           title="<?php echo JText::_('COM_TZ_PORTFOLIO_AUDIO')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_TAB_AUDIO_DESC')?>"><?php echo JText::_('COM_TZ_PORTFOLIO_AUDIO');?></a></li>
</ul>
<div class="row-fluid">
    <div class="span12">
        <!-- Begin Content -->
        <div class="tab-content">
            <!-- Begin Tabs -->
            <div class="tab-pane active" id="tz_content">
                <?php echo $this->form->getInput('articletext'); ?>
            </div>
            <div class="tab-pane" id="tztabsImage">
                <div id="tz_images">
                    <div class="control-group">
                        <div class="control-label">
                            <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE');?></strong>
                        </div>
                        <div class="controls">
                            <input type="file" name="tz_img" id="tz_img" value="">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">

                        </div>
                        <div class="controls">
                            <div id="tz_img_server" class="input-prepend input-append"></div>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE_TITLE');?></strong>
                        </div>
                        <div class="controls">
                            <input type="text" name="tz_image_title" id="tz_image_title"
                                   value="<?php //echo $list -> imagetitle;?>">
                            <input type="hidden" name="tz_img_image" value="image">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FROM_IMAGE_HOVER');?></strong>
                        </div>
                        <div class="controls">
                            <input type="file" name="tz_img_hover" id="tz_img_hover" value="">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">

                        </div>
                        <div id="tz_img_hover_server" class="controls input-prepend input-append">
                        </div>
                    </div>
                    <!--                                    <table class="admintable" style="width: 100%">-->
                    <!--                                        <tr>-->
                    <!--                                            <td style="background: #F6F6F6; min-width:100px;" align="right" rowspan="2" valign="top">-->
                    <!--                                                <strong>--><?php //echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE');?><!--</strong>-->
                    <!--                                            </td>-->
                    <!--                                            <td>-->
                    <!--                                                <input type="file" name="tz_img" id="tz_img" value="">-->
                    <!--                                            </td>-->
                    <!--                                        </tr>-->
                    <!--                                        <tr class="input-prepend input-append">-->
                    <!--                                            <td id="tz_img_server">-->
                    <!--                                            </td>-->
                    <!--                                        </tr>-->
                    <!--                                        <tr>-->
                    <!--                                            <td style="background: #F6F6F6; min-width:100px;" align="right">-->
                    <!--                                                <strong>--><?php //echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE_TITLE');?><!--</strong>-->
                    <!--                                            </td>-->
                    <!--                                            <td>-->
                    <!--                                                <input type="text" name="tz_image_title" id="tz_image_title"-->
                    <!--                                                       value="--><?php ////echo $list -> imagetitle;?><!--">-->
                    <!--                                                <input type="hidden" name="tz_img_image" value="image">-->
                    <!--                                            </td>-->
                    <!--                                        </tr>-->
                    <!--                                        <tr>-->
                    <!--                                            <td style="background: #F6F6F6; min-width:100px;" align="right" rowspan="2" valign="top">-->
                    <!--                                                <strong>--><?php //echo JText::_('COM_TZ_PORTFOLIO_FROM_IMAGE_HOVER');?><!--</strong>-->
                    <!--                                            </td>-->
                    <!--                                            <td>-->
                    <!--                                                <input type="file" name="tz_img_hover" id="tz_img_hover" value="">-->
                    <!--                                            </td>-->
                    <!--                                        </tr>-->
                    <!--                                        <tr>-->
                    <!--                                            <td id="tz_img_hover_server" class="input-prepend input-append">-->
                    <!--                                            </td>-->
                    <!--                                        </tr>-->
                    <!--                                    </table>-->
                </div>
            </div>

            <div class="tab-pane" id="tztabsGallery">
                <div id="tz_image_gallery">
                    <div class="control-group">
                        <div id="tz_img_gallery" class="controls"></div>
                    </div>
                </div>
                <!--                                    <table  id="tz_image_gallery">-->
                <!--                                        <tr>-->
                <!--                                            <td id="tz_img_gallery"></td>-->
                <!--                                        </tr>-->
                <!--                                    </table>-->
            </div>

            <div class="tab-pane" id="tztabsMedia">
                <div id="tz_media">
                    <div class="control-group">
                        <div class="control-label">
                            <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_MEDIA_TYPE')?></strong>
                        </div>
                        <div class="controls">
                            <select name="tz_media_type" id="tz_media_type">
                                <option value="default"<?php echo ($list -> video -> type =='default')?' selected="selected"':''?>>Default</option>
                                <option value="youtube"<?php echo ($list -> video -> type =='youtube')?' selected="selected"':''?>>Youtube</option>
                                <option value="vimeo"<?php echo ($list -> video -> type =='vimeo')?' selected="selected"':''?>>Vimeo</option>

                            </select>
                        </div>
                    </div>
                    <div id="tz_media_code_outer" class="control-group">
                        <div class="control-label">
                            <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_MEDIA_SOURCE')?></strong>
                        </div>
                        <div id="tz_media_code" class="controls">
                            <?php echo JText::_('COM_TZ_PORTFOLIO_PASTE_HTML_CODE');?><br/>
                            <textarea rows="10" cols="20" name="tz_media_code">
                                <?php echo $list -> video -> code;?>
                            </textarea>
                        </div>
                    </div>
                    <div id="tz_thumb" class="control-group">
                        <div id="tz_thumb_inner" class="control-label">
                        </div>
                        <div id="tz_thumb_preview" class="controls">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="control-label">
                            <strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_MEDIA_TITLE');?></strong>
                        </div>
                        <div id="tz_media_title" class="controls">
                            <input type="text"
                                   name="tz_media_title"
                                   value="<?php echo trim($list -> video -> title);?>">
                        </div>
                    </div>


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

                    <!--                                    <table id="tz_attachments_table"></table>-->
                    <div id="tz_attachments_table"></div>
                </div>
            </div>
            <!-- End Begin Tabs -->

            <?php echo $this -> loadTemplate('quote');?>

            <?php echo $this -> loadTemplate('link');?>

            <?php echo $this -> loadTemplate('audio');?>
        </div>
        <!-- End Begin Content -->
    </div>
</div>

</fieldset>
<?php if ($params->get('show_urls_images_frontend')  ): ?>
    <fieldset>
        <legend><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS'); ?></legend>
        <div class="formelm">
            <?php echo $this->form->getLabel('image_intro', 'images'); ?>
            <?php echo $this->form->getInput('image_intro', 'images'); ?>
        </div>
        <div style="clear:both"></div>
        <div class="formelm">
            <?php echo $this->form->getLabel('image_intro_alt', 'images'); ?>
            <?php echo $this->form->getInput('image_intro_alt', 'images'); ?>
        </div>
        <div class="formelm">
            <?php echo $this->form->getLabel('image_intro_caption', 'images'); ?>
            <?php echo $this->form->getInput('image_intro_caption', 'images'); ?>
        </div>
        <div class="formelm">
            <?php echo $this->form->getLabel('float_intro', 'images'); ?>
            <?php echo $this->form->getInput('float_intro', 'images'); ?>
        </div>

        <div class="formelm">
            <?php echo $this->form->getLabel('image_fulltext', 'images'); ?>
            <?php echo $this->form->getInput('image_fulltext', 'images'); ?>
        </div>
        <div style="clear:both"></div>
        <div class="formelm">
            <?php echo $this->form->getLabel('image_fulltext_alt', 'images'); ?>
            <?php echo $this->form->getInput('image_fulltext_alt', 'images'); ?>
        </div>
        <div class="formelm">
            <?php echo $this->form->getLabel('image_fulltext_caption', 'images'); ?>
            <?php echo $this->form->getInput('image_fulltext_caption', 'images'); ?>
        </div>
        <div class="formelm">
            <?php echo $this->form->getLabel('float_fulltext', 'images'); ?>
            <?php echo $this->form->getInput('float_fulltext', 'images'); ?>
        </div>

        <div  class="formelm">
            <?php echo $this->form->getLabel('urla', 'urls'); ?>
            <?php echo $this->form->getInput('urla', 'urls'); ?>
        </div>
        <div  class="formelm">
            <?php echo $this->form->getLabel('urlatext', 'urls'); ?>
            <?php echo $this->form->getInput('urlatext', 'urls'); ?>
        </div>
        <?php echo $this->form->getInput('targeta', 'urls'); ?>
        <div  class="formelm">
            <?php echo $this->form->getLabel('urlb', 'urls'); ?>
            <?php echo $this->form->getInput('urlb', 'urls'); ?>
        </div>
        <div  class="formelm">
            <?php echo $this->form->getLabel('urlbtext', 'urls'); ?>
            <?php echo $this->form->getInput('urlbtext', 'urls'); ?>
        </div>
        <?php echo $this->form->getInput('targetb', 'urls'); ?>
        <div  class="formelm">
            <?php echo $this->form->getLabel('urlc', 'urls'); ?>
            <?php echo $this->form->getInput('urlc', 'urls'); ?>
        </div>
        <div  class="formelm">
            <?php echo $this->form->getLabel('urlctext', 'urls'); ?>
            <?php echo $this->form->getInput('urlctext', 'urls'); ?>
        </div>
        <?php echo $this->form->getInput('targetc', 'urls'); ?>
    </fieldset>
<?php endif; ?>

<fieldset>
<legend><?php echo JText::_('COM_CONTENT_PUBLISHING'); ?></legend>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
    <div class="controls">
        <?php if($this->params->get('enable_category', 0) == 1) : ?>
            <span class="category">
                            <?php echo $this->category_title; ?>
                        </span>
        <?php else : ?>
            <?php echo $this->form->getInput('catid', null, $this->item->catid); ?>
        <?php endif;?>
    </div>
</div>
<script type="text/javascript">
// extra fields
window.addEvent('domready', function() {

    var tz_portfolio_extraFields = function(){

        var jSonRequest = new Request.JSON({url: "index.php?option=com_tz_portfolio&task=form.listsfields",
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

            },
            data: {
                json: JSON.encode({
                    'groupid':$('groupid').value,
                    'id':'<?php echo $this -> item -> id;?>',
                    'catid':$('jform_catid').value
                })
            }
        }).send();

    }

    var jSonRequest2 = new Request.JSON({url: "index.php?option=com_tz_portfolio&task=form.selectgroup",
        onComplete: function(item){
            tz_portfolio_extraFields();
        },
        data:{
            json2: JSON.encode({
                'catid':$('jform_catid').value,
                'id':'<?php echo $this -> item -> id;?>',
                'groupid':$('groupid').value
            })
        }
    }).send();

    $('jform_catid').addEvent('change',function(e){
        e.stop();

        var jSonRequest2 = new Request.JSON({url: "index.php?option=com_tz_portfolio&task=form.selectgroup",
            onComplete: function(){
                tz_portfolio_extraFields();
            },
            data:{
                json2: JSON.encode({
                    'catid':$('jform_catid').value,
                    'id':'<?php echo $this -> item -> id;?>',
                    'groupid':$('groupid').value
                })
            }
        }).send();
    });

    var tz_portfolio_groupChange = function(){
        $('groupid').addEvent('change',function(e){
            e.stop();
            tz_portfolio_extraFields();
        });
    }
    tz_portfolio_groupChange();

});

//Media
window.addEvent('domready',function(){
    function tz_thumb(){
        <?php //if(!empty($list -> video -> thumb)):?>
        if($('tz_thumb'))
            $('tz_thumb').dispose();
        <?php //endif;?>
        var myTr = new Element('div', {id: 'tz_thumb',class:'control-group'})
            .inject($('tz_media_code_outer'),'after');
        var myThumbInner    = new Element('div',{
            id: 'tz_thumb_inner',
            class:'control-label'
        }).inject(myTr);
        var myElement  = new Element('strong');
        myElement.appendText('<?php echo JText::_('COM_TZ_PORTFOLIO_THUMBNAIL');?>');
        myThumbInner.adopt(myElement);

        var myThumbPre  = new Element('div',{
            id: 'tz_thumb_preview',
            class: 'controls'
        }).inject(myThumbInner,'after');

//                            var tz_e = location.href.match(/^(.+)\/index\.php.*/i)[1];

        var div = new Element('div',{class: 'input-prepend input-append'});
        div.inject($('tz_thumb_preview'));
        var icon = new Element('div',{
            class: 'add-on',
            html: '<\i class="icon-eye-open"></i>'
        }).inject(div);
        var tz_a = new Element('input',{
            type:"text",
            class:"inputbox image-select",
            name:"tz_thumb",
            "id":"image-thumb",
            readonly:'true'
        });
        tz_a.inject(div);
        var tz_d = "image-thumb",
            tz_b = (new Element("button", {
                type: "button",
                class: 'btn',
                "id": "tz_thumb_button"
            })).set('html', '<i class="icon-file"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BROWSE_SERVER');?>').inject(div),
            tz_f = (new Element("button", {
                "name": "tz_thumb_cancel",
                "id"  : "tz_thumb_cancel",
                class: 'btn',
                html:'<i class="icon-refresh"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_RESET');?>'
            })).inject(tz_b,'after'),
            tz_g = (new Element("div", {
                "class": "tz-image-preview",
                "style": "clear:both;"
            })).inject($('tz_thumb_preview'));

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
            src: '<?php echo $src;?>',
            style: 'cursor:pointer; max-width: 200px;'
        }).inject(tz_g);
        tz_img.addEvent('click',function(){
            SqueezeBox.fromElement(this, {
                handler: "image",
                url: '<?php echo $src2;?>'
            });
        });
        var tz_checkbox = new Element('input',{
            type: 'checkbox',
            style:'clear:both;',
            name: 'tz_thumb_del',
            id: 'tz_thumb_del'
        });
        $('tz_thumb_preview').adopt(tz_checkbox);
        var tz_label = new Element('label',{
            'for': 'tz_thumb_del',
            style: 'clear: none; margin: 2px 3px;',
            html: '<?php echo JText::_('COM_TZ_PORTFOLIO_CURRENT_IMAGE_DESC');?>'
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
                        src:'http://img.youtube.com/vi/'+ myCode.value+'/hqdefault.jpg'
                    }).inject(video);
                    iframe.addEvent('click',function(){
                        SqueezeBox.fromElement(this, {
                            handler: "image",
                            url: 'http://img.youtube.com/vi/'+ myCode.value+'/hqdefault.jpg'
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
                src: '<?php echo $src;?>'
            }).inject(video);
            iframe.addEvent('click',function(){
                SqueezeBox.fromElement(this, {
                    handler: "image",
                    url: '<?php echo $src2;?>'
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
    $('tz_media_type').addEvent('change',function(){
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
                            src:'http://img.youtube.com/vi/'+ myCode.value+'/hqdefault.jpg'
                        }).inject(video);
                        iframe.addEvent('click',function(){
                            SqueezeBox.fromElement(this, {
                                handler: "image",
                                url: 'http://img.youtube.com/vi/'+ myCode.value+'/hqdefault.jpg'
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
                                src:'http://img.youtube.com/vi/'+ myCode.value+'/hqdefault.jpg'
                            }).inject(video);
                            iframe.addEvent('click',function(){
                                SqueezeBox.fromElement(this, {
                                    handler: "image",
                                    url: 'http://img.youtube.com/vi/'+ myCode.value+'/hqdefault.jpg'
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
                        url: '<?php echo $src2;?>'
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
window.addEvent("domready", function () {
    var tz_count=0;
    var tz_portfolio_image = function(id,name,value,title,i){

//                            var tz_e = location.href.match(/^(.+)\/index\.php.*/i)[1];

        var div = new Element('div',{class: 'input-prepend input-append'});
        div.inject($(id));
        var icon = new Element('div',{
            class: 'add-on',
            html: '<\i class="icon-eye-open"></i>'
        }).inject(div);
        var tz_a = new Element('input',{
            type:"text",
            class:"inputbox image-select",
            name:name,
            id:"image-select-"+tz_count,
            readonly:'true',
            style:"width:200px;"
        });
        tz_a.inject(div);
        var tz_d = "image-select-" + tz_count,
            tz_b = (new Element("button", {
                type: "button",
                "id": "tz_img_button"+tz_count,
                class: 'btn'
            })).set('html', '<i class="icon-file"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BROWSE_SERVER')?>').inject(tz_a,'after'),
            tz_f = (new Element("button", {
                "name": "tz_img_cancel_"+i,
                class: 'btn',
                html:'<i class="icon-refresh"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_RESET');?>'
            })).inject(tz_b,'after'),
            tz_g = (new Element("div", {
                "class": "tz-image-preview",
                "style": "clear:both;"
            })).inject(div,'after');

        tz_a.setProperty("id", tz_d);
        if(value){
            var tz_h = (new Element("img", {
                src: value,
                style:'max-width:300px; cursor:pointer;',
                title:title
            })).inject(tz_g);
            tz_h.addEvent('click',function(){
                SqueezeBox.fromElement(this, {
                    handler: "image",
                    url: String.from(value.replace(/_S/,'_L'))
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
        html: '<?php echo JText::_('COM_TZ_PORTFOLIO_DELETE_IMAGES');?>'
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
        html: '<?php echo JText::_('COM_TZ_PORTFOLIO_DELETE_IMAGES');?>'
    }).inject($('tz_img_hover_server'));
    <?php
    }
    else{
    ?>
    tz_portfolio_image('tz_img_hover_server','tz_img_hover_server','','',0);
    <?php } ?>
    $('tz_image_gallery').empty();

    var _tz_portfolio_myGallery = function(name,title,i){
        var myTr = new Element('div',{class:"control-group"});
        myTr.inject($('tz_image_gallery'));
        var myTd = new Element('div',{
            html:'<strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE');?></strong>',
            class:"control-label"
        });
        myTd.inject(myTr);
        var myTd = new Element('div',{class: "controls"});
        myTd.inject(myTr);
        var myFile = new Element('input',{
            type:'file',
            id:'tz_img_client_'+i,
            name:'tz_img_client[]',
            size:'50px'
        });
        myFile.inject(myTd);

        //row 2
        var myTr2 = new Element('div',{class: 'control-group'});
        myTr2.inject($('tz_image_gallery'));
        var myTd2   = new Element('div',{class: 'controls'});
        myTd2.inject(myTr2);
//                        var myTd2 = new Element('div',{
//                        });
//                        myTd2.inject(myTd);

        var myField = tz_portfolio_image(myTd2,'tz_img_gallery_server[]',name,title,i);

        if(name.length >0){
            var tz_hidden = new Element('input',{
                'type': 'hidden',
                'name': 'tz_image_gallery_current[]',
                'value': name
            }).inject(myTd2);
            var tz_checkbox = new Element("input",{
                type: 'checkbox',
                id: 'tz_del_gallery_'+i,
                'name': 'tz_delete_image_gallery[]',
                'value': i,
                style: 'clear: both'
            }).inject(myTd2);
            var tz_label = new Element('label',{
                'for': 'tz_del_gallery_'+i,
                style: 'clear: none; margin: 2px 3px;',
                html: '<?php echo JText::_('COM_TZ_PORTFOLIO_CURRENT_IMAGE_DESC');?>'
            }).inject(myTd2);
        }

        //row 3
        var myTr3 = new Element('div',{class: 'control-group'});
        myTr3.inject($('tz_image_gallery'));
        var myTd = new Element('div',{
            html:'<strong><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_IMAGE_TITLE');?></strong>',
            class: 'control-label'
        });
        myTd.inject(myTr3);
        var myTd = new Element('div',{class:'controls'});
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
        var myTr4 = new Element('div',{class:'control-group'});
        myTr4.inject($('tz_image_gallery'));
        var myTd = new Element('div',{ class: 'controls'
        });
        myTr4.adopt(myTd);
//                        var myTd = new Element('td',{
//                            styles:{
//                                //float:'right'
//                            }
//                        });
//                        myTd.inject(myTr4);

        if(tz_count>2){
            var myRemove = new Element('button',{
                type:'button',
                name:'tz_remove_image_'+i,
                class: 'btn',
                style: 'margin-bottom: 10px;',
                html:'<i class="icon-remove"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE');?>',
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
    hidden.inject($('tz_image_gallery'),'after');
    var k=1;
    var tr_add  = new Element('div',{class: 'control-group'}).inject($('tz_image_gallery')),
        td_add  = new Element('div',{class: 'control-label', style: 'text-align: left;'}).inject(tr_add);
    var myGallery = new Element('button',{
        class: 'btn',
        html:'<i class="icon-plus"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_NEW');?>',
        events:{
            click: function(e){
                e.stop();
                _tz_portfolio_myGallery('','',k);
                k++;
            }
        }
    });
    myGallery.inject(td_add);
//                    var myDiv = (new Element("div", {
//                        "style": "clear:both;"
//                    })).inject(myGallery,'after');

    <?php
   if(count($list -> gallery -> images)>1){
       $galleryTitle   = null;
       if(isset($list -> gallery -> title)){
           $galleryTitle   = $list -> gallery -> title;
       }
       foreach($list -> gallery -> images as $i => $item):
           $src    = null;
           if($pos = strpos($item,'.')){
               $ext    = substr($item,$pos,strlen($item));
               $src    = JURI::root().str_replace($ext,'_S'.$ext,$item);
           }

   ?>

    _tz_portfolio_myGallery('<?php echo $src;?>','<?php echo ($galleryTitle)?$galleryTitle[$i]:'';?>',<?php echo $i;?>);
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

    //tz_image('tz_image_gallery');

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
            value:'<?php echo $row -> attachold;?>'
        }).inject(myTd);
        var myTd = new Element('td',{
            html:'<?php echo !empty($row -> attachtitle)? $row -> attachtitle: $row -> attachold;?>'
        }).inject(myTr);
        var myHidden = new Element('input',{
            type:'hidden',
            name:'tz_attachments_hidden_title[]',
            value:'<?php if($row -> attachfiles != $row -> attachtitle) echo $row -> attachtitle;?>'
        }).inject(myTd);
        var myTd = new Element('td',{
        }).inject(myTr);
        var myInput = new Element('button',{
            type:'button',
            class: 'btn',
            id:'tz_attachments_delete_<?php echo $i;?>',
            html:'<i class="icon-remove"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_BUTTON_DELETE');?>'
        }).inject(myTd);
        $('tz_attachments_delete_<?php echo $i;?>').addEvent('click',function(){
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

        var myTr0 = new Element('div',{ class: 'control-group'
        }).inject($('tz_attachments_table'));
//                        var myTd = new Element('div',{class: 'controls'
//                        }).inject(myTr0);

        var myButton = new Element('button',{
            class: 'btn',
            html:'<i class="icon-plus"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_ATTACHMENT_FIELD');?>'
        }).inject(myTr0);
        var myI = new Element('i',{
            html:'<?php echo JText::_('COM_TZ_PORTFOLIO_ADD_ATTACHMENT_FIELD_DESC');?>'
        }).inject(myTr0);

        myButton.addEvent('click',function(e){
            e.stop();
            var myTr1 = new Element('div',{class: 'control-group'
            }).inject($('tz_attachments_table'));

            var myTd = new Element('div',{
                html:'<?php echo JText::_('COM_TZ_PORTFOLIO_FILED_ATTACHMENTS');?>',
                class: 'control-label'
            }).inject(myTr1);
            var myTd = new Element('div',{
                class: 'controls'
            }).inject(myTr1);

            var myFile = new Element('input',{
                type:'file',
                name:'tz_attachments_file[]',
                size:'60%'
            }).inject(myTd);

            var myTr2 = new Element('div',{ class: 'control-group'
            }).inject($('tz_attachments_table'));
            var myTd = new Element('div',{
                class: 'control-label',
                html:'<?php echo JText::_('COM_TZ_PORTFOLIO_FORM_LINK_TITLE');?>',
            }).inject(myTr2);
            var myTd = new Element('div',{
                class: 'controls'
            }).inject(myTr2);

            var myInput = new Element('input',{
                type:'text',
                name:'tz_attachments_title[]',
                value:'',
                size:'70%'
            }).inject(myTd);
//                            var myTr3 = new Element('tr',{
//                            }).inject($('tz_attachments_table'));

            var myTr3 = new Element('div',{class:'control-group'}).inject($('tz_attachments_table'));
            var myTd = new Element('div',{
                class: 'controls'
            }).inject(myTr3);
            var myRemove = new Element('button',{
                type:'button',
                class: 'btn',
                html:'<i class="icon-remove"></i>&nbsp;<?php echo JText::_('COM_TZ_PORTFOLIO_REMOVE');?>',
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
<div class="control-group" id="tz_fields_group">
    <div class="control-label">
        <label title="<?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED_DESC')?>"
               class="hasTip required" for="groupid" id="jform_groupid-lbl" aria-invalid="false">
            <?php echo JText::_('COM_TZ_PORTFOLIO_FIELDS_GROUP_REQUIRED')?>
        </label>
    </div>
    <div class="controls"><?php echo $this -> listsGroup;?></div>
</div>
<div class="control-group">
    <div class="control-label">
        <label title="<?php echo JText::_('COM_TZ_PORTFOLIO_TYPE_OF_MEDIA')?>::<?php echo JText::_('COM_TZ_PORTFOLIO_TYPE_OF_MEDIA_DESC');?>"
               class="hasTip required"
               for="jform_type_of_media"
               id="jform_type_of_media-lbl">
            <?php echo JText::_('COM_TZ_PORTFOLIO_TYPE_OF_MEDIA')?>
        </label>
    </div>
    <div class="controls">
        <select id="jform_type_of_media" name="type_of_media" required="required" class="required">
            <option value=""><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_MEDIA_TYPE');?></option>
            <option value="none"<?php if($type == 'none') echo ' selected="selected"';?>><?php echo JText::_('COM_TZ_PORTFOLIO_OPTION_NONE_MEDIA');?></option>
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
        <label><?php echo JText::_('COM_TZ_PORTFOLIO_FORM_TAGS');?></label>
    </div>
    <div class="controls">
        <!--                        <input type="text" name="tz_tags" value="--><?php //echo $this -> listsTags;?><!--"-->
        <!--                               size="50"-->
        <!--                                placeholder="--><?php //echo JText::_('COM_TZ_PORTFOLIO_FORM_TAGS_DESC');?><!--"/>-->
        <input type="text" name="tz_tags[]" class="suggest tagsinput"
               data-provide="typeahead"/>
    </div>
</div>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('created_by_alias'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('created_by_alias'); ?></div>
</div>

<div class="control-group">
    <div class="control-label">
        <?php echo $this->form->getLabel('template_id'); ?>
    </div>
    <div class="controls">
        <?php echo $this->form->getInput('template_id'); ?>
    </div>
</div>

<?php if ($this->item->params->get('access-change')): ?>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('state'); ?></div>
    </div>

    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('featured'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('featured'); ?></div>
    </div>

    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('publish_up'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('publish_up'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('publish_down'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('publish_down'); ?></div>
    </div>

<?php endif; ?>
<div class="control-group">
    <div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
    <div class="controls"><?php echo $this->form->getInput('access'); ?></div>
</div>
<?php if (is_null($this->item->id)):?>
    <div class="control-group">
        <p><?php echo JText::_('COM_CONTENT_ORDERING'); ?></p>
    </div>
<?php endif; ?>
</fieldset>

<fieldset>
    <legend><?php echo JText::_('JFIELD_LANGUAGE_LABEL'); ?></legend>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('language'); ?></div>
    </div>
</fieldset>

<fieldset>
    <legend><?php echo JText::_('COM_CONTENT_METADATA'); ?></legend>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('metadesc'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('metadesc'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('metakey'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('metakey'); ?></div>
    </div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
    <?php if($this->params->get('enable_category', 0) == 1) :?>
        <input type="hidden" id="jform_catid" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
    <?php endif;?>
    <?php echo JHtml::_( 'form.token' ); ?>
</fieldset>
</form>
</div>
</div>
