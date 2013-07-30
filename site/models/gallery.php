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
jimport('joomla.html.pagination');

class TZ_PortfolioModelGallery extends JModelLegacy
{
    function populateState(){
        $app    = JFactory::getApplication();
        $params = $app -> getParams();
//        $params -> merge($app -> getParams());
        $this -> setState('params',$params);
    }
    function getArticle(){

        $params = $this -> getState('params');

        $catids = $params -> get('tz_catid');

        if(count($catids) > 1){
            if(empty($catids[0])){
                array_shift($catids);
            }
            $catids = implode(',',$catids);
        }
        else{
            if(!empty($catids[0])){
                $catids = $catids[0];
            }
            else
                $catids = null;
        }

        $where  = null;
        if($catids){
            $where  = ' AND c.catid IN('.$catids.')';
        }

        if($params -> get('use_filter_first_letter',0)){
            $letters    = null;
            if($_letters = $params -> get('tz_letters')){
                $letters    = explode(',',$_letters);
                if(is_array($letters)){
                    foreach($letters as $i => &$arr){
                        if(!trim($arr)){
                            unset($letters[$i]);
                        }
                        $arr    = 'LOWER( c.title ) LIKE "'.mb_strtolower($arr).'%"';
                    }

                }
                $letters    = implode(' OR ',$letters);
                $where      .= ' AND ('.$letters.')';
            }
        }

        $total      = null;
        $limit      = $this -> getState('limit');
        $limitstart = $this -> getState('offset');
        $data       = array();

        $this->setState('params', $params);

        $query  = 'SELECT c.*'
                  .' FROM #__content AS c'
                  .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
                  .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.contentid=c.id'
                  .' LEFT JOIN #__tz_portfolio_tags AS t ON t.id=x.tagsid'
                  .' WHERE c.state=1'
                  .$where
                  .' GROUP BY c.id';
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if($db -> query())
            $total  = $db -> getNumRows($db -> query());
        else
            $total  = 0;

        $this -> pagNav = new JPagination($total,$limitstart,$limit);

        switch ($params -> get('orderby_pri')){
            default:
                $cateOrder  = null;
                break;
            case 'alpha' :
				$cateOrder = 'cc.path, ';
				break;

			case 'ralpha' :
				$cateOrder = 'cc.path DESC, ';
				break;

			case 'order' :
				$cateOrder = 'cc.lft, ';
				break;
        }
        
        switch ($params -> get('orderby_sec')){
            default:
                $orderby    = 'id DESC';
                break;
            case 'rdate':
                $orderby    = 'created DESC';
                break;
            case 'date':
                $orderby    = 'created ASC';
                break;
            case 'alpha':
                $orderby    = 'title ASC';
                break;
            case 'ralpha':
                $orderby    = 'title DESC';
                break;
            case 'author':
                $orderby    = 'create_by ASC';
                break;
            case 'rauthor':
                $orderby    = 'create_by DESC';
                break;
            case 'hits':
                $orderby    = 'hits DESC';
                break;
            case 'rhits':
                $orderby    = 'hits ASC';
                break;
            case 'order':
                $orderby    = 'ordering ASC';
                break;
        }

        $query  = 'SELECT c.*,cc.title AS category_title,u.name AS author,'
                  .' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug,'
                  .' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug,'
                  .' CASE WHEN CHAR_LENGTH(c.fulltext) THEN c.fulltext ELSE null END as readmore'
                  .' FROM #__content AS c'
                  .' LEFT JOIN #__categories AS cc ON cc.id=c.catid'
                  .' LEFT JOIN #__users AS u ON u.id=c.created_by'
                  .' WHERE c.state=1'
                  .$where
                  .' GROUP BY c.id'
                  .' ORDER BY '.$cateOrder.$orderby;

        if($params -> get('tz_portfolio_layout') == 'default')
            $db -> setQuery($query);
        else
            $db -> setQuery($query,$limitstart,$limit);

        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }

        $rows   = $db -> loadObjectList();
        $model  = JModelLegacy::getInstance('Media','TZ_PortfolioModel');

        $contentId  = array();
        if(count($rows)>0){
            foreach($rows as $key => $item){

//                $contentUrl =JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($item -> slug,$item -> catid), true ,-1);
//                $url    = 'http://graph.facebook.com/?ids='.$contentUrl;
//
//                if($params -> get('tz_show_count_comment',1) == 1){
//                    $face       = new Services_Yadis_PlainHTTPFetcher();
//                    $content    = $face -> get($url);
//                    if($content)
//                        $content    = json_decode($content -> body);
//
//                    if(isset($content -> $contentUrl -> comments))
//                        $item -> commentCount   = $content -> $contentUrl  -> comments;
//                    else
//                        $item -> commentCount   = 0;
//                }

                if($model){
                    if($image  = $model -> getMedia($rows[$key] -> id)){
                        if($image[0] -> type != 'quote' && $image[0] -> type != 'link'){
                            if($image[0] -> type == 'video')
                                $rows[$key] -> tz_image_type = $image[0] -> type;
                            else
                                $rows[$key] -> tz_image_type = null;

                            if(isset($image[0] -> images) && JFile::exists(JURI::base(JPATH_SITE).'/'.$image[0] -> images))
                                $rows[$key] -> tz_image = JURI::base(JPATH_SITE).'/'.$image[0] -> images;
                            else
                                $rows[$key] -> tz_image = null;
                        }
                    }
                }

                if(!isset($rows[$key] -> tz_image))
                    $rows[$key] -> tz_image = JURI::base(JPATH_SITE).'/'.'components/com_tz_portfolio/assets/no_image.png';

                //Get Catid
                $this -> categories[]   = $item -> catid;

                if ($params->get('show_intro', '1')=='1') {
                    $rows[$key] -> text = $rows[$key] -> introtext;
                }
                $dispatcher	= JDispatcher::getInstance();
                    //
                // Process the content plugins.
                //
                JPluginHelper::importPlugin('content');
                $results = $dispatcher->trigger('onContentPrepare', array ('com_tz_portfolio.portfolio', &$rows[$key], &$params, $this -> getState('offset')));
//
                $rows[$key]->event = new stdClass();
                $results = $dispatcher->trigger('onContentAfterTitle', array('com_tz_portfolio.portfolio', &$rows[$key], &$params, $this -> getState('offset')));
                $rows[$key]->event->afterDisplayTitle = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_tz_portfolio.portfolio', &$rows[$key], &$params, $this -> getState('offset')));
                $rows[$key]->event->beforeDisplayContent = trim(implode("\n", $results));

                $results = $dispatcher->trigger('onContentAfterDisplay', array('com_tz_portfolio.portfolio', &$rows[$key], &$params, $this -> getState('offset')));
                $rows[$key]->event->afterDisplayContent = trim(implode("\n", $results));
            }

            return $rows;
        }
    }
}
 
