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

jimport('joomla.application.component.model');

class TZ_PortfolioModelMedia extends JModelLegacy
{

    public $_params  = null;

    function populateState(){
        $pk = JRequest::getInt('id');
        $this -> setState('article.id',$pk);
    }

    function getCatParams($catid = null){
        if($catid){
            $query  = 'SELECT params FROM #__categories'
                      .' WHERE id='.(int) $catid;
            $db     = &JFactory::getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                die();
            }
            if($rows   = $db -> loadObject()){
                $params = new JRegistry();
                $params -> loadString($rows -> params);
                return $params;
            }
        }
        return false;
    }

    public function getMedia($articleId=null){

        if(!$articleId)
            $articleId  = $this -> getState('article.id');
        
        $data   = array();

        $query  = 'SELECT c.featured,xc.*,c.catid,c.attribs AS param FROM #__tz_portfolio_xref_content AS xc'
                  .' LEFT JOIN #__content AS c ON c.id=xc.contentid'
                  .' WHERE c.state=1 AND xc.contentid='.$articleId;
        $db = &JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }

        if($rows   = $db -> loadObject()){
            $params = new JRegistry();
            $params -> loadString($rows -> param);

            $params -> merge($this -> getCatParams($rows -> catid));

            if($this -> _params)
                $params -> merge($this -> _params);

            if(!empty($rows -> type)){
                $data[0] -> type           = strtolower($rows -> type);

                if($rows -> type == 'image'){
                    $data[0] -> type           = strtolower($rows -> type);
                    $data[0] -> featured       = $rows -> featured;
                    $data[0] -> images         = $rows -> images;
                    $data[0] -> imagetitle     = $rows -> imagetitle;
                    $data[0] -> images_hover    = $rows -> images_hover;
                    $data[0] -> articleId   = $articleId;

//                    if($params -> get('detail_article_image_size'))
//                        $params -> set('article_image_resize',$params -> get('detail_article_image_size'));
//                    if($params -> get('article_leading_image_size'))
//                        $params -> set('article_leading_image_resize',$params -> get('article_leading_image_size'));
//                    if($params -> get('article_secondary_image_size'))
//                        $params -> set('article_secondary_image_resize',$params -> get('article_secondary_image_size'));
                }
                if(strtolower($rows -> type) == 'imagegallery'){
                    if(!empty($rows -> gallery)){
                        $gallery    = explode('///',$rows -> gallery);
                        $title      = explode('///',$rows -> gallerytitle);
                        foreach($gallery as $i => $item){
                            $data[$i] -> type           = strtolower($rows -> type);
                            $data[$i] -> featured       = $rows -> featured;
                            $data[$i] -> images         = $item;
                            if(isset($title[$i]))
                                $data[$i] -> imagetitle     = trim($title[$i]);
                            else
                                $data[$i] -> imagetitle     = '';

                            $data[$i] -> articleId   = $articleId;
                        }
                    }
                }
                if(trim($rows -> type) == 'video'){
                    if(!empty($rows -> video)){
                        // Video
                        if(preg_match('/.*:.*/i',$rows -> video,$match)){
                            // Embed code
                            for($i = 0; $i<strlen($rows -> video); $i ++){
                                if(substr($rows -> video,$i,1) == ':'){
                                    $pos    = $i;
                                    break;
                                }
                            }

                            $data[0] -> type        = $rows -> type;
                            $data[0] -> featured    = $rows -> featured;
                            $data[0] -> images      = substr($rows -> video,$pos + 1,strlen($rows -> video));
                            $data[0] -> from        = substr($rows -> video,0,$pos);
                            $data[0] -> imagetitle  = $rows -> videotitle;
                            $data[0] -> thumb       = $rows -> videothumb;
                        }
                    }
                }
            }
        }
        
        return $data;
    }

    function setParams($params){
        if($params && count($params)>0){
            $this -> _params    = $params;
        }
    }
}