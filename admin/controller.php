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

// No direct access
defined('_JEXEC') or die;


class TZ_PortfolioController extends JControllerLegacy
{
	/**
	 * @var		string	The extension for which the categories apply.
	 * @since	1.6
	 */
	protected $extension;

    protected $input;

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
        $this -> input  = JFactory::getApplication() -> input;
		parent::__construct($config);

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension)) {
			$this->extension = JRequest::getCmd('extension', 'com_content');
		}

        // If the joomla's version is more than or equal to 3.0
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/cms/html');
            tzportfolioimport('cms/html/sidebar');

            $doc    = JFactory::getDocument();
            //Add Script to the header
            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/jquery.min.js');
            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/jquery-noconflict.js');
            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/bootstrap.min.js');
            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/chosen.jquery.min.js');
            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/jquery.ui.core.min.js');
            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/jquery.ui.sortable.min.js');
            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/sortablelist.js');
            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/js/template.min.js');

            $doc -> addStyleSheet(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/css/chosen.min.css');
//                    $doc -> addStyleSheet(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/css/bootstrap.min.css');
            //        $doc -> addStyleSheet(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/css/template.css');
            $doc -> addCustomTag('<link href="'.COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/css/template.min.css'.
                '" rel="stylesheet" type="text/css"/>');
        }
	}

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{

        // Get the document object.
        $document = JFactory::getDocument();
        $app    = JFactory::getApplication();

        // Set the default view name and format from the Request.
        $vName		= $this -> input -> get('view', 'articles');

        $vFormat	= $document->getType();
        $lName		= $this -> input -> get('layout', 'default');
        $id			= $this -> input -> getInt('id');

        // Check for edit form.
        if ($vName == 'category' && $lName == 'edit' && !$this->checkEditId('com_tz_portfolio.edit.category', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio&view=categories&extension='.$this->extension, false));

            return false;
        }

        // Get and render the view.
        if ($view = $this->getView($vName, $vFormat)) {
            // Get the model for the view.
            $model = $this->getModel($vName, 'TZ_PortfolioModel', array('name' => $vName . '.' . substr($this->extension, 4)));

            // Push the model into the view (as default).
            $view->setModel($model, true);
            //            if(JRequest::getCmd('task')=='listsfields'){
            //                $this ->
            //            }

            $view->setLayout($lName);

            // Push document object into the view.
            $view->assignRef('document', $document);
            // Load the submenu.
            require_once JPATH_COMPONENT.'/helpers/categories.php';

            //CategoriesHelper::addSubmenu($model->getState('filter.extension'));
            $view->display();
        }

		return $this;
	}
    
}
