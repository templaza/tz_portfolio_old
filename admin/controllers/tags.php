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

class TZ_PortfolioControllerTags extends JControllerLegacy
{
    var $_link  = null;
    var $model  = null;
    var $cids   = null;

    function __construct(){
        parent::__construct();
        $this -> cids   = JRequest::getVar('cid',array(),'','array');
    }
    function display(){
        $this->_link     = 'index.php?option=com_tz_portfolio&view=tags';

        $doc    = &JFactory::getDocument();
        $type   = $doc -> getType();
        $view   = &$this -> getView('Tags',$type);
        if($this -> model = &$this -> getModel('Tags')){
            $view -> setModel($this -> model,true);
        }

        $this -> model -> _link     = $this -> _link;

        switch(JRequest::getCmd('task')){
            default:
                $view -> setLayout('default');
                break;
            case 'add':
            case 'new':
                $view -> setLayout('add');
                break;
            case 'edit':
                $view -> setLayout('edit');
                break;
            case 'cancel':
                $this -> cancel();
                break;
            case 'save':
            case 'apply':
            case 'save2new':
                $this->saveTags();
                break;
            case 'publish':
                $this -> publishTags(1);
                break;
            case 'unpublish':
                $this -> publishTags(0);
                break;
            case 'remove':
                $this -> removeTags();
                break;
        }
        if(JRequest::getCmd('layout') == 'modal'){
            $view -> setLayout('modal');
        }
        $view -> display();
    }

    function publishTags($state){
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> publishTags($this -> cids,$state))
            $this -> setRedirect($this -> _link,$this -> model -> msg);
        else
            $this -> setRedirect($this -> _link,$this -> model -> getError(),'error');
        $this -> redirect();
    }

    protected function removeTags(){
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> removeTags($this -> cids))
            $this -> setRedirect($this -> model -> _link,$this -> model -> msg);
        else{
            $this -> setRedirect($this -> model -> _link,$this -> model -> getError(),'error');
            return false;
        }
        $this -> redirect();
    }

    function saveTags(){
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> saveTags(JRequest::getCmd('task'))){
            $this -> setRedirect($this -> model -> _link,$this -> model -> msg);
        }
        else{
            $this -> setRedirect($this -> model -> _link,$this -> model -> getError(),'error');
        }
        $this -> redirect();
    }

    function cancel(){
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $this -> setRedirect($this->_link);
        $this -> redirect();
    }

}