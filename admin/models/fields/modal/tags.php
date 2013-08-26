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
class JFormFieldModal_Tags extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Tags';

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
		$script[] = '	function jSelectTag_'.$this->id.'(id, title, catid, object) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_tz_portfolio&amp;view=tags&amp;layout=modal&amp;tmpl=component&amp;function=jSelectTag_'.$this->id;

		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT name' .
			' FROM #__tz_portfolio_tags' .
			' WHERE id = '.(int) $this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_TZ_PORTFOLIO_SELECT_AN_TAG');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current tag display field.
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){ // If the joomla's version is more than or equal to 3.0
            $html[] = '<div class="fltlft">';
        }
        else{
		    $html[] = '<div class="input-append">';
        }

		$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';

        // If the joomla's version is more than or equal to 3.0
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $html[] = '</div>';
        }

        $title      = JText::_('COM_TZ_PORTFOLIO_SELECT_TAGS_BUTTON_DESC');
        $textLink   = '<i class="icon-file"></i>&nbsp;'.JText::_('COM_TZ_PORTFOLIO_SELECT_TAGS_BUTTON');
        $class      = 'modal btn';

        // The user select button.
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){ // If the joomla's version is more than or equal to 3.0
            $html[]     = '<div class="button2-left">';
            $html[]     = ' <div class="blank">';
            $textLink   = JText::_('COM_TZ_PORTFOLIO_SELECT_TAGS_BUTTON');
            $class      = 'modal modal_jform_tags';
        }
        
		// The user select button.
		$html[] = '	<a class="'.$class.'" title="'.$title.'"'
            .' href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
            .$textLink.'</a>';
		$html[] = '</div>';

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
