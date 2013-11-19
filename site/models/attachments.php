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

jimport('joomla.application.component.model');

class TZ_PortfolioModelAttachments extends JModelLegacy
{
    function populateState(){
        $pk = JRequest::getInt('id');
        $this -> setState('article.id',$pk);
    }

    function getAttachments(){
        $data   = array();
        $query  = 'SELECT xc.*,c.attribs AS param FROM #__tz_portfolio_xref_content AS xc'
                  .' LEFT JOIN #__content AS c ON c.id=xc.contentid'
                  .' WHERE c.state=1 AND xc.contentid='.$this -> getState('article.id');
        
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }
        if($rows   = $db -> loadObject()){
            if(!empty($rows -> attachfiles)){
                $attach         = explode('///',$rows -> attachfiles);
                $attachTitle    = explode('///',$rows -> attachtitle);
                $attachOld      = explode('///',$rows -> attachold);

                if(count($attach)>0){
                    foreach($attach as $i => $item){
                        $data[$i]   = new stdClass();
                        $data[$i] -> _link          = 'index.php?option=com_tz_portfolio&view=article'
                                                      .'&task=article.download&attach='
                                                      .md5($item).'&id='.JRequest::getCmd('id')
                                                      .'&Itemid='.JRequest::getCmd('Itemid');
                        $data[$i] -> attachfiles    = 'media/'.$item;
                        $data[$i] -> attachtitle    = $attachOld[$i];
                        if(!empty($attachTitle[$i])){
                            $data[$i] -> attachtitle    = $attachTitle[$i];
                        }
                    }
                }

            }
        }
        return $data;
    }
}
 
