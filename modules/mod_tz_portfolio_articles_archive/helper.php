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

class modTZ_PortfolioArchiveHelper
{
	static function getList(&$params)
	{
		//get database
		$db		    = JFactory::getDbo();
		$query	    = $db->getQuery(true);
        $subQuery   = $db -> getQuery(true);

		$query->select('MONTH(created) AS created_month, created, id, title, YEAR(created) AS created_year');
		$query->from('#__content');
        $query -> where('checked_out = 0');
        if($params -> get('redirect_to','date') == 'archive'){
            $query->where('state = 2');
            $subQuery->where('state = 2');
        }else{
            $query -> where('state = 1');
            $subQuery -> where('state = 1');
        }
		$query->group('created_year DESC, created_month DESC');

        $subQuery -> select('COUNT(*)');
        $subQuery -> from('#__content');
        $subQuery -> where('checked_out = 0');
        $subQuery -> where('MONTH(created) = created_month AND YEAR(created) = created_year');
        $query -> select('('.$subQuery -> __toString().') AS total');

		// Filter by language
		if (JFactory::getApplication()->getLanguageFilter()) {
			$query->where('language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}

		$db->setQuery($query, 0, intval($params->get('count')));
		$rows = (array) $db->loadObjectList();

		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$item	= $menu->getItems('link', 'index.php?option=com_tz_portfolio&view=archive', true);

		$itemid = (isset($item) && count($item)>0) ? '&Itemid='.$item->id : '';

		$i		= 0;
		$lists	= array();
		if($rows){
			foreach ($rows as $row) {
				$date = JFactory::getDate($row->created);

				$created_month	= $date->format('n');
				$created_year	= $date->format('Y');

				$created_year_cal	= JHTML::_('date', $row->created, 'Y');
				$month_name_cal	= JHTML::_('date', $row->created, 'F');

				$lists[$i] = new stdClass;

				if($params -> get('redirect_to','date') == 'archive'){
					$lists[$i]->link	= JRoute::_('index.php?option=com_tz_portfolio&view=archive&year='
					.$created_year.'&month='.$created_month.$itemid);
				}else{
					$lists[$i]->link	= JRoute::_('index.php?option=com_tz_portfolio&view=date&year='
					.$created_year.'&month='.$created_month.$itemid);
				}
				$lists[$i]->text	= JText::sprintf('MOD_ARTICLES_ARCHIVE_DATE', $month_name_cal, $created_year_cal);

                $lists[$i] -> total = 0;
                if(isset($row -> total)){
                    $lists[$i] -> total = $row -> total;
                }
				$i++;
			}
		}

		return $lists;
	}
}
