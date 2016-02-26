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

    public function upload()
    {
        $app = JFactory::getApplication();
        $context = "$this->option.edit.$this->context";

        // Redirect to the edit screen.
        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=templates'
                . '&layout=upload', false
            )
        );

        return true;
    }

    public function publish()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $ids    = $this->input->get('cid', array(), 'array');
        $values = array('publish' => 1, 'unpublish' => 0);
        $task   = $this->getTask();
        $value  = JArrayHelper::getValue($values, $task, 0, 'int');
        $mtype  = 'message';

        if (empty($ids))
        {
            JError::raiseError(500, JText::_('COM_INSTALLER_ERROR_NO_EXTENSIONS_SELECTED'));
        }
        else
        {
            // Get the model.
            $model	= $this->getModel();

            // Change the state of the records.
            if (!$model->publish($ids, $value))
            {
                JError::raiseError(500, implode('<br />', $model->getErrors()));
            }
            else
            {
                if ($value == 1)
                {
                    $ntext = 'COM_INSTALLER_N_EXTENSIONS_PUBLISHED';
                }
                elseif ($value == 0)
                {
                    $ntext = 'COM_INSTALLER_N_EXTENSIONS_UNPUBLISHED';
                }

                $this->setMessage(JText::plural($ntext, count($ids)));
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio&view=templates', false));
    }
}