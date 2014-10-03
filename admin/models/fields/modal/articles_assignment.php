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

defined('JPATH_BASE') or die;
include_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/core/defines.php';

JFormHelper::loadFieldClass('checkboxes');

/**
 * Supports a modal article picker.
 */
class JFormFieldModal_Articles_Assignment extends JFormFieldCheckboxes
{
    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'Modal_Articles_Assignment';

//    function __construct($form ){
//        JFactory::getLanguage() -> load('com_tz_portfolio');
//    }

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput()
    {
        $allowEdit		= ((string) $this->element['edit'] == 'true') ? true : false;
        $allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

        // Load the modal behavior script.
        JHtml::_('behavior.modal', 'a.modal');

        // Build the script.
        $script = array();
        $script[] = '	function '.$this->id.'Remove(obj) {';
        $script[] = '	    obj.parentNode.parentNode.parentNode.removeChild(obj.parentNode.parentNode);';
        $script[] = '		var tztable = document.getElementById("'.$this->id.'_table");';
        $script[] = '		var tbody = tztable.getElementsByTagName("tbody");';
        $script[] = '		if(!tbody[0].innerHTML.trim().length){
                                var tzclear = document.getElementById("' . $this->id . '_clear");
                                tzclear.setAttribute("class",tzclear.getAttribute("class")+" hidden");
                            }';
        $script[] = '	};';

        $script[] = '	function jSelectArticle_'.$this->id.'(ids, titles, categories) {';
//        $script[] = '		document.getElementById("'.$this->id.'").value = ids;';
        $script[] = '		var tztable = document.getElementById("'.$this->id.'_table");';
        $script[] = '		var tbody = tztable.getElementsByTagName("tbody");';
//        $script[] = '		var html = "";';
        $script[] = '		var parser = new DOMParser();';
        $script[] = '		if(ids.length){';
        $script[] = '		for(var i = 0; i < ids.length; i++){
                                var tr = document.createElement("tr");

                                var td = document.createElement("td");
                                td.innerHTML = titles[i];
                                tr.appendChild(td);';

        $script[] =            'td = td.cloneNode(true);
                                td.innerHTML = categories[i];
                                tr.appendChild(td);

                                tbody[0].appendChild(tr);';

        $script[] = '           td = td.cloneNode(true);
                                td.innerHTML = "<a href=\"javascript:\" class=\"btn\" onclick=\"'.$this->id.'Remove(this);\"><i class=\"icon-remove\"></i> '.JText::_('JTOOLBAR_REMOVE').'</a>";';
        // Edit article button
        if ($allowEdit)
        {
            $script[]   =       'td.innerHTML = "<a class=\"btn\" target=\"_blank\" href=\"index.php?option=com_tz_portfolio&task=article.edit&id="+ids[i]+"\"><span class=\"icon-edit\"></span> ' . JText::_('JACTION_EDIT') . '</a> <a href=\"javascript:\" class=\"btn\" onclick=\"'.$this->id.'Remove(this);\"><i class=\"icon-remove\"></i> '.JText::_('JTOOLBAR_REMOVE').'</a>"';
        }
        $script[] =            'tr.appendChild(td);';


        $script[] =            'td = td.cloneNode(true);
                                td.innerHTML = ids[i]+"<input type=\"hidden\" name=\"'.$this -> name.'\"'
            .' id=\"'.$this -> id.'\" value=\""+ids[i]+"\">";
                                tr.appendChild(td);

                                tbody[0].appendChild(tr);

                            }';
        $script[] = '       }';
//        $script[] = '       tbody[0].innerHTML = html;';
        if ($allowClear)
        {
            $script[] = '		var tzclear = document.getElementById("' . $this->id . '_clear");';
            $script[] = '		if(tzclear.getAttribute("class").match(/(.*?)\shidden\s?(.*?)/)){
                                    tzclear.setAttribute("class",tzclear.getAttribute("class").replace(/\shidden/,""));
                                };';
        }
//        if ($allowEdit)
//        {
//            $script[] = '		var tzedit = document.getElementById("' . $this->id . '_edit");';
//            $script[] = '		if(tzedit.getAttribute("class").match(/(.*?)\shidden\s?(.*?)/)){
//                                    tzedit.setAttribute("class",tzedit.getAttribute("class").replace(/\shidden/,""));
//                                };';
//        }

        $script[] = '		SqueezeBox.close();';
        $script[] = '	}';

        // Clear button script
        static $scriptClear;

        if ($allowClear && !$scriptClear){

            $scriptClear = true;

            $script[] = '	function jClearArticle(id) {';
            $script[] = '	    var tztable = document.getElementById(id+"_table");';
            $script[] = '		var tbody = tztable.getElementsByTagName("tbody");';
            $script[] = '		tbody[0].innerHTML = "";';
//            $script[] = '		document.getElementById(id + "_id").value = "";';
//            $script[] = '		document.getElementById(id + "_name").value = "' . htmlspecialchars(JText::_('COM_CONTENT_SELECT_AN_ARTICLE', true), ENT_COMPAT, 'UTF-8') . '";';
            $script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
            $script[] = '		if (document.getElementById(id + "_edit")) {';
            $script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
            $script[] = '		}';
            $script[] = '		return false;';
            $script[] = '	}';
        }

        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
        $lang   = JFactory::getLanguage();
        $lang -> load('com_content');


        // Setup variables for display.
        $html	= array();
        $link	= 'index.php?option=com_tz_portfolio&amp;view=articles&amp;layout=modals&amp;tmpl=component&amp;function=jSelectArticle_'.$this->id;

        if (isset($this->element['language']))
        {
            $link .= '&amp;forcedLanguage=' . $this->element['language'];
        }

        $db	= JFactory::getDBO();
        $db->setQuery(
            'SELECT title' .
            ' FROM #__content' .
            ' WHERE id = '.(int) $this->value
        );
        $title = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            JError::raiseWarning(500, $error);
        }

        if (empty($title)) {
            $title = JText::_('COM_CONTENT_SELECT_AN_ARTICLE');
        }
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // The current user display field.
        // The current tag display field.
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){ // If the joomla's version is more than or equal to 3.0
            $html[] = '<div class="fltlft">';
        }
        else{
            $html[] = '<div class="input-append">';
        }

//        $html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';

        // If the joomla's version is more than or equal to 3.0
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $html[] = '</div>';
        }

        $title      = JText::_('COM_TZ_PORTFOLIO_CHANGE_ARTICLES');
        $textLink   = '<i class="icon-copy"></i>&nbsp;'.JText::_('COM_TZ_PORTFOLIO_FIELD_SELECT_ARTICLES');
        $class      = 'modal btn';

        // The user select button.
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){ // If the joomla's version is more than or equal to 3.0
            $html[]     = '<div class="button2-left">';
            $html[]     = ' <div class="blank">';
            $textLink   = JText::_('COM_TZ_PORTFOLIO_CHANGE_ARTICLE_BUTTON');
            $class      = 'modal modal_jform_article';
        }

        // The active article id field.
        $value  = $this -> value;

        // The user select button.
        $html[] = '	<a class="modal btn" title="'.$title.'"'
            .' href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
            .$textLink.'</a>';

        // Clear article button
        if ($allowClear)
        {
            $html[] = '<a href="javascript:" id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearArticle(\'' . $this->id . '\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</a>';
        }
        // The user select button.
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){ // If the joomla's version is more than or equal to 3.0
            $html[]     = '</div>';
        }

        $html[] = '</div>';

        // class='required' for client side validation
        $class = '';
        if ($this->required) {
            $class = ' class="required modal-value"';
        }

        $html[] = $this ->_getHtml($this -> id,$value);

//        $html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

        return implode("\n", $html);
    }

    protected function _getHtml($id,$values = null){
    ?>
        <?php
        $tbody  = null;
        $old    = null;
        if($values){
            if($items = $this -> _getItems($values)){
                ob_start();
                foreach($items as $item){
                    $old    .= '<input type="hidden" name="jform['.$this -> fieldname.'_old][]" value="'.$item -> id.'">';
                    ?>
                    <tr>
                        <td><?php echo $item -> title;?></td>
                        <td><?php echo $item -> category_title;?></td>
                        <td>
                            <a class="btn" target="_blank" href="index.php?option=com_tz_portfolio&task=article.edit&id=<?php echo $item -> id;?>"><span class="icon-edit"></span> <?php echo JText::_('JACTION_EDIT')?></a>
                            <a href="javascript:" class="btn" onclick="<?php echo $id;?>Remove(this);"><i class="icon-remove"></i> <?php echo JText::_('JTOOLBAR_REMOVE');?></a>
                        </td>
                        <td>
                            <?php echo $item -> id;?>
                            <input type="hidden" name="<?php echo $this -> name;?>"
                                   value="<?php echo $item -> id;?>">
                        </td>
                    </tr>
                <?php
                }
                $tbody  = ob_get_contents();
                ob_end_clean();
            }
        }
        ?>
    <?php
        ob_start();
        ?>
        <div class="clearfix"></div>
        <table id="<?php echo $id.'_table';?>" class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_('JGLOBAL_TITLE');?></th>
                    <th><?php echo JText::_('JCATEGORY');?></th>
                    <th style="text-align:center; width: 15%;"><?php echo JText::_('JSTATUS');?></th>
                    <th style="width: 5%;"><?php echo JText::_('JGRID_HEADING_ID');?></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $tbody;?>
            </tbody>
        </table>

        <?php
        echo $old;
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    protected function _getItems($ids){
        if($ids){
            $db     = JFactory::getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('a.id,a.title,c.title AS category_title,xc.template_id');
            $query -> from('#__content AS a');
            $query -> join('LEFT','#__categories AS c ON a.catid = c.id');
            $query -> join('LEFT','#__tz_portfolio_xref_content AS xc ON a.id = xc.contentid');
            $query -> where('xc.contentid IN('.$ids.')');
            $db -> setQuery($query);
            if($rows = $db -> loadObjectList()){
                return $rows;
            }
        }
        return false;
    }
}
