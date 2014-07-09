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

class TZ_PortfolioControllerTemplates extends JControllerAdmin
{
    protected $text_prefix  = 'COM_TZ_PORTFOLIO_TEMPLATES';

    public function getModel($name = 'Template', $prefix = 'TZ_PortfolioModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function duplicate()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        JFactory::getLanguage() -> load('com_templates');

        $pks = $this->input->post->get('cid', array(), 'array');

        try
        {
            if (empty($pks))
            {
                throw new Exception(JText::_('COM_TEMPLATES_NO_TEMPLATE_SELECTED'));
            }

            JArrayHelper::toInteger($pks);

            $model = $this->getModel();
            $model->duplicate($pks);
            $this->setMessage(JText::_('COM_TEMPLATES_SUCCESS_DUPLICATED'));
        }
        catch (Exception $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        $this->setRedirect('index.php?option=com_tz_portfolio&view=templates');
    }

    public function setDefault()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        JFactory::getLanguage() -> load('com_templates');

        $pks = $this->input->post->get('cid', array(), 'array');

        try
        {
            if (empty($pks))
            {
                throw new Exception(JText::_('COM_TEMPLATES_NO_TEMPLATE_SELECTED'));
            }

            JArrayHelper::toInteger($pks);

            // Pop off the first element.
            $id = array_shift($pks);
            $model = $this->getModel();
            $model->setHome($id);
            $this->setMessage(JText::_('COM_TEMPLATES_SUCCESS_HOME_SET'));
        }
        catch (Exception $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        $this->setRedirect('index.php?option=com_tz_portfolio&view=templates');
    }

    public function unsetDefault()
    {
        // Check for request forgeries
        JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

        JFactory::getLanguage() -> load('com_templates');

        $pks = $this->input->get->get('cid', array(), 'array');
        JArrayHelper::toInteger($pks);

        try
        {
            if (empty($pks))
            {
                throw new Exception(JText::_('COM_TEMPLATES_NO_TEMPLATE_SELECTED'));
            }

            // Pop off the first element.
            $id = array_shift($pks);
            $model = $this->getModel();
            $model->unsetHome($id);
            $this->setMessage(JText::_('COM_TEMPLATES_SUCCESS_HOME_UNSET'));
        }
        catch (Exception $e)
        {
            JError::raiseWarning(500, $e->getMessage());
        }

        $this->setRedirect('index.php?option=com_tz_portfolio&view=templates');
    }

}