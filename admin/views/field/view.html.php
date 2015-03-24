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

class TZ_PortfolioViewField extends JViewLegacy
{
    protected $item     = null;
    protected $groups   = null;

    public function display($tpl = null){
        $this -> item   = $this -> get('Item');

        $buttons_plugin = JPluginHelper::getPlugin('editors-xtd');

        if($buttons_plugin){

        }

        $groupModel = JModelLegacy::getInstance('Groups','TZ_PortfolioModel',array('ignore_request' => true));
        $groupModel -> setState('filter_order','name');
        $groupModel -> setState('filter_order_Dir','ASC');

        $this -> groups = $groupModel -> getItems();

        $editor = JFactory::getEditor();
        $this -> assign('editor',$editor);

        if($this -> item -> id == 0){
            $this -> item -> published = 'P';
        }
        else{
            if($this -> item -> published == 1){
                $this -> item -> published  = 'P';
            }
            else{
                $this -> item -> published  = 'U';
            }
        }

        $this -> addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(){
        JRequest::setVar('hidemainmenu',true);

        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();

        $isNew  = ($this -> item -> id == 0);

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_FIELDS_MANAGER_TASK',' <small><small>'
            .JText::_(($isNew)?'COM_TZ_PORTFOLIO_PAGE_ADD_FIELD':'COM_TZ_PORTFOLIO_PAGE_EDIT_FIELD')
            .'</small></small>'));
        JToolBarHelper::apply('field.apply');
        JToolBarHelper::save('field.save');
        JToolBarHelper::save2new('field.save2new');
        JToolBarHelper::cancel('field.cancel',JText::_('JTOOLBAR_CLOSE'));

        JToolBarHelper::divider();

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_v3:Administration#How_to_Add_or_Edit_3');

        // If the joomla is version 3.0
        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio/fonts/font-awesome-v3.0.2/css/font-awesome.min.css');
        }

        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio/css/style.min.css');

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