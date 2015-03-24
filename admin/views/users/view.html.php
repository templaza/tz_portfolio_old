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

class TZ_PortfolioViewUsers extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
        require_once(JPATH_COMPONENT.'/helpers/users.php');
        JFactory::getLanguage() -> load('com_users');
        
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

        TZ_PortfolioHelper::addSubmenu('users');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		$this->addToolbar();
        $this -> sidebar    = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= TZ_PortfolioHelperUsers::getActions();

        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_USERS_VIEW_USERS_TITLE'), 'user');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('user.add');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('user.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('users.activate', 'COM_USERS_TOOLBAR_ACTIVATE', true);
			JToolBarHelper::unpublish('users.block', 'COM_USERS_TOOLBAR_BLOCK', true);
			JToolBarHelper::custom('users.unblock', 'unblock.png', 'unblock_f2.png', 'COM_USERS_TOOLBAR_UNBLOCK', true);
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'users.delete');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_tz_portfolio');
//			JToolBarHelper::preferences('com_users');
			JToolBarHelper::divider();
		}

        $doc    = JFactory::getDocument();
        // If the joomla is version 3.0
        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio/fonts/font-awesome-v3.0.2/css/font-awesome.min.css');
        }

        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio/css/style.min.css');


//        // Complie button
//        $compileTitle   = JText::_('COM_TZ_PORTFOLIO_COMPILE_LESS_TO_CSS');
//        $compileIcon    = '<i class="icon-check"></i>&nbsp;';
//        $compileClass   = ' class="btn btn-small"';
//
//        //// If the joomla's version is more than or equal to 3.0
//        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
//            $compileIcon    = '<span class="tz-icon-compile"></span>';
//            $compileClass   = null;
//        }
//
//        $compileButton   = '<a'.$compileClass.' onclick="Joomla.submitbutton(\'action.lesscall\')" href="#">'
//            .$compileIcon.$compileTitle.'</a> ';
//
//        //  JS Compress button
//        $compressTitle  = JText::_('COM_TZ_PORTFOLIO_COMPRESSION_JS');
//        $compressIcon   = '<i class="icon-check"></i>&nbsp;';
//        $compressClass  = ' class="btn btn-small"';
//
//        //// If the joomla's version is more than or equal to 3.0
//        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
//            $compressIcon    = '<span class="tz-icon-compress"></span>';
//            $compressClass   = null;
//        }
//
//        $compressButton   = '<a'.$compressClass.' onclick="Joomla.submitbutton(\'action.jscompress\')" href="#">'
//            .$compressIcon.$compressTitle.'</a> ';
//
//        $bar -> appendButton('Custom',$compileButton,'compile');
//        $bar -> appendButton('Custom',$compressButton,'compress');
//        JToolBarHelper::divider();


		JToolBarHelper::help('JHELP_USERS_USER_MANAGER');
        JHtmlSidebar::setAction('index.php?option=com_users&view=users');

        // Special HTML workaround to get send popup working
        $docClass       = ' class="btn btn-small"';
        $youtubeIcon    = '<i class="tz-icon-youtube tz-icon-14"></i>&nbsp;';
        $wikiIcon       = '<i class="tz-icon-wikipedia tz-icon-14"></i>&nbsp;';

        $youtubeTitle   = JText::_('COM_TZ_PORTFOLIO_VIDEO_TUTORIALS');
        $wikiTitle      = JText::_('COM_TZ_PORTFOLIO_WIKIPEDIA_TUTORIALS');

        //// If the joomla's version is more than or equal to 3.0
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $youtubeIcon  = '<span class="tz-icon-youtube" title="'.$youtubeTitle.'"></span>';
            $wikiIcon  = '<span class="tz-icon-wikipedia" title="'.$wikiTitle.'"></span>';
            $docClass   = null;
        }

        $videoTutorial    ='<a'.$docClass.' onclick="Joomla.popupWindow(\'http://www.youtube.com/channel/UCykS6SX6L2GOI-n3IOPfTVQ/videos\', \''
            .$youtubeTitle.'\', 800, 500, 1)"'.' href="#">'
            .$youtubeIcon.$youtubeTitle.'</a>';

        $wikiTutorial    ='<a'.$docClass.' onclick="Joomla.popupWindow(\'http://wiki.templaza.com/Main_Page\', \''
            .$wikiTitle.'\', 800, 500, 1)"'.' href="#">'
            .$wikiIcon
            .$wikiTitle.'</a>';

        $bar->appendButton('Custom',$videoTutorial,'youtube');
        $bar->appendButton('Custom',$wikiTutorial,'wikipedia');

		JHtmlSidebar::addFilter(
			JText::_('COM_USERS_FILTER_STATE'),
			'filter_state',
			JHtml::_('select.options', TZ_PortfolioHelperUsers::getStateOptions(), 'value', 'text', $this->state->get('filter.state'))
		);

		JHtmlSidebar::addFilter(
			JText::_('COM_USERS_FILTER_ACTIVE'),
			'filter_active',
			JHtml::_('select.options', TZ_PortfolioHelperUsers::getActiveOptions(), 'value', 'text', $this->state->get('filter.active'))
		);

		JHtmlSidebar::addFilter(
			JText::_('COM_USERS_FILTER_USERGROUP'),
			'filter_group_id',
			JHtml::_('select.options', TZ_PortfolioHelperUsers::getGroups(), 'value', 'text', $this->state->get('filter.group_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('COM_USERS_OPTION_FILTER_DATE'),
			'filter_range',
			JHtml::_('select.options', TZ_PortfolioHelperUsers::getRangeOptions(), 'value', 'text', $this->state->get('filter.range'))
		);
	}
}
