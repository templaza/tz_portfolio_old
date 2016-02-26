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

// no direct access
defined('_JEXEC') or die;

//jimport('joomla.application.component.controller');

class TZ_PortfolioControllerPortfolio extends TZ_PortfolioControllerLegacy
{
    public function getModel($name = 'Portfolio', $prefix = 'TZ_PortfolioModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    function ajax(){

        $document   = JFactory::getDocument();
        $viewType   = $document->getType();
        $vName      = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');

        if($view = $this->getView($vName, $viewType)) {

            // Get/Create the model
            if ($model = $this->getModel($vName)) {
                if (!$model->ajax()) {
                    die();
                }

                // Push the model into the view (as default)
                $view->setModel($model, true);
            }

            $view->document = $document;

            JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

            // Display the view
            ob_start();
            $view->display($viewLayout);
            $content    = ob_get_contents();
            ob_end_clean();
            echo str_replace('</script>','<\\/script>',$content);
        }
        die();
    }

    function ajaxtags(){

        $document   = JFactory::getDocument();
        $viewType   = $document->getType();

        if($view = $this->getView('portfolio', $viewType)) {

            // Get/Create the model
            if ($model = $this->getModel('portfolio')) {
                if (!$tags = $model -> ajaxtags()) {
                    die();
                }

                // Push the model into the view (as default)
                $view->setModel($model, true);

                $view -> assign('listsTags',$tags);
            }

            $view->document = $document;

            JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

            // Display the view
            echo $view->loadTemplate('tags');
        }
        die();
    }

    function ajaxcategories(){

        $document   = JFactory::getDocument();
        $viewType   = $document->getType();

        if($view = $this->getView('portfolio', $viewType)) {

            // Get/Create the model
            if ($model = $this->getModel('portfolio')) {
                if (!$catids = $model -> ajaxCategories()) {
                    die();
                }

                // Push the model into the view (as default)
                $view->setModel($model, true);

                $view -> assign('listsCategories',$catids);
            }

            $view->document = $document;

            JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

            // Display the view
            echo $view->loadTemplate('categories');
        }
        die();
    }

    public function ajaxComments(){
        $model  = $this -> getModel();
        echo $model -> ajaxComments();
        die();
    }
}