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

jimport('joomla.application.component.modelitem');
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_tz_portfolio'.DIRECTORY_SEPARATOR
             .'models'.DIRECTORY_SEPARATOR.'article.php');

/**
 * Content Component Article Model.
 */
class TZ_PortfolioModelP_Article extends TZ_PortfolioModelArticle
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_tz_portfolio.p_article';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('article.id', $pk);
        $this -> setState('p_article.catid',null);

		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
//        $params = JComponentHelper::getParams('com_content');
//        var_dump($params);
		$this->setState('params', $params);

		// TODO: Tune these values based on other permissions.
		$user		= JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_tz_portfolio')) &&  (!$user->authorise('core.edit', 'com_tz_portfolio'))){
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}

    public function download(){
        $query  = 'SELECT * FROM #__tz_portfolio_xref_content'
                  .' WHERE contentid='.JRequest::getInt('id');

        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }
        if(!$rows = $db -> loadObject()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }

        $file   = '';
        $arr    = explode('///',$rows -> attachfiles);
        if(count($arr)>0){
            foreach($arr as $item){
                if(md5($item) == JRequest::getCmd('attach')){
                    $file   = $item;
                }
            }
        }
        
        return $file;
    }

    function getFindItemId($_cid=null)
	{
        $cid    = $this -> getState('p_article.catid');
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
        $active     = $menus->getActive();
        $cid        =   intval($cid);
        if($_cid){
            $cid    = intval($_cid);
        }

        $component	= JComponentHelper::getComponent('com_tz_portfolio');
		$items		= $menus->getItems('component_id', $component->id);


        foreach ($items as $item)
        {

            if (isset($item->query) && isset($item->query['view'])) {
                $view = $item->query['view'];


                if (isset($item->query['id'])) {
                    if ($item->query['id'] == $cid) {
                        return $item -> id;
                    }
                } else {

                    $catids = $item->params->get('tz_catid');
                    if ($view == 'portfolio' && $catids) {
                        if (is_array($catids)) {
                            for ($i = 0; $i < count($catids); $i++) {
                                if ($catids[$i] == 0 || $catids[$i] == $cid) {
                                    return $item -> id;
                                }
                            }
                        } else {
                            if ($catids == $cid) {
                                return $item -> id;
                            }
                        }
                    }
                    elseif($view == 'category' && $catids){
                        return $item -> id;
                    }
                }
            }
        }

		return $active -> id;
	}
	
}
