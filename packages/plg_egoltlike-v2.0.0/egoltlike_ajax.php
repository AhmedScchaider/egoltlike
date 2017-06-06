<?php
/**
 * @package   	Egolt Like
 * @link 		http://www.egolt.com
 * @copyright 	Copyright (C) Egolt - www.egolt.com
 * @author    	Soheil Novinfard
 * @license    	GNU/GPL 2
 *
 * Name:		Egolt Like
 * License:		GNU/GPL 2
 * Product:		http://www.egolt.com/products/egoltlike
 */

// Set flag that this is a parent file
define('_JEXEC', 1);

// Check Joomla! Library and direct access
defined('_JEXEC') or die('Direct access denied!');

define( 'DS', DIRECTORY_SEPARATOR );

define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..' );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

jimport('joomla.database.database');
jimport('joomla.database.table');

$app = JFactory::getApplication('site');
$app->initialise();

// Load languages
$lang = JFactory::getLanguage();
$lang->load('plg_content_egoltlike', JPATH_ADMINISTRATOR);

$user = JFactory::getUser();

$plugin	= JPluginHelper::getPlugin('content', 'egoltlike');

$params = new JRegistry;
$params->loadString($plugin->params);

$tflag	= TRUE;
$status	= 'fail';
$input	= JFactory::getApplication()->input;
$id		= $input->get('cid');
$lval	= $input->get('lval');
$db		= JFactory::getDbo();
$currip = $_SERVER['REMOTE_ADDR'];

if($params->get('regonly', 0))
{
	if ($user->guest)
	{
		$status	= 'noaccess';
		$tflag = FALSE;
	}
}

// Validate like value
if(($lval !='1') AND ($lval !='2'))
	$tflag = FALSE;

// Flag for like or dislike	
if($lval == '1') $like = 1; else $like = 0;

// Categories and Articles filter
if($params->get('encats') or $params->get('discats') or $params->get('disarts'))
{
	if( $params->get('disarts') and (in_array($id, $params->get('disarts'))) )
	{
			echo 'noaccess';
			exit();	
	}
		
	if($params->get('discats') or $params->get('encats'))
	{
		// get article category
		$query = $db->getQuery(true);
		$query->select('id, catid');
		$query->from('#__content');
		$query->where('id = '.$db->quote($id));
		$db->setQuery( (string)$query );
		$cnres = $db->loadObject();
		$catid = $cnres->catid;
		
		if( $params->get('discats') and (in_array($catid, $params->get('discats'))) )
		{
			echo 'noaccess';	
			exit();
		}
		
		if( $params->get('encats') and (!in_array($catid, $params->get('encats'))) )
		{
			echo 'noaccess';
			exit();			
		}
		
	}
}

if($tflag)
{
	$va = $params->get('voteagain', -1);
	$query = $db->getQuery(true);
	$query->select('*');
	$query->from('#__egoltlike');
	$query->where('cid = '.$db->quote($id));
	$query->where('service = '.$db->quote(1));
	$db->setQuery( (string)$query );
	$res = $db->loadObject();
			
	// check empty row
	if(empty($res))
	{
		$query = $db->getQuery(true);
		$query->insert('#__egoltlike');
		$query->set('cid = '.$db->quote($id));
		$query->set('service = '.$db->quote(1));
		$query->set('lastip = '.$db->quote($currip));
		$query->set('lastdate = NOW()');
		if($like)
		{
			$pos = 1;
			$sum = 1;
			$query->set('pos = '.$db->quote($pos));
		}
		else
		{
			$neg = 1;
			$sum = -1;
			$query->set('neg = '.$db->quote($neg));
		}
		$query->set('sum = '.$db->quote($sum));
		$db->setQuery( (string)$query );	
		if (!$db->execute()) {
			return false;
		}
		$status = 'thanks';
	}
	else
	{
		$upflag = 0;
		if($currip != $res->lastip)
		{
			$upflag = 1;
		}
		elseif($va == '1')
		{
			$upflag = 1;
		}
		elseif($va != '-1')
		{
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__egoltlike');
			$query->where('cid = '.$db->quote($id));
			$query->where('lastip = '.$db->quote($currip));
			$query->where('service = '.$db->quote(1));
			$query->where('lastdate <> '. $db->Quote($db->getNullDate()));
			switch($va) 
			{
				case '1day':
					$dur_param = 'INTERVAL 1 DAY';
				break;
						
				case '1week':
					$dur_param = 'INTERVAL 7 DAY';
				break;
						
				case '1month':
					$dur_param = 'INTERVAL 1 MONTH';
				break;
								
				case '3month':
					$dur_param = 'INTERVAL 3 MONTH';
				break;
								
				case '6month':
					$dur_param = 'INTERVAL 6 MONTH';
				break;
								
				case '1year':
						$dur_param = 'INTERVAL 1 YEAR';
				break;
			}
			$query->where('lastdate BETWEEN DATE_SUB(NOW(), '. $dur_param .') AND NOW()');
			$db->setQuery( (string)$query );
			// die($query);
			$res2 = $db->loadObject();
				
			if(empty($res2))
			{
				$upflag = 1;
			}
		}
			
		// Update
		if($upflag == 1)
		{
			$query	= $db->getQuery(true);
			$query->update('`#__egoltlike`');
			$query->set('lastip = '.$db->quote($currip));
			if($like)
			{
				$pos = $res->pos+1;
				$sum = $res->sum+1;
				$query->set('pos = '.$db->quote($pos));
			}
			else
			{
				$neg = $res->neg+1;
				$sum = $res->sum-1;
				$query->set('neg = '.$db->quote($neg));
			}
			$query->set('sum = '.$db->quote($sum));
			$query->set('lastdate = NOW()');
			$query->where('cid = '.$db->quote($id));
			$query->where('service = '.$db->quote(1));
			$db->setQuery( (string)$query );	
			if (!$db->execute()) {
				return false;
			}
			$status = 'thanks';		
		}
		else
		{
			$status = 'liked';
		}	
	}
	
	// Submit Logs
	if($status == 'thanks')
	{
		$query = $db->getQuery(true);
		$query->insert('#__egoltlike_logs');
		$query->set('service = '.$db->quote(1));
		$query->set('cid = '.$db->quote($id));
		$query->set('logip = '.$db->quote($currip));
		$query->set('logdate = NOW()');
		if($like)
			$query->set('rate = '.$db->quote('1'));
		else
			$query->set('rate = '.$db->quote('-1'));
		$db->setQuery( (string)$query );	
		if (!$db->execute()) {
			return false;
		}			
	}
}

echo $status;
