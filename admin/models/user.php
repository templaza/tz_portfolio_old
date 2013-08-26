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

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * User model.
 *
 */
class TZ_PortfolioModelUser extends JModelAdmin
{
    function __construct(){
        parent::__construct();
    }
    
    function populateState(){
        $this -> setState($this -> getName().'.id', JRequest::getInt('id'));
//        parent::populateState();

    }
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	*/
	public function getTable($type = 'User', $prefix = 'JTable', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);

		return $table;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed	Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$result = parent::getItem($pk);
//        var_dump($result);

		// Get the dispatcher and load the users plugins.
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('user');

		// Trigger the data preparation event.
		$results = $dispatcher->trigger('onContentPrepareData', array('com_users.user', $result));

		return $result;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_tz_portfolio.user', 'user', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tz_portfolio.edit.user.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		// TODO: Maybe this can go into the parent model somehow?
		// Get the dispatcher and load the users plugins.
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin('user');

		// Trigger the data preparation event.
		$results = $dispatcher->trigger('onContentPrepareData', array('com_users.profile', $data));

		// Check for errors encountered while preparing the data.
		if (count($results) && in_array(false, $results, true))
		{
			$this->setError($dispatcher->getError());
		}


		return $data;
	}

	/**
	 * Override JModelAdmin::preprocessForm to ensure the correct plugin group is loaded.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		parent::preprocessForm($form, $data, $group);
	}

    function getUsers($userId=null){
        if(!$userId)
            $userId = $this -> getState($this -> getName().'.id');

        $query  = 'SELECT * FROM #__tz_portfolio_users'
                  .' WHERE usersid='.$userId;
        $db     = $this -> getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }
        if($rows   = $db -> loadObject()){
            if($rows -> images){
                $rows -> imageName  = $rows -> images;

                $rows -> images = JURI::root().$rows -> images;
            }
            return $rows;
        }

        return '';
    }

    function deleteImages($fileName){
        if($fileName){
            $file   = JPATH_SITE.DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$fileName);
            if(!JFile::exists($file)){
                $this -> setError(JText::_('COM_TZ_PORTFOLIO_INVALID_FILE'));
                return false;
            }
            JFile::delete($file);
        }
        return true;
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

    function uploadImages($file,$currentImage=null){
        if($file){
            $maxSize    = 2*1024*1024;
            $arr        = array('image/jpeg','image/jpg','image/bmp','image/gif','image/png','image/ico');

            // Create folder
            $tzFolder           = 'tz_portfolio';
            $tzUserFolder       = 'users';
            $tzFolderPath       = JPATH_ROOT.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.$tzFolder;
            $tzUserFolderPath   = $tzFolderPath.DIRECTORY_SEPARATOR.$tzUserFolder;

            if(!JFolder::exists($tzFolderPath)){
                JFolder::create($tzFolderPath);
                if(!JFile::exists($tzFolderPath.DIRECTORY_SEPARATOR.'index.html')){
                    JFile::write($tzFolderPath.DIRECTORY_SEPARATOR.'index.html'
                        ,htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
                }
            }
            if(JFolder::exists($tzFolderPath)){
                if(!JFolder::exists($tzUserFolderPath)){
                    JFolder::create($tzUserFolderPath);
                    if(!JFile::exists($tzUserFolderPath.DIRECTORY_SEPARATOR.'index.html'))
                        JFile::write($tzUserFolderPath.DIRECTORY_SEPARATOR.'index.html'
                            ,htmlspecialchars_decode('<!DOCTYPE html><title></title>'));
                }
            }
            if(is_array($file)){
                foreach($file as $key => $val){
                    if(is_array($val)){
                        foreach($val as $key2 => $val2){
                            $file[$key] = $val2;
                        }
                    }
                }

                //Upload image
                if(in_array($file['type'],$arr)){

                    if($file['size'] <= $maxSize){

                        $desFileName    = 'user_'.time().uniqid().'.'.JFile::getExt($file['name']);
                        $desPath        = $tzUserFolderPath.DIRECTORY_SEPARATOR.$desFileName;

                        if(JFile::exists($file['tmp_name'])){
                            if(!JFile::copy($file['tmp_name'],$desPath)){
                                JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_CAN_NOT_UPLOAD_FILE'));
                            }
                            $image      = new JImage();
                            $image -> loadFile($desPath);
                            $params     = JComponentHelper::getParams('com_tz_portfolio');

                            if($params -> get('tz_user_image_width',100))
                                $width  = $params -> get('tz_user_image_width',100);

                            $height     = ceil( ( ($image -> getHeight()) * $width ) / ($image -> getWidth()) );
                            $image      = $image -> resize($width,$height);
                            $type       = $this -> _getImageType($file['name']);
                            $image -> toFile($desPath,$type);

                            $this -> deleteImages($currentImage);

                            return 'media/'.$tzFolder.'/'.$tzUserFolder.'/'.$desFileName;
                        }
                    }
                    else{
                        JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_TOO_LARGE'));
                    }
                }
                else{
                    JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_IMAGE_FILE_NOT_SUPPORTED'));
                }

            }
            else{
                tzportfolioimport('HTTPFetcher');
                tzportfolioimport('readfile');

                $image  = new Services_Yadis_PlainHTTPFetcher();
                $image  = $image -> get($file);

                if(in_array($image -> headers['Content-Type'],$arr)){


                    if($image -> headers['Content-Length'] > $maxSize){


                        $this -> deleteImages($currentImage);

                        $desFileName    = 'user_'.time().uniqid().'.'
                                          .str_replace('image/','',$image -> headers['Content-Type'] );
                        $desPath        = $tzUserFolderPath.DIRECTORY_SEPARATOR.$desFileName;

                        if(JFolder::exists($tzFolderPath)){
                            if(!JFile::write($desPath,$image -> body)){
                                $this -> setError(JText::_('COM_TZ_PORTFOLIO_CAN_NOT_UPLOAD_FILE'));
                                return false;
                            }
                            return 'media/'.$tzFolder.'/'.$tzUserFolder.'/'.$desFileName;
                        }
                    }else{
                        JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_IMAGE_SIZE_TOO_LARGE'));
                    }
                }
                else{
                    JError::raiseNotice(300,JText::_('COM_TZ_PORTFOLIO_IMAGE_FILE_NOT_SUPPORTED'));
                }
            }
        }
        if($currentImage){
            return $currentImage;
        }
        return '';
    }

    function saveUser($data){

        if(count($data)<=0){
            $this -> setError(JText::_('COM_TZ_PORTFOLIO_DATA_EMPTY'));
            return false;
        }

        $query  = 'DELETE FROM #__tz_portfolio_users'
            .' WHERE usersid='.$data['usersid'];

        $db     = JFactory::getDbo();
        $db -> setQuery($query);
        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }

        $value  = '('.$data['usersid'].','
                  .$db -> quote($data['images']).','
                  .$db -> quote($data['url']).','
                  .$db -> quote($data['gender']).','
                  .$db -> quote($data['description']).','
                  .$db -> quote($data['twitter']).','
                  .$db -> quote($data['facebook']).','
                  .$db -> quote($data['google_one']).')';
        $query  = 'INSERT INTO #__tz_portfolio_users(`usersid`,`images`,`url`,`gender`,`description`,`twitter`,`facebook`,`google_one`)'
            .' VALUES '.$value;

        $db -> setQuery($query);
        if(!$db -> query()){
            $this -> setError($db -> getErrorMsg());
            return false;
        }

        return true;
    }

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
//        var_dump($data); die();

		// Initialise variables;
		$pk			= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('user.id');
		$user		= JUser::getInstance($pk);

		$my = JFactory::getUser();

		if ($data['block'] && $pk == $my->id && !$my->block)
		{
			$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_BLOCK_SELF'));
			return false;
		}

		// Make sure that we are not removing ourself from Super Admin group
		$iAmSuperAdmin = $my->authorise('core.admin');
		if ($iAmSuperAdmin && $my->get('id') == $pk)
		{
			// Check that at least one of our new groups is Super Admin
			$stillSuperAdmin = false;
			$myNewGroups = $data['groups'];
			foreach ($myNewGroups as $group)
			{
				$stillSuperAdmin = ($stillSuperAdmin) ? ($stillSuperAdmin) : JAccess::checkGroup($group, 'core.admin');
			}
			if (!$stillSuperAdmin)
			{
				$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_DEMOTE_SELF'));
				return false;
			}
		}

		// Bind the data.
		if (!$user->bind($data))
		{
			$this->setError($user->getError());
			return false;
		}

		// Store the data.
		if (!$user->save())
		{
			$this->setError($user->getError());
			return false;
		}

        $avatar             = JRequest::getVar('jform','','files','array');
        $description        = JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWHTML );

        $deleteImage        = JRequest::getInt('delete_images');
        $currentImage       = JRequest::getString('current_images');
        $userData['url']    = JRequest::getVar( 'url', '', 'post', 'string' );
//        var_dump($deleteImage); die();

        $userData['usersid']        = $user -> id;
        $userData['gender']         = JRequest::getCmd('gender');
        $userData['description']    = $description;
        $userData['twitter']        = JRequest::getVar( 'url_twitter', '', 'post', 'string');
        $userData['facebook']       = JRequest::getVar( 'url_facebook', '', 'post', 'string' );
        $userData['google_one']     = JRequest::getVar( 'url_google_one_plus', '', 'post', 'string' );


        if(!$userData['gender'])
            $userData['gender'] = 'm';

        if(!empty($avatar['name']['client_images'])){
            $image  = $avatar;
        }
        else{
            if(!empty($data['url_images']))
                $image  = $data['url_images'];
        }

        if($image){
            $userData['images'] = $this -> uploadImages($image,$currentImage);
        }
        else
            $userData['images'] = $currentImage;

        if($deleteImage == 1){
            $this -> deleteImages($currentImage);
            $userData['images'] = '';
        }

        if(!$this -> saveUser($userData)){
            $this -> setError($this -> getError());
            return false;
        }

		$this->setState('user.id', $user->id);

		return true;
	}

	/**
	 * Method to delete rows.
	 *
	 * @param   array  &$pks  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$table	= $this->getTable();
		$pks	= (array) $pks;

		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');

		// Trigger the onUserBeforeSave event.
		JPluginHelper::importPlugin('user');
		$dispatcher = JDispatcher::getInstance();

		if (in_array($user->id, $pks))
		{
			$this->setError(JText::_('COM_USERS_USERS_ERROR_CANNOT_DELETE_SELF'));
			return false;
		}

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				// Access checks.
				$allow = $user->authorise('core.delete', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				if ($allow)
				{
					// Get users data for the users to delete.
					$user_to_delete = JFactory::getUser($pk);

					// Fire the onUserBeforeDelete event.
					$dispatcher->trigger('onUserBeforeDelete', array($table->getProperties()));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
					else
					{
						// Trigger the onUserAfterDelete event.
						$dispatcher->trigger('onUserAfterDelete', array($user_to_delete->getProperties(), true, $this->getError()));
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to block user records.
	 *
	 * @param   array    &$pks   The ids of the items to publish.
	 * @param   integer  $value  The value of the published state
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	function block(&$pks, $value = 1)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');
		$table		= $this->getTable();
		$pks		= (array) $pks;

		JPluginHelper::importPlugin('user');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($value == 1 && $pk == $user->get('id'))
			{
				// Cannot block yourself.
				unset($pks[$i]);
				JError::raiseWarning(403, JText::_('COM_USERS_USERS_ERROR_CANNOT_BLOCK_SELF'));

			}
			elseif ($table->load($pk))
			{
				$old	= $table->getProperties();
				$allow	= $user->authorise('core.edit.state', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				// Prepare the logout options.
				$options = array(
					'clientid' => array(0, 1)
				);

				if ($allow)
				{
					// Skip changing of same state
					if ($table->block == $value)
					{
						unset($pks[$i]);
						continue;
					}

					$table->block = (int) $value;

					// Allow an exception to be thrown.
					try
					{
						if (!$table->check())
						{
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onUserBeforeSave event.
						$result = $dispatcher->trigger('onUserBeforeSave', array($old, false, $table->getProperties()));
						if (in_array(false, $result, true))
						{
							// Plugin will have to raise it's own error or throw an exception.
							return false;
						}

						// Store the table.
						if (!$table->store())
						{
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onAftereStoreUser event
						$dispatcher->trigger('onUserAfterSave', array($table->getProperties(), false, true, null));
					}
					catch (Exception $e)
					{
						$this->setError($e->getMessage());

						return false;
					}

					// Log the user out.
					if ($value)
					{
						$app->logout($table->id, $options);
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
			}
		}

		return true;
	}

	/**
	 * Method to activate user records.
	 *
	 * @param   array  &$pks  The ids of the items to activate.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	function activate(&$pks)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		// Check if I am a Super Admin
		$iAmSuperAdmin	= $user->authorise('core.admin');
		$table		= $this->getTable();
		$pks		= (array) $pks;

		JPluginHelper::importPlugin('user');

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				$old	= $table->getProperties();
				$allow	= $user->authorise('core.edit.state', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::check($pk, 'core.admin')) ? false : $allow;

				if (empty($table->activation))
				{
					// Ignore activated accounts.
					unset($pks[$i]);
				}
				elseif ($allow)
				{
					$table->block		= 0;
					$table->activation	= '';

					// Allow an exception to be thrown.
					try
					{
						if (!$table->check())
						{
							$this->setError($table->getError());
							return false;
						}

						// Trigger the onUserBeforeSave event.
						$result = $dispatcher->trigger('onUserBeforeSave', array($old, false, $table->getProperties()));
						if (in_array(false, $result, true))
						{
							// Plugin will have to raise it's own error or throw an exception.
							return false;
						}

						// Store the table.
						if (!$table->store())
						{
							$this->setError($table->getError());
							return false;
						}

						// Fire the onAftereStoreUser event
						$dispatcher->trigger('onUserAfterSave', array($table->getProperties(), false, true, null));
					}
					catch (Exception $e)
					{
						$this->setError($e->getMessage());

						return false;
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
			}
		}

		return true;
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('COM_USERS_USERS_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		if (!empty($commands['group_id']))
		{
			$cmd = JArrayHelper::getValue($commands, 'group_action', 'add');

			if (!$this->batchUser((int) $commands['group_id'], $pks, $cmd))
			{
				return false;
			}
			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Perform batch operations
	 *
	 * @param   integer  $group_id  The group ID which assignments are being edited
	 * @param   array    $user_ids  An array of user IDs on which to operate
	 * @param   string   $action    The action to perform
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @since	1.6
	 */
	public function batchUser($group_id, $user_ids, $action)
	{
		// Get the DB object
		$db = $this->getDbo();

		JArrayHelper::toInteger($user_ids);

		if ($group_id < 1)
		{
			$this->setError(JText::_('COM_USERS_ERROR_INVALID_GROUP'));
			return false;
		}

		switch ($action)
		{
			// Sets users to a selected group
			case 'set':
				$doDelete	= 'all';
				$doAssign	= true;
				break;

			// Remove users from a selected group
			case 'del':
				$doDelete	= 'group';
				break;

			// Add users to a selected group
			case 'add':
			default:
				$doAssign	= true;
				break;
		}

		// Remove the users from the group if requested.
		if (isset($doDelete))
		{
			$query = $db->getQuery(true);

			// Remove users from the group
			$query->delete($db->quoteName('#__user_usergroup_map'));
			$query->where($db->quoteName('user_id') . ' IN (' . implode(',', $user_ids) . ')');

			// Only remove users from selected group
			if ($doDelete == 'group')
			{
				$query->where($db->quoteName('group_id') . ' = ' . (int) $group_id);
			}

			$db->setQuery($query);

			// Check for database errors.
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		// Assign the users to the group if requested.
		if (isset($doAssign))
		{
			$query = $db->getQuery(true);

			// First, we need to check if the user is already assigned to a group
			$query->select($db->quoteName('user_id'));
			$query->from($db->quoteName('#__user_usergroup_map'));
			$query->where($db->quoteName('group_id') . ' = ' . (int) $group_id);
			$db->setQuery($query);
			$users = $db->loadColumn();

			// Build the values clause for the assignment query.
			$query->clear();
			$groups = false;
			foreach ($user_ids as $id)
			{
				if (!in_array($id, $users))
				{
					$query->values($id . ',' . $group_id);
					$groups = true;
				}
			}

			// If we have no users to process, throw an error to notify the user
			if (!$groups)
			{
				$this->setError(JText::_('COM_USERS_ERROR_NO_ADDITIONS'));
				return false;
			}

			$query->insert($db->quoteName('#__user_usergroup_map'));
			$query->columns(array($db->quoteName('user_id'), $db->quoteName('group_id')));
			$db->setQuery($query);

			// Check for database errors.
			if (!$db->query())
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 * Gets the available groups.
	 *
	 * @return  array  An array of groups
	 *
	 * @since   1.6
	 */
	public function getGroups()
	{
		$user = JFactory::getUser();
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_users'.DIRECTORY_SEPARATOR.'models');
//        require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_users'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'groups.php');
		if ($user->authorise('core.edit', 'com_users') && $user->authorise('core.manage', 'com_users'))
		{
			$model = JModelLegacy::getInstance('Groups', 'UsersModel', array('ignore_request' => true));
			return $model->getItems();
		}
		else
		{
			return null;
		}
	}

	/**
	 * Gets the groups this object is assigned to
	 *
	 * @param   integer  $userId  The user ID to retrieve the groups for
	 *
	 * @return  array  An array of assigned groups
	 *
	 * @since   1.6
	 */
	public function getAssignedGroups($userId = null)
	{
		// Initialise variables.
		$userId = (!empty($userId)) ? $userId : (int)$this->getState('user.id');

		if (empty($userId))
		{
			$result = array();
			$config = JComponentHelper::getParams('com_users');
			if ($groupId = $config->get('new_usertype'))
			{
				$result[] = $groupId;
			}
		}
		else
		{
			jimport('joomla.user.helper');
			$result = JUserHelper::getUserGroups($userId);
		}

		return $result;
	}
}
