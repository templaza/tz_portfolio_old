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

class TZ_PortfolioViewFields extends JViewLegacy
{
    public $_option     = null;
    public $_view       = null;
    public $_link       = null;
    public $_task       = null;

    function display($tpl=null){

        $state  = $this -> get('state');
        $this -> assign('state',$state);
         //Get editor
        $editor = JFactory::getEditor();
        if ($this->getLayout() !== 'modal')
		{
			TZ_PortfolioHelper::addSubmenu('fields');
		}

        $this -> assign('lists',$this -> get(ucfirst($this -> _view)));

        $this -> assign('defvalue',$this -> get('Params'));
        //var_dump($this -> get('Params'));
        $this -> assign('listsEdit',$this -> get(ucfirst($this -> _view).'Edit'));
        $this -> assign('listsGroup',$this -> get('FieldsGroup'));
        $this -> assign('pagination',$this -> get('Pagination'));
        $this -> assignRef('editor',$editor);
        $this -> assignRef('option',$this -> _option);
        $this -> assignRef('view',$this -> _view);
        $this -> assignRef('order',$state -> filter_order);
        $this -> assignRef('order_Dir',$state -> filter_order_Dir);
        $this -> assign('filter_state',$state -> get('filter_state'));
        $this -> assign('filter_search',$state -> get('filter_search'));
        $this -> assign('filter_type',$state -> get('filter_type'));
        $this -> assign('filter_group',$state -> get('filter_group'));
        $this -> setToolBar();
        $this -> sidebar    = JHtmlSidebar::render();

        parent::display($tpl);
    }

    protected function setToolBar(){
        switch ($this -> _task){
            default:
                JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_FIELDS_MANAGER'));
                //JSubmenuHelper::addEntry(JText::_('Fields'),$this -> _link,true);
                JToolBarHelper::publishList();
                JToolBarHelper::unpublishList();
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
                JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_FIELDS_MANAGER_TASK','<small><small>'
                                               .JText::_(ucfirst($this-> _task)).'</small></small>'));
                JToolBarHelper::save2new();
                JToolBarHelper::save();
                JToolBarHelper::apply();
                JToolBarHelper::cancel();
                break;
            case 'edit':
                JRequest::setVar('hidemainmenu',true);
                JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_FIELDS_MANAGER_TASK','<small><small>'
                                               .JText::_(ucfirst($this-> _task)).'</small></small>'));
                JToolBarHelper::save();
                JToolBarHelper::save2new();
                JToolBarHelper::apply();
                JToolBarHelper::cancel('cancel',JText::_('JTOOLBAR_CLOSE'));
                break;
        }

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
            JHtml::_('select.options',$fieldsType,'value','text',$this -> filter_type)
		);

        $fieldsGroup    = array();
        if(count($this -> listsGroup)){
            foreach($this -> listsGroup as $item){
                $fieldsGroup[$item -> id]   = $item -> name;
            }
        }
        JHtmlSidebar::addFilter(
			JText::_('COM_TZ_PORTFOLIO_OPTION_SELECT_GROUP'),
			'filter_group',
			JHtml::_('select.options',$fieldsGroup,'value','text',$this -> filter_group)
		);

        $state = array( 'P' => JText::_('JPUBLISHED'), 'U' => JText::_('JUNPUBLISHED'));
        JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options',$state,'value','text',$this -> state -> filter_state)
		);


    }
}
?>