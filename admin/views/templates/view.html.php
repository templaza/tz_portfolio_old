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

//no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/helpers');

class TZ_PortfolioViewTemplates extends JViewLegacy
{
    protected $state;
    protected $items;
    protected $sidebar;
    protected $pagination;

    public function display($tpl=null){
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            tzportfolioimport('helper/content');
        }

        $this->items		= $this->get('Items');
        $this->state		= $this->get('State');
        $this->pagination	= $this->get('pagination');

        JFactory::getLanguage() -> load('com_templates');

        TZ_PortfolioHelper::addSubmenu('templates');
        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this -> addToolbar();
        }

        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);
    }

    protected function addToolbar(){

        $canDo	= JHelperContent::getActions('com_tz_portfolio');

        $user		= JFactory::getUser();

        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_TEMPLATES_MANAGER'));
        if ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::makeDefault('templates.setDefault', 'COM_TEMPLATES_TOOLBAR_SET_HOME');
            JToolbarHelper::divider();
        }
        JToolBarHelper::addNew('template.add');
        JToolBarHelper::editList('template.edit');
        JToolBarHelper::divider();
//        JToolBarHelper::publish('templates.publish');
//        JToolBarHelper::unpublish('templates.unpublish');
        if ($canDo->get('core.create'))
        {
            JToolbarHelper::custom('templates.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
        }

        if ($canDo->get('core.delete')){
            JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_QUESTION_DELETE'),'templates.delete');
            JToolBarHelper::divider();
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

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_v3:Administration#Tags');

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

//        $state = array( 'P' => JText::_('JPUBLISHED'), 'U' => JText::_('JUNPUBLISHED'));
//        JHtmlSidebar::addFilter(
//            JText::_('JOPTION_SELECT_PUBLISHED'),
//            'filter_state',
//            JHtml::_('select.options',$state,'value','text',$this -> state -> filter_state)
//        );

    }
}