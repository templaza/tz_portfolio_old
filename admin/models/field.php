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

jimport('joomla.application.component.modeladmin');

class TZ_PortfolioModelField extends JModelAdmin
{
    public function __construct($config = array()){
        parent::__construct($config);
    }

    public function populateState(){
        parent::populateState();
    }

    public function getTable($type = 'Fields', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio.field', 'field', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getItem($pk = null){
        // Initialise variables.
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table  = $this -> getTable();

        if ($pk > 0)
        {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());
                return false;
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item = JArrayHelper::toObject($properties, 'JObject');

        $item -> title  = htmlspecialchars(strip_tags($item -> title));

        if (property_exists($item, 'params'))
        {
            $registry = new JRegistry;
            $registry->loadString($item->params);
            $item->params = $registry->toArray();
        }

        if($item){
            $arr    = str_replace('[','',$item -> value);
            $arr    = str_replace(']','',$arr);

            if(preg_match('/.*\},{\.*?/s',$arr,$match)){
                //var_dump($match);
                $values     = str_replace('},','}///',$arr);
                $values     = explode('///',$values);
            }
            else
                $values=(array) $arr;

//            $artOptFields   = $this -> _checkArticleFields($item -> id);

            if(count($values)>0){
                $list   = array();
                $i=0;
                foreach($values as $value){
                    $list[$i]   = new stdClass();
                    $param  = new JRegistry($value);
                    $list[$i] -> type           = $item -> type;
                    if(!empty($item -> default_value))
                        $list[$i] -> default_value  = explode(',',$item -> default_value);
                    else
                        $list[$i] -> default_value  = array();

                    $list[$i] -> name           = $param -> get('name');
                    $list[$i] -> value          = $param -> get('value');
                    $list[$i] -> target         = $param -> get('target');
                    $list[$i] -> editor         = $param -> get('editor');
                    $list[$i] -> image          = $param -> get('image');
                    $list[$i] -> ordering       = $param -> get('ordering');
                    $i++;
                }
                $item -> defvalue       = $list;
            }
            $item -> groups    = $this -> getGroups();
        }
        
        return $item;
    }

    public function getGroups($pk = null){
        // Initialise variables.
        $pk     = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('g.*,f.id AS fieldsid');
        $query -> from($db -> quoteName('#__tz_portfolio_fields_group').' AS g');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_xref').' AS x ON x.groupid=g.id');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_fields').' AS f ON x.fieldsid = f.id');
        $query -> where('f.id='.$pk);
        $db -> setQuery($query);

        if($items = $db -> loadObjectList()){
            $list   = array();
            foreach($items as $item){
                $list[$item -> id]  = $item;
            }
            return $list;
        }

        return array();
    }


    protected function _prepareData($_data){
        $data                   = array();
        $data['default_value']  = array();
        $data['id']             = $_data['id'];
        $data['title']          = strip_tags($_data['title']);
        $data['type']           = $_data['type'];
        $data['published']      = ($_data['published'] == 'U')?0:1;
        $data['description']    = $_data['description'];
        
        if(isset($_data['default'])){
            $_default    = implode(',',$_data['default']);
            $data['default_value']  = $_default;
        }

        if(isset($_data['option_icon'])){
            foreach($_data['option_icon'] as $i => $item){
                if(empty($item)){
                    $item = null;
                }
            }
        }

        switch ($_data['type']){
            case 'textfield':
                $data['value']  = '[{"name":"'.htmlspecialchars(strip_tags($_data['option_value'][0])).'","value":"0"'
                                  .',"target":"null","editor":"null","image":"'
                                  .$_data['option_icon'][0].'"}]';
                $defautValue[]  = htmlspecialchars(strip_tags($_data['option_value'][0]));
                break;
            case 'textarea':
//                var_dump(htmlspecialchars(strip_tags($_data['option_editor']))); die();
                $data['value']  = '[{"name":"'.htmlspecialchars(strip_tags($_data['option_value'][0]))
                                  .'","value":"0","target":"null","editor":"'
                                  .htmlspecialchars(strip_tags($_data['option_editor'])).'","image":"'
                                  .htmlspecialchars(strip_tags($_data['option_icon'][0])).'"}]';
                $defautValue[]  = $_data['option_value'][0];
                break;
            case 'select':
                case 'multipleSelect':
                case 'radio':
                case 'checkbox':
                    $values     = array();
                    if(isset($_data['option_name'])){
                        $count  = 0;
                        for($i=0;$i<count($_data['option_name']);$i++){
                            if(isset($_data['option_name'][$i]) && !empty($_data['option_name'][$i])){
                                if(!isset($_data['ordering'][$i])){
                                    $_data['ordering'][$i]	= 0;
                                }
                                $values[]   = '{"name":"'.htmlspecialchars(strip_tags($_data['option_name'][$i]))
                                    .'","value":"'.$count.'","target":"null","editor":"null","image":"'
                                              .$_data['option_icon'][$i].'","ordering":"'.$_data['ordering'][$i].'"}';
                                if(in_array($i,$data['default_value'])){
                                    $defautValue[$i]   = htmlspecialchars(strip_tags($_data['option_name'][$i]));
                                }

                                $count++;
                            }
                        }
                        $values             = '['.implode(',',$values).']';
                    }
                        $data['value']      = $values;
            break;
            case 'link':

                $data['value']  = '[{"name":"'.htmlspecialchars(strip_tags($_data['option_name'][0]))
                                  .'","value":"'.htmlspecialchars(strip_tags($_data['option_value'][0]))
                                  .'","target":"'.htmlspecialchars(strip_tags($_data['option_target'][0])).'","editor":"null","image":"'
                                  .$_data['option_icon'][0].'"}]';

                    if(empty($_data['option_name'][0]))
                        $title  = htmlspecialchars(strip_tags($_data['option_value'][0]));
                    else
                         $title  = htmlspecialchars(strip_tags($_data['option_name'][0]));
                $defautValue[]  = htmlspecialchars('<a href="'.$_data['option_value'][0].'" target="'.$_data['option_target'][0].'">'.$title.'</a> ');
                break;
            case 'file':
            case 'date':
            default:
                $data['value']='[{"name":"null","value":"null","target":"null","editor":"null","image":"null"}]';
                break;

        }

        return $data;
    }
    
    public function save($data){

        $db     = $this -> getDbo();
        $table  = $this -> getTable();

        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Allow an exception to be thrown.
        try
        {
            // Prepare the data for table
            $_data   = $this -> _prepareData($data);

            $groupid    = array();
            if(isset($data['groups'])){

                for($i=0;$i<count($data['groups']);$i++){
                    if($data['groups'][$i]!=-1)
                        $groupid[]      = (int) $data['groups'][$i];
                }

            }

            // if new item, order last in appropriate group
            if(!$table -> id)
                $table -> ordering = $table -> getNextOrder();



            if(!$table -> bind($_data)){
                $this -> setError($table -> getError());
                return false;
            }

            // Prepare the row for saving
//            $this -> _prepareTable($table);
            // Check tag name
//            if(!$table -> check($olname['old_name'])){
//                $this -> setError($table -> getError());
//                return false;
//            }

            //Delete xref with fieldsid
            if(count($groupid)>0){
                if($table -> id){
                    $query  = 'DELETE FROM #__tz_portfolio_xref WHERE fieldsid ='.$table -> id;
                    $db -> setQuery($query);

                    if(!$db -> query()){
                        $this -> setError($db -> getErrorMsg());
                            return false;
                    }
                }
                // Save field
                if(!$table -> store()){
                    $this -> setError($table -> getError());
                    return false;
                }

                $tzvalues   = json_decode($_data['value']);

                if($table -> id){
                    $this -> saveArticleFields($groupid,$table -> id,$tzvalues);
                }

                foreach($data['groups'] as $item){
                        $value[] = '('.$table -> id.','.$item.')';
                }

                //Save into xref
                $value     = implode(',',$value);
                $query      = 'INSERT INTO #__tz_portfolio_xref(`fieldsid`,`groupid`)'
                              .' VALUES'.$value;



                $db -> setQuery($query);

                if(!$db ->execute()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }
            }
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        $pkName = $table->getKeyName();

        if (isset($table->$pkName))
        {
            $this->setState($this->getName() . '.id', $table->$pkName);
        }
        $this->setState($this->getName() . '.new', $isNew);

        return true;
    }

    function getArticleFields($fieldsId=null){
        if($fieldsId){
            if(is_array($fieldsId)){
                $fieldsId   = implode(',',$fieldsId);
            }
            $where  = ' WHERE fieldsid IN('.$fieldsId.')';

            $query  = 'SELECT * FROM #__tz_portfolio'
                      .$where;
            $db = $this -> getDbo();
            $db -> setQuery($query);
            if($rows = $db -> loadObjectList()){
                return $rows;
            }
        }
        return null;
    }

    function saveArticleFields($groupid,$fieldsId,$data = array(),$value = array()){
        if($listArticle = $this -> getArticleFields($fieldsId)){
            $db = $this -> getDbo();

            foreach($listArticle as $item){
                if(count($data)>0){

                    foreach($data as $_value){

                        if($_value -> name == $item -> value){
                            $query  = 'UPDATE #__tz_portfolio SET images="'.$_value -> image
                                .'",'.$db -> quoteName('ordering').'='.$_value -> ordering
                                .' WHERE fieldsid='.$fieldsId.' AND contentid='.$item -> contentid.' AND value='.
                                      $db -> quote($_value->name);
                            $db -> setQuery($query);
                            if(!$db -> query()){
                                JError::raiseError(500,$db -> getErrorMsg());
                            }
                        }
                    }
                }
            }

        }
        return true;
    }

    public function publish(&$pks,$value=1){
        $table  = $this -> getTable();
        if(!$table -> publish($pks,$value)){
            $this -> setError($table -> getError());
            return false;
        }
        return true;
    }
}