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

    function getList(&$params){
        if($params){
            $catId  = $params -> get('catid');
            if(count($catId) == 1 && empty($catId[0] -> catid))
                $query  = 'SELECT c.*,'
                          .' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug,'
                          .' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'
                          .' FROM #__content AS c'
                          .' LEFT JOIN #__categories AS cc ON cc.id = c.catid'
                          .' WHERE c.state=1'
                          .' AND NOT c.title="Uncategorised"'
                          .' ORDER BY c.id ASC';
            else{
                $catIds = implode(',',$catId);
                $query  = 'SELECT c.*,'
                          .' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug,'
                          .' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'
                          .' FROM #__content AS c'
                          .' WHERE c.state=1'
                          .' AND NOT c.title="Uncategorised"'
                          .' AND c.catid IN('.$catIds.')'
                          .' ORDER BY c.id ASC';
            }

            $db     = &JFactory::getDbo();
            $db -> setQuery($query);

            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                die();
            }

            $randomId   = null;
//            $data       = array();
//            $width  = null;
//            $height = null;
//            $crop   = null;
//            if($params -> get('tz_image_width'))
//                $width = '?width='.$params -> get('tz_image_width');
//            if($params -> get('tz_image_height'))
//                if($width)
//                    $height = '&height='.$params -> get('tz_image_height');
//                else
//                    $height = '?height='.$params -> get('tz_image_height');
//            if($params -> get('tz_image_crop'))
//                if($width || $height)
//                    $crop = '&cropratio='.$params -> get('tz_image_crop');
//                else
//                    $crop = '?cropratio='.$params -> get('tz_image_crop');
//
//
//
//            if($width || $height || $crop)
//                $_src   = '&image='.JURI::base(JPATH_SITE).'/';
//            else
//                $_src   = '?image='.JURI::base(JPATH_SITE).'/';

            if($rows = $db -> loadObjectList()){
                $max    = count($rows) - 1;
                for($i=0;$i<$params -> get('count');$i++){
                    $randomId   = (int) mt_rand(0,$max);
                    $data[$i]   = $rows[$randomId];

                    $data[$i] ->link    = JRoute::_('index.php?option=com_tz_portfolio&view=article&id='
                                                    .$rows[$randomId] -> slug.'&catid='.$rows[$randomId] -> catid);

                    $model  = new TZ_PortfolioModelMedia();

                    if($model && $params -> get('show_tz_image',1)){
                        if($image  = $model -> getMedia($rows[$randomId] -> id)){
                            if($image[0] -> type != 'video'){
                                if(!empty($image[0] -> images)){
                                    if($params -> get('tz_image_size','S')){
                                        $imageName  = $image[0] -> images;
                                        $data[$i] -> tz_image   = str_replace('.'.JFile::getExt($imageName)
                                            ,'_'.$params -> get('tz_image_size').'.'.JFile::getExt($imageName)
                                            ,$imageName);
                                    }

                                }
                            }
                            else{
                                if(!empty($image[0] -> thumb)){
                                    if($params -> get('tz_image_size','S')){
                                        $imageName  = $image[0] -> thumb;
                                        $data[$i] -> tz_image   = str_replace('.'.JFile::getExt($imageName)
                                            ,'_'.$params -> get('tz_image_size').'.'.JFile::getExt($imageName)
                                            ,$imageName);
                                    }
                                }
                            }
                            $data[$i] -> tz_imagetitle = $image[0] -> imagetitle;
                        }
                    }
                }
            }

            return $data;
        }
        return false;

    }

}