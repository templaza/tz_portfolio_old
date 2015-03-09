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
class TZ_PortfolioModelTag extends JModelLegacy
{
    function populateState(){
        $app    = JFactory::getApplication();
        $params = $app -> getParams();
        $this -> setState('params',$params);

        $pk = JRequest::getInt('id');
        $this -> setState('article.id',$pk);
        $this -> setState('list.ordering',null);
        $this -> setState('list.direction', null);

    }

    function getTag(){
//        $query  = 'SELECT t.* FROM #__tz_portfolio_tags AS t'
//            .' INNER JOIN #__tz_portfolio_tags_xref AS x ON t.id=x.tagsid'
//            .' WHERE t.published=1 AND x.contentid='.$this -> getState('article.id');

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('t.*,x.contentid');
        $query -> from($db -> quoteName('#__tz_portfolio_tags').' AS t');
        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_tags_xref').' AS x ON t.id=x.tagsid');
        $query -> where('t.published = 1');
        if($pk  = $this -> getState('article.id')){
            if(is_array($pk)){
                $query -> where('x.contentid IN('.implode(',',$pk).')');
            }else{
                $query -> where('x.contentid = '.$pk);
            }
        }
        if($order   = $this -> getState('list.ordering')){
            $query -> order($order,$this -> getState('list.direction','ASC'));
        }
        $db -> setQuery($query);

        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }

        $rows   = $db -> loadObjectList();

        if(count($rows)>0){
            return $rows;
        }
        return false;
    }

    function getArticleTags(){
        if($tags = $this -> getTag()){
            $new_tags   = array();
            $_new_tags  = array();
            $tagIds     = array();
            foreach($tags as &$tag){
                $itemId = $this -> FindItemId($tag -> id);
                $tag ->link = JRoute::_('index.php?option=com_tz_portfolio&view=tags&id='.$tag -> id.'&Itemid='.$itemId);

                if(!isset($new_tags[$tag -> contentid])){
                    $new_tags[$tag -> contentid][]  = $tag;
                    $_new_tags[$tag -> contentid][] = $tag -> id;
                }else{
                    if(!in_array($tag -> id,$_new_tags[$tag->contentid])) {
                        $new_tags[$tag->contentid][]    = $tag;
                    }
                }
            }
            return $new_tags;
        }
        return false;
    }

    protected function FindItemId($_tagid=null)
    {
        $tagid      = null;
        $app		= JFactory::getApplication();
        $menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $params     = $this -> getState('params');

        if($_tagid){
            $tagid    = intval($_tagid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio');
        $items		= $menus->getItems('component_id', $component->id);

        if($params -> get('menu_active') && $params -> get('menu_active') != 'auto'){
            return $params -> get('menu_active');
        }

        foreach ($items as $item)
        {

            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];


                if (isset($item->query['id'])) {
                    if ($item->query['id'] == $tagid) {
                        return $item -> id;
                    }
                } else {
                    $catids = $item->params->get('tz_catid');
                    if ($view == 'tags' && $catids) {
                        if (is_array($catids)) {
                            for ($i = 0; $i < count($catids); $i++) {
                                if ($catids[$i] == 0 || $catids[$i] == $tagid) {
                                    return $item -> id;
                                }
                            }
                        } else {
                            if ($catids == $tagid) {
                                return $item -> id;
                            }
                        }
                    }
                }
            }
        }

        return $active -> id;
    }
}