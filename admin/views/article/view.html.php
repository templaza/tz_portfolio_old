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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit an article.
 *
 */
class TZ_PortfolioViewArticle extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
    protected $pluginsTab;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        if(JRequest::getCmd('task')!='lists'){
            JFactory::getLanguage()->load('com_content');
            if ($this->getLayout() == 'pagebreak') {
                // TODO: This is really dogy - should change this one day.
                $eName		= JRequest::getVar('e_name');
                $eName		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
                $document	= JFactory::getDocument();
                $document->setTitle(JText::_('COM_CONTENT_PAGEBREAK_DOC_TITLE'));
                $this->assignRef('eName', $eName);
                parent::display($tpl);
                return;
            }

            // Initialiase variables.
            $this->form		= $this->get('Form');
            $this->item		= $this->get('Item');
            $this->state	= $this->get('State');

            $this->canDo	= TZ_PortfolioHelper::getActions($this->state->get('filter.category_id'));

            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }
            $this -> assign('listsGroup',$this -> get('Groups'));
            $this -> assign('listsTags',json_encode($this -> get('Tags')));
//            $this -> assign('listsFields',$this -> get('ListsFields'));
            $this -> assign('listAttach',$this -> get('Attachment'));
            $this -> assign('listEdit',$this -> get('FieldsContent'));
            $modelTag   = JModelLegacy::getInstance('Tags','TZ_PortfolioModel');
            $this -> assign('tagsSuggest',$modelTag -> getTagsName());
//            $this -> assign('tagsSuggest',$modelTag -> getItems());

            if($model  = JModelLegacy::getInstance('Plugin','TZ_PortfolioModel',array('ignore_request' => true))){
                $model -> setState('com_tz_portfolio.plugin.articleId',JRequest::getInt('id',null));
                $this -> pluginsTab = $model -> getForm();
            }

            $this->addToolbar();
        }
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= TZ_PortfolioHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		JToolBarHelper::title(JText::_('COM_CONTENT_PAGE_'.($checkedOut ? 'VIEW_ARTICLE' : ($isNew ? 'ADD_ARTICLE' : 'EDIT_ARTICLE'))), 'article-add.png');

		// Built the actions for new and existing records.

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories('com_content', 'core.create')) > 0)) {
			JToolBarHelper::apply('article.apply');
			JToolBarHelper::save('article.save');
			JToolBarHelper::save2new('article.save2new');
			JToolBarHelper::cancel('article.cancel');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::apply('article.apply');
					JToolBarHelper::save('article.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::save2new('article.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('article.save2copy');
			}

			JToolBarHelper::cancel('article.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER_EDIT');

	}
}
