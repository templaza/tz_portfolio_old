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

/**
 * Supports a modal article picker.
 */
class JFormFieldModal_Users extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Users';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectUser_'.$this->id.'(id, title) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_tz_portfolio&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field='.$this -> id.'&amp;function=jSelectUser_'.$this->id;

		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT name' .
			' FROM #__users' .
			' WHERE id = '.(int) $this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_TZ_PORTFOLIO_SELECT_AN_USER');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');


        $size   = null;
        if(isset($this -> element['size'])){
            $size   = ' size ="'.$this -> element['size'].'"';
        }

		// The current user display field.
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){ // If the joomla's version is more than or equal to 3.0
            $html[] = '<div class="fltlft">';
        }
        else{
		    $html[] = '<div class="input-append">';
        }

		$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled"'.$size.'/>';

        // If the joomla's version is more than or equal to 3.0
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $html[] = '</div>';
        }

        $title      = JText::_('JLIB_FORM_CHANGE_USER');
        $textLink   = '<i class="icon-user"></i>&nbsp;';
        $class      = 'modal btn btn-primary';

		// The user select button.
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){ // If the joomla's version is more than or equal to 3.0
            $html[]     = '<div class="button2-left">';
            $html[]     = ' <div class="blank">';
            $textLink   = $title;
            $class      = 'modal modal_jform_created_by';
        }


		$html[] = '	<a class="'.$class.'" title="'.$title.'"'
            .'href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
            .$textLink.'</a>';

        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){ // If the joomla's version is more than or equal to 3.0
            $html[] = ' </div>';
            $html[] = '</div>';
        }
        else{
            $html[] = '</div>';
        }


		// The active article id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}
