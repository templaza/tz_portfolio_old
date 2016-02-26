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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.event.dispatcher');

//require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/content.php';
define('TZ_IMAGE_SIZE',10*1024*1024);
//define('TZ_IMAGE_TYPE',array('image/jpeg','image/jpg','image/bmp','image/gif','image/png','image/ico'));
tzportfolioimport('HTTPFetcher');
tzportfolioimport('readfile');

/**
 * Item Model for an Article.
 */
class TZ_PortfolioModelArticle extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix  = 'COM_CONTENT';
    private $fieldsid       = array();
    private $imageUrl       = 'media/tz_portfolio/article';
    private $tzfolder       = 'tz_portfolio';
    private $attachUrl      = 'attachments';
    protected  $audioFolder = 'audio';
    private $contentid      = null;
    private $catParams      = null;
    protected $tztask       = null;
    protected $input        = null;

    function __construct(){
        $app    = JFactory::getApplication();
        $this -> contentid  = JRequest::getCmd('id');
        $this -> input  = $app -> input;

        parent::__construct();
    }

    protected function populateState(){
        parent::populateState();
        $params = JComponentHelper::getParams('com_tz_portfolio');
        $this -> setState('params',$params);
        if($params -> get('tz_image_xsmall',100)){
            $sizeImage['XS'] = (int) $params -> get('tz_image_xsmall',100);
        }
        if($params -> get('tz_image_small',200)){
            $sizeImage['S'] = (int) $params -> get('tz_image_small',200);
        }
        if($params -> get('tz_image_medium',400)){
            $sizeImage['M'] = (int) $params -> get('tz_image_medium',400);
        }
        if($params -> get('tz_image_large',600)){
            $sizeImage['L'] = (int) $params -> get('tz_image_large',600);
        }
        if($params -> get('tz_image_xsmall',900)){
            $sizeImage['XL'] = (int) $params -> get('tz_image_xlarge',900);
        }

        if($params -> get('tz_image_gallery_xsmall')){
            $size['XS'] = (int) $params -> get('tz_image_gallery_xsmall');
        }
        if($params -> get('tz_image_gallery_small')){
            $size['S'] = (int) $params -> get('tz_image_gallery_small');
        }
        if($params -> get('tz_image_gallery_medium')){
            $size['M'] = (int) $params -> get('tz_image_gallery_medium');
        }
        if($params -> get('tz_image_gallery_large')){
            $size['L'] = (int) $params -> get('tz_image_gallery_large');
        }
        if($params -> get('tz_image_gallery_xsmall')){
            $size['XL'] = (int) $params -> get('tz_image_gallery_xlarge');
        }
        $this -> setState('sizeImage',$sizeImage);
        $this -> setState('size',$size);
        $this -> setState('article.id',JRequest::getInt('id'));
    }

    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            // Association content items
            $app = JFactory::getApplication();
            $assoc = JLanguageAssociations::isEnabled();
            if ($assoc)
            {
                $languages = JLanguageHelper::getLanguages('lang_code');

                // force to array (perhaps move to $this->loadFormData())
                $data = (array) $data;

                $addform = new SimpleXMLElement('<form />');
                $fields = $addform->addChild('fields');
                $fields->addAttribute('name', 'associations');
                $fieldset = $fields->addChild('fieldset');
                $fieldset->addAttribute('name', 'item_associations');
                $fieldset->addAttribute('description', 'COM_CONTENT_ITEM_ASSOCIATIONS_FIELDSET_DESC');
                $add = false;
                foreach ($languages as $tag => $language)
                {
                    if (empty($data['language']) || $tag != $data['language'])
                    {
                        $add = true;
                        $field = $fieldset->addChild('field');
                        $field->addAttribute('name', $tag);
                        $field->addAttribute('type', 'modal_article');
                        $field->addAttribute('language', $tag);
                        $field->addAttribute('label', $language->title);
                        $field->addAttribute('translate_label', 'false');
                        $field->addAttribute('edit', 'true');
                        $field->addAttribute('clear', 'true');
                    }
                }
                if ($add)
                {
                    $form->load($addform, false);
                }
            }
        }

        parent::preprocessForm($form, $data, $group);
    }

    public function saveorder($pks = null, $order = null){
        return parent::saveorder($pks,$order);
    }

    function getFieldsParams($groupid = null){
        $where  = null;
        if($groupid != null)
            $where  = ' WHERE x.groupid='.$groupid;

        $query  = 'SELECT f.id,f.title FROM #__tz_portfolio_fields AS f'
                  .' LEFT JOIN #__tz_portfolio_xref AS x ON x.fieldsid=f.id'
                  .' LEFT JOIN #__tz_portfolio_categories AS c ON c.groupid=x.groupid'
                  .$where
                  .' GROUP BY f.id';

        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($rows   = $db -> loadObjectList())
            return $rows;

        return false;
    }

    // Get group id with catid
    function getGroupId($catid=null){
        if($catid)
            $where  = ' WHERE catid IN('.$catid.')';
        $query  = 'SELECT * FROM #__tz_portfolio_categories'
                  .$where;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($rows = $db -> loadObjectList()){
            return $rows;
        }
        return false;
    }

    function getContentGroupId($articleId=null){
        if($articleId)
            $where  = ' WHERE contentid IN('.$articleId.')';
        $query  = 'SELECT contentid,groupid FROM #__tz_portfolio_xref_content'
                  .$where;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($rows = $db -> loadObject()){
            return $rows;
        }
        return false;
    }

    function extrafields(){
        $data       = null;
        $json       = JRequest::getString('json',null,null,2);
        $ob_json    = json_decode($json);

        $groupId    = $ob_json -> groupid;
        if($ob_json -> groupid == 0){
            if($rows       = $this -> getGroupId($ob_json -> catid))
                $groupId    = $rows[0] -> groupid;
        }

        if($groupId == 0)
            $groupId    = -1;

        $fieldsId       = array(-1);
        if($ob_json -> id != 0){
            $contenGroupId  = $this -> getContentGroupId($ob_json -> id);

            $listArticle    = $this -> getItem($ob_json -> id);

            if($contenGroupId && $ob_json){
                if($contenGroupId -> groupid == $ob_json -> groupid){
                    if(isset($listArticle -> attribs['tz_fieldsid']) && $fieldsId = $listArticle -> attribs['tz_fieldsid']){
                        if(empty($fieldsId[0]))
                            $fieldsId[0]    = -1;
                    }
                }
            }
        }

        if($list   = $this -> getFieldsParams($groupId)){
            ob_start();
?>
        <select id="jform_attribs_tz_fieldsid" multiple="multiple"
                name="jform[attribs][tz_fieldsid][]" class="" aria-invalid="false"
                style="min-width: 130px; min-height: 80px;">
            <option value=""<?php if(in_array(-1,$fieldsId)) echo ' selected="selected"';?>><?php echo JText::_('COM_TZ_PORTFOLIO_ALL_FIELDS');?></option>
            <?php if($list):?>
                <?php foreach($list as $item):?>
                    <option value="<?php echo addslashes($item -> id);?>"<?php if(in_array($item -> id,$fieldsId)) echo ' selected="selected"';?>><?php echo $item -> title;?></option>
                <?php endforeach;?>
            <?php endif;?>
        </select>
<?php
            $data   = ob_get_contents();
            ob_end_clean();
        }
        return json_encode($data);
    }

    function getExtraFields($fieldId=null){
        if($fieldId){
            $where  = ' WHERE id='.$fieldId;
        }
        $query  = 'SELECT * FROM #__tz_portfolio_fields'.$where;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            die();
        }
        if($rows   = $db -> loadObjectList()){
            return $rows;
        }
        return false;
    }

    function getOptionField($fieldsId,$value=null){
        if($fieldsId){
            $fields     = $this -> getExtraFields($fieldsId);
            if(count($fields) > 0){
                foreach($fields as $row){
                    $json   = json_decode($row -> value);
                    foreach($json as $item){

                        if($item -> value == (int)$value){
                            return $item;
                            break;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function getThumb(){

        $data       = null;
        $json       = JRequest::getString('json', null, null, 2);
        $code       = json_decode($json);
        $thumbUrl   = 'http://www.vimeo.com/api/v2/video/'.$code -> videocode.'.json';

        $file       = new Services_Yadis_PlainHTTPFetcher();
        $vimeo      = $file ->get($thumbUrl);
        $vimeo      = json_decode($vimeo -> body);
        if($vimeo)
            $data   = $vimeo[0] -> thumbnail_large;

        return json_encode($data);

    }

    function getCatParams($catid=null){
        if($catid){
            $query  = 'SELECT params FROM #__categories'
                      .' WHERE id='.$catid;
            $db     = JFactory::getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                die();
            }
            $rows   = $db -> loadObject();
            $params = new JRegistry();
            $params -> loadString($rows -> params);
            if(count($params))
                return $params;
        }
        return false;
    }

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since	11.1
	 */
	protected function batchCopy($value, $pks, $contexts)
	{

		$categoryId = (int) $value;

		$table = $this->getTable();
		$i = 0;

		// Check that the category exists
		if ($categoryId)
		{
			$categoryTable = JTable::getInstance('Category');
			if (!$categoryTable->load($categoryId))
			{
				if ($error = $categoryTable->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
					return false;
				}
			}
		}

		if (empty($categoryId))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
			return false;
		}

		// Check that the user has create permission for the component
		$extension = JFactory::getApplication()->input->get('option', '');
		$user = JFactory::getUser();
		if (!$user->authorise('core.create', $extension . '.category.' . $categoryId))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title = $data['0'];
			$table->alias = $data['1'];

			// Reset the ID because we are making a copy
			$table->id = 0;

			// New category ID
			$table->catid = $categoryId;

			// TODO: Deal with ordering?
			//$table->ordering	= 1;

			// Get the featured state
			$featured = $table->featured;

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Get the new item ID
			$newId = $table->get('id');

            $db = JFactory::getDbo();
            // Store new article to table tz_portfolio
            $query2  = 'SELECT * FROM #__tz_portfolio'
                       .' WHERE contentid='.$pk;
            $db -> setQuery($query2);
            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }

            $val    = array();
            $rows   = $db -> loadObjectList();
            foreach($rows as $row){
                $val[]  = '('.$newId.','.$row -> fieldsid.','.$db -> quote($row -> value).',"'.$row -> images.'","'
                    .$row -> imagetitle.'",'.$row -> ordering.')';
            }
            if(count($val)>0){
                $val    = implode(',',$val);

                $query2 = 'INSERT INTO #__tz_portfolio(`contentid`,`fieldsid`,`value`,`images`,`imagetitle`,'
                    .$db -> quoteName('ordering').')'
                          .' VALUES '.$val;
                $db -> setQuery($query2);
                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }
            }


            // Store new ariticle to table tz_portfolio_xref_content
            $query2  = 'SELECT * FROM #__tz_portfolio_xref_content'
                       .' WHERE contentid='.$pk;
            $db -> setQuery($query2);
            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }

            $values         = null;
            $attachFiles    = null;
            $attachTitle    = null;
            $audioFields    = null;
            $audioFields    = ','.$this -> _db -> quoteName('audio').','
                               .$this -> _db -> quoteName('audiothumb').','
                               .$this -> _db -> quoteName('audiotitle');

            $rows           = $db -> loadObjectList();
            foreach($rows as $row){
                //Copy attachment
                if(!empty($row -> attachfiles)){
                    $attachFileName     = explode('///',$row -> attachfiles);
                    $attachTitle    = $row -> attachtitle;
                    $attachmentsOld     = $row -> attachold;
                    $i=0;
                    foreach($attachFileName as $item){
                        $fileName   = 'tz_portfolio_'.(time()+$i)
                            .'.'.JFile::getExt($item);
                        $srcPath    = JPATH_SITE.DIRECTORY_SEPARATOR.'media'
                            .DIRECTORY_SEPARATOR.$item;
                        $desPath    = JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR
                            .$this -> tzfolder
                            .DIRECTORY_SEPARATOR.$this -> attachUrl
                            .DIRECTORY_SEPARATOR.$fileName;

                        $_fileName  = explode('/',$item);
                        if(JFile::exists($srcPath)){
                            $attachFiles[$i]    = $this -> tzfolder.'/'
                                .$this -> attachUrl.'/'
                                .$fileName;
                            JFile::copy($srcPath,$desPath);
                        }

                        $i++;
                    }
                }
                if(is_array($attachFiles) && count($attachFiles)>0){
                    $attachFiles    = implode('///',$attachFiles);
                }
                //end attachment

                $sizes          = $this -> getState('size');
                $imageName      = '';
                $imageHoverName = '';
                $galleryName    = '';
                $videoThumb     = '';
                $sizes['o']     = null;
                //Copy Image
                if(!empty($row -> images)){
                    $imageName  = ((!$table -> alias)?JApplication::stringURLSafe($table -> title):$table -> alias)
                        .'-'.$newId.'.'.JFile::getExt($row -> images);
                    $subUrl     = substr($row -> images,0,strrpos($row -> images,'/'));
                    $imageName  = $subUrl.'/'.$imageName;

                    foreach($sizes as $key => $val){
                        $newImage   = str_replace('/',DIRECTORY_SEPARATOR,$imageName);
                        $newImage   = str_replace('.'.JFile::getExt($row -> images),
                                                  '_'.$key.'.'.JFile::getExt($row -> images),$newImage);
                        $path       = JPATH_SITE.DIRECTORY_SEPARATOR.$newImage;

                        $srcName    = str_replace('/',DIRECTORY_SEPARATOR,$row -> images);
                        $srcName    = str_replace('.'.JFile::getExt($srcName),
                                                  '_'.$key.'.'.JFile::getExt($srcName),$srcName);
                        $srcPath2   = JPATH_SITE.DIRECTORY_SEPARATOR.$srcName;
                        if(JFile::exists($srcPath2)){
                            JFile::copy($srcPath2,$path);
                        }

                    }
                }
                //end Image
                //Copy Image Hover
                if(!empty($row -> images_hover)){
                    $imageHoverName     = ((!$table -> alias)?JApplication::stringURLSafe($table -> title):$table -> alias)
                        .'-'.$newId.'-h.'.JFile::getExt($row -> images_hover);
                    $subUrl             = substr($row -> images_hover,0,strrpos($row -> images_hover,'/'));
                    $imageHoverName     = $subUrl.'/'.$imageHoverName;

                    foreach($sizes as $key => $val){
                        $newImageHover  = str_replace('/',DIRECTORY_SEPARATOR,$imageHoverName);
                        $newImageHover  = str_replace('.'.JFile::getExt($row -> images_hover),
                                                  '_'.$key.'.'.JFile::getExt($row -> images_hover),$newImageHover);
                        $path       = JPATH_SITE.DIRECTORY_SEPARATOR.$newImageHover;

                        $srcName    = str_replace('/',DIRECTORY_SEPARATOR,$row -> images_hover);
                        $srcName    = str_replace('.'.JFile::getExt($srcName),
                                                  '_'.$key.'.'.JFile::getExt($srcName),$srcName);
                        $srcPath2   = JPATH_SITE.DIRECTORY_SEPARATOR.$srcName;
                        if(JFile::exists($srcPath2)){
                            JFile::copy($srcPath2,$path);
                        }

                    }
                }
                //end Image Hover
                //Copy gallery
                if(!empty($row -> gallery)){
                    $arr    = explode('///',$row -> gallery);
                    foreach($arr as $i => $gallery){
                        $str    = ((!$table -> alias)?JApplication::stringURLSafe($table -> title):$table -> alias)
                            .'-'.$newId.'-'.$i.'.'.JFile::getExt($gallery);
                        $subUrl = substr($gallery,0,strrpos($gallery,'/'));
                        $galleryName[]  = $subUrl.'/'.$str;

                        foreach($sizes as $key => $val){
                            $newGallery   = str_replace('/',DIRECTORY_SEPARATOR,$subUrl.'/'.$str);
                            $newGallery    = str_replace('.'.JFile::getExt($newGallery),
                                                       '_'.$key.'.'.JFile::getExt($gallery),$newGallery);
                            $path       = JPATH_SITE.DIRECTORY_SEPARATOR.$newGallery;

                            $srcName    = str_replace('/',DIRECTORY_SEPARATOR,$gallery);
                            $srcName    = str_replace('.'.JFile::getExt($srcName),
                                                      '_'.$key.'.'.JFile::getExt($srcName),$srcName);
                            $srcPath2   = JPATH_SITE.DIRECTORY_SEPARATOR.$srcName;
                            if(JFile::exists($srcPath2)){
                                JFile::copy($srcPath2,$path);
                            }
                        }
                        if(JFile::exists(JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$gallery))){
                            JFile::copy(JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$gallery),$path);
                        }
                    }
                    if(count($galleryName) > 0){
                        $galleryName    = implode('///',$galleryName);
                    }
                }
                //end Gallery
                //Copy video thumbnail
                if(!empty($row -> videothumb)){
                    $videoThumb = ((!$table -> alias)?JApplication::stringURLSafe($table -> title):$table -> alias)
                        .'-'.$newId.'.'.JFile::getExt($row -> videothumb);
                    $subUrl     = substr($row -> videothumb,0,strrpos($row -> videothumb,'/'));
                    $videoThumb = $subUrl.'/'.$videoThumb;

                    foreach($sizes as $key => $val){
                        $newThumb   = str_replace('/',DIRECTORY_SEPARATOR,$videoThumb);
                        $newThumb    = str_replace('.'.JFile::getExt($newThumb),
                                                   '_'.$key.'.'.JFile::getExt($newThumb),$newThumb);
                        $path       = JPATH_SITE.DIRECTORY_SEPARATOR.$newThumb;

                        $srcName    = str_replace('/',DIRECTORY_SEPARATOR,$row -> videothumb);
                        $srcName    = str_replace('.'.JFile::getExt($srcName),
                                                  '_'.$key.'.'.JFile::getExt($srcName),$srcName);
                        $srcPath2   = JPATH_SITE.DIRECTORY_SEPARATOR.$srcName;
                        if(JFile::exists($srcPath2)){
                            JFile::copy($srcPath2,$path);
                        }
                    }
                }
                //end video thumbnail

                // Copy audio thumnail
                $audioData      = null;

                $audioData['id']                            = $newId;
                $audioData['alias']                         = $table -> alias;
                $audioData['title']                         = $table -> title;
                $audioData['audio_soundcloud_id']           = $row -> audio;
                $audioData['audio_soundcloud_title']        = $row -> audiotitle;
                $audioData['audio_soundcloud_image_server'] = $row -> audiothumb;

                $audioValue = ','.$this -> prepareAudio($audioData,null,true);

                // End copy audio thumbnail

                $values[]  = '('.$newId.','.$row -> groupid.','
                          .$db -> quote($imageName).','
                          .$db -> quote($row -> imagetitle).','.$db -> quote($imageHoverName).','
                          .$db -> quote($attachFiles).','
                          .$db -> quote($attachTitle).','.$db -> quote($attachmentsOld).','
                          .$db -> quote($galleryName).','.$db -> quote($row -> gallerytitle).','
                          .$db -> quote($row -> video).','.$db -> quote($row -> videotitle).','.$db -> quote($videoThumb)
                             .','.$db -> quote($row -> type).$audioValue.')';
            }
            if(count($values)>0){
                $values    = implode(',',$values);
                $query2 = 'INSERT INTO #__tz_portfolio_xref_content(`contentid`,`groupid`,`images`,`imagetitle`,'
                          .'`images_hover`,`attachfiles`,`attachtitle`,`attachold`,`gallery`,`gallerytitle`,`video`,'
                          .'`videotitle`,`videothumb`,`type`'.$audioFields.')'
                          .' VALUES '.$values;
                $db -> setQuery($query2);
                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }
            }



			// Add the new ID to the array
			$newIds[$i]	= $newId;
			$i++;

			// Check if the article was featured and update the #__content_frontpage table
			if ($featured == 1)
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				$query->insert($db->quoteName('#__content_frontpage'));
				$query->values($newId . ', 0');
				$db->setQuery($query);
				$db->query();
			}

		}



		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id)) {
			if ($record->state != -2) {
				return ;
			}
			$user = JFactory::getUser();
			return $user->authorise('core.delete', 'com_content.article.'.(int) $record->id);
		}
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing article.
		if (!empty($record->id)) {
			return $user->authorise('core.edit.state', 'com_content.article.'.(int) $record->id);
		}
		// New article, so check against the category.
		elseif (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_content.category.'.(int) $record->catid);
		}
		// Default to component settings if neither article nor category known.
		else {
			return parent::canEditState('com_content');
		}
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param	JTable	A JTable object.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function _prepareTable($table)
	{
		// Set the publish date to now
        $db = $this->getDbo();
        if ($table->state == 1 && (int) $table->publish_up == 0)
        {
            $table->publish_up = JFactory::getDate()->toSql();
        }

        if ($table->state == 1 && intval($table->publish_down) == 0)
        {
            $table->publish_down = $db->getNullDate();
        }

        // Increment the content version number.
        $table->version++;

        // Reorder the articles within the category so the new article is first
        if (empty($table->id))
        {
            $table->reorder('catid = ' . (int) $table->catid . ' AND state >= 0');
        }
	}

    function deleteAttachment(){
        $json 		= JRequest::getString('json', null, null, 2);
        $obj_json 	= json_decode($json);

        $db     = JFactory::getDbo();
        $query  = 'SELECT attachfiles,attachtitle FROM #__tz_portfolio_xref_content'
                    .' WHERE contentid = '.$obj_json -> id;
        $db -> setQuery($query);
        $rows   = $db -> loadObject();


        $file   = array();
        $title  = array();


            $arr    = explode('///',$rows -> attachfiles);
            $arr2   = explode('///',$rows -> attachtitle);

            $i=0;
            foreach($arr as $item){
                if($this -> tzfolder.'/'.$this -> attachUrl.'/'.$obj_json -> attachmentsFile != $item)
                    $file[] = $item;
                else{
                    // Delete file
                    $filePath   = JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.$this -> tzfolder.DIRECTORY_SEPARATOR.$this-> attachUrl.DIRECTORY_SEPARATOR.$obj_json -> attachmentsFile;

                    if(JFile::exists($filePath)){
                        JFile::delete($filePath);
                    }
                }

                if($obj_json -> attachmentsTitle != $arr2[$i])
                    $title[] = $arr2[$i];
                $i++;
            }



        $file   = implode('///',$file);
        $title  = implode('///',$title);

        $query  = 'UPDATE #__tz_portfolio_xref_content'
                  .' SET attachfiles=\''.$file.'\''
                  .', attachtitle=\''.$title.'\''
                  .' WHERE contentid='.$obj_json -> id;

        $db -> setQuery($query);
        $db -> query();

        $this -> contentid = $obj_json -> id;

        return true;
    }

    function removeAllAttach($contentids=null,$folder){
        if($contentids){
            if(count($contentids)>0){
                //$contentids = implode(',',$contentids);

                $query  = 'SELECT * FROM #__tz_portfolio_xref_content'
                          .' WHERE contentid ='.$contentids;
                $db     = & JFactory::getDbo();
                $db -> setQuery($query);

                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }

                $rows   = $db -> loadObject();

                //if(count($rows)>0){
                    //foreach($rows as $row){

                        if(preg_match('/.*\/\/\/.*/i',$rows -> attachfiles,$match)){
                            $attachFiles    = explode('///',$rows -> attachfiles);

                            foreach($attachFiles as $item){
                                $filePath   = JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$item;
                                if(JFile::exists($filePath)){
                                    JFile::delete($filePath);
                                }
                            }
                        }
                        else{
                            $filePath   = JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$rows -> attachfiles;
                            if(JFile::exists($filePath)){
                                JFile::delete($filePath);
                            }
                        }
                    //}
                //}


            }
        }
        return true;
    }


    public function FieldsEdit($fieldsid){

        $data   = array();

        if($this -> contentid){
            $query  = 'SELECT * FROM #__tz_portfolio'
                      .' WHERE contentid = '.$this -> contentid
                      .' AND fieldsid = '.$fieldsid;

            $db     = JFactory::getDbo();
            $db -> setQuery($query);

            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }

            if($rows = $db -> loadObjectList()){

                $data   = $rows;
                return $data;
            }

        }

        return $data;
    }

    function getAttachment(){
        $data   = array();
        if($this -> contentid){
            $query  = 'SELECT attachfiles,attachtitle,attachold FROM #__tz_portfolio_xref_content'
                .' WHERE contentid = '.$this -> contentid;
            $db     = $this -> getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }
            if($rows = $db -> loadObject()){

                if(!empty($rows -> attachfiles)){
                   if(preg_match('/.*\/\/\/.*/i',$rows -> attachfiles,$match)){
                        $attachFiles    = explode('///',$match[0]);
                        $attachTitle    = explode('///',$rows -> attachtitle);
                        $attachOld      = explode('///',$rows -> attachold);
                        $i=0;
                        foreach($attachFiles as $item){
                            $data[$i]   = new stdClass();
                            $fileName   = explode('/',$item);
                            $data[$i] -> attachfiles    = $fileName[count($fileName)-1];
                            $data[$i] -> attachtitle    = $attachTitle[$i];
                            $data[$i] -> attachold      = $attachOld[$i];
                            $i++;
                        }
                   }
                   else{
                       $fileName   = explode('/',$rows -> attachfiles);
                       $data[0] = new stdClass();
                       $data[0] -> attachfiles  = $fileName[count($fileName)-1];
                       $data[0] -> attachtitle  = $rows -> attachtitle;
                       $data[0] -> attachold    = $rows -> attachold;
                   }
                }
            }
        }
        return $data;
    }

    function getFieldsContent(){
        $data   = new stdClass();
        $data -> gallery    = new stdClass();
        $data -> video    = new stdClass();
        $data -> audio  = new stdClass();

        $data -> images             = '';
        $data -> imagetitle         = '';
        $data -> images_hover       = '';
        $data -> gallery -> images  = '';
        $data -> gallery -> title   = '';
        $data -> video -> code      = '';
        $data -> video -> type      = '';
        $data -> video -> title     = '';
        $data -> type               = '';

        $data -> audio -> audio_id  = '';
        $data -> audio -> audiothumb= '';
        $data -> audio -> audiotitle= '';
        $data -> quote_author       = '';
        $data -> quote_text         = '';

        $data -> link_title         = '';
        $data -> link_url           = '';
        $data -> link_follow        = '';
        $data -> link_target        = '';
        if($this -> contentid){
            $query  = 'SELECT * FROM #__tz_portfolio_xref_content'
                .' WHERE contentid = '.$this -> contentid;
            //.' GROUP BY contentid';

            $db     = $this -> getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }

            if($row = $db -> loadObject()){


                $data -> images = $row -> images;
                $data -> imagetitle = addslashes(htmlspecialchars($row -> imagetitle));
                $data -> images_hover       = $row -> images_hover;

                if(preg_match('/.*\/\/\/.*/i',$row -> gallery,$match)){
                    $gallery        = explode('///',$row -> gallery);
                    $gallerytitle   = explode('///',$row -> gallerytitle);
                    if($gallery){
                        foreach($gallery as $i => $item){
                            if(!isset($gallerytitle[$i])){
                                $gallerytitle[$i]   = '';
                            }
                            $gallerytitle[$i]    = addslashes($gallerytitle[$i]);
                        }
                    }
                }
                else{
                    $gallery        = $row -> gallery;
                    $gallerytitle   = addslashes($row -> gallerytitle);
                }
                $data -> gallery -> images      = $gallery;
                $data -> gallery -> title  = $gallerytitle;

                if(preg_match('/.*:.*/i',$row -> video,$match)){
                    for($i = 0; $i<strlen($row -> video); $i ++){
                        if(substr($row -> video,$i,1) == ':'){
                            $pos    = $i;
                            break;
                        }
                    }

                    $data -> video -> code  = substr($row -> video,$pos + 1,strlen($row -> video));
                    $data -> video -> type  = substr($row -> video,0,$pos);
                    $data -> video -> title = addslashes($row -> videotitle);
                    $data -> video -> thumb = $row -> videothumb;
                }
                else{
                    $data -> video -> code  = '';
                    $data -> video -> type  = 'default';
                    $data -> video -> title = '';
                    $data -> video -> thumb = '';
                }

                $data -> audio -> audio_id      = $row -> audio;
                $data -> audio -> audiothumb    = $row -> audiothumb;
                $data -> audio -> audiotitle    = $row -> audiotitle;

                $data   -> type = strtolower($row -> type);

                $data -> quote_author   = $row -> quote_author;
                $data -> quote_text     = $row -> quote_text;

                $data -> link_title     = $row -> link_title;
                $data -> link_url       = $row -> link_url;
                $linkParams = new JRegistry($row -> link_attribs);
                $data -> link_follow    = $linkParams -> get('link_follow');
                $data -> link_target    = $linkParams -> get('link_target');
            }
        }

        return $data;
    }

    function  listsfields(){
        $json 		= JRequest::getString('json', null, null, 2);
        $obj_json 	= json_decode($json);
        $arr        = array();
        $this -> contentid  =  $obj_json -> id;

        $arr['data']    = $this -> renderFields($obj_json -> groupid,$obj_json -> catid);

//        $arr['row']     = $this -> getFieldsContent();

        if(count($this -> fieldsid)>0)
            $arr['id']  = implode(',',$this -> fieldsid);

        return json_encode($arr);
    }

    public function getListsFields(){
        if(JRequest::getCmd('return'))
            $where  = ' AND c.featured = 1';
        else
            $where  = ' AND c.featured = 0';

        $query  = 'SELECT a.* FROM #__tz_portfolio_categories AS a'
                  .' LEFT JOIN #__categories AS b ON a.catid = b.id'
                  .' LEFT JOIN #__content AS c ON c.catid = b.id'
                  .' WHERE c.id = '.(int) JRequest::getCmd('id')
                  .$where;

        $db     = & JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }
        $rows   = $db -> loadObject();
        $html   = '';
        if(count($rows)>0)
            $html   = $this -> renderFields($rows -> groupid);

        return $html;
    }

    // Render control
    public function renderFields($groupId=null,$catid=null){

        $lang   = JFactory::getLanguage();
        $lang -> load('com_tz_portfolio',JPATH_ADMINISTRATOR);
        $html   = '';

        // if inherit category
        if($groupId == 0){
            $query  = 'SELECT f.* FROM #__tz_portfolio_fields AS f'
                      .' LEFT JOIN #__tz_portfolio_xref AS x ON x.fieldsid=f.id'
                      .' LEFT JOIN #__tz_portfolio_categories AS c ON c.groupid = x.groupid'
                      .' WHERE f.published=1 AND c.catid = '.$catid
                      .' ORDER BY f.ordering ASC';
        }
        else{
            $query  = 'SELECT f.* FROM #__tz_portfolio_fields AS f'
                      .' LEFT JOIN #__tz_portfolio_xref  AS x ON f.id=x.fieldsid'
                      .' WHERE f.published=1 AND x.groupid='.$groupId
                      .' ORDER BY f.ordering ASC';
        }


        $db = JFactory::getDbo();
        $db -> setQuery($query);

        if(!$db -> query()){
            var_dump($db -> getErrorMsg());
            return false;
        }
        $rows   = $db -> loadObjectList();

        // if have fields
        if(count($rows)>0){

            // require file artilce  in helpers
            require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'article.php');

            foreach($rows as $row){
                $param  = str_replace('[','',$row -> value);
                $param  = str_replace(']','',$param);
                $param  = str_replace('},{','}/////{',$param);
                $param  = explode('/////',$param);
//                if($row -> type=='link')
//                for($i=0;$i<count($param);$i++){
//                    $param[$i]  = json_decode($param[$i]);
//                    $param[$i] -> fieldsid  = $row -> id;
//                }

                $fieldEdits     = null;
                $defaultValue   = null;
                if($row -> default_value != ''){
                    $defaultValue   = explode(',',$row -> default_value);
                }

                if(!$fieldEdits     = $this -> FieldsEdit($row -> id))
                    $fieldEdits = null;

                $j=0;
                for($i=0;$i<count($param);$i++){
                    if(!empty($param[$i])){
                        $param[$i]  = json_decode($param[$i]);
                        $param[$i] -> fieldsid  = $row -> id;
                        //new
                        if($row -> type != 'link' && $row -> type != 'textfield' && $row -> type != 'textarea'){
                            if(isset($defaultValue) && $defaultValue){
                                if(!isset($fieldEdits) || (isset($fieldEdits) && count($fieldEdits)<=0)){
                                    if(in_array($param[$i] -> value,$defaultValue)){
                                        $_fieldEdits[$j]   = new stdClass();
                                        $_fieldEdits[$j] -> fieldsid     = $row -> id;
                                        $_fieldEdits[$j] -> value        = $param[$i] -> name;
                                        $j++;
                                    }
                                }
                            }
                        }
                    }
                    ///////////////
                }


                if($row -> type != 'link' && $row -> type != 'textfield' && $row -> type != 'textarea'){
                    if(isset($_fieldEdits)){
                        $fieldEdits = $_fieldEdits;
                    }
                }

                $value  = null;

                if($fieldEdits){
                    foreach($fieldEdits as &$item){
                        $item -> value  = htmlspecialchars($item -> value);
                        if(($row -> type == 'textfield') || ($row -> type == 'textarea')){
                            if($row -> id == $item -> fieldsid)
                                $value  = $item -> value;
                        }
                    }
                }

                $name   = 'tzfields'.$row -> id;
                $html  .= '<tr><td style="background: #F6F6F6; min-width:100px;" align="right" valign="top">'.$row -> title.'</td><td>';

//                $param  = array_filter($param);

                switch($row -> type){
                    case 'textfield':
                        $html .= ArticleHTML::renderTextField($name,$value);
                        break;
                    case 'textarea':
                        $this -> fieldsid[]   = $row -> id;
                        $html .= ArticleHTML::renderTextArea($name,$value,'',$param[0] -> editor,'90%','200','','',array('image' => true));
                        $html .= '<input type="hidden" name="tz_textarea_hidden[]" value="'
                                 .(($param[0] -> editor == 1)?$row -> id:'').'" class="tzidhidden">';
                        break;
                    case 'select':
                        $html .= ArticleHTML::renderDropDown($name,$param,$fieldEdits);
                        break;
                    case 'multipleSelect':
                        $name   .='[]';
                        $html   .= ArticleHTML::renderDropdown($name,$param,$fieldEdits,'',true,10,'style="min-width:130px;"');
                        break;
                    case 'radio':
                        $html   .= ArticleHTML::renderRadio($name.'[]',$param,$fieldEdits);
                        break;
                    case 'checkbox':
                        $html   .= ArticleHTML::renderCheckBox($name.'[]',$param,'',$fieldEdits);
                        break;
                    case 'link':
                        $url    = null;
                        $target = null;
                        $text   = '';
                        if($fieldEdits){
                            $linkValue  = htmlspecialchars_decode($fieldEdits[0]->value);
                            $linkValue  = html_entity_decode($linkValue);

                            if(preg_match('/>.*</i',$linkValue,$a)) {
                                $text = $a[0];
                            }

                            $text   = str_replace('>','',$text);
                            $text   = str_replace('<','',$text);

                            if(preg_match('/target=".*"/i',$linkValue,$a))
                                if(preg_match('/\".*\"/i',$a[0],$a))
                                    $target = str_replace('"','',$a[0]);

                            if(preg_match('/href=".*"\s/i',$linkValue,$a)){
                                if(preg_match('/".*"/',$a[0],$a)){
                                    $url    = str_replace('"','',$a[0]);
                                    $url    = str_replace('http://','',$url);
                                }
                            }

                        }
                        else{
                            $text   = $param[0] -> name;
                            $target = $param[0] -> target;
                            $url    = str_replace('http://','',$param[0] -> value);
                        }
                        $html   .= ArticleHTML::renderLink($name.'[]',$text,$url,$target);
                        $html   .= '<input type="hidden" name="tz_link_hidden[]" value="'.$row -> id.'">';
//                    case 'file':
//                        $html   .= ArticleHTML::renderFile($name.'[]');
//                        break;
                }
                $html   .= '</td></tr>';

            }
        }
        else{
            $html   = '<div id="system-message-container">'
                        .'<div id="system-message">
                            <div class="alert alert-message">
                                <h4 class="alert-heading">'.JText::_('WARNING').'</h4>
                                <div>
                                        <p>'.JText::_('COM_TZ_PORTFOLIO_FIELD_GROUP_DESC').'</p>
                                </div>
                            </div>
                        </div>
                    </div>';

        }

        return $html;
    }
    function selectgroup(){
        $json 		        = JRequest::getString('json2', null, null, 2);
        $obj_json 	        = json_decode($json);
        $arr['data']        = $this -> renderFields($obj_json -> groupid,$obj_json -> catid);
        $arr['id']          = $obj_json -> id;
        $arr['catid']       = $obj_json -> catid;
        $arr['groupid']     = $obj_json -> groupid;

        return json_encode($arr);
    }

    // Show tags
    public function getTags(){
        $artid  = JRequest::getInt('id',null);
        $db     = $this -> getDbo();
        $tags   = null;

        if($artid){
            $query  = 'SELECT t.name FROM #__tz_portfolio_tags AS t'
                      .' LEFT JOIN #__tz_portfolio_tags_xref AS x ON x.tagsid=t.id'
                      .' WHERE x.contentid='.$artid;

            $db -> setQuery($query);
            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                return false;
            }
            $rows   = $db -> loadColumn();


            if(count($rows)>0){
                return array_unique($rows);
//                foreach($rows as $row){
//                    $tags[]    = trim($row -> name);
//                }
            }
//            if(!empty($tags) && count($tags)>0)
//                $tags  = implode(',',$tags);
        }

        return null;

    }

    // Show fields group
    public function getGroups($catid=null){

//        $artid          = JRequest::getInt('id',null);
        $artid          = $this -> getState('article.id');
        $groups    = '';

        $lang           = JFactory::getLanguage();
        $lang -> load('com_tz_portfolio',JPATH_ADMINISTRATOR);
        $dbo            = $this -> getDbo();
        $rows           = array();
        $arr            = array();

        if($artid){
            $query      = 'SELECT groupid FROM #__tz_portfolio_xref_content'
                          //.' LEFT JOIN #__content AS a ON a.catid = c.catid'
                          .' WHERE contentid='.$artid;
            $dbo -> setQuery($query);
            //$rows   = array();
            if(!$dbo -> query()){
                $this -> setError($dbo -> getErrorMsg());
                return false;
            }
            $rows = $dbo -> loadObjectList();
            foreach($rows as $row){
                $arr[]  = $row -> groupid;
            }
        }
        elseif(!empty($catid)){
            $query  = 'SELECT groupid FROM #__tz_portfolio_categories'
                .' WHERE catid = '.(int) $catid;
            $dbo -> setQuery($query);

            if(!$dbo -> query()){
                $this -> setError($dbo -> getErrorMsg());
                return false;
            }
            $rows = $dbo -> loadObjectList();
            foreach($rows as $row){
                $arr[]  = $row -> groupid;
            }
        }

        $query          = 'SELECT * FROM #__tz_portfolio_fields_group';
        $dbo -> setQuery($query);

        if(!$rows2 = $dbo -> loadObjectList()){
            $groups  = '<select name="groupid" size="1" id="groupid" style="min-width: 130px;">';
            $groups  .= '<option value="0">'.JText::_('COM_TZ_PORTFOLIO_OPTION_INHERIT_CATEGORY').'</option>';
            $groups  .= '</select>';
            return $groups;
        }

        $groups  .= '<option value="0">'.JText::_('COM_TZ_PORTFOLIO_OPTION_INHERIT_CATEGORY').'</option>';

        foreach($rows2 as $row){
            $groups  = $groups.'<option value="'.$row -> id.'"'
                              .((in_array($row -> id,$arr))?' selected="selected"':'').'>&nbsp;&nbsp;'.$row -> name.'</option>';
        }

        $groups  = '<select name="groupid" size="1" id="groupid" style="min-width: 130px;">'
                        .$groups
                        .'</select>';

        return $groups;
    }

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Content', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
            // Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->attribs);
			$item->attribs = $registry->toArray();

			// Convert the metadata field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();

			// Convert the images field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->images);
			$item->images = $registry->toArray();

			// Convert the urls field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->urls);
			$item->urls = $registry->toArray();


			$item->articletext = trim($item->fulltext) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;

            if($pContent   = $this -> getFieldsContent()){
                if(isset($pContent -> audio)){
                    $item -> audio_soundcloud_id            = $pContent -> audio -> audio_id;
                    $item -> audio_soundcloud_hidden_image  = $pContent -> audio -> audiothumb;
                    $item -> audio_soundcloud_title         = $pContent -> audio -> audiotitle;
                }

                $item -> quote_author   = $pContent -> quote_author;
                $item -> quote_text     = $pContent -> quote_text;

                $item -> link_title     = $pContent -> link_title;
                $item -> link_url       = $pContent -> link_url;
                $item -> link_follow    = $pContent -> link_follow;
                $item -> link_target    = $pContent -> link_target;
            }


		}

        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            // Load associated content items
            $app = JFactory::getApplication();
            $assoc = JLanguageAssociations::isEnabled();

            if ($assoc)
            {
                $item->associations = array();

                if ($item->id != null)
                {
                    $associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $item->id);

                    foreach ($associations as $tag => $association)
                    {
                        $item->associations[$tag] = $association->id;
                    }
                }
            }
        }

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_tz_portfolio.article', 'article', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id =  $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id =  $jinput->get('id', 0);
		}
		// Determine correct permissions to check.
		if ($this->getState('article.id'))
		{
			$id = $this->getState('article.id');
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
			// Existing record. Can only edit own articles in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		$user = JFactory::getUser();

		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_tz_portfolio.article.'.(int) $id))
		|| ($id == 0 && !$user->authorise('core.edit.state', 'com_tz_portfolio'))
		)
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');

		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tz_portfolio.edit.article.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('article.id') == 0) {
				$app = JFactory::getApplication();
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_tz_portfolio.articles.filter.category_id')));
			}
            $template   = JModelLegacy::getInstance('Template_Style','TZ_PortfolioModel');
            $data -> set('template_id',$template -> getItemTemplate($this -> getState('article.id')));
		}

		return $data;
	}

    function deleteTags($articleId){
        if(count($articleId)>0){
            $articleId  = implode(',',$articleId);
            $query  = 'DELETE FROM #__tz_portfolio_tags_xref'
                      .' WHERE contentid IN('.$articleId.')';
            $db     = JFactory::getDbo();
            $db -> setQuery($query);
            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                return false;
            }
        }
        return true;
    }

    function deleteImage($artId=null){

        $sizes      = $this -> getState('size');
        $sizes['o'] = null;
        $query      ='SELECT * FROM #__tz_portfolio_xref_content'
                 .' WHERE contentid IN('.implode(',',$artId).')';
        $db         = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            echo $db -> getErrorMsg();
            return false;
        }

        if($rows = $db -> loadObjectList()){
            foreach($rows as $item){
                $path   = null;

                foreach($sizes as $key => $size){
                    //Delete Image
                    if(!empty($item -> images)){
                        $str    = str_replace('.'.JFile::getExt($item -> images),
                                              '_'.$key.'.'.JFile::getExt($item -> images),$item -> images);
                        $str    = str_replace('/',DIRECTORY_SEPARATOR,$str);
                        $path   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);

                        if(JFile::exists($path)){
                            JFile::delete($path);
                        }
                    }

                    //Delete Image Hover
                    if(!empty($item -> images_hover)){
                        $str4    = str_replace('.'.JFile::getExt($item -> images_hover),
                                              '_'.$key.'.'.JFile::getExt($item -> images_hover),$item -> images_hover);
                        $str4    = str_replace('/',DIRECTORY_SEPARATOR,$str4);
                        $path4   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str4);

                        if(JFile::exists($path4)){
                            JFile::delete($path4);
                        }
                    }

                    //Delete Image gallery
                    if(!empty($item -> gallery)){
                        $gallerys   = explode('///',$item -> gallery);
                        foreach($gallerys as $i => $gallery){
                            $str2    = str_replace('.'.JFile::getExt($gallery),
                                              '_'.$key.'.'.JFile::getExt($gallery),$gallery);
                            $path2   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str2);
                            if(JFile::exists($path2)){
                                JFile::delete($path2);
                            }
                        }
                    }

                    //Delete video thumb
                    if(!empty($item -> videothumb)){
                        $str3    = str_replace('.'.JFile::getExt($item -> videothumb),
                                              '_'.$key.'.'.JFile::getExt($item -> videothumb),$item -> videothumb);
                        $path3   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str3);
                        if(JFile::exists($path3)){
                            JFile::delete($path3);
                        }
                    }

                    // Delete audio thumb
                    if(!empty($item -> audiothumb)){
                        $this -> deleteThumb(null,$item -> audiothumb);
                    }
                }
            }
        }
    }

    public function delete(&$pks){

        if($pks){
            $db     = JFactory::getDbo();
            $this -> deleteImage($pks);

            foreach($pks as $item){
                $this -> removeAllAttach($item,'media');

                $query  = 'DELETE FROM #__tz_portfolio_xref_content'
                      .' WHERE contentid = '.$item;
                $db -> setQuery($query);
                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }

                $query  = 'DELETE FROM #__tz_portfolio'
                          .' WHERE contentid = '.$item;
                $db -> setQuery($query);
                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }
            }

            $this -> deleteTags($pks);


        }
        parent::delete($pks);

    }

    function filterTagsName($tagsName=array(),$articleId){

        $query      = 'SELECT * FROM #__tz_portfolio_tags';

        $db         = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }

        $tags   = array();

        $rows   = $db -> loadObjectList();


        for($i=0;$i<count($tagsName);$i++){
                $bool   = false;
            if(count($rows)>0){
                foreach($rows as $row){
                    if(trim($row -> name) == $tagsName[$i]){
                        $bool   = true;
                        break;
                    }
                }
            }
            if($bool != true)
                $tags[] = $tagsName[$i];
        }

        return $tags;

    }

    function insertTagsNew($nameFiltered=array()){
        if(count($nameFiltered)>0){
            $value  = array();
            foreach($nameFiltered as $row){
                $value[]    = '("'.$row.'",1)';
            }
            $value   = implode(',',$value);

            $query  = 'INSERT INTO #__tz_portfolio_tags(`name`,`published`)'
                      .' VALUES '.$value;

            $db     = JFactory::getDbo();
            $db -> setQuery($query);

            if(!$db -> query()){
                $this -> setError($db -> getErrorMsg());
                return false;
            }
            return true;
        }
        return false;
    }

    function getTagsId($tagsName=array()){

        $tagsId = array();

        $query  = 'SELECT * FROM #__tz_portfolio_tags';

        $db     = JFactory::getDbo();
        $db -> setQuery($query);

        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }
        $rows   = $db -> loadObjectList();

        if(count($rows)>0){
            foreach($rows as $row){
                if(is_array($tagsName)){
                    if(in_array(trim($row -> name),$tagsName)){
                        $tagsId[]   = $row -> id;
                    }
                }
                else{
                    if($row -> name == $tagsName){
                        $tagsId[]   = $row -> id;
                    }
                }
            }
        }

        return $tagsId;
    }

    function _checkTags($tagsName=null){
        $_tagsName  = array();
        if($tagsName){
            $tagsName   = str_replace(array(';','/'),',',$tagsName);
            $tagsName   = explode(',',$tagsName);

            if(count($tagsName)>0){
                for($i=0; $i <= count($tagsName)-1; $i++){
                    $bool2   = false;
                    for($j=$i+1; $j <= count($tagsName); $j++){
                        if(trim($tagsName[$i]) == trim($tagsName[$j])){
                            $bool2  = true;
                            break;
                        }
                    }
                    if($bool2 != true){
                        $_tagsName[]    = trim($tagsName[$i]);
                    }
                }
            }
        }
        return $_tagsName;
    }

    function _saveTags($articleId,$tagsName){

        $tagsName   = trim($tagsName);
        $_tagsName   = $this -> _checkTags($tagsName);
        $tagsIds    = array();
        $nameFiltered   =  $this -> filterTagsName($_tagsName,$articleId);

        $this -> insertTagsNew($nameFiltered);

        if($_tagsName){
            $tagsIds    = $this -> getTagsId($_tagsName);
        }

        $query  = 'DELETE FROM #__tz_portfolio_tags_xref'
                  .' WHERE contentid='.(int) $articleId;
        $db     = JFactory::getDbo();
        $db -> setQuery($query);

        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }


        if(count($tagsIds)>0){
            $value  = array();
            foreach($tagsIds as $item){
                $value[]    = '('.$articleId.','.$item.')';
            }
            $value  = implode(',',$value);
            $query  = 'INSERT INTO #__tz_portfolio_tags_xref(`contentid`,`tagsid`)'
                      .'VALUES '.$value;
            $db -> setQuery($query);
            if(!$db -> query()){
                var_dump($db -> getErrorMsg());
                return false;
            }
        }
    }

    protected function _getImageSizes($params){
        if(!$sizes  = $this -> getState('sizeImage')){
            if($params -> get('tz_image_xsmall',100)){
                $sizeImage['XS'] = (int) $params -> get('tz_image_xsmall',100);
            }
            if($params -> get('tz_image_small',200)){
                $sizeImage['S'] = (int) $params -> get('tz_image_small',200);
            }
            if($params -> get('tz_image_medium',400)){
                $sizeImage['M'] = (int) $params -> get('tz_image_medium',400);
            }
            if($params -> get('tz_image_large',600)){
                $sizeImage['L'] = (int) $params -> get('tz_image_large',600);
            }
            if($params -> get('tz_image_xsmall',900)){
                $sizeImage['XL'] = (int) $params -> get('tz_image_xlarge',900);
            }
            return $sizeImage;
        }
        return $this -> getState('sizeImage');
    }

    function getImageHover($fileClient,$fileServer = null,$data=null,$task=null){
        $params     = $this -> getState('params');
        $sizes      = $this -> _getImageSizes($params);
        $sizes['o'] = null;

        if($data){
            if(isset($data['tz_imgHover_current'])){
                $curfile        = $data['tz_imgHover_current'];
            }

            if(isset($data['tz_delete_imgHover'])){
                $imageDelete    = $data['tz_delete_imgHover'];
            }
        }

        if($fileClient){
            if(!empty($fileClient['name'])){
                //Upload image
                $arr    = array('image/jpeg','image/jpg','image/bmp','image/gif','image/png','image/ico');

                // Start check file type
                if(in_array($fileClient['type'],$arr)){
                    // Start check file size
                    if($fileClient['size'] <= TZ_IMAGE_SIZE ){

                        $obj        = new JImage($fileClient['tmp_name']);
                        $width      = $obj -> getWidth();
                        $height     = $obj -> getHeight();
                        $sizes['o'] = $width;
                        $str        = $this -> imageUrl.'/cache/'.
                            ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                            .'-'.$this -> getState($this -> getName().'.id').'-h.'
                                  .JFile::getExt($fileClient['name']);

                        foreach($sizes as $key => $newWidth){
                            // Delete current Image file
                            if(!$this -> tztask){
                                if(isset($curfile) && !empty($curfile)){
                                    $curName    = null;
                                    $curName    = str_replace('.'.JFile::getExt($curfile)
                                        ,'_'.$key.'.'.JFile::getExt($curfile),$curfile);
                                    $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;

                                    if(JFile::exists($_curFile)){
                                        JFile::delete($_curFile);
                                    }

                                }
                            }

                            //Upload Image
                            $destPath   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);
                            $destPath   = str_replace('.'.JFile::getExt($destPath),'_'.$key.'.'.JFile::getExt($destPath),$destPath);

                            if($key == 'o') {
                                if($params -> get('allow_upload_original_image',1)) {
                                    JFile::copy($fileClient['tmp_name'], $destPath);
                                }
                            }else{
                                $newHeight = ($height * (int)$newWidth) / $width;
                                $newImage = $obj->resize($newWidth, $newHeight);
                                $type = $this->_getImageType($str);
                                $newImage->toFile($destPath, $type);
                            }

                        }
                        return $str;
                    }
                    else{
                        JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_IMAGE_HOVER_SIZE_TOO_LARGE'));
                    }
                    // End check file size
                }
                else{
                    JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_IMAGE_HOVER_FILE_NOT_SUPPORTED'));
                }
                // End check file type

            }
            elseif(!empty($fileServer)){
                // Check size
                $originalFile   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$fileServer);
                // Start Check File choose from server
                if(JFile::exists($originalFile)){
                    $obj        = new JImage($originalFile);
                    $width      = $obj -> getWidth();
                    $height     = $obj -> getHeight();
                    $sizes['o'] = $width;

                    $str        = $this -> imageUrl.'/cache/'.
                        ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                        .'-'.$this -> getState($this -> getName().'.id').'-h.'
                              .JFile::getExt($fileServer);

                    foreach($sizes as $key => $newWidth){
                        // Delete current Image file
                        if(!$this -> tztask){
                            if(isset($curfile) && !empty($curfile)){
                                $curName    = null;
                                $curName    = str_replace('.'.JFile::getExt($curfile)
                                    ,'_'.$key.'.'.JFile::getExt($curfile),$curfile);
                                $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;

                                if(JFile::exists($_curFile)){
                                    JFile::delete($_curFile);
                                }
                            }
                        }

                        //Upload Image
                        $destPath   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);
                        $destPath   = str_replace('.'.JFile::getExt($destPath),'_'.$key.'.'.JFile::getExt($destPath),$destPath);

                        if($key == 'o') {
                            if($params -> get('allow_upload_original_image',1)) {
                                JFile::copy($originalFile, $destPath);
                            }
                        }else{
                            $newHeight = ($height * (int)$newWidth) / $width;
                            $newImage = $obj->resize($newWidth, $newHeight, $originalFile);
                            $type = $this->_getImageType($str);
                            $newImage->toFile($destPath, $type);
                        }

                    }
                    return $str;
                }
                else{
                    JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_IMAGE_HOVER_FILE_DOES_NOT_EXIST'));
                    return '""';
                }
                // End Check File choose from server
            }
            else{
                $fileName   = '';
                if(isset($curfile) && !empty($curfile)){
                    // Delete current Image file
                    if(isset($imageDelete)){
                        if($this -> tztask){
                            return '';
                        }

                        $curName    = null;

                        foreach($sizes as $key => $val){
                            $curName    = str_replace('.'.JFile::getExt($curfile)
                                ,'_'.$key.'.'.JFile::getExt($curfile),$curfile);
                            $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;

                            if(JFile::exists($_curFile)){
                                JFile::delete($_curFile);
                            }

                        }
                        return null;
                    }

                    $curPath    = null;
                    $str2       = null;
                    if($this -> tztask){
                        $str2    = $this -> imageUrl.'/cache/'.
                            ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                            .'-'.$this -> getState($this -> getName().'.id').'-h.'
                          .JFile::getExt($curfile);

                        foreach($sizes as $key => $val){
                            $curPath    =  str_replace('.'.JFile::getExt($curfile),'_'.$key.'.'.JFile::getExt($curfile),$curfile);
                            $curPath    = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$curPath);

                            $destPath2  = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str2);
                            $destPath2  = str_replace('.'.JFile::getExt($destPath2),'_'.$key.'.'.JFile::getExt($destPath2),$destPath2);
                            if(JFile::exists($curPath)){
                                JFile::copy($curPath,$destPath2);
                            }
                        }
                        $fileName   = $str2;

                    }
                    else{
                        $fileName   = $curfile;
                    }
                }

                return $fileName;
            }
        }
        return '';
    }

    function getImage($file = null,$data = null,$task=null){

        $imageGallery   = new stdClass();
        $imageGallery -> name   = '';
        $imageGallery -> title   = '';
        $params = $this -> getState('params');
        if(!$sizes  = $this -> getState('sizeImage')){
            $sizeImage  = array();
            if($params -> get('tz_image_xsmall',100)){
                $sizeImage['XS'] = (int) $params -> get('tz_image_xsmall',100);
            }
            if($params -> get('tz_image_small',200)){
                $sizeImage['S'] = (int) $params -> get('tz_image_small',200);
            }
            if($params -> get('tz_image_medium',400)){
                $sizeImage['M'] = (int) $params -> get('tz_image_medium',400);
            }
            if($params -> get('tz_image_large',600)){
                $sizeImage['L'] = (int) $params -> get('tz_image_large',600);
            }
            if($params -> get('tz_image_xsmall',900)){
                $sizeImage['XL'] = (int) $params -> get('tz_image_xlarge',900);
            }
            $sizes  = $sizeImage;
        }

        $sizes['o'] = null;


        $fileServer = $data['tz_img_gallery_server'];

        if(!empty($data['tz_image_title']))
            $imageGallery -> title  = $data['tz_image_title'];


        if(isset($data['tz_image_current'])){
            $curfile        = $data['tz_image_current'];
        }

        if(isset($data['tz_delete_image'])){
            $imageDelete    = $data['tz_delete_image'];
        }

        //Upload image
        $arr    = array('image/jpeg','image/jpg','image/bmp','image/gif','image/png','image/ico');

        //Client
        if(!empty($file['name'])){

            // Is the PHP tmp directory missing?
            if ($file['error'] && ($file['error'] == UPLOAD_ERR_NO_TMP_DIR))
            {
                JError::raiseWarning('', JText::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR')
                    . '<br />' . JText::_('COM_TZ_PORTFOLIO_MSG_WARNINGS_PHPUPLOADNOTSET'));
            }else{
                // Start check the image file type
                if(in_array(strtolower($file['type']),$arr)){

                    // Start check file size
                    if($file['size']<= TZ_IMAGE_SIZE){
                        // Check and set chmod folder again
                        $folder         = JPATH_SITE.DIRECTORY_SEPARATOR
                            .str_replace('/',DIRECTORY_SEPARATOR,$this -> imageUrl).DIRECTORY_SEPARATOR.'cache';
                        $chmodFolder    = JPath::getPermissions($folder);

                        if($chmodFolder != 'rwxrwxrwx' || $chmodFolder != 'rwxr-xr-x'){
                            JPath::setPermissions($folder);
                        }

                        $obj    = new JImage($file['tmp_name']);
                        $width  = $obj -> getWidth();
                        $height = $obj -> getHeight();
                        $str    = $this -> imageUrl.'/cache/'
                            .((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                            .'-'.$this -> getState($this -> getName().'.id').'.'.JFile::getExt($file['name']);

                        $sizes['o'] = $width;

                        foreach($sizes as $key => $newWidth){
                            // Delete current Image file
                            if(!$this -> tztask){
                                if(isset($curfile) && !empty($curfile)){
                                    $curName    = null;
                                    $curName    = str_replace('.'.JFile::getExt($curfile)
                                        ,'_'.$key.'.'.JFile::getExt($curfile),$curfile);
                                    $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;

                                    if(JFile::exists($_curFile)){
                                        JFile::delete($_curFile);
                                    }

                                }
                            }

                            //Upload Image
                            $destPath   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);
                            $destPath   = str_replace('.'.JFile::getExt($destPath),'_'.$key.'.'.JFile::getExt($destPath),$destPath);

                            if($key == 'o') {
                                if($params -> get('allow_upload_original_image',1)) {
                                    JFile::copy($file['tmp_name'], $destPath);
                                }
                            }else{
                                if($key == 'o'){
                                    die('4324');
                                }
                                $newHeight = ($height * (int)$newWidth) / $width;
                                $newImage = $obj->resize($newWidth, $newHeight, $file['tmp_name']);
                                $type = $this->_getImageType($str);
                                $newImage->toFile($destPath, $type);
                            }

                        }
                        $imageGallery -> name   = $str;

                    }
                    else{
                        // Is the max upload size too small in php.ini?
                        if ($file['error'] && ($file['error'] == UPLOAD_ERR_INI_SIZE))
                        {
                            JError::raiseNotice('', JText::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR')
                                . '<br />' . JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_TOO_LARGE')
                                . '<br />' . JText::_('COM_TZ_PORTFOLIO_MSG_WARNINGS_SMALLUPLOADSIZE'));
                        }else {
                            JError::raiseNotice('',JText::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR')
                                . '<br />'. JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_TOO_LARGE'));
                        }
                    }
                    // End check file size
                }
                else{
                    JError::raiseNotice('',JText::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR')
                        . '<br />'.JText::_('COM_TZ_PORTFOLIO_IMAGE_FILE_NOT_SUPPORTED')
                        . '<br />'. JText::_('COM_TZ_PORTFOLIO_MSG_WARNINGS_SMALLUPLOADSIZE'));
                }
                // End check the image file type
            }
        }//Image from Client
        elseif(!empty($fileServer[0])){
            // Check size
            $originalFile   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$fileServer[0]);

            // Start check the image file that it was chosen from server;
            if(JFile::exists($originalFile)){
                $obj    = new JImage($originalFile);
                $width  = $obj -> getWidth();
                $height = $obj -> getHeight();

                $str    = $this -> imageUrl.'/cache/'.
                    ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                    .'-'.$this -> getState($this -> getName().'.id').'.'.JFile::getExt($fileServer[0]);

                $sizes['o']   =  $width;

                foreach($sizes as $key => $newWidth){
                    // Delete current Image file
                    if(!$this -> tztask){
                        if(isset($curfile) && !empty($curfile)){
                            $curName    = null;
                            $curName    = str_replace('.'.JFile::getExt($curfile)
                                ,'_'.$key.'.'.JFile::getExt($curfile),$curfile);
                            $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;

                            if(JFile::exists($_curFile)){
                                JFile::delete($_curFile);
                            }
                        }
                    }

                    //Upload Image
                    $destPath   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);
                    $destPath   = str_replace('.'.JFile::getExt($destPath),'_'.$key.'.'.JFile::getExt($destPath),$destPath);

                    if($key == 'o') {
                        if($params -> get('allow_upload_original_image',1)) {
                            JFile::copy($originalFile,$destPath);
                        }
                    }else{
                        $newHeight  = ($height*(int) $newWidth)/$width;
                        $newImage   = $obj -> resize((int) $newWidth,$newHeight,$originalFile);
                        $type       = $this -> _getImageType($str);
                        $newImage -> toFile($destPath,$str);
                    }

                }
                $imageGallery -> name   = $str;
            }
            else{
                JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_IMAGE_FILE_DOES_NOT_EXIST'));
            }
            // End check the image file that it was chosen from server;
        }
        else{

            // Delete current Image file
            if(isset($imageDelete)){
                if($this -> tztask){
                    $imageGallery -> name   = null;
                    $imageGallery -> title  = '';
                    return $imageGallery;
                }

                if(isset($curfile) && !empty($curfile)){
                    $curName    = null;

                    foreach($sizes as $key => $val){
                        $curName    = str_replace('.'.JFile::getExt($curfile)
                            ,'_'.$key.'.'.JFile::getExt($curfile),$curfile);
                        $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;

                        if(JFile::exists($_curFile)){
                            JFile::delete($_curFile);
                        }

                    }
                    $imageGallery -> name   = null;
                    $imageGallery -> title  = '';
                    return $imageGallery;
                }
            }

            if(isset($curfile)){
                $curPath    = null;
                $str2       = null;
                if($this -> tztask){
                    $str2    = $this -> imageUrl.'/cache/'
                        .((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias']).'-'
                        .$this -> getState($this -> getName().'.id').'.'.JFile::getExt($curfile);

                    foreach($sizes as $key => $val){
                        $curPath    =  str_replace('.'.JFile::getExt($curfile),'_'.$key.'.'.JFile::getExt($curfile),$curfile);
                        $curPath    = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$curPath);

                        $destPath2  = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str2);
                        $destPath2  = str_replace('.'.JFile::getExt($destPath2),'_'.$key.'.'.JFile::getExt($destPath2),$destPath2);
                        if(JFile::exists($curPath)){
                            JFile::copy($curPath,$destPath2);
                        }
                    }
                    $imageGallery -> name   = $str2;
                }
                else{
                    $imageGallery -> name   = $curfile;
                }
            }

        }

        return $imageGallery;
    }

    protected function _getImageType($filename){
        if($filename){
            $type   = JFile::getExt($filename);
            if(strtolower($type) == 'png'){
                return IMAGETYPE_PNG;
            }
            elseif(strtolower($type) == 'gif'){
                return IMAGETYPE_GIF;
            }
            else{
                return IMAGETYPE_JPEG;
            }
        }
        return false;
    }

    function getGallery($files = null,$data = null,$params = null,$task=null){

        $params = $this -> getState('params');
        if(!$size    = $this -> getState('size')){
            $sizes  = array();
            if($params -> get('tz_image_gallery_xsmall')){
                $sizes['XS'] = (int) $params -> get('tz_image_gallery_xsmall');
            }
            if($params -> get('tz_image_gallery_small')){
                $sizes['S'] = (int) $params -> get('tz_image_gallery_small');
            }
            if($params -> get('tz_image_gallery_medium')){
                $sizes['M'] = (int) $params -> get('tz_image_gallery_medium');
            }
            if($params -> get('tz_image_gallery_large')){
                $sizes['L'] = (int) $params -> get('tz_image_gallery_large');
            }
            if($params -> get('tz_image_gallery_xsmall')){
                $sizes['XL'] = (int) $params -> get('tz_image_gallery_xlarge');
            }
            $size   = $sizes;
        }

        $size['o'] = null;

        $imageGallery   = new stdClass();
        $imageGallery -> name   = '';
        $imageGallery -> title  = '';

        $galleryTitle   = $data['tz_image_gallery_title'];

        $fileServer  = $data['tz_img_gallery_server'];
        array_shift($fileServer);
        $arr2       = null;

        if(isset($data['tz_image_gallery_current'])){
            $curfile        = $data['tz_image_gallery_current'];
            foreach($curfile as &$row){
                //Delete Url root
                $pattern    = JURI::root();
                $row   = preg_replace('/'.addcslashes($pattern,'/:').'/','',$row);
                $row    = str_replace('_S.'.JFile::getExt($row),'.'.JFile::getExt($row)
                    ,$row);
            }
        }

        if(isset($data['tz_delete_image_gallery'])){
            $imageDelete    = $data['tz_delete_image_gallery'];
        }

        // Check and set chmod folder again
        $folder         = JPATH_SITE.DIRECTORY_SEPARATOR
            .str_replace('/',DIRECTORY_SEPARATOR,$this -> imageUrl).DIRECTORY_SEPARATOR.'cache';
        $chmodFolder    = JPath::getPermissions($folder);

        if($chmodFolder != 'rwxrwxrwx' || $chmodFolder != 'rwxr-xr-x'){
            JPath::setPermissions($folder);
        }

        //Upload image
        $arr    = array('image/jpeg','image/jpg','image/bmp','image/gif','image/png','image/ico');

        $err    = array();
        $err2   =array();

        foreach($fileServer as $i => $item){
            if($files && count($files['name'])>0 && !empty($files['name'][$i])){
                if(count($files['type'])>0){
                    if(!empty($files['name'][$i])){
                        if(!empty($files['type'][$i]) && in_array(strtolower($files['type'][$i]),$arr)){

                            $_type      = strtolower(JFile::getExt($files['name'][$i]));
                            $_type  = '.'.$_type;
                            $str = $this -> imageUrl.'/cache/'
                                .((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                                .'-'.$this -> getState($this -> getName().'.id').'-'.$i.$_type;
                            $arr2[$i]   = $str;

                            // Check size
                            if($files['size'][$i] <= TZ_IMAGE_SIZE){

                                $curName    = null;
                                $destPath   = null;
                                $obj        = new JImage($files['tmp_name'][$i]);
                                $width      = $obj->getWidth();
                                $height     = $obj->getHeight();
                                $size['o']  = $width;

                                foreach($size as $key => $newWidth){

                                    //Delete current file if have it
                                    if(!$this -> tztask){
                                        if(isset($curfile) && isset($curfile[$i]) && !empty($curfile[$i])){
                                            $curName    = str_replace('.'.JFile::getExt($curfile[$i])
                                                ,'_'.$key.'.'.JFile::getExt($curfile[$i]),$curfile[$i]);
                                            $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;
                                            if(JFile::exists($_curFile)){
                                                JFile::delete($_curFile);
                                            }
                                        }
                                    }

                                    $destPath   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);
                                    $destPath   = str_replace('.'.JFile::getExt($destPath),'_'.$key.'.'.JFile::getExt($destPath),$destPath);

                                    if($key == 'o') { // Upload original image
                                        if($params -> get('allow_upload_original_image',1)) {
                                            JFile::copy($files['tmp_name'][$i], $destPath);
                                        }
                                    }else{
                                        $newHeight = ($height * (int)$newWidth) / $width;
                                        $newImage = $obj->resize($newWidth, $newHeight, $files['tmp_name'][$i]);
                                        $type = $this->_getImageType($files['name'][$i]);
                                        $newImage->toFile($destPath, $type);
                                    }

                                }

                            }
                            else{
                                $err2[] = $files['name'][$i];
                            }

                        }
                        else{//if(!empty($type) && !in_array(strtolower($files['type'][$i]),$arr)){

                            $err[]  = $files['name'][$i];
                        }
                    }
                }


            }//Image client
            elseif(!empty($fileServer[$i])){
                if(!empty($fileServer[$i])){
                    $original   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$fileServer[$i]);

                    if(!JFile::exists($original)){
                        $this -> setError(JText::_('Invalid file:').' "'.$fileServer[$i].'"');
                        return false;
                    }

                    $str = $this -> imageUrl.'/cache/'
                        .((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias']).'-'
                        .$this -> getState($this -> getName().'.id').'-'.$i.'.'.JFile::getExt($item);
                    $arr2[$i]   = $str;

                    $curName    = null;
                    $obj        = new JImage($original);
                    $width      = $obj -> getWidth();
                    $height     = $obj -> getHeight();
                    $size['o']  = $width;

                    foreach($size as $key => $newWidth){
                        //Delete current file if have it
                        if(!$this -> tztask){
                            if(isset($curfile) && isset($curfile[$i]) && !empty($curfile[$i])){
                                $curName    = str_replace('.'.JFile::getExt($curfile[$i])
                                    ,'_'.$key.'.'.JFile::getExt($curfile[$i]),$curfile[$i]);
                                $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;

                                if(JFile::exists($_curFile)){
                                    JFile::delete($_curFile);
                                }
                            }
                        }

                        $destPath   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);
                        $destPath   = str_replace('.'.JFile::getExt($destPath),'_'.$key.'.'.JFile::getExt($destPath),$destPath);

                        if($key == 'o') { // Upload original image
                            if($params -> get('allow_upload_original_image',1)) {
                                JFile::copy($original, $destPath);
                            }
                        }else{
                            $newHeight  = ($height*(int) $newWidth)/$width;
                            $newImage   = $obj -> resize($newWidth,$newHeight,$original);
                            $type       = $this -> _getImageType($str);
                            $newImage -> toFile($destPath,$type);
                        }
                    }
                }

            }// Image from server
            else{
                if(isset($curfile) && isset($curfile[$i]) && !empty($curfile[$i])){
                    $pattern    = JURI::root();
                    $curName   = preg_replace('/'.addcslashes($pattern,'/:').'/','',$curfile[$i]);
                    $curName   = str_replace('_S.'.JFile::getExt($curName),'.'.JFile::getExt($curName),$curName);
                    if($this -> tztask){

                        $str2    = $this -> imageUrl.'/cache/'
                            .((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias']).'-'
                            .$this -> getState($this -> getName().'.id').'-'.$i.'.'.JFile::getExt($curName);


                        foreach($size as $key => $val){
                            $curPath    =  str_replace('.'.JFile::getExt($curName),'_'.$key.'.'.JFile::getExt($curName),$curName);
                            $curPath    = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$curPath);

                            $destPath2  = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str2);
                            $destPath2  = str_replace('.'.JFile::getExt($destPath2),'_'.$key.'.'.JFile::getExt($destPath2),$destPath2);
                            if(JFile::exists($curPath)){
                                JFile::copy($curPath,$destPath2);
                            }
                        }
                        $arr2[$i]   = $str2;
                    }
                    else{
                        $arr2[$i] = $curName;
                    }
                }
                // Delete current Image file
                if(isset($imageDelete)){
                    if(in_array($i,$imageDelete)){
                        if(isset($curfile) && isset($curfile[$i]) && !empty($curfile[$i])){
                            $curName    = null;
                            if(!$this -> tztask){

                                foreach($size as $key => $val){
                                    $curName    = str_replace('.'.JFile::getExt($curfile[$i])
                                        ,'_'.$key.'.'.JFile::getExt($curfile[$i]),$curfile[$i]);
                                    $_curFile    = JPATH_SITE.DIRECTORY_SEPARATOR.$curName;

                                    if(JFile::exists($_curFile)){
                                        JFile::delete($_curFile);
                                    }

                                }
                            }
                            $arr2[$i]   = null;
                            $galleryTitle[$i] = null;

                        }
                    }
                }
            }

        }// end foreach

        if($count = count($err)>0){
            JError::raiseNotice('',JText::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR')
                . '<br />'.JText::sprintf('COM_TZ_PORTFOLIO_SINGLE_IMAGE_SLIDER_FILE_NOT_SUPPORTED',
                implode(',',$err)));
            if($count > 1){
                JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR')
                    . '<br />'.JText::sprintf('COM_TZ_PORTFOLIO_MULTI_IMAGE_SLIDER_FILE_NOT_SUPPORTED',
                    $count,$err));
            }
        }

        if($count = count($err2)>0){
            if($count > 1){
                JError::raiseNotice('',JText::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR')
                    . '<br />' .JText::sprintf('COM_TZ_PORTFOLIO_MULTI_IMAGE_SLIDER_SIZE_TOO_LARGE',
                    $count,$err2)
                    . '<br />' . JText::_('COM_TZ_PORTFOLIO_MSG_WARNINGS_SMALLUPLOADSIZE'));
            }else{
                JError::raiseNotice('',JText::_('COM_TZ_PORTFOLIO_MSG_INSTALL_WARNINSTALLUPLOADERROR')
                    . '<br />' .JText::sprintf('COM_TZ_PORTFOLIO_SINGLE_IMAGE_SLIDER_SIZE_TOO_LARGE',
                        implode(',',$err2))
                    . '<br />' . JText::_('COM_TZ_PORTFOLIO_MSG_WARNINGS_SMALLUPLOADSIZE'));
            }
        }

        $imageSave  = null;
        if($arr2 && count($arr2)){
            foreach($arr2 as $row){
                if($row && !empty($row)){
                    $imageSave[]    = $row;
                }
            }
        }

        if($imageSave)
            $imageGallery -> name  = implode('///',$imageSave);

        if(count($data['tz_image_gallery_title']>0)){
            $b  = array();
            foreach($galleryTitle as $i => $item){
                if(!empty($item) && $i<= count($arr2))
                    $b[] = $item;
            }
            $imageGallery -> title  = implode('///',$b);
        }

        return $imageGallery;
    }

    function getVideo($data = null,$task = null){
        $imageGallery           = new stdClass();
        $imageGallery -> name   = '';
        $imageGallery -> title  = '';
        $imageGallery -> thumb  = '';
        $destPath               = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',
                DIRECTORY_SEPARATOR,$this ->imageUrl).DIRECTORY_SEPARATOR.'cache';

        $params                 = $this -> getState('params');
        $size                   = $this -> _getImageSizes($params);
        $size['o']              = null;

        if(!JFolder::exists($destPath)){
            JFolder::create($destPath);
        }
        if(!JFile::exists($destPath.DIRECTORY_SEPARATOR.'index.html')){
            JFile::write($destPath.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
        }
        if(!JFolder::exists($destPath.DIRECTORY_SEPARATOR.'thumbnail')){
            JFolder::create($destPath.DIRECTORY_SEPARATOR.'thumbnail');
        }
        if(!JFile::exists($destPath.DIRECTORY_SEPARATOR.'thumbnail'.DIRECTORY_SEPARATOR.'index.html')){
            JFile::write($destPath.DIRECTORY_SEPARATOR.'thumbnail'.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
        }

        $destPath       .= DIRECTORY_SEPARATOR.'thumbnail';

        // Check and set chmod folder again
        $chmodFolder    = JPath::getPermissions($destPath);

        if($chmodFolder != 'rwxrwxrwx' || $chmodFolder != 'rwxr-xr-x'){
            JPath::setPermissions($destPath);
        }


        if($data){
            if(isset($data['tz_thumb_global_hidden'])
               && !empty($data['tz_thumb_global_hidden'])){
                $this -> deleteThumb(null,$data['tz_thumb_global_hidden']);
            }
            $media  = null;
            switch ($data['tz_media_type']){
                default:
                    $data['tz_media_code'] = JRequest::getVar('tz_media_code','','post','string',JREQUEST_ALLOWRAW);

                    if($data['tz_media_code']){
                        $media['code']  = 'default:'.$data['tz_media_code'];
                        $media['title'] = $data['tz_media_title'];
                        $media['thumb'] = '';
                        if(isset($data['tz_thumb_hidden'])){
                            $media['thumb'] = $data['tz_thumb_hidden'];
                        }

                        if(isset($data['tz_thumb_del'])){
                            if(!$this -> tztask){
                                $this -> deleteThumb($data['id']);
                            }
                            $media['thumb'] = '';
                        }

                        if($this -> tztask && !empty($media['thumb'])){
                            $str2    = $this -> imageUrl.'/cache/thumbnail/'
                                .((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias']).'-'.
                                $this -> getState($this -> getName().'.id').'.'
                              .JFile::getExt($media['thumb']);

                            foreach($size as $key => $val){
                                $curPath    =  str_replace('.'.JFile::getExt($media['thumb']),
                                                           '_'.$key.'.'.JFile::getExt($media['thumb']),$media['thumb']);
                                $curPath    = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$curPath);

                                $destPath2  = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str2);
                                $destPath2  = str_replace('.'.JFile::getExt($destPath2),'_'.$key.'.'.JFile::getExt($destPath2),$destPath2);
                                if(JFile::exists($curPath)){
                                    JFile::copy($curPath,$destPath2);
                                }
                            }
                            $media['thumb']  = $str2;
                        }

                        if($data['tz_thumb']){
                            if(JFile::exists(JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$data['tz_thumb']))){

                                $fileName   = $this -> imageUrl.'/cache/thumbnail/'
                                    .((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias']).'-'.
                                    $this -> getState($this -> getName().'.id').'.'
                                    .JFile::getExt($data['tz_thumb']);

                                if(!$this -> tztask){
                                    $this -> deleteThumb($data['id']);
                                }

                                $url        = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$data['tz_thumb']);

                                $obj        = new JImage($url);
                                $width      = $obj -> getWidth();
                                $height     = $obj -> getHeight();
                                $size['o']  = $width;

                                foreach($size as $key => $newWidth){
                                    $str        = str_replace('.'.JFile::getExt($fileName)
                                        ,'_'.$key.'.'.JFile::getExt($fileName),$fileName);
                                    $str        = str_replace('/',DIRECTORY_SEPARATOR,$str);
                                    $_destPath  = JPATH_SITE.DIRECTORY_SEPARATOR.$str;

                                    if($key == 'o') {
                                        if($params -> get('allow_upload_original_image',1)) {
                                            JFile::copy($url, $_destPath);
                                        }
                                    }else{
                                        $newHeight = ($newWidth * $height) / $width;
                                        $newImage = $obj->resize($newWidth, $newHeight, $url);
                                        $type = $this->_getImageType($str);
                                        $newImage->toFile($_destPath, $type);
                                    }
                                }
                                $media['thumb'] = $fileName;
                            }
                            else{
                                JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_THUMBNAIL_FILE_DOES_NOT_EXIST'));
                            }
                        }
                    }else{
                        if(!$this -> tztask){
                            $this -> deleteThumb($data['id']);
                        }
                    }
                    $bool           = 3;
                    break;
                case 'youtube':
                    $bool           = 3;
                    if(JRequest::getCmd('task') != 'save2copy'){
                        $this -> deleteThumb($data['id']);
                    }
                    if(!empty($data['tz_media_code_youtube'])){
                        $media['code']  = 'youtube:'.$data['tz_media_code_youtube'];
                        $media['title'] = $data['tz_media_title_youtube'];
                        $thumbUrl   = 'http://img.youtube.com/vi/'.$data['tz_media_code_youtube'].'/maxresdefault.jpg';
                        $file       = new Services_Yadis_PlainHTTPFetcher();
                        $thumb      = $file ->get($thumbUrl);

                        if($thumb -> status == '404'){
                            $thumbUrl   = 'http://img.youtube.com/vi/'.$data['tz_media_code_youtube'].'/mqdefault.jpg';
                            $thumb      = $file ->get($thumbUrl);
                        }

                        $fileName   = null;
                        $_fileName   = ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                            .'-'.$this -> getState($this -> getName().'.id')
                            .'.'. JFile::getExt($thumbUrl);

                        if(!JFolder::exists($destPath.DIRECTORY_SEPARATOR.'youtube')){
                            JFolder::create($destPath.DIRECTORY_SEPARATOR.'youtube');
                        }
                        if(!JFile::exists($destPath.DIRECTORY_SEPARATOR.'youtube'.DIRECTORY_SEPARATOR.'index.html')){
                            JFile::write($destPath.DIRECTORY_SEPARATOR.'youtube'.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
                        }

                        // Check and set chmod folder again
                        $folder         = $destPath.DIRECTORY_SEPARATOR.'youtube';
                        $chmodFolder    = JPath::getPermissions($folder);

                        if($chmodFolder != 'rwxrwxrwx' || $chmodFolder != 'rwxr-xr-x'){
                            JPath::setPermissions($folder);
                        }

                        //Upload Image
                        if(!JFile::exists($destPath.DIRECTORY_SEPARATOR.'youtube'.DIRECTORY_SEPARATOR.$_fileName)){
                            if($thumb){
                                if(JFile::write($destPath.DIRECTORY_SEPARATOR.'youtube'.DIRECTORY_SEPARATOR.$_fileName,$thumb -> body)){
                                    $url = $destPath.DIRECTORY_SEPARATOR.'youtube'.DIRECTORY_SEPARATOR.$_fileName;
                                }
                            }
                        }

                        if(isset($url) && !empty($url)){
                            $obj        = new JImage($url);
                            $width      = $obj -> getWidth();
                            $height     = $obj -> getHeight();
                            $size['o']  = $width;

                            foreach($size as $key => $newWidth){
                                $newHeight  = ($newWidth * $height)/$width;
                                $newImage   = $obj -> resize($newWidth,$newHeight);

                                $str   = $this -> imageUrl.'/cache/thumbnail/youtube/'.str_replace('.'.JFile::getExt($_fileName)
                                    ,'_'.$key.'.'.JFile::getExt($_fileName),$_fileName);
                                $_destPath   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);

                                if($key == 'o') {
                                    if($params -> get('allow_upload_original_image',1)) {
                                        JFile::copy($url, $_destPath);
                                    }
                                }else{
                                    $type = $this->_getImageType($str);
                                    $newImage->toFile($_destPath, $type);
                                }
                            }
                            $fileName   = $this -> imageUrl.'/cache/thumbnail/youtube/'.$_fileName;

                            JFile::delete($url);
                        }
                        $media['thumb'] = '';
                        if($fileName){
                            $media['thumb'] = $fileName;
                        }
                    }
                    break;
                case 'vimeo':
                    $bool           = 3;
                    if(JRequest::getCmd('task') != 'save2copy'){
                        $this -> deleteThumb($data['id']);
                    }
                    if(!empty($data['tz_media_code_vimeo'])){
                        $media['code']  = 'vimeo:'.$data['tz_media_code_vimeo'];
                        $media['title'] = $data['tz_media_title_vimeo'];

                        $thumbUrl   = 'http://vimeo.com/api/v2/video/'.$data['tz_media_code_vimeo'].'.php';
                        $file       = new Services_Yadis_PlainHTTPFetcher();
                        $vimeo      = $file ->get($thumbUrl);
                        $vimeo      = unserialize($vimeo -> body);
                        $thumbUrl   = $vimeo[0]['thumbnail_large'];

                        if($ipos = stripos($thumbUrl,'_')){
                            $img_w  = (int)substr($thumbUrl,$ipos+1,strlen($thumbUrl));
                            if($img_w < $vimeo[0]['width']) {
                                $thumbUrl = substr($thumbUrl, 0, $ipos).'_'.$vimeo[0]['width']
                                    .'.'.JFile::getExt($thumbUrl);
                            }
                        }

                        $thumb      = $file -> get($thumbUrl);
                        $fileName   = null;
                        $_fileName  = ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                            .'-'.$this -> getState($this -> getName().'.id').'.'. JFile::getExt($thumbUrl);

                        if(!JFolder::exists($destPath.DIRECTORY_SEPARATOR.'vimeo')){
                            JFolder::create($destPath.DIRECTORY_SEPARATOR.'vimeo');
                        }
                        if(!JFile::exists($destPath.DIRECTORY_SEPARATOR.'vimeo'.DIRECTORY_SEPARATOR.'index.html')){
                            JFile::write($destPath.DIRECTORY_SEPARATOR.'vimeo'.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
                        }

                        // Check and set chmod folder again
                        $folder         = $destPath.DIRECTORY_SEPARATOR.'vimeo';
                        $chmodFolder    = JPath::getPermissions($folder);

                        if($chmodFolder != 'rwxrwxrwx' || $chmodFolder != 'rwxr-xr-x'){
                            JPath::setPermissions($folder);
                        }

                        if(!JFile::exists($destPath.DIRECTORY_SEPARATOR.'vimeo'.DIRECTORY_SEPARATOR.$_fileName)){
                            if($thumb){
                                if(JFile::write($destPath.DIRECTORY_SEPARATOR.'vimeo'.DIRECTORY_SEPARATOR.$_fileName,$thumb -> body)){
                                    $url = JPATH_SITE.DIRECTORY_SEPARATOR.$this -> imageUrl.'/cache/thumbnail/vimeo/'.$_fileName;
                                }
                            }
                        }

                        if(isset($url) && !empty($url)){
                            $obj    = new JImage($url);
                            $width  = $obj -> getWidth();
                            $height = $obj -> getHeight();
                            foreach($size as $key => $newWidth){
                                $newHeight  = ($newWidth * $height)/$width;
                                $newImage   = $obj -> resize($newWidth,$newHeight,$url);

                                $str   = $this -> imageUrl.'/cache/thumbnail/vimeo/'.str_replace('.'.JFile::getExt($_fileName)
                                    ,'_'.$key.'.'.JFile::getExt($_fileName),$_fileName);
                                $_destPath   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$str);

                                if($key == 'o') {
                                    if($params -> get('allow_upload_original_image',1)) {
                                        JFile::copy($url, $_destPath);
                                    }
                                }else{
                                    $type = $this->_getImageType($str);
                                    $newImage->toFile($_destPath, $type);
                                }
                            }
                            $fileName   = $this -> imageUrl.'/cache/thumbnail/vimeo/'.$_fileName;
                            JFile::delete($url);
                        }

                        $media['thumb'] = '';
                        if($fileName){
                            $media['thumb'] = $fileName;
                        }

                    }
                    break;
            }
            if(count($media)>0){
                $imageGallery -> name   = $media['code'];
                $imageGallery -> title  = $media['title'];
                $imageGallery -> thumb  = $media['thumb'];
            }
        }

        return $imageGallery;
    }

    function deleteThumb($articleId=null,$file=null){
        $size       = $this -> getState('size');
        $size['o']  = null;
        if($file){
            foreach($size as $key => $val){
                $url        = str_replace('.'.JFile::getExt($file),'_'.$key.'.'.JFile::getExt($file),$file);
                $url        = str_replace('/',DIRECTORY_SEPARATOR,$url);
                $path       = JPATH_SITE.DIRECTORY_SEPARATOR.$url;
                if(JFile::exists($path)){
                    JFile::delete($path);
                }
            }
        }
        else{
            if($articleId){
                $where  = ' WHERE contentid ='.(int) $articleId;

                $query  = 'SELECT * FROM #__tz_portfolio_xref_content'
                          .$where;
                $db = JFactory::getDbo();
                $db -> setQuery($query);
                if(!$db -> query()){
                    echo $db -> getErrorMsg();
                    die();
                }
                if($row   = $db -> loadObject()){
                    if(!empty($row -> videothumb)){
                        $file   = JPATH_SITE.DIRECTORY_SEPARATOR.$row -> videothumb;

//                        $video_type = null;
//                        if($row -> video && $pos = strpos($row -> video,':')) {
//                            $video_type = substr($row -> video,0,$pos);
//                        }
//                        $org_url = $this->imageUrl . '/src/thumbnail/';
//                        if($video_type && $video_type != 'default') {
//                            $org_url .= $video_type.'/';
//                        }
//                        $org_url    .= JFile::getName($row->videothumb);
//                        $org_path   =  JPATH_SITE.DIRECTORY_SEPARATOR.$org_url;
//                        if(JFile::exists($org_path)){
//                            JFile::delete($org_path);
//                        }
                        $size['o']  = null;

                        foreach($size as $key => $val){
                            $url    = str_replace('.'.JFile::getExt($file),'_'.$key.'.'.JFile::getExt($file),$file);
                            $url    = str_replace('/',DIRECTORY_SEPARATOR,$url);
                            if(JFile::exists($url)){
                                JFile::delete($url);
                            }
                        }
                    }
                }
            }
        }

    }

    function _save($contentid = null,$task=null,$data=null){

        if($data){
            $post   = $data;
        }else{
            $input  = $this -> input;
            $post   = $input->post;
            $post   = $post -> getArray();
            $post   = array_merge(array_shift($post),$post);
        }

        $db     = JFactory::getDbo();

        if($task != 'save2copy'){
			// Clean the cache.
			$this->cleanCache();
            $params     = $this -> getState('params');

            $typeOfMedia    = JRequest::getString('type_of_media');

            $groupid    = $post['groupid'];



            $bool   = 0;
            if(isset($groupid)){
                //$data       = JRequest::getVar('jform',array(),'post','array');
                $textarea   = JRequest::getVar('tz_textarea_hidden',array(),'post','array');

                if($textarea){
                    foreach($textarea as $item){
                        $post['tzfields'.$item] = JRequest::getVar('tzfields'.$item,'','post','string', JREQUEST_ALLOWRAW);
                    }
                }

                // get link with fields link
                $link       = JRequest::getVar('tz_link_hidden',array(),'post','array');
                if($link){
                    foreach($link as $item){
                        $arr  = JRequest::getVar('tzfields'.$item,array(),'post','array');

                        // set value
                        if(!empty($arr[0]))
                            $post['tzfields'.$item] = array(htmlspecialchars('<a href="'.$arr[1]
                                .'" target="'.$arr[2].'">'.$arr[0].'</a>'));
                        else
                            $post['tzfields'.$item] = array(htmlspecialchars('<a href="'.$arr[1]
                                .'" target="'.$arr[2].'">'.$arr[1].'</a>'));
                    }
                }
            }

                // Create folder
                // Check folder
                $destPath   = JPATH_SITE.'/'.$this ->imageUrl;

                if(!JFolder::exists($destPath)){
                    JFolder::create($destPath);

                }
                if(!JFile::exists($destPath.'/index.html')){

                    JFile::write($destPath.'/index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
                }

                // Store image
                $imageUpload    = array('images'=>'','imagetitle'=>'');

                // Store attachments
                $attachFile         = JRequest::getVar('tz_attachments_file','','files','array');
                $attachHiddenFile   = JRequest::getVar('tz_attachments_hidden_file',array(),'post','array');
                $attachHiddenTitle  = JRequest::getVar('tz_attachments_hidden_title',array(),'post','array');
                $attachHiddenOld    = JRequest::getVar('tz_attachments_hidden_old',array(),'post','array');

                $attachTitle        = JRequest::getVar('tz_attachments_title',array(),'post','array');

                $attachFileName     = array();
                $attachFileTitle    = array();
                $attachOld          = null;

                if($attachFile){
                    if(count($attachFile)>0){
                        $tzfolderPath       = JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.$this -> tzfolder;
                        $attachFolderPath   = JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.$this -> tzfolder.DIRECTORY_SEPARATOR.$this -> attachUrl;

                        if(!JFolder::exists($tzfolderPath)){
                            JFolder::create($tzfolderPath);
                            JFile::write($tzfolderPath.DIRECTORY_SEPARATOR.'index.html',htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
                        }
                        if(!JFolder::exists($attachFolderPath)){
                            JFolder::create($attachFolderPath);
                            JFile::copy($tzfolderPath.DIRECTORY_SEPARATOR.'index.html',$attachFolderPath.DIRECTORY_SEPARATOR.'index.html');
                        }
                        $total  = count($attachFile) + count($attachHiddenFile);

                        if(count($attachHiddenFile)>0){
                            $i=0;
                            foreach($attachHiddenFile as $i => $item){
                                $type               = '.'.JFile::getExt($item);
                                $attachOld[]      = $attachHiddenOld[$i];

                                if($task == 'save2copy'){
                                    $fileName   = $item;
                                }
                                else{
                                    if(JRequest::getVar('task') == 'save2copy')
                                        $fileName   = uniqid().'tz_portfolio_'.(time()+ $i).$type;
                                    else
                                        $fileName   = $item;
                                }

                                $srcPath        = $attachFolderPath.DIRECTORY_SEPARATOR.$item;
                                $destPath       = $attachFolderPath.DIRECTORY_SEPARATOR.$fileName;

                                $attachFileName[]   = $this -> tzfolder.'/'.$this -> attachUrl.'/'.$fileName;

                                if(!empty($attachHiddenTitle[$i]))
                                    $attachFileTitle[]  = $attachHiddenTitle[$i];
                                else
                                    $attachFileTitle[]  = '';

                                if($task != 'save2copy'){
                                    if(!JFile::exists($destPath))
                                        JFile::copy($srcPath,$destPath);
                                }

                                $i++;
                            }
                        }

                        $errobj = new stdClass();
                        if(isset($attachFile['name']) && count($attachFile['name'])>0){
                            foreach($attachFile['name'] as $i => $item){
                                if(!empty($item)){
                                    // Check file size
                                    $type   = JFile::getExt($item);
                                    $listType   = explode(',',$params -> get('tz_attach_type'));

                                    if(!in_array($type,$listType)){
                                        // Error file type
                                        if(count($errobj -> name) < 1){
                                            $errobj -> errorType = 'filetype';
                                        }
                                        $errobj -> name[] = $item;
                                    }
                                    else{
                                        if($attachFile['size'][$i] <= (10*1024*1024)){

                                            $type               = '.'.$type;
                                            $attachOld[]          = $attachFile['name'][$i];

                                            if($task == 'save2copy')
                                                $fileName           = uniqid().'tz_portfolio_'.(time()+ count($attachHiddenFile) + $i).$type;
                                            else
                                                $fileName           = uniqid().'tz_portfolio_'.(time() + $i + count($attachHiddenFile)+count($attachFile)).$type;
                                            $destPath           = $attachFolderPath.DIRECTORY_SEPARATOR.$fileName;

                                            $attachFileName[]   = $this -> tzfolder.'/'.$this -> attachUrl.'/'.$fileName;

                                            if(!empty($attachTitle[$i]))
                                                $attachFileTitle[]  = $attachTitle[$i];
                                            else
                                                $attachFileTitle[]  = '';

                                            if(!JFile::exists($destPath))
                                                JFile::copy($attachFile['tmp_name'][$i],$destPath);

                                        }
                                        else{
                                            // Error file type
                                            if(count($errobj -> name) < 1){
                                                $errobj -> errorType = 'filesize';
                                            }
                                            $errobj -> name[] = $item;
                                        }
                                    }
                                }
                            }

                            // Start message error
                            if(isset($errobj -> name) && ($count = count($errobj -> name)) > 0){


                                if($errobj -> errorType == 'filetype'){
                                    $errobj -> name = implode(',',$errobj -> name);
                                    if($count > 1){
                                        $text   = 'COM_TZ_PORTFOLIO_MULTI_ATTACH_FILE_TYPE_NOT_SUPPORTED';
                                        JError::raiseNotice(300,JText::sprintf($text,$count,$errobj -> name));
                                    }
                                    else{
                                        $text   = '"COM_TZ_PORTFOLIO_SINGLE_ATTACH_FILE_TYPE_NOT_SUPPORTED"';
                                        JError::raiseNotice(300,JText::sprintf($text,$errobj -> name));
                                    }

                                }

                                // Error File Size
                                if($errobj -> errorType == 'filesize'){
                                    $errobj -> name = implode(',',$errobj -> name);
                                    if($count > 1){
                                        $text   = 'COM_TZ_PORTFOLIO_MULTI_ATTACH_FILE_TOO_LARGE';
                                        JError::raiseNotice(300,JText::sprintf($text,$count,$errobj -> name));
                                    }
                                    else{
                                        $text   = 'COM_TZ_PORTFOLIO_SINGLE_ATTACH_FILE_TOO_LARGE';
                                        JError::raiseNotice(300,JText::sprintf($text,$errobj -> name));
                                    }
                                }

                            }
                            // End message error

                        }

                    }
                }

                /////////////////////////////////////////////

                $tzFields     = array();

               // get fields id from fields name
                $m=0;
                foreach($post as $key=>$val){
                    if(preg_match('/tzfields.*/i',$key,$match)==1){

                        $fieldsid   = str_replace('tzfields','',$key);

                        $ordering   = 0;

                        // Get value extra fields
						if($fieldsid){
							if(is_array($val)){

								foreach($val as $i => $row){

									if(preg_match('/(\@\[\{\(\&\*\_([0-9]+))|(\@\[\{\(\&amp\;\*\_([0-9]+))$/m',$row,$match2)){
//										$stt = str_replace($match2[1],'',$match2[0]);
										$stt = $match2[2];
										$optionField    = $this -> getOptionField($fieldsid,$stt);
									}
									else{
                                        $optionField    = $this -> getOptionField($fieldsid,0);
                                    }


                                    if($optionField -> ordering){
                                        $ordering   = $optionField -> ordering;
                                    }

									if(!empty($row)){

										if(preg_match('/(\@\[\{\(\&\*\_[0-9]+)|(\@\[\{\(\&amp\;\*\_[0-9]+)$/',$row,$match2)){
											$tzFields[] = '('.$this -> getState($this -> getName().'.id').','
                                                .$fieldsid.','.$db -> quote(str_replace($match2[0],'',$row)).',\''
                                                .$optionField -> image.'\','.$ordering.')';
										}
										else
											$tzFields[] = '('.$this -> getState($this -> getName().'.id').','
                                                .$fieldsid.','.$db -> quote((string) $row).',\''
                                                .$optionField -> image.'\','.$ordering.')';

									}
								}
							}
							else{
								if(!empty($val)){
									if(preg_match('/(\@\[\{\(\&\*\_[0-9]+)|(\@\[\{\(\&amp\;\*\_[0-9]+)$/',$val,$match2)){
										$stt    = str_replace(array('@[{(&*_','@[{(&amp;*_'),'',$match2[0]);
										$optionField    = $this -> getOptionField($fieldsid,$stt);

										$tzFields[] = '('.$this -> getState($this -> getName().'.id')
												  .','.$fieldsid.','.$db -> quote(str_replace($match2[0],'',$val)).',\''.$optionField -> image.'\','.$ordering.')';
									}
									else{
										$optionField    = $this -> getOptionField($fieldsid,0);
										if($optionField){
											$tzFields[] = '('.$this -> getState($this -> getName().'.id')
                                                .','.$fieldsid.','.$db -> quote((string) $val).',\''
                                                .$optionField -> image.'\','.$ordering.')';
										}
										else{
											$tzFields[] = '('.$this -> getState($this -> getName().'.id')
													  .','.$fieldsid.','.$db -> quote((string) $val).',\'\','.$ordering.')';
										}
									}
								}

							}
						}
                        //////end get
                    }

                    $m++;
                }

                // Store fields group
                //// Get images
                $attachFileName     = implode('///',$attachFileName);
                $attachFileTitle    = implode('///',$attachFileTitle);
                /////end get

                $fileHover  = JRequest::getVar('tz_img_hover', '', 'files','array');

                $file		= JRequest::getVar('tz_img', '', 'files','array');

                $file2		= JRequest::getVar('tz_img_client', '', 'files','array');

                $images = $this -> getImage($file,$post,$task);

                $value['groupid']    = $post['groupid'];
                $value['contentid']    = $contentid;

                $value['images']        = $db -> quote($images -> name);
                $value['imagetitle']    = $db -> quote($images -> title);

                $value['images_hover'] = $db -> quote($this -> getImageHover($fileHover,$post['tz_img_hover_server'],$post,$task));

                $gallery = $this -> getGallery($file2,$post,$params,$task);

                $value['gallery']       = $db -> quote($gallery -> name);
                $value['gallerytitle']  = $db -> quote($gallery -> title);

                $video = $this -> getVideo($post,$task);

                $value['video']         = $db ->quote($video -> name);
                $value['videotitle']    = $db -> quote($video -> title);

                $value['attachfiles']   = $db -> quote($attachFileName);
                $value['attachtitle']   = $db -> quote($attachFileTitle);

                if($attachOld && count($attachOld)){
                    $value['attachold']     = $db -> quote(implode('///',$attachOld));
                }
                else{
                    $value['attachold']     = $db -> quote('');
                }

                $value['videothumb']    = $db ->quote($video -> thumb);

                $value['type']    = $db -> quote($typeOfMedia);

                // Get and prepare data for audio
                $audioFields    = null;

                $jinput = JFactory::getApplication()->input;
                $files  = $jinput->files->get('jform');
                $audioFile   = $files['audio_soundcloud_image_client'];

                $audioFields    = ','.$this -> _db -> quoteName('audio').','
                    .$this -> _db -> quoteName('audiothumb').','
                    .$this -> _db -> quoteName('audiotitle');

            if($this -> tztask){
                $post['audio_soundcloud_hidden_image']  = null;
            }
                $value['audio'] = $this -> prepareAudio($post,$audioFile);
                // End get and prepare data for audio

                // Get and prepare data for quote
                $quoteFields    = '';
                if($this -> prepareQuote($post)){
                    $quoteFields    = ','.$this -> _db -> quoteName('quote_author').','.
                        $this -> _db -> quoteName('quote_text');
                    $value['quote'] = $this -> prepareQuote($post);
                }
                // End get and prepare data for quote

                // Get and prepare data for link
                $linkFields = ','.$this -> _db -> quoteName('link_title').','.
                    $this -> _db -> quoteName('link_url').','.$this -> _db -> quoteName('link_attribs');
                $value['link']  = $this -> prepareLink($post);
                // End get and prepare data for link

                $value['template_id']   = $data['template_id'];

                $value  = '('.implode(',',$value).')';

                $query  = 'DELETE FROM #__tz_portfolio_xref_content WHERE contentid = '.$contentid;
                $db -> setQuery($query);

                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }

                $query  = 'INSERT INTO `#__tz_portfolio_xref_content`'
                    .'(`groupid`,`contentid`,`images`,`imagetitle`,`images_hover`,`gallery`,`gallerytitle`,'
                    .'`video`,`videotitle`,`attachfiles`,`attachtitle`,`attachold`,`videothumb`,`type`'
                    .$audioFields.$quoteFields.$linkFields.',template_id)'
                    .' VALUES '.$value;

                $db -> setQuery($query);
                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }
                ///////////////////

                // Store Tz fields
                $query  = 'DELETE FROM #__tz_portfolio WHERE contentid = '.$contentid;
                $db -> setQuery($query);

                if(!$db -> query()){
                    $this -> setError($db -> getErrorMsg());
                    return false;
                }

                if(!empty($tzFields)){
                    $tzFields   = (count($tzFields)>0)?implode(',',$tzFields):'(\'\',\'\',\'\')';

                    $query  = 'INSERT INTO #__tz_portfolio(`contentid`,`fieldsid`,`value`,`images`,'
                        .$db -> quoteName('ordering').')'
                            .' VALUES'.$tzFields;

                    $db -> setQuery($query);

                    if(!$db -> query()){
                        $this -> setError($db -> getErrorMsg());
                        return false;
                    }
                }

            //////////////////

            // Tags
            $tags   = JRequest::getVar('tz_tags',null);
            $tags   = array_unique($tags);
            if($tags){
                $tags   = implode(',',$tags);
            }
            $this -> _saveTags($contentid,$tags);

        }

        return true;

    }

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
    public function save($data)
    {
        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $dispatcher = JEventDispatcher::getInstance();
        }else{
            $dispatcher = JDispatcher::getInstance();
        }
        $table = $this->getTable();

        $post   = JRequest::get('post');

        if($data){
            $post   = array_merge($data,array_shift($post),$post);
        }else{
            $post   = array_merge(array_shift($post),$post);
        }

        // Alter the title for save as copy
        if (JRequest::getVar('task') == 'save2copy') {
            list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);

            $this -> _save($this -> getState($this -> getName().'.id'),JRequest::getVar('task'),$post);

            $this -> tztask   = true;

            $data['id'] = 0;

            $data['title']	= $title;
            $data['alias']	= $alias;

        }

        if(isset($post['jform']['attribs']['tz_fieldsid'])){
            $fieldsId   = $post['jform']['attribs']['tz_fieldsid'];
            if(count($fieldsId) == 1 && !empty($fieldsId[0])){
                $data['attribs']['tz_fieldsid'] = $fieldsId;
            }
            elseif(count($fieldsId) > 1){
                if(empty($fieldsId[0]))
                    array_shift($fieldsId);
                $data['attribs']['tz_fieldsid'] = $fieldsId;
            }
        }


        if ((!empty($data['tags']) && $data['tags'][0] != ''))
        {
            $table->newTags = $data['tags'];
        }

        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Include the content plugins for the on save events.
        JPluginHelper::importPlugin('content');

        // Allow an exception to be thrown.
        try
        {
            // Load the row if saving an existing record.
            if ($pk > 0)
            {
                $table->load($pk);
                $isNew = false;
            }

            // Bind the data.
            if (!$table->bind($data))
            {
                $this->setError($table->getError());
                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);
            $this->_prepareTable($table);


            // Check the data.
            if (!$table->check())
            {
                $this->setError($table->getError());
                return false;
            }

            // Trigger the onContentBeforeSave event.
            $result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
            if (in_array(false, $result, true))
            {
                $this->setError($table->getError());
                return false;
            }

            // Store the data.
            if (!$table->store())
            {
                $this->setError($table->getError());
                return false;
            }

            // Clean the cache.
            $this->cleanCache();

            // Trigger the onContentAfterSave event.
            $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
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


        $post   = array_merge($post,$data);

        //Save parameter of plugins in group tzportfolio
        $plgData    = JRequest::getVar('tzplgform');
        if($plgData){
            $model  = JModelLegacy::getInstance('Plugin','TZ_PortfolioModel',array('ignore_request' => true));
            $model -> setState('com_tz_portfolio.plugin.articleId',$this -> getState('article.id'));
            $model -> save($plgData);
        }

        $this -> _save($table->$pkName,null,$post);

        if (isset($data['featured'])) {
            $this->featured($table->$pkName, $data['featured']);
        }

        if(COM_TZ_PORTFOLIO_JVERSION_COMPARE){
            $assoc = JLanguageAssociations::isEnabled();
            if ($assoc)
            {
                $id = (int) $this->getState($this->getName() . '.id');
                $item = $this->getItem($id);

                // Adding self to the association
                $associations = $data['associations'];

                foreach ($associations as $tag => $id)
                {
                    if (empty($id))
                    {
                        unset($associations[$tag]);
                    }
                }

                // Detecting all item menus
                $all_language = $item->language == '*';

                if ($all_language && !empty($associations))
                {
                    JError::raiseNotice(403, JText::_('COM_CONTENT_ERROR_ALL_LANGUAGE_ASSOCIATED'));
                }

                $associations[$item->language] = $item->id;

                // Deleting old association for these items
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                    ->delete('#__associations')
                    ->where('context=' . $db->quote('com_content.item'))
                    ->where('id IN (' . implode(',', $associations) . ')');
                $db->setQuery($query);
                $db->execute();

                if ($error = $db->getErrorMsg())
                {
                    $this->setError($error);
                    return false;
                }

                if (!$all_language && count($associations))
                {
                    // Adding new association for these items
                    $key = md5(json_encode($associations));
                    $query->clear()
                        ->insert('#__associations');

                    foreach ($associations as $id)
                    {
                        $query->values($id . ',' . $db->quote('com_content.item') . ',' . $db->quote($key));
                    }

                    $db->setQuery($query);
                    $db->execute();

                    if ($error = $db->getErrorMsg())
                    {
                        $this->setError($error);
                        return false;
                    }
                }
            }
        }

        return true;
    }


	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks)) {
			$this->setError(JText::_('COM_CONTENT_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable('Featured', 'ContentTable');

		try {
			$db = $this->getDbo();

			$db->setQuery(
				'UPDATE #__content' .
				' SET featured = '.(int) $value.
				' WHERE id IN ('.implode(',', $pks).')'
			);
			if (!$db->query()) {
				throw new Exception($db->getErrorMsg());
			}

			if ((int)$value == 0) {
				// Adjust the mapping table.
				// Clear the existing features settings.
				$db->setQuery(
					'DELETE FROM #__content_frontpage' .
					' WHERE content_id IN ('.implode(',', $pks).')'
				);
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			} else {
				// first, we find out which of our new featured articles are already featured.
				$query = $db->getQuery(true);
				$query->select('f.content_id');
				$query->from('#__content_frontpage AS f');
				$query->where('content_id IN ('.implode(',', $pks).')');
				//echo $query;
				$db->setQuery($query);

				if (!is_array($old_featured = $db->loadColumn())) {
					throw new Exception($db->getErrorMsg());
				}

				// we diff the arrays to get a list of the articles that are newly featured
				$new_featured = array_diff($pks, $old_featured);

				// Featuring.
				$tuples = array();
				foreach ($new_featured as $pk) {
					$tuples[] = '('.$pk.', 0)';
				}
				if (count($tuples)) {
					$db->setQuery(
						'INSERT INTO #__content_frontpage ('.$db->quoteName('content_id').', '.$db->quoteName('ordering').')' .
						' VALUES '.implode(',', $tuples)
					);
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
						return false;
					}
				}
			}

		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		$table->reorder();

		$this->cleanCache();

		return true;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}

    public function uploadImageClient($file,$destName,$base,$fileTypes,$size=array(),$oldThumb = null){
        if($file && $base){
            $size['o']  = null;
            $params     = $this -> getState('params');
            if(JFile::exists($file['tmp_name'])){
                // Check file type
                if(in_array($file['type'],$fileTypes)){
                    // Check file size
                    if($file['size'] <= TZ_IMAGE_SIZE){
                        // Create file with new name
                        $dest   = $base.DIRECTORY_SEPARATOR.$destName;

                        $obj    = new JImage($file['tmp_name']);

                        if(count($size)){
                            foreach($size as $key => $width){
                                $_dest    = str_replace('.'.JFile::getExt($file['name']),
                                    '_'.$key.'.'.JFile::getExt($file['name']),$dest);

                                if($key == 'o') {
                                    if($params -> get('allow_upload_original_image',1)) {
                                        JFile::copy($file['tmp_name'], JPATH_SITE . DIRECTORY_SEPARATOR . $_dest);
                                    }
                                }else{
                                    if($width){
                                        $newHeight = ($obj->getHeight() * (int)$width) / $obj->getWidth();
                                        $newImage = $obj->resize($width, $newHeight);
                                        $newImage->toFile(JPATH_SITE . DIRECTORY_SEPARATOR . $_dest, $this->_getImageType($dest));
                                    }
                                }
                            }

                            return str_replace(DIRECTORY_SEPARATOR,'/',$dest);
                        }
                        return null;
                    }else{
                        JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_AUDIO_THUMBNAIL_SIZE_TOO_LARGE'));
                    }
                }else{
                    JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_AUDIO_THUMBNAIL_FILE_NOT_SUPPORTED'));
                }
            }
        }
    }

    public function uploadImageServer($src,$destName,$base,$size=array(),$oldThumb = null,$copy = false){
        if($src){
            $sizes      = $size;
            $params     = $this -> getState('params');
            $sizes['o'] = null;
            $dest    = $base.DIRECTORY_SEPARATOR.$destName;
            if($copy){ // If batch copy
                foreach($sizes as $key => $width){
                    $_dest    = str_replace('.'.JFile::getExt($src),
                        '_'.strtoupper($key).'.'.JFile::getExt($src),$dest);
                    $_src    = str_replace('.'.JFile::getExt($src),
                        '_'.strtoupper($key).'.'.JFile::getExt($src),$src);
                    if(JPATH_SITE.DIRECTORY_SEPARATOR.$_dest){
                        JFile::copy(JPATH_SITE.DIRECTORY_SEPARATOR.$_src,JPATH_SITE.DIRECTORY_SEPARATOR.$_dest);
                    }
                }
                return str_replace(DIRECTORY_SEPARATOR,'/',$dest);
            }else{
                if(JFile::exists(JPATH_SITE.DIRECTORY_SEPARATOR.$src)){

                    $obj    = new JImage(JPATH_SITE.DIRECTORY_SEPARATOR.$src);

                    // Delete old thumbnail
                    if($oldThumb){
                        $this -> deleteThumb(null,$oldThumb);
                    }

                    if(filesize(JPATH_SITE.DIRECTORY_SEPARATOR.$src) <= TZ_IMAGE_SIZE){
                        if(count($sizes)){
                            foreach($sizes as $key => $width){
                                $_dest    = str_replace('.'.JFile::getExt($src),
                                    '_'.$key.'.'.JFile::getExt($src),$dest);

                                if($key == 'o') {
                                    if($params -> get('allow_upload_original_image',1)) {
                                        JFile::copy(JPATH_SITE . DIRECTORY_SEPARATOR . $src, JPATH_SITE . DIRECTORY_SEPARATOR . $_dest);
                                    }
                                }else{
                                    if($width){
                                        $newHeight = ($obj->getHeight() * (int)$width) / $obj->getWidth();
                                        $newImage = $obj->resize($width, $newHeight);
                                        $newImage->toFile(JPATH_SITE . DIRECTORY_SEPARATOR . $_dest, $this->_getImageType($dest));
                                    }
                                }
                            }

                            return str_replace(DIRECTORY_SEPARATOR,'/',$dest);
                        }
                        return null;
                    }
                }else{
                    JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_AUDIO_THUMBNAIL_SIZE_TOO_LARGE'));
                }
            }
        }
    }


    public function prepareAudio($data,$file = null,$_copy = false){
        $copy   = $_copy;
        if($this -> tztask){
            $copy   = true;
        }
        if($data){
            if(isset($data['jform'])){
                $data   = $data['jform'];
            }

            if($data['audio_soundcloud_id']){
                $fileTypes  = array('image/jpeg','image/jpg','image/bmp','image/gif','image/png','image/ico');

                $params = $this -> getState('params');
                $_data  = null;
                $_data  = $this -> _db -> quote($data['audio_soundcloud_id']);

                $id = $this -> getState($this -> getName().'.id');
                if(!$id){
                    $id = $data['id'];
                }

                // Create folder to save thumb if this folder isn't created.
                $audioPath  = $this -> imageUrl
                            .DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'thumbnail'
                    .DIRECTORY_SEPARATOR.$this -> audioFolder;
                if(!JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.$audioPath)){
                    JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.$audioPath);
                }
                if(JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.$audioPath)){
                    if(!JFile::exists(JPATH_SITE.DIRECTORY_SEPARATOR.$audioPath.DIRECTORY_SEPARATOR.'index.html')){
                        JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.$audioPath.DIRECTORY_SEPARATOR.'index.html',
                            htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
                    }
                }

                // Check and set chmod folder again
                $chmodFolder    = JPath::getPermissions($audioPath);

                if($chmodFolder != 'rwxrwxrwx' || $chmodFolder != 'rwxr-xr-x'){
                    JPath::setPermissions($audioPath);
                }

                // Prepare data (Return string to save the database)
                //// Delete old thumbnail if delete checkbox input is checked
                if($data['audio_soundcloud_delete_image'] && $hiddenImage = $data['audio_soundcloud_hidden_image']){
                    $this -> deleteThumb(null,$hiddenImage);

//                    // Delete old original thumbnail
//                    $org_path   = JPATH_SITE.DIRECTORY_SEPARATOR.$org_audioPath.DIRECTORY_SEPARATOR
//                        .JFile::getName($data['audio_soundcloud_hidden_image']);
//                    if(JFile::exists($org_path)){
//                        JFile::delete($org_path);
//                    }
                }

                if($file && !empty($file['name'])){ // If choose thumbnail from client
                    $destName   = ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias']).'-'.
                        $id.'.'. JFile::getExt($file['name']);
                    $image = $this -> uploadImageClient($file,$destName,$audioPath,
                        $fileTypes,$this -> _getImageSizes($params),$data['audio_soundcloud_hidden_image']);

                }elseif(!empty($data['audio_soundcloud_image_server'])){ // If choose thumbnail from server
                    $destName   = ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                        .'-'.$id.'.'
                        .JFile::getExt($data['audio_soundcloud_image_server']);
                    $image  = $this -> uploadImageServer($data['audio_soundcloud_image_server'],$destName,
                        $audioPath,$this -> _getImageSizes($params),$data['audio_soundcloud_hidden_image'],$copy);

                }else{ // Get thumbnail from soundcloud page
                    if($data['audio_soundcloud_delete_image'] && $hiddenImage = $data['audio_soundcloud_hidden_image']){
                        $data['audio_soundcloud_hidden_image']  = '';
                    }
                    if(!isset($data['audio_soundcloud_hidden_image']) || empty($data['audio_soundcloud_hidden_image'])){
                        if($client_id = $params -> get('soundcloud_client_id','4a24c193db998e3b88c34cad41154055')){
                            // Register fetch object
                            $fetch  = new Services_Yadis_PlainHTTPFetcher();
                            $url    = 'http://api.soundcloud.com/tracks/'.$data['audio_soundcloud_id']
                                .'.json?client_id='.$client_id;

                            if($content    = $fetch -> get($url)){
                                $content    = json_decode($content -> body);
                                $thumbUrl   = null;
                                if($content -> artwork_url && !empty($content -> artwork_url)){
                                    $thumbUrl   = $content -> artwork_url;
                                }
                                else{
                                    $audioUser   = $content -> user;
                                    if($audioUser -> avatar_url && !empty($audioUser -> avatar_url)){
                                        $thumbUrl   = $audioUser -> avatar_url;
                                    }
                                }
                                if($thumbUrl){

                                    if(JString::strrpos($thumbUrl,'-',0) != false){
                                        $thumbUrl = JString::substr($thumbUrl,0,JString::strrpos($thumbUrl,'-',0)+1).'t500x500.'.JFile::getExt($thumbUrl);
                                    }

                                    // Create folder tmp if not exists
                                    if(!JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'media'
                                            .DIRECTORY_SEPARATOR.$this -> tzfolder)){
                                        JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'media'
                                                .DIRECTORY_SEPARATOR.$this -> tzfolder);
                                    }
                                    if(JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'media'
                                            .DIRECTORY_SEPARATOR.$this -> tzfolder)){
                                        if(!JFile::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'media'
                                                .DIRECTORY_SEPARATOR.$this -> tzfolder.'index.html')){
                                            JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'media'
                                                .DIRECTORY_SEPARATOR.$this -> tzfolder.'index.html',
                                                htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
                                        }
                                    }

                                    // Save image from other server to this server (temp file)
                                    $fetch2         = new Services_Yadis_PlainHTTPFetcher();
                                    $audioTempPath  = null;
                                    if($audioTemp  = $fetch2 -> get($thumbUrl)){
                                        if(in_array($audioTemp -> headers['Content-Type'],$fileTypes)){
                                            $audioType  = JFile::getExt($thumbUrl);
                                            if(preg_match('/(.*)(\\|\/|\:|\*|\?|\"|\<|\>|\|.*?)/i',$audioType,$match)){
                                                $audioType  = $match[1];
                                            }

                                            $audioTempPath  = 'media'.DIRECTORY_SEPARATOR.$this -> tzfolder
                                                .DIRECTORY_SEPARATOR.uniqid().time()
                                                .'.'.$audioType;

                                            JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.$audioTempPath,$audioTemp -> body);
                                        }
                                    }

                                    if($audioTempPath){
                                        $destName   = ((!$data['alias'])?uniqid() .'tz_portfolio_'.time():$data['alias'])
                                            .'-'.$id.'.'
                                            .JFile::getExt($audioTempPath);

                                        $image  = $this -> uploadImageServer($audioTempPath,$destName,$audioPath,
                                            $this -> _getImageSizes($params));

                                        if(JFile::exists(JPATH_SITE.DIRECTORY_SEPARATOR.$audioTempPath)){
                                            JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.$audioTempPath);
                                        }
                                    }
                                }
                            }

                        }
                    }else{
                        $image  = $data['audio_soundcloud_hidden_image'];
                    }
                }

                $_data  .= ',';
                if($image){
                    $_data  .= $this -> _db -> quote($image);
                }
                else{
                    $_data .= $this -> _db -> quote('');
                }

                $_data  .= ',';
                if($data['audio_soundcloud_title']){
                    $_data  .= $this -> _db -> quote($data['audio_soundcloud_title']);
                }
                else{
                    $_data  .= $this -> _db -> quote('');
                }

                return $_data;
            }

            if($data['audio_soundcloud_hidden_image']) {
                $this->deleteThumb(null,$data['audio_soundcloud_hidden_image']);
            }
            return $this -> _db -> quote('').','.$this -> _db -> quote('').','.$this -> _db -> quote('');
        }
        return $this -> _db -> quote('').','.$this -> _db -> quote('').','.$this -> _db -> quote('');
    }

    public function prepareQuote($data){
        if($data){
            if(isset($data['jform'])){
                $data   = $data['jform'];
            }
            $_data  = null;
            if($data['quote_author']){
                $_data  = $this -> _db -> quote($data['quote_author']);
            }else{
                $_data  = $this -> _db -> quote('');
            }
            if($data['quote_text']){
                $_data  .= ','.$this -> _db -> quote($data['quote_text']);
            }else{
                $_data  .= ','.$this -> _db -> quote('');
            }
            if($_data ){
                return $_data;
            }
            return $this -> _db -> quote('').','.$this -> _db -> quote('');
        }
        return $this -> _db -> quote('').','.$this -> _db -> quote('');
    }

    public function prepareLink($data){
        $_data  = $this -> _db -> quote('').','.$this -> _db -> quote('').','.$this -> _db -> quote('');
        if($data){
            if(isset($data['jform'])){
                $data   = $data['jform'];
            }
            if($data['link_title']){
                $_data  = $this -> _db -> quote($data['link_title']);
            }else{
                $_data  = $this -> _db -> quote('');
            }
            if($url = $data['link_url']){
                if(!preg_match('/^http\:\/\/.*/i',$url)){
                    $url    = 'http://'.$url;
                }
                $_data  .= ','.$this -> _db -> quote($url);
            }else{
                $_data  .= ','.$this -> _db -> quote('');
            }
            if($this -> _prepareAttribs($data)){
                $_data  .= ','.$this -> _db -> quote($this -> _prepareAttribs($data));
            }else{
                $_data  .= ','.$this -> _db -> quote('');
            }
        }
        return $_data;
    }

    protected function _prepareAttribs($data){
        if($data){
            $attribs    = new JRegistry();
            if($data['link_target']){
                $_data['link_target']    = $data['link_target'];
            }
            if($data['link_follow']){
                $_data['link_follow']   = $data['link_follow'];
            }
            $attribs -> loadArray($_data);
            return $attribs -> toString();
        }
        return null;
    }

    public function getArticles($pk=null){
        if($pk){
            $db     = $this -> getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('x.*,c.alias,c.title');
            $query -> from($db -> quoteName('#__tz_portfolio_xref_content').' AS x');
            $query -> join('LEFT',$db -> quoteName('#__content').' AS c ON c.id=x.contentid');
            if(is_array($pk)) {
                $query->where('x.contentid IN(' . implode(',', $pk) . ')');
            }else{
                $query -> where('x.contentid='.$pk);
            }
            $db -> setQuery($query);

            return $db -> loadObjectList();
        }
        return false;
    }

    public function resizeImage($src,$gallery=null,$dest=null){
        if($src){

            $type       = JFile::getExt($src);
            $org_file   = str_replace('.'.$type,'_o.'.$type,$src);
            $params     = $this -> getState('params');
            $sizes      = $this -> getState('sizeImage');

            if($gallery){
                $sizes  = $this -> getState('size');
            }

            if(!JFile::exists($org_file)){
                $this -> setError(JText::_('COM_TZ_PORTFOLIO_IMAGE_FILE_DOES_NOT_EXIST'));
                return false;
            }

            $dest_path  = $dest;

            if($sizes){
                $image  = new JImage($org_file);
                $width  = $image -> getWidth();
                $height = $image -> getHeight();

                // Resize images
                foreach($sizes as $key => $newWidth){
                    $newHeight  = $height * $newWidth / $width;
                    $newImage   = $image -> resize($newWidth,$newHeight);
                    if(!$dest) {
                        $dest_path = str_replace('.' . $type, '_' . $key . '.' . $type, $src);
                    }
                    if(JFile::exists($dest_path)){
                        JFile::delete($dest_path);
                    }
                    $newImage -> toFile($dest_path,$this->_getImageType($src));
                }
                return true;
            }
            return false;
        }
        return false;
    }

	/**
	 * Custom clean the cache of com_content and content modules
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_tz_portfolio');
		parent::cleanCache('mod_tz_portfolio_articles_archive');
		parent::cleanCache('mod_tz_portfolio_articles_categories');
		parent::cleanCache('mod_tz_portfolio_articles_category');
		parent::cleanCache('mod_tz_portfolio_articles_latest');
		parent::cleanCache('mod_tz_portfolio_articles_news');
		parent::cleanCache('mod_tz_portfolio_articles_popular');
	}
}
