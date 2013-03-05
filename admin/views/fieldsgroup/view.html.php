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

class TZ_PortfolioViewFieldsGroup extends JViewLegacy
{
    public $_option     = null;
    public $_view       = null;
    public $_task       = null;
    public $_link      = null;

    function display($tpl=null){

        if ($this->getLayout() !== 'modal')
		{
			TZ_PortfolioHelper::addSubmenu('fieldsgroup');
		}
        //Get editor
        $editor = JFactory::getEditor();
        $this -> state  = $this -> get('State');

        $this -> assign('editor',$editor);
        $this -> assign('option',$this -> _option);
        $this -> assign('view',$this -> _view);
        $this -> assign('filter_state',$this -> state -> filter_state);
        $this -> assign('order',$this -> state -> filter_order);
        $this -> assign('order_Dir',$this -> state -> filter_order_Dir);
        $this -> assign('lists',$this -> get($this -> _view));
        $this -> assign('pagination',$this -> get('Pagination'));
        ($this -> _task =='edit')?$this -> assign('listsEdit',$this -> get($this -> _view.'edit')):'';
        $this -> setToolbar();
        $this -> sidebar    = JHtmlSidebar::render();
        
        parent::display($tpl);
    }
    function setToolbar(){
        switch ($this -> _task){
            default:
                JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_GROUP_FIELDS_MANAGER'));
                //JSubmenuHelper::addEntry(JText::_('Group fields'),$this -> _link,true);
                JToolBarHelper::addNew();
                JToolBarHelper::editList();
                JToolBarHelper::deleteList(JText::_('COM_TZ_PORTFOLIO_QUESTION_DELETE'));
                JToolBarHelper::preferences('com_tz_portfolio');
                    
                $doc    = JFactory::getDocument();
                $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio/assets/style.css');
                // Special HTML workaround to get send popup working
                $videoTutorial    ='<a class="btn btn-small" onclick="Joomla.popupWindow(\'http://www.youtube.com/channel/UCykS6SX6L2GOI-n3IOPfTVQ/videos\', \''
                    .JText::_('COM_TZ_PORTFOLIO_VIDEO_TUTORIALS').'\', 800, 500, 1)"'.' href="#">'
                    .'<i class="icon-14-youtube"></i>&nbsp;'
                    .JText::_('COM_TZ_PORTFOLIO_VIDEO_TUTORIALS').'</a>';
                $wikiTutorial    ='<a class="btn btn-small" onclick="Joomla.popupWindow(\'http://wiki.templaza.com/Main_Page\', \''
                    .JText::_('COM_TZ_PORTFOLIO_VIDEO_TUTORIALS').'\', 800, 500, 1)"'.' href="#">'
                    .'<i class="icon-14-wikipedia"></i>&nbsp;'
                    .JText::_('COM_TZ_PORTFOLIO_WIKIPEDIA_TUTORIALS').'</a>';
                $bar= JToolBar::getInstance( 'toolbar' );
                $bar->appendButton('Custom',$videoTutorial);
                $bar->appendButton('Custom',$wikiTutorial);
                break;
            case 'add':
            case 'new':
                JRequest::setVar('hidemainmenu',true);
                JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_GROUP_FIELDS_MANAGER_TASK','<small><small>'
                                               .JText::_(ucfirst($this-> _task)).'</small></small>'));
                JToolBarHelper::save2new();
                JToolBarHelper::save();
                JToolBarHelper::apply();
                JToolBarHelper::cancel();
                break;
            case 'edit':
                JRequest::setVar('hidemainmenu',true);
                JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_GROUP_FIELDS_MANAGER_TASK','<small><small>'
                                               .JText::_(ucfirst($this -> _task)).'</small></small>'));
                JToolBarHelper::save();
                JToolBarHelper::save2new();
                JToolBarHelper::apply();
                JToolBarHelper::cancel('cancel',JText::_('JTOOLBAR_CLOSE'));
                break;

        }
    }
}