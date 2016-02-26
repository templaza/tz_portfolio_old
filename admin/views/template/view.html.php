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


class TZ_PortfolioViewTemplate extends JViewLegacy
{
    protected $item = null;
    protected $tzlayout = null;
    protected $form = null;
    protected $childrens = null;

    public function display($tpl=null)
    {

        $this -> form       = $this -> get('Form');

        TZ_PortfolioHelper::addSubmenu('templates');
        $this -> sidebar    = JHtmlSidebar::render();

        $this -> addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(){
        JRequest::setVar('hidemainmenu',true);
        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_TEMPLATES_MANAGER_TASK'
            ,JText::_('COM_TZ_PORTFOLIO_INSTALL_TEMPLATE')));
        JToolBarHelper::cancel('template.cancel',JText::_('JTOOLBAR_CLOSE'));
    }
}