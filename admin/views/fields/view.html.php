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

jimport('joomla.application.component.view');

class TZ_PortfolioViewFields extends JViewLegacy
{
    protected $items        = null;
    protected $state        = null;
    protected $pagination   = null;
    protected $sidebar      = null;

    public function display($tpl = null){

        $this -> items      = $this -> get('Items');
        $this -> state      = $this -> get('State');
        $this -> pagination = $this -> get('Pagination');

        TZ_PortfolioHelper::addSubmenu('fields');

        $this -> addToolbar();

        $this -> sidebar    = JHtmlSidebar::render();
        parent::display($tpl);
    }

    protected function addToolbar(){
        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_FIELDS_MANAGER'));
        JToolBarHelper::addNew('field.add');
        JToolBarHelper::editList('field.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publish('fields.publish');
        JToolBarHelper::unpublish('fields.unpublish');
        JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_QUESTION_DELETE'),'fields.delete');
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

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_v3:Administration#Fields');

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


        $fieldsType = array('textfield' => JText::_('COM_TZ_PORTFOLIO_TEXT_FIELD'),
            'textarea' => JText::_('COM_TZ_PORTFOLIO_TEXTAREA'),
            'select' => JText::_('COM_TZ_PORTFOLIO_DROP_DOWN_SELECTION'),
            'multipleSelect' => JText::_('COM_TZ_PORTFOLIO_MULTI_SELECT_LIST'),
            'radio' => JText::_('COM_TZ_PORTFOLIO_RADIO_BUTTONS'),
            'checkbox' => JText::_('COM_TZ_PORTFOLIO_CHECK_BOX'),
            'link' => JText::_('COM_TZ_PORTFOLIO_LINK'));

        JHtmlSidebar::addFilter(
            JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_TYPE'),
            'filter_type',
            JHtml::_('select.options',$fieldsType,'value','text',$this -> state -> filter_type)
        );

        $groupModel = JModelLegacy::getInstance('Groups','TZ_PortfolioModel');
        $groups     = $groupModel -> getItemsArray();

        JHtmlSidebar::addFilter(
            JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_GROUP'),
            'filter_group',
            JHtml::_('select.options',$groups,'value','text',$this -> state ->  filter_group)
        );

        $state = array( 'P' => JText::_('JPUBLISHED'), 'U' => JText::_('JUNPUBLISHED'));
        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options',$state,'value','text',$this -> state -> filter_state)
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
        return array('f.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'a.state' => JText::_('JSTATUS'),
            'f.title' => JText::_('COM_TZ_PORTFOLIO_HEADING_TITLE'),
            'groupname' => JText::_('COM_TZ_PORTFOLIO_HEADING_GROUP'),
            'f.type' => JText::_('COM_TZ_PORTFOLIO_HEADING_TYPE'),
            'f.published' => JText::_('JSTATUS'),
            'f.id' => JText::_('JGRID_HEADING_ID'));
    }
}