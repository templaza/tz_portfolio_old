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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.controller');

class TZ_PortfolioControllerLegacy  extends JControllerLegacy{
    private $docOptions = array();

    private $generateLayout = null;

    public function display($cachable = false, $urlparams = array())
    {

        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');

        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

        // Get/Create the model
        if ($model = $this->getModel($viewName))
        {
            // Push the model into the view (as default)
            $view->setModel($model, true);
        }

        $view->document = $document;

        ob_start();
        parent::display($cachable, $urlparams);
        $result = ob_get_contents();
        ob_end_clean();

        $this -> parseDocument($view);

        echo $result;

        return $this;
    }

    public function getView($name = '', $type = '', $prefix = 'TZ_PortfolioView', $config = array())
    {
        $view   = parent::getView($name,$type,$prefix,$config);
        if($view) {
            if($template   = TZ_PortfolioTemplate::getTemplate(true)){
                if($template -> id){
                    $tplparams  = $template -> params;
                    $path       = $view -> get('_path');
                    $last_path  = array_pop($path['template']);

                    $bool_tpl   = false;
                    if(JFolder::exists(COM_TZ_PORTFOLIO_TEMPLATE_PATH.DIRECTORY_SEPARATOR.$template -> template)) {
                        $bool_tpl   = true;
                    }
                    if($bool_tpl) {
                        if($tplparams -> get('override_html_template_site',0)) {
                            if(isset($template -> home_path) && $template -> home_path) {
                                $path['template'][] = $template->home_path . DIRECTORY_SEPARATOR . $name;
                            }
                            if(isset($template -> base_path) && $template -> base_path) {
                                $path['template'][] = $template->base_path . DIRECTORY_SEPARATOR . $name;
                            }
                            $path['template'][] = $last_path;
                            $view -> set('_path',$path);
                        }else{
                            if(isset($template -> home_path) && $template -> home_path) {
                                $view->addTemplatePath($template->home_path . DIRECTORY_SEPARATOR . $name);
                            }
                            $view ->addTemplatePath($template -> base_path . DIRECTORY_SEPARATOR . $name);
                        }

                        $this -> docOptions['template']     = $template->template;
                        $this -> docOptions['file']         = 'template.php';
                        $this -> docOptions['params']       = $template->params;
                        $this -> docOptions['directory']    = COM_TZ_PORTFOLIO_PATH_SITE . DIRECTORY_SEPARATOR . 'templates';
                    }
                }
            }
            return $view;
        }
        return $view;
    }

    public function getModel($name = '', $prefix = 'TZ_PortfolioModel', $config = array())
    {
        return parent::getModel($name,$prefix,$config);
    }

    public function parseDocument(&$view = null){
        if($view && count($this -> docOptions)){
            if(isset($view -> document)){
                if($template   = TZ_PortfolioTemplate::getTemplate()) {

                    // Add template.css file if it has have in template
                    if (JFile::exists(COM_TZ_PORTFOLIO_TEMPLATE_PATH . DIRECTORY_SEPARATOR . $template
                        . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'template.css')
                    ) {
                        $view->document->addStyleSheet(TZ_PortfolioUri::base(true) . '/templates/' . $template . '/css/template.css');
                    }

                    // Parse document of view to require template.php(in tz portfolio template) file.
                    $view->document->parse($this->docOptions);

                    return true;
                }
            }
            return false;
        }
        return false;
    }

//    protected function generateLayout(&$view,&$article,&$params = null,$dispatcher=null,$csscompress=null){
//        JPluginHelper::importPlugin('content');
//
//        $template       = JModelLegacy::getInstance('Template','TZ_PortfolioModel',array('ignore_request' => true));
//        $template -> setState('content.id',$article -> id);
//        $template -> setState('category.id',$article -> catid);
//
//        $theme  = $template -> getItem();
//        $html   = null;
//
//        if($theme){
//            if($tplParams  = $theme ->layout){
//                $view -> document -> addStyleSheet('components/com_tz_portfolio/css/tz.bootstrap.min.css');
//                foreach($tplParams as $tplItems){
//                    $rows   = null;
//
//                    $background = null;
//                    $color      = null;
//                    $margin     = null;
//                    $padding    = null;
//
//                    if($tplItems -> backgroundcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> backgroundcolor))){
//                        $background  = 'background: '.$tplItems -> backgroundcolor.';';
//                    }
//                    if($tplItems -> textcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> textcolor))){
//                        $color      =  'color: '.$tplItems -> textcolor.';';
//                    }
//                    if(isset($tplItems -> margin) && !empty($tplItems -> margin)){
//                        $margin = 'margin: '.$tplItems -> margin.';';
//                    }
//                    if(isset($tplItems -> padding) && !empty($tplItems -> padding)){
//                        $padding = 'padding: '.$tplItems -> padding.';';
//                    }
//                    if($background || $color || $margin || $padding){
//                        $view -> document -> addStyleDeclaration('
//                        #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).'{
//                            '.$background.$color.$margin.$padding.'
//                        }
//                    ');
//                    }
//                    if($tplItems -> linkcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> linkcolor))){
//                        $view -> document -> addStyleDeclaration('
//                            #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).' a{
//                                color: '.$tplItems -> linkcolor.';
//                            }
//                        ');
//                    }
//                    if($tplItems -> linkhovercolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> linkhovercolor))){
//                        $view -> document -> addStyleDeclaration('
//                            #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).' a:hover{
//                                color: '.$tplItems -> linkhovercolor.';
//                            }
//                        ');
//                    }
//                    $rows[] = '<div id="tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).'"'
//                        .' class="tz-container-fluid'.($tplItems -> {"class"}?' '.$tplItems -> {"class"}:'')
//                        .($tplItems -> responsive?' '.$tplItems -> responsive:'').'">';
//                    if(isset($tplItems -> containertype) && $tplItems -> containertype){
//                        $rows[] = '<div class="'.$tplItems -> containertype.'">';
//                    }
//
//                    $rows[] = '<div class="tz-row">';
//                    foreach($tplItems -> children as $children){
//                        $html   = null;
//
//                        if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
//                            || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
//                            || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
//                            || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
//                            || !empty($children -> {"customclass"}) || $children -> responsiveclass){
//                            $rows[] = '<div class="'
//                                .(!empty($children -> {"col-lg"})?'tz-col-lg-'.$children -> {"col-lg"}:'')
//                                .(!empty($children -> {"col-md"})?' tz-col-md-'.$children -> {"col-md"}:'')
//                                .(!empty($children -> {"col-sm"})?' tz-col-sm-'.$children -> {"col-sm"}:'')
//                                .(!empty($children -> {"col-xs"})?' tz-col-xs-'.$children -> {"col-xs"}:'')
//                                .(!empty($children -> {"col-lg-offset"})?' tz-col-lg-offset-'.$children -> {"col-lg-offset"}:'')
//                                .(!empty($children -> {"col-md-offset"})?' tz-col-md-offset-'.$children -> {"col-md-offset"}:'')
//                                .(!empty($children -> {"col-sm-offset"})?' tz-col-sm-offset-'.$children -> {"col-sm-offset"}:'')
//                                .(!empty($children -> {"col-xs-offset"})?' tz-col-xs-offset-'.$children -> {"col-xs-offset"}:'')
//                                .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
//                                .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
//                        }
//
//                        if($children -> type && $children -> type !='none'){
//                            $html   = $view -> loadTemplate($children -> type);
//                            $html   = trim($html);
//                        }
//
//                        $rows[] = $html;
//
//                        if( !empty($children -> children) and is_array($children -> children) ){
//                            $this -> childrenLayout($rows,$children,$article,$params,$dispatcher);
//                        }
//
//                        if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
//                            || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
//                            || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
//                            || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
//                            || !empty($children -> {"customclass"}) || $children -> responsiveclass){
//                            $rows[] = '</div>'; // Close col tag
//                        }
//                    }
//
//                    if(isset($tplItems -> containertype) && $tplItems -> containertype){
//                        $rows[] = '</div>';
//                    }
//                    $rows[] = '</div>';
//                    $rows[] = '</div>';
//                    $this -> generateLayout .= implode("\n",$rows);
//                }
//            }
//        }
//    }
//
//    protected function childrenLayout(&$view,&$rows,$children,&$article,&$params,$dispatcher){
//        foreach($children -> children as $children){
//            $background = null;
//            $color      = null;
//            $margin     = null;
//            $padding    = null;
//
//            if($children -> backgroundcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> backgroundcolor))){
//                $background  = 'background: '.$children -> backgroundcolor.';';
//            }
//            if($children -> textcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> textcolor))){
//                $color      =  'color: '.$children -> textcolor.';';
//            }
//            if(isset($children -> margin) && !empty($children -> margin)){
//                $margin = 'margin: '.$children -> margin.';';
//            }
//            if(isset($children -> padding) && !empty($children -> padding)){
//                $padding = 'padding: '.$children -> padding.';';
//            }
//            if($background || $color){
//                $this -> document -> addStyleDeclaration('
//                    #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner{
//                        '.$background.$color.$margin.$padding.'
//                    }
//                ');
//            }
//            if($children -> linkcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> linkcolor))){
//                $view -> document -> addStyleDeclaration('
//                        #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner a{
//                            color: '.$children -> linkcolor.';
//                        }
//                    ');
//            }
//            if($children -> linkhovercolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> linkhovercolor))){
//                $view -> document -> addStyleDeclaration('
//                        #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner a:hover{
//                            color: '.$children -> linkhovercolor.';
//                        }
//                    ');
//            }
//            $rows[] = '<div id="tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner" class="tz-container-fluid '
//                .$children -> {"class"}.($children -> responsive?' '.$children -> responsive:'').'">';
//            $rows[] = '<div class="tz-row">';
//            foreach($children -> children as $children){
//                $html   = null;
//
//                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
//                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
//                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
//                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
//                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
//                    $rows[] = '<div class="'
//                        .(!empty($children -> {"col-lg"})?'tz-col-lg-'.$children -> {"col-lg"}:'')
//                        .(!empty($children -> {"col-md"})?' tz-col-md-'.$children -> {"col-md"}:'')
//                        .(!empty($children -> {"col-sm"})?' tz-col-sm-'.$children -> {"col-sm"}:'')
//                        .(!empty($children -> {"col-xs"})?' tz-col-xs-'.$children -> {"col-xs"}:'')
//                        .(!empty($children -> {"col-lg-offset"})?' tz-col-lg-offset-'.$children -> {"col-lg-offset"}:'')
//                        .(!empty($children -> {"col-md-offset"})?' tz-col-md-offset-'.$children -> {"col-md-offset"}:'')
//                        .(!empty($children -> {"col-sm-offset"})?' tz-col-sm-offset-'.$children -> {"col-sm-offset"}:'')
//                        .(!empty($children -> {"col-xs-offset"})?' tz-col-xs-offset-'.$children -> {"col-xs-offset"}:'')
//                        .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
//                        .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
//                }
//
//                if($children -> type && $children -> type !='none'){
//                    $html   = $view -> loadTemplate($children -> type);
//                    $html   = trim($html);
//                }
//                $rows[] = $html;
//
//                if( !empty($children -> children) and is_array($children -> children) ){
//                    $this -> childrenLayout($view,$rows,$children,$article,$params,$dispatcher);
//                }
//
//                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
//                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
//                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
//                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
//                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
//                    $rows[] = '</div>'; // Close col tag
//                }
//
//            }
//            $rows[] = '</div>';
//            $rows[] = '</div>';
//        }
//        return;
//    }
}