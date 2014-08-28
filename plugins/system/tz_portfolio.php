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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemTZ_Portfolio extends JPlugin {

    function onAfterInitialise(){
        $app		= JFactory::getApplication();
        
        if($app -> isAdmin()){
            $option     = 'com_tz_portfolio';
            $curOption  = JRequest::getCmd('option');
            $curPath    = JURI::getInstance() -> toString();
            $view       = JRequest::getString('view');
            $extension  = JRequest::getString('extension');
            $newPath    = null;

            if($curOption == 'com_content' || $curOption == 'com_categories'){
                if($curOption == 'com_categories' && $extension == 'com_content')
                    $newPath    = str_replace($curOption,$option,$curPath);
                if($curOption == 'com_content')
                    $newPath    = str_replace($curOption,$option,$curPath);
                if($curOption == 'com_content' && !$view){
                    $newPath    .= '&view=articles';
                }

                if($curOption == 'com_categories' && $extension == 'com_content'){
                    $newPath    = str_replace('&extension='.$extension,'',$newPath);
                    $newPath    .= '&view=categories';
                }

                if($newPath){
                    $app -> redirect($newPath);
                }
            }
        }
    }


    // Extend user forms with TZ Portfolio fields
	function onAfterDispatch() {
        JFactory::getLanguage() -> load('com_tz_portfolio');
        $mainframe = JFactory::getApplication();
        
		if($mainframe->isAdmin())return;

        if($this -> params -> get('override_user_form',1)){
            $option = JRequest::getCmd('option');
            $view = JRequest::getCmd('view');
            $task = JRequest::getCmd('task');
            $layout = JRequest::getCmd('layout');
            $user = JFactory::getUser();

            if($option == 'com_users' && $view == 'registration' && !$layout){

                JLoader::register('UsersController', JPATH_ADMINISTRATOR .'/components/com_users/controller.php');
                $controller = JControllerLegacy::getInstance('Controller',array('name' => 'Users'));

                    $views = $controller->getView($view, 'html');
                    $tplName    = JFactory::getApplication() -> getTemplate();
                    $tplPath    = JPATH_THEMES.DIRECTORY_SEPARATOR.$tplName.DIRECTORY_SEPARATOR.'html'
                                  .DIRECTORY_SEPARATOR.'com_tz_portfolio'.DIRECTORY_SEPARATOR.'users';
                    if(!JFile::exists($tplPath.DIRECTORY_SEPARATOR.'register.php')){
                        $tplPath    = JPATH_SITE.DIRECTORY_SEPARATOR.'components'
                                      .DIRECTORY_SEPARATOR.'com_tz_portfolio'
                                      .DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.'tmpl';
                    }

                    $views -> addTemplatePath($tplPath);

                    $views->setLayout('register');

                    ob_start();
                    $views->display();
                    $contents = ob_get_clean();
                    $document = JFactory::getDocument();
                    $document->setBuffer($contents, 'component');
            }
            if($user -> username && $option == 'com_users'
               && $view == 'profile' && ($layout == 'edit' || $task == 'profile.edit')){

                JLoader::register('UsersController', JPATH_ADMINISTRATOR .'/components/com_users/controller.php');
                $controller = JControllerLegacy::getInstance('Controller',array('name' => 'Users'));

                $views = $controller->getView($view, 'html');

                $tplName    = JFactory::getApplication() -> getTemplate();
                $tplPath    = JPATH_THEMES.DIRECTORY_SEPARATOR.$tplName.DIRECTORY_SEPARATOR.'html'
                              .DIRECTORY_SEPARATOR.'com_tz_portfolio'.DIRECTORY_SEPARATOR.'users';
                if(!JFile::exists($tplPath.DIRECTORY_SEPARATOR.'profile.php')){
                    $tplPath    = JPATH_SITE.DIRECTORY_SEPARATOR.'components'
                                  .DIRECTORY_SEPARATOR.'com_tz_portfolio'
                                  .DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.'tmpl';
                }

                $views -> addTemplatePath($tplPath);

                $views->setLayout('profile');

                JLoader::register('TZ_PortfolioModelUser', JPATH_ADMINISTRATOR .'/components/com_tz_portfolio/models/user.php');
                $model              = JModelAdmin::getInstance('User','TZ_PortfolioModel',array('ignore_request' => true));

                $userData   = $model -> getUsers($user -> id);


                $views -> assign('TZUser',$userData);

                ob_start();
                $active = JFactory::getApplication()->getMenu()->getActive();
                if (isset($active->query['layout']) && $active->query['layout'] != 'profile')
                {
                    $active->query['layout'] = 'profile';
                }
                $views -> assign('user',$user);
                $views->display();
                $contents = ob_get_clean();
                $document = JFactory::getDocument();
                $document->setBuffer($contents, 'component');
            }
        }

    }
    
}
?>