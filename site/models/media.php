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
jimport('joomla.filesystem.file');

class TZ_PortfolioModelMedia extends JModelLegacy
{

    public $_params  = null;

    function populateState(){
        $pk = JRequest::getInt('id');
        $this -> setState('article.id',$pk);

        $user   = JFactory::getUser();
        if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
            // limit to published for people who can't edit or edit.state.
            $this->setState('filter.published', 1);
        }
        else {
            $this->setState('filter.published', array(0, 1, 2));
        }

        $this -> setState('params',null);
    }

    function getCatParams($catid = null){
        if($catid){
            $query  = 'SELECT params FROM #__categories'
                      .' WHERE id='.(int) $catid;
            $db     = JFactory::getDbo();
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

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);

        $query -> select('c.featured,xc.*,c.catid,c.attribs AS param,c.title AS c_title');

        $query -> from('#__tz_portfolio_xref_content AS xc');

        $query -> join('LEFT','#__content AS c ON c.id=xc.contentid');

        // Filter by published state
        $published = $this->getState('filter.published');

        if (is_numeric($published)) {
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state = ' . (int) $published);
        }
        elseif (is_array($published)) {
            JArrayHelper::toInteger($published);
            $published = implode(',', $published);
            // Use article state if badcats.id is null, otherwise, force 0 for unpublished
            $query->where('c.state IN ('.$published.')');
        }

        $query -> where('xc.contentid='.$articleId);

//        $query  = 'SELECT c.featured,xc.*,c.catid,c.attribs AS param FROM #__tz_portfolio_xref_content AS xc'
//                  .' LEFT JOIN #__content AS c ON c.id=xc.contentid'
//                  .' WHERE c.state=1 AND xc.contentid='.$articleId;
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }

        if($rows   = $db -> loadObject()){
            $params = new JRegistry();
            $params -> loadString($rows -> param);

//            $params -> merge($this -> getCatParams($rows -> catid));

            if($this -> _params)
                $params -> merge($this -> _params);

            if($_params = $this -> getState('params')){
                $params = $_params;
            }

            if(!empty($rows -> type)){

                switch (trim(strtolower($rows -> type))){
                    case 'image':
                        $data[0]    = new stdClass();
                        $data[0] -> type           = strtolower($rows -> type);
                        $data[0] -> featured       = $rows -> featured;
                        $data[0] -> images         = $rows -> images;
                        $data[0] -> imagetitle     = '';

                        if(isset($rows -> imagetitle) && !empty($rows -> imagetitle)){
                            $data[0] -> imagetitle     = htmlspecialchars($rows -> imagetitle);
                        }elseif(isset($rows -> c_title) && !empty($rows -> c_title)){
                            $data[0] -> imagetitle     = $rows -> c_title;
                        }
                        $data[0] -> images_hover    = $rows -> images_hover;
                        $data[0] -> articleId   = $articleId;
                        break;
                    case 'imagegallery':
                        if(!empty($rows -> gallery)){
                            $gallery    = explode('///',$rows -> gallery);
                            $title      = explode('///',$rows -> gallerytitle);
                            foreach($gallery as $i => $item){
                                $data[$i]    = new stdClass();
                                $data[$i] -> type           = strtolower($rows -> type);
                                $data[$i] -> featured       = $rows -> featured;
                                $data[$i] -> images         = $item;
                                $data[$i] -> imagetitle     = '';

                                if(isset($title[$i]) && !empty($title[$i])){
                                    $data[$i] -> imagetitle     = htmlspecialchars(trim($title[$i]));
                                }
//                                elseif(isset($rows -> c_title) && !empty($rows -> c_title)){
//                                    $data[$i] -> imagetitle     = $rows -> c_title;
//                                }

                                $data[$i] -> articleId   = $articleId;
                            }
                        }
                        break;
                    case 'video':
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

                                $data[0]    = new stdClass();
                                $data[0] -> type        = $rows -> type;
                                $data[0] -> featured    = $rows -> featured;
                                $data[0] -> images      = substr($rows -> video,$pos + 1,strlen($rows -> video));
                                $data[0] -> from        = substr($rows -> video,0,$pos);
                                $data[0] -> thumb       = $rows -> videothumb;
                                $data[0] -> imagetitle  = '';

                                if(isset($rows -> videotitle) && !empty($rows -> videotitle)){
                                    $data[0] -> imagetitle  = htmlspecialchars($rows -> videotitle);
                                }elseif(isset($rows -> c_title) && !empty($rows -> c_title)){
                                    $data[0] -> imagetitle  = $rows -> c_title;
                                }
                            }
                        }
                        break;
                    case 'audio':
                        if(!empty($rows -> audio)){
                            $data[0]    = new stdClass();
                            $data[0] -> type        = $rows -> type;
                            $data[0] -> featured    = $rows -> featured;
                            $data[0] -> audio_id    = $rows -> audio;
                            $data[0] -> thumb       = $rows -> audiothumb;
                            $data[0] -> imagetitle  = '';

                            if(isset($rows -> audiotitle) && !empty($rows -> audiotitle)){
                                $data[0] -> imagetitle  = htmlspecialchars($rows -> audiotitle);
                            }elseif(isset($rows -> c_title) && !empty($rows -> c_title)){
                                $data[0] -> imagetitle  = $rows -> c_title;
                            }
                        }
                        break;
                    case 'quote':
                        $data[0]    = new stdClass();
                        $data[0] -> type            = $rows -> type;
                        $data[0] -> featured        = $rows -> featured;
                        $data[0] -> quote_author    = $rows -> quote_author;
                        $data[0] -> quote_text      = $rows -> quote_text;
                        break;
                    case 'link':
                        $data[0]    = new stdClass();
                        $data[0] -> type            = $rows -> type;
                        $data[0] -> featured        = $rows -> featured;
                        $data[0] -> link_title      = $rows -> link_title;
                        $data[0] -> link_url        = $rows -> link_url;
                        $data[0] -> link_target     = '';
                        $data[0] -> link_follow     = '';
                        if($rows -> link_attribs){
                            $linkParams  = new JRegistry($rows -> link_attribs);
                            $data[0] -> link_target     = $linkParams -> get('link_target','');
                            $data[0] -> link_follow     = $linkParams -> get('link_follow','');
                        }
                        break;
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