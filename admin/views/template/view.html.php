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
        JFactory::getLanguage() -> load('com_content');
//        JFactory::getLanguage() -> load('com_content',JPATH_SITE);
        JFactory::getLanguage() -> load('com_templates');
        $this -> document -> addCustomTag('<script type="text/javascript" src="'.JUri::base(true).'/components/com_tz_portfolio/js/jquery-ui.min.js"></script>');
        $this -> document -> addCustomTag('<script type="text/javascript">
        var pluginPath = "'.JURI::root(true).'/administrator/components/com_tz_portfolio/views/template/tmpl";
        var fieldName = \'jform[attrib]\';
        </script>');
        $this -> document -> addCustomTag('<script type="text/javascript" src="'.JUri::base(true).'/components/com_tz_portfolio/js/layout.admin.js"></script>');
        $this -> document -> addCustomTag('<script type="text/javascript" src="'.JUri::base(true).'/components/com_tz_portfolio/js/spectrum.js"></script>');
        $this -> document -> addCustomTag('<script type="text/javascript">
        Joomla.submitbutton = function(task) {
            if (task == \'template.cancel\' || document.formvalidator.isValid(document.id(\'template-form\'))) {
                jQuery.tzTemplateSubmit();
                Joomla.submitform(task, document.getElementById(\'template-form\'));
            }else {
                alert("'.$this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')).'");
            }
        };
        </script>');
        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.JUri::root(true).'/components/com_tz_portfolio/bootstrap/css/bootstrap-responsive.min.css" type="text/css"/>');
        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.JUri::base(true).'/components/com_tz_portfolio/css/admin-layout.css" type="text/css"/>');
        $this -> document -> addCustomTag('<link rel="stylesheet" href="'.JUri::base(true).'/components/com_tz_portfolio/css/spectrum.css" type="text/css"/>');
//        $doc    = JFactory::getDocument();
//        $doc -> addScript(JUri::base(true).'/components/com_tz_portfolio/js/jquery-ui.min.js');
//        $doc -> addStyleSheet()

        $this -> item   = $this -> get('Item');
        $this -> tzlayout   = $this -> get('TZLayout');
//        var_dump($this -> tzlayout);
        $this -> form   = $this -> get('Form');

        $this -> addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(){
        JRequest::setVar('hidemainmenu',true);

        $doc    = JFactory::getDocument();
        $bar    = JToolBar::getInstance();
        $user	= JFactory::getUser();

        $isNew  = ($this -> item -> id == 0);

        JToolBarHelper::title(JText::sprintf('COM_TZ_PORTFOLIO_TEMPLATES_MANAGER_TASK',' <small><small>'
            .JText::_(($isNew)?'COM_TZ_PORTFOLIO_PAGE_ADD_TEMPLATE':'COM_TZ_PORTFOLIO_PAGE_EDIT_TEMPLATE')
            .'</small></small>'));
        JToolBarHelper::apply('template.apply');
        JToolBarHelper::save('template.save');
        JToolBarHelper::save2new('template.save2new');

        // If checked out, we can still save
        if (!$isNew && $user->authorise('core.edit.state', 'com_tz_portfolio')) {
            JToolBarHelper::save2copy('template.save2copy');
        }

        JToolBarHelper::cancel('template.cancel',JText::_('JTOOLBAR_CLOSE'));

        JToolBarHelper::divider();

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,'http://wiki.templaza.com/TZ_Portfolio_v3:Administration#How_to_Add_or_Edit_3');

        // If the joomla is version 3.0
        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio/fonts/font-awesome-4.1.0/css/font-awesome.css');
        }

        $doc -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio/css/style.css');

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

    protected function get_value($item, $method){
        if (!isset($item -> $method)) {
            if (preg_match('/offset/', $method)) {
                return isset($item -> offset) ? $item -> offset : '';
            }
            if (preg_match('/col/', $method)) {
                return isset($item -> span) ? $item -> span : '12';
            }
        }
        return isset($item -> $method) ? $item -> $method : '';
    }

    protected function get_color($item, $method){
        return isset($item -> $method) ? $item -> $method : 'rgba(255, 255, 255, 0)';
    }
}