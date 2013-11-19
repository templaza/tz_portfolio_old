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

// No direct access
defined('_JEXEC') or die;

/**
 * Pagenavigation plugin class.
 */
class plgContentTZ_Portfolio extends JPlugin
{

   protected static $modules = array();
	protected static $mods = array();
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
        if($this -> params -> get('use_short_code',1)){
            // Don't run this plugin when the content is being indexed
            if ($context == 'com_finder.indexer') {
                return true;
            }

            // simple performance check to determine whether bot should process further
            if (strpos($article->text, 'loadposition') === false && strpos($article->text, 'loadmodule') === false) {
                return true;
            }

            // expression to search for (positions)
            $regex		= '/{loadposition\s+(.*?)}/i';
            $style		= $this->params->def('style', 'none');
            // expression to search for(modules)
            $regexmod	= '/{loadmodule\s+(.*?)}/i';
            $title		= null;
            $stylemod	= $this->params->def('style', 'none');

            // Find all instances of plugin and put in $matches for loadposition
            // $matches[0] is full pattern match, $matches[1] is the position
            preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);


            // No matches, skip this
            if ($matches) {
                foreach ($matches as $match) {

                $matcheslist =  explode(',', $match[1]);

                if (!array_key_exists(1, $matcheslist)) {
                    $matcheslist[1] = null;
                }
                // We may not have a module style so fall back to the plugin default.
                if (!array_key_exists(2, $matcheslist)) {
                    $matcheslist[2] = $style;
                }

                $position = trim($matcheslist[0]);
                $style    = trim($matcheslist[1]);

                    $output = $this->_load($position, $style);

                    // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
                    $article->text = preg_replace("|$match[0]|", addcslashes($output, '\\'), $article->text, 1);

                } 
            }
            // Find all instances of plugin and put in $matchesmod for loadmodule

            preg_match_all($regexmod, $article->text, $matchesmod, PREG_SET_ORDER);
            // If no matches, skip this
            if ($matchesmod){
                foreach ($matchesmod as $matchmod) {

                    $matchesmodlist = explode(',', $matchmod[1]);
                    //We may not have a specific module so set to null
                    if (!array_key_exists(1, $matchesmodlist)) {
                        $matchesmodlist[1] = null;
                    }
                    // We may not have a module style so fall back to the plugin default.
                    if (!array_key_exists(2, $matchesmodlist)) {
                        $matchesmodlist[2] = $stylemod;
                    }

                    $module = trim($matchesmodlist[0]);
                    $name   = trim($matchesmodlist[1]);
                    $style  = trim($matchesmodlist[2]);
                    $name   = str_replace('Articles','TZ Portfolio Articles',$name);
                    $module = str_replace('articles','tz_portfolio_articles',$module);

                    // $match[0] is full pattern match, $match[1] is the module,$match[2] is the title
                    $output = $this->_loadmod($module, $name, $style);
                    // We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
                    $article->text = preg_replace("|$matchmod[0]|", addcslashes($output, '\\'), $article->text, 1);
                }
            }
        }

	}

	protected function _load($position, $style = 'none')
	{

		if (!isset(self::$modules[$position])) {
			self::$modules[$position] = '';
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$modules	= JModuleHelper::getModules($position);
			$params		= array('style' => $style);
			ob_start();

			foreach ($modules as $module) {
				echo $renderer->render($module, $params);
			}

			self::$modules[$position] = ob_get_clean();
		}
		return self::$modules[$position];
	}
	// This is always going to get the first instance of the module type unless
	// there is a title.
	protected function _loadmod($module, $title, $style = 'none')
	{
		if (!isset(self::$mods[$module])) {
			self::$mods[$module] = '';
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$mod		= JModuleHelper::getModule($module, $title);

			// If the module without the mod_ isn't found, try it with mod_.
			// This allows people to enter it either way in the content
			if (!isset($mod)){
				$name = 'mod_'.$module;
				$mod  = JModuleHelper::getModule($name, $title);
			}
			$params = array('style' => $style,'mod_usage' => 1);
			ob_start();

			echo $renderer->render($mod, $params);

			self::$mods[$module] = ob_get_clean();
		}

		return self::$mods[$module];
	}

	/**
	 * @since	1.6
	 */
	public function onContentBeforeDisplay($context, &$row, &$params, $page=0)
	{
		$view = JRequest::getCmd('view');
		$print = JRequest::getBool('print');

		if ($print) {
			return false;
		}

		if ($params->get('show_item_navigation') == 1 && ((($context == 'com_tz_portfolio.article') && ($view == 'article'))||(($context == 'com_tz_portfolio.p_article') && $view == 'p_article'))) {
			$html = '';
			$db		= JFactory::getDbo();
			$user	= JFactory::getUser();
			$app	= JFactory::getApplication();
			$lang	= JFactory::getLanguage();
			$nullDate = $db->getNullDate();

			$date	= JFactory::getDate();
			$config	= JFactory::getConfig();
			$now = $date->toSql();

			$uid	= $row->id;
			$option	= 'com_tz_portfolio';
			$canPublish = $user->authorise('core.edit.state', $option.'.article.'.$row->id);

			// The following is needed as different menu items types utilise a different param to control ordering.
			// For Blogs the `orderby_sec` param is the order controlling param.
			// For Table and List views it is the `orderby` param.
			$params_list = $params->toArray();
			if (array_key_exists('orderby_sec', $params_list)) {
				$order_method = $params->get('orderby_sec', '');
			} else {
				$order_method = $params->get('orderby', '');
			}
			// Additional check for invalid sort ordering.
			if ($order_method == 'front') {
				$order_method = '';
			}

			// Determine sort order.
			switch ($order_method) {
				case 'date' :
					$orderby = 'a.created';
					break;
				case 'rdate' :
					$orderby = 'a.created DESC';
					break;
				case 'alpha' :
					$orderby = 'a.title';
					break;
				case 'ralpha' :
					$orderby = 'a.title DESC';
					break;
				case 'hits' :
					$orderby = 'a.hits';
					break;
				case 'rhits' :
					$orderby = 'a.hits DESC';
					break;
				case 'order' :
					$orderby = 'a.ordering';
					break;
				case 'author' :
					$orderby = 'a.created_by_alias, u.name';
					break;
				case 'rauthor' :
					$orderby = 'a.created_by_alias DESC, u.name DESC';
					break;
				case 'front' :
					$orderby = 'f.ordering';
					break;
				default :
					$orderby = 'a.ordering';
					break;
			}

			$xwhere = ' AND (a.state = 1 OR a.state = -1)' .
			' AND (publish_up = '.$db->Quote($nullDate).' OR publish_up <= '.$db->Quote($now).')' .
			' AND (publish_down = '.$db->Quote($nullDate).' OR publish_down >= '.$db->Quote($now).')';

			// Array of articles in same category correctly ordered.
			$query	= $db->getQuery(true);
	       //sqlsrv changes
	        $case_when = ' CASE WHEN ';
	        $case_when .= $query->charLength('a.alias');
	        $case_when .= ' THEN ';
	        $a_id = $query->castAsChar('a.id');
	        $case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
	        $case_when .= ' ELSE ';
	        $case_when .= $a_id.' END as slug';

	        $case_when1 = ' CASE WHEN ';
	        $case_when1 .= $query->charLength('cc.alias');
	        $case_when1 .= ' THEN ';
	        $c_id = $query->castAsChar('cc.id');
	        $case_when1 .= $query->concatenate(array($c_id, 'cc.alias'), ':');
	        $case_when1 .= ' ELSE ';
	        $case_when1 .= $c_id.' END as catslug';
      		$query->select('a.id,'.$case_when.','.$case_when1);
			$query->from('#__content AS a');
			$query->leftJoin('#__categories AS cc ON cc.id = a.catid');
			$query->where('a.catid = '. (int)$row->catid .' AND a.state = '. (int)$row->state
						. ($canPublish ? '' : ' AND a.access = ' .(int)$row->access) . $xwhere);
			$query->order($orderby);
			if ($app->isSite() && $app->getLanguageFilter()) {
				$query->where('a.language in ('.$db->quote($lang->getTag()).','.$db->quote('*').')');
			}

			$db->setQuery($query);
			$list = $db->loadObjectList('id');

			// This check needed if incorrect Itemid is given resulting in an incorrect result.
			if (!is_array($list)) {
				$list = array();
			}

			reset($list);

			// Location of current content item in array list.
			$location = array_search($uid, array_keys($list));

			$rows = array_values($list);

			$row->prev = null;
			$row->next = null;

			if ($location -1 >= 0)	{
				// The previous content item cannot be in the array position -1.
				$row->prev = $rows[$location -1];
			}

			if (($location +1) < count($rows)) {
				// The next content item cannot be in an array position greater than the number of array postions.
				$row->next = $rows[$location +1];
			}

			$pnSpace = "";
			if (JText::_('JGLOBAL_LT') || JText::_('JGLOBAL_GT')) {
				$pnSpace = " ";
			}

            $tmpl   = null;
            if($params -> get('tz_use_lightbox')){
                $tmpl   = '&amp;tmpl=component';
            }

			if ($row->prev) {
                if($view == 'p_article'){
				    $row->prev = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($row->prev->slug, $row->prev->catslug).$tmpl);
                }
                else{
				    $row->prev = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($row->prev->slug, $row->prev->catslug).$tmpl);
                }
			} else {
				$row->prev = '';
			}

			if ($row->next) {
                if($view == 'p_article'){
				    $row->next = JRoute::_(TZ_PortfolioHelperRoute::getPortfolioArticleRoute($row->next->slug, $row->next->catslug).$tmpl);
                }
                else{
				    $row->next = JRoute::_(TZ_PortfolioHelperRoute::getArticleRoute($row->next->slug, $row->next->catslug).$tmpl);
                }
			} else {
				$row->next = '';
			}

			// Output.
			if ($row->prev || $row->next) {
				$html = '
				<ul class="pager pagenav">'
				;
				if ($row->prev) {
					$html .= '
					<li class="previous">
						<a href="'. $row->prev .'" rel="prev">'
							. JText::_('JGLOBAL_LT') . $pnSpace . JText::_('JPREV') . '</a>
					</li>'
					;
				}



				if ($row->next) {
					$html .= '
					<li class="next">
						<a href="'. $row->next .'" rel="next">'
							. JText::_('JNEXT') . $pnSpace . JText::_('JGLOBAL_GT') .'</a>
					</li>'
					;
				}
				$html .= '
				</ul>'
				;

				$row->pagination = $html;
				$row->paginationposition = $this->params->get('position', 1);
				// This will default to the 1.5 and 1.6-1.7 behavior.
				$row->paginationrelative = $this->params->get('relative',0);
			}
		}

		return ;
	}
}