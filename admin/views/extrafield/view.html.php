<?php
/*------------------------------------------------------------------------

# JVisualContent Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

class JVisualContentViewExtraField extends JViewLegacy{

    protected $item     = null;
    protected $form     = null;

    function display($tpl=null){

        // Assign data item
        $this -> item   = $this -> get('Item');
        // Assign form field
        $this -> form   = $this -> get('Form');

        // Call function addtoolbar
        $this -> addToolbar();

        parent::display($tpl);
    }

    // This is function to add toolbar
    protected function addToolbar(){
        JRequest::setVar('hidemainmenu',true);

        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();
        $user	= JFactory::getUser();
        $canDo  = JHelperContent::getActions('com_jvisualcontent');

        $isNew  = ($this -> item -> id == 0);

        JToolBarHelper::title(JText::sprintf('COM_JVISUALCONTENT_EXTRAFIELD_MANAGER_TASK',' <small><small>'
            .JText::_(($isNew)?'COM_JVISUALCONTENT_PAGE_ADD_TEMPLATE':'COM_JVISUALCONTENT_PAGE_EDIT_TEMPLATE')
            .'</small></small>'));
        JToolBarHelper::apply('extrafield.apply');
        JToolBarHelper::save('extrafield.save');
        JToolBarHelper::save2new('extrafield.save2new');

        // If checked out, we can still save
        if (!$isNew && ($user->authorise('core.edit.state', 'com_jvisualcontent') || $canDo->get('core.edit.state'))) {
            JToolBarHelper::save2copy('extrafield.save2copy');
        }

        JToolBarHelper::cancel('extrafield.cancel',JText::_('JTOOLBAR_CLOSE'));

        JToolBarHelper::divider();
    }
}