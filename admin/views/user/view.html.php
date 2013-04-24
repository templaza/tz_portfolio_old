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

jimport('joomla.application.component.view');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/users.php');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 */
class TZ_PortfolioViewUser extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $grouplist;
	protected $groups;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        JFactory::getLanguage() -> load('com_users');
		$this->form			= $this->get('Form');
		$this->item			= $this->get('Item');
		$this->grouplist	= $this->get('Groups');
		$this->groups		= $this->get('AssignedGroups');
		$this->state		= $this->get('State');
        $editor             = JFactory::getEditor();
        $this -> assign('editor',$editor);
        $this -> assign('listsUsers',$this -> get('Users'));
//        $editor -> display();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->form->setValue('password',		null);
		$this->form->setValue('password2',	null);

		parent::display($tpl);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', 1);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$canDo		= TZ_PortfolioHelperUsers::getActions();


		$isNew	= ($this->item->id == 0);
		$isProfile = $this->item->id == $user->id;
		JToolBarHelper::title(JText::_($isNew ? 'COM_USERS_VIEW_NEW_USER_TITLE' : ($isProfile ? 'COM_USERS_VIEW_EDIT_PROFILE_TITLE' : 'COM_USERS_VIEW_EDIT_USER_TITLE')), $isNew ? 'user-add' : ($isProfile ? 'user-profile' : 'user-edit'));
		if ($canDo->get('core.edit')||$canDo->get('core.create')) {
			JToolBarHelper::apply('user.apply');
			JToolBarHelper::save('user.save');
		}
		if ($canDo->get('core.create')&&$canDo->get('core.manage')) {
			JToolBarHelper::save2new('user.save2new');
		}
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('user.cancel');
		} else {
			JToolBarHelper::cancel('user.cancel', 'JTOOLBAR_CLOSE');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_USERS_USER_MANAGER_EDIT');
	}
}
