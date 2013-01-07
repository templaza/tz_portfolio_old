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

class TZ_PortfolioControllerFieldsGroup extends JControllerLegacy
{
    var $_option     = null;
    var $_task;
    var $_view      = null;
    var $_link      = null;
    var $model      = null;
    public function __construct($config=array()){
        $this -> _option   = JRequest::getCmd('option',null);
        $this -> _view      = JRequest::getCmd('view',null);
        $this -> _task     = JRequest::getCmd('task',null);
        //$this-> ('task',JRequest::getCmd('task',null));

         parent::__construct($config);
    }
    function display(){

        $this->_link     = 'index.php?option='.$this -> _option.'&view='.($this->getTask());

        $doc    = &JFactory::getDocument();
        $type   = $doc->getType();

        if(!$view   = &$this -> getView($this -> getTask(),$type)){
            $this -> setRedirect($this -> link,$this -> getError(),'error');
            $this -> redirect();
        }

        if($this -> model   = & $this -> getModel($this -> getTask()))
            $view   -> setModel($this -> model,true);
        
        $this -> model -> _link     = $this -> _link;
        $view -> _option            = $this -> _option;
        $view -> _view              = $this -> getTask();
        $view -> _task              = $this -> _task;
        $view -> _link              = $this -> _link;

        switch ($this -> _task){
            case 'add':
            case 'new':
                $view -> setLayout('add');
                break;
            case 'edit':
                $view -> setLayout('edit');
                break;
            case 'save':
            case 'apply':
            case 'save2new':
                $this->saveFieldsGroup();
                break;
            case 'remove':
                $this -> removeFieldsGroup();
                break;
            default:
                $view -> setLayout('default');
                break;
        }

        $view -> display();
    }

    function removeFieldsGroup(){
        if($this -> model -> removeFieldsGroup(JRequest::getVar('cid',array(),'','array')))
            $this -> setRedirect($this -> _link,$this -> model -> msg);
        else
            $this -> setRedirect($this -> _link, $this -> model -> getError(),'error');
        $this -> redirect();
    }

    function saveFieldsGroup(){
        
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> saveFieldsGroup($this -> _task))
            $this -> setRedirect($this -> model -> _link,$this -> model -> msg);
        else
            $this -> setRedirect($this -> _link, $this -> model -> getError(),'error');
        $this -> redirect();
        
    }
    
    function cancel(){
        global $mainframe;

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        // Initialize variables
//        $db =& JFactory::getDBO();
//
//        $redirect = JRequest::getCmd( 'redirect', '', 'post' );
//
//        $row =& JTable::getInstance('category');
//        $row->bind( JRequest::get( 'post' ));
//        $row->checkin();

        $mainframe -> redirect( $this->_link);
    }
}