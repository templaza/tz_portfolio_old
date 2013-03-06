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
        $pk = JRequest::getInt('id');
        $this -> setState('article.id',$pk);
    }

    function getTag(){
        $query  = 'SELECT t.* FROM #__tz_portfolio_tags AS t'
            .' INNER JOIN #__tz_portfolio_tags_xref AS x ON t.id=x.tagsid'
            .' WHERE t.published=1 AND x.contentid='.$this -> getState('article.id');

        $db     = JFactory::getDbo();
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
}