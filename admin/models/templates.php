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

//no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class TZ_PortfolioModelTemplates extends JModelList
{
    function populateState($ordering = null, $direction = null){

        parent::populateState('id','desc');

        $search  = $this -> getUserStateFromRequest('com_tz_portfolio.templates.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);

        $order  = $this -> getUserStateFromRequest('com_tz_portfolio.templates.filter_order','filter_order',null,'string');
        $this -> setState('filter_order',$order);

        $orderDir  = $this -> getUserStateFromRequest('com_tz_portfolio.templates.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('t.*,COUNT(xc.template_id) AS content_assigned');
        $query -> from($db -> quoteName('#__tz_portfolio_templates').' AS t');
        $query -> join('LEFT','#__tz_portfolio_xref_content AS xc ON t.id = xc.template_id');
        $query -> group('t.id');

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('COUNT(c.template_id) AS category_assigned');
            $query -> from($db -> quoteName('#__tz_portfolio_templates').' AS t');
            $query -> join('LEFT','#__tz_portfolio_categories AS c ON t.id = c.template_id');
            $query -> group('t.id');
            $db -> setQuery($query);
            if($rows = $db -> loadObjectList()){
                foreach($items as $i => &$item){
                    if(isset($rows[$i] -> category_assigned)){
                        $item -> category_assigned = $rows[$i] -> category_assigned;
                    }
                }
            }

            return $items;
        }
        return false;
    }


}