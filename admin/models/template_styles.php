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

class TZ_PortfolioModelTemplate_Styles extends JModelList
{
    function populateState($ordering = null, $direction = null){

        parent::populateState('id','desc');

        $search  = $this -> getUserStateFromRequest('com_tz_portfolio.styles.filter_search','filter_search',null,'string');
        $this -> setState('filter_search',$search);

        $order  = $this -> getUserStateFromRequest('com_tz_portfolio.styles.filter_order','filter_order',null,'string');
        $this -> setState('filter_order',$order);

        $orderDir  = $this -> getUserStateFromRequest('com_tz_portfolio.styles.filter_order_Dir','filter_order_Dir','asc','string');
        $this -> setState('filter_order_Dir',$orderDir);
    }

    function getListQuery(){
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('t.*');
        $query -> select('(SELECT COUNT(xc2.template_id) FROM #__tz_portfolio_templates AS t2'
            .' INNER JOIN #__tz_portfolio_xref_content AS xc2 ON t2.id = xc2.template_id WHERE t.id = t2.id)'
            .' AS content_assigned');
        $query -> select('(SELECT COUNT(c2.template_id) FROM #__tz_portfolio_templates AS t3'
            .' INNER JOIN #__tz_portfolio_categories AS c2 ON t3.id = c2.template_id WHERE t.id = t3.id)'
            .' AS category_assigned');
        $query -> from($db -> quoteName('#__tz_portfolio_templates').' AS t');
        $query -> join('INNER',$db -> quoteName('#__tz_portfolio_extensions').' AS e ON t.template = e.name');
        $query -> join('LEFT','#__tz_portfolio_xref_content AS xc ON t.id = xc.template_id');
        $query -> join('LEFT','#__tz_portfolio_categories AS c ON t.id = c.template_id');
        $query -> where('e.published = 1');
        $query -> group('t.id');

        return $query;
    }

    public function getItems(){
        if($items = parent::getItems()){
            $component  = JComponentHelper::getComponent('com_tz_portfolio');
            $menus  = JMenu::getInstance('site');
            $menu_assigned  = array();
            if($menu_items  = $menus -> getItems(array('component_id'),$component -> id)){
                if(count($menu_items)){
                    foreach($menu_items as $m){
                        if(isset($m -> params)){
                            $params = $m -> params;
                            if($tpl_style_id = $params -> get('tz_template_style_id')){
                                if(!isset($menu_assigned[$tpl_style_id])){
                                    $menu_assigned[$tpl_style_id]   = 0;
                                }
                                $menu_assigned[$tpl_style_id] ++;
                            }
                        }
                    }
                }
            }

            foreach($items as $i => &$item){
                $item -> menu_assigned      = 0;
                if(isset($menu_assigned[$item -> id])){
                    $item -> menu_assigned  = $menu_assigned[$item -> id];
                }
            }

            return $items;
        }
        return false;
    }


}