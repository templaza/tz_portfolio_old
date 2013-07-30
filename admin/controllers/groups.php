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

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Users list controller class.
 */
class TZ_PortfolioControllerGroups extends JControllerAdmin
{
    protected $view_list = 'groups';
    
    public function getModel($name = 'Group', $prefix = 'TZ_PortfolioModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    function publish(){
        JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

        $cid    = JRequest::getVar('cid',array(),'','array');
        $data   = array('unpublish' => 0  ,'publish' => 1  );
        $task = $this->getTask();

        $value  = JArrayHelper::getValue($data,$task,0, 'int');

        $model  = $this -> getModel();

        if(!$model -> publish($cid,$value)){
            $this -> setMessage($model -> getError());
        }
        $this -> setRedirect('index.php?option='.$this -> option .'&view='.$this -> view_list);
    }

    public function delete()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = JRequest::getVar('cid', array(), '', 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid))
            {
                $this->setMessage(JText::plural('COM_TZ_PORTFOLIO_FIELDS_GROUP_COUNT_DELETED', count($cid)));
            }
            else
            {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }

}