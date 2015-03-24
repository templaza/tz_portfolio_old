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

jimport('joomla.application.component.controller');

/**
 * Content Component Controller.
 */
class TZ_PortfolioController extends JControllerLegacy
{
    protected $input;
	function __construct($config = array())
	{
        $this->input    = JFactory::getApplication()->input;
        $params         = JFactory::getApplication() -> getParams();

		// Article frontpage Editor pagebreak proxying:
		if (($this->input -> get('view') == 'article' || $this->input -> get('view') == 'p_article')
            && $this->input -> get('layout') == 'pagebreak') {
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
		// Article frontpage Editor article proxying:
		elseif($this->input -> get('view') == 'articles' && $this->input -> get('layout') == 'modal') {
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

         // If the joomla's version is more than or equal to 3.0
        if(!COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            JHtml::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/libraries/cms/html');
            tzportfolioimport('cms/html/sidebar');

            $doc    = JFactory::getDocument();
            //Add Script to the header
            if($params -> get('enable_jquery',1)){
                $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/js/jquery-1.9.1.js');
                $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/js/jquery-noconflict.js');
                $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/js/jquery-migrate-1.2.1.js');
            }
            if($params -> get('enable_bootstrap',1)){
                $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/bootstrap/js/bootstrap.min.js');
                $doc -> addStyleSheet(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/bootstrap/css/bootstrap.min.css');
                $doc -> addStyleSheet(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/bootstrap/css/bootstrap-responsive.min.css');
            }
//            $doc -> addStyleSheet(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/css/tz-portfolio.css');
//            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/chosen.jquery.min.js');
//            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/jquery.ui.core.min.js');
//            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/jquery.ui.sortable.min.js');
//            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/js/sortablelist.js');
//            $doc -> addScript(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/js/template.js');

//            $doc -> addStyleSheet(COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/jui/css/chosen.css');
//            $doc -> addCustomTag('<link href="'.COM_TZ_PORTFOLIO_ADMIN_HOST_PATH.'/css/template.css'.
//                '" rel="stylesheet" type="text/css"/>');
        }

		parent::__construct($config);
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

        JFactory::getLanguage() -> load('com_content');
		$cachable = true;


		JHtml::_('behavior.caption');

		// Set the default view name and format from the Request.
		// Note we are using a_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$id		= $this -> input -> get('a_id');
		$vName	= $this -> input -> get('view', 'categories');

        $this->input->set('view', $vName);

		$user = JFactory::getUser();

		if ($user->get('id') ||
			($_SERVER['REQUEST_METHOD'] == 'POST' &&
				(($vName == 'category' && $this -> input -> get('layout') != 'blog') || $vName == 'archive' ))) {
			$cachable = false;
		}


        $safeurlparams = array('catid' => 'INT', 'id' => 'INT', 'cid' => 'ARRAY', 'year' => 'INT', 'month' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT',
        			'showall' => 'INT', 'return' => 'BASE64', 'filter' => 'STRING', 'filter_order' => 'CMD', 'filter_order_Dir' => 'CMD', 'filter-search' => 'STRING', 'print' => 'BOOLEAN', 'lang' => 'CMD', 'Itemid' => 'INT');

		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_tz_portfolio.edit.article', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		parent::display($cachable, $safeurlparams);

		return $this;
	}
}
