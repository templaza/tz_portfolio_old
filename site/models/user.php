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
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class TZ_PortfolioModelUser extends JModelLegacy
{
    function populateState(){
        $pk = JRequest::getInt('id');
        $this -> setState('article.id',$pk);
    }

    function getUser(){
        $articleId  = JRequest::getInt('id');
        $query  = 'SELECT u.id,u.name,u.email,tu.images,tu.url,tu.gender,tu.description,'
                      .'tu.twitter,tu.facebook,tu.google_one FROM #__users AS u'
                  .' LEFT JOIN #__tz_portfolio_users AS tu ON tu.usersid=u.id'
                  .' LEFT JOIN #__content AS c ON c.created_by=u.id'
                  .' WHERE c.id='.$articleId;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }

        $rows   = $db -> loadObject();
        if(count($rows)>0){
            $rows -> description    = $rows -> description;
            if($rows -> google_one AND !empty($rows -> google_one)){
                if(preg_match('/.*?(\?|\&).*?/',$rows -> google_one)){
                    $rows -> google_one .= '&rel=author';
                }
                else{
                    $rows -> google_one .= '?rel=author';
                }
            }
            return $rows;
        }
        return false;
    }

    function getUserId($id=null){
        if($id){
            $query  = 'SELECT u.id,u.name,u.email,tu.images,tu.url,tu.gender,tu.description,'
                      .'tu.twitter,tu.facebook,tu.google_one FROM #__users AS u'
                      .' LEFT JOIN #__tz_portfolio_users AS tu ON tu.usersid=u.id'
                      .' WHERE u.id='.$id;
            $db     = JFactory::getDbo();
            $db -> setQuery($query);
            
            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                return false;
            }

            $rows   = $db -> loadObject();
            if(count($rows)>0){
                $rows -> description    = $rows -> description;
                if($rows -> google_one AND !empty($rows -> google_one)){
                    if(preg_match('/.*?(\?|\&).*?/',$rows -> google_one)){
                        $rows -> google_one .= '&rel=author';
                    }
                    else{
                        $rows -> google_one .= '?rel=author';
                    }
                }

                return $rows;
            }
        }
        return false;
    }
}
