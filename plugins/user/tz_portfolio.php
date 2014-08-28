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

class plgUserTZ_Portfolio extends JPlugin
{
    function plgUserTZ_Portfolio(&$subject, $config) {

        parent::__construct($subject, $config);
    }

    function onUserAfterSave($user, $isnew, $success, $msg){
        return $this -> onAfterSaveUser($user, $isnew, $success, $msg);
    }

//    function onUserBeforeSave($user, $isNew){
//    	return $this->onBeforeStoreUser($user, $isNew);
//    }
//
//    function onBeforeSaveUser($user,$isNew){
//        //var_dump($isNew,'<hr>',$user); die();
//    }

    function onAfterSaveUser($user, $isnew, $success, $msg){

        $mainframe = JFactory::getApplication();
        $task = JRequest::getCmd('task');

        if($mainframe->isSite() && $task != 'activate'){

            JLoader::register('TZ_PortfolioModelUser', JPATH_ADMINISTRATOR .'/components/com_tz_portfolio/models/user.php');
            $model              = JModelAdmin::getInstance('User','TZ_PortfolioModel',array('ignore_request' => true));

            $avatar             = JRequest::getVar('jform','','files','array');
            $description        = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWHTML );
            $deleteImage        = JRequest::getCmd('delete_images');
            $currentImage       = JRequest::getString('current_images');
            $userData['url']    = JRequest::getVar( 'url', '', 'post', 'string' );


            $description        = trim($description);
            $userData['usersid']        = $user ['id'];
            $userData['gender']         = JRequest::getCmd('gender');
            $userData['description']    = $description;
            $userData['twitter']        = JRequest::getVar( 'url_twitter', '', 'post', 'string' );
            $userData['facebook']       = JRequest::getVar( 'url_facebook', '', 'post', 'string' );
            $userData['google_one']     = JRequest::getVar( 'url_google_one_plus', '', 'post', 'string' );
            $image  = null;


            if(!$userData['gender'])
                $userData['gender'] = 'm';

            if(!empty($avatar['name']['client_images'])){
                $image  = $avatar;
            }
            else{
                if(!empty($data['url_images']))
                    $image  = $data['url_images'];
            }

            if($image){
                $model -> deleteImages($currentImage);

                if(!$userData['images'] = $model -> uploadImages($image)){
                    $this -> setError($this -> getError());
                    return false;
                }
            }
            else
                $userData['images'] = $currentImage;



            if($deleteImage == 1){
                $model -> deleteImages($currentImage);
                $userData['images'] = '';
            }

            if(!empty($userData['url']) || !empty($userData['description']) || !empty($userData['twitter'])
                || !empty($userData['facebook']) || !empty($userData['google_one']) || !empty($userData['images'])){
                if(!$model -> saveUser($userData)){
                    $this -> setError($this -> getError());
                    return false;
                }
            }

        }

    }

}
