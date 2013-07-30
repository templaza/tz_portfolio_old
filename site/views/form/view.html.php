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
 * HTML Article View class for the Content component.
 */
class TZ_PortfolioViewForm extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;

	public function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
        $doc        = JFactory::getDocument();
		$user		= JFactory::getUser();

		// Get model data.
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');

		if (empty($this->item->id)) {
			$authorised = $user->authorise('core.create', 'com_tz_portfolio') || (count($user->getAuthorisedCategories('com_tz_portfolio', 'core.create')));
		}
		else {
			$authorised = $this->item->params->get('access-edit');
		}

		if ($authorised !== true) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		if (!empty($this->item)) {
			$this->item->images = json_decode($this->item->images);
			$this->item->urls = json_decode($this->item->urls);

			$this->form->bind($this->item);
			$this->form->bind($this->item->urls);
			$this->form->bind($this->item->images);

		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut to the parameters.
		$params	= &$this->state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params	= $params;
		$this->user		= $user;

		if ($this->params->get('enable_category') == 1) {
			$catid = JRequest::getInt('catid');
			$category = JCategories::getInstance('Content')->get($this->params->get('catid', 1));
			$this->category_title = $category->title;
		}

		$this->_prepareDocument();

//        $model  = Jmodel::getInstance('Article','TZ_PortfolioModel');
//        $groupFields    = $model -> getGroups();
//        $this -> assign('listsGroup',$groupFields);

        $this -> assign('listsGroup',$this -> get('Groups'));
        $this -> assign('listsTags',json_encode($this -> get('Tags')));
        $this -> assign('listAttach',$this -> get('Attachment'));
        $this -> assign('listEdit',$this -> get('FieldsContent'));
        JModelLegacy::addIncludePath('administrator/components/com_tz_portfolio/models','TZ_PortfolioModel');
        $modelTag   = JModelLegacy::getInstance('Tags','TZ_PortfolioModel');
        $this -> assign('tagsSuggest',$modelTag -> getTagsName());

        $csscompress    = null;
        if($params -> get('css_compression',0)){
            $csscompress    = '.min';
        }

        $jscompress         = new stdClass();
        $jscompress -> extfile  = null;
        $jscompress -> folder   = null;
        if($params -> get('js_compression',1)){
            $jscompress -> extfile  = '.min';
            $jscompress -> folder   = '/packed';
        }

        $doc -> addStyleSheet('components/com_tz_portfolio/css/tzportfolio'.$csscompress.'.css');
        
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_CONTENT_FORM_EDIT_ARTICLE'));
		}

		$title = $this->params->def('page_title', JText::_('COM_CONTENT_FORM_EDIT_ARTICLE'));
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
