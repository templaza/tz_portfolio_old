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
 * Articles list controller class.
 */
class TZ_PortfolioControllerArticles extends JControllerAdmin
{
    protected $input    = null;
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	ContentControllerArticles
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
        JFactory::getLanguage() -> load('com_content');
        $this -> input  = JFactory::getApplication()->input;
		// Articles default form can come from the articles or featured view.
		// Adjust the redirect view on the value of 'view' in the request.
		if (JRequest::getCmd('view') == 'featured') {
			$this->view_list = 'featured';
		}
		parent::__construct($config);

		$this->registerTask('unfeatured',	'featured');
	}

    /**
	 * Method to publish a list of items
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid = JRequest::getVar('cid', array(), '', 'array');
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->publish($cid, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				if ($value == 1)
				{
					$ntext = $this->text_prefix . '_ARTICLE_N_ITEMS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = $this->text_prefix . '_ARTICLE_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = $this->text_prefix . '_ARTICLE_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->text_prefix . '_ARTICLE_N_ITEMS_TRASHED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
		$extension = JRequest::getCmd('extension');
		$extensionURL = ($extension) ? '&extension=' . JRequest::getCmd('extension') : '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}

	/**
	 * Method to toggle the featured setting of a list of articles.
	 *
	 * @return	void
	 * @since	1.6
	 */
	function featured()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('featured' => 1, 'unfeatured' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_tz_portfolio.article.'.(int) $id)) {
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->featured($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_tz_portfolio&view=articles');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Article', $prefix = 'TZ_PortfolioModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

    public function saveOrderAjax()
    {
        $pks = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    function resizeimage(){

        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
        }
        else
        {
            // Get the model.
            $model  = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            if($items  = $model -> getArticles($cid)){
                foreach($items as $item){
                    // Resize images
                    if((isset($item -> images) && !empty($item -> images))){
                        if($model -> resizeImage(JPATH_SITE.DIRECTORY_SEPARATOR.$item -> images)){
                            $this -> setMessage(JText::_('COM_TZ_PORTFOLIO_RESIZE_IMAGES_SUCCESSFUL'));
                        }else{
                            $this -> setMessage($model -> getError(),'error');
                        }
                    }

                    // Resize image hover
                    if(isset($item -> images_hover) && !empty($item -> images_hover)){
                        if($model -> resizeImage(JPATH_SITE.DIRECTORY_SEPARATOR.$item -> images_hover)){
                            $this -> setMessage(JText::_('COM_TZ_PORTFOLIO_RESIZE_IMAGES_SUCCESSFUL'));
                        }else{
                            $this -> setMessage($model -> getError(),'error');
                        }
                    }

                    // Resize video's thumbnail
                    if(isset($item -> videothumb) && !empty($item -> videothumb)){
                        if($model -> resizeImage(JPATH_SITE.DIRECTORY_SEPARATOR.$item -> videothumb)){
                            $this -> setMessage(JText::_('COM_TZ_PORTFOLIO_RESIZE_IMAGES_SUCCESSFUL'));
                        }else{
                            $this -> setMessage($model -> getError(),'error');
                        }
                    }

                    // Resize audio's thumbnail
                    if(isset($item -> audiothumb) && !empty($item -> audiothumb)){
                        if($model -> resizeImage(JPATH_SITE.DIRECTORY_SEPARATOR.$item -> audiothumb)){
                            $this -> setMessage(JText::_('COM_TZ_PORTFOLIO_RESIZE_IMAGES_SUCCESSFUL'));
                        }else{
                            $this -> setMessage($model -> getError(),'error');
                        }
                    }

                    // Resize image slider
                    if(isset($item -> gallery) && !empty($item -> gallery)){
                        $images = explode('///',$item -> gallery);
                        if(count($images)){
                            foreach($images as $i => $image){
                                if($model -> resizeImage(JPATH_SITE.DIRECTORY_SEPARATOR.$image,true)){
                                    $this -> setMessage(JText::_('COM_TZ_PORTFOLIO_RESIZE_IMAGES_SUCCESSFUL'));
                                }else{
                                    $this -> setMessage($model -> getError(),'error');
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list));
    }
}
