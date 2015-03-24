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
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/helpers');

/**
 * View class for a list of articles.

 */
class TZ_PortfolioViewArticles extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
        JFactory::getLanguage() -> load('com_content');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->authors		= $this->get('Authors');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

		$this->assign('f_levels', $options);



		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

        TZ_PortfolioHelper::addSubmenu('articles');
        $this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= TZ_PortfolioHelper::getActions($this->state->get('filter.category_id'));
		$user		= JFactory::getUser();
        
        // Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolBarHelper::title(JText::_('COM_CONTENT_ARTICLES_TITLE'), 'article.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_content', 'core.create'))) > 0 ) {
			JToolBarHelper::addNew('article.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('article.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('articles.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('articles.archive');
			JToolBarHelper::checkin('articles.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'articles.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('articles.trash');
			JToolBarHelper::divider();
		}



        //// If the joomla's version is more than or equal to 3.0
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $resizeTitle      = JText::_('COM_TZ_PORTFOLIO_RESIZE_ALL_IMAGES');
            $resizeIcon  = '<span class="tz-icon-expand" title="'.$resizeTitle.'"></span>';

            $rdhtml = '<a class="toolbar" href="#"'
                .' onclick="if (document.adminForm.boxchecked.value==0){'
                .'alert(\'Please first make a selection from the list\');}'
                .'else{ Joomla.submitbutton(\'articles.resizeimage\')}">';
            $rdhtml .= $resizeIcon.$resizeTitle.'</a>';

            $bar->appendButton('Custom', $rdhtml, JText::_('COM_TZ_PORTFOLIO_RESIZE_ALL_IMAGES'),'articles.resizeimage',true);
        }else{
            JToolbarHelper::custom('articles.resizeimage','expand-2','',JText::_('COM_TZ_PORTFOLIO_RESIZE_ALL_IMAGES'),true);
        }
        
         //Add a batch button
		if ($user->authorise('core.edit'))
		{
			JHtml::_('bootstrap.modal', 'collapseModal');

            $title      = JText::_('JTOOLBAR_BATCH');
            $batchIcon  = '<i class="icon-checkbox-partial" title="'.$title.'"></i>';
            $batchClass = ' class="btn btn-small"';

            //// If the joomla's version is more than or equal to 3.0
            if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
                $title      = JText::_('COM_TZ_PORTFOLIO_BATCH');
                $batchIcon  = '<span class="tz-icon-batch" title="'.$title.'"></span>';
                $batchClass = '';
            }

			$dhtml = '<a'.$batchClass.' href="#" data-toggle="modal" data-target="#collapseModal">';
            $dhtml .= $batchIcon.$title.'</a>';

			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_tz_portfolio');
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
//                           .$compileIcon.$compileTitle.'</a> ';
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
//                            .$compressIcon.$compressTitle.'</a> ';
//
//        $bar -> appendButton('Custom',$compileButton,'compile');
//        $bar -> appendButton('Custom',$compressButton,'compress');
//        JToolBarHelper::divider();

		JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER');

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

        JHtmlSidebar::setAction('index.php?option=com_tz_portfolio&view=articles');

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category_id'))
		);

//        require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/categories.php');
        JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/models');
        $model      = JModelLegacy::getInstance('Categories','TZ_PortfolioModel',array('ignore_request' => true));
        $model -> setState('filter.group',$this -> state -> get('filter.group'));
        $listGroup  = $model -> getGroups();

        JHtmlSidebar::addFilter(
			JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_FIELDS_GROUP'),
			'filter_group',
			JHtml::_('select.options', $listGroup, 'value', 'text', $this->state->get('filter.group'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_MAX_LEVELS'),
			'filter_level',
			JHtml::_('select.options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_AUTHOR'),
			'filter_author_id',
			JHtml::_('select.options', $this->authors, 'value', 'text', $this->state->get('filter.author_id'))
		);

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
		);
	}
    
    /**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.title' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'access_level' => JText::_('JGRID_HEADING_ACCESS'),
			'a.created_by' => JText::_('JAUTHOR'),
			'language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.created' => JText::_('JDATE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
