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

class TZ_PortfolioControllerFields extends JControllerLegacy
{
    protected $_option      = null;
    protected $_task        = null;
    protected $_link        = null;
    public $model           = null;
    protected $cids         = null;

    public function __construct($config=array()){
        parent::__construct($config);
        $this -> _task      = JRequest::getCmd('task',null);
        $this -> _option    = JRequest::getCmd('option', null);
        $this -> cids       = JRequest::getVar('cid',array(),'','array');
    }

    public function display($cachable = false, $urlparams = array()){
        $this -> _link      = 'index.php?option='.$this -> _option.'&view='.$this -> getTask();

        $doc        = JFactory::getDocument();
        $type       = $doc -> getType();
        
        if(!$view       = $this -> getView($this -> getTask(),$type)){
            $this -> setRedirect($this -> _link,$this -> getError(),'error');
            $this -> redirect();
        }
        if(!$this -> model   = $this -> getModel(ucfirst($this -> getTask()))){
            $this -> setRedirect($this -> _link,$this->getError(),'error');
            $this -> redirect();
        }

        $this -> model -> _link     = $this -> _link;
        $this -> model -> _task     = $this -> _task;
        $view -> _task              = $this -> _task;
        $view -> _option            = $this -> _option;
        $view -> _view              = $this -> getTask();
        $view -> _link              = $this -> _link;

        $view -> setModel($this -> model,true);

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
               $this->saveFields();
               break;
            case 'remove':
               $this -> removeFields();
               break;
            case 'publish':
               $this -> publishFields(1);
               break;
            case 'unpublish':
               $this -> publishFields(0);
               break;
            case 'saveorder':
                $this -> saveOrderFields();
                break;
            case 'orderup':
                $this -> moveOrderFields(-1);
                break;
            case 'orderdown':
                $this -> moveOrderFields(1);
                break;
            case 'cancel':
                $this -> cancel();
                break;
            default:
               $view -> setLayout('default');
               break;
        }

        $view -> display();
    }

    function cancel(){
        $this -> setRedirect('index.php?option=com_tz_portfolio&view=fields');
        $this -> redirect();
        return true;
    }
    protected function saveFields(){
        
       // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> saveFields($this -> _task))
            $this -> setRedirect($this -> model -> _link,$this -> model -> msg);
        else
            $this -> setRedirect($this -> _link, $this -> model -> getError(),'error');
        $this -> redirect();

    }

    protected function publishFields($state = null){
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> publishFields($this -> cids,$state))
            $this -> setRedirect($this -> _link,$this -> model -> msg);
        else
            $this -> setRedirect($this -> _link,$this -> model -> getError(),'error');

        $this -> redirect();
    }

    protected function removeFields(){
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> removeFields($this -> cids))
            $this -> setRedirect($this -> model -> _link,$this -> model -> msg);
        else{
            $this -> setRedirect($this -> model -> _link,$this -> model -> getError(),'error');
            return false;
        }
        $this -> redirect();
    }

    protected function saveOrderFields(){
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> saveOrderFields($this -> cids,JRequest::getVar('order',array(),'','array')))
            $this -> setRedirect($this -> _link,$this -> model -> msg);
        else
            $this ->setRedirect($this -> _link, $this -> model -> getError(),'error');
        $this -> redirect();
    }

    protected function moveOrderFields($des=null){
        // Check for request forgeries
        JRequest::checkToken() or jexit('Invalid Token');

        if($this -> model -> moveOrderFields($this -> cids,$des))
            $this -> setRedirect($this -> _link,$this -> model -> msg);
        else
            $this ->setRedirect($this -> _link, $this -> model -> getError(),'error');
        $this -> redirect();
    }
}