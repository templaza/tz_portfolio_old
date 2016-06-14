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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.components.view');

class TZ_PortfolioViewGroups extends JViewLegacy
{
    protected $items        = null;
    protected $pagination   = null;
    protected $state        = null;

    function display($tpl = null){

        $this -> items      = $this -> get('Items');
        $this -> pagination = $this -> get('Pagination');
        $this -> state      = $this -> get('State');

        TZ_PortfolioHelper::addSubmenu('groups');

        $this -> addToolbar();

        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);
    }

    protected function addToolbar(){
        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_GROUP_FIELDS_MANAGER'));
        JToolBarHelper::addNew('group.add');
        JToolBarHelper::editList('group.edit');
        JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_QUESTION_DELETE'),'groups.delete');
        JToolBarHelper::divider();
        JToolBarHelper::preferences('com_tz_portfolio');
        JToolBarHelper::divider();


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
//
//        JToolBarHelper::divider();

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_v3:Administration#Group_Fields');

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
    }
}