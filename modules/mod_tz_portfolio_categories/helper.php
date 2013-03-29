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

require_once JPATH_SITE.'/components/com_tz_portfolio/helpers/route.php';

jimport('joomla.application.component.model');

abstract class modTZ_PortfolioCategoriesHelper
{
  public static function getList(&$params){
      $crop = $params->get('crop');
      $width = $params->get('width');
      $height = $params->get('height');
      $categoryName   = null;
      $total          = null;
      $catIds         = null;
      if($params -> get('catid'))
          $catIds         = implode(',',$params -> get('catid'));
      if($catIds){
          $categoryName   = strtolower(modTZ_PortfolioCategoriesHelper::getCategoryName($catIds));
      }
      if($params -> get('show_total',1))
          $total  = ',(SELECT COUNT(*) FROM #__content AS c WHERE c.catid = a.id) AS total';

      if($categoryName == strtolower('Uncategorised'))
          $query  = 'SELECT a.id, a.title, a.alias, a.note, a.published, a.access,'
                    .' a.checked_out, a.checked_out_time, a.created_user_id, a.path,'
                    .' a.parent_id, a.description, a.params, a.level, a.lft, a.rgt, a.language,'
                    .'l.title AS language_title,ag.title AS access_level,'
                    .'ua.name AS author_name'
                    .$total
                    .' FROM #__categories AS a'
                    .' LEFT JOIN `#__languages` AS l ON l.lang_code = a.language'
                    .' LEFT JOIN #__users AS uc ON uc.id=a.checked_out'
                    .' LEFT JOIN #__viewlevels AS ag ON ag.id = a.access'
                    .' LEFT JOIN #__users AS ua ON ua.id = a.created_user_id'
                    .' WHERE a.extension = \'com_content\' AND (a.published = 1)'
                    .' AND NOT a.title="Uncategorised"'
                    .' GROUP BY a.id'
                    .' ORDER BY a.lft asc';
      else
          $query  = 'SELECT a.id, a.description, a.params, a.title, a.alias, a.note, a.published, a.access,'
                    .' a.checked_out, a.checked_out_time, a.created_user_id, a.path,'
                    .' a.parent_id, a.level, a.lft, a.rgt, a.language,'
                    .'l.title AS language_title,ag.title AS access_level,'
                    .'ua.name AS author_name'
                    .$total
                    .' FROM #__categories AS a'
                    .' LEFT JOIN `#__languages` AS l ON l.lang_code = a.language'
                    .' LEFT JOIN #__users AS uc ON uc.id=a.checked_out'
                    .' LEFT JOIN #__viewlevels AS ag ON ag.id = a.access'
                    .' LEFT JOIN #__users AS ua ON ua.id = a.created_user_id'
                    .' WHERE a.extension = \'com_content\' AND (a.published = 1)'
                    .' AND a.id IN('.$catIds.')'
                    .' GROUP BY a.id'
                    .' ORDER BY a.lft asc';

      $db     = JFactory::getDbo();
      $db -> setQuery($query);
      if($items   = $db -> loadObjectList()){
          $i=0;
          foreach($items as $item){
              $items[$i] ->link   = 'index.php?option=com_tz_portfolio&view=category&id='
                                    .$item -> id.'&Itemid='.JRequest::getInt('Itemid');
              $registry = new JRegistry;
              $registry->loadString($item->params);
              $images = $registry->toArray();
              if(isset($images['image']))
                $imglink = $images['image'];
              if($crop){
              $items[$i]->images = modTZ_PortfolioCategoriesHelper::tz_resizeImgcrop($imglink, $width, $height,$crop);
              } else{
              $items[$i]->images = modTZ_PortfolioCategoriesHelper::tz_resizeImg($imglink, $width, $height);
              }
              $i++;
          }
          return $items;
      }
      return false;
  }

  public static function getCategoryName($catIds = array()){
      if($catIds && count($catIds) == 1){
          $query  = 'SELECT title FROM #__categories'
                    .' WHERE extension="com_content" AND id='.(int)$catIds[0];
          $db     = JFactory::getDbo();
          $db -> setQuery($query);
          if($db -> query()){
              $rows   = $db -> loadObject();

              if($rows)
                return $rows -> title;
          }
      }
      return false;
  }

  function tz_resizeImgcrop($imglink, $width, $height,$crop)
    {
        $img = new stdClass();
        $img->src = $imglink;
        $root_url = parse_url(JURI::base());
        if ($height != "") {
            $height1 = '&amp;height=' . $height;
        } else {
            $height1 = "";
        }
      $crop1 = '&amp;cropratio=' . $crop;

        $image = 'modules/mod_tz_portfolio_categories/image.php?width=' . $width . $height1 . $crop1 . '&amp;image=' . $root_url ['path'] . $img->src;


        return $image;

    }

  function tz_resizeImg($imglink, $width, $height)
    {
        $img = new stdClass();
        $img->src = $imglink;
        $root_url = parse_url(JURI::base());
        if ($height != "") {
            $height1 = '&amp;height=' . $height;
        } else {
            $height1 = "";
        }

        $image = 'modules/mod_tz_portfolio_categories/image.php?width=' . $width . $height1 . '&amp;image=' . $root_url ['path'] . $img->src;


        return $image;

    }

}
?>
