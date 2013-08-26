<?php
/*------------------------------------------------------------------------
# plg_extravote - ExtraVote Plugin
# ------------------------------------------------------------------------
# author    Joomla!Vargas
# copyright Copyright (C) 2010 joomla.vargas.co.cr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomla.vargas.co.cr
# Technical Support:  Forum - http://joomla.vargas.co.cr/forum
-------------------------------------------------------------------------*/

// Set flag that this is a parent file
define('_JEXEC', 1);

// No direct access.
defined('_JEXEC') or die;

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

jimport('joomla.database.database');
jimport('joomla.database.table');

$app = JFactory::getApplication('site');
$app->initialise();

$user = JFactory::getUser();

$plugin	= JPluginHelper::getPlugin('content', 'tz_portfolio_vote');

$params = new JRegistry;
$params->loadString($plugin->params);

if ( $params->get('access') == 1 && !$user->get('id') ) {
	echo 'login';
} else {
	$user_rating = JRequest::getInt('user_rating');
	$cid = JRequest::getInt('cid');
	$xid = JRequest::getVar('xid');
	$db  = JFactory::getDbo();
	if ($user_rating >= 1 && $user_rating <= 5) {
		$currip = $_SERVER['REMOTE_ADDR'];
		if ( !(int)$xid ){
			$query = "SELECT * FROM #__content_rating WHERE content_id = " . $cid;
			$db->setQuery( $query );
			$votesdb = $db->loadObject();
			if ( !$votesdb ) {
				$query = "INSERT INTO #__content_rating ( content_id, lastip, rating_sum, rating_count )"
				. "\n VALUES ( " . $cid . ", " . $db->Quote( $currip ) . ", " . $user_rating . ", 1 )";
				$db->setQuery( $query );
				$db->query() or die( $db->getErrorMsg() );
			} else {
				if ($currip != ($votesdb->lastip)) {
					$query = "UPDATE #__content_rating"
					. "\n SET rating_count = rating_count + 1, rating_sum = rating_sum + " .   $user_rating . ", lastip = " . $db->Quote( $currip )
					. "\n WHERE content_id = " . $cid;
					$db->setQuery( $query );
					$db->query() or die( $db->getErrorMsg() );
				} else {
					echo 'voted';
					exit();
				}
			}
		} else {
			$xid = intval($xid);
			$query = "SELECT * FROM #__tz_portfolio_vote WHERE content_id=".$cid." AND extra_id=".$xid;
			$db->setQuery( $query );
			$votesdb = $db->loadObject();
			if ( !$votesdb ) {
				$query = "INSERT INTO #__tz_portfolio_vote  (content_id,extra_id,lastip,rating_sum,rating_count)"
				. "\n VALUES (".$cid.",".$xid.",".$db->Quote($currip).",".$user_rating.",1)";
				$db->setQuery( $query );
				$db->query() or die( $db->getErrorMsg() );
			} else {
				if ($currip != ($votesdb->lastip)) {
					$query = "UPDATE #__tz_portfolio_vote"
					. "\n SET rating_count = rating_count + 1, rating_sum = rating_sum + " .  $user_rating . ", lastip = " . $db->Quote( $currip )
					. "\n WHERE content_id=".$cid." AND extra_id=".$xid;
					$db->setQuery( $query );
					$db->query() or die( $db->getErrorMsg() );
				} else {
					echo 'voted';
					exit();
				}
			}
		}
		echo 'thanks';
	}
}
