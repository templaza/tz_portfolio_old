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

require_once('components'.DIRECTORY_SEPARATOR.'com_tz_portfolio'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'route.php');
require_once('components'.DIRECTORY_SEPARATOR.'com_tz_portfolio'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'media.php');

class modTZ_PortfolioRandomHelper{

    static function getList(&$params){
        if($params){
            $catId  = $params -> get('catid',array());
            if(empty($catId[0])){
                array_shift($catId);
            }
            $catId  = implode(',',$catId);
            $db     = JFactory::getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('c.*');
            $query -> select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
            $query -> select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
            $query -> from('#__content AS c');
            $query -> join('LEFT','#__categories AS cc ON cc.id=c.catid');
            $query -> where('c.state=1');
            if($catId){
                $query -> where('c.catid IN('.$catId.')');
            }
            $query -> order('RAND()');
            $query -> order('c.id ASC');
            $db -> setQuery($query,0,$params -> get('count',5));

            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                die();
            }


            if($rows = $db -> loadObjectList()){
                $model  = new TZ_PortfolioModelMedia();
                foreach($rows as $item){
                    if($params -> get('redirect','article') == 'p_article'){
                        $item ->link    = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($item -> slug, $item -> catslug));
                    }
                    else{
                        $item ->link    = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug, $item -> catslug));
                    }

                    if($model && $params -> get('show_tz_image',1)){
                        if($image  = $model -> getMedia($item -> id)){
                            if($image[0] -> type != 'quote' && $image[0] -> type != 'link'){
                                if($image[0] -> type != 'video'){
                                    if(!empty($image[0] -> images)){
                                        if($params -> get('tz_image_size','S')){
                                            $imageName  = $image[0] -> images;
                                            $item -> tz_image   = str_replace('.'.JFile::getExt($imageName)
                                                ,'_'.$params -> get('tz_image_size').'.'.JFile::getExt($imageName)
                                                ,$imageName);
                                        }

                                    }
                                }
                                else{
                                    if(!empty($image[0] -> thumb)){
                                        if($params -> get('tz_image_size','S')){
                                            $imageName  = $image[0] -> thumb;
                                            $item -> tz_image   = str_replace('.'.JFile::getExt($imageName)
                                                ,'_'.$params -> get('tz_image_size').'.'.JFile::getExt($imageName)
                                                ,$imageName);
                                        }
                                    }
                                }
                                $item -> tz_imagetitle = $image[0] -> imagetitle;
                            }else{
                                $item -> quote_author   = null;
                                if(isset($image[0] -> quote_author)){
                                    $item -> quote_author   = $image[0] -> quote_author;
                                }
                                if(isset($image[0] -> quote_text)){
                                    $item -> quote_text = $image[0] -> quote_text;
                                }
                                if(isset($image[0] -> link_title)){
                                    $item -> link_title = $image[0] -> link_title;
                                }
                                if(isset($image[0] -> link_url)){
                                    $item -> link_url   = $image[0] -> link_url;
                                }
                                if(isset($image[0] -> link_target)){
                                    $item -> link_target    = $image[0] -> link_target;
                                }
                                if(isset($image[0] -> link_follow)){
                                    $item -> link_follow    = $image[0] -> link_follow;
                                }
                            }
                        }
                    }
                }

                return $rows;
            }
            return false;
        }
        return false;

    }

}